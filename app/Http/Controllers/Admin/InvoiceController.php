<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DomainOrder;
use App\Helpers\OtpHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
	use App\Models\InvoiceSmsLog;
class InvoiceController extends Controller
{
    // Show all invoices
    public function index()
    {
        $orders = DomainOrder::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.layouts.invoices.index', compact('orders'));
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

    return redirect()->back()->with('success', 'Invoice SMS sent and logged successfully.');
}



public function report()
{
    $logs = InvoiceSmsLog::with('invoice')->latest()->get();
    return view('admin.layouts.invoices.report', compact('logs'));
}


}
