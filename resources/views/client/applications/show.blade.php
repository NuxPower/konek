@extends('layouts.app')

@section('title', 'Application Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('client.applications.index') }}" 
                   class="text-blue-600 hover:text-blue-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Applications
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Application Details</h1>
            </div>
            
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'accepted' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'shortlisted' => 'bg-blue-100 text-blue-800',
                    'completed' => 'bg-purple-100 text-purple-800',
                    'pending_completion' => 'bg-orange-100 text-orange-800',
                    'revision_requested' => 'bg-yellow-100 text-yellow-800'
                ];
            @endphp
            <div class="flex items-center space-x-3">
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$application->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                </span>
                
                {{-- Always show review button if there's completion data --}}
                @if($application->status === 'pending_completion' || 
                    $application->status === 'completed' || 
                    $application->status === 'revision_requested' ||
                    $application->completion_message ||
                    $application->completion_attachments)
                    <a href="{{ route('client.applications.review-completion', $application) }}" 
                       class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        @if($application->status === 'pending_completion')
                            Review Completion
                        @elseif($application->status === 'completed')
                            View Review
                        @else
                            Review Work
                        @endif
                    </a>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <!-- Completion Alert -->
    @if($application->status === 'pending_completion')
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-orange-800">Job Completion Submitted</h3>
                    <p class="text-orange-700">The freelancer has marked this job as completed. Please review their work and provide feedback.</p>
                    <div class="mt-3">
                        <a href="{{ route('client.applications.review-completion', $application) }}" 
                           class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 transition-colors">
                            Review Completion
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif($application->status === 'revision_requested')
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-yellow-800">Revision Requested</h3>
                    <p class="text-yellow-700">You have requested revisions for this job. The freelancer will resubmit their work.</p>
                    <div class="mt-3">
                        <a href="{{ route('client.applications.review-completion', $application) }}" 
                           class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 transition-colors">
                            View Revision Request
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif($application->status === 'completed' && $application->client_feedback)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-green-800">Job Completed Successfully</h3>
                    <p class="text-green-700">This job has been completed and approved. You gave a rating of {{ $application->client_rating }}/5 stars.</p>
                    <div class="mt-3">
                        <a href="{{ route('client.applications.review-completion', $application) }}" 
                           class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                            View Your Review
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Application Details -->
        <div class="lg:col-span-2">
            <!-- Job Information -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Job Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                        <p class="text-gray-900">{{ $application->job->title }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Budget</label>
                        <p class="text-gray-900">{{ $application->job->formatted_budget }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <p class="text-gray-900">{{ $application->job->category->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                        <p class="text-gray-900">{{ $application->job->deadline ? $application->job->deadline->format('M d, Y') : 'No deadline' }}</p>
                    </div>
                </div>
                @if($application->job->description)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Job Description</label>
                        <div class="text-gray-700 prose prose-sm max-w-none">
                            {!! nl2br(e($application->job->description)) !!}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Application Details -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Application Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Applied Date</label>
                        <p class="text-gray-900">{{ $application->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Proposed Rate</label>
                        <p class="text-gray-900">₱{{ number_format((float) ($application->proposed_rate ?? 0), 2) }}</p>
                    </div>
                    @if($application->reviewed_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reviewed Date</label>
                            <p class="text-gray-900">{{ $application->reviewed_at->format('M d, Y g:i A') }}</p>
                        </div>
                    @endif
                    @if($application->accepted_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Accepted Date</label>
                            <p class="text-gray-900">{{ $application->accepted_at->format('M d, Y g:i A') }}</p>
                        </div>
                    @endif
                </div>

                @if($application->cover_letter)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cover Letter</label>
                        <div class="bg-gray-50 rounded-lg p-4 border">
                            <div class="text-gray-700 prose prose-sm max-w-none">
                                {!! nl2br(e($application->cover_letter)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Completion Details -->
                @if($application->status === 'pending_completion' || $application->status === 'completed' || $application->status === 'revision_requested')
                    <div class="mb-6 pt-6 border-t">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Completion Details</h3>
                            <a href="{{ route('client.applications.review-completion', $application) }}" 
                               class="bg-orange-600 text-white px-3 py-1 rounded text-sm hover:bg-orange-700 transition-colors">
                                @if($application->status === 'pending_completion')
                                    Review Now
                                @else
                                    View Review
                                @endif
                            </a>
                        </div>
                        
                        @if($application->completed_at)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Completion Date</label>
                                <p class="text-gray-900">{{ $application->completed_at->format('M d, Y g:i A') }}</p>
                            </div>
                        @endif

                        @if($application->completion_message)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Completion Message</label>
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                    <div class="text-gray-700 prose prose-sm max-w-none">
                                        {!! nl2br(e($application->completion_message)) !!}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($application->completion_attachments)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Completion Attachments</label>
                                <div class="space-y-2">
                                    @foreach($application->completion_attachments as $index => $attachment)
                                        <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3 border">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="text-sm text-gray-700">{{ $attachment['original_name'] ?? 'Attachment ' . ($index + 1) }}</span>
                                                <span class="text-xs text-gray-500 ml-2">({{ round($attachment['size']/1024, 1) }} KB)</span>
                                            </div>
                                            <a href="{{ route('client.applications.download-completion-attachment', [$application, $index]) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Download
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($application->revision_notes)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Revision Notes</label>
                                <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                                    <div class="text-gray-700 prose prose-sm max-w-none">
                                        {!! nl2br(e($application->revision_notes)) !!}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($application->client_feedback)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Your Feedback</label>
                                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                    <div class="text-gray-700 prose prose-sm max-w-none">
                                        {!! nl2br(e($application->client_feedback)) !!}
                                    </div>
                                    @if($application->client_rating)
                                        <div class="mt-3 flex items-center">
                                            <span class="text-sm font-medium text-gray-700 mr-2">Rating:</span>
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $application->client_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                                <span class="ml-2 text-sm text-gray-600">({{ $application->client_rating }}/5)</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3 pt-4 border-t">
                    @if($application->status === 'pending')
                        <form action="{{ route('client.applications.accept', $application) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors flex items-center"
                                    onclick="return confirm('Are you sure you want to accept this application?')">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Accept Application
                            </button>
                        </form>
                        
                        <form action="{{ route('client.applications.shortlist', $application) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors flex items-center"
                                    onclick="return confirm('Are you sure you want to shortlist this application?')">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                </svg>
                                Shortlist
                            </button>
                        </form>
                        
                        <form action="{{ route('client.applications.reject', $application) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors flex items-center"
                                    onclick="return confirm('Are you sure you want to reject this application?')">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reject Application
                            </button>
                        </form>
                    @endif
                    
                    {{-- Enhanced Review Button --}}
                    @if($application->status === 'pending_completion')
                        <a href="{{ route('client.applications.review-completion', $application) }}" 
                           class="bg-orange-600 text-white px-6 py-2 rounded-md hover:bg-orange-700 transition-colors flex items-center font-semibold">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Review Completion
                        </a>
                    @elseif(($application->status === 'completed' || $application->status === 'revision_requested') && 
                            ($application->completion_message || $application->completion_attachments))
                        <a href="{{ route('client.applications.review-completion', $application) }}" 
                           class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            View Review Details
                        </a>
                    @endif
                    
                    @if($application->status === 'accepted')
                        <form action="{{ route('client.applications.complete', $application) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition-colors flex items-center"
                                    onclick="return confirm('Are you sure you want to mark this application as completed?')">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Mark as Completed
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar (keeping the existing sidebar content) -->
        <div class="lg:col-span-1">
            <!-- Freelancer Information -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Freelancer Information</h2>
                
                <div class="text-center mb-4">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-blue-600 font-bold text-xl">
                            {{ substr($application->freelancer->user->name ?? $application->freelancer->name ?? 'N/A', 0, 2) }}
                        </span>
                    </div>
                    <h3 class="font-semibold text-gray-900">{{ $application->freelancer->user->name ?? $application->freelancer->name ?? 'Name not available' }}</h3>
                    <p class="text-gray-600 text-sm">{{ $application->freelancer->user->email ?? $application->freelancer->email ?? 'Email not available' }}</p>
                </div>

                <div class="space-y-3">
                    @if(optional($application->freelancer)->phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <p class="text-gray-900 text-sm">{{ $application->freelancer->phone }}</p>
                        </div>
                    @endif
                    
                    @if(optional($application->freelancer)->bio)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                            <p class="text-gray-700 text-sm">{{ $application->freelancer->bio }}</p>
                        </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Member Since</label>
                        <p class="text-gray-900 text-sm">{{ $application->freelancer->created_at->format('M Y') }}</p>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t">
                   <a href="{{ route('profile.public.show', $application->freelancer) }}" 
                      class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                       View Full Profile
                   </a>
                </div>
            </div>

            <!-- Application Timeline -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Application Timeline</h2>
                
                <div class="relative">
                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                    
                    <div class="relative flex items-center mb-4">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center relative z-10">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Application Submitted</p>
                            <p class="text-xs text-gray-500">{{ $application->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                    
                    @if($application->reviewed_at)
                        <div class="relative flex items-center mb-4">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center relative z-10">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Application Reviewed</p>
                                <p class="text-xs text-gray-500">{{ $application->reviewed_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($application->accepted_at)
                        <div class="relative flex items-center mb-4">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center relative z-10">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Application Accepted</p>
                                <p class="text-xs text-gray-500">{{ $application->accepted_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($application->completed_at)
                        <div class="relative flex items-center mb-4">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center relative z-10">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Work Completed</p>
                                <p class="text-xs text-gray-500">{{ $application->completed_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($application->revision_requested_at)
                        <div class="relative flex items-center mb-4">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center relative z-10">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Revision Requested</p>
                                <p class="text-xs text-gray-500">{{ $application->revision_requested_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($application->approved_at)
                        <div class="relative flex items-center">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center relative z-10">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Job Approved</p>
                                <p class="text-xs text-gray-500">{{ $application->approved_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection