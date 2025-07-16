<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $current = auth()->guard('admin')->user();

        // Super admin sees all, others only themselves
        $query = ($current->role === 'super_admin')
            ? Admin::query()
            : Admin::where('id', $current->id);

        // Paginate results without any search filtering
        $admins = $query->orderBy('id')->paginate(10);

        return view('admin.layouts.management.adminManagement', compact('admins'));
    }

    public function destroy(Admin $admin)
    {
        $current = auth()->guard('admin')->user();

        if ($current->role !== 'super_admin') {
            abort(403, 'Only Super Admin can delete admins.');
        }

        if ($current->id === $admin->id) {
            Session::flash('error', 'You cannot delete your own account.');
            return redirect()->back();
        }

        $admin->delete();
        Session::flash('success', 'Admin moved to trash.');
        return redirect()->route('admin.users.index');
    }

    public function suspend(Admin $admin)
    {
        $current = auth()->guard('admin')->user();

        if ($current->role !== 'super_admin') {
            abort(403, 'Only Super Admin can suspend or activate admins.');
        }

        if ($current->id === $admin->id) {
            Session::flash('error', 'You cannot suspend or activate your own account.');
            return redirect()->back();
        }

        $admin->is_suspended = !$admin->is_suspended;
        $admin->save();

        $message = $admin->is_suspended
            ? 'Admin account has been suspended successfully.'
            : 'Admin account has been activated successfully.';

        Session::flash('success', $message);
        return redirect()->route('admin.users.index');
    }

    public function edit(Admin $admin)
    {
        $current = auth()->guard('admin')->user();

        if ($current->role !== 'super_admin' && $current->id !== $admin->id) {
            abort(403, 'ACCESS DENIED: SUPER ADMINS ONLY.');
        }

        return view('admin.users.editAdmin', compact('admin'));
    }

    public function update(Request $request, Admin $admin)
    {
        $current = auth()->guard('admin')->user();

        if ($current->role !== 'super_admin' && $current->id !== $admin->id) {
            abort(403, 'You are not authorized to update this profile.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
        ];

        if ($current->role === 'super_admin' && $request->has('role')) {
            $rules['role'] = 'required|in:admin,super_admin';
        }

        if ($request->filled('password')) {
            $rules['password'] = 'confirmed|min:6';
        }

        $validated = $request->validate($rules);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];

        if (!empty($validated['password'])) {
            $admin->password = bcrypt($validated['password']);
        }

        if ($current->role === 'super_admin' && isset($validated['role'])) {
            $admin->role = $validated['role'];
        }

        $admin->save();

        return redirect()->route('admin.users.index')->with('success', 'Admin profile updated successfully.');
    }

    public function trash()
    {
        $trashedAdmins = Admin::onlyTrashed()->orderByDesc('deleted_at')->paginate(10);

        return view('admin.users.trash', compact('trashedAdmins'));
    }

    public function restore($id)
    {
        $admin = Admin::onlyTrashed()->findOrFail($id);
        $admin->restore();

        return redirect()->route('admin.users.trash')->with('success', 'Admin restored successfully.');
    }

    public function forceDelete($id)
    {
        $admin = Admin::onlyTrashed()->findOrFail($id);
        $admin->forceDelete();

        return redirect()->route('admin.users.trash')->with('success', 'Admin permanently deleted.');
    }

    public function create()
    {
        $current = auth()->guard('admin')->user();

        if ($current->role !== 'super_admin') {
            abort(403, 'Only Super Admins can add new admins.');
        }

        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $current = auth()->guard('admin')->user();

        if ($current->role !== 'super_admin') {
            abort(403, 'Only Super Admins can add new admins.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,super_admin',
        ]);

        Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_suspended' => false,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Admin added successfully.');
    }
}
