<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="pusher-key" content="{{ env('PUSHER_APP_KEY') }}">
    <meta name="pusher-cluster" content="{{ env('PUSHER_APP_CLUSTER', 'mt1') }}">

    <title>@yield('title', 'Dark Fibre CRM')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
    /* Fixed navbar offset */
    body {
        padding-top: 76px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Ensure navbar stays on top */
    .navbar {
        box-shadow: 0 2px 10px rgba(0,0,0,.1);
        z-index: 1030;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
    }

    /* Fix dropdown z-index */
    .dropdown-menu {
        z-index: 1050;
        pointer-events: auto !important;
    }

    /* Custom styles for better appearance */
    .main-content {
        flex: 1;
        padding-top: 1rem;
        padding-bottom: 2rem;
    }

    .navbar-brand {
        font-weight: 600;
    }

    .dropdown-header {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }

    /* Improved responsive behavior */
    .navbar-nav {
        flex-wrap: wrap;
    }

    .nav-item {
        white-space: nowrap;
    }

    /* Ensure dropdowns don't cause overflow */
    .dropdown-menu {
        max-height: 70vh;
        overflow-y: auto;
    }

    /* Better mobile handling */
    @media (max-width: 991.98px) {
        body {
            padding-top: 56px;
        }

        .navbar-collapse {
            max-height: 80vh;
            overflow-y: auto;
            margin-top: 0.5rem;
            background-color: #212529;
            padding: 1rem;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .navbar-collapse.show {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1040;
        }

        .nav-item {
            width: 100%;
        }

        .dropdown-menu {
            position: static !important;
            transform: none !important;
            border: none;
            background-color: transparent;
            padding-left: 1.5rem;
            box-shadow: none;
        }

        .dropdown-divider {
            margin-left: 1.5rem;
        }
    }

    /* Prevent horizontal scroll on very small screens */
    @media (max-width: 575.98px) {
        .container-fluid {
            padding-left: 10px;
            padding-right: 10px;
        }

        .navbar-brand {
            font-size: 1rem;
        }
    }

    /* Card styling for consistency */
    .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
    }

    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
        padding: 0.75rem 1.25rem;
    }

    /* Statistics cards styling */
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }

    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }

    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }

    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }

    .text-gray-800 {
        color: #5a5c69 !important;
    }

    .text-gray-300 {
        color: #dddfeb !important;
    }

    /* Table styling */
    .table th {
        border-top: none;
        font-weight: 600;
        color: #6e707e;
        background-color: #f8f9fc;
    }

    /* Alert styling */
    .alert {
        border: none;
        border-radius: 0.35rem;
        padding: 1rem 1.25rem;
    }

    /* Avatar styling */
    .avatar-sm {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
    }

    /* Badge styling */
    .badge {
        font-weight: 500;
        padding: 0.25em 0.6em;
    }

    /* Button styling */
    .btn {
        border-radius: 0.35rem;
        font-weight: 500;
    }

    /* Footer styling */
    footer {
        margin-top: auto;
        background-color: #212529 !important;
    }

    /* Loading spinner */
    .spinner-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 200px;
    }

    /* Print styles */
    @media print {
        .navbar, footer, .no-print {
            display: none !important;
        }

        body {
            padding-top: 0;
        }

        .card {
            border: none;
            box-shadow: none;
        }
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Kenya Fibre Dashboard link base styles */
    .kenya-fibre-link {
        position: relative;
        transition: all 0.3s ease;
    }

    .kenya-fibre-link:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-1px);
    }

    .kenya-fibre-link.active {
        background: rgba(79, 70, 229, 0.2);
        color: #fff !important;
        border-radius: 5px;
    }

    /* Modal fixes */
    .modal-backdrop {
        z-index: 1040 !important;
    }

    .modal {
        z-index: 1055 !important;
    }

    /* Footer Styles */
    .footer-compact {
        background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
        border-top: 3px solid #4f46e5;
        font-size: 0.875rem;
    }

    .footer-brand .brand-icon {
        width: 40px;
        height: 40px;
        background: rgba(79, 70, 229, 0.1);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .footer-heading {
        position: relative;
        padding-bottom: 5px;
    }

    .footer-heading::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 25px;
        height: 2px;
        background: linear-gradient(90deg, #4f46e5, transparent);
        border-radius: 1px;
    }

    .footer-link {
        color: #cbd5e0 !important;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .footer-link:hover:not(.disabled-link) {
        color: #fff !important;
    }

    .footer-link.disabled-link {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .footer-link i {
        width: 16px;
        text-align: center;
    }

    .social-icon.small-icon {
        width: 28px;
        height: 28px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 6px;
        color: #cbd5e0;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .social-icon.small-icon:hover {
        background: #4f46e5;
        color: white;
        transform: translateY(-1px);
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .back-to-top {
        border-radius: 6px;
        padding: 4px 12px;
        font-weight: 500;
        transition: all 0.2s ease;
        border-width: 1px;
        font-size: 0.875rem;
    }

    .back-to-top:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    /* Finance dropdown styling */
    .icon-container {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background-color: rgba(0,0,0,0.05);
    }

    .dropdown-item:hover .icon-container {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }

    .dropdown-item:active .icon-container {
        background-color: rgba(var(--bs-primary-rgb), 0.2);
    }

    /* Finance badge */
    .navbar .badge.bg-finance {
        background: linear-gradient(45deg, #28a745, #20c997);
    }

    /* ===== NAVIGATION FIXES ===== */
    /* Fix for all nav links */
    .navbar-nav .nav-link,
    .nav-link-fix,
    .kenya-fibre-link {
        cursor: pointer !important;
        pointer-events: auto !important;
    }

    /* Dropdown items */
    .dropdown-item {
        cursor: pointer !important;
        pointer-events: auto !important;
    }

    /* Mobile navigation fixes */
    @media (max-width: 991.98px) {
        .navbar-nav .nav-link,
        .nav-link-fix,
        .kenya-fibre-link {
            padding: 12px 16px !important;
            margin: 2px 0 !important;
            width: 100% !important;
            display: block !important;
        }

        .dropdown-menu {
            position: static !important;
            width: 100% !important;
            background: transparent !important;
            border: none !important;
            padding-left: 20px !important;
        }

        .dropdown-item {
            color: rgba(255,255,255,0.8) !important;
            padding: 8px 20px !important;
        }

        .dropdown-item:hover {
            color: rgba(255,255,255,0.9) !important;
            background-color: transparent !important;
        }
    }

    /* Responsive footer */
    @media (max-width: 768px) {
        .footer-compact {
            text-align: center;
        }

        .footer-heading::after {
            left: 50%;
            transform: translateX(-50%);
        }

        .footer-meta {
            justify-content: center !important;
            margin-top: 10px;
        }
    }

    @media (max-width: 576px) {
        .footer-compact {
            padding-top: 20px !important;
            padding-bottom: 20px !important;
        }

        .footer-brand {
            flex-direction: column;
            text-align: center;
        }

        .footer-brand .brand-icon {
            margin: 0 auto 10px;
        }
    }

    /* Notification badge styling */
    .notification-badge {
        font-size: 0.6rem;
        padding: 0.25rem 0.4rem;
    }

    /* Notification dropdown styling */
    .notifications-menu .dropdown-item-text {
        white-space: normal;
        padding: 0.5rem 1rem;
    }

    .notifications-menu .avatar {
        flex-shrink: 0;
    }

    /* Notification dropdown styling */
.notifications-menu {
    padding: 0;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.notification-item {
    transition: all 0.2s ease;
    padding: 12px 15px;
}

.notification-item.unread {
    background-color: #f0f7ff;
    border-left: 3px solid #0d6efd;
}

.notification-item.read {
    background-color: #ffffff;
    border-left: 3px solid #dee2e6;
    opacity: 0.85;
}

.notification-item.unread:hover {
    background-color: #e3f2fd;
}

.notification-item.read:hover {
    background-color: #f8f9fa;
    opacity: 1;
}

.notification-item .avatar div {
    transition: all 0.2s ease;
}

.notification-item.unread .avatar div {
    box-shadow: 0 2px 4px rgba(13, 110, 253, 0.3);
}

.notification-badge {
    font-size: 0.6rem;
    padding: 0.25rem 0.4rem;
}

/* Animation for new notifications */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-item {
    animation: fadeIn 0.3s ease;
}
</style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-network-wired me-2"></i>Dark Fibre CRM
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <!-- DASHBOARD - DIRECT LINK (NOT A DROPDOWN) -->
                        @if(in_array(Auth::user()->role, ['admin', 'technical_admin', 'system_admin', 'accountmanager_admin']))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->role === 'customer')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.customer-dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->role === 'finance')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('finance.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                          <li class="nav-item">
            <a class="nav-link" href="{{ route('finance.emails.settings') }}">
                <i class="fas fa-envelope me-2"></i>
                <span>Email Settings</span>
            </a>
        </li>
                        @endif

                        @if(Auth::user()->role === 'designer')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('designer.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->role === 'surveyor')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('surveyor.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->role === 'technician')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('technician.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->role === 'ict_engineer')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('ictengineer.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->role === 'account_manager')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('account-manager.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->role === 'debt_manager')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('finance.debt.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        @endif

                        <!-- Maintenance Module - Dropdown -->
                        @can('view-maintenance')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-tools me-1"></i> Maintenance
                            </a>
                            <div class="dropdown-menu">
                                @can('isTechnician')
                                    <a class="dropdown-item" href="{{ route('technician.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i> My Dashboard
                                    </a>
                                    <a class="dropdown-item" href="{{ route('technician.work-orders.index') }}">
                                        <i class="fas fa-clipboard-list me-2"></i> My Work Orders
                                    </a>
                                    <a class="dropdown-item" href="{{ route('technician.equipment.index') }}">
                                        <i class="fas fa-toolbox me-2"></i> Equipment
                                    </a>
                                    <div class="dropdown-divider"></div>
                                @endcan

                                <a class="dropdown-item" href="{{ route('maintenance.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Maintenance Dashboard
                                </a>

                                @can('create-maintenance-request')
                                <a class="dropdown-item" href="{{ route('maintenance.requests.create') }}">
                                    <i class="fas fa-plus-circle me-2"></i> New Request
                                </a>
                                @endcan

                                <a class="dropdown-item" href="{{ route('maintenance.requests.index') }}">
                                    <i class="fas fa-list me-2"></i> All Requests
                                </a>

                                @can('assign-work-orders')
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('maintenance.work-orders.index') }}">
                                    <i class="fas fa-clipboard-check me-2"></i> Work Orders
                                </a>
                                <a class="dropdown-item" href="{{ route('maintenance.work-orders.create') }}">
                                    <i class="fas fa-plus-circle me-2"></i> Create Work Order
                                </a>
                                @endcan

                                @can('manage-equipment')
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('maintenance.equipment.index') }}">
                                    <i class="fas fa-toolbox me-2"></i> Equipment Management
                                </a>
                                @endcan

                                @can('view-maintenance-reports')
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('maintenance.reports') }}">
                                    <i class="fas fa-chart-bar me-2"></i> Reports & Analytics
                                </a>
                                @endcan
                            </div>
                        </li>
                        @endcan

                        <!-- Admin Menu Items -->
                        @if(in_array(Auth::user()->role, ['admin', 'technical_admin', 'system_admin']))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.users') }}">
                                    <i class="fas fa-users me-1"></i>Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.customers.assign') }}">
                                    <i class="fas fa-user-tie me-1"></i>Assign Customers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.design-requests.index') }}">
                                    <i class="fas fa-drafting-compass me-1"></i>Design Requests
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.leases.index') }}">
                                    <i class="fas fa-network-wired me-1"></i>Leases
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.tickets') }}">
                                    <i class="fas fa-ticket-alt me-1"></i>Tickets
                                </a>
                            </li>
                        @endif

                        <!-- Customer Menu Items -->
                        @if(Auth::user()->role === 'customer')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.profile.show') }}">
                                    <i class="fas fa-id-card me-1"></i>Profile
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.leases.index') }}">
                                    <i class="fas fa-network-wired me-1"></i>My Leases
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.tickets') }}">
                                    <i class="fas fa-ticket-alt me-1"></i>Support
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.design-requests.index') }}">
                                    <i class="fas fa-drafting-compass me-1"></i>Design Requests
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.billings.index') }}">
                                    <i class="fas fa-file-invoice me-1"></i>Invoices
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.documents.index') }}">
                                    <i class="fas fa-folder me-1"></i>My Documents
                                </a>
                            </li>
                           <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="certificatesDropdown" role="button"
       data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-certificate me-1"></i> Certificates
    </a>
    <ul class="dropdown-menu" aria-labelledby="certificatesDropdown">
        <li>
            <a class="dropdown-item" href="{{ route('customer.certificates.conditional.index') }}">
                <i class="fas fa-file-contract me-2"></i> Conditional Certificates
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('customer.certificates.acceptance.index') }}">
                <i class="fas fa-check-circle me-2"></i> Acceptance Certificates
            </a>
        </li>
    </ul>
</li>

                        @endif

                        <!-- Finance Menu Items - Dropdown -->
                       @if(Auth::user()->role === 'finance')
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-chart-line me-1"></i>
        <span class="d-none d-lg-inline">Finance</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 300px;">
        <li class="dropdown-header">
            <div class="d-flex align-items-center">
                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                    <i class="fas fa-chart-line text-white"></i>
                </div>
                <div>
                    <strong>Finance Dashboard</strong>
                    <div class="text-muted small">Manage all financial operations</div>
                </div>
            </div>
        </li>

        <li><hr class="dropdown-divider"></li>

        <!-- Existing Finance Items -->
        <li>
            <a class="dropdown-item py-2" href="{{ route('leases.finance.index') }}">
                <div class="d-flex align-items-center">
                    <div class="icon-container me-3"><i class="fas fa-file-contract text-primary"></i></div>
                    <div><div class="fw-medium">Leases Management</div><small class="text-muted">View, search and manage all leases</small></div>
                </div>
            </a>
        </li>
        <li>
            <a class="dropdown-item py-2" href="{{ route('finance.billing.index') }}">
                <div class="d-flex align-items-center">
                    <div class="icon-container me-3"><i class="fas fa-file-invoice-dollar text-success"></i></div>
                    <div><div class="fw-medium">Lease Billings</div><small class="text-muted">Manage billing cycles and invoices</small></div>
                </div>
            </a>
        </li>
        <li>
            <a class="dropdown-item py-2" href="{{ route('finance.payments.followups') }}">
                <div class="d-flex align-items-center">
                    <div class="icon-container me-3"><i class="fas fa-money-check text-info"></i></div>
                    <div><div class="fw-medium">Payment Management</div><small class="text-muted">Track and process payments</small></div>
                </div>
            </a>
        </li>

        <li><hr class="dropdown-divider"></li>

        <li>
            <a class="dropdown-item py-2" href="{{ route('finance.transactions.index') }}">
                <div class="d-flex align-items-center">
                    <div class="icon-container me-3"><i class="fas fa-exchange-alt text-warning"></i></div>
                    <div><div class="fw-medium">Transactions</div><small class="text-muted">View all financial transactions</small></div>
                </div>
            </a>
        </li>
        <li>
            <a class="dropdown-item py-2" href="{{ route('finance.auto-billing') }}">
                <div class="d-flex align-items-center">
                    <div class="icon-container me-3"><i class="fas fa-robot text-secondary"></i></div>
                    <div><div class="fw-medium">Auto Billing</div><small class="text-muted">Automated billing configurations</small></div>
                </div>
            </a>
        </li>

        <li><hr class="dropdown-divider"></li>

        <!-- Financial Reports -->
        <li>
            <a class="dropdown-item py-2" href="{{ route('finance.reports') }}">
                <div class="d-flex align-items-center">
                    <div class="icon-container me-3"><i class="fas fa-chart-bar text-danger"></i></div>
                    <div><div class="fw-medium">Financial Reports</div><small class="text-muted">Generate detailed financial reports</small></div>
                </div>
            </a>
        </li>

        <!-- AI Analytics Section Header -->
        <li class="dropdown-header mt-2">
            <div class="text-muted small text-uppercase">AI Analytics</div>
        </li>

        <!-- AI Dashboard -->
        <li>
            <a class="dropdown-item py-2" href="{{ route('finance.ai.dashboard') }}">
                <div class="d-flex align-items-center">
                    <div class="icon-container me-3"><i class="fas fa-brain text-purple"></i></div>
                    <div>
                        <div class="fw-medium">Debtors Analytics</div>
                        <small class="text-muted">AI-powered debt analysis dashboard</small>
                    </div>
                </div>
            </a>
        </li>

        <!-- Predictive Analytics -->
        <li>
            <a class="dropdown-item py-2" href="{{ route('finance.ai.predictive') }}">
                <div class="d-flex align-items-center">
                    <div class="icon-container me-3"><i class="fas fa-chart-line text-warning"></i></div>
                    <div>
                        <div class="fw-medium">Predictive Analytics</div>
                        <small class="text-muted">Forecasts and predictions</small>
                    </div>
                </div>
            </a>
        </li>

        <!-- Recommendations -->
        <li>
            <a class="dropdown-item py-2" href="{{ route('finance.ai.recommendations') }}">
                <div class="d-flex align-items-center">
                    <div class="icon-container me-3"><i class="fas fa-lightbulb text-success"></i></div>
                    <div>
                        <div class="fw-medium">AI Recommendations</div>
                        <small class="text-muted">Actionable insights</small>
                    </div>
                </div>
            </a>
        </li>

        <!-- Generate Report -->
        <li>
            <a class="dropdown-item py-2" href="{{ route('finance.ai.report') }}">
                <div class="d-flex align-items-center">
                    <div class="icon-container me-3"><i class="fas fa-file-pdf text-danger"></i></div>
                    <div>
                        <div class="fw-medium">Generate Report</div>
                        <small class="text-muted">Export analysis report</small>
                    </div>
                </div>
            </a>
        </li>

        <li><hr class="dropdown-divider"></li>

        <!-- Finance Analytics -->
        <li>
            <a class="dropdown-item py-2" href="{{ route('finance.financial-analytics.dashboard') }}">
                <div class="d-flex align-items-center">
                    <div class="icon-container me-3"><i class="fas fa-chart-pie text-info"></i></div>
                    <div><div class="fw-medium">Finance Analytics</div><small class="text-muted">Comprehensive financial analytics</small></div>
                </div>
            </a>
        </li>

        <li><hr class="dropdown-divider"></li>

        <!-- Financial Parameters -->
        <li>
            <a class="dropdown-item py-2" href="{{ route('finance.financial-parameters.index') }}">
                <div class="d-flex align-items-center">
                    <div class="icon-container me-3"><i class="fas fa-cog text-dark"></i></div>
                    <div><div class="fw-medium">Financial Parameters</div><small class="text-muted">Configure financial settings</small></div>
                </div>
            </a>
        </li>
    </ul>
</li>
@endif

                        <!-- Designer Menu Items -->
                        @if(Auth::user()->role === 'designer')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('designer.requests.index') }}">
                                    <i class="fas fa-drafting-compass me-1"></i>Design Requests
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('designer.quotations.index') }}">
                                    <i class="fas fa-file-invoice-dollar me-1"></i>Quotations
                                </a>
                            </li>
                        @endif

                        <!-- Surveyor Menu Items -->
                        @if(Auth::user()->role === 'surveyor')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('surveyor.assignments.index') }}">
                                    <i class="fas fa-tasks me-1"></i>Assignments
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('surveyor.routes.index') }}">
                                    <i class="fas fa-route me-1"></i>Routes
                                </a>
                            </li>
                        @endif

                        <!-- Technician Quick Access Menu Items -->
                        @can('isTechnician')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('technician.work-orders.index') }}">
                                    <i class="fas fa-clipboard-list me-1"></i> Work Orders
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('technician.equipment.index') }}">
                                    <i class="fas fa-toolbox me-1"></i> Equipment
                                </a>
                            </li>
                        @endcan

                        <!-- Account Manager Menu Items -->
                        @if(Auth::user()->role === 'account_manager')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('account-manager.customers.index') }}">
                                    <i class="fas fa-users me-1"></i>My Customers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('account-manager.tickets.index') }}">
                                    <i class="fas fa-ticket-alt me-1"></i>Support Tickets
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('account-manager.payments.index') }}">
                                    <i class="fas fa-money-bill-wave me-1"></i>Payment Followups
                                </a>
                            </li>
                        @endif

                        <!-- Debt Manager Menu -->
                        @include('partials.menus.debt-manager')

                        <!-- Marketing Admin (Account Manager Admin) Menu Items -->
                        @if(Auth::user()->role === 'accountmanager_admin')
                            <!-- Marketing Analytics Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-chart-pie me-1"></i>Marketing Analytics
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.analytics') }}">
                                        <i class="fas fa-chart-line me-2"></i>Performance Analytics
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.campaigns') }}">
                                        <i class="fas fa-bullhorn me-2"></i>Campaign Management
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.reports') }}">
                                        <i class="fas fa-file-alt me-2"></i>Marketing Reports
                                    </a></li>
                                </ul>
                            </li>

                            <!-- Team Management Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-tie me-1"></i>Team Management
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.account-managers.index') ?? url('/admin/account-managers/index') }}">
                                        <i class="fas fa-users me-2"></i>Account Managers
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.performance') ?? url('/marketing-admin/performance') }}">
                                        <i class="fas fa-trophy me-2"></i>Team Performance
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.targets') ?? url('/marketing-admin/targets') }}">
                                        <i class="fas fa-bullseye me-2"></i>Sales Targets
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.commissions') ?? url('/marketing-admin/commissions') }}">
                                        <i class="fas fa-money-bill-wave me-2"></i>Commission Reports
                                    </a></li>
                                </ul>
                            </li>

                            <!-- STANDALONE LINKS -->
                            <li class="nav-item">
                                <a class="nav-link nav-link-fix" href="{{ route('marketing-admin.customer-insights') }}">
                                    <i class="fas fa-user-chart me-1"></i>Customer Insights
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link nav-link-fix" href="{{ route('marketing-admin.sales-pipeline') ?? url('/marketing-admin/sales-pipeline') }}">
                                    <i class="fas fa-funnel-dollar me-1"></i>Sales Pipeline
                                </a>
                            </li>
                        @endif

                        <!-- Kenya Fibre Dashboard -->
                        @if(!in_array(Auth::user()->role, ['customer']))
                            <li class="nav-item">
                                <a class="nav-link kenya-fibre-link nav-link-fix" href="{{ route('kenya.fibre.dashboard') }}">
                                    <i class="fas fa-globe-africa me-1"></i> Kenya Fibre Dashboard
                                </a>
                            </li>
                        @endif

                          <!-- Statements -->
                    <li class="nav-item">
                        @auth
                            <a class="nav-link nav-link-fix"
                            href="{{ auth()->user()->role === 'customer'
                                        ? route('customer.statements')
                                        : route('statements.index') }}">
                                <i class="fas fa-file-invoice me-1"></i>
                                {{ auth()->user()->role === 'customer'
                                        ? 'My Statements'
                                        : 'Generate Statements' }}
                            </a>
                        @endauth
                    </li>

                        <!-- Chat Link -->
                        @can('use-chat')
<li class="nav-item">
    <a class="nav-link nav-link-fix" href="{{ route('chat.index') }}">
        <i class="fas fa-comments me-1"></i> WeChat
        @auth
            @php
                $unreadCount = auth()->user()->totalUnreadMessages();
            @endphp
            @if($unreadCount > 0)
                <span class="badge bg-danger ms-1">{{ $unreadCount }}</span>
            @endif
        @endauth
    </a>
</li>
@endcan

                        <!-- Notifications Dropdown -->
                        @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                @php
                                    $unreadNotificationsCount = auth()->user()->unreadNotifications->count();
                                    $allNotifications = auth()->user()->notifications()->latest()->take(10)->get();
                                @endphp
                                @if($unreadNotificationsCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge">
                                        {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                                    </span>
                                @else
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge" style="display: none;">
                                        0
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end notifications-menu" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 450px; overflow-y: auto;">
                                <li class="dropdown-header bg-light d-flex justify-content-between align-items-center">
                                    <span>Notifications</span>
                                    @if($unreadNotificationsCount > 0)
                                        <span class="badge bg-primary">{{ $unreadNotificationsCount }} new</span>
                                    @endif
                                </li>
                                <div id="notificationsList">
                                    @forelse($allNotifications as $notification)
                                        @php
                                            $data = $notification->data;
                                            $isUnread = is_null($notification->read_at);
                                        @endphp
                                        <li class="dropdown-item-text notification-item {{ $isUnread ? 'unread' : 'read' }}"
                                            data-id="{{ $notification->id }}">
                                            <div class="d-flex align-items-start">
                                                <div class="avatar me-2 mt-1">
                                                    <div class="bg-{{ $isUnread ? 'primary' : 'secondary' }} rounded-circle text-white d-flex align-items-center justify-content-center"
                                                         style="width: 32px; height: 32px; font-size: 14px;">
                                                        {{ $data['sender_avatar'] ?? 'N' }}
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between">
                                                        <small class="fw-bold {{ $isUnread ? 'text-dark' : 'text-secondary' }}">
                                                            {{ $data['sender_name'] ?? 'Someone' }}
                                                            @if($isUnread)
                                                                <span class="badge bg-primary ms-1" style="font-size: 0.5rem;">New</span>
                                                            @endif
                                                        </small>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                                    </div>
                                                    <small class="d-block {{ $isUnread ? 'text-dark' : 'text-muted' }}">{{ $data['message_preview'] ?? 'New message' }}</small>
                                                    <div class="mt-2 d-flex gap-2">
                                                        <a href="#" onclick="event.preventDefault(); openChat({{ $data['conversation_id'] ?? 0 }})"
                                                           class="small text-primary text-decoration-none">
                                                            <i class="fas fa-comment"></i> Open Chat
                                                        </a>
                                                        @if($isUnread)
                                                            <a href="#" onclick="event.preventDefault(); markAsRead('{{ $notification->id }}')"
                                                               class="small text-success text-decoration-none">
                                                                <i class="fas fa-check-circle"></i> Mark Read
                                                            </a>
                                                        @else
                                                            <span class="small text-muted">
                                                                <i class="fas fa-check-double"></i> Read
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        @if(!$loop->last)
                                            <li class="dropdown-divider" style="margin: 0;"></li>
                                        @endif
                                    @empty
                                        <li class="text-center text-muted py-4">
                                            <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                            <p class="mb-0">No notifications</p>
                                        </li>
                                    @endforelse
                                </div>
                                @if($allNotifications->count() > 0)
                                    <li><hr class="dropdown-divider"></li>
                                    <li class="text-center p-2">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-list"></i> View All
                                            </a>
                                            @if($unreadNotificationsCount > 0)
                                                <a href="#" onclick="event.preventDefault(); markAllAsRead()" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-check-double"></i> Mark All Read
                                                </a>
                                            @endif
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </li>
                        @endauth
                    @endauth
                </ul>

                <!-- Right-side navigation -->
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i>
                                <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                                <span class="badge bg-{{
                                    Auth::user()->role === 'admin' ? 'danger' :
                                    (Auth::user()->role === 'technical_admin' ? 'warning' :
                                    (Auth::user()->role === 'system_admin' ? 'primary' :
                                    (Auth::user()->role === 'accountmanager_admin' ? 'info' :
                                    (Auth::user()->role === 'technician' ? 'warning' :
                                    (Auth::user()->role === 'debt_manager' ? 'info' :
                                    (Auth::user()->role === 'account_manager' ? 'info' : 'secondary'))))))
                                }} ms-1">
                                    {{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}
                                </span>
                                @if(Auth::user()->role === 'technician' && Auth::user()->employee_id)
                                    <span class="badge bg-info ms-1">{{ Auth::user()->employee_id }}</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li class="dropdown-header">
                                    <small>Logged in as</small><br>
                                    <strong>{{ Auth::user()->email }}</strong>
                                </li>
                                <li><hr class="dropdown-divider"></li>

                                @if(Auth::user()->role === 'customer')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('customer.profile.show') }}">
                                            <i class="fas fa-user me-2"></i>My Profile
                                        </a>
                                    </li>
                                @endif

                                @if(Auth::user()->role === 'technician')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('technician.profile') }}">
                                            <i class="fas fa-id-badge me-2"></i>My Profile
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('technician.equipment.index') }}">
                                            <i class="fas fa-toolbox me-2"></i>Equipment
                                        </a>
                                    </li>
                                @endif

                                @if(Auth::user()->role === 'surveyor')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('surveyor.profile') }}">
                                            <i class="fas fa-user me-2"></i>My Profile
                                        </a>
                                    </li>
                                @endif

                                @if(Auth::user()->role === 'designer')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('designer.profile') }}">
                                            <i class="fas fa-user me-2"></i>My Profile
                                        </a>
                                    </li>
                                @endif

                                @if(Auth::user()->role === 'account_manager')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('account-manager.dashboard') }}">
                                            <i class="fas fa-user-tie me-2"></i>My Dashboard
                                        </a>
                                    </li>
                                @endif

                                @if(Auth::user()->role === 'debt_manager')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('finance.debt.dashboard') }}">
                                            <i class="fas fa-user-tie me-2"></i>My Dashboard
                                        </a>
                                    </li>
                                @endif

                                @if(Auth::user()->role === 'accountmanager_admin')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('marketing-admin.dashboard') }}">
                                            <i class="fas fa-chart-pie me-2"></i>Marketing Dashboard
                                        </a>
                                    </li>
                                @endif

                                <li>
                                    <a class="dropdown-item" href="{{ route('designer.profile') ?? '#' }}">
                                        <i class="fas fa-cog me-2"></i>Settings
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        @if(Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register.customer') }}">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @hasSection('page-title')
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 text-gray-800 mb-0">
                            @yield('page-title')
                        </h1>
                        @hasSection('breadcrumbs')
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    @yield('breadcrumbs')
                                </ol>
                            </nav>
                        @endif
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-compact bg-dark text-light py-3 py-sm-4 mt-auto">
        <div class="container-fluid px-3 px-sm-4">
            <div class="row align-items-center g-2 g-sm-3">
                <div class="col-lg-4 mb-2 mb-lg-0">
                    <div class="footer-brand d-flex align-items-center mb-2">
                        <div class="brand-icon me-2">
                            <i class="fas fa-network-wired fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-white">Dark Fibre CRM</h5>
                            <p class="mb-0 text-light opacity-75 small">Fibre infrastructure management</p>
                        </div>
                    </div>
                    <div class="social-links mt-2">
                        <div class="d-flex gap-1">
                            <a href="#" class="social-icon small-icon" title="LinkedIn" data-bs-toggle="tooltip">
                                <i class="fab fa-linkedin-in fa-sm"></i>
                            </a>
                            <a href="#" class="social-icon small-icon" title="Twitter" data-bs-toggle="tooltip">
                                <i class="fab fa-twitter fa-sm"></i>
                            </a>
                            <a href="#" class="social-icon small-icon" title="GitHub" data-bs-toggle="tooltip">
                                <i class="fab fa-github fa-sm"></i>
                            </a>
                            <a href="mailto:support@example.com" class="social-icon small-icon" title="Email" data-bs-toggle="tooltip">
                                <i class="fas fa-envelope fa-sm"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 mb-2 mb-lg-0">
                    <div class="row g-2">
                        <div class="col-6 col-sm-3">
                            <h6 class="footer-heading mb-1 text-primary small fw-bold">Quick Links</h6>
                            <ul class="list-unstyled footer-links mb-0">
                                <li class="mb-1">
                                    @if(Route::has('home'))
                                        <a href="{{ route('home') }}" class="footer-link small">
                                            <i class="fas fa-home fa-xs me-1"></i>Home
                                        </a>
                                    @else
                                        <a href="#" class="footer-link disabled-link small" data-bs-toggle="tooltip" title="Route not available">
                                            <i class="fas fa-home fa-xs me-1"></i>Home
                                        </a>
                                    @endif
                                </li>
                                <li class="mb-2">
                                    @if(Route::has('about'))
                                        <a href="{{ route('about') }}" class="footer-link">
                                            <i class="fas fa-info-circle fa-fw me-2"></i>About Us
                                        </a>
                                    @else
                                        <a href="#" class="footer-link disabled-link" data-bs-toggle="tooltip" title="Route not available">
                                            <i class="fas fa-info-circle fa-fw me-2"></i>About Us
                                        </a>
                                    @endif
                                </li>
                                <li class="mb-1">
                                    @if(Route::has('contact'))
                                        <a href="{{ route('contact') }}" class="footer-link small">
                                            <i class="fas fa-envelope fa-xs me-1"></i>Contact
                                        </a>
                                    @else
                                        <a href="#" class="footer-link disabled-link small" data-bs-toggle="tooltip" title="Route not available">
                                            <i class="fas fa-envelope fa-xs me-1"></i>Contact
                                        </a>
                                    @endif
                                </li>
                                <li class="mb-1">
                                    @if(Route::has('support'))
                                        <a href="{{ route('support') }}" class="footer-link small">
                                            <i class="fas fa-headset fa-xs me-1"></i>Support
                                        </a>
                                    @else
                                        <a href="#" class="footer-link disabled-link small" data-bs-toggle="tooltip" title="Route not available">
                                            <i class="fas fa-headset fa-xs me-1"></i>Support
                                        </a>
                                    @endif
                                </li>
                            </ul>
                        </div>
                        <div class="col-6 col-sm-3">
                            <h6 class="footer-heading mb-1 text-primary small fw-bold">Legal</h6>
                            <ul class="list-unstyled footer-links mb-0">
                                <li class="mb-1">
                                    @if(Route::has('privacy'))
                                        <a href="{{ route('privacy') }}" class="footer-link small">
                                            <i class="fas fa-shield-alt fa-xs me-1"></i>Privacy
                                        </a>
                                    @else
                                        <a href="#" class="footer-link disabled-link small" data-bs-toggle="tooltip" title="Route not available">
                                            <i class="fas fa-shield-alt fa-xs me-1"></i>Privacy
                                        </a>
                                    @endif
                                </li>
                                <li class="mb-1">
                                    @if(Route::has('terms'))
                                        <a href="{{ route('terms') }}" class="footer-link small">
                                            <i class="fas fa-file-contract fa-xs me-1"></i>Terms
                                        </a>
                                    @else
                                        <a href="#" class="footer-link disabled-link small" data-bs-toggle="tooltip" title="Route not available">
                                            <i class="fas fa-file-contract fa-xs me-1"></i>Terms
                                        </a>
                                    @endif
                                </li>
                                <li class="mb-2">
                                    @if(Route::has('documentation'))
                                        <a href="{{ route('documentation') }}" class="footer-link">
                                            <i class="fas fa-book fa-fw me-2"></i>Documentation
                                        </a>
                                    @else
                                        <a href="#" class="footer-link disabled-link" data-bs-toggle="tooltip" title="Route not available">
                                            <i class="fas fa-book fa-fw me-2"></i>Documentation
                                        </a>
                                    @endif
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-sm-6">
                            <h6 class="footer-heading mb-1 text-primary small fw-bold">Contact</h6>
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-1 d-flex align-items-start">
                                    <i class="fas fa-map-marker-alt fa-xs me-1 mt-1 text-primary"></i>
                                    <span class="text-light opacity-75">Nairobi, Kenya</span>
                                </li>
                                <li class="mb-1 d-flex align-items-start">
                                    <i class="fas fa-envelope fa-xs me-1 mt-1 text-primary"></i>
                                    <span class="text-light opacity-75">support@darkfibre-crm.test</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="system-status d-flex align-items-center justify-content-lg-end mb-2">
                        <div class="status-indicator me-2">
                            <div class="status-dot bg-success"></div>
                        </div>
                        <span class="text-success fw-bold small">Operational</span>
                    </div>
                    <div class="footer-meta d-flex flex-wrap justify-content-lg-end gap-1 small">
                        <span class="badge bg-primary text-white px-2 py-1">
                            v{{ config('app.version', '1.0.0') }}
                        </span>
                        @if(app()->environment('local'))
                            <span class="badge bg-warning text-dark px-2 py-1">Dev</span>
                        @elseif(app()->environment('staging'))
                            <span class="badge bg-info text-white px-2 py-1">Staging</span>
                        @else
                            <span class="badge bg-success text-white px-2 py-1">Prod</span>
                        @endif
                    </div>
                </div>
            </div>

            <hr class="my-3 bg-light opacity-25">

            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="copyright small">
                        <p class="mb-0 text-light opacity-75">
                            &copy; {{ date('Y') }} <strong class="text-primary">Dark Fibre CRM</strong>. All rights reserved.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end align-items-center">
                        <button class="btn btn-outline-light btn-sm back-to-top" id="backToTop">
                            <i class="fas fa-arrow-up"></i>
                            <span class="d-none d-sm-inline ms-1">Top</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

    <!-- Main Script -->
    <script>
    (function() {
        // Back to top button functionality
        document.getElementById('backToTop')?.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Initialize tooltips
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
            try {
                new bootstrap.Tooltip(el);
            } catch (e) {
                console.log('Tooltip error:', e);
            }
        });

        // ==================== NOTIFICATION FUNCTIONS ====================
        window.markAsRead = function(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the notification item in the dropdown
                    const notificationItem = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                    if (notificationItem) {
                        notificationItem.classList.remove('unread');
                        notificationItem.classList.add('read');

                        // Update the avatar color
                        const avatar = notificationItem.querySelector('.avatar div');
                        if (avatar) {
                            avatar.classList.remove('bg-primary');
                            avatar.classList.add('bg-secondary');
                        }

                        // Update the "New" badge
                        const newBadge = notificationItem.querySelector('.badge.bg-primary.ms-1');
                        if (newBadge) newBadge.remove();

                        // Update the action buttons
                        const actionDiv = notificationItem.querySelector('.mt-2.d-flex.gap-2');
                        if (actionDiv) {
                            const markReadBtn = actionDiv.querySelector('a.text-success');
                            if (markReadBtn) {
                                const readSpan = document.createElement('span');
                                readSpan.className = 'small text-muted';
                                readSpan.innerHTML = '<i class="fas fa-check-double"></i> Read';
                                actionDiv.replaceChild(readSpan, markReadBtn);
                            }
                        }
                    }

                    // Update the notification badge
                    window.updateNotificationBadge();
                }
            })
            .catch(error => console.error('Error marking as read:', error));
        };

        window.markAllAsRead = function() {
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update all notification items
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                        item.classList.add('read');

                        const avatar = item.querySelector('.avatar div');
                        if (avatar) {
                            avatar.classList.remove('bg-primary');
                            avatar.classList.add('bg-secondary');
                        }

                        const newBadge = item.querySelector('.badge.bg-primary.ms-1');
                        if (newBadge) newBadge.remove();

                        const actionDiv = item.querySelector('.mt-2.d-flex.gap-2');
                        if (actionDiv) {
                            const markReadBtn = actionDiv.querySelector('a.text-success');
                            if (markReadBtn) {
                                const readSpan = document.createElement('span');
                                readSpan.className = 'small text-muted';
                                readSpan.innerHTML = '<i class="fas fa-check-double"></i> Read';
                                actionDiv.replaceChild(readSpan, markReadBtn);
                            }
                        }
                    });

                    // Update the notification badge
                    window.updateNotificationBadge();

                    // Hide the "Mark All Read" button
                    const markAllBtn = document.querySelector('.btn-outline-success');
                    if (markAllBtn) markAllBtn.style.display = 'none';

                    // Update the header badge
                    const headerBadge = document.querySelector('.dropdown-header .badge.bg-primary');
                    if (headerBadge) headerBadge.remove();
                }
            })
            .catch(error => console.error('Error marking all as read:', error));
        };

        window.openChat = function(conversationId) {
            if (conversationId && conversationId > 0) {
                window.location.href = '{{ route("chat.index") }}?conversation=' + conversationId;
            }
        };

        window.updateNotificationBadge = function() {
            fetch('/notifications/unread-count', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.style.display = 'inline';

                        // Update header badge if exists
                        const headerBadge = document.querySelector('.dropdown-header .badge.bg-primary');
                        if (headerBadge) {
                            headerBadge.textContent = data.count + ' new';
                        }
                    } else {
                        badge.style.display = 'none';

                        // Remove header badge if exists
                        const headerBadge = document.querySelector('.dropdown-header .badge.bg-primary');
                        if (headerBadge) headerBadge.remove();
                    }
                }
            })
            .catch(error => console.error('Error updating badge:', error));
        };

        // Update notification badge periodically
        setInterval(() => {
            window.updateNotificationBadge();
        }, 30000); // Update every 30 seconds

        // Initial badge update
        window.updateNotificationBadge();

        // Listen for Echo events if available
        if (typeof Echo !== 'undefined' && {{ auth()->id() ?? 0 }} > 0) {
            Echo.private(`user.{{ auth()->id() }}`)
                .listen('.message.sent', (e) => {
                    console.log('New message received, updating notifications');
                    window.updateNotificationBadge();

                    // Optional: Show a toast notification
                    if (e.message && e.message.user && e.message.user.id !== {{ auth()->id() }}) {
                        // You could show a toast here
                    }
                });
        }
    })();
    </script>

    @if(config('app.use_alpine', false))
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @endif

    @if(class_exists(\Livewire\Livewire::class))
        @livewireScripts
    @endif

    @stack('scripts')
</body>
</html>
