<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DomainOrder;
use Illuminate\Support\Facades\Session;
use App\Helpers\OtpHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DomainOrderController extends Controller
{
    // Show contact info form with prefilled domain data
    public function showContactForm(Request $request)
    {
        $domain_name = $request->query('domain_name', session('domain_name', ''));
        $price = $request->query('price', session('price', ''));
        $category = $request->query('category', session('category', ''));

        session([
            'domain_name' => $domain_name,
            'price' => $price,
            'category' => $category,
        ]);

        Log::info('Contact form opened', compact('domain_name', 'price', 'category'));

        return view('layouts.contactInfomation', compact('domain_name', 'price', 'category'));
    }

    // Store submitted data and send OTP
    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_name' => 'required|string|min:3|max:255|regex:/^([a-z0-9-]+\.)+[a-z]{2,}$/i',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:50',
            'first_name' => 'required|string|regex:/^[A-Za-z\s]+$/|max:100',
            'last_name' => 'required|string|regex:/^[A-Za-z\s]+$/|max:100',
            'email' => 'required|email|max:255',
            'mobile' => [
                'required',
                'regex:/^(0\d{9}|94\d{9})$/',
            ],
        ], [
            'domain_name.required' => 'Please enter a domain name.',
            'domain_name.regex' => 'Domain name must be a valid format like example.lk',
            'price.required' => 'Please provide the domain price.',
            'first_name.required' => 'First name is required.',
            'first_name.regex' => 'First name must only contain letters and spaces.',
            'last_name.required' => 'Last name is required.',
            'last_name.regex' => 'Last name must only contain letters and spaces.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Email must be a valid email address.',
            'mobile.required' => 'Mobile number is required.',
            'mobile.regex' => 'Mobile must start with 0 or 94 (e.g., 0771234567 or 94771234567).',
        ]);

        $mobile = $validated['mobile'];
        if (Str::startsWith($mobile, '0')) {
            $mobile = '94' . substr($mobile, 1);
        }

        $otp = rand(100000, 999999);

        session([
            'domain_order_data' => array_merge($validated, ['mobile' => $mobile]),
            'otp' => $otp,
            'mobile' => $mobile,
            'email' => $validated['email'],
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        Log::info('OTP generated and session data stored', [
            'mobile' => $mobile,
            'otp' => $otp,
            'email' => $validated['email'],
        ]);

        OtpHelper::sendOtpSms($mobile, (string) $otp);

        Log::info('OTP sent', ['mobile' => $mobile, 'otp' => $otp]);

        return redirect()->route('otp.verification.page')->with('success', 'OTP sent to your mobile number.');
    }

    // Admin: List all domain orders
    public function adminIndex(Request $request)
    {
        $queryAll = DomainOrder::orderBy('created_at', 'desc');
        $queryPaid = DomainOrder::where('payment_status', 'paid')->orderBy('created_at', 'desc');
        $queryPending = DomainOrder::where('payment_status', 'pending')->orderBy('created_at', 'desc');

        if ($request->has('date_range') && !empty($request->date_range)) {
            $dates = explode(' - ', $request->date_range);

            if (count($dates) === 2) {
                $startDate = $dates[0];
                $endDate = $dates[1];

                $startDateTime = $startDate . ' 00:00:00';
                $endDateTime = $endDate . ' 23:59:59';

                $queryAll->whereBetween('created_at', [$startDateTime, $endDateTime]);
                $queryPaid->whereBetween('created_at', [$startDateTime, $endDateTime]);
                $queryPending->whereBetween('created_at', [$startDateTime, $endDateTime]);

                Log::info('Admin filtered orders by date range', compact('startDateTime', 'endDateTime'));
            }
        }

        $allOrders = $queryAll->get();
        $paidOrders = $queryPaid->get();
        $pendingOrders = $queryPending->get();

        Log::info('Admin viewed orders', [
            'total' => count($allOrders),
            'paid' => count($paidOrders),
            'pending' => count($pendingOrders),
        ]);

        return view('admin.layouts.management.orders', [
            'orders' => $allOrders,
            'paidOrders' => $paidOrders,
            'pendingOrders' => $pendingOrders,
        ]);
    }

    // Admin: Show a single order
    public function show($id)
    {
        $order = DomainOrder::findOrFail($id);
        Log::info('Admin viewed order details', ['order_id' => $id]);
        return view('admin.layouts.management.order_show', compact('order'));
    }

    // Admin: Soft-delete an order
    public function destroy($id)
    {
        $order = DomainOrder::findOrFail($id);
        $order->delete();
        Log::warning('Order moved to trash', ['order_id' => $id]);
        return redirect()->back()->with('success', 'Order moved to trash.');
    }

    // Admin: View trashed orders
    public function trashed()
    {
        $all = DomainOrder::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        $paid = DomainOrder::onlyTrashed()->where('payment_status', 'paid')->orderBy('deleted_at', 'desc')->get();
        $pending = DomainOrder::onlyTrashed()->where('payment_status', 'pending')->orderBy('deleted_at', 'desc')->get();

        Log::info('Admin accessed trashed orders');

        return view('admin.layouts.management.orders_trashed', compact('all', 'paid', 'pending'));
    }

    // Admin: Restore a trashed order
    public function restore($id)
    {
        $order = DomainOrder::onlyTrashed()->findOrFail($id);
        $order->restore();

        Log::info('Order restored', ['order_id' => $id]);

        $trashedCount = DomainOrder::onlyTrashed()->count();

        if ($trashedCount > 0) {
            return redirect()->route('admin.orders.trash')->with('success', 'Order restored successfully.');
        } else {
            return redirect()->route('admin.orders.index')->with('success', 'Order restored successfully. Trash is now empty.');
        }
    }

    // Admin: Permanently delete a trashed order
    public function forceDelete($id)
    {
        $order = DomainOrder::onlyTrashed()->findOrFail($id);
        $order->forceDelete();

        Log::error('Order permanently deleted', ['order_id' => $id]);

        return redirect()->route('admin.orders.trash')->with('success', 'Order permanently deleted.');
    }

    // Show payment details
    public function showPaymentDetails($orderId)
    {
        $order = DomainOrder::findOrFail($orderId);
        Log::info('User viewed payment details', ['order_id' => $orderId]);
        return view('layouts.paymentDetails', compact('order'));
    }


}
