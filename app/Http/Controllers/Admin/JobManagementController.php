<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Job;
use App\Models\User;
use App\Services\JobService;
use Illuminate\Http\Request;

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
        $clients = User::where('role', 'client')->where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.jobs.edit', compact('job', 'clients', 'categories'));
    }

    /**
     * Update the specified job in storage.
     */
    public function update(Request $request, Job $job)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:30',
            'requirements' => 'required|string|min:20',
            'client_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:full-time,part-time,contract,internship',
            'experience_level' => 'required|in:entry,intermediate,expert',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'budget_type' => 'required|in:hourly,fixed,negotiable',
            'status' => 'required|in:draft,published,paused,closed,cancelled',
            'deadline' => 'nullable|date',
            'max_applications' => 'nullable|integer|min:1',
        ]);

        $job->update($validated);

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
