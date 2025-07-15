<?php

namespace App\Services;

use App\Models\User;

class NotificationService
{
    /**
     * Send a notification to a user or users.
     */
    public function send(User|array $users, string $message, array $data = []): void
    {
        // Stub: Replace with real notification logic
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