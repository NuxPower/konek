<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:4096'],
        ]);
        $user = $request->user();

        if ($user->resume_path) {
            Storage::disk('local')->delete($user->resume_path);
        }

        $path = $request->file('resume')->store("resumes/{$user->id}", 'local');
        $user->resume_path = $path;
        $user->save();

        return back()->with('success', 'Resume uploaded successfully.');
    }

    public function downloadResume(Request $request): StreamedResponse
    {
        abort_unless(
            $request->user()->resume_path && Storage::disk('local')->exists($request->user()->resume_path),
            404
        );

        return Storage::disk('local')->download(
            $request->user()->resume_path,
            'konek-resume.'.pathinfo($request->user()->resume_path, PATHINFO_EXTENSION)
        );
    }
}
