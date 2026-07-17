<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    public function jobQuery(array $filters = []): Builder
    {
        return Job::query()
            ->with(['client', 'category'])
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->latest();
    }

    public function userQuery(array $filters = []): Builder
    {
        return User::query()
            ->when($filters['role'] ?? null, fn ($query, $role) => $query->where('role', $role))
            ->when(array_key_exists('active', $filters) && $filters['active'] !== '', function ($query) use ($filters) {
                $query->where('is_active', (bool) $filters['active']);
            })
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->latest();
    }

    public function applicationQuery(array $filters = []): Builder
    {
        return Application::query()
            ->with(['job.client', 'freelancer'])
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->latest();
    }

    public function summary(): array
    {
        $totalApplications = Application::count();
        $acceptedApplications = Application::where('status', 'accepted')->count();
        $totalJobs = Job::count();

        return [
            'totalUsers' => User::count(),
            'activeUsers' => User::where('is_active', true)->count(),
            'totalJobs' => $totalJobs,
            'publishedJobs' => Job::where('status', 'published')->count(),
            'totalApplications' => $totalApplications,
            'acceptedApplications' => $acceptedApplications,
            'acceptanceRate' => $totalApplications > 0 ? round($acceptedApplications / $totalApplications * 100, 1) : 0,
            'applicationsPerJob' => $totalJobs > 0 ? round($totalApplications / $totalJobs, 1) : 0,
            'userRoleCounts' => User::query()->selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role'),
            'jobStatusCounts' => Job::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'applicationStatusCounts' => Application::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ];
    }

    public function export(string $report, array $filters = []): StreamedResponse
    {
        [$headers, $rows] = match ($report) {
            'users' => [
                ['Name', 'Email', 'Role', 'Status', 'Joined'],
                $this->userQuery($filters)->get()->map(fn (User $user) => [
                    $user->name,
                    $user->email,
                    ucfirst($user->role),
                    $user->is_active ? 'Active' : 'Inactive',
                    $user->created_at->format('Y-m-d H:i:s'),
                ]),
            ],
            'jobs' => [
                ['Title', 'Posted By', 'Category', 'Type', 'Status', 'Applications', 'Deadline', 'Created'],
                $this->jobQuery($filters)->get()->map(fn (Job $job) => [
                    $job->title,
                    $job->client->name ?? '',
                    $job->category->name ?? '',
                    $job->type,
                    $job->status,
                    $job->applications_count,
                    $job->deadline?->format('Y-m-d') ?? '',
                    $job->created_at->format('Y-m-d H:i:s'),
                ]),
            ],
            'applications' => [
                ['Job', 'Posted By', 'Applicant', 'Status', 'Proposed Rate', 'Submitted', 'Reviewed'],
                $this->applicationQuery($filters)->get()->map(fn (Application $application) => [
                    $application->job->title ?? '',
                    $application->job->client->name ?? '',
                    $application->freelancer->name ?? '',
                    $application->status,
                    $application->proposed_rate,
                    $application->created_at->format('Y-m-d H:i:s'),
                    $application->reviewed_at?->format('Y-m-d H:i:s') ?? '',
                ]),
            ],
        };

        $filename = "konek-{$report}-".now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
