<!-- Admin dashboard view -->
@extends('layouts.app')
@section('content')
<h1 style="margin-bottom: 1.5rem; font-size: 2rem; font-weight: bold; color: #1e293b;">Admin Dashboard</h1>
<div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
    <x-stats-card label="Total Users" :value="$totalUsers" icon="<i class='fas fa-users'></i>" color="#2563eb" />
    <x-stats-card label="Admins" :value="$totalAdmins" icon="<i class='fas fa-user-shield'></i>" color="#f59e42" />
    <x-stats-card label="Clients" :value="$totalClients" icon="<i class='fas fa-user-tie'></i>" color="#10b981" />
    <x-stats-card label="Freelancers" :value="$totalFreelancers" icon="<i class='fas fa-user-graduate'></i>" color="#6366f1" />
    <x-stats-card label="Total Jobs" :value="$totalJobs" icon="<i class='fas fa-briefcase'></i>" color="#f43f5e" />
    <x-stats-card label="Applications" :value="$totalApplications" icon="<i class='fas fa-file-alt'></i>" color="#fbbf24" />
</div>

<div style="display: flex; gap: 2rem; flex-wrap: wrap; margin-bottom: 2rem;">
    <div style="flex: 1 1 350px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #e5e7eb; padding: 1rem;">
        <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem;">User Registration Trend</h3>
        <canvas id="userTrendChart" height="120"></canvas>
    </div>
    <div style="flex: 1 1 350px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #e5e7eb; padding: 1rem;">
        <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem;">Job Posting Trend</h3>
        <canvas id="jobTrendChart" height="120"></canvas>
    </div>
</div>

<h2 style="margin-top:2rem; font-size:1.2rem; font-weight:600;">Recent Users</h2>
<table style="width:100%; border-collapse:collapse; margin-bottom:2rem; background:#fff; border-radius:8px; box-shadow:0 2px 8px #e5e7eb;">
    <thead style="background:#f1f5f9;">
        <tr><th>Name</th><th>Email</th><th>Role</th></tr>
    </thead>
    <tbody>
        @foreach($recentUsers as $user)
        <tr><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ ucfirst($user->role) }}</td></tr>
        @endforeach
    </tbody>
</table>

<h2 style="font-size:1.2rem; font-weight:600;">Recent Jobs</h2>
<table style="width:100%; border-collapse:collapse; margin-bottom:2rem; background:#fff; border-radius:8px; box-shadow:0 2px 8px #e5e7eb;">
    <thead style="background:#f1f5f9;">
        <tr><th>Title</th><th>Client</th><th>Status</th></tr>
    </thead>
    <tbody>
        @foreach($recentJobs as $job)
        <tr><td>{{ $job->title }}</td><td>{{ $job->client->name ?? '-' }}</td><td>{{ ucfirst($job->status) }}</td></tr>
        @endforeach
    </tbody>
</table>

<h2 style="font-size:1.2rem; font-weight:600;">Recent Applications</h2>
<table style="width:100%; border-collapse:collapse; margin-bottom:2rem; background:#fff; border-radius:8px; box-shadow:0 2px 8px #e5e7eb;">
    <thead style="background:#f1f5f9;">
        <tr><th>Job</th><th>Freelancer</th><th>Status</th></tr>
    </thead>
    <tbody>
        @foreach($recentApplications as $app)
        <tr><td>{{ $app->job->title ?? '-' }}</td><td>{{ $app->freelancer->name ?? '-' }}</td><td>{{ ucfirst($app->status) }}</td></tr>
        @endforeach
    </tbody>
</table>

<h2 style="font-size:1.2rem; font-weight:600;">Recent Activity Log</h2>
<table style="width:100%; border-collapse:collapse; background:#fff; border-radius:8px; box-shadow:0 2px 8px #e5e7eb;">
    <thead style="background:#f1f5f9;">
        <tr><th>Date</th><th>User</th><th>Description</th></tr>
    </thead>
    <tbody>
        @foreach($recentActivities as $log)
        <tr><td>{{ $log->created_at->format('Y-m-d H:i') }}</td><td>{{ $log->causer->name ?? '-' }}</td><td>{{ $log->description }}</td></tr>
        @endforeach
    </tbody>
</table>

{{-- Chart.js scripts --}}
@push('scripts')
<script>
const userTrendCtx = document.getElementById('userTrendChart').getContext('2d');
const jobTrendCtx = document.getElementById('jobTrendChart').getContext('2d');
new Chart(userTrendCtx, {
    type: 'line',
    data: {
        labels: @json($months),
        datasets: [{
            label: 'User Registrations',
            data: @json($userTrendData),
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,0.1)',
            tension: 0.4,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
new Chart(jobTrendCtx, {
    type: 'bar',
    data: {
        labels: @json($months),
        datasets: [{
            label: 'Jobs Posted',
            data: @json($jobTrendData),
            backgroundColor: '#f43f5e',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endpush
@endsection 