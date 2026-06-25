@extends('layouts.app')

@section('content')
<a href="{{ route('admin.applications.index') }}" class="mb-6 inline-flex text-sm font-semibold text-slate-500 hover:text-emerald-800">← Back to applications</a>
<div class="page-header">
    <div><p class="page-eyebrow">Application review</p><h1>{{ $application->freelancer->name ?? 'Applicant' }}</h1><p class="page-subtitle">{{ $application->job->title ?? 'Unavailable job' }} · Submitted {{ $application->created_at->format('M d, Y') }}</p></div>
    <span class="badge {{ $application->status === 'accepted' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($application->status) }}</span>
</div>
<section class="panel max-w-4xl">
    <h2>Cover letter</h2>
    <div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $application->cover_letter }}</div>
    <div class="mt-8 grid gap-3 sm:grid-cols-3">
        <div class="job-fact"><span>Proposed rate</span><strong>{{ $application->proposed_rate ? '₱'.number_format((float) $application->proposed_rate) : '—' }}</strong></div>
        <div class="job-fact"><span>Rate type</span><strong class="capitalize">{{ $application->rate_type ?? '—' }}</strong></div>
        <div class="job-fact"><span>Estimated hours</span><strong>{{ $application->estimated_hours ?? '—' }}</strong></div>
    </div>
</section>
@endsection
