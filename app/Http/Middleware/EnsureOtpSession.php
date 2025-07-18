<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if there's a valid OTP session
        $userId = session('login.id');
        $timestamp = session('login.timestamp');
        
        if (!$userId || !$timestamp) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        // Check if session is older than 30 minutes (1800 seconds)
        if (now()->timestamp - $timestamp > 1800) {
            session()->forget(['login.id', 'login.remember', 'login.timestamp']);
            return redirect()->route('login')
                ->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        // Check if user still exists
        $user = \App\Models\User::find($userId);
        if (!$user) {
            session()->forget(['login.id', 'login.remember', 'login.timestamp']);
            return redirect()->route('login')
                ->withErrors(['email' => 'User not found. Please log in again.']);
        }

        // Check if user is still active
        if (isset($user->is_active) && !$user->is_active) {
            session()->forget(['login.id', 'login.remember', 'login.timestamp']);
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been deactivated. Please contact support.']);
        }

        return $next($request);
    }
}