<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application welcome/landing page.
     */
    public function index(Request $request)
    {
        return view('welcome');
    }
} 