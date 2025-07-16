<!-- Admin dashboard view -->
@extends('layouts.app')
@section('content')
<h1>Admin Dashboard</h1>
<div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
    <x-stats-card label="Total Users" :value="123" />
    <x-stats-card label="Total Jobs" :value="45" />
    <x-stats-card label="Applications" :value="321" />
</div>
<h2>Recent Users</h2>
<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr><th>Name</th><th>Email</th><th>Role</th></tr>
    </thead>
    <tbody>
        <tr><td>Jane Doe</td><td>jane@cmu.edu.ph</td><td>Client</td></tr>
        <tr><td>John Smith</td><td>john@cmu.edu.ph</td><td>Freelancer</td></tr>
    </tbody>
</table>
@endsection 