@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 col-lg-2">
            <!-- Role Banner -->
            <div class="card shadow-sm mb-3
                @if($role == 'admin' || $role == 'system_admin') border-danger
                @elseif($role == 'finance') border-info
                @elseif($role == 'designer') border-kp-blue
                @elseif($role == 'surveyor') border-secondary
                @elseif($role == 'technician') border-dark
                @elseif($role == 'account_manager') border-kp-green
                @elseif($role == 'technical_admin') border-kp-blue
                @elseif($role == 'customer') border-kp-yellow
                @elseif($role == 'ict_engineer') border-kp-blue
                @elseif($role == 'debt_manager') border-danger
                @elseif($role == 'compliance_officer') border-info
                @elseif($role == 'accountmanager_admin') border-kp-green
                @else border-secondary
                @endif">

                <div class="card-header
                    @if($role == 'admin' || $role == 'system_admin') bg-danger text-white
                    @elseif($role == 'finance') bg-info text-white
                    @elseif($role == 'designer') bg-kp-blue text-white
                    @elseif($role == 'surveyor') bg-secondary text-white
                    @elseif($role == 'technician') bg-dark text-white
                    @elseif($role == 'account_manager') bg-kp-green text-white
                    @elseif($role == 'technical_admin') bg-kp-blue text-white
                    @elseif($role == 'customer') bg-kp-yellow text-dark
                    @elseif($role == 'ict_engineer') bg-kp-blue text-white
                    @elseif($role == 'debt_manager') bg-danger text-white
                    @elseif($role == 'compliance_officer') bg-info text-white
                    @elseif($role == 'accountmanager_admin') bg-kp-green text-white
                    @else bg-secondary text-white
                    @endif">
                    <h5 class="mb-0">
                        <i class="fas
                            @if($role == 'admin' || $role == 'system_admin') fa-shield-alt
                            @elseif($role == 'finance') fa-chart-line
                            @elseif($role == 'designer') fa-pencil-ruler
                            @elseif($role == 'surveyor') fa-ruler-combined
                            @elseif($role == 'technician') fa-tools
                            @elseif($role == 'account_manager') fa-users
                            @elseif($role == 'technical_admin') fa-network-wired
                            @elseif($role == 'customer') fa-user-circle
                            @elseif($role == 'ict_engineer') fa-microchip
                            @elseif($role == 'debt_manager') fa-chart-simple
                            @elseif($role == 'compliance_officer') fa-file-alt
                            @elseif($role == 'accountmanager_admin') fa-chart-line
                            @else fa-question-circle
                            @endif me-2"></i>
                        {{ $roleDisplayName }} Help Center
                    </h5>
                </div>

                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-question-circle fa-3x text-muted"></i>
                        <p class="small mt-2">Personalized help for your role</p>
                    </div>

                    @if(!empty($quickTips) && count($quickTips) > 0)
                    <div class="alert alert-info small">
                        <i class="fas fa-lightbulb me-1"></i>
                        <strong>Quick Tip:</strong> {{ $quickTips[0] }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Navigation Menu -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <strong><i class="fas fa-compass me-1"></i> Help Topics</strong>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('help.role.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('help.role.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard Guide
                    </a>
                    <a href="{{ route('help.role.getting-started') }}" class="list-group-item list-group-item-action {{ request()->routeIs('help.role.getting-started') ? 'active' : '' }}">
                        <i class="fas fa-rocket me-2"></i> Getting Started
                    </a>

                    <div class="list-group-item list-group-item-light fw-bold">
                        <i class="fas fa-cog me-2"></i> Role-Specific Guides
                    </div>

                    <!-- Finance Guides -->
                    @if($role == 'finance')
                    <a href="{{ route('help.role.finance') }}" class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('help.role.finance') ? 'active' : '' }}">
                        <i class="fas fa-chart-line me-2"></i> Financial Management
                    </a>
                    <a href="{{ route('help.role.billing') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-file-invoice-dollar me-2"></i> Billing Guide
                    </a>
                    <a href="{{ route('help.role.payments') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-money-bill-wave me-2"></i> Payment Processing
                    </a>

                    <!-- Designer Guides -->
                    @elseif($role == 'designer')
                    <a href="{{ route('help.role.designer') }}" class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('help.role.designer') ? 'active' : '' }}">
                        <i class="fas fa-drafting-compass me-2"></i> Design Workflow
                    </a>
                    <a href="{{ route('help.role.quotations') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-file-signature me-2"></i> Quotations Guide
                    </a>
                    <a href="{{ route('help.role.fibre-dashboard') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-network-wired me-2"></i> Fibre Dashboard
                    </a>

                    <!-- Debt Manager Guides -->
                    @elseif($role == 'debt_manager')
                    <a href="{{ route('help.role.debt-manager') }}" class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('help.role.debt-manager') ? 'active' : '' }}">
                        <i class="fas fa-chart-simple me-2"></i> Debt Management
                    </a>
                    <a href="{{ route('help.role.aging-report') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-calendar-alt me-2"></i> Aging Reports
                    </a>
                    <a href="{{ route('help.role.collection') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-hand-holding-usd me-2"></i> Collection Strategies
                    </a>

                   <!-- Customer Guides -->
@elseif($role == 'customer')
<a href="{{ route('help.role.customer') }}" class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('help.role.customer') ? 'active' : '' }}">
    <i class="fas fa-user-circle me-2"></i> Customer Portal
</a>
<a href="{{ route('help.role.customer-profile') }}" class="list-group-item list-group-item-action ps-4">
    <i class="fas fa-id-card me-2"></i> Profile Setup
</a>
<a href="{{ route('help.role.customer-invoices') }}" class="list-group-item list-group-item-action ps-4">
    <i class="fas fa-file-invoice me-2"></i> Invoices & Payments
</a>
<a href="{{ route('help.role.customer-tickets') }}" class="list-group-item list-group-item-action ps-4">
    <i class="fas fa-ticket-alt me-2"></i> Support Tickets
</a>

                    <!-- ICT Engineer Guides -->
                    @elseif($role == 'ict_engineer')
                    <a href="{{ route('help.role.ict-engineer') }}" class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('help.role.ict-engineer') ? 'active' : '' }}">
                        <i class="fas fa-microchip me-2"></i> Network Operations
                    </a>
                    <a href="{{ route('help.role.tickets-ict') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-ticket-alt me-2"></i> Ticket Management
                    </a>
                    <a href="{{ route('help.role.monitoring') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-chart-line me-2"></i> Network Monitoring
                    </a>

                    <!-- Account Manager Guides -->
                    @elseif($role == 'account_manager')
                    <a href="{{ route('help.role.account-manager') }}" class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('help.role.account-manager') ? 'active' : '' }}">
                        <i class="fas fa-users me-2"></i> Customer Management
                    </a>

                    <a href="{{ route('help.account-manager-customers') }}" class="btn btn-sm btn-kp-success">
                <i class="fas fa-book-open me-1"></i> Customer Management Guide
            </a>
                    <a href="{{ route('help.role.customer-care') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-headset me-2"></i> Customer Care
                    </a>
                    <a href="{{ route('help.role.renewals') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-file-signature me-2"></i> Contract Renewals
                    </a>

                    <!-- Compliance Officer Guides -->
                    @elseif($role == 'compliance_officer')
                    <a href="{{ route('help.role.compliance-officer') }}" class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('help.role.compliance-officer') ? 'active' : '' }}">
                        <i class="fas fa-file-alt me-2"></i> CAK Compliance
                    </a>
                    <a href="{{ route('help.asp') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-server me-2"></i> ASP Guide
                    </a>
                    <a href="{{ route('help.csp') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-envelope me-2"></i> CSP Guide
                    </a>
                    <a href="{{ route('help.nfp') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-network-wired me-2"></i> NFP Guide
                    </a>

                    <!-- Surveyor Guides -->
                    @elseif($role == 'surveyor')
                    <a href="{{ route('help.role.surveyor') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-ruler-combined me-2"></i> Survey Guide
                    </a>
                    <a href="{{ route('help.role.field-data') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-database me-2"></i> Field Data Collection
                    </a>

                    <!-- Technician Guides -->
                    @elseif($role == 'technician')
                    <a href="{{ route('help.role.technician') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-tools me-2"></i> Work Orders
                    </a>
                    <a href="{{ route('help.role.equipment') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-microchip me-2"></i> Equipment Management
                    </a>

                    <!-- Technical Admin Guides -->
                    @elseif($role == 'technical_admin')
                    <a href="{{ route('help.role.technical-admin') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-network-wired me-2"></i> Network Operations
                    </a>
                    <a href="{{ route('help.role.leases') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-file-signature me-2"></i> Lease Management
                    </a>
                    <a href="{{ route('help.role.maintenance') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-wrench me-2"></i> Maintenance
                    </a>

                    <!-- Admin Guides -->
                    @else
                    <a href="{{ route('help.role.admin') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-shield-alt me-2"></i> System Administration
                    </a>
                    <a href="{{ route('help.role.user-management') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-users me-2"></i> User Management
                    </a>
                    <a href="{{ route('help.role.backup') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-database me-2"></i> Backup & Recovery
                    </a>
                    @endif

                    <!-- Common Sections -->
                    <div class="list-group-item list-group-item-light fw-bold">
                        <i class="fas fa-user-circle me-2"></i> Profile & Account
                    </div>
                    <a href="{{ route('help.profile.index') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-user-edit me-2"></i> Profile Management
                    </a>
                    <a href="{{ route('help.profile.security') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-lock me-2"></i> Password & Security
                    </a>
                    <a href="{{ route('help.profile.notifications') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-bell me-2"></i> Notifications
                    </a>

                    <!-- Support Section -->
                    <div class="list-group-item list-group-item-light fw-bold">
                        <i class="fas fa-question-circle me-2"></i> Support
                    </div>
                    <a href="{{ route('help.faq') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-question me-2"></i> FAQ
                    </a>
                    <a href="{{ route('help.contact') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-headset me-2"></i> Contact Support
                    </a>
                    <a href="{{ route('help.video-tutorials') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-video me-2"></i> Video Tutorials
                    </a>
                </div>
            </div>

            <!-- Support Contact Card -->
            <div class="card shadow-sm mt-3">
                <div class="card-body text-center">
                    <i class="fas fa-headset fa-2x text-kp-blue mb-2"></i>
                    <h6>Need More Help?</h6>
                    <p class="small text-muted">Contact our support team</p>
                    <a href="mailto:support@darkfibre.co.ke" class="btn btn-sm btn-outline-kp-primary w-100 mb-1">
                        <i class="fas fa-envelope"></i> Email Support
                    </a>
                    <a href="tel:+254203201000" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fas fa-phone"></i> Call: 020 3201 000
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-book-open text-kp-blue me-2"></i>
                        <strong>Help Center</strong>
                        <small class="text-muted ms-2">{{ $roleDisplayName }} Documentation</small>
                    </div>
                    <div>
                        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    @yield('role-help-content')
                </div>

                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Last updated: {{ config('help.last_updated', 'May 2026') }}
                            </small>
                        </div>
                        <div>
                            <small class="text-muted me-2">Was this page helpful?</small>
                            <button class="btn btn-sm btn-outline-kp-success feedback-btn" data-helpful="1">
                                <i class="fas fa-thumbs-up"></i> Yes
                            </button>
                            <button class="btn btn-sm btn-outline-danger feedback-btn" data-helpful="0">
                                <i class="fas fa-thumbs-down"></i> No
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Feedback buttons
    document.querySelectorAll('.feedback-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const helpful = this.dataset.helpful;
            const page = window.location.pathname;

            fetch('{{ route("help.feedback") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    page: page,
                    helpful: helpful,
                    role: '{{ $role ?? 'unknown' }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Thank you for your feedback!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });

    // Search functionality
    const searchInput = document.getElementById('helpSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const items = document.querySelectorAll('.list-group-item-action');

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(filter)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endpush

@push('styles')
<style>
    .list-group-item.active {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .list-group-item-action:hover {
        background-color: #f8f9fa;
    }

    @media print {
        .btn, .dropdown, .list-group-item-action, .card-footer, .support-card {
            display: none !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .container-fluid {
            padding: 0 !important;
        }
    }

    .feedback-btn {
        transition: all 0.3s ease;
    }

    .feedback-btn:hover {
        transform: scale(1.05);
    }
</style>
@endpush
@endsection
