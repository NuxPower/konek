@php
    $role = auth()->user()->role;
    $navigation = match ($role) {
        'admin' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin/dashboard', 'caption' => 'Platform overview', 'icon' => 'dashboard'],
            ['label' => 'Users', 'route' => 'admin.users.index', 'pattern' => 'admin/users*', 'caption' => 'Accounts and access', 'icon' => 'users'],
            ['label' => 'ID Proof', 'route' => 'admin.identity.index', 'pattern' => 'admin/identity-verifications*', 'caption' => 'Verification review', 'icon' => 'profile'],
            ['label' => 'Jobs', 'route' => 'admin.jobs.index', 'pattern' => 'admin/jobs*', 'caption' => 'Opportunity moderation', 'icon' => 'jobs'],
            ['label' => 'Applications', 'route' => 'admin.applications.index', 'pattern' => 'admin/applications*', 'caption' => 'Hiring activity', 'icon' => 'applications'],
            ['label' => 'Reports', 'route' => 'admin.reports.index', 'pattern' => 'admin/reports*', 'caption' => 'Platform data', 'icon' => 'reports'],
            ['label' => 'Activity logs', 'route' => 'admin.activity-logs.index', 'pattern' => 'admin/activity-logs*', 'caption' => 'System history', 'icon' => 'activity'],
        ],
        default => [
            ['label' => 'Dashboard', 'route' => 'member.dashboard', 'pattern' => 'member/dashboard', 'caption' => 'Hiring and work overview', 'icon' => 'dashboard'],
            ['label' => 'Browse jobs', 'route' => 'member.jobs.index', 'pattern' => 'member/jobs*', 'caption' => 'Find opportunities', 'icon' => 'jobs'],
            ['label' => 'My posted jobs', 'route' => 'member.posted-jobs.index', 'pattern' => 'member/posted-jobs*', 'caption' => 'Manage work requests', 'icon' => 'posted'],
            ['label' => 'My applications', 'route' => 'member.applications.index', 'pattern' => 'member/applications*', 'caption' => 'Track submitted applications', 'icon' => 'applications'],
            ['label' => 'Saved jobs', 'route' => 'member.saved-jobs.index', 'pattern' => 'member/saved-jobs*', 'caption' => 'Opportunities to revisit', 'icon' => 'saved'],
            ['label' => 'Profile', 'route' => 'member.profile.show', 'pattern' => 'member/profile*', 'caption' => 'Student identity', 'icon' => 'profile'],
            ['label' => 'ID Proof', 'route' => 'member.identity.edit', 'pattern' => 'member/identity*', 'caption' => 'Verification score', 'icon' => 'profile'],
        ],
    };
    $navigation[] = [
        'label' => 'Notifications',
        'route' => 'notifications.index',
        'pattern' => 'notifications*',
        'caption' => 'Application and job updates',
        'icon' => 'notifications',
    ];
@endphp

<aside class="app-sidebar">
    <div class="sidebar-title">{{ $role === 'admin' ? 'Admin workspace' : 'Student workspace' }}</div>
    <ul class="sidebar-nav">
        @foreach($navigation as $item)
            <li>
                <a href="{{ route($item['route']) }}" class="{{ request()->is($item['pattern']) ? 'active' : '' }}">
                    <span class="sidebar-nav-icon">
                        <x-nav-icon :name="$item['icon'] ?? 'circle'" />
                    </span>
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
