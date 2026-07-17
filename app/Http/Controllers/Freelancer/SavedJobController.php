<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavedJobController extends Controller
{
    public function index(Request $request): View
    {
        $jobs = $request->user()
            ->savedJobs()
            ->with(['category', 'client', 'skills'])
            ->where('jobs.status', 'published')
            ->where(fn ($query) => $query->whereNull('deadline')->orWhere('deadline', '>', now()))
            ->latest('saved_jobs.created_at')
            ->paginate(12);

        return view('freelancer.saved-jobs.index', compact('jobs'));
    }

    public function store(Request $request, Job $job): RedirectResponse
    {
        abort_unless(
            $job->client_id !== $request->user()->id
                && $job->status === 'published'
                && (! $job->deadline || $job->deadline->isFuture()),
            404
        );

        $request->user()->savedJobs()->syncWithoutDetaching([$job->id]);

        return back()->with('success', 'Job saved for later.');
    }

    public function destroy(Request $request, Job $job): RedirectResponse
    {
        $request->user()->savedJobs()->detach($job->id);

        return back()->with('success', 'Job removed from saved jobs.');
    }
}
