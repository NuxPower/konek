<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" placeholder="you@cmu.edu.ph" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" placeholder="Enter your password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-4">
            <label for="remember_me" class="mb-0 inline-flex items-center gap-2">
                <input id="remember_me" type="checkbox" class="rounded" name="remember">
                <span class="text-sm text-slate-500">{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-900" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full">Log in</x-primary-button>
    </form>

    <div class="mt-6 border-t border-slate-100 pt-5 text-center">
        <p class="text-sm text-slate-500">New to KONEK? <a href="{{ route('register') }}" class="font-semibold text-emerald-700 hover:text-emerald-900">Create an account</a></p>
    </div>
</x-guest-layout>
