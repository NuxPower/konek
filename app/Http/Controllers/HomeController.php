<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

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
