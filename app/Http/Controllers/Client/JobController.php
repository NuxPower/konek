<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use App\Services\JobService;
use App\Http\Requests\Job\StoreJobRequest;
use App\Http\Requests\Job\UpdateJobRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class JobController extends Controller
{
    use AuthorizesRequests;

    protected $jobService;

    public function __construct(JobService $jobService)
    {
        $this->jobService = $jobService;
    }

    /**
     * Display a listing of the client's jobs.
     */
    public function index(Request $request)
    {
        $jobs = $request->user()->jobs()->with('category')->paginate(15);
        return view('client.jobs.index', compact('jobs'));
    }

    /**
     * Show the form for creating a new job.
     */
    public function create()
    {
        return view('client.jobs.create');
    }

    /**
     * Store a newly created job in storage.
     */
    public function store(StoreJobRequest $request)
    {
        $data = $request->validated();
        $data['client_id'] = $request->user()->id;
        $job = Job::create($data);
        // Optionally attach skills, etc.
        return redirect()->route('client.jobs.index')->with('success', 'Job created successfully.');
    }

    /**
     * Display the specified job.
     */
    public function show(Job $job)
    {
        $this->authorize('view', $job);
        $job->load('category', 'skills');
        return view('client.jobs.show', compact('job'));
    }

    /**
     * Show the form for editing the specified job.
     */
    public function edit(Job $job)
    {
        $this->authorize('update', $job);
        $job->load('category', 'skills');
        return view('client.jobs.edit', compact('job'));
    }

    /**
     * Update the specified job in storage.
     */
    public function update(UpdateJobRequest $request, Job $job)
    {
        $this->authorize('update', $job);
        $job->update($request->validated());
        // Optionally update skills, etc.
        return redirect()->route('client.jobs.index')->with('success', 'Job updated successfully.');
    }

    /**
     * Remove the specified job from storage.
     */
    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);
        $job->delete();
        return redirect()->route('client.jobs.index')->with('success', 'Job deleted successfully.');
    }

    /**
     * Toggle the status of a job (e.g., published/paused/closed).
     */
    public function toggleStatus(Job $job)
    {
        $this->authorize('update', $job);
        $job->status = $job->status === 'published' ? 'paused' : 'published';
        $job->save();
        return back()->with('success', 'Job status updated.');
    }

    /**
     * Duplicate a job posting.
     */
    public function duplicate(Job $job)
    {
        $this->authorize('update', $job);
        $newJob = $job->replicate();
        $newJob->status = 'draft';
        $newJob->save();
        // Optionally duplicate relationships (skills, etc.)
        return redirect()->route('client.jobs.edit', $newJob)->with('success', 'Job duplicated.');
    }
} 