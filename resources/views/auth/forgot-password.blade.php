<x-guest-layout>
    <p class="mb-6 text-sm leading-6 text-slate-500">Enter your account email and we will send you a secure password reset link.</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" placeholder="you@cmu.edu.ph" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">Send reset link</x-primary-button>
    </form>
    <p class="mt-6 text-center text-sm"><a href="{{ route('login') }}" class="font-semibold text-emerald-700">Back to login</a></p>
</x-guest-layout>
