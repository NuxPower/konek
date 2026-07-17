@extends('layouts.app')
@section('content')
<a href="{{ route('admin.reports.index') }}" class="mb-6 inline-flex text-sm font-semibold text-slate-500 hover:text-emerald-800">← Back to reports</a>
<div class="page-header">
    <div><p class="page-eyebrow">Report</p><h1>Users</h1><p class="page-subtitle">Account roles, status, and registration data.</p></div>
    <form method="POST" action="{{ route('admin.reports.export') }}" class="!m-0 !max-w-none !border-0 !bg-transparent !p-0 !shadow-none">
        @csrf
        <input type="hidden" name="report" value="users">
        @foreach($filters as $key => $value)<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endforeach
        <button class="btn btn-primary">Export CSV</button>
    </form>
</div>
<form method="GET" class="job-filter-panel">
    <select name="role"><option value="">All roles</option>@foreach(['admin','member'] as $role)<option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>@endforeach</select>
    <select name="active"><option value="">Any status</option><option value="1" @selected(request('active') === '1')>Active</option><option value="0" @selected(request('active') === '0')>Inactive</option></select>
    <input type="date" name="from" value="{{ request('from') }}" aria-label="From date">
    <input type="date" name="to" value="{{ request('to') }}" aria-label="To date">
    <button class="btn btn-primary">Apply filters</button>
    <a href="{{ route('admin.reports.users') }}" class="btn btn-secondary">Reset</a>
</form>
<p class="mb-4 text-sm text-slate-500"><strong class="text-slate-800">{{ $users->total() }}</strong> matching users</p>
<div class="table-wrap"><table class="table">
    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th></tr></thead>
    <tbody>
        @forelse($users as $user)
        <tr><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td><span class="badge badge-muted">{{ ucfirst($user->role) }}</span></td><td><span class="badge {{ $user->is_active ? 'badge-success' : 'badge-muted' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></td><td>{{ $user->created_at->format('M d, Y') }}</td></tr>
        @empty<tr><td colspan="5" class="empty-state">No users match these filters.</td></tr>@endforelse
    </tbody>
</table></div>
<div class="mt-6">{{ $users->links() }}</div>
@endsection
