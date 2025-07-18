<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'CMU Freelance'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Additional Styles -->
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mx-4 mt-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-4 mt-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if (session('warning'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mx-4 mt-4" role="alert">
                    <span class="block sm:inline">{{ session('warning') }}</span>
                </div>
            @endif

            @if (session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mx-4 mt-4" role="alert">
                    <span class="block sm:inline">{{ session('info') }}</span>
                </div>
            @endif

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-gray-800 text-white py-12 mt-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div class="col-span-1 md:col-span-2">
                            <h3 class="text-2xl font-bold mb-4">{{ config('app.name', 'CMU Freelance') }}</h3>
                            <p class="text-gray-300 mb-4">
                                Connect with top freelancers and clients at Central Mindanao University. Your success is our mission.
                            </p>
                            <div class="flex space-x-4">
                                <a href="#" class="text-gray-400 hover:text-white transition duration-200" aria-label="Twitter">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                    </svg>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-white transition duration-200" aria-label="Facebook">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-white transition duration-200" aria-label="LinkedIn">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold mb-4">For Freelancers</h4>
                            <ul class="space-y-2 text-gray-300">
                                <li><a href="{{ route('search') }}" class="hover:text-white transition duration-200">Find Jobs</a></li>
                                @auth
                                    @if(auth()->user()->hasRole('freelancer'))
                                        <li><a href="{{ route('freelancer.applications.index') }}" class="hover:text-white transition duration-200">My Applications</a></li>
                                        <li><a href="{{ route('freelancer.jobs.browse') }}" class="hover:text-white transition duration-200">Browse Jobs</a></li>
                                        <li><a href="{{ route('freelancer.profile.edit') }}" class="hover:text-white transition duration-200">Profile</a></li>
                                    @endif
                                @else
                                    <li><a href="{{ route('register') }}" class="hover:text-white transition duration-200">Join as Freelancer</a></li>
                                    <li><a href="{{ route('login') }}" class="hover:text-white transition duration-200">Login</a></li>
                                @endauth
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold mb-4">For Clients</h4>
                            <ul class="space-y-2 text-gray-300">
                                @auth
                                    @if(auth()->user()->hasRole('client'))
                                        <li><a href="{{ route('client.jobs.create') }}" class="hover:text-white transition duration-200">Post a Job</a></li>
                                        <li><a href="{{ route('client.jobs.index') }}" class="hover:text-white transition duration-200">My Jobs</a></li>
                                        <li><a href="{{ route('client.applications.index') }}" class="hover:text-white transition duration-200">Applications</a></li>
                                        <li><a href="{{ route('client.profile.edit') }}" class="hover:text-white transition duration-200">Profile</a></li>
                                    @endif
                                @else
                                    <li><a href="{{ route('register') }}" class="hover:text-white transition duration-200">Hire Freelancers</a></li>
                                    <li><a href="{{ route('about') }}" class="hover:text-white transition duration-200">How It Works</a></li>
                                @endauth
                            </ul>
                        </div>
                    </div>
                    <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                        <p class="text-gray-400">© {{ date('Y') }} {{ config('app.name', 'CMU Freelance') }}. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Additional Scripts -->
        @stack('scripts')
    </body>
</html>