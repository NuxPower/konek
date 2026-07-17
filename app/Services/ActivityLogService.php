<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogService
{
    /**
     * Log a user activity.
     */
    public function log(User $user, string $description, array $properties = []): void
    {
        ActivityLog::create([
            'log_name' => 'default',
            'description' => $description,
            'causer_type' => get_class($user),
            'causer_id' => $user->id,
            'properties' => $properties,
        ]);
    }
}
