@extends('layouts.app')

@section('title', 'Dashboard - Dark Fibre CRM')

@section('content')
<div class="container-fluid px-0">

    {{-- Hero Header Section --}}
    <div class="dashboard-hero text-white py-4 py-md-5">
        <div class="container-fluid px-3 px-sm-4 px-md-5">
            <div class="row align-items-center g-4">

                {{-- Left Side - Welcome Message --}}
                <div class="col-12 col-lg-7">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="hero-icon">
                            <i class="fas fa-chart-line fa-3x fa-fw"></i>
                        </div>
                        <div>
                            <h1 class="display-6 fw-bold mb-2">
                                {{ Auth::user()->full_role_name }} Dashboard
                            </h1>
                            <p class="lead mb-0 opacity-90">
                                Welcome back, <strong>{{ Auth::user()->name }}</strong>!
                            </p>
                        </div>
                    </div>

                    {{-- User Meta Information --}}
                    <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
                        <span class="badge bg-white text-kp-blue px-3 py-2 rounded-pill">
                            <i class="fas fa-user-check me-1"></i>
                            {{ Auth::user()->full_role_name }}
                        </span>
                        <span class="text-white-50">
                            <i class="far fa-calendar-alt me-1"></i>
                            {{ now()->format('l, F j, Y') }}
                        </span>
                        @if(Auth::user()->is_online)
                            <span class="badge bg-success px-3 py-2 rounded-pill">
                                <i class="fas fa-circle me-1 small"></i>Online
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Right Side - Action Buttons --}}
                <div class="col-12 col-lg-5">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        @include('partials.role-help-widget')

                        {{-- Analytics Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle px-3 py-2" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-chart-line me-2"></i>
                                Analytics & Reports
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li><a class="dropdown-item" href="{{ route('kpi.dashboard') }}"><i class="fas fa-chart-pie me-2"></i>All KPIs</a></li>
                                <li><a class="dropdown-item" href="{{ route('kpi.dashboard', ['currency' => 'USD']) }}"><i class="fas fa-dollar-sign me-2"></i>USD Portfolio</a></li>
                                <li><a class="dropdown-item" href="{{ route('kpi.dashboard', ['currency' => 'KSH']) }}"><i class="fas fa-shilling-sign me-2"></i>KSH Portfolio</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('kpi.export') }}"><i class="fas fa-download me-2"></i>Export Data</a></li>
                            </ul>
                        </div>

                        {{-- Logout Button --}}
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-light px-3 py-2">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="container-fluid px-3 px-sm-4 px-md-5 py-4">

        {{-- Welcome Card with Role-Specific Message --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                @php
                                    $roleConfig = [
                                        'system_admin' => ['icon' => 'fa-shield-alt', 'color' => 'text-kp-blue', 'title' => 'System Administration & Analytics'],
                                        'accountmanager_admin' => ['icon' => 'fa-chart-line', 'color' => 'text-kp-green', 'title' => 'Marketing Analytics & Customer Insights'],
                                        'technical_admin' => ['icon' => 'fa-network-wired', 'color' => 'text-info', 'title' => 'Technical Operations & Network Monitoring'],
                                        'finance' => ['icon' => 'fa-money-bill-wave', 'color' => 'text-warning', 'title' => 'Financial Management & Reporting'],
                                        'designer' => ['icon' => 'fa-pencil-ruler', 'color' => 'text-purple', 'title' => 'Network Design & Quotation Center'],
                                        'surveyor' => ['icon' => 'fa-map-marked-alt', 'color' => 'text-danger', 'title' => 'Field Survey Operations'],
                                        'technician' => ['icon' => 'fa-tools', 'color' => 'text-secondary', 'title' => 'Field Maintenance & Operations'],
                                        'account_manager' => ['icon' => 'fa-handshake', 'color' => 'text-info', 'title' => 'Customer Relationship Management'],
                                        'debt_manager' => ['icon' => 'fa-chart-line', 'color' => 'text-warning', 'title' => 'Debt Management & Recovery'],
                                        'customer' => ['icon' => 'fa-user-circle', 'color' => 'text-kp-blue', 'title' => 'Customer Portal & Services'],
                                    ];
                                    $role = Auth::user()->role;
                                    $config = $roleConfig[$role] ?? ['icon' => 'fa-user-circle', 'color' => 'text-kp-blue', 'title' => 'Dashboard'];
                                @endphp

                                <h3 class="h4 mb-3">
                                    <i class="fas {{ $config['icon'] }} me-2 {{ $config['color'] }}"></i>
                                    {{ $config['title'] }}
                                </h3>
                                <p class="text-muted mb-3">
                                    Access your tools, view real-time metrics, and manage your tasks from this centralized dashboard.
                                </p>

                                {{-- Profile Completion Progress (Customers Only) --}}
                                @if(Auth::user()->isCustomer())
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small fw-semibold">Profile Completion</span>
                                            <span class="small fw-bold text-kp-blue">{{ Auth::user()->profile_completion_percentage }}%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-kp-green"
                                                 role="progressbar"
                                                 style="width: {{ Auth::user()->profile_completion_percentage }}%">
                                            </div>
                                        </div>
                                        @if(Auth::user()->profile_completion_percentage < 100)
                                            <small class="text-muted mt-2 d-block">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Complete your profile to unlock all features
                                            </small>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="col-lg-4 text-center d-none d-lg-block">
                                <i class="fas fa-network-wired fa-5x text-kp-blue opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Cards Grid --}}
        <div class="row g-3 g-md-4 mb-4">
            @forelse($stats as $key => $stat)
                @if(is_array($stat) && isset($stat['title'], $stat['value']))
                    <div class="col-6 col-md-4 col-xl-3 col-xxl-2">
                        <div class="stat-card bg-white rounded-4 shadow-sm h-100 p-3">
                            {{-- Card Header with Icon & Trend --}}
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="stat-icon-wrapper rounded-circle d-flex align-items-center justify-content-center stat-icon-{{ $stat['color'] ?? 'primary' }}">
                                    <i class="fas fa-{{ $stat['icon'] ?? 'chart-bar' }} fa-fw"></i>
                                </div>
                                @if(isset($stat['trend']))
                                    <span class="badge bg-{{ $stat['trend']['color'] ?? 'secondary' }} rounded-pill">
                                        <i class="fas fa-{{ $stat['trend']['icon'] ?? 'arrow-up' }} me-1"></i>
                                        {{ $stat['trend']['value'] ?? 0 }}%
                                    </span>
                                @endif
                            </div>

                            {{-- Card Title --}}
                            <h6 class="text-muted text-uppercase small fw-semibold mb-2">{{ $stat['title'] }}</h6>

                            {{-- Card Value --}}
                            @if(isset($stat['is_currency']) && $stat['is_currency'] === true)
                                {{-- Multi-Currency Display --}}
                                @if(is_array($stat['value']) && count($stat['value']) > 0)
                                    <div class="mb-2">
                                        @foreach($stat['value'] as $currency => $amount)
                                            <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                                                <span class="small text-muted">{{ $currency }}</span>
                                                <span class="fw-bold text-kp-blue">
                                                    @if($currency === 'USD') $@elseif(in_array($currency, ['KSH', 'KES'])) KSh @else {{ $currency }} @endif
                                                    {{ number_format($amount, 2) }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if(count($stat['value']) > 1)
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Showing original currencies
                                        </small>
                                    @endif
                                @else
                                    <div class="text-muted small">No payment data available</div>
                                @endif
                            @elseif(isset($stat['is_percentage']) && $stat['is_percentage'] === true)
                                <div class="fw-bold fs-3 mb-2 text-kp-blue">{{ number_format($stat['value'], 1) }}%</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $stat['color'] ?? 'primary' }}" style="width: {{ $stat['value'] }}%"></div>
                                </div>
                            @else
                                <div class="fw-bold fs-2 mb-2 text-kp-blue">{{ number_format($stat['value']) }}</div>
                            @endif

                            {{-- Subtitle --}}
                            @if(isset($stat['subtitle']))
                                <small class="text-muted d-block mt-2">{{ $stat['subtitle'] }}</small>
                            @endif
                        </div>
                    </div>
                @endif
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-chart-line me-2"></i>
                        No statistics available at the moment.
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Quick Actions Section --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                            </h5>
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                {{ Auth::user()->getQuickActionCount() }} Actions Available
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4 pt-2">
                        @php
                            $quickActions = $quickActions ?? match(true) {
                                Auth::user()->isSystemAdmin() => [
                                    ['title' => 'Add User', 'icon' => 'user-plus', 'color' => 'primary', 'link' => route('admin.users.create'), 'desc' => 'Create new system users'],
                                    ['title' => 'System Settings', 'icon' => 'cogs', 'color' => 'success', 'link' => route('admin.settings'), 'desc' => 'Configure system parameters'],
                                    ['title' => 'System Reports', 'icon' => 'chart-bar', 'color' => 'info', 'link' => route('admin.reports'), 'desc' => 'View analytics & insights'],
                                    ['title' => 'Manage Users', 'icon' => 'users', 'color' => 'warning', 'link' => route('admin.users'), 'desc' => 'View & manage users'],
                                    ['title' => 'Link Inventory', 'icon' => 'tachometer-alt', 'color' => 'danger', 'desc' => 'Fibre link management', 'links' => [
                                        ['label' => 'View All', 'route' => route('conversion-data.index'), 'icon' => 'list'],
                                        ['label' => 'Summary', 'route' => route('conversion-data.summary'), 'icon' => 'chart-bar'],
                                        ['label' => 'Add New', 'route' => route('conversion-data.create'), 'icon' => 'plus'],
                                    ]],
                                ],
                                Auth::user()->isTechnicalAdmin() => [
                                    ['title' => 'Manage Leases', 'icon' => 'network-wired', 'color' => 'primary', 'link' => route('admin.leases.index'), 'desc' => 'View & manage all leases'],
                                    ['title' => 'Design Requests', 'icon' => 'pencil-ruler', 'color' => 'success', 'link' => route('admin.design-requests.index'), 'desc' => 'Handle design requests'],
                                    ['title' => 'Quotations', 'icon' => 'file-invoice', 'color' => 'info', 'link' => route('admin.quotations.index'), 'desc' => 'Manage quotations'],
                                    ['title' => 'Manage Users', 'icon' => 'users', 'color' => 'warning', 'link' => route('admin.users'), 'desc' => 'View & manage users'],
                                    ['title' => 'Link Inventory', 'icon' => 'tachometer-alt', 'color' => 'danger', 'desc' => 'Fibre link management', 'links' => [
                                        ['label' => 'View All', 'route' => route('conversion-data.index'), 'icon' => 'list'],
                                        ['label' => 'Summary', 'route' => route('conversion-data.summary'), 'icon' => 'chart-bar'],
                                        ['label' => 'Add New', 'route' => route('conversion-data.create'), 'icon' => 'plus'],
                                    ]],
                                ],
                                Auth::user()->isAccountManager() => [
                                    ['title' => 'My Customers', 'icon' => 'users', 'color' => 'primary', 'link' => route('admin.customers.assign'), 'desc' => 'Manage assigned customers'],
                                    ['title' => 'New Lease', 'icon' => 'plus-circle', 'color' => 'success', 'link' => route('admin.leases.create'), 'desc' => 'Create lease agreements'],
                                    ['title' => 'Support Tickets', 'icon' => 'ticket-alt', 'color' => 'info', 'link' => route('admin.design-requests.index'), 'desc' => 'Handle customer support'],
                                    ['title' => 'Payments', 'icon' => 'credit-card', 'color' => 'warning', 'link' => route('admin.payments.index'), 'desc' => 'Manage payments & invoices'],
                                    ['title' => 'Link Inventory', 'icon' => 'tachometer-alt', 'color' => 'danger', 'desc' => 'Fibre link management', 'links' => [
                                        ['label' => 'View All', 'route' => route('conversion-data.index'), 'icon' => 'list'],
                                        ['label' => 'Summary', 'route' => route('conversion-data.summary'), 'icon' => 'chart-bar'],
                                        ['label' => 'Add New', 'route' => route('conversion-data.create'), 'icon' => 'plus'],
                                    ]],
                                ],
                                default => [
                                    ['title' => 'Manage Leases', 'icon' => 'network-wired', 'color' => 'primary', 'link' => route('admin.leases.index'), 'desc' => 'Approve & manage leases'],
                                    ['title' => 'Manage Users', 'icon' => 'users', 'color' => 'success', 'link' => route('admin.users'), 'desc' => 'User management'],
                                    ['title' => 'Design Requests', 'icon' => 'pencil-ruler', 'color' => 'info', 'link' => route('admin.design-requests.index'), 'desc' => 'Assign & view requests'],
                                    ['title' => 'Quotations', 'icon' => 'file-invoice', 'color' => 'warning', 'link' => route('admin.quotations.index'), 'desc' => 'Approve quotations'],
                                    ['title' => 'Manage Contracts', 'icon' => 'file-contract', 'color' => 'purple', 'link' => route('admin.contracts.index'), 'desc' => 'Approve & send contracts'],
                                    ['title' => 'Customer Listing', 'icon' => 'users', 'color' => 'dark', 'link' => route('admin.customers.index'), 'desc' => 'View & assign managers'],
                                    ['title' => 'Link Inventory', 'icon' => 'tachometer-alt', 'color' => 'danger', 'desc' => 'Fibre link management', 'links' => [
                                        ['label' => 'View All', 'route' => route('conversion-data.index'), 'icon' => 'list'],
                                        ['label' => 'Summary', 'route' => route('conversion-data.summary-report'), 'icon' => 'chart-bar'],
                                        ['label' => 'Add New', 'route' => route('conversion-data.create'), 'icon' => 'plus'],
                                    ]],
                                ],
                            };
                        @endphp

                        <div class="row g-3">
                            @foreach($quickActions as $action)
                                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                                    @if(isset($action['links']))
                                        <div class="action-card text-center p-3 rounded-3 border h-100">
                                            <div class="action-icon action-{{ $action['color'] }} rounded-3 mb-2">
                                                <i class="fas fa-{{ $action['icon'] }} fa-fw"></i>
                                            </div>
                                            <h6 class="mb-2 fw-semibold">{{ $action['title'] }}</h6>
                                            <p class="small text-muted mb-3">{{ $action['desc'] }}</p>
                                            @foreach($action['links'] as $link)
                                                <a href="{{ $link['route'] }}" class="btn btn-sm btn-outline-secondary w-100 mb-1">
                                                    <i class="fas fa-{{ $link['icon'] }} me-1"></i>{{ $link['label'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <a href="{{ $action['link'] }}" class="action-card text-center p-3 rounded-3 border h-100 text-decoration-none d-block">
                                            <div class="action-icon action-{{ $action['color'] }} rounded-3 mb-2">
                                                <i class="fas fa-{{ $action['icon'] }} fa-fw"></i>
                                            </div>
                                            <h6 class="mb-1 fw-semibold">{{ $action['title'] }}</h6>
                                            <p class="small text-muted mb-0 d-none d-sm-block">{{ $action['desc'] }}</p>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Two Column Content Area --}}
        <div class="row g-4">

            {{-- Left Column --}}
            <div class="col-12 col-lg-6">

                {{-- Recent Activity Timeline --}}
                @if(isset($recentActivities) && count($recentActivities) > 0)
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-clock text-kp-blue me-2"></i>Recent Activity
                            </h5>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <div class="timeline">
                                @foreach($recentActivities as $activity)
                                    <div class="timeline-item d-flex mb-3">
                                        <div class="timeline-marker rounded-circle bg-{{ $activity['color'] ?? 'secondary' }} me-3 flex-shrink-0">
                                            <i class="fas fa-{{ $activity['icon'] ?? 'info-circle' }} fa-fw text-white"></i>
                                        </div>
                                        <div class="timeline-content flex-grow-1">
                                            <div class="mb-1">{!! $activity['text'] !!}</div>
                                            <small class="text-muted">
                                                <i class="far fa-clock me-1"></i>{{ $activity['time'] }}
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Recent Items Table --}}
                @if(isset($recentItems) && $recentItems->count() > 0)
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-list text-kp-green me-2"></i>{{ $recentItemsTitle ?? 'Recent Items' }}
                            </h5>
                            <a href="{{ $recentItemsLink ?? '#' }}" class="btn btn-sm btn-outline-kp-green rounded-pill px-3">
                                View All <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            @foreach($recentItemsColumns as $column)
                                                <th class="border-0 py-3 px-3">{{ $column }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentItems as $item)
                                            <tr>
                                                @foreach($recentItemsColumns as $columnKey => $columnName)
                                                    <td class="px-3 py-2 align-middle">
                                                        @if($columnKey === 'status')
                                                            <span class="badge bg-{{ $item->getStatusColor() ?? 'secondary' }} rounded-pill px-3 py-1">
                                                                {{ ucfirst($item->status) }}
                                                            </span>
                                                        @elseif($columnKey === 'amount')
                                                            <span class="fw-bold text-kp-blue">KSh {{ number_format($item->amount, 2) }}</span>
                                                        @elseif($columnKey === 'created_at')
                                                            <span class="text-muted">{{ $item->created_at->format('M d, Y') }}</span>
                                                        @else
                                                            {{ $item->{$columnKey} ?? 'N/A' }}
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Right Column --}}
            <div class="col-12 col-lg-6">

                {{-- System Health Status --}}
                @if(isset($systemHealth) && count($systemHealth) > 0)
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-gradient-primary text-white border-0 pt-4 pb-2 px-4 rounded-top-4">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-heartbeat me-2"></i>System Health Status
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @foreach($systemHealth as $healthItem)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold">{{ $healthItem['label'] }}</span>
                                        <span class="badge bg-{{ $healthItem['status_color'] ?? 'secondary' }} rounded-pill px-3 py-1">
                                            {{ $healthItem['status'] }}
                                        </span>
                                    </div>
                                    @if(isset($healthItem['value']))
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-{{ $healthItem['status_color'] ?? 'primary' }}"
                                                 style="width: {{ $healthItem['value'] }}%">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Notifications & Alerts --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-bell text-warning me-2"></i>Notifications & Alerts
                        </h5>
                    </div>
                    <div class="card-body p-4 pt-2">
                        @if(isset($notifications) && count($notifications) > 0)
                            @foreach($notifications as $notification)
                                <div class="alert alert-{{ $notification['type'] ?? 'info' }} alert-dismissible fade show mb-3 rounded-3 border-0">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-{{ $notification['icon'] ?? 'info-circle' }} me-3 mt-1 fa-lg"></i>
                                        <div class="flex-grow-1">
                                            {!! $notification['message'] !!}
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-4x text-kp-green mb-3 opacity-50"></i>
                                <p class="text-muted mb-0">All systems are operating normally</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Performance Metrics --}}
                @if(isset($performanceMetrics) && count($performanceMetrics) > 0)
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-chart-line text-info me-2"></i>Performance Metrics
                            </h5>
                        </div>
                        <div class="card-body p-4 pt-2">
                            @foreach($performanceMetrics as $metric)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-semibold">{{ $metric['label'] }}</span>
                                        <span class="fw-bold text-kp-blue">{{ $metric['value'] }}{{ $metric['unit'] ?? '' }}</span>
                                    </div>
                                    <div class="progress mb-1" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $metric['color'] ?? 'primary' }}"
                                             style="width: {{ $metric['percentage'] }}%">
                                        </div>
                                    </div>
                                    <small class="text-muted">Target: {{ $metric['target'] }}{{ $metric['unit'] ?? '' }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

        </div>

        {{-- Charts Section --}}
        @if(isset($charts) && count($charts) > 0)
            <div class="row g-4 mt-2">
                @foreach($charts as $chart)
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                                <h5 class="mb-0 fw-bold">
                                    <i class="fas fa-chart-{{ $chart['type'] }} text-kp-blue me-2"></i>
                                    {{ $chart['title'] }}
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="chart-container" style="height: 300px;">
                                    <canvas id="chart-{{ $loop->index }}"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Additional Sections --}}
        @if(isset($additionalSections) && count($additionalSections) > 0)
            <div class="row g-4 mt-2">
                @foreach($additionalSections as $section)
                    <div class="col-12 col-lg-{{ $section['size'] ?? 6 }}">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-gradient-primary text-white border-0 pt-4 pb-2 px-4 rounded-top-4">
                                <h5 class="mb-0 fw-bold">
                                    <i class="fas fa-{{ $section['icon'] }} me-2"></i>
                                    {{ $section['title'] }}
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                {!! $section['content'] !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>

@if(isset($charts) && count($charts) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @foreach($charts as $chart)
        const ctx{{ $loop->index }} = document.getElementById('chart-{{ $loop->index }}')?.getContext('2d');
        if (ctx{{ $loop->index }}) {
            new Chart(ctx{{ $loop->index }}, {
                type: '{{ $chart['type'] }}',
                data: {
                    labels: {!! json_encode($chart['labels']) !!},
                    datasets: [{
                        label: '{{ $chart['dataset']['label'] }}',
                        data: {!! json_encode($chart['dataset']['data']) !!},
                        backgroundColor: '{{ $chart['dataset']['backgroundColor'] ?? 'rgba(0, 102, 179, 0.2)' }}',
                        borderColor: '{{ $chart['dataset']['borderColor'] ?? '#0066B3' }}',
                        borderWidth: 2,
                        fill: {{ isset($chart['dataset']['fill']) ? json_encode($chart['dataset']['fill']) : 'true' }},
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#e9ecef' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    @endforeach
});
</script>
@endif

<style>
:root {
    --kp-blue: #0066B3;
    --kp-green: #009639;
    --kp-yellow: #FFD700;
    --kp-dark: #003f20;
}

.dashboard-hero {
    background: linear-gradient(135deg, var(--kp-blue) 0%, var(--kp-green) 100%);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, var(--kp-blue) 0%, var(--kp-green) 100%);
}

.text-kp-blue { color: var(--kp-blue) !important; }
.text-kp-green { color: var(--kp-green) !important; }
.bg-kp-green { background-color: var(--kp-green) !important; }
.text-purple { color: #6f42c1 !important; }

.stat-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.08);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.stat-icon-wrapper {
    width: 48px;
    height: 48px;
    font-size: 1.25rem;
}

.stat-icon-primary { background: rgba(0, 102, 179, 0.1); color: var(--kp-blue); }
.stat-icon-success { background: rgba(0, 150, 57, 0.1); color: var(--kp-green); }
.stat-icon-info { background: rgba(23, 162, 184, 0.1); color: #17a2b8; }
.stat-icon-warning { background: rgba(255, 215, 0, 0.15); color: #856404; }
.stat-icon-danger { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
.stat-icon-secondary { background: rgba(108, 117, 125, 0.1); color: #6c757d; }

.action-card {
    transition: all 0.3s ease;
    background: white;
}

.action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-color: transparent !important;
}

.action-icon {
    width: 50px;
    height: 50px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.action-primary { background: var(--kp-blue); }
.action-success { background: var(--kp-green); }
.action-info { background: #17a2b8; }
.action-warning { background: var(--kp-yellow); color: var(--kp-dark); }
.action-danger { background: #dc3545; }
.action-purple { background: #6f42c1; }
.action-dark { background: #343a40; }

.timeline-marker {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-outline-kp-green {
    border: 1px solid var(--kp-green);
    color: var(--kp-green);
}
.btn-outline-kp-green:hover {
    background: var(--kp-green);
    color: white;
}

.progress {
    background-color: #e9ecef;
    border-radius: 9999px;
}

.rounded-4 { border-radius: 1rem !important; }

@media (max-width: 576px) {
    .col-6 { width: 100%; }
    .action-card { text-align: left; }
    .action-icon { margin: 0 0 0.75rem 0; }
}

@media print {
    .dashboard-hero, .action-card, .btn, .badge { display: none !important; }
    .card { border: 1px solid #ddd !important; box-shadow: none !important; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

    // Auto-dismiss alerts after 10 seconds
    document.querySelectorAll('.alert-dismissible').forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 10000);
    });
});
</script>

@endsection
