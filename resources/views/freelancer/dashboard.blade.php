@extends('layouts.app')

@section('title', 'Freelancer Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            Welcome back, {{ Auth::user()->name }}!
        </h1>
        <p class="text-gray-600">Here's what's happening with your freelance work today.</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stats-card 
            title="Applications Sent" 
            :value="$stats['applications_sent']" 
            icon="document-text"
            color="blue" />
        
        <x-stats-card 
            title="Pending Applications" 
            :value="$stats['pending_applications']" 
            icon="clock"
            color="yellow" />
        
        <x-stats-card 
            title="Accepted Applications" 
            :value="$stats['accepted_applications']" 
            icon="check-circle"
            color="green" />
        
        <x-stats-card 
            title="Jobs Completed" 
            :value="$stats['jobs_completed']" 
            icon="check-badge"
            color="purple" />
    </div>

    <!-- Quick Stats Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Overview</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Success Rate</span>
                    <span class="font-semibold text-green-600">{{ number_format($stats['success_rate'], 1) }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Earned</span>
                    <span class="font-semibold text-gray-900">₱{{ number_format($stats['total_earned'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Average Rating</span>
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-900 mr-1">{{ number_format($stats['rating'], 1) }}</span>
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $stats['rating'])
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">This Month</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Applications</span>
                    <span class="font-semibold text-blue-600">{{ $applicationStats['this_month'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Acceptance Rate</span>
                    <span class="font-semibold text-green-600">{{ number_format($applicationStats['acceptance_rate'], 1) }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Earnings</span>
                    <span class="font-semibold text-gray-900">₱{{ number_format($earningsThisMonth, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('freelancer.jobs.browse') }}" 
                   class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                    Browse Jobs
                </a>
                <a href="{{ route('freelancer.applications.index') }}" 
                   class="block w-full bg-gray-100 text-gray-700 text-center py-2 px-4 rounded-md hover:bg-gray-200 transition-colors">
                    View Applications
                </a>
                <a href="{{ route('freelancer.profile.edit') }}" 
                   class="block w-full bg-gray-100 text-gray-700 text-center py-2 px-4 rounded-md hover:bg-gray-200 transition-colors">
                    Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Applications -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Applications</h2>
                    <a href="{{ route('freelancer.applications.index') }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($recentApplications->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentApplications as $application)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-medium text-gray-900 truncate pr-4">
                                        {{ $application->job->title }}
                                    </h3>
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'accepted' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'shortlisted' => 'bg-blue-100 text-blue-800'
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$application->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">
                                    Client: {{ $application->job->client->name }}
                                </p>
                                <div class="flex justify-between items-center text-sm text-gray-500">
                                    <span>Applied {{ $application->created_at->diffForHumans() }}</span>
                                    <span class="font-medium text-green-600">{{ $application->job->formatted_budget }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-sm font-medium text-gray-900 mb-1">No applications yet</h3>
                        <p class="text-sm text-gray-500 mb-4">Start browsing and applying to jobs to see them here.</p>
                        <a href="{{ route('freelancer.jobs.browse') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm">
                            Browse Jobs
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Suggested Jobs -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-900">Suggested Jobs</h2>
                    <a href="{{ route('freelancer.jobs.browse') }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($suggestedJobs->count() > 0)
                    <div class="space-y-4">
                        @foreach($suggestedJobs as $job)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-medium text-gray-900 truncate pr-4">
                                        <a href="{{ route('freelancer.jobs.show', $job) }}" class="hover:text-blue-600">
                                            {{ $job->title }}
                                        </a>
                                    </h3>
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $job->status_color }}">
                                        {{ ucfirst($job->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2 line-clamp-2">
                                    {{ Str::limit($job->description, 100) }}
                                </p>
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach($job->skills->take(3) as $skill)
                                        <span class="inline-flex px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                            {{ $skill->name }}
                                        </span>
                                    @endforeach
                                    @if($job->skills->count() > 3)
                                        <span class="inline-flex px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">
                                            +{{ $job->skills->count() - 3 }} more
                                        </span>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">{{ $job->time_ago }}</span>
                                    <div class="text-right">
                                        <div class="font-medium text-green-600">{{ $job->formatted_budget }}</div>
                                        <div class="text-gray-500">{{ $job->applications_count }} applications</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6" />
                        </svg>
                        <h3 class="text-sm font-medium text-gray-900 mb-1">No jobs available</h3>
                        <p class="text-sm text-gray-500 mb-4">Complete your profile to get better job recommendations.</p>
                        <a href="{{ route('freelancer.profile.edit') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm">
                            Complete Profile
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Activity Timeline (Optional) -->
    <div class="mt-8 bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Recent Activity</h2>
        </div>
        <div class="p-6">
            <div class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p>Activity timeline will appear here as you use the platform.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush