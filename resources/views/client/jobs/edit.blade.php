@extends('layouts.app')

@section('content')
<a href="{{ route('member.posted-jobs.show', $job) }}" class="mb-6 inline-flex text-sm font-semibold text-slate-500 hover:text-emerald-800">← Back to job</a>
<div class="page-header">
    <div>
        <p class="page-eyebrow">Edit opportunity</p>
        <h1>{{ $job->title }}</h1>
        <p class="page-subtitle">Update the job details. Status changes are managed from the job page.</p>
    </div>
</div>

<form method="POST" action="{{ route('member.posted-jobs.update', $job) }}" class="job-form">
    @csrf
    @method('PATCH')
    @include('client.jobs._form')

    <div class="flex flex-wrap items-center gap-3 border-t border-slate-100 pt-6">
        <x-primary-button>Save changes</x-primary-button>
        <a href="{{ route('member.posted-jobs.show', $job) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection
