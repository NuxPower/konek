<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Profile\UpdateProfileRequest;

class ClientProfileController extends Controller
{
    /**
     * Display the client's profile.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        return view('client.profile.show', compact('user'));
    }

    /**
     * Show the form for editing the client's profile.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        return view('client.profile.edit', compact('user'));
    }

    /**
     * Update the client's profile information.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $user->update($request->validated());
        return redirect()->route('client.profile.show')->with('success', 'Profile updated successfully.');
    }
} 