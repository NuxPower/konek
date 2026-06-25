@extends('layouts.app')
@section('content')
<div class="page-header">
    <div><p class="page-eyebrow">Hiring activity</p><h1>Applications</h1><p class="page-subtitle">Monitor applications submitted across every opportunity.</p></div>
</div>
<div class="table-wrap">
<table class="table">
    <thead>
        <tr>
            <th>Job</th><th>Freelancer</th><th>Status</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($applications as $application)
        <tr>
            <td>{{ $application->job->title ?? '-' }}</td>
            <td>{{ $application->freelancer->name ?? '-' }}</td>
            <td><span class="badge {{ $application->status === 'accepted' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($application->status) }}</span></td>
            <td>
                <a href="{{ route('admin.applications.show', $application) }}">Show</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="4">No applications found.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
{{ $applications->links() }}
@endsection
