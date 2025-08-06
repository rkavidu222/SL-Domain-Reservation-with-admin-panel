<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Eager-load the 'admin' relationship to avoid N+1 problem
        $logs = ActivityLog::with('admin')->latest()->paginate(20);

        // Render the activity log index Blade file
     return view('admin.layouts.activity_logs.index', compact('logs'));


    }
}
