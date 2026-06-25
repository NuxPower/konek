@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">Platform control center</p>
        <h1>Admin dashboard</h1>
        <p class="page-subtitle">Monitor platform health, clear review backlogs, and catch time-sensitive jobs.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">View reports</a>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add user</a>
    </div>
</div>

<div class="stats-grid">
    <x-stats-card label="Active users" :value="$activeUsers.' / '.$totalUsers" />
    <x-stats-card label="Published jobs" :value="$publishedJobs.' / '.$totalJobs" />
    <x-stats-card label="Needs review" :value="$applicationsNeedingReview" />
    <x-stats-card label="Application success" :value="$acceptanceRate.'%'" />
</div>

<div class="mb-7 grid gap-5 xl:grid-cols-2">
    <section class="panel">
        <div class="panel-header">
            <div>
                <h2>Application review queue</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $applicationsNeedingReview }} applications are pending or under review.</p>
            </div>
            <a href="{{ route('admin.applications.index') }}" class="text-sm font-semibold text-emerald-700">View all</a>
        </div>
        <div class="space-y-3">
            @forelse($reviewQueue as $application)
                <a href="{{ route('admin.applications.show', $application) }}" class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 p-4 hover:border-emerald-200 hover:bg-emerald-50/40">
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-slate-900">{{ $application->freelancer->name ?? 'Unknown talent' }}</p>
                        <p class="mt-1 truncate text-sm text-slate-500">{{ $application->job->title ?? 'Unavailable job' }} · {{ $application->job->client->name ?? 'Unknown client' }}</p>
                    </div>
                    <span class="badge badge-application-{{ $application->status }}">{{ ucfirst($application->status) }}</span>
                </a>
            @empty
                <div class="empty-state">No applications currently need review.</div>
            @endforelse
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <h2>Deadlines in the next 14 days</h2>
                <p class="mt-1 text-sm text-slate-500">Published jobs that may need attention before they close.</p>
            </div>
            <a href="{{ route('admin.jobs.index') }}" class="text-sm font-semibold text-emerald-700">View jobs</a>
        </div>
        <div class="space-y-3">
            @forelse($upcomingDeadlines as $job)
                <a href="{{ route('admin.jobs.show', $job) }}" class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 p-4 hover:border-emerald-200 hover:bg-emerald-50/40">
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-slate-900">{{ $job->title }}</p>
                        <p class="mt-1 truncate text-sm text-slate-500">{{ $job->client->name ?? 'Unknown client' }} · {{ $job->applications_count }} applications</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-sm font-semibold text-slate-900">{{ $job->deadline->format('M d') }}</p>
                        <p class="text-xs text-slate-400">{{ $job->deadline->diffForHumans() }}</p>
                    </div>
                </a>
            @empty
                <div class="empty-state">No published jobs close within the next 14 days.</div>
            @endforelse
        </div>
    </section>
</div>

<div class="mb-7 grid gap-5 lg:grid-cols-3">
    <section class="panel lg:col-span-2">
        <div class="panel-header">
            <div>
                <h2>Workflow snapshot</h2>
                <p class="mt-1 text-sm text-slate-500">Current workload across jobs and applications.</p>
            </div>
        </div>
        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">Jobs</p>
                <div class="space-y-3">
                    @foreach(['draft', 'published', 'closed'] as $status)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">{{ ucfirst($status) }}</span>
                            <strong class="text-slate-900">{{ $jobStatusCounts[$status] ?? 0 }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
            <div>
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">Applications</p>
                <div class="space-y-3">
                    @foreach(['pending', 'reviewing', 'shortlisted', 'accepted', 'rejected'] as $status)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">{{ ucfirst($status) }}</span>
                            <strong class="text-slate-900">{{ $applicationStatusCounts[$status] ?? 0 }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header"><h2>Latest activity</h2></div>
        <div class="space-y-4">
            @forelse($recentActivities as $log)
                <div class="flex gap-3 border-b border-slate-100 pb-4 last:border-0 last:pb-0">
                    <div class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-emerald-500"></div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium leading-5 text-slate-800">{{ $log->description }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ $log->causer->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No recent activity.</p>
            @endforelse
        </div>
    </section>
</div>

<div class="grid gap-5 xl:grid-cols-2">
    <section class="panel">
        <div class="panel-header">
            <div>
                <h2>User growth</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $totalClients }} clients and {{ $totalFreelancers }} freelancers registered.</p>
            </div>
        </div>
        <canvas id="userTrendChart" height="130"></canvas>
    </section>
    <section class="panel">
        <div class="panel-header">
            <div>
                <h2>Job activity</h2>
                <p class="mt-1 text-sm text-slate-500">New opportunities posted over the last six months.</p>
            </div>
        </div>
        <canvas id="jobTrendChart" height="130"></canvas>
    </section>
</div>

@push('scripts')
<script>
const chartDefaults = {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', precision: 0 } }
    }
};

new Chart(document.getElementById('userTrendChart'), {
    type: 'line',
    data: {
        labels: @json($months),
        datasets: [{
            data: @json($userTrendData),
            borderColor: '#047857',
            backgroundColor: 'rgba(16, 185, 129, .09)',
            tension: .4,
            fill: true,
            pointRadius: 3,
            pointBackgroundColor: '#047857'
        }]
    },
    options: chartDefaults
});

new Chart(document.getElementById('jobTrendChart'), {
    type: 'bar',
    data: {
        labels: @json($months),
        datasets: [{
            data: @json($jobTrendData),
            backgroundColor: '#34d399',
            borderRadius: 8,
            maxBarThickness: 34
        }]
    },
    options: chartDefaults
});
</script>
@endpush
@endsection
