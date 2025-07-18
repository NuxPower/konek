<?php

// JobManagementController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;

class JobManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::with(['client.user', 'skills']);

        // Apply filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $jobs = $query->latest()->paginate(15);

        return view('admin.jobs.index', compact('jobs'));
    }

    public function show(Job $job)
    {
        $job->load(['client.user', 'skills', 'applications.freelancer.user']);
        
        $stats = [
            'applications_count' => $job->applications()->count(),
            'pending_applications' => $job->applications()->pending()->count(),
            'accepted_applications' => $job->applications()->accepted()->count(),
            'rejected_applications' => $job->applications()->rejected()->count(),
            'views_count' => $job->views()->count(),
        ];

        return view('admin.jobs.show', compact('job', 'stats'));
    }

    public function edit(Job $job)
    {
        return view('admin.jobs.edit', compact('job'));
    }

    public function update(Request $request, Job $job)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'deadline' => 'required|date|after:today',
            'status' => 'required|in:draft,open,in_progress,completed,cancelled',
            'type' => 'required|in:fixed,hourly',
            'experience_level' => 'required|in:entry,intermediate,expert',
            'duration' => 'required|string',
        ]);

        $job->update($request->only([
            'title', 'description', 'budget', 'deadline', 'status', 
            'type', 'experience_level', 'duration'
        ]));

        return redirect()->route('admin.jobs.show', $job)
                        ->with('success', 'Job updated successfully.');
    }

    public function destroy(Job $job)
    {
        $job->delete();
        
        return redirect()->route('admin.jobs.index')
                        ->with('success', 'Job deleted successfully.');
    }

    public function approve(Job $job)
    {
        $job->update(['status' => 'open']);
        
        return back()->with('success', 'Job approved successfully.');
    }

    public function reject(Job $job)
    {
        $job->update(['status' => 'cancelled']);
        
        return back()->with('success', 'Job rejected successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'jobs' => 'required|array',
            'jobs.*' => 'exists:jobs,id',
            'action' => 'required|in:approve,reject,delete',
        ]);

        $jobs = Job::whereIn('id', $request->jobs);

        switch ($request->action) {
            case 'approve':
                $jobs->update(['status' => 'open']);
                $message = 'Jobs approved successfully.';
                break;
            case 'reject':
                $jobs->update(['status' => 'cancelled']);
                $message = 'Jobs rejected successfully.';
                break;
            case 'delete':
                $jobs->delete();
                $message = 'Jobs deleted successfully.';
                break;
        }

        return back()->with('success', $message);
    }
}