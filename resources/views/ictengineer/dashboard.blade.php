@extends('layouts.app')

@section('title', 'ICT Engineer Dashboard - Dark Fibre CRM')

@section('content')
<div class="container-fluid px-0">

    {{-- Hero Section --}}
    <div class="dashboard-hero text-white py-4 py-md-5">
        <div class="container-fluid px-3 px-sm-4 px-md-5">
            <div class="row align-items-center g-4">

                {{-- Left Column - Welcome --}}
                <div class="col-12 col-lg-8">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="hero-icon">
                            <i class="fas fa-network-wired fa-3x fa-fw"></i>
                        </div>
                        <div>
                            <h1 class="display-5 fw-bold mb-2">ICT Engineer Dashboard</h1>
                            <p class="lead mb-0 opacity-90">
                                @if(auth()->user()->county)
                                    {{ auth()->user()->county->name }} County ICT Management
                                @else
                                    System-wide ICT Infrastructure
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Meta Information --}}
                    <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
                        <span class="badge bg-white text-kp-blue px-3 py-2 rounded-pill">
                            <i class="fas fa-user me-1"></i>
                            {{ Str::limit(Auth::user()->name, 20) }}
                        </span>
                        @if(auth()->user()->county)
                            <span class="badge bg-white text-kp-blue px-3 py-2 rounded-pill">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ auth()->user()->county->name }} County
                            </span>
                        @endif
                        <span class="badge bg-white text-kp-blue px-3 py-2 rounded-pill">
                            <i class="far fa-calendar-alt me-1"></i>
                            {{ now()->format('M d, Y') }}
                        </span>
                        <span class="badge bg-success px-3 py-2 rounded-pill">
                            <i class="fas fa-circle me-1 small"></i> Active
                        </span>
                    </div>
                </div>

                {{-- Right Column - Actions --}}
                <div class="col-12 col-lg-4">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        @include('partials.role-help-widget')

                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-light btn-dashboard-action">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container-fluid px-3 px-sm-4 px-md-5 py-4">

        {{-- Key Performance Metrics --}}
        <div class="row g-4 mb-5">
            @php
                $stats = [
                    [
                        'title' => 'Active Networks',
                        'value' => $networkStats['activeNetworks'] ?? 0,
                        'icon' => 'network-wired',
                        'color' => 'success',
                        'badge' => 'Live',
                        'subtitle' => 'Currently operational'
                    ],
                    [
                        'title' => 'Pending Tickets',
                        'value' => $networkStats['pendingTickets'] ?? 0,
                        'icon' => 'ticket-alt',
                        'color' => 'warning',
                        'badge' => 'Urgent',
                        'subtitle' => 'Awaiting resolution'
                    ],
                    [
                        'title' => 'Servers Online',
                        'value' => $networkStats['serversOnline'] ?? 0,
                        'icon' => 'server',
                        'color' => 'info',
                        'badge' => 'Stable',
                        'subtitle' => 'Server infrastructure'
                    ],
                    [
                        'title' => 'Network Uptime',
                        'value' => ($networkStats['uptimePercentage'] ?? 0) . '%',
                        'icon' => 'chart-line',
                        'color' => 'primary',
                        'badge' => 'High',
                        'subtitle' => 'Last 30 days'
                    ]
                ];
            @endphp

            @foreach($stats as $stat)
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="metric-card bg-white rounded-4 shadow-sm h-100 p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="metric-icon rounded-circle bg-{{ $stat['color'] }}-light">
                                <i class="fas fa-{{ $stat['icon'] }} fa-fw text-{{ $stat['color'] }}"></i>
                            </div>
                            <span class="badge bg-{{ $stat['color'] }} rounded-pill px-3 py-1">{{ $stat['badge'] }}</span>
                        </div>
                        <h6 class="text-muted text-uppercase small fw-semibold mb-2">{{ $stat['title'] }}</h6>
                        <div class="metric-value fw-bold text-kp-blue mb-2">{{ $stat['value'] }}</div>
                        <p class="small text-muted mb-0">{{ $stat['subtitle'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Additional Metrics Row --}}
        <div class="row g-3 mb-5">
            @php
                $additionalMetrics = [
                    ['title' => 'Users Managed', 'value' => $networkStats['usersManaged'] ?? 0, 'icon' => 'users', 'color' => 'primary'],
                    ['title' => 'Devices Online', 'value' => $networkStats['devicesOnline'] ?? 0, 'icon' => 'desktop', 'color' => 'info'],
                    ['title' => 'Avg Response Time', 'value' => ($networkStats['avgResponseTime'] ?? 0) . 'ms', 'icon' => 'stopwatch', 'color' => 'warning'],
                    ['title' => 'Security Alerts', 'value' => $networkStats['securityAlerts'] ?? 0, 'icon' => 'shield-alt', 'color' => 'danger']
                ];
            @endphp

            @foreach($additionalMetrics as $metric)
                <div class="col-6 col-sm-3">
                    <div class="small-metric-card text-center p-3 rounded-4 h-100">
                        <div class="metric-icon-sm bg-{{ $metric['color'] }}-light rounded-3 mx-auto mb-2">
                            <i class="fas fa-{{ $metric['icon'] }} fa-fw text-{{ $metric['color'] }}"></i>
                        </div>
                        <div class="h3 mb-0 fw-bold text-{{ $metric['color'] }}">{{ $metric['value'] }}</div>
                        <small class="text-muted">{{ $metric['title'] }}</small>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Quick Actions Section --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 fw-bold">
                                <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                            </h4>
                            <span class="badge bg-kp-yellow text-dark rounded-pill px-3 py-2">Quick Access</span>
                        </div>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="row g-3">
                           @php
       $quickActions = [
        ['title' => 'My Requests', 'icon' => 'list', 'color' => 'primary', 'route' => 'ictengineer.requests.index', 'desc' => 'View assigned requests'],
        ['title' => 'Manage Tickets', 'icon' => 'ticket-alt', 'color' => 'warning', 'route' => 'ictengineer.tickets.index', 'desc' => 'Resolve support tickets'],
        ['title' => 'My County', 'icon' => 'map-marker-alt', 'color' => 'info', 'route' => 'ictengineer.county', 'desc' => 'County ICT management'],
        ['title' => 'All Certificates', 'icon' => 'file-contract', 'color' => 'success', 'route' => 'ictengineer.certificates.conditional.index', 'desc' => 'View all certificates'],
        ['title' => 'Network Monitor', 'icon' => 'network-wired', 'color' => 'info', 'route' => 'ictengineer.network.monitor', 'desc' => 'Monitor network'],
        ['title' => 'Reports', 'icon' => 'chart-bar', 'color' => 'warning', 'route' => 'ictengineer.reports.index', 'desc' => 'Generate reports'],
        ['title' => 'Help Desk', 'icon' => 'life-ring', 'color' => 'danger', 'route' => 'ictengineer.helpdesk', 'desc' => 'ICT support'],
        ['title' => 'Settings', 'icon' => 'cog', 'color' => 'secondary', 'route' => 'ictengineer.settings.index', 'desc' => 'System settings']
    ];
@endphp

                            @foreach($quickActions as $action)
                                <div class="col-6 col-md-4 col-lg-2">
                                    <a href="{{ route($action['route']) }}" class="action-card text-center p-3 rounded-3 border h-100 text-decoration-none d-block">
                                        <div class="action-icon bg-{{ $action['color'] }} rounded-3 mx-auto mb-2">
                                            <i class="fas fa-{{ $action['icon'] }} fa-fw"></i>
                                        </div>
                                        <h6 class="fw-semibold mb-1">{{ $action['title'] }}</h6>
                                        <small class="text-muted d-none d-md-block">{{ $action['desc'] }}</small>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Design Requests Table --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-history text-info me-2"></i>Recent Design Requests
                        </h5>
                        <a href="{{ route('ictengineer.requests.index') }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @if(isset($recentRequests) && $recentRequests->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="px-4 py-3">Request #</th>
                                            <th class="py-3">Customer</th>
                                            <th class="py-3">Title</th>
                                            <th class="py-3 d-none d-sm-table-cell">Status</th>
                                            <th class="py-3 d-none d-lg-table-cell">Assigned</th>
                                            <th class="px-4 py-3 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentRequests as $request)
                                            <tr>
                                                <td class="px-4 py-3 fw-bold text-kp-blue">#{{ $request->request_number ?? $request->id }}</td>
                                                <td class="py-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="customer-avatar bg-kp-blue-light rounded-circle">
                                                            <i class="fas fa-user fa-sm text-kp-blue"></i>
                                                        </div>
                                                        <span>{{ Str::limit($request->customer->name ?? 'N/A', 25) }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3">{{ Str::limit($request->title, 35) }}</td>
                                                <td class="py-3 d-none d-sm-table-cell">
                                                    @php
                                                        $statusColors = [
                                                            'pending' => 'secondary',
                                                            'assigned' => 'warning',
                                                            'in_progress' => 'primary',
                                                            'review' => 'info',
                                                            'completed' => 'success',
                                                            'cancelled' => 'danger'
                                                        ];
                                                        $color = $statusColors[strtolower($request->status)] ?? 'info';
                                                    @endphp
                                                    <span class="badge bg-{{ $color }} rounded-pill px-3 py-1">
                                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                                    </span>
                                                </td>
                                                <td class="py-3 d-none d-lg-table-cell">
                                                    <div class="small">
                                                        @if($request->assigned_at)
                                                            <div>{{ $request->assigned_at->format('M d, Y') }}</div>
                                                            <div class="text-muted">{{ $request->assigned_at->diffForHumans() }}</div>
                                                        @else
                                                            <span class="text-muted">Not assigned</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="btn-group gap-1">
                                                        <a href="{{ route('ictengineer.requests.show', $request) }}"
                                                           class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                                           data-bs-toggle="tooltip" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        @if(in_array(strtolower($request->status), ['assigned', 'in_progress']))
                                                            <a href="{{ route('ictengineer.requests.show', $request) }}?edit=true"
                                                               class="btn btn-sm btn-outline-warning rounded-pill px-3 d-none d-md-inline-block"
                                                               data-bs-toggle="tooltip" title="Update Status">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endif

                                                        @if(strtolower($request->status) === 'review')
                                                            <a href="{{ route('ictengineer.requests.show', $request) }}"
                                                               class="btn btn-sm btn-outline-success rounded-pill px-3 d-none d-md-inline-block"
                                                               data-bs-toggle="tooltip" title="Approve Design">
                                                                <i class="fas fa-check-circle"></i>
                                                            </a>
                                                        @endif
                                                    </div>

                                                    {{-- Mobile Dropdown --}}
                                                    <div class="dropdown d-md-none">
                                                        <button class="btn btn-sm btn-outline-secondary rounded-pill" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a class="dropdown-item" href="{{ route('ictengineer.requests.show', $request) }}">View Details</a></li>
                                                            @if(in_array(strtolower($request->status), ['assigned', 'in_progress']))
                                                                <li><a class="dropdown-item" href="{{ route('ictengineer.requests.show', $request) }}?edit=true">Update Status</a></li>
                                                            @endif
                                                            @if(strtolower($request->status) === 'review')
                                                                <li><a class="dropdown-item" href="{{ route('ictengineer.requests.show', $request) }}">Approve Design</a></li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-drafting-compass fa-4x text-muted opacity-25 mb-3"></i>
                                <h6 class="text-muted">No Design Requests Assigned</h6>
                                <p class="small text-muted">No design requests have been assigned to you yet</p>
                                <a href="{{ route('ictengineer.requests.index') }}" class="btn btn-kp-primary rounded-pill px-4">
                                    <i class="fas fa-list me-2"></i>View All Requests
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-chart-bar text-kp-blue me-2"></i>Network Performance
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center py-5">
                            <i class="fas fa-network-wired fa-4x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted">Network performance metrics would appear here</p>
                            <small class="text-muted">Connect to monitoring system for real-time data</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-chart-pie text-kp-green me-2"></i>Ticket Distribution
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center py-5">
                            <i class="fas fa-chart-pie fa-4x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted">Ticket status distribution would appear here</p>
                            <small class="text-muted">Analytics available when data is available</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- System Notifications --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-bell text-warning me-2"></i>System Notifications
                        </h5>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1">{{ $notifications->count() ?? 0 }} New</span>
                    </div>
                    <div class="card-body p-0">
                        @if(isset($notifications) && $notifications->count() > 0)
                            @foreach($notifications as $notification)
                                <div class="notification-item p-4 border-bottom">
                                    <div class="d-flex gap-3">
                                        <div class="flex-shrink-0">
                                            @switch($notification->type)
                                                @case('security')
                                                    <i class="fas fa-shield-alt fa-2x text-danger"></i>
                                                    @break
                                                @case('server')
                                                    <i class="fas fa-server fa-2x text-warning"></i>
                                                    @break
                                                @case('network')
                                                    <i class="fas fa-network-wired fa-2x text-info"></i>
                                                    @break
                                                @default
                                                    <i class="fas fa-info-circle fa-2x text-kp-blue"></i>
                                            @endswitch
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-semibold mb-1">{{ $notification->title }}</h6>
                                            <p class="text-muted small mb-2">{{ $notification->message }}</p>
                                            <small class="text-muted">
                                                <i class="far fa-clock me-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-bell-slash fa-4x text-muted opacity-25 mb-3"></i>
                                <h6 class="text-muted">No New Notifications</h6>
                                <p class="small text-muted">All systems are running normally</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
:root {
    --kp-blue: #0066B3;
    --kp-green: #009639;
    --kp-yellow: #FFD700;
    --kp-dark: #003f20;
}

/* Hero Section */
.dashboard-hero {
    background: linear-gradient(135deg, var(--kp-blue) 0%, var(--kp-green) 100%);
}

/* Metric Cards */
.metric-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}

.metric-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}

.metric-icon {
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.metric-value {
    font-size: 2rem;
    line-height: 1.2;
}

/* Small Metric Cards */
.small-metric-card {
    background: white;
    border: 1px solid rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.small-metric-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.metric-icon-sm {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Action Cards */
.action-card {
    transition: all 0.3s ease;
    background: white;
}

.action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-color: transparent !important;
}

.action-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

/* Customer Avatar */
.customer-avatar {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Notification Item */
.notification-item {
    transition: background 0.2s ease;
}

.notification-item:hover {
    background: #f8f9fa;
}

/* Button Styles */
.btn-dashboard-action {
    padding: 8px 20px;
    border-radius: 50px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-dashboard-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-kp-primary {
    background: var(--kp-blue);
    border-color: var(--kp-blue);
    color: white;
}

.btn-kp-primary:hover {
    background: #005499;
    border-color: #005499;
}

/* Color Classes */
.bg-primary-light { background: rgba(0, 102, 179, 0.1); }
.bg-success-light { background: rgba(0, 150, 57, 0.1); }
.bg-warning-light { background: rgba(255, 215, 0, 0.15); }
.bg-info-light { background: rgba(23, 162, 184, 0.1); }
.bg-danger-light { background: rgba(220, 53, 69, 0.1); }

.text-kp-blue { color: var(--kp-blue) !important; }
.text-kp-green { color: var(--kp-green) !important; }
.bg-kp-blue-light { background: rgba(0, 102, 179, 0.1); }

/* Table Styles */
.table th {
    font-weight: 600;
    font-size: 0.875rem;
    color: #4a5568;
}

.table td {
    vertical-align: middle;
}

/* Rounded Utilities */
.rounded-4 { border-radius: 1rem !important; }
.rounded-3 { border-radius: 0.75rem !important; }

/* Responsive Adjustments */
@media (max-width: 768px) {
    .metric-value { font-size: 1.5rem; }
    .btn-dashboard-action { padding: 6px 16px; font-size: 0.875rem; }
    .action-card { text-align: left; }
    .action-icon { margin: 0 0 0.75rem 0; }
}

@media (max-width: 576px) {
    .dashboard-hero { text-align: center; }
    .hero-icon { display: none; }
    .table-responsive { font-size: 0.875rem; }
}

@media print {
    .dashboard-hero, .action-card, .btn, .badge { display: none !important; }
    .card { border: 1px solid #ddd !important; box-shadow: none !important; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(el => new bootstrap.Tooltip(el));

    // Initialize dropdowns
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    dropdowns.forEach(el => new bootstrap.Dropdown(el));

    // Add animation to cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.metric-card, .action-card, .small-metric-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });

    // Auto-refresh dashboard every 5 minutes (optional)
    let refreshTimer;
    const startRefreshTimer = () => {
        refreshTimer = setTimeout(() => {
            window.location.reload();
        }, 5 * 60 * 1000);
    };

    const resetRefreshTimer = () => {
        if (refreshTimer) clearTimeout(refreshTimer);
        startRefreshTimer();
    };

    ['click', 'mousemove', 'keypress'].forEach(event => {
        document.addEventListener(event, resetRefreshTimer);
    });

    startRefreshTimer();
});
</script>

@endsection
