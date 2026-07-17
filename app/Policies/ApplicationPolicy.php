<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function view(User $user, Application $application): bool
    {
        return $user->role === 'admin'
            || $application->freelancer_id === $user->id
            || $application->job()->where('client_id', $user->id)->exists();
    }

    public function update(User $user, Application $application): bool
    {
        return $user->role === 'admin'
            || ($application->freelancer_id === $user->id && $application->status === 'pending');
    }

    public function review(User $user, Application $application): bool
    {
        return $user->role === 'admin'
            || ($user->role === 'member' && $application->job()->where('client_id', $user->id)->exists());
    }

    public function withdraw(User $user, Application $application): bool
    {
        return $application->freelancer_id === $user->id
            && in_array($application->status, ['pending', 'reviewing', 'shortlisted'], true);
    }
}
