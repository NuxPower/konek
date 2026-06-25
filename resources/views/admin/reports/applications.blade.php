@extends('layouts.app')
@section('content')
<a href="{{ route('admin.reports.index') }}" class="mb-6 inline-flex text-sm font-semibold text-slate-500 hover:text-emerald-800">← Back to reports</a>
<div class="page-header">
    <div><p class="page-eyebrow">Report</p><h1>Applications</h1><p class="page-subtitle">Submission volume, review progress, and candidate outcomes.</p></div>
    <form method="POST" action="{{ route('admin.reports.export') }}" class="!m-0 !max-w-none !border-0 !bg-transparent !p-0 !shadow-none">
        @csrf
        <input type="hidden" name="report" value="applications">
        @foreach($filters as $key => $value)<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endforeach
        <button class="btn btn-primary">Export CSV</button>
    </form>
</div>
<form method="GET" class="job-filter-panel">
    <select name="status"><option value="">All statuses</option>@foreach(['pending','reviewing','shortlisted','accepted','rejected','withdrawn'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
    <input type="date" name="from" value="{{ request('from') }}" aria-label="From date">
    <input type="date" name="to" value="{{ request('to') }}" aria-label="To date">
    <button class="btn btn-primary">Apply filters</button>
    <a href="{{ route('admin.reports.applications') }}" class="btn btn-secondary">Reset</a>
</form>
<p class="mb-4 text-sm text-slate-500"><strong class="text-slate-800">{{ $applications->total() }}</strong> matching applications</p>
<div class="table-wrap"><table class="table">
    <thead><tr><th>Job</th><th>Client</th><th>Freelancer</th><th>Status</th><th>Rate</th><th>Submitted</th></tr></thead>
    <tbody>
        @forelse($applications as $application)
        <tr><td>{{ $application->job->title ?? '—' }}</td><td>{{ $application->job->client->name ?? '—' }}</td><td>{{ $application->freelancer->name ?? '—' }}</td><td><span class="badge badge-application-{{ $application->status }}">{{ ucfirst($application->status) }}</span></td><td>{{ $application->proposed_rate ? '₱'.number_format((float) $application->proposed_rate) : '—' }}</td><td>{{ $application->created_at->format('M d, Y') }}</td></tr>
        @empty<tr><td colspan="6" class="empty-state">No applications match these filters.</td></tr>@endforelse
    </tbody>
</table></div>
<div class="mt-6">{{ $applications->links() }}</div>
@endsection
