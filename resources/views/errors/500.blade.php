@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-red-800">Server Error (500)</h1>
                    <p class="text-red-600">Something went wrong on our end. We're working to fix it.</p>
                </div>
            </div>
            
            @if(config('app.debug') && isset($error))
                <div class="mt-6 p-4 bg-red-100 border border-red-300 rounded">
                    <h3 class="font-bold text-red-800 mb-2">Debug Information:</h3>
                    <pre class="text-sm text-red-700 whitespace-pre-wrap">{{ $error }}</pre>
                </div>
            @endif
            
            <div class="mt-6 flex space-x-4">
                <a href="{{ route('client.dashboard') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-home mr-2"></i>Back to Dashboard
                </a>
                <button onclick="window.location.reload()" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-refresh mr-2"></i>Try Again
                </button>
            </div>
        </div>
    </div>
</div>
@endsection