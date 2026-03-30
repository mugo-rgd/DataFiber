@extends('layouts.app')

@section('title', 'ICT Engineer Dashboard')

@section('content')
<div class="container-fluid px-0">
    <!-- Dashboard Header - Fully Responsive -->
    <div class="dashboard-header bg-gradient-primary py-2 py-sm-3 py-md-4">
        <div class="container-fluid px-3 px-sm-4 px-md-5">
            <div class="row align-items-center g-2 g-md-3">
                <div class="col-12 col-lg-8 mb-2 mb-lg-0">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="header-icon me-2 me-sm-3 mb-1 mb-sm-0">
                            <i class="fas fa-network-wired text-white responsive-icon"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h1 class="responsive-heading text-white mb-1">ICT Engineer Dashboard</h1>
                            <p class="mb-0 opacity-75 text-white responsive-text">
                                @if(auth()->user()->county)
                                    {{ auth()->user()->county->name }} County
                                @else
                                    System-wide ICT Management
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-1 gap-sm-2 mt-2">
                        <span class="badge bg-white text-primary responsive-badge">
                            <i class="fas fa-user me-1"></i> <span class="name-text">{{ Str::limit(Auth::user()->name, 15) }}</span>
                        </span>
                        @if(auth()->user()->county)
                        <span class="badge bg-white-20 text-white responsive-badge">
                            <i class="fas fa-map-marker-alt me-1"></i> <span class="date-text">{{ auth()->user()->county->name }} County</span>
                        </span>
                        @endif
                        <span class="badge bg-white-20 text-white responsive-badge">
                            <i class="fas fa-calendar me-1"></i> <span class="date-text">{{ now()->format('M d, Y') }}</span>
                        </span>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="d-flex flex-wrap gap-1 gap-sm-2 justify-content-start justify-content-lg-end">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline w-100 w-sm-auto">
                            @csrf
                            <button type="submit" class="btn btn-light responsive-btn w-100">
                                <i class="fas fa-sign-out-alt me-1 me-sm-2"></i>
                                <span class="btn-text">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content - Fluid Container -->
    <div class="container-fluid px-3 px-sm-4 px-md-5 py-3 py-sm-4">
        <!-- Network Statistics - Dynamic Grid -->
        <div class="row g-2 g-sm-3 g-md-4 mb-3 mb-sm-4">
            @php
                $stats = [
                    [
                        'title' => 'Active Networks',
                        'value' => $networkStats['activeNetworks'] ?? 0,
                        'icon' => 'network-wired',
                        'color' => 'success',
                        'badge' => 'Live',
                        'badge_color' => 'success',
                        'subtitle' => 'Currently operational networks'
                    ],
                    [
                        'title' => 'Pending Tickets',
                        'value' => $networkStats['pendingTickets'] ?? 0,
                        'icon' => 'ticket-alt',
                        'color' => 'warning',
                        'badge' => 'Urgent',
                        'badge_color' => 'danger',
                        'subtitle' => 'Awaiting resolution'
                    ],
                    [
                        'title' => 'Servers Online',
                        'value' => $networkStats['serversOnline'] ?? 0,
                        'icon' => 'server',
                        'color' => 'info',
                        'badge' => 'Stable',
                        'badge_color' => 'success',
                        'subtitle' => 'Active server infrastructure'
                    ],
                    [
                        'title' => 'Network Uptime',
                        'value' => ($networkStats['uptimePercentage'] ?? 0) . '%',
                        'icon' => 'chart-line',
                        'color' => 'primary',
                        'badge' => 'High',
                        'badge_color' => 'success',
                        'subtitle' => 'Last 30 days uptime'
                    ]
                ];
            @endphp

            @foreach($stats as $stat)
            <div class="col-6 col-md-6 col-lg-3 mb-2 mb-sm-3">
                <div class="stat-card bg-white rounded-lg shadow-sm border-0 h-100">
                    <div class="stat-card-body p-2 p-sm-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start mb-2 mb-sm-3 mb-md-4">
                            <div class="stat-icon bg-{{ $stat['color'] }}-light rounded-circle responsive-stat-icon">
                                <i class="fas fa-{{ $stat['icon'] }} text-{{ $stat['color'] }}"></i>
                            </div>
                            <div class="trend-indicator">
                                <span class="badge bg-{{ $stat['badge_color'] }} responsive-badge">{{ $stat['badge'] }}</span>
                            </div>
                        </div>
                        <h6 class="stat-title text-muted text-uppercase small mb-1 mb-sm-2">{{ $stat['title'] }}</h6>
                        <div class="stat-value fw-bold text-dark responsive-stat-value">{{ $stat['value'] }}</div>
                        <div class="stat-subtitle">
                            <small class="text-muted responsive-text">{{ $stat['subtitle'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Additional Metrics - Dynamic Grid -->
        <div class="row g-2 g-sm-3 g-md-4 mb-3 mb-sm-4">
            @php
                $additionalMetrics = [
                    [
                        'title' => 'Users Managed',
                        'value' => $networkStats['usersManaged'] ?? 0,
                        'icon' => 'users',
                        'color' => 'primary'
                    ],
                    [
                        'title' => 'Devices Online',
                        'value' => $networkStats['devicesOnline'] ?? 0,
                        'icon' => 'desktop',
                        'color' => 'info'
                    ],
                    [
                        'title' => 'Avg Response Time',
                        'value' => ($networkStats['avgResponseTime'] ?? 0) . 'ms',
                        'icon' => 'stopwatch',
                        'color' => 'warning'
                    ],
                    [
                        'title' => 'Security Alerts',
                        'value' => $networkStats['securityAlerts'] ?? 0,
                        'icon' => 'shield-alt',
                        'color' => 'danger'
                    ]
                ];
            @endphp

            @foreach($additionalMetrics as $metric)
            <div class="col-6 col-md-6 col-lg-3">
                <div class="card border h-100">
                    <div class="card-body text-center p-2 p-sm-3">
                        <div class="text-{{ $metric['color'] }} mb-1 mb-sm-2">
                            <i class="fas fa-{{ $metric['icon'] }} responsive-metric-icon"></i>
                        </div>
                        <div class="responsive-metric-value fw-bold mb-1">{{ $metric['value'] }}</div>
                        <small class="text-muted responsive-text">{{ $metric['title'] }}</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Quick Actions - Dynamic Grid -->
        <div class="row mb-3 mb-sm-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-2 mb-sm-3 mb-md-4">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 responsive-subheading">
                                <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                            </h5>
                            <span class="badge bg-warning d-none d-sm-inline responsive-badge">Quick Access</span>
                        </div>
                    </div>
                    <div class="card-body p-2 p-sm-3 p-md-4">
                        @php
    $actions = [
        [
            'title' => 'My Requests',
            'icon' => 'list',
            'color' => 'primary',
            'link' => route('ictengineer.requests.index'),
            'desc' => 'View all design requests assigned to you',
            'badge' => function() {
                return \App\Models\DesignRequest::where('ict_engineer_id', auth()->id())
                    // ->whereIn('status', ['assigned', 'pending'])
                    ->count();
            }
        ],
        // [
        //     'title' => 'Network Monitor',
        //     'icon' => 'desktop',
        //     'color' => 'success',
        //     'link' => route('ictengineer.network-monitor'),
        //     'desc' => 'Monitor network performance and status'
        // ],
        [
            'title' => 'Manage Tickets',
            'icon' => 'ticket-alt',
            'color' => 'warning',
            'link' => route('ictengineer.tickets'),
            'desc' => 'View and resolve support tickets',
            'badge' => function() {
                return \App\Models\Ticket::where('assigned_to', auth()->id())
                    ->where('status', 'pending')
                    ->count();
            }
        ],
        [
            'title' => 'My County',
            'icon' => 'map-marker-alt',
            'color' => 'info',
            'link' => route('ictengineer.county'),
            'desc' => 'County-specific ICT management'
        ],
        // [
        //     'title' => 'Server Management',
        //     'icon' => 'server',
        //     'color' => 'dark',
        //     'link' => route('ictengineer.servers'),
        //     'desc' => 'Manage server infrastructure'
        // ],
        // [
        //     'title' => 'Equipment',
        //     'icon' => 'microchip',
        //     'color' => 'secondary',
        //     'link' => route('ictengineer.equipment'),
        //     'desc' => 'Manage ICT equipment inventory'
        // ],
        // [
        //     'title' => 'Users',
        //     'icon' => 'users',
        //     'color' => 'purple',
        //     'link' => route('ictengineer.users'),
        //     'desc' => 'Manage ICT system users'
        // ],
        [
            'title' => 'Reports',
            'icon' => 'chart-bar',
            'color' => 'info',
            'link' => route('ictengineer.reports'),
            'desc' => 'Generate ICT system reports'
        ],
        // [
        //     'title' => 'Security',
        //     'icon' => 'shield-alt',
        //     'color' => 'danger',
        //     'link' => route('ictengineer.security'),
        //     'desc' => 'Security monitoring and alerts'
        // ],
        // [
        //     'title' => 'Backup Status',
        //     'icon' => 'database',
        //     'color' => 'success',
        //     'link' => route('ictengineer.backups'),
        //     'desc' => 'Check backup systems status'
        // ],
        // [
        //     'title' => 'Settings',
        //     'icon' => 'cog',
        //     'color' => 'secondary',
        //     'link' => route('ictengineer.settings'),
        //     'desc' => 'ICT system settings'
        // ],
        [
            'title' => 'Help Desk',
            'icon' => 'life-ring',
            'color' => 'warning',
            'link' => route('ictengineer.helpdesk'),
            'desc' => 'ICT help desk and support'
        ]
    ];
@endphp

                        <div class="row g-2 g-sm-3 g-md-4">
                            @foreach($actions as $action)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2 col-xxl-2">
                                @php
                                    $link = is_callable($action['link']) ? $action['link']() : $action['link'];
                                    $badgeCount = is_callable($action['badge'] ?? null) ? $action['badge']() : ($action['badge'] ?? 0);
                                @endphp

                                <a href="{{ $link }}" class="action-card position-relative">
                                    @if($badgeCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning responsive-badge">
                                        {{ $badgeCount }}
                                        <span class="visually-hidden">pending items</span>
                                    </span>
                                    @endif
                                    <div class="action-icon bg-{{ $action['color'] }}">
                                        <i class="fas fa-{{ $action['icon'] }}"></i>
                                    </div>
                                    <div class="action-content">
                                        <h6 class="responsive-text">{{ $action['title'] }}</h6>
                                        <p class="text-muted small d-none d-sm-block">{{ $action['desc'] }}</p>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Tickets/Issues -->
        <!-- Recent Design Requests -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-2 py-sm-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-0 responsive-subheading">
                        <i class="fas fa-history text-info me-2"></i>Recent Design Requests
                    </h5>
                    <div class="d-flex align-items-center mt-1 mt-sm-0">
                        <span class="badge bg-light text-dark responsive-badge d-none d-sm-inline">{{ $recentRequests->count() }} requests</span>
                        <a href="{{ route('ictengineer.requests.index') }}" class="btn btn-sm btn-primary ms-1 ms-sm-2 responsive-btn">
                            <span class="d-none d-sm-inline">View All</span>
                            <span class="d-inline d-sm-none">All</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($recentRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 responsive-table-header">Request #</th>
                                <th class="border-0 responsive-table-header">Customer</th>
                                <th class="border-0 responsive-table-header">Title</th>
                                <th class="border-0 responsive-table-header d-none d-sm-table-cell">Status</th>
                                <th class="border-0 responsive-table-header d-none d-lg-table-cell">Assigned</th>
                                <th class="border-0 text-center responsive-table-header">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentRequests as $request)
                            <tr class="border-bottom">
                                <td class="fw-bold">#{{ $request->request_number ?? $request->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="customer-avatar me-2">
                                            <i class="fas fa-user text-primary responsive-icon-sm"></i>
                                        </div>
                                        <div class="responsive-text">{{ $request->customer->name ?? 'N/A' }}</div>
                                    </div>
                                </td>
                                <td class="responsive-text">{{ Str::limit($request->title, 30) }}</td>
                                <td class="d-none d-sm-table-cell">
    @php
        $statusColors = [
            'pending' => 'secondary',
            'assigned' => 'warning',
            'in_progress' => 'primary',
            'review' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            'default' => 'info'
        ];
    @endphp
    <span class="badge bg-{{ $statusColors[strtolower($request->status)] ?? $statusColors['default'] }} responsive-badge">
        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
    </span>
</td>
                                <td class="d-none d-lg-table-cell">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted responsive-text-sm">{{ $request->assigned_at ? $request->assigned_at->format('M d, Y') : 'Not assigned' }}</span>
                                        @if($request->assigned_at)
                                        <span class="small text-muted responsive-text-sm">{{ $request->assigned_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm responsive-btn-group" role="group">
                                        <a href="{{ route('ictengineer.requests.show', $request) }}"
                                           class="btn btn-outline-primary"
                                           data-bs-toggle="tooltip"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if(in_array(strtolower($request->status), ['assigned', 'in_progress']))
                                        <a href="{{ route('ictengineer.requests.show', $request) }}?edit=true"
                                           class="btn btn-outline-warning d-none d-md-inline"
                                           data-bs-toggle="tooltip"
                                           title="Update Status">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif

                                        @if($request->status === 'review')
                                        <a href="{{ route('ictengineer.requests.show', $request) }}"
                                           class="btn btn-outline-success d-none d-md-inline"
                                           data-bs-toggle="tooltip"
                                           title="Approve Design">
                                            <i class="fas fa-check-circle"></i>
                                        </a>
                                        @endif
                                    </div>
                                    <!-- Mobile action dropdown -->
                                    <div class="dropdown d-md-none">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('ictengineer.requests.show', $request) }}">View Details</a></li>
                                            @if(in_array(strtolower($request->status), ['assigned', 'in_progress']))
                                            <li><a class="dropdown-item" href="{{ route('ictengineer.requests.show', $request) }}?edit=true">Update Status</a></li>
                                            @endif
                                            @if($request->status === 'review')
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
                <div class="text-center py-4 py-sm-5">
                    <div class="empty-state">
                        <i class="fas fa-drafting-compass text-gray-300 responsive-empty-icon mb-3"></i>
                        <h5 class="text-gray-600 responsive-subheading">No Design Requests Assigned</h5>
                        <p class="text-muted mb-3 mb-sm-4 responsive-text">No design requests have been assigned to you yet.</p>
                        <a href="{{ route('ictengineer.requests.index') }}" class="btn btn-primary responsive-btn">
                            <i class="fas fa-list me-2"></i>View All Requests
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

        <!-- System Status Charts -->
        <div class="row g-2 g-sm-3 g-md-4 mt-3 mt-sm-4">
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <h5 class="mb-0 responsive-subheading">
                            <i class="fas fa-chart-bar text-primary me-2"></i>Network Performance
                        </h5>
                    </div>
                    <div class="card-body p-2 p-sm-3 p-md-4">
                        <div class="text-center py-3 py-sm-4">
                            <div class="chart-placeholder">
                                <i class="fas fa-network-wired text-muted opacity-25 responsive-empty-icon"></i>
                            </div>
                            <p class="text-muted mt-3 mb-0 responsive-text">Network performance metrics would appear here</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <h5 class="mb-0 responsive-subheading">
                            <i class="fas fa-chart-pie text-success me-2"></i>Ticket Distribution
                        </h5>
                    </div>
                    <div class="card-body p-2 p-sm-3 p-md-4">
                        <div class="text-center py-3 py-sm-4">
                            <div class="chart-placeholder">
                                <i class="fas fa-chart-pie text-muted opacity-25 responsive-empty-icon"></i>
                            </div>
                            <p class="text-muted mt-3 mb-0 responsive-text">Ticket status distribution would appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Notifications/Alerts -->
        <div class="row mt-3 mt-sm-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 responsive-subheading">
                                <i class="fas fa-bell text-warning me-2"></i>System Notifications
                            </h5>
                            <span class="badge bg-warning responsive-badge">{{ $notifications->count() ?? 0 }} New</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($notifications && $notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                            <div class="list-group-item border-0 py-2 py-sm-3">
                                <div class="d-flex align-items-center">
                                    <div class="notification-icon me-2 me-sm-3">
                                        @switch($notification->type)
                                            @case('security')
                                                <i class="fas fa-shield-alt text-danger"></i>
                                                @break
                                            @case('server')
                                                <i class="fas fa-server text-warning"></i>
                                                @break
                                            @case('network')
                                                <i class="fas fa-network-wired text-info"></i>
                                                @break
                                            @default
                                                <i class="fas fa-info-circle text-primary"></i>
                                        @endswitch
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 responsive-text">{{ $notification->title }}</h6>
                                        <p class="mb-0 text-muted small responsive-text-sm">{{ $notification->message }}</p>
                                    </div>
                                    <div class="ms-2">
                                        <span class="text-muted small responsive-text-sm">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4 py-sm-5">
                            <div class="empty-state">
                                <i class="fas fa-bell-slash text-gray-300 responsive-empty-icon mb-3"></i>
                                <h5 class="text-gray-600 responsive-subheading">No New Notifications</h5>
                                <p class="text-muted mb-0 responsive-text">All systems are running normally</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS Custom Properties for dynamic scaling */
:root {
    --scale-factor: 1;
    --min-scale: 0.8;
    --max-scale: 1.2;
    --base-font-size: 16px;
    --spacing-unit: 0.25rem;
}

/* Fluid Typography */
.responsive-heading {
    font-size: clamp(1.25rem, 4vw, 2rem);
    line-height: 1.2;
}

.responsive-subheading {
    font-size: clamp(1rem, 3vw, 1.5rem);
    line-height: 1.3;
}

.responsive-text {
    font-size: clamp(0.875rem, 2vw, 1rem);
}

.responsive-text-sm {
    font-size: clamp(0.75rem, 1.5vw, 0.875rem);
}

/* Fluid Spacing */
.dashboard-header {
    padding-top: clamp(1rem, 3vw, 1.5rem);
    padding-bottom: clamp(1rem, 3vw, 1.5rem);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Fluid Icons */
.responsive-icon {
    font-size: clamp(1.5rem, 4vw, 2.5rem);
}

.responsive-icon-sm {
    font-size: clamp(1rem, 2.5vw, 1.5rem);
}

.responsive-metric-icon {
    font-size: clamp(1.5rem, 3.5vw, 2rem);
}

.responsive-empty-icon {
    font-size: clamp(2.5rem, 8vw, 4rem);
}

/* Fluid Cards & Containers */
.stat-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border: 1px solid #e9ecef;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}

.responsive-stat-icon {
    width: clamp(2.5rem, 6vw, 3.75rem);
    height: clamp(2.5rem, 6vw, 3.75rem);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    padding: clamp(0.5rem, 1.5vw, 0.75rem);
}

.responsive-stat-value {
    font-size: clamp(1.5rem, 4vw, 2rem);
    font-weight: 700;
}

.responsive-metric-value {
    font-size: clamp(1.25rem, 3.5vw, 1.75rem);
    font-weight: 700;
}

/* Fluid Badges */
.responsive-badge {
    font-size: clamp(0.65rem, 1.5vw, 0.75rem);
    padding: clamp(0.25rem, 0.5vw, 0.375rem) clamp(0.5rem, 1vw, 0.75rem);
    border-radius: 9999px;
}

/* Fluid Buttons */
.responsive-btn {
    font-size: clamp(0.75rem, 2vw, 0.875rem);
    padding: clamp(0.375rem, 1vw, 0.5rem) clamp(0.75rem, 2vw, 1rem);
    min-height: clamp(2.5rem, 6vw, 2.75rem);
    white-space: nowrap;
}

.responsive-btn-group .btn {
    font-size: clamp(0.7rem, 1.8vw, 0.8rem);
    padding: clamp(0.25rem, 0.75vw, 0.375rem) clamp(0.5rem, 1.5vw, 0.75rem);
}

/* Action Cards */
.action-card {
    display: block;
    padding: clamp(0.75rem, 2vw, 1rem);
    background: white;
    border: 1px solid #e9ecef;
    border-radius: clamp(0.5rem, 1.5vw, 0.75rem);
    text-decoration: none;
    color: inherit;
    transition: all 0.3s;
    height: 100%;
    text-align: center;
}

.action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #4e73df;
}

.action-icon {
    width: clamp(2.5rem, 6vw, 3.125rem);
    height: clamp(2.5rem, 6vw, 3.125rem);
    border-radius: clamp(0.5rem, 1.5vw, 0.625rem);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto clamp(0.5rem, 1.5vw, 0.75rem);
    color: white;
    font-size: clamp(1rem, 2.5vw, 1.25rem);
}

/* Table Responsive */
.responsive-table-header {
    font-size: clamp(0.7rem, 1.5vw, 0.8rem);
    padding: clamp(0.75rem, 1.5vw, 1rem) clamp(0.5rem, 1vw, 0.75rem);
}

.table td {
    padding: clamp(0.75rem, 1.5vw, 1rem) clamp(0.5rem, 1vw, 0.75rem);
    vertical-align: middle;
}

/* Customer Avatar */
.customer-avatar {
    width: clamp(1.75rem, 4vw, 2rem);
    height: clamp(1.75rem, 4vw, 2rem);
    border-radius: 50%;
    background-color: #e3f2fd;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: clamp(0.875rem, 2vw, 1rem);
}

/* Color Classes */
.bg-primary-light { background-color: rgba(78, 115, 223, 0.1); }
.bg-success-light { background-color: rgba(28, 200, 138, 0.1); }
.bg-info-light { background-color: rgba(54, 185, 204, 0.1); }
.bg-warning-light { background-color: rgba(246, 194, 62, 0.1); }
.bg-danger-light { background-color: rgba(231, 74, 59, 0.1); }
.bg-purple-light { background-color: rgba(111, 66, 193, 0.1); }
.bg-white-20 { background-color: rgba(255, 255, 255, 0.2); }

.bg-purple { background-color: #6f42c1 !important; }

/* Notification Icon */
.notification-icon i {
    font-size: clamp(1.25rem, 3vw, 1.5rem);
}

/* Touch Device Optimization */
@media (hover: none) and (pointer: coarse) {
    .stat-card:hover,
    .action-card:hover {
        transform: none;
    }

    .btn, .action-card {
        min-height: 44px;
    }

    .btn-sm {
        min-height: 36px;
    }

    /* Increase tap target sizes */
    .responsive-badge,
    .dropdown-toggle {
        padding: 0.5em 0.75em;
    }

    .table td {
        padding: 0.75rem 0.5rem;
    }
}

/* Viewport Height Adjustments */
@media (max-height: 600px) and (orientation: landscape) {
    .dashboard-header {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }

    .responsive-heading {
        font-size: 1.25rem;
    }

    .stat-card {
        margin-bottom: 0.5rem;
    }

    .table-responsive {
        max-height: 200px;
        overflow-y: auto;
    }
}

/* Print Styles */
@media print {
    .dashboard-header,
    .action-card,
    .btn,
    .badge {
        display: none !important;
    }

    .stat-card,
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }

    .table {
        border: 1px solid #ddd;
    }
}

/* Dynamic Grid Adjustment */
@media (max-width: 360px) {
    .col-6 {
        width: 100%;
    }

    .action-icon {
        width: 2.25rem;
        height: 2.25rem;
        font-size: 1rem;
    }

    .responsive-table-header {
        font-size: 0.65rem;
    }
}

@media (min-width: 1400px) {
    .col-xxl-2 {
        width: 20%;
        flex: 0 0 auto;
    }
}

/* Smooth Transitions */
.stat-card,
.action-card,
.btn,
.badge {
    transition: all 0.2s ease-in-out;
}

/* Performance Optimizations */
@media (prefers-reduced-motion: reduce) {
    .stat-card,
    .action-card,
    .btn,
    .badge {
        transition: none;
    }
}

/* Accessibility */
@media (prefers-contrast: high) {
    .text-muted {
        color: #666 !important;
    }

    .bg-white-20 {
        background-color: rgba(255, 255, 255, 0.3) !important;
    }
}

/* Custom Scrollbar for Desktop */
@media (min-width: 768px) {
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
}

/* Dropdown adjustments for mobile */
@media (max-width: 767.98px) {
    .dropdown-menu {
        min-width: 200px;
        font-size: 0.875rem;
    }

    .table-responsive {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap components
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Initialize dropdowns
    const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
    const dropdownList = [...dropdownElementList].map(dropdownToggleEl => new bootstrap.Dropdown(dropdownToggleEl));

    // Dynamic scaling based on viewport
    function updateScaleFactor() {
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        const isMobile = viewportWidth < 768;
        const isTablet = viewportWidth >= 768 && viewportWidth < 1024;

        // Calculate scale factor based on viewport size
        let scaleFactor;
        if (isMobile) {
            scaleFactor = Math.max(0.8, Math.min(1.2, viewportWidth / 375));
        } else if (isTablet) {
            scaleFactor = Math.max(0.9, Math.min(1.1, viewportWidth / 768));
        } else {
            scaleFactor = 1;
        }

        // Apply scale factor to root
        document.documentElement.style.setProperty('--scale-factor', scaleFactor);

        // Adjust grid layout for very small screens
        const metricsGrid = document.querySelector('.row.g-2.g-sm-3.g-md-4');
        if (metricsGrid && viewportWidth < 400) {
            metricsGrid.style.gap = '0.5rem';
        }
    }

    // Update date format based on screen size
    function updateDateDisplay() {
        const dateElements = document.querySelectorAll('.date-text');
        const nameElements = document.querySelectorAll('.name-text');
        const now = new Date();

        dateElements.forEach(el => {
            if (window.innerWidth < 576) {
                el.textContent = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            } else if (window.innerWidth < 768) {
                el.textContent = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            } else {
                el.textContent = now.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            }
        });

        nameElements.forEach(el => {
            const fullName = '{{ Auth::user()->name }}';
            if (window.innerWidth < 400) {
                el.textContent = fullName.split(' ')[0];
            } else if (window.innerWidth < 576) {
                el.textContent = fullName.length > 15 ? fullName.substring(0, 12) + '...' : fullName;
            } else {
                el.textContent = fullName;
            }
        });
    }

    // Update button text based on screen size
    function updateButtonText() {
        const btnTexts = document.querySelectorAll('.btn-text');
        const isMobile = window.innerWidth < 768;

        btnTexts.forEach(el => {
            const text = el.textContent;
            if (isMobile) {
                // Shorten button text on mobile
                if (text === 'Logout') el.textContent = 'Logout';
                if (text === 'View All') el.textContent = 'All';
            } else {
                // Restore full text on larger screens
                if (text === 'All') el.textContent = 'View All';
            }
        });
    }

    // Optimize for touch devices
    if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
        document.body.classList.add('touch-device');

        // Increase touch targets
        document.querySelectorAll('.btn, .action-card, .dropdown-toggle').forEach(el => {
            el.style.minHeight = '44px';
            if (el.classList.contains('btn')) {
                el.style.padding = '12px 16px';
            }
        });

        // Make table rows more tappable on mobile
        if (window.innerWidth < 768) {
            document.querySelectorAll('.table tbody tr').forEach(row => {
                row.style.cursor = 'pointer';
                row.addEventListener('click', function(e) {
                    if (!e.target.closest('a') && !e.target.closest('button') && !e.target.closest('.dropdown')) {
                        const viewLink = this.querySelector('a[href*="show"]');
                        if (viewLink) {
                            window.location = viewLink.href;
                        }
                    }
                });
            });
        }
    }

    // Prevent horizontal scroll
    document.body.style.overflowX = 'hidden';
    document.documentElement.style.overflowX = 'hidden';

    // Initialize and update on resize
    updateScaleFactor();
    updateDateDisplay();
    updateButtonText();

    // Debounced resize handler
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            updateScaleFactor();
            updateDateDisplay();
            updateButtonText();
        }, 100);
    });

    // Handle orientation changes
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            updateScaleFactor();
            updateDateDisplay();
            updateButtonText();
        }, 100);
    });

    // Performance optimization for animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    // Observe cards for lazy animation
    document.querySelectorAll('.stat-card, .action-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });

    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Auto-refresh dashboard every 5 minutes
    const refreshInterval = 5 * 60 * 1000; // 5 minutes
    let refreshTimer = setTimeout(function() {
        window.location.reload();
    }, refreshInterval);

    // Reset timer on user activity
    document.addEventListener('click', function() {
        clearTimeout(refreshTimer);
        refreshTimer = setTimeout(function() {
            window.location.reload();
        }, refreshInterval);
    });

    // Real-time notification updates (simulated)
    function checkForUpdates() {
        // In a real application, this would be a WebSocket or polling request
        console.log('Checking for system updates...');
    }

    // Check for updates every 2 minutes
    setInterval(checkForUpdates, 2 * 60 * 1000);
});
</script>
@endsection
