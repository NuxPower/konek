<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{
    /**
     * Show the client dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $jobsQuery = Job::where('client_id', $user->id);
        $applicationsQuery = Application::whereHas(
            'job',
            fn ($query) => $query->where('client_id', $user->id)
        );

        $totalJobs = (clone $jobsQuery)->count();
        $publishedJobs = (clone $jobsQuery)->where('status', 'published')->count();
        $draftJobs = (clone $jobsQuery)->where('status', 'draft')->count();
        $totalApplications = (clone $applicationsQuery)->count();
        $applicationsNeedingReview = (clone $applicationsQuery)
            ->whereIn('status', ['pending', 'reviewing'])
            ->count();
        $shortlistedApplications = (clone $applicationsQuery)
            ->where('status', 'shortlisted')
            ->count();

        $reviewQueue = (clone $applicationsQuery)
            ->whereIn('status', ['pending', 'reviewing'])
            ->with(['job', 'freelancer'])
            ->latest()
            ->take(6)
            ->get();
        $upcomingDeadlines = (clone $jobsQuery)
            ->where('status', 'published')
            ->whereBetween('deadline', [now(), now()->addDays(14)])
            ->withCount('applications')
            ->orderBy('deadline')
            ->take(5)
            ->get();
        $draftsNeedingAction = (clone $jobsQuery)
            ->where('status', 'draft')
            ->latest('updated_at')
            ->take(4)
            ->get();
        $recentJobs = (clone $jobsQuery)
            ->withCount('applications')
            ->latest()
            ->take(5)
            ->get();

        return view('client.dashboard', compact(
            'totalJobs',
            'publishedJobs',
            'draftJobs',
            'totalApplications',
            'applicationsNeedingReview',
            'shortlistedApplications',
            'reviewQueue',
            'upcomingDeadlines',
            'draftsNeedingAction',
            'recentJobs'
        ));
    }

    /**
     * Return dashboard statistics (for API).
     */
    public function getStats(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'jobs' => Job::where('client_id', $user->id)->count(),
            'published_jobs' => Job::where('client_id', $user->id)->where('status', 'published')->count(),
            'applications' => Application::whereHas('job', fn ($query) => $query->where('client_id', $user->id))->count(),
            'applications_needing_review' => Application::whereHas('job', fn ($query) => $query->where('client_id', $user->id))
                ->whereIn('status', ['pending', 'reviewing'])
                ->count(),
        ]);
    }
}
