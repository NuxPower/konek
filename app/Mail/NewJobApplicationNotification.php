<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewJobApplicationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

    /**
     * Create a new message instance.
     */
    public function __construct(Application $application)
    {
        // Ensure all necessary relationships are loaded
        $this->application = $application->load([
            'job',
            'job.client', 
            'freelancer',
            'freelancer.user',
            'freelancer.skills'
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Application for: ' . $this->application->job->title,
            from: config('mail.from.address', 'noreply@konek-freelance.com'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-job-application',
            with: [
                'application' => $this->application,
                'job' => $this->application->job,
                'freelancer' => $this->application->freelancer,
                'client' => $this->application->job->client,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}