@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">Student workspace</p>
        <h1>Dashboard</h1>
        <p class="page-subtitle">Post work when you need help, find work when you want to earn, and track both sides from one place.</p>
    </div>
</div>

<div class="stats-grid">
    <x-stats-card label="Active posts" :value="$publishedJobs" />
    <x-stats-card label="Needs review" :value="$applicationsNeedingReview" />
    <x-stats-card label="Active applications" :value="$activeApplications" />
    <x-stats-card label="Saved jobs" :value="$savedJobs" />
</div>

<div class="mb-7 grid gap-5 2xl:grid-cols-2">
    <section class="panel">
        <div class="panel-header">
            <div>
                <h2>Hiring</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $totalJobs }} posted jobs · {{ $receivedApplications }} received applications.</p>
            </div>
            <a href="{{ route('member.posted-jobs.index') }}" class="text-sm font-semibold text-emerald-700">Manage posts</a>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Job</th><th>Status</th><th>Applicants</th><th>Updated</th></tr></thead>
                <tbody>
                    @forelse($recentJobs as $job)
                        <tr>
                            <td><a href="{{ route('member.posted-jobs.show', $job) }}">{{ $job->title }}</a></td>
                            <td><span class="badge {{ $job->status === 'published' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($job->status) }}</span></td>
                            <td>{{ $job->applications_count }}</td>
                            <td>{{ $job->updated_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-state">No posted jobs yet. Create one when you need help.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <h2>Applicants to review</h2>
                <p class="mt-1 text-sm text-slate-500">New and in-progress applications for your posted jobs.</p>
            </div>
            <a href="{{ route('member.received-applications.index') }}" class="text-sm font-semibold text-emerald-700">View all</a>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Applicant</th><th>Job</th><th>Status</th><th>Received</th></tr></thead>
                <tbody>
                    @forelse($reviewQueue as $application)
                        <tr>
                            <td><a href="{{ route('member.received-applications.show', $application) }}">{{ $application->freelancer->name ?? 'Unknown applicant' }}</a></td>
                            <td>{{ $application->job->title ?? 'Unavailable job' }}</td>
                            <td><span class="badge badge-application-{{ $application->status }}">{{ ucfirst($application->status) }}</span></td>
                            <td>{{ $application->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-state">No applicants need review right now.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

@if($closingSoonJobs->isNotEmpty())
<section class="panel mb-7 border-amber-200/80 bg-amber-50/30">
    <div class="panel-header">
        <div>
            <h2>Closing soon</h2>
            <p class="mt-1 text-sm text-slate-500">Open jobs you have not applied to that close within seven days.</p>
        </div>
    </div>
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($closingSoonJobs as $job)
            <a href="{{ route('member.jobs.show', $job) }}" class="rounded-xl border border-amber-200 bg-white p-4 hover:border-emerald-300">
                <p class="font-semibold text-slate-900">{{ $job->title }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ $job->client->name ?? 'CMU member' }}</p>
                <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-amber-700">Due {{ $job->deadline->format('M d') }}</p>
            </a>
        @endforeach
    </div>
</section>
@endif

<section class="panel mb-7">
    <div class="panel-header">
        <div>
            <h2>Recommended work</h2>
            <p class="mt-1 text-sm text-slate-500">Open jobs you have not applied to, excluding jobs you posted.</p>
        </div>
        <a href="{{ route('member.jobs.index') }}" class="text-sm font-semibold text-emerald-700">Browse more</a>
    </div>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($recommendedJobs as $job)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-emerald-700">{{ $job->category->name ?? 'General' }}</p>
                    @if(($job->matching_skills_count ?? 0) > 0)
                        <span class="badge badge-success">{{ $job->matching_skills_count }} skill match</span>
                    @endif
                </div>
                <h3 class="mt-3 text-lg"><a href="{{ route('member.jobs.show', $job) }}" class="hover:text-emerald-800">{{ $job->title }}</a></h3>
                <p class="mt-1 text-sm text-slate-500">{{ $job->client->name ?? 'CMU member' }}</p>
                <p class="mt-4 text-sm leading-6 text-slate-600">{{ Str::limit($job->description, 110) }}</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($job->skills->take(3) as $skill)
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ $skill->name }}</span>
                    @endforeach
                </div>
                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                    <span class="text-xs text-slate-400">{{ $job->deadline ? 'Due '.$job->deadline->format('M d') : 'Open deadline' }}</span>
                    <a href="{{ route('member.jobs.show', $job) }}" class="text-sm font-semibold text-emerald-700">View job</a>
                </div>
            </article>
        @empty
            <div class="empty-state md:col-span-2 xl:col-span-3">No new recommendations right now. Check the full job board for more opportunities.</div>
        @endforelse
    </div>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <h2>Finding work</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $totalApplications }} applications submitted to date.</p>
        </div>
        <a href="{{ route('member.applications.index') }}" class="text-sm font-semibold text-emerald-700">View applications</a>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Opportunity</th><th>Posted by</th><th>Status</th><th>Updated</th></tr></thead>
            <tbody>
                @forelse($recentApplications as $application)
                    <tr>
                        <td><a href="{{ route('member.applications.show', $application) }}">{{ $application->job->title ?? 'Unavailable job' }}</a></td>
                        <td>{{ $application->job->client->name ?? '—' }}</td>
                        <td><span class="badge badge-application-{{ $application->status }}">{{ ucfirst($application->status) }}</span></td>
                        <td>{{ $application->updated_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">You have not submitted any applications yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
