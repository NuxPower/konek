@extends('layouts.app')
@section('content')
<a href="{{ route('admin.users.index') }}" class="mb-6 inline-flex text-sm font-semibold text-slate-500 hover:text-emerald-800">← Back to users</a>
<div class="page-header">
    <div><p class="page-eyebrow">User account</p><h1>{{ $user->name }}</h1><p class="page-subtitle">{{ $user->email }}</p></div>
    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Edit user</a>
</div>
<div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_320px]">
    <section class="panel">
        <div class="flex items-center gap-4">
            <div class="grid h-16 w-16 place-items-center rounded-2xl bg-emerald-100 text-xl font-bold text-emerald-800">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div><p class="text-lg font-semibold text-slate-900">{{ $user->name }}</p><p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p></div>
        </div>
        <div class="mt-8 grid gap-3 sm:grid-cols-2">
            <div class="job-fact"><span>Role</span><strong>{{ ucfirst($user->role) }}</strong></div>
            <div class="job-fact"><span>Status</span><strong>{{ $user->is_active ? 'Active' : 'Inactive' }}</strong></div>
            <div class="job-fact"><span>Joined</span><strong>{{ $user->created_at->format('M d, Y') }}</strong></div>
            <div class="job-fact"><span>Last login</span><strong>{{ $user->last_login_at?->format('M d, Y H:i') ?? 'Never' }}</strong></div>
        </div>
    </section>
    @if(!$user->is(auth()->user()))
    <aside class="space-y-5">
        <section class="panel">
            <p class="text-sm font-semibold text-slate-900">Account status</p>
            <p class="mt-2 text-sm text-slate-500">{{ $user->is_active ? 'Deactivate this account to block future logins.' : 'Reactivate this account to restore access.' }}</p>
            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="mt-5 !border-0 !bg-transparent !p-0 !shadow-none">@csrf @method('PATCH')<button class="btn btn-secondary">{{ $user->is_active ? 'Deactivate' : 'Activate' }}</button></form>
        </section>
        <section class="panel border-red-100">
            <p class="text-sm font-semibold text-red-700">Delete account</p>
            <p class="mt-2 text-sm text-slate-500">Permanently removes the user and related records.</p>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-5 !border-0 !bg-transparent !p-0 !shadow-none" onsubmit="return confirm('Permanently delete this user?')">@csrf @method('DELETE')<button class="btn border border-red-200 bg-white text-red-700 hover:bg-red-50">Delete user</button></form>
        </section>
    </aside>
    @endif
</div>
@endsection
