<!-- Freelancer Jobs Search -->
@extends('layouts.app')
@section('content')
<h1>Search Jobs</h1>
<form method="GET" action="{{ route('freelancer.jobs.search') }}" style="margin-bottom:1rem;">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search jobs..." style="width:200px;">
    <button type="submit">Search</button>
</form>
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
                <a href="{{ route('freelancer.jobs.show', $job) }}">Show</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="5">No jobs found.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $jobs->links() }}
@endsection 