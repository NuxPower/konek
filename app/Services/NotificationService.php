<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\InAppNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send a notification to a user or users.
     */
    public function send(User|array|Collection $users, string $message, array $data = []): void
    {
        $recipients = $users instanceof User ? collect([$users]) : collect($users);

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send(
            $recipients,
            new InAppNotification($data['title'] ?? 'KONEK update', $message, $data)
        );
    }

    /**
     * Send a notification to a single user.
     */
    public function sendToUser(User $user, string $message, array $data = []): void
    {
        $this->send($user, $message, $data);
    }

    /**
     * Send a notification to multiple users.
     */
    public function sendToMany(array $users, string $message, array $data = []): void
    {
        $this->send($users, $message, $data);
    }
}
