<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DomainOrder;
use App\Models\Verification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Models\InvoiceSmsLog;
use App\Helpers\OtpHelper;

class VerificationController extends Controller
{
    // Show verification creation page with orders filtered by payment status
    public function create()
    {
        $orders = DomainOrder::orderBy('created_at', 'desc')->get();

        $paidOrders = $orders->where('payment_status', 'paid');
        $pendingOrders = $orders->where('payment_status', 'pending');
        $awaitingProofOrders = $orders->where('payment_status', 'awaiting_proof');
        $clientAccCreatedOrders = $orders->where('payment_status', 'client_acc_created');
        $activedOrders = $orders->where('payment_status', 'actived');

        return view('admin.layouts.verification.verify', compact(
            'orders',
            'paidOrders',
            'pendingOrders',
            'awaitingProofOrders',
            'clientAccCreatedOrders',
            'activedOrders'
        ));
    }

    // Store new verification record and update order status
    public function store(Request $request)
    {
        $request->validate([
            'domain_order_id' => 'required|exists:domain_orders,id',
            'reference_number' => 'required|string|max:255',
            'receipt' => 'required|mimes:jpeg,png,jpg,pdf|max:2048',
            'description' => 'nullable|string',
        ]);

        $receipt = $request->file('receipt');
        $destinationPath = public_path('payment_slips/receipts');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $filename = time() . '_' . $receipt->getClientOriginalName();
        $receipt->move($destinationPath, $filename);
        $receiptPath = 'payment_slips/receipts/' . $filename;

        $verification = Verification::create([
            'domain_order_id' => $request->domain_order_id,
            'reference_number' => $request->reference_number,
            'description' => $request->description,
            'receipt_path' => $receiptPath,
        ]);

        DomainOrder::where('id', $request->domain_order_id)->update([
            'payment_status' => 'awaiting_proof',
        ]);

        // Log activity
        log_activity("Payment verification created for order_id={$request->domain_order_id}, verification_id={$verification->id}");

        return redirect()->back()->with('success', 'Payment verification submitted successfully.');
    }

    // List all verifications
    public function index()
    {
        $verifications = Verification::with('domainOrder')->orderBy('created_at', 'desc')->get();

        return view('admin.layouts.verification.view_verifications', compact('verifications'));
    }

    // Show details of one verification
    public function show($id)
    {
        $verification = Verification::with('domainOrder')->findOrFail($id);

        return view('admin.layouts.verification.verification_details', compact('verification'));
    }

    // Download receipt file
    public function downloadReceipt($id)
    {
        $verification = Verification::findOrFail($id);
        $filePath = public_path($verification->receipt_path);

        if (!File::exists($filePath)) {
            abort(404, 'File not found.');
        }

        $mimeType = File::mimeType($filePath);

        return Response::file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }

    // Update payment status on related domain order
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,awaiting_proof,client_acc_created,actived',
        ]);

        $verification = Verification::with('domainOrder')->findOrFail($id);

        if ($verification->domainOrder) {
            $oldStatus = $verification->domainOrder->payment_status;

            $verification->domainOrder->payment_status = $request->payment_status;
            $verification->domainOrder->save();

            // Log activity
            log_activity("Payment status updated for order_id={$verification->domain_order_id}, from '{$oldStatus}' to '{$request->payment_status}'");

            // Send SMS after saving status
            $this->sendSms($verification->domainOrder->id);
        }

        return redirect()->back()->with('success', 'Payment status updated and SMS sent successfully.');
    }

    // Delete verification and associated receipt file
    public function destroy($id)
    {
        $verification = Verification::findOrFail($id);
        $filePath = public_path($verification->receipt_path);

        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $verification->delete();

        // Log activity
        log_activity("Payment verification deleted, verification_id={$id}");

        return redirect()->back()->with('success', 'Verification deleted successfully.');
    }

    // Map payment status to readable label
    protected function getPaymentStatusLabel(string $status): string
    {
        $statusLabels = [
            'paid' => 'PAID',
            'pending' => 'PENDING',
            'awaiting_proof' => 'AWAITING PROOF',
            'client_acc_created' => 'CLIENT ACC CREATED',
            'actived' => 'ACTIVED',
        ];

        $key = strtolower(trim($status));

        return $statusLabels[$key] ?? 'UNKNOWN';
    }

    // Send SMS with invoice link and actual payment status label
    public function sendSms($id)
    {
        $invoice = DomainOrder::findOrFail($id);
        $mobile = $invoice->mobile;
        $code = $invoice->unique_code;

        $paymentStatusLabel = $this->getPaymentStatusLabel($invoice->payment_status ?? '');

        $url = "https://buydomains.srilankahosting.lk/invoice/view/{$code}";

        $message = "Hello {$invoice->first_name}, Thank you for connecting with us. Your payment status is: {$paymentStatusLabel}. Here is your invoice: {$url}";

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
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        Log::info("SMS API response for invoice #{$invoice->id}: HTTP {$httpCode} - {$response}");

        $status = 'Failed';

        if ($response === false) {
            Log::error("SMS sending failed for invoice #{$invoice->id}: {$curlError}");
        } elseif ($httpCode !== 200) {
            Log::warning("SMS API returned HTTP status {$httpCode} for invoice #{$invoice->id}: {$response}");
        } else {
            $responseData = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning("Invalid JSON response for invoice #{$invoice->id}: {$response}");
            } else {
                if (isset($responseData['status'])) {
                    $apiStatus = strtoupper($responseData['status']);
                } elseif (isset($responseData['data']['status'])) {
                    $apiStatus = strtoupper($responseData['data']['status']);
                } else {
                    $apiStatus = null;
                    Log::warning("No status field in SMS API response for invoice #{$invoice->id}");
                }

                if ($apiStatus) {
                    if (in_array($apiStatus, ['SUCCESS', 'SENT'])) {
                        $status = 'Success';
                    } elseif ($apiStatus === 'PENDING') {
                        $status = 'Pending';
                    } else {
                        $status = 'Failed';
                        Log::warning("SMS API returned unexpected status '{$apiStatus}' for invoice #{$invoice->id}");
                    }
                }
            }
        }

        InvoiceSmsLog::create([
            'invoice_id' => $invoice->id,
            'phone' => $mobile,
            'message' => $message,
            'status' => $status,
        ]);

        // Log SMS sending activity
        log_activity("Invoice SMS sent for order_id={$invoice->id}, status={$status}");

        return redirect()->back()->with('success', 'Invoice SMS sent and logged successfully.');
    }
}
