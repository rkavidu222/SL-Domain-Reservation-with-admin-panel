<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SmsTemplate;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    // Show the SMS send form with templates
    public function showSendForm()
    {
        $templatesList = SmsTemplate::all();
        $templates = $templatesList->pluck('content', 'slug')->toArray();

        return view('admin.layouts.sms.sendSMS', compact('templatesList', 'templates'));
    }

    // Normalize Sri Lankan mobile numbers (e.g. +9477xxxxxxx → 9477xxxxxxx → 9477xxxxxxx)
    private function normalizeSriLankanMobile($number)
    {
        $number = trim($number);

        if (substr($number, 0, 1) === '+') {
            $number = substr($number, 1);
        }

        if (substr($number, 0, 1) === '0') {
            $number = '94' . substr($number, 1);
        }

        if (substr($number, 0, 2) !== '94') {
            $number = '94' . $number;
        }

        return $number;
    }

    // Send SMS using cURL to ensure consistency with your OTP function
    private function sendSmsCurl(array $data)
    {
        $url = 'https://sms.serverclub.lk/api/http/sms/send'; // Use your actual API endpoint here

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'error' => $err];
        }

        $decoded = json_decode($response, true);
        if ($decoded && isset($decoded['status']) && $decoded['status'] == 'success') {
            return ['success' => true, 'response' => $decoded];
        }

        return ['success' => false, 'response' => $decoded ?: $response];
    }

    // Handle the form submission and send SMS
    public function sendSms(Request $request)
    {
        $request->validate([
            'template_slug' => 'required|exists:sms_templates,slug',
            'recipients' => 'required|string',
            'placeholders' => 'required|array',
        ]);

        $template = SmsTemplate::where('slug', $request->template_slug)->firstOrFail();

        // Split and clean recipients by new lines
        $recipients = array_filter(array_map('trim', explode("\n", $request->recipients)));

        $messageTemplate = $template->content;
        $totalSent = 0;
        $failures = [];

        foreach ($recipients as $phone) {
            // Normalize phone number format
            $normalizedPhone = $this->normalizeSriLankanMobile($phone);

            $message = $messageTemplate;

            // Replace placeholders with user inputs
            foreach ($request->placeholders as $key => $value) {
                $message = str_replace('{' . $key . '}', $value, $message);
            }

            // Prepare payload
            $smsData = [
                'api_token' => '10|BJcXe3w1SVIpoJKYLm6cpgCaWMIMkCyiCfq4NHFU97b97a43',
                'recipient' => $normalizedPhone,
                'sender_id' => 'SLHosting',
                'type' => 'plain',
                'message' => $message,
            ];

            // Send SMS via cURL helper
            $result = $this->sendSmsCurl($smsData);

            if ($result['success']) {
                $totalSent++;
            } else {
                // Log failure details for debugging
                Log::error("Failed to send SMS to {$normalizedPhone}: ", ['response' => $result['response'] ?? $result['error']]);
                $failures[] = $normalizedPhone . ' → ' . json_encode($result['response'] ?? $result['error']);
            }
        }

        return back()
            ->with('success', "SMS sent to {$totalSent} recipient(s).")
            ->with('failures', $failures);
    }

    // Template management - list templates
    public function createTemplate()
    {
        $templates = SmsTemplate::latest()->get();
        return view('admin.layouts.sms.template', compact('templates'));
    }

    // Template management - store new template
    public function storeTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:sms_templates,slug',
            'content' => 'required|string',
        ]);

        SmsTemplate::create($request->only('name', 'slug', 'content'));

        return redirect()->back()->with('success', 'SMS template saved.');
    }

    // SMS report view
    public function report()
    {
        return view('admin.layouts.sms.report');
    }
}
