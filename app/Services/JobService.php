<?php

namespace App\Services;

use App\Models\Job;

class JobService
{
    /**
     * Create a new job posting.
     */
    public function createJob(array $data): Job
    {
        return Job::create($data);
    }

    /**
     * Update an existing job posting.
     */
    public function updateJob(Job $job, array $data): Job
    {
        $job->update($data);
        return $job;
    }

    /**
     * Change the status of a job.
     */
    public function changeStatus(Job $job, string $status): Job
    {
        $job->status = $status;
        $job->save();
        return $job;
    }

    /**
     * Sync skills for a job.
     */
    public function syncSkills(Job $job, array $skills): void
    {
        $job->skills()->sync($skills);
    }
} 