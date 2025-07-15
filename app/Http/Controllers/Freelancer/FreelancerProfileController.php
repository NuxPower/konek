<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Profile\UpdateProfileRequest;

class FreelancerProfileController extends Controller
{
    /**
     * Display the freelancer's profile.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        return view('freelancer.profile.show', compact('user'));
    }

    /**
     * Show the form for editing the freelancer's profile.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        return view('freelancer.profile.edit', compact('user'));
    }

    /**
     * Update the freelancer's profile information.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $user->update($request->validated());
        return redirect()->route('freelancer.profile.show')->with('success', 'Profile updated successfully.');
    }

    /**
     * Upload resume for the freelancer.
     */
    public function uploadResume(Request $request)
    {
        $request->validate([
            'resume' => 'required|mimes:pdf,doc,docx|max:4096',
        ]);
        $user = $request->user();
        $path = $request->file('resume')->store('resumes', 'public');
        $user->resume = $path;
        $user->save();
        return back()->with('success', 'Resume uploaded successfully.');
    }
} 