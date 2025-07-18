@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">My Applications</h1>
            <a href="{{ route('freelancer.jobs.browse') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Browse Jobs
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" action="{{ route('freelancer.applications.index') }}" class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-64">
                    <input type="text" 
                           name="search" 
                           placeholder="Search by job title..."
                           value="{{ request('search') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="withdrawn" {{ request('status') == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('freelancer.applications.index') }}" class="text-gray-600 hover:text-gray-800">
                        Clear Filters
                    </a>
                @endif
            </form>
        </div>

        @if($applications->count() > 0)
            <!-- Bulk Actions -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <form id="bulk-action-form" method="POST" action="{{ route('freelancer.applications.bulk-action') }}">
                    @csrf
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="checkbox" id="select-all" class="text-blue-600 rounded">
                                <span class="ml-2 text-sm text-gray-700">Select All</span>
                            </label>
                            <select name="action" id="bulk-action" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Bulk Actions</option>
                                <option value="withdraw">Withdraw Selected</option>
                                <option value="mark_read">Mark as Read</option>
                            </select>
                            <button type="submit" id="apply-bulk-action" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 disabled:opacity-50" disabled>
                                Apply
                            </button>
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $applications->total() }} application(s) found
                        </div>
                    </div>
                </form>
            </div>

            <!-- Applications List -->
            <div class="space-y-4">
                @foreach($applications as $application)
                    <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-start space-x-3">
                                <input type="checkbox" 
                                       name="applications[]" 
                                       value="{{ $application->id }}" 
                                       form="bulk-action-form"
                                       class="application-checkbox text-blue-600 rounded mt-1">
                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold mb-2">
                                        <a href="{{ route('freelancer.applications.show', $application) }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $application->job->title }}
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 mb-3">{{ Str::limit($application->cover_letter, 150) }}</p>
                                    
                                    <!-- Application Details -->
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-3">
                                        <span class="flex items-center">
                                            <i class="fas fa-money-bill-wave mr-1"></i>
                                            Proposed: ₱{{ number_format((float) $application->proposed_rate, 2) }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $application->estimated_duration }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-calendar mr-1"></i>
                                            Applied {{ $application->created_at->diffForHumans() }}
                                        </span>
                                        @if($application->attachments && is_array($application->attachments) && count($application->attachments) > 0)
                                            <span class="flex items-center">
                                                <i class="fas fa-paperclip mr-1"></i>
                                                {{ count($application->attachments) }} attachment(s)
                                            </span>
                                        @elseif($application->attachments && is_string($application->attachments))
                                            @php
                                                $attachmentsArray = json_decode($application->attachments, true);
                                            @endphp
                                            @if($attachmentsArray && count($attachmentsArray) > 0)
                                                <span class="flex items-center">
                                                    <i class="fas fa-paperclip mr-1"></i>
                                                    {{ count($attachmentsArray) }} attachment(s)
                                                </span>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- Client Info -->
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-building mr-1"></i>
                                        <span>{{ $application->job->client->company_name ?? 'Private Client' }}</span>
                                        <span class="mx-2">•</span>
                                        <span>Job Budget: {{ $application->job->formatted_budget }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col items-end space-y-2">
                                <!-- Status Badge -->
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $application->status === 'accepted' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $application->status === 'withdrawn' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($application->status) }}
                                </span>

                                <!-- Actions -->
                                <div class="flex space-x-2">
                                    <a href="{{ route('freelancer.applications.show', $application) }}" 
                                       class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                        View
                                    </a>
                                    @if($application->status === 'pending')
                                        <a href="{{ route('freelancer.applications.edit', $application) }}" 
                                           class="bg-gray-600 text-white px-3 py-1 rounded text-sm hover:bg-gray-700">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('freelancer.applications.destroy', $application) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Are you sure you want to withdraw this application?')"
                                                    class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
                                                Withdraw
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($applications->hasPages())
                <div class="mt-6">
                    {{ $applications->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <i class="fas fa-file-alt text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No applications found</h3>
                <p class="text-gray-600 mb-4">
                    @if(request()->hasAny(['search', 'status']))
                        Try adjusting your filters or search terms.
                    @else
                        You haven't applied to any jobs yet. Start browsing and apply to jobs that match your skills.
                    @endif
                </p>
                <a href="{{ route('freelancer.jobs.browse') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Browse Jobs
                </a>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.application-checkbox');
    const bulkAction = document.getElementById('bulk-action');
    const applyButton = document.getElementById('apply-bulk-action');
    const bulkForm = document.getElementById('bulk-action-form');

    // Select all functionality
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        toggleBulkActions();
    });

    // Individual checkbox change
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.application-checkbox:checked').length;
            selectAll.checked = checkedCount === checkboxes.length;
            selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
            toggleBulkActions();
        });
    });

    function toggleBulkActions() {
        const checkedCount = document.querySelectorAll('.application-checkbox:checked').length;
        applyButton.disabled = checkedCount === 0 || bulkAction.value === '';
    }

    bulkAction.addEventListener('change', toggleBulkActions);

    // Bulk action form submission
    bulkForm.addEventListener('submit', function(e) {
        const checkedCount = document.querySelectorAll('.application-checkbox:checked').length;
        const action = bulkAction.value;
        
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Please select at least one application.');
            return;
        }

        if (action === 'withdraw') {
            if (!confirm(`Are you sure you want to withdraw ${checkedCount} application(s)?`)) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endsection