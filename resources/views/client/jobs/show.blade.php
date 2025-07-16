<!-- Client Jobs Show -->
@extends('layouts.app')
@section('content')
<h1>Job Details</h1>
<ul>
    <li><strong>Title:</strong> {{ $job->title }}</li>
    <li><strong>Status:</strong> {{ ucfirst($job->status) }}</li>
    <li><strong>Applications:</strong> {{ $job->applications_count ?? $job->applications->count() }}</li>
    <li><strong>Category:</strong> {{ $job->category->name ?? '-' }}</li>
    <li><strong>Created:</strong> {{ $job->created_at->format('Y-m-d') }}</li>
</ul>
<a href="{{ route('client.jobs.edit', $job) }}">Edit</a> |
<a href="{{ route('client.jobs.index') }}">Back to My Jobs</a>
@endsection 