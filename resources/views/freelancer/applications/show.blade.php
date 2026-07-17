@extends('layouts.app')

@section('content')
<a href="{{ route('member.applications.index') }}" class="mb-6 inline-flex text-sm font-semibold text-slate-500 hover:text-emerald-800">← Back to my applications</a>
<div class="page-header">
    <div>
        <p class="page-eyebrow">Application status</p>
        <h1>{{ $application->job->title ?? 'Application' }}</h1>
        <p class="page-subtitle">Submitted to {{ $application->job->client->name ?? 'CMU Client' }} on {{ $application->created_at->format('M d, Y') }}.</p>
    </div>
    <span class="badge badge-application-{{ $application->status }}">{{ ucfirst($application->status) }}</span>
</div>

<div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_310px]">
    <section class="panel">
        @can('update', $application)
            <form method="POST" action="{{ route('member.applications.update', $application) }}" class="!m-0 !max-w-none !border-0 !bg-transparent !p-0 !shadow-none">
                @csrf
                @method('PATCH')
                <div>
                    <label for="cover_letter">Cover letter</label>
                    <textarea id="cover_letter" name="cover_letter" rows="9" required>{{ old('cover_letter', $application->cover_letter) }}</textarea>
                    <x-input-error :messages="$errors->get('cover_letter')" class="mt-2" />
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div><label for="proposed_rate">Proposed rate</label><input id="proposed_rate" type="number" min="0" step="0.01" name="proposed_rate" value="{{ old('proposed_rate', $application->proposed_rate) }}"></div>
                    <div>
                        <label for="rate_type">Rate type</label>
                        <select id="rate_type" name="rate_type">
                            <option value="">Select rate type</option>
                            <option value="fixed" @selected(old('rate_type', $application->rate_type) === 'fixed')>Fixed</option>
                            <option value="hourly" @selected(old('rate_type', $application->rate_type) === 'hourly')>Hourly</option>
                        </select>
                    </div>
                </div>
                <div><label for="estimated_hours">Estimated hours</label><input id="estimated_hours" type="number" min="1" name="estimated_hours" value="{{ old('estimated_hours', $application->estimated_hours) }}"></div>
                <div><label for="portfolio_links">Portfolio links</label><textarea id="portfolio_links" name="portfolio_links" rows="3">{{ old('portfolio_links', $application->portfolio_links) }}</textarea></div>
                <x-primary-button>Save application</x-primary-button>
            </form>
        @else
            <h2>Your cover letter</h2>
            <div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $application->cover_letter }}</div>
            @if($application->portfolio_links)
                <div class="my-7 border-t border-slate-100"></div>
                <h2>Portfolio</h2>
                <div class="mt-3 whitespace-pre-line text-sm text-emerald-700">{{ $application->portfolio_links }}</div>
            @endif
        @endcan
    </section>

    <aside class="space-y-5">
        <section class="panel">
            <div class="space-y-5">
                <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Proposed rate</p><p class="mt-1 text-lg font-semibold text-slate-900">{{ $application->proposed_rate ? '₱'.number_format((float) $application->proposed_rate) : 'Not specified' }}</p></div>
                <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Rate type</p><p class="mt-1 text-sm capitalize text-slate-700">{{ $application->rate_type ?? '—' }}</p></div>
                <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Estimated hours</p><p class="mt-1 text-sm text-slate-700">{{ $application->estimated_hours ?? '—' }}</p></div>
                @if($application->reviewed_at)<div><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Last reviewed</p><p class="mt-1 text-sm text-slate-700">{{ $application->reviewed_at->format('M d, Y') }}</p></div>@endif
            </div>
        </section>

        @can('withdraw', $application)
            <section class="panel border-amber-100">
                <p class="text-sm font-semibold text-amber-800">Withdraw application</p>
                <p class="mt-2 text-sm leading-6 text-slate-500">The client will no longer consider this application. This cannot be reversed.</p>
                <form method="POST" action="{{ route('member.applications.destroy', $application) }}" class="mt-5 !border-0 !bg-transparent !p-0 !shadow-none" onsubmit="return confirm('Withdraw this application?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn border border-amber-200 bg-white text-amber-800 hover:bg-amber-50 focus:ring-amber-500">Withdraw</button>
                </form>
            </section>
        @endcan
    </aside>
</div>
@endsection
