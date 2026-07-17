<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ApplicationManagementController;
use App\Http\Controllers\Admin\JobManagementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Client\ApplicationController;
use App\Http\Controllers\Client\JobController;
use App\Http\Controllers\Freelancer\FreelancerProfileController;
use App\Http\Controllers\Freelancer\JobApplicationController;
use App\Http\Controllers\Freelancer\JobBrowseController;
use App\Http\Controllers\Freelancer\SavedJobController;
use App\Http\Controllers\Member\MemberDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/members/{user:username}', [FreelancerProfileController::class, 'publicShow'])->name('members.profile.show');
Route::get('/members/{user:username}/resume', [FreelancerProfileController::class, 'downloadResume'])
    ->middleware('auth')
    ->name('members.profile.resume.download');

Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'member', 'client', 'freelancer' => redirect()->route('member.dashboard'),
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

Route::middleware(['auth', 'verified', 'role:member'])
    ->prefix('member')
    ->name('member.')
    ->group(function () {
        Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [MemberDashboardController::class, 'getStats'])->name('dashboard.stats');

        Route::resource('posted-jobs', JobController::class)
            ->parameters(['posted-jobs' => 'job']);
        Route::patch('/posted-jobs/{job}/status', [JobController::class, 'updateStatus'])->name('posted-jobs.status');
        Route::post('/posted-jobs/{job}/duplicate', [JobController::class, 'duplicate'])->name('posted-jobs.duplicate');

        Route::resource('received-applications', ApplicationController::class)
            ->only(['index', 'show'])
            ->parameters(['received-applications' => 'application']);
        Route::patch('/received-applications/{application}/status', [ApplicationController::class, 'updateStatus'])->name('received-applications.status');
        Route::patch('/received-applications/{application}/notes', [ApplicationController::class, 'addNotes'])->name('received-applications.notes');

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
        Route::get('/profile/resume', [FreelancerProfileController::class, 'downloadOwnResume'])->name('profile.resume.download');
    });

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/client/dashboard', '/member/dashboard')->name('client.dashboard');
    Route::redirect('/freelancer/dashboard', '/member/dashboard')->name('freelancer.dashboard');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified', 'role:member'])
    ->prefix('member')
    ->name('member.')
    ->group(function () {
        Route::get('/identity', [\App\Http\Controllers\Member\IdentityVerificationController::class, 'edit'])->name('identity.edit');
        Route::post('/identity', [\App\Http\Controllers\Member\IdentityVerificationController::class, 'submit'])->name('identity.submit');
        Route::delete('/identity', [\App\Http\Controllers\Member\IdentityVerificationController::class, 'reset'])->name('identity.reset');
        Route::post('/identity/phone/send', [\App\Http\Controllers\Member\IdentityVerificationController::class, 'sendPhoneCode'])->name('identity.phone.send');
        Route::post('/identity/phone/verify', [\App\Http\Controllers\Member\IdentityVerificationController::class, 'verifyPhoneCode'])->name('identity.phone.verify');
    });

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/identity-verifications', [\App\Http\Controllers\Admin\IdentityVerificationController::class, 'index'])->name('identity.index');
        Route::get('/identity-verifications/{identityVerification}', [\App\Http\Controllers\Admin\IdentityVerificationController::class, 'show'])->name('identity.show');
        Route::get('/identity-verifications/{identityVerification}/evidence/{type}', [\App\Http\Controllers\Admin\IdentityVerificationController::class, 'evidence'])->name('identity.evidence');
        Route::post('/identity-verifications/{identityVerification}/approve', [\App\Http\Controllers\Admin\IdentityVerificationController::class, 'approve'])->name('identity.approve');
        Route::post('/identity-verifications/{identityVerification}/reject', [\App\Http\Controllers\Admin\IdentityVerificationController::class, 'reject'])->name('identity.reject');
    });
