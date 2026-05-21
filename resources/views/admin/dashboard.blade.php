@extends('layouts.app')

@section('title', 'Dashboard - Dark Fibre CRM')

@section('content')
<div class="container-fluid px-0">

    {{-- Dashboard Header --}}
    <div class="dashboard-header text-white py-3 py-md-4">
        <div class="container-fluid px-3 px-sm-4 px-md-5">
            <div class="row align-items-center g-3">

                <div class="col-12 col-lg-7">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="header-icon me-3">
                            <i class="fas fa-tachometer-alt responsive-icon"></i>
                        </div>

                        <div>
                            <h1 class="responsive-heading mb-1">
                                {{ Auth::user()->full_role_name }} Dashboard
                            </h1>

                            <p class="mb-0 opacity-75 responsive-text">
                                Welcome back,
                                <strong>{{ Auth::user()->name }}</strong>!
                            </p>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-2 gap-md-3 mt-2">
                        <span class="badge bg-white responsive-badge text-kp-blue">
                            <i class="fas fa-user me-1"></i>
                            {{ Auth::user()->full_role_name }}
                        </span>

                        <span class="opacity-75 responsive-text-sm">
                            <i class="fas fa-calendar me-1"></i>
                            {{ now()->format('l, F j, Y') }}
                        </span>

                        @if(Auth::user()->is_online)
                            <span class="badge responsive-badge bg-kp-green">
                                <i class="fas fa-circle me-1"></i>Online
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-lg-5">
                    <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-lg-end align-items-center">

                        @include('partials.role-help-widget')

                        <div class="dropdown">
                            <button class="btn btn-light responsive-btn dropdown-toggle"
                                    type="button"
                                    data-bs-toggle="dropdown">
                                <i class="fas fa-chart-line me-2"></i>
                                Analytics KPIs
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('kpi.dashboard') }}">
                                        <i class="fas fa-chart-pie me-2"></i>All KPIs
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ route('kpi.dashboard', ['currency' => 'USD']) }}">
                                        <i class="fas fa-dollar-sign me-2"></i>USD Portfolio
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ route('kpi.dashboard', ['currency' => 'KSH']) }}">
                                        <i class="fas fa-shilling-sign me-2"></i>KSH Portfolio
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <a class="dropdown-item" href="{{ route('kpi.export') }}">
                                        <i class="fas fa-download me-2"></i>Export Data
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-light responsive-btn">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="container-fluid px-3 px-sm-4 px-md-5 py-4">

        {{-- Welcome Section --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm dashboard-card">
                    <div class="card-body p-4">
                        <div class="row align-items-center">

                            <div class="col-lg-8 mb-3 mb-lg-0">
                                <h3 class="responsive-subheading mb-2">
                                    @if(Auth::user()->isSystemAdmin())
                                        <i class="fas fa-shield-alt me-2 text-kp-blue"></i>System Administration & Analytics
                                    @elseif(Auth::user()->isMarketingAdmin())
                                        <i class="fas fa-chart-line me-2 text-kp-green"></i>Marketing Analytics & Customer Insights
                                    @elseif(Auth::user()->isTechnicalAdmin())
                                        <i class="fas fa-network-wired me-2 text-info"></i>Technical Operations & Network Monitoring
                                    @elseif(Auth::user()->isFinance())
                                        <i class="fas fa-money-bill-wave me-2 text-warning"></i>Financial Management & Reporting
                                    @elseif(Auth::user()->isDesigner())
                                        <i class="fas fa-pencil-ruler me-2 text-purple"></i>Network Design & Quotation Center
                                    @elseif(Auth::user()->isSurveyor())
                                        <i class="fas fa-map-marked-alt me-2 text-danger"></i>Field Survey Operations
                                    @elseif(Auth::user()->isTechnician())
                                        <i class="fas fa-tools me-2 text-secondary"></i>Field Maintenance & Operations
                                    @elseif(Auth::user()->isAccountManager())
                                        <i class="fas fa-handshake me-2 text-info"></i>Customer Relationship Management
                                    @elseif(Auth::user()->isDebtManager())
                                        <i class="fas fa-handshake me-2 text-info"></i>Debt Management
                                    @else
                                        <i class="fas fa-user-circle me-2 text-kp-blue"></i>Customer Portal & Services
                                    @endif
                                </h3>

                                <p class="text-muted mb-3 responsive-text">
                                    Access your tools, view metrics, and manage your tasks from this centralized dashboard.
                                </p>

                                @if(Auth::user()->isCustomer())
                                    <div class="profile-progress">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="responsive-text-sm">Profile Completion</span>
                                            <span class="responsive-text-sm fw-bold">
                                                {{ Auth::user()->profile_completion_percentage }}%
                                            </span>
                                        </div>

                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar"
                                                 role="progressbar"
                                                 style="width: {{ Auth::user()->profile_completion_percentage }}%; background-color:
                                                    {{ Auth::user()->profile_completion_percentage >= 80 ? '#009639' : (Auth::user()->profile_completion_percentage >= 50 ? '#FFD700' : '#dc3545') }}">
                                            </div>
                                        </div>

                                        @if(Auth::user()->profile_completion_percentage < 100)
                                            <small class="text-muted responsive-text-sm">
                                                Complete your profile to unlock all features
                                            </small>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="col-lg-4 text-center">
                                <i class="fas fa-network-wired responsive-empty-icon opacity-25 text-kp-blue"></i>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row g-3 g-md-4 mb-4">
            @foreach($stats as $key => $stat)
                @if(is_array($stat) && isset($stat['color'], $stat['title'], $stat['value']))
                    <div class="col-6 col-md-4 col-lg-2 mb-2">
                        <div class="stat-card bg-white rounded-lg shadow-sm border-0 h-100">
                            <div class="stat-card-body p-3">

                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="stat-icon rounded-circle responsive-stat-icon stat-icon-{{ $stat['color'] }}">
                                        <i class="fas fa-{{ $stat['icon'] ?? 'chart-bar' }}"></i>
                                    </div>

                                    @if(isset($stat['trend']))
                                        <span class="badge responsive-badge bg-{{ $stat['trend']['color'] ?? 'secondary' }}">
                                            <i class="fas fa-{{ $stat['trend']['icon'] ?? 'chart-line' }} me-1"></i>
                                            {{ $stat['trend']['value'] ?? 0 }}%
                                        </span>
                                    @endif
                                </div>

                                <h6 class="stat-title text-muted text-uppercase small mb-2">
                                    {{ $stat['title'] }}
                                </h6>

                                @if(isset($stat['is_currency']) && $stat['is_currency'] === true)
                                    <div class="stat-value fw-bold mb-2 text-dark responsive-stat-value">
                                        @if(is_array($stat['value']) && count($stat['value']) > 0)
                                            @foreach($stat['value'] as $currency => $amount)
                                                <div class="currency-row d-flex justify-content-between align-items-center mb-1"
                                                     data-bs-toggle="tooltip"
                                                     title="{{ $currency === 'USD' ? 'US Dollar' : ($currency === 'KSH' || $currency === 'KES' ? 'Kenyan Shilling' : $currency) }}">
                                                    <span class="currency-code small text-muted">
                                                        {{ $currency }}
                                                    </span>

                                                    <span class="currency-amount fw-bold text-kp-blue">
                                                        {{ $currency === 'USD' ? '$' : (($currency === 'KSH' || $currency === 'KES') ? 'KSh' : $currency) }}
                                                        {{ number_format($amount, 2) }}
                                                    </span>
                                                </div>
                                            @endforeach

                                            @if(count($stat['value']) > 1)
                                                <div class="currency-total mt-2 pt-1 border-top">
                                                    <small class="text-muted">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Original currencies shown
                                                    </small>
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-muted small">
                                                <i class="fas fa-info-circle me-1"></i>
                                                No payment data available
                                            </div>
                                        @endif
                                    </div>

                                @elseif(isset($stat['is_percentage']) && $stat['is_percentage'] === true)
                                    <div class="stat-value fw-bold mb-2 text-dark responsive-stat-value">
                                        {{ number_format($stat['value'], 1) }}%
                                    </div>

                                    <div class="progress mt-2" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $stat['color'] }}"
                                             role="progressbar"
                                             style="width: {{ $stat['value'] }}%;">
                                        </div>
                                    </div>

                                @else
                                    <div class="stat-value fw-bold mb-2 text-kp-blue responsive-stat-value">
                                        {{ number_format($stat['value']) }}
                                    </div>
                                @endif

                                @if(isset($stat['subtitle']))
                                    <div class="stat-subtitle responsive-text-sm text-muted">
                                        {{ $stat['subtitle'] }}
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Quick Actions --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm dashboard-card">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 responsive-subheading">
                                <i class="fas fa-bolt me-2 text-warning"></i>Quick Actions
                            </h5>

                            <span class="badge responsive-badge d-none d-sm-inline bg-warning text-dark">
                                {{ Auth::user()->getQuickActionCount() }} Actions
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-3">
                        @php
                            $quickActions = [];

                            if (Auth::user()->isSystemAdmin()) {
                                $quickActions = [
                                    [
                                        'title' => 'Add User',
                                        'icon' => 'user-plus',
                                        'color' => 'primary',
                                        'link' => route('admin.users.create'),
                                        'desc' => 'Create new system user accounts'
                                    ],
                                    [
                                        'title' => 'System Settings',
                                        'icon' => 'cogs',
                                        'color' => 'success',
                                        'link' => route('admin.settings'),
                                        'desc' => 'Configure system parameters'
                                    ],
                                    [
                                        'title' => 'System Reports',
                                        'icon' => 'chart-bar',
                                        'color' => 'info',
                                        'link' => route('admin.reports'),
                                        'desc' => 'View analytics and insights'
                                    ],
                                    [
                                        'title' => 'Manage Users',
                                        'icon' => 'users',
                                        'color' => 'warning',
                                        'link' => route('admin.users'),
                                        'desc' => 'View and manage all users'
                                    ],
                                    [
                                        'title' => 'Link Inventory',
                                        'icon' => 'tachometer-alt',
                                        'color' => 'danger',
                                        'desc' => 'Fibre link management',
                                        'links' => [
                                            ['label' => 'View All', 'route' => route('conversion-data.index'), 'icon' => 'list'],
                                            ['label' => 'Summary', 'route' => route('conversion-data.summary'), 'icon' => 'chart-bar'],
                                            ['label' => 'Add New', 'route' => route('conversion-data.create'), 'icon' => 'plus'],
                                        ]
                                    ],
                                ];
                            } elseif (Auth::user()->isTechnicalAdmin()) {
                                $quickActions = [
                                    [
                                        'title' => 'Manage Leases',
                                        'icon' => 'network-wired',
                                        'color' => 'primary',
                                        'link' => route('admin.leases.index'),
                                        'desc' => 'View and manage all leases'
                                    ],
                                    [
                                        'title' => 'Design Requests',
                                        'icon' => 'pencil-ruler',
                                        'color' => 'success',
                                        'link' => route('admin.design-requests.index'),
                                        'desc' => 'Handle design requests'
                                    ],
                                    [
                                        'title' => 'Quotations',
                                        'icon' => 'file-invoice',
                                        'color' => 'info',
                                        'link' => route('admin.quotations.index'),
                                        'desc' => 'Manage all quotations'
                                    ],
                                    [
                                        'title' => 'Manage Users',
                                        'icon' => 'users',
                                        'color' => 'warning',
                                        'link' => route('admin.users'),
                                        'desc' => 'View and manage users'
                                    ],
                                    [
                                        'title' => 'Link Inventory',
                                        'icon' => 'tachometer-alt',
                                        'color' => 'danger',
                                        'desc' => 'Fibre link management',
                                        'links' => [
                                            ['label' => 'View All', 'route' => route('conversion-data.index'), 'icon' => 'list'],
                                            ['label' => 'Summary', 'route' => route('conversion-data.summary'), 'icon' => 'chart-bar'],
                                            ['label' => 'Add New', 'route' => route('conversion-data.create'), 'icon' => 'plus'],
                                        ]
                                    ],
                                ];
                            } elseif (Auth::user()->isAccountManager()) {
                                $quickActions = [
                                    [
                                        'title' => 'My Customers',
                                        'icon' => 'users',
                                        'color' => 'primary',
                                        'link' => route('admin.customers.assign'),
                                        'desc' => 'Manage assigned customers'
                                    ],
                                    [
                                        'title' => 'New Lease',
                                        'icon' => 'plus-circle',
                                        'color' => 'success',
                                        'link' => route('admin.leases.create'),
                                        'desc' => 'Create new lease agreements'
                                    ],
                                    [
                                        'title' => 'Support Tickets',
                                        'icon' => 'ticket-alt',
                                        'color' => 'info',
                                        'link' => route('admin.design-requests.index'),
                                        'desc' => 'Handle customer support'
                                    ],
                                    [
                                        'title' => 'Payments',
                                        'icon' => 'credit-card',
                                        'color' => 'warning',
                                        'link' => route('admin.payments.index'),
                                        'desc' => 'Manage payments and invoices'
                                    ],
                                    [
                                        'title' => 'Link Inventory',
                                        'icon' => 'tachometer-alt',
                                        'color' => 'danger',
                                        'desc' => 'Fibre link management',
                                        'links' => [
                                            ['label' => 'View All', 'route' => route('conversion-data.index'), 'icon' => 'list'],
                                            ['label' => 'Summary', 'route' => route('conversion-data.summary'), 'icon' => 'chart-bar'],
                                            ['label' => 'Add New', 'route' => route('conversion-data.create'), 'icon' => 'plus'],
                                        ]
                                    ],
                                ];
                            } else {
                                $quickActions = [
                                    [
                                        'title' => 'Manage Leases',
                                        'icon' => 'network-wired',
                                        'color' => 'primary',
                                        'link' => route('admin.leases.index'),
                                        'desc' => 'Approve, view, and send leases'
                                    ],
                                    [
                                        'title' => 'Manage Users',
                                        'icon' => 'users',
                                        'color' => 'success',
                                        'link' => route('admin.users'),
                                        'desc' => 'User management'
                                    ],
                                    [
                                        'title' => 'Design Requests',
                                        'icon' => 'pencil-ruler',
                                        'color' => 'info',
                                        'link' => route('admin.design-requests.index'),
                                        'desc' => 'Assign engineers and view requests'
                                    ],
                                    [
                                        'title' => 'Quotations',
                                        'icon' => 'file-invoice',
                                        'color' => 'warning',
                                        'link' => route('admin.quotations.index'),
                                        'desc' => 'Approve and send quotations'
                                    ],
                                    [
                                        'title' => 'Manage Contracts',
                                        'icon' => 'file-contract',
                                        'color' => 'purple',
                                        'link' => route('admin.contracts.index'),
                                        'desc' => 'Approve, view, and send contracts'
                                    ],
                                    [
                                        'title' => 'Customer Listing',
                                        'icon' => 'users',
                                        'color' => 'dark',
                                        'link' => route('admin.customers.index'),
                                        'desc' => 'View profiles and assign managers'
                                    ],
                                    [
                                        'title' => 'Link Inventory',
                                        'icon' => 'tachometer-alt',
                                        'color' => 'danger',
                                        'desc' => 'Fibre link management',
                                        'links' => [
                                            ['label' => 'View All', 'route' => route('conversion-data.index'), 'icon' => 'list'],
                                            ['label' => 'Summary', 'route' => route('conversion-data.summary-report'), 'icon' => 'chart-bar'],
                                            ['label' => 'Add New', 'route' => route('conversion-data.create'), 'icon' => 'plus'],
                                        ]
                                    ],
                                ];
                            }
                        @endphp

                        <div class="row g-3">
                            @foreach($quickActions as $action)
                                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                    @if(isset($action['links']))
                                        <div class="action-card">
                                            <div class="action-icon action-{{ $action['color'] }}">
                                                <i class="fas fa-{{ $action['icon'] }}"></i>
                                            </div>

                                            <div class="action-content">
                                                <h6>{{ $action['title'] }}</h6>
                                                <p class="text-muted small mb-2">{{ $action['desc'] }}</p>

                                                @foreach($action['links'] as $link)
                                                    <a href="{{ $link['route'] }}"
                                                       class="btn btn-sm w-100 mb-1 btn-outline-secondary">
                                                        <i class="fas fa-{{ $link['icon'] }} me-1"></i>
                                                        {{ $link['label'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <a href="{{ $action['link'] }}" class="action-card">
                                            <div class="action-icon action-{{ $action['color'] }}">
                                                <i class="fas fa-{{ $action['icon'] }}"></i>
                                            </div>

                                            <div class="action-content">
                                                <h6>{{ $action['title'] }}</h6>
                                                <p class="text-muted small d-none d-sm-block">
                                                    {{ $action['desc'] }}
                                                </p>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="row g-4">

            <div class="col-12 col-lg-6">

                @if(isset($recentActivities) && count($recentActivities) > 0)
                    <div class="card border-0 shadow-sm dashboard-card mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0 responsive-subheading">
                                <i class="fas fa-clock me-2 text-kp-blue"></i>Recent Activity
                            </h5>
                        </div>

                        <div class="card-body p-3">
                            <div class="activity-timeline">
                                @foreach($recentActivities as $activity)
                                    <div class="timeline-item mb-3">
                                        <div class="timeline-marker bg-{{ $activity['color'] ?? 'secondary' }}">
                                            <i class="fas fa-{{ $activity['icon'] ?? 'info-circle' }}"></i>
                                        </div>

                                        <div class="timeline-content">
                                            <div class="timeline-text responsive-text">
                                                {!! $activity['text'] !!}
                                            </div>
                                            <div class="timeline-time text-muted responsive-text-sm">
                                                {{ $activity['time'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($recentItems) && $recentItems->count() > 0)
                    <div class="card border-0 shadow-sm dashboard-card">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <h5 class="mb-0 responsive-subheading">
                                    <i class="fas fa-list me-2 text-kp-green"></i>
                                    {{ $recentItemsTitle }}
                                </h5>

                                <a href="{{ $recentItemsLink }}" class="btn btn-sm btn-outline-success">
                                    View All
                                </a>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            @foreach($recentItemsColumns as $column)
                                                <th class="border-0 responsive-table-header">
                                                    {{ $column }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($recentItems as $item)
                                            <tr>
                                                @foreach($recentItemsColumns as $columnKey => $columnName)
                                                    <td class="responsive-text-sm">
                                                        @if($columnKey === 'status')
                                                            <span class="badge bg-{{ $item->getStatusColor() ?? 'secondary' }}">
                                                                {{ ucfirst($item->status) }}
                                                            </span>
                                                        @elseif($columnKey === 'amount')
                                                            <span class="fw-bold text-kp-blue">
                                                                KSh {{ number_format($item->amount, 2) }}
                                                            </span>
                                                        @elseif($columnKey === 'created_at')
                                                            <span class="text-muted">
                                                                {{ $item->created_at->format('M d, Y') }}
                                                            </span>
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

            <div class="col-12 col-lg-6">

                @if(isset($systemHealth) && count($systemHealth) > 0)
                    <div class="card border-0 shadow-sm dashboard-card mb-4">
                        <div class="card-header text-white border-0 py-3 bg-dashboard-gradient">
                            <h5 class="mb-0 responsive-subheading">
                                <i class="fas fa-heartbeat me-2"></i>System Status
                            </h5>
                        </div>

                        <div class="card-body p-4">
                            @foreach($systemHealth as $healthItem)
                                <div class="system-health-item mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold responsive-text">
                                            {{ $healthItem['label'] }}
                                        </span>

                                        <span class="badge bg-{{ $healthItem['status_color'] ?? 'secondary' }}">
                                            {{ $healthItem['status'] }}
                                        </span>
                                    </div>

                                    @if(isset($healthItem['value']))
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $healthItem['status_color'] ?? 'primary' }}"
                                                 role="progressbar"
                                                 style="width: {{ $healthItem['value'] }}%;">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="card border-0 shadow-sm dashboard-card mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 responsive-subheading">
                            <i class="fas fa-bell me-2 text-warning"></i>Notifications & Alerts
                        </h5>
                    </div>

                    <div class="card-body p-0">
                        @if(isset($notifications) && count($notifications) > 0)
                            @foreach($notifications as $notification)
                                <div class="alert alert-{{ $notification['type'] ?? 'info' }} alert-dismissible fade show m-3 rounded">
                                    <div class="d-flex">
                                        <div class="alert-icon me-3">
                                            <i class="fas fa-{{ $notification['icon'] ?? 'info-circle' }}"></i>
                                        </div>

                                        <div class="flex-grow-1">
                                            {!! $notification['message'] !!}
                                        </div>

                                        <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="alert"></button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle responsive-empty-icon mb-3 text-kp-green"></i>
                                <p class="text-muted mb-0 responsive-text">
                                    All systems are operating normally
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                @if(isset($performanceMetrics) && count($performanceMetrics) > 0)
                    <div class="card border-0 shadow-sm dashboard-card">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0 responsive-subheading">
                                <i class="fas fa-chart-line me-2 text-info"></i>Performance Metrics
                            </h5>
                        </div>

                        <div class="card-body p-4">
                            @foreach($performanceMetrics as $metric)
                                <div class="metric-item mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="responsive-text-sm">
                                            {{ $metric['label'] }}
                                        </span>

                                        <span class="fw-bold responsive-text">
                                            {{ $metric['value'] }}{{ $metric['unit'] ?? '' }}
                                        </span>
                                    </div>

                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-{{ $metric['color'] ?? 'primary' }}"
                                             role="progressbar"
                                             style="width: {{ $metric['percentage'] }}%;">
                                        </div>
                                    </div>

                                    <small class="text-muted responsive-text-sm">
                                        Target: {{ $metric['target'] }}{{ $metric['unit'] ?? '' }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

        </div>

        {{-- Charts --}}
        @if(isset($charts) && count($charts) > 0)
            <div class="row g-4 mt-3">
                @foreach($charts as $chart)
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm dashboard-card">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="mb-0 responsive-subheading">
                                    <i class="fas fa-chart-{{ $chart['type'] }} me-2 text-kp-blue"></i>
                                    {{ $chart['title'] }}
                                </h5>
                            </div>

                            <div class="card-body p-3">
                                <div class="chart-container">
                                    <canvas id="{{ $chart['id'] }}"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Additional Sections --}}
        @if(isset($additionalSections) && count($additionalSections) > 0)
            <div class="row g-4 mt-3">
                @foreach($additionalSections as $section)
                    <div class="col-12 col-lg-{{ $section['size'] ?? 6 }}">
                        <div class="card border-0 shadow-sm dashboard-card">
                            <div class="card-header text-white border-0 py-3 bg-dashboard-gradient">
                                <h5 class="mb-0 responsive-subheading">
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
        document.addEventListener('DOMContentLoaded', function () {
            @foreach($charts as $chart)
                const chartCanvas{{ $loop->index }} = document.getElementById('{{ $chart['id'] }}');

                if (chartCanvas{{ $loop->index }}) {
                    new Chart(chartCanvas{{ $loop->index }}.getContext('2d'), {
                        type: '{{ $chart['type'] }}',
                        data: {
                            labels: {!! json_encode($chart['labels']) !!},
                            datasets: [{
                                label: '{{ $chart['dataset']['label'] }}',
                                data: {!! json_encode($chart['dataset']['data']) !!},
                                backgroundColor: '{{ $chart['dataset']['backgroundColor'] }}',
                                borderColor: '{{ $chart['dataset']['borderColor'] }}',
                                borderWidth: 2,
                                fill: {{ isset($chart['dataset']['fill']) ? json_encode($chart['dataset']['fill']) : 'true' }},
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
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

.dashboard-header,
.bg-dashboard-gradient {
    background: linear-gradient(135deg, var(--kp-blue) 0%, var(--kp-green) 100%);
}

.text-kp-blue {
    color: var(--kp-blue) !important;
}

.text-kp-green {
    color: var(--kp-green) !important;
}

.bg-kp-green {
    background-color: var(--kp-green) !important;
}

.text-purple {
    color: #6f42c1 !important;
}

.dashboard-card {
    border-radius: 14px;
}

.responsive-heading {
    font-size: clamp(1.25rem, 4vw, 2rem);
    line-height: 1.2;
}

.responsive-subheading {
    font-size: clamp(1rem, 3vw, 1.35rem);
    line-height: 1.3;
}

.responsive-text {
    font-size: clamp(0.875rem, 2vw, 1rem);
}

.responsive-text-sm {
    font-size: clamp(0.75rem, 1.5vw, 0.875rem);
}

.responsive-icon {
    font-size: clamp(1.5rem, 4vw, 2.5rem);
}

.responsive-empty-icon {
    font-size: clamp(2.5rem, 8vw, 5rem);
}

.responsive-badge {
    font-size: clamp(0.65rem, 1.5vw, 0.75rem);
    padding: 0.35rem 0.65rem;
    border-radius: 9999px;
}

.responsive-btn {
    font-size: clamp(0.75rem, 2vw, 0.875rem);
    padding: 0.45rem 0.9rem;
    min-height: 40px;
    white-space: nowrap;
}

.stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #e9ecef;
}

.stat-card:hover,
.action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}

.responsive-stat-icon {
    width: clamp(2.5rem, 6vw, 3.75rem);
    height: clamp(2.5rem, 6vw, 3.75rem);
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon-primary {
    background: rgba(0, 102, 179, 0.1);
    color: var(--kp-blue);
}

.stat-icon-success {
    background: rgba(0, 150, 57, 0.1);
    color: var(--kp-green);
}

.stat-icon-info {
    background: rgba(23, 162, 184, 0.1);
    color: #17a2b8;
}

.stat-icon-warning {
    background: rgba(255, 215, 0, 0.18);
    color: #856404;
}

.stat-icon-danger {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.stat-icon-secondary {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
}

.responsive-stat-value {
    font-size: clamp(1.25rem, 3.5vw, 1.75rem);
}

.action-card {
    display: block;
    padding: 1rem;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
    height: 100%;
    text-align: center;
}

.action-icon {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
    color: white;
    font-size: 1.2rem;
}

.action-primary {
    background: var(--kp-blue);
}

.action-success {
    background: var(--kp-green);
}

.action-info {
    background: #17a2b8;
}

.action-warning {
    background: var(--kp-yellow);
    color: var(--kp-dark);
}

.action-danger {
    background: #dc3545;
}

.action-purple {
    background: #6f42c1;
}

.action-dark {
    background: #343a40;
}

.currency-row {
    font-size: clamp(0.875rem, 2vw, 1rem);
    line-height: 1.4;
}

.currency-code {
    font-weight: 500;
    letter-spacing: 0.5px;
}

.currency-row:not(:last-child) {
    border-bottom: 1px dashed rgba(0,0,0,0.05);
    padding-bottom: 0.25rem;
}

.activity-timeline {
    position: relative;
}

.timeline-item {
    display: flex;
    position: relative;
}

.timeline-marker {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    margin-right: 1rem;
}

.timeline-content {
    flex-grow: 1;
    padding-top: 0.25rem;
}

.responsive-table-header {
    font-size: 0.8rem;
    padding: 0.75rem;
}

.table td {
    padding: 0.75rem;
    vertical-align: middle;
}

.chart-container {
    position: relative;
    height: 240px;
    width: 100%;
}

.progress {
    background-color: #e9ecef;
    border-radius: 9999px;
    overflow: hidden;
}

.progress-bar {
    transition: width 0.6s ease;
}

@media (max-width: 576px) {
    .col-6 {
        width: 100%;
    }

    .action-card {
        text-align: left;
    }

    .action-icon {
        margin-left: 0;
    }
}

@media print {
    .dashboard-header,
    .action-card,
    .btn,
    .badge {
        display: none !important;
    }

    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {
        new bootstrap.Tooltip(element);
    });

    document.querySelectorAll('.alert.alert-dismissible').forEach(function (alertElement) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alertElement);
            bsAlert.close();
        }, 10000);
    });
});
</script>
@endsection
