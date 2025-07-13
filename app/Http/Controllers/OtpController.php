<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DomainOrder;

class OtpController extends Controller
{
    public function showVerificationForm()
    {
        return view('layouts.otpVerification');
    }

    public function paymentDetails(Request $request)
    {
        $sessionOtp = session('otp');
        $enteredOtp = $request->input('otp');

        if ($enteredOtp == $sessionOtp) {
            // ✅ OTP is correct

            $data = session('domain_order_data');

            if ($data) {
                // Save to database
                DomainOrder::create([
                    'domain_name' => $data['domain_name'],
                    'price'       => $data['price'],
                    'category'    => $data['category'],
                    'first_name'  => $data['first_name'],
                    'last_name'   => $data['last_name'],
                    'email'       => $data['email'],
                    'mobile'      => $data['mobile'],
                ]);

                // Clear session data
                session()->forget(['otp', 'domain_order_data', 'email', 'mobile']);

                // Proceed to payment details page
                return view('layouts.paymentDetails')->with('success', 'OTP verified. Order saved successfully.');
            } else {
                return redirect()->route('contact.form')->withErrors(['session' => 'Session expired. Please try again.']);
            }
        } else {
            return redirect()->back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        }
    }
}
