<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientDashboardController extends Controller
{
    public function index()
    {
        $client = Auth::user()->client;
        
        if (!$client) {
            $client = Auth::user()->getClientProfile();
        }

        // Add null check to prevent the error
        if (!$client) {
            // Handle the case where client profile creation failed
            return redirect()->route('profile.complete')
                ->with('error', 'Please complete your client profile first.');
        }

        $stats = [
            'total_jobs' => $client->jobs()->count(),
            'active_jobs' => $client->jobs()->whereIn('status', ['active', 'open'])->count(),
            'completed_jobs' => $client->jobs()->where('status', 'completed')->count(),
            'total_applications' => Application::whereHas('job', function($q) use ($client) {
                $q->where('client_id', $client->id);
            })->count(),
            'pending_applications' => Application::whereHas('job', function($q) use ($client) {
                $q->where('client_id', $client->id);
            })->where('status', 'pending')->count(),
            'total_spent' => $client->total_spent ?? 0,
            'rating' => $client->rating ?? 0,
        ];

        // Recent jobs with null checks
        $recentJobs = $client->jobs()
                           ->with(['skills', 'applications'])
                           ->latest()
                           ->take(5)
                           ->get();

        // Recent applications with null checks
        $recentApplications = Application::whereHas('job', function($q) use ($client) {
                                         $q->where('client_id', $client->id);
                                     })
                                     ->with(['freelancer.user', 'job'])
                                     ->latest()
                                     ->take(5)
                                     ->get();

        // Job statistics
        $jobStats = [
            'this_month' => $client->jobs()->whereMonth('created_at', now()->month)->count(),
            'last_month' => $client->jobs()->whereMonth('created_at', now()->subMonth()->month)->count(),
            'completion_rate' => $this->calculateCompletionRate($client),
        ];

        return view('client.dashboard', compact('stats', 'recentJobs', 'recentApplications', 'jobStats'));
    }

    private function calculateCompletionRate($client)
    {
        if (!$client) {
            return 0;
        }

        $totalJobs = $client->jobs()->count();
        $completedJobs = $client->jobs()->where('status', 'completed')->count();
        
        return $totalJobs > 0 ? round(($completedJobs / $totalJobs) * 100, 2) : 0;
    }

    public function analytics()
    {
        $client = Auth::user()->client;
        
        if (!$client) {
            $client = Auth::user()->getClientProfile();
        }

        if (!$client) {
            return redirect()->route('profile.complete')
                ->with('error', 'Please complete your client profile first.');
        }
        
        $analytics = [
            'job_performance' => $this->getJobPerformanceData($client),
            'application_trends' => $this->getApplicationTrendsData($client),
            'spending_overview' => $this->getSpendingOverviewData($client),
            'skill_demand' => $this->getSkillDemandData($client),
        ];

        return view('client.analytics', compact('analytics'));
    }

    private function getJobPerformanceData($client)
    {
        if (!$client) {
            return collect();
        }

        return $client->jobs()
                     ->selectRaw('status, COUNT(*) as count')
                     ->groupBy('status')
                     ->get();
    }

    private function getApplicationTrendsData($client)
    {
        if (!$client) {
            return collect();
        }

        return Application::whereHas('job', function($q) use ($client) {
                          $q->where('client_id', $client->id);
                      })
                      ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                      ->where('created_at', '>=', now()->subDays(30))
                      ->groupBy('date')
                      ->orderBy('date')
                      ->get();
    }

    private function getSpendingOverviewData($client)
    {
        if (!$client) {
            return [
                'total_spent' => 0,
                'average_job_budget' => 0,
                'monthly_spending' => 0,
            ];
        }

        return [
            'total_spent' => $client->total_spent ?? 0,
            'average_job_budget' => $client->jobs()->avg('budget') ?? 0,
            'monthly_spending' => $client->jobs()
                                       ->whereMonth('created_at', now()->month)
                                       ->sum('budget') ?? 0,
        ];
    }

    private function getSkillDemandData($client)
    {
        if (!$client) {
            return collect();
        }

        return $client->jobs()
                     ->join('job_skill', 'jobs.id', '=', 'job_skill.job_id')
                     ->join('skills', 'job_skill.skill_id', '=', 'skills.id')
                     ->selectRaw('skills.name, COUNT(*) as count')
                     ->groupBy('skills.name')
                     ->orderBy('count', 'desc')
                     ->take(10)
                     ->get();
    }
}