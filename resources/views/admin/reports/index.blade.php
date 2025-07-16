<!-- Admin Reports Index -->
@extends('layouts.app')
@section('content')
<h1>Reports</h1>
<ul>
    @foreach($reportLinks as $report)
        <li><a href="{{ route($report['route']) }}">{{ $report['label'] }}</a></li>
    @endforeach
</ul>
<form method="POST" action="{{ route('admin.reports.export') }}">
    @csrf
    <button type="submit">Export All Reports</button>
</form>
@endsection 