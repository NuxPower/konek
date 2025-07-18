<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCMUEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only validate on registration
        if ($request->routeIs('register') && $request->isMethod('POST')) {
            $email = $request->input('email');
            
            if ($email && !str_ends_with(strtolower($email), '@cmu.edu.ph')) {
                return back()->withInput()->withErrors([
                    'email' => 'You must use your official CMU email address ending with @cmu.edu.ph'
                ]);
            }

            // Additional validation for CMU email format
            if ($email && !$this->isValidCMUEmail($email)) {
                return back()->withInput()->withErrors([
                    'email' => 'Please enter a valid CMU email address format.'
                ]);
            }
        }

        return $next($request);
    }

    /**
     * Validate CMU email format
     */
    private function isValidCMUEmail(string $email): bool
    {
        // Basic email format validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Must end with @cmu.edu.ph
        if (!str_ends_with(strtolower($email), '@cmu.edu.ph')) {
            return false;
        }

        // Get the local part (before @)
        $localPart = substr($email, 0, strrpos($email, '@'));
        
        // Local part should not be empty and should contain valid characters
        if (empty($localPart) || strlen($localPart) > 64) {
            return false;
        }

        // Check for valid characters in local part
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $localPart)) {
            return false;
        }

        // Should not start or end with special characters
        if (str_starts_with($localPart, '.') || str_ends_with($localPart, '.') ||
            str_starts_with($localPart, '-') || str_ends_with($localPart, '-') ||
            str_starts_with($localPart, '_') || str_ends_with($localPart, '_')) {
            return false;
        }

        // Should not have consecutive special characters
        if (preg_match('/[._-]{2,}/', $localPart)) {
            return false;
        }

        return true;
    }
}