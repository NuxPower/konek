<!-- Client Applications Show -->
@extends('layouts.app')
@section('content')
<h1>Application Details</h1>
<ul>
    <li><strong>Job:</strong> {{ $application->job->title ?? '-' }}</li>
    <li><strong>Freelancer:</strong> {{ $application->freelancer->name ?? '-' }}</li>
    <li><strong>Status:</strong> {{ ucfirst($application->status) }}</li>
    <li><strong>Cover Letter:</strong> {{ $application->cover_letter }}</li>
    <li><strong>Submitted:</strong> {{ $application->created_at->format('Y-m-d') }}</li>
</ul>
<a href="{{ route('client.applications.index') }}">Back to Applications</a>
@endsection 