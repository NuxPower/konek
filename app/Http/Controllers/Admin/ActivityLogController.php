<?php

// ActivityLogController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        // This assumes you have an activity log system in place
        // You might want to use a package like spatie/laravel-activitylog
        
        $query = DB::table('activity_logs')
                   ->join('users', 'activity_logs.user_id', '=', 'users.id')
                   ->select('activity_logs.*', 'users.name as user_name', 'users.email as user_email');

        // Apply filters
        if ($request->has('user_id') && $request->user_id !== '') {
            $query->where('activity_logs.user_id', $request->user_id);
        }

        if ($request->has('action') && $request->action !== '') {
            $query->where('activity_logs.action', $request->action);
        }

        if ($request->has('date_from') && $request->date_from !== '') {
            $query->where('activity_logs.created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to !== '') {
            $query->where('activity_logs.created_at', '<=', $request->date_to);
        }

        $activities = $query->latest('activity_logs.created_at')->paginate(20);

        return view('admin.activity-logs.index', compact('activities'));
    }

    public function show($id)
    {
        $activity = DB::table('activity_logs')
                      ->join('users', 'activity_logs.user_id', '=', 'users.id')
                      ->select('activity_logs.*', 'users.name as user_name', 'users.email as user_email')
                      ->where('activity_logs.id', $id)
                      ->first();

        if (!$activity) {
            abort(404);
        }

        return view('admin.activity-logs.show', compact('activity'));
    }

    public function clear(Request $request)
    {
        $days = $request->get('days', 30);
        
        DB::table('activity_logs')
          ->where('created_at', '<', now()->subDays($days))
          ->delete();

        return back()->with('success', "Activity logs older than {$days} days have been cleared.");
    }
}