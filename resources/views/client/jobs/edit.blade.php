@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('client.jobs.show', $job) }}" class="text-blue-600 hover:text-blue-800 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Edit Job: {{ $job->title }}</h1>
        </div>

        <form method="POST" action="{{ route('client.jobs.update', $job) }}" class="bg-white rounded-lg shadow-md p-6">
            @csrf
            @method('PUT')
            
            <!-- Job Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Job Title *</label>
                <input type="text" name="title" id="title" value="{{ old('title', $job->title) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                       placeholder="Enter a clear, descriptive job title">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Job Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Job Description *</label>
                <textarea name="description" id="description" rows="8" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                          placeholder="Describe the job requirements, deliverables, and what you're looking for...">{{ old('description', $job->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Job Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Payment Type *</label>
                    <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror">
                        <option value="">Select payment type</option>
                        <option value="fixed" {{ old('type', $job->type) == 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                        <option value="hourly" {{ old('type', $job->type) == 'hourly' ? 'selected' : '' }}>Hourly Rate</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Experience Level -->
                <div>
                    <label for="experience_level" class="block text-sm font-medium text-gray-700 mb-2">Experience Level *</label>
                    <select name="experience_level" id="experience_level" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('experience_level') border-red-500 @enderror">
                        <option value="">Select experience level</option>
                        <option value="entry" {{ old('experience_level', $job->experience_level) == 'entry' ? 'selected' : '' }}>Entry Level</option>
                        <option value="intermediate" {{ old('experience_level', $job->experience_level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="expert" {{ old('experience_level', $job->experience_level) == 'expert' ? 'selected' : '' }}>Expert</option>
                    </select>
                    @error('experience_level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Budget Section -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Budget <span class="text-sm text-gray-500">(Optional)</span></label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="budget_min" class="block text-xs font-medium text-gray-600 mb-1">Minimum Budget</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="budget_max" id="budget_max" value="{{ old('budget_max', $job->budget_max) }}" 
                                   class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('budget_max') border-red-500 @enderror"
                                   placeholder="0.00" step="0.01" min="0">
                        </div>
                        @error('budget_max')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-500" id="budget-help">
                    Set your budget range to help freelancers understand your project scope.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Deadline -->
                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700 mb-2">Deadline *</label>
                    <input type="date" name="deadline" id="deadline" value="{{ old('deadline', $job->deadline ? $job->deadline->format('Y-m-d') : '') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('deadline') border-red-500 @enderror"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    @error('deadline')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duration -->
                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">Project Duration *</label>
                    <input type="text" name="duration" id="duration" value="{{ old('duration', $job->duration) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('duration') border-red-500 @enderror"
                           placeholder="e.g., 2 weeks, 1 month, 3 months">
                    @error('duration')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Skills -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Required Skills <span class="text-sm text-gray-500">(Optional)</span></label>
                <input type="text" name="skills" id="skills" value="{{ old('skills', $job->skills->pluck('name')->implode(', ')) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('skills') border-red-500 @enderror"
                       placeholder="e.g., PHP, Laravel, JavaScript, React (comma-separated)">
                @error('skills')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Enter skills separated by commas. This helps freelancers find your job.</p>
            </div>

            <!-- Current Status Display -->
            <div class="mb-6 p-4 bg-gray-50 rounded-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Current Status</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($job->status === 'open') bg-green-100 text-green-800
                            @elseif($job->status === 'closed') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($job->status) }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-500">
                        <p>{{ $job->applications->count() }} applications received</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between items-center pt-6 border-t">
                <a href="{{ route('client.jobs.show', $job) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-md transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition duration-200">
                    <i class="fas fa-save mr-2"></i>Update Job
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-resize textarea
document.getElementById('description').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});

// Set initial height for description textarea
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('description');
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight) + 'px';
});

// Update budget help text based on payment type
document.getElementById('type').addEventListener('change', function() {
    const budgetHelp = document.getElementById('budget-help');
    if (this.value === 'hourly') {
        budgetHelp.textContent = 'Set your hourly rate range to help freelancers understand your budget.';
    } else if (this.value === 'fixed') {
        budgetHelp.textContent = 'Set your project budget range to help freelancers understand the scope.';
    } else {
        budgetHelp.textContent = 'Set your budget range to help freelancers understand your project scope.';
    }
});

// Budget validation
document.getElementById('budget_min').addEventListener('input', function() {
    const budgetMax = document.getElementById('budget_max');
    if (this.value && budgetMax.value && parseFloat(this.value) > parseFloat(budgetMax.value)) {
        budgetMax.setCustomValidity('Maximum budget must be greater than or equal to minimum budget');
    } else {
        budgetMax.setCustomValidity('');
    }
});

document.getElementById('budget_max').addEventListener('input', function() {
    const budgetMin = document.getElementById('budget_min');
    if (this.value && budgetMin.value && parseFloat(this.value) < parseFloat(budgetMin.value)) {
        this.setCustomValidity('Maximum budget must be greater than or equal to minimum budget');
    } else {
        this.setCustomValidity('');
    }
});
</script>
@endsectioncenter pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="budget_min" id="budget_min" value="{{ old('budget_min', $job->budget_min) }}" 
                                   class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('budget_min') border-red-500 @enderror"
                                   placeholder="0.00" step="0.01" min="0">
                        </div>
                        @error('budget_min')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="budget_max" class="block text-xs font-medium text-gray-600 mb-1">Maximum Budget</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-