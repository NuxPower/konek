<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has any of the required roles
        if (!in_array($user->role, $roles)) {
            // Redirect based on user's actual role
            return $this->redirectBasedOnRole($user->role);
        }

        return $next($request);
    }

    /**
     * Redirect user based on their role
     */
    protected function redirectBasedOnRole(string $role): Response
    {
        return match($role) {
            'admin' => redirect()->route('admin.dashboard')
                ->with('error', 'You do not have permission to access that page.'),
            'client' => redirect()->route('client.dashboard')
                ->with('error', 'You do not have permission to access that page.'),
            'freelancer' => redirect()->route('freelancer.dashboard')
                ->with('error', 'You do not have permission to access that page.'),
            default => redirect()->route('home')
                ->with('error', 'You do not have permission to access that page.'),
        };
    }
}