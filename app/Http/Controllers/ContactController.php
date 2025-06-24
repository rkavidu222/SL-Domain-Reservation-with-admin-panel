<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function showForm()
    {
        return view('layouts.contactInfomation');
    }

    public function submit(Request $request)
    {

        return view('layouts.otpVerification');
    }



}
