<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $action;

    /**
     * Create a new message instance.
     */
    public function __construct(Application $application, string $action)
    {
        $this->application = $application;
        $this->action = $action; // 'approved' or 'revision_requested'
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->action === 'approved' 
            ? 'Job Completed Successfully - ' . $this->application->job->title
            : 'Revision Requested - ' . $this->application->job->title;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view = $this->action === 'approved' 
            ? 'emails.job-approved'
            : 'emails.revision-requested';

        return new Content(
            view: $view,
            with: [
                'application' => $this->application,
                'freelancer' => $this->application->freelancer,
                'client' => $this->application->job->client,
                'job' => $this->application->job,
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