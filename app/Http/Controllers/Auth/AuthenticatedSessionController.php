<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        protected EmailOtpService $emailOtpService
    ) {}

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            // Authenticate the user
            $request->authenticate();

            $user = Auth::user();

            // Log successful authentication attempt
            Log::info('User authenticated successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            // Store user info in session for OTP verification
            session([
                'login.id' => $user->id,
                'login.remember' => $request->boolean('remember'),
                'login.timestamp' => now()->timestamp,
            ]);

            // Log the user out temporarily until OTP is verified
            Auth::logout();

            // Generate and send OTP
            if (!$this->emailOtpService->generateAndSendOtp($user)) {
                // Failed to send OTP - clear session and show error
                session()->forget(['login.id', 'login.remember', 'login.timestamp']);
                
                Log::error('Failed to send OTP email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                ]);
                
                return back()->withErrors([
                    'email' => 'Failed to send verification code to your email. Please check your email address and try again.',
                ])->withInput($request->only('email'));
            }

            // Log successful OTP generation
            Log::info('OTP sent successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            // Redirect to OTP verification page
            return redirect()->route('email-otp.challenge')
                ->with('status', 'A 6-digit verification code has been sent to your email address. Please check your inbox and enter the code to complete login.');

        } catch (\Exception $e) {
            // Log the error
            Log::error('Login process failed', [
                'error' => $e->getMessage(),
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Clear any session data that might have been set
            session()->forget(['login.id', 'login.remember', 'login.timestamp']);

            // Return with error
            return back()->withErrors([
                'email' => 'An error occurred during login. Please try again.',
            ])->withInput($request->only('email'));
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            // Clear any pending OTP data
            if (Auth::check()) {
                $this->emailOtpService->clearOtp(Auth::user());
                
                Log::info('User logged out', [
                    'user_id' => Auth::user()->id,
                    'email' => Auth::user()->email,
                    'ip' => $request->ip(),
                ]);
            }

            // Clear any pending login session data
            session()->forget(['login.id', 'login.remember', 'login.timestamp']);

            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('status', 'You have been logged out successfully.');

        } catch (\Exception $e) {
            Log::error('Logout process failed', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Force logout even if there's an error
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('error', 'An error occurred during logout, but you have been logged out.');
        }
    }

    /**
     * Check if the current login session is valid.
     */
    public function checkLoginSession(): bool
    {
        $userId = session('login.id');
        $timestamp = session('login.timestamp');
        
        if (!$userId || !$timestamp) {
            return false;
        }

        // Check if session is older than 30 minutes
        if (now()->timestamp - $timestamp > 1800) {
            session()->forget(['login.id', 'login.remember', 'login.timestamp']);
            return false;
        }

        return true;
    }
}