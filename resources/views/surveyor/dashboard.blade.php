@extends('layouts.app')

@section('title', 'Surveyor Dashboard - Dark Fibre CRM')

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
                            <i class="fas fa-map-marked-alt fa-3x fa-fw"></i>
                        </div>
                        <div>
                            <h1 class="display-5 fw-bold mb-2">Surveyor Dashboard</h1>
                            <p class="lead mb-0 opacity-90">
                                Manage field surveys and site inspections
                            </p>
                        </div>
                    </div>

                    {{-- Meta Information --}}
                    <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
                        <span class="badge bg-white text-kp-blue px-3 py-2 rounded-pill">
                            <i class="fas fa-user me-1"></i>
                            {{ Str::limit(Auth::user()->name, 20) }}
                        </span>
                        <span class="badge bg-white text-kp-blue px-3 py-2 rounded-pill">
                            <i class="fas fa-calendar-alt me-1"></i>
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

                        <a href="{{ route('surveyor.assignments.index') }}" class="btn btn-light btn-dashboard-action">
                            <i class="fas fa-tasks me-2"></i>My Assignments
                        </a>

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

        {{-- Welcome Card --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="welcome-card rounded-4 p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h3 class="fw-bold text-white mb-2">
                                Welcome back, {{ Auth::user()->name }}!
                            </h3>
                            <p class="text-white-70 mb-0">
                                Here's your work overview for today
                            </p>
                        </div>
                        <div class="col-lg-4 text-center d-none d-lg-block">
                            <i class="fas fa-clipboard-list fa-3x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row g-4 mb-5">

            {{-- Pending Assignments --}}
            <div class="col-xl-3 col-md-6">
                <div class="metric-card metric-card-warning rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-white-70 mb-2">PENDING ASSIGNMENTS</h6>
                            <div class="metric-value-large fw-bold text-white mb-1">
                                {{ $pendingAssignments }}
                            </div>
                            <small class="text-white-50">Awaiting your action</small>
                        </div>
                        <div class="metric-icon-large bg-white-20 rounded-3">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- In Progress --}}
            <div class="col-xl-3 col-md-6">
                <div class="metric-card metric-card-info rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-white-70 mb-2">IN PROGRESS</h6>
                            <div class="metric-value-large fw-bold text-white mb-1">
                                {{ $inProgressAssignments }}
                            </div>
                            <small class="text-white-50">Currently working on</small>
                        </div>
                        <div class="metric-icon-large bg-white-20 rounded-3">
                            <i class="fas fa-spinner fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Completed This Week --}}
            <div class="col-xl-3 col-md-6">
                <div class="metric-card metric-card-success rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-white-70 mb-2">COMPLETED (THIS WEEK)</h6>
                            <div class="metric-value-large fw-bold text-white mb-1">
                                {{ $completedThisWeek }}
                            </div>
                            <small class="text-white-50">Successfully delivered</small>
                        </div>
                        <div class="metric-icon-large bg-white-20 rounded-3">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Assignments --}}
            <div class="col-xl-3 col-md-6">
                <div class="metric-card metric-card-primary rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-white-70 mb-2">TOTAL ASSIGNMENTS</h6>
                            <div class="metric-value-large fw-bold text-white mb-1">
                                {{ $assignedDesignRequests->count() }}
                            </div>
                            <small class="text-white-50">All-time assignments</small>
                        </div>
                        <div class="metric-icon-large bg-white-20 rounded-3">
                            <i class="fas fa-list-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Recent Assignments & Quick Actions Row --}}
        <div class="row g-4 mb-5">

            {{-- Recent Design Requests Table --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-history text-info me-2"></i>Recent Design Requests
                        </h5>
                        <a href="{{ route('surveyor.assignments.index') }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @if($recentAssignments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="px-4 py-3">Request #</th>
                                            <th class="py-3">Customer</th>
                                            <th class="py-3">Title</th>
                                            <th class="py-3">Priority</th>
                                            <th class="py-3 d-none d-lg-table-cell">Scheduled</th>
                                            <th class="py-3">Status</th>
                                            <th class="px-4 py-3 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentAssignments as $designRequest)
                                            <tr>
                                                <td class="px-4 py-3 fw-bold text-kp-blue">
                                                    #{{ $designRequest->request_number }}
                                                </td>
                                                <td class="py-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="customer-avatar bg-kp-blue-light rounded-circle">
                                                            <i class="fas fa-user fa-sm text-kp-blue"></i>
                                                        </div>
                                                        <span>{{ Str::limit($designRequest->customer->name ?? 'N/A', 25) }}</span>
                                                    </div>
                                                 </td>
                                                <td class="py-3">{{ Str::limit($designRequest->title, 30) }}</td>
                                                <td class="py-3">
                                                    @php
                                                        $priorityColor = match($designRequest->priority) {
                                                            'high' => 'danger',
                                                            'medium' => 'warning',
                                                            default => 'secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $priorityColor }} rounded-pill px-3 py-1">
                                                        {{ ucfirst($designRequest->priority) }}
                                                    </span>
                                                </td>
                                                <td class="py-3 d-none d-lg-table-cell">
                                                    @if($designRequest->survey_scheduled_at)
                                                        <div class="small">
                                                            <div>{{ $designRequest->survey_scheduled_at->format('M d, Y') }}</div>
                                                            <div class="text-muted">{{ $designRequest->survey_scheduled_at->format('H:i') }}</div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Not scheduled</span>
                                                    @endif
                                                </td>
                                                <td class="py-3">
                                                    @php
                                                        $statusColor = match($designRequest->survey_status) {
                                                            'completed' => 'success',
                                                            'in_progress' => 'info',
                                                            default => 'warning'
                                                        };
                                                        $statusText = ucfirst(str_replace('_', ' ', $designRequest->survey_status));
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColor }} rounded-pill px-3 py-1">
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <a href="{{ route('surveyor.assignments.show', $designRequest->id) }}"
                                                       class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                                       data-bs-toggle="tooltip" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-clipboard-list fa-4x text-muted opacity-25 mb-3"></i>
                                <h6 class="text-muted">No Design Requests Assigned</h6>
                                <p class="small text-muted">You don't have any design requests assigned to you yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick Actions Sidebar --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="d-grid gap-3">
                            <a href="{{ route('surveyor.assignments.index') }}" class="btn btn-info rounded-pill py-2">
                                <i class="fas fa-tasks me-2"></i>My Assignments
                            </a>

                            @if($recentAssignments->count() > 0)
                                <a href="{{ route('surveyor.assignments.show', $recentAssignments->first()->id) }}"
                                   class="btn btn-kp-success rounded-pill py-2">
                                    <i class="fas fa-file-alt me-2"></i>Work on Latest
                                </a>
                            @else
                                <button class="btn btn-secondary rounded-pill py-2" disabled>
                                    <i class="fas fa-file-alt me-2"></i>Work on Latest
                                </button>
                            @endif

                            <a href="{{ route('surveyor.profile') }}" class="btn btn-secondary rounded-pill py-2">
                                <i class="fas fa-user-cog me-2"></i>Profile Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Upcoming Deadlines Section --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-calendar-alt text-warning me-2"></i>Upcoming Survey Deadlines
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if($upcomingDeadlines->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($upcomingDeadlines as $designRequest)
                                    <a href="{{ route('surveyor.assignments.show', $designRequest->id) }}"
                                       class="list-group-item list-group-item-action p-4">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                            <div>
                                                <h6 class="fw-bold mb-1 text-kp-blue">
                                                    #{{ $designRequest->request_number }} - {{ Str::limit($designRequest->title, 40) }}
                                                </h6>
                                                <p class="mb-1 text-muted small">
                                                    <i class="fas fa-user me-1"></i>
                                                    Customer: {{ $designRequest->customer->name ?? 'N/A' }}
                                                </p>
                                                <div class="mt-2">
                                                    <span class="badge bg-{{ $designRequest->survey_status == 'in_progress' ? 'info' : 'warning' }} rounded-pill px-3 py-1">
                                                        {{ ucfirst(str_replace('_', ' ', $designRequest->survey_status)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge {{ $designRequest->survey_scheduled_at && $designRequest->survey_scheduled_at->isToday() ? 'bg-danger' : 'bg-warning text-dark' }} rounded-pill px-3 py-2">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Scheduled: {{ $designRequest->survey_scheduled_at ? $designRequest->survey_scheduled_at->diffForHumans() : 'Not scheduled' }}
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-check fa-4x text-muted opacity-25 mb-3"></i>
                                <h6 class="text-muted">No Upcoming Deadlines</h6>
                                <p class="small text-muted">All surveys are on track</p>
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

/* Welcome Card */
.welcome-card {
    background: linear-gradient(135deg, var(--kp-blue) 0%, var(--kp-green) 100%);
}

/* Metric Cards */
.metric-card-warning {
    background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
}

.metric-card-info {
    background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
}

.metric-card-success {
    background: linear-gradient(135deg, var(--kp-green) 0%, #00802c 100%);
}

.metric-card-primary {
    background: linear-gradient(135deg, var(--kp-blue) 0%, #005499 100%);
}

.metric-value-large {
    font-size: 2rem;
    line-height: 1.2;
}

.metric-icon-large {
    width: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Customer Avatar */
.customer-avatar {
    width: 32px;
    height: 32px;
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

.btn-kp-success {
    background: var(--kp-green);
    border-color: var(--kp-green);
    color: white;
}

.btn-kp-success:hover {
    background: #00802c;
    border-color: #00802c;
    color: white;
}

/* Color Classes */
.bg-kp-blue-light { background: rgba(0, 102, 179, 0.1); }
.bg-white-20 { background: rgba(255, 255, 255, 0.2); }
.bg-white-50 { background: rgba(255, 255, 255, 0.5); }

.text-white-70 { color: rgba(255, 255, 255, 0.7); }
.text-white-50 { color: rgba(255, 255, 255, 0.5); }

.text-kp-blue { color: var(--kp-blue) !important; }

/* Table Styles */
.table th {
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #5a5c69;
}

.table td {
    vertical-align: middle;
}

/* List Group */
.list-group-item {
    transition: all 0.2s ease;
}

.list-group-item:hover {
    background: #f8f9fa;
    transform: translateX(5px);
}

/* Rounded Utilities */
.rounded-4 { border-radius: 1rem !important; }
.rounded-pill { border-radius: 9999px !important; }

/* Responsive Adjustments */
@media (max-width: 768px) {
    .metric-value-large { font-size: 1.5rem; }
    .btn-dashboard-action { padding: 6px 16px; font-size: 0.875rem; }
}

@media (max-width: 576px) {
    .dashboard-hero { text-align: center; }
    .hero-icon { display: none; }
    .table-responsive { font-size: 0.875rem; }
}

@media print {
    .dashboard-hero, .btn, .badge { display: none !important; }
    .card { border: 1px solid #ddd !important; box-shadow: none !important; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(el => new bootstrap.Tooltip(el));

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

    document.querySelectorAll('.metric-card').forEach(card => {
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
