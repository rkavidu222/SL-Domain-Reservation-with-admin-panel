<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DomainOrder;
use App\Models\User;       // Import User model
use Illuminate\Support\Facades\Auth;

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

    // Admin: List all domain orders (paginated)
    public function adminIndex()
    {
        $orders = DomainOrder::orderBy('created_at', 'asc')->paginate(15);
        return view('admin.layouts.management.orders', compact('orders'));
    }

    // Admin: Show details of a single order
    public function show($id)
    {
        $order = DomainOrder::findOrFail($id);
        return view('admin.layouts.management.order_show', compact('order'));
    }

    // Admin: Delete a domain order (soft delete)
    public function destroy($id)
    {
        $order = DomainOrder::findOrFail($id);

        $authUserId = auth()->id();

        // Check if user exists in users table before assigning deleted_by
        if ($authUserId && User::where('id', $authUserId)->exists()) {
            $order->deleted_by = $authUserId;
        } else {
            $order->deleted_by = null;
        }

        $order->save();

        $order->delete();

        return redirect()->back()->with('success', 'Order moved to trash.');
    }

    // Admin: View trashed (soft-deleted) orders
    public function trashed()
    {
        $orders = DomainOrder::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(15);
        return view('admin.layouts.management.orders_trashed', compact('orders'));
    }

    // Admin: Restore a soft-deleted order
    public function restore($id)
    {
        $order = DomainOrder::onlyTrashed()->findOrFail($id);
        $order->restore();

        return redirect()->route('admin.orders.index')->with('success', 'Order restored successfully.');
    }

    // Admin: Permanently delete a soft-deleted order
    public function forceDelete($id)
    {
        $order = DomainOrder::onlyTrashed()->findOrFail($id);
        $order->forceDelete();

        return redirect()->route('admin.orders.trash')->with('success', 'Order permanently deleted.');
    }
}
