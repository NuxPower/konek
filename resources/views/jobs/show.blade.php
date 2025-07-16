<!-- Public Jobs Show -->
@extends('layouts.app')
@section('content')
<h1>Job Details</h1>
<ul>
    <li><strong>Title:</strong> {{ $job->title }}</li>
    <li><strong>Type:</strong> {{ ucfirst($job->type) }}</li>
    <li><strong>Status:</strong> {{ ucfirst($job->status) }}</li>
    <li><strong>Client:</strong> {{ $job->client->name ?? '-' }}</li>
    <li><strong>Category:</strong> {{ $job->category->name ?? '-' }}</li>
    <li><strong>Created:</strong> {{ $job->created_at->format('Y-m-d') }}</li>
</ul>
<a href="{{ route('jobs.public') }}">Back to All Jobs</a>
@endsection 