<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Freelancer;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FreelancerProfileController extends Controller
{
    public function show()
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            return redirect()->route('freelancer.profile.edit')
                           ->with('info', 'Please complete your freelancer profile.');
        }

        $freelancer->load(['skills', 'applications.job']);

        // Calculate additional statistics
        $stats = [
            'profile_completion' => $this->calculateProfileCompletion($freelancer),
            'recent_applications' => $freelancer->applications()->latest()->take(5)->get(),
            'skills_count' => $freelancer->skills()->count(),
            'average_proposal_rate' => $this->calculateAverageProposalRate($freelancer),
        ];

        return view('freelancer.profile.show', compact('freelancer', 'stats'));
    }

    public function edit()
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            $freelancer = Auth::user()->getFreelancerProfile();
        }

        $freelancer->load('skills');
        $skills = Skill::all();

        return view('freelancer.profile.edit', compact('freelancer', 'skills'));
    }

    public function update(Request $request)
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            $freelancer = Auth::user()->getFreelancerProfile();
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'bio' => 'required|string|max:2000',
            'hourly_rate' => 'required|numeric|min:0|max:9999.99',
            'experience_level' => ['required', Rule::in(['entry', 'intermediate', 'expert'])],
            'availability' => ['required', Rule::in(['full-time', 'part-time', 'occasional', 'unavailable'])],
            'location' => 'nullable|string|max:255',
            'languages' => 'nullable|array',
            'languages.*' => 'string|max:100',
            'portfolio_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'skills' => 'required|array|min:1',
            'skills.*' => 'exists:skills,id',
            'skill_proficiency' => 'required|array',
            'skill_proficiency.*' => ['required', Rule::in(['basic', 'intermediate', 'advanced', 'expert'])],
            'skill_experience' => 'required|array',
            'skill_experience.*' => 'required|integer|min:0|max:50',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        // Handle avatar upload
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($freelancer->avatar) {
                Storage::delete($freelancer->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('freelancer_avatars');
        }

        // Update freelancer profile
        $freelancer->update([
            'title' => $request->title,
            'bio' => $request->bio,
            'hourly_rate' => $request->hourly_rate,
            'experience_level' => $request->experience_level,
            'availability' => $request->availability,
            'location' => $request->location,
            'languages' => $request->languages,
            'portfolio_url' => $request->portfolio_url,
            'linkedin_url' => $request->linkedin_url,
            'github_url' => $request->github_url,
            'is_available' => $request->has('is_available'),
            'avatar' => $avatarPath ?? $freelancer->avatar,
        ]);

        // Update skills
        $skillsData = [];
        foreach ($request->skills as $index => $skillId) {
            $skillsData[$skillId] = [
                'proficiency_level' => $request->skill_proficiency[$index] ?? 'basic',
                'years_experience' => $request->skill_experience[$index] ?? 0,
            ];
        }
        
        $freelancer->skills()->sync($skillsData);

        return redirect()->route('freelancer.profile.show')
                        ->with('success', 'Profile updated successfully!');
    }

    public function portfolio()
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            return redirect()->route('freelancer.profile.edit')
                           ->with('error', 'Please complete your freelancer profile first.');
        }

        // This would typically load portfolio items from a separate model
        // For now, we'll return a placeholder
        $portfolioItems = collect();

        return view('freelancer.profile.portfolio', compact('freelancer', 'portfolioItems'));
    }

    public function addPortfolioItem(Request $request)
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            return response()->json(['error' => 'Freelancer profile not found'], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'url' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        // This would typically create a portfolio item in a separate model
        // For now, we'll return a success response
        return response()->json(['success' => true, 'message' => 'Portfolio item added successfully']);
    }

    public function removePortfolioItem($id)
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            return response()->json(['error' => 'Freelancer profile not found'], 404);
        }

        // This would typically delete a portfolio item
        return response()->json(['success' => true, 'message' => 'Portfolio item removed successfully']);
    }

    public function verification()
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            return redirect()->route('freelancer.profile.edit')
                           ->with('error', 'Please complete your freelancer profile first.');
        }

        return view('freelancer.profile.verification', compact('freelancer'));
    }

    public function submitVerification(Request $request)
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            return redirect()->route('freelancer.profile.edit')
                           ->with('error', 'Please complete your freelancer profile first.');
        }

        $request->validate([
            'student_id' => 'required|string|max:50',
            'id_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'verification_type' => 'required|in:student_id,passport,drivers_license',
        ]);

        // Handle document upload
        $documentPath = $request->file('id_document')->store('verification_documents');

        // This would typically create a verification request record
        // For now, we'll update the freelancer with verification pending status
        $freelancer->update([
            'verification_status' => 'pending',
            'verification_document' => $documentPath,
            'student_id' => $request->student_id,
        ]);

        return redirect()->route('freelancer.profile.verification')
                        ->with('success', 'Verification documents submitted successfully. We will review your submission within 24-48 hours.');
    }

    public function settings()
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            return redirect()->route('freelancer.profile.edit')
                           ->with('error', 'Please complete your freelancer profile first.');
        }

        return view('freelancer.profile.settings', compact('freelancer'));
    }

    public function updateSettings(Request $request)
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            return redirect()->route('freelancer.profile.edit')
                           ->with('error', 'Please complete your freelancer profile first.');
        }

        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'job_alerts' => 'boolean',
            'marketing_emails' => 'boolean',
            'privacy_level' => 'required|in:public,private,connections',
            'auto_apply' => 'boolean',
            'response_time_hours' => 'required|integer|min:1|max:168',
        ]);

        // This would typically update user preferences in a separate model
        // For now, we'll update some basic settings on the freelancer model
        $freelancer->update([
            'response_time' => $request->response_time_hours,
            'privacy_level' => $request->privacy_level,
            'auto_apply_enabled' => $request->has('auto_apply'),
        ]);

        return redirect()->route('freelancer.profile.settings')
                        ->with('success', 'Settings updated successfully!');
    }

    public function toggleAvailability()
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            return response()->json(['error' => 'Freelancer profile not found'], 404);
        }

        $freelancer->toggleAvailability();

        return response()->json([
            'success' => true,
            'is_available' => $freelancer->is_available,
            'message' => $freelancer->is_available ? 'You are now available for work' : 'You are now unavailable for work'
        ]);
    }

    public function analytics()
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            return redirect()->route('freelancer.profile.edit')
                           ->with('error', 'Please complete your freelancer profile first.');
        }

        $analytics = [
            'profile_views' => $this->getProfileViews($freelancer),
            'application_success_rate' => $this->calculateApplicationSuccessRate($freelancer),
            'earnings_trend' => $this->getEarningsTrend($freelancer),
            'skill_demand' => $this->getSkillDemand($freelancer),
            'response_time_analysis' => $this->getResponseTimeAnalysis($freelancer),
        ];

        return view('freelancer.profile.analytics', compact('freelancer', 'analytics'));
    }

    public function exportData()
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            return redirect()->route('freelancer.profile.edit')
                           ->with('error', 'Please complete your freelancer profile first.');
        }

        // This would typically generate and download a data export
        // For now, we'll return a placeholder response
        return response()->json(['message' => 'Data export will be available soon']);
    }

    private function calculateProfileCompletion($freelancer)
    {
        $completionScore = 0;
        $totalFields = 10;

        // Basic info
        if ($freelancer->title) $completionScore++;
        if ($freelancer->bio) $completionScore++;
        if ($freelancer->hourly_rate) $completionScore++;
        if ($freelancer->experience_level) $completionScore++;
        if ($freelancer->availability) $completionScore++;
        
        // Optional but important
        if ($freelancer->location) $completionScore++;
        if ($freelancer->portfolio_url) $completionScore++;
        if ($freelancer->skills()->count() > 0) $completionScore++;
        if ($freelancer->languages && count($freelancer->languages) > 0) $completionScore++;
        if ($freelancer->is_verified) $completionScore++;

        return round(($completionScore / $totalFields) * 100);
    }

    private function calculateAverageProposalRate($freelancer)
    {
        $applications = $freelancer->applications();
        return $applications->count() > 0 ? 
               round($applications->avg('proposed_rate'), 2) : 0;
    }

    private function calculateApplicationSuccessRate($freelancer)
    {
        $totalApplications = $freelancer->applications()->count();
        $acceptedApplications = $freelancer->applications()->where('status', 'accepted')->count();
        
        return $totalApplications > 0 ? 
               round(($acceptedApplications / $totalApplications) * 100, 2) : 0;
    }

    private function getProfileViews($freelancer)
    {
        // This would typically query a profile_views table
        return [
            'total' => 150,
            'this_week' => 25,
            'last_week' => 30,
        ];
    }

    private function getEarningsTrend($freelancer)
    {
        // This would typically query earnings data
        return [
            'current_month' => 0,
            'last_month' => 0,
            'trend' => 'stable',
        ];
    }

    private function getSkillDemand($freelancer)
    {
        // This would typically analyze job postings for skill demand
        return $freelancer->skills->map(function($skill) {
            return [
                'name' => $skill->name,
                'demand' => rand(1, 10), // Placeholder
                'avg_rate' => rand(15, 50), // Placeholder
            ];
        });
    }

    private function getResponseTimeAnalysis($freelancer)
    {
        // This would typically analyze response times
        return [
            'average_response_time' => $freelancer->response_time ?? 24,
            'target_response_time' => 2,
            'improvement_suggestion' => 'Try to respond within 2 hours for better client satisfaction',
        ];
    }
}