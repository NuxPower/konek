<?php

namespace App\Services;

use App\Models\Application;

class ApplicationService
{
    /**
     * Submit a new application.
     */
    public function submitApplication(array $data): Application
    {
        return Application::create($data);
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
        $application->status = $status;
        $application->save();
        return $application;
    }
} 