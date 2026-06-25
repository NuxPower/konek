@extends('layouts.app')
@section('content')
<a href="{{ route('admin.jobs.show', $job) }}" class="mb-6 inline-flex text-sm font-semibold text-slate-500 hover:text-emerald-800">← Back to job</a>
<div class="page-header"><div><p class="page-eyebrow">Platform moderation</p><h1>Edit job</h1><p class="page-subtitle">Correct job information or update its platform status.</p></div></div>
<form method="POST" action="{{ route('admin.jobs.update', $job) }}">@csrf @method('PATCH')
    <div><label for="title">Title</label><input id="title" name="title" value="{{ old('title',$job->title) }}" required></div>
    <div><label for="description">Description</label><textarea id="description" rows="7" name="description" required>{{ old('description',$job->description) }}</textarea></div>
    <div><label for="requirements">Requirements</label><textarea id="requirements" rows="6" name="requirements" required>{{ old('requirements',$job->requirements) }}</textarea></div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div><label for="client_id">Client</label><select id="client_id" name="client_id">@foreach($clients as $client)<option value="{{ $client->id }}" @selected((int) old('client_id',$job->client_id)===$client->id)>{{ $client->name }}</option>@endforeach</select></div>
        <div><label for="category_id">Category</label><select id="category_id" name="category_id">@foreach($categories as $category)<option value="{{ $category->id }}" @selected((int) old('category_id',$job->category_id)===$category->id)>{{ $category->name }}</option>@endforeach</select></div>
    </div>
    <div class="grid gap-5 sm:grid-cols-3">
        <div><label for="type">Work type</label><select id="type" name="type">@foreach(['full-time','part-time','contract','internship'] as $type)<option value="{{ $type }}" @selected(old('type',$job->type)===$type)>{{ ucfirst(str_replace('-',' ',$type)) }}</option>@endforeach</select></div>
        <div><label for="experience_level">Experience</label><select id="experience_level" name="experience_level">@foreach(['entry','intermediate','expert'] as $level)<option value="{{ $level }}" @selected(old('experience_level',$job->experience_level)===$level)>{{ ucfirst($level) }}</option>@endforeach</select></div>
        <div><label for="budget_type">Budget type</label><select id="budget_type" name="budget_type">@foreach(['fixed','hourly','negotiable'] as $type)<option value="{{ $type }}" @selected(old('budget_type',$job->budget_type)===$type)>{{ ucfirst($type) }}</option>@endforeach</select></div>
    </div>
    <div class="grid gap-5 sm:grid-cols-2"><div><label for="budget_min">Minimum budget</label><input id="budget_min" type="number" step="0.01" min="0" name="budget_min" value="{{ old('budget_min',$job->budget_min) }}"></div><div><label for="budget_max">Maximum budget</label><input id="budget_max" type="number" step="0.01" min="0" name="budget_max" value="{{ old('budget_max',$job->budget_max) }}"></div></div>
    <div class="grid gap-5 sm:grid-cols-3">
        <div><label for="deadline">Deadline</label><input id="deadline" type="date" name="deadline" value="{{ old('deadline',$job->deadline?->format('Y-m-d')) }}"></div>
        <div><label for="max_applications">Application limit</label><input id="max_applications" type="number" min="1" name="max_applications" value="{{ old('max_applications',$job->max_applications) }}"></div>
        <div><label for="status">Status</label><select id="status" name="status">@foreach(['draft','published','paused','closed','cancelled'] as $status)<option value="{{ $status }}" @selected(old('status',$job->status)===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
    </div>
    @if($errors->any())<x-alert type="error">{{ $errors->first() }}</x-alert>@endif
    <div class="flex gap-3"><x-primary-button>Save changes</x-primary-button><a href="{{ route('admin.jobs.show',$job) }}" class="btn btn-secondary">Cancel</a></div>
</form>
@endsection
