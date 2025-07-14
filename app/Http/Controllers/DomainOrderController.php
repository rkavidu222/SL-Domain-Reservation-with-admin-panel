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

        // Remove leading '+'
        if (substr($number, 0, 1) === '+') {
            $number = substr($number, 1);
        }

        // If starts with 0, replace with 94
        if (substr($number, 0, 1) === '0') {
            $number = '94' . substr($number, 1);
        }

        // If does not start with 94, prepend it (just in case)
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

    // Save data temporarily in session
    session([
        'domain_order_data' => $validated,
    ]);

    // Generate OTP
    $otp = rand(100000, 999999);
    $normalizedMobile = $this->normalizeSriLankanMobile($validated['mobile']);

    // Store OTP and mobile/email in session
    session([
        'otp' => $otp,
        'mobile' => $normalizedMobile,
        'email' => $validated['email'],
		'otp_expires_at' => now()->addMinutes(5),
    ]);

    // Send SMS
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


    // Send OTP SMS using cURL and log results
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

    // Admin: List all domain orders (paginated) with search
    public function adminIndex(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $orders = DomainOrder::query();

        if ($search) {
            $orders->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('domain_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $orders->where('category', $category);
        }

        if ($dateFrom) {
            $orders->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $orders->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $orders->orderBy('created_at', 'asc')->paginate(10)->withQueryString();

        return view('admin.layouts.management.orders', compact('orders', 'search', 'category', 'dateFrom', 'dateTo'));
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

    // Admin: View trashed (soft-deleted) orders
    public function trashed(Request $request)
    {
        $search    = $request->input('search');
        $category  = $request->input('category');
        $dateFrom  = $request->input('date_from');
        $dateTo    = $request->input('date_to');

        $orders = DomainOrder::onlyTrashed()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%")
                      ->orWhere('domain_name', 'like', "%{$search}%");
                });
            })
            ->when($category, function ($query, $category) {
                $query->where('category', $category);
            })
            ->when($dateFrom, function ($query, $dateFrom) {
                $query->whereDate('deleted_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                $query->whereDate('deleted_at', '<=', $dateTo);
            })
            ->orderBy('deleted_at', 'desc')
            ->paginate(10)
            ->appends(request()->except('page'));

        return view('admin.layouts.management.orders_trashed', compact('orders', 'search', 'category', 'dateFrom', 'dateTo'));
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



	public function showPaymentDetails($orderId)
{
    $order = DomainOrder::findOrFail($orderId);

    return view('layouts.paymentDetails', compact('order'));
}


}
