<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DomainOrder;
use App\Models\Verification;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    // Show the verification form with all orders to select from
    public function create()
{
    $orders = DomainOrder::orderBy('created_at', 'desc')->get();

    $paidOrders = $orders->where('payment_status', 'paid');
    $pendingOrders = $orders->where('payment_status', 'pending');
    $awaitingProofOrders = $orders->where('payment_status', 'awaiting_proof');
    $clientAccCreatedOrders = $orders->where('payment_status', 'client_acc_created');
    $activedOrders = $orders->where('payment_status', 'actived');

    return view('admin.layouts.invoices.verify', compact(
        'orders',
        'paidOrders',
        'pendingOrders',
        'awaitingProofOrders',
        'clientAccCreatedOrders',
        'activedOrders'
    ));
}


    // Handle the form submission
    public function store(Request $request)
    {
        $request->validate([
            'domain_order_id' => 'required|exists:domain_orders,id',
            'reference_number' => 'required|string|max:255',
            'receipt' => 'required|mimes:jpeg,png,jpg,pdf|max:2048',
            'description' => 'nullable|string',
        ]);

        // Store receipt file in 'storage/app/public/receipts'
        $receiptPath = $request->file('receipt')->store('receipts', 'public');

        // Create a new verification record
        Verification::create([
            'domain_order_id' => $request->domain_order_id,
            'reference_number' => $request->reference_number,
            'description' => $request->description,
            'receipt_path' => $receiptPath,
            'status' => 'pending', // optional status
        ]);

        // Update order payment status (optional)
        DomainOrder::where('id', $request->domain_order_id)->update([
            'payment_status' => 'awaiting_proof',
        ]);

        return redirect()->route('admin.orders.index')->with('success', 'Payment verification submitted successfully.');
    }
}
