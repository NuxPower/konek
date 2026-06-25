<?php

namespace App\Services;

use App\Models\Job;
use Illuminate\Support\Facades\DB;

class JobService
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Create a new job posting.
     */
    public function createJob(array $data): Job
    {
        return DB::transaction(function () use ($data) {
            $skills = $data['skills'] ?? [];
            unset($data['skills']);

            $job = Job::create($data);
            $this->syncSkills($job, $skills);

            return $job;
        });
    }

    /**
     * Update an existing job posting.
     */
    public function updateJob(Job $job, array $data): Job
    {
        return DB::transaction(function () use ($job, $data) {
            $skills = $data['skills'] ?? [];
            unset($data['skills']);

            $job->update($data);
            $this->syncSkills($job, $skills);

            return $job->refresh();
        });
    }

    /**
     * Change the status of a job.
     */
    public function changeStatus(Job $job, string $status): Job
    {
        $previousStatus = $job->status;
        $job->status = $status;
        $job->published_at = $status === 'published'
            ? ($job->published_at ?? now())
            : $job->published_at;
        $job->save();

        if ($previousStatus === 'published' && in_array($status, ['closed', 'cancelled'], true)) {
            $job->loadMissing('applications.freelancer');
            $job->applications
                ->where('status', '!=', 'withdrawn')
                ->each(function ($application) use ($job, $status) {
                    if (! $application->freelancer) {
                        return;
                    }

                    $this->notificationService->sendToUser(
                        $application->freelancer,
                        "{$job->title} is now {$status}.",
                        [
                            'title' => 'Job status updated',
                            'type' => 'job',
                            'url' => route('freelancer.applications.show', $application),
                        ]
                    );
                });
        }

        return $job;
    }

    /**
     * Sync skills for a job.
     */
    public function syncSkills(Job $job, array $skills): void
    {
        $job->skills()->sync(
            collect($skills)->mapWithKeys(fn ($skillId) => [
                $skillId => ['proficiency_required' => 'basic'],
            ])->all()
        );
    }
}
