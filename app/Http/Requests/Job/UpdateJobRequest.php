<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobRequest extends FormRequest
{
    public function authorize()
    {
        $job = $this->route('job');

        return $job && $this->user()?->can('update', $job);
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:30',
            'requirements' => 'required|string|min:20',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:full-time,part-time,contract,internship',
            'experience_level' => 'required|in:entry,intermediate,expert',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'budget_type' => 'required|in:hourly,fixed,negotiable',
            'deadline' => 'nullable|date|after:today',
            'max_applications' => 'nullable|integer|min:1',
            'skills' => 'nullable|array|max:12',
            'skills.*' => 'integer|distinct|exists:skills,id',
        ];
    }
}
