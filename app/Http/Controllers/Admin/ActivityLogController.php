<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('admin')->latest()->paginate(20);
        return view('admin.layouts.activity_logs.index', compact('logs'));
    }
}
