<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

class HomeController extends Controller
{
    /**
     * Show the application welcome/landing page.
     */
    public function index(Request $request)
    {
        $jobs = Job::latest()->paginate(10);
        return view('jobs.index', compact('jobs'));
    }
} 