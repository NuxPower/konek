@extends('layouts.app')
@section('content')
<a href="{{ route('admin.users.show', $user) }}" class="mb-6 inline-flex text-sm font-semibold text-slate-500 hover:text-emerald-800">← Back to user</a>
<div class="page-header"><div><p class="page-eyebrow">Account management</p><h1>Edit {{ $user->name }}</h1><p class="page-subtitle">Update account identity, role, status, or password.</p></div></div>
<form method="POST" action="{{ route('admin.users.update', $user) }}">
    @csrf @method('PATCH')
    <div><label for="name">Full name</label><input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required></div>
    <div><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required></div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div><label for="role">Role</label><select id="role" name="role">@foreach(['admin','member'] as $role)<option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ ucfirst($role) }}</option>@endforeach</select></div>
        <div><label for="is_active">Status</label><select id="is_active" name="is_active"><option value="1" @selected((string) old('is_active', (int) $user->is_active) === '1')>Active</option><option value="0" @selected((string) old('is_active', (int) $user->is_active) === '0')>Inactive</option></select></div>
    </div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div><label for="password">New password</label><input id="password" type="password" name="password" placeholder="Leave blank to keep current password"></div>
        <div><label for="password_confirmation">Confirm new password</label><input id="password_confirmation" type="password" name="password_confirmation"></div>
    </div>
    @if($errors->any())<x-alert type="error">{{ $errors->first() }}</x-alert>@endif
    <div class="flex gap-3"><x-primary-button>Save changes</x-primary-button><a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">Cancel</a></div>
</form>
@endsection
