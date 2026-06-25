<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogger
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check() && ! $request->isMethodSafe() && $response->getStatusCode() < 400) {
            $this->activityLogService->log(
                Auth::user(),
                $request->method().' '.$request->path(),
                [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );
        }

        return $response;
    }
}
