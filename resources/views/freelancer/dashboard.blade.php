<!-- Freelancer dashboard view -->
@extends('layouts.app')
@section('content')
<h1>Freelancer Dashboard</h1>
<div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
    <x-stats-card label="My Applications" :value="7" />
    <x-stats-card label="Jobs Available" :value="20" />
</div>
<h2>Recent Applications</h2>
<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr><th>Job</th><th>Status</th><th>Date</th></tr>
    </thead>
    <tbody>
        <tr><td>Web Design</td><td>Pending</td><td>2024-06-01</td></tr>
        <tr><td>Logo Creation</td><td>Accepted</td><td>2024-05-28</td></tr>
    </tbody>
</table>
@endsection 