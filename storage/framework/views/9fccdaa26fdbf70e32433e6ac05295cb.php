<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="pusher-key" content="<?php echo e(env('PUSHER_APP_KEY')); ?>">
    <meta name="pusher-cluster" content="<?php echo e(env('PUSHER_APP_CLUSTER', 'mt1')); ?>">

    <title><?php echo $__env->yieldContent('title', 'Dark Fibre CRM'); ?> - Kenya Power</title>

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

    /* Dropdown Styles */
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

    /* Desktop hover effect */
    @media (min-width: 992px) {
        .nav-item.dropdown:hover .dropdown-menu {
            display: block;
        }
    }

    .dropdown-menu-end {
        right: 0;
        left: auto;
    }

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

    .dropdown-header {
        display: block;
        padding: 0.5rem 1.5rem;
        margin-bottom: 0;
        font-size: 0.875rem;
        color: var(--kp-dark);
        white-space: nowrap;
        background: linear-gradient(135deg, var(--kp-light-blue), var(--kp-light-green));
    }

    .dropdown-divider {
        height: 0;
        margin: 0.5rem 0;
        overflow: hidden;
        border-top: 1px solid #e9ecef;
    }

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

    /* Card styling */
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

    /* Kenya Power Colors */
    .border-left-primary { border-left: 4px solid var(--kp-blue) !important; }
    .border-left-success { border-left: 4px solid var(--kp-green) !important; }
    .border-left-warning { border-left: 4px solid var(--kp-yellow) !important; }
    .border-left-info { border-left: 4px solid #36b9cc !important; }

    .text-kp-blue { color: var(--kp-blue) !important; }
    .text-kp-green { color: var(--kp-green) !important; }
    .text-kp-yellow { color: var(--kp-yellow) !important; }

    .bg-kp-blue { background-color: var(--kp-blue) !important; }
    .bg-kp-green { background-color: var(--kp-green) !important; }
    .bg-kp-yellow { background-color: var(--kp-yellow) !important; }

    /* Buttons */
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

    /* Alerts */
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

    /* Footer */
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

    .footer-link:hover {
        color: var(--kp-yellow) !important;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .status-dot.bg-kp-green { background-color: var(--kp-green) !important; }

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

    /* Responsive Styles */
    @media (max-width: 991.98px) {
        body { padding-top: 56px; }

        .navbar-collapse {
            background: linear-gradient(135deg, var(--kp-blue) 0%, var(--kp-green) 100%);
            padding: 1rem;
            border-radius: 0 0 12px 12px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .nav-item { width: 100%; }

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
        .footer-compact { text-align: center; }
        .footer-heading::after { left: 50%; transform: translateX(-50%); }
        .footer-brand { justify-content: center; }
    }

    @media (max-width: 576px) {
        .container-fluid { padding-left: 10px; padding-right: 10px; }
        .navbar-brand { font-size: 1rem; }
    }

    @media print {
        .navbar, footer, .no-print { display: none !important; }
        body { padding-top: 0; }
        .card { border: 1px solid #ddd; box-shadow: none; }
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: var(--kp-light-blue); border-radius: 4px; }
    ::-webkit-scrollbar-thumb { background: var(--kp-blue); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--kp-green); }
    /* Dropdown enhancements */
.dropdown-menu {
    border-radius: 0.75rem;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    margin-top: 0.5rem;
}

.dropdown-item {
    padding: 0.6rem 1.2rem;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: rgba(0, 102, 179, 0.08);
    transform: translateX(3px);
}

.dropdown-item.active {
    background: linear-gradient(90deg, rgba(0, 102, 179, 0.1), rgba(0, 150, 57, 0.05));
    color: #0066B3;
    font-weight: 500;
}

.dropdown-item i {
    width: 20px;
    text-align: center;
}

.dropdown-header {
    background: linear-gradient(135deg, #0066B3, #009639);
    color: white;
    border-radius: 0.75rem 0.75rem 0 0;
    padding: 0.75rem 1rem;
}

.dropdown-divider {
    margin: 0.3rem 0;
}

/* Color classes */
.text-kp-blue { color: #0066B3 !important; }
.text-kp-green { color: #009639 !important; }
.text-purple { color: #6f42c1 !important; }
.text-warning { color: #ffc107 !important; }

.bg-kp-primary { background: #0066B3 !important; }
.bg-kp-blue-light { background: rgba(0, 102, 179, 0.1) !important; }

/* Hover animation on dropdown parent */
.nav-item.dropdown:hover .dropdown-menu {
    display: block;
    margin-top: 0;
}
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo e(url('/')); ?>">
                <i class="fas fa-network-wired me-2"></i>Dark Fibre CRM
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if(auth()->guard()->check()): ?>
                        <!-- Dashboard Links by Role -->
                        <?php if(in_array(Auth::user()->role, ['admin', 'technical_admin', 'system_admin', 'accountmanager_admin']) && Route::has('admin.dashboard')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('admin.dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if(Auth::user()->role === 'customer' && Route::has('customer.customer-dashboard')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('customer.customer-dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if(Auth::user()->role === 'finance' && Route::has('finance.dashboard')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('finance.dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if(Auth::user()->role === 'designer' && Route::has('designer.dashboard')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('designer.dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if(Auth::user()->role === 'surveyor' && Route::has('surveyor.dashboard')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('surveyor.dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if(Auth::user()->role === 'technician' && Route::has('technician.dashboard')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('technician.dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if(Auth::user()->role === 'ict_engineer' && Route::has('ictengineer.dashboard')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('ictengineer.dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if(Auth::user()->role === 'account_manager' && Route::has('account-manager.dashboard')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('account-manager.dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if(Auth::user()->role === 'debt_manager' && Route::has('finance.debt.dashboard')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('finance.debt.dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Maintenance Module Dropdown -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-maintenance')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="maintenanceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-tools me-1"></i> Maintenance
                            </a>
                            <div class="dropdown-menu">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isTechnician')): ?>
                                    <?php if(Route::has('technician.dashboard')): ?>
                                        <a class="dropdown-item" href="<?php echo e(route('technician.dashboard')); ?>">
                                            <i class="fas fa-tachometer-alt me-2"></i> My Dashboard
                                        </a>
                                    <?php endif; ?>
                                    <?php if(Route::has('technician.work-orders.index')): ?>
                                        <a class="dropdown-item" href="<?php echo e(route('technician.work-orders.index')); ?>">
                                            <i class="fas fa-clipboard-list me-2"></i> My Work Orders
                                        </a>
                                    <?php endif; ?>
                                    <?php if(Route::has('technician.equipment.index')): ?>
                                        <a class="dropdown-item" href="<?php echo e(route('technician.equipment.index')); ?>">
                                            <i class="fas fa-toolbox me-2"></i> Equipment
                                        </a>
                                    <?php endif; ?>
                                    <div class="dropdown-divider"></div>
                                <?php endif; ?>

                                <?php if(Route::has('maintenance.dashboard')): ?>
                                    <a class="dropdown-item" href="<?php echo e(route('maintenance.dashboard')); ?>">
                                        <i class="fas fa-tachometer-alt me-2"></i> Maintenance Dashboard
                                    </a>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-maintenance-request')): ?>
                                    <?php if(Route::has('maintenance.requests.create')): ?>
                                        <a class="dropdown-item" href="<?php echo e(route('maintenance.requests.create')); ?>">
                                            <i class="fas fa-plus-circle me-2"></i> New Request
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if(Route::has('maintenance.requests.index')): ?>
                                    <a class="dropdown-item" href="<?php echo e(route('maintenance.requests.index')); ?>">
                                        <i class="fas fa-list me-2"></i> All Requests
                                    </a>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('assign-work-orders')): ?>
                                    <div class="dropdown-divider"></div>
                                    <?php if(Route::has('maintenance.work-orders.index')): ?>
                                        <a class="dropdown-item" href="<?php echo e(route('maintenance.work-orders.index')); ?>">
                                            <i class="fas fa-clipboard-check me-2"></i> Work Orders
                                        </a>
                                    <?php endif; ?>
                                    <?php if(Route::has('maintenance.work-orders.create')): ?>
                                        <a class="dropdown-item" href="<?php echo e(route('maintenance.work-orders.create')); ?>">
                                            <i class="fas fa-plus-circle me-2"></i> Create Work Order
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-equipment')): ?>
                                    <div class="dropdown-divider"></div>
                                    <?php if(Route::has('maintenance.equipment.index')): ?>
                                        <a class="dropdown-item" href="<?php echo e(route('maintenance.equipment.index')); ?>">
                                            <i class="fas fa-toolbox me-2"></i> Equipment Management
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-maintenance-reports')): ?>
                                    <div class="dropdown-divider"></div>
                                    <?php if(Route::has('maintenance.reports')): ?>
                                        <a class="dropdown-item" href="<?php echo e(route('maintenance.reports')); ?>">
                                            <i class="fas fa-chart-bar me-2"></i> Reports & Analytics
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </li>
                        <?php endif; ?>

                        <!-- Admin Menu Items -->
                        <?php if(in_array(Auth::user()->role, ['admin', 'technical_admin', 'system_admin'])): ?>
                            <?php if(in_array(Auth::user()->role, ['admin', 'system_admin']) && Route::has('admin.users')): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('admin.users')); ?>">
                                        <i class="fas fa-users me-1"></i>Users
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if(Route::has('admin.customers.assign')): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('admin.customers.assign')); ?>">
                                        <i class="fas fa-user-tie me-1"></i>Assign Customers
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if(Route::has('admin.design-requests.index')): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('admin.design-requests.index')); ?>">
                                        <i class="fas fa-drafting-compass me-1"></i>Design Requests/Tickets
                                    </a>
                                </li>
                            <?php endif; ?>

                            <!-- Commercial Documents Dropdown (Admin) -->
                            <?php
    $leaseCount = \App\Models\Lease::count();
    $quotationCount = \App\Models\Quotation::count();
    $contractCount = \App\Models\Contract::count();
?>

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="commercialDocsAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-file-alt me-1"></i>
        <span>Commercial Documents</span>
        <span class="badge bg-kp-primary ms-1 rounded-pill"><?php echo e($leaseCount + $quotationCount + $contractCount); ?></span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="commercialDocsAdmin">
        <?php if(Route::has('admin.leases.index')): ?>
            <li>
                <a class="dropdown-item d-flex justify-content-between align-items-center <?php echo e(request()->routeIs('admin.leases.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.leases.index')); ?>">
                    <span>
                        <i class="fas fa-network-wired me-2 text-kp-blue"></i> Leases
                    </span>
                    <span class="badge bg-secondary rounded-pill"><?php echo e($leaseCount); ?></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if(Route::has('admin.quotations.index')): ?>
            <li>
                <a class="dropdown-item d-flex justify-content-between align-items-center <?php echo e(request()->routeIs('admin.quotations.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.quotations.index')); ?>">
                    <span>
                        <i class="fas fa-file-invoice-dollar me-2 text-kp-green"></i> Quotations
                    </span>
                    <span class="badge bg-secondary rounded-pill"><?php echo e($quotationCount); ?></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if(Route::has('contracts.index')): ?>
            <li>
                <a class="dropdown-item d-flex justify-content-between align-items-center <?php echo e(request()->routeIs('contracts.*') ? 'active' : ''); ?>" href="<?php echo e(route('contracts.index')); ?>">
                    <span>
                        <i class="fas fa-file-contract me-2 text-purple"></i> Contracts
                    </span>
                    <span class="badge bg-secondary rounded-pill"><?php echo e($contractCount); ?></span>
                </a>
            </li>
        <?php endif; ?>

        <li><hr class="dropdown-divider"></li>

        <?php if(Route::has('cak.dashboard')): ?>
            <li>
                <a class="dropdown-item <?php echo e(request()->routeIs('cak.*') ? 'active' : ''); ?>" href="<?php echo e(route('cak.dashboard')); ?>">
                    <i class="fas fa-tachometer-alt me-2 text-warning"></i> CAK Forms
                </a>
            </li>
        <?php endif; ?>
    </ul>
</li>
                        <?php endif; ?>

                        <!-- Customer Menu Items -->
                        <?php if(Auth::user()->role === 'customer'): ?>
                            <?php if(Route::has('customer.profile.show')): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('customer.profile.show')); ?>">
                                        <i class="fas fa-id-card me-1"></i>Profile
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if(Route::has('customer.leases.index')): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('customer.leases.index')); ?>">
                                        <i class="fas fa-network-wired me-1"></i>My Leases
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if(Route::has('customer.contracts.index')): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('customer.contracts.index')); ?>">
                                        <i class="fas fa-file-contract me-2"></i> My Contracts
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if(Route::has('customer.tickets')): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('customer.tickets')); ?>">
                                        <i class="fas fa-ticket-alt me-1"></i>Support
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if(Route::has('customer.design-requests.index')): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('customer.design-requests.index')); ?>">
                                        <i class="fas fa-drafting-compass me-1"></i>Design Requests
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if(Route::has('customer.billings.index')): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('customer.billings.index')); ?>">
                                        <i class="fas fa-file-invoice me-1"></i>Invoices
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if(Route::has('customer.documents.index')): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('customer.documents.index')); ?>">
                                        <i class="fas fa-folder me-1"></i>My Documents
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="customerCertificatesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-certificate me-1"></i> Certificates
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="customerCertificatesDropdown">
                                    <?php if(Route::has('customer.certificates.conditional.index')): ?>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo e(route('customer.certificates.conditional.index')); ?>">
                                                <i class="fas fa-file-contract me-2"></i> Conditional Certificates
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Route::has('customer.certificates.acceptance.index')): ?>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo e(route('customer.certificates.acceptance.index')); ?>">
                                                <i class="fas fa-check-circle me-2"></i> Acceptance Certificates
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <!-- Finance Menu Items -->
                        <?php if(Auth::user()->role === 'finance'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="financeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                                    <?php if(Route::has('leases.finance.index')): ?>
                                        <li><a class="dropdown-item py-2" href="<?php echo e(route('leases.finance.index')); ?>"><i class="fas fa-file-contract text-kp-blue me-2"></i> Leases Management</a></li>
                                    <?php endif; ?>
                                    <?php if(Route::has('finance.billing.index')): ?>
                                        <li><a class="dropdown-item py-2" href="<?php echo e(route('finance.billing.index')); ?>"><i class="fas fa-file-invoice-dollar text-kp-green me-2"></i> Lease Billings</a></li>
                                    <?php endif; ?>
                                    <?php if(Route::has('finance.debt.overdue-invoices')): ?>
                                        <li><a class="dropdown-item py-2" href="<?php echo e(route('finance.debt.overdue-invoices')); ?>"><i class="fas fa-money-check text-info me-2"></i> Payment Installment Plans</a></li>
                                    <?php endif; ?>

                                     <?php if(Route::has('finance.payments.index')): ?>
                                        <li><a class="dropdown-item py-2" href="<?php echo e(route('finance.payments.index')); ?>"><i class="fas fa-money-check text-info me-2"></i> Payment Followups</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <?php if(Route::has('finance.transactions.index')): ?>
                                        <li><a class="dropdown-item py-2" href="<?php echo e(route('finance.transactions.index')); ?>"><i class="fas fa-exchange-alt text-kp-yellow me-2"></i> Transactions</a></li>
                                    <?php endif; ?>
                                    <?php if(Route::has('finance.auto-billing')): ?>
                                        <li><a class="dropdown-item py-2" href="<?php echo e(route('finance.auto-billing')); ?>"><i class="fas fa-robot text-secondary me-2"></i> Auto Billing</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <?php if(Route::has('finance.reports')): ?>
                                        <li><a class="dropdown-item py-2" href="<?php echo e(route('finance.reports')); ?>"><i class="fas fa-chart-bar text-danger me-2"></i> Financial Reports</a></li>
                                    <?php endif; ?>
                                    <li class="dropdown-header mt-2">AI Analytics</li>
                                    <?php if(Route::has('finance.ai.dashboard')): ?>
                                        <li><a class="dropdown-item py-2" href="<?php echo e(route('finance.ai-analytics.dashboard')); ?>"><i class="fas fa-brain text-purple me-2"></i> Debtors Analytics</a></li>
                                    <?php endif; ?>
                                    <?php if(Route::has('finance.ai.predictive')): ?>
                                        <li><a class="dropdown-item py-2" href="<?php echo e(route('finance.ai-analytics.predictive')); ?>"><i class="fas fa-chart-line text-kp-yellow me-2"></i> Predictive Analytics</a></li>
                                    <?php endif; ?>
                                    <?php if(Route::has('finance.ai.recommendations')): ?>
                                        <li><a class="dropdown-item py-2" href="<?php echo e(route('finance.ai-analytics.recommendations')); ?>"><i class="fas fa-lightbulb text-kp-green me-2"></i> AI Recommendations</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <?php if(Route::has('finance.financial-parameters.index')): ?>
                                        <li><a class="dropdown-item py-2" href="<?php echo e(route('finance.financial-parameters.index')); ?>"><i class="fas fa-cog text-dark me-2"></i> Financial Parameters</a></li>
                                    <?php endif; ?>
                                </ul>
                            </li>

                            <?php if(Auth::user()->role === 'finance'): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('finance.payments.index')); ?>">
            <i class="fas fa-money-bill-wave me-1"></i>Payments
        </a>
    </li>
<?php endif; ?>

                            <!-- Commercial Documents Dropdown (Finance) -->
                            <?php
    $leaseCount = \App\Models\Lease::count();
    $quotationCount = \App\Models\Quotation::count();
    $contractCount = \App\Models\Contract::count();
?>

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="commercialDocsFinance" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-file-alt me-1"></i>
        <span>Commercial Documents</span>
        <span class="badge bg-kp-primary ms-1 rounded-pill"><?php echo e($leaseCount + $quotationCount + $contractCount); ?></span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="commercialDocsFinance">
        <?php if(Route::has('admin.leases.index')): ?>
            <li>
                <a class="dropdown-item d-flex justify-content-between align-items-center <?php echo e(request()->routeIs('admin.leases.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.leases.index')); ?>">
                    <span>
                        <i class="fas fa-network-wired me-2 text-kp-blue"></i> Leases
                    </span>
                    <span class="badge bg-secondary rounded-pill"><?php echo e($leaseCount); ?></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if(Route::has('admin.quotations.index')): ?>
            <li>
                <a class="dropdown-item d-flex justify-content-between align-items-center <?php echo e(request()->routeIs('admin.quotations.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.quotations.index')); ?>">
                    <span>
                        <i class="fas fa-file-invoice-dollar me-2 text-kp-green"></i> Quotations
                    </span>
                    <span class="badge bg-secondary rounded-pill"><?php echo e($quotationCount); ?></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if(Route::has('contracts.index')): ?>
            <li>
                <a class="dropdown-item d-flex justify-content-between align-items-center <?php echo e(request()->routeIs('contracts.*') ? 'active' : ''); ?>" href="<?php echo e(route('contracts.index')); ?>">
                    <span>
                        <i class="fas fa-file-contract me-2 text-purple"></i> Contracts
                    </span>
                    <span class="badge bg-secondary rounded-pill"><?php echo e($contractCount); ?></span>
                </a>
            </li>
        <?php endif; ?>

        <li><hr class="dropdown-divider"></li>

        <?php if(Route::has('cak.dashboard')): ?>
            <li>
                <a class="dropdown-item <?php echo e(request()->routeIs('cak.*') ? 'active' : ''); ?>" href="<?php echo e(route('cak.dashboard')); ?>">
                    <i class="fas fa-tachometer-alt me-2 text-warning"></i> CAK Forms
                </a>
            </li>
        <?php endif; ?>
    </ul>
</li>
                        <?php endif; ?>

                        <!-- Designer Menu Items -->
                        <?php if(Auth::user()->role === 'designer'): ?>
                            <?php if(Route::has('designer.requests.index')): ?>
                                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('designer.requests.index')); ?>"><i class="fas fa-drafting-compass me-1"></i>Design Requests</a></li>
                            <?php endif; ?>
                            <?php if(Route::has('designer.quotations.index')): ?>
                                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('designer.quotations.index')); ?>"><i class="fas fa-file-invoice-dollar me-1"></i>Quotations</a></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Surveyor Menu Items -->
                        <?php if(Auth::user()->role === 'surveyor'): ?>
                            <?php if(Route::has('surveyor.assignments.index')): ?>
                                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('surveyor.assignments.index')); ?>"><i class="fas fa-tasks me-1"></i>Assignments</a></li>
                            <?php endif; ?>
                            <?php if(Route::has('surveyor.routes.index')): ?>
                                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('surveyor.routes.index')); ?>"><i class="fas fa-route me-1"></i>Routes</a></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Technician Menu Items -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isTechnician')): ?>
                            <?php if(Route::has('technician.work-orders.index')): ?>
                                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('technician.work-orders.index')); ?>"><i class="fas fa-clipboard-list me-1"></i> Work Orders</a></li>
                            <?php endif; ?>
                            <?php if(Route::has('technician.equipment.index')): ?>
                                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('technician.equipment.index')); ?>"><i class="fas fa-toolbox me-1"></i> Equipment</a></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Account Manager Menu Items -->
                        <?php if(Auth::user()->role === 'account_manager'): ?>
                            <?php if(Route::has('account-manager.customers.index')): ?>
                                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('account-manager.customers.index')); ?>"><i class="fas fa-users me-1"></i>My Customers</a></li>
                            <?php endif; ?>

                            <!-- Legal Documents Dropdown (Account Manager) -->
                            <?php
    $leaseCount = \App\Models\Lease::count();
    $quotationCount = \App\Models\Quotation::count();
    $contractCount = \App\Models\Contract::count();
?>

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="legalDocsAccountManager" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-file-alt me-1"></i>
        <span>Commercial Documents</span>
        <span class="badge bg-kp-primary ms-1 rounded-pill"><?php echo e($leaseCount + $quotationCount + $contractCount); ?></span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="legalDocsAccountManager">
        <?php if(Route::has('admin.leases.index')): ?>
            <li>
                <a class="dropdown-item d-flex justify-content-between align-items-center <?php echo e(request()->routeIs('admin.leases.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.leases.index')); ?>">
                    <span>
                        <i class="fas fa-network-wired me-2 text-kp-blue"></i> Leases
                    </span>
                    <span class="badge bg-secondary rounded-pill"><?php echo e($leaseCount); ?></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if(Route::has('admin.quotations.index')): ?>
            <li>
                <a class="dropdown-item d-flex justify-content-between align-items-center <?php echo e(request()->routeIs('admin.quotations.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.quotations.index')); ?>">
                    <span>
                        <i class="fas fa-file-invoice-dollar me-2 text-kp-green"></i> Quotations
                    </span>
                    <span class="badge bg-secondary rounded-pill"><?php echo e($quotationCount); ?></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if(Route::has('contracts.index')): ?>
            <li>
                <a class="dropdown-item d-flex justify-content-between align-items-center <?php echo e(request()->routeIs('contracts.*') ? 'active' : ''); ?>" href="<?php echo e(route('contracts.index')); ?>">
                    <span>
                        <i class="fas fa-file-contract me-2 text-purple"></i> Contracts
                    </span>
                    <span class="badge bg-secondary rounded-pill"><?php echo e($contractCount); ?></span>
                </a>
            </li>
        <?php endif; ?>

        <li><hr class="dropdown-divider"></li>

        <?php if(Route::has('cak.dashboard')): ?>
            <li>
                <a class="dropdown-item <?php echo e(request()->routeIs('cak.*') ? 'active' : ''); ?>" href="<?php echo e(route('cak.dashboard')); ?>">
                    <i class="fas fa-tachometer-alt me-2 text-warning"></i> CAK Forms
                </a>
            </li>
        <?php endif; ?>
    </ul>
</li>

                            <?php if(Route::has('account-manager.tickets.index')): ?>
                                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('account-manager.tickets.index')); ?>"><i class="fas fa-ticket-alt me-1"></i>Support Tickets</a></li>
                            <?php endif; ?>
                            <?php if(Route::has('account-manager.payments.index')): ?>
                                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('account-manager.payments.index')); ?>"><i class="fas fa-money-bill-wave me-1"></i>Payment Followups</a></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Debt Manager Menu -->
                        <?php if(Auth::user()->role === 'debt_manager'): ?>
                            <?php if(Route::has('debt-manager.dashboard')): ?>
                                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('debt-manager.dashboard')); ?>"><i class="fas fa-chart-line me-1"></i> Debt Dashboard</a></li>
                            <?php endif; ?>
                            <?php if(Route::has('debt-manager.customers')): ?>
                                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('debt-manager.customers')); ?>"><i class="fas fa-users me-1"></i> Customers</a></li>
                            <?php endif; ?>
                            <?php if(Route::has('debt-manager.payments')): ?>
                                <li class="nav-item"><a class="nav-link" href="<?php echo e(route('debt-manager.payments')); ?>"><i class="fas fa-money-bill-wave me-1"></i> Payments</a></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Marketing Admin Menu Items -->
                        <?php if(Auth::user()->role === 'accountmanager_admin'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="marketingAnalyticsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-chart-pie me-1"></i>Marketing Analytics
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="marketingAnalyticsDropdown" style="min-width: 220px;">
    <li class="dropdown-header bg-light py-2">
        <i class="fas fa-chart-line me-1"></i> Marketing Analytics
    </li>
    <?php if(Route::has('marketing-admin.dashboard')): ?>
        <li>
            <a class="dropdown-item" href="<?php echo e(route('marketing-admin.dashboard')); ?>">
                <i class="fas fa-tachometer-alt me-2 text-primary"></i> Dashboard
                <span class="badge bg-primary rounded-pill ms-2">New</span>
            </a>
        </li>
    <?php endif; ?>
    <?php if(Route::has('marketing-admin.analytics')): ?>
        <li>
            <a class="dropdown-item" href="<?php echo e(route('marketing-admin.analytics')); ?>">
                <i class="fas fa-chart-bar me-2 text-success"></i> Performance Analytics
            </a>
        </li>
    <?php endif; ?>
    <?php if(Route::has('marketing-admin.campaigns')): ?>
        <li>
            <a class="dropdown-item" href="<?php echo e(route('marketing-admin.campaigns')); ?>">
                <i class="fas fa-bullhorn me-2 text-warning"></i> Campaign Management
            </a>
        </li>
    <?php endif; ?>
    <?php if(Route::has('marketing-admin.reports')): ?>
        <li>
            <a class="dropdown-item" href="<?php echo e(route('marketing-admin.reports')); ?>">
                <i class="fas fa-file-alt me-2 text-info"></i> Marketing Reports
            </a>
        </li>
    <?php endif; ?>
    <li><hr class="dropdown-divider"></li>
    <li class="px-3 py-2">
        <small class="text-muted">
            <i class="fas fa-sync-alt me-1"></i> Updated: <?php echo e(now()->format('H:i')); ?>

        </small>
    </li>
</ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="teamManagementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-tie me-1"></i>Team Management
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="teamManagementDropdown" style="min-width: 220px;">
    <!-- Team Management -->
    <li>
        <a class="dropdown-item" href="<?php echo e(route('admin.account-managers.index') ?? '#'); ?>">
            <i class="fas fa-user-tie me-2 text-primary"></i> Account Managers
        </a>
    </li>
    <li>
        <a class="dropdown-item" href="<?php echo e(route('admin.customers.index') ?? '#'); ?>">
            <i class="fas fa-exchange-alt me-2 text-success"></i> Assign Managers
        </a>
    </li>

    <li><hr class="dropdown-divider my-1"></li>

    <!-- Performance -->
    <li>
        <a class="dropdown-item" href="<?php echo e(route('marketing-admin.performance') ?? '#'); ?>">
            <i class="fas fa-tachometer-alt me-2 text-info"></i> Performance Dashboard
        </a>
    </li>
    <li>
        <a class="dropdown-item" href="<?php echo e(route('marketing-admin.sales-pipeline') ?? '#'); ?>">
            <i class="fas fa-funnel-dollar me-2 text-warning"></i> Sales Pipeline
        </a>
    </li>

    <li><hr class="dropdown-divider my-1"></li>

    <!-- Sales & Targets -->
    <li>
        <a class="dropdown-item" href="<?php echo e(route('marketing-admin.targets') ?? '#'); ?>">
            <i class="fas fa-crosshairs me-2 text-danger"></i> Sales Targets
        </a>
    </li>
    <li>
        <a class="dropdown-item" href="<?php echo e(route('marketing-admin.commissions') ?? '#'); ?>">
            <i class="fas fa-coins me-2 text-success"></i> Commissions
        </a>
    </li>

    <li><hr class="dropdown-divider my-1"></li>

    <!-- Analytics -->
    <li>
        <a class="dropdown-item" href="<?php echo e(route('marketing-admin.customer-insights') ?? '#'); ?>">
            <i class="fas fa-chart-bar me-2 text-purple"></i> Customer Insights
        </a>
    </li>
</ul>
                            </li>

                            <!-- Legal Documents Dropdown (Marketing Admin) -->
                            <?php
    $leaseCount = \App\Models\Lease::count();
    $quotationCount = \App\Models\Quotation::count();
    $contractCount = \App\Models\Contract::count();
?>

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="legalDocsMarketingAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-file-alt me-1"></i>
        <span>Commercial Documents</span>
        <span class="badge bg-kp-primary ms-1 rounded-pill"><?php echo e($leaseCount + $quotationCount + $contractCount); ?></span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="legalDocsMarketingAdmin">
        <?php if(Route::has('admin.leases.index')): ?>
            <li>
                <a class="dropdown-item d-flex justify-content-between align-items-center <?php echo e(request()->routeIs('admin.leases.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.leases.index')); ?>">
                    <span>
                        <i class="fas fa-network-wired me-2 text-kp-blue"></i> Leases
                    </span>
                    <span class="badge bg-secondary rounded-pill"><?php echo e($leaseCount); ?></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if(Route::has('admin.quotations.index')): ?>
            <li>
                <a class="dropdown-item d-flex justify-content-between align-items-center <?php echo e(request()->routeIs('admin.quotations.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.quotations.index')); ?>">
                    <span>
                        <i class="fas fa-file-invoice-dollar me-2 text-kp-green"></i> Quotations
                    </span>
                    <span class="badge bg-secondary rounded-pill"><?php echo e($quotationCount); ?></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if(Route::has('contracts.index')): ?>
            <li>
                <a class="dropdown-item d-flex justify-content-between align-items-center <?php echo e(request()->routeIs('contracts.*') ? 'active' : ''); ?>" href="<?php echo e(route('contracts.index')); ?>">
                    <span>
                        <i class="fas fa-file-contract me-2 text-purple"></i> Contracts
                    </span>
                    <span class="badge bg-secondary rounded-pill"><?php echo e($contractCount); ?></span>
                </a>
            </li>
        <?php endif; ?>

        <li><hr class="dropdown-divider"></li>

        <?php if(Route::has('cak.dashboard')): ?>
            <li>
                <a class="dropdown-item <?php echo e(request()->routeIs('cak.*') ? 'active' : ''); ?>" href="<?php echo e(route('cak.dashboard')); ?>">
                    <i class="fas fa-tachometer-alt me-2 text-warning"></i> CAK Forms
                </a>
            </li>
        <?php endif; ?>
    </ul>
</li>
                        <?php endif; ?>

                        <!-- Kenya Fibre Dashboard (GIS Map) -->
                        <?php if(!in_array(Auth::user()->role, ['customer']) && Route::has('kenya.fibre.dashboard')): ?>
                            <li class="nav-item">
                                <a class="nav-link kenya-fibre-link" href="<?php echo e(route('kenya.fibre.dashboard')); ?>">
                                    <i class="fas fa-globe-africa me-1"></i> Map(GIS)
                                </a>
                            </li>

                            <li class="nav-item">
    <?php if(in_array(Auth::user()->role, ['admin','technical_admin','accountmanager_admin', 'system_admin', 'executive', 'finance', 'management'])): ?>
    <li class="nav-item">
        <a href="<?php echo e(route('executive.dashboard')); ?>"
           class="nav-link <?php echo e(request()->routeIs('executive.dashboard') ? 'active' : ''); ?>">
            <i class="fas fa-chart-line"></i>
            <span>Executive Dashboard</span>
        </a>
    </li>
<?php endif; ?>
</li>
                        <?php endif; ?>

                        <!-- Statements -->
                        <?php if((Auth::user()->role === 'customer' && Route::has('customer.statements')) || Route::has('statements.index')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(Auth::user()->role === 'customer' ? route('customer.statements') : route('statements.index')); ?>">
                                    <i class="fas fa-file-invoice me-1"></i>
                                    <?php echo e(Auth::user()->role === 'customer' ? 'My Statements' : 'Generate Statements'); ?>

                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if(in_array(Auth::user()->role, ['executive', 'management', 'admin', 'system_admin'])): ?>
    <li class="nav-item">
        <a href="<?php echo e(route('executive.role.dashboard')); ?>"
           class="nav-link <?php echo e(request()->routeIs('executive.role.dashboard') ? 'active' : ''); ?>">
            <i class="fas fa-user-tie me-1"></i>
            Executive Role Dashboard
        </a>
    </li>
<?php endif; ?>

                        <!-- Chat Link -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('use-chat')): ?>
                            <?php if(Route::has('chat.index')): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('chat.index')); ?>">
                                        <i class="fas fa-comments me-1"></i> WeChat
                                        <?php $unreadCount = auth()->user()->totalUnreadMessages(); ?>
                                        <?php if($unreadCount > 0): ?>
                                            <span class="badge bg-danger ms-1"><?php echo e($unreadCount); ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>

<!-- Notifications Dropdown -->
<!-- Notifications Dropdown -->
<li class="nav-item dropdown">
    <a class="nav-link position-relative dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell"></i>
        <?php
            $unreadNotificationsCount = auth()->user()->unreadNotifications->count();
        ?>
        <?php if($unreadNotificationsCount > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge">
                <?php echo e($unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount); ?>

            </span>
        <?php endif; ?>
    </a>
    <ul class="dropdown-menu dropdown-menu-end notifications-menu" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 450px; overflow-y: auto;">
        <li class="dropdown-header bg-light d-flex justify-content-between align-items-center">
            <span>Notifications</span>
           <?php if($unreadNotificationsCount > 0): ?>
    <a href="#" class="text-muted small" id="markAllNotificationsRead">
        Mark all as read (<?php echo e($unreadNotificationsCount); ?>)
    </a>
<?php endif; ?>
        </li>
        <div id="notificationsList">
            <?php $__empty_1 = true; $__currentLoopData = auth()->user()->notifications()->latest()->take(10)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $data = $notification->data;
                    $isUnread = is_null($notification->read_at);
                    $isCertificateNotification = isset($data['type']) && $data['type'] === 'conditional_certificate';
                ?>
                <li class="dropdown-item-text notification-item <?php echo e($isUnread ? 'unread' : 'read'); ?>" data-id="<?php echo e($notification->id); ?>">
                    <div class="d-flex align-items-start">
                        <div class="avatar me-2 mt-1">
                            <div class="bg-<?php echo e($isUnread ? 'info' : 'secondary'); ?> rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">
                                <i class="fas <?php echo e($isCertificateNotification ? 'fa-file-contract' : 'fa-bell'); ?>"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <small class="fw-bold <?php echo e($isUnread ? 'text-dark' : 'text-secondary'); ?>">
                                    <?php echo e($data['sender_name'] ?? ($isCertificateNotification ? 'ICT Engineer' : 'System')); ?>

                                    <?php if($isUnread): ?>
    <a href="#" onclick="event.preventDefault(); markSingleNotificationRead('<?php echo e($notification->id); ?>')"
       class="small text-kp-green text-decoration-none mark-notification-read"
       data-id="<?php echo e($notification->id); ?>">
        <i class="fas fa-check-circle"></i> Mark Read
    </a>
<?php endif; ?>
                                </small>
                                <small class="text-muted"><?php echo e(\Carbon\Carbon::parse($notification->created_at)->diffForHumans()); ?></small>
                            </div>
                            <small class="d-block <?php echo e($isUnread ? 'text-dark' : 'text-muted'); ?>">
                                <?php if($isCertificateNotification): ?>
                                    <strong>Conditional Certificate Issued</strong><br>
                                    <?php echo e($data['message_preview'] ?? $data['message']); ?>

                                <?php else: ?>
                                    <?php echo e($data['message_preview'] ?? 'New notification'); ?>

                                <?php endif; ?>
                            </small>
                            <div class="mt-2 d-flex gap-2">
                                <?php if($isCertificateNotification && isset($data['action_url'])): ?>
                                    <a href="<?php echo e($data['action_url']); ?>" class="small text-kp-blue text-decoration-none">
                                        <i class="fas fa-eye"></i> View Certificate
                                    </a>
                                <?php endif; ?>
                                <?php if($isUnread): ?>
                                    <a href="#" onclick="event.preventDefault(); markSingleNotificationRead('<?php echo e($notification->id); ?>')" class="small text-kp-green text-decoration-none">
                                        <i class="fas fa-check-circle"></i> Mark Read
                                    </a>
                                <?php else: ?>
                                    <span class="small text-muted"><i class="fas fa-check-double"></i> Read</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </li>
                <?php if(!$loop->last): ?>
                    <li class="dropdown-divider" style="margin: 0;"></li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="text-center text-muted py-4">
                    <i class="fas fa-bell-slash fa-2x mb-2"></i>
                    <p class="mb-0">No notifications</p>
                </li>
            <?php endif; ?>
        </div>
        <?php if(auth()->user()->notifications()->count() > 0): ?>
            <li><hr class="dropdown-divider"></li>
            <li class="text-center p-2">
                <?php if(Route::has('designer.notifications')): ?>
                    <a href="<?php echo e(route('designer.notifications')); ?>" class="btn btn-sm btn-outline-kp-primary w-100">
                        <i class="fas fa-list"></i> View All Notifications
                    </a>
                <?php endif; ?>
            </li>
        <?php endif; ?>
    </ul>
</li>
                    <?php endif; ?>
                </ul>

                <!-- Right-side User Menu -->
                <ul class="navbar-nav ms-auto">
                    <?php if(auth()->guard()->check()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenuDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i>
                                <span class="d-none d-sm-inline"><?php echo e(Auth::user()->name); ?></span>
                                <span class="badge bg-<?php echo e(Auth::user()->role === 'admin' ? 'danger' :
                                    (Auth::user()->role === 'technical_admin' ? 'warning' :
                                    (Auth::user()->role === 'system_admin' ? 'primary' :
                                    (Auth::user()->role === 'accountmanager_admin' ? 'info' :
                                    (Auth::user()->role === 'technician' ? 'warning' :
                                    (Auth::user()->role === 'debt_manager' ? 'info' :
                                    (Auth::user()->role === 'account_manager' ? 'info' : 'secondary'))))))); ?> ms-1">
                                    <?php echo e(ucfirst(str_replace('_', ' ', Auth::user()->role))); ?>

                                </span>
                                <?php if(Auth::user()->role === 'technician' && Auth::user()->employee_id): ?>
                                    <span class="badge bg-info ms-1"><?php echo e(Auth::user()->employee_id); ?></span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuDropdown">
                                <li class="dropdown-header">
                                    <small>Logged in as</small><br>
                                    <strong><?php echo e(Auth::user()->email); ?></strong>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('login')); ?>">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <?php if(Route::has('register.customer')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('register.customer')); ?>">
                                    <i class="fas fa-user-plus me-1"></i>Register
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <?php if(session('success')): ?>
                <div class="alert alert-kp-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(session('warning')): ?>
                <div class="alert alert-kp-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo e(session('warning')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(session('info')): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <?php echo e(session('info')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <!-- Footer -->
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
                                <li class="mb-1"><a href="<?php echo e(url('/')); ?>" class="footer-link small">Home</a></li>
                                <?php if(Route::has('help.index')): ?>
                                    <li class="mb-1"><a href="<?php echo e(route('help.index')); ?>" class="footer-link small">Help Center</a></li>
                                <?php endif; ?>
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
                            v<?php echo e(config('app.version', '1.0.0')); ?>

                        </span>
                        <?php if(app()->environment('local')): ?>
                            <span class="badge px-2 py-1" style="background: var(--kp-yellow); color: var(--kp-dark);">Development</span>
                        <?php elseif(app()->environment('staging')): ?>
                            <span class="badge px-2 py-1" style="background: #17a2b8; color: white;">Staging</span>
                        <?php else: ?>
                            <span class="badge px-2 py-1" style="background: var(--kp-green); color: white;">Production</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <hr class="my-3 bg-light opacity-25">

            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="copyright small">
                        <p class="mb-0 text-light opacity-75">
                            &copy; <?php echo e(date('Y')); ?> <strong style="color: var(--kp-yellow);">Kenya Power and Lighting Company</strong>. All rights reserved.
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    (function() {
        // Back to top button
        const backToTop = document.getElementById('backToTop');
        if (backToTop) {
            backToTop.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        // Initialize all tooltips
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
            try {
                new bootstrap.Tooltip(el);
            } catch (e) {
                console.log('Tooltip error:', e);
            }
        });

        // Reinitialize all dropdowns to ensure they work
        document.querySelectorAll('.dropdown-toggle').forEach(function(dropdown) {
            try {
                new bootstrap.Dropdown(dropdown);
            } catch (e) {
                console.log('Dropdown error:', e);
            }
        });

        // Auto-dismiss alerts after 5 seconds
        document.querySelectorAll('.alert').forEach(function(alert) {
            setTimeout(function() {
                try {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    bsAlert.close();
                } catch (e) {
                    console.log('Alert error:', e);
                }
            }, 5000);
        });

        // Mobile dropdown fix
        if (window.innerWidth < 992) {
            document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const parent = this.closest('.dropdown');
                    const menu = parent ? parent.querySelector('.dropdown-menu') : null;
                    if (menu) {
                        menu.classList.toggle('show');
                    }
                });
            });
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                    menu.classList.remove('show');
                });
            }
        });

        // ==================== NOTIFICATION FUNCTIONS ====================

        /**
         * Mark a single notification as read
         * @param {string|number} notificationId
         */
        window.markSingleNotificationRead = function(notificationId) {
            if (!notificationId) {
                console.error('Notification ID is required');
                return;
            }

            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Reload to update the notification count
                    location.reload();
                } else {
                    console.error('Failed to mark as read:', data.message);
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        };

        /**
         * Mark all notifications as read
         */
        window.markAllNotificationsAsRead = function() {
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Reload to update the notification count
                    location.reload();
                } else {
                    console.error('Failed to mark all as read:', data.message);
                }
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
            });
        };

        // Alias for backward compatibility
        window.markAsRead = window.markSingleNotificationRead;
        window.markAllAsRead = window.markAllNotificationsAsRead;

        /**
         * Open chat with a specific conversation
         * @param {number} conversationId
         */
        window.openChat = function(conversationId) {
            if (conversationId && conversationId > 0) {
                window.location.href = '/chat?conversation=' + conversationId;
            } else {
                window.location.href = '/chat';
            }
        };

        /**
         * Update the notification badge count
         */
        window.updateNotificationBadge = function() {
            fetch('/notifications/unread-count', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.style.display = 'inline';
                    } else {
                        badge.style.display = 'none';
                    }
                }

                // Also update the "Mark All as Read" button text if it exists
                const markAllBtn = document.getElementById('markAllNotificationsRead');
                if (markAllBtn && data.count > 0) {
                    markAllBtn.textContent = `Mark all as read (${data.count})`;
                } else if (markAllBtn) {
                    markAllBtn.textContent = 'Mark all as read';
                }
            })
            .catch(error => {
                console.error('Error updating notification badge:', error);
            });
        };

        // Initialize notification badge update
        if (document.querySelector('.notification-badge')) {
            window.updateNotificationBadge();
            // Update every 30 seconds
            setInterval(function() {
                window.updateNotificationBadge();
            }, 30000);
        }

        // Add event listener for "Mark All as Read" button if it exists
        const markAllBtn = document.getElementById('markAllNotificationsRead');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                window.markAllNotificationsAsRead();
            });
        }

        // Add event listeners for individual mark as read buttons (for dynamically added notifications)
        document.addEventListener('click', function(e) {
            const markReadBtn = e.target.closest('.mark-notification-read');
            if (markReadBtn) {
                e.preventDefault();
                const notificationId = markReadBtn.dataset.id;
                if (notificationId) {
                    window.markSingleNotificationRead(notificationId);
                }
            }
        });
    })();
</script>

    <?php if(config('app.use_alpine', false)): ?>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <?php endif; ?>

    <?php if(class_exists(\Livewire\Livewire::class)): ?>
        @livewireScripts
    <?php endif; ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH G:\project\darkfibre-crm\resources\views/layouts/app.blade.php ENDPATH**/ ?>