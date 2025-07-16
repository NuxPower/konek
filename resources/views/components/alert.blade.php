<!-- Alert component -->
@props(['type' => 'info'])
<div class="alert alert-{{ $type }}" style="padding: 1rem; border-radius: 4px; margin-bottom: 1rem; background: {{ $type === 'success' ? '#d1fae5' : ($type === 'error' ? '#fee2e2' : '#e0e7ef') }}; color: #222;">
    {{ $slot }}
</div> 