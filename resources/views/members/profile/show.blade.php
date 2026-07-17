@php
    $photoUrl = $user->profile_photo_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_photo_path) : null;
    $canDownloadResume = $viewer && $user->resume_path && $user->canBeViewedBy($viewer);
    $profileLinks = collect([
        'Portfolio' => $user->portfolio_url,
        'LinkedIn' => $user->linkedin_url,
        'GitHub' => $user->github_url,
        'Facebook' => $user->facebook_url,
    ])->filter();
@endphp

@auth
    <x-app-layout>
        @include('members.profile._body')
    </x-app-layout>
@else
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>{{ $user->name }} - KONEK</title>
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        </head>
        <body class="font-sans antialiased">
            <main class="mx-auto min-h-screen max-w-[1120px] px-4 py-8 sm:px-7 lg:px-10">
                @include('members.profile._body')
            </main>
        </body>
    </html>
@endauth
