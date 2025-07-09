<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DomainOrder;
use App\Models\User;
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

    // Admin: List all domain orders (paginated) with search
	public function adminIndex(Request $request)
	{
		$search = $request->input('search');

		$orders = DomainOrder::query()
			->when($search, function($query, $search) {
				$query->where('first_name', 'like', "%{$search}%")
					  ->orWhere('last_name', 'like', "%{$search}%")
					  ->orWhere('email', 'like', "%{$search}%")
					  ->orWhere('mobile', 'like', "%{$search}%")
					  ->orWhere('domain_name', 'like', "%{$search}%")
					  ->orWhere('category', 'like', "%{$search}%");
			})
			->orderBy('created_at', 'asc')
			->paginate(15)
			->withQueryString();

		return view('admin.layouts.management.orders', compact('orders', 'search'));
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
    $order->delete();
    return redirect()->back()->with('success', 'Order moved to trash.');
}

    // Admin: View trashed (soft-deleted) orders with search
	public function trashed(Request $request)
	{
		$search = $request->input('search');

		$ordersQuery = DomainOrder::onlyTrashed();

		if ($search) {
			$ordersQuery->where(function ($query) use ($search) {
				$query->where('first_name', 'like', "%{$search}%")
					  ->orWhere('last_name', 'like', "%{$search}%")
					  ->orWhere('email', 'like', "%{$search}%")
					  ->orWhere('mobile', 'like', "%{$search}%")
					  ->orWhere('domain_name', 'like', "%{$search}%")
					  ->orWhere('category', 'like', "%{$search}%");
			});
		}

		$orders = $ordersQuery->orderBy('deleted_at', 'desc')->paginate(15);

		return view('admin.layouts.management.orders_trashed', compact('orders', 'search'));
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
