@extends('layouts.app')
@section('content')
<div class="page-header">
    <div><p class="page-eyebrow">Marketplace</p><h1>Jobs</h1><p class="page-subtitle">Review and manage opportunities posted across the platform.</p></div>
</div>
@if(session('status'))
    <x-alert type="success">{{ session('status') }}</x-alert>
@endif
<div class="table-wrap">
<table class="table">
    <thead>
        <tr>
            <th>Title</th><th>Type</th><th>Status</th><th>Client</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jobs as $job)
        <tr>
            <td>{{ $job->title }}</td>
            <td>{{ ucfirst($job->type) }}</td>
            <td><span class="badge {{ $job->status === 'published' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($job->status) }}</span></td>
            <td>{{ $job->client->name ?? '-' }}</td>
            <td>
                <a href="{{ route('admin.jobs.show', $job) }}">Show</a> |
                <a href="{{ route('admin.jobs.edit', $job) }}">Edit</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="5">No jobs found.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
{{ $jobs->links() }}
@endsection
