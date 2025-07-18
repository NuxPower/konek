<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - FreelanceHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Search Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">Search Results</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Found {{ $jobs->total() }} jobs matching your criteria
                    </p>
                </div>
                
                <!-- Search Form -->
                <div class="mt-4 lg:mt-0 lg:ml-6">
                    <form method="GET" action="{{ route('search') }}" class="flex flex-col sm:flex-row gap-3">
                        <div class="relative">
                            <input type="text" 
                                   name="q" 
                                   value="{{ $query }}"
                                   placeholder="Search jobs..."
                                   class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                        </div>
                        
                        <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Categories</option>
                            <!-- Add category options here -->
                        </select>
                        
                        <input type="text" 
                               name="location" 
                               value="{{ $location }}"
                               placeholder="Location"
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Search
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Results -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="lg:grid lg:grid-cols-4 lg:gap-8">
            <!-- Filters Sidebar -->
            <div class="hidden lg:block">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
                    
                    <!-- Job Type Filter -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Job Type</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Full-time</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Part-time</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Contract</span>
                            </label>
                        </div>
                    </div>

                    <!-- Budget Range Filter -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Budget Range</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">$0 - $500</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">$500 - $1000</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">$1000+</span>
                            </label>
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    <button class="w-full px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Clear All Filters
                    </button>
                </div>
            </div>

            <!-- Results -->
            <div class="lg:col-span-3">
                <!-- Sort Options -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <button class="lg:hidden px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Filters
                        </button>
                    </div>
                    
                    <div class="flex items-center space-x-2 mt-4 sm:mt-0">
                        <span class="text-sm text-gray-600">Sort by:</span>
                        <select class="px-3 py-1 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option>Most Recent</option>
                            <option>Budget: High to Low</option>
                            <option>Budget: Low to High</option>
                            <option>Most Relevant</option>
                        </select>
                    </div>
                </div>

                <!-- Job Cards -->
                <div class="space-y-6">
                    @forelse($jobs as $job)
                    <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow p-6">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between">
                            <div class="flex-1">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 hover:text-blue-600 cursor-pointer">
                                            <a href="{{ route('jobs.show', $job->id) }}">{{ $job->title }}</a>
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Posted by {{ $job->user->name }} • {{ $job->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $job->job_type }}
                                        </span>
                                    </div>
                                </div>
                                
                                <p class="mt-3 text-gray-600 line-clamp-3">
                                    {{ Str::limit($job->description, 200) }}
                                </p>
                                
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @if($job->requirements)
                                        @foreach(explode(',', $job->requirements) as $skill)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ trim($skill) }}
                                            </span>
                                        @endforeach
                                    @endif
                                </div>
                                
                                <div class="mt-4 flex items-center justify-between">
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span class="flex items-center">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            {{ $job->location }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-folder mr-1"></i>
                                            {{ $job->category->name ?? 'Uncategorized' }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-users mr-1"></i>
                                            {{ $job->applications_count ?? 0 }} applications
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-semibold text-gray-900">
                                            ${{ number_format($job->budget, 2) }}
                                        </p>
                                        <p class="text-sm text-gray-500">{{ $job->budget_type }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No jobs found</h3>
                        <p class="text-gray-500">Try adjusting your search criteria or browse all jobs.</p>
                        <a href="{{ route('home') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Browse All Jobs
                        </a>
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($jobs->hasPages())
                <div class="mt-8">
                    {{ $jobs->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>