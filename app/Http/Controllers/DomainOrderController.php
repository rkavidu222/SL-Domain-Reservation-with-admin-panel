<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DomainOrder;

class DomainOrderController extends Controller
{
    // Show contact info form with prefilled domain data
    public function showContactForm(Request $request)
    {
        $domain_name = $request->query('domain_name', '');
        $price = $request->query('price', '');
        $category = $request->query('category', '');

        return view('layouts.contactInfomation', compact('domain_name', 'price', 'category'));
    }

    // Store submitted data to DB and redirect to OTP verification page
    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category' => 'required|string|max:50',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'mobile' => 'required|string|max:20',
        ]);

        DomainOrder::create($validated);

        // Redirect immediately to OTP verification blade
        return redirect()->route('otp.verification.page')->with('success', 'Please verify your OTP.');
    }

    public function adminIndex()
{
    $orders = DomainOrder::orderBy('created_at', 'asc')->paginate(15);
    return view('admin.layouts.management.oders', compact('orders'));
}
}
