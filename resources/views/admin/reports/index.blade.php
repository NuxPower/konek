@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">Platform intelligence</p>
        <h1>Reports</h1>
        <p class="page-subtitle">Measure platform adoption, opportunity health, and hiring outcomes.</p>
    </div>
</div>

<div class="stats-grid">
    <x-stats-card label="Active users" :value="$activeUsers.' / '.$totalUsers" />
    <x-stats-card label="Published jobs" :value="$publishedJobs.' / '.$totalJobs" />
    <x-stats-card label="Acceptance rate" :value="$acceptanceRate.'%'" />
    <x-stats-card label="Applications per job" :value="$applicationsPerJob" />
</div>

<div class="mb-7 grid gap-5 lg:grid-cols-3">
    <section class="panel">
        <div class="panel-header"><h2>Users by role</h2></div>
        <div class="space-y-4">
            @foreach(['admin', 'client', 'freelancer'] as $role)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600">{{ ucfirst($role) }}</span>
                    <strong class="text-slate-900">{{ $userRoleCounts[$role] ?? 0 }}</strong>
                </div>
            @endforeach
        </div>
    </section>
    <section class="panel">
        <div class="panel-header"><h2>Jobs by status</h2></div>
        <div class="space-y-4">
            @foreach(['draft', 'published', 'closed', 'cancelled'] as $status)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600">{{ ucfirst($status) }}</span>
                    <strong class="text-slate-900">{{ $jobStatusCounts[$status] ?? 0 }}</strong>
                </div>
            @endforeach
        </div>
    </section>
    <section class="panel">
        <div class="panel-header"><h2>Application outcomes</h2></div>
        <div class="space-y-4">
            @foreach(['pending', 'reviewing', 'shortlisted', 'accepted', 'rejected', 'withdrawn'] as $status)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600">{{ ucfirst($status) }}</span>
                    <strong class="text-slate-900">{{ $applicationStatusCounts[$status] ?? 0 }}</strong>
                </div>
            @endforeach
        </div>
    </section>
</div>

<div class="grid gap-5 md:grid-cols-3">
    @foreach([
        ['route' => 'admin.reports.users', 'title' => 'User report', 'text' => 'Filter account roles, status, and registration dates.'],
        ['route' => 'admin.reports.jobs', 'title' => 'Jobs report', 'text' => 'Analyze opportunity types, status, clients, and deadlines.'],
        ['route' => 'admin.reports.applications', 'title' => 'Applications report', 'text' => 'Review candidate volume, status, and hiring outcomes.'],
    ] as $report)
        <a href="{{ route($report['route']) }}" class="panel group block transition hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-sm">
            <p class="text-lg font-semibold text-slate-900 group-hover:text-emerald-800">{{ $report['title'] }}</p>
            <p class="mt-2 text-sm leading-6 text-slate-500">{{ $report['text'] }}</p>
            <p class="mt-6 text-sm font-semibold text-emerald-700">Open report →</p>
        </a>
    @endforeach
</div>
@endsection
