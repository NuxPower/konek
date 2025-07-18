<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Skill;
use App\Models\Application;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            Log::info('JobController@index called', ['user_id' => $user->id]);
            
            // Simple query without complex relationships first
            $jobs = Job::where('client_id', $user->id)
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);
            
            Log::info('Jobs fetched', ['count' => $jobs->count()]);
            
            return view('client.jobs.index', compact('jobs'));
        } catch (\Exception $e) {
            Log::error('Error in JobController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->view('errors.500', ['error' => $e->getMessage()], 500);
        }
    }

    public function show(Job $job)
    {
        try {
            $user = Auth::user();
            Log::info('JobController@show called', [
                'job_id' => $job->id,
                'user_id' => $user->id,
                'job_client_id' => $job->client_id
            ]);
            
            // Check ownership
            if ($user->id !== $job->client_id) {
                Log::warning('Unauthorized access attempt', [
                    'user_id' => $user->id,
                    'job_client_id' => $job->client_id
                ]);
                abort(403, 'Unauthorized action.');
            }
            
            // Simple stats without complex relationships
            $stats = [
                'applications_count' => Application::where('job_id', $job->id)->count(),
                'pending_applications' => Application::where('job_id', $job->id)->where('status', 'pending')->count(),
                'accepted_applications' => Application::where('job_id', $job->id)->where('status', 'accepted')->count(),
                'views_count' => 0,
            ];
            
            // Load basic relationships
            $job->load('skills');
            $job->applications = Application::where('job_id', $job->id)->get();
            
            Log::info('Job data prepared', [
                'job_id' => $job->id,
                'stats' => $stats,
                'skills_count' => $job->skills->count(),
                'applications_count' => $job->applications->count()
            ]);
            
            return view('client.jobs.show', compact('job', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error in JobController@show', [
                'job_id' => $job->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('client.jobs.index')
                ->with('error', 'Error loading job details: ' . $e->getMessage());
        }
    }

    public function edit(Job $job)
    {
        try {
            $user = Auth::user();
            Log::info('JobController@edit called', [
                'job_id' => $job->id,
                'user_id' => $user->id
            ]);
            
            // Check ownership
            if ($user->id !== $job->client_id) {
                abort(403, 'Unauthorized action.');
            }
            
            $skills = Skill::all();
            $job->load('skills');
            
            Log::info('Edit data prepared', [
                'job_id' => $job->id,
                'skills_count' => $skills->count(),
                'job_skills_count' => $job->skills->count()
            ]);
            
            return view('client.jobs.edit', compact('job', 'skills'));
        } catch (\Exception $e) {
            Log::error('Error in JobController@edit', [
                'job_id' => $job->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('client.jobs.index')
                ->with('error', 'Error loading job for editing: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            Log::info('JobController@create called');
            
            $categories = Category::where('is_active', true)->orderBy('name')->get();
            $skills = Skill::all();
            
            Log::info('Create data prepared', [
                'categories_count' => $categories->count(),
                'skills_count' => $skills->count()
            ]);
            
            return view('client.jobs.create', compact('skills', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error in JobController@create', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('client.jobs.index')
                ->with('error', 'Error loading job creation form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('JobController@store called', ['request_data' => $request->all()]);
            
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'deadline' => 'required|date|after:today',
                'type' => 'required|in:fixed,hourly',
                'budget_min' => 'nullable|numeric|min:0',
                'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
                'experience_level' => 'required|in:entry,intermediate,expert',
                'duration' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'skills' => 'nullable|string',
            ]);

            $user = Auth::user();
            
            // Get or create category
            $category = Category::where('name', $request->category)->first();
            if (!$category) {
                $category = Category::create([
                    'name' => $request->category,
                    'slug' => Str::slug($request->category),
                    'is_active' => true
                ]);
            }

            // Create job
            $job = Job::create([
                'title' => $request->title,
                'description' => $request->description,
                'deadline' => $request->deadline,
                'type' => $request->type,
                'budget_min' => $request->budget_min,
                'budget_max' => $request->budget_max,
                'experience_level' => $request->experience_level,
                'duration' => $request->duration,
                'status' => 'open',
                'client_id' => $user->id,
                'category_id' => $category->id,
            ]);

            // Process skills
            if ($request->skills) {
                $skillNames = array_map('trim', explode(',', $request->skills));
                $skillNames = array_filter($skillNames);
                
                $skillIds = [];
                foreach ($skillNames as $skillName) {
                    if (!empty($skillName)) {
                        $skill = Skill::firstOrCreate(
                            ['name' => $skillName],
                            [
                                'name' => $skillName,
                                'slug' => Str::slug($skillName),
                                'is_active' => true
                            ]
                        );
                        $skillIds[] = $skill->id;
                    }
                }
                
                if (!empty($skillIds)) {
                    $job->skills()->attach($skillIds);
                }
            }

            Log::info('Job created successfully', ['job_id' => $job->id]);

            return redirect()->route('client.jobs.show', $job)
                            ->with('success', 'Job posted successfully!');

        } catch (\Exception $e) {
            Log::error('Error in JobController@store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error creating job: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, Job $job)
    {
        try {
            Log::info('JobController@update called', ['job_id' => $job->id]);
            
            // Check ownership
            if (Auth::user()->id !== $job->client_id) {
                abort(403, 'Unauthorized action.');
            }
            
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'deadline' => 'required|date|after:today',
                'type' => 'required|in:fixed,hourly',
                'budget_min' => 'nullable|numeric|min:0',
                'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
                'experience_level' => 'required|in:entry,intermediate,expert',
                'duration' => 'required|string|max:255',
                'skills' => 'nullable|string',
            ]);

            $job->update($validatedData);

            // Process skills
            if ($request->skills) {
                $skillNames = array_map('trim', explode(',', $request->skills));
                $skillNames = array_filter($skillNames);
                
                $skillIds = [];
                foreach ($skillNames as $skillName) {
                    if (!empty($skillName)) {
                        $skill = Skill::firstOrCreate(
                            ['name' => $skillName],
                            [
                                'name' => $skillName,
                                'slug' => Str::slug($skillName),
                                'is_active' => true
                            ]
                        );
                        $skillIds[] = $skill->id;
                    }
                }
                
                $job->skills()->sync($skillIds);
            } else {
                $job->skills()->detach();
            }

            Log::info('Job updated successfully', ['job_id' => $job->id]);

            return redirect()->route('client.jobs.show', $job)
                            ->with('success', 'Job updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error in JobController@update', [
                'job_id' => $job->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error updating job: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Job $job)
    {
        try {
            // Check ownership
            if (Auth::user()->id !== $job->client_id) {
                abort(403, 'Unauthorized action.');
            }
            
            $job->delete();
            
            return redirect()->route('client.jobs.index')
                            ->with('success', 'Job deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error in JobController@destroy', [
                'job_id' => $job->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error deleting job: ' . $e->getMessage());
        }
    }

    public function duplicate(Job $job)
    {
        try {
            // Check ownership
            if (Auth::user()->id !== $job->client_id) {
                abort(403, 'Unauthorized action.');
            }
            
            $newJob = $job->replicate();
            $newJob->title = $job->title . ' (Copy)';
            $newJob->status = 'draft';
            $newJob->created_at = now();
            $newJob->save();

            // Copy skills
            if ($job->skills) {
                $newJob->skills()->attach($job->skills->pluck('id'));
            }

            Log::info('Job duplicated successfully', [
                'original_job_id' => $job->id,
                'new_job_id' => $newJob->id
            ]);

            return redirect()->route('client.jobs.edit', $newJob)
                            ->with('success', 'Job duplicated successfully!');
        } catch (\Exception $e) {
            Log::error('Error in JobController@duplicate', [
                'job_id' => $job->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error duplicating job: ' . $e->getMessage());
        }
    }

    public function close(Job $job)
    {
        try {
            // Check ownership
            if (Auth::user()->id !== $job->client_id) {
                abort(403, 'Unauthorized action.');
            }
            
            $job->update(['status' => 'closed']);
            
            return back()->with('success', 'Job closed successfully!');
        } catch (\Exception $e) {
            Log::error('Error in JobController@close', [
                'job_id' => $job->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Error closing job: ' . $e->getMessage());
        }
    }

    public function reopen(Job $job)
    {
        try {
            // Check ownership
            if (Auth::user()->id !== $job->client_id) {
                abort(403, 'Unauthorized action.');
            }
            
            $job->update(['status' => 'open']);
            
            return back()->with('success', 'Job reopened successfully!');
        } catch (\Exception $e) {
            Log::error('Error in JobController@reopen', [
                'job_id' => $job->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Error reopening job: ' . $e->getMessage());
        }
    }
}