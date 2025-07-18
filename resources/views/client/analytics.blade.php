<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .analytics-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .analytics-card {
            border-left: 4px solid #007bff;
            transition: transform 0.3s ease;
        }
        .analytics-card:hover {
            transform: translateY(-5px);
        }
        .chart-container {
            position: relative;
            height: 400px;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Analytics Header -->
    <div class="analytics-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Analytics Dashboard
                    </h1>
                    <p class="mb-0 opacity-75">Insights into your hiring performance</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="btn-group">
                        <a href="{{ route('client.dashboard') }}" class="btn btn-outline-light">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a href="{{ route('client.profile.show') }}" class="btn btn-outline-light">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card analytics-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Spending</h6>
                                <h3 class="text-primary mb-0">${{ number_format($analytics['spending_overview']['total_spent'], 2) }}</h3>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card analytics-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Average Job Budget</h6>
                                <h3 class="text-success mb-0">${{ number_format($analytics['spending_overview']['average_job_budget'], 2) }}</h3>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-calculator fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card analytics-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Monthly Spending</h6>
                                <h3 class="text-warning mb-0">${{ number_format($analytics['spending_overview']['monthly_spending'], 2) }}</h3>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-calendar-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            Job Performance
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="jobPerformanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Application Trends (Last 30 Days)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="applicationTrendsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Skill Demand -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Top Skills in Demand
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($analytics['skill_demand']->count() > 0)
                            <div class="row">
                                @foreach($analytics['skill_demand'] as $skill)
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">{{ $skill->name }}</span>
                                            <span class="badge bg-primary">{{ $skill->count }} jobs</span>
                                        </div>
                                        <div class="progress mt-2" style="height: 8px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ ($skill->count / $analytics['skill_demand']->first()->count) * 100 }}%">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No skill data available yet.</p>
                                <p class="text-muted">Post jobs with skill requirements to see analytics.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Job Performance Chart
        const jobPerformanceCtx = document.getElementById('jobPerformanceChart').getContext('2d');
        const jobPerformanceData = @json($analytics['job_performance']);
        
        new Chart(jobPerformanceCtx, {
            type: 'doughnut',
            data: {
                labels: jobPerformanceData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
                datasets: [{
                    data: jobPerformanceData.map(item => item.count),
                    backgroundColor: [
                        '#28a745', // Active/Open - Green
                        '#007bff', // Completed - Blue
                        '#ffc107', // Pending - Yellow
                        '#dc3545', // Cancelled - Red
                        '#6c757d'  // Other - Gray
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Application Trends Chart
        const applicationTrendsCtx = document.getElementById('applicationTrendsChart').getContext('2d');
        const applicationTrendsData = @json($analytics['application_trends']);
        
        new Chart(applicationTrendsCtx, {
            type: 'line',
            data: {
                labels: applicationTrendsData.map(item => new Date(item.date).toLocaleDateString()),
                datasets: [{
                    label: 'Applications',
                    data: applicationTrendsData.map(item => item.count),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>