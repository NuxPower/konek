<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\ApplicationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    use AuthorizesRequests;

    protected $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    /**
     * Display a listing of the applications for the client's jobs.
     */
    public function index(Request $request)
    {
        $query = Application::whereHas('job', function ($builder) use ($request) {
            $builder->where('client_id', $request->user()->id);
        })->with('job', 'freelancer');

        $query
            ->when($request->filled('status'), fn ($builder) => $builder->where('status', $request->string('status')->toString()))
            ->when($request->filled('job'), fn ($builder) => $builder->where('job_id', $request->integer('job')));

        $applications = $query->latest()->paginate(15)->withQueryString();
        $jobs = $request->user()->jobs()->orderBy('title')->get(['id', 'title']);

        return view('client.applications.index', compact('applications', 'jobs'));
    }

    /**
     * Display the specified application.
     */
    public function show(Application $application)
    {
        $this->authorize('view', $application);
        $application->load('job', 'freelancer');

        return view('client.applications.show', compact('application'));
    }

    /**
     * Update the status of an application.
     */
    public function updateStatus(Request $request, Application $application)
    {
        $this->authorize('review', $application);
        $request->validate([
            'status' => 'required|in:reviewing,shortlisted,rejected,accepted',
        ]);
        $this->applicationService->changeStatus($application, $request->string('status')->toString());

        return back()->with('success', 'Application status updated.');
    }

    /**
     * Add notes to an application.
     */
    public function addNotes(Request $request, Application $application)
    {
        $this->authorize('review', $application);
        $request->validate([
            'client_notes' => 'nullable|string|max:2000',
        ]);
        $application->client_notes = $request->input('client_notes');
        $application->save();

        return back()->with('success', 'Notes added to application.');
    }
}
