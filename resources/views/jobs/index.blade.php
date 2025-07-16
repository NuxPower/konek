<!-- Public Jobs Index -->
@extends('layouts.app')
@section('content')
<h1>All Jobs</h1>
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
            <td>{{ ucfirst($job->status) }}</td>
            <td>{{ $job->client->name ?? '-' }}</td>
            <td>
                <a href="{{ route('jobs.public.show', $job) }}">Show</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="5">No jobs found.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $jobs->links() }}
@endsection 