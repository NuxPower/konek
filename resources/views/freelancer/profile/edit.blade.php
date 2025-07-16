<!-- Freelancer Profile Edit -->
@extends('layouts.app')
@section('content')
<h1>Edit Profile</h1>
@if($errors->any())
    <x-alert type="error">{{ $errors->first() }}</x-alert>
@endif
<form method="POST" action="{{ route('freelancer.profile.update') }}">
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
        <label>Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" style="width:100%;">
    </div>
    <div style="margin-bottom:1rem;">
        <label>Bio</label>
        <textarea name="bio" style="width:100%;">{{ old('bio', $user->bio) }}</textarea>
    </div>
    <button type="submit">Update</button>
</form>
<a href="{{ route('freelancer.profile.show') }}">Back to Profile</a>
@endsection 