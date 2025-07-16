<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Services\ApplicationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
        $applications = Application::whereHas('job', function ($q) use ($request) {
            $q->where('client_id', $request->user()->id);
        })->with('job', 'freelancer')->paginate(15);
        return view('client.applications.index', compact('applications'));
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
        $this->authorize('update', $application);
        $request->validate([
            'status' => 'required|in:pending,reviewing,shortlisted,rejected,accepted,withdrawn',
        ]);
        $application->status = $request->input('status');
        $application->save();
        return back()->with('success', 'Application status updated.');
    }

    /**
     * Add notes to an application.
     */
    public function addNotes(Request $request, Application $application)
    {
        $this->authorize('update', $application);
        $request->validate([
            'client_notes' => 'required|string|max:2000',
        ]);
        $application->client_notes = $request->input('client_notes');
        $application->save();
        return back()->with('success', 'Notes added to application.');
    }
} 