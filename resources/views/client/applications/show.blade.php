@extends('layouts.app')

@section('content')
<a href="{{ route('client.applications.index') }}" class="mb-6 inline-flex text-sm font-semibold text-slate-500 hover:text-emerald-800">← Back to applications</a>
<div class="page-header">
    <div>
        <p class="page-eyebrow">Candidate application</p>
        <h1>{{ $application->freelancer->name ?? 'Applicant' }}</h1>
        <p class="page-subtitle">Applied for {{ $application->job->title ?? 'an opportunity' }} on {{ $application->created_at->format('M d, Y') }}.</p>
    </div>
    <span class="badge badge-application-{{ $application->status }}">{{ ucfirst($application->status) }}</span>
</div>

<div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_330px]">
    <section class="panel">
        <h2>Cover letter</h2>
        <div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $application->cover_letter }}</div>
        @if($application->portfolio_links)
            <div class="my-7 border-t border-slate-100"></div>
            <h2>Portfolio</h2>
            <div class="mt-3 whitespace-pre-line text-sm text-emerald-700">{{ $application->portfolio_links }}</div>
        @endif
        <div class="my-7 border-t border-slate-100"></div>
        <form method="POST" action="{{ route('client.applications.notes', $application) }}" class="!m-0 !max-w-none !border-0 !bg-transparent !p-0 !shadow-none">
            @csrf
            @method('PATCH')
            <label for="client_notes">Private review notes</label>
            <textarea id="client_notes" name="client_notes" rows="4" placeholder="Add notes visible only to your team.">{{ old('client_notes', $application->client_notes) }}</textarea>
            <button class="btn btn-secondary mt-3">Save notes</button>
        </form>
    </section>

    <aside class="space-y-5">
        <section class="panel">
            <div class="space-y-5">
                <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Proposed rate</p><p class="mt-1 text-lg font-semibold text-slate-900">{{ $application->proposed_rate ? '₱'.number_format((float) $application->proposed_rate) : 'Not specified' }}</p></div>
                <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Rate type</p><p class="mt-1 text-sm capitalize text-slate-700">{{ $application->rate_type ?? '—' }}</p></div>
                <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Estimated hours</p><p class="mt-1 text-sm text-slate-700">{{ $application->estimated_hours ?? '—' }}</p></div>
            </div>
        </section>

        @can('review', $application)
            @if(!in_array($application->status, ['accepted', 'rejected', 'withdrawn'], true))
            <section class="panel">
                <p class="text-sm font-semibold text-slate-900">Move application</p>
                <p class="mt-2 text-sm leading-6 text-slate-500">Choose the next stage for this candidate.</p>
                <div class="mt-5 grid gap-2">
                    @foreach([
                        'reviewing' => 'Mark as reviewing',
                        'shortlisted' => 'Shortlist candidate',
                        'accepted' => 'Accept application',
                        'rejected' => 'Reject application',
                    ] as $status => $label)
                        @if($status !== $application->status)
                        <form method="POST" action="{{ route('client.applications.status', $application) }}" class="!m-0 !border-0 !bg-transparent !p-0 !shadow-none">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $status }}">
                            <button class="btn {{ $status === 'accepted' ? 'btn-primary' : 'btn-secondary' }} w-full">{{ $label }}</button>
                        </form>
                        @endif
                    @endforeach
                </div>
            </section>
            @endif
        @endcan
    </aside>
</div>
@endsection
