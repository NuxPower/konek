@php
    $routeName = request()->route()?->getName() ?? '';
    $role = auth()->user()->role;
    $homeRoute = $role.'.dashboard';
    $parts = explode('.', $routeName);
    $isAccountSettings = str_starts_with($routeName, 'profile.');
    $section = str_starts_with($routeName, 'notifications.')
        ? 'notifications'
        : ($isAccountSettings ? 'account' : ($parts[1] ?? null));
    $action = end($parts);
    $sectionLabels = [
        'users' => 'Users',
        'jobs' => $role === 'client' ? 'My jobs' : 'Jobs',
        'applications' => $role === 'freelancer' ? 'My applications' : 'Applications',
        'reports' => 'Reports',
        'activity-logs' => 'Activity logs',
        'profile' => 'Profile',
        'dashboard' => 'Dashboard',
        'account' => 'Account settings',
        'notifications' => 'Notifications',
        'saved-jobs' => 'Saved jobs',
    ];
    $actionLabels = [
        'create' => 'Create',
        'edit' => 'Edit',
        'show' => 'Details',
    ];
    $sectionRoute = match ($section) {
        'users' => 'admin.users.index',
        'jobs' => $role.'.jobs.index',
        'applications' => $role.'.applications.index',
        'reports' => 'admin.reports.index',
        'activity-logs' => 'admin.activity-logs.index',
        'profile' => $role.'.profile.show',
        'notifications' => 'notifications.index',
        'saved-jobs' => 'freelancer.saved-jobs.index',
        default => null,
    };
@endphp

@if($section && $section !== 'dashboard')
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route($homeRoute) }}">Home</a>
        <span>/</span>
        @if($sectionRoute && in_array($action, ['create', 'edit', 'show'], true))
            <a href="{{ route($sectionRoute) }}">{{ $sectionLabels[$section] ?? ucfirst($section) }}</a>
            <span>/</span>
            <span>{{ $actionLabels[$action] }}</span>
        @else
            <span>{{ $sectionLabels[$section] ?? ucfirst($section) }}</span>
        @endif
    </nav>
@endif
