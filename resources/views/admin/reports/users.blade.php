<!-- Admin Users Report -->
@extends('layouts.app')
@section('content')
<h1>Users Report</h1>
<table class="table">
    <thead>
        <tr>
            <th>Name</th><th>Email</th><th>Role</th><th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ ucfirst($user->role) }}</td>
            <td>{{ $user->is_active ? 'Active' : 'Inactive' }}</td>
        </tr>
        @empty
        <tr><td colspan="4">No users found.</td></tr>
        @endforelse
    </tbody>
</table>
@if(method_exists($users, 'links'))
    {{ $users->links() }}
@endif
@endsection 