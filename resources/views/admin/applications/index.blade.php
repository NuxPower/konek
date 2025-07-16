<!-- Admin Applications Index -->
@extends('layouts.app')
@section('content')
<h1>Applications</h1>
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
            <td>{{ ucfirst($application->status) }}</td>
            <td>
                <a href="{{ route('admin.applications.show', $application) }}">Show</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="4">No applications found.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $applications->links() }}
@endsection 