<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

if (!function_exists('log_activity')) {
    function log_activity($task)
    {
        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'task' => $task,
        ]);
    }
}
