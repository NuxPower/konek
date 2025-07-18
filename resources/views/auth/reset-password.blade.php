<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900">Reset Your Password</h2>
        <p class="text-sm text-gray-600 mt-1">Enter your new password below</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('CMU Email Address')" />
            <x-text-input id="email" 
                          class="block mt-1 w-full bg-gray-50" 
                          type="email" 
                          name="email" 
                          :value="old('email', $request->email)" 
                          required 
                          autofocus 
                          autocomplete="username"
                          readonly />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <p class="mt-1 text-xs text-gray-600">This field is pre-filled and cannot be changed</p>
        </div>

        <!-- New Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('New Password')" />
            <x-text-input id="password" 
                          class="block mt-1 w-full" 
                          type="password" 
                          name="password" 
                          required 
                          autocomplete="new-password"
                          placeholder="Enter your new password" />
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
            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
            <x-text-input id="password_confirmation" 
                          class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation" 
                          required 
                          autocomplete="new-password"
                          placeholder="Confirm your new password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Security Notice -->
        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-blue-800">
                        {{ __('Security Information') }}
                    </p>
                    <ul class="text-xs text-blue-700 mt-1 space-y-0.5">
                        <li>• Your password will be securely encrypted</li>
                        <li>• After reset, you'll need to complete email verification for login</li>
                        <li>• Consider using a password manager for better security</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex flex-col space-y-3 mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Reset Password') }}
            </x-primary-button>

            <div class="text-center">
                <a class="text-sm text-blue-600 hover:text-blue-900 underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-md" 
                   href="{{ route('login') }}">
                    {{ __('Back to Login') }}
                </a>
            </div>
        </div>
    </form>

    <script>
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const requirements = [
                { regex: /.{8,}/, text: 'At least 8 characters' },
                { regex: /[A-Z]/, text: 'At least one uppercase letter' },
                { regex: /[a-z]/, text: 'At least one lowercase letter' },
                { regex: /\d/, text: 'At least one number' }
            ];

            // Visual feedback could be added here to show password strength
        });

        // Match password confirmation
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmation = this.value;
            
            if (confirmation && password !== confirmation) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</x-guest-layout>