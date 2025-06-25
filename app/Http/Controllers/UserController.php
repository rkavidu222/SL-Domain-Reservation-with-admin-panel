<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function index()
    {
        $current = auth()->guard('admin')->user();

        $admins = ($current->role === 'super_admin')
            ? Admin::latest()->paginate(10)
            : Admin::where('role', '!=', 'super_admin')->latest()->paginate(10);

        return view('admin.layouts.userManagement', compact('admins'));
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

        return view('admin.layouts.editAdmin', compact('admin'));
    }

    public function update(Request $request, Admin $admin)
    {
        $current = auth()->guard('admin')->user();

        if ($current->role !== 'super_admin' && $current->id !== $admin->id) {
            abort(403, 'You are not authorized to update this profile.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
        ]);

        $admin->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'Admin profile updated successfully.');
    }

    public function trash()
    {
        $trashedAdmins = Admin::onlyTrashed()->paginate(10);
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
}
