@extends('layouts.app')

@section('title', 'Dashboard - Dark Fibre CRM')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </h1>
        <div class="d-flex align-items-center">
            <span class="badge bg-{{ Auth::user()->getRoleBadgeColor() }} badge-pill p-2 mr-3">
                <i class="fas fa-user me-1"></i> {{ Auth::user()->full_role_name }}
            </span>
            <span class="text-muted">{{ now()->format('l, F j, Y') }}</span>
            @if(Auth::user()->is_online)
                <span class="badge bg-success badge-pill p-2 ml-2">
                    <i class="fas fa-circle me-1"></i> Online
                </span>
            @endif
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Welcome back, {{ Auth::user()->name }}!
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if(Auth::user()->isSystemAdmin())
                                    System Administration & Analytics
                                @elseif(Auth::user()->isMarketingAdmin())
                                    Marketing Analytics & Customer Insights
                                @elseif(Auth::user()->isTechnicalAdmin())
                                    Technical Operations & Network Monitoring
                                @elseif(Auth::user()->isFinance())
                                    Financial Management & Reporting
                                @elseif(Auth::user()->isDesigner())
                                    Network Design & Quotation Center
                                @elseif(Auth::user()->isSurveyor())
                                    Field Survey Operations
                                @elseif(Auth::user()->isTechnician())
                                    Field Maintenance & Operations
                                @elseif(Auth::user()->isAccountManager())
                                    Customer Relationship Management
                                @else
                                    Customer Portal & Services
                                @endif
                            </div>
                            @if(Auth::user()->isCustomer())
                                <div class="text-sm text-muted mt-2">
                                    Profile Completion:
                                    <div class="progress mt-1" style="height: 8px; width: 200px;">
                                        <div class="progress-bar bg-{{ Auth::user()->profile_completion_percentage >= 80 ? 'success' : (Auth::user()->profile_completion_percentage >= 50 ? 'warning' : 'danger') }}"
                                             role="progressbar"
                                             style="width: {{ Auth::user()->profile_completion_percentage }}%">
                                        </div>
                                    </div>
                                    <small>{{ Auth::user()->profile_completion_percentage }}% Complete</small>
                                </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-network-wired fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        @foreach($stats as $key => $stat)
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="card border-left-{{ $stat['color'] }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $stat['color'] }} text-uppercase mb-1">
                                {{ $stat['title'] }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if(in_array($key, ['total_revenue', 'revenue_this_month', 'pending_payments', 'overdue_payments', 'revenue_managed', 'average_deal_size', 'quoted_amount']))
                                    ${{ number_format($stat['value'], 2) }}
                                @elseif(in_array($key, ['conversion_rate', 'collection_rate', 'network_uptime', 'equipment_health', 'customer_growth_rate']))
                                    {{ $stat['value'] }}%
                                @else
                                    {{ number_format($stat['value']) }}
                                @endif
                            </div>
                            @if(isset($stat['trend']))
                            <div class="text-xs text-{{ $stat['trend']['color'] }}">
                                <i class="fas fa-{{ $stat['trend']['icon'] }} me-1"></i>
                                {{ $stat['trend']['value'] }}% from last period
                            </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-{{ $stat['icon'] }} fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Role-Specific Charts & Visualizations -->
    @if(isset($charts) && count($charts) > 0)
    <div class="row mt-4">
        @foreach($charts as $chart)
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $chart['title'] }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="{{ $chart['id'] }}" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Quick Actions Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-bolt me-1"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- System Admin Actions -->
                        @if(Auth::user()->isSystemAdmin())
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-user-plus"></i> Add User
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.settings') }}" class="btn btn-success w-100">
                                <i class="fas fa-cogs"></i> System Settings
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.system-reports') }}" class="btn btn-info w-100">
                                <i class="fas fa-chart-bar"></i> System Reports
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.audit-logs') }}" class="btn btn-warning w-100">
                                <i class="fas fa-clipboard-list"></i> Audit Logs
                            </a>
                        </div>

                        <!-- Account Manager Admin Actions -->
@elseif(Auth::user()->isAccountManagerAdmin())
<div class="col-md-3 mb-3">
    <a href="{{ route('marketing-admin.analytics') }}" class="btn btn-primary w-100">
        <i class="fas fa-chart-pie"></i> View Analytics
    </a>
</div>
<div class="col-md-3 mb-3">
    <a href="{{ route('marketing-admin.account-managers') }}" class="btn btn-success w-100">
        <i class="fas fa-user-tie"></i> Manage Team
    </a>
</div>
<div class="col-md-3 mb-3">
    <a href="{{ route('marketing-admin.campaigns') }}" class="btn btn-info w-100">
        <i class="fas fa-bullhorn"></i> Campaigns
    </a>
</div>
<div class="col-md-3 mb-3">
    <a href="{{ route('marketing-admin.reports') }}" class="btn btn-warning w-100">
        <i class="fas fa-file-alt"></i> Reports
    </a>
</div>

                        <!-- Marketing Admin Actions -->
                        @elseif(Auth::user()->isMarketingAdmin())
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('marketing-admin.analytics') }}" class="btn btn-primary w-100">
                                <i class="fas fa-chart-pie"></i> View Analytics
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('marketing-admin.campaigns') }}" class="btn btn-success w-100">
                                <i class="fas fa-bullhorn"></i> Manage Campaigns
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('marketing-admin.customer-insights') }}" class="btn btn-info w-100">
                                <i class="fas fa-users"></i> Customer Insights
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('marketing-admin.reports') }}" class="btn btn-warning w-100">
                                <i class="fas fa-file-alt"></i> Marketing Reports
                            </a>
                        </div>

                        <!-- Technical Admin Actions -->
                        @elseif(Auth::user()->isTechnicalAdmin())
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('technical-admin.network-monitor') }}" class="btn btn-primary w-100">
                                <i class="fas fa-network-wired"></i> Network Monitor
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('technical-admin.infrastructure') }}" class="btn btn-success w-100">
                                <i class="fas fa-server"></i> Infrastructure
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('technical-admin.technical-reports') }}" class="btn btn-info w-100">
                                <i class="fas fa-file-alt"></i> Technical Reports
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('technical-admin.system-health') }}" class="btn btn-warning w-100">
                                <i class="fas fa-heartbeat"></i> System Health
                            </a>
                        </div>

                        <!-- Finance Actions -->
                        @elseif(Auth::user()->isFinance())
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('finance.billing.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-plus-circle"></i> Create Billing
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('finance.transactions.create') }}" class="btn btn-success w-100">
                                <i class="fas fa-exchange-alt"></i> Record Transaction
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('finance.financial-reports') }}" class="btn btn-info w-100">
                                <i class="fas fa-chart-line"></i> Financial Reports
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('finance.auto-billing') }}" class="btn btn-warning w-100">
                                <i class="fas fa-robot"></i> Auto Billing
                            </a>
                        </div>

                        <!-- Designer Actions -->
                        @elseif(Auth::user()->isDesigner())
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('designer.requests') }}" class="btn btn-primary w-100">
                                <i class="fas fa-tasks"></i> Design Requests
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('designer.quotations') }}" class="btn btn-success w-100">
                                <i class="fas fa-file-invoice-dollar"></i> My Quotations
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-info w-100">
                                <i class="fas fa-project-diagram"></i> Active Projects
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('designer.profile') }}" class="btn btn-warning w-100">
                                <i class="fas fa-user-cog"></i> My Profile
                            </a>
                        </div>

                        <!-- Surveyor Actions -->
                        @elseif(Auth::user()->isSurveyor())
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('surveyor.assignments') }}" class="btn btn-primary w-100">
                                <i class="fas fa-clipboard-list"></i> My Assignments
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('surveyor.routes') }}" class="btn btn-success w-100">
                                <i class="fas fa-route"></i> Survey Routes
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('surveyor.reports.create') }}" class="btn btn-info w-100">
                                <i class="fas fa-file-alt"></i> Create Report
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('surveyor.availability') }}" class="btn btn-warning w-100">
                                <i class="fas fa-calendar-alt"></i> Availability
                            </a>
                        </div>

                        <!-- Technician Actions -->
                        @elseif(Auth::user()->isTechnician())
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('technician.work-orders') }}" class="btn btn-primary w-100">
                                <i class="fas fa-tools"></i> Work Orders
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('technician.equipment') }}" class="btn btn-success w-100">
                                <i class="fas fa-server"></i> Equipment
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('technician.maintenance-requests') }}" class="btn btn-info w-100">
                                <i class="fas fa-wrench"></i> Maintenance
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('technician.profile') }}" class="btn btn-warning w-100">
                                <i class="fas fa-user-cog"></i> My Profile
                            </a>
                        </div>

                        <!-- Account Manager Actions -->
                        @elseif(Auth::user()->isAccountManager())
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('account-manager.customers.index') }}" class="btn btn-primary w-100">
                                <i class="fas fa-users"></i> My Customers
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('account-manager.tickets.create') }}" class="btn btn-success w-100">
                                <i class="fas fa-ticket-alt"></i> Create Ticket
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('account-manager.leases.create') }}" class="btn btn-info w-100">
                                <i class="fas fa-plus-circle"></i> New Lease
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('account-manager.payments.create') }}" class="btn btn-warning w-100">
                                <i class="fas fa-credit-card"></i> Payment Followup
                            </a>
                        </div>

                        <!-- Customer Actions -->
                        @else
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('customer.design-requests.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-plus-circle"></i> New Design Request
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('customer.leases') }}" class="btn btn-success w-100">
                                <i class="fas fa-network-wired"></i> My Leases
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('customer.tickets') }}" class="btn btn-info w-100">
                                <i class="fas fa-ticket-alt"></i> Support Tickets
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('customer.profile') }}" class="btn btn-warning w-100">
                                <i class="fas fa-user-cog"></i> My Profile
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="row">
        <!-- Left Column: Recent Activity -->
        <div class="col-lg-6">
            @if(isset($recentActivities) && count($recentActivities) > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock me-1"></i> Recent Activity
                    </h6>
                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="activity-feed">
                        @foreach($recentActivities as $activity)
                        <div class="activity-item d-flex align-items-start mb-3">
                            <div class="activity-icon me-3">
                                <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                            </div>
                            <div class="activity-content flex-grow-1">
                                <div class="activity-text">{!! $activity['text'] !!}</div>
                                <div class="activity-time text-muted small">
                                    {{ $activity['time'] }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Role-Specific Recent Items -->
            @if(isset($recentItems) && $recentItems->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-{{ Auth::user()->getRoleBadgeColor() }}">
                        {{ $recentItemsTitle }}
                    </h6>
                    <a href="{{ $recentItemsLink }}" class="btn btn-sm btn-outline-{{ Auth::user()->getRoleBadgeColor() }}">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="thead-light">
                                <tr>
                                    @foreach($recentItemsColumns as $column)
                                    <th>{{ $column }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentItems as $item)
                                <tr>
                                    @foreach($recentItemsColumns as $columnKey => $columnName)
                                    <td>
                                        @if($columnKey === 'status')
                                            <span class="badge bg-{{ $item->getStatusColor() }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        @elseif($columnKey === 'amount')
                                            ${{ number_format($item->amount, 2) }}
                                        @elseif($columnKey === 'created_at')
                                            {{ $item->created_at->format('M d, Y') }}
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

        <!-- Right Column: Role-Specific Panels -->
        <div class="col-lg-6">
            <!-- System Health / Alerts Panel -->
            @if(isset($systemHealth) && count($systemHealth) > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-{{ Auth::user()->getRoleBadgeColor() }} text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-heartbeat me-1"></i> System Status
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($systemHealth as $healthItem)
                    <div class="system-health-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold">{{ $healthItem['label'] }}</span>
                            <span class="badge bg-{{ $healthItem['status_color'] }}">
                                {{ $healthItem['status'] }}
                            </span>
                        </div>
                        @if(isset($healthItem['value']))
                        <div class="progress mt-1" style="height: 6px;">
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
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-bell me-1"></i> Notifications & Alerts
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($notifications) && count($notifications) > 0)
                        @foreach($notifications as $notification)
                        <div class="alert alert-{{ $notification['type'] }} alert-dismissible fade show">
                            <i class="fas fa-{{ $notification['icon'] }} me-2"></i>
                            {!! $notification['message'] !!}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>System Status:</strong> All systems are operating normally.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Performance Metrics -->
            @if(isset($performanceMetrics) && count($performanceMetrics) > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Performance Metrics</h6>
                </div>
                <div class="card-body">
                    @foreach($performanceMetrics as $metric)
                    <div class="metric-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-sm">{{ $metric['label'] }}</span>
                            <span class="font-weight-bold">{{ $metric['value'] }}{{ $metric['unit'] ?? '' }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-{{ $metric['color'] }}"
                                 role="progressbar"
                                 style="width: {{ $metric['percentage'] }}%"
                                 aria-valuenow="{{ $metric['percentage'] }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
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

    <!-- Bottom Section: Additional Role-Specific Content -->
    @if(isset($additionalSections) && count($additionalSections) > 0)
    <div class="row mt-4">
        @foreach($additionalSections as $section)
        <div class="col-lg-{{ $section['size'] ?? 6 }} mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-{{ $section['color'] ?? 'primary' }} text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-{{ $section['icon'] }} me-1"></i> {{ $section['title'] }}
                    </h6>
                    @if(isset($section['action_link']))
                    <a href="{{ $section['action_link'] }}" class="btn btn-sm btn-outline-light">View All</a>
                    @endif
                </div>
                <div class="card-body">
                    {!! $section['content'] !!}
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if(isset($charts))
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($charts as $chart)
        var ctx = document.getElementById('{{ $chart['id'] }}').getContext('2d');
        new Chart(ctx, {
            type: '{{ $chart['type'] }}',
            data: {
                labels: {!! json_encode($chart['labels']) !!},
                datasets: [{
                    label: '{{ $chart['dataset']['label'] }}',
                    data: {!! json_encode($chart['dataset']['data']) !!},
                    backgroundColor: '{{ $chart['dataset']['backgroundColor'] }}',
                    borderColor: '{{ $chart['dataset']['borderColor'] }}',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: '{{ $chart['title'] }}'
                    }
                }
            }
        });
        @endforeach
    });
@endif
</script>

<style>
.card {
    transition: transform 0.2s ease-in-out;
    border: none;
    border-radius: 10px;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.card-title {
    font-weight: 600;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
}

.alert {
    border-radius: 8px;
    border: none;
    margin-bottom: 1rem;
}

.badge {
    font-size: 0.75em;
    font-weight: 500;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.activity-feed .activity-item {
    border-left: 3px solid #e3e6f0;
    padding-left: 15px;
}

.activity-feed .activity-item:last-child {
    margin-bottom: 0;
}

.activity-icon {
    width: 30px;
    text-align: center;
}

.system-health-item {
    padding: 10px 0;
    border-bottom: 1px solid #e3e6f0;
}

.system-health-item:last-child {
    border-bottom: none;
}

.metric-item {
    padding: 10px 0;
}

.metric-item:last-child {
    padding-bottom: 0;
}

.chart-container {
    position: relative;
    height: 200px;
    width: 100%;
}
</style>
@endsection
