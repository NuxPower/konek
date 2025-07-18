@extends('layouts.app')

@section('title', 'Review Job Completion')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center space-x-4 mb-4">
            <a href="{{ route('client.applications.show', $application) }}" 
               class="text-blue-600 hover:text-blue-800 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Application
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Review Job Completion</h1>
        </div>
        
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-orange-800">Job Completion Submitted</h3>
                    <p class="text-orange-700">{{ optional($application->freelancer)->user->name ?? optional($application->freelancer)->name ?? 'Freelancer' }} has marked the job "{{ $application->job->title }}" as completed. Please review their work and provide feedback.</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Agreed Rate</label>
                        <p class="text-gray-900">₱{{ number_format((float) ($application->proposed_rate ?? 0), 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Completion Date</label>
                        <p class="text-gray-900">{{ $application->completed_at->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Completion Details -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Completion Details</h2>
                
                @if($application->completion_message)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Freelancer's Completion Message</label>
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <div class="text-gray-700 prose prose-sm max-w-none">
                                {!! nl2br(e($application->completion_message)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                @if($application->completion_attachments)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Completion Attachments</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($application->completion_attachments as $index => $attachment)
                                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4 border hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center flex-1 min-w-0">
                                        @php
                                            $extension = pathinfo($attachment['original_name'] ?? '', PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        @endphp
                                        
                                        @if($isImage)
                                            <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        @endif
                                        
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-700 truncate">{{ $attachment['original_name'] ?? 'Attachment ' . ($index + 1) }}</p>
                                            <p class="text-xs text-gray-500">{{ round(($attachment['size'] ?? 0)/1024, 1) }} KB</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('client.applications.download-completion-attachment', [$application, $index]) }}" 
                                       class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors ml-3">
                                        Download
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action Forms -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Review Actions</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Approve Completion -->
                    <div class="border border-green-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-green-800 mb-3">Approve Completion</h3>
                        <p class="text-gray-600 text-sm mb-4">Mark this job as successfully completed and provide feedback to the freelancer.</p>
                        
                        <form action="{{ route('client.applications.approve-completion', $application) }}" method="POST" id="approveForm">
                            @csrf
                            @method('PATCH')
                            
                            <div class="mb-4">
                                <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">Rating (Required)</label>
                                <div class="flex items-center space-x-1" id="starRating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="rating" value="{{ $i }}" class="sr-only" required>
                                            <svg class="w-6 h-6 text-gray-300 hover:text-yellow-300 transition-colors star-icon" fill="currentColor" viewBox="0 0 20 20" data-rating="{{ $i }}">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="feedback" class="block text-sm font-medium text-gray-700 mb-2">Feedback (Optional)</label>
                                <textarea name="feedback" id="feedback" rows="4" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                          placeholder="Provide feedback about the freelancer's work..."></textarea>
                            </div>
                            
                            <button type="submit" 
                                    class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors"
                                    onclick="return confirm('Are you sure you want to approve this job completion?')">
                                Approve & Complete Job
                            </button>
                        </form>
                    </div>

                    <!-- Request Revisions -->
                    <div class="border border-yellow-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-yellow-800 mb-3">Request Revisions</h3>
                        <p class="text-gray-600 text-sm mb-4">Ask the freelancer to make changes or improvements to their work.</p>
                        
                        <form action="{{ route('client.applications.request-revisions', $application) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="mb-4">
                                <label for="revision_notes" class="block text-sm font-medium text-gray-700 mb-2">Revision Notes (Required)</label>
                                <textarea name="revision_notes" id="revision_notes" rows="6" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                          placeholder="Describe what changes or improvements you need..."
                                          required></textarea>
                            </div>
                            
                            <button type="submit" 
                                    class="w-full bg-yellow-600 text-white py-2 px-4 rounded-md hover:bg-yellow-700 transition-colors"
                                    onclick="return confirm('Are you sure you want to request revisions?')">
                                Request Revisions
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Freelancer Information -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Freelancer Information</h2>
                
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-blue-600 font-bold text-lg">
                            {{ substr(optional($application->freelancer)->user->name ?? optional($application->freelancer)->name ?? 'N/A', 0, 2) }}
                        </span>
                    </div>
                    <h3 class="font-semibold text-gray-900">{{ optional($application->freelancer)->user->name ?? optional($application->freelancer)->name ?? 'Name not available' }}</h3>
                    <p class="text-gray-600 text-sm">{{ optional($application->freelancer)->user->email ?? optional($application->freelancer)->email ?? 'Email not available' }}</p>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Application Date:</span>
                        <span class="text-gray-900">{{ $application->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Accepted Date:</span>
                        <span class="text-gray-900">{{ $application->accepted_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Completion Date:</span>
                        <span class="text-gray-900">{{ $application->completed_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
                
                <div class="space-y-3">
                    <a href="{{ route('client.applications.show', $application) }}" 
                       class="block w-full text-center bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                        View Full Application
                    </a>
                    
                    <a href="{{ route('profile.public.show', $application->freelancer) }}" 
                       class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        View Freelancer Profile
                    </a>
                    
                    <a href="{{ route('client.jobs.show', $application->job) }}" 
                       class="block w-full text-center bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                        View Job Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Interactive star rating
document.addEventListener('DOMContentLoaded', function() {
    const starContainer = document.getElementById('starRating');
    const starIcons = starContainer.querySelectorAll('.star-icon');
    const radioInputs = starContainer.querySelectorAll('input[type="radio"]');
    
    // Handle star hover
    starIcons.forEach((star, index) => {
        star.addEventListener('mouseenter', function() {
            highlightStars(index + 1);
        });
        
        star.addEventListener('click', function() {
            const rating = index + 1;
            radioInputs[index].checked = true;
            highlightStars(rating);
        });
    });
    
    // Reset on mouse leave
    starContainer.addEventListener('mouseleave', function() {
        const checkedInput = starContainer.querySelector('input[type="radio"]:checked');
        if (checkedInput) {
            highlightStars(parseInt(checkedInput.value));
        } else {
            highlightStars(0);
        }
    });
    
    function highlightStars(rating) {
        starIcons.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }
});
</script>
@endsection