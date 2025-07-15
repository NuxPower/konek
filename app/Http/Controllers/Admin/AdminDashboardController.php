<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(Request $request)
    {
        // You can pass dashboard stats to the view if needed
        return view('admin.dashboard');
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