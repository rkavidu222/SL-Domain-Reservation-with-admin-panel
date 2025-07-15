<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\DomainOrder;

class DashboardController extends Controller
{
    public function index()
{
    return view('admin.layouts.dashboard', [
        'adminCount' => Admin::count(), // all admins
        'superAdminCount' => Admin::where('role', 'super')->count(),
        'normalAdminCount' => Admin::where('role', 'normal')->count(),
        'allOrdersCount' => DomainOrder::count(),
        'paidOrdersCount' => DomainOrder::where('payment_status', 'paid')->count(),
        'pendingOrdersCount' => DomainOrder::where('payment_status', 'pending')->count(),
        'trashedOrdersCount' => DomainOrder::onlyTrashed()->count(),
    ]);
}

}
