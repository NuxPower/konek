<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * Display a listing of the activity logs.
     */
    public function index(Request $request)
    {
        $logs = ActivityLog::with('causer')->orderByDesc('created_at')->paginate(20);

        return view('admin.activity-logs.index', compact('logs'));
    }
}
