<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailOtpController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/help', [HomeController::class, 'help'])->name('help');
Route::get('/jobs', [HomeController::class, 'jobs'])->name('jobs.index');
Route::get('/jobs/{job}', [HomeController::class, 'showJob'])->name('jobs.show');
Route::get('/jobs/category/{category}', [HomeController::class, 'jobsByCategory'])->name('jobs.category');
Route::get('/search', [HomeController::class, 'search'])->name('search');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])->middleware('cmu_email');
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Email OTP Routes
|--------------------------------------------------------------------------
*/

Route::middleware('web')->group(function () {
    Route::get('/email-otp/challenge', [EmailOtpController::class, 'challenge'])->name('email-otp.challenge');
    Route::post('/email-otp/verify', [EmailOtpController::class, 'verify'])->name('email-otp.verify');
    Route::post('/email-otp/resend', [EmailOtpController::class, 'resend'])->name('email-otp.resend');
    Route::get('/email-otp/cancel', [EmailOtpController::class, 'cancel'])->name('email-otp.cancel');
});

/*
|--------------------------------------------------------------------------
| Common Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile/complete', [App\Http\Controllers\Client\ClientProfileController::class, 'complete'])->name('profile.complete');
    Route::post('/profile/complete', [App\Http\Controllers\Client\ClientProfileController::class, 'storeComplete'])->name('profile.complete.store');
    
    // Public profile route accessible by all authenticated users
    Route::get('/profile/{user}', [App\Http\Controllers\ProfileController::class, 'show'])
        ->name('profile.public.show');
    
    Route::get('/profile', function () {
        $user = auth()->user();
        if ($user->role === 'admin') return redirect()->route('admin.profile.show');
        if ($user->role === 'client') return redirect()->route('client.profile.show');
        if ($user->role === 'freelancer') return redirect()->route('freelancer.profile.show');
        return view('profile.show', ['user' => $user]);
    })->name('profile.show');
    
    Route::get('/profile/edit', function () {
        $user = auth()->user();
        if ($user->role === 'admin') return redirect()->route('admin.profile.edit');
        if ($user->role === 'client') return redirect()->route('client.profile.edit');
        if ($user->role === 'freelancer') return redirect()->route('freelancer.profile.edit');
        return view('profile.edit', ['user' => $user]);
    })->name('profile.edit');
    
    Route::put('/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password.update');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES - SIMPLIFIED
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/admin/dashboard', function() {
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_admins' => \App\Models\User::where('role', 'admin')->count(),
            'total_clients' => \App\Models\User::where('role', 'client')->count(),
            'total_freelancers' => \App\Models\User::where('role', 'freelancer')->count(),
            'active_users' => \App\Models\User::where('is_active', true)->count(),
            'inactive_users' => \App\Models\User::where('is_active', false)->count(),
            'verified_users' => \App\Models\User::whereNotNull('email_verified_at')->count(),
            'unverified_users' => \App\Models\User::whereNull('email_verified_at')->count(),
        ];
        $recentUsers = \App\Models\User::latest()->take(10)->get();
        return view('admin.dashboard', compact('stats', 'recentUsers'));
    })->name('admin.dashboard');
    
    // Profile
    Route::get('/admin/profile', function() {
        return view('admin.profile.show', ['admin' => auth()->user()]);
    })->name('admin.profile.show');
    
    Route::get('/admin/profile/edit', function() {
        return view('admin.profile.edit', ['admin' => auth()->user()]);
    })->name('admin.profile.edit');
    
    Route::put('/admin/profile', [App\Http\Controllers\Admin\AdminProfileController::class, 'update'])->name('admin.profile.update');
    
    // Users Management
    Route::get('/admin/users', function() {
        $users = \App\Models\User::paginate(15);
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_admins' => \App\Models\User::where('role', 'admin')->count(),
            'total_clients' => \App\Models\User::where('role', 'client')->count(),
            'total_freelancers' => \App\Models\User::where('role', 'freelancer')->count(),
            'active_users' => \App\Models\User::where('is_active', true)->count(),
            'inactive_users' => \App\Models\User::where('is_active', false)->count(),
            'verified_users' => \App\Models\User::whereNotNull('email_verified_at')->count(),
            'unverified_users' => \App\Models\User::whereNull('email_verified_at')->count(),
        ];
        return view('admin.users.index', compact('users', 'stats'));
    })->name('admin.users.index');
    
    // Redirects for job/application management
    Route::get('/admin/jobs', function() {
        return redirect()->route('admin.users.index')->with('info', 'Job management is handled by clients and freelancers directly.');
    })->name('admin.jobs.index');
    
    Route::get('/admin/applications', function() {
        return redirect()->route('admin.users.index')->with('info', 'Application management is handled by clients and freelancers directly.');
    })->name('admin.applications.index');
    
    // Settings
    Route::get('/admin/settings', function() {
        return view('admin.settings.index');
    })->name('admin.settings.index');
    
    // Reports
    Route::get('/admin/reports', function() {
        return view('admin.reports.index');
    })->name('admin.reports.index');
    
    // Freelancers/Clients views
    Route::get('/admin/freelancers', function() {
        $freelancers = \App\Models\User::where('role', 'freelancer')->paginate(15);
        return view('admin.users.freelancers', compact('freelancers'));
    })->name('admin.freelancers');
    
    Route::get('/admin/clients', function() {
        $clients = \App\Models\User::where('role', 'client')->paginate(15);
        return view('admin.users.clients', compact('clients'));
    })->name('admin.clients');
});

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Client\ClientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [App\Http\Controllers\Client\ClientDashboardController::class, 'analytics'])->name('analytics');
    Route::get('/profile/complete', [App\Http\Controllers\Client\ClientProfileController::class, 'completeProfile'])->name('profile.complete');
    
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [App\Http\Controllers\Client\ClientProfileController::class, 'show'])->name('show');
        Route::get('/edit', [App\Http\Controllers\Client\ClientProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [App\Http\Controllers\Client\ClientProfileController::class, 'update'])->name('update');
        Route::post('/avatar', [App\Http\Controllers\Client\ClientProfileController::class, 'uploadAvatar'])->name('upload-avatar');
        Route::delete('/avatar', [App\Http\Controllers\Client\ClientProfileController::class, 'deleteAvatar'])->name('delete-avatar');
        Route::post('/verify', [App\Http\Controllers\Client\ClientProfileController::class, 'requestVerification'])->name('verify');
        Route::post('/request-verification', [App\Http\Controllers\Client\ClientProfileController::class, 'requestVerification'])->name('request-verification');
        Route::get('/settings', [App\Http\Controllers\Client\ClientProfileController::class, 'settings'])->name('settings');
        Route::put('/settings', [App\Http\Controllers\Client\ClientProfileController::class, 'updateSettings'])->name('settings.update');
        Route::put('/password', [App\Http\Controllers\Client\ClientProfileController::class, 'changePassword'])->name('password.change');
        Route::post('/change-password', [App\Http\Controllers\Client\ClientProfileController::class, 'changePassword'])->name('change-password');
    });
    
    // Jobs resource routes
    Route::resource('jobs', App\Http\Controllers\Client\JobController::class);
    
    // Additional job routes that aren't part of the standard resource
    Route::post('jobs/{job}/close', [App\Http\Controllers\Client\JobController::class, 'close'])->name('jobs.close');
    Route::post('jobs/{job}/reopen', [App\Http\Controllers\Client\JobController::class, 'reopen'])->name('jobs.reopen');
    Route::post('jobs/{job}/duplicate', [App\Http\Controllers\Client\JobController::class, 'duplicate'])->name('jobs.duplicate');
    
    // Application management routes - UPDATED WITH COMPLETION REVIEW
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Client\ApplicationController::class, 'index'])->name('index');
        Route::get('/{application}', [App\Http\Controllers\Client\ApplicationController::class, 'show'])->name('show');
        
        // Application status actions
        Route::patch('/{application}/accept', [App\Http\Controllers\Client\ApplicationController::class, 'accept'])->name('accept');
        Route::patch('/{application}/reject', [App\Http\Controllers\Client\ApplicationController::class, 'reject'])->name('reject');
        Route::patch('/{application}/shortlist', [App\Http\Controllers\Client\ApplicationController::class, 'shortlist'])->name('shortlist');
        Route::patch('/{application}/complete', [App\Http\Controllers\Client\ApplicationController::class, 'complete'])->name('complete');
        
        // NEW: Completion review routes
        Route::get('/{application}/review-completion', [App\Http\Controllers\Client\ApplicationController::class, 'reviewCompletion'])->name('review-completion');
        Route::patch('/{application}/approve-completion', [App\Http\Controllers\Client\ApplicationController::class, 'approveCompletion'])->name('approve-completion');
        Route::patch('/{application}/request-revisions', [App\Http\Controllers\Client\ApplicationController::class, 'requestRevisions'])->name('request-revisions');
        
        // NEW: File download routes
        Route::get('/{application}/download-completion/{index}', [App\Http\Controllers\Client\ApplicationController::class, 'downloadCompletionAttachment'])->name('download-completion-attachment');
    });
});

/*
|--------------------------------------------------------------------------
| Freelancer Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:freelancer'])->prefix('freelancer')->name('freelancer.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Freelancer\FreelancerDashboardController::class, 'index'])->name('dashboard');
    
    // Updated freelancer profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [App\Http\Controllers\Freelancer\FreelancerProfileController::class, 'show'])->name('show');
        Route::get('/edit', [App\Http\Controllers\Freelancer\FreelancerProfileController::class, 'edit'])->name('edit');
        Route::put('/', [App\Http\Controllers\Freelancer\FreelancerProfileController::class, 'update'])->name('update');
        Route::get('/portfolio', [App\Http\Controllers\Freelancer\FreelancerProfileController::class, 'portfolio'])->name('portfolio');
        Route::post('/portfolio', [App\Http\Controllers\Freelancer\FreelancerProfileController::class, 'addPortfolioItem'])->name('portfolio.add');
        Route::delete('/portfolio/{id}', [App\Http\Controllers\Freelancer\FreelancerProfileController::class, 'removePortfolioItem'])->name('portfolio.remove');
        Route::get('/verification', [App\Http\Controllers\Freelancer\FreelancerProfileController::class, 'verification'])->name('verification');
        Route::post('/verification', [App\Http\Controllers\Freelancer\FreelancerProfileController::class, 'submitVerification'])->name('verification.submit');
        Route::get('/settings', [App\Http\Controllers\Freelancer\FreelancerProfileController::class, 'settings'])->name('settings');
        Route::put('/settings', [App\Http\Controllers\Freelancer\FreelancerProfileController::class, 'updateSettings'])->name('settings.update');
        Route::post('/toggle-availability', [App\Http\Controllers\Freelancer\FreelancerProfileController::class, 'toggleAvailability'])->name('toggle-availability');
        Route::get('/analytics', [App\Http\Controllers\Freelancer\FreelancerProfileController::class, 'analytics'])->name('analytics');
        Route::get('/export', [App\Http\Controllers\Freelancer\FreelancerProfileController::class, 'exportData'])->name('export');
    });
    
    // Job browsing routes
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/', [App\Http\Controllers\Freelancer\JobBrowseController::class, 'index'])->name('browse');
        Route::get('/saved', [App\Http\Controllers\Freelancer\JobBrowseController::class, 'saved'])->name('saved');
        Route::get('/{job}', [App\Http\Controllers\Freelancer\JobBrowseController::class, 'show'])->name('show');
        Route::get('/category/{category}', [App\Http\Controllers\Freelancer\JobBrowseController::class, 'category'])->name('category');
        
        // Job save/unsave functionality
        Route::post('/{job}/save', [App\Http\Controllers\Freelancer\JobBrowseController::class, 'saveJob'])->name('save');
        Route::delete('/{job}/unsave', [App\Http\Controllers\Freelancer\JobBrowseController::class, 'unsaveJob'])->name('unsave');
        
        // Job application routes (nested under jobs)
        Route::get('/{job}/apply', [App\Http\Controllers\Freelancer\JobApplicationController::class, 'create'])->name('apply');
        Route::post('/{job}/apply', [App\Http\Controllers\Freelancer\JobApplicationController::class, 'store'])->name('apply.store');
    });
    
    // Application management routes
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Freelancer\JobApplicationController::class, 'index'])->name('index');
        Route::get('/{application}', [App\Http\Controllers\Freelancer\JobApplicationController::class, 'show'])->name('show');
        Route::get('/{application}/edit', [App\Http\Controllers\Freelancer\JobApplicationController::class, 'edit'])->name('edit');
        Route::put('/{application}', [App\Http\Controllers\Freelancer\JobApplicationController::class, 'update'])->name('update');
        Route::delete('/{application}', [App\Http\Controllers\Freelancer\JobApplicationController::class, 'destroy'])->name('destroy');
        
        // Job completion routes
        Route::get('/{application}/complete', [App\Http\Controllers\Freelancer\JobApplicationController::class, 'markCompleted'])->name('complete');
        Route::post('/{application}/submit-completion', [App\Http\Controllers\Freelancer\JobApplicationController::class, 'submitCompletion'])->name('submit-completion');
        
        // Application attachment routes
        Route::get('/{application}/download/{index}', [App\Http\Controllers\Freelancer\JobApplicationController::class, 'downloadAttachment'])->name('download-attachment');
        Route::delete('/{application}/attachment/{index}', [App\Http\Controllers\Freelancer\JobApplicationController::class, 'removeAttachment'])->name('remove-attachment');
        
        // Completion attachment download route
        Route::get('/{application}/download-completion/{index}', [App\Http\Controllers\Freelancer\JobApplicationController::class, 'downloadCompletionAttachment'])->name('download-completion-attachment');
        
        // Bulk actions
        Route::post('/bulk-action', [App\Http\Controllers\Freelancer\JobApplicationController::class, 'bulkAction'])->name('bulk-action');
    });
});

/*
|--------------------------------------------------------------------------
| Fallback
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});