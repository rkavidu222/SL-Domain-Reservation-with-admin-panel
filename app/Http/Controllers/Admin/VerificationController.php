<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DomainOrder;
use App\Models\Verification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

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

    // Define destination path inside public
    $destinationPath = public_path('payment_slips/receipts');

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    // Generate unique file name
    $filename = time() . '_' . $receipt->getClientOriginalName();

    // Move uploaded file to public/payment_slips/receipts
    $receipt->move($destinationPath, $filename);

    // Store relative path from public folder (for URLs)
    $receiptPath = 'payment_slips/receipts/' . $filename;

    Verification::create([
        'domain_order_id' => $request->domain_order_id,
        'reference_number' => $request->reference_number,
        'description' => $request->description,
        'receipt_path' => $receiptPath,
        'status' => 'pending',
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

        $filePath = base_path($verification->receipt_path); // full path: payment_slips/receipts/filename.pdf

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
            'payment_status' => 'required|in:pending,verified,rejected',
        ]);

        $verification = Verification::findOrFail($id);
        $verification->payment_status = $request->payment_status;
        $verification->save();

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
}
