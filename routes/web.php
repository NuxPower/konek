<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ApplicationManagementController;
use App\Http\Controllers\Admin\JobManagementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Client\ApplicationController;
use App\Http\Controllers\Client\ClientDashboardController;
use App\Http\Controllers\Client\ClientProfileController;
use App\Http\Controllers\Client\JobController;
use App\Http\Controllers\Freelancer\FreelancerDashboardController;
use App\Http\Controllers\Freelancer\FreelancerProfileController;
use App\Http\Controllers\Freelancer\JobApplicationController;
use App\Http\Controllers\Freelancer\JobBrowseController;
use App\Http\Controllers\Freelancer\SavedJobController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'client' => redirect()->route('client.dashboard'),
        'freelancer' => redirect()->route('freelancer.dashboard'),
        default => redirect()->route('home')
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [NotificationController::class, 'open'])->name('notifications.open');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->middleware('role:admin')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [AdminDashboardController::class, 'getStats'])->name('dashboard.stats');

        Route::resource('users', UserManagementController::class);
        Route::patch('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');

        Route::resource('jobs', JobManagementController::class)->except(['create', 'store']);
        Route::patch('/jobs/{job}/toggle-status', [JobManagementController::class, 'toggleStatus'])->name('jobs.toggle-status');

        Route::resource('applications', ApplicationManagementController::class)->only(['index', 'show', 'edit', 'update']);
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/jobs', [ReportController::class, 'jobs'])->name('reports.jobs');
        Route::get('/reports/applications', [ReportController::class, 'applications'])->name('reports.applications');
        Route::get('/reports/users', [ReportController::class, 'users'])->name('reports.users');
        Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });

Route::middleware(['auth', 'verified', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [ClientDashboardController::class, 'getStats'])->name('dashboard.stats');

        Route::resource('jobs', JobController::class);
        Route::patch('/jobs/{job}/status', [JobController::class, 'updateStatus'])->name('jobs.status');
        Route::post('/jobs/{job}/duplicate', [JobController::class, 'duplicate'])->name('jobs.duplicate');

        Route::resource('applications', ApplicationController::class)->only(['index', 'show']);
        Route::patch('/applications/{application}/status', [ApplicationController::class, 'updateStatus'])->name('applications.status');
        Route::patch('/applications/{application}/notes', [ApplicationController::class, 'addNotes'])->name('applications.notes');

        Route::get('/profile', [ClientProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ClientProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ClientProfileController::class, 'update'])->name('profile.update');
    });

Route::middleware(['auth', 'verified', 'role:freelancer'])
    ->prefix('freelancer')
    ->name('freelancer.')
    ->group(function () {
        Route::get('/dashboard', [FreelancerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [FreelancerDashboardController::class, 'getStats'])->name('dashboard.stats');

        Route::get('/jobs/search', [JobBrowseController::class, 'search'])->name('jobs.search');
        Route::get('/jobs', [JobBrowseController::class, 'index'])->name('jobs.index');
        Route::get('/saved-jobs', [SavedJobController::class, 'index'])->name('saved-jobs.index');
        Route::post('/saved-jobs/{job}', [SavedJobController::class, 'store'])->name('saved-jobs.store');
        Route::delete('/saved-jobs/{job}', [SavedJobController::class, 'destroy'])->name('saved-jobs.destroy');
        Route::get('/jobs/{job}', [JobBrowseController::class, 'show'])->name('jobs.show');
        Route::get('/jobs/{job}/apply', [JobApplicationController::class, 'create'])->name('jobs.apply.create');
        Route::post('/jobs/{job}/apply', [JobApplicationController::class, 'store'])->name('jobs.apply');

        Route::resource('applications', JobApplicationController::class)->only(['index', 'show', 'update', 'destroy']);

        Route::get('/profile', [FreelancerProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [FreelancerProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [FreelancerProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/resume', [FreelancerProfileController::class, 'uploadResume'])->name('profile.resume');
    });

require __DIR__.'/auth.php';
