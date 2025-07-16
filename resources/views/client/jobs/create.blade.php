<!-- Client Jobs Create -->
@extends('layouts.app')
@section('content')
<h1>Add Job</h1>
<form method="POST" action="#">
    @csrf
    <div style="margin-bottom:1rem;">
        <label>Title</label>
        <input type="text" name="title" style="width:100%;">
    </div>
    <div style="margin-bottom:1rem;">
        <label>Status</label>
        <select name="status" style="width:100%;">
            <option value="published">Published</option>
            <option value="draft">Draft</option>
        </select>
    </div>
    <button type="submit">Create</button>
</form>
<a href="{{ route('client.jobs.index') }}">Back to My Jobs</a>
@endsection 