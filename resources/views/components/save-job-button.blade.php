@props(['job', 'saved' => false, 'compact' => false])

<form method="POST"
      action="{{ $saved ? route('freelancer.saved-jobs.destroy', $job) : route('freelancer.saved-jobs.store', $job) }}"
      class="!m-0 !max-w-none !border-0 !bg-transparent !p-0 !shadow-none">
    @csrf
    @if($saved)
        @method('DELETE')
    @endif
    <button type="submit"
            class="{{ $compact ? 'grid h-9 w-9 place-items-center rounded-xl border' : 'btn btn-secondary w-full' }} {{ $saved ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-slate-200 bg-white text-slate-500 hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-800' }}"
            aria-label="{{ $saved ? 'Remove from saved jobs' : 'Save job' }}"
            title="{{ $saved ? 'Remove from saved jobs' : 'Save job' }}">
        <svg class="h-4 w-4" fill="{{ $saved ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4.8A1.8 1.8 0 0 1 7.8 3h8.4A1.8 1.8 0 0 1 18 4.8V21l-6-3.8L6 21V4.8Z"/>
        </svg>
        @unless($compact)
            <span class="ml-2">{{ $saved ? 'Saved' : 'Save for later' }}</span>
        @endunless
    </button>
</form>
