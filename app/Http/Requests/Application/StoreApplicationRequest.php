<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize()
    {
        $job = $this->route('job');

        return $job && $this->user()?->can('apply', $job);
    }

    public function rules()
    {
        return [
            'cover_letter' => 'required|string|min:50|max:5000',
            'proposed_rate' => 'nullable|numeric|min:0',
            'rate_type' => 'nullable|required_with:proposed_rate|in:hourly,fixed',
            'estimated_hours' => 'nullable|integer|min:1',
            'portfolio_links' => 'nullable|string|max:2000',
        ];
    }
}
