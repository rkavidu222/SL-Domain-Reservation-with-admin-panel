<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DomainOrder;

class InvoiceController extends Controller
{
    // Show all invoices
    public function index()
    {
        $orders = DomainOrder::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.invoices.index', compact('orders'));
    }

    // Show a single invoice
    public function show($id)
    {
        $order = DomainOrder::findOrFail($id);
        return view('admin.invoices.show', compact('order'));
    }
}
