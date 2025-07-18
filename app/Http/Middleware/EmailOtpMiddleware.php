<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EmailOtpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip OTP check for certain routes
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        // Check if user is in the middle of OTP verification
        if (session('login.id') && !Auth::check()) {
            return redirect()->route('email-otp.challenge');
        }

        return $next($request);
    }

    /**
     * Determine if the middleware should be skipped for this request.
     */
    protected function shouldSkip(Request $request): bool
    {
        $skipRoutes = [
            'email-otp.challenge',
            'email-otp.verify',
            'email-otp.resend',
            'email-otp.cancel',
            'login',
            'register',
            'password.request',
            'password.email',
            'password.reset',
            'password.store',
            'verification.notice',
            'verification.verify',
            'verification.send',
            'logout',
        ];

        return in_array($request->route()?->getName(), $skipRoutes) ||
               $request->routeIs('password.*') ||
               $request->routeIs('verification.*');
    }
}