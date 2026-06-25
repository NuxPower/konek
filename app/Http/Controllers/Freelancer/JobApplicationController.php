<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\StoreApplicationRequest;
use App\Http\Requests\Application\UpdateApplicationRequest;
use App\Models\Application;
use App\Models\Job;
use App\Services\ApplicationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    use AuthorizesRequests;

    protected $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    /**
     * Display a listing of the freelancer's applications.
     */
    public function index(Request $request)
    {
        $applications = $request->user()->applications()
            ->with('job.client')
            ->latest()
            ->paginate(15);

        return view('freelancer.applications.index', compact('applications'));
    }

    /**
     * Display the specified application.
     */
    public function show(Application $application)
    {
        $this->authorize('view', $application);
        $application->load('job');

        return view('freelancer.applications.show', compact('application'));
    }

    /**
     * Show the form for applying to a job.
     */
    public function create(Job $job)
    {
        $this->authorize('apply', $job);

        return view('freelancer.applications.create', compact('job'));
    }

    /**
     * Store a new application for a job.
     */
    public function store(StoreApplicationRequest $request, Job $job)
    {
        $this->authorize('apply', $job);
        $data = $request->validated();
        $data['job_id'] = $job->id;
        $data['freelancer_id'] = $request->user()->id;
        $application = $this->applicationService->submitApplication($data);

        return redirect()->route('freelancer.applications.show', $application)->with('success', 'Application submitted successfully.');
    }

    /**
     * Update the specified application.
     */
    public function update(UpdateApplicationRequest $request, Application $application)
    {
        $this->applicationService->updateApplication($application, $request->validated());

        return back()->with('success', 'Application updated successfully.');
    }

    /**
     * Remove the specified application from storage.
     */
    public function destroy(Application $application)
    {
        $this->authorize('withdraw', $application);
        $this->applicationService->withdraw($application);

        return redirect()->route('freelancer.applications.index')->with('success', 'Application withdrawn.');
    }
}
