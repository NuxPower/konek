@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('freelancer.applications.index') }}" class="hover:text-blue-600">My Applications</a>
                <span>→</span>
                <a href="{{ route('freelancer.applications.show', $application) }}" class="hover:text-blue-600">{{ Str::limit($application->job->title, 30) }}</a>
                <span>→</span>
                <span class="text-gray-900">Edit</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900">Edit Application</h1>
            <p class="text-gray-600 mt-2">You can only edit pending applications</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Edit Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-6">Update Your Application</h2>
                    
                    <form method="POST" action="{{ route('freelancer.applications.update', $application) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Cover Letter -->
                        <div class="mb-6">
                            <label for="cover_letter" class="block text-sm font-medium text-gray-700 mb-2">
                                Cover Letter <span class="text-red-500">*</span>
                            </label>
                            <textarea name="cover_letter" 
                                      id="cover_letter" 
                                      rows="8"
                                      placeholder="Tell the client why you're the perfect fit for this job..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('cover_letter') border-red-500 @enderror">{{ old('cover_letter', $application->cover_letter) }}</textarea>
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
                                <span class="absolute left-3 top-2 text-gray-500">$</span>
                                <input type="number" 
                                       name="proposed_rate" 
                                       id="proposed_rate" 
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00"
                                       value="{{ old('proposed_rate', $application->proposed_rate) }}"
                                       class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('proposed_rate') border-red-500 @enderror">
                            </div>
                            @error('proposed_rate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Client's budget: ${{ number_format($application->job->budget) }}</p>
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
                                   value="{{ old('estimated_duration', $application->estimated_duration) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('estimated_duration') border-red-500 @enderror">
                            @error('estimated_duration')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Existing Attachments -->
                        @if($application->attachments && count(json_decode($application->attachments, true)) > 0)
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Attachments</label>
                                <div class="space-y-2" id="existing-attachments">
                                    @foreach(json_decode($application->attachments, true) as $index => $attachment)
                                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg" id="attachment-{{ $index }}">
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
                                                <button type="button" 
                                                        onclick="removeExistingAttachment({{ $index }})" 
                                                        class="text-red-600 hover:text-red-800">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- New Attachments -->
                        <div class="mb-6">
                            <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">
                                Add New Attachments (Optional)
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
                                    <p class="text-gray-600">Click to upload new files or drag and drop</p>
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
                            <a href="{{ route('freelancer.applications.show', $application) }}" class="text-gray-600 hover:text-gray-800">
                                ← Cancel
                            </a>
                            <div class="space-x-3">
                                <button type="submit" 
                                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Update Application
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
                            <h4 class="font-medium text-gray-900">{{ $application->job->title }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($application->job->description, 100) }}</p>
                        </div>

                        <div class="border-t pt-4">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Budget:</span>
                                    <div class="font-medium">${{ number_format($application->job->budget) }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-500">Type:</span>
                                    <div class="font-medium">{{ ucfirst($application->job->type) }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-500">Experience:</span>
                                    <div class="font-medium">{{ ucfirst($application->job->experience_level) }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-500">Deadline:</span>
                                    <div class="font-medium">
                                        {{ $application->job->deadline ? $application->job->deadline->format('M d, Y') : 'Flexible' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <span class="text-gray-500 text-sm">Skills Required:</span>
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($application->job->skills as $skill)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                        {{ $skill->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <span class="text-gray-500 text-sm">Client:</span>
                            <div class="mt-1">
                                <div class="font-medium">{{ $application->job->client->company_name ?? 'Private Client' }}</div>
                                <div class="text-sm text-gray-500">Member since {{ $application->job->client->created_at->format('M Y') }}</div>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <span class="text-gray-500 text-sm">Application Status:</span>
                            <div class="mt-1">
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                    {{ ucfirst($application->status) }}
                                </span>
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

function removeExistingAttachment(index) {
    if (confirm('Are you sure you want to remove this attachment?')) {
        fetch(`{{ route('freelancer.applications.remove-attachment', $application) }}/${index}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`attachment-${index}`).remove();
                
                // Check if no more existing attachments
                const existingContainer = document.getElementById('existing-attachments');
                if (existingContainer && existingContainer.children.length === 0) {
                    existingContainer.parentElement.style.display = 'none';
                }
            } else {
                alert('Error removing attachment. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing attachment. Please try again.');
        });
    }
}
</script>
@endsection