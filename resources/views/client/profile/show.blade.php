@extends('layouts.client')

@section('title', 'Profile')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Profile Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">My Profile</h1>
                <a href="{{ route('client.profile.edit') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200">
                    Edit Profile
                </a>
            </div>

            <div class="flex items-start space-x-6">
                <!-- Avatar -->
                <div class="flex flex-col items-center">
                    @if($client->user->avatar)
                        <img src="{{ Storage::url($client->user->avatar) }}" alt="Avatar" class="w-24 h-24 rounded-full object-cover border-4 border-gray-200">
                    @else
                        <div class="w-24 h-24 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-2xl font-bold border-4 border-gray-200">
                            {{ strtoupper(substr($client->user->name, 0, 2)) }}
                        </div>
                    @endif
                    
                    <div class="mt-2 flex space-x-2">
                        <form action="{{ route('client.profile.upload-avatar') }}" method="POST" enctype="multipart/form-data" class="inline">
                            @csrf
                            <input type="file" name="avatar" accept="image/*" class="hidden" id="avatar-input" onchange="this.form.submit()">
                            <label for="avatar-input" class="cursor-pointer text-blue-600 hover:text-blue-800 text-sm">
                                Change
                            </label>
                        </form>
                        
                        @if($client->user->avatar)
                            <form action="{{ route('client.profile.delete-avatar') }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Are you sure you want to delete your avatar?')">
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <h2 class="text-xl font-semibold text-gray-800">{{ $client->user->name }}</h2>
                        @if($client->is_verified)
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Verified
                            </span>
                        @else
                            <form action="{{ route('client.profile.request-verification') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full hover:bg-yellow-200 transition duration-200">
                                    Request Verification
                                </button>
                            </form>
                        @endif
                    </div>
                    
                    <p class="text-gray-600 mb-2">{{ $client->user->email }}</p>
                    
                    @if($client->user->phone)
                        <p class="text-gray-600 mb-2">{{ $client->user->phone }}</p>
                    @endif
                    
                    @if($client->location)
                        <p class="text-gray-600 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $client->location }}
                        </p>
                    @endif
                    
                    <p class="text-gray-500 text-sm">Member since {{ $stats['member_since'] }}</p>
                </div>

                <!-- Quick Stats -->
                <div class="text-right">
                    @if($stats['rating'])
                        <div class="flex items-center justify-end mb-2">
                            <div class="flex text-yellow-400 mr-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $stats['rating'])
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-gray-600">{{ number_format($stats['rating'], 1) }}</span>
                        </div>
                    @endif
                    
                    @if($stats['total_spent'])
                        <p class="text-gray-600 text-sm">Total Spent: ${{ number_format($stats['total_spent'], 2) }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-md">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Jobs Posted</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['jobs_posted'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-md">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Active Jobs</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_jobs'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-md">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Completed</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['completed_jobs'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-md">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Total Spent</p>
                        <p class="text-2xl font-semibold text-gray-900">${{ number_format($stats['total_spent'], 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Information -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Company Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">{{ $client->company_name }}</h4>
                    <p class="text-gray-600 text-sm mb-4">{{ $client->company_description ?: 'No description provided.' }}</p>
                    
                    @if($client->company_website)
                        <p class="text-sm">
                            <span class="font-medium text-gray-700">Website:</span>
                            <a href="{{ $client->company_website }}" target="_blank" class="text-blue-600 hover:text-blue-800 ml-2">
                                {{ $client->company_website }}
                            </a>
                        </p>
                    @endif
                </div>
                
                <div>
                    <div class="space-y-2">
                        <p class="text-sm">
                            <span class="font-medium text-gray-700">Industry:</span>
                            <span class="ml-2 text-gray-600">{{ $client->industry }}</span>
                        </p>
                        
                        <p class="text-sm">
                            <span class="font-medium text-gray-700">Company Size:</span>
                            <span class="ml-2 text-gray-600">{{ ucfirst($client->company_size) }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bio Section -->
        @if($client->user->bio)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">About</h3>
                <p class="text-gray-600 leading-relaxed">{{ $client->user->bio }}</p>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('client.profile.edit') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                    <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-800">Edit Profile</p>
                        <p class="text-sm text-gray-600">Update your information</p>
                    </div>
                </a>
                
                <a href="{{ route('client.profile.settings') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                    <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-800">Settings</p>
                        <p class="text-sm text-gray-600">Manage preferences</p>
                    </div>
                </a>
                
                <a href="{{ route('client.jobs.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                    <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-800">Post New Job</p>
                        <p class="text-sm text-gray-600">Create a new job posting</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection