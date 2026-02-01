<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Permission;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        $users = $query->orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Toggle the status of a user.
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return redirect()->back()->with('success', 'User status updated successfully.');
    }

    /**
     * Show permissions management for a user.
     */
    public function permissions(User $user)
    {
        $permissions = Permission::all()->groupBy('module');
        return view('admin.users.permissions', compact('user', 'permissions'));
    }

    /**
     * Update user permissions.
     */
    public function updatePermissions(Request $request, User $user)
    {
        $user->permissions()->sync($request->permissions ?? []);

        return redirect()->route('admin.users.index')->with('success', 'User permissions updated successfully.');
    }
}
