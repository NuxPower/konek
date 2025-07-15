<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cover_letter' => 'required|string',
            'proposed_rate' => 'nullable|numeric|min:0',
            'rate_type' => 'nullable|in:hourly,fixed',
            'estimated_hours' => 'nullable|integer|min:1',
            'portfolio_links' => 'nullable|string',
            'attachments' => 'nullable|array',
            'status' => 'in:pending,reviewing,shortlisted,rejected,accepted,withdrawn',
        ];
    }
} 