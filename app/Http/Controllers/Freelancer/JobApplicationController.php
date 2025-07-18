<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use App\Http\Requests\Application\StoreApplicationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\JobCompletionNotification;
use App\Mail\NewJobApplicationNotification;

class JobApplicationController extends Controller
{
    /**
     * Display a listing of applications for the freelancer
     */
    public function index()
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;
        
        if (!$freelancer) {
            return redirect()->route('freelancer.dashboard')
                ->with('error', 'Please complete your freelancer profile first.');
        }

        $applications = Application::where('freelancer_id', $freelancer->id)
            ->with(['job', 'job.client'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('freelancer.applications.index', compact('applications'));
    }

    /**
     * Show the form for creating a new application
     */
    public function create(Job $job)
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;

        if (!$freelancer) {
            return redirect()->route('freelancer.dashboard')
                ->with('error', 'Please complete your freelancer profile first.');
        }

        if ($job->status !== 'open') {
            return redirect()->route('freelancer.jobs.show', $job)
                ->with('error', 'This job is no longer accepting applications.');
        }

        $existingApplication = Application::where('job_id', $job->id)
            ->where('freelancer_id', $freelancer->id)
            ->first();

        if ($existingApplication) {
            return redirect()->route('freelancer.jobs.show', $job)
                ->with('error', 'You have already applied to this job.');
        }

        $job->load([
            'client',
            'skills',
            'category'
        ]);

        return view('freelancer.applications.create', compact('job', 'freelancer'));
    }

    /**
     * Store a newly created application - ENHANCED WITH EMAIL NOTIFICATIONS
     */
    public function store(StoreApplicationRequest $request, Job $job)
    {
        try {
            $user = Auth::user();
            $freelancer = $user->freelancer;

            if (!$freelancer) {
                return redirect()->route('freelancer.dashboard')
                    ->with('error', 'Please complete your freelancer profile first.');
            }

            if ($job->status !== 'open') {
                return redirect()->route('freelancer.jobs.show', $job)
                    ->with('error', 'This job is no longer accepting applications.');
            }

            $existingApplication = Application::where('job_id', $job->id)
                ->where('freelancer_id', $freelancer->id)
                ->first();

            if ($existingApplication) {
                return redirect()->route('freelancer.jobs.show', $job)
                    ->with('error', 'You have already applied to this job.');
            }

            // Handle file attachments
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments', 'private');
                    $attachments[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ];
                }
            }

            // Create the application
            $application = Application::create([
                'job_id' => $job->id,
                'freelancer_id' => $freelancer->id,
                'cover_letter' => $request->cover_letter,
                'proposed_rate' => $request->proposed_rate,
                'estimated_duration' => $request->estimated_duration,
                'attachments' => !empty($attachments) ? $attachments : null,
                'status' => 'pending',
            ]);

            Log::info('New job application created', [
                'application_id' => $application->id,
                'job_id' => $job->id,
                'freelancer_id' => $freelancer->id
            ]);

            // ENHANCED: Send email notification to client
            $emailSent = $this->sendNewApplicationNotification($application);

            // Prepare success message based on email status
            $message = 'Your application has been submitted successfully!';
            $alertType = 'success';

            if ($emailSent) {
                $message .= ' The client has been notified about your application.';
            } else {
                $message .= ' However, we were unable to notify the client via email.';
                $alertType = 'warning';
            }

            return redirect()->route('freelancer.applications.show', $application)
                ->with($alertType, $message);

        } catch (\Exception $e) {
            Log::error('Error creating job application', [
                'job_id' => $job->id,
                'freelancer_id' => $freelancer->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to submit application: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified application
     */
    public function show(Application $application)
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;

        if ($application->freelancer_id !== $freelancer->id) {
            abort(403, 'Unauthorized access to this application.');
        }

        $application->load([
            'job',
            'job.client',
            'job.skills',
            'job.category'
        ]);

        return view('freelancer.applications.show', compact('application'));
    }

    /**
     * Show the form for editing the specified application
     */
    public function edit(Application $application)
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;

        if ($application->freelancer_id !== $freelancer->id) {
            abort(403, 'Unauthorized access to this application.');
        }

        if ($application->status !== 'pending') {
            return redirect()->route('freelancer.applications.show', $application)
                ->with('error', 'This application cannot be edited.');
        }

        $application->load([
            'job',
            'job.client',
            'job.skills'
        ]);

        return view('freelancer.applications.edit', compact('application'));
    }

    /**
     * Update the specified application
     */
    public function update(Request $request, Application $application)
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;

        if ($application->freelancer_id !== $freelancer->id) {
            abort(403, 'Unauthorized access to this application.');
        }

        if ($application->status !== 'pending') {
            return redirect()->route('freelancer.applications.show', $application)
                ->with('error', 'This application cannot be updated.');
        }

        $validated = $request->validate([
            'cover_letter' => 'required|string|max:2000',
            'proposed_rate' => 'required|numeric|min:0',
            'estimated_duration' => 'required|string|max:100',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $existingAttachments = $application->attachments ?: [];
        
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'private');
                $existingAttachments[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
            }
        }

        $validated['attachments'] = !empty($existingAttachments) ? $existingAttachments : null;

        $application->update($validated);

        return redirect()->route('freelancer.applications.show', $application)
            ->with('success', 'Your application has been updated successfully!');
    }

    /**
     * Remove the specified application
     */
    public function destroy(Application $application)
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;

        if ($application->freelancer_id !== $freelancer->id) {
            abort(403, 'Unauthorized access to this application.');
        }

        if ($application->status !== 'pending') {
            return redirect()->route('freelancer.applications.show', $application)
                ->with('error', 'This application cannot be deleted.');
        }

        if ($application->attachments) {
            foreach ($application->attachments as $attachment) {
                if (isset($attachment['path']) && Storage::disk('private')->exists($attachment['path'])) {
                    Storage::disk('private')->delete($attachment['path']);
                }
            }
        }

        $application->delete();

        return redirect()->route('freelancer.applications.index')
            ->with('success', 'Your application has been withdrawn successfully!');
    }

    /**
     * Mark job as completed by freelancer
     */
    public function markCompleted(Application $application)
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;

        if ($application->freelancer_id !== $freelancer->id) {
            abort(403, 'Unauthorized access to this application.');
        }

        // Only accepted applications can be marked as completed
        if ($application->status !== 'accepted') {
            return redirect()->route('freelancer.applications.show', $application)
                ->with('error', 'Only accepted applications can be marked as completed.');
        }

        // Check if already marked as completed
        if ($application->status === 'completed' || $application->status === 'pending_completion') {
            return redirect()->route('freelancer.applications.show', $application)
                ->with('error', 'This job has already been marked as completed.');
        }

        return view('freelancer.applications.complete', compact('application'));
    }

    /**
     * Submit completion notification to client - ENHANCED VERSION WITH BETTER DEBUGGING
     */
    public function submitCompletion(Request $request, Application $application)
    {
        try {
            $user = Auth::user();
            $freelancer = $user->freelancer;

            if ($application->freelancer_id !== $freelancer->id) {
                abort(403, 'Unauthorized access to this application.');
            }

            if ($application->status !== 'accepted') {
                return redirect()->route('freelancer.applications.show', $application)
                    ->with('error', 'Only accepted applications can be marked as completed.');
            }

            $validated = $request->validate([
                'completion_message' => 'required|string|max:2000',
                'completion_attachments.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip|max:10240',
            ]);

            // Handle completion attachments
            $completionAttachments = [];
            if ($request->hasFile('completion_attachments')) {
                try {
                    foreach ($request->file('completion_attachments') as $file) {
                        $path = $file->store('completion_attachments', 'private');
                        $completionAttachments[] = [
                            'original_name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'size' => $file->getSize(),
                            'mime_type' => $file->getMimeType()
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to upload completion attachments', [
                        'application_id' => $application->id,
                        'error' => $e->getMessage()
                    ]);
                    return back()->with('error', 'Failed to upload attachments: ' . $e->getMessage());
                }
            }

            // Update application with completion details
            $application->update([
                'status' => 'pending_completion',
                'completion_message' => $validated['completion_message'],
                'completion_attachments' => !empty($completionAttachments) ? $completionAttachments : null,
                'completed_at' => now(),
            ]);

            Log::info('Application status updated to pending_completion', [
                'application_id' => $application->id,
                'job_id' => $application->job_id,
                'freelancer_id' => $freelancer->id,
                'attachments_count' => count($completionAttachments)
            ]);

            // Enhanced email notification to client with better error handling
            $emailResult = $this->sendCompletionNotificationEnhanced($application);

            // Prepare success message based on email status
            if ($emailResult['success']) {
                $message = 'Job completion has been submitted successfully! The client has been notified via email and can now review your work.';
                $alertType = 'success';
            } else {
                $message = 'Job completion has been submitted successfully! However, we were unable to send an email notification to the client.';
                if (!empty($emailResult['error'])) {
                    $message .= ' Error: ' . $emailResult['error'];
                }
                $alertType = 'warning';
                
                // Log the detailed error for debugging
                Log::warning('Email notification failed during job completion', [
                    'application_id' => $application->id,
                    'email_error' => $emailResult['error'] ?? 'Unknown error',
                    'email_debug' => $emailResult['debug'] ?? []
                ]);
            }

            return redirect()->route('freelancer.applications.show', $application)
                ->with($alertType, $message);

        } catch (\Exception $e) {
            Log::error('Error in JobApplicationController@submitCompletion', [
                'application_id' => $application->id ?? 'unknown',
                'freelancer_id' => $freelancer->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to submit job completion: ' . $e->getMessage());
        }
    }

    /**
     * ENHANCED: Send completion notification to client with comprehensive debugging
     */
    private function sendCompletionNotificationEnhanced(Application $application): array
    {
        try {
            // Step 1: Load necessary relationships
            Log::info('Starting completion notification process', [
                'application_id' => $application->id,
                'step' => 'load_relationships'
            ]);

            if (!$application->relationLoaded('job')) {
                $application->load(['job.client', 'freelancer']);
            } elseif (!$application->job->relationLoaded('client')) {
                $application->job->load('client');
            }

            if (!$application->relationLoaded('freelancer')) {
                $application->load('freelancer');
            }

            // Step 2: Validate application data
            if (!$application->job) {
                Log::error('Completion email failed: Job not found', [
                    'application_id' => $application->id,
                    'step' => 'validate_job'
                ]);
                return [
                    'success' => false, 
                    'error' => 'Job not found',
                    'debug' => ['job_exists' => false]
                ];
            }

            if (!$application->job->client) {
                Log::error('Completion email failed: Client not found', [
                    'application_id' => $application->id,
                    'job_id' => $application->job_id,
                    'step' => 'validate_client'
                ]);
                return [
                    'success' => false, 
                    'error' => 'Client not found',
                    'debug' => ['client_exists' => false, 'job_id' => $application->job_id]
                ];
            }

            if (!$application->job->client->email) {
                Log::error('Completion email failed: Client email not found', [
                    'application_id' => $application->id,
                    'client_id' => $application->job->client->id,
                    'step' => 'validate_client_email'
                ]);
                return [
                    'success' => false, 
                    'error' => 'Client email not found',
                    'debug' => [
                        'client_id' => $application->job->client->id,
                        'client_email' => 'null'
                    ]
                ];
            }

            if (!$application->freelancer) {
                Log::error('Completion email failed: Freelancer not found', [
                    'application_id' => $application->id,
                    'freelancer_id' => $application->freelancer_id,
                    'step' => 'validate_freelancer'
                ]);
                return [
                    'success' => false, 
                    'error' => 'Freelancer not found',
                    'debug' => ['freelancer_exists' => false]
                ];
            }

            // Step 3: Validate email configuration
            Log::info('Validating email configuration', [
                'application_id' => $application->id,
                'step' => 'validate_email_config'
            ]);

            $configValidation = $this->validateEmailConfigEnhanced();
            if (!$configValidation['valid']) {
                Log::error('Completion email failed: Email configuration invalid', [
                    'application_id' => $application->id,
                    'config_error' => $configValidation['error'],
                    'step' => 'validate_email_config'
                ]);
                return [
                    'success' => false, 
                    'error' => 'Email configuration error: ' . $configValidation['error'],
                    'debug' => $configValidation['debug']
                ];
            }

            $clientEmail = $application->job->client->email;

            // Step 4: Check if JobCompletionNotification class exists
            if (!class_exists('App\Mail\JobCompletionNotification')) {
                Log::error('Completion email failed: JobCompletionNotification class not found', [
                    'application_id' => $application->id,
                    'step' => 'check_mail_class'
                ]);
                return [
                    'success' => false, 
                    'error' => 'JobCompletionNotification mail class not found',
                    'debug' => ['mail_class_exists' => false]
                ];
            }

            Log::info('Attempting to send completion notification', [
                'application_id' => $application->id,
                'client_email' => $clientEmail,
                'freelancer_id' => $application->freelancer_id,
                'job_title' => $application->job->title ?? 'Unknown',
                'step' => 'send_email'
            ]);

            // Step 5: Send the email
            Mail::to($clientEmail)->send(new JobCompletionNotification($application));
            
            Log::info('Job completion notification sent successfully', [
                'application_id' => $application->id,
                'client_email' => $clientEmail,
                'step' => 'email_sent'
            ]);
            
            return [
                'success' => true,
                'debug' => [
                    'client_email' => $clientEmail,
                    'mail_driver' => config('mail.default'),
                    'timestamp' => now()->toISOString()
                ]
            ];
            
        } catch (\Swift_TransportException $e) {
            Log::error('Swift Transport Exception in completion notification', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'step' => 'swift_transport_error',
                'mail_config' => $this->getMailConfigForLogging()
            ]);
            
            return [
                'success' => false, 
                'error' => 'Email transport error: ' . $e->getMessage(),
                'debug' => $this->getMailConfigForLogging()
            ];
            
        } catch (\Exception $e) {
            Log::error('General exception in completion notification', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'step' => 'general_exception',
                'client_email' => $application->job->client->email ?? 'not available',
                'mail_config' => $this->getMailConfigForLogging()
            ]);
            
            return [
                'success' => false, 
                'error' => $e->getMessage(),
                'debug' => [
                    'exception_class' => get_class($e),
                    'mail_config' => $this->getMailConfigForLogging(),
                    'client_email' => $application->job->client->email ?? 'not available'
                ]
            ];
        }
    }

    /**
     * ENHANCED: Send completion notification to client (backward compatibility)
     */
    private function sendCompletionNotification(Application $application): bool
    {
        $result = $this->sendCompletionNotificationEnhanced($application);
        return $result['success'];
    }

    /**
     * ADDED: Send new application notification to client
     */
    private function sendNewApplicationNotification(Application $application): bool
    {
        try {
            // Load necessary relationships
            if (!$application->relationLoaded('job')) {
                $application->load(['job.client', 'freelancer']);
            } elseif (!$application->job->relationLoaded('client')) {
                $application->job->load('client');
            }

            // Validate client exists
            if (!$application->job->client) {
                Log::error('New application email failed: Client not found', [
                    'application_id' => $application->id,
                    'job_id' => $application->job_id
                ]);
                return false;
            }

            // Validate client email
            if (!$application->job->client->email) {
                Log::error('New application email failed: Client email not found', [
                    'application_id' => $application->id,
                    'client_id' => $application->job->client->id
                ]);
                return false;
            }

            // Validate email configuration
            $this->validateEmailConfig();

            $clientEmail = $application->job->client->email;

            Log::info('Attempting to send new application notification', [
                'application_id' => $application->id,
                'job_id' => $application->job_id,
                'client_email' => $clientEmail,
                'freelancer_id' => $application->freelancer_id,
                'mail_driver' => config('mail.default')
            ]);

            // Send the email
            Mail::to($clientEmail)->send(new NewJobApplicationNotification($application));

            Log::info('New application notification sent successfully', [
                'application_id' => $application->id,
                'client_email' => $clientEmail
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send new application notification', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'client_email' => $application->job->client->email ?? 'not available',
                'mail_config' => $this->getMailConfigForLogging()
            ]);

            return false;
        }
    }

    /**
     * ENHANCED: Validate email configuration with detailed debugging
     */
    private function validateEmailConfigEnhanced(): array
    {
        try {
            $mailDriver = config('mail.default');
            
            if (!$mailDriver) {
                return [
                    'valid' => false,
                    'error' => 'Mail driver not configured',
                    'debug' => ['mail_default' => 'null']
                ];
            }
            
            if (!config('mail.from.address')) {
                return [
                    'valid' => false,
                    'error' => 'Mail FROM address not configured',
                    'debug' => ['from_address' => 'null', 'driver' => $mailDriver]
                ];
            }
            
            if (!config('mail.from.name')) {
                return [
                    'valid' => false,
                    'error' => 'Mail FROM name not configured',
                    'debug' => ['from_name' => 'null', 'driver' => $mailDriver]
                ];
            }
            
            // Validate specific driver configuration
            switch ($mailDriver) {
                case 'smtp':
                    if (!config('mail.mailers.smtp.host')) {
                        return [
                            'valid' => false,
                            'error' => 'SMTP host not configured',
                            'debug' => ['smtp_host' => 'null']
                        ];
                    }
                    if (!config('mail.mailers.smtp.port')) {
                        return [
                            'valid' => false,
                            'error' => 'SMTP port not configured',
                            'debug' => ['smtp_port' => 'null']
                        ];
                    }
                    if (!config('mail.mailers.smtp.username')) {
                        return [
                            'valid' => false,
                            'error' => 'SMTP username not configured',
                            'debug' => ['smtp_username' => 'null']
                        ];
                    }
                    if (!config('mail.mailers.smtp.password')) {
                        return [
                            'valid' => false,
                            'error' => 'SMTP password not configured',
                            'debug' => ['smtp_password' => 'null']
                        ];
                    }
                    break;
                    
                case 'sendmail':
                    if (!config('mail.mailers.sendmail.path')) {
                        return [
                            'valid' => false,
                            'error' => 'Sendmail path not configured',
                            'debug' => ['sendmail_path' => 'null']
                        ];
                    }
                    break;
                    
                case 'log':
                    // Log driver doesn't need additional validation
                    break;
                    
                default:
                    return [
                        'valid' => false,
                        'error' => "Unsupported mail driver: {$mailDriver}",
                        'debug' => ['unsupported_driver' => $mailDriver]
                    ];
            }
            
            return [
                'valid' => true,
                'debug' => $this->getMailConfigForLogging()
            ];
            
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'error' => 'Exception during config validation: ' . $e->getMessage(),
                'debug' => ['exception' => $e->getMessage()]
            ];
        }
    }

    /**
     * ADDED: Validate email configuration (backward compatibility)
     */
    private function validateEmailConfig(): void
    {
        $result = $this->validateEmailConfigEnhanced();
        if (!$result['valid']) {
            throw new \Exception($result['error']);
        }
    }
    
    /**
     * ENHANCED: Get mail configuration for logging (without sensitive data)
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
            'timeout' => config("mail.mailers.{$mailDriver}.timeout"),
        ];
    }

    /**
     * Download a completion attachment
     */
    public function downloadCompletionAttachment(Application $application, $index)
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;

        if ($application->freelancer_id !== $freelancer->id) {
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

    /**
     * Download an attachment file
     */
    public function downloadAttachment(Application $application, $index)
    {
        $user = Auth::user();
        $freelancer = $user->freelancer;

        if ($application->freelancer_id !== $freelancer->id) {
            abort(403, 'Unauthorized access to this file.');
        }

        $attachments = $application->attachments ?: [];
        
        if (!isset($attachments[$index])) {
            return redirect()->back()->with('error', 'Attachment not found.');
        }

        $attachment = $attachments[$index];
        
        if (!isset($attachment['path']) || !Storage::disk('private')->exists($attachment['path'])) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return Storage::disk('private')->download(
            $attachment['path'], 
            $attachment['original_name'] ?? 'attachment'
        );
    }

    /**
     * ENHANCED: Test email functionality with better debugging
     */
    public function testEmail(Application $application)
    {
        try {
            $user = Auth::user();
            $freelancer = $user->freelancer;

            if ($application->freelancer_id !== $freelancer->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access to this application.'
                ]);
            }

            $application->load(['job.client', 'freelancer']);

            if (!$application->job->client || !$application->job->client->email) {
                return response()->json([
                    'success' => false,
                    'error' => 'Client or client email not found',
                    'debug' => [
                        'client_exists' => !is_null($application->job->client),
                        'client_email' => $application->job->client->email ?? 'null'
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
            Mail::raw('This is a test email from KONEK freelancer system. If you receive this, email configuration is working properly.', function ($message) use ($application) {
                $message->to($application->job->client->email)
                        ->subject('Test Email - KONEK Freelancer System');
            });

            // Also test the actual notifications
            Mail::to($application->job->client->email)->send(new NewJobApplicationNotification($application));

            return response()->json([
                'success' => true,
                'message' => 'Test emails sent successfully to ' . $application->job->client->email,
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

    /**
     * NEW: Test completion email functionality
     */
    public function testCompletionEmail(Application $application)
    {
        try {
            $user = Auth::user();
            $freelancer = $user->freelancer;

            if ($application->freelancer_id !== $freelancer->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access to this application.'
                ]);
            }

            // Use the enhanced notification method
            $result = $this->sendCompletionNotificationEnhanced($application);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Test completion email sent successfully!' : 'Failed to send test email',
                'error' => $result['error'] ?? null,
                'debug' => $result['debug'] ?? []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'debug' => [
                    'exception_class' => get_class($e),
                    'config' => $this->getMailConfigForLogging()
                ]
            ]);
        }
    }

    /**
     * NEW: Debug email configuration endpoint
     */
    public function debugEmailConfig()
    {
        try {
            $configValidation = $this->validateEmailConfigEnhanced();
            
            return response()->json([
                'config_valid' => $configValidation['valid'],
                'config_error' => $configValidation['error'] ?? null,
                'config_debug' => $configValidation['debug'] ?? [],
                'mail_config' => $this->getMailConfigForLogging(),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'exception_class' => get_class($e)
            ]);
        }
    }
}