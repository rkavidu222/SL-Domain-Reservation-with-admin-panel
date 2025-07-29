<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DomainOrder;
use App\Models\Verification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Models\InvoiceSmsLog;
use App\Helpers\OtpHelper;

class VerificationController extends Controller
{
   public function create()
    {
        $orders = DomainOrder::orderBy('created_at', 'desc')->get();

        $paidOrders = $orders->where('payment_status', 'paid');
        $pendingOrders = $orders->where('payment_status', 'pending');
        $awaitingProofOrders = $orders->where('payment_status', 'awaiting_proof');
        $clientAccCreatedOrders = $orders->where('payment_status', 'client_acc_created');
        $activedOrders = $orders->where('payment_status', 'actived');

      return view('admin.layouts.verification.verify', compact(
            'orders',
            'paidOrders',
            'pendingOrders',
            'awaitingProofOrders',
            'clientAccCreatedOrders',
            'activedOrders'
        ));
    }

   public function store(Request $request)
{
    $request->validate([
        'domain_order_id' => 'required|exists:domain_orders,id',
        'reference_number' => 'required|string|max:255',
        'receipt' => 'required|mimes:jpeg,png,jpg,pdf|max:2048',
        'description' => 'nullable|string',
    ]);

    $receipt = $request->file('receipt');

    $destinationPath = public_path('payment_slips/receipts');

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    $filename = time() . '_' . $receipt->getClientOriginalName();

    $receipt->move($destinationPath, $filename);

    $receiptPath = 'payment_slips/receipts/' . $filename;

    Verification::create([
        'domain_order_id' => $request->domain_order_id,
        'reference_number' => $request->reference_number,
        'description' => $request->description,
        'receipt_path' => $receiptPath,
        // Removed 'status' field here
    ]);

    DomainOrder::where('id', $request->domain_order_id)->update([
        'payment_status' => 'awaiting_proof',
    ]);

    return redirect()->back()->with('success', 'Payment verification submitted successfully.');
}



    public function index()
    {
        $verifications = Verification::with('domainOrder')->orderBy('created_at', 'desc')->get();

        return view('admin.layouts.verification.view_verifications', compact('verifications'));
    }

    public function show($id)
    {
        $verification = Verification::with('domainOrder')->findOrFail($id);

        return view('admin.layouts.verification.verification_details', compact('verification'));
    }

    // Serve receipt file for download or viewing
    public function downloadReceipt($id)
    {
        $verification = Verification::findOrFail($id);

        $filePath = public_path($verification->receipt_path);


        if (!File::exists($filePath)) {
            abort(404, 'File not found.');
        }

        $mimeType = File::mimeType($filePath);

        return Response::file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }

  public function updateStatus(Request $request, $id)
{
    $request->validate([
    'payment_status' => 'required|in:pending,paid,awaiting_proof,client_acc_created,actived',
]);


    $verification = Verification::with('domainOrder')->findOrFail($id);

    // Update payment status on the related domainOrder
    if ($verification->domainOrder) {
        $verification->domainOrder->payment_status = $request->payment_status;
        $verification->domainOrder->save();
    }

    return redirect()->back()->with('success', 'Payment status updated successfully.');
}



    public function destroy($id)
    {
        $verification = Verification::findOrFail($id);

        $filePath = base_path($verification->receipt_path);

        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $verification->delete();

        return redirect()->back()->with('success', 'Verification deleted successfully.');
    }



	public function sendSms($id)
{
    // Retrieve the order by ID or fail
    $invoice = DomainOrder::findOrFail($id);
    $mobile = $invoice->mobile;
    $code = $invoice->unique_code;

    // Construct the invoice URL and SMS message
    $url = "https://buydomains.srilankahosting.lk/invoice/view/{$code}";
    $message = "Hello {$invoice->first_name}, Thank you for connecting with us. Here is your invoice: {$url}";

    // Prepare the payload for the SMS API
    $data = [
        'api_token' => env('SMS_API_TOKEN'),
        'recipient' => OtpHelper::normalizeSriLankanMobile($mobile),
        'sender_id' => 'SLHosting',
        'type' => 'plain',
        'message' => $message,
    ];

    // Initialize curl for SMS API call
    $ch = curl_init('https://sms.serverclub.lk/api/http/sms/send');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute curl request
    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        Log::error("SMS sending failed for invoice #{$invoice->id}: $error");

        $status = 'Failed';
    } else {
        curl_close($ch);
        $responseData = json_decode($response, true);

        if (isset($responseData['status'])) {
            $apiStatus = strtoupper($responseData['status']);

            if (in_array($apiStatus, ['SUCCESS', 'SENT'])) {
                $status = 'Success';
            } elseif ($apiStatus === 'PENDING') {
                $status = 'Pending';
            } else {
                $status = 'Failed';
                Log::warning("SMS API returned unexpected status '{$apiStatus}' for invoice #{$invoice->id}");
            }
        } else {
            $status = 'Failed';
            Log::warning("Unexpected SMS API response for invoice #{$invoice->id}: " . $response);
        }
    }

    // Log SMS attempt to database
    InvoiceSmsLog::create([
        'invoice_id' => $invoice->id,
        'phone' => $mobile,
        'message' => $message,
        'status' => $status,
    ]);

    // Redirect back with a success message (or consider sending error message on failure)
    return redirect()->back()->with('success', 'Invoice SMS sent and logged successfully.');
}

}
