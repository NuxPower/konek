<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/dashboard?verified=1');
        }
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }
        return redirect()->intended('/dashboard?verified=1');
    }
} 