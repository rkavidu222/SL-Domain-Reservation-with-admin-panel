<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DomainOrder;
use App\Helpers\OtpHelper;
use Illuminate\Support\Facades\Log;
use App\Models\InvoiceSmsLog;

class PaymentController extends Controller
{
    public function skipPayment(Request $request)
    {
        // Use order_id from form POST, fallback to session if needed
        $orderId = $request->input('order_id') ?? session('domain_order_id');

        if (!$orderId) {
            return redirect('/')->with('error', 'Session expired or order ID missing.');
        }

        $invoice = DomainOrder::find($orderId);

        if (!$invoice) {
            return redirect('/')->with('error', 'Order not found.');
        }

        // ✅ Update payment status
        $invoice->payment_status = 'pending';
        $invoice->save();

        // ✅ Debug log
        Log::info('Payment status updated to pending', [
            'invoice_id' => $invoice->id,
            'payment_status' => $invoice->payment_status,
        ]);

        // Custom activity log
        log_activity("Payment status updated to 'skipped' for order_id={$invoice->id}, mobile={$invoice->mobile}");

        $mobile = $invoice->mobile;
        $code = $invoice->unique_code;
        $url = "https://buydomains.srilankahosting.lk/invoice/view/{$code}";
        $message = "Hello {$invoice->first_name}, Thank you for connecting with us. Here you can view your invoice: {$url}";

        /*
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
            $status = 'Failed';
            $responseContent = curl_error($ch);
        } else {
            $responseContent = $response;
            $responseData = json_decode($response, true);
            $apiStatus = strtoupper($responseData['status'] ?? '');
            $status = in_array($apiStatus, ['SUCCESS', 'SENT']) ? 'Success' : ($apiStatus === 'PENDING' ? 'Pending' : 'Failed');
        }

        curl_close($ch);

        Log::info('Skip Payment SMS sent', [
            'invoice_id' => $invoice->id,
            'mobile' => $mobile,
            'response' => $responseContent,
            'status' => $status,
        ]);

        InvoiceSmsLog::create([
            'invoice_id' => $invoice->id,
            'phone' => $mobile,
            'message' => $message,
            'status' => $status,
            'response' => $responseContent,
        ]);
        */

        return redirect('/confirmation')
    ->with('paymentMethod', 'skip')
    ->with('success', 'Payment skipped. Status saved to database.')
    ->with('invoice_code', $invoice->unique_code);

    }

    public function paySecurely(Request $request)
    {
        // Use order_id from form POST, fallback to session if needed
        $orderId = $request->input('order_id') ?? session('domain_order_id');

        if (!$orderId) {
            return redirect('/')->with('error', 'Session expired or order ID missing.');
        }

        $invoice = DomainOrder::find($orderId);

        if (!$invoice) {
            return redirect('/')->with('error', 'Order not found.');
        }

        // ✅ Update payment status
        $invoice->payment_status = 'awaiting_proof';
        $invoice->save();

        // ✅ Debug log
        Log::info('Payment status updated to awaiting_proof', [
            'invoice_id' => $invoice->id,
            'payment_status' => $invoice->payment_status,
        ]);

        // Custom activity log
        log_activity("Payment status updated to 'awaiting_proof' for order_id={$invoice->id}, mobile={$invoice->mobile}");

        $mobile = $invoice->mobile;
        $code = $invoice->unique_code;
        $url = "https://buydomains.srilankahosting.lk/invoice/view/{$code}";
        $message = "Hello {$invoice->first_name}, Thank you for choosing to pay securely. Please upload your payment proof. View your invoice here: {$url}";

        /*
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
            $status = 'Failed';
            $responseContent = curl_error($ch);
        } else {
            $responseContent = $response;
            $responseData = json_decode($response, true);
            $apiStatus = strtoupper($responseData['status'] ?? '');
            $status = in_array($apiStatus, ['SUCCESS', 'SENT']) ? 'Success' : ($apiStatus === 'PENDING' ? 'Pending' : 'Failed');
        }

        curl_close($ch);

        Log::info('Pay Securely SMS sent', [
            'invoice_id' => $invoice->id,
            'mobile' => $mobile,
            'response' => $responseContent,
            'status' => $status,
        ]);

        InvoiceSmsLog::create([
            'invoice_id' => $invoice->id,
            'phone' => $mobile,
            'message' => $message,
            'status' => $status,
            'response' => $responseContent,
        ]);
        */

        return redirect('/confirmation')
    ->with('paymentMethod', 'paysecurely')
    ->with('status', 'secure')
    ->with('message', 'Secure payment selected. Status saved to database.')
    ->with('invoice_code', $invoice->unique_code);

    }
}
