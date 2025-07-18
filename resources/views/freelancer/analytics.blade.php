@extends('layouts.app')

@section('title', 'Analytics')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Analytics Dashboard</h1>
                <p class="text-gray-600">Track your freelance performance and insights.</p>
            </div>
            <div class="flex space-x-4">
                <select class="border border-gray-300 rounded-md px-3 py-2 bg-white">
                    <option>Last 30 days</option>
                    <option>Last 3 months</option>
                    <option>Last 6 months</option>
                    <option>Last year</option>
                </select>
                <a href="{{ route('freelancer.dashboard') }}" 
                   class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Earnings Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Earnings Overview</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">
                        ₱{{ number_format($analytics['earnings_overview']['total_earnings'], 2) }}
                    </div>
                    <div class="text-sm text-gray-600">Total Earnings</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">
                        {{ $analytics['earnings_overview']['jobs_completed'] }}
                    </div>
                    <div class="text-sm text-gray-600">Jobs Completed</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">
                        ₱{{ number_format($analytics['earnings_overview']['average_job_value'], 2) }}
                    </div>
                    <div class="text-sm text-gray-600">Avg Job Value</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">
                        ₱{{ number_format($analytics['earnings_overview']['hourly_rate'], 2) }}
                    </div>
                    <div class="text-sm text-gray-600">Hourly Rate</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Client Feedback</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Average Rating</span>
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-900 mr-2">
                            {{ number_format($analytics['client_feedback']['average_rating'], 1) }}
                        </span>
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $analytics['client_feedback']['average_rating'])
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Reviews</span>
                    <span class="font-semibold text-gray-900">{{ $analytics['client_feedback']['total_reviews'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Positive Feedback</span>
                    <span class="font-semibold text-green-600">{{ $analytics['client_feedback']['positive_feedback'] }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Repeat Clients</span>
                    <span class="font-semibold text-blue-600">{{ $analytics['client_feedback']['repeat_clients'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Trends Chart -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Application Trends (Last 30 Days)</h2>
        <div class="h-64 flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg">
            @if($analytics['application_trends']->count() > 0)
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <p class="text-gray-500">Chart visualization would be implemented here using Chart.js or similar library</p>
                    <p class="text-sm text-gray-400 mt-2">Total applications in period: {{ $analytics['application_trends']->sum('count') }}</p>
                </div>
            @else
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <p class="text-gray-500">No application data available for the selected period</p>
                    <p class="text-sm text-gray-400 mt-2">Start applying to jobs to see trends here</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Skills Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Skills Performance</h2>
            @if($analytics['skill_performance']->count() > 0)
                <div class="space-y-4">
                    @foreach($analytics['skill_performance'] as $skill)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-medium text-gray-900">{{ $skill['name'] }}</h3>
                                <span class="text-sm text-gray-500">{{ ucfirst($skill['proficiency']) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm text-gray-600">
                                <span>Experience: {{ $skill['experience'] }} years</span>
                                <div class="flex items-center">
                                    @php
                                        $proficiencyLevels = ['beginner' => 1, 'intermediate' => 2, 'advanced' => 3, 'expert' => 4];
                                        $level = $proficiencyLevels[$skill['proficiency']] ?? 1;
                                    @endphp
                                    @for($i = 1; $i <= 4; $i++)
                                        <div class="w-2 h-2 rounded-full mr-1 {{ $i <= $level ? 'bg-blue-500' : 'bg-gray-300' }}"></div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    <h3 class="text-sm font-medium text-gray-900 mb-1">No skills added</h3>
                    <p class="text-sm text-gray-500 mb-4">Add skills to your profile to track performance</p>
                    <a href="{{ route('freelancer.profile.edit') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm">
                        Add Skills
                    </a>
                </div>
            @endif
        </div>

        <!-- Performance Metrics -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Performance Metrics</h2>
            <div class="space-y-6">
                <!-- Success Rate -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-600">Success Rate</span>
                        <span class="text-sm font-medium text-gray-900">85%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: 85%"></div>
                    </div>
                </div>

                <!-- Response Time -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-600">Response Time</span>
                        <span class="text-sm font-medium text-gray-900">< 2 hours</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: 90%"></div>
                    </div>
                </div>

                <!-- Project Completion Rate -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-600">Project Completion</span>
                        <span class="text-sm font-medium text-gray-900">92%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: 92%"></div>
                    </div>
                </div>

                <!-- Client Satisfaction -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-600">Client Satisfaction</span>
                        <span class="text-sm font-medium text-gray-900">4.8/5.0</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: 96%"></div>
                    </div>
                </div>
            </div>

            <!-- Action Items -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h3 class="text-sm font-medium text-blue-900 mb-2">Improvement Tips</h3>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Complete your profile to get better job matches</li>
                    <li>• Add portfolio items to showcase your work</li>
                    <li>• Respond to messages within 1 hour for better ratings</li>
                    <li>• Update your skills regularly to stay competitive</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Monthly Goals (Optional) -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Monthly Goals</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-4 border border-gray-200 rounded-lg">
                <div class="text-2xl font-bold text-blue-600 mb-1">8/10</div>
                <div class="text-sm text-gray-600 mb-2">Applications Goal</div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: 80%"></div>
                </div>
            </div>
            <div class="text-center p-4 border border-gray-200 rounded-lg">
                <div class="text-2xl font-bold text-green-600 mb-1">₱15,000</div>
                <div class="text-sm text-gray-600 mb-2">Earnings Goal</div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: 60%"></div>
                </div>
            </div>
            <div class="text-center p-4 border border-gray-200 rounded-lg">
                <div class="text-2xl font-bold text-purple-600 mb-1">2/3</div>
                <div class="text-sm text-gray-600 mb-2">Projects Goal</div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-500 h-2 rounded-full" style="width: 66%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // You can add Chart.js or other charting libraries here for better visualizations
    console.log('Analytics page loaded');
</script>
@endpush