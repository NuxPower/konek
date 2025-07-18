<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log the activity before processing the request
        $this->logActivity($request);

        $response = $next($request);

        // You can also log after the response if needed
        // $this->logResponse($request, $response);

        return $response;
    }

    /**
     * Log the user activity
     */
    private function logActivity(Request $request): void
    {
        $user = Auth::user();
        
        $logData = [
            'user_id' => $user ? $user->id : null,
            'user_email' => $user ? $user->email : null,
            'user_role' => $user ? $user->role : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route_name' => $request->route() ? $request->route()->getName() : null,
            'timestamp' => now()->toISOString(),
        ];

        // Log to Laravel log file
        Log::channel('activity')->info('User Activity', $logData);

        // Optional: Store in database
        // You can create an ActivityLog model and store this data
        // ActivityLog::create($logData);
    }

    /**
     * Log the response (optional)
     */
    private function logResponse(Request $request, Response $response): void
    {
        if ($response->getStatusCode() >= 400) {
            Log::channel('activity')->warning('HTTP Error Response', [
                'status_code' => $response->getStatusCode(),
                'url' => $request->fullUrl(),
                'user_id' => optional(Auth::user())->id,
            ]);
        }
    }
}