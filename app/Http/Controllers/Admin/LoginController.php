<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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

            if ($admin->is_suspended) {
                Auth::guard('admin')->logout();
                Session::flash('error', 'Your account was suspended.');
                return redirect()->route('admin.login');
            }

            Session::flash('success', 'Login successful!');
            return redirect()->route('admin.dashboard');
        }

        Session::flash('error', 'Invalid credentials.');
        return redirect()->route('admin.login');
    }

    // Handle admin logout
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Session::flash('success', 'You have been logged out.');
        return redirect()->route('admin.login');
    }
}
