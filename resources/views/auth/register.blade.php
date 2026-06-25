<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="mt-2 block w-full" type="text" name="name" :value="old('name')" placeholder="Your full name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" placeholder="you@cmu.edu.ph" required autocomplete="username" />
            <p class="mt-2 text-xs text-slate-400">Use your official CMU email address.</p>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="mt-2 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="role" value="I want to" />
            <select id="role" name="role" class="mt-2" required>
                <option value="freelancer" @selected(old('role', 'freelancer') === 'freelancer')>Find work as talent</option>
                <option value="client" @selected(old('role') === 'client')>Post jobs as a client</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="mt-2 block w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">Create account</x-primary-button>
    </form>
    <p class="mt-6 border-t border-slate-100 pt-5 text-center text-sm text-slate-500">Already registered? <a class="font-semibold text-emerald-700" href="{{ route('login') }}">Log in</a></p>
</x-guest-layout>
