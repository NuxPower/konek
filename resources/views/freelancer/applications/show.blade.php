@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('freelancer.applications.index') }}" class="hover:text-blue-600">My Applications</a>
                <span>→</span>
                <span class="text-gray-900">Application Details</span>
            </nav>
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $application->job->title }}</h1>
                    <div class="flex items-center space-x-4">
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $application->status === 'accepted' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $application->status === 'withdrawn' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $application->status === 'reviewing' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $application->status === 'shortlisted' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $application->status === 'pending_completion' ? 'bg-orange-100 text-orange-800' : '' }}
                            {{ $application->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : '' }}">
                            {{ $application->formatted_status }}
                        </span>
                        <span class="text-gray-500 text-sm">Applied {{ $application->time_ago }}</span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    @if($application->isPending())
                        <a href="{{ route('freelancer.applications.edit', $application) }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Edit Application
                        </a>
                        <form method="POST" action="{{ route('freelancer.applications.destroy', $application) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Are you sure you want to withdraw this application?')"
                                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                                Withdraw
                            </button>
                        </form>
                    @elseif($application->status === 'accepted')
                        <a href="{{ route('freelancer.applications.complete', $application) }}" 
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                            <i class="fas fa-check-circle mr-1"></i>
                            Mark as Completed
                        </a>
                    @endif
                    <a href="{{ route('freelancer.jobs.show', $application->job) }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                        View Job
                    </a>
                </div>
            </div>
        </div>

        <!-- Completion Status Alert -->
        @if($application->status === 'pending_completion')
            <div class="mb-6 bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-clock text-orange-600 mr-2"></i>
                    <div>
                        <h3 class="font-semibold text-orange-800">Completion Submitted</h3>
                        <p class="text-orange-700 text-sm">Your completion notification has been sent to the client. They will review your work and confirm completion.</p>
                    </div>
                </div>
            </div>
        @elseif($application->status === 'completed')
            <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-emerald-600 mr-2"></i>
                    <div>
                        <h3 class="font-semibold text-emerald-800">Job Completed</h3>
                        <p class="text-emerald-700 text-sm">This job has been successfully completed and approved by the client.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Application Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Cover Letter -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Cover Letter</h2>
                    <div class="prose max-w-none">
                        {!! nl2br(e($application->cover_letter)) !!}
                    </div>
                </div>

                <!-- Proposal Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Proposal Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-sm text-gray-500 mb-1">Proposed Rate</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $application->formatted_proposed_rate }}</div>
                        </div>
                        @if($application->estimated_hours)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-sm text-gray-500 mb-1">Estimated Duration</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $application->formatted_estimated_hours }}</div>
                        </div>
                        @endif
                        @if($application->total_project_cost)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-sm text-gray-500 mb-1">Total Project Cost</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $application->formatted_total_project_cost }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Completion Details -->
                @if($application->completion_message || $application->completion_attachments)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Completion Details</h2>
                    
                    @if($application->completion_message)
                        <div class="mb-4">
                            <h3 class="font-medium text-gray-900 mb-2">Completion Message</h3>
                            <div class="prose max-w-none">
                                {!! nl2br(e($application->completion_message)) !!}
                            </div>
                        </div>
                    @endif

                    @if($application->completion_attachments && count($application->completion_attachments) > 0)
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Completion Attachments</h3>
                            <div class="space-y-2">
                                @foreach($application->completion_attachments as $index => $attachment)
                                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-file text-gray-400 mr-3"></i>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $attachment['original_name'] }}</div>
                                                <div class="text-sm text-gray-500">{{ number_format($attachment['size'] / 1024, 1) }} KB</div>
                                            </div>
                                        </div>
                                        <a href="{{ route('freelancer.applications.download-completion-attachment', [$application, $index]) }}" 
                                           class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                @endif

                <!-- Portfolio Links -->
                @if($application->portfolio_links && count($application->portfolio_links) > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Portfolio Links</h2>
                    <div class="space-y-2">
                        @foreach($application->portfolio_links as $link)
                            @if(filter_var($link, FILTER_VALIDATE_URL))
                                <a href="{{ $link }}" target="_blank" class="block text-blue-600 hover:text-blue-800 break-all">
                                    {{ $link }}
                                </a>
                            @else
                                <div class="text-gray-700">{{ $link }}</div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Attachments -->
                @if($application->attachments && count($application->attachments) > 0)
                    <div class="bg-white rounded-lg shadow p-6 attachments-section">
                        <h2 class="text-xl font-semibold mb-4">Attachments</h2>
                        <div class="space-y-3">
                            @foreach($application->attachments as $index => $attachment)
                                <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg attachment-item" data-index="{{ $index }}">
                                    <div class="flex items-center">
                                        <i class="fas fa-file text-gray-400 mr-3"></i>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $attachment['original_name'] }}</div>
                                            <div class="text-sm text-gray-500">{{ number_format($attachment['size'] / 1024, 1) }} KB</div>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('freelancer.applications.download-attachment', [$application, $index]) }}" 
                                           class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @if($application->isPending())
                                            <button onclick="removeAttachment({{ $index }})" 
                                                    class="text-red-600 hover:text-red-800"
                                                    data-remove-url="{{ route('freelancer.applications.remove-attachment', [$application, $index]) }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Client Notes -->
                @if($application->client_notes)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Client Notes</h2>
                    <div class="prose max-w-none">
                        {!! nl2br(e($application->client_notes)) !!}
                    </div>
                </div>
                @endif

                <!-- Application Timeline -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Application Timeline</h2>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-3 h-3 bg-blue-600 rounded-full mt-1"></div>
                            <div class="ml-4">
                                <div class="font-medium text-gray-900">Application Submitted</div>
                                <div class="text-sm text-gray-500">{{ $application->created_at->format('M d, Y \a\t g:i A') }}</div>
                            </div>
                        </div>
                        
                        @if($application->updated_at != $application->created_at && $application->status === 'pending')
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-3 h-3 bg-gray-400 rounded-full mt-1"></div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">Application Updated</div>
                                    <div class="text-sm text-gray-500">{{ $application->updated_at->format('M d, Y \a\t g:i A') }}</div>
                                </div>
                            </div>
                        @endif

                        @if($application->reviewed_at)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-3 h-3 bg-yellow-600 rounded-full mt-1"></div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">Application Reviewed</div>
                                    <div class="text-sm text-gray-500">{{ $application->reviewed_at->format('M d, Y \a\t g:i A') }}</div>
                                </div>
                            </div>
                        @endif

                        @if($application->status === 'accepted')
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-3 h-3 bg-green-600 rounded-full mt-1"></div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">Application Accepted</div>
                                    <div class="text-sm text-gray-500">{{ $application->accepted_at ? $application->accepted_at->format('M d, Y \a\t g:i A') : 'Date not available' }}</div>
                                </div>
                            </div>
                        @endif

                        @if($application->status === 'rejected')
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-3 h-3 bg-red-600 rounded-full mt-1"></div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">Application Rejected</div>
                                    <div class="text-sm text-gray-500">{{ $application->rejected_at ? $application->rejected_at->format('M d, Y \a\t g:i A') : 'Date not available' }}</div>
                                </div>
                            </div>
                        @endif

                        @if($application->status === 'shortlisted')
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-3 h-3 bg-purple-600 rounded-full mt-1"></div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">Application Shortlisted</div>
                                    <div class="text-sm text-gray-500">{{ $application->shortlisted_at ? $application->shortlisted_at->format('M d, Y \a\t g:i A') : 'Date not available' }}</div>
                                </div>
                            </div>
                        @endif

                        @if($application->completed_at)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-3 h-3 bg-orange-600 rounded-full mt-1"></div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">Work Completed</div>
                                    <div class="text-sm text-gray-500">{{ $application->completed_at->format('M d, Y \a\t g:i A') }}</div>
                                </div>
                            </div>
                        @endif

                        @if($application->status === 'completed')
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-3 h-3 bg-emerald-600 rounded-full mt-1"></div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">Job Completed & Approved</div>
                                    <div class="text-sm text-gray-500">{{ $application->approved_at ? $application->approved_at->format('M d, Y \a\t g:i A') : 'Date not available' }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Job Summary -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Job Summary</h3>
                    <div class="space-y-3">
                        <div>
                            <div class="text-sm text-gray-500">Client</div>
                            <div class="font-medium">{{ $application->job->client->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Category</div>
                            <div class="font-medium">{{ $application->job->category->name ?? 'Not specified' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Budget</div>
                            <div class="font-medium">{{ $application->job->formatted_budget }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Job Type</div>
                            <div class="font-medium">{{ ucfirst($application->job->job_type) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Posted</div>
                            <div class="font-medium">{{ $application->job->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Required Skills -->
                @if($application->job->skills && count($application->job->skills) > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Required Skills</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($application->job->skills as $skill)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                {{ $skill->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Application Stats -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Application Stats</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Applications</span>
                            <span class="font-medium">{{ $application->job->applications_count ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Average Bid</span>
                            <span class="font-medium">{{ $application->job->average_bid ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Your Position</span>
                            <span class="font-medium">{{ $application->position ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('freelancer.jobs.show', $application->job) }}" 
                           class="block w-full bg-blue-600 text-white text-center py-2 rounded-lg hover:bg-blue-700">
                            View Job Details
                        </a>
                        @if($application->isPending())
                            <a href="{{ route('freelancer.applications.edit', $application) }}" 
                               class="block w-full bg-gray-600 text-white text-center py-2 rounded-lg hover:bg-gray-700">
                                Edit Application
                            </a>
                        @endif
                        <a href="{{ route('freelancer.applications.index') }}" 
                           class="block w-full bg-gray-300 text-gray-700 text-center py-2 rounded-lg hover:bg-gray-400">
                            Back to Applications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function removeAttachment(index) {
    if (!confirm('Are you sure you want to remove this attachment?')) {
        return;
    }
    
    // Get the URL from the button's data attribute
    const button = document.querySelector(`[data-index="${index}"] button[data-remove-url]`);
    if (!button) {
        alert('Unable to find remove URL');
        return;
    }
    
    const url = button.getAttribute('data-remove-url');
    
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`[data-index="${index}"]`).remove();
            
            // Check if there are no more attachments
            const attachmentsSection = document.querySelector('.attachments-section');
            if (attachmentsSection && attachmentsSection.querySelectorAll('.attachment-item').length === 0) {
                attachmentsSection.remove();
            }
            
            // Show success message (optional)
            if (data.message) {
                // You can show a toast notification here if you have one
                console.log(data.message);
            }
        } else {
            alert(data.message || 'Error removing attachment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while removing the attachment');
    });
}
</script>
@endpush
@endsection