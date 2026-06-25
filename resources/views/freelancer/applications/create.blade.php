@extends('layouts.app')

@section('content')
<a href="{{ route('freelancer.jobs.show', $job) }}" class="mb-6 inline-flex text-sm font-semibold text-slate-500 hover:text-emerald-800">← Back to job</a>
<div class="page-header">
    <div><p class="page-eyebrow">Application</p><h1>Apply for {{ $job->title }}</h1><p class="page-subtitle">Introduce yourself and explain why you are a strong fit.</p></div>
</div>

@if($errors->any())<x-alert type="error">{{ $errors->first() }}</x-alert>@endif

<form method="POST" action="{{ route('freelancer.jobs.apply', $job) }}">
    @csrf
    <div>
        <label for="cover_letter">Cover letter</label>
        <textarea id="cover_letter" name="cover_letter" rows="8" required placeholder="Share your relevant experience, approach, and availability.">{{ old('cover_letter') }}</textarea>
        <p class="mt-2 text-xs text-slate-400">Be specific. Mention similar work, your approach, and when you can start.</p>
        <x-input-error :messages="$errors->get('cover_letter')" class="mt-2" />
    </div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div><label for="proposed_rate">Proposed rate</label><input id="proposed_rate" type="number" min="0" step="0.01" name="proposed_rate" value="{{ old('proposed_rate') }}" placeholder="Optional"></div>
        <div>
            <label for="rate_type">Rate type</label>
            <select id="rate_type" name="rate_type">
                <option value="">Select rate type</option>
                <option value="fixed" @selected(old('rate_type') === 'fixed')>Fixed</option>
                <option value="hourly" @selected(old('rate_type') === 'hourly')>Hourly</option>
            </select>
        </div>
    </div>
    <div><label for="estimated_hours">Estimated hours</label><input id="estimated_hours" type="number" min="1" name="estimated_hours" value="{{ old('estimated_hours') }}"></div>
    <div><label for="portfolio_links">Portfolio links</label><textarea id="portfolio_links" name="portfolio_links" rows="3" placeholder="Add one or more links to relevant work.">{{ old('portfolio_links') }}</textarea></div>
    <div class="flex gap-3">
        <x-primary-button>Submit application</x-primary-button>
        <a href="{{ route('freelancer.jobs.show', $job) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection
