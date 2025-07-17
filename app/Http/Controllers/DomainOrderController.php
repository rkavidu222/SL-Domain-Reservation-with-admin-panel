<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DomainOrder;
use Illuminate\Support\Facades\Session;

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

    // Normalize mobile number to "94xxxxxxxxx" format
    private function normalizeSriLankanMobile($number)
    {
        $number = trim($number);

        if (substr($number, 0, 1) === '+') {
            $number = substr($number, 1);
        }

        if (substr($number, 0, 1) === '0') {
            $number = '94' . substr($number, 1);
        }

        if (substr($number, 0, 2) !== '94') {
            $number = '94' . $number;
        }

        return $number;
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

        session(['domain_order_data' => $validated]);

        $otp = rand(100000, 999999);
        $normalizedMobile = $this->normalizeSriLankanMobile($validated['mobile']);

        session([
            'otp' => $otp,
            'mobile' => $normalizedMobile,
            'email' => $validated['email'],
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        $smsData = [
            'api_token' => '10|BJcXe3w1SVIpoJKYLm6cpgCaWMIMkCyiCfq4NHFU97b97a43',
            'recipient' => $normalizedMobile,
            'sender_id' => 'SLHosting',
            'type' => 'plain',
            'message' => "Your OTP code is: {$otp}",
        ];

        $this->sendOtpSms($smsData);

        return redirect()->route('otp.verification.page')->with('success', 'OTP sent to your mobile number.');
    }

    // Send OTP SMS using cURL
    private function sendOtpSms(array $data)
    {
        $url = 'https://sms.serverclub.lk/api/http/sms/send';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);
    }

    // Admin: List all domain orders (paginated) with filters
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
        }
    }

    // ✨ Return full datasets (no pagination)
    $allOrders = $queryAll->get();
    $paidOrders = $queryPaid->get();
    $pendingOrders = $queryPending->get();

    return view('admin.layouts.management.orders', [
        'orders' => $allOrders,
        'paidOrders' => $paidOrders,
        'pendingOrders' => $pendingOrders,
    ]);
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

    // Admin: View trashed (soft-deleted) orders (All / Paid / Pending tabs support)
    public function trashed()
    {
        $all = DomainOrder::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        $paid = DomainOrder::onlyTrashed()->where('payment_status', 'paid')->orderBy('deleted_at', 'desc')->get();
        $pending = DomainOrder::onlyTrashed()->where('payment_status', 'pending')->orderBy('deleted_at', 'desc')->get();

        return view('admin.layouts.management.orders_trashed', compact('all', 'paid', 'pending'));
    }

    // Admin: Restore a soft-deleted order
    public function restore($id)
    {
        $order = DomainOrder::onlyTrashed()->findOrFail($id);
        $order->restore();

        $trashedCount = DomainOrder::onlyTrashed()->count();

        if ($trashedCount > 0) {
            return redirect()->route('admin.orders.trash')->with('success', 'Order restored successfully.');
        } else {
            return redirect()->route('admin.orders.index')->with('success', 'Order restored successfully. Trash is now empty.');
        }
    }

    // Admin: Permanently delete a soft-deleted order
    public function forceDelete($id)
    {
        $order = DomainOrder::onlyTrashed()->findOrFail($id);
        $order->forceDelete();

        return redirect()->route('admin.orders.trash')->with('success', 'Order permanently deleted.');
    }

    // Show payment details (frontend)
    public function showPaymentDetails($orderId)
    {
        $order = DomainOrder::findOrFail($orderId);
        return view('layouts.paymentDetails', compact('order'));
    }



}
