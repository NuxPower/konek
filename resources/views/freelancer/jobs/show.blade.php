@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $job->title }}</h1>
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <i class="fas fa-building mr-1"></i>
                        <span>{{ $job->client->company_name ?? 'Private Client' }}</span>
                        <span class="mx-2">•</span>
                        <span>Posted {{ $job->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="saveJob({{ $job->id }})" class="text-gray-400 hover:text-blue-600">
                        <i class="fas fa-bookmark text-lg"></i>
                    </button>
                    @if($matchPercentage > 0)
                        <div class="text-sm">
                            <span class="text-gray-500">Match:</span>
                            <span class="font-semibold {{ $matchPercentage >= 70 ? 'text-green-600' : ($matchPercentage >= 40 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $matchPercentage }}%
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Job Details -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-1">Budget</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $job->formatted_budget }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-1">Type</div>
                    <div class="text-lg font-semibold text-gray-900">{{ ucfirst($job->type) }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-1">Experience Level</div>
                    <div class="text-lg font-semibold text-gray-900">{{ ucfirst($job->experience_level) }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-1">Deadline</div>
                    <div class="text-lg font-semibold text-gray-900">
                        {{ $job->deadline ? $job->deadline->format('M d, Y') : 'Flexible' }}
                    </div>
                </div>
            </div>

            <!-- Application Status -->
            @if($hasApplied)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-blue-600 mr-2"></i>
                        <span class="text-blue-800 font-medium">
                            You have applied to this job
                        </span>
                        <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                            {{ ucfirst($application->status) }}
                        </span>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('freelancer.applications.show', $application) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            View your application →
                        </a>
                    </div>
                </div>
            @else
                <div class="flex justify-center mb-6">
                    @if($job->status === 'open')
                        <a href="{{ route('freelancer.jobs.apply', $job) }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                            Apply for this Job
                        </a>
                    @else
                        <div class="bg-gray-100 text-gray-600 px-6 py-3 rounded-lg">
                            This job is no longer accepting applications
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Job Description -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Job Description</h2>
            <div class="prose max-w-none">
                {!! nl2br(e($job->description)) !!}
            </div>
        </div>

        <!-- Skills Required -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Skills Required</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($job->skills as $skill)
                    <span class="px-3 py-2 bg-blue-100 text-blue-800 rounded-lg font-medium">
                        {{ $skill->name }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Client Information -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">About the Client</h2>
            <div class="space-y-3">
                <div>
                    <span class="text-gray-600">Member since:</span>
                    <span class="font-medium">{{ $job->client->created_at->format('M Y') }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Total jobs posted:</span>
                    <span class="font-medium">{{ $job->client ? $job->client->jobs()->count() : 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Job Activity -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Job Activity</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $job->applications->count() }}</div>
                    <div class="text-sm text-gray-500">Applications</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $job->views_count ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Views</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $job->created_at->diffInDays() }}</div>
                    <div class="text-sm text-gray-500">Days ago</div>
                </div>
            </div>
        </div>

        <!-- Similar Jobs -->
        @if($similarJobs->isNotEmpty())
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Similar Jobs</h2>
                <div class="space-y-4">
                    @foreach($similarJobs as $similarJob)
                        <div class="border-b border-gray-200 pb-4 last:border-b-0">
                            <h3 class="font-medium mb-2">
                                <a href="{{ route('freelancer.jobs.show', $similarJob) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $similarJob->title }}
                                </a>
                            </h3>
                            <p class="text-gray-600 text-sm mb-2">{{ Str::limit($similarJob->description, 100) }}</p>
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>{{ $similarJob->formatted_budget }}</span>
                                <span>{{ $similarJob->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function saveJob(jobId) {
    fetch(`/freelancer/jobs/${jobId}/save`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Job saved successfully!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving job. Please try again.');
    });
}
</script>
@endsection    <span class="text-gray-600">Company:</span>
                    <span class="font-medium">{{ $job->client->company_name ?? 'Private Client' }}</span>
                </div>
                @if($job->client && $job->client->website)
                    <div>
                        <span class="text-gray-600">Website:</span>
                        <a href="{{ $job->client->website }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            {{ $job->client->website }}
                        </a>
                    </div>
                @endif
                <div>