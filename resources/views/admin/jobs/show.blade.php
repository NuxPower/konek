@extends('layouts.app')

@section('content')
<a href="{{ route('admin.jobs.index') }}" class="mb-6 inline-flex text-sm font-semibold text-slate-500 hover:text-emerald-800">← Back to jobs</a>
<div class="page-header">
    <div><p class="page-eyebrow">{{ $job->category->name ?? 'Opportunity' }}</p><h1>{{ $job->title }}</h1><p class="page-subtitle">Posted by {{ $job->client->name ?? 'Unknown client' }} on {{ $job->created_at->format('M d, Y') }}</p></div>
    <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-primary">Edit job</a>
</div>
<section class="panel">
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <div class="job-fact"><span>Status</span><strong>{{ ucfirst($job->status) }}</strong></div>
        <div class="job-fact"><span>Work type</span><strong>{{ ucfirst(str_replace('-', ' ', $job->type)) }}</strong></div>
        <div class="job-fact"><span>Experience</span><strong>{{ ucfirst($job->experience_level) }}</strong></div>
        <div class="job-fact"><span>Deadline</span><strong>{{ $job->deadline?->format('M d, Y') ?? 'Open' }}</strong></div>
    </div>
    <div class="my-8 border-t border-slate-100"></div>
    <h2>Description</h2>
    <div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $job->description }}</div>
    <div class="my-8 border-t border-slate-100"></div>
    <h2>Requirements</h2>
    <div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $job->requirements }}</div>
</section>
@endsection
