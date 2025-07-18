<?php
// JobBrowseController.php
namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Skill;
use App\Models\JobView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobBrowseController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::active()->with(['client', 'skills']);

        // Apply filters
        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('skills') && !empty($request->skills)) {
            $query->whereHas('skills', function($q) use ($request) {
                $q->whereIn('skills.id', $request->skills);
            });
        }

        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }

        if ($request->has('experience_level') && $request->experience_level !== '') {
            $query->where('experience_level', $request->experience_level);
        }

        // Fixed budget filtering to work with budget_min and budget_max
        if ($request->has('budget_min') && $request->budget_min !== '') {
            $requestedMin = (float) $request->budget_min;
            $query->where(function($q) use ($requestedMin) {
                $q->where('budget_max', '>=', $requestedMin)
                  ->orWhere(function($subQ) use ($requestedMin) {
                      $subQ->whereNull('budget_max')
                           ->where('budget_min', '>=', $requestedMin);
                  });
            });
        }

        if ($request->has('budget_max') && $request->budget_max !== '') {
            $requestedMax = (float) $request->budget_max;
            $query->where(function($q) use ($requestedMax) {
                $q->where('budget_min', '<=', $requestedMax)
                  ->orWhere(function($subQ) use ($requestedMax) {
                      $subQ->whereNull('budget_min')
                           ->where('budget_max', '<=', $requestedMax);
                  });
            });
        }

        if ($request->has('posted_within') && $request->posted_within !== '') {
            $days = (int) $request->posted_within;
            $query->where('created_at', '>=', now()->subDays($days));
        }

        // Fixed sort options for budget_min/budget_max structure
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'budget_high':
                // Sort by highest budget (use budget_max if available, otherwise budget_min)
                $query->orderByRaw('COALESCE(budget_max, budget_min, 0) DESC');
                break;
            case 'budget_low':
                // Sort by lowest budget (use budget_min if available, otherwise budget_max)
                $query->orderByRaw('COALESCE(budget_min, budget_max, 0) ASC');
                break;
            case 'deadline':
                $query->orderBy('deadline', 'asc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $jobs = $query->paginate(12);

        // Get skills for filter
        $skills = Skill::all();

        // Get freelancer's skills for job matching
        $freelancer = Auth::user()->freelancer;
        $freelancerSkills = $freelancer ? $freelancer->skills->pluck('id') : collect();

        return view('freelancer.jobs.browse', compact('jobs', 'skills', 'freelancerSkills'));
    }

    public function show(Job $job)
    {
        $job->load(['client', 'skills', 'applications']);

        // Record job view
        $this->recordJobView($job);

        // Check if freelancer has already applied
        $hasApplied = false;
        $application = null;
        
        if (Auth::user()->freelancer) {
            $application = $job->applications()
                             ->where('freelancer_id', Auth::user()->freelancer->id)
                             ->first();
            $hasApplied = $application !== null;
        }

        // Get similar jobs
        $similarJobs = Job::active()
                         ->where('id', '!=', $job->id)
                         ->whereHas('skills', function($q) use ($job) {
                             $q->whereIn('skills.id', $job->skills->pluck('id'));
                         })
                         ->with(['client', 'skills'])
                         ->take(4)
                         ->get();

        // Calculate job match percentage
        $matchPercentage = $this->calculateJobMatch($job);

        return view('freelancer.jobs.show', compact(
            'job', 'hasApplied', 'application', 'similarJobs', 'matchPercentage'
        ));
    }

    private function recordJobView(Job $job)
    {
        if (Auth::check()) {
            JobView::firstOrCreate([
                'job_id' => $job->id,
                'user_id' => Auth::id(),
            ], [
                'ip_address' => request()->ip(),
            ]);
        }
    }

    private function calculateJobMatch(Job $job)
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            return 0;
        }

        $jobSkills = $job->skills->pluck('id');
        $freelancerSkills = $freelancer->skills->pluck('id');
        
        $matchingSkills = $jobSkills->intersect($freelancerSkills);
        
        if ($jobSkills->isEmpty()) {
            return 0;
        }

        $skillMatch = ($matchingSkills->count() / $jobSkills->count()) * 100;
        
        // Factor in experience level match
        $experienceMatch = 0;
        if ($job->experience_level === $freelancer->experience_level) {
            $experienceMatch = 100;
        } elseif (
            ($job->experience_level === 'entry' && $freelancer->experience_level === 'intermediate') ||
            ($job->experience_level === 'intermediate' && $freelancer->experience_level === 'expert')
        ) {
            $experienceMatch = 75;
        } else {
            $experienceMatch = 25;
        }

        // Weighted average (60% skills, 40% experience)
        return round(($skillMatch * 0.6) + ($experienceMatch * 0.4));
    }

    public function saved(Request $request)
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            return redirect()->route('freelancer.jobs.browse')
                           ->with('error', 'Please complete your freelancer profile first.');
        }

        // This would need to be implemented based on your saved jobs system
        // For now, we'll return an empty collection
        $savedJobs = collect();

        return view('freelancer.jobs.saved', compact('savedJobs'));
    }

    public function saveJob(Job $job)
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            return response()->json(['error' => 'Freelancer profile not found'], 404);
        }

        // This would typically create a saved job record
        // You might want to implement a saved_jobs table

        return response()->json(['success' => true, 'message' => 'Job saved successfully']);
    }

    public function unsaveJob(Job $job)
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            return response()->json(['error' => 'Freelancer profile not found'], 404);
        }

        // This would typically remove a saved job record

        return response()->json(['success' => true, 'message' => 'Job removed from saved jobs']);
    }
}