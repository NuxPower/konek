<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Please enter your CMU email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Attempt authentication
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        // Clear rate limiter on successful authentication
        RateLimiter::clear($this->throttleKey());

        // Get the authenticated user
        $user = Auth::user();

        // Check if user account is active
        if (isset($user->is_active) && !$user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Your account has been deactivated. Please contact support.',
            ]);
        }

        // Check if user is verified (if your system requires email verification)
        if (method_exists($user, 'hasVerifiedEmail') && !$user->hasVerifiedEmail()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Please verify your email address before logging in.',
            ]);
        }

        // Validate CMU email domain (if required)
        if (!$this->isValidCmuEmail($user->email)) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Please use your official CMU email address (@cmu.edu.ph).',
            ]);
        }
    }

    /**
     * Validate if the email is a valid CMU email address.
     *
     * @param string $email
     * @return bool
     */
    protected function isValidCmuEmail(string $email): bool
    {
        // Check if email ends with @cmu.edu.ph
        return str_ends_with(strtolower($email), '@cmu.edu.ph');
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional validation can be added here if needed
            if ($validator->errors()->isEmpty()) {
                // You can add custom validation logic here
            }
        });
    }
}