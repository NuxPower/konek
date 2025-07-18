<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\Freelancer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['client', 'freelancer']);

        // Apply filters
        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('student_id', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_clients' => User::where('role', 'client')->count(),
            'total_freelancers' => User::where('role', 'freelancer')->count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'client', 'freelancer'])],
            'student_id' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'year_level' => ['nullable', 'integer', 'min:1', 'max:4'],
            'phone' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'student_id' => $validated['student_id'],
            'department' => $validated['department'],
            'year_level' => $validated['year_level'],
            'phone' => $validated['phone'],
            'bio' => $validated['bio'],
            'is_active' => $request->has('is_active'),
            'email_verified_at' => now(), // Auto-verify admin created users
            'email_otp_attempts' => 0,
            'two_factor_enabled' => false,
        ]);

        return redirect()->route('admin.users')
                        ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        // Safely load relationships
        try {
            $user->load(['client', 'freelancer']);
        } catch (\Exception $e) {
            // Continue without loading relationships if there are issues
        }
        
        $stats = [];
        
        if ($user->role === 'client' && $user->client) {
            $stats = [
                'jobs_posted' => $user->client->jobs()->count(),
                'active_jobs' => $user->client->jobs()->where('status', 'published')->count(),
                'total_spent' => $user->client->total_spent ?? 0,
                'rating' => $user->client->rating ?? 0,
            ];
        } elseif ($user->role === 'freelancer' && $user->freelancer) {
            $stats = [
                'applications_sent' => $user->freelancer->applications()->count(),
                'jobs_completed' => $user->freelancer->total_jobs_completed ?? 0,
                'total_earned' => $user->freelancer->total_earnings ?? 0,
                'rating' => $user->freelancer->rating ?? 0,
            ];
        }

        return view('admin.users.show', compact('user', 'stats'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'role' => ['required', Rule::in(['admin', 'client', 'freelancer'])],
            'student_id' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'year_level' => ['nullable', 'integer', 'min:1', 'max:4'],
            'phone' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'student_id' => $validated['student_id'],
            'department' => $validated['department'],
            'year_level' => $validated['year_level'],
            'phone' => $validated['phone'],
            'bio' => $validated['bio'],
            'is_active' => $request->has('is_active'),
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($validated['password'])
            ]);
        }

        return redirect()->route('admin.users.show', $user)
                        ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting the current admin
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                           ->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting the last admin
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('admin.users')
                           ->with('error', 'Cannot delete the last admin user.');
        }

        $user->delete();

        return redirect()->route('admin.users')
                        ->with('success', 'User deleted successfully.');
    }

    public function activate(User $user)
    {
        $user->update(['is_active' => true]);
        
        return back()->with('success', 'User activated successfully.');
    }

    public function deactivate(User $user)
    {
        $user->update(['is_active' => false]);
        
        return back()->with('success', 'User deactivated successfully.');
    }

    public function verifyClient(User $user)
    {
        if ($user->role === 'client' && $user->client) {
            $user->client->update(['is_verified' => true]);
            return back()->with('success', 'Client verified successfully.');
        }
        
        return back()->with('error', 'User is not a client.');
    }

    public function verifyFreelancer(User $user)
    {
        if ($user->role === 'freelancer' && $user->freelancer) {
            $user->freelancer->update(['is_verified' => true]);
            return back()->with('success', 'Freelancer verified successfully.');
        }
        
        return back()->with('error', 'User is not a freelancer.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
            'action' => 'required|in:activate,deactivate,delete',
        ]);

        $users = User::whereIn('id', $request->users);

        switch ($request->action) {
            case 'activate':
                $users->update(['is_active' => true]);
                $message = 'Users activated successfully.';
                break;
            case 'deactivate':
                $users->update(['is_active' => false]);
                $message = 'Users deactivated successfully.';
                break;
            case 'delete':
                // Prevent deleting current admin or last admin
                $userList = $users->get();
                $currentAdminId = auth()->id();
                $adminCount = User::where('role', 'admin')->count();
                
                foreach ($userList as $user) {
                    if ($user->id === $currentAdminId) {
                        return back()->with('error', 'Cannot delete your own account.');
                    }
                    if ($user->role === 'admin' && $adminCount <= 1) {
                        return back()->with('error', 'Cannot delete the last admin user.');
                    }
                }
                
                $users->delete();
                $message = 'Users deleted successfully.';
                break;
        }

        return back()->with('success', $message);
    }
}