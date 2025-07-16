<!-- Admin Activity Logs Index -->
@extends('layouts.app')
@section('content')
<h1>Activity Logs</h1>
<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr><th>Date</th><th>User</th><th>Action</th></tr>
    </thead>
    <tbody>
        @forelse($logs as $log)
        <tr>
            <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
            <td>{{ $log->causer ? $log->causer->name : 'System' }}</td>
            <td>{{ $log->description }}</td>
        </tr>
        @empty
        <tr><td colspan="3">No activity logs found.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $logs->links() }}
@endsection 