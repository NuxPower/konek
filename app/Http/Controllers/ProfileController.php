<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $user->update($request->validated());
        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();
        Auth::logout();
        $user->delete();
        return redirect('/')->with('success', 'Account deleted successfully.');
    }

    /**
     * Upload avatar for the user.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);
        $user = $request->user();
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();
        return back()->with('success', 'Avatar uploaded successfully.');
    }
} 