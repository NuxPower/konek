<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Job;
use Illuminate\Http\Request;

class JobBrowseController extends Controller
{
    /**
     * Display a listing of jobs for freelancers (authenticated).
     */
    public function index(Request $request)
    {
        return $this->renderJobBoard($request);
    }

    /**
     * Display the specified job for freelancers (authenticated).
     */
    public function show(Request $request, Job $job)
    {
        abort_unless($job->status === 'published' && $job->client_id !== $request->user()->id, 404);

        $job->load('category', 'client', 'skills');
        $job->loadExists([
            'savedByUsers as is_saved' => fn ($query) => $query->where('users.id', $request->user()->id),
        ]);

        return view('freelancer.jobs.show', compact('job'));
    }

    /**
     * Search jobs for freelancers (authenticated).
     */
    public function search(Request $request)
    {
        return $this->renderJobBoard($request);
    }

    private function renderJobBoard(Request $request)
    {
        $query = Job::query()
            ->where('status', 'published')
            ->where('client_id', '!=', $request->user()->id)
            ->with(['category', 'client', 'skills'])
            ->withExists([
                'savedByUsers as is_saved' => fn ($builder) => $builder->where('users.id', $request->user()->id),
            ]);

        if ($request->filled('q')) {
            $search = $request->string('q')->trim();
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('client', fn ($client) => $client->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('skills', fn ($skill) => $skill->where('name', 'like', "%{$search}%"));
            });
        }

        $query
            ->when($request->filled('category'), fn ($builder) => $builder->where('category_id', $request->integer('category')))
            ->when($request->filled('type'), fn ($builder) => $builder->where('type', $request->string('type')->toString()))
            ->when($request->filled('experience'), fn ($builder) => $builder->where('experience_level', $request->string('experience')->toString()));

        match ($request->string('sort')->toString()) {
            'deadline' => $query->orderByRaw('deadline IS NULL, deadline ASC'),
            'budget' => $query->orderByDesc('budget_max'),
            default => $query->latest(),
        };

        $jobs = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('freelancer.jobs.index', compact('jobs', 'categories'));
    }
}
