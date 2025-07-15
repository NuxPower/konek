<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\RegisterRequest;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration form.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function store(RegisterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        Auth::login($user);
        return redirect()->route('dashboard');
    }
} 