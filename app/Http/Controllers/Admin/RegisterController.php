<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('admin.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|confirmed|min:6',
            'auth_code' => 'required|string',
        ]);

        $correctCode = '@#slh';

        if ($request->auth_code !== $correctCode) {
            Log::warning('Failed admin registration attempt due to invalid auth code.', [
                'email' => $request->email,
                'ip' => $request->ip(),
            ]);
            return back()->withErrors(['auth_code' => 'Invalid authentication code'])->withInput();
        }

        $admin = Admin::create([
            'name' => $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
        ]);

        Log::info('New admin registered successfully.', [
            'admin_id' => $admin->id,
            'email' => $admin->email,
            'ip' => $request->ip(),
        ]);

        return redirect('/admin/login')->with('success', 'Registration successful. Please login.');
    }
}
