@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">My Jobs</h1>
        <a href="{{ route('client.jobs.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
            <i class="fas fa-plus mr-2"></i>Post New Job
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('client.jobs.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search jobs..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            
            <div class="min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                    <option value="hourly" {{ request('type') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition duration-200">
                    Filter
                </button>
                <a href="{{ route('client.jobs.index') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md transition duration-200">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Jobs List -->
    @if($jobs->count() > 0)
        <div class="space-y-4">
            @foreach($jobs as $job)
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-200">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                <a href="{{ route('client.jobs.show', $job) }}" class="hover:text-blue-600">
                                    {{ $job->title }}
                                </a>
                            </h3>
                            <p class="text-gray-600 mb-3">{{ Str::limit($job->description, 150) }}</p>
                            
                            <div class="flex flex-wrap gap-2 mb-3">
                                @forelse($job->skills as $skill)
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                        {{ $skill->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-500">No specific skills required</span>
                                @endforelse
                            </div>
                            
                            <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                <span>
                                    <i class="fas fa-dollar-sign mr-1"></i>
                                    @if($job->budget_min || $job->budget_max)
                                        @if($job->budget_min && $job->budget_max)
                                            ${{ number_format($job->budget_min, 2) }} - ${{ number_format($job->budget_max, 2) }}
                                        @elseif($job->budget_min)
                                            From ${{ number_format($job->budget_min, 2) }}
                                        @elseif($job->budget_max)
                                            Up to ${{ number_format($job->budget_max, 2) }}
                                        @endif
                                    @elseif($job->budget)
                                        ${{ number_format($job->budget, 2) }}
                                    @else
                                        Budget not specified
                                    @endif
                                </span>
                                <span><i class="fas fa-clock mr-1"></i>{{ $job->type === 'fixed' ? 'Fixed Price' : 'Hourly' }}</span>
                                <span><i class="fas fa-calendar mr-1"></i>{{ $job->deadline ? $job->deadline->format('M d, Y') : 'No deadline' }}</span>
                                <span><i class="fas fa-user mr-1"></i>{{ ucfirst($job->experience_level) }}</span>
                                <span><i class="fas fa-paper-plane mr-1"></i>{{ $job->applications->count() }} applications</span>
                            </div>
                        </div>
                        
                        <div class="flex flex-col items-end ml-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mb-2
                                @if($job->status === 'open') bg-green-100 text-green-800
                                @elseif($job->status === 'closed') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($job->status) }}
                            </span>
                            
                            <div class="flex space-x-2">
                                <a href="{{ route('client.jobs.show', $job) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                <a href="{{ route('client.jobs.edit', $job) }}" 
                                   class="text-green-600 hover:text-green-800 text-sm">Edit</a>
                                <form method="POST" action="{{ route('client.jobs.duplicate', $job) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-purple-600 hover:text-purple-800 text-sm">Duplicate</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $jobs->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-briefcase text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No jobs found</h3>
            <p class="text-gray-600 mb-4">You haven't posted any jobs yet or no jobs match your current filters.</p>
            <a href="{{ route('client.jobs.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200">
                Post Your First Job
            </a>
        </div>
    @endif
</div>
@endsection