<!-- Freelancer Applications Index -->
@extends('layouts.app')
@section('content')
<h1>My Applications</h1>
<table class="table">
    <thead>
        <tr>
            <th>Job</th><th>Status</th><th>Date</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($applications as $application)
        <tr>
            <td>{{ $application->job->title ?? '-' }}</td>
            <td>{{ ucfirst($application->status) }}</td>
            <td>{{ $application->created_at->format('Y-m-d') }}</td>
            <td>
                <a href="{{ route('freelancer.applications.show', $application) }}">Show</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="4">No applications found.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $applications->links() }}
@endsection 