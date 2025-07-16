<!-- Admin Users Show -->
@extends('layouts.app')
@section('content')
<h1>User Details</h1>
<ul>
    <li><strong>Name:</strong> {{ $user->name }}</li>
    <li><strong>Email:</strong> {{ $user->email }}</li>
    <li><strong>Role:</strong> {{ ucfirst($user->role) }}</li>
    <li><strong>Status:</strong> {{ $user->is_active ? 'Active' : 'Inactive' }}</li>
    <li><strong>Created:</strong> {{ $user->created_at->format('Y-m-d') }}</li>
</ul>
<a href="{{ route('admin.users.edit', $user) }}">Edit</a> |
<a href="{{ route('admin.users.index') }}">Back to Users</a>
@endsection 