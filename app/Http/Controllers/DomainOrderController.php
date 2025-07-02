<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DomainOrder;

class DomainOrderController extends Controller
{
    // Show domain contact info form with prefilled domain and price
    public function showContactForm(Request $request)
	{
		$domain_name = $request->query('domain_name', '');
		$price = $request->query('price', '');
		$category = $request->query('category', '');

		return view('layouts.contactInfomation', compact('domain_name', 'price', 'category'));
	}


    // Store order and contact info
    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'mobile' => 'required|string|max:20',
        ]);

        DomainOrder::create($validated);

        return redirect()->route('otp.verification.page')->with('success', 'Please verify your OTP.');

    }

    // Simple thank you page
    public function thankYou()
    {
        return view('thank_you');
    }
}
