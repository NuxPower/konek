<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidateCMUEmail
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user && !str_ends_with($user->email, '@cmu.edu.ph')) {
            abort(403, 'Only CMU email addresses are allowed.');
        }
        return $next($request);
    }
} 