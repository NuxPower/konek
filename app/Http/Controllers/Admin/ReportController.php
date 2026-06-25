<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Show the reports dashboard.
     */
    public function index(Request $request)
    {
        return view('admin.reports.index', $this->reportService->summary());
    }

    /**
     * Show job reports.
     */
    public function jobs(Request $request)
    {
        $filters = $this->filters($request, 'jobs');
        $jobs = $this->reportService->jobQuery($filters)->paginate(20)->withQueryString();

        return view('admin.reports.jobs', compact('jobs', 'filters'));
    }

    /**
     * Show application reports.
     */
    public function applications(Request $request)
    {
        $filters = $this->filters($request, 'applications');
        $applications = $this->reportService->applicationQuery($filters)->paginate(20)->withQueryString();

        return view('admin.reports.applications', compact('applications', 'filters'));
    }

    /**
     * Show user reports.
     */
    public function users(Request $request)
    {
        $filters = $this->filters($request, 'users');
        $users = $this->reportService->userQuery($filters)->paginate(20)->withQueryString();

        return view('admin.reports.users', compact('users', 'filters'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'report' => 'required|in:users,jobs,applications',
            'status' => 'nullable|string|max:30',
            'role' => 'nullable|in:admin,client,freelancer',
            'active' => 'nullable|in:0,1',
            'type' => 'nullable|in:full-time,part-time,contract,internship',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $report = $validated['report'];
        unset($validated['report']);

        return $this->reportService->export($report, $validated);
    }

    private function filters(Request $request, string $report): array
    {
        $rules = [
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ];

        $rules += match ($report) {
            'users' => ['role' => 'nullable|in:admin,client,freelancer', 'active' => 'nullable|in:0,1'],
            'jobs' => ['status' => 'nullable|in:draft,published,closed,cancelled', 'type' => 'nullable|in:full-time,part-time,contract,internship'],
            'applications' => ['status' => 'nullable|in:pending,reviewing,shortlisted,accepted,rejected,withdrawn'],
        };

        return $request->validate($rules);
    }
}
