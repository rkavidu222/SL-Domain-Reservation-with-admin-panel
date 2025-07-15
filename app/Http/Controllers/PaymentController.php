<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DomainOrder;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    public function skipPayment(Request $request)
    {
        // Get order ID from query parameter or session
        $orderId = $request->query('order_id') ?? Session::get('order_id');

        if (!$orderId) {
            return redirect('/')->with('error', 'Order not found.');
        }

        $order = DomainOrder::find($orderId);

        if (!$order) {
            return redirect('/')->with('error', 'Order not found.');
        }

        // Update payment status to 'notpaid'
        $order->payment_status = 'notpaid';
        $order->save();

        // Optionally clear order_id from session if you stored it
        Session::forget('order_id');

        // Redirect to confirmation page with success message
        return redirect('/confirmation')->with('message', 'You have skipped payment. Order marked as not paid.');
    }
}
