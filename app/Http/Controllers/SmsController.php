<?php

namespace App\Http\Controllers;
use App\Models\SmsTemplates;
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
        $request->validate([
            'originator' => 'required|string|max:11',
            'country_code' => 'required|string',
            'recipients' => 'required|string',
            'message_template_slug' => 'required|string',
        ]);

        // Fetch the selected SMS template from DB by slug
        $sms_template = SmsTemplates::where('slug', $request->message_template_slug)->first();

        if (!$sms_template) {
            return back()->with('error', 'SMS template not found.');
        }

        // Use the template content as the message directly (no replacements)
        $message = $sms_template->content;

        // Split recipients by new line, comma, or space and trim
        $recipients = preg_split('/[\s,]+/', trim($request->recipients));

        // Limit to max 100 recipients
        $totalRecipients = min(count($recipients), 100);

        // Loop through recipients and send SMS (replace with real SMS gateway call)
        foreach (array_slice($recipients, 0, 100) as $recipient) {
            $this->smsSend(trim($recipient), $message, $request->originator);
        }

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
