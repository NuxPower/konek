<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminProfileController extends Controller
{
    /**
     * Display the admin's profile.
     */
    public function show()
    {
        $admin = Auth::user();
        
        return view('admin.profile.show', compact('admin'));
    }

    /**
     * Show the form for editing the admin's profile.
     */
    public function edit()
    {
        $admin = Auth::user();
        
        return view('admin.profile.edit', compact('admin'));
    }

    /**
     * Update the admin's profile information.
     */
    public function update(Request $request)
    {
        $admin = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users')->ignore($admin->id)
            ],
            'current_password' => ['nullable', 'required_with:password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        // Check current password if new password is provided
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $admin->password)) {
                return back()->withErrors([
                    'current_password' => 'The current password is incorrect.'
                ]);
            }
        }

        // Update basic information
        $admin->name = $validated['name'];
        $admin->email = $validated['email'];

        // Update password if provided
        if ($request->filled('password')) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        return redirect()->route('admin.profile.show')
                        ->with('success', 'Profile updated successfully.');
    }
}