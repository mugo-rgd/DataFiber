<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
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
        --transition-base: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Fixed navbar offset */
    body {
        padding-top: 70px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        overflow-x: hidden;
    }

    /* Navbar styling */
    .navbar {
        background: linear-gradient(135deg, var(--kp-blue) 0%, var(--kp-green) 100%) !important;
        box-shadow: 0 2px 12px rgba(0,0,0,0.12);
        z-index: 1030;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        padding: 0.5rem 1rem;
    }

    .navbar-brand {
        color: white !important;
        font-weight: 600;
        font-size: 1.2rem;
        white-space: nowrap;
    }

    .navbar-brand:hover {
        color: var(--kp-yellow) !important;
        transform: scale(1.02);
        transition: var(--transition-base);
    }

    /* Default: Icon-only mode (text hidden) */
    .navbar-nav .nav-link {
        color: white !important;
        transition: var(--transition-base);
        cursor: pointer;
        padding: 0.6rem 0.9rem !important;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        position: relative;
    }

    .navbar-nav .nav-link .nav-text {
        display: none;
    }

    /* Full menu mode (text visible) */
    body.full-menu-mode .navbar-nav .nav-link {
        justify-content: flex-start;
        gap: 10px;
        padding: 0.6rem 1rem !important;
    }

    body.full-menu-mode .navbar-nav .nav-link .nav-text {
        display: inline;
        font-size: 0.9rem;
    }

    body.full-menu-mode .navbar-nav .nav-link i {
        width: 1.6rem;
        font-size: 1.1rem;
    }

    /* Settings button styling */
    .settings-btn {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        border-radius: 8px;
        padding: 0.4rem 0.7rem;
        margin-left: 0.5rem;
        transition: var(--transition-base);
        cursor: pointer;
    }

    .settings-btn:hover {
        background: rgba(255,255,255,0.25);
        transform: rotate(15deg);
    }

    /* Make icons larger */
    .navbar-nav .nav-link i {
        width: 1.4rem;
        font-size: 1.2rem;
        text-align: center;
        flex-shrink: 0;
        margin: 0;
        transition: var(--transition-base);
    }

    .navbar-nav .nav-link:hover {
        color: var(--kp-yellow) !important;
        background-color: rgba(255,255,255,0.15);
        transform: translateY(-1px);
    }

    .navbar-nav .nav-link.active {
        background-color: rgba(255,255,255,0.2);
        border-radius: 10px;
        color: var(--kp-yellow) !important;
    }

    /* Badge positioning for icons */
    .navbar-nav .nav-link .badge {
        position: absolute;
        top: 0;
        right: 0;
        transform: translate(30%, -30%);
        font-size: 0.65rem;
        padding: 0.2rem 0.4rem;
        min-width: 18px;
    }

    /* In full menu mode, adjust badge position */
    body.full-menu-mode .navbar-nav .nav-link .badge {
        position: static;
        transform: none;
        margin-left: auto;
    }

    /* Dropdown toggle indicator */
    .dropdown-toggle::after {
        display: none;
    }

    .nav-item.dropdown:hover .dropdown-toggle::after {
        display: inline-block;
        margin-left: 0.255em;
        vertical-align: middle;
    }

    body.full-menu-mode .dropdown-toggle::after {
        display: inline-block !important;
    }

    /* Dropdown Styles */
    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1050;
        min-width: 14rem;
        max-width: 90vw;
        padding: 0.6rem 0;
        margin: 0.5rem 0 0;
        font-size: 0.9rem;
        color: #1e293b;
        background-color: #fff;
        background-clip: padding-box;
        border: none;
        border-radius: 14px;
        box-shadow: 0 12px 28px rgba(0,0,0,0.15);
        border-top: 3px solid var(--kp-yellow);
    }

    @media (min-width: 992px) {
        .nav-item.dropdown:hover .dropdown-menu {
            display: block;
            margin-top: 0;
        }
    }

    .dropdown-menu-end {
        right: 0;
        left: auto;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 0.6rem 1.2rem;
        clear: both;
        font-weight: 450;
        color: #1e293b;
        text-align: inherit;
        text-decoration: none;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
        transition: var(--transition-base);
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .dropdown-item {
            white-space: normal;
            word-break: break-word;
        }
        .dropdown-menu {
            max-width: 95vw;
            min-width: 200px;
        }
    }

    .dropdown-item i {
        width: 1.6rem;
        font-size: 1rem;
        text-align: center;
        flex-shrink: 0;
    }

    .dropdown-item:hover,
    .dropdown-item:focus {
        background-color: var(--kp-light-blue);
        color: var(--kp-dark);
        transform: translateX(4px);
    }

    .dropdown-item:active {
        background-color: var(--kp-blue);
        color: white;
    }

    .dropdown-header {
        display: block;
        padding: 0.6rem 1.2rem;
        margin-bottom: 0;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--kp-dark);
        background: linear-gradient(135deg, var(--kp-light-blue), var(--kp-light-green));
        border-radius: 14px 14px 0 0;
    }

    .dropdown-divider {
        height: 0;
        margin: 0.4rem 0;
        overflow: hidden;
        border-top: 1px solid #e9ecef;
    }

    /* Main Content */
    .main-content {
        flex: 1;
        padding: 1rem 0 2rem;
    }

    /* Card styling */
    .card {
        box-shadow: 0 0.15rem 1rem 0 rgba(0, 102, 179, 0.08);
        border: 1px solid #eef2f6;
        border-radius: 16px;
        transition: var(--transition-base);
    }

    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.75rem 1.5rem rgba(0, 102, 179, 0.12);
    }

    .card-header {
        background-color: var(--kp-light-blue);
        border-bottom: 2px solid var(--kp-blue);
        padding: 0.9rem 1.25rem;
        font-weight: 600;
        color: var(--kp-dark);
        border-radius: 16px 16px 0 0;
    }

    /* Buttons */
    .btn-kp-primary {
        background-color: var(--kp-blue);
        border-color: var(--kp-blue);
        transition: var(--transition-base);
        border-radius: 40px;
        padding: 0.5rem 1.2rem;
    }
    .btn-kp-primary:hover {
        background-color: #005499;
        border-color: #005499;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0,102,179,0.2);
    }

    .btn-kp-success {
        background-color: var(--kp-green);
        border-color: var(--kp-green);
        transition: var(--transition-base);
        border-radius: 40px;
    }
    .btn-kp-success:hover {
        background-color: #00802c;
        border-color: #00802c;
        transform: translateY(-1px);
    }

    .btn-outline-kp-primary {
        border-color: var(--kp-blue);
        color: var(--kp-blue);
        transition: var(--transition-base);
        border-radius: 40px;
    }
    .btn-outline-kp-primary:hover {
        background-color: var(--kp-blue);
        border-color: var(--kp-blue);
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
        padding: 0.9rem 0.75rem;
    }

    .table td {
        padding: 0.8rem 0.75rem;
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: var(--kp-light-yellow);
    }

    /* Alerts */
    .alert-kp-success {
        background-color: var(--kp-light-green);
        border-left: 4px solid var(--kp-green);
        color: var(--kp-dark);
        border-radius: 12px;
    }

    .alert-kp-warning {
        background-color: var(--kp-light-yellow);
        border-left: 4px solid var(--kp-yellow);
        color: var(--kp-dark);
        border-radius: 12px;
    }

    /* Footer */
    .footer-compact {
        background: linear-gradient(135deg, var(--kp-dark) 0%, #001a0d 100%) !important;
        border-top: 3px solid var(--kp-yellow);
        font-size: 0.85rem;
    }

    .footer-brand .brand-icon {
        width: 38px;
        height: 38px;
        background: rgba(255, 215, 0, 0.12);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .footer-heading {
        position: relative;
        padding-bottom: 5px;
        font-size: 0.85rem;
    }

    .footer-heading::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 28px;
        height: 2px;
        background: linear-gradient(90deg, var(--kp-yellow), transparent);
        border-radius: 2px;
    }

    .footer-link {
        color: #cbd5e0 !important;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .footer-link:hover {
        color: var(--kp-yellow) !important;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(1.1); }
        100% { opacity: 1; transform: scale(1); }
    }

    .status-dot.bg-kp-green { background-color: var(--kp-green) !important; }

    .back-to-top {
        border-radius: 30px;
        padding: 5px 14px;
        font-weight: 500;
        transition: var(--transition-base);
        border-width: 1px;
        font-size: 0.8rem;
        border-color: rgba(255,255,255,0.25);
        color: rgba(255,255,255,0.9);
    }

    .back-to-top:hover {
        background: rgba(255, 215, 0, 0.2);
        color: var(--kp-yellow);
        border-color: var(--kp-yellow);
    }

    /* Notifications */
    .notification-item.unread {
        background-color: var(--kp-light-blue);
        border-left: 3px solid var(--kp-blue);
    }

    .notification-item.read {
        background-color: #ffffff;
        border-left: 3px solid #dee2e6;
        opacity: 0.85;
    }

    .avatar-sm {
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.85rem;
        background: linear-gradient(135deg, var(--kp-blue), var(--kp-green));
        color: white;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* Tooltip custom styling */
    .tooltip-inner {
        background-color: var(--kp-dark);
        color: var(--kp-yellow);
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
    }
    .bs-tooltip-top .tooltip-arrow::before {
        border-top-color: var(--kp-dark);
    }

    /* ========== RESPONSIVE STYLES ========== */
    @media (max-width: 991.98px) {
        body { padding-top: 60px; }

        .navbar-collapse {
            background: linear-gradient(135deg, var(--kp-blue) 0%, var(--kp-green) 100%);
            padding: 0.8rem;
            border-radius: 0 0 20px 20px;
            max-height: calc(100vh - 60px);
            overflow-y: auto;
            margin-top: 0.5rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        /* On mobile, always show text */
        .navbar-nav .nav-link {
            justify-content: flex-start;
            gap: 10px;
            padding: 0.7rem 1rem !important;
        }

        .navbar-nav .nav-link .nav-text {
            display: inline !important;
            font-size: 0.95rem;
        }

        .navbar-nav .nav-link i {
            width: 1.6rem;
            font-size: 1.2rem;
        }

        .navbar-nav .nav-link .badge {
            position: static;
            transform: none;
            margin-left: auto;
        }

        .nav-item { width: 100%; margin: 2px 0; }

        .dropdown-menu {
            position: static !important;
            width: 100% !important;
            background: rgba(255,255,255,0.12) !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0.25rem 0 0.25rem 1.8rem !important;
            margin-top: 0.25rem !important;
            border-radius: 12px !important;
            max-width: 100%;
        }

        .dropdown-item {
            color: white !important;
            padding: 0.6rem 1rem !important;
            border-radius: 10px !important;
            white-space: normal !important;
            word-break: break-word;
        }

        .dropdown-item i {
            color: var(--kp-yellow) !important;
        }

        .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: var(--kp-yellow) !important;
            transform: translateX(4px);
        }

        .dropdown-divider {
            border-color: rgba(255, 255, 255, 0.15);
        }

        .dropdown-header {
            background: rgba(0,0,0,0.15);
            color: var(--kp-yellow);
        }

        .navbar-toggler {
            padding: 0.4rem 0.6rem;
        }

        .badge {
            font-size: 0.7rem;
        }

        .dropdown-toggle::after {
            display: inline-block !important;
            float: right;
            margin-top: 8px;
        }

        .settings-btn {
            margin: 0 0.5rem;
        }
    }

    @media (max-width: 768px) {
        .footer-compact { text-align: center; }
        .footer-heading::after { left: 50%; transform: translateX(-50%); }
        .footer-brand { justify-content: center; text-align: center; }
        .footer-brand .d-flex { justify-content: center; }
        .system-status { justify-content: center !important; }
        .footer-meta { justify-content: center !important; }

        .table-responsive-custom {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    @media (max-width: 576px) {
        .container-fluid { padding-left: 12px; padding-right: 12px; }
        .navbar-brand { font-size: 0.9rem; }
        .navbar-brand i { font-size: 1rem; }
        .main-content { padding-top: 0.5rem; }
        .card-header { padding: 0.7rem 1rem; font-size: 0.95rem; }
        .dropdown-menu.notifications-menu { width: calc(100vw - 20px) !important; right: -10px; left: auto; min-width: auto; }
    }

    @media print {
        .navbar, footer, .no-print { display: none !important; }
        body { padding-top: 0; }
        .card { border: 1px solid #ddd; box-shadow: none; }
    }

    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: var(--kp-light-blue); border-radius: 10px; }
    ::-webkit-scrollbar-thumb { background: var(--kp-blue); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--kp-green); }

    .dropdown-menu {
        border-radius: 0.9rem;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        margin-top: 0.4rem;
    }

    .dropdown-item.active {
        background: linear-gradient(90deg, rgba(0, 102, 179, 0.1), rgba(0, 150, 57, 0.05));
        color: #0066B3;
        font-weight: 500;
    }

    .text-purple { color: #6f42c1 !important; }
    .bg-kp-primary { background: #0066B3 !important; }

    .notification-badge {
        font-size: 0.65rem;
        padding: 0.2rem 0.45rem;
        position: absolute;
        top: 0;
        right: 0;
        transform: translate(30%, -30%);
    }

    /* Colorful Icons */
    .navbar-nav .nav-link i.fa-tachometer-alt,
    .navbar-nav .nav-link i.fa-chart-line,
    .navbar-nav .nav-link i.fa-chart-pie {
        color: #00d2ff;
    }

    .navbar-nav .nav-link i.fa-tools,
    .navbar-nav .nav-link i.fa-wrench,
    .navbar-nav .nav-link i.fa-toolbox,
    .navbar-nav .nav-link i.fa-clipboard-list,
    .navbar-nav .nav-link i.fa-clipboard-check {
        color: #f39c12;
    }

    .navbar-nav .nav-link i.fa-users,
    .navbar-nav .nav-link i.fa-user-tie,
    .navbar-nav .nav-link i.fa-user-friends,
    .navbar-nav .nav-link i.fa-user-circle {
        color: #1abc9c;
    }

    .navbar-nav .nav-link i.fa-file-alt,
    .navbar-nav .nav-link i.fa-file-invoice,
    .navbar-nav .nav-link i.fa-file-contract,
    .navbar-nav .nav-link i.fa-file-invoice-dollar,
    .navbar-nav .nav-link i.fa-folder {
        color: #FFD700;
    }

    .navbar-nav .nav-link i.fa-drafting-compass,
    .navbar-nav .nav-link i.fa-globe-africa,
    .navbar-nav .nav-link i.fa-map-marked-alt,
    .navbar-nav .nav-link i.fa-route {
        color: #9b59b6;
    }

    .navbar-nav .nav-link i.fa-money-bill-wave,
    .navbar-nav .nav-link i.fa-coins,
    .navbar-nav .nav-link i.fa-chart-bar,
    .navbar-nav .nav-link i.fa-exchange-alt,
    .navbar-nav .nav-link i.fa-cog {
        color: #2ecc71;
    }

    .navbar-nav .nav-link i.fa-ticket-alt {
        color: #fd79a8;
    }

    .navbar-nav .nav-link i.fa-comments {
        color: #3498db;
    }

    .navbar-nav .nav-link i.fa-certificate {
        color: #9b59b6;
    }

    .navbar-nav .nav-link i.fa-bell {
        color: #e67e22;
    }

    .navbar-nav .nav-link i.fa-id-card {
        color: #1abc9c;
    }

    .navbar-nav .nav-link i.fa-microchip {
        color: #3498db;
    }

    .navbar-nav .nav-link i.fa-tasks {
        color: #f39c12;
    }

    /* Hover effect - all icons turn yellow */
    .navbar-nav .nav-link:hover i {
        color: var(--kp-yellow) !important;
        transform: scale(1.1);
    }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar with Settings Toggle Button -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-network-wired me-2"></i><span class="d-none d-sm-inline">Dark Fibre CRM</span><span class="d-inline d-sm-none">DF-CRM</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        @php
                            // Define document counts once at the top
                            $leaseCount = \App\Models\Lease::count();
                            $quotationCount = \App\Models\Quotation::count();
                            $contractCount = \App\Models\Contract::count();
                        @endphp

                        <!-- Dashboard Links -->
                        @if(in_array(Auth::user()->role, ['admin', 'technical_admin', 'system_admin', 'accountmanager_admin']) && Route::has('admin.dashboard'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Admin Dashboard">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span class="nav-text">Dashboard</span>
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->role === 'customer' && Route::has('customer.customer-dashboard'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.customer-dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="My Dashboard">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span class="nav-text">Dashboard</span>
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->role === 'finance' && Route::has('finance.dashboard'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('finance.dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Finance Dashboard">
                                    <i class="fas fa-chart-line"></i>
                                    <span class="nav-text">Finance</span>
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->role === 'designer' && Route::has('designer.dashboard'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('designer.dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Designer Dashboard">
                                    <i class="fas fa-drafting-compass"></i>
                                    <span class="nav-text">Designer</span>
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->role === 'surveyor' && Route::has('surveyor.dashboard'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('surveyor.dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Surveyor Dashboard">
                                    <i class="fas fa-map-marked-alt"></i>
                                    <span class="nav-text">Surveyor</span>
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->role === 'technician' && Route::has('technician.dashboard'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('technician.dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Technician Dashboard">
                                    <i class="fas fa-wrench"></i>
                                    <span class="nav-text">Technician</span>
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->role === 'ict_engineer' && Route::has('ictengineer.dashboard'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('ictengineer.dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="ICT Engineer Dashboard">
                                    <i class="fas fa-microchip"></i>
                                    <span class="nav-text">ICT Engineer</span>
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->role === 'account_manager' && Route::has('account-manager.dashboard'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('account-manager.dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Account Manager Dashboard">
                                    <i class="fas fa-user-friends"></i>
                                    <span class="nav-text">Account Mgr</span>
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->role === 'debt_manager' && Route::has('finance.debt.dashboard'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('finance.debt.dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Debt Manager Dashboard">
                                    <i class="fas fa-chart-line"></i>
                                    <span class="nav-text">Debt Manager</span>
                                </a>
                            </li>
                        @endif

                        <!-- Maintenance Module Dropdown -->
                        @can('view-maintenance')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="maintenanceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Maintenance Management">
                                <i class="fas fa-tools"></i>
                                <span class="nav-text">Maintenance</span>
                            </a>
                            <div class="dropdown-menu">
                                @can('isTechnician')
                                    @if(Route::has('technician.dashboard'))
                                        <a class="dropdown-item" href="{{ route('technician.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> My Dashboard</a>
                                    @endif
                                    @if(Route::has('technician.work-orders.index'))
                                        <a class="dropdown-item" href="{{ route('technician.work-orders.index') }}"><i class="fas fa-clipboard-list me-2"></i> My Work Orders</a>
                                    @endif
                                    @if(Route::has('technician.equipment.index'))
                                        <a class="dropdown-item" href="{{ route('technician.equipment.index') }}"><i class="fas fa-toolbox me-2"></i> Equipment</a>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                @endcan
                                @if(Route::has('maintenance.dashboard'))
                                    <a class="dropdown-item" href="{{ route('maintenance.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Maint Dashboard</a>
                                @endif
                                @can('create-maintenance-request')
                                    @if(Route::has('maintenance.requests.create'))
                                        <a class="dropdown-item" href="{{ route('maintenance.requests.create') }}"><i class="fas fa-plus-circle me-2"></i> New Request</a>
                                    @endif
                                @endcan
                                @if(Route::has('maintenance.requests.index'))
                                    <a class="dropdown-item" href="{{ route('maintenance.requests.index') }}"><i class="fas fa-list me-2"></i> All Requests</a>
                                @endif
                                @can('assign-work-orders')
                                    <div class="dropdown-divider"></div>
                                    @if(Route::has('maintenance.work-orders.index'))
                                        <a class="dropdown-item" href="{{ route('maintenance.work-orders.index') }}"><i class="fas fa-clipboard-check me-2"></i> Work Orders</a>
                                    @endif
                                    @if(Route::has('maintenance.work-orders.create'))
                                        <a class="dropdown-item" href="{{ route('maintenance.work-orders.create') }}"><i class="fas fa-plus-circle me-2"></i> Create WO</a>
                                    @endif
                                @endcan
                                @can('manage-equipment')
                                    <div class="dropdown-divider"></div>
                                    @if(Route::has('maintenance.equipment.index'))
                                        <a class="dropdown-item" href="{{ route('maintenance.equipment.index') }}"><i class="fas fa-toolbox me-2"></i> Equipment Mgt</a>
                                    @endif
                                @endcan
                                @can('view-maintenance-reports')
                                    <div class="dropdown-divider"></div>
                                    @if(Route::has('maintenance.reports'))
                                        <a class="dropdown-item" href="{{ route('maintenance.reports') }}"><i class="fas fa-chart-bar me-2"></i> Reports</a>
                                    @endif
                                @endcan
                            </div>
                        </li>
                        @endcan

                        <!-- Admin Menu Items -->
                        @if(in_array(Auth::user()->role, ['admin', 'technical_admin', 'system_admin']))
                            @if(in_array(Auth::user()->role, ['admin', 'system_admin']) && Route::has('admin.users'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.users') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="User Management">
                                        <i class="fas fa-users"></i>
                                        <span class="nav-text">Users</span>
                                    </a>
                                </li>
                            @endif
 @if(in_array(Auth::user()->role, [ 'accountmanager_admin']) && Route::has('admin.customers.assign'))
                            {{-- @if(Route::has('admin.customers.assign')) --}}
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.customers.assign') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Assign Customers to Managers">
                                        <i class="fas fa-user-tie"></i>
                                        <span class="nav-text">Assign Cust</span>
                                    </a>
                                </li>
                            @endif

                            @if(Route::has('admin.design-requests.index'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.design-requests.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Design Requests & Tickets">
                                        <i class="fas fa-drafting-compass"></i>
                                        <span class="nav-text">Design Req</span>
                                    </a>
                                </li>
                            @endif

                            <!-- Commercial Documents Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="commercialDocsAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Commercial Documents">
                                    <i class="fas fa-file-alt"></i>
                                    <span class="nav-text">Com Docs</span>
                                    <span class="badge bg-warning text-dark rounded-pill">{{ $leaseCount + $quotationCount + $contractCount }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    @if(Route::has('admin.leases.index'))
                                        <li><a class="dropdown-item" href="{{ route('admin.leases.index') }}"><i class="fas fa-network-wired me-2"></i> Leases <span class="badge bg-secondary ms-2">{{ $leaseCount }}</span></a></li>
                                    @endif
                                    @if(Route::has('admin.quotations.index'))
                                        <li><a class="dropdown-item" href="{{ route('admin.quotations.index') }}"><i class="fas fa-file-invoice-dollar me-2"></i> Quotations <span class="badge bg-secondary ms-2">{{ $quotationCount }}</span></a></li>
                                    @endif
                                    @if(Route::has('contracts.index'))
                                        <li><a class="dropdown-item" href="{{ route('contracts.index') }}"><i class="fas fa-file-contract me-2"></i> Contracts <span class="badge bg-secondary ms-2">{{ $contractCount }}</span></a></li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    @if(Route::has('cak.dashboard'))
                                        <li><a class="dropdown-item" href="{{ route('cak.dashboard') }}"><i class="fas fa-tachometer-alt me-2 text-warning"></i> CAK Forms</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        <!-- Customer Menu Items -->
                        @if(Auth::user()->role === 'customer')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.profile.show') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="My Profile">
                                    <i class="fas fa-id-card"></i>
                                    <span class="nav-text">Profile</span>
                                </a>
                            </li>

                            <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="customerDocDropdown" role="button"
       data-bs-toggle="dropdown" aria-expanded="false"
       title="Documents">
        <i class="fas fa-file-alt"></i>
        <span class="nav-text">Documents</span>
    </a>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route('customer.leases.index') }}" title="My Leases">
                <i class="fas fa-network-wired me-2"></i>
                <span class="nav-text">Leases</span>
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('customer.contracts.index') }}" title="My Contracts">
                <i class="fas fa-file-contract me-2"></i>
                <span class="nav-text">Contracts</span>
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('customer.design-requests.index') }}" title="Design Requests">
                <i class="fas fa-drafting-compass me-2"></i>
                <span class="nav-text">Design</span>
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('customer.billings.index') }}" title="My Invoices">
                <i class="fas fa-file-invoice me-2"></i>
                <span class="nav-text">Invoices</span>
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('customer.documents.index') }}" title="My Documents">
                <i class="fas fa-folder-open me-2"></i>
                <span class="nav-text">Other Documents</span>
            </a>
        </li>
    </ul>
</li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.tickets') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Support Tickets">
                                    <i class="fas fa-ticket-alt"></i>
                                    <span class="nav-text">Support</span>
                                </a>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="customerCertificatesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Certificates">
                                    <i class="fas fa-certificate"></i>
                                    <span class="nav-text">Certs</span>
                                </a>
                                <ul class="dropdown-menu">
                                    @if(Route::has('customer.certificates.conditional.index'))
                                        <li><a class="dropdown-item" href="{{ route('customer.certificates.conditional.index') }}"><i class="fas fa-file-contract me-2"></i> Conditional Certificates</a></li>
                                    @endif
                                    @if(Route::has('customer.certificates.acceptance.index'))
                                        <li><a class="dropdown-item" href="{{ route('customer.certificates.acceptance.index') }}"><i class="fas fa-check-circle me-2"></i> Acceptance Certificates</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        <!-- Finance Menu Items -->
                        @if(Auth::user()->role === 'finance')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="financeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Finance Operations">
                                    <i class="fas fa-chart-line"></i>
                                    <span class="nav-text">Finance</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 260px;">
                                    <li class="dropdown-header"><i class="fas fa-chart-line me-2"></i> Finance Dashboard</li>
                                    <li><hr class="dropdown-divider"></li>
                                    @if(Route::has('leases.finance.index'))
                                        <li><a class="dropdown-item" href="{{ route('leases.finance.index') }}"><i class="fas fa-file-contract me-2"></i> Leases Mgt</a></li>
                                    @endif
                                    @if(Route::has('finance.billing.index'))
                                        <li><a class="dropdown-item" href="{{ route('finance.billing.index') }}"><i class="fas fa-file-invoice-dollar me-2"></i> Billings</a></li>
                                    @endif
                                    @if(Route::has('finance.debt.overdue-invoices'))
                                        <li><a class="dropdown-item" href="{{ route('finance.debt.overdue-invoices') }}"><i class="fas fa-money-check me-2"></i> Installment Plans</a></li>
                                    @endif
                                    @if(Route::has('finance.payments.index'))
                                        <li><a class="dropdown-item" href="{{ route('finance.payments.index') }}"><i class="fas fa-money-bill-wave me-2"></i> Payment Followups</a></li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    @if(Route::has('finance.transactions.index'))
                                        <li><a class="dropdown-item" href="{{ route('finance.transactions.index') }}"><i class="fas fa-exchange-alt me-2"></i> Transactions</a></li>
                                    @endif
                                    @if(Route::has('finance.reports'))
                                        <li><a class="dropdown-item" href="{{ route('finance.reports') }}"><i class="fas fa-chart-bar me-2"></i> Reports</a></li>
                                    @endif
                                    @if(Route::has('finance.financial-parameters.index'))
                                        <li><a class="dropdown-item" href="{{ route('finance.financial-parameters.index') }}"><i class="fas fa-cog me-2"></i> Parameters</a></li>
                                    @endif
                                </ul>
                            </li>

                            @if(Route::has('finance.payments.index'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('finance.payments.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Payment Management">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <span class="nav-text">Payments</span>
                                    </a>
                                </li>
                            @endif

                            <!-- Commercial Documents Dropdown (Finance) -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="commercialDocsFinance" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Commercial Documents">
                                    <i class="fas fa-file-alt"></i>
                                    <span class="nav-text">Com Docs</span>
                                    <span class="badge bg-warning text-dark rounded-pill">{{ $leaseCount + $quotationCount + $contractCount }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    @if(Route::has('admin.leases.index'))
                                        <li><a class="dropdown-item" href="{{ route('admin.leases.index') }}"><i class="fas fa-network-wired me-2"></i> Leases</a></li>
                                    @endif
                                    @if(Route::has('admin.quotations.index'))
                                        <li><a class="dropdown-item" href="{{ route('admin.quotations.index') }}"><i class="fas fa-file-invoice-dollar me-2"></i> Quotations</a></li>
                                    @endif
                                    @if(Route::has('contracts.index'))
                                        <li><a class="dropdown-item" href="{{ route('contracts.index') }}"><i class="fas fa-file-contract me-2"></i> Contracts</a></li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    @if(Route::has('cak.dashboard'))
                                        <li><a class="dropdown-item" href="{{ route('cak.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> CAK Forms</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        <!-- Designer Menu Items -->
                        @if(Auth::user()->role === 'designer')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('designer.requests.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Design Requests">
                                    <i class="fas fa-drafting-compass"></i>
                                    <span class="nav-text">Design Req</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('designer.quotations.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Quotations">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <span class="nav-text">Quotes</span>
                                </a>
                            </li>
                        @endif

                        <!-- Surveyor Menu Items -->
                        @if(Auth::user()->role === 'surveyor')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('surveyor.assignments.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="My Assignments">
                                    <i class="fas fa-tasks"></i>
                                    <span class="nav-text">Assignments</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('surveyor.routes.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Survey Routes">
                                    <i class="fas fa-route"></i>
                                    <span class="nav-text">Routes</span>
                                </a>
                            </li>
                        @endif

                        <!-- Technician Menu Items -->
                        @can('isTechnician')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('technician.work-orders.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="My Work Orders">
                                    <i class="fas fa-clipboard-list"></i>
                                    <span class="nav-text">Work Orders</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('technician.equipment.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Equipment Management">
                                    <i class="fas fa-toolbox"></i>
                                    <span class="nav-text">Equipment</span>
                                </a>
                            </li>
                        @endcan

                        <!-- Account Manager Menu Items -->
                        @if(Auth::user()->role === 'account_manager')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('account-manager.customers.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="My Customers">
                                    <i class="fas fa-users"></i>
                                    <span class="nav-text">Customers</span>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="legalDocsAccountManager" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Commercial Documents">
                                    <i class="fas fa-file-alt"></i>
                                    <span class="nav-text">Com Docs</span>
                                    <span class="badge bg-warning text-dark rounded-pill">{{ $leaseCount + $quotationCount + $contractCount }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    @if(Route::has('admin.leases.index'))
                                        <li><a class="dropdown-item" href="{{ route('admin.leases.index') }}"><i class="fas fa-network-wired me-2"></i> Leases</a></li>
                                    @endif
                                    @if(Route::has('admin.quotations.index'))
                                        <li><a class="dropdown-item" href="{{ route('admin.quotations.index') }}"><i class="fas fa-file-invoice-dollar me-2"></i> Quotations</a></li>
                                    @endif
                                    @if(Route::has('contracts.index'))
                                        <li><a class="dropdown-item" href="{{ route('contracts.index') }}"><i class="fas fa-file-contract me-2"></i> Contracts</a></li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    @if(Route::has('cak.dashboard'))
                                        <li><a class="dropdown-item" href="{{ route('cak.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> CAK Forms</a></li>
                                    @endif
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('account-manager.tickets.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Support Tickets">
                                    <i class="fas fa-ticket-alt"></i>
                                    <span class="nav-text">Tickets</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('account-manager.payments.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Payment Followups">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span class="nav-text">Payments</span>
                                </a>
                            </li>
                        @endif

                        <!-- Debt Manager Menu -->
                        @if(Auth::user()->role === 'debt_manager')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('finance.debt.dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Debt Dashboard">
                                    <i class="fas fa-chart-line"></i>
                                    <span class="nav-text">Debt Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('finance.debt.customers') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Customer Management">
                                    <i class="fas fa-users"></i>
                                    <span class="nav-text">Customers</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('finance.payments.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Payment Tracking">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span class="nav-text">Payments</span>
                                </a>
                            </li>
                        @endif

                        <!-- Marketing Admin Menu Items -->
                        @if(Auth::user()->role === 'accountmanager_admin')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="marketingAnalyticsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Marketing Analytics">
                                    <i class="fas fa-chart-pie"></i>
                                    <span class="nav-text">Marketing</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    @if(Route::has('marketing-admin.dashboard'))
                                        <li><a class="dropdown-item" href="{{ route('marketing-admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
                                    @endif
                                    @if(Route::has('marketing-admin.analytics'))
                                        <li><a class="dropdown-item" href="{{ route('marketing-admin.analytics') }}"><i class="fas fa-chart-bar me-2"></i> Performance</a></li>
                                    @endif
                                    @if(Route::has('marketing-admin.campaigns'))
                                        <li><a class="dropdown-item" href="{{ route('marketing-admin.campaigns') }}"><i class="fas fa-bullhorn me-2"></i> Campaigns</a></li>
                                    @endif
                                    @if(Route::has('marketing-admin.reports'))
                                        <li><a class="dropdown-item" href="{{ route('marketing-admin.reports') }}"><i class="fas fa-file-alt me-2"></i> Reports</a></li>
                                    @endif
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="teamManagementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Team Management">
                                    <i class="fas fa-user-tie"></i>
                                    <span class="nav-text">Team</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li><a class="dropdown-item" href="{{ route('admin.account-managers.index') ?? '#' }}"><i class="fas fa-user-tie me-2"></i> Account Managers</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.customers.index') ?? '#' }}"><i class="fas fa-exchange-alt me-2"></i> Assign Managers</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.performance') ?? '#' }}"><i class="fas fa-tachometer-alt me-2"></i> Performance</a></li>
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.sales-pipeline') ?? '#' }}"><i class="fas fa-funnel-dollar me-2"></i> Sales Pipeline</a></li>
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.targets') ?? '#' }}"><i class="fas fa-crosshairs me-2"></i> Sales Targets</a></li>
                                    <li><a class="dropdown-item" href="{{ route('marketing-admin.commissions') ?? '#' }}"><i class="fas fa-coins me-2"></i> Commissions</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="legalDocsMarketingAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Commercial Documents">
                                    <i class="fas fa-file-alt"></i>
                                    <span class="nav-text">Com Docs</span>
                                    <span class="badge bg-warning text-dark rounded-pill">{{ $leaseCount + $quotationCount + $contractCount }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    @if(Route::has('admin.leases.index'))
                                        <li><a class="dropdown-item" href="{{ route('admin.leases.index') }}"><i class="fas fa-network-wired me-2"></i> Leases</a></li>
                                    @endif
                                    @if(Route::has('admin.quotations.index'))
                                        <li><a class="dropdown-item" href="{{ route('admin.quotations.index') }}"><i class="fas fa-file-invoice-dollar me-2"></i> Quotations</a></li>
                                    @endif
                                    @if(Route::has('contracts.index'))
                                        <li><a class="dropdown-item" href="{{ route('contracts.index') }}"><i class="fas fa-file-contract me-2"></i> Contracts</a></li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    @if(Route::has('cak.dashboard'))
                                        <li><a class="dropdown-item" href="{{ route('cak.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> CAK Forms</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        <!-- Kenya Fibre Dashboard (GIS Map) -->
                        @if(!in_array(Auth::user()->role, ['customer']) && Route::has('kenya.fibre.dashboard'))
                            <li class="nav-item">
                                <a class="nav-link kenya-fibre-link" href="{{ route('kenya.fibre.dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="GIS Map - Fibre Infrastructure">
                                    <i class="fas fa-globe-africa"></i>
                                    <span class="nav-text">Map/GIS</span>
                                </a>
                            </li>
                            @if(in_array(Auth::user()->role, ['admin','technical_admin','accountmanager_admin', 'system_admin', 'executive', 'finance', 'management']))
                                <li class="nav-item">
                                    <a href="{{ route('executive.dashboard') }}" class="nav-link {{ request()->routeIs('executive.dashboard') ? 'active' : '' }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Executive Dashboard">
                                        <i class="fas fa-chart-line"></i>
                                        <span class="nav-text">Exec Dash</span>
                                    </a>
                                </li>
                            @endif
                        @endif

                        <!-- Statements -->
                        @if((Auth::user()->role === 'customer' && Route::has('customer.statements')) || Route::has('statements.index'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ Auth::user()->role === 'customer' ? route('customer.statements') : route('statements.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ Auth::user()->role === 'customer' ? 'My Account Statements' : 'Generate Statements' }}">
                                    <i class="fas fa-file-invoice"></i>
                                    <span class="nav-text">{{ Auth::user()->role === 'customer' ? 'Statements' : 'Gen Stmts' }}</span>
                                </a>
                            </li>
                        @endif

                        @if(in_array(Auth::user()->role, ['executive', 'management', 'admin', 'system_admin']))
                            <li class="nav-item">
                                <a href="{{ route('executive.role.dashboard') }}" class="nav-link {{ request()->routeIs('executive.role.dashboard') ? 'active' : '' }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Executive Role Dashboard">
                                    <i class="fas fa-user-tie"></i>
                                    <span class="nav-text">Exec Role</span>
                                </a>
                            </li>
                        @endif

                        <!-- Chat Link -->
                        @can('use-chat')
                            @if(Route::has('chat.index'))
                                <li class="nav-item">
                                    <a class="nav-link position-relative" href="{{ route('chat.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Team Chat">
                                        <i class="fas fa-comments"></i>
                                        <span class="nav-text">Chat</span>
                                        @php $unreadCount = auth()->user()->totalUnreadMessages(); @endphp
                                        @if($unreadCount > 0)
                                            <span class="badge bg-danger">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        @endcan

                        <!-- Notifications Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Notifications">
                                <i class="fas fa-bell"></i>
                                <span class="nav-text">Notif</span>
                                @php
                                    $unreadNotificationsCount = auth()->user()->unreadNotifications->count();
                                @endphp
                                @if($unreadNotificationsCount > 0)
                                    <span class="badge bg-danger notification-badge">{{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end notifications-menu" style="width: 350px; max-height: 460px; overflow-y: auto;">
                                <li class="dropdown-header bg-light d-flex justify-content-between align-items-center">
                                    <span>Notifications</span>
                                    @if($unreadNotificationsCount > 0)
                                        <a href="#" class="text-muted small" id="markAllNotificationsRead">Mark all read</a>
                                    @endif
                                </li>
                                <div id="notificationsList">
                                    @forelse(auth()->user()->notifications()->latest()->take(10)->get() as $notification)
                                        @php
                                            $data = $notification->data;
                                            $isUnread = is_null($notification->read_at);
                                            $isCertificateNotification = isset($data['type']) && $data['type'] === 'conditional_certificate';
                                        @endphp
                                        <li class="dropdown-item-text notification-item {{ $isUnread ? 'unread' : 'read' }}" data-id="{{ $notification->id }}">
                                            <div class="d-flex align-items-start gap-2">
                                                <div class="avatar-sm"><i class="fas {{ $isCertificateNotification ? 'fa-file-contract' : 'fa-bell' }}"></i></div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between">
                                                        <small class="fw-bold {{ $isUnread ? 'text-dark' : 'text-secondary' }}">{{ $data['sender_name'] ?? ($isCertificateNotification ? 'ICT Engineer' : 'System') }}</small>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                                    </div>
                                                    <small class="d-block {{ $isUnread ? 'text-dark' : 'text-muted' }}">
                                                        @if($isCertificateNotification)
                                                            <strong>Conditional Certificate Issued</strong><br>{{ $data['message_preview'] ?? $data['message'] }}
                                                        @else
                                                            {{ $data['message_preview'] ?? 'New notification' }}
                                                        @endif
                                                    </small>
                                                    <div class="mt-2 d-flex gap-2">
                                                        @if($isCertificateNotification && isset($data['action_url']))
                                                            <a href="{{ $data['action_url'] }}" class="small text-kp-blue text-decoration-none"><i class="fas fa-eye"></i> View</a>
                                                        @endif
                                                        @if($isUnread)
                                                            <a href="#" onclick="event.preventDefault(); markSingleNotificationRead('{{ $notification->id }}')" class="small text-kp-green text-decoration-none"><i class="fas fa-check-circle"></i> Mark Read</a>
                                                        @else
                                                            <span class="small text-muted"><i class="fas fa-check-double"></i> Read</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        @if(!$loop->last)
                                            <li class="dropdown-divider" style="margin: 0;"></li>
                                        @endif
                                    @empty
                                        <li class="text-center text-muted py-4"><i class="fas fa-bell-slash fa-2x mb-2"></i><p class="mb-0">No notifications</p></li>
                                    @endforelse
                                </div>
                                @if(auth()->user()->notifications()->count() > 0)
                                    <li><hr class="dropdown-divider"></li>
                                    <li class="text-center p-2">
                                        @if(Route::has('designer.notifications'))
                                            <a href="{{ route('designer.notifications') }}" class="btn btn-sm btn-outline-kp-primary w-100"><i class="fas fa-list"></i> View All</a>
                                        @endif
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endauth
                </ul>

                <!-- Right-side User Menu and Settings Button -->
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <button class="settings-btn" id="layoutToggleBtn" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Toggle between Icon-only and Full Menu mode">
                                <i class="fas fa-sliders-h"></i>
                            </button>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenuDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ Auth::user()->name }} ({{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }})">
                                <i class="fas fa-user-circle fa-lg"></i>
                                <span class="nav-text d-none">Profile</span>
                                <span class="badge bg-{{ Auth::user()->role === 'admin' ? 'danger' : (Auth::user()->role === 'technical_admin' ? 'warning' : (Auth::user()->role === 'system_admin' ? 'primary' : (Auth::user()->role === 'accountmanager_admin' ? 'info' : (Auth::user()->role === 'technician' ? 'warning' : (Auth::user()->role === 'debt_manager' ? 'info' : (Auth::user()->role === 'account_manager' ? 'info' : 'secondary')))))) }} ms-1 d-none">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuDropdown">
                                <li class="dropdown-header"><small>Logged in as</small><br><strong class="text-break">{{ Auth::user()->email }}</strong></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Login">
                                <i class="fas fa-sign-in-alt"></i>
                                <span class="nav-text">Login</span>
                            </a>
                        </li>
                        @if(Route::has('register.customer'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register.customer') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Register New Account">
                                    <i class="fas fa-user-plus"></i>
                                    <span class="nav-text">Register</span>
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
        <div class="container-fluid px-3 px-md-4">
            @if(session('success'))
                <div class="alert alert-kp-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-kp-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                        <div class="brand-icon me-2"><i class="fas fa-network-wired fa-lg" style="color: var(--kp-yellow);"></i></div>
                        <div><h5 class="mb-0 fw-bold" style="color: var(--kp-yellow);">Dark Fibre CRM</h5><p class="mb-0 text-light opacity-75 small">Kenya Power Fibre Infrastructure</p></div>
                    </div>
                </div>
                <div class="col-lg-5 mb-2 mb-lg-0">
                    <div class="row g-2">
                        <div class="col-6 col-sm-3">
                            <h6 class="footer-heading mb-1 small fw-bold" style="color: var(--kp-yellow);">Quick Links</h6>
                            <ul class="list-unstyled footer-links mb-0">
                                <li class="mb-1"><a href="{{ url('/') }}" class="footer-link small">Home</a></li>
                                @if(Route::has('help.index'))
                                    <li class="mb-1"><a href="{{ route('help.index') }}" class="footer-link small">Help</a></li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-6 col-sm-3">
                            <h6 class="footer-heading mb-1 small fw-bold" style="color: var(--kp-yellow);">Legal</h6>
                            <ul class="list-unstyled footer-links mb-0">
                                <li class="mb-1"><a href="#" class="footer-link small">Privacy</a></li>
                                <li class="mb-1"><a href="#" class="footer-link small">Terms</a></li>
                            </ul>
                        </div>
                        <div class="col-12 col-sm-6">
                            <h6 class="footer-heading mb-1 small fw-bold" style="color: var(--kp-yellow);">Contact</h6>
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-1 d-flex align-items-start"><i class="fas fa-map-marker-alt fa-xs me-1 mt-1" style="color: var(--kp-yellow);"></i><span class="text-light opacity-75">Nairobi, KE</span></li>
                                <li class="mb-1 d-flex align-items-start"><i class="fas fa-envelope fa-xs me-1 mt-1" style="color: var(--kp-yellow);"></i><span class="text-light opacity-75">Fibre@kplc.co.ke</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="system-status d-flex align-items-center justify-content-lg-end mb-2">
                        <div class="status-indicator me-2"><div class="status-dot bg-kp-green"></div></div>
                        <span class="text-kp-green fw-bold small">Operational</span>
                    </div>
                    <div class="footer-meta d-flex flex-wrap justify-content-lg-end gap-1 small">
                        <span class="badge px-2 py-1" style="background: linear-gradient(135deg, var(--kp-blue), var(--kp-green)); color: white;">v{{ config('app.version', '1.0.0') }}</span>
                        @if(app()->environment('local'))
                            <span class="badge px-2 py-1" style="background: var(--kp-yellow); color: var(--kp-dark);">Dev</span>
                        @elseif(app()->environment('staging'))
                            <span class="badge px-2 py-1" style="background: #17a2b8; color: white;">Stage</span>
                        @else
                            <span class="badge px-2 py-1" style="background: var(--kp-green); color: white;">Prod</span>
                        @endif
                    </div>
                </div>
            </div>
            <hr class="my-3 bg-light opacity-25">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="copyright small">
                        <p class="mb-0 text-light opacity-75">&copy; {{ date('Y') }} <strong style="color: var(--kp-yellow);">Kenya Power</strong>. All rights reserved.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end align-items-center">
                        <button class="btn btn-outline-light btn-sm back-to-top" id="backToTop"><i class="fas fa-arrow-up"></i><span class="d-none d-sm-inline ms-1">Top</span></button>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    (function() {
        // Layout Toggle Functionality
        const LAYOUT_KEY = 'navbar_layout_mode';

        function setLayoutMode(mode) {
            if (mode === 'full') {
                document.body.classList.add('full-menu-mode');
                localStorage.setItem(LAYOUT_KEY, 'full');
            } else {
                document.body.classList.remove('full-menu-mode');
                localStorage.setItem(LAYOUT_KEY, 'icon');
            }
        }

        function getLayoutMode() {
            const saved = localStorage.getItem(LAYOUT_KEY);
            if (saved === 'full') return 'full';
            return 'icon';
        }

        // Initialize layout from localStorage
        setLayoutMode(getLayoutMode());

        // Toggle button click handler
        const toggleBtn = document.getElementById('layoutToggleBtn');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                const currentMode = getLayoutMode();
                const newMode = currentMode === 'icon' ? 'full' : 'icon';
                setLayoutMode(newMode);

                // Update tooltip
                const newTitle = newMode === 'icon' ? 'Switch to Full Menu mode' : 'Switch to Icon-only mode';
                toggleBtn.setAttribute('title', newTitle);

                // Refresh tooltip
                const tooltip = bootstrap.Tooltip.getInstance(toggleBtn);
                if (tooltip) {
                    tooltip.dispose();
                }
                new bootstrap.Tooltip(toggleBtn, { delay: { show: 300, hide: 100 } });
            });
        }

        // Initialize all tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                delay: { show: 300, hide: 100 }
            })
        });

        const backToTop = document.getElementById('backToTop');
        if (backToTop) backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

        document.querySelectorAll('.dropdown-toggle').forEach(dropdown => { try { new bootstrap.Dropdown(dropdown); } catch(e) {} });

        document.querySelectorAll('.alert').forEach(alert => { setTimeout(() => { try { bootstrap.Alert.getOrCreateInstance(alert).close(); } catch(e) {} }, 5000); });

        document.addEventListener('click', e => { if (!e.target.closest('.dropdown')) document.querySelectorAll('.dropdown-menu.show').forEach(menu => menu.classList.remove('show')); });

        window.markSingleNotificationRead = function(notificationId) {
            if (!notificationId) return;
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Content-Type': 'application/json', 'Accept': 'application/json' }
            }).then(res => res.json()).then(data => { if (data.success) location.reload(); }).catch(console.error);
        };

        window.markAllNotificationsAsRead = function() {
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Content-Type': 'application/json' }
            }).then(res => res.json()).then(data => { if (data.success) location.reload(); });
        };

        window.markAsRead = window.markSingleNotificationRead;
        window.markAllAsRead = window.markAllNotificationsAsRead;

        window.updateNotificationBadge = function() {
            fetch('/notifications/unread-count', { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Accept': 'application/json' } })
                .then(res => res.json()).then(data => {
                    const badge = document.querySelector('.notification-badge');
                    if (badge) { if (data.count > 0) { badge.textContent = data.count > 99 ? '99+' : data.count; badge.style.display = 'inline'; } else { badge.style.display = 'none'; } }
                    const markAllBtn = document.getElementById('markAllNotificationsRead');
                    if (markAllBtn) markAllBtn.textContent = data.count > 0 ? `Mark all read (${data.count})` : 'Mark all read';
                }).catch(console.error);
        };

        if (document.querySelector('.notification-badge')) { window.updateNotificationBadge(); setInterval(() => window.updateNotificationBadge(), 30000); }

        const markAllBtn = document.getElementById('markAllNotificationsRead');
        if (markAllBtn) markAllBtn.addEventListener('click', e => { e.preventDefault(); window.markAllNotificationsAsRead(); });

        document.addEventListener('click', e => { const markReadBtn = e.target.closest('.mark-notification-read'); if (markReadBtn) { e.preventDefault(); const id = markReadBtn.dataset.id; if (id) window.markSingleNotificationRead(id); } });
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
