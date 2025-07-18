<!-- Client dashboard view -->
@extends('layouts.app')
@section('content')
<h1>Client Dashboard</h1>

{{-- Add this to your client dashboard view --}}

@if(isset($pendingCompletions) && $pendingCompletions > 0)
    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-orange-800">
                    {{ $pendingCompletions }} Job{{ $pendingCompletions > 1 ? 's' : '' }} Pending Review
                </h3>
                <p class="text-orange-700">
                    You have {{ $pendingCompletions }} completed job{{ $pendingCompletions > 1 ? 's' : '' }} waiting for your review and approval.
                </p>
            </div>
            <div class="ml-4">
                <a href="{{ route('client.applications.index', ['status' => 'pending_completion']) }}" 
                   class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 transition-colors">
                    Review Now
                </a>
            </div>
        </div>
    </div>
@endif

{{-- Stats Card for Dashboard --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Existing stats cards... --}}
    
    {{-- Pending Completions Card --}}
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-800">Pending Reviews</h3>
                <p class="text-2xl font-bold text-orange-600">{{ $pendingCompletions ?? 0 }}</p>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('client.applications.index', ['status' => 'pending_completion']) }}" 
               class="text-orange-600 hover:text-orange-800 text-sm font-medium">
                View all →
            </a>
        </div>
    </div>
</div>
@endsection 