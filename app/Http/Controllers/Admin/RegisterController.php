<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

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
            return back()->withErrors(['auth_code' => 'Invalid authentication code'])->withInput();
        }

        Admin::create([
            'name' => $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
        ]);

        return redirect('/admin/login')->with('success', 'Registration successful. Please login.');
    }
}
