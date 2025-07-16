<!-- Admin Users Index -->
@extends('layouts.app')
@section('content')
<h1>Users</h1>
<a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-2">Add User</a>
@if(session('status'))
    <x-alert type="success">{{ session('status') }}</x-alert>
@endif
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
            <td>{{ ucfirst($user->role) }}</td>
            <td>{{ $user->is_active ? 'Active' : 'Inactive' }}</td>
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
{{ $users->links() }}
@endsection 