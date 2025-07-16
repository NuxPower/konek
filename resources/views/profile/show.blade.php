<!-- User Profile Show -->
@extends('layouts.app')
@section('content')
<h1>My Profile</h1>
<ul>
    <li><strong>Name:</strong> {{ $user->name }}</li>
    <li><strong>Email:</strong> {{ $user->email }}</li>
    <li><strong>Role:</strong> {{ ucfirst($user->role) }}</li>
    <li><strong>Phone:</strong> {{ $user->phone ?? '-' }}</li>
    <li><strong>Bio:</strong> {{ $user->bio ?? '-' }}</li>
    <li><strong>Created:</strong> {{ $user->created_at->format('Y-m-d') }}</li>
</ul>
<a href="{{ route('profile.edit') }}">Edit Profile</a>
@endsection 