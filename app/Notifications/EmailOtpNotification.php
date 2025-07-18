<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailOtpNotification extends Notification
{
    use Queueable;

    public function __construct(private string $otp) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your CMU Freelance Verification Code')
            ->line('Your 6-digit verification code is: ' . $this->otp)
            ->line('This code will expire in 10 minutes.')
            ->line('If you did not request this code, please ignore this email.');
    }
}