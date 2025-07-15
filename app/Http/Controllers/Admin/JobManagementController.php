<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use App\Services\JobService;
use App\Http\Requests\Job\UpdateJobRequest;

class JobManagementController extends Controller
{
    protected $jobService;

    public function __construct(JobService $jobService)
    {
        $this->jobService = $jobService;
    }

    /**
     * Display a listing of the jobs.
     */
    public function index(Request $request)
    {
        $jobs = Job::with('category', 'client')->paginate(15);
        return view('admin.jobs.index', compact('jobs'));
    }

    /**
     * Display the specified job.
     */
    public function show(Job $job)
    {
        $job->load('category', 'client', 'skills');
        return view('admin.jobs.show', compact('job'));
    }

    /**
     * Show the form for editing the specified job.
     */
    public function edit(Job $job)
    {
        $job->load('category', 'client', 'skills');
        return view('admin.jobs.edit', compact('job'));
    }

    /**
     * Update the specified job in storage.
     */
    public function update(UpdateJobRequest $request, Job $job)
    {
        $job->update($request->validated());
        // Optionally update skills, etc.
        return redirect()->route('admin.jobs.index')->with('success', 'Job updated successfully.');
    }

    /**
     * Remove the specified job from storage.
     */
    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route('admin.jobs.index')->with('success', 'Job deleted successfully.');
    }

    /**
     * Toggle the status of a job (e.g., published/paused/closed).
     */
    public function toggleStatus(Job $job)
    {
        $job->status = $job->status === 'published' ? 'paused' : 'published';
        $job->save();
        return back()->with('success', 'Job status updated.');
    }
} 