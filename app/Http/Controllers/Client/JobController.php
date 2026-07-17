<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Job\StoreJobRequest;
use App\Http\Requests\Job\UpdateJobRequest;
use App\Models\Category;
use App\Models\Job;
use App\Models\Skill;
use App\Services\JobService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

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
        $jobs = $request->user()->jobs()
            ->with('category')
            ->withCount('applications')
            ->latest()
            ->paginate(15);

        return view('client.jobs.index', compact('jobs'));
    }

    /**
     * Show the form for creating a new job.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $skills = Skill::where('is_active', true)->orderBy('name')->get();

        return view('client.jobs.create', compact('categories', 'skills'));
    }

    /**
     * Store a newly created job in storage.
     */
    public function store(StoreJobRequest $request)
    {
        $data = $request->validated();
        $submitAction = $data['submit_action'];
        unset($data['submit_action']);

        $data['client_id'] = $request->user()->id;
        $data['is_featured'] = false;
        $data['status'] = $submitAction === 'publish' ? 'published' : 'draft';
        $data['published_at'] = $submitAction === 'publish' ? now() : null;

        $job = $this->jobService->createJob($data);

        return redirect()
            ->route('member.posted-jobs.show', $job)
            ->with('success', $submitAction === 'publish' ? 'Job published successfully.' : 'Draft saved successfully.');
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
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $skills = Skill::where('is_active', true)->orderBy('name')->get();

        return view('client.jobs.edit', compact('job', 'categories', 'skills'));
    }

    /**
     * Update the specified job in storage.
     */
    public function update(UpdateJobRequest $request, Job $job)
    {
        $this->authorize('update', $job);
        $data = $request->validated();
        $this->jobService->updateJob($job, $data);

        return redirect()->route('member.posted-jobs.show', $job)->with('success', 'Job updated successfully.');
    }

    /**
     * Remove the specified job from storage.
     */
    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);
        $job->delete();

        return redirect()->route('member.posted-jobs.index')->with('success', 'Job deleted successfully.');
    }

    /**
     * Toggle the status of a job (e.g., published/paused/closed).
     */
    public function updateStatus(Request $request, Job $job)
    {
        $this->authorize('update', $job);

        $validated = $request->validate([
            'status' => 'required|in:draft,published,paused,closed,cancelled',
        ]);

        $this->jobService->changeStatus($job, $validated['status']);

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
        $newJob->published_at = null;
        $newJob->applications_count = 0;
        $newJob->title = $job->title.' (Copy)';
        $newJob->save();
        $newJob->skills()->sync(
            $job->skills->mapWithKeys(fn ($skill) => [
                $skill->id => ['proficiency_required' => $skill->pivot->proficiency_required],
            ])->all()
        );

        return redirect()->route('member.posted-jobs.edit', $newJob)->with('success', 'Job duplicated.');
    }
}
