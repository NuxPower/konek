<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900">Forgot Your Password?</h2>
        <p class="text-sm text-gray-600 mt-1">No problem. Just let us know your CMU email address and we'll email you a password reset link.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
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
            <p class="mt-1 text-xs text-gray-600">Enter the CMU email address associated with your account</p>
        </div>

        <!-- Information Box -->
        <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-gray-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-gray-900">
                        {{ __('What happens next?') }}
                    </p>
                    <ul class="text-xs text-gray-600 mt-1 space-y-0.5">
                        <li>• We'll send a secure reset link to your email</li>
                        <li>• The link will expire in 60 minutes for security</li>
                        <li>• Check your spam/junk folder if you don't see the email</li>
                        <li>• You can request a new link if the current one expires</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex flex-col space-y-3 mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>

            <div class="text-center">
                <a class="text-sm text-blue-600 hover:text-blue-900 underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-md" 
                   href="{{ route('login') }}">
                    {{ __('Back to Login') }}
                </a>
            </div>
        </div>
    </form>

    <!-- Help Section -->
    <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <h3 class="text-sm font-medium text-yellow-800 mb-2">Still having trouble?</h3>
        <ul class="text-xs text-yellow-700 space-y-1">
            <li>• Make sure you're using your official CMU email address</li>
            <li>• Contact IT support if you can't access your CMU email</li>
            <li>• For new students, your account may need activation first</li>
        </ul>
    </div>
</x-guest-layout>