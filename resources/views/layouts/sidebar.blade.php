<!-- Sidebar partial -->
<aside style="width: 220px; background: #f1f5f9; min-height: 100vh; padding: 1rem;">
    @auth
        @if(auth()->user()->role === 'admin')
            <div><strong>Admin</strong></div>
            <ul>
                <li><a href="/admin/dashboard">Dashboard</a></li>
                <li><a href="/admin/users">Users</a></li>
                <li><a href="/admin/jobs">Jobs</a></li>
                <li><a href="/admin/applications">Applications</a></li>
                <li><a href="/admin/reports">Reports</a></li>
                <li><a href="/admin/activity-logs">Activity Logs</a></li>
            </ul>
        @elseif(auth()->user()->role === 'client')
            <div><strong>Client</strong></div>
            <ul>
                <li><a href="/client/dashboard">Dashboard</a></li>
                <li><a href="/client/jobs">My Jobs</a></li>
                <li><a href="/client/applications">Applications</a></li>
                <li><a href="/client/profile">Profile</a></li>
            </ul>
        @elseif(auth()->user()->role === 'freelancer')
            <div><strong>Freelancer</strong></div>
            <ul>
                <li><a href="/freelancer/dashboard">Dashboard</a></li>
                <li><a href="/freelancer/jobs">Browse Jobs</a></li>
                <li><a href="/freelancer/applications">My Applications</a></li>
                <li><a href="/freelancer/profile">Profile</a></li>
            </ul>
        @endif
    @endauth
</aside> 