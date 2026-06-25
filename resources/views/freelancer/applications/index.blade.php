@extends('layouts.app')
@section('content')
<div class="page-header">
    <div><p class="page-eyebrow">Your progress</p><h1>My applications</h1><p class="page-subtitle">Track every opportunity you have applied for.</p></div>
</div>
<div class="table-wrap">
<table class="table">
    <thead>
        <tr>
            <th>Job</th><th>Client</th><th>Status</th><th>Submitted</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($applications as $application)
        <tr>
            <td>{{ $application->job->title ?? '-' }}</td>
            <td>{{ $application->job->client->name ?? '—' }}</td>
            <td><span class="badge badge-application-{{ $application->status }}">{{ ucfirst($application->status) }}</span></td>
            <td>{{ $application->created_at->format('M d, Y') }}</td>
            <td>
                <a href="{{ route('freelancer.applications.show', $application) }}">View</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="empty-state">No applications yet. Browse opportunities to get started.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
{{ $applications->links() }}
@endsection
