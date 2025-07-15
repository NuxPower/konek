<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FreelancerDashboardController extends Controller
{
    /**
     * Show the freelancer dashboard.
     */
    public function index(Request $request)
    {
        // You can pass dashboard stats to the view if needed
        return view('freelancer.dashboard');
    }

    /**
     * Return dashboard statistics (for API).
     */
    public function getStats(Request $request)
    {
        // Example stats (replace with real queries)
        $stats = [
            'jobs_applied' => 5,
            'jobs_saved' => 3,
        ];
        return response()->json($stats);
    }
} 