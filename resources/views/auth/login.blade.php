<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
        <p class="text-sm text-gray-600 mt-1">Sign in to your CMU Freelance account</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('CMU Email Address')" />
            <x-text-input id="email" 
                          class="block mt-1 w-full" 
                          type="email" 
                          name="email" 
                          :value="old('email')" 
                          required 
                          autofocus 
                          autocomplete="username"
                          placeholder="your-name@cmu.edu.ph" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" 
                          class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required 
                          autocomplete="current-password"
                          placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Email OTP Information -->
        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-blue-800">
                        {{ __('Two-Factor Authentication Required') }}
                    </p>
                    <p class="text-xs text-blue-600 mt-1">
                        {{ __('A 6-digit verification code will be sent to your email address after login for enhanced security.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" 
                       type="checkbox" 
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" 
                       name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex flex-col space-y-3 mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Sign In') }}
            </x-primary-button>

            <div class="flex items-center justify-between text-sm">
                @if (Route::has('password.request'))
                    <a class="text-blue-600 hover:text-blue-900 underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-md" 
                       href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <a class="text-blue-600 hover:text-blue-900 underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-md" 
                   href="{{ route('register') }}">
                    {{ __('New user? Sign up') }}
                </a>
            </div>
        </div>
    </form>

    <!-- Help Section -->
    <div class="mt-8 p-4 bg-gray-50 border border-gray-200 rounded-lg">
        <h3 class="text-sm font-medium text-gray-900 mb-2">Need Help?</h3>
        <ul class="text-xs text-gray-600 space-y-1">
            <li>• Use your official CMU email address (@cmu.edu.ph)</li>
            <li>• Check your email for the verification code after login</li>
            <li>• Contact IT support if you're having trouble accessing your account</li>
        </ul>
    </div>
</x-guest-layout>