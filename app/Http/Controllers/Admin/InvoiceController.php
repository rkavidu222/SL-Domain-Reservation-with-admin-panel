<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DomainOrder;
use App\Helpers\OtpHelper;
use Illuminate\Support\Facades\Log;
use App\Models\InvoiceSmsLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    // Show all invoices
    public function index(Request $request)
{
    $queryAll = DomainOrder::orderBy('created_at', 'desc');
    $queryPaid = DomainOrder::where('payment_status', 'paid')->orderBy('created_at', 'desc');
    $queryPending = DomainOrder::where('payment_status', 'pending')->orderBy('created_at', 'desc');
    $queryAwaitingProof = DomainOrder::where('payment_status', 'awaiting_proof')->orderBy('created_at', 'desc');
    $queryClientAccCreated = DomainOrder::where('payment_status', 'client_acc_created')->orderBy('created_at', 'desc');
    $queryActived = DomainOrder::where('payment_status', 'actived')->orderBy('created_at', 'desc');

    // Filter payment_status for main list
    $paymentStatus = $request->input('payment_status', 'all');
    if ($paymentStatus !== 'all') {
        $queryAll->where('payment_status', $paymentStatus);
    }

    if ($request->has('date_range') && !empty($request->date_range)) {
        $dates = explode(' - ', $request->date_range);
        if (count($dates) === 2) {
            $startDate = $dates[0] . ' 00:00:00';
            $endDate = $dates[1] . ' 23:59:59';

            $queryAll->whereBetween('created_at', [$startDate, $endDate]);
            $queryPaid->whereBetween('created_at', [$startDate, $endDate]);
            $queryPending->whereBetween('created_at', [$startDate, $endDate]);
            $queryAwaitingProof->whereBetween('created_at', [$startDate, $endDate]);
            $queryClientAccCreated->whereBetween('created_at', [$startDate, $endDate]);
            $queryActived->whereBetween('created_at', [$startDate, $endDate]);
        }
    }

    $allOrders = $queryAll->get();

    $orders = $queryAll->paginate(15)->appends($request->all());

    $paidOrders = $queryPaid->get();
    $pendingOrders = $queryPending->get();
    $awaitingProofOrders = $queryAwaitingProof->get();
    $clientAccCreatedOrders = $queryClientAccCreated->get();
    $activedOrders = $queryActived->get();

    return view('admin.layouts.invoices.index', compact(
        'allOrders',
        'orders',
        'paidOrders',
        'pendingOrders',
        'awaitingProofOrders',
        'clientAccCreatedOrders',
        'activedOrders',
        'request'
    ));
}


    public function show($id)
    {
        $order = DomainOrder::findOrFail($id);
        return view('admin.layouts.invoices.show', compact('order'));
    }

    public function sendSms($id)
    {
        $invoice = DomainOrder::findOrFail($id);
        $mobile = $invoice->mobile;
        $code = $invoice->unique_code;
        $admin = auth()->guard('admin')->user();

        $url = "https://buydomains.srilankahosting.lk/invoice/view/{$code}";
        $message = "Hello {$invoice->first_name}, Thank you for connecting with us. Here is your invoice: {$url}";

        $data = [
            'api_token' => env('SMS_API_TOKEN'),
            'recipient' => OtpHelper::normalizeSriLankanMobile($mobile),
            'sender_id' => 'SLHosting',
            'type' => 'plain',
            'message' => $message,
        ];

        $ch = curl_init('https://sms.serverclub.lk/api/http/sms/send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            Log::error("SMS sending failed for invoice #{$invoice->id}: $error");
            $status = 'Failed';
        } else {
            curl_close($ch);
            $responseData = json_decode($response, true);

            if (isset($responseData['status'])) {
                $apiStatus = strtoupper($responseData['status']);
                if ($apiStatus === 'SUCCESS' || $apiStatus === 'SENT') {
                    $status = 'Success';
                } elseif ($apiStatus === 'PENDING') {
                    $status = 'Pending';
                } else {
                    $status = 'Failed';
                }
            } else {
                $status = 'Failed';
                Log::warning("Unexpected SMS API response for invoice #{$invoice->id}: " . $response);
            }
        }

        // Save SMS log to DB
        InvoiceSmsLog::create([
            'invoice_id' => $invoice->id,
            'phone' => $mobile,
            'message' => $message,
            'status' => $status,
        ]);

        // Log activity for SMS send action
        ActivityLog::create([
            'admin_id' => $admin->id,
            'task' => "Invoice SMS sent for invoice_id={$invoice->id}, phone={$mobile}, status={$status}"
        ]);

        return redirect()->back()->with('success', 'Invoice SMS sent and logged successfully.');
    }

    public function report()
    {
        $logs = InvoiceSmsLog::with('invoice')->latest()->get();
        return view('admin.layouts.invoices.report', compact('logs'));
    }
}
