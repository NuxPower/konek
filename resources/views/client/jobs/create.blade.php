@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">New opportunity</p>
        <h1>Post a job</h1>
        <p class="page-subtitle">Describe the work clearly, then publish immediately or save it as a draft.</p>
    </div>
</div>

<form method="POST" action="{{ route('member.posted-jobs.store') }}" class="job-form">
    @csrf
    @include('client.jobs._form')

    <div class="flex flex-wrap items-center gap-3 border-t border-slate-100 pt-6">
        <button type="submit" name="submit_action" value="publish" class="btn btn-primary">Publish job</button>
        <button type="submit" name="submit_action" value="draft" class="btn btn-secondary">Save draft</button>
        <a href="{{ route('member.posted-jobs.index') }}" class="ml-auto text-sm font-semibold text-slate-500 hover:text-slate-800">Cancel</a>
    </div>
</form>
@endsection
