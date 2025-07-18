<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        // Load relationships based on user role
        if ($user->role === 'freelancer') {
            // Load freelancer profile and its skills
            $user->load(['freelancer.skills', 'applications' => function($query) {
                $query->where('status', 'completed')->with('job');
            }]);
        } elseif ($user->role === 'client') {
            // Load client profile
            $user->load(['client']);
        }
        
        return view('profile.public.show', compact('user'));
    }
}