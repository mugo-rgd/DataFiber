<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DarkFibre CRM - Customer Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* ========================================
           DARK FIBRE CRM - CUSTOMER PORTAL STYLES
        ========================================= */
        :root {
            --kp-blue: #0066B3;
            --kp-green: #009639;
            --kp-yellow: #FFD700;
            --kp-dark: #003f20;
            --kp-light-blue: #e8f4fd;
            --kp-light-green: #e6f7ec;
            --kp-light-yellow: #fff8e1;
            --sidebar-bg: #1a1f2e;
            --sidebar-hover: #2d3348;
            --transition-base: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            overflow-x: hidden;
            background: #f4f6f9;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* ========== NAVBAR STYLES ========== */
        .navbar {
            background: linear-gradient(135deg, var(--kp-blue) 0%, var(--kp-green) 100%) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            padding: 0.75rem 1rem;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
            color: white !important;
            transition: var(--transition-base);
        }

        .navbar-brand:hover {
            color: var(--kp-yellow) !important;
            transform: scale(1.02);
        }

        .navbar .dropdown-toggle {
            color: white !important;
            font-weight: 500;
        }

        .navbar .dropdown-toggle:hover {
            color: var(--kp-yellow) !important;
        }

        .dropdown-menu {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            margin-top: 0.5rem;
        }

        .dropdown-item {
            padding: 0.6rem 1rem;
            transition: var(--transition-base);
        }

        .dropdown-item:hover {
            background-color: var(--kp-light-blue);
            transform: translateX(5px);
        }

        .dropdown-item.text-danger:hover {
            background-color: #fee2e2;
            color: #dc2626 !important;
        }

        /* ========== SIDEBAR STYLES ========== */
        .sidebar {
            min-height: calc(100vh - 70px);
            background: linear-gradient(180deg, #1a1f2e 0%, #131722 100%);
            transition: var(--transition-base);
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08);
            position: relative;
            z-index: 1020;
        }

        .sidebar .nav {
            padding: 1rem 0;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 0.75rem 1.25rem;
            transition: var(--transition-base);
            border-left: 3px solid transparent;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.08);
            color: white;
            border-left-color: var(--kp-yellow);
            transform: translateX(3px);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(90deg, rgba(0, 102, 179, 0.2), transparent);
            color: var(--kp-yellow);
            border-left-color: var(--kp-yellow);
            font-weight: 500;
        }

        .sidebar .nav-link i {
            width: 1.8rem;
            font-size: 1.1rem;
            text-align: center;
            flex-shrink: 0;
        }

        /* Sidebar Dropdown */
        .sidebar .dropdown-toggle {
            width: 100%;
            text-align: left;
            position: relative;
        }

        .sidebar .dropdown-toggle::after {
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            transition: transform 0.3s;
        }

        .sidebar .dropdown-toggle[aria-expanded="true"]::after {
            transform: translateY(-50%) rotate(180deg);
        }

        .sidebar .dropdown-menu {
            background-color: #0f111a;
            border: none;
            border-radius: 0;
            padding: 0;
            margin: 0;
            box-shadow: none;
            position: relative !important;
            transform: none !important;
            width: 100%;
        }

        .sidebar .dropdown-item {
            color: rgba(255, 255, 255, 0.7);
            padding: 0.6rem 1rem 0.6rem 3rem;
            font-size: 0.9rem;
            background-color: transparent;
        }

        .sidebar .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.08);
            color: var(--kp-yellow);
            transform: translateX(3px);
        }

        .sidebar .dropdown-item i {
            width: 1.5rem;
        }

        .sidebar .dropdown-item.active {
            background-color: rgba(0, 102, 179, 0.3);
            color: var(--kp-yellow);
        }

        /* ========== MOBILE RESPONSIVE ========== */
        .sidebar-toggle {
            display: none;
            background: transparent;
            border: none;
            color: white;
            font-size: 1.3rem;
            padding: 0.25rem 0.5rem;
            transition: var(--transition-base);
        }

        .sidebar-toggle:hover {
            color: var(--kp-yellow);
            transform: scale(1.05);
        }

        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                top: 60px;
                left: -100%;
                width: 280px;
                height: calc(100vh - 60px);
                z-index: 1050;
                overflow-y: auto;
                transition: left 0.3s ease;
                border-radius: 0 12px 12px 0;
            }

            .sidebar.show {
                left: 0;
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
            }

            .sidebar-backdrop {
                position: fixed;
                top: 60px;
                left: 0;
                width: 100%;
                height: calc(100% - 60px);
                background-color: rgba(0, 0, 0, 0.6);
                z-index: 1040;
                display: none;
                backdrop-filter: blur(2px);
            }

            .sidebar-backdrop.show {
                display: block;
            }

            .sidebar-toggle {
                display: block;
            }

            main {
                padding: 1rem !important;
            }
        }

        @media (min-width: 768px) {
            .sidebar-toggle {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1rem;
            }

            .navbar-brand i {
                font-size: 1rem;
            }
        }

        /* ========== MAIN CONTENT ========== */
        main {
            min-height: calc(100vh - 70px);
            background: #f4f6f9;
        }

        /* ========== ALERTS ========== */
        .alert-custom-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-left: 4px solid var(--kp-green);
            border-radius: 12px;
            color: #155724;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .alert-custom-success i {
            color: var(--kp-green);
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.25rem;
        }

        /* ========== CARD STYLES ========== */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: var(--transition-base);
            margin-bottom: 1.5rem;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: white;
            border-bottom: 2px solid var(--kp-light-blue);
            padding: 1rem 1.25rem;
            font-weight: 600;
            border-radius: 16px 16px 0 0;
        }

        /* ========== TABLE STYLES ========== */
        .table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }

        .table th {
            background-color: var(--kp-light-blue);
            color: var(--kp-dark);
            font-weight: 600;
            border-bottom: 2px solid var(--kp-blue);
            padding: 1rem;
        }

        .table td {
            padding: 0.875rem 1rem;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: var(--kp-light-yellow);
        }

        /* ========== BUTTONS ========== */
        .btn-primary {
            background: linear-gradient(135deg, var(--kp-blue), var(--kp-green));
            border: none;
            border-radius: 40px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: var(--transition-base);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 102, 179, 0.3);
        }

        .btn-outline-primary {
            border: 2px solid var(--kp-blue);
            color: var(--kp-blue);
            border-radius: 40px;
            transition: var(--transition-base);
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, var(--kp-blue), var(--kp-green));
            border-color: transparent;
            transform: translateY(-2px);
        }

        /* ========== UTILITIES ========== */
        .text-kp-blue { color: var(--kp-blue) !important; }
        .text-kp-green { color: var(--kp-green) !important; }
        .text-kp-yellow { color: var(--kp-yellow) !important; }
        .bg-kp-light-blue { background-color: var(--kp-light-blue); }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--kp-light-blue);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--kp-blue);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--kp-green);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <button class="sidebar-toggle" type="button" id="sidebarToggleBtn">
                <i class="fas fa-bars"></i>
            </button>

            <a class="navbar-brand" href="{{ route('customer.customer-dashboard') }}">
                <i class="fas fa-satellite-dish me-2"></i>
                <span>DarkFibre CRM</span>
            </a>

            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>
                        <span>{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('customer.profile.edit') }}">
                                <i class="fas fa-user me-2"></i> Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Sidebar Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar" id="sidebar">
                <div class="sidebar-inner">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.customer-dashboard') ? 'active' : '' }}"
                               href="{{ route('customer.customer-dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.design-requests.*') ? 'active' : '' }}"
                               href="{{ route('customer.design-requests.index') }}">
                                <i class="fas fa-pencil-ruler me-2"></i>
                                <span>Design Requests</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.leases.*') ? 'active' : '' }}"
                               href="{{ route('customer.leases.index') }}">
                                <i class="fas fa-network-wired me-2"></i>
                                <span>My Leases</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.invoices.*') ? 'active' : '' }}"
                               href="{{ route('customer.invoices.index') }}">
                                <i class="fas fa-file-invoice me-2"></i>
                                <span>Invoices</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.tickets.*') ? 'active' : '' }}"
                               href="{{ route('customer.tickets.index') }}">
                                <i class="fas fa-headset me-2"></i>
                                <span>Support Tickets</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.documents.*') ? 'active' : '' }}"
                               href="{{ route('customer.documents.index') }}">
                                <i class="fas fa-folder-open me-2"></i>
                                <span>My Documents</span>
                            </a>
                        </li>

                        <!-- Certificates Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('customer.certificates.*') ? 'active' : '' }}"
                               href="#" id="certificatesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-certificate me-2"></i>
                                <span>Certificates</span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="certificatesDropdown">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('customer.certificates.conditional.*') ? 'active' : '' }}"
                                       href="{{ route('customer.certificates.conditional.index') }}">
                                        <i class="fas fa-file-contract me-2"></i> Conditional Certificates
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('customer.certificates.acceptance.*') ? 'active' : '' }}"
                                       href="{{ route('customer.certificates.acceptance.index') }}">
                                        <i class="fas fa-check-circle me-2"></i> Acceptance Certificates
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 ms-auto px-md-4 py-4">
                <!-- Success Alert -->
                @if(session('success'))
                    <div class="alert alert-custom-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Error Alert -->
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Warning Alert -->
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Info Alert -->
                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            'use strict';

            // DOM Elements
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggleBtn');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');

            // Toggle Sidebar Function
            function toggleSidebar() {
                if (!sidebar || !sidebarBackdrop) return;

                const isOpen = sidebar.classList.contains('show');

                if (!isOpen) {
                    sidebar.classList.add('show');
                    sidebarBackdrop.classList.add('show');
                    document.body.style.overflow = 'hidden';
                } else {
                    sidebar.classList.remove('show');
                    sidebarBackdrop.classList.remove('show');
                    document.body.style.overflow = '';
                }
            }

            // Close Sidebar Function
            function closeSidebar() {
                if (!sidebar || !sidebarBackdrop) return;

                sidebar.classList.remove('show');
                sidebarBackdrop.classList.remove('show');
                document.body.style.overflow = '';
            }

            // Event Listeners
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }

            if (sidebarBackdrop) {
                sidebarBackdrop.addEventListener('click', closeSidebar);
            }

            // Close sidebar when clicking on nav links on mobile
            if (window.innerWidth < 768) {
                const sidebarLinks = document.querySelectorAll('.sidebar .nav-link, .sidebar .dropdown-item');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        if (sidebar && sidebar.classList.contains('show')) {
                            closeSidebar();
                        }
                    });
                });
            }

            // Handle window resize - auto-close sidebar on desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    closeSidebar();
                }
            });

            // Initialize Bootstrap dropdowns in sidebar
            const dropdownElements = document.querySelectorAll('.sidebar .dropdown-toggle');
            dropdownElements.forEach(element => {
                try {
                    new bootstrap.Dropdown(element);
                } catch (e) {
                    console.log('Dropdown initialization error:', e);
                }
            });

            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    try {
                        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                        bsAlert.close();
                    } catch (e) {
                        console.log('Alert auto-dismiss error:', e);
                    }
                }, 5000);
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
