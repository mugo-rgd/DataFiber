<?php $__env->startSection('title', 'Account Manager Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-0">
    <!-- Dashboard Header - Fully Responsive -->
    <div class="dashboard-header bg-gradient-primary py-2 py-sm-3 py-md-4">
        <div class="container-fluid px-3 px-sm-4 px-md-5">
            <div class="row align-items-center g-2 g-md-3">
                <div class="col-12 col-lg-8 mb-2 mb-lg-0">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="header-icon me-2 me-sm-3 mb-1 mb-sm-0">
                            <i class="fas fa-user-tie text-white responsive-icon"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h1 class="responsive-heading text-white mb-1">Account Manager Dashboard</h1>
                            <?php
                                $hour = now()->hour;
                                if ($hour < 12) {
                                    $greeting = 'Good morning';
                                } elseif ($hour < 17) {
                                    $greeting = 'Good afternoon';
                                } else {
                                    $greeting = 'Good evening';
                                }
                            ?>
                            <p class="mb-0 opacity-75 text-white responsive-text"><?php echo e($greeting); ?>, <strong><?php echo e(Auth::user()->name); ?></strong>!</p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-1 gap-sm-2 mt-2">
                        <span class="badge bg-white text-primary responsive-badge">
                            <i class="fas fa-calendar-day me-1"></i>
                            <span class="date-text"><?php echo e(now()->format('F j, Y')); ?></span>
                        </span>
                        <span class="badge bg-white-20 text-white responsive-badge">
                            <i class="fas fa-clock me-1"></i>
                            <?php echo e(now()->format('g:i A')); ?>

                        </span>
                        <span class="badge bg-success responsive-badge">
                            <i class="fas fa-circle me-1"></i> Online
                        </span>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="d-flex flex-wrap gap-1 gap-sm-2 justify-content-start justify-content-lg-end">
                        <a href="<?php echo e(route('account-manager.tickets.create')); ?>" class="btn btn-light responsive-btn">
                            <i class="fas fa-plus-circle me-1 me-sm-2"></i>
                            <span class="btn-text">New Ticket</span>
                        </a>
                        <a href="<?php echo e(route('account-manager.payments.create')); ?>" class="btn btn-light responsive-btn">
                            <i class="fas fa-money-bill-wave me-1 me-sm-2"></i>
                            <span class="btn-text">Track Payment</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Section - Auto Adjusting -->
    <?php if(($stats['high_priority_tickets'] ?? 0) > 0 || ($stats['overdue_payments'] ?? 0) > 0): ?>
    <div class="container-fluid px-3 px-sm-4 px-md-5 py-2 py-sm-3">
        <div class="alert alert-warning alert-dismissible fade show rounded-lg border-0 shadow-sm m-0" role="alert">
            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
                <div class="alert-icon me-0 me-sm-2 me-md-3 mb-2 mb-sm-0">
                    <i class="fas fa-exclamation-triangle responsive-alert-icon"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="alert-heading mb-2 fw-bold responsive-subheading">Attention Required</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if(($stats['high_priority_tickets'] ?? 0) > 0): ?>
                        <div class="alert-item responsive-text">
                            <i class="fas fa-headset me-1"></i>
                            <span class="fw-bold"><?php echo e($stats['high_priority_tickets']); ?></span> urgent tickets
                        </div>
                        <?php endif; ?>
                        <?php if(($stats['overdue_payments'] ?? 0) > 0): ?>
                        <div class="alert-item responsive-text">
                            <i class="fas fa-money-bill-wave me-1"></i>
                            <span class="fw-bold"><?php echo e($stats['overdue_payments']); ?></span> overdue
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <button type="button" class="btn-close mt-2 mt-sm-0 ms-0 ms-sm-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content - Fluid Container -->
    <div class="container-fluid px-3 px-sm-4 px-md-5 py-3 py-sm-4">
        <!-- Customer Success Metrics - Dynamic Grid -->
        <div class="row g-2 g-sm-3 g-md-4 mb-3 mb-sm-4">
            <?php
                $metrics = [
                    [
                        'title' => 'Customers',
                        'value' => $stats['total_customers'] ?? 0,
                        'icon' => 'user-friends',
                        'color' => 'primary',
                        'trend' => '12%',
                        'trend_color' => 'success',
                        'badge' => 'Active',
                        'badge_color' => 'success',
                        'subtitle' => 'in portfolio',
                        'link' => route('account-manager.customers.index'),
                        'link_text' => 'View Portfolio'
                    ],
                    [
                        'title' => 'Active Support',
                        'value' => $stats['open_tickets'] ?? 0,
                        'icon' => 'headset',
                        'color' => 'warning',
                        'alert' => ($stats['high_priority_tickets'] ?? 0) > 0 ? $stats['high_priority_tickets'] . ' urgent' : null,
                        'alert_color' => 'danger',
                        'subtitle' => 'open requests',
                        'link' => route('account-manager.tickets.index'),
                        'link_text' => 'Manage Tickets'
                    ],
                    [
                        'title' => 'Payment Health',
                        'value' => $stats['pending_payments'] ?? 0,
                        'icon' => 'chart-line',
                        'color' => 'info',
                        'alert' => ($stats['overdue_payments'] ?? 0) > 0 ? $stats['overdue_payments'] . ' overdue' : null,
                        'alert_color' => 'danger',
                        'subtitle' => 'pending collection',
                        'link' => route('account-manager.payments.index'),
                        'link_text' => 'Review Payments'
                    ],
                    [
                        'title' => 'Satisfaction',
                        'value' => ($stats['satisfaction_score'] ?? 'N/A') . '%',
                        'icon' => 'star',
                        'color' => 'success',
                        'score' => $stats['satisfaction_score'] ?? 0,
                        'subtitle' => 'average rating',
                        'link' => '#',
                        'link_text' => 'View Feedback'
                    ]
                ];
            ?>

            <?php $__currentLoopData = $metrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-6 col-md-6 col-lg-3 mb-2 mb-sm-3">
                <div class="stat-card bg-white rounded-lg shadow-sm border-0 h-100">
                    <div class="stat-card-body p-2 p-sm-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start mb-2 mb-sm-3 mb-md-4">
                            <div class="stat-icon bg-<?php echo e($metric['color']); ?>-light rounded-circle responsive-stat-icon">
                                <i class="fas fa-<?php echo e($metric['icon']); ?> text-<?php echo e($metric['color']); ?>"></i>
                            </div>
                            <div class="trend-indicator">
                                <?php if(isset($metric['alert'])): ?>
                                <span class="badge bg-<?php echo e($metric['alert_color']); ?> responsive-badge"><?php echo e($metric['alert']); ?></span>
                                <?php elseif(isset($metric['trend'])): ?>
                                <span class="badge bg-<?php echo e($metric['trend_color'] ?? 'success'); ?> responsive-badge">
                                    <i class="fas fa-arrow-up me-1"></i><?php echo e($metric['trend']); ?>

                                </span>
                                <?php elseif(isset($metric['score'])): ?>
                                <?php
                                    $score = $metric['score'];
                                    $scoreClass = $score >= 90 ? 'success' : ($score >= 80 ? 'warning' : 'danger');
                                ?>
                                <span class="badge bg-<?php echo e($scoreClass); ?> responsive-badge"><?php echo e($score); ?>%</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <h6 class="stat-title text-muted text-uppercase small mb-1 mb-sm-2"><?php echo e($metric['title']); ?></h6>
                        <div class="stat-value fw-bold text-dark responsive-stat-value"><?php echo e($metric['value']); ?></div>
                        <div class="stat-subtitle mb-2 mb-sm-3">
                            <?php if(isset($metric['badge'])): ?>
                            <span class="badge bg-<?php echo e($metric['badge_color'] ?? 'success'); ?> responsive-badge"><?php echo e($metric['badge']); ?></span>
                            <?php endif; ?>
                            <small class="text-muted ms-1 responsive-text"><?php echo e($metric['subtitle']); ?></small>
                        </div>
                        <a href="<?php echo e($metric['link']); ?>" class="d-block text-<?php echo e($metric['color']); ?> text-decoration-none small fw-bold responsive-link">
                            <?php echo e($metric['link_text']); ?> <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

<!-- Quick Actions - Dynamic Grid -->
<div class="row mb-3 mb-sm-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-2 mb-sm-3 mb-md-4">
            <?php
    $actions = [
        [
            'title' => 'New Ticket',
            'icon' => 'headset',
            'color' => 'info',
            'link' => route('account-manager.tickets.create'),
            'desc' => 'Register customer support issues'
        ],
        [
            'title' => 'Track Payment',
            'icon' => 'money-bill-wave',
            'color' => 'success',
            'link' => route('account-manager.payments.create'),
            'desc' => 'Track payments and debts'
        ],
        [
            'title' => 'Customers',
            'icon' => 'users',
            'color' => 'primary',
            'link' => route('account-manager.customers.index'),
            'desc' => 'Review customer information'
        ],
        [
            'title' => 'Requests',
            'icon' => 'drafting-compass',
            'color' => 'warning',
            'link' => route('admin.design-requests.index'),
            'desc' => 'Allocate design requests'
        ],
        [
            'title' => 'Link Inventory',
            'icon' => 'tachometer-alt',
            'color' => 'danger',
            'desc' => 'Fibre link management',
            'links' => [
                [
                    'label' => 'View All',
                    'route' => route('conversion-data.index'),
                    'icon' => 'list'
                ],
                [
                    'label' => 'Summary',
                    'route' => route('conversion-data.summary-report'),
                    'icon' => 'chart-bar'
                ],
                [
                    'label' => 'Add New',
                    'route' => route('conversion-data.create'),
                    'icon' => 'plus'
                ]
            ]
        ],
        [
            'title' => 'Leases',
            'icon' => 'network-wired',
            'color' => 'dark',
            'link' => function() {
                if(in_array(auth()->user()->role, ['admin', 'technical_admin', 'system_admin'])) {
                    return route('admin.leases.index');
                } elseif(auth()->user()->role === 'account_manager') {
                    return route('account-manager.leases.index');
                }
                return '#';
            },
            'desc' => 'Manage network leases',
            'disabled' => !in_array(auth()->user()->role, ['admin', 'technical_admin', 'system_admin', 'account_manager'])
        ],
        [
            'title' => 'Reports',
            'icon' => 'chart-bar',
            'color' => 'secondary',
            'link' => route('account-manager.reports.performance'),
            'desc' => 'Analytics & performance'
        ],
        [
            'title' => 'Contracts',
            'icon' => 'file-contract',
            'color' => 'purple',
            'link' => route('admin.contracts.index'),
            'desc' => 'Manage agreements'
        ],
        [
            'title' => 'Quotations',
            'icon' => 'file-invoice-dollar',
            'color' => 'danger',
            'link' => route('admin.quotations.index'),
            'desc' => 'Create and view quotations',
            'permission' => 'isAccountManager'
        ],
        [
            'title' => 'System Documents',
            'icon' => 'folder',
            'color' => 'danger',
            'link' => route('documents.index'),
            'desc' => 'View System Generated Documents',
            'permission' => 'view-system-documents'  // Changed from Closure to string
        ]
    ];

    // Calculate visible actions count (excluding those without permission)
    $visibleActionsCount = 0;
    foreach ($actions as $action) {
        $hasPermission = true;

        if (isset($action['permission'])) {
            if (is_string($action['permission'])) {
                $hasPermission = \Illuminate\Support\Facades\Gate::allows($action['permission']);
            } elseif ($action['permission'] instanceof \Closure) {
                // Handle Closure-based permissions (if you still want to support them)
                $hasPermission = $action['permission'](auth()->user());
            }
        }

        // Check if action is disabled
        if (isset($action['disabled']) && $action['disabled']) {
            $hasPermission = false;
        }

        if ($hasPermission) {
            $visibleActionsCount++;
        }
    }
?>

            <div class="card-header bg-white border-0 py-2 py-sm-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 responsive-subheading">
                        <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                    </h5>
                    <span class="badge bg-warning d-none d-sm-inline responsive-badge"><?php echo e($visibleActionsCount); ?> Actions</span>
                </div>
            </div>
            <div class="card-body p-2 p-sm-3 p-md-4">
                <div class="row g-2 g-sm-3 g-md-4">
                    <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2 col-xxl-2">
                        <?php
                            // Handle both single link and multiple links actions
                            $hasMultipleLinks = isset($action['links']);
                            $hasSingleLink = isset($action['link']);
                            $disabled = $action['disabled'] ?? false;

                            if ($hasSingleLink) {
                                $link = is_callable($action['link']) ? $action['link']() : $action['link'];
                            } else {
                                $link = '#';
                            }

                            // Check permission using Laravel's Gate facade
                            $hasPermission = true;
                            if (isset($action['permission'])) {
                                $hasPermission = \Illuminate\Support\Facades\Gate::allows($action['permission']);
                            }
                        ?>

                        <?php if(isset($action['permission']) && !$hasPermission): ?>
                            <?php continue; ?>
                        <?php endif; ?>

                        <?php if($hasMultipleLinks): ?>
                            <!-- Dropdown menu for multiple links -->
                            <div class="dropdown position-relative">
                                <div class="action-card dropdown-toggle <?php echo e($disabled ? 'disabled' : ''); ?>"
                                     <?php if(!$disabled): ?> data-bs-toggle="dropdown" aria-expanded="false" <?php endif; ?>
                                     style="cursor: pointer;">
                                    <div class="action-icon bg-<?php echo e($action['color']); ?>">
                                        <i class="fas fa-<?php echo e($action['icon']); ?>"></i>
                                    </div>
                                    <div class="action-content">
                                        <h6 class="responsive-text"><?php echo e($action['title']); ?></h6>
                                        <p class="text-muted small d-none d-sm-block"><?php echo e($action['desc']); ?></p>
                                        <span class="dropdown-toggle-indicator">
                                            <i class="fas fa-chevron-down small text-muted"></i>
                                        </span>
                                    </div>
                                </div>
                                <?php if(!$disabled): ?>
                                <ul class="dropdown-menu dropdown-menu-end p-2 border-0 shadow-lg rounded-lg"
                                    style="min-width: 200px;">
                                    <?php $__currentLoopData = $action['links']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a href="<?php echo e($subLink['route']); ?>"
                                           class="dropdown-item d-flex align-items-center py-2 px-3 rounded"
                                           onclick="event.stopPropagation();">
                                            <i class="fas fa-<?php echo e($subLink['icon']); ?> text-<?php echo e($action['color']); ?> me-2"></i>
                                            <span><?php echo e($subLink['label']); ?></span>
                                        </a>
                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                                <?php endif; ?>
                            </div>
                        <?php elseif($disabled): ?>
                            <!-- Disabled action -->
                            <div class="action-card disabled">
                                <div class="action-icon bg-<?php echo e($action['color']); ?>">
                                    <i class="fas fa-<?php echo e($action['icon']); ?>"></i>
                                </div>
                                <div class="action-content">
                                    <h6 class="responsive-text"><?php echo e($action['title']); ?></h6>
                                    <p class="text-muted small d-none d-sm-block"><?php echo e($action['desc']); ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Single link action -->
                            <a href="<?php echo e($link); ?>" class="action-card">
                                <div class="action-icon bg-<?php echo e($action['color']); ?>">
                                    <i class="fas fa-<?php echo e($action['icon']); ?>"></i>
                                </div>
                                <div class="action-content">
                                    <h6 class="responsive-text"><?php echo e($action['title']); ?></h6>
                                    <p class="text-muted small d-none d-sm-block"><?php echo e($action['desc']); ?></p>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Recent Activity Section - Dynamic Layout -->
        <div class="row g-2 g-sm-3 g-md-4">
            <!-- Recent Support Tickets -->
            <div class="col-12 col-lg-6 col-xl-6 col-xxl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="mb-0 responsive-subheading">
                                <i class="fas fa-headset text-primary me-2"></i>Recent Support
                            </h5>
                            <div class="d-flex align-items-center mt-1 mt-sm-0">
                                <span class="badge bg-light text-dark responsive-badge d-none d-sm-inline"><?php echo e($stats['open_tickets'] ?? 0); ?> active</span>
                                <a href="<?php echo e(route('account-manager.tickets.index')); ?>" class="btn btn-sm btn-primary ms-1 ms-sm-2 responsive-btn">
                                    <span class="d-none d-sm-inline">View All</span>
                                    <span class="d-inline d-sm-none">All</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php $__empty_1 = true; $__currentLoopData = $recentTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="ticket-item p-2 p-sm-3 p-md-4 border-bottom">
                            <div class="d-flex align-items-start">
                                <div class="ticket-avatar me-2 me-sm-3">
                                    <div class="avatar-circle bg-<?php echo e($ticket->priority === 'high' ? 'danger' : 'warning'); ?>-light">
                                        <i class="fas fa-<?php echo e($ticket->priority === 'high' ? 'exclamation-triangle' : 'ticket-alt'); ?> text-<?php echo e($ticket->priority === 'high' ? 'danger' : 'warning'); ?> responsive-icon-sm"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1 mb-sm-2">
                                        <h6 class="fw-bold mb-0 text-dark responsive-text"><?php echo e(Str::limit($ticket->title, 50)); ?></h6>
                                        <span class="badge bg-<?php echo e($ticket->priority === 'high' ? 'danger' : 'warning'); ?> badge-pill responsive-badge">
                                            <?php echo e(ucfirst($ticket->priority)); ?>

                                        </span>
                                    </div>
                                    <p class="text-muted small mb-2 mb-sm-3 responsive-text-sm">
                                        <i class="fas fa-user me-1"></i><?php echo e(Str::limit($ticket->customer->name ?? 'Unknown', 25)); ?>

                                        • <i class="fas fa-clock me-1"></i><?php echo e($ticket->created_at->diffForHumans()); ?>

                                    </p>
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <span class="badge bg-<?php echo e($ticket->status === 'open' ? 'primary' : ($ticket->status === 'in_progress' ? 'info' : 'success')); ?> mb-1 responsive-badge">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $ticket->status))); ?>

                                        </span>
                                        <div>
                                            <?php if($ticket->due_date): ?>
                                            <small class="text-<?php echo e($ticket->due_date->isPast() ? 'danger' : 'muted'); ?> responsive-text-sm">
                                                <i class="fas fa-clock me-1"></i>Due <?php echo e($ticket->due_date->format('M d')); ?>

                                            </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <a href="<?php echo e(route('account-manager.tickets.show', $ticket)); ?>"
                                       class="btn btn-sm btn-outline-primary w-100 w-sm-auto mt-2 responsive-btn">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-4 py-sm-5">
                            <div class="empty-state">
                                <i class="fas fa-headset text-gray-300 responsive-empty-icon mb-3"></i>
                                <h5 class="text-gray-600 responsive-subheading">No Active Support Requests</h5>
                                <p class="text-muted mb-3 mb-sm-4 responsive-text">All customers are supported!</p>
                                <a href="<?php echo e(route('account-manager.tickets.create')); ?>" class="btn btn-primary responsive-btn">
                                    <i class="fas fa-plus-circle me-2"></i>Create Ticket
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Payment Follow-ups -->
            <div class="col-12 col-lg-6 col-xl-6 col-xxl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="mb-0 responsive-subheading">
                                <i class="fas fa-money-bill-wave text-primary me-2"></i>Payment Follow-ups
                            </h5>
                            <div class="d-flex align-items-center mt-1 mt-sm-0">
                                <span class="badge bg-light text-dark responsive-badge d-none d-sm-inline"><?php echo e($upcomingPayments->count()); ?> pending</span>
                                <a href="<?php echo e(route('account-manager.payments.index')); ?>" class="btn btn-sm btn-primary ms-1 ms-sm-2 responsive-btn">
                                    <span class="d-none d-sm-inline">View All</span>
                                    <span class="d-inline d-sm-none">All</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php $__empty_1 = true; $__currentLoopData = $upcomingPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="payment-item p-2 p-sm-3 p-md-4 border-bottom">
                            <div class="d-flex align-items-start">
                                <div class="payment-avatar me-2 me-sm-3">
                                    <div class="avatar-circle bg-<?php echo e($payment->due_date->isPast() ? 'danger' : 'info'); ?>-light">
                                        <i class="fas fa-<?php echo e($payment->due_date->isPast() ? 'exclamation-triangle' : 'calendar'); ?> text-<?php echo e($payment->due_date->isPast() ? 'danger' : 'info'); ?> responsive-icon-sm"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1 mb-sm-2">
                                        <h6 class="fw-bold mb-0 text-dark responsive-text"><?php echo e(Str::limit($payment->customer->name ?? 'Unknown', 30)); ?></h6>
                                        <span class="h6 mb-0 fw-bold text-primary responsive-text">
                                            $<?php echo e(number_format($payment->amount, 2)); ?>

                                        </span>
                                    </div>
                                    <p class="text-muted small mb-2 mb-sm-3 responsive-text-sm">
                                        <i class="fas fa-calendar me-1"></i>
                                        Due <?php echo e($payment->due_date->format('M d, Y')); ?>

                                        <span class="d-none d-sm-inline">•</span>
                                        <br class="d-block d-sm-none">
                                        <i class="fas fa-clock me-1"></i><?php echo e($payment->due_date->diffForHumans()); ?>

                                    </p>
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <span class="badge bg-<?php echo e($payment->status === 'pending' ? 'warning' : ($payment->status === 'reminded' ? 'info' : 'success')); ?> mb-2 mb-sm-0 responsive-badge">
                                            <?php echo e(ucfirst($payment->status)); ?>

                                        </span>
                                        <div class="action-buttons d-flex gap-1">
                                            <?php if($payment->status === 'pending'): ?>
                                            <form action="<?php echo e(route('account-manager.payments.remind', $payment)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-warning responsive-btn-sm">
                                                    <i class="fas fa-bell"></i>
                                                    <span class="d-none d-sm-inline">Remind</span>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                            <form action="<?php echo e(route('account-manager.payments.paid', $payment)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-success responsive-btn-sm">
                                                    <i class="fas fa-check"></i>
                                                    <span class="d-none d-sm-inline">Paid</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-4 py-sm-5">
                            <div class="empty-state">
                                <i class="fas fa-money-bill-wave text-gray-300 responsive-empty-icon mb-3"></i>
                                <h5 class="text-gray-600 responsive-subheading">No Payment Follow-ups</h5>
                                <p class="text-muted mb-3 mb-sm-4 responsive-text">All payments are up to date!</p>
                                <a href="<?php echo e(route('account-manager.payments.create')); ?>" class="btn btn-primary responsive-btn">
                                    <i class="fas fa-plus-circle me-2"></i>Track Payment
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Metrics - Dynamic Grid -->
        <div class="row g-2 g-sm-3 g-md-4 mt-3 mt-sm-4">
            <?php
                $additionalMetrics = [
                    [
                        'title' => 'Ticket Distribution',
                        'icon' => 'chart-pie',
                        'color' => 'info',
                        'desc' => 'Ticket statistics chart'
                    ],
                    [
                        'title' => 'Upcoming Activities',
                        'icon' => 'calendar-check',
                        'color' => 'success',
                        'desc' => 'Meetings & follow-ups'
                    ],
                    [
                        'title' => 'Top Customers',
                        'icon' => 'trophy',
                        'color' => 'warning',
                        'desc' => 'Top by revenue'
                    ]
                ];
            ?>

            <?php $__currentLoopData = $additionalMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <h5 class="mb-0 responsive-subheading">
                            <i class="fas fa-<?php echo e($metric['icon']); ?> text-<?php echo e($metric['color']); ?> me-2"></i><?php echo e($metric['title']); ?>

                        </h5>
                    </div>
                    <div class="card-body p-2 p-sm-3 p-md-4">
                        <div class="text-center py-3 py-sm-4">
                            <div class="metric-placeholder">
                                <i class="fas fa-<?php echo e($metric['icon']); ?> text-muted opacity-25 responsive-empty-icon"></i>
                            </div>
                            <p class="text-muted mt-3 mb-0 responsive-text"><?php echo e($metric['desc']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<style>
/* CSS Custom Properties for dynamic scaling */
:root {
    --scale-factor: 1;
    --min-scale: 0.8;
    --max-scale: 1.2;
    --base-font-size: 16px;
    --spacing-unit: 0.25rem;
}

/* Fluid Typography */
.responsive-heading {
    font-size: clamp(1.25rem, 4vw, 2rem);
    line-height: 1.2;
}

.responsive-subheading {
    font-size: clamp(1rem, 3vw, 1.5rem);
    line-height: 1.3;
}

.responsive-text {
    font-size: clamp(0.875rem, 2vw, 1rem);
}

.responsive-text-sm {
    font-size: clamp(0.75rem, 1.5vw, 0.875rem);
}

/* Fluid Spacing */
.dashboard-header {
    padding-top: clamp(1rem, 3vw, 1.5rem);
    padding-bottom: clamp(1rem, 3vw, 1.5rem);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Fluid Icons */
.responsive-icon {
    font-size: clamp(1.5rem, 4vw, 2.5rem);
}

.responsive-icon-sm {
    font-size: clamp(1rem, 2.5vw, 1.5rem);
}

.responsive-alert-icon {
    font-size: clamp(1.25rem, 3vw, 1.75rem);
}

.responsive-empty-icon {
    font-size: clamp(2.5rem, 8vw, 5rem);
}

/* Fluid Cards & Containers */
.stat-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border: 1px solid #e9ecef;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}

.responsive-stat-icon {
    width: clamp(2.5rem, 6vw, 3.75rem);
    height: clamp(2.5rem, 6vw, 3.75rem);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    padding: clamp(0.5rem, 1.5vw, 0.75rem);
}

.responsive-stat-value {
    font-size: clamp(1.5rem, 4vw, 2rem);
    font-weight: 700;
}

/* Fluid Badges */
.responsive-badge {
    font-size: clamp(0.65rem, 1.5vw, 0.75rem);
    padding: clamp(0.25rem, 0.5vw, 0.375rem) clamp(0.5rem, 1vw, 0.75rem);
    border-radius: 9999px;
}

/* Fluid Buttons */
.responsive-btn {
    font-size: clamp(0.75rem, 2vw, 0.875rem);
    padding: clamp(0.375rem, 1vw, 0.5rem) clamp(0.75rem, 2vw, 1rem);
    min-height: clamp(2.5rem, 6vw, 2.75rem);
    white-space: nowrap;
}

.responsive-btn-sm {
    font-size: clamp(0.7rem, 1.8vw, 0.8rem);
    padding: clamp(0.25rem, 0.75vw, 0.375rem) clamp(0.5rem, 1.5vw, 0.75rem);
    min-height: clamp(2rem, 5vw, 2.25rem);
}

/* Action Cards */
.action-card {
    display: block;
    padding: clamp(0.75rem, 2vw, 1rem);
    background: white;
    border: 1px solid #e9ecef;
    border-radius: clamp(0.5rem, 1.5vw, 0.75rem);
    text-decoration: none;
    color: inherit;
    transition: all 0.3s;
    height: 100%;
    text-align: center;
    position: relative;
}

.action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #4e73df;
}

.action-card.disabled {
    opacity: 0.6;
    cursor: not-allowed;
    pointer-events: none;
}

.action-icon {
    width: clamp(2.5rem, 6vw, 3.125rem);
    height: clamp(2.5rem, 6vw, 3.125rem);
    border-radius: clamp(0.5rem, 1.5vw, 0.625rem);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto clamp(0.5rem, 1.5vw, 0.75rem);
    color: white;
    font-size: clamp(1rem, 2.5vw, 1.25rem);
}

/* Avatar Circles */
.avatar-circle {
    width: clamp(2.25rem, 5vw, 3rem);
    height: clamp(2.25rem, 5vw, 3rem);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: clamp(0.875rem, 2vw, 1.125rem);
}

/* Dropdown styling for multi-link actions */
.dropdown-toggle-indicator {
    position: absolute;
    bottom: 8px;
    right: 8px;
    opacity: 0.6;
}

.action-card.dropdown {
    cursor: pointer;
}

.action-card.dropdown:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #4e73df;
}

.action-card.dropdown .dropdown-menu {
    transform: translateY(10px);
    opacity: 0;
    display: block;
    visibility: hidden;
    transition: all 0.2s ease;
}

.action-card.dropdown.show .dropdown-menu {
    transform: translateY(0);
    opacity: 1;
    visibility: visible;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

/* Color Classes */
.bg-primary-light { background-color: rgba(78, 115, 223, 0.1); }
.bg-success-light { background-color: rgba(28, 200, 138, 0.1); }
.bg-info-light { background-color: rgba(54, 185, 204, 0.1); }
.bg-warning-light { background-color: rgba(246, 194, 62, 0.1); }
.bg-danger-light { background-color: rgba(231, 74, 59, 0.1); }
.bg-purple-light { background-color: rgba(111, 66, 193, 0.1); }
.bg-white-20 { background-color: rgba(255, 255, 255, 0.2); }

.bg-purple { background-color: #6f42c1 !important; }

/* Touch Device Optimization */
@media (hover: none) and (pointer: coarse) {
    .stat-card:hover,
    .action-card:hover {
        transform: none;
    }

    .btn, .action-card, .ticket-item, .payment-item {
        min-height: 44px;
    }

    .btn-sm {
        min-height: 36px;
    }

    /* Increase tap target sizes */
    .responsive-badge,
    .responsive-link {
        padding: 0.5em 0.75em;
    }
}

/* Viewport Height Adjustments */
@media (max-height: 600px) and (orientation: landscape) {
    .dashboard-header {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }

    .responsive-heading {
        font-size: 1.25rem;
    }

    .stat-card {
        margin-bottom: 0.5rem;
    }
}

/* High DPI Screens */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .stat-card,
    .action-card,
    .card {
        border-width: 0.5px;
    }
}

/* Print Styles */
@media print {
    .dashboard-header,
    .alert,
    .action-card,
    .btn {
        display: none !important;
    }

    .stat-card,
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}

/* Dynamic Grid Adjustment */
@media (max-width: 360px) {
    .col-6 {
        width: 100%;
    }

    .action-icon {
        width: 2.25rem;
        height: 2.25rem;
        font-size: 1rem;
    }
}

@media (min-width: 1400px) {
    .col-xxl-2 {
        width: 20%;
        flex: 0 0 auto;
    }
}

/* Smooth Transitions */
.stat-card,
.action-card,
.btn,
.badge {
    transition: all 0.2s ease-in-out;
}

/* Performance Optimizations */
@media (prefers-reduced-motion: reduce) {
    .stat-card,
    .action-card,
    .btn,
    .badge {
        transition: none;
    }
}

/* Accessibility */
@media (prefers-contrast: high) {
    .text-muted {
        color: #666 !important;
    }

    .bg-white-20 {
        background-color: rgba(255, 255, 255, 0.3) !important;
    }
}

/* Custom Scrollbar for Desktop */
@media (min-width: 768px) {
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Dropdown styling for multi-link actions */
.dropdown-toggle-indicator {
    position: absolute;
    bottom: 8px;
    right: 8px;
    opacity: 0.6;
}

/* Fix for dropdown positioning */
.dropdown {
    position: relative;
}

/* Ensure dropdown menu appears above other content */
.dropdown-menu {
    z-index: 1050;
    margin-top: 0.125rem;
}

/* Style for dropdown toggle */
.action-card.dropdown-toggle {
    cursor: pointer;
}

/* Remove the hover transform for dropdown toggles to prevent conflicts */
.action-card.dropdown-toggle:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #4e73df;
}

/* Show dropdown menu on hover for better UX */
@media (min-width: 768px) {
    .dropdown:hover .dropdown-menu {
        display: block;
        margin-top: 0;
    }

    .dropdown .dropdown-menu {
        display: none;
    }
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap components
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Initialize dropdowns
    const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
    const dropdownList = [...dropdownElementList].map(dropdownToggleEl => {
        // Only initialize if not disabled
        if (!dropdownToggleEl.classList.contains('disabled')) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        }
    });

    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 10000);
    });

    // Dynamic scaling based on viewport
    function updateScaleFactor() {
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        const isMobile = viewportWidth < 768;
        const isTablet = viewportWidth >= 768 && viewportWidth < 1024;

        // Calculate scale factor based on viewport size
        let scaleFactor;
        if (isMobile) {
            scaleFactor = Math.max(0.8, Math.min(1.2, viewportWidth / 375));
        } else if (isTablet) {
            scaleFactor = Math.max(0.9, Math.min(1.1, viewportWidth / 768));
        } else {
            scaleFactor = 1;
        }

        // Apply scale factor to root
        document.documentElement.style.setProperty('--scale-factor', scaleFactor);

        // Adjust grid layout for very small screens
        const metricsGrid = document.querySelector('.row.g-2.g-sm-3.g-md-4');
        if (metricsGrid && viewportWidth < 400) {
            metricsGrid.style.gap = '0.5rem';
        }
    }

    // Update date format based on screen size
    function updateDateDisplay() {
        const dateElements = document.querySelectorAll('.date-text');
        const now = new Date();

        dateElements.forEach(el => {
            if (window.innerWidth < 576) {
                el.textContent = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            } else if (window.innerWidth < 768) {
                el.textContent = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            } else {
                el.textContent = now.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            }
        });
    }

    // Update button text based on screen size
    function updateButtonText() {
        const btnTexts = document.querySelectorAll('.btn-text');
        const isMobile = window.innerWidth < 768;

        btnTexts.forEach(el => {
            const text = el.textContent;
            if (isMobile) {
                // Shorten button text on mobile
                if (text === 'New Ticket') el.textContent = 'Ticket';
                if (text === 'Track Payment') el.textContent = 'Payment';
            } else {
                // Restore full text on larger screens
                if (text === 'Ticket') el.textContent = 'New Ticket';
                if (text === 'Payment') el.textContent = 'Track Payment';
            }
        });
    }

    // Optimize for touch devices
    if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
        document.body.classList.add('touch-device');

        // Increase touch targets
        document.querySelectorAll('.btn, .action-card, .responsive-link').forEach(el => {
            el.style.minHeight = '44px';
            el.style.padding = '12px 16px';
        });
    }

    // Prevent horizontal scroll
    document.body.style.overflowX = 'hidden';
    document.documentElement.style.overflowX = 'hidden';

    // Initialize and update on resize
    updateScaleFactor();
    updateDateDisplay();
    updateButtonText();

    // Debounced resize handler
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            updateScaleFactor();
            updateDateDisplay();
            updateButtonText();
        }, 100);
    });

    // Handle orientation changes
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            updateScaleFactor();
            updateDateDisplay();
            updateButtonText();
        }, 100);
    });

    // Performance optimization for animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    // Observe cards for lazy animation
    document.querySelectorAll('.stat-card, .action-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\project\darkfibre-crm\resources\views/account-manager/dashboard.blade.php ENDPATH**/ ?>