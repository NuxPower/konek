@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">Your shortlist</p>
        <h1>Saved jobs</h1>
        <p class="page-subtitle">Keep promising opportunities here while you compare requirements and prepare applications.</p>
    </div>
    <a href="{{ route('freelancer.jobs.index') }}" class="btn btn-primary">Browse jobs</a>
</div>

<div class="mb-5 flex items-center justify-between">
    <p class="text-sm text-slate-500">
        <span class="font-semibold text-slate-800">{{ $jobs->total() }}</span>
        saved {{ Str::plural('opportunity', $jobs->total()) }}
    </p>
</div>

<div class="job-grid">
    @forelse($jobs as $job)
        <article class="job-listing-card">
            <div class="flex items-start justify-between gap-4">
                <div class="job-client-mark">{{ strtoupper(substr($job->client->name ?? 'C', 0, 1)) }}</div>
                <x-save-job-button :job="$job" :saved="true" :compact="true" />
            </div>

            <div class="mt-5">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-emerald-700">{{ $job->category->name ?? 'General' }}</p>
                <h2 class="mt-2 text-xl leading-7">
                    <a href="{{ route('freelancer.jobs.show', $job) }}" class="hover:text-emerald-800">{{ $job->title }}</a>
                </h2>
                <p class="mt-1 text-sm text-slate-500">{{ $job->client->name ?? 'CMU Client' }}</p>
            </div>

            <p class="job-description">{{ Str::limit($job->description, 155) }}</p>

            <div class="job-meta-row">
                <span>{{ ucfirst(str_replace('-', ' ', $job->type)) }}</span>
                <span>{{ ucfirst($job->experience_level) }}</span>
                @if($job->deadline)<span>Due {{ $job->deadline->format('M d') }}</span>@endif
            </div>

            @if($job->skills->isNotEmpty())
                <div class="job-skill-row">
                    @foreach($job->skills->take(3) as $skill)<span>{{ $skill->name }}</span>@endforeach
                </div>
            @endif

            <div class="job-card-footer">
                <div>
                    <p class="text-xs text-slate-400">{{ ucfirst($job->budget_type) }} budget</p>
                    <p class="mt-0.5 font-semibold text-slate-900">
                        @if($job->budget_min || $job->budget_max)
                            ₱{{ number_format((float) ($job->budget_min ?? $job->budget_max)) }}
                            @if($job->budget_min && $job->budget_max)–{{ number_format((float) $job->budget_max) }}@endif
                        @else
                            Negotiable
                        @endif
                    </p>
                </div>
                <a href="{{ route('freelancer.jobs.show', $job) }}" class="btn btn-secondary">View job</a>
            </div>
        </article>
    @empty
        <div class="job-empty-state">
            <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-emerald-100 text-emerald-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4.8A1.8 1.8 0 0 1 7.8 3h8.4A1.8 1.8 0 0 1 18 4.8V21l-6-3.8L6 21V4.8Z"/></svg>
            </div>
            <h2 class="mt-4">No saved jobs yet</h2>
            <p class="mt-2 text-sm text-slate-500">Save opportunities from the job board to revisit them here.</p>
            <a href="{{ route('freelancer.jobs.index') }}" class="btn btn-primary mt-5">Explore opportunities</a>
        </div>
    @endforelse
</div>

<div class="mt-8">{{ $jobs->links() }}</div>
@endsection
