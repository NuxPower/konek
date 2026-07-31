<?php

namespace App\Services;

use App\Models\Job;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $job = DB::transaction(function () use ($data) {
            $skills = $data['skills'] ?? [];
            unset($data['skills']);

            $job = Job::create($data);
            $this->syncSkills($job, $skills);

            return $job;
        });

        $this->notifyJobPostedWebhook($job);

        return $job;
    }

    /**
     * Notify the n8n webhook that a job was posted. Best-effort: a down or
     * unconfigured webhook must never block job creation.
     */
    private function notifyJobPostedWebhook(Job $job): void
    {
        $url = config('services.n8n.new_job_webhook');

        if (! $url) {
            return;
        }

        try {
            $request = Http::timeout(3)->acceptJson();
            $token = config('services.n8n.webhook_token');

            if ($token) {
                $request = $request->withHeader('X-Konek-Webhook-Token', $token);
            }

            $request->post($url, [
                'event' => 'job.created',
                'job_id' => $job->id,
                'title' => $job->title,
                'posted_by' => $job->client?->name,
                'status' => $job->status,
                'url' => route('member.jobs.show', $job),
            ]);
        } catch (\Throwable $e) {
            Log::warning('n8n new-job webhook failed', ['error' => $e->getMessage()]);
        }
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
                            'url' => route('member.applications.show', $application),
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
