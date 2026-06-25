<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'KONEK') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#f7f8f4]">
        @php
            [$eyebrow, $heading, $subtitle] = match (request()->route()?->getName()) {
                'register' => ['Create account', 'Join KONEK', 'Use your official CMU email to get started.'],
                'password.request' => ['Password reset', 'Recover your account', 'We will email you a secure password reset link.'],
                'password.reset' => ['Password reset', 'Set a new password', 'Choose a secure password for your account.'],
                'password.confirm' => ['Security', 'Confirm your password', 'Enter your password to continue.'],
                'verification.notice' => ['Email verification', 'Check your inbox', 'Verify your CMU email to activate your account.'],
                default => ['Welcome back', 'Sign in to KONEK', 'Enter your account details below.'],
            };
        @endphp

        <div class="min-h-screen">
            <header class="px-5 py-6 sm:px-8">
                <div class="mx-auto flex max-w-6xl items-center justify-between">
                    <a href="/" class="flex items-center gap-3">
                        <x-application-logo class="h-9 w-9 text-[#176b4d]" />
                        <span class="text-sm font-bold tracking-[0.16em] text-[#143d30]">KONEK</span>
                    </a>
                    <a href="/" class="text-sm font-medium text-slate-500 hover:text-[#176b4d]">Back to home</a>
                </div>
            </header>

            <main class="flex items-center justify-center px-5 pb-16 pt-8 sm:px-8 sm:pt-14">
                <div class="w-full max-w-[430px]">
                    <div class="mb-8 text-center">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-[#2f7d5f]">{{ $eyebrow }}</p>
                        <h1 class="text-3xl font-semibold tracking-[-0.025em] text-[#163c30]">{{ $heading }}</h1>
                        <p class="mt-3 text-sm leading-6 text-slate-500">{{ $subtitle }}</p>
                    </div>

                    <div class="rounded-[22px] border border-[#dfe6df] bg-white p-6 shadow-[0_18px_50px_rgba(31,65,50,0.06)] sm:p-8">
                        {{ $slot }}
                    </div>

                    <p class="mt-7 text-center text-xs leading-5 text-slate-400">
                        Central Mindanao University Talent Network
                    </p>
                </div>
            </main>
        </div>
    </body>
</html>
