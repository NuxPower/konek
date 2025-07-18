<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900">Join CMU Freelance</h2>
        <p class="text-sm text-gray-600 mt-1">Create your account to start freelancing or hiring</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" 
                          class="block mt-1 w-full" 
                          type="text" 
                          name="name" 
                          :value="old('name')" 
                          required 
                          autofocus 
                          autocomplete="name"
                          placeholder="Enter your full name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('CMU Email Address')" />
            <x-text-input id="email" 
                          class="block mt-1 w-full" 
                          type="email" 
                          name="email" 
                          :value="old('email')" 
                          required 
                          autocomplete="username"
                          placeholder="your-name@cmu.edu.ph" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <p class="mt-1 text-xs text-gray-600">Must be a valid CMU email address ending with @cmu.edu.ph</p>
        </div>

        <!-- Role Selection -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('I want to')" />
            <select id="role" 
                    name="role" 
                    class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                    required>
                <option value="">Select your role</option>
                <option value="client" {{ old('role') == 'client' ? 'selected' : '' }}>
                    Hire freelancers (Client)
                </option>
                <option value="freelancer" {{ old('role') == 'freelancer' ? 'selected' : '' }}>
                    Offer my services (Freelancer)
                </option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Additional Information (conditionally shown) -->
        <div id="additional-fields" class="mt-4 space-y-4 hidden">
            <!-- Student ID -->
            <div>
                <x-input-label for="student_id" :value="__('Student ID (Optional)')" />
                <x-text-input id="student_id" 
                              class="block mt-1 w-full" 
                              type="text" 
                              name="student_id" 
                              :value="old('student_id')"
                              placeholder="e.g., 2021-12345" />
                <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
            </div>

            <!-- Department -->
            <div>
                <x-input-label for="department" :value="__('Department/College (Optional)')" />
                <select id="department" 
                        name="department" 
                        class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                    <option value="">Select your department</option>
                    <option value="College of Engineering" {{ old('department') == 'College of Engineering' ? 'selected' : '' }}>College of Engineering</option>
                    <option value="College of Computer Studies" {{ old('department') == 'College of Computer Studies' ? 'selected' : '' }}>College of Computer Studies</option>
                    <option value="College of Business Administration" {{ old('department') == 'College of Business Administration' ? 'selected' : '' }}>College of Business Administration</option>
                    <option value="College of Arts and Sciences" {{ old('department') == 'College of Arts and Sciences' ? 'selected' : '' }}>College of Arts and Sciences</option>
                    <option value="College of Education" {{ old('department') == 'College of Education' ? 'selected' : '' }}>College of Education</option>
                    <option value="College of Architecture and Fine Arts" {{ old('department') == 'College of Architecture and Fine Arts' ? 'selected' : '' }}>College of Architecture and Fine Arts</option>
                    <option value="Other" {{ old('department') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                <x-input-error :messages="$errors->get('department')" class="mt-2" />
            </div>

            <!-- Year Level (for students) -->
            <div>
                <x-input-label for="year_level" :value="__('Year Level (Optional)')" />
                <select id="year_level" 
                        name="year_level" 
                        class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                    <option value="">Select year level</option>
                    <option value="1" {{ old('year_level') == '1' ? 'selected' : '' }}>1st Year</option>
                    <option value="2" {{ old('year_level') == '2' ? 'selected' : '' }}>2nd Year</option>
                    <option value="3" {{ old('year_level') == '3' ? 'selected' : '' }}>3rd Year</option>
                    <option value="4" {{ old('year_level') == '4' ? 'selected' : '' }}>4th Year</option>
                    <option value="5" {{ old('year_level') == '5' ? 'selected' : '' }}>5th Year</option>
                    <option value="graduate" {{ old('year_level') == 'graduate' ? 'selected' : '' }}>Graduate Student</option>
                    <option value="faculty" {{ old('year_level') == 'faculty' ? 'selected' : '' }}>Faculty/Staff</option>
                </select>
                <x-input-error :messages="$errors->get('year_level')" class="mt-2" />
            </div>

            <!-- Phone Number -->
            <div>
                <x-input-label for="phone" :value="__('Phone Number (Optional)')" />
                <x-text-input id="phone" 
                              class="block mt-1 w-full" 
                              type="tel" 
                              name="phone" 
                              :value="old('phone')"
                              placeholder="e.g., +63 912 345 6789" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" 
                          class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required 
                          autocomplete="new-password"
                          placeholder="Create a strong password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <div class="mt-1 text-xs text-gray-600">
                <p>Password must contain:</p>
                <ul class="list-disc list-inside ml-2 space-y-0.5">
                    <li>At least 8 characters</li>
                    <li>At least one uppercase letter</li>
                    <li>At least one lowercase letter</li>
                    <li>At least one number</li>
                </ul>
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" 
                          class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation" 
                          required 
                          autocomplete="new-password"
                          placeholder="Confirm your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Terms and Conditions -->
        <div class="mt-4">
            <label for="terms" class="inline-flex items-start">
                <input id="terms" 
                       type="checkbox" 
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 mt-1" 
                       name="terms" 
                       required>
                <span class="ms-2 text-sm text-gray-600">
                    I agree to the 
                    <a href="{{ route('terms') }}" class="text-blue-600 hover:text-blue-900 underline" target="_blank">Terms of Service</a> 
                    and 
                    <a href="{{ route('privacy') }}" class="text-blue-600 hover:text-blue-900 underline" target="_blank">Privacy Policy</a>
                </span>
            </label>
            <x-input-error :messages="$errors->get('terms')" class="mt-2" />
        </div>

        <!-- Security Notice -->
        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-yellow-800">
                        {{ __('Email Verification Required') }}
                    </p>
                    <p class="text-xs text-yellow-700 mt-1">
                        {{ __('After registration, you\'ll need to verify your CMU email address and complete two-factor authentication setup.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-col space-y-3 mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Create Account') }}
            </x-primary-button>

            <div class="text-center">
                <a class="text-sm text-blue-600 hover:text-blue-900 underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-md" 
                   href="{{ route('login') }}">
                    {{ __('Already have an account? Sign in') }}
                </a>
            </div>
        </div>
    </form>

    <script>
        // Show additional fields when role is selected
        document.getElementById('role').addEventListener('change', function() {
            const additionalFields = document.getElementById('additional-fields');
            if (this.value) {
                additionalFields.classList.remove('hidden');
            } else {
                additionalFields.classList.add('hidden');
            }
        });

        // Show additional fields if role is pre-selected (e.g., on validation error)
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            if (roleSelect.value) {
                document.getElementById('additional-fields').classList.remove('hidden');
            }
        });

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const requirements = [
                { regex: /.{8,}/, text: 'At least 8 characters' },
                { regex: /[A-Z]/, text: 'At least one uppercase letter' },
                { regex: /[a-z]/, text: 'At least one lowercase letter' },
                { regex: /\d/, text: 'At least one number' }
            ];

            // You can add visual feedback for password strength here
        });
    </script>
</x-guest-layout>