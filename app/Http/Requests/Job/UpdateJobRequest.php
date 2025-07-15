<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'requirements' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'type' => 'sometimes|required|in:full-time,part-time,contract,internship',
            'experience_level' => 'sometimes|required|in:entry,intermediate,expert',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'budget_type' => 'sometimes|required|in:hourly,fixed,negotiable',
            'deadline' => 'nullable|date|after:today',
            'max_applications' => 'nullable|integer|min:1',
            'is_featured' => 'boolean',
            'attachments' => 'nullable|array',
        ];
    }
} 