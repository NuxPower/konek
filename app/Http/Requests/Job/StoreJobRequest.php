<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:full-time,part-time,contract,internship',
            'experience_level' => 'required|in:entry,intermediate,expert',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'budget_type' => 'required|in:hourly,fixed,negotiable',
            'deadline' => 'nullable|date|after:today',
            'max_applications' => 'nullable|integer|min:1',
            'is_featured' => 'boolean',
            'attachments' => 'nullable|array',
        ];
    }
} 