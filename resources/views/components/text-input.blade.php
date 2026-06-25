@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500']) }}>
