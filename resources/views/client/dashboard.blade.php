@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">Hiring workspace</p>
        <h1>Client dashboard</h1>
        <p class="page-subtitle">Review candidates, watch approaching deadlines, and keep your job posts moving.</p>
    </div>
    <a href="{{ route('member.posted-jobs.create') }}" class="btn btn-primary">Post a job</a>
</div>

<div class="stats-grid">
    <x-stats-card label="Published jobs" :value="$publishedJobs" />
    <x-stats-card label="Needs your review" :value="$applicationsNeedingReview" />
    <x-stats-card label="Shortlisted talent" :value="$shortlistedApplications" />
    <x-stats-card label="Draft jobs" :value="$draftJobs" />
</div>

<div class="mb-7 grid gap-5 xl:grid-cols-3">
    <section class="panel xl:col-span-2">
        <div class="panel-header">
            <div>
                <h2>Candidates needing review</h2>
                <p class="mt-1 text-sm text-slate-500">Prioritize new and in-progress applications.</p>
            </div>
            <a href="{{ route('member.received-applications.index') }}" class="text-sm font-semibold text-emerald-700">View all</a>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Candidate</th><th>Job</th><th>Status</th><th>Received</th></tr></thead>
                <tbody>
                    @forelse($reviewQueue as $application)
                        <tr>
                            <td><a href="{{ route('member.received-applications.show', $application) }}">{{ $application->freelancer->name ?? 'Unknown talent' }}</a></td>
                            <td>{{ $application->job->title ?? 'Unavailable job' }}</td>
                            <td><span class="badge badge-application-{{ $application->status }}">{{ ucfirst($application->status) }}</span></td>
                            <td>{{ $application->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-state">Your review queue is clear.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <h2>Closing soon</h2>
                <p class="mt-1 text-sm text-slate-500">Deadlines within 14 days.</p>
            </div>
        </div>
        <div class="space-y-3">
            @forelse($upcomingDeadlines as $job)
                <a href="{{ route('member.posted-jobs.show', $job) }}" class="block rounded-xl border border-slate-200 p-4 hover:border-emerald-200 hover:bg-emerald-50/40">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-semibold text-slate-900">{{ $job->title }}</p>
                        <span class="shrink-0 text-sm font-semibold text-emerald-700">{{ $job->deadline->format('M d') }}</span>
                    </div>
                    <p class="mt-2 text-sm text-slate-500">{{ $job->applications_count }} applications · {{ $job->deadline->diffForHumans() }}</p>
                </a>
            @empty
                <p class="text-sm text-slate-500">No jobs close in the next 14 days.</p>
            @endforelse
        </div>
    </section>
</div>

@if($draftsNeedingAction->isNotEmpty())
<section class="panel mb-7 border-amber-200/80 bg-amber-50/30">
    <div class="panel-header">
        <div>
            <h2>Drafts waiting to be published</h2>
            <p class="mt-1 text-sm text-slate-500">Finish these job posts when they are ready for applicants.</p>
        </div>
        <a href="{{ route('member.posted-jobs.index', ['status' => 'draft']) }}" class="text-sm font-semibold text-emerald-700">View drafts</a>
    </div>
    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
        @foreach($draftsNeedingAction as $job)
            <a href="{{ route('member.posted-jobs.edit', $job) }}" class="rounded-xl border border-amber-200 bg-white p-4 hover:border-emerald-300">
                <p class="font-semibold text-slate-900">{{ $job->title }}</p>
                <p class="mt-2 text-xs text-slate-400">Updated {{ $job->updated_at->diffForHumans() }}</p>
            </a>
        @endforeach
    </div>
</section>
@endif

<section class="panel">
    <div class="panel-header">
        <div>
            <h2>Recent jobs</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $totalJobs }} jobs have received {{ $totalApplications }} total applications.</p>
        </div>
        <a href="{{ route('member.posted-jobs.index') }}" class="text-sm font-semibold text-emerald-700">Manage jobs</a>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Title</th><th>Status</th><th>Applications</th><th>Updated</th></tr></thead>
            <tbody>
                @forelse($recentJobs as $job)
                    <tr>
                        <td><a href="{{ route('member.posted-jobs.show', $job) }}">{{ $job->title }}</a></td>
                        <td><span class="badge {{ $job->status === 'published' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($job->status) }}</span></td>
                        <td>{{ $job->applications_count }}</td>
                        <td>{{ $job->updated_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No jobs yet. Post your first opportunity.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
