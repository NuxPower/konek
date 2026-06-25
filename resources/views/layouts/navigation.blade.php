@php
    $role = auth()->user()->role;
    $unreadNotificationCount = auth()->user()->unreadNotifications()->count();
    $mobileNavigation = match ($role) {
        'admin' => [
            ['label' => 'Home', 'route' => 'admin.dashboard', 'pattern' => 'admin/dashboard'],
            ['label' => 'Users', 'route' => 'admin.users.index', 'pattern' => 'admin/users*'],
            ['label' => 'Jobs', 'route' => 'admin.jobs.index', 'pattern' => 'admin/jobs*'],
            ['label' => 'Apps', 'route' => 'admin.applications.index', 'pattern' => 'admin/applications*'],
        ],
        'client' => [
            ['label' => 'Home', 'route' => 'client.dashboard', 'pattern' => 'client/dashboard'],
            ['label' => 'Jobs', 'route' => 'client.jobs.index', 'pattern' => 'client/jobs*'],
            ['label' => 'Applicants', 'route' => 'client.applications.index', 'pattern' => 'client/applications*'],
            ['label' => 'Profile', 'route' => 'client.profile.show', 'pattern' => 'client/profile*'],
        ],
        default => [
            ['label' => 'Home', 'route' => 'freelancer.dashboard', 'pattern' => 'freelancer/dashboard'],
            ['label' => 'Jobs', 'route' => 'freelancer.jobs.index', 'pattern' => 'freelancer/jobs*'],
            ['label' => 'Saved', 'route' => 'freelancer.saved-jobs.index', 'pattern' => 'freelancer/saved-jobs*'],
            ['label' => 'Applied', 'route' => 'freelancer.applications.index', 'pattern' => 'freelancer/applications*'],
        ],
    };
@endphp

<nav x-data="{ open: false }" @keydown.escape.window="open = false" class="sticky top-0 z-40 border-b border-emerald-950/5 bg-white/95 backdrop-blur">
    <div class="mx-auto max-w-[1440px] px-4 sm:px-7">
        <div class="flex h-[72px] items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <x-application-logo class="h-9 w-9 text-emerald-700" />
                <div>
                    <div class="text-sm font-bold tracking-[0.14em] text-emerald-950">KONEK</div>
                    <div class="text-[10px] font-medium text-slate-400">{{ ucfirst($role) }} workspace</div>
                </div>
            </a>

            <div class="hidden items-center gap-4 sm:flex">
                @if($role === 'client')
                    <a href="{{ route('client.jobs.create') }}" class="btn btn-primary !px-3.5 !py-2">Post a job</a>
                @elseif($role === 'freelancer')
                    <a href="{{ route('freelancer.jobs.index') }}" class="btn btn-primary !px-3.5 !py-2">Find work</a>
                @endif

                <a href="{{ route('notifications.index') }}" class="relative grid h-10 w-10 place-items-center rounded-xl text-slate-500 transition hover:bg-emerald-50 hover:text-emerald-800" aria-label="Notifications">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 0 1-6 0v-1"/>
                    </svg>
                    @if($unreadNotificationCount)
                        <span class="absolute right-0.5 top-0.5 min-w-4 rounded-full bg-emerald-600 px-1 text-center text-[10px] font-bold leading-4 text-white">{{ min($unreadNotificationCount, 99) }}</span>
                    @endif
                </a>

                <div class="h-8 w-px bg-slate-200"></div>
                <div class="text-right">
                    <div class="text-sm font-semibold text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="text-xs capitalize text-slate-400">{{ $role }}</div>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="grid h-10 w-10 place-items-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-800 transition hover:bg-emerald-200" aria-label="Open account menu">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Account settings</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log out</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="flex items-center gap-1 sm:hidden">
                <a href="{{ route('notifications.index') }}" class="relative rounded-xl p-2 text-slate-500 hover:bg-emerald-50 hover:text-emerald-800" aria-label="Notifications">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 0 1-6 0v-1"/></svg>
                    @if($unreadNotificationCount)
                        <span class="absolute right-0 top-0 min-w-4 rounded-full bg-emerald-600 px-1 text-center text-[10px] font-bold leading-4 text-white">{{ min($unreadNotificationCount, 99) }}</span>
                    @endif
                </a>
                <button @click="open = true" class="rounded-xl p-2 text-slate-500 hover:bg-emerald-50 hover:text-emerald-800" aria-label="Open navigation menu" :aria-expanded="open">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 sm:hidden">
        <button @click="open = false" class="absolute inset-0 bg-slate-950/35 backdrop-blur-sm" aria-label="Close navigation menu"></button>
        <div x-show="open"
             x-transition:enter="transition duration-200 ease-out"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition duration-150 ease-in"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="absolute inset-y-0 left-0 flex w-[min(86vw,340px)] flex-col bg-[#fbfdfb] p-5 shadow-2xl">
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <x-application-logo class="h-9 w-9 text-emerald-700" />
                    <span class="text-sm font-bold tracking-[0.14em] text-emerald-950">KONEK</span>
                </div>
                <button @click="open = false" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100" aria-label="Close menu">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="mobile-sidebar flex-1 overflow-y-auto">@include('layouts.sidebar')</div>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-left text-sm font-semibold text-slate-600">Log out</button>
            </form>
        </div>
    </div>
</nav>

<nav class="mobile-bottom-nav sm:hidden" aria-label="Primary mobile navigation">
    @foreach($mobileNavigation as $item)
        <a href="{{ route($item['route']) }}" class="{{ request()->is($item['pattern']) ? 'active' : '' }}">
            <span class="mobile-nav-dot"></span>
            <span>{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>
