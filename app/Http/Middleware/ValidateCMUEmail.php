<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateCMUEmail
{
    public function handle(Request $request, Closure $next)
    {
        // CMU email validation logic will go here
        return $next($request);
    }
} 