@extends('layouts.app')

@section('content')
<a href="{{ route('member.jobs.index') }}" class="mb-6 inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-emerald-800">
    <span aria-hidden="true">←</span> Back to opportunities
</a>

<div class="job-detail-layout">
    <article class="min-w-0">
        <section class="panel p-7 sm:p-9">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex gap-4">
                    <div class="job-client-mark h-14 w-14 text-lg">{{ strtoupper(substr($job->client->name ?? 'C', 0, 1)) }}</div>
                    <div>
                        <p class="page-eyebrow">{{ $job->category->name ?? 'General' }}</p>
                        <h1>{{ $job->title }}</h1>
                        <p class="mt-2 text-sm text-slate-500">{{ $job->client->name ?? 'CMU Client' }} · Posted {{ $job->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <span class="inline-flex min-w-44 shrink-0 items-center justify-center rounded-full bg-emerald-100 px-4 py-2 text-center text-xs font-semibold leading-5 text-emerald-800 sm:self-start">Accepting applications</span>
            </div>

            <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="job-fact"><span>Work type</span><strong>{{ ucfirst(str_replace('-', ' ', $job->type)) }}</strong></div>
                <div class="job-fact"><span>Experience</span><strong>{{ ucfirst($job->experience_level) }}</strong></div>
                <div class="job-fact"><span>Budget</span><strong>@if($job->budget_min || $job->budget_max)₱{{ number_format((float) ($job->budget_min ?? $job->budget_max)) }}@if($job->budget_min && $job->budget_max)–{{ number_format((float) $job->budget_max) }}@endif @else Negotiable @endif</strong></div>
                <div class="job-fact"><span>Deadline</span><strong>{{ $job->deadline?->format('M d, Y') ?? 'Open' }}</strong></div>
            </div>
        </section>

        <section class="panel mt-5 p-7 sm:p-9">
            <h2>About the opportunity</h2>
            <div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $job->description }}</div>

            <div class="my-8 border-t border-slate-100"></div>

            <h2>What the client is looking for</h2>
            <div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $job->requirements }}</div>

            @if($job->skills->isNotEmpty())
                <div class="my-8 border-t border-slate-100"></div>
                <h2>Skills</h2>
                <div class="job-skill-row mt-4">
                    @foreach($job->skills as $skill)<span>{{ $skill->name }}</span>@endforeach
                </div>
            @endif
        </section>
    </article>

    <aside>
        <div class="panel sticky top-28">
            <p class="text-sm font-semibold text-slate-900">Interested in this opportunity?</p>
            <p class="mt-2 text-sm leading-6 text-slate-500">Send the client a focused application with your rate and relevant experience.</p>
            @can('apply', $job)
                <a href="{{ route('member.jobs.apply.create', $job) }}" class="btn btn-primary mt-6 w-full">Apply now</a>
            @else
                <div class="mt-6 rounded-xl bg-slate-100 px-4 py-3 text-center text-sm font-medium text-slate-500">
                    @if($job->deadline && $job->deadline->isPast())
                        Applications are closed
                    @elseif($job->max_applications && $job->applications_count >= $job->max_applications)
                        Application limit reached
                    @else
                        You already applied
                    @endif
                </div>
            @endcan
            <div class="mt-3">
                <x-save-job-button :job="$job" :saved="(bool) $job->is_saved" />
            </div>
            <div class="mt-6 border-t border-slate-100 pt-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">About the client</p>
                <p class="mt-3 text-sm font-semibold text-slate-900">{{ $job->client->name ?? 'CMU Client' }}</p>
                <p class="mt-1 text-xs text-slate-500">Member since {{ $job->client->created_at?->format('Y') }}</p>
            </div>
        </div>
    </aside>
</div>
@endsection
