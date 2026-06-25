@extends('layouts.app')

@section('content')
<div class="page-header">
    <div><p class="page-eyebrow">Candidates</p><h1>Applications</h1><p class="page-subtitle">Review and manage people interested in your opportunities.</p></div>
</div>

<form method="GET" action="{{ route('client.applications.index') }}" class="mb-6 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_1fr_auto]">
    <select name="job">
        <option value="">All jobs</option>
        @foreach($jobs as $job)<option value="{{ $job->id }}" @selected((int) request('job') === $job->id)>{{ $job->title }}</option>@endforeach
    </select>
    <select name="status">
        <option value="">All statuses</option>
        @foreach(['pending', 'reviewing', 'shortlisted', 'accepted', 'rejected', 'withdrawn'] as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    <button class="btn btn-primary">Filter</button>
</form>

<div class="table-wrap">
<table class="table">
    <thead><tr><th>Candidate</th><th>Job</th><th>Rate</th><th>Status</th><th>Submitted</th><th></th></tr></thead>
    <tbody>
        @forelse($applications as $application)
        <tr>
            <td>{{ $application->freelancer->name ?? '—' }}</td>
            <td>{{ $application->job->title ?? '—' }}</td>
            <td>{{ $application->proposed_rate ? '₱'.number_format((float) $application->proposed_rate) : '—' }}</td>
            <td><span class="badge badge-application-{{ $application->status }}">{{ ucfirst($application->status) }}</span></td>
            <td>{{ $application->created_at->format('M d, Y') }}</td>
            <td><a href="{{ route('client.applications.show', $application) }}">Review</a></td>
        </tr>
        @empty
        <tr><td colspan="6" class="empty-state">No applications match these filters.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
{{ $applications->links() }}
@endsection
