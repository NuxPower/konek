@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">Opportunity board</p>
        <h1>Find your next project</h1>
        <p class="page-subtitle">Explore work posted by clients across the CMU community.</p>
    </div>
</div>

<form method="GET" action="{{ route('freelancer.jobs.index') }}" class="job-filter-panel">
    <div class="job-search-field">
        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
        </svg>
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search title, skill, or client">
    </div>

    <select name="category" aria-label="Category">
        <option value="">All categories</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>

    <select name="type" aria-label="Work type">
        <option value="">All work types</option>
        <option value="full-time" @selected(request('type') === 'full-time')>Full-time</option>
        <option value="part-time" @selected(request('type') === 'part-time')>Part-time</option>
        <option value="contract" @selected(request('type') === 'contract')>Contract</option>
        <option value="internship" @selected(request('type') === 'internship')>Internship</option>
    </select>

    <select name="experience" aria-label="Experience">
        <option value="">Any experience</option>
        <option value="entry" @selected(request('experience') === 'entry')>Entry</option>
        <option value="intermediate" @selected(request('experience') === 'intermediate')>Intermediate</option>
        <option value="expert" @selected(request('experience') === 'expert')>Expert</option>
    </select>

    <select name="sort" aria-label="Sort jobs">
        <option value="">Newest</option>
        <option value="deadline" @selected(request('sort') === 'deadline')>Deadline soon</option>
        <option value="budget" @selected(request('sort') === 'budget')>Highest budget</option>
    </select>

    <button type="submit" class="btn btn-primary">Search</button>
</form>

<div class="mb-5 flex items-center justify-between">
    <p class="text-sm text-slate-500">
        <span class="font-semibold text-slate-800">{{ $jobs->total() }}</span>
        {{ Str::plural('opportunity', $jobs->total()) }} available
    </p>
    @if(request()->hasAny(['q', 'category', 'type', 'experience', 'sort']))
        <a href="{{ route('freelancer.jobs.index') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">Clear filters</a>
    @endif
</div>

<div class="job-grid">
    @forelse($jobs as $job)
        <article class="job-listing-card">
            <div class="flex items-start justify-between gap-4">
                <div class="job-client-mark">{{ strtoupper(substr($job->client->name ?? 'C', 0, 1)) }}</div>
                <div class="flex items-center gap-2">
                    <span class="badge badge-success">Open</span>
                    <x-save-job-button :job="$job" :saved="(bool) $job->is_saved" :compact="true" />
                </div>
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
                @if($job->deadline)
                    <span>Due {{ $job->deadline->format('M d') }}</span>
                @endif
            </div>

            @if($job->skills->isNotEmpty())
                <div class="job-skill-row">
                    @foreach($job->skills->take(3) as $skill)
                        <span>{{ $skill->name }}</span>
                    @endforeach
                    @if($job->skills->count() > 3)
                        <span>+{{ $job->skills->count() - 3 }}</span>
                    @endif
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
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7h-4V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/></svg>
            </div>
            <h2 class="mt-4">No matching jobs</h2>
            <p class="mt-2 text-sm text-slate-500">Try removing a filter or searching for a broader skill.</p>
            <a href="{{ route('freelancer.jobs.index') }}" class="btn btn-secondary mt-5">Reset search</a>
        </div>
    @endforelse
</div>

<div class="mt-8">{{ $jobs->links() }}</div>
@endsection
