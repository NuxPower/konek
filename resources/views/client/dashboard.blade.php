<!-- Client dashboard view -->
@extends('layouts.app')
@section('content')
<h1>Client Dashboard</h1>
<div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
    <x-stats-card label="My Jobs" :value="5" />
    <x-stats-card label="Applications" :value="12" />
</div>
<h2>Recent Jobs</h2>
<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr><th>Title</th><th>Status</th><th>Applications</th></tr>
    </thead>
    <tbody>
        <tr><td>Web Design</td><td>Published</td><td>3</td></tr>
        <tr><td>Logo Creation</td><td>Draft</td><td>0</td></tr>
    </tbody>
</table>
@endsection 