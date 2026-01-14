<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AccountController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = \App\Models\User::all();

        // Log the access to user management
        \App\Services\LoggingService::logCustom(
            'view_users',
            'users',
            null,
            null,
            null,
            "Accessed user management page - viewed " . $users->count() . " user accounts"
        );

        return Inertia::render('Settings/ManageAccounts', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Check if current user has admin role
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('settings.manage-accounts')
                ->with('error', 'You do not have permission to create user accounts.');
        }

        // Log the access to add user form
        \App\Services\LoggingService::logCustom(
            'access_add_user_form',
            'users',
            null,
            null,
            null,
            "Accessed add new user account form"
        );

        return Inertia::render('Settings/AddAccounts');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // Check if current user has admin role
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('settings.manage-accounts')
                ->with('error', 'You do not have permission to create user accounts.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,staff,legislator',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        // Log the user creation
        \App\Services\LoggingService::logCreate(
            $user,
            "Created new user account: {$user->name} ({$user->username})"
        );

        return redirect()->route('settings.manage-accounts')
            ->with('success', 'User account created successfully.');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        // Check if current user has admin role
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('settings.manage-accounts')
                ->with('error', 'You do not have permission to update user accounts.');
        }

        $user = \App\Models\User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['admin', 'staff', 'legislator'])],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $oldValues = $user->getAttributes();

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Log the user update
        \App\Services\LoggingService::logUpdate(
            $user,
            $oldValues,
            "Updated user account: {$user->name} ({$user->username})"
        );

        return redirect()->route('settings.manage-accounts')
            ->with('success', 'User account updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        // Check if current user has admin role
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('settings.manage-accounts')
                ->with('error', 'You do not have permission to delete user accounts.');
        }

        $user = \App\Models\User::findOrFail($id);
        
        // Prevent deleting the current admin user
        if ($user->id === auth()->id()) {
            return redirect()->route('settings.manage-accounts')
                ->with('error', 'You cannot delete your own account.');
        }
        
        // Log the user deletion before deleting
        \App\Services\LoggingService::logDelete(
            $user,
            "Deleted user account: {$user->name} ({$user->username})"
        );
        
        $user->delete();

        return redirect()->route('settings.manage-accounts')
            ->with('success', 'User account deleted successfully.');
    }

    /**
     * Show the change password form.
     */
    public function changePassword()
    {
        // Log the access to change password form
        \App\Services\LoggingService::logCustom(
            'access_change_password_form',
            'users',
            auth()->id(),
            null,
            null,
            "Accessed change password form"
        );

        return Inertia::render('Settings/ChangePassword');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        // Verify current password
        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password
        $user->update([
            'password' => bcrypt($request->password),
        ]);

        // Log the password change
        \App\Services\LoggingService::logCustom(
            'password_change',
            'users',
            $user->id,
            null,
            ['password_changed' => true],
            "User {$user->name} ({$user->username}) changed their password"
        );

        return back()->with('success', 'Password changed successfully.');
    }
}
