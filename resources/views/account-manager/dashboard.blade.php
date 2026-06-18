@extends('layouts.app')

@section('title', 'Account Manager Dashboard - Dark Fibre CRM')

@section('content')
<div class="container-fluid px-0">

    {{-- Hero Section with Welcome Message --}}
    <div class="dashboard-hero text-white py-4 py-md-5">
        <div class="container-fluid px-3 px-sm-4 px-md-5">
            <div class="row align-items-center g-4">

                {{-- Left Column - Greeting & User Info --}}
                <div class="col-12 col-lg-7">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="hero-icon">
                            <i class="fas fa-user-tie fa-3x fa-fw"></i>
                        </div>
                        <div>
                            @php
                                $hour = now()->hour;
                                $greeting = match(true) {
                                    $hour < 12 => 'Good morning',
                                    $hour < 17 => 'Good afternoon',
                                    default => 'Good evening'
                                };
                            @endphp
                            <h1 class="display-5 fw-bold mb-2">Account Manager Dashboard</h1>
                            <p class="lead mb-0 opacity-90">{{ $greeting }}, <strong>{{ Auth::user()->name }}</strong>!</p>
                        </div>
                    </div>

                    {{-- Meta Information --}}
                    <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
                        <span class="badge bg-white text-kp-blue px-3 py-2 rounded-pill">
                            <i class="far fa-calendar-alt me-1"></i>
                            {{ now()->format('l, F j, Y') }}
                        </span>
                        <span class="badge bg-white text-kp-blue px-3 py-2 rounded-pill">
                            <i class="far fa-clock me-1"></i>
                            {{ now()->format('g:i A') }}
                        </span>
                        <span class="badge bg-success px-3 py-2 rounded-pill">
                            <i class="fas fa-circle me-1 small"></i> Online
                        </span>
                    </div>
                </div>

                {{-- Right Column - Action Buttons --}}
                <div class="col-12 col-lg-5">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        @include('partials.role-help-widget')

                        <a href="{{ route('kpi.dashboard', ['account_manager_id' => auth()->user()->id]) }}"
                           class="btn btn-light btn-dashboard-action">
                            <i class="fas fa-chart-line me-2"></i>My KPIs
                        </a>

                        <a href="{{ route('account-manager.tickets.create') }}"
                           class="btn btn-light btn-dashboard-action">
                            <i class="fas fa-plus-circle me-2"></i>New Ticket
                        </a>

                        <a href="{{ route('account-manager.payments.create') }}"
                           class="btn btn-light btn-dashboard-action">
                            <i class="fas fa-money-bill-wave me-2"></i>Track Payment
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Alert Banner for Urgent Issues --}}
    @if(($stats['high_priority_tickets'] ?? 0) > 0 || ($stats['overdue_payments'] ?? 0) > 0)
    <div class="container-fluid px-3 px-sm-4 px-md-5 py-3">
        <div class="alert alert-warning alert-dismissible fade show border-0 rounded-4 shadow-sm" role="alert">
            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
                <div class="alert-icon me-0 me-sm-3 mb-2 mb-sm-0">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="alert-heading mb-2 fw-bold">⚠️ Attention Required</h6>
                    <div class="d-flex flex-wrap gap-3">
                        @if(($stats['high_priority_tickets'] ?? 0) > 0)
                            <span class="badge bg-danger rounded-pill px-3 py-2">
                                <i class="fas fa-headset me-1"></i>
                                {{ $stats['high_priority_tickets'] }} Urgent Ticket(s)
                            </span>
                        @endif
                        @if(($stats['overdue_payments'] ?? 0) > 0)
                            <span class="badge bg-danger rounded-pill px-3 py-2">
                                <i class="fas fa-money-bill-wave me-1"></i>
                                {{ $stats['overdue_payments'] }} Overdue Payment(s)
                            </span>
                        @endif
                    </div>
                </div>
                <button type="button" class="btn-close mt-2 mt-sm-0" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
    @endif

    {{-- Main Content --}}
    <div class="container-fluid px-3 px-sm-4 px-md-5 py-4">

        {{-- Key Performance Metrics Grid --}}
        <div class="row g-4 mb-5">
            @php
                $metrics = [
                    [
                        'title' => 'Customers',
                        'value' => $stats['total_customers'] ?? 0,
                        'icon' => 'users',
                        'color' => 'primary',
                        'trend' => '+12%',
                        'subtitle' => 'active customers',
                        'link' => route('account-manager.customers.index'),
                        'link_text' => 'View Portfolio'
                    ],
                    [
                        'title' => 'Active Support',
                        'value' => $stats['open_tickets'] ?? 0,
                        'icon' => 'headset',
                        'color' => 'warning',
                        'alert' => ($stats['high_priority_tickets'] ?? 0) > 0 ? $stats['high_priority_tickets'] . ' urgent' : null,
                        'subtitle' => 'open tickets',
                        'link' => route('account-manager.tickets.index'),
                        'link_text' => 'Manage Tickets'
                    ],
                    [
                        'title' => 'Payment Health',
                        'value' => $stats['pending_payments'] ?? 0,
                        'icon' => 'credit-card',
                        'color' => 'info',
                        'alert' => ($stats['overdue_payments'] ?? 0) > 0 ? $stats['overdue_payments'] . ' overdue' : null,
                        'subtitle' => 'pending collection',
                        'link' => route('account-manager.payments.index'),
                        'link_text' => 'Review Payments'
                    ],
                    [
                        'title' => 'Satisfaction Score',
                        'value' => ($stats['satisfaction_score'] ?? 'N/A') . '%',
                        'icon' => 'star',
                        'color' => 'success',
                        'subtitle' => 'average rating',
                        'link' => '#',
                        'link_text' => 'View Feedback'
                    ]
                ];
            @endphp

            @foreach($metrics as $metric)
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="metric-card bg-white rounded-4 shadow-sm h-100 p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="metric-icon rounded-circle bg-{{ $metric['color'] }}-light">
                                <i class="fas fa-{{ $metric['icon'] }} fa-fw text-{{ $metric['color'] }}"></i>
                            </div>
                            @if(isset($metric['alert']))
                                <span class="badge bg-danger rounded-pill">{{ $metric['alert'] }}</span>
                            @elseif(isset($metric['trend']))
                                <span class="badge bg-success rounded-pill">
                                    <i class="fas fa-arrow-up me-1"></i>{{ $metric['trend'] }}
                                </span>
                            @endif
                        </div>

                        <h6 class="text-muted text-uppercase small fw-semibold mb-2">{{ $metric['title'] }}</h6>
                        <div class="metric-value fw-bold text-kp-blue mb-2">{{ $metric['value'] }}</div>
                        <p class="small text-muted mb-3">{{ $metric['subtitle'] }}</p>

                        <a href="{{ $metric['link'] }}" class="btn btn-sm btn-outline-{{ $metric['color'] }} rounded-pill w-100">
                            {{ $metric['link_text'] }} <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Quick Actions Section --}}
        @php
            $quickActions = [
                ['title' => 'New Ticket', 'icon' => 'headset', 'color' => 'info', 'link' => route('account-manager.tickets.create'), 'desc' => 'Register support issues'],
                ['title' => 'Track Payment', 'icon' => 'money-bill-wave', 'color' => 'success', 'link' => route('account-manager.payments.create'), 'desc' => 'Track payments & debts'],
                ['title' => 'Customers', 'icon' => 'users', 'color' => 'primary', 'link' => route('account-manager.customers.index'), 'desc' => 'Review customer info'],
                ['title' => 'Design Requests', 'icon' => 'drafting-compass', 'color' => 'warning', 'link' => route('admin.design-requests.index'), 'desc' => 'Allocate requests'],
                ['title' => 'Contracts', 'icon' => 'file-contract', 'color' => 'purple', 'link' => route('contracts.index'), 'desc' => 'Manage agreements'],
                ['title' => 'Quotations', 'icon' => 'file-invoice-dollar', 'color' => 'danger', 'link' => route('admin.quotations.index'), 'desc' => 'Create quotations'],
                ['title' => 'Leases', 'icon' => 'network-wired', 'color' => 'dark', 'link' => route('account-manager.leases.index'), 'desc' => 'Manage leases'],
                ['title' => 'Reports', 'icon' => 'chart-bar', 'color' => 'secondary', 'link' => route('account-manager.reports.performance'), 'desc' => 'Analytics & performance'],
['title' => 'Maintainance requests', 'icon' => 'toolbox', 'color' => 'secondary', 'link' => route('maintenance.admin-dashboard'), 'desc' => 'Maintenance requests'],
            ];
        @endphp

        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 fw-bold">
                                <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                            </h4>
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                {{ count($quickActions) }} Available
                            </span>
                        </div>
                        <p class="text-muted mb-0 mt-2">Frequently used tools and shortcuts</p>
                    </div>

                    <div class="card-body p-4 pt-2">
                        <div class="row g-3">
                            @foreach($quickActions as $action)
                                <div class="col-6 col-md-4 col-lg-3">
                                    <a href="{{ $action['link'] }}" class="action-card text-center p-3 rounded-3 border h-100 text-decoration-none d-block">
                                        <div class="action-icon bg-{{ $action['color'] }} rounded-3 mx-auto mb-3">
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

        {{-- Recent Activity Section --}}
        <div class="row g-4">

            {{-- Recent Support Tickets --}}
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-headset text-kp-blue me-2"></i>Recent Support Tickets
                        </h5>
                        <a href="{{ route('account-manager.tickets.index') }}" class="btn btn-sm btn-outline-kp-blue rounded-pill px-3">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @forelse($recentTickets ?? [] as $ticket)
                            <div class="ticket-item p-4 border-bottom">
                                <div class="d-flex gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-circle bg-{{ $ticket->priority === 'high' ? 'danger' : 'warning' }}-light">
                                            <i class="fas fa-{{ $ticket->priority === 'high' ? 'exclamation-triangle' : 'ticket-alt' }} text-{{ $ticket->priority === 'high' ? 'danger' : 'warning' }}"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                            <h6 class="fw-bold mb-0">{{ Str::limit($ticket->title, 50) }}</h6>
                                            <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : 'warning' }} rounded-pill">
                                                {{ ucfirst($ticket->priority) }} Priority
                                            </span>
                                        </div>
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-user me-1"></i>{{ Str::limit($ticket->customer->name ?? 'Unknown', 25) }}
                                            • <i class="far fa-clock me-1"></i>{{ $ticket->created_at->diffForHumans() }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <span class="badge bg-{{ $ticket->status === 'open' ? 'primary' : ($ticket->status === 'in_progress' ? 'info' : 'success') }} rounded-pill">
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                            <a href="{{ route('account-manager.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                                View Details <i class="fas fa-chevron-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-ticket-alt fa-4x text-muted opacity-25 mb-3"></i>
                                <h6 class="text-muted">No active support tickets</h6>
                                <p class="small text-muted">All customer issues are resolved</p>
                                <a href="{{ route('account-manager.tickets.create') }}" class="btn btn-kp-primary rounded-pill px-4">
                                    <i class="fas fa-plus-circle me-2"></i>Create Ticket
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Payment Follow-ups --}}
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-money-bill-wave text-kp-green me-2"></i>Payment Follow-ups
                        </h5>
                        <a href="{{ route('account-manager.payments.index') }}" class="btn btn-sm btn-outline-kp-green rounded-pill px-3">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @forelse($upcomingPayments ?? [] as $payment)
                            <div class="payment-item p-4 border-bottom">
                                <div class="d-flex gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-circle bg-{{ $payment->due_date->isPast() ? 'danger' : 'info' }}-light">
                                            <i class="fas fa-{{ $payment->due_date->isPast() ? 'exclamation-triangle' : 'calendar' }} text-{{ $payment->due_date->isPast() ? 'danger' : 'info' }}"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                            <h6 class="fw-bold mb-0">{{ Str::limit($payment->customer->name ?? 'Unknown', 30) }}</h6>
                                            <span class="fw-bold text-kp-blue fs-5">${{ number_format($payment->amount, 2) }}</span>
                                        </div>
                                        <p class="text-muted small mb-2">
                                            <i class="far fa-calendar me-1"></i>Due {{ $payment->due_date->format('M d, Y') }}
                                            • <i class="far fa-clock me-1"></i>{{ $payment->due_date->diffForHumans() }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <span class="badge bg-{{ $payment->status === 'pending' ? 'warning' : ($payment->status === 'reminded' ? 'info' : 'success') }} rounded-pill">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                            <div class="btn-group">
                                                @if($payment->status === 'pending')
                                                    <form action="{{ route('account-manager.payments.remind', $payment) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill">
                                                            <i class="fas fa-bell me-1"></i>Remind
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('account-manager.payments.paid', $payment) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill">
                                                        <i class="fas fa-check me-1"></i>Mark Paid
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-4x text-success opacity-25 mb-3"></i>
                                <h6 class="text-muted">All payments up to date!</h6>
                                <p class="small text-muted">No pending follow-ups required</p>
                                <a href="{{ route('account-manager.payments.create') }}" class="btn btn-kp-primary rounded-pill px-4">
                                    <i class="fas fa-plus-circle me-2"></i>Record Payment
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- Additional Insights Section --}}
        <div class="row g-4 mt-3">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                    <div class="insight-icon bg-info-light rounded-circle mx-auto mb-3">
                        <i class="fas fa-chart-pie fa-2x text-info"></i>
                    </div>
                    <h5 class="fw-bold">Ticket Distribution</h5>
                    <p class="small text-muted">View analytics and statistics</p>
                    <button class="btn btn-outline-info rounded-pill px-4" disabled>Coming Soon</button>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                    <div class="insight-icon bg-success-light rounded-circle mx-auto mb-3">
                        <i class="fas fa-calendar-check fa-2x text-success"></i>
                    </div>
                    <h5 class="fw-bold">Upcoming Activities</h5>
                    <p class="small text-muted">Meetings & follow-ups</p>
                    <button class="btn btn-outline-success rounded-pill px-4" disabled>Coming Soon</button>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                    <div class="insight-icon bg-warning-light rounded-circle mx-auto mb-3">
                        <i class="fas fa-trophy fa-2x text-warning"></i>
                    </div>
                    <h5 class="fw-bold">Top Customers</h5>
                    <p class="small text-muted">Highest revenue clients</p>
                    <button class="btn btn-outline-warning rounded-pill px-4" disabled>Coming Soon</button>
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

/* Action Cards */
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
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

/* Avatar Circle */
.avatar-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

/* Insight Icons */
.insight-icon {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
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

.btn-outline-kp-blue {
    border: 1px solid var(--kp-blue);
    color: var(--kp-blue);
}
.btn-outline-kp-blue:hover {
    background: var(--kp-blue);
    color: white;
}

.btn-outline-kp-green {
    border: 1px solid var(--kp-green);
    color: var(--kp-green);
}
.btn-outline-kp-green:hover {
    background: var(--kp-green);
    color: white;
}

/* Color Classes */
.bg-primary-light { background: rgba(0, 102, 179, 0.1); }
.bg-success-light { background: rgba(0, 150, 57, 0.1); }
.bg-warning-light { background: rgba(255, 215, 0, 0.15); }
.bg-info-light { background: rgba(23, 162, 184, 0.1); }
.bg-danger-light { background: rgba(220, 53, 69, 0.1); }
.bg-purple-light { background: rgba(111, 66, 193, 0.1); }

.bg-purple { background-color: #6f42c1 !important; }
.text-purple { color: #6f42c1 !important; }

.btn-outline-purple {
    border: 1px solid #6f42c1;
    color: #6f42c1;
}
.btn-outline-purple:hover {
    background: #6f42c1;
    color: white;
}

.text-kp-blue { color: var(--kp-blue) !important; }
.text-kp-green { color: var(--kp-green) !important; }
.bg-kp-blue { background-color: var(--kp-blue) !important; }
.bg-kp-green { background-color: var(--kp-green) !important; }
.btn-kp-primary { background: var(--kp-blue); border-color: var(--kp-blue); color: white; }
.btn-kp-primary:hover { background: #005499; border-color: #005499; }

/* Rounded utilities */
.rounded-4 { border-radius: 1rem !important; }
.rounded-3 { border-radius: 0.75rem !important; }

/* Responsive Adjustments */
@media (max-width: 768px) {
    .metric-value { font-size: 1.5rem; }
    .action-card { text-align: left; }
    .action-icon { margin: 0 0 0.75rem 0; }
    .btn-dashboard-action { padding: 6px 16px; font-size: 0.875rem; }
}

@media (max-width: 576px) {
    .col-6 { width: 100%; }
    .dashboard-hero { text-align: center; }
    .hero-icon { display: none; }
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

    // Auto-dismiss alerts after 8 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 8000);
    });

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

    document.querySelectorAll('.metric-card, .action-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
});
</script>

@endsection
