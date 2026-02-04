<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Permission;
use App\Mail\AccountActivated;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();
        $tab = $request->get('tab', 'active');

        if ($tab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($tab === 'deactivated') {
            $query->where('status', 'deactivated');
        } else {
            $query->where('status', 'active');
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->get();
        
        // Counts for tabs
        $activeCount = User::where('status', 'active')->count();
        $pendingCount = User::where('status', 'pending')->count();
        $deactivatedCount = User::where('status', 'deactivated')->count();

        return view('admin.users.index', compact('users', 'activeCount', 'pendingCount', 'deactivatedCount', 'tab'));
    }

    /**
     * Toggle the status of a user (Active/Deactivated).
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->status = $user->status === 'active' ? 'deactivated' : 'active';
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
        
        // Update the permissions fingerprint for real-time sync
        $user->updatePermissionsHash();

        return redirect()->route('admin.users.index')->with('success', 'User permissions updated successfully.');
    }

    /**
     * Activate a pending user.
     */
    public function activate(User $user)
    {
        $user->status = 'active';
        $user->save();

        try {
            Mail::to($user->email)->send(new AccountActivated($user));
        } catch (\Exception $e) {
            // Silently fail if mail is not configured or fails
            \Illuminate\Support\Facades\Log::error('Activation email failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'User account has been activated and notification email sent.');
    }
}
