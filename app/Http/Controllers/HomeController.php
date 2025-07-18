<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Job;
use App\Models\User;
use App\Models\Application;
use App\Models\Category;

class HomeController extends Controller
{
    /**
     * Display the application homepage.
     */
    public function index()
    {
        // Get featured/recent jobs for homepage
        $featuredJobs = Job::where('status', 'published') // Changed from 'active' to 'published'
            ->where('is_featured', true)
            ->latest()
            ->take(6)
            ->get();

        $recentJobs = Job::where('status', 'published') // Changed from 'active' to 'published'
            ->latest()
            ->take(8)
            ->get();

        // Get some basic statistics for homepage
        $stats = [
            'total_jobs' => Job::where('status', 'published')->count(), // Changed from 'active'
            'total_freelancers' => User::where('role', 'freelancer')->count(),
            'total_clients' => User::where('role', 'client')->count(),
            'completed_projects' => Application::where('status', 'completed')->count(),
        ];

        // Get top categories - FIXED: Join with categories table
        $topCategories = Job::select('categories.name as category', DB::raw('COUNT(*) as job_count'))
            ->join('categories', 'jobs.category_id', '=', 'categories.id')
            ->where('jobs.status', 'published')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('job_count', 'desc')
            ->take(6)
            ->get();

        return view('home', compact('featuredJobs', 'recentJobs', 'stats', 'topCategories'));
    }

    /**
     * Handle user dashboard redirection based on role.
     */
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Redirect to appropriate dashboard based on user role
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'client':
                return redirect()->route('client.dashboard');
            case 'freelancer':
                return redirect()->route('freelancer.dashboard');
            default:
                return redirect()->route('home');
        }
    }

    /**
     * Display the about page.
     */
    public function about()
    {
        return view('about');
    }

    /**
     * Display the contact page.
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Handle contact form submission.
     */
    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        // Here you would typically send an email or store the contact message
        // For now, we'll just flash a success message
        
        return redirect()->route('contact')->with('success', 'Thank you for your message! We will get back to you soon.');
    }

    /**
     * Display the terms of service page.
     */
    public function terms()
    {
        return view('terms');
    }

    /**
     * Display the privacy policy page.
     */
    public function privacy()
    {
        return view('privacy');
    }

    /**
     * Display the help/FAQ page.
     */
    public function help()
    {
        return view('help');
    }

    /**
     * Handle search functionality from homepage.
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        $category = $request->input('category');
        $location = $request->input('location');

        $jobs = Job::where('status', 'published'); // Changed from 'active'

        if ($query) {
            $jobs->where(function($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%')
                  ->orWhere('requirements', 'like', '%' . $query . '%'); // Changed from 'skills_required'
            });
        }

        if ($category) {
            // FIXED: Use category_id instead of category
            $jobs->where('category_id', $category);
        }

        if ($location) {
            $jobs->where('location', 'like', '%' . $location . '%');
        }

        $jobs = $jobs->latest()->paginate(12);

        return view('search-results', compact('jobs', 'query', 'category', 'location'));
    }
}