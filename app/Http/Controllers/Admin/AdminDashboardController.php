<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(Request $request)
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $totalClients = User::where('role', 'client')->count();
        $totalFreelancers = User::where('role', 'freelancer')->count();
        $totalJobs = Job::count();
        $publishedJobs = Job::where('status', 'published')->count();
        $totalApplications = Application::count();
        $applicationsNeedingReview = Application::whereIn('status', ['pending', 'reviewing'])->count();
        $acceptedApplications = Application::where('status', 'accepted')->count();
        $acceptanceRate = $totalApplications > 0
            ? round(($acceptedApplications / $totalApplications) * 100)
            : 0;

        $reviewQueue = Application::query()
            ->whereIn('status', ['pending', 'reviewing'])
            ->with(['job.client', 'freelancer'])
            ->latest()
            ->take(6)
            ->get();
        $upcomingDeadlines = Job::query()
            ->where('status', 'published')
            ->whereBetween('deadline', [now(), now()->addDays(14)])
            ->with('client')
            ->withCount('applications')
            ->orderBy('deadline')
            ->take(6)
            ->get();
        $jobStatusCounts = Job::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $applicationStatusCounts = Application::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $recentActivities = ActivityLog::with('causer')->latest()->take(5)->get();

        $periods = collect(range(0, 5))->map(function ($i) {
            $start = now()->subMonths(5 - $i)->startOfMonth();

            return [
                'label' => $start->format('M Y'),
                'start' => $start,
                'end' => $start->copy()->endOfMonth(),
            ];
        });
        $months = $periods->pluck('label');
        $userTrendData = $periods->map(fn ($period) => User::whereBetween('created_at', [$period['start'], $period['end']])->count());
        $jobTrendData = $periods->map(fn ($period) => Job::whereBetween('created_at', [$period['start'], $period['end']])->count());

        return view('admin.dashboard', compact(
            'totalUsers', 'activeUsers', 'totalClients', 'totalFreelancers',
            'totalJobs', 'publishedJobs', 'totalApplications', 'applicationsNeedingReview',
            'acceptedApplications', 'acceptanceRate', 'reviewQueue', 'upcomingDeadlines',
            'jobStatusCounts', 'applicationStatusCounts', 'recentActivities',
            'months', 'userTrendData', 'jobTrendData'
        ));
    }

    /**
     * Return dashboard statistics (for API).
     */
    public function getStats(Request $request)
    {
        return response()->json([
            'users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'jobs' => Job::count(),
            'published_jobs' => Job::where('status', 'published')->count(),
            'applications' => Application::count(),
            'applications_needing_review' => Application::whereIn('status', ['pending', 'reviewing'])->count(),
        ]);
    }
}
