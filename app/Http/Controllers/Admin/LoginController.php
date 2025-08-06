<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    // Show the admin login form
    public function showLoginForm()
    {
        return view('admin.login');
    }

    // Handle admin login attempt
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();

            // Log Laravel log
            Log::info('Admin login attempt successful.', ['admin_id' => $admin->id, 'email' => $admin->email]);
            // Log custom activity
            log_activity("Admin login successful, admin_id={$admin->id}, email={$admin->email}");



            if ($admin->is_suspended) {
                Auth::guard('admin')->logout();

                Log::warning('Suspended admin tried to login.', ['admin_id' => $admin->id, 'email' => $admin->email]);
                log_activity("Suspended admin login attempt, admin_id={$admin->id}, email={$admin->email}");

                Session::flash('error', 'Your account was suspended.');
                return redirect()->route('admin.login');
            }

            Session::flash('success', 'Login successful!');
            return redirect()->route('admin.dashboard');
        }

        Log::warning('Admin login attempt failed.', ['email' => $request->input('email'), 'ip' => $request->ip()]);
        log_activity("Admin login failed, email={$request->input('email')}, ip={$request->ip()}");

        Session::flash('error', 'Invalid credentials.');
        return redirect()->route('admin.login');
    }

    // Handle admin logout
    public function logout(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if ($admin) {
            Log::info('Admin logged out.', ['admin_id' => $admin->id, 'email' => $admin->email]);
            log_activity("Admin logged out, admin_id={$admin->id}, email={$admin->email}");
        }

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Session::flash('success', 'You have been logged out.');
        return redirect()->route('admin.login');
    }
}
