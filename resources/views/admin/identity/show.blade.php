@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">{{ $verification->user->name }}</h1>
                    <p class="mt-1 text-sm text-slate-600">{{ $verification->user->email }}</p>
                </div>
                <span class="rounded bg-slate-100 px-3 py-1 text-sm font-semibold capitalize text-slate-700">
                    {{ $verification->status }}
                </span>
            </div>

            <dl class="mt-6 grid gap-6 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Entered school ID</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $verification->school_id ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Scanned ID number</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $verification->extracted_school_id ?? 'Not detected' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">OCR confidence</dt>
                    <dd class="mt-1 text-sm text-slate-900">
                        {{ $verification->ocr_confidence === null ? 'Not available' : $verification->ocr_confidence.'%' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Biometric score</dt>
                    <dd class="mt-1 text-sm text-slate-900">
                        {{ $verification->biometric_score === null ? 'Not available' : $verification->biometric_score.'%' }}
                    </dd>
                </div>
            </dl>

            @if (! empty($verification->warnings))
                <div class="mt-6 rounded border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
                    <p class="font-semibold">Analyzer warnings</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($verification->warnings as $warning)
                            <li>{{ $warning }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Private evidence</h2>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('admin.identity.evidence', [$verification, 'id']) }}" target="_blank" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Open ID image
                </a>
                <a href="{{ route('admin.identity.evidence', [$verification, 'selfie']) }}" target="_blank" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Open selfie
                </a>
            </div>
        </div>

        @if ($verification->status === \App\Models\IdentityVerification::STATUS_PENDING)
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <form id="reject-proof-form" method="POST" action="{{ route('admin.identity.reject', $verification) }}" class="space-y-3">
                    @csrf
                    <input
                        name="rejection_reason"
                        type="text"
                        value="{{ old('rejection_reason') }}"
                        placeholder="Reason for rejection"
                        class="h-10 w-full rounded border border-slate-300 px-4 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500"
                    >
                    @error('rejection_reason')
                        <p class="text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </form>

                <div class="mt-3 flex justify-end gap-3">
                    <form method="POST" action="{{ route('admin.identity.approve', $verification) }}">
                        @csrf
                        <button type="submit" class="inline-flex h-10 items-center rounded bg-emerald-700 px-4 text-sm font-semibold text-white transition hover:bg-emerald-800">
                            Approve proof
                        </button>
                    </form>

                    <button type="submit" form="reject-proof-form" class="inline-flex h-10 items-center justify-center rounded bg-red-700 px-4 text-sm font-semibold text-white transition hover:bg-red-800">
                        Reject proof
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection
