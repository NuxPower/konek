<?php


// ReportController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use App\Models\Client;
use App\Models\Freelancer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function userReport(Request $request)
    {
        $period = $request->get('period', 'monthly');
        
        $data = $this->getUserReportData($period);
        
        return view('admin.reports.users', compact('data', 'period'));
    }

    public function jobReport(Request $request)
    {
        $period = $request->get('period', 'monthly');
        
        $data = $this->getJobReportData($period);
        
        return view('admin.reports.jobs', compact('data', 'period'));
    }

    public function earningsReport(Request $request)
    {
        $period = $request->get('period', 'monthly');
        
        $data = $this->getEarningsReportData($period);
        
        return view('admin.reports.earnings', compact('data', 'period'));
    }

    private function getUserReportData($period)
    {
        $dateFormat = $period === 'daily' ? '%Y-%m-%d' : '%Y-%m';
        
        return [
            'registrations' => User::selectRaw("DATE_FORMAT(created_at, '$dateFormat') as period, COUNT(*) as count")
                                  ->where('created_at', '>=', $this->getPeriodStart($period))
                                  ->groupBy('period')
                                  ->orderBy('period')
                                  ->get(),
            'by_role' => User::selectRaw('role, COUNT(*) as count')
                           ->groupBy('role')
                           ->get(),
            'verification_stats' => [
                'verified_clients' => Client::where('is_verified', true)->count(),
                'unverified_clients' => Client::where('is_verified', false)->count(),
                'verified_freelancers' => Freelancer::where('is_verified', true)->count(),
                'unverified_freelancers' => Freelancer::where('is_verified', false)->count(),
            ]
        ];
    }

    private function getJobReportData($period)
    {
        $dateFormat = $period === 'daily' ? '%Y-%m-%d' : '%Y-%m';
        
        return [
            'posted_jobs' => Job::selectRaw("DATE_FORMAT(created_at, '$dateFormat') as period, COUNT(*) as count")
                               ->where('created_at', '>=', $this->getPeriodStart($period))
                               ->groupBy('period')
                               ->orderBy('period')
                               ->get(),
            'by_status' => Job::selectRaw('status, COUNT(*) as count')
                             ->groupBy('status')
                             ->get(),
            'by_type' => Job::selectRaw('type, COUNT(*) as count')
                           ->groupBy('type')
                           ->get(),
            'completion_rate' => $this->calculateCompletionRate(),
        ];
    }

    private function getEarningsReportData($period)
    {
        return [
            'total_earnings' => Freelancer::sum('total_earnings'),
            'total_spent' => Client::sum('total_spent'),
            'average_job_value' => Job::avg('budget'),
            'top_earning_freelancers' => Freelancer::with('user')
                                                  ->orderBy('total_earnings', 'desc')
                                                  ->take(10)
                                                  ->get(),
            'top_spending_clients' => Client::with('user')
                                           ->orderBy('total_spent', 'desc')
                                           ->take(10)
                                           ->get(),
        ];
    }

    private function getPeriodStart($period)
    {
        switch ($period) {
            case 'daily':
                return Carbon::now()->subDays(30);
            case 'weekly':
                return Carbon::now()->subWeeks(12);
            case 'monthly':
            default:
                return Carbon::now()->subMonths(12);
        }
    }

    private function calculateCompletionRate()
    {
        $totalJobs = Job::count();
        $completedJobs = Job::where('status', 'completed')->count();
        
        return $totalJobs > 0 ? round(($completedJobs / $totalJobs) * 100, 2) : 0;
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'users');
        $format = $request->get('format', 'csv');
        
        // Implementation for exporting reports
        // This would typically use a package like Laravel Excel
        
        return response()->download(storage_path("app/reports/{$type}_report.{$format}"));
    }
}
