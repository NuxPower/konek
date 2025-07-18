@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('freelancer.jobs.browse') }}" class="hover:text-blue-600">Browse Jobs</a>
                <span>→</span>
                <a href="{{ route('freelancer.jobs.show', $job) }}" class="hover:text-blue-600">{{ Str::limit($job->title, 30) }}</a>
                <span>→</span>
                <span class="text-gray-900">Apply</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900">Apply for Job</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Application Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-6">Submit Your Application</h2>
                    
                    <form method="POST" action="{{ route('freelancer.jobs.apply.store', $job) }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Cover Letter -->
                        <div class="mb-6">
                            <label for="cover_letter" class="block text-sm font-medium text-gray-700 mb-2">
                                Cover Letter <span class="text-red-500">*</span>
                            </label>
                            <textarea name="cover_letter" 
                                      id="cover_letter" 
                                      rows="8"
                                      placeholder="Tell the client why you're the perfect fit for this job..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('cover_letter') border-red-500 @enderror">{{ old('cover_letter') }}</textarea>
                            @error('cover_letter')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Maximum 2000 characters</p>
                        </div>

                        <!-- Proposed Rate -->
                        <div class="mb-6">
                            <label for="proposed_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                Proposed Rate <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">₱</span>
                                <input type="number" 
                                       name="proposed_rate" 
                                       id="proposed_rate" 
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00"
                                       value="{{ old('proposed_rate') }}"
                                       class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('proposed_rate') border-red-500 @enderror">
                            </div>
                            @error('proposed_rate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Client's budget: {{ $job->formatted_budget }}</p>
                        </div>

                        <!-- Estimated Duration -->
                        <div class="mb-6">
                            <label for="estimated_duration" class="block text-sm font-medium text-gray-700 mb-2">
                                Estimated Duration <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="estimated_duration" 
                                   id="estimated_duration" 
                                   placeholder="e.g., 2 weeks, 1 month, 3-5 days"
                                   value="{{ old('estimated_duration') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('estimated_duration') border-red-500 @enderror">
                            @error('estimated_duration')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Attachments -->
                        <div class="mb-6">
                            <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">
                                Attachments (Optional)
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-md p-6 text-center hover:border-gray-400 transition-colors">
                                <input type="file" 
                                       name="attachments[]" 
                                       id="attachments" 
                                       multiple
                                       accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png"
                                       class="hidden"
                                       onchange="handleFileSelect(this)">
                                <label for="attachments" class="cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                                    <p class="text-gray-600">Click to upload files or drag and drop</p>
                                    <p class="text-sm text-gray-500 mt-1">PDF, DOC, DOCX, TXT, JPG, JPEG, PNG (max 2MB each)</p>
                                </label>
                            </div>
                            <div id="file-list" class="mt-3 space-y-2"></div>
                            @error('attachments.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-between items-center">
                            <a href="{{ route('freelancer.jobs.show', $job) }}" class="text-gray-600 hover:text-gray-800">
                                ← Back to Job
                            </a>
                            <div class="space-x-3">
                                <button type="button" 
                                        onclick="saveDraft()"
                                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Save Draft
                                </button>
                                <button type="submit" 
                                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Submit Application
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Job Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                    <h3 class="text-lg font-semibold mb-4">Job Summary</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $job->title }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($job->description, 100) }}</p>
                        </div>

                        <div class="border-t pt-4">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Budget:</span>
                                    <div class="font-medium">{{ $job->formatted_budget }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-500">Type:</span>
                                    <div class="font-medium">{{ ucfirst($job->type) }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-500">Experience:</span>
                                    <div class="font-medium">{{ ucfirst($job->experience_level) }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-500">Deadline:</span>
                                    <div class="font-medium">
                                        {{ $job->deadline ? $job->deadline->format('M d, Y') : 'Flexible' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <span class="text-gray-500 text-sm">Skills Required:</span>
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($job->skills as $skill)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                        {{ $skill->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <span class="text-gray-500 text-sm">Client:</span>
                            <div class="mt-1">
                                <div class="font-medium">{{ $job->client->company_name ?? 'Private Client' }}</div>
                                <div class="text-sm text-gray-500">Member since {{ $job->client->created_at->format('M Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function handleFileSelect(input) {
    const fileList = document.getElementById('file-list');
    fileList.innerHTML = '';
    
    if (input.files.length > 0) {
        Array.from(input.files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between bg-gray-50 p-2 rounded';
            fileItem.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-file text-gray-400 mr-2"></i>
                    <span class="text-sm text-gray-700">${file.name}</span>
                    <span class="text-xs text-gray-500 ml-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                </div>
                <button type="button" onclick="removeFile(${index})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            `;
            fileList.appendChild(fileItem);
        });
    }
}

function removeFile(index) {
    const input = document.getElementById('attachments');
    const dt = new DataTransfer();
    
    Array.from(input.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    input.files = dt.files;
    handleFileSelect(input);
}

function saveDraft() {
    // Save form data to in-memory object (since localStorage isn't supported)
    const formData = {
        cover_letter: document.getElementById('cover_letter').value,
        proposed_rate: document.getElementById('proposed_rate').value,
        estimated_duration: document.getElementById('estimated_duration').value,
        timestamp: new Date().getTime()
    };
    
    // Store in a global variable for this session
    window.applicationDraft = formData;
    
    // Show success message
    const alertDiv = document.createElement('div');
    alertDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
    alertDiv.innerHTML = `
        <span class="block sm:inline">Draft saved successfully!</span>
        <button onclick="this.parentElement.remove()" class="float-right ml-2">×</button>
    `;
    document.body.appendChild(alertDiv);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.remove();
        }
    }, 3000);
}

// Load draft on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if there's a stored draft for this session
    if (window.applicationDraft) {
        const data = window.applicationDraft;
        if (data.cover_letter) document.getElementById('cover_letter').value = data.cover_letter;
        if (data.proposed_rate) document.getElementById('proposed_rate').value = data.proposed_rate;
        if (data.estimated_duration) document.getElementById('estimated_duration').value = data.estimated_duration;
    }

    // Clear draft after successful submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function() {
        window.applicationDraft = null;
    });
});
</script>
@endsection