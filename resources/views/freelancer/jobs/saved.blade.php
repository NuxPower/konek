@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Saved Jobs</h1>
            <a href="{{ route('freelancer.jobs.browse') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Browse Jobs
            </a>
        </div>

        @if($savedJobs->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <i class="fas fa-bookmark text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No saved jobs yet</h3>
                <p class="text-gray-600 mb-4">Save jobs you're interested in to easily find them later.</p>
                <a href="{{ route('freelancer.jobs.browse') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Browse Jobs
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($savedJobs as $job)
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold mb-2">
                                    <a href="{{ route('freelancer.jobs.show', $job) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $job->title }}
                                    </a>
                                </h3>
                                <p class="text-gray-600 mb-3">{{ Str::limit($job->description, 200) }}</p>
                                
                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-3">
                                    <span>${{ number_format($job->budget) }}</span>
                                    <span>{{ ucfirst($job->type) }}</span>
                                    <span>{{ ucfirst($job->experience_level) }}</span>
                                    <span>{{ $job->created_at->diffForHumans() }}</span>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @foreach($job->skills->take(5) as $skill)
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                            {{ $skill->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="flex flex-col items-end space-y-2">
                                <button onclick="unsaveJob({{ $job->id }})" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <a href="{{ route('freelancer.jobs.show', $job) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
function unsaveJob(jobId) {
    if (confirm('Are you sure you want to remove this job from your saved jobs?')) {
        fetch(`/freelancer/jobs/${jobId}/unsave`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing job. Please try again.');
        });
    }
}
</script>
@endsection