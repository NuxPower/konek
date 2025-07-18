<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .stat-card {
            border-left: 4px solid #007bff;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Client Dashboard
                    </h1>
                    <p class="mb-0 opacity-75">Welcome back, {{ Auth::user()->name }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="btn-group">
                        <a href="{{ route('client.profile.show') }}" class="btn btn-outline-light">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                        <a href="{{ route('client.analytics') }}" class="btn btn-outline-light">
                            <i class="fas fa-chart-bar me-2"></i>Analytics
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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card card-hover h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Jobs</h6>
                                <div class="stat-number">{{ $stats['total_jobs'] }}</div>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-briefcase fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card card-hover h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Active Jobs</h6>
                                <div class="stat-number">{{ $stats['active_jobs'] }}</div>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-play-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card card-hover h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Applications</h6>
                                <div class="stat-number">{{ $stats['total_applications'] }}</div>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-file-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card card-hover h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Spent</h6>
                                <div class="stat-number">${{ number_format($stats['total_spent'], 2) }}</div>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-rocket me-2"></i>
                            Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <button class="btn btn-outline-primary btn-sm w-100">
                                    <i class="fas fa-plus me-2"></i>Post New Job
                                </button>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button class="btn btn-outline-success btn-sm w-100">
                                    <i class="fas fa-eye me-2"></i>View Applications
                                </button>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button class="btn btn-outline-warning btn-sm w-100">
                                    <i class="fas fa-search me-2"></i>Find Freelancers
                                </button>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button class="btn btn-outline-info btn-sm w-100">
                                    <i class="fas fa-cog me-2"></i>Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Jobs and Applications -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-briefcase me-2"></i>
                            Recent Jobs
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($recentJobs->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentJobs->take(5) as $job)
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $application->freelancer->user->name ?? 'Unknown Freelancer' }}</h6>
                                            <small class="text-muted">{{ $application->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1 text-muted">Applied for: {{ $application->job->title ?? 'Unknown Job' }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-dollar-sign"></i>
                                                ${{ number_format($application->proposed_rate ?? 0, 2) }}
                                            </small>
                                            <span class="badge bg-{{ $application->status === 'accepted' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($application->status ?? 'pending') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No applications received yet.</p>
                                <button class="btn btn-warning btn-sm">
                                    <i class="fas fa-search me-2"></i>Find Freelancers
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Job Statistics -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Job Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <h3 class="text-primary">{{ $jobStats['this_month'] }}</h3>
                                <p class="text-muted">Jobs This Month</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <h3 class="text-success">{{ $jobStats['last_month'] }}</h3>
                                <p class="text-muted">Jobs Last Month</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <h3 class="text-warning">{{ $jobStats['completion_rate'] }}%</h3>
                                <p class="text-muted">Completion Rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>">{{ $job->title ?? 'Untitled Job' }}</h6>
                                            <small class="text-muted">{{ $job->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1 text-muted">{{ Str::limit($job->description ?? 'No description', 100) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-dollar-sign"></i>
                                                ${{ number_format($job->budget ?? 0, 2) }}
                                            </small>
                                            <span class="badge bg-{{ $job->status === 'active' ? 'success' : ($job->status === 'completed' ? 'primary' : 'warning') }}">
                                                {{ ucfirst($job->status ?? 'pending') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No jobs posted yet.</p>
                                <button class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-2"></i>Post Your First Job
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>
                            Recent Applications
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($recentApplications->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentApplications->take(5) as $application)
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1