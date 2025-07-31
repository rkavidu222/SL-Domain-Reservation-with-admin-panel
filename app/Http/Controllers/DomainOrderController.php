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

        // Log activity
        log_activity("Contact form opened with domain: {$domain_name}, price: {$price}, category: {$category}");

        return view('layouts.contactInfomation', compact('domain_name', 'price', 'category'));
    }

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
        ]);

        $mobile = $validated['mobile'];
        if (Str::startsWith($mobile, '0')) {
            $mobile = '94' . substr($mobile, 1);
        }

        $existingOrder = DomainOrder::where('domain_name', $validated['domain_name'])
            ->where('mobile', $mobile)
            ->where('payment_status', 'pending')
            ->first();

        if ($existingOrder) {
            $existingOrder->update([
                'price' => $validated['price'],
                'category' => $validated['category'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
            ]);

            $order = $existingOrder;
            $uniqueCode = $order->unique_code;

            // Log update activity
            log_activity("Existing domain order updated: order_id={$order->id}, domain={$order->domain_name}");
        } else {
            do {
                $uniqueCode = strtoupper(Str::random(8));
            } while (DomainOrder::where('unique_code', $uniqueCode)->exists());

            $order = DomainOrder::create([
                'domain_name' => $validated['domain_name'],
                'price' => $validated['price'],
                'category' => $validated['category'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'mobile' => $mobile,
                'unique_code' => $uniqueCode,
                'payment_status' => 'pending',
            ]);

            // Log new order created
            log_activity("New domain order created: order_id={$order->id}, domain={$order->domain_name}");
        }

        $otp = rand(100000, 999999);

        session([
            'domain_order_id' => $order->id,
            'unique_code' => $uniqueCode,
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
            'unique_code' => $uniqueCode,
        ]);

        // Log OTP generation
        log_activity("OTP generated for order_id={$order->id}, mobile={$mobile}");

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
        $queryAwaitingProof = DomainOrder::where('payment_status', 'awaiting_proof')->orderBy('created_at', 'desc');
        $queryClientAccCreated = DomainOrder::where('payment_status', 'client_acc_created')->orderBy('created_at', 'desc');
        $queryActived = DomainOrder::where('payment_status', 'actived')->orderBy('created_at', 'desc');

        if ($request->has('date_range') && !empty($request->date_range)) {
            $dates = explode(' - ', $request->date_range);

            if (count($dates) === 2) {
                $startDateTime = $dates[0] . ' 00:00:00';
                $endDateTime = $dates[1] . ' 23:59:59';

                $queryAll->whereBetween('created_at', [$startDateTime, $endDateTime]);
                $queryPaid->whereBetween('created_at', [$startDateTime, $endDateTime]);
                $queryPending->whereBetween('created_at', [$startDateTime, $endDateTime]);
                $queryAwaitingProof->whereBetween('created_at', [$startDateTime, $endDateTime]);
                $queryClientAccCreated->whereBetween('created_at', [$startDateTime, $endDateTime]);
                $queryActived->whereBetween('created_at', [$startDateTime, $endDateTime]);

                Log::info('Admin filtered orders by date range', compact('startDateTime', 'endDateTime'));
                // Activity log
                log_activity("Admin filtered orders by date range: {$startDateTime} - {$endDateTime}");
            }
        }

        return view('admin.layouts.management.orders', [
            'orders' => $queryAll->get(),
            'paidOrders' => $queryPaid->get(),
            'pendingOrders' => $queryPending->get(),
            'awaitingProofOrders' => $queryAwaitingProof->get(),
            'clientAccCreatedOrders' => $queryClientAccCreated->get(),
            'activedOrders' => $queryActived->get(),
        ]);
    }

    // Admin: Show a single order
    public function show($id)
    {
        $order = DomainOrder::findOrFail($id);
        Log::info('Admin viewed order details', ['order_id' => $id]);
        log_activity("Admin viewed order details: order_id={$id}");
        return view('admin.layouts.management.order_show', compact('order'));
    }

    // Admin: Soft-delete an order
    public function destroy($id)
    {
        $order = DomainOrder::findOrFail($id);
        $order->delete();
        Log::warning('Order moved to trash', ['order_id' => $id]);
        log_activity("Order moved to trash: order_id={$id}");
        return redirect()->back()->with('success', 'Order moved to trash.');
    }

    // Admin: View trashed orders
    public function trashed(Request $request)
    {
        $query = DomainOrder::onlyTrashed()->orderBy('deleted_at', 'desc');

        if ($request->has('date_range') && !empty($request->date_range)) {
            $dates = explode(' - ', $request->date_range);

            if (count($dates) === 2) {
                $startDateTime = $dates[0] . ' 00:00:00';
                $endDateTime = $dates[1] . ' 23:59:59';

                $query->whereBetween('deleted_at', [$startDateTime, $endDateTime]);
            }
        }

        $all = (clone $query)->get();
        $paid = (clone $query)->where('payment_status', 'paid')->get();
        $pending = (clone $query)->where('payment_status', 'pending')->get();
        $awaitingProof = (clone $query)->where('payment_status', 'awaiting_proof')->get();
        $clientAccCreated = (clone $query)->where('payment_status', 'client_acc_created')->get();
        $actived = (clone $query)->where('payment_status', 'actived')->get();

        Log::info('Admin filtered trashed orders', ['date_range' => $request->date_range ?? null]);
        log_activity("Admin filtered trashed orders, date_range={$request->date_range}");

        return view('admin.layouts.management.orders_trashed', compact(
            'all', 'paid', 'pending', 'awaitingProof', 'clientAccCreated', 'actived'
        ));
    }

    // Admin: Restore a trashed order
    public function restore($id)
    {
        $order = DomainOrder::onlyTrashed()->findOrFail($id);
        $order->restore();

        Log::info('Order restored', ['order_id' => $id]);
        log_activity("Order restored: order_id={$id}");

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
        log_activity("Order permanently deleted: order_id={$id}");

        return redirect()->route('admin.orders.trash')->with('success', 'Order permanently deleted.');
    }

    // Show payment details
    public function showPaymentDetails($orderId)
    {
        $order = DomainOrder::findOrFail($orderId);
        Log::info('User viewed payment details', ['order_id' => $orderId]);
        log_activity("User viewed payment details: order_id={$orderId}");
        return view('layouts.paymentDetails', compact('order'));
    }

    public function viewInvoiceByCode($unique_code)
    {
        $order = DomainOrder::where('unique_code', $unique_code)->first();

        if (!$order) {
            abort(404, 'Invoice not found.');
        }

        // Log invoice viewed publicly
        log_activity("Invoice viewed publicly by code: {$unique_code}, order_id={$order->id}");

        return view('layouts.viewInvoice', compact('order'));
    }
}
