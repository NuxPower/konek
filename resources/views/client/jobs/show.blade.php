@extends('layouts.app')

@section('content')
<a href="{{ route('client.jobs.index') }}" class="mb-6 inline-flex text-sm font-semibold text-slate-500 hover:text-emerald-800">← Back to my jobs</a>
<div class="page-header">
    <div>
        <p class="page-eyebrow">{{ $job->category->name ?? 'Opportunity' }}</p>
        <h1>{{ $job->title }}</h1>
        <p class="page-subtitle">Posted {{ $job->created_at->format('M d, Y') }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('client.jobs.edit', $job) }}" class="btn btn-primary">Edit job</a>
        <form method="POST" action="{{ route('client.jobs.duplicate', $job) }}" class="!m-0 !border-0 !bg-transparent !p-0 !shadow-none">
            @csrf
            <button class="btn btn-secondary">Duplicate</button>
        </form>
    </div>
</div>

<div class="job-detail-layout">
    <div class="space-y-5">
        <section class="panel">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="job-fact"><span>Status</span><strong>{{ ucfirst($job->status) }}</strong></div>
                <div class="job-fact"><span>Applications</span><strong>{{ $job->applications_count ?? $job->applications->count() }}</strong></div>
                <div class="job-fact"><span>Work type</span><strong>{{ ucfirst(str_replace('-', ' ', $job->type)) }}</strong></div>
                <div class="job-fact"><span>Deadline</span><strong>{{ $job->deadline?->format('M d, Y') ?? 'Open' }}</strong></div>
            </div>
        </section>
        <section class="panel">
            <h2>Job description</h2>
            <div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $job->description }}</div>
            <div class="my-7 border-t border-slate-100"></div>
            <h2>Requirements</h2>
            <div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $job->requirements }}</div>
        </section>
    </div>
    <aside class="space-y-5">
        <section class="panel">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Budget</p>
        <p class="mt-2 text-2xl font-semibold text-emerald-950">
            @if($job->budget_min || $job->budget_max)
                ₱{{ number_format((float) ($job->budget_min ?? $job->budget_max)) }}
                @if($job->budget_min && $job->budget_max)–{{ number_format((float) $job->budget_max) }}@endif
            @else
                Negotiable
            @endif
        </p>
        <p class="mt-1 text-sm capitalize text-slate-500">{{ $job->budget_type }}</p>
        <a href="{{ route('client.applications.index') }}" class="btn btn-secondary mt-6 w-full">Review applications</a>
        </section>

        <section class="panel">
            <p class="text-sm font-semibold text-slate-900">Job status</p>
            <p class="mt-2 text-sm leading-6 text-slate-500">Control whether freelancers can discover and apply to this job.</p>
            <form method="POST" action="{{ route('client.jobs.status', $job) }}" class="mt-5 !border-0 !bg-transparent !p-0 !shadow-none">
                @csrf
                @method('PATCH')
                <select name="status" onchange="this.form.submit()">
                    @foreach(['draft' => 'Draft', 'published' => 'Published', 'paused' => 'Paused', 'closed' => 'Closed', 'cancelled' => 'Cancelled'] as $value => $label)
                        <option value="{{ $value }}" @selected($job->status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </section>

        <section class="panel border-red-100">
            <p class="text-sm font-semibold text-red-700">Delete job</p>
            <p class="mt-2 text-sm leading-6 text-slate-500">This permanently removes the job and its applications.</p>
            <form method="POST" action="{{ route('client.jobs.destroy', $job) }}" class="mt-5 !border-0 !bg-transparent !p-0 !shadow-none" onsubmit="return confirm('Delete this job and all associated applications? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button class="btn border border-red-200 bg-white text-red-700 hover:bg-red-50 focus:ring-red-500">Delete job</button>
            </form>
        </section>
    </aside>
</div>
@endsection
