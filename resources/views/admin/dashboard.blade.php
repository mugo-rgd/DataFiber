@extends('layouts.app')

@section('title', 'Dashboard - Dark Fibre CRM')

@section('content')
<div class="container-fluid px-0">
    <!-- Dashboard Header - Fully Responsive -->
    <div class="dashboard-header bg-gradient-primary text-white py-2 py-sm-3 py-md-4">
        <div class="container-fluid px-3 px-sm-4 px-md-5">
            <div class="row align-items-center g-2 g-md-3">
                <div class="col-12 col-lg-8 mb-2 mb-lg-0">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="header-icon me-2 me-sm-3 mb-1 mb-sm-0">
                            <i class="fas fa-tachometer-alt responsive-icon"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h1 class="responsive-heading mb-1">{{ Auth::user()->full_role_name }} Dashboard</h1>
                            <p class="mb-0 opacity-75 responsive-text">Welcome back, <strong class="name-text">{{ Auth::user()->name }}</strong>!</p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-1 gap-sm-2 gap-md-3 mt-2">
                        <span class="badge bg-white text-primary responsive-badge">
                            <i class="fas fa-user me-1"></i> <span class="role-text">{{ Auth::user()->full_role_name }}</span>
                        </span>
                        <span class="opacity-75 responsive-text-sm">
                            <i class="fas fa-calendar me-1"></i> <span class="date-text">{{ now()->format('l, F j, Y') }}</span>
                        </span>
                        @if(Auth::user()->is_online)
                            <span class="badge bg-success responsive-badge">
                                <i class="fas fa-circle me-1"></i> Online
                            </span>
                        @endif
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

    <!-- Welcome Section - Responsive -->
    <div class="container-fluid px-3 px-sm-4 px-md-5 py-3 py-sm-4">
        <div class="row mb-3 mb-sm-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 p-sm-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8 mb-3 mb-lg-0">
                                <h3 class="responsive-subheading mb-2">
                                    @if(Auth::user()->isSystemAdmin())
                                        <i class="fas fa-shield-alt text-primary me-2"></i>System Administration & Analytics
                                    @elseif(Auth::user()->isMarketingAdmin())
                                        <i class="fas fa-chart-line text-success me-2"></i>Marketing Analytics & Customer Insights
                                    @elseif(Auth::user()->isTechnicalAdmin())
                                        <i class="fas fa-network-wired text-info me-2"></i>Technical Operations & Network Monitoring
                                    @elseif(Auth::user()->isFinance())
                                        <i class="fas fa-money-bill-wave text-warning me-2"></i>Financial Management & Reporting
                                    @elseif(Auth::user()->isDesigner())
                                        <i class="fas fa-pencil-ruler text-purple me-2"></i>Network Design & Quotation Center
                                    @elseif(Auth::user()->isSurveyor())
                                        <i class="fas fa-map-marked-alt text-danger me-2"></i>Field Survey Operations
                                    @elseif(Auth::user()->isTechnician())
                                        <i class="fas fa-tools text-secondary me-2"></i>Field Maintenance & Operations
                                    @elseif(Auth::user()->isAccountManager())
                                        <i class="fas fa-handshake text-info me-2"></i>Customer Relationship Management
                                        @elseif(Auth::user()->isDebtManager())
                                        <i class="fas fa-handshake text-info me-2"></i>Debt Management
                                    @else
                                        <i class="fas fa-user-circle text-primary me-2"></i>Customer Portal & Services
                                    @endif
                                </h3>
                                <p class="text-muted mb-3 responsive-text">
                                    Access your tools, view metrics, and manage your tasks from this centralized dashboard.
                                </p>

                                @if(Auth::user()->isCustomer())
                                <div class="profile-progress">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="responsive-text-sm">Profile Completion</span>
                                        <span class="responsive-text-sm fw-bold">{{ Auth::user()->profile_completion_percentage }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-{{ Auth::user()->profile_completion_percentage >= 80 ? 'success' : (Auth::user()->profile_completion_percentage >= 50 ? 'warning' : 'danger') }}"
                                             role="progressbar"
                                             style="width: {{ Auth::user()->profile_completion_percentage }}%">
                                        </div>
                                    </div>
                                    @if(Auth::user()->profile_completion_percentage < 100)
                                        <small class="text-muted responsive-text-sm">Complete your profile to unlock all features</small>
                                    @endif
                                </div>
                                @endif
                            </div>
                            <div class="col-lg-4 text-center">
                                <div class="welcome-illustration">
                                    <i class="fas fa-network-wired responsive-empty-icon text-primary opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards - Dynamic Grid -->
        <!-- Statistics Cards - Dynamic Grid -->
<div class="row g-2 g-sm-3 g-md-4 mb-3 mb-sm-4">
    @foreach($stats as $key => $stat)
        @if(is_array($stat) && isset($stat['color']) && isset($stat['title']) && isset($stat['value']))
        <div class="col-6 col-md-4 col-lg-2 mb-2 mb-sm-3">
            <div class="stat-card bg-white rounded-lg shadow-sm border-0 h-100">
                <div class="stat-card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-start mb-2 mb-sm-3">
                        <div class="stat-icon bg-{{ $stat['color'] }}-light rounded-circle responsive-stat-icon">
                            <i class="fas fa-{{ $stat['icon'] ?? 'chart-bar' }} text-{{ $stat['color'] }}"></i>
                        </div>
                        @if(isset($stat['trend']))
                        <div class="trend-indicator">
                            <span class="badge bg-{{ $stat['trend']['color'] ?? 'muted' }} responsive-badge">
                                <i class="fas fa-{{ $stat['trend']['icon'] ?? 'chart-line' }} me-1"></i>
                                {{ $stat['trend']['value'] ?? 0 }}%
                            </span>
                        </div>
                        @endif
                    </div>
                    <h6 class="stat-title text-muted text-uppercase small mb-1 mb-sm-2">{{ $stat['title'] }}</h6>

                    {{-- Handle Currency Values with Multiple Currencies --}}
                    @if(isset($stat['is_currency']) && $stat['is_currency'] === true)
                        <div class="stat-value fw-bold mb-2 mb-sm-3 text-dark responsive-stat-value">
                            @if(is_array($stat['value']) && count($stat['value']) > 0)
                                @foreach($stat['value'] as $currency => $amount)
                                    <div class="currency-row d-flex justify-content-between align-items-center mb-1"
                                         data-bs-toggle="tooltip"
                                         data-bs-placement="top"
                                         title="{{ $currency === 'USD' ? 'US Dollar' : ($currency === 'KSH' ? 'Kenyan Shilling' : $currency) }}">
                                        <span class="currency-code small text-muted">{{ $currency }}</span>
                                        <span class="currency-amount fw-bold">
                                            {{ $currency === 'USD' ? '$' : ($currency === 'KSH' ? 'KSh' : $currency . ' ') }}
                                            {{ number_format($amount, 2) }}
                                        </span>
                                    </div>
                                @endforeach
                                @if(count($stat['value']) > 1)
                                    <div class="currency-total mt-2 pt-1 border-top">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Showing amounts in original currencies
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
                        @if(isset($stat['subtitle']))
                            <div class="stat-subtitle responsive-text-sm text-muted mt-2">
                                <small>{{ $stat['subtitle'] }}</small>
                            </div>
                        @endif

                    {{-- Handle Percentage Values --}}
                    @elseif(isset($stat['is_percentage']) && $stat['is_percentage'] === true)
                        <div class="stat-value fw-bold mb-2 mb-sm-3 text-dark responsive-stat-value">
                            {{ number_format($stat['value'], 1) }}%
                        </div>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-{{ $stat['color'] }}"
                                 role="progressbar"
                                 style="width: {{ $stat['value'] }}%"
                                 aria-valuenow="{{ $stat['value'] }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                        @if(isset($stat['subtitle']))
                            <div class="stat-subtitle responsive-text-sm text-muted mt-2">{{ $stat['subtitle'] }}</div>
                        @endif

                    {{-- Handle Regular Numeric Values --}}
                    @else
                        <div class="stat-value fw-bold mb-2 mb-sm-3 text-dark responsive-stat-value">
                            {{ number_format($stat['value']) }}
                        </div>
                        @if(isset($stat['subtitle']))
                            <div class="stat-subtitle responsive-text-sm text-muted">{{ $stat['subtitle'] }}</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif
    @endforeach
</div>

        <!-- Quick Actions Section - Dynamic Grid -->
        <div class="row mb-3 mb-sm-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-3 mb-sm-4">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 responsive-subheading">
                                <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                            </h5>
                            <span class="badge bg-warning responsive-badge d-none d-sm-inline">{{ Auth::user()->getQuickActionCount() }} Actions</span>
                        </div>
                    </div>
                    <div class="card-body p-2 p-sm-3 p-sm-4">
                        @php
                            $quickActions = [];

                            // System Admin Actions
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
                                            [
                                                'label' => 'View All',
                                                'route' => route('conversion-data.index'),
                                                'icon' => 'list'
                                            ],
                                            [
                                                'label' => 'Summary',
                                                'route' => route('conversion-data.summary'),
                                                'icon' => 'chart-bar'
                                            ],
                                            [
                                                'label' => 'Add New',
                                                'route' => route('conversion-data.create'),
                                                'icon' => 'plus'
                                            ]
                                        ]
                                    ]
                                ];
                            }
                            // Technical Admin Actions
                            elseif (Auth::user()->isTechnicalAdmin()) {
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
                                        'desc' => 'View and manage all users'
                                    ],
                                    [
                                        'title' => 'Link Inventory',
                                        'icon' => 'tachometer-alt',
                                        'color' => 'danger',
                                        'desc' => 'Fibre link management',
                                        'links' => [
                                            [
                                                'label' => 'View All',
                                                'route' => route('conversion-data.index'),
                                                'icon' => 'list'
                                            ],
                                            [
                                                'label' => 'Summary',
                                                'route' => route('conversion-data.summary'),
                                                'icon' => 'chart-bar'
                                            ],
                                            [
                                                'label' => 'Add New',
                                                'route' => route('conversion-data.create'),
                                                'icon' => 'plus'
                                            ]
                                        ]
                                    ]
                                ];
                            }
                            // Account Manager Actions
                            elseif (Auth::user()->isAccountManager()) {
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
                                            [
                                                'label' => 'View All',
                                                'route' => route('conversion-data.index'),
                                                'icon' => 'list'
                                            ],
                                            [
                                                'label' => 'Summary',
                                                'route' => route('conversion-data.summary'),
                                                'icon' => 'chart-bar'
                                            ],
                                            [
                                                'label' => 'Add New',
                                                'route' => route('conversion-data.create'),
                                                'icon' => 'plus'
                                            ]
                                        ]
                                    ]
                                ];
                            }
                            // Default Admin Actions
                            else {
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
                                        'desc' => 'Assign engineers & view requests'
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
                                        'desc' => 'View profiles & assign managers'
                                    ],
                                    [
                                        'title' => 'Link Inventory',
                                        'icon' => 'tachometer-alt',
                                        'color' => 'danger',
                                        'desc' => 'Fibre link management',
                                        'links' => [
                                            [
                                                'label' => 'View All',
                                                'route' => route('conversion-data.index'),
                                                'icon' => 'list'
                                            ],
                                            [
                                                'label' => 'Summary',
                                                'route' => route('conversion-data.summary-report'),
                                                'icon' => 'chart-bar'
                                            ],
                                            [
                                                'label' => 'Add New',
                                                'route' => route('conversion-data.create'),
                                                'icon' => 'plus'
                                            ]
                                        ]
                                    ]
                                ];
                            }
                        @endphp

                        <div class="row g-2 g-sm-3 g-md-4">
                            @foreach($quickActions as $action)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2 col-xxl-2">
                                @if(isset($action['links']))
                                    {{-- Multiple Links Card --}}
                                    <div class="action-card">
                                        <div class="action-icon bg-{{ $action['color'] }}">
                                            <i class="fas fa-{{ $action['icon'] }}"></i>
                                        </div>
                                        <div class="action-content">
                                            <h6 class="responsive-text">{{ $action['title'] }}</h6>
                                            <p class="text-muted small mb-2">{{ $action['desc'] }}</p>

                                            {{-- Multiple Links --}}
                                            <div class="action-links mt-2">
                                                @foreach($action['links'] as $link)
                                                    <a href="{{ $link['route'] }}" class="btn btn-sm btn-outline-{{ $action['color'] }} w-100 mb-1">
                                                        <i class="fas fa-{{ $link['icon'] }} me-1"></i> {{ $link['label'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    {{-- Single Link Card --}}
                                    <a href="{{ $action['link'] }}" class="action-card">
                                        <div class="action-icon bg-{{ $action['color'] }}">
                                            <i class="fas fa-{{ $action['icon'] }}"></i>
                                        </div>
                                        <div class="action-content">
                                            <h6 class="responsive-text">{{ $action['title'] }}</h6>
                                            <p class="text-muted small d-none d-sm-block">{{ $action['desc'] }}</p>
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

        <!-- Main Content Area - Responsive Grid -->
        <div class="row g-3 g-sm-4">
            <!-- Left Column: Recent Activity & Items -->
            <div class="col-12 col-lg-6">
                <!-- Recent Activity -->
                @if(isset($recentActivities) && count($recentActivities) > 0)
                <div class="card border-0 shadow-sm mb-3 mb-sm-4">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="mb-0 responsive-subheading">
                                <i class="fas fa-clock text-primary me-2"></i>Recent Activity
                            </h5>
                            <a href="#" class="btn btn-sm btn-outline-primary responsive-btn mt-1 mt-sm-0">
                                <span class="d-none d-sm-inline">View All</span>
                                <span class="d-inline d-sm-none">All</span>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="activity-timeline p-2 p-sm-3">
                            @foreach($recentActivities as $activity)
                            <div class="timeline-item mb-2 mb-sm-3">
                                <div class="timeline-marker bg-{{ $activity['color'] }}">
                                    <i class="fas fa-{{ $activity['icon'] }} responsive-icon-sm"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-text responsive-text">{!! $activity['text'] !!}</div>
                                    <div class="timeline-time text-muted responsive-text-sm">{{ $activity['time'] }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Recent Items -->
                @if(isset($recentItems) && $recentItems->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="mb-0 responsive-subheading">
                                <i class="fas fa-list text-{{ Auth::user()->getRoleBadgeColor() }} me-2"></i>{{ $recentItemsTitle }}
                            </h5>
                            <a href="{{ $recentItemsLink }}" class="btn btn-sm btn-outline-{{ Auth::user()->getRoleBadgeColor() }} responsive-btn mt-1 mt-sm-0">
                                <span class="d-none d-sm-inline">View All</span>
                                <span class="d-inline d-sm-none">All</span>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        @foreach($recentItemsColumns as $column)
                                        <th class="border-0 responsive-table-header">{{ $column }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentItems as $item)
                                    <tr class="border-bottom">
                                        @foreach($recentItemsColumns as $columnKey => $columnName)
                                        <td class="responsive-text-sm">
                                            @if($columnKey === 'status')
                                                <span class="badge bg-{{ $item->getStatusColor() }} responsive-badge">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            @elseif($columnKey === 'amount')
                                                <span class="fw-bold">KSh {{ number_format($item->amount, 2) }}</span>
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

            <!-- Right Column: System Status & Notifications -->
            <div class="col-12 col-lg-6">
                <!-- System Health -->
                @if(isset($systemHealth) && count($systemHealth) > 0)
                <div class="card border-0 shadow-sm mb-3 mb-sm-4">
                    <div class="card-header bg-{{ Auth::user()->getRoleBadgeColor() }} text-white border-0 py-2 py-sm-3">
                        <h5 class="mb-0 responsive-subheading">
                            <i class="fas fa-heartbeat me-2"></i>System Status
                        </h5>
                    </div>
                    <div class="card-body p-3 p-sm-4">
                        @foreach($systemHealth as $healthItem)
                        <div class="system-health-item mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold responsive-text">{{ $healthItem['label'] }}</span>
                                <span class="badge bg-{{ $healthItem['status_color'] }} responsive-badge">
                                    {{ $healthItem['status'] }}
                                </span>
                            </div>
                            @if(isset($healthItem['value']))
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $healthItem['status_color'] }}"
                                     role="progressbar"
                                     style="width: {{ $healthItem['value'] }}%">
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Notifications & Alerts -->
                <div class="card border-0 shadow-sm mb-3 mb-sm-4">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <h5 class="mb-0 responsive-subheading">
                            <i class="fas fa-bell text-warning me-2"></i>Notifications & Alerts
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if(isset($notifications) && count($notifications) > 0)
                            @foreach($notifications as $notification)
                            <div class="alert alert-{{ $notification['type'] }} alert-dismissible fade show m-3 rounded">
                                <div class="d-flex flex-column flex-sm-row">
                                    <div class="alert-icon me-0 me-sm-3 mb-2 mb-sm-0">
                                        <i class="fas fa-{{ $notification['icon'] }} responsive-icon-sm"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="responsive-text">{!! $notification['message'] !!}</div>
                                    </div>
                                    <button type="button" class="btn-close mt-2 mt-sm-0 align-self-start" data-bs-dismiss="alert"></button>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-4 py-sm-5">
                                <i class="fas fa-check-circle text-success responsive-empty-icon mb-3"></i>
                                <p class="text-muted mb-0 responsive-text">All systems are operating normally</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Performance Metrics -->
                @if(isset($performanceMetrics) && count($performanceMetrics) > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <h5 class="mb-0 responsive-subheading">
                            <i class="fas fa-chart-line text-info me-2"></i>Performance Metrics
                        </h5>
                    </div>
                    <div class="card-body p-3 p-sm-4">
                        @foreach($performanceMetrics as $metric)
                        <div class="metric-item mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="responsive-text-sm">{{ $metric['label'] }}</span>
                                <span class="fw-bold responsive-text">{{ $metric['value'] }}{{ $metric['unit'] ?? '' }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $metric['color'] }}"
                                     role="progressbar"
                                     style="width: {{ $metric['percentage'] }}%">
                                </div>
                            </div>
                            <small class="text-muted responsive-text-sm">Target: {{ $metric['target'] }}{{ $metric['unit'] ?? '' }}</small>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Charts Section -->
        @if(isset($charts) && count($charts) > 0)
        <div class="row g-3 g-sm-4 mt-3 mt-sm-4">
            @foreach($charts as $chart)
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <h5 class="mb-0 responsive-subheading">
                            <i class="fas fa-chart-{{ $chart['type'] }} text-primary me-2"></i>{{ $chart['title'] }}
                        </h5>
                    </div>
                    <div class="card-body p-2 p-sm-3">
                        <div class="chart-container">
                            <canvas id="{{ $chart['id'] }}" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Additional Sections -->
        @if(isset($additionalSections) && count($additionalSections) > 0)
        <div class="row g-3 g-sm-4 mt-3 mt-sm-4">
            @foreach($additionalSections as $section)
            <div class="col-12 col-lg-{{ $section['size'] ?? 6 }}">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-{{ $section['color'] ?? 'primary' }} text-white border-0 py-2 py-sm-3">
                        <h5 class="mb-0 responsive-subheading">
                            <i class="fas fa-{{ $section['icon'] }} me-2"></i>{{ $section['title'] }}
                        </h5>
                    </div>
                    <div class="card-body p-3 p-sm-4">
                        <div class="responsive-text">{!! $section['content'] !!}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if(isset($charts))
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($charts as $chart)
        var ctx = document.getElementById('{{ $chart['id'] }}');
        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: '{{ $chart['type'] }}',
                data: {
                    labels: {!! json_encode($chart['labels']) !!},
                    datasets: [{
                        label: '{{ $chart['dataset']['label'] }}',
                        data: {!! json_encode($chart['dataset']['data']) !!},
                        backgroundColor: '{{ $chart['dataset']['backgroundColor'] }}',
                        borderColor: '{{ $chart['dataset']['borderColor'] }}',
                        borderWidth: 2,
                        fill: {{ $chart['dataset']['fill'] ?? 'true' }},
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
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
@endif
</script>

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

.responsive-empty-icon {
    font-size: clamp(2.5rem, 8vw, 5rem);
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

/* Activity Timeline */
.activity-timeline {
    position: relative;
}

.timeline-item {
    display: flex;
    padding-bottom: clamp(1rem, 2vw, 1.5rem);
    position: relative;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    width: clamp(2rem, 4vw, 2.5rem);
    height: clamp(2rem, 4vw, 2.5rem);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    margin-right: clamp(0.75rem, 1.5vw, 1rem);
}

.timeline-content {
    flex-grow: 1;
    padding-top: clamp(0.25rem, 0.5vw, 0.5rem);
}

.timeline-text {
    margin-bottom: 0.25rem;
    color: #2d3748;
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

/* Chart Container */
.chart-container {
    position: relative;
    height: clamp(180px, 30vw, 250px);
    width: 100%;
}

/* Color Classes */
.bg-primary-light { background-color: rgba(78, 115, 223, 0.1); }
.bg-success-light { background-color: rgba(28, 200, 138, 0.1); }
.bg-info-light { background-color: rgba(54, 185, 204, 0.1); }
.bg-warning-light { background-color: rgba(246, 194, 62, 0.1); }
.bg-danger-light { background-color: rgba(231, 74, 59, 0.1); }
.bg-purple-light { background-color: rgba(111, 66, 193, 0.1); }
.bg-dark-light { background-color: rgba(52, 58, 64, 0.1); }

.bg-purple { background-color: #6f42c1 !important; }

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
    .responsive-badge {
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

    .chart-container {
        height: 150px;
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

    .chart-container {
        height: 200px;
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

/* Alert adjustments for mobile */
@media (max-width: 767.98px) {
    .alert {
        margin: 0.5rem;
    }

    .alert .d-flex {
        flex-direction: column;
    }

    .alert-icon {
        margin-bottom: 0.5rem;
        text-align: center;
    }
}

/* Currency Display Styles */
.currency-row {
    font-size: clamp(0.875rem, 2vw, 1rem);
    line-height: 1.4;
    transition: all 0.2s ease;
}

.currency-code {
    font-weight: 500;
    letter-spacing: 0.5px;
    opacity: 0.7;
}

.currency-amount {
    font-weight: 700;
}

.currency-row:not(:last-child) {
    border-bottom: 1px dashed rgba(0,0,0,0.05);
    padding-bottom: 0.25rem;
    margin-bottom: 0.25rem;
}

/* Hover effect for currency rows */
.currency-row:hover {
    background-color: rgba(0,0,0,0.02);
    transform: translateX(2px);
}

/* Responsive currency display */
@media (max-width: 576px) {
    .currency-row {
        font-size: 0.75rem;
    }

    .currency-amount {
        font-size: 0.875rem;
    }
}

/* Progress bar for collection rate */
.progress {
    background-color: #e9ecef;
    border-radius: 9999px;
    overflow: hidden;
}

.progress-bar {
    transition: width 0.6s ease;
}

/* Currency Display Styles for Payments */
.currency-row {
    font-size: clamp(0.875rem, 2vw, 1rem);
    line-height: 1.4;
    transition: all 0.2s ease;
    padding: 0.25rem 0;
}

.currency-code {
    font-weight: 600;
    letter-spacing: 0.5px;
    opacity: 0.7;
    font-size: 0.75rem;
}

.currency-amount {
    font-weight: 700;
    font-size: clamp(0.875rem, 2.5vw, 1rem);
}

.currency-row:not(:last-child) {
    border-bottom: 1px dashed rgba(0,0,0,0.08);
    margin-bottom: 0.25rem;
}

.currency-total {
    border-top: 1px solid rgba(0,0,0,0.1);
    font-size: 0.7rem;
}

/* Hover effect for currency rows */
.currency-row:hover {
    background-color: rgba(0,0,0,0.02);
    transform: translateX(2px);
    cursor: help;
}

/* Responsive currency display */
@media (max-width: 576px) {
    .currency-row {
        font-size: 0.7rem;
        padding: 0.15rem 0;
    }

    .currency-amount {
        font-size: 0.8rem;
    }

    .currency-code {
        font-size: 0.65rem;
    }
}

/* Progress bar for collection rate */
.progress {
    background-color: #e9ecef;
    border-radius: 9999px;
    overflow: hidden;
}

.progress-bar {
    transition: width 0.6s ease;
}

/* Stat Card Enhancements */
.stat-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border: 1px solid #e9ecef;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}

.stat-subtitle {
    font-size: 0.7rem;
    opacity: 0.8;
}

/* Badge colors for payment status */
.badge.bg-success {
    background-color: #28a745 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}

.badge.bg-info {
    background-color: #17a2b8 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap components
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Auto-dismiss alerts after 10 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 10000);
    });

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

    // Update date and role format based on screen size
    function updateTextDisplay() {
        const dateElements = document.querySelectorAll('.date-text');
        const roleElements = document.querySelectorAll('.role-text');
        const nameElements = document.querySelectorAll('.name-text');
        const now = new Date();

        dateElements.forEach(el => {
            if (window.innerWidth < 576) {
                el.textContent = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            } else if (window.innerWidth < 768) {
                el.textContent = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            } else if (window.innerWidth < 992) {
                el.textContent = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
            } else {
                el.textContent = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
            }
        });

        roleElements.forEach(el => {
            const role = el.textContent;
            if (window.innerWidth < 576) {
                // Shorten role name on very small screens
                el.textContent = role.replace('Administrator', 'Admin').replace('Manager', 'Mgr').replace('Technician', 'Tech');
            } else if (window.innerWidth < 768) {
                // Keep reasonable length
                el.textContent = role.length > 20 ? role.substring(0, 18) + '...' : role;
            }
        });

        nameElements.forEach(el => {
            const name = el.textContent;
            if (window.innerWidth < 400) {
                // Show only first name on very small screens
                el.textContent = name.split(' ')[0];
            } else if (window.innerWidth < 576) {
                // Truncate long names
                el.textContent = name.length > 15 ? name.substring(0, 13) + '...' : name;
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
        document.querySelectorAll('.btn, .action-card, .badge').forEach(el => {
            if (el.classList.contains('btn') || el.classList.contains('action-card')) {
                el.style.minHeight = '44px';
            }
        });

        // Make table rows more tappable on mobile
        if (window.innerWidth < 768 && document.querySelector('.table tbody')) {
            document.querySelectorAll('.table tbody tr').forEach(row => {
                row.style.cursor = 'pointer';
                const firstLink = row.querySelector('a');
                if (firstLink) {
                    row.addEventListener('click', function(e) {
                        if (!e.target.closest('a') && !e.target.closest('button') && !e.target.closest('.badge')) {
                            window.location = firstLink.href;
                        }
                    });
                }
            });
        }
    }

    // Prevent horizontal scroll
    document.body.style.overflowX = 'hidden';
    document.documentElement.style.overflowX = 'hidden';

    // Initialize and update on resize
    updateScaleFactor();
    updateTextDisplay();
    updateButtonText();

    // Debounced resize handler
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            updateScaleFactor();
            updateTextDisplay();
            updateButtonText();
        }, 100);
    });

    // Handle orientation changes
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            updateScaleFactor();
            updateTextDisplay();
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
    document.querySelectorAll('.stat-card, .action-card, .card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });

    // Auto-refresh dashboard every 5 minutes
    const refreshInterval = 5 * 60 * 1000; // 5 minutes
    let refreshTimer = setTimeout(function() {
        window.location.reload();
    }, refreshInterval);

    // Reset timer on user activity
    ['click', 'scroll', 'keypress', 'mousemove'].forEach(event => {
        document.addEventListener(event, function() {
            clearTimeout(refreshTimer);
            refreshTimer = setTimeout(function() {
                window.location.reload();
            }, refreshInterval);
        }, { passive: true });
    });

    // Handle chart resizing on window resize
    let chartResizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(chartResizeTimer);
        chartResizeTimer = setTimeout(function() {
            // Trigger Chart.js resize
            if (window.Chart && window.Chart.instances) {
                Object.values(window.Chart.instances).forEach(chart => {
                    chart.resize();
                });
            }
        }, 250);
    });
});
</script>
@endsection
