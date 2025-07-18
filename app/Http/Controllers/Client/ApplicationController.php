<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\JobApprovalNotification;
use App\Mail\ApplicationStatusNotification;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get filter parameters
            $status = $request->get('status');
            $search = $request->get('search');
            
            // Get all applications for jobs owned by this client
            $applications = Application::whereHas('job', function($query) use ($user) {
                $query->where('client_id', $user->id);
            })->with(['job', 'freelancer.user']); // FIXED: Load freelancer.user relationship
            
            // Apply filters
            if ($status) {
                $applications->where('status', $status);
            }
            
            if ($search) {
                $applications->whereHas('freelancer.user', function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })->orWhereHas('job', function($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%");
                });
            }
            
            // Check for pending completions (priority notifications)
            $pendingCompletions = Application::whereHas('job', function($query) use ($user) {
                $query->where('client_id', $user->id);
            })->where('status', 'pending_completion')->count();
            
            $applications = $applications->orderBy('created_at', 'desc')->paginate(15);
            
            return view('client.applications.index', compact('applications', 'pendingCompletions'));
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('client.dashboard')
                ->with('error', 'Error loading applications: ' . $e->getMessage());
        }
    }

    public function show(Application $application)
    {
        try {
            $user = Auth::user();
            
            // Check if user owns the job this application is for
            if ($application->job->client_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
            
            $application->load(['job', 'freelancer.user']); // FIXED: Load freelancer.user relationship
            
            return view('client.applications.show', compact('application'));
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@show', [
                'application_id' => $application->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('client.applications.index')
                ->with('error', 'Error loading application: ' . $e->getMessage());
        }
    }

    /**
     * ENHANCED: Accept application with email notification
     */
    public function accept(Application $application)
    {
        try {
            $user = Auth::user();
            
            // Check if user owns the job this application is for
            if ($application->job->client_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
            
            $application->update([
                'status' => 'accepted',
                'accepted_at' => now()
            ]);
            
            Log::info('Application accepted', [
                'application_id' => $application->id,
                'job_id' => $application->job_id,
                'client_id' => $user->id
            ]);

            // ENHANCED: Send email notification to freelancer
            $emailSent = $this->sendStatusNotification($application, 'accepted');

            // Prepare success message based on email status
            $message = 'Application accepted successfully!';
            $alertType = 'success';

            if ($emailSent) {
                $message .= ' The freelancer has been notified via email.';
            } else {
                $message .= ' However, we were unable to send an email notification to the freelancer.';
                $alertType = 'warning';
            }
            
            return back()->with($alertType, $message);
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@accept', [
                'application_id' => $application->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error accepting application: ' . $e->getMessage());
        }
    }

    /**
     * ENHANCED: Reject application with email notification
     */
    public function reject(Application $application)
    {
        try {
            $user = Auth::user();
            
            // Check if user owns the job this application is for
            if ($application->job->client_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
            
            $application->update([
                'status' => 'rejected',
                'reviewed_at' => now()
            ]);
            
            Log::info('Application rejected', [
                'application_id' => $application->id,
                'job_id' => $application->job_id,
                'client_id' => $user->id
            ]);

            // ENHANCED: Send email notification to freelancer
            $emailSent = $this->sendStatusNotification($application, 'rejected');

            // Prepare success message based on email status
            $message = 'Application rejected successfully!';
            $alertType = 'success';

            if ($emailSent) {
                $message .= ' The freelancer has been notified via email.';
            } else {
                $message .= ' However, we were unable to send an email notification to the freelancer.';
                $alertType = 'warning';
            }
            
            return back()->with($alertType, $message);
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@reject', [
                'application_id' => $application->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error rejecting application: ' . $e->getMessage());
        }
    }

    /**
     * ENHANCED: Shortlist application with email notification
     */
    public function shortlist(Application $application)
    {
        try {
            $user = Auth::user();
            
            // Check if user owns the job this application is for
            if ($application->job->client_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
            
            $application->update([
                'status' => 'shortlisted',
                'reviewed_at' => now()
            ]);
            
            Log::info('Application shortlisted', [
                'application_id' => $application->id,
                'job_id' => $application->job_id,
                'client_id' => $user->id
            ]);

            // ENHANCED: Send email notification to freelancer
            $emailSent = $this->sendStatusNotification($application, 'shortlisted');

            // Prepare success message based on email status
            $message = 'Application shortlisted successfully!';
            $alertType = 'success';

            if ($emailSent) {
                $message .= ' The freelancer has been notified via email.';
            } else {
                $message .= ' However, we were unable to send an email notification to the freelancer.';
                $alertType = 'warning';
            }
            
            return back()->with($alertType, $message);
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@shortlist', [
                'application_id' => $application->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error shortlisting application: ' . $e->getMessage());
        }
    }

    /**
     * FIXED: Send application status notification to freelancer
     */
    private function sendStatusNotification(Application $application, string $status): bool
    {
        try {
            // FIXED: Load necessary relationships - INCLUDE user relationship for freelancer
            if (!$application->relationLoaded('freelancer')) {
                $application->load(['freelancer.user', 'job.client']);
            } elseif (!$application->freelancer->relationLoaded('user')) {
                $application->freelancer->load('user');
            }

            // Validate freelancer exists
            if (!$application->freelancer) {
                Log::error('Status notification failed: Freelancer not found', [
                    'application_id' => $application->id,
                    'freelancer_id' => $application->freelancer_id,
                    'status' => $status
                ]);
                return false;
            }

            // Validate freelancer user exists
            if (!$application->freelancer->user) {
                Log::error('Status notification failed: Freelancer user not found', [
                    'application_id' => $application->id,
                    'freelancer_id' => $application->freelancer_id,
                    'status' => $status
                ]);
                return false;
            }

            // FIXED: Get email from freelancer->user->email instead of freelancer->email
            $freelancerEmail = $application->freelancer->user->email;

            // Validate freelancer email
            if (!$freelancerEmail || trim($freelancerEmail) === '') {
                Log::error('Status notification failed: Freelancer email not found', [
                    'application_id' => $application->id,
                    'freelancer_id' => $application->freelancer_id,
                    'user_id' => $application->freelancer->user_id,
                    'status' => $status
                ]);
                return false;
            }

            // Validate email format
            if (!filter_var($freelancerEmail, FILTER_VALIDATE_EMAIL)) {
                Log::error('Status notification failed: Invalid email format', [
                    'application_id' => $application->id,
                    'freelancer_email' => $freelancerEmail,
                    'status' => $status
                ]);
                return false;
            }

            // Validate email configuration
            $this->validateEmailConfig();

            Log::info('Attempting to send application status notification', [
                'application_id' => $application->id,
                'status' => $status,
                'freelancer_email' => $freelancerEmail,
                'job_id' => $application->job_id,
                'mail_driver' => config('mail.default')
            ]);

            // Send the email
            Mail::to($freelancerEmail)->send(new ApplicationStatusNotification($application, $status));

            Log::info('Application status notification sent successfully', [
                'application_id' => $application->id,
                'status' => $status,
                'freelancer_email' => $freelancerEmail
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send application status notification', [
                'application_id' => $application->id,
                'status' => $status,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'freelancer_email' => $application->freelancer->user->email ?? 'not available',
                'mail_config' => $this->getMailConfigForLogging()
            ]);

            return false;
        }
    }

    /**
     * Show completion review page
     */
    public function reviewCompletion(Application $application)
    {
        try {
            $user = Auth::user();
            
            // Check if user owns the job this application is for
            if ($application->job->client_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
            
            // Only pending completion applications can be reviewed
            if ($application->status !== 'pending_completion') {
                return redirect()->route('client.applications.show', $application)
                    ->with('error', 'This application is not pending completion review.');
            }
            
            $application->load(['job', 'freelancer.user']); // FIXED: Load freelancer.user relationship
            
            return view('client.applications.review-completion', compact('application'));
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@reviewCompletion', [
                'application_id' => $application->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('client.applications.index')
                ->with('error', 'Error loading completion review: ' . $e->getMessage());
        }
    }

    /**
     * Approve job completion - FIXED VERSION
     */
    public function approveCompletion(Request $request, Application $application)
    {
        try {
            $user = Auth::user();
            
            // Check if user owns the job this application is for
            if ($application->job->client_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
            
            // Only pending completion applications can be approved
            if ($application->status !== 'pending_completion') {
                return redirect()->route('client.applications.show', $application)
                    ->with('error', 'This application is not pending completion review.');
            }
            
            $validated = $request->validate([
                'feedback' => 'nullable|string|max:2000',
                'rating' => 'required|integer|min:1|max:5'
            ]);
            
            $application->update([
                'status' => 'completed',
                'client_feedback' => $validated['feedback'],
                'client_rating' => $validated['rating'],
                'approved_at' => now()
            ]);
            
            // Also update the job status to completed
            $application->job->update([
                'status' => 'completed'
            ]);
            
            // FIXED: Better email handling with proper error checking
            $emailSent = $this->sendEmailNotification($application, 'approved');
            
            Log::info('Job completion approved', [
                'application_id' => $application->id,
                'job_id' => $application->job_id,
                'client_id' => $user->id,
                'rating' => $validated['rating'],
                'email_sent' => $emailSent
            ]);
            
            // Prepare success message based on email status
            $message = 'Job completion approved successfully!';
            $alertType = 'success';
            
            if ($emailSent) {
                $message .= ' The freelancer has been notified via email.';
            } else {
                $message .= ' However, we were unable to send an email notification to the freelancer. Please check your email configuration.';
                $alertType = 'warning';
            }
            
            return redirect()->route('client.applications.show', $application)
                ->with($alertType, $message);
                
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@approveCompletion', [
                'application_id' => $application->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error approving completion: ' . $e->getMessage());
        }
    }

    /**
     * Request revisions for job completion - FIXED VERSION
     */
    public function requestRevisions(Request $request, Application $application)
    {
        try {
            $user = Auth::user();
            
            // Check if user owns the job this application is for
            if ($application->job->client_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
            
            // Only pending completion applications can have revisions requested
            if ($application->status !== 'pending_completion') {
                return redirect()->route('client.applications.show', $application)
                    ->with('error', 'This application is not pending completion review.');
            }
            
            $validated = $request->validate([
                'revision_notes' => 'required|string|max:2000'
            ]);
            
            $application->update([
                'status' => 'revision_requested',
                'revision_notes' => $validated['revision_notes'],
                'revision_requested_at' => now()
            ]);
            
            // FIXED: Better email handling
            $emailSent = $this->sendEmailNotification($application, 'revision_requested');
            
            Log::info('Job revision requested', [
                'application_id' => $application->id,
                'job_id' => $application->job_id,
                'client_id' => $user->id,
                'email_sent' => $emailSent
            ]);
            
            // Prepare success message based on email status
            $message = 'Revision request sent successfully!';
            $alertType = 'success';
            
            if ($emailSent) {
                $message .= ' The freelancer has been notified via email.';
            } else {
                $message .= ' However, we were unable to send an email notification to the freelancer. Please check your email configuration.';
                $alertType = 'warning';
            }
            
            return redirect()->route('client.applications.show', $application)
                ->with($alertType, $message);
                
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@requestRevisions', [
                'application_id' => $application->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error requesting revisions: ' . $e->getMessage());
        }
    }

    /**
     * FIXED: Centralized email sending method with better error handling
     */
    private function sendEmailNotification(Application $application, string $action): bool
    {
        try {
            // FIXED: Ensure the application has all necessary relationships loaded
            if (!$application->relationLoaded('freelancer')) {
                $application->load(['freelancer.user', 'job', 'job.client']);
            } elseif (!$application->freelancer->relationLoaded('user')) {
                $application->freelancer->load('user');
            }
            
            // Validate freelancer exists
            if (!$application->freelancer) {
                Log::error('Email sending failed: Freelancer not found', [
                    'application_id' => $application->id,
                    'freelancer_id' => $application->freelancer_id
                ]);
                return false;
            }
            
            // Validate freelancer user exists
            if (!$application->freelancer->user) {
                Log::error('Email sending failed: Freelancer user not found', [
                    'application_id' => $application->id,
                    'freelancer_id' => $application->freelancer_id
                ]);
                return false;
            }
            
            // FIXED: Get email from freelancer->user->email instead of freelancer->email
            $freelancerEmail = $application->freelancer->user->email;
            
            // Validate freelancer email
            if (!$freelancerEmail) {
                Log::error('Email sending failed: Freelancer email not found', [
                    'application_id' => $application->id,
                    'freelancer_id' => $application->freelancer_id,
                    'user_id' => $application->freelancer->user_id
                ]);
                return false;
            }
            
            // Validate email configuration
            $this->validateEmailConfig();
            
            Log::info('Attempting to send email notification', [
                'application_id' => $application->id,
                'action' => $action,
                'freelancer_email' => $freelancerEmail,
                'mail_driver' => config('mail.default'),
                'mail_host' => config('mail.mailers.' . config('mail.default') . '.host')
            ]);
            
            // Send the email
            Mail::to($freelancerEmail)->send(new JobApprovalNotification($application, $action));
            
            Log::info('Email notification sent successfully', [
                'application_id' => $application->id,
                'action' => $action,
                'freelancer_email' => $freelancerEmail
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Email sending failed', [
                'application_id' => $application->id,
                'action' => $action,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'freelancer_email' => $application->freelancer->user->email ?? 'not available',
                'mail_config' => $this->getMailConfigForLogging()
            ]);
            
            return false;
        }
    }
    
    /**
     * ADDED: Validate email configuration
     */
    private function validateEmailConfig(): void
    {
        $mailDriver = config('mail.default');
        
        if (!$mailDriver) {
            throw new \Exception('Mail driver not configured');
        }
        
        if (!config('mail.from.address')) {
            throw new \Exception('Mail FROM address not configured');
        }
        
        if (!config('mail.from.name')) {
            throw new \Exception('Mail FROM name not configured');
        }
        
        // Validate specific driver configuration
        switch ($mailDriver) {
            case 'smtp':
                if (!config('mail.mailers.smtp.host')) {
                    throw new \Exception('SMTP host not configured');
                }
                if (!config('mail.mailers.smtp.port')) {
                    throw new \Exception('SMTP port not configured');
                }
                if (!config('mail.mailers.smtp.username')) {
                    throw new \Exception('SMTP username not configured');
                }
                if (!config('mail.mailers.smtp.password')) {
                    throw new \Exception('SMTP password not configured');
                }
                break;
                
            case 'sendmail':
                if (!config('mail.mailers.sendmail.path')) {
                    throw new \Exception('Sendmail path not configured');
                }
                break;
                
            case 'log':
                // Log driver doesn't need additional validation
                break;
                
            default:
                throw new \Exception("Unsupported mail driver: {$mailDriver}");
        }
    }
    
    /**
     * ADDED: Get mail configuration for logging (without sensitive data)
     */
    private function getMailConfigForLogging(): array
    {
        $mailDriver = config('mail.default');
        
        return [
            'driver' => $mailDriver,
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'host' => config("mail.mailers.{$mailDriver}.host"),
            'port' => config("mail.mailers.{$mailDriver}.port"),
            'encryption' => config("mail.mailers.{$mailDriver}.encryption"),
            'username' => config("mail.mailers.{$mailDriver}.username") ? 'configured' : 'not configured',
            'password' => config("mail.mailers.{$mailDriver}.password") ? 'configured' : 'not configured',
        ];
    }

    /**
     * Download completion attachment
     */
    public function downloadCompletionAttachment(Application $application, $index)
    {
        $user = Auth::user();
        
        // Check if user owns the job this application is for
        if ($application->job->client_id !== $user->id) {
            abort(403, 'Unauthorized access to this file.');
        }
        
        $attachments = $application->completion_attachments ?: [];
        
        if (!isset($attachments[$index])) {
            return redirect()->back()->with('error', 'Attachment not found.');
        }
        
        $attachment = $attachments[$index];
        
        if (!isset($attachment['path']) || !Storage::disk('private')->exists($attachment['path'])) {
            return redirect()->back()->with('error', 'File not found.');
        }
        
        return Storage::disk('private')->download(
            $attachment['path'], 
            $attachment['original_name'] ?? 'completion_attachment'
        );
    }

    public function complete(Application $application)
    {
        try {
            $user = Auth::user();
            
            // Check if user owns the job this application is for
            if ($application->job->client_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
            
            $application->update([
                'status' => 'completed'
            ]);
            
            // Also update the job status to completed
            $application->job->update([
                'status' => 'completed'
            ]);
            
            Log::info('Application completed', [
                'application_id' => $application->id,
                'job_id' => $application->job_id,
                'client_id' => $user->id
            ]);
            
            return back()->with('success', 'Application marked as completed successfully!');
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@complete', [
                'application_id' => $application->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error completing application: ' . $e->getMessage());
        }
    }

    /**
     * FIXED: Test email functionality with better debugging
     */
    public function testEmail(Application $application)
    {
        try {
            $user = Auth::user();
            
            // Check if user owns the job this application is for
            if ($application->job->client_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access to this application.'
                ]);
            }

            $application->load(['freelancer.user', 'job', 'job.client']); // FIXED: Load freelancer.user

            if (!$application->freelancer || !$application->freelancer->user || !$application->freelancer->user->email) {
                return response()->json([
                    'success' => false,
                    'error' => 'Freelancer, user, or email not found',
                    'debug' => [
                        'freelancer_exists' => !is_null($application->freelancer),
                        'freelancer_user_exists' => !is_null($application->freelancer->user ?? null),
                        'freelancer_id' => $application->freelancer_id,
                        'freelancer_email' => $application->freelancer->user->email ?? 'null'
                    ]
                ]);
            }

            // Validate configuration first
            try {
                $this->validateEmailConfig();
            } catch (\Exception $configE) {
                return response()->json([
                    'success' => false,
                    'error' => 'Email configuration error: ' . $configE->getMessage(),
                    'config' => $this->getMailConfigForLogging()
                ]);
            }

            // Test basic email sending
            Mail::raw('This is a test email from KONEK client system. If you receive this, email configuration is working properly.', function ($message) use ($application) {
                $message->to($application->freelancer->user->email) // FIXED: Use user->email
                        ->subject('Test Email - KONEK Client System');
            });

            // Also test the status notifications
            Mail::to($application->freelancer->user->email)->send(new ApplicationStatusNotification($application, 'accepted')); // FIXED: Use user->email

            return response()->json([
                'success' => true,
                'message' => 'Test emails sent successfully to ' . $application->freelancer->user->email,
                'config' => $this->getMailConfigForLogging()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'config' => $this->getMailConfigForLogging(),
                'trace' => app()->environment('local') ? $e->getTraceAsString() : null
            ]);
        }
    }
}