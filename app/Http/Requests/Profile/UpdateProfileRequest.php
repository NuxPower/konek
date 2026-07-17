<?php

namespace App\Http\Requests\Profile;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => [
                'nullable',
                'string',
                'min:3',
                'max:40',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'introduction' => 'nullable|string|max:160',
            'department' => 'nullable|string|max:255',
            'year_level' => 'nullable|integer|min:1|max:6',
            'availability' => 'nullable|string|max:50',
            'portfolio_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'is_profile_public' => 'sometimes|boolean',
            'show_email' => 'sometimes|boolean',
            'show_phone' => 'sometimes|boolean',
            'show_links' => 'sometimes|boolean',
            'remove_profile_photo' => 'sometimes|boolean',
            'skills' => 'nullable|array|max:20',
            'skills.*' => ['integer', Rule::exists('skills', 'id')->where('is_active', true)],
            'profile_photo' => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:2048|dimensions:max_width=3000,max_height=3000',
            'resume' => 'nullable|file|mimes:pdf|max:4096',
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'Use lowercase letters, numbers, and single hyphens only.',
            'profile_photo.image' => 'Upload a valid image file.',
            'profile_photo.mimes' => 'Profile photos must be JPG, PNG, or WebP files.',
            'resume.mimes' => 'Resume/CV uploads must be PDF files.',
        ];
    }
}
