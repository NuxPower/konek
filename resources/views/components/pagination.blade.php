<!-- Pagination component -->
@props(['paginator'])
@if($paginator->hasPages())
    <div class="pagination" style="margin: 1rem 0;">
        {{ $paginator->links() }}
    </div>
@endif 