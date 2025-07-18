<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use App\Models\Freelancer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FreelancerDashboardController extends Controller
{
    public function index()
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            $freelancer = Auth::user()->getFreelancerProfile();
        }

        // If freelancer is still null, handle it appropriately
        if (!$freelancer) {
            // Option 1: Create a freelancer profile automatically
            $freelancer = $this->createFreelancerProfile(Auth::user());
            
            // Option 2: Redirect to profile creation page (uncomment if preferred)
            // return redirect()->route('freelancer.profile.create')
            //     ->with('error', 'Please complete your freelancer profile first.');
        }

        $stats = [
            'applications_sent' => $freelancer->applications()->count(),
            'pending_applications' => $freelancer->applications()->pending()->count(),
            'accepted_applications' => $freelancer->applications()->accepted()->count(),
            'jobs_completed' => $freelancer->total_jobs_completed ?? 0,
            'total_earned' => $freelancer->total_earnings ?? 0,
            'rating' => $freelancer->rating ?? 0,
            'success_rate' => $freelancer->success_rate ?? 0,
            'response_time' => $freelancer->response_time ?? 0,
        ];

        // Recent applications - FIXED: Removed .user from job.client.user
        $recentApplications = $freelancer->applications()
                                       ->with(['job.client'])  // ✅ Fixed: client IS the user
                                       ->latest()
                                       ->take(5)
                                       ->get();

        // Available jobs matching freelancer's skills - FIXED: Removed .user from client.user
        $suggestedJobs = Job::active()
                           ->whereHas('skills', function($q) use ($freelancer) {
                               $q->whereIn('skills.id', $freelancer->skills()->pluck('skills.id'));
                           })
                           ->with(['client', 'skills'])  // ✅ Fixed: client IS the user
                           ->latest()
                           ->take(5)
                           ->get();

        // Application statistics
        $applicationStats = [
            'this_month' => $freelancer->applications()->whereMonth('created_at', now()->month)->count(),
            'last_month' => $freelancer->applications()->whereMonth('created_at', now()->subMonth()->month)->count(),
            'acceptance_rate' => $this->calculateAcceptanceRate($freelancer),
        ];

        // Earnings this month
        $earningsThisMonth = $this->calculateEarningsThisMonth($freelancer);

        return view('freelancer.dashboard', compact(
            'stats', 'recentApplications', 'suggestedJobs', 
            'applicationStats', 'earningsThisMonth'
        ));
    }

    /**
     * Create a basic freelancer profile for the user
     */
    private function createFreelancerProfile($user)
    {
        return Freelancer::create([
            'user_id' => $user->id,
            'title' => 'Freelancer', // Default title
            'bio' => '', // Empty bio to be filled later
            'experience_level' => 'beginner', // Default experience level
            'availability' => 'full_time', // Default availability
            'is_available' => true,
            'is_verified' => false,
            'rating' => 0,
            'total_jobs_completed' => 0,
            'total_earnings' => 0,
            'success_rate' => 0,
            'response_time' => 0,
            'hourly_rate' => 0,
        ]);
    }

    private function calculateAcceptanceRate($freelancer)
    {
        if (!$freelancer) {
            return 0;
        }
        
        $totalApplications = $freelancer->applications()->count();
        $acceptedApplications = $freelancer->applications()->accepted()->count();
        
        return $totalApplications > 0 ? round(($acceptedApplications / $totalApplications) * 100, 2) : 0;
    }

    private function calculateEarningsThisMonth($freelancer)
    {
        if (!$freelancer) {
            return 0;
        }
        
        // This would need to be implemented based on your payment/earnings tracking system
        // For now, we'll return a placeholder
        return 0;
    }

    public function analytics()
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            $freelancer = Auth::user()->getFreelancerProfile();
        }

        // Handle null freelancer for analytics too
        if (!$freelancer) {
            return redirect()->route('freelancer.dashboard')
                ->with('error', 'Please complete your freelancer profile first.');
        }
        
        $analytics = [
            'application_trends' => $this->getApplicationTrendsData($freelancer),
            'earnings_overview' => $this->getEarningsOverviewData($freelancer),
            'skill_performance' => $this->getSkillPerformanceData($freelancer),
            'client_feedback' => $this->getClientFeedbackData($freelancer),
        ];

        return view('freelancer.analytics', compact('analytics'));
    }

    private function getApplicationTrendsData($freelancer)
    {
        if (!$freelancer) {
            return collect();
        }
        
        return $freelancer->applications()
                         ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                         ->where('created_at', '>=', now()->subDays(30))
                         ->groupBy('date')
                         ->orderBy('date')
                         ->get();
    }

    private function getEarningsOverviewData($freelancer)
    {
        if (!$freelancer) {
            return [
                'total_earnings' => 0,
                'jobs_completed' => 0,
                'average_job_value' => 0,
                'hourly_rate' => 0,
            ];
        }
        
        return [
            'total_earnings' => $freelancer->total_earnings ?? 0,
            'jobs_completed' => $freelancer->total_jobs_completed ?? 0,
            'average_job_value' => ($freelancer->total_jobs_completed ?? 0) > 0 ? 
                                  ($freelancer->total_earnings ?? 0) / ($freelancer->total_jobs_completed ?? 1) : 0,
            'hourly_rate' => $freelancer->hourly_rate ?? 0,
        ];
    }

    private function getSkillPerformanceData($freelancer)
    {
        if (!$freelancer) {
            return collect();
        }
        
        return $freelancer->skills()
                         ->withPivot('proficiency_level', 'years_experience')
                         ->get()
                         ->map(function($skill) {
                             return [
                                 'name' => $skill->name,
                                 'proficiency' => $skill->pivot->proficiency_level ?? 'beginner',
                                 'experience' => $skill->pivot->years_experience ?? 0,
                             ];
                         });
    }

    private function getClientFeedbackData($freelancer)
    {
        if (!$freelancer) {
            return [
                'average_rating' => 0,
                'total_reviews' => 0,
                'positive_feedback' => 0,
                'repeat_clients' => 0,
            ];
        }
        
        // This would need to be implemented based on your feedback/rating system
        return [
            'average_rating' => $freelancer->rating ?? 0,
            'total_reviews' => $freelancer->total_jobs_completed ?? 0,
            'positive_feedback' => 85, // Placeholder
            'repeat_clients' => 12, // Placeholder
        ];
    }
}