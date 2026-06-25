<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApplicationService
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Submit a new application.
     */
    public function submitApplication(array $data): Application
    {
        return DB::transaction(function () use ($data) {
            $job = Job::query()->lockForUpdate()->findOrFail($data['job_id']);

            if ($job->status !== 'published' || ($job->deadline && $job->deadline->isPast())) {
                throw ValidationException::withMessages([
                    'job' => 'This job is no longer accepting applications.',
                ]);
            }

            $activeCount = Application::where('job_id', $job->id)
                ->where('status', '!=', 'withdrawn')
                ->count();

            if ($job->max_applications && $activeCount >= $job->max_applications) {
                throw ValidationException::withMessages([
                    'job' => 'This job has reached its application limit.',
                ]);
            }

            $application = Application::create([
                ...$data,
                'status' => 'pending',
            ]);

            $job->update(['applications_count' => $activeCount + 1]);
            $application->loadMissing(['job.client', 'freelancer']);

            $this->notificationService->sendToUser(
                $application->job->client,
                "{$application->freelancer->name} applied for {$application->job->title}.",
                [
                    'title' => 'New application received',
                    'type' => 'application',
                    'url' => route('client.applications.show', $application),
                ]
            );

            return $application;
        });
    }

    /**
     * Update an existing application.
     */
    public function updateApplication(Application $application, array $data): Application
    {
        $application->update($data);

        return $application;
    }

    /**
     * Change the status of an application.
     */
    public function changeStatus(Application $application, string $status): Application
    {
        $allowedTransitions = [
            'pending' => ['reviewing', 'shortlisted', 'rejected', 'accepted'],
            'reviewing' => ['shortlisted', 'rejected', 'accepted'],
            'shortlisted' => ['reviewing', 'rejected', 'accepted'],
            'rejected' => [],
            'accepted' => [],
            'withdrawn' => [],
        ];

        if (! in_array($status, $allowedTransitions[$application->status] ?? [], true)) {
            throw ValidationException::withMessages([
                'status' => "The application cannot move from {$application->status} to {$status}.",
            ]);
        }

        $application->status = $status;
        $application->reviewed_at = now();
        $application->accepted_at = $status === 'accepted' ? now() : null;
        $application->save();
        $application->loadMissing(['job', 'freelancer']);

        $this->notificationService->sendToUser(
            $application->freelancer,
            "Your application for {$application->job->title} is now {$status}.",
            [
                'title' => $status === 'accepted' ? 'Application accepted' : 'Application status updated',
                'type' => 'application',
                'url' => route('freelancer.applications.show', $application),
            ]
        );

        return $application;
    }

    public function withdraw(Application $application): Application
    {
        return DB::transaction(function () use ($application) {
            $application = Application::query()->lockForUpdate()->findOrFail($application->id);

            if (! in_array($application->status, ['pending', 'reviewing', 'shortlisted'], true)) {
                throw ValidationException::withMessages([
                    'status' => 'This application can no longer be withdrawn.',
                ]);
            }

            $application->update(['status' => 'withdrawn']);

            $activeCount = Application::where('job_id', $application->job_id)
                ->where('status', '!=', 'withdrawn')
                ->count();

            Job::whereKey($application->job_id)->update(['applications_count' => $activeCount]);
            $application->loadMissing(['job.client', 'freelancer']);

            $this->notificationService->sendToUser(
                $application->job->client,
                "{$application->freelancer->name} withdrew their application for {$application->job->title}.",
                [
                    'title' => 'Application withdrawn',
                    'type' => 'application',
                    'url' => route('client.applications.show', $application),
                ]
            );

            return $application;
        });
    }
}
