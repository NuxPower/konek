<?php
// ApplicationManagementController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with(['freelancer.user', 'job.client.user']);

        // Apply filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->whereHas('freelancer.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhereHas('job', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        $applications = $query->latest()->paginate(15);

        return view('admin.applications.index', compact('applications'));
    }

    public function show(Application $application)
    {
        $application->load(['freelancer.user', 'job.client.user']);
        
        return view('admin.applications.show', compact('application'));
    }

    public function destroy(Application $application)
    {
        $application->delete();
        
        return redirect()->route('admin.applications.index')
                        ->with('success', 'Application deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'applications' => 'required|array',
            'applications.*' => 'exists:applications,id',
            'action' => 'required|in:delete',
        ]);

        Application::whereIn('id', $request->applications)->delete();

        return back()->with('success', 'Applications deleted successfully.');
    }
}