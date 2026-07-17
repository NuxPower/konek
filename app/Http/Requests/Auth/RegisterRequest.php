<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|ends_with:cmu.edu.ph|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|in:member',
        ];
    }
}
