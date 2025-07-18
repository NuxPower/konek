<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // User-focused statistics only
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_clients' => User::where('role', 'client')->count(),
            'total_freelancers' => User::where('role', 'freelancer')->count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'unverified_users' => User::whereNull('email_verified_at')->count(),
            'users_today' => User::whereDate('created_at', today())->count(),
            'users_this_week' => User::where('created_at', '>=', Carbon::now()->subWeek())->count(),
            'users_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        // Recent user registrations (last 10)
        $recentUsers = User::orderBy('created_at', 'desc')
                          ->take(10)
                          ->get();

        // User registration trends (last 30 days)
        $userTrends = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                         ->where('created_at', '>=', Carbon::now()->subDays(30))
                         ->groupBy('date')
                         ->orderBy('date')
                         ->get();

        // User role distribution
        $roleDistribution = User::selectRaw('role, COUNT(*) as count')
                               ->groupBy('role')
                               ->get();

        // User activity status
        $userActivity = [
            'active_today' => User::where('last_login_at', '>=', Carbon::today())->count(),
            'active_this_week' => User::where('last_login_at', '>=', Carbon::now()->subWeek())->count(),
            'active_this_month' => User::where('last_login_at', '>=', Carbon::now()->subMonth())->count(),
            'never_logged_in' => User::whereNull('last_login_at')->count(),
        ];

        // Recent user activity (users who logged in recently)
        $recentActivity = User::whereNotNull('last_login_at')
                             ->orderBy('last_login_at', 'desc')
                             ->take(10)
                             ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'recentUsers', 
            'userTrends', 
            'roleDistribution', 
            'userActivity',
            'recentActivity'
        ));
    }
}