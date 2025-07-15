<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use App\Services\ApplicationService;
use App\Http\Requests\Application\StoreApplicationRequest;
use App\Http\Requests\Application\UpdateApplicationRequest;

class JobApplicationController extends Controller
{
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
        $applications = $request->user()->applications()->with('job')->paginate(15);
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
        Application::create($data);
        return redirect()->route('freelancer.applications.index')->with('success', 'Application submitted successfully.');
    }

    /**
     * Update the specified application.
     */
    public function update(UpdateApplicationRequest $request, Application $application)
    {
        $this->authorize('update', $application);
        $application->update($request->validated());
        return back()->with('success', 'Application updated successfully.');
    }

    /**
     * Remove the specified application from storage.
     */
    public function destroy(Application $application)
    {
        $this->authorize('delete', $application);
        $application->delete();
        return redirect()->route('freelancer.applications.index')->with('success', 'Application deleted successfully.');
    }
} 