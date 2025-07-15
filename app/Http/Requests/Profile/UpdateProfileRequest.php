<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'department' => 'nullable|string|max:255',
            'year_level' => 'nullable|integer|min:1|max:6',
            'student_id' => 'nullable|string|max:50',
        ];
    }
} 