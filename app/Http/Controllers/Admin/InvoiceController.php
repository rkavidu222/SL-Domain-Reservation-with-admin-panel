<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DomainOrder;
use App\Helpers\OtpHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
class InvoiceController extends Controller
{
    // Show all invoices
    public function index()
    {
        $orders = DomainOrder::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.layouts.invoices.index', compact('orders'));
    }

    // Show a single invoice
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

    // build full invoice link
    $url = "https://buydomains.srilankahosting.lk/invoice/view/{$code}";
    $message = "Hello {$invoice->first_name}, view your invoice: {$url}";

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

    Log::info('Invoice SMS sent', [
        'invoice_id' => $invoice->id,
        'mobile' => $mobile,
        'response' => $response
    ]);

    return redirect()->back()->with('success', 'Invoice SMS sent successfully.');
}
}
