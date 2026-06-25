@extends('layouts.app')
@section('content')
<a href="{{ route('freelancer.profile.show') }}" class="mb-6 inline-flex text-sm font-semibold text-slate-500 hover:text-emerald-800">← Back to profile</a>
<div class="page-header"><div><p class="page-eyebrow">Profile settings</p><h1>Edit talent profile</h1><p class="page-subtitle">Help clients understand your background and availability.</p></div></div>
<form method="POST" action="{{ route('freelancer.profile.update') }}">@csrf @method('PATCH')
    <div><label for="name">Name</label><input id="name" name="name" value="{{ old('name',$user->name) }}" required></div>
    <div class="grid gap-5 sm:grid-cols-2"><div><label for="phone">Phone</label><input id="phone" name="phone" value="{{ old('phone',$user->phone) }}"></div><div><label for="department">Department</label><input id="department" name="department" value="{{ old('department',$user->department) }}"></div></div>
    <div class="grid gap-5 sm:grid-cols-2"><div><label for="student_id">Student ID</label><input id="student_id" name="student_id" value="{{ old('student_id',$user->student_id) }}"></div><div><label for="year_level">Year level</label><input id="year_level" type="number" min="1" max="6" name="year_level" value="{{ old('year_level',$user->year_level) }}"></div></div>
    <div><label for="bio">Professional bio</label><textarea id="bio" rows="7" name="bio" placeholder="Share your skills, interests, and the kind of work you do.">{{ old('bio',$user->bio) }}</textarea></div>
    @if($errors->any())<x-alert type="error">{{ $errors->first() }}</x-alert>@endif
    <x-primary-button>Save profile</x-primary-button>
</form>
@endsection
