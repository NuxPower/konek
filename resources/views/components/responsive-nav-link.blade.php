@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-xl bg-emerald-50 px-4 py-2.5 text-start text-sm font-semibold text-emerald-800'
            : 'block w-full rounded-xl px-4 py-2.5 text-start text-sm font-medium text-slate-600 hover:bg-emerald-50 hover:text-emerald-800';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
