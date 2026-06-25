@extends('layouts.app')

@section('content')
<div class="page-header">
    <div><p class="page-eyebrow">System history</p><h1>Activity logs</h1><p class="page-subtitle">Recent authenticated actions recorded across KONEK.</p></div>
</div>
<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Date</th><th>User</th><th>Action</th><th>IP address</th></tr></thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                <td>{{ $log->causer?->name ?? 'System' }}</td>
                <td>{{ $log->description }}</td>
                <td>{{ data_get($log->properties, 'ip', '—') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="empty-state">No activity has been recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $logs->links() }}
@endsection
