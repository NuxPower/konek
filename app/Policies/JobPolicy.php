<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;

class JobPolicy
{
    public function view(User $user, Job $job): bool
    {
        return $user->role === 'admin' || $job->client_id === $user->id;
    }

    public function update(User $user, Job $job): bool
    {
        return $user->role === 'admin' || $job->client_id === $user->id;
    }

    public function delete(User $user, Job $job): bool
    {
        return $user->role === 'admin' || $job->client_id === $user->id;
    }

    public function apply(User $user, Job $job): bool
    {
        return $user->role === 'member'
            && $job->client_id !== $user->id
            && $job->status === 'published'
            && (! $job->deadline || $job->deadline->isFuture())
            && (! $job->max_applications || Application::where('job_id', $job->id)->where('status', '!=', 'withdrawn')->count() < $job->max_applications)
            && ! Application::where('job_id', $job->id)
                ->where('freelancer_id', $user->id)
                ->exists();
    }
}
