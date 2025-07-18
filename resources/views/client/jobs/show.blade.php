@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <a href="{{ route('client.jobs.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">{{ $job->title }}</h1>
            </div>
            
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($job->status === 'open') bg-green-100 text-green-800
                    @elseif($job->status === 'closed') bg-red-100 text-red-800
                    @else bg-yellow-100 text-yellow-800 @endif">
                    {{ ucfirst($job->status) }}
                </span>
                
                <div class="relative">
                    <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-md" onclick="toggleDropdown()">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div id="dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                        <a href="{{ route('client.jobs.edit', $job) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-edit mr-2"></i>Edit Job
                        </a>
                        <form method="POST" action="{{ route('client.jobs.duplicate', $job) }}" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-copy mr-2"></i>Duplicate Job
                            </button>
                        </form>
                        @if($job->status === 'open')
                            <form method="POST" action="{{ route('client.jobs.close', $job) }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-lock mr-2"></i>Close Job
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('client.jobs.reopen', $job) }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-unlock mr-2"></i>Reopen Job
                                </button>
                            </form>
                        @endif
                        <div class="border-t"></div>
                        <form method="POST" action="{{ route('client.jobs.destroy', $job) }}" class="block" onsubmit="return confirm('Are you sure you want to delete this job?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                <i class="fas fa-trash mr-2"></i>Delete Job
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-paper-plane text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Applications</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['applications_count'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_applications'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Accepted</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['accepted_applications'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-eye text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Views</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['views_count'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Job Details -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Job Description</h2>
                    <div class="prose max-w-none">
                        {!! nl2br(e($job->description)) !!}
                    </div>
                </div>

                <!-- Applications -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Applications ({{ $job->applications->count() }})</h2>
                    
                    @if($job->applications->count() > 0)
                        <div class="space-y-4">
                            @foreach($job->applications as $application)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600"></i>
                                            </div>
                                            <div class="ml-3">
                                                <h4 class="font-medium text-gray-900">
                                                    {{ $application->freelancer->user->name ?? 'Unknown User' }}
                                                </h4>
                                                <p class="text-sm text-gray-600">Applied {{ $application->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($application->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($application->status === 'accepted') bg-green-100 text-green-800
                                            @elseif($application->status === 'rejected') bg-red-100 text-red-800
                                            @elseif($application->status === 'reviewing') bg-blue-100 text-blue-800
                                            @elseif($application->status === 'shortlisted') bg-purple-100 text-purple-800
                                            @endif">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-700 mb-3">{{ $application->cover_letter }}</p>
                                    
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-600">
                                                Proposed Rate: ${{ number_format($application->proposed_rate ?? 0, 2) }}
                                                {{ $application->rate_type === 'hourly' ? '/hr' : '' }}
                                            </span>
                                            @if($application->estimated_hours)
                                                <span class="text-sm text-gray-600">
                                                    Estimated Time: {{ $application->estimated_hours }} hours
                                                </span>
                                            @endif
                                        </div>
                                        
                                        @if($application->status === 'pending')
                                            <div class="flex space-x-2">
                                                <button onclick="updateApplicationStatus({{ $application->id }}, 'accepted')" 
                                                        class="text-green-600 hover:text-green-800 text-sm">Accept</button>
                                                <button onclick="updateApplicationStatus({{ $application->id }}, 'rejected')" 
                                                        class="text-red-600 hover:text-red-800 text-sm">Reject</button>
                                                <button onclick="updateApplicationStatus({{ $application->id }}, 'shortlisted')" 
                                                        class="text-purple-600 hover:text-purple-800 text-sm">Shortlist</button>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Portfolio Links -->
                                    @if($application->portfolio_links && is_array($application->portfolio_links) && count($application->portfolio_links) > 0)
                                        <div class="mt-3 pt-3 border-t border-gray-200">
                                            <p class="text-sm font-medium text-gray-700 mb-2">Portfolio Links:</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($application->portfolio_links as $link)
                                                    <a href="{{ $link }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                                        <i class="fas fa-external-link-alt mr-1"></i>View Portfolio
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Client Notes -->
                                    @if($application->client_notes)
                                        <div class="mt-3 pt-3 border-t border-gray-200">
                                            <p class="text-sm font-medium text-gray-700 mb-1">Client Notes:</p>
                                            <p class="text-sm text-gray-600">{{ $application->client_notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-600">No applications yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Job Information Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Job Information</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Budget</p>
                            @if($job->budget_min || $job->budget_max)
                                <p class="text-lg font-semibold text-gray-900">
                                    @if($job->budget_min && $job->budget_max)
                                        ${{ number_format($job->budget_min, 2) }} - ${{ number_format($job->budget_max, 2) }}
                                    @elseif($job->budget_min)
                                        From ${{ number_format($job->budget_min, 2) }}
                                    @elseif($job->budget_max)
                                        Up to ${{ number_format($job->budget_max, 2) }}
                                    @endif
                                </p>
                            @elseif($job->budget)
                                <p class="text-lg font-semibold text-gray-900">${{ number_format($job->budget, 2) }}</p>
                            @else
                                <p class="text-lg font-semibold text-gray-900">Budget not specified</p>
                            @endif
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Payment Type</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $job->type === 'fixed' ? 'Fixed Price' : 'Hourly Rate' }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Experience Level</p>
                            <p class="text-lg font-semibold text-gray-900">{{ ucfirst($job->experience_level) }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Duration</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $job->duration }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Deadline</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $job->deadline ? $job->deadline->format('M d, Y') : 'No deadline set' }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Posted</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $job->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Required Skills</h4>
                        <div class="flex flex-wrap gap-2">
                            @forelse($job->skills as $skill)
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                    {{ $skill->name }}
                                </span>
                            @empty
                                <span class="text-sm text-gray-500">No specific skills required</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDropdown() {
    const dropdown = document.getElementById('dropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('dropdown');
    const button = event.target.closest('button');
    
    if (!button || !button.getAttribute('onclick')) {
        dropdown.classList.add('hidden');
    }
});

// Simple function to update application status
function updateApplicationStatus(applicationId, status) {
    if (confirm(`Are you sure you want to ${status} this application?`)) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/client/applications/${applicationId}/${status}`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection