<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SmsController extends Controller
{
    // Show the SMS send form
    public function showSendForm()
    {
        // Dummy SMS templates
        $templates = [
            'Welcome Message' => 'Hello {name}, welcome to SriLankaHosting!',
            'Promo Offer' => 'Hi {name}, check out our new promo: 50% off!',
            'Reminder' => 'Dear {name}, your subscription expires soon.',
        ];

        return view('admin.layouts.sms.sendSMS', compact('templates'));
    }

    // Handle sending SMS (dummy logic)
    public function sendSms(Request $request)
    {
        // Validate required inputs (simple example)
        $request->validate([
            'originator' => 'required|string|max:11',
            'country_code' => 'required|string',
            'recipients' => 'required|string',
            'message' => 'required|string|max:160',
        ]);

        // Extract data (dummy)
        $originator = $request->input('originator');
        $countryCode = $request->input('country_code');
        $recipients = explode("\n", $request->input('recipients'));
        $message = $request->input('message');

        // Count recipients (max 100 allowed)
        $totalRecipients = min(count($recipients), 100);

        // Here you would integrate actual SMS sending API.
        // For now, just pretend it sent successfully.

        return back()->with('success', "SMS sent to {$totalRecipients} recipient(s) successfully!");
    }

    // Dummy handler for sender ID request
    public function requestSenderId(Request $request)
    {
        // Dummy logic, just redirect back with success message
        return back()->with('success', 'Sender ID request submitted (dummy).');
    }

    // Other methods (templates, reports) can remain as stubs
    public function createTemplate()
    {
        return view('admin.layouts.sms.template');
    }

    public function storeTemplate(Request $request)
    {
        return redirect()->back()->with('success', 'SMS template saved (demo).');
    }

    public function report()
    {
        return view('admin.layouts.sms.report');
    }
}
