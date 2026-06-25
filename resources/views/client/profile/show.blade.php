@extends('layouts.app')
@section('content')
<div class="page-header"><div><p class="page-eyebrow">Client profile</p><h1>{{ $user->name }}</h1><p class="page-subtitle">Your public identity and contact information.</p></div><a href="{{ route('client.profile.edit') }}" class="btn btn-primary">Edit profile</a></div>
<section class="panel max-w-3xl">
    <div class="flex items-center gap-4"><div class="grid h-16 w-16 place-items-center rounded-2xl bg-emerald-100 text-xl font-bold text-emerald-800">{{ strtoupper(substr($user->name,0,1)) }}</div><div><p class="text-lg font-semibold text-slate-900">{{ $user->name }}</p><p class="text-sm text-slate-500">{{ $user->email }}</p></div></div>
    <div class="mt-8 grid gap-3 sm:grid-cols-2">
        <div class="job-fact"><span>Phone</span><strong>{{ $user->phone ?? 'Not provided' }}</strong></div>
        <div class="job-fact"><span>Member since</span><strong>{{ $user->created_at->format('M Y') }}</strong></div>
        <div class="job-fact"><span>Department</span><strong>{{ $user->department ?? 'Not provided' }}</strong></div>
        <div class="job-fact"><span>Student ID</span><strong>{{ $user->student_id ?? 'Not provided' }}</strong></div>
    </div>
</section>
@endsection
