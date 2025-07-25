<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DomainOrder;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Helpers\OtpHelper;


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
        // Retrieve order ID or unique_code from session
        $orderId = session('order_id');
        $uniqueCode = session('unique_code');

        if ($orderId) {
            // Fetch existing order by ID
            $order = DomainOrder::find($orderId);
        } elseif ($uniqueCode) {
            // Or fallback to find by unique_code
            $order = DomainOrder::where('unique_code', $uniqueCode)->first();
        } else {
            $order = null;
        }

        if ($order) {
            // Optional: update payment_status here if needed
            // For example, keep it 'pending' until actual payment is confirmed
            // $order->payment_status = 'pending';
            // $order->save();

            // Clear session related to OTP and order data
            session()->forget(['otp', 'otp_expires_at', 'domain_order_data', 'email', 'mobile', 'order_id', 'unique_code']);

            return view('layouts.paymentDetails', [
                'success' => 'OTP verified. Order confirmed successfully.',
                'order' => $order,
            ]);
        } else {
            return redirect()->route('contact.form')->withErrors(['session' => 'Order not found or session expired. Please try again.']);
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

        // Use helper instead of inline cURL logic
        $sent = OtpHelper::sendOtpSms($mobile, $otp);

        if (!$sent) {
            return response()->json(['message' => 'Failed to send OTP. Please try again later.'], 500);
        }

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

        //$response = curl_exec($ch);
        //if ($response === false) {
        //    \Log::error('SMS sending failed: ' . curl_error($ch));
        //}
        //curl_close($ch);
    }
}
