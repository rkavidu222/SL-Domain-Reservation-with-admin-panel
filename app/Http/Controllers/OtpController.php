<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DomainOrder;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class OtpController extends Controller
{
    // Show the OTP input form
    public function showVerificationForm()
    {
        $expiresAt = session('otp_expires_at');
        $now = Carbon::now();

        $remainingSeconds = 0;

        if ($expiresAt && $now->lt($expiresAt)) {
            $remainingSeconds = $now->diffInSeconds($expiresAt);
        }

        return view('layouts.otpVerification', ['remainingSeconds' => $remainingSeconds]);
    }

    // Verify OTP and proceed with order saving and payment details view
   public function paymentDetails(Request $request)
	{
		$sessionOtp = session('otp');
		$expiresAt = session('otp_expires_at');
		$enteredOtp = $request->input('otp');

		if (!$expiresAt || now()->gt($expiresAt)) {
			return redirect()->back()->withErrors(['otp' => 'OTP expired. Please request a new one.']);
		}

		if ($enteredOtp == $sessionOtp) {
			$data = session('domain_order_data');

			if ($data) {
				$order = DomainOrder::create([
					'domain_name' => $data['domain_name'],
					'price'       => $data['price'],
					'category'    => $data['category'],
					'first_name'  => $data['first_name'],
					'last_name'   => $data['last_name'],
					'email'       => $data['email'],
					'mobile'      => $data['mobile'],
				]);

				session()->forget(['otp', 'otp_expires_at', 'domain_order_data', 'email', 'mobile']);

				return view('layouts.paymentDetails', [
					'success' => 'OTP verified. Order saved successfully.',
					'order' => $order,
				]);
			} else {
				return redirect()->route('contact.form')->withErrors(['session' => 'Session expired. Please try again.']);
			}
		} else {
			return redirect()->back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
		}
	}

    // Resend OTP and reset timer, send SMS again
    public function resendOtp(Request $request)
    {
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);

        session([
            'otp' => $otp,
            'otp_expires_at' => $expiresAt,
        ]);

        $mobile = session('mobile');

        if (!$mobile) {
            return response()->json(['message' => 'Mobile number missing from session.'], 422);
        }

        $smsData = [
            'api_token' => '10|BJcXe3w1SVIpoJKYLm6cpgCaWMIMkCyiCfq4NHFU97b97a43',
            'recipient' => $mobile,
            'sender_id' => 'SLHosting',
            'type' => 'plain',
            'message' => "Your OTP code is: {$otp}",
        ];

        $this->sendOtpSms($smsData);

        return response()->json([
            'message' => 'OTP resent successfully.',
            'expiresAt' => $expiresAt->toDateTimeString(),
        ]);
    }

    // Send OTP SMS using cURL with the Serverclub SMS API
    private function sendOtpSms(array $data)
    {
        $url = 'https://sms.serverclub.lk/api/http/sms/send';

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        if ($response === false) {
            \Log::error('SMS sending failed: ' . curl_error($ch));
        }
        curl_close($ch);
    }
}
