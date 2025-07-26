<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DomainOrder;
use App\Helpers\OtpHelper;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function skipPayment(Request $request)
    {
        // Get the domain order using session or a passed ID
        $orderId = session('domain_order_id'); // <- you MUST store this before
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
        curl_close($ch);

        Log::info('Skip Payment SMS sent', [
            'invoice_id' => $invoice->id,
            'mobile' => $mobile,
            'response' => $response
        ]);

        return redirect('/confirmation')->with('success', 'Payment skipped and SMS sent.');
    }
}
