@extends('layouts.app')

@section('title', $user->name . ' - Profile')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <button onclick="history.back()" 
                       class="text-blue-600 hover:text-blue-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back
                </button>
                <h1 class="text-3xl font-bold text-gray-800">{{ $user->name }}'s Profile</h1>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Profile Content -->
        <div class="lg:col-span-2">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">About</h2>
                
                @if($user->role === 'freelancer' && $user->freelancer && $user->freelancer->title)
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-blue-600">{{ $user->freelancer->title }}</h3>
                    </div>
                @endif

                @if($user->bio)
                    <div class="mb-4">
                        <p class="text-gray-700">{{ $user->bio }}</p>
                    </div>
                @elseif($user->role === 'freelancer' && $user->freelancer && $user->freelancer->bio)
                    <div class="mb-4">
                        <div class="text-gray-700 prose prose-sm max-w-none">
                            {!! nl2br(e($user->freelancer->bio)) !!}
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 italic">No bio available.</p>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <p class="text-gray-900">{{ $user->email }}</p>
                    </div>
                    @if($user->phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <p class="text-gray-900">{{ $user->phone }}</p>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">User Type</label>
                        <p class="text-gray-900">{{ ucfirst($user->role) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Member Since</label>
                        <p class="text-gray-900">{{ $user->created_at->format('F Y') }}</p>
                    </div>

                    @if($user->role === 'freelancer' && $user->freelancer)
                        @if($user->freelancer->experience_level)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Experience Level</label>
                                <p class="text-gray-900">{{ ucfirst($user->freelancer->experience_level) }}</p>
                            </div>
                        @endif
                        
                        @if($user->freelancer->hourly_rate)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hourly Rate</label>
                                <p class="text-gray-900">₱{{ number_format($user->freelancer->hourly_rate, 2) }}/hour</p>
                            </div>
                        @endif
                        
                        @if($user->freelancer->availability)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                                <p class="text-gray-900">{{ ucfirst(str_replace('-', ' ', $user->freelancer->availability)) }}</p>
                            </div>
                        @endif
                        
                        @if($user->freelancer->location)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                <p class="text-gray-900">{{ $user->freelancer->location }}</p>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Languages (for freelancers) -->
                @if($user->role === 'freelancer' && $user->freelancer && $user->freelancer->languages && count($user->freelancer->languages) > 0)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Languages</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->freelancer->languages as $language)
                                <span class="inline-flex px-3 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded-full">
                                    {{ $language }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Skills Section (if freelancer) -->
            @if($user->role === 'freelancer' && $user->freelancer && $user->freelancer->skills && $user->freelancer->skills->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Skills & Expertise</h2>
                    <div class="space-y-3">
                        @foreach($user->freelancer->skills as $skill)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $skill->name }}</span>
                                    @if($skill->pivot && $skill->pivot->proficiency_level)
                                        <span class="ml-2 text-sm text-gray-600">
                                            ({{ ucfirst($skill->pivot->proficiency_level) }})
                                        </span>
                                    @endif
                                </div>
                                @if($skill->pivot && $skill->pivot->years_experience)
                                    <span class="text-sm text-blue-600 font-medium">
                                        {{ $skill->pivot->years_experience }} {{ $skill->pivot->years_experience == 1 ? 'year' : 'years' }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Portfolio Links (if freelancer) -->
            @if($user->role === 'freelancer' && $user->freelancer && ($user->freelancer->portfolio_url || $user->freelancer->linkedin_url || $user->freelancer->github_url))
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Portfolio & Links</h2>
                    <div class="space-y-3">
                        @if($user->freelancer->portfolio_url)
                            <a href="{{ $user->freelancer->portfolio_url }}" target="_blank" 
                               class="flex items-center text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Portfolio Website
                            </a>
                        @endif
                        
                        @if($user->freelancer->linkedin_url)
                            <a href="{{ $user->freelancer->linkedin_url }}" target="_blank" 
                               class="flex items-center text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                LinkedIn Profile
                            </a>
                        @endif
                        
                        @if($user->freelancer->github_url)
                            <a href="{{ $user->freelancer->github_url }}" target="_blank" 
                               class="flex items-center text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                </svg>
                                GitHub Profile
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Work History -->
            @if($user->role === 'freelancer')
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Completed Work</h2>
                    
                    @php
                        $completedApplications = $user->applications()
                            ->where('status', 'completed')
                            ->with('job')
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp

                    @if($completedApplications->count() > 0)
                        <div class="space-y-4">
                            @foreach($completedApplications as $application)
                                <div class="border rounded-lg p-4">
                                    <h3 class="font-semibold text-gray-900">{{ $application->job->title }}</h3>
                                    <p class="text-gray-600 text-sm">Completed on {{ $application->updated_at->format('M d, Y') }}</p>
                                    @if($application->job->description)
                                        <p class="text-gray-700 text-sm mt-2">{{ Str::limit($application->job->description, 150) }}</p>
                                    @endif
                                    @if($application->proposed_rate)
                                        <p class="text-blue-600 font-medium text-sm mt-2">₱{{ number_format($application->proposed_rate, 2) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">No completed projects yet.</p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Profile Summary -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="text-center">
                    @if($user->role === 'freelancer' && $user->freelancer && $user->freelancer->avatar)
                        <img src="{{ Storage::url($user->freelancer->avatar) }}" 
                             alt="{{ $user->name }}" 
                             class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                    @else
                        <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-blue-600 font-bold text-2xl">
                                {{ substr($user->name, 0, 2) }}
                            </span>
                        </div>
                    @endif
                    
                    <h3 class="font-bold text-xl text-gray-900">{{ $user->name }}</h3>
                    @if($user->role === 'freelancer' && $user->freelancer && $user->freelancer->title)
                        <p class="text-gray-600">{{ $user->freelancer->title }}</p>
                    @else
                        <p class="text-gray-600">{{ ucfirst($user->role) }}</p>
                    @endif
                    
                    @if($user->role === 'freelancer')
                        <div class="mt-4 pt-4 border-t">
                            @php
                                $completedCount = $user->applications()->where('status', 'completed')->count();
                                $totalApplications = $user->applications()->count();
                                $skillsCount = $user->freelancer && $user->freelancer->skills ? $user->freelancer->skills->count() : 0;
                            @endphp
                            
                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div>
                                    <p class="text-2xl font-bold text-blue-600">{{ $completedCount }}</p>
                                    <p class="text-xs text-gray-600">Completed Projects</p>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-green-600">{{ $skillsCount }}</p>
                                    <p class="text-xs text-gray-600">Skills</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Contact Information</h2>
                
                <div class="space-y-3">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-gray-900">{{ $user->email }}</span>
                    </div>
                    
                    @if($user->phone)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span class="text-gray-900">{{ $user->phone }}</span>
                        </div>
                    @endif
                    
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4h6m-6 4h6"></path>
                        </svg>
                        <span class="text-gray-900">Member since {{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection