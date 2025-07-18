@extends('layouts.app')

@section('title', 'Complete Job - ' . $application->job->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Complete Job</h1>
                    <p class="text-gray-600 mt-2">Submit your completed work to the client</p>
                </div>
                <a href="{{ route('freelancer.applications.show', $application) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Application
                </a>
            </div>
        </div>

        <!-- Job Summary Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Job Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Job Title</h3>
                    <p class="text-gray-900">{{ $application->job->title }}</p>
                </div>
                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Client</h3>
                    <p class="text-gray-900">{{ $application->job->client->name }}</p>
                </div>
                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Your Proposed Rate</h3>
                    <p class="text-gray-900">
                        ${{ number_format($application->proposed_rate, 2) }}
                        @if($application->rate_type === 'hourly')
                            /hour
                        @endif
                    </p>
                </div>
                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Estimated Hours</h3>
                    <p class="text-gray-900">{{ $application->estimated_hours ?: 'Not specified' }}</p>
                </div>
            </div>
            
            @if($application->job->description)
                <div class="mt-6">
                    <h3 class="font-medium text-gray-700 mb-2">Job Description</h3>
                    <div class="text-gray-900 bg-gray-50 p-4 rounded-lg">
                        {!! nl2br(e($application->job->description)) !!}
                    </div>
                </div>
            @endif
        </div>

        <!-- Completion Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Submit Completed Work</h2>
            
            <form action="{{ route('freelancer.applications.submit-completion', $application) }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf
                
                <!-- Completion Message -->
                <div>
                    <label for="completion_message" class="block text-sm font-medium text-gray-700 mb-2">
                        Completion Message <span class="text-red-500">*</span>
                    </label>
                    <textarea name="completion_message" 
                              id="completion_message" 
                              rows="6" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('completion_message') border-red-500 @enderror"
                              placeholder="Describe what you've completed, how it meets the requirements, and any important notes for the client..."
                              required>{{ old('completion_message') }}</textarea>
                    @error('completion_message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        Maximum 2000 characters. Be detailed about what you've delivered and how it meets the job requirements.
                    </p>
                </div>

                <!-- Completion Attachments -->
                <div>
                    <label for="completion_attachments" class="block text-sm font-medium text-gray-700 mb-2">
                        Deliverables & Files
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition duration-200">
                        <div class="space-y-2">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
                            <div>
                                <label for="completion_attachments" class="cursor-pointer">
                                    <span class="text-blue-600 hover:text-blue-700 font-medium">Upload your deliverables</span>
                                    <input type="file" 
                                           name="completion_attachments[]" 
                                           id="completion_attachments" 
                                           class="hidden" 
                                           multiple
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip">
                                </label>
                            </div>
                            <p class="text-sm text-gray-500">
                                Upload completed work, source files, documentation, etc.
                            </p>
                            <p class="text-xs text-gray-400">
                                Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG, ZIP (Max 10MB per file)
                            </p>
                        </div>
                    </div>
                    @error('completion_attachments.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    <!-- File Preview Area -->
                    <div id="file-preview" class="mt-4 space-y-2 hidden">
                        <h4 class="text-sm font-medium text-gray-700">Selected Files:</h4>
                        <div id="file-list" class="space-y-2"></div>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-blue-800">Important Notes</h3>
                            <ul class="mt-2 text-sm text-blue-700 space-y-1">
                                <li>• The client will be notified via email about your completion</li>
                                <li>• Include all deliverables and source files as attachments</li>
                                <li>• Be thorough in your completion message</li>
                                <li>• The client can approve or request revisions</li>
                                <li>• Payment will be processed after client approval</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('freelancer.applications.show', $application) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition duration-200 flex items-center">
                        <i class="fas fa-check mr-2"></i>
                        Submit Completion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('completion_attachments');
    const filePreview = document.getElementById('file-preview');
    const fileList = document.getElementById('file-list');
    
    fileInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        
        if (files.length > 0) {
            filePreview.classList.remove('hidden');
            fileList.innerHTML = '';
            
            files.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg';
                
                const fileInfo = document.createElement('div');
                fileInfo.className = 'flex items-center space-x-3';
                
                const fileIcon = document.createElement('i');
                fileIcon.className = getFileIcon(file.type);
                
                const fileDetails = document.createElement('div');
                fileDetails.innerHTML = `
                    <p class="text-sm font-medium text-gray-900">${file.name}</p>
                    <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                `;
                
                fileInfo.appendChild(fileIcon);
                fileInfo.appendChild(fileDetails);
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'text-red-500 hover:text-red-700 text-sm';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.onclick = function() {
                    // Remove file from input
                    const dt = new DataTransfer();
                    const files = Array.from(fileInput.files);
                    files.splice(index, 1);
                    files.forEach(f => dt.items.add(f));
                    fileInput.files = dt.files;
                    
                    // Trigger change event to update preview
                    fileInput.dispatchEvent(new Event('change'));
                };
                
                fileItem.appendChild(fileInfo);
                fileItem.appendChild(removeBtn);
                fileList.appendChild(fileItem);
            });
        } else {
            filePreview.classList.add('hidden');
        }
    });
    
    function getFileIcon(mimeType) {
        if (mimeType.startsWith('image/')) {
            return 'fas fa-image text-green-500';
        } else if (mimeType.includes('pdf')) {
            return 'fas fa-file-pdf text-red-500';
        } else if (mimeType.includes('word') || mimeType.includes('document')) {
            return 'fas fa-file-word text-blue-500';
        } else if (mimeType.includes('zip')) {
            return 'fas fa-file-archive text-purple-500';
        } else {
            return 'fas fa-file text-gray-500';
        }
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Character counter for completion message
    const completionMessage = document.getElementById('completion_message');
    const charCounter = document.createElement('div');
    charCounter.className = 'text-sm text-gray-500 mt-1';
    charCounter.innerHTML = `<span id="char-count">0</span>/2000 characters`;
    completionMessage.parentNode.insertBefore(charCounter, completionMessage.nextSibling);
    
    completionMessage.addEventListener('input', function() {
        document.getElementById('char-count').textContent = this.value.length;
        if (this.value.length > 2000) {
            charCounter.classList.add('text-red-500');
        } else {
            charCounter.classList.remove('text-red-500');
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.file-drop-zone {
    transition: all 0.3s ease;
}

.file-drop-zone:hover {
    background-color: #f9fafb;
}

.file-drop-zone.drag-over {
    background-color: #eff6ff;
    border-color: #3b82f6;
}
</style>
@endpush
@endsection