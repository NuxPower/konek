<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:admin,client,freelancer'],
            'phone' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'student_id' => ['nullable', 'string', 'max:255', 'unique:users'],
            'department' => ['nullable', 'string', 'max:255'],
            'year_level' => ['nullable', 'integer', 'min:1', 'max:' . date('Y')],
        ]);

        // Additional validation for freelancer role
        if ($request->input('role') === 'freelancer') {
            $request->validate([
                'student_id' => ['required', 'string', 'max:255', 'unique:users'],
                'department' => ['required', 'string', 'max:255'],
                'year_level' => ['required', 'integer', 'min:1', 'max:' . date('Y')],
            ]);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
            'phone' => $request->input('phone'),
            'bio' => $request->input('bio'),
            'student_id' => $request->input('student_id'),
            'department' => $request->input('department'),
            'year_level' => $request->input('year_level'),
            'is_active' => true,
        ]);

        event(new Registered($user));
        Auth::login($user);

        // Redirect based on role
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole(User $user): RedirectResponse
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'client':
                return redirect()->route('client.dashboard');
            case 'freelancer':
                return redirect()->route('freelancer.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }
}

// Alternative approach using Form Request (recommended)
class RegisterUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:admin,client,freelancer'],
            'phone' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'student_id' => ['nullable', 'string', 'max:255', 'unique:users'],
            'department' => ['nullable', 'string', 'max:255'],
            'year_level' => ['nullable', 'integer', 'min:1', 'max:' . date('Y')],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already registered.',
            'student_id.unique' => 'This student ID is already registered.',
            'role.in' => 'Please select a valid role.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();
            
            // If role is freelancer, require student fields
            if (isset($data['role']) && $data['role'] === 'freelancer') {
                if (empty($data['student_id'])) {
                    $validator->errors()->add('student_id', 'Student ID is required for freelancers.');
                }
                if (empty($data['department'])) {
                    $validator->errors()->add('department', 'Department is required for freelancers.');
                }
                if (empty($data['year_level'])) {
                    $validator->errors()->add('year_level', 'Year level is required for freelancers.');
                }
            }
        });
    }
}