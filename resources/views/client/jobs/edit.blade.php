<!-- Client Jobs Edit -->
@extends('layouts.app')
@section('content')
<h1>Edit Job</h1>
@if($errors->any())
    <x-alert type="error">{{ $errors->first() }}</x-alert>
@endif
<form method="POST" action="{{ route('client.jobs.update', $job) }}">
    @csrf
    @method('PATCH')
    <div style="margin-bottom:1rem;">
        <label>Title</label>
        <input type="text" name="title" value="{{ old('title', $job->title) }}" style="width:100%;">
    </div>
    <div style="margin-bottom:1rem;">
        <label>Status</label>
        <select name="status" style="width:100%;">
            <option value="draft" @if(old('status', $job->status)==='draft') selected @endif>Draft</option>
            <option value="published" @if(old('status', $job->status)==='published') selected @endif>Published</option>
            <option value="paused" @if(old('status', $job->status)==='paused') selected @endif>Paused</option>
            <option value="closed" @if(old('status', $job->status)==='closed') selected @endif>Closed</option>
            <option value="cancelled" @if(old('status', $job->status)==='cancelled') selected @endif>Cancelled</option>
        </select>
    </div>
    <div style="margin-bottom:1rem;">
        <label>Category</label>
        <select name="category_id" style="width:100%;">
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @if(old('category_id', $job->category_id)==$category->id) selected @endif>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit">Update</button>
</form>
<a href="{{ route('client.jobs.index') }}">Back to My Jobs</a>
@endsection 