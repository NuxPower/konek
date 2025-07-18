<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Please enter the verification code sent to your email address.') }}
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.verify') }}">
            @csrf

            <!-- OTP Code -->
            <div>
                <x-input-label for="otp" :value="__('Verification Code')" />
                <x-text-input id="otp" class="block mt-1 w-full" type="text" name="otp" :value="old('otp')" required autofocus />
                <x-input-error :messages="$errors->get('otp')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <x-primary-button>
                    {{ __('Verify Email') }}
                </x-primary-button>

                <form method="POST" action="{{ route('verification.resend') }}" class="inline">
                    @csrf
                    <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Resend Code') }}
                    </button>
                </form>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </x-auth-card>
</x-guest-layout>