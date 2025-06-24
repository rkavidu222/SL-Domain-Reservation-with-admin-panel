<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OtpController extends Controller
{

    public function paymentDetails()
    {
        return view('layouts.paymentDetails');
    }
}
