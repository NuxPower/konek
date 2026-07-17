<section>
    <header>
        <h2>{{ __('Profile details') }}</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">
            {{ __('Update the name shown across KONEK. Your email is your verified sign-in identity and cannot be changed here.') }}
        </p>
    </header>

    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50/70 p-4">
        <div class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('Sign-in email') }}</div>
        <div class="mt-1 break-all text-sm font-semibold text-slate-900">{{ $user->email }}</div>
        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500">
            @if($user->hasVerifiedEmail())
                <span class="rounded-full bg-emerald-100 px-2.5 py-1 font-semibold text-emerald-700">{{ __('Verified') }}</span>
            @else
                <span class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-700">{{ __('Verification pending') }}</span>
            @endif
            <span class="capitalize">{{ $user->role }} {{ __('account') }}</span>
        </div>
    </div>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5 !m-0 !max-w-none !border-0 !bg-transparent !p-0 !shadow-none">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Display name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save changes') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-emerald-700"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
