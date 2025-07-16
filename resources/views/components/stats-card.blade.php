<!-- Stats card component -->
@props(['label', 'value'])
<div class="stats-card" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; background: #f9fafb; text-align: center; margin-bottom: 1rem;">
    <div style="font-size: 2rem; font-weight: bold;">{{ $value }}</div>
    <div style="color: #6b7280;">{{ $label }}</div>
</div> 