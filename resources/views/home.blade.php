@extends('layouts.app')

@section('title', 'Find Your Perfect Freelance Job')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 text-white py-20">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                Find Your Perfect <span class="text-yellow-300">Freelance</span> Job
            </h1>
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto text-gray-200">
                Connect with top clients and discover amazing opportunities in your field
            </p>
            
            <!-- Search Form -->
            <div class="max-w-4xl mx-auto">
                <form action="{{ route('search', [], false) ?? '/search' }}" method="GET" class="bg-white rounded-lg shadow-xl p-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <input type="text" 
                               name="q" 
                               placeholder="Search jobs, skills, or keywords..."
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700">
                    </div>
                    <div>
                        <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700">
                            <option value="">All Categories</option>
                            @if(isset($topCategories) && $topCategories->count() > 0)
                                @foreach($topCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105">
                            Search Jobs
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-blue-600 mb-2 hover:scale-110 transition-transform duration-200">
                    {{ isset($stats['total_jobs']) ? number_format($stats['total_jobs']) : '1,234' }}
                </div>
                <div class="text-gray-600">Active Jobs</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-green-600 mb-2 hover:scale-110 transition-transform duration-200">
                    {{ isset($stats['total_freelancers']) ? number_format($stats['total_freelancers']) : '5,678' }}
                </div>
                <div class="text-gray-600">Freelancers</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-purple-600 mb-2 hover:scale-110 transition-transform duration-200">
                    {{ isset($stats['total_clients']) ? number_format($stats['total_clients']) : '2,345' }}
                </div>
                <div class="text-gray-600">Clients</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-orange-600 mb-2 hover:scale-110 transition-transform duration-200">
                    {{ isset($stats['completed_projects']) ? number_format($stats['completed_projects']) : '9,876' }}
                </div>
                <div class="text-gray-600">Completed Projects</div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Jobs Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Featured Jobs</h2>
            <p class="text-lg text-gray-600">Discover premium opportunities from top clients</p>
        </div>
        
        @if(isset($featuredJobs) && $featuredJobs->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredJobs as $job)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 p-6 border border-gray-200 hover:border-blue-300 transform hover:-translate-y-1">
                        <div class="flex items-center justify-between mb-4">
                            <span class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                ⭐ Featured
                            </span>
                            <span class="text-sm text-gray-500">{{ $job->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <h3 class="text-xl font-semibold text-gray-900 mb-2 hover:text-blue-600 transition-colors">
                            {{ Str::limit($job->title, 50) }}
                        </h3>
                        <p class="text-gray-600 mb-4">{{ Str::limit($job->description, 100) }}</p>
                        
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-lg font-bold text-green-600">${{ number_format($job->budget) }}</span>
                            <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $job->job_type }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">{{ $job->location }}</span>
                            <a href="{{ route('jobs.show', $job->id, false) ?? '#' }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 px-4 rounded transition duration-200 transform hover:scale-105">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-500 text-lg">No featured jobs available at the moment.</div>
                <p class="text-gray-400 mt-2">Check back soon for exciting opportunities!</p>
            </div>
        @endif
    </div>
</section>

<!-- Recent Jobs Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Recent Jobs</h2>
            <p class="text-lg text-gray-600">Latest opportunities posted by our clients</p>
        </div>
        
        @if(isset($recentJobs) && $recentJobs->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($recentJobs as $job)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-6 border border-gray-200 hover:border-blue-300 transform hover:-translate-y-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 hover:text-blue-600 transition-colors">
                            {{ Str::limit($job->title, 40) }}
                        </h3>
                        <p class="text-gray-600 mb-3 text-sm">{{ Str::limit($job->description, 80) }}</p>
                        
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-lg font-bold text-green-600">${{ number_format($job->budget) }}</span>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $job->job_type }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">{{ Str::limit($job->location, 20) }}</span>
                            <a href="{{ route('jobs.show', $job->id, false) ?? '#' }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-2 px-3 rounded transition duration-200 transform hover:scale-105">
                                View
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-500 text-lg">No recent jobs available at the moment.</div>
                <p class="text-gray-400 mt-2">Be the first to post a job and find talented freelancers!</p>
            </div>
        @endif
        
        <div class="text-center mt-12">
            <a href="@auth
                @if(auth()->user()->hasRole('admin'))
                    {{ route('admin.jobs.index', [], false) ?? '#' }}
                @elseif(auth()->user()->hasRole('client'))
                    {{ route('client.jobs.index', [], false) ?? '#' }}
                @elseif(auth()->user()->hasRole('freelancer'))
                    {{ route('freelancer.jobs.browse', [], false) ?? '#' }}
                @else
                    {{ route('home', [], false) ?? '#' }}
                @endif
            @else
                {{ route('login', [], false) ?? '#' }}
            @endauth" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-200 transform hover:scale-105 shadow-lg">
                View All Jobs
            </a>
        </div>
    </div>
</section>

<!-- Top Categories Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Top Categories</h2>
            <p class="text-lg text-gray-600">Browse jobs by popular categories</p>
        </div>
        
        @if(isset($topCategories) && $topCategories->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($topCategories as $category)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-6 border border-gray-200 hover:border-blue-300 text-center transform hover:-translate-y-1">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 hover:scale-110 transition-transform duration-200">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2 hover:text-blue-600 transition-colors">
                            {{ $category->category }}
                        </h3>
                        <p class="text-gray-600 mb-4">{{ $category->job_count ?? '0' }} jobs available</p>
                        <a href="{{ route('search', [], false) ?? '#' }}?category={{ $category->id }}" class="text-blue-600 hover:text-blue-700 font-semibold inline-flex items-center">
                            Browse Jobs
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-500 text-lg">No categories available at the moment.</div>
                <p class="text-gray-400 mt-2">Categories will appear here as jobs are posted.</p>
            </div>
        @endif
    </div>
</section>

<!-- How It Works Section -->
<section id="how-it-works" class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
            <p class="text-lg text-gray-600">Get started in just a few simple steps</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-white text-2xl font-bold">1</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Create Your Profile</h3>
                <p class="text-gray-600">Sign up and create a compelling profile that showcases your skills and experience.</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-white text-2xl font-bold">2</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Find & Apply</h3>
                <p class="text-gray-600">Browse through thousands of jobs and submit proposals to projects that match your skills.</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-white text-2xl font-bold">3</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Get Hired & Work</h3>
                <p class="text-gray-600">Get hired by clients, complete projects, and build your reputation on our platform.</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">What Our Users Say</h2>
            <p class="text-lg text-gray-600">Success stories from our amazing community</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-4">
                        <span class="text-white font-semibold">JS</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">John Smith</h4>
                        <p class="text-sm text-gray-600">Web Developer</p>
                    </div>
                </div>
                <p class="text-gray-600 italic">"This platform has completely transformed my freelance career. I've found consistent, high-quality projects and amazing clients."</p>
                <div class="flex items-center mt-4">
                    @for($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-blue-600 rounded-full flex items-center justify-center mr-4">
                        <span class="text-white font-semibold">MJ</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Maria Johnson</h4>
                        <p class="text-sm text-gray-600">Business Owner</p>
                    </div>
                </div>
                <p class="text-gray-600 italic">"I've hired several freelancers through this platform and the quality of work has been exceptional. Highly recommended!"</p>
                <div class="flex items-center mt-4">
                    @for($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center mr-4">
                        <span class="text-white font-semibold">DW</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">David Wilson</h4>
                        <p class="text-sm text-gray-600">Graphic Designer</p>
                    </div>
                </div>
                <p class="text-gray-600 italic">"The user interface is intuitive and the payment system is secure. I feel confident working through this platform."</p>
                <div class="flex items-center mt-4">
                    @for($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-16 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to Get Started?</h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">Join thousands of freelancers and clients who are already making their dreams happen.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register', [], false) ?? '#' }}" class="bg-white text-blue-600 hover:bg-gray-100 font-semibold py-3 px-8 rounded-lg transition duration-200 transform hover:scale-105">
                Start as Freelancer
            </a>
            <a href="{{ route('register', [], false) ?? '#' }}" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-600 font-semibold py-3 px-8 rounded-lg transition duration-200 transform hover:scale-105">
                Hire Talent
            </a>
        </div>
    </div>
</section>
@endsection