@extends('layouts.app')
@section('content')
<a href="{{ route('admin.reports.index') }}" class="mb-6 inline-flex text-sm font-semibold text-slate-500 hover:text-emerald-800">← Back to reports</a>
<div class="page-header">
    <div><p class="page-eyebrow">Report</p><h1>Jobs</h1><p class="page-subtitle">Opportunity volume, status, ownership, and deadlines.</p></div>
    <form method="POST" action="{{ route('admin.reports.export') }}" class="!m-0 !max-w-none !border-0 !bg-transparent !p-0 !shadow-none">
        @csrf
        <input type="hidden" name="report" value="jobs">
        @foreach($filters as $key => $value)<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endforeach
        <button class="btn btn-primary">Export CSV</button>
    </form>
</div>
<form method="GET" class="job-filter-panel">
    <select name="status"><option value="">All statuses</option>@foreach(['draft','published','closed','cancelled'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
    <select name="type"><option value="">All work types</option>@foreach(['full-time','part-time','contract','internship'] as $type)<option value="{{ $type }}" @selected(request('type') === $type)>{{ ucfirst(str_replace('-', ' ', $type)) }}</option>@endforeach</select>
    <input type="date" name="from" value="{{ request('from') }}" aria-label="From date">
    <input type="date" name="to" value="{{ request('to') }}" aria-label="To date">
    <button class="btn btn-primary">Apply filters</button>
    <a href="{{ route('admin.reports.jobs') }}" class="btn btn-secondary">Reset</a>
</form>
<p class="mb-4 text-sm text-slate-500"><strong class="text-slate-800">{{ $jobs->total() }}</strong> matching jobs</p>
<div class="table-wrap"><table class="table">
    <thead><tr><th>Title</th><th>Type</th><th>Status</th><th>Client</th><th>Applications</th><th>Deadline</th></tr></thead>
    <tbody>
        @forelse($jobs as $job)
        <tr><td>{{ $job->title }}</td><td>{{ ucfirst(str_replace('-', ' ', $job->type)) }}</td><td><span class="badge {{ $job->status === 'published' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($job->status) }}</span></td><td>{{ $job->client->name ?? '—' }}</td><td>{{ $job->applications_count }}</td><td>{{ $job->deadline?->format('M d, Y') ?? 'Open' }}</td></tr>
        @empty<tr><td colspan="6" class="empty-state">No jobs match these filters.</td></tr>@endforelse
    </tbody>
</table></div>
<div class="mt-6">{{ $jobs->links() }}</div>
@endsection
