@php
    $role = auth()->user()->role;
    $navigation = match ($role) {
        'admin' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin/dashboard', 'caption' => 'Platform overview'],
            ['label' => 'Users', 'route' => 'admin.users.index', 'pattern' => 'admin/users*', 'caption' => 'Accounts and access'],
            ['label' => 'Jobs', 'route' => 'admin.jobs.index', 'pattern' => 'admin/jobs*', 'caption' => 'Opportunity moderation'],
            ['label' => 'Applications', 'route' => 'admin.applications.index', 'pattern' => 'admin/applications*', 'caption' => 'Hiring activity'],
            ['label' => 'Reports', 'route' => 'admin.reports.index', 'pattern' => 'admin/reports*', 'caption' => 'Platform data'],
            ['label' => 'Activity logs', 'route' => 'admin.activity-logs.index', 'pattern' => 'admin/activity-logs*', 'caption' => 'System history'],
        ],
        'client' => [
            ['label' => 'Dashboard', 'route' => 'client.dashboard', 'pattern' => 'client/dashboard', 'caption' => 'Workspace overview'],
            ['label' => 'My jobs', 'route' => 'client.jobs.index', 'pattern' => 'client/jobs*', 'caption' => 'Manage opportunities'],
            ['label' => 'Applications', 'route' => 'client.applications.index', 'pattern' => 'client/applications*', 'caption' => 'Review candidates'],
            ['label' => 'Profile', 'route' => 'client.profile.show', 'pattern' => 'client/profile*', 'caption' => 'Public information'],
        ],
        default => [
            ['label' => 'Dashboard', 'route' => 'freelancer.dashboard', 'pattern' => 'freelancer/dashboard', 'caption' => 'Your overview'],
            ['label' => 'Browse jobs', 'route' => 'freelancer.jobs.index', 'pattern' => 'freelancer/jobs*', 'caption' => 'Find opportunities'],
            ['label' => 'Saved jobs', 'route' => 'freelancer.saved-jobs.index', 'pattern' => 'freelancer/saved-jobs*', 'caption' => 'Opportunities to revisit'],
            ['label' => 'My applications', 'route' => 'freelancer.applications.index', 'pattern' => 'freelancer/applications*', 'caption' => 'Track your progress'],
            ['label' => 'Profile', 'route' => 'freelancer.profile.show', 'pattern' => 'freelancer/profile*', 'caption' => 'Experience and contact'],
        ],
    };
    $navigation[] = [
        'label' => 'Notifications',
        'route' => 'notifications.index',
        'pattern' => 'notifications*',
        'caption' => 'Application and job updates',
    ];
@endphp

<aside class="app-sidebar">
    <div class="sidebar-title">{{ $role === 'freelancer' ? 'Talent workspace' : ucfirst($role).' workspace' }}</div>
    <ul class="sidebar-nav">
        @foreach($navigation as $item)
            <li>
                <a href="{{ route($item['route']) }}" class="{{ request()->is($item['pattern']) ? 'active' : '' }}">
                    <span class="sidebar-nav-marker"></span>
                    <span>
                        <span class="block">{{ $item['label'] }}</span>
                        <span class="sidebar-nav-caption">{{ $item['caption'] }}</span>
                    </span>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="mt-auto px-3 pt-10">
        <a href="{{ route('profile.edit') }}" class="block rounded-xl border border-slate-200 bg-white p-3 hover:border-emerald-200 hover:bg-emerald-50">
            <p class="truncate text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
            <p class="mt-1 truncate text-xs text-slate-400">Account settings</p>
        </a>
    </div>
</aside>
