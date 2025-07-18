<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ClientProfileController extends Controller
{
    public function complete()
    {
        $user = Auth::user();
        
        // Allow users to skip profile completion and go directly to dashboard
        // Only show completion form if they specifically want to complete it
        return view('client.profile.complete');
    }

    public function storeComplete(Request $request)
    {
        $user = Auth::user();

        // Simplified validation - only basic info required
        $request->validate([
            'display_name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            // Company info is now optional
            'company_name' => 'nullable|string|max:255',
            'company_description' => 'nullable|string|max:2000',
            'company_website' => 'nullable|url|max:255',
            'company_size' => 'nullable|in:startup,small,medium,large,enterprise',
            'industry' => 'nullable|string|max:255',
        ]);

        // Create or update client profile with minimal required data
        $client = $user->client ?: new Client(['user_id' => $user->id]);
        
        // Only save company info if provided
        $client->fill(array_filter($request->only([
            'company_name', 'company_description', 'company_website', 
            'company_size', 'industry'
        ])));
        
        $client->location = $request->location;
        $client->save();

        // Update user info
        $user->update([
            'name' => $request->display_name,
            'phone' => $request->phone,
            'bio' => $request->bio,
        ]);

        return redirect()->route('client.dashboard')
                        ->with('success', 'Profile updated successfully!');
    }

    public function show()
    {
        $client = Auth::user()->client;
        
        // Create a basic client profile if none exists
        if (!$client) {
            $client = $this->createBasicProfile();
        }

        $client->load('user');

        $stats = [
            'jobs_posted' => $client->jobs()->count(),
            'active_jobs' => $client->jobs()->whereIn('status', ['active', 'open'])->count(),
            'completed_jobs' => $client->jobs()->where('status', 'completed')->count(),
            'total_spent' => $client->total_spent ?? 0,
            'rating' => $client->rating ?? 0,
            'member_since' => $client->created_at->format('M Y'),
        ];

        return view('client.profile.show', compact('client', 'stats'));
    }

    public function edit()
    {
        $client = Auth::user()->client;
        
        // Create a basic client profile if none exists
        if (!$client) {
            $client = $this->createBasicProfile();
        }

        $client->load('user');

        return view('client.profile.edit', compact('client'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $client = $user->client;
        
        // Create a basic client profile if none exists
        if (!$client) {
            $client = $this->createBasicProfile();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            // Company fields are now optional
            'company_name' => 'nullable|string|max:255',
            'company_description' => 'nullable|string|max:2000',
            'company_website' => 'nullable|url|max:255',
            'company_size' => 'nullable|in:startup,small,medium,large,enterprise',
            'industry' => 'nullable|string|max:255',
            'verification_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        // Update user information
        $user->update($request->only([
            'name', 'email', 'phone', 'bio'
        ]));

        // Handle verification document upload
        $documentPath = null;
        if ($request->hasFile('verification_document')) {
            if ($client->verification_document) {
                Storage::delete($client->verification_document);
            }
            $documentPath = $request->file('verification_document')->store('verification_documents');
        }

        // Update client information (only if provided)
        $clientData = array_filter($request->only([
            'company_name', 'company_description', 'company_website', 
            'company_size', 'industry'
        ]));

        $clientData['location'] = $request->location;

        if ($documentPath) {
            $clientData['verification_document'] = $documentPath;
        }

        $client->update($clientData);

        return redirect()->route('client.profile.show')
                        ->with('success', 'Profile updated successfully!');
    }

    /**
     * Create a basic client profile with minimal required data
     */
    private function createBasicProfile()
    {
        $user = Auth::user();
        
        return Client::create([
            'user_id' => $user->id,
            'company_name' => $user->name . "'s Projects", // Default company name
            'location' => null,
            'company_description' => null,
            'company_website' => null,
            'company_size' => null,
            'industry' => null,
        ]);
    }

    // ... (keep the rest of your methods: uploadAvatar, deleteAvatar, etc.)
    
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();
        
        if ($user->avatar) {
            Storage::delete($user->avatar);
        }

        $avatarPath = $request->file('avatar')->store('avatars');
        $user->update(['avatar' => $avatarPath]);

        return back()->with('success', 'Avatar updated successfully!');
    }

    public function deleteAvatar()
    {
        $user = Auth::user();
        
        if ($user->avatar) {
            Storage::delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return back()->with('success', 'Avatar deleted successfully!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }
}