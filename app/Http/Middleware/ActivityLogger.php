<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ActivityLogger
{
    public function handle(Request $request, Closure $next)
    {
        // Activity logging logic will go here
        return $next($request);
    }
} 