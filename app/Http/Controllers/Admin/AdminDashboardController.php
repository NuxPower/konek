<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(Request $request)
    {
        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalClients = User::where('role', 'client')->count();
        $totalFreelancers = User::where('role', 'freelancer')->count();
        $totalJobs = Job::count();
        $totalApplications = Application::count();
        $recentUsers = User::latest()->take(5)->get();
        $recentJobs = Job::latest()->take(5)->get();
        $recentApplications = Application::latest()->with('job', 'freelancer')->take(5)->get();
        $recentActivities = ActivityLog::with('causer')->latest()->take(5)->get();

        // Monthly user registrations (last 6 months)
        $userTrends = User::select(DB::raw("DATE_FORMAT(created_at, '%b %Y') as month"), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderByRaw("MIN(created_at)")
            ->pluck('count', 'month');
        $jobTrends = Job::select(DB::raw("DATE_FORMAT(created_at, '%b %Y') as month"), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderByRaw("MIN(created_at)")
            ->pluck('count', 'month');
        // Fill missing months with 0
        $months = collect(range(0, 5))->map(function($i) {
            return now()->subMonths(5 - $i)->format('M Y');
        });
        $userTrendData = $months->map(fn($m) => $userTrends[$m] ?? 0);
        $jobTrendData = $months->map(fn($m) => $jobTrends[$m] ?? 0);

        return view('admin.dashboard', compact(
            'totalUsers', 'totalAdmins', 'totalClients', 'totalFreelancers',
            'totalJobs', 'totalApplications',
            'recentUsers', 'recentJobs', 'recentApplications', 'recentActivities',
            'months', 'userTrendData', 'jobTrendData'
        ));
    }

    /**
     * Return dashboard statistics (for API).
     */
    public function getStats(Request $request)
    {
        // Example stats (replace with real queries)
        $stats = [
            'users' => 100,
            'jobs' => 50,
            'applications' => 200,
        ];
        return response()->json($stats);
    }
} 