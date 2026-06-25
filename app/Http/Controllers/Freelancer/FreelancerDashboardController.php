<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;

class FreelancerDashboardController extends Controller
{
    /**
     * Show the freelancer dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $applicationsQuery = Application::where('freelancer_id', $user->id);

        $totalApplications = (clone $applicationsQuery)->count();
        $activeApplications = (clone $applicationsQuery)
            ->whereIn('status', ['pending', 'reviewing', 'shortlisted'])
            ->count();
        $shortlistedApplications = (clone $applicationsQuery)->where('status', 'shortlisted')->count();
        $acceptedApplications = (clone $applicationsQuery)->where('status', 'accepted')->count();
        $savedJobs = $user->savedJobs()->count();
        $availableJobsQuery = Job::query()
            ->where('status', 'published')
            ->where(fn ($query) => $query->whereNull('deadline')->orWhere('deadline', '>', now()));
        $availableJobs = (clone $availableJobsQuery)->count();
        $recentApplications = (clone $applicationsQuery)
            ->with('job.client')
            ->latest()
            ->take(5)
            ->get();

        $appliedJobIds = (clone $applicationsQuery)->pluck('job_id');
        $skillIds = $user->skills()->pluck('skills.id');
        $recommendedJobsQuery = (clone $availableJobsQuery)
            ->whereNotIn('id', $appliedJobIds)
            ->with(['client', 'category', 'skills']);

        if ($skillIds->isNotEmpty()) {
            $recommendedJobsQuery
                ->withCount([
                    'skills as matching_skills_count' => fn ($query) => $query->whereIn('skills.id', $skillIds),
                ])
                ->orderByDesc('matching_skills_count');
        }

        $recommendedJobs = $recommendedJobsQuery
            ->latest('published_at')
            ->take(6)
            ->get();
        $closingSoonJobs = (clone $availableJobsQuery)
            ->whereNotIn('id', $appliedJobIds)
            ->whereBetween('deadline', [now(), now()->addDays(7)])
            ->with('client')
            ->orderBy('deadline')
            ->take(4)
            ->get();

        return view('freelancer.dashboard', compact(
            'totalApplications',
            'activeApplications',
            'shortlistedApplications',
            'acceptedApplications',
            'savedJobs',
            'availableJobs',
            'recentApplications',
            'recommendedJobs',
            'closingSoonJobs'
        ));
    }

    /**
     * Return dashboard statistics (for API).
     */
    public function getStats(Request $request)
    {
        $user = $request->user();
        $applicationsQuery = Application::where('freelancer_id', $user->id);

        return response()->json([
            'applications' => (clone $applicationsQuery)->count(),
            'active_applications' => (clone $applicationsQuery)
                ->whereIn('status', ['pending', 'reviewing', 'shortlisted'])
                ->count(),
            'shortlisted_applications' => (clone $applicationsQuery)->where('status', 'shortlisted')->count(),
            'accepted_applications' => (clone $applicationsQuery)->where('status', 'accepted')->count(),
            'saved_jobs' => $user->savedJobs()->count(),
            'available_jobs' => Job::where('status', 'published')
                ->where(fn ($query) => $query->whereNull('deadline')->orWhere('deadline', '>', now()))
                ->count(),
        ]);
    }
}
