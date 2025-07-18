<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct(Application $application, string $status)
    {
        $this->application = $application;
        $this->status = $status; // 'accepted', 'rejected', 'shortlisted'
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusMessages = [
            'accepted' => 'Application Accepted',
            'rejected' => 'Application Update',
            'shortlisted' => 'Application Shortlisted'
        ];

        $subject = ($statusMessages[$this->status] ?? 'Application Update') . ' - ' . $this->application->job->title;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // For now, we'll use a simple view that works with any status
        return new Content(
            view: 'emails.application-status',
            with: [
                'application' => $this->application,
                'job' => $this->application->job,
                'freelancer' => $this->application->freelancer->user, // Access user for email/name
                'client' => $this->application->job->client,
                'status' => $this->status,
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