<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OtpController extends Controller
{
    public function showVerificationForm()
    {
        return view('layouts.otpVerification');

    }

    public function paymentDetails()
    {
        return view('layouts.paymentDetails');
    }
}
