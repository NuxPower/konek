<!-- Stats card component -->
@props(['label', 'value', 'icon' => null, 'color' => '#2563eb'])
<div class="stats-card" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; background: #f9fafb; text-align: center; margin-bottom: 1rem; min-width: 120px; box-shadow: 0 2px 8px #e5e7eb;">
    @if($icon)
        <div style="font-size: 2rem; color: {{ $color }}; margin-bottom: 0.25rem;">{!! $icon !!}</div>
    @endif
    <div style="font-size: 2rem; font-weight: bold; color: {{ $color }};">{{ $value }}</div>
    <div style="color: #6b7280; font-weight: 500;">{{ $label }}</div>
</div> 