<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\JobManagementController as AdminJobManagementController;
use App\Http\Controllers\Admin\ApplicationManagementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Client\ClientDashboardController;
use App\Http\Controllers\Client\JobController as ClientJobController;
use App\Http\Controllers\Client\ApplicationController as ClientApplicationController;
use App\Http\Controllers\Client\ClientProfileController;
use App\Http\Controllers\Freelancer\FreelancerDashboardController;
use App\Http\Controllers\Freelancer\JobBrowseController;
use App\Http\Controllers\Freelancer\JobApplicationController;
use App\Http\Controllers\Freelancer\FreelancerProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs', [JobBrowseController::class, 'publicIndex'])->name('jobs.public');
Route::get('/jobs/{job}', [JobBrowseController::class, 'publicShow'])->name('jobs.public.show');
Route::get('/jobs/search', [JobBrowseController::class, 'publicSearch'])->name('jobs.public.search');

// Authentication routes (Laravel Breeze)
require __DIR__.'/auth.php';

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // General dashboard redirect
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'client' => redirect()->route('client.dashboard'),
            'freelancer' => redirect()->route('freelancer.dashboard'),
            default => redirect()->route('home')
        };
    })->name('dashboard');

    // Profile routes (common for all users)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // User management
        Route::resource('users', UserManagementController::class);
        Route::patch('users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
        
        // Job management (admin view)
        Route::resource('jobs', AdminJobManagementController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
        Route::patch('jobs/{job}/toggle-status', [AdminJobManagementController::class, 'toggleStatus'])->name('jobs.toggle-status');
        
        // Application management (admin view)
        Route::resource('applications', ApplicationManagementController::class)->only(['index', 'show', 'edit', 'update']);
        
        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/jobs', [ReportController::class, 'jobs'])->name('jobs');
            Route::get('/applications', [ReportController::class, 'applications'])->name('applications');
            Route::get('/users', [ReportController::class, 'users'])->name('users');
            Route::post('/export', [ReportController::class, 'export'])->name('export');
        });
        
        // Activity logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');
    });

    // Client routes
    Route::middleware(['role:client'])->prefix('client')->name('client.')->group(function () {
        Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
        
        // Job management
        Route::resource('jobs', ClientJobController::class);
        Route::patch('jobs/{job}/toggle-status', [ClientJobController::class, 'toggleStatus'])->name('jobs.toggle-status');
        Route::post('jobs/{job}/duplicate', [ClientJobController::class, 'duplicate'])->name('jobs.duplicate');
        
        // Application management
        Route::prefix('applications')->name('applications.')->group(function () {
            Route::get('/', [ClientApplicationController::class, 'index'])->name('index');
            Route::get('/{application}', [ClientApplicationController::class, 'show'])->name('show');
            Route::patch('/{application}/status', [ClientApplicationController::class, 'updateStatus'])->name('update-status');
            Route::post('/{application}/notes', [ClientApplicationController::class, 'addNotes'])->name('add-notes');
        });
        
        // Profile
        Route::get('/profile', [ClientProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ClientProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ClientProfileController::class, 'update'])->name('profile.update');
    });

    // Freelancer routes
    Route::middleware(['role:freelancer'])->prefix('freelancer')->name('freelancer.')->group(function () {
        Route::get('/dashboard', [FreelancerDashboardController::class, 'index'])->name('dashboard');
        
        // Job browsing
        Route::prefix('jobs')->name('jobs.')->group(function () {
            Route::get('/', [JobBrowseController::class, 'index'])->name('index');
            Route::get('/search', [JobBrowseController::class, 'search'])->name('search');
            Route::get('/{job}', [JobBrowseController::class, 'show'])->name('show');
            Route::get('/{job}/apply', [JobApplicationController::class, 'create'])->name('apply');
            Route::post('/{job}/apply', [JobApplicationController::class, 'store'])->name('apply.store');
        });
        
        // Applications management
        Route::prefix('applications')->name('applications.')->group(function () {
            Route::get('/', [JobApplicationController::class, 'index'])->name('index');
            Route::get('/{application}', [JobApplicationController::class, 'show'])->name('show');
            Route::patch('/{application}', [JobApplicationController::class, 'update'])->name('update');
            Route::delete('/{application}', [JobApplicationController::class, 'destroy'])->name('destroy');
        });
        
        // Profile
        Route::get('/profile', [FreelancerProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [FreelancerProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [FreelancerProfileController::class, 'update'])->name('profile.update');
    });

    // Shared routes for all authenticated users
    Route::prefix('shared')->name('shared.')->group(function () {
        // Job viewing (all users can view jobs)
        Route::get('/jobs/{job}', [JobBrowseController::class, 'show'])->name('jobs.show');
        
        // Notifications (if implemented)
        Route::get('/notifications', function () {
            return view('notifications.index');
        })->name('notifications.index');
        
        Route::patch('/notifications/{notification}/read', function () {
            // Mark notification as read
        })->name('notifications.read');
    });
});

// API routes for AJAX requests
Route::middleware(['auth', 'verified'])->prefix('api')->name('api.')->group(function () {
    // Dashboard data
    Route::get('/dashboard/stats', function () {
        $user = auth()->user();
        return match($user->role) {
            'admin' => app(AdminDashboardController::class)->getStats(request()),
            'client' => app(ClientDashboardController::class)->getStats(request()),
            'freelancer' => app(FreelancerDashboardController::class)->getStats(request()),
            default => response()->json(['error' => 'Unauthorized'], 403)
        };
    })->name('dashboard.stats');
    
    // Job search/filter
    Route::get('/jobs/search', [JobBrowseController::class, 'apiSearch'])->name('jobs.search');
    
    // Application status updates
    Route::patch('/applications/{application}/status', [ClientApplicationController::class, 'apiUpdateStatus'])->name('applications.update-status');
});

// Error pages
Route::fallback(function () {
    return view('errors.404');
});

// Additional utility routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Download routes
    Route::get('/download/application/{application}/resume', function ($application) {
        // Handle resume download
    })->name('download.application.resume');
    
    Route::get('/download/job/{job}/attachment', function ($job) {
        // Handle job attachment download
    })->name('download.job.attachment');
    
    // File upload routes
    Route::post('/upload/avatar', [ProfileController::class, 'uploadAvatar'])->name('upload.avatar');
    Route::post('/upload/resume', [FreelancerProfileController::class, 'uploadResume'])->name('upload.resume');
});