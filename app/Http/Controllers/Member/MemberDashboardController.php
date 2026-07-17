<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;

class MemberDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $postedJobsQuery = Job::where('client_id', $user->id);
        $receivedApplicationsQuery = Application::whereHas(
            'job',
            fn ($query) => $query->where('client_id', $user->id)
        );
        $submittedApplicationsQuery = Application::where('freelancer_id', $user->id);

        $totalJobs = (clone $postedJobsQuery)->count();
        $publishedJobs = (clone $postedJobsQuery)->where('status', 'published')->count();
        $draftJobs = (clone $postedJobsQuery)->where('status', 'draft')->count();
        $receivedApplications = (clone $receivedApplicationsQuery)->count();
        $applicationsNeedingReview = (clone $receivedApplicationsQuery)
            ->whereIn('status', ['pending', 'reviewing'])
            ->count();

        $totalApplications = (clone $submittedApplicationsQuery)->count();
        $activeApplications = (clone $submittedApplicationsQuery)
            ->whereIn('status', ['pending', 'reviewing', 'shortlisted'])
            ->count();
        $shortlistedApplications = (clone $submittedApplicationsQuery)->where('status', 'shortlisted')->count();
        $acceptedApplications = (clone $submittedApplicationsQuery)->where('status', 'accepted')->count();
        $savedJobs = $user->savedJobs()->count();

        $availableJobsQuery = Job::query()
            ->where('status', 'published')
            ->where('client_id', '!=', $user->id)
            ->where(fn ($query) => $query->whereNull('deadline')->orWhere('deadline', '>', now()));

        $availableJobs = (clone $availableJobsQuery)->count();
        $appliedJobIds = (clone $submittedApplicationsQuery)->pluck('job_id');
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

        $reviewQueue = (clone $receivedApplicationsQuery)
            ->whereIn('status', ['pending', 'reviewing'])
            ->with(['job', 'freelancer'])
            ->latest()
            ->take(5)
            ->get();

        $recentJobs = (clone $postedJobsQuery)
            ->withCount('applications')
            ->latest()
            ->take(5)
            ->get();

        $recentApplications = (clone $submittedApplicationsQuery)
            ->with('job.client')
            ->latest()
            ->take(5)
            ->get();

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

        return view('member.dashboard', compact(
            'totalJobs',
            'publishedJobs',
            'draftJobs',
            'receivedApplications',
            'applicationsNeedingReview',
            'totalApplications',
            'activeApplications',
            'shortlistedApplications',
            'acceptedApplications',
            'savedJobs',
            'availableJobs',
            'reviewQueue',
            'recentJobs',
            'recentApplications',
            'recommendedJobs',
            'closingSoonJobs'
        ));
    }

    public function getStats(Request $request)
    {
        $user = $request->user();
        $submittedApplicationsQuery = Application::where('freelancer_id', $user->id);

        return response()->json([
            'posted_jobs' => Job::where('client_id', $user->id)->count(),
            'published_jobs' => Job::where('client_id', $user->id)->where('status', 'published')->count(),
            'received_applications' => Application::whereHas('job', fn ($query) => $query->where('client_id', $user->id))->count(),
            'applications_needing_review' => Application::whereHas('job', fn ($query) => $query->where('client_id', $user->id))
                ->whereIn('status', ['pending', 'reviewing'])
                ->count(),
            'submitted_applications' => (clone $submittedApplicationsQuery)->count(),
            'active_applications' => (clone $submittedApplicationsQuery)
                ->whereIn('status', ['pending', 'reviewing', 'shortlisted'])
                ->count(),
            'saved_jobs' => $user->savedJobs()->count(),
            'available_jobs' => Job::where('status', 'published')
                ->where('client_id', '!=', $user->id)
                ->where(fn ($query) => $query->whereNull('deadline')->orWhere('deadline', '>', now()))
                ->count(),
        ]);
    }
}
