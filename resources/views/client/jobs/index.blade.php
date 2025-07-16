<!-- Client Jobs Index -->
@extends('layouts.app')
@section('content')
<h1>My Jobs</h1>
<a href="{{ route('client.jobs.create') }}" class="btn btn-primary mb-2">Add Job</a>
@if(session('status'))
    <x-alert type="success">{{ session('status') }}</x-alert>
@endif
<table class="table">
    <thead>
        <tr>
            <th>Title</th><th>Status</th><th>Applications</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jobs as $job)
        <tr>
            <td>{{ $job->title }}</td>
            <td>{{ ucfirst($job->status) }}</td>
            <td>{{ $job->applications_count ?? $job->applications->count() }}</td>
            <td>
                <a href="{{ route('client.jobs.show', $job) }}">Show</a> |
                <a href="{{ route('client.jobs.edit', $job) }}">Edit</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="4">No jobs found.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $jobs->links() }}
@endsection 