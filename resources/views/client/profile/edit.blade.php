@extends('layouts.client')

@section('title', 'Edit Profile')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Edit Profile</h1>
                <a href="{{ route('client.profile.show') }}" class="text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            </div>

            <form action="{{ route('client.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Personal Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $client->user->name) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                   required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $client->user->email) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                                   required>
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $client->user->phone) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <input type="text" 
                                   id="location" 
                                   name="location" 
                                   value="{{ old('location', $client->location) }}"
                                   placeholder="City, Country"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror">
                            @error('location')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                        <textarea id="bio" 
                                  name="bio" 
                                  rows="4"
                                  placeholder="Tell us about yourself..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('bio') border-red-500 @enderror">{{ old('bio', $client->user->bio) }}</textarea>
                        @error('bio')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">Maximum 1000 characters</p>
                    </div>
                </div>

                <!-- Company Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Company Information</h3>
                    
                    <div class="mb-4">
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company Name *</label>
                        <input type="text" 
                               id="company_name" 
                               name="company_name" 
                               value="{{ old('company_name', $client->company_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('company_name') border-red-500 @enderror"
                               required>
                        @error('company_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="company_description" class="block text-sm font-medium text-gray-700 mb-1">Company Description</label>
                        <textarea id="company_description" 
                                  name="company_description" 
                                  rows="4"
                                  placeholder="Describe your company..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('company_description') border-red-500 @enderror">{{ old('company_description', $client->company_description) }}</textarea>
                        @error('company_description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">Maximum 2000 characters</p>
                    </div>

                    <div class="mb-4">
                        <label for="company_website" class="block text-sm font-medium text-gray-700 mb-1">Company Website</label>
                        <input type="url" 
                               id="company_website" 
                               name="company_website" 
                               value="{{ old('company_website', $client->company_website) }}"
                               placeholder="https://example.com"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('company_website') border-red-500 @enderror">
                        @error('company_website')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="company_size" class="block text-sm font-medium text-gray-700 mb-1">Company Size *</label>
                            <select id="company_size" 
                                    name="company_size" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('company_size') border-red-500 @enderror"
                                    required>
                                <option value="">Select company size</option>
                                <option value="startup" {{ old('company_size', $client->company_size) == 'startup' ? 'selected' : '' }}>Startup (1-10 employees)</option>
                                <option value="small" {{ old('company_size', $client->company_size) == 'small' ? 'selected' : '' }}>Small (11-50 employees)</option>
                                <option value="medium" {{ old('company_size', $client->company_size) == 'medium' ? 'selected' : '' }}>Medium (51-200 employees)</option>
                                <option value="large" {{ old('company_size', $client->company_size) == 'large' ? 'selected' : '' }}>Large (201-1000 employees)</option>
                                <option value="enterprise" {{ old('company_size', $client->company_size) == 'enterprise' ? 'selected' : '' }}>Enterprise (1000+ employees)</option>
                            </select>
                            @error('company_size')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="industry" class="block text-sm font-medium text-gray-700 mb-1">Industry *</label>
                            <input type="text" 
                                   id="industry" 
                                   name="industry" 
                                   value="{{ old('industry', $client->industry) }}"
                                   placeholder="e.g., Technology, Healthcare, Finance"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('industry') border-red-500 @enderror"
                                   required>
                            @error('industry')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Verification Document -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Verification Document</h3>
                    
                    @if($client->verification_document)
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
                            <p class="text-sm text-green-800">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Verification document uploaded
                            </p>
                        </div>
                    @endif

                    <div>
                        <label for="verification_document" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ $client->verification_document ? 'Replace Verification Document' : 'Upload Verification Document' }}
                        </label>
                        <input type="file" 
                               id="verification_document" 
                               name="verification_document" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('verification_document') border-red-500 @enderror">
                        @error('verification_document')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">
                            Upload a business license, tax document, or other official document to verify your company. 
                            Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG (max 2MB)
                        </p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('client.profile.show') }}" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition duration-200">
                        Cancel
                    </a>
                    
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition duration-200">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection