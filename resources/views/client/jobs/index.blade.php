@extends('layouts.app')
@section('content')
<div class="page-header">
    <div><p class="page-eyebrow">Opportunities</p><h1>My jobs</h1><p class="page-subtitle">Create, publish, and manage your open roles.</p></div>
    <a href="{{ route('member.posted-jobs.create') }}" class="btn btn-primary">Post a job</a>
</div>
<div class="table-wrap">
<table class="table">
    <thead>
        <tr>
            <th>Title</th><th>Status</th><th>Deadline</th><th>Applications</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jobs as $job)
        <tr>
            <td>{{ $job->title }}</td>
            <td><span class="badge {{ $job->status === 'published' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($job->status) }}</span></td>
            <td>{{ $job->deadline?->format('M d, Y') ?? 'Open' }}</td>
            <td>{{ $job->applications_count }}</td>
            <td>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('member.posted-jobs.show', $job) }}">View</a>
                    <a href="{{ route('member.posted-jobs.edit', $job) }}">Edit</a>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="empty-state">No jobs yet. Post your first opportunity.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
{{ $jobs->links() }}
@endsection
