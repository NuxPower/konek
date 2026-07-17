@props(['name' => 'circle'])

@php
    $paths = [
        'dashboard' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 11.25 12 4.5l8.25 6.75M5.25 10.5v8.25h4.5V14.25h4.5v4.5h4.5V10.5"/>',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 19.5v-1.25A3.25 3.25 0 0 0 13.25 15h-2.5a3.25 3.25 0 0 0-3.25 3.25v1.25M12 12a3.25 3.25 0 1 0 0-6.5 3.25 3.25 0 0 0 0 6.5Zm6.25 1.5a2.75 2.75 0 0 0-2-4.65M5.75 13.5a2.75 2.75 0 0 1 2-4.65"/>',
        'jobs' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 7.5V6.25A2.25 2.25 0 0 1 11.25 4h1.5A2.25 2.25 0 0 1 15 6.25V7.5m-9.25 0h12.5A1.75 1.75 0 0 1 20 9.25v7.5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7.5A1.75 1.75 0 0 1 5.75 7.5Zm0 4.5h12.5M12 11.25v1.5"/>',
        'posted' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 5v8m-4-4h8M5.75 4.75h12.5A1.75 1.75 0 0 1 20 6.5v11.75A1.75 1.75 0 0 1 18.25 20H5.75A1.75 1.75 0 0 1 4 18.25V6.5a1.75 1.75 0 0 1 1.75-1.75Z"/>',
        'applications' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 5h6.5L18 8.25v10A1.75 1.75 0 0 1 16.25 20h-8.5A1.75 1.75 0 0 1 6 18.25V6.75A1.75 1.75 0 0 1 7.75 5h.5Zm6.25.25V8.5h3.25M8.75 12h6.5m-6.5 3h6.5"/>',
        'saved' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 5.75A2.75 2.75 0 0 1 9.5 3h5A2.75 2.75 0 0 1 17.25 5.75V20L12 16.75 6.75 20V5.75Z"/>',
        'profile' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 12.25a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Zm7 7a7 7 0 0 0-14 0"/>',
        'notifications' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 17.25H9m6 0a3 3 0 0 1-6 0m6 0h4.25l-1.2-1.35a2.5 2.5 0 0 1-.63-1.66V11a5.42 5.42 0 0 0-10.84 0v3.24c0 .61-.22 1.2-.63 1.66l-1.2 1.35H9"/>',
        'reports' => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 19V5m0 14h14M9 16v-5m4 5V8m4 8v-3"/>',
        'activity' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 12h3l2-5 4 10 2-5h5"/>',
        'circle' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 12h.01"/>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
    {!! $paths[$name] ?? $paths['circle'] !!}
</svg>
