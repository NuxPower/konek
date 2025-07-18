@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Browse Jobs</h1>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600">{{ $jobs->total() }} jobs found</span>
            <a href="{{ route('freelancer.jobs.saved') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-bookmark mr-1"></i>Saved Jobs
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Filters Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Filters</h2>
                <form method="GET" action="{{ route('freelancer.jobs.browse') }}">
                    <!-- Search -->
                    <div class="mb-4">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ request('search') }}"
                               placeholder="Search jobs..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Skills -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Skills</label>
                        <div class="space-y-2 max-h-32 overflow-y-auto">
                            @foreach($skills as $skill)
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="skills[]" 
                                           value="{{ $skill->id }}"
                                           {{ in_array($skill->id, request('skills', [])) ? 'checked' : '' }}
                                           class="text-blue-600 rounded">
                                    <span class="ml-2 text-sm text-gray-700">{{ $skill->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Job Type -->
                    <div class="mb-4">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Job Type</label>
                        <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Types</option>
                            <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                            <option value="hourly" {{ request('type') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                        </select>
                    </div>

                    <!-- Experience Level -->
                    <div class="mb-4">
                        <label for="experience_level" class="block text-sm font-medium text-gray-700 mb-2">Experience Level</label>
                        <select name="experience_level" id="experience_level" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Levels</option>
                            <option value="entry" {{ request('experience_level') == 'entry' ? 'selected' : '' }}>Entry Level</option>
                            <option value="intermediate" {{ request('experience_level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="expert" {{ request('experience_level') == 'expert' ? 'selected' : '' }}>Expert</option>
                        </select>
                    </div>

                    <!-- Budget Range -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Budget Range</label>
                        <div class="flex space-x-2">
                            <input type="number" 
                                   name="budget_min" 
                                   placeholder="Min"
                                   value="{{ request('budget_min') }}"
                                   class="w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="number" 
                                   name="budget_max" 
                                   placeholder="Max"
                                   value="{{ request('budget_max') }}"
                                   class="w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Posted Within -->
                    <div class="mb-4">
                        <label for="posted_within" class="block text-sm font-medium text-gray-700 mb-2">Posted Within</label>
                        <select name="posted_within" id="posted_within" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Any Time</option>
                            <option value="1" {{ request('posted_within') == '1' ? 'selected' : '' }}>Last 24 hours</option>
                            <option value="7" {{ request('posted_within') == '7' ? 'selected' : '' }}>Last 7 days</option>
                            <option value="30" {{ request('posted_within') == '30' ? 'selected' : '' }}>Last 30 days</option>
                        </select>
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Apply Filters
                        </button>
                        <a href="{{ route('freelancer.jobs.browse') }}" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 text-center">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Jobs List -->
        <div class="lg:col-span-3">
            <!-- Sort Options -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ $jobs->total() }} jobs found</span>
                    <div class="flex items-center space-x-2">
                        <label for="sort" class="text-sm text-gray-700">Sort by:</label>
                        <select name="sort" id="sort" onchange="updateSort()" class="px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="budget_high" {{ request('sort') == 'budget_high' ? 'selected' : '' }}>Budget (High to Low)</option>
                            <option value="budget_low" {{ request('sort') == 'budget_low' ? 'selected' : '' }}>Budget (Low to High)</option>
                            <option value="deadline" {{ request('sort') == 'deadline' ? 'selected' : '' }}>Deadline</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Jobs Grid -->
            <div class="space-y-4">
                @forelse($jobs as $job)
                    <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold mb-2">
                                    <a href="{{ route('freelancer.jobs.show', $job) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $job->title }}
                                    </a>
                                </h3>
                                <p class="text-gray-600 mb-3">{{ Str::limit($job->description, 200) }}</p>
                                
                                <!-- Job Details -->
                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-3">
                                    <span class="flex items-center">
                                        <i class="fas fa-money-bill-wave mr-1"></i>
                                        {{ $job->formatted_budget }}
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ ucfirst($job->type) }}
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-user-tie mr-1"></i>
                                        {{ ucfirst($job->experience_level) }}
                                    </span>
                                    @if($job->deadline)
                                        <span class="flex items-center">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $job->deadline->format('M d, Y') }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Skills -->
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @foreach($job->skills->take(5) as $skill)
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                            {{ $skill->name }}
                                        </span>
                                    @endforeach
                                    @if($job->skills->count() > 5)
                                        <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm">
                                            +{{ $job->skills->count() - 5 }} more
                                        </span>
                                    @endif
                                </div>

                                <!-- Client Info -->
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-building mr-1"></i>
                                    <span>{{ $job->client->company_name ?? 'Private Client' }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $job->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="flex flex-col items-end space-y-2">
                                <!-- Job Match -->
                                @if($freelancerSkills->isNotEmpty())
                                    @php
                                        $matchPercentage = $job->skills->pluck('id')->intersect($freelancerSkills)->count() / max($job->skills->count(), 1) * 100;
                                    @endphp
                                    <div class="text-sm">
                                        <span class="text-gray-500">Match:</span>
                                        <span class="font-semibold {{ $matchPercentage >= 70 ? 'text-green-600' : ($matchPercentage >= 40 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ round($matchPercentage) }}%
                                        </span>
                                    </div>
                                @endif

                                <!-- Actions -->
                                <div class="flex space-x-2">
                                    <button onclick="saveJob({{ $job->id }})" class="text-gray-400 hover:text-blue-600">
                                        <i class="fas fa-bookmark"></i>
                                    </button>
                                    <a href="{{ route('freelancer.jobs.show', $job) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow p-8 text-center">
                        <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No jobs found</h3>
                        <p class="text-gray-600">Try adjusting your filters or search terms to find more jobs.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($jobs->hasPages())
                <div class="mt-6">
                    {{ $jobs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function updateSort() {
    const sortSelect = document.getElementById('sort');
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('sort', sortSelect.value);
    window.location.search = urlParams.toString();
}

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
            // Show success message or update UI
            alert('Job saved successfully!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving job. Please try again.');
    });
}
</script>
@endsection