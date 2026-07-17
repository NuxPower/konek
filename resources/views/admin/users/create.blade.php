@extends('layouts.app')
@section('content')
<a href="{{ route('admin.users.index') }}" class="mb-6 inline-flex text-sm font-semibold text-slate-500 hover:text-emerald-800">← Back to users</a>
<div class="page-header"><div><p class="page-eyebrow">New account</p><h1>Add user</h1><p class="page-subtitle">Create an account and assign its workspace role.</p></div></div>
<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf
    <div><label for="name">Full name</label><input id="name" type="text" name="name" value="{{ old('name') }}" required><x-input-error :messages="$errors->get('name')" class="mt-2" /></div>
    <div><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required><x-input-error :messages="$errors->get('email')" class="mt-2" /></div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div><label for="role">Role</label><select id="role" name="role">@foreach(['admin','member'] as $role)<option value="{{ $role }}" @selected(old('role', 'member') === $role)>{{ ucfirst($role) }}</option>@endforeach</select></div>
        <div><label for="is_active">Status</label><select id="is_active" name="is_active"><option value="1" @selected(old('is_active', '1') === '1')>Active</option><option value="0" @selected(old('is_active') === '0')>Inactive</option></select></div>
    </div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div><label for="password">Password</label><input id="password" type="password" name="password" required></div>
        <div><label for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" required></div>
    </div>
    <x-input-error :messages="$errors->get('password')" class="mb-4" />
    <div class="flex gap-3"><x-primary-button>Create user</x-primary-button><a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a></div>
</form>
@endsection
