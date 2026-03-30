<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DarkFibre CRM - Customer Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            overflow-x: hidden;
        }

        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #343a40;
            transition: all 0.3s;
        }

        .sidebar .nav-link {
            color: #fff;
            white-space: nowrap;
        }

        .sidebar .nav-link:hover {
            background-color: #495057;
        }

        .sidebar .nav-link.active {
            background-color: #007bff;
        }

        /* Mobile sidebar behavior */
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                top: 56px;
                left: -100%;
                width: 250px;
                z-index: 1000;
                transition: left 0.3s;
            }

            .sidebar.show {
                left: 0;
            }

            .sidebar-backdrop {
                position: fixed;
                top: 56px;
                left: 0;
                width: 100%;
                height: calc(100% - 56px);
                background-color: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            }

            .sidebar-backdrop.show {
                display: block;
            }

            /* Adjust main content when sidebar is visible on mobile */
            main {
                transition: margin-left 0.3s;
            }

            main.sidebar-open {
                margin-left: 250px;
            }
        }

        /* Sidebar toggle button for mobile */
        .sidebar-toggle {
            display: none;
        }

        @media (max-width: 767.98px) {
            .sidebar-toggle {
                display: block;
            }
        }

        /* Ensure proper spacing */
        .navbar-brand {
            margin-right: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <!-- Sidebar toggle button for mobile -->
            <button class="btn btn-dark sidebar-toggle me-2" type="button">
                <i class="fas fa-bars"></i>
            </button>

            <a class="navbar-brand" href="{{ route('customer.customer-dashboard') }}">
                <i class="fas fa-satellite-dish"></i> DarkFibre CRM
            </a>

            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('customer.profile.edit') }}">
                            <i class="fas fa-user me-2"></i>Profile
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile sidebar backdrop -->
    <div class="sidebar-backdrop"></div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.customer-dashboard') ? 'active' : '' }}"
                               href="{{ route('customer.customer-dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.design-requests.*') ? 'active' : '' }}"
                               href="{{ route('customer.design-requests.index') }}">
                                <i class="fas fa-pencil-ruler me-2"></i> Design Requests
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.leases.*') ? 'active' : '' }}"
                               href="{{ route('customer.leases.index') }}">
                                <i class="fas fa-network-wired me-2"></i> My Leases
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.invoices.*') ? 'active' : '' }}"
                               href="{{ route('customer.invoices.index') }}">
                                <i class="fas fa-file-invoice me-2"></i> Invoices
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.tickets.*') ? 'active' : '' }}"
                               href="{{ route('customer.tickets') }}">
                                <i class="fas fa-headset me-2"></i> Support
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.documents.*') ? 'active' : '' }}"
                               href="{{ route('customer.documents.create') }}">
                                <i class="fas fa-file-upload me-2"></i> Documents
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="certificatesDropdown" role="button" data-toggle="dropdown">
                                <i class="fas fa-certificate"></i> Certificates
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('customer.certificates.conditional.index') }}">
                                    <i class="fas fa-file-contract me-2"></i> Conditional Certificates
                                </a>
                                <a class="dropdown-item" href="{{ route('customer.certificates.acceptance.index') }}">
                                    <i class="fas fa-check-circle me-2"></i> Acceptance Certificates
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebarBackdrop = document.querySelector('.sidebar-backdrop');
            const mainContent = document.querySelector('main');

            function toggleSidebar() {
                sidebar.classList.toggle('show');
                sidebarBackdrop.classList.toggle('show');
                mainContent.classList.toggle('sidebar-open');
            }

            sidebarToggle.addEventListener('click', toggleSidebar);
            sidebarBackdrop.addEventListener('click', toggleSidebar);

            // Close sidebar when clicking on a link (mobile only)
            if (window.innerWidth < 768) {
                const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', toggleSidebar);
                });
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('show');
                    sidebarBackdrop.classList.remove('show');
                    mainContent.classList.remove('sidebar-open');
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
