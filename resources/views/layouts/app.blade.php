<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="pusher-key" content="{{ env('PUSHER_APP_KEY') }}">
    <meta name="pusher-cluster" content="{{ env('PUSHER_APP_CLUSTER', 'mt1') }}">

    <title>@yield('title', 'Dark Fibre CRM') - Kenya Power</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
    /* ========================================
       KENYA POWER CORPORATE COLORS
    ========================================= */
    :root {
        --kp-blue: #0066B3;
        --kp-green: #009639;
        --kp-yellow: #FFD700;
        --kp-dark: #003f20;
        --kp-light-blue: #e8f4fd;
        --kp-light-green: #e6f7ec;
        --kp-light-yellow: #fff8e1;
        --kp-white: #ffffff;
        --kp-gray: #6c757d;
    }

    /* Fixed navbar offset */
    body {
        padding-top: 76px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Navbar styling - Kenya Power Colors */
    .navbar {
        background: linear-gradient(135deg, var(--kp-blue) 0%, var(--kp-green) 100%) !important;
        box-shadow: 0 2px 10px rgba(0,0,0,.1);
        z-index: 1030;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
    }

    .navbar-brand {
        color: white !important;
        font-weight: 600;
    }

    .navbar-brand:hover {
        color: var(--kp-yellow) !important;
        transform: scale(1.02);
        transition: all 0.3s ease;
    }

    .navbar-nav .nav-link {
        color: white !important;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .navbar-nav .nav-link:hover {
        color: var(--kp-yellow) !important;
        transform: translateY(-1px);
    }

    .navbar-nav .nav-link.active {
        background-color: rgba(255,255,255,0.1);
        border-radius: 5px;
        color: var(--kp-yellow) !important;
    }

    /* ========================================
       DROPDOWN STYLES - FIXED
    ========================================= */
    /* Base dropdown - let Bootstrap control display */
    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1050;
        min-width: 10rem;
        padding: 0.5rem 0;
        margin: 0.125rem 0 0;
        font-size: 1rem;
        color: #212529;
        text-align: left;
        list-style: none;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid rgba(0,0,0,.15);
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-top: 3px solid var(--kp-yellow);
    }

    /* Right-aligned dropdown */
    .dropdown-menu-end {
        right: 0;
        left: auto;
    }

    /* Dropdown items */
    .dropdown-item {
        display: block;
        width: 100%;
        padding: 0.5rem 1.5rem;
        clear: both;
        font-weight: 400;
        color: #212529;
        text-align: inherit;
        text-decoration: none;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .dropdown-item:hover,
    .dropdown-item:focus {
        background-color: var(--kp-light-blue);
        color: var(--kp-dark);
    }

    .dropdown-item:active {
        background-color: var(--kp-blue);
        color: white;
    }

    /* Dropdown header */
    .dropdown-header {
        display: block;
        padding: 0.5rem 1.5rem;
        margin-bottom: 0;
        font-size: 0.875rem;
        color: var(--kp-dark);
        white-space: nowrap;
        background: linear-gradient(135deg, var(--kp-light-blue), var(--kp-light-green));
    }

    /* Dropdown divider */
    .dropdown-divider {
        height: 0;
        margin: 0.5rem 0;
        overflow: hidden;
        border-top: 1px solid #e9ecef;
    }

    /* Dropdown toggle arrow */
    .dropdown-toggle::after {
        display: inline-block;
        margin-left: 0.255em;
        vertical-align: 0.255em;
        content: "";
        border-top: 0.3em solid;
        border-right: 0.3em solid transparent;
        border-bottom: 0;
        border-left: 0.3em solid transparent;
    }

    /* Main Content */
    .main-content {
        flex: 1;
        padding-top: 1rem;
        padding-bottom: 2rem;
    }

    /* Card styling - Kenya Power */
    .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(0, 102, 179, 0.1);
        border: 1px solid #e3e6f0;
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 102, 179, 0.15);
    }

    .card-header {
        background-color: var(--kp-light-blue);
        border-bottom: 2px solid var(--kp-blue);
        padding: 0.75rem 1.25rem;
        font-weight: 600;
        color: var(--kp-dark);
        border-radius: 12px 12px 0 0;
    }

    /* Statistics cards - Kenya Power colors */
    .border-left-primary {
        border-left: 4px solid var(--kp-blue) !important;
    }

    .border-left-success {
        border-left: 4px solid var(--kp-green) !important;
    }

    .border-left-warning {
        border-left: 4px solid var(--kp-yellow) !important;
    }

    .border-left-info {
        border-left: 4px solid #36b9cc !important;
    }

    .text-kp-blue {
        color: var(--kp-blue) !important;
    }

    .text-kp-green {
        color: var(--kp-green) !important;
    }

    .text-kp-yellow {
        color: var(--kp-yellow) !important;
    }

    .bg-kp-blue {
        background-color: var(--kp-blue) !important;
    }

    .bg-kp-green {
        background-color: var(--kp-green) !important;
    }

    .bg-kp-yellow {
        background-color: var(--kp-yellow) !important;
    }

    /* Buttons - Kenya Power */
    .btn-kp-primary {
        background-color: var(--kp-blue);
        border-color: var(--kp-blue);
        transition: all 0.3s ease;
    }

    .btn-kp-primary:hover {
        background-color: #005499;
        border-color: #005499;
        transform: translateY(-1px);
    }

    .btn-kp-success {
        background-color: var(--kp-green);
        border-color: var(--kp-green);
        transition: all 0.3s ease;
    }

    .btn-kp-success:hover {
        background-color: #00802c;
        border-color: #00802c;
        transform: translateY(-1px);
    }

    .btn-kp-warning {
        background-color: var(--kp-yellow);
        border-color: var(--kp-yellow);
        color: var(--kp-dark);
        transition: all 0.3s ease;
    }

    .btn-kp-warning:hover {
        background-color: #e6c300;
        border-color: #e6c300;
        transform: translateY(-1px);
    }

    .btn-outline-kp-primary {
        border-color: var(--kp-blue);
        color: var(--kp-blue);
        transition: all 0.3s ease;
    }

    .btn-outline-kp-primary:hover {
        background-color: var(--kp-blue);
        border-color: var(--kp-blue);
        color: white;
        transform: translateY(-1px);
    }

    .btn-outline-kp-success {
        border-color: var(--kp-green);
        color: var(--kp-green);
        transition: all 0.3s ease;
    }

    .btn-outline-kp-success:hover {
        background-color: var(--kp-green);
        border-color: var(--kp-green);
        color: white;
        transform: translateY(-1px);
    }

    /* Table styling */
    .table th {
        border-top: none;
        font-weight: 600;
        color: var(--kp-dark);
        background-color: var(--kp-light-blue);
        border-bottom: 2px solid var(--kp-blue);
    }

    .table-hover tbody tr:hover {
        background-color: var(--kp-light-yellow);
    }

    /* Alert styling - Kenya Power */
    .alert-kp-success {
        background-color: var(--kp-light-green);
        border-left: 4px solid var(--kp-green);
        color: var(--kp-dark);
        border-radius: 10px;
    }

    .alert-kp-warning {
        background-color: var(--kp-light-yellow);
        border-left: 4px solid var(--kp-yellow);
        color: var(--kp-dark);
        border-radius: 10px;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-left: 4px solid #dc3545;
        color: #721c24;
        border-radius: 10px;
    }

    .alert-info {
        background-color: var(--kp-light-blue);
        border-left: 4px solid var(--kp-blue);
        color: var(--kp-dark);
        border-radius: 10px;
    }

    /* Badge styling */
    .badge.bg-kp-blue {
        background-color: var(--kp-blue) !important;
    }

    .badge.bg-kp-green {
        background-color: var(--kp-green) !important;
    }

    .badge.bg-kp-yellow {
        background-color: var(--kp-yellow) !important;
        color: var(--kp-dark);
    }

    /* Pagination */
    .pagination .page-item.active .page-link {
        background-color: var(--kp-blue);
        border-color: var(--kp-blue);
    }

    .pagination .page-link {
        color: var(--kp-blue);
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover {
        background-color: var(--kp-green);
        border-color: var(--kp-green);
        color: white;
    }

    /* Progress bar */
    .progress {
        border-radius: 10px;
        background-color: var(--kp-light-blue);
    }

    .progress-bar {
        background-color: var(--kp-green);
        border-radius: 10px;
    }

    /* Modal styling */
    .modal-header {
        background: linear-gradient(135deg, var(--kp-blue), var(--kp-green));
        color: white;
        border-bottom: none;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    .modal-footer {
        border-top: 1px solid var(--kp-light-blue);
    }

    /* Footer - Kenya Power Colors */
    .footer-compact {
        background: linear-gradient(135deg, var(--kp-dark) 0%, #001a0d 100%) !important;
        border-top: 3px solid var(--kp-yellow);
        font-size: 0.875rem;
    }

    .footer-brand .brand-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 215, 0, 0.1);
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
        background: linear-gradient(90deg, var(--kp-yellow), transparent);
        border-radius: 1px;
    }

    .footer-link {
        color: #cbd5e0 !important;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .footer-link:hover:not(.disabled-link) {
        color: var(--kp-yellow) !important;
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
        background: var(--kp-yellow);
        color: var(--kp-dark);
        transform: translateY(-2px);
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-dot.bg-kp-green {
        background-color: var(--kp-green) !important;
    }

    .back-to-top {
        border-radius: 6px;
        padding: 4px 12px;
        font-weight: 500;
        transition: all 0.2s ease;
        border-width: 1px;
        font-size: 0.875rem;
        border-color: rgba(255,255,255,0.2);
        color: rgba(255,255,255,0.8);
    }

    .back-to-top:hover {
        background: rgba(255, 215, 0, 0.2);
        color: var(--kp-yellow);
        border-color: var(--kp-yellow);
    }

    /* Kenya Fibre Dashboard link styles */
    .kenya-fibre-link {
        position: relative;
        transition: all 0.3s ease;
    }

    .kenya-fibre-link:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-1px);
    }

    /* Notification styling */
    .notification-item.unread {
        background-color: var(--kp-light-blue);
        border-left: 3px solid var(--kp-blue);
    }

    .notification-item.read {
        background-color: #ffffff;
        border-left: 3px solid #dee2e6;
        opacity: 0.85;
    }

    .notification-item.unread:hover {
        background-color: #e3f2fd;
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
        background: linear-gradient(135deg, var(--kp-blue), var(--kp-green));
        color: white;
        border-radius: 50%;
    }

    /* Form controls */
    .form-control:focus, .form-select:focus {
        border-color: var(--kp-blue);
        box-shadow: 0 0 0 0.2rem rgba(0, 102, 179, 0.25);
    }

    /* ========================================
       RESPONSIVE STYLES - FIXED FOR DROPDOWNS
    ========================================= */
    @media (max-width: 991.98px) {
        body {
            padding-top: 56px;
        }

        .navbar-collapse {
            background: linear-gradient(135deg, var(--kp-blue) 0%, var(--kp-green) 100%);
            padding: 1rem;
            border-radius: 0 0 12px 12px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .nav-item {
            width: 100%;
        }

        /* Mobile dropdown styles - different from desktop */
        .dropdown-menu {
            position: static !important;
            width: 100% !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding-left: 1.5rem !important;
            margin-top: 0 !important;
        }

        .dropdown-item {
            color: white !important;
            padding: 8px 16px !important;
        }

        .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.15) !important;
            color: var(--kp-yellow) !important;
        }

        .dropdown-divider {
            border-color: rgba(255, 255, 255, 0.1);
        }

        .dropdown-toggle::after {
            float: right;
            margin-top: 8px;
        }
    }

    @media (max-width: 768px) {
        .footer-compact {
            text-align: center;
        }

        .footer-heading::after {
            left: 50%;
            transform: translateX(-50%);
        }

        .footer-brand {
            justify-content: center;
        }

        .footer-meta {
            justify-content: center !important;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 10px;
            padding-right: 10px;
        }

        .navbar-brand {
            font-size: 1rem;
        }
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
            border: 1px solid #ddd;
            box-shadow: none;
        }
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: var(--kp-light-blue);
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: var(--kp-blue);
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: var(--kp-green);
    }
</style>

    @stack('styles')
</head>
<body>
    <!-- Navbar with Kenya Power Colors -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
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
                            <a href="{{ route('contracts.index') }}"
   class="nav-link {{ request()->routeIs('contracts.*') ? 'active' : '' }}">
    <i class="fas fa-file-contract me-2"></i>
    Contracts
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

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="cakDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-file-alt me-1"></i> CAK Compliance
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="cakDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('asp.create') }}">
                                            <i class="fas fa-server me-2 text-kp-blue"></i> ASP Return
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('csp.create') }}">
                                            <i class="fas fa-envelope me-2 text-kp-green"></i> CSP Return
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('nfp.create') }}">
                                            <i class="fas fa-network-wired me-2 text-kp-yellow"></i> NFP Return
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('asp.index') }}">
                                            <i class="fas fa-list me-2"></i> ASP Submissions
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('csp.index') }}">
                                            <i class="fas fa-list me-2"></i> CSP Submissions
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('nfp.index') }}">
                                            <i class="fas fa-list me-2"></i> NFP Submissions
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('cak.dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-2"></i> CAK Dashboard
                                        </a>
                                    </li>
                                </ul>
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
                            <a href="{{ route('customer.contracts.index') }}"
   class="nav-link {{ request()->routeIs('customer.contracts.*') ? 'active' : '' }}">
    <i class="fas fa-file-contract me-2"></i>
    My Contracts
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
                                            <div class="avatar-sm bg-kp-blue rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-chart-line text-white"></i>
                                            </div>
                                            <div>
                                                <strong>Finance Dashboard</strong>
                                                <div class="text-muted small">Manage all financial operations</div>
                                            </div>
                                        </div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('leases.finance.index') }}">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container me-3"><i class="fas fa-file-contract text-kp-blue"></i></div>
                                                <div><div class="fw-medium">Leases Management</div><small class="text-muted">View, search and manage all leases</small></div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('finance.billing.index') }}">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container me-3"><i class="fas fa-file-invoice-dollar text-kp-green"></i></div>
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
                                                <div class="icon-container me-3"><i class="fas fa-exchange-alt text-kp-yellow"></i></div>
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
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('finance.reports') }}">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container me-3"><i class="fas fa-chart-bar text-danger"></i></div>
                                                <div><div class="fw-medium">Financial Reports</div><small class="text-muted">Generate detailed financial reports</small></div>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="dropdown-header mt-2">
                                        <div class="text-muted small text-uppercase">AI Analytics</div>
                                    </li>
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
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('finance.ai.predictive') }}">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container me-3"><i class="fas fa-chart-line text-kp-yellow"></i></div>
                                                <div>
                                                    <div class="fw-medium">Predictive Analytics</div>
                                                    <small class="text-muted">Forecasts and predictions</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('finance.ai.recommendations') }}">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container me-3"><i class="fas fa-lightbulb text-kp-green"></i></div>
                                                <div>
                                                    <div class="fw-medium">AI Recommendations</div>
                                                    <small class="text-muted">Actionable insights</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
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
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('finance.financial-analytics.dashboard') }}">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container me-3"><i class="fas fa-chart-pie text-info"></i></div>
                                                <div><div class="fw-medium">Finance Analytics</div><small class="text-muted">Comprehensive financial analytics</small></div>
                                            </div>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
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
                            <a href="{{ route('contracts.index') }}"
   class="nav-link {{ request()->routeIs('contracts.*') ? 'active' : '' }}">
    <i class="fas fa-file-contract me-2"></i>
    Contracts
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
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-chart-pie me-1"></i>Marketing Analytics
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.dashboard') }}">Dashboard</a></li>
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.analytics') }}">Performance Analytics</a></li>
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.campaigns') }}">Campaign Management</a></li>
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.reports') }}">Marketing Reports</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-tie me-1"></i>Team Management
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.account-managers.index') ?? url('/admin/account-managers/index') }}">Account Managers</a></li>
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.performance') ?? url('/marketing-admin/performance') }}">Team Performance</a></li>
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.targets') ?? url('/marketing-admin/targets') }}">Sales Targets</a></li>
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.commissions') ?? url('/marketing-admin/commissions') }}">Commission Reports</a></li>
                                </ul>
                            </li>

                            <li class="nav-item">
                            <a href="{{ route('contracts.index') }}"
   class="nav-link {{ request()->routeIs('contracts.*') ? 'active' : '' }}">
    <i class="fas fa-file-contract me-2"></i>
    Contracts
</a>
</li>

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
                                        <span class="badge bg-kp-blue">{{ $unreadNotificationsCount }} new</span>
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
                                                                <span class="badge bg-kp-blue ms-1" style="font-size: 0.5rem;">New</span>
                                                            @endif
                                                        </small>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                                    </div>
                                                    <small class="d-block {{ $isUnread ? 'text-dark' : 'text-muted' }}">{{ $data['message_preview'] ?? 'New message' }}</small>
                                                    <div class="mt-2 d-flex gap-2">
                                                        <a href="#" onclick="event.preventDefault(); openChat({{ $data['conversation_id'] ?? 0 }})"
                                                           class="small text-kp-blue text-decoration-none">
                                                            <i class="fas fa-comment"></i> Open Chat
                                                        </a>
                                                        @if($isUnread)
                                                            <a href="#" onclick="event.preventDefault(); markAsRead('{{ $notification->id }}')"
                                                               class="small text-kp-green text-decoration-none">
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
                                            <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-kp-primary">
                                                <i class="fas fa-list"></i> View All
                                            </a>
                                            @if($unreadNotificationsCount > 0)
                                                <a href="#" onclick="event.preventDefault(); markAllAsRead()" class="btn btn-sm btn-outline-kp-success">
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
                <div class="alert alert-kp-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-kp-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer with Kenya Power Colors -->
    <footer class="footer-compact bg-dark text-light py-3 py-sm-4 mt-auto">
        <div class="container-fluid px-3 px-sm-4">
            <div class="row align-items-center g-2 g-sm-3">
                <div class="col-lg-4 mb-2 mb-lg-0">
                    <div class="footer-brand d-flex align-items-center mb-2">
                        <div class="brand-icon me-2">
                            <i class="fas fa-network-wired fa-lg" style="color: var(--kp-yellow);"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" style="color: var(--kp-yellow);">Dark Fibre CRM</h5>
                            <p class="mb-0 text-light opacity-75 small">Kenya Power Fibre Infrastructure Management</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 mb-2 mb-lg-0">
                    <div class="row g-2">
                        <div class="col-6 col-sm-3">
                            <h6 class="footer-heading mb-1 small fw-bold" style="color: var(--kp-yellow);">Quick Links</h6>
                            <ul class="list-unstyled footer-links mb-0">
                                <li class="mb-1"><a href="{{ url('/') }}" class="footer-link small">Home</a></li>
                                <li class="mb-1"><a href="{{ route('help.index') }}" class="footer-link small">Help Center</a></li>
                                {{-- <li class="mb-1"><a href="{{ route('cak.dashboard') }}" class="footer-link small">CAK Compliance</a></li> --}}
                            </ul>
                        </div>
                        <div class="col-6 col-sm-3">
                            <h6 class="footer-heading mb-1 small fw-bold" style="color: var(--kp-yellow);">Legal</h6>
                            <ul class="list-unstyled footer-links mb-0">
                                <li class="mb-1"><a href="#" class="footer-link small">Privacy Policy</a></li>
                                <li class="mb-1"><a href="#" class="footer-link small">Terms of Service</a></li>
                            </ul>
                        </div>
                        <div class="col-12 col-sm-6">
                            <h6 class="footer-heading mb-1 small fw-bold" style="color: var(--kp-yellow);">Contact</h6>
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-1 d-flex align-items-start">
                                    <i class="fas fa-map-marker-alt fa-xs me-1 mt-1" style="color: var(--kp-yellow);"></i>
                                    <span class="text-light opacity-75">Nairobi, Kenya</span>
                                </li>
                                <li class="mb-1 d-flex align-items-start">
                                    <i class="fas fa-envelope fa-xs me-1 mt-1" style="color: var(--kp-yellow);"></i>
                                    <span class="text-light opacity-75">Fibre@kplc.co.ke</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="system-status d-flex align-items-center justify-content-lg-end mb-2">
                        <div class="status-indicator me-2">
                            <div class="status-dot bg-kp-green"></div>
                        </div>
                        <span class="text-kp-green fw-bold small">System Operational</span>
                    </div>
                    <div class="footer-meta d-flex flex-wrap justify-content-lg-end gap-1 small">
                        <span class="badge px-2 py-1" style="background: linear-gradient(135deg, var(--kp-blue), var(--kp-green)); color: white;">
                            v{{ config('app.version', '1.0.0') }}
                        </span>
                        @if(app()->environment('local'))
                            <span class="badge px-2 py-1" style="background: var(--kp-yellow); color: var(--kp-dark);">Development</span>
                        @elseif(app()->environment('staging'))
                            <span class="badge px-2 py-1" style="background: #17a2b8; color: white;">Staging</span>
                        @else
                            <span class="badge px-2 py-1" style="background: var(--kp-green); color: white;">Production</span>
                        @endif
                    </div>
                </div>
            </div>

            <hr class="my-3 bg-light opacity-25">

            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="copyright small">
                        <p class="mb-0 text-light opacity-75">
                            &copy; {{ date('Y') }} <strong style="color: var(--kp-yellow);">Kenya Power and Lighting Company</strong>. All rights reserved.
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

    <!-- Scripts -->
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/68SIy3Te4Bkz" crossorigin="anonymous"></script> --}}

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Bootstrap JS MUST be loaded after jQuery -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


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
                        const notificationItem = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                        if (notificationItem) {
                            notificationItem.classList.remove('unread');
                            notificationItem.classList.add('read');
                            const avatar = notificationItem.querySelector('.avatar div');
                            if (avatar) {
                                avatar.classList.remove('bg-kp-blue');
                                avatar.classList.add('bg-secondary');
                            }
                            const newBadge = notificationItem.querySelector('.badge.bg-kp-blue.ms-1');
                            if (newBadge) newBadge.remove();
                            const actionDiv = notificationItem.querySelector('.mt-2.d-flex.gap-2');
                            if (actionDiv) {
                                const markReadBtn = actionDiv.querySelector('a.text-kp-green');
                                if (markReadBtn) {
                                    const readSpan = document.createElement('span');
                                    readSpan.className = 'small text-muted';
                                    readSpan.innerHTML = '<i class="fas fa-check-double"></i> Read';
                                    actionDiv.replaceChild(readSpan, markReadBtn);
                                }
                            }
                        }
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
                        document.querySelectorAll('.notification-item.unread').forEach(item => {
                            item.classList.remove('unread');
                            item.classList.add('read');
                            const avatar = item.querySelector('.avatar div');
                            if (avatar) {
                                avatar.classList.remove('bg-kp-blue');
                                avatar.classList.add('bg-secondary');
                            }
                            const newBadge = item.querySelector('.badge.bg-kp-blue.ms-1');
                            if (newBadge) newBadge.remove();
                            const actionDiv = item.querySelector('.mt-2.d-flex.gap-2');
                            if (actionDiv) {
                                const markReadBtn = actionDiv.querySelector('a.text-kp-green');
                                if (markReadBtn) {
                                    const readSpan = document.createElement('span');
                                    readSpan.className = 'small text-muted';
                                    readSpan.innerHTML = '<i class="fas fa-check-double"></i> Read';
                                    actionDiv.replaceChild(readSpan, markReadBtn);
                                }
                            }
                        });
                        window.updateNotificationBadge();
                        const markAllBtn = document.querySelector('.btn-outline-kp-success');
                        if (markAllBtn) markAllBtn.style.display = 'none';
                        const headerBadge = document.querySelector('.dropdown-header .badge.bg-kp-blue');
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
                            const headerBadge = document.querySelector('.dropdown-header .badge.bg-kp-blue');
                            if (headerBadge) {
                                headerBadge.textContent = data.count + ' new';
                            }
                        } else {
                            badge.style.display = 'none';
                            const headerBadge = document.querySelector('.dropdown-header .badge.bg-kp-blue');
                            if (headerBadge) headerBadge.remove();
                        }
                    }
                })
                .catch(error => console.error('Error updating badge:', error));
            };

            window.updateNotificationBadge();
            setInterval(() => window.updateNotificationBadge(), 30000);
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
