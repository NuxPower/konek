<!-- Admin Jobs Index -->
@extends('layouts.app')
@section('content')
<h1>Jobs</h1>
<a href="{{ route('admin.jobs.create') }}" class="btn btn-primary mb-2">Add Job</a>
@if(session('status'))
    <x-alert type="success">{{ session('status') }}</x-alert>
@endif
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
                <a href="{{ route('admin.jobs.show', $job) }}">Show</a> |
                <a href="{{ route('admin.jobs.edit', $job) }}">Edit</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="5">No jobs found.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $jobs->links() }}
@endsection 