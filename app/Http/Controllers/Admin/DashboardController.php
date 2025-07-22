<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\DomainOrder;
use App\Models\SmsTemplate;
use App\Models\SmsReport;
use App\Models\SmsLog;
class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.layouts.dashboard', [
            'adminCount' => Admin::count(),
            'superAdminCount' => Admin::where('role', 'super')->count(),
            'normalAdminCount' => Admin::where('role', 'normal')->count(),
            'allOrdersCount' => DomainOrder::count(),
            'paidOrdersCount' => DomainOrder::where('payment_status', 'paid')->count(),
            'pendingOrdersCount' => DomainOrder::where('payment_status', 'pending')->count(),
            'trashedOrdersCount' => DomainOrder::onlyTrashed()->count(),
            'smsTemplatesCount' => SmsTemplate::count(),
            'successSmsCount' => SmsLog::where('status', 'success')->count(),
            'failedSmsCount' => SmsLog::where('status', 'failed')->count(),
        ]);
    }

}





