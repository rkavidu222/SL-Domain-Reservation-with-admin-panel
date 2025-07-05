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

        // Query admins (exclude super_admin if current is not super_admin)
        $query = ($current->role === 'super_admin')
            ? Admin::query()
            : Admin::where('role', '!=', 'super_admin');

        // Get all admins ascending by ID
        $admins = $query->orderBy('id', 'asc')->get();

        // Put current admin first
        $admins = $admins->sortBy(function ($admin) use ($current) {
            return $admin->id === $current->id ? 0 : 1;
        });

        // Manual pagination
        $perPage = 10;
        $page = request()->get('page', 1);
        $items = $admins->slice(($page - 1) * $perPage, $perPage)->values();

        $paginatedAdmins = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $admins->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.layouts.management.userManagement', ['admins' => $paginatedAdmins]);
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

        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
        ];

        // Only super_admin can update the role, and role must be in this list
        if ($current->role === 'super_admin' && $request->has('role')) {
            $rules['role'] = 'required|in:admin,super_admin';
        }

        // Password optional, but if present, confirmed and min length
        if ($request->filled('password')) {
            $rules['password'] = 'confirmed|min:6';
        }

        $validated = $request->validate($rules);

        // Update basic fields
        $admin->name = $validated['name'];
        $admin->email = $validated['email'];

        // Update password if provided
        if (!empty($validated['password'])) {
            $admin->password = bcrypt($validated['password']);
        }

        // Update role if current user is super_admin and role provided
        if ($current->role === 'super_admin' && isset($validated['role'])) {
            $admin->role = $validated['role'];
        }

        $admin->save();

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
