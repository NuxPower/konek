<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{
    /**
     * Show the client dashboard.
     */
    public function index(Request $request)
    {
        // You can pass dashboard stats to the view if needed
        return view('client.dashboard');
    }

    /**
     * Return dashboard statistics (for API).
     */
    public function getStats(Request $request)
    {
        // Example stats (replace with real queries)
        $stats = [
            'jobs' => 10,
            'applications' => 30,
        ];
        return response()->json($stats);
    }
} 