<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\UpdateApplicationRequest;
use App\Models\Application;
use App\Services\ApplicationService;
use Illuminate\Http\Request;

class ApplicationManagementController extends Controller
{
    protected $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    /**
     * Display a listing of the applications.
     */
    public function index(Request $request)
    {
        $applications = Application::with('job', 'freelancer')->paginate(15);

        return view('admin.applications.index', compact('applications'));
    }

    /**
     * Display the specified application.
     */
    public function show(Application $application)
    {
        $application->load('job', 'freelancer');

        return view('admin.applications.show', compact('application'));
    }

    /**
     * Show the form for editing the specified application.
     */
    public function edit(Application $application)
    {
        $application->load('job', 'freelancer');

        return view('admin.applications.edit', compact('application'));
    }

    /**
     * Update the specified application in storage.
     */
    public function update(UpdateApplicationRequest $request, Application $application)
    {
        $application->update($request->validated());

        return redirect()->route('admin.applications.index')->with('success', 'Application updated successfully.');
    }
}
