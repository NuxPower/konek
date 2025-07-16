<!-- Admin Jobs Report -->
@extends('layouts.app')
@section('content')
<h1>Jobs Report</h1>
<table class="table">
    <thead>
        <tr>
            <th>Title</th><th>Type</th><th>Status</th><th>Client</th><th>Category</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jobs as $job)
        <tr>
            <td>{{ $job->title }}</td>
            <td>{{ ucfirst($job->type) }}</td>
            <td>{{ ucfirst($job->status) }}</td>
            <td>{{ $job->client->name ?? '-' }}</td>
            <td>{{ $job->category->name ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="5">No jobs found.</td></tr>
        @endforelse
    </tbody>
</table>
@if(method_exists($jobs, 'links'))
    {{ $jobs->links() }}
@endif
@endsection 