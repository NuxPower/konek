<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6M8 8v10m8-10v10"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-xl font-bold text-gray-900">CMU Freelance</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @auth
                        @switch(auth()->user()->role)
                            @case('admin')
                                <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                                    {{ __('Admin Dashboard') }}
                                </x-nav-link>
                                <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                    {{ __('Users') }}
                                </x-nav-link>
                                <x-nav-link :href="route('admin.jobs.index')" :active="request()->routeIs('admin.jobs.*')">
                                    {{ __('Jobs') }}
                                </x-nav-link>
                                <x-nav-link :href="route('admin.applications.index')" :active="request()->routeIs('admin.applications.*')">
                                    {{ __('Applications') }}
                                </x-nav-link>
                                @break
                            
                            @case('client')
                                <x-nav-link :href="route('client.dashboard')" :active="request()->routeIs('client.dashboard')">
                                    {{ __('Dashboard') }}
                                </x-nav-link>
                                <x-nav-link :href="route('client.jobs.index')" :active="request()->routeIs('client.jobs.*')">
                                    {{ __('My Jobs') }}
                                </x-nav-link>
                                <x-nav-link :href="route('client.applications.index')" :active="request()->routeIs('client.applications.*')">
                                    {{ __('Applications') }}
                                </x-nav-link>
                                @break
                            
                            @case('freelancer')
                                <x-nav-link :href="route('freelancer.dashboard')" :active="request()->routeIs('freelancer.dashboard')">
                                    {{ __('Dashboard') }}
                                </x-nav-link>
                                <x-nav-link :href="route('freelancer.jobs.browse')" :active="request()->routeIs('freelancer.jobs.*')">
                                    {{ __('Browse Jobs') }}
                                </x-nav-link>
                                <x-nav-link :href="route('freelancer.applications.index')" :active="request()->routeIs('freelancer.applications.*')">
                                    {{ __('My Applications') }}
                                </x-nav-link>
                                @break
                        @endswitch
                    @else
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                            {{ __('Home') }}
                        </x-nav-link>
                        @if(Route::has('jobs.index'))
                            <x-nav-link :href="route('jobs.index')" :active="request()->routeIs('jobs.*')">
                                {{ __('Browse Jobs') }}
                            </x-nav-link>
                        @endif
                        <x-nav-link :href="route('search')" :active="request()->routeIs('search')">
                            {{ __('Search') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            @auth
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ auth()->user()->name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Role Display -->
                            <div class="px-4 py-2 text-xs text-gray-400 border-b">
                                {{ auth()->user()->getRoleDisplayName() }}
                            </div>

                            <!-- Profile Link -->
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Role-specific links -->
                            @if(auth()->user()->isAdmin())
                                <x-dropdown-link :href="route('admin.reports.index')">
                                    {{ __('Reports') }}
                                </x-dropdown-link>
                            @endif

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            @else
                <!-- Guest Links -->
                <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                        {{ __('Login') }}
                    </a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition duration-200">
                        {{ __('Sign Up') }}
                    </a>
                </div>
            @endauth

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @switch(auth()->user()->role)
                    @case('admin')
                        <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Admin Dashboard') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Users') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.jobs.index')" :active="request()->routeIs('admin.jobs.*')">
                            {{ __('Jobs') }}
                        </x-responsive-nav-link>
                        @break
                    
                    @case('client')
                        <x-responsive-nav-link :href="route('client.dashboard')" :active="request()->routeIs('client.dashboard')">
                            {{ __('Dashboard') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('client.jobs.index')" :active="request()->routeIs('client.jobs.*')">
                            {{ __('My Jobs') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('client.applications.index')" :active="request()->routeIs('client.applications.*')">
                            {{ __('Applications') }}
                        </x-responsive-nav-link>
                        @break
                    
                    @case('freelancer')
                        <x-responsive-nav-link :href="route('freelancer.dashboard')" :active="request()->routeIs('freelancer.dashboard')">
                            {{ __('Dashboard') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('freelancer.jobs.browse')" :active="request()->routeIs('freelancer.jobs.*')">
                            {{ __('Browse Jobs') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('freelancer.applications.index')" :active="request()->routeIs('freelancer.applications.*')">
                            {{ __('My Applications') }}
                        </x-responsive-nav-link>
                        @break
                @endswitch
            @else
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                    {{ __('Home') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('search')" :active="request()->routeIs('search')">
                    {{ __('Search Jobs') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ auth()->user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Login') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        @endauth
    </div>
</nav>