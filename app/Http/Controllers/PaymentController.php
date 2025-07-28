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
        $orderId = session('domain_order_id');
        $invoice = DomainOrder::find($orderId);

        if (!$invoice) {
            return redirect('/')->with('error', 'Order not found.');
        }

        $mobile = $invoice->mobile;
        $code = $invoice->unique_code;
        $url = "https://buydomains.srilankahosting.lk/invoice/view/{$code}";
        $message = "Hello {$invoice->first_name}, Thank you for connecting with us. Here you can view your invoice: {$url}";

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

            // Optionally parse JSON response to detect success/failure status
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
                // If API response format unexpected
                $status = 'Failed';
                Log::warning("Unexpected SMS API response: " . $response);
            }
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

        return redirect('/confirmation')->with('success', 'Payment skipped and SMS sent.');
    }
}
