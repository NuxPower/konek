@extends('layouts.app')
@section('content')
<div class="page-header">
    <div><p class="page-eyebrow">People</p><h1>Users</h1><p class="page-subtitle">Manage access and roles across the KONEK community.</p></div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add user</a>
</div>
@if(session('status'))
    <x-alert type="success">{{ session('status') }}</x-alert>
@endif
<div class="table-wrap">
<table class="table">
    <thead>
        <tr>
            <th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td><span class="badge badge-muted">{{ ucfirst($user->role) }}</span></td>
            <td><span class="badge {{ $user->is_active ? 'badge-success' : 'badge-muted' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td>
                <a href="{{ route('admin.users.show', $user) }}">Show</a> |
                <a href="{{ route('admin.users.edit', $user) }}">Edit</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="5">No users found.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
{{ $users->links() }}
@endsection
