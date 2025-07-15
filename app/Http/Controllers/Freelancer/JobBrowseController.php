<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobBrowseController extends Controller
{
    /**
     * Display a listing of jobs for freelancers (authenticated).
     */
    public function index(Request $request)
    {
        $jobs = Job::where('status', 'published')->with('category', 'client')->paginate(15);
        return view('freelancer.jobs.index', compact('jobs'));
    }

    /**
     * Display the specified job for freelancers (authenticated).
     */
    public function show(Job $job)
    {
        $job->load('category', 'client', 'skills');
        return view('freelancer.jobs.show', compact('job'));
    }

    /**
     * Search jobs for freelancers (authenticated).
     */
    public function search(Request $request)
    {
        $query = Job::query()->where('status', 'published');
        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }
        $jobs = $query->with('category', 'client')->paginate(15);
        return view('freelancer.jobs.search', compact('jobs'));
    }

    /**
     * Public job listing (for guests).
     */
    public function publicIndex(Request $request)
    {
        $jobs = Job::where('status', 'published')->with('category', 'client')->paginate(15);
        return view('jobs.index', compact('jobs'));
    }

    /**
     * Public job show (for guests).
     */
    public function publicShow(Job $job)
    {
        $job->load('category', 'client', 'skills');
        return view('jobs.show', compact('job'));
    }

    /**
     * Public job search (for guests).
     */
    public function publicSearch(Request $request)
    {
        $query = Job::query()->where('status', 'published');
        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }
        $jobs = $query->with('category', 'client')->paginate(15);
        return view('jobs.search', compact('jobs'));
    }

    /**
     * API job search (for AJAX).
     */
    public function apiSearch(Request $request)
    {
        $query = Job::query()->where('status', 'published');
        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }
        $jobs = $query->with('category', 'client')->paginate(15);
        return response()->json($jobs);
    }
} 