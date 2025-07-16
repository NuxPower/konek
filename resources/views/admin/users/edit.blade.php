<!-- Admin Users Edit -->
@extends('layouts.app')
@section('content')
<h1>Edit User</h1>
@if($errors->any())
    <x-alert type="error">{{ $errors->first() }}</x-alert>
@endif
<form method="POST" action="{{ route('admin.users.update', $user) }}">
    @csrf
    @method('PATCH')
    <div style="margin-bottom:1rem;">
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" style="width:100%;">
    </div>
    <div style="margin-bottom:1rem;">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" style="width:100%;">
    </div>
    <div style="margin-bottom:1rem;">
        <label>Role</label>
        <select name="role" style="width:100%;">
            <option value="admin" @if(old('role', $user->role)==='admin') selected @endif>Admin</option>
            <option value="client" @if(old('role', $user->role)==='client') selected @endif>Client</option>
            <option value="freelancer" @if(old('role', $user->role)==='freelancer') selected @endif>Freelancer</option>
        </select>
    </div>
    <div style="margin-bottom:1rem;">
        <label>Status</label>
        <select name="is_active" style="width:100%;">
            <option value="1" @if(old('is_active', $user->is_active)) selected @endif>Active</option>
            <option value="0" @if(!old('is_active', $user->is_active)) selected @endif>Inactive</option>
        </select>
    </div>
    <button type="submit">Update</button>
</form>
<a href="{{ route('admin.users.index') }}">Back to Users</a>
@endsection 