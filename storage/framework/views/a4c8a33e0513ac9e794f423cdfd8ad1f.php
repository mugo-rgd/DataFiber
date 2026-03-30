<?php $__env->startSection('title', 'Dashboard - Dark Fibre CRM'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-0">
    <!-- Dashboard Header - Fully Responsive -->
    <div class="dashboard-header bg-gradient-primary text-white py-2 py-sm-3 py-md-4">
        <div class="container-fluid px-3 px-sm-4 px-md-5">
            <div class="row align-items-center g-2 g-md-3">
                <div class="col-12 col-lg-8 mb-2 mb-lg-0">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="header-icon me-2 me-sm-3 mb-1 mb-sm-0">
                            <i class="fas fa-tachometer-alt responsive-icon"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h1 class="responsive-heading mb-1"><?php echo e(Auth::user()->full_role_name); ?> Dashboard</h1>
                            <p class="mb-0 opacity-75 responsive-text">Welcome back, <strong class="name-text"><?php echo e(Auth::user()->name); ?></strong>!</p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-1 gap-sm-2 gap-md-3 mt-2">
                        <span class="badge bg-white text-primary responsive-badge">
                            <i class="fas fa-user me-1"></i> <span class="role-text"><?php echo e(Auth::user()->full_role_name); ?></span>
                        </span>
                        <span class="opacity-75 responsive-text-sm">
                            <i class="fas fa-calendar me-1"></i> <span class="date-text"><?php echo e(now()->format('l, F j, Y')); ?></span>
                        </span>
                        <?php if(Auth::user()->is_online): ?>
                            <span class="badge bg-success responsive-badge">
                                <i class="fas fa-circle me-1"></i> Online
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="d-flex flex-wrap gap-1 gap-sm-2 justify-content-start justify-content-lg-end">
                        <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline w-100 w-sm-auto">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-light responsive-btn w-100">
                                <i class="fas fa-sign-out-alt me-1 me-sm-2"></i>
                                <span class="btn-text">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Section - Responsive -->
    <div class="container-fluid px-3 px-sm-4 px-md-5 py-3 py-sm-4">
        <div class="row mb-3 mb-sm-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 p-sm-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8 mb-3 mb-lg-0">
                                <h3 class="responsive-subheading mb-2">
                                    <?php if(Auth::user()->isSystemAdmin()): ?>
                                        <i class="fas fa-shield-alt text-primary me-2"></i>System Administration & Analytics
                                    <?php elseif(Auth::user()->isMarketingAdmin()): ?>
                                        <i class="fas fa-chart-line text-success me-2"></i>Marketing Analytics & Customer Insights
                                    <?php elseif(Auth::user()->isTechnicalAdmin()): ?>
                                        <i class="fas fa-network-wired text-info me-2"></i>Technical Operations & Network Monitoring
                                    <?php elseif(Auth::user()->isFinance()): ?>
                                        <i class="fas fa-money-bill-wave text-warning me-2"></i>Financial Management & Reporting
                                    <?php elseif(Auth::user()->isDesigner()): ?>
                                        <i class="fas fa-pencil-ruler text-purple me-2"></i>Network Design & Quotation Center
                                    <?php elseif(Auth::user()->isSurveyor()): ?>
                                        <i class="fas fa-map-marked-alt text-danger me-2"></i>Field Survey Operations
                                    <?php elseif(Auth::user()->isTechnician()): ?>
                                        <i class="fas fa-tools text-secondary me-2"></i>Field Maintenance & Operations
                                    <?php elseif(Auth::user()->isAccountManager()): ?>
                                        <i class="fas fa-handshake text-info me-2"></i>Customer Relationship Management
                                        <?php elseif(Auth::user()->isDebtManager()): ?>
                                        <i class="fas fa-handshake text-info me-2"></i>Debt Management
                                    <?php else: ?>
                                        <i class="fas fa-user-circle text-primary me-2"></i>Customer Portal & Services
                                    <?php endif; ?>
                                </h3>
                                <p class="text-muted mb-3 responsive-text">
                                    Access your tools, view metrics, and manage your tasks from this centralized dashboard.
                                </p>

                                <?php if(Auth::user()->isCustomer()): ?>
                                <div class="profile-progress">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="responsive-text-sm">Profile Completion</span>
                                        <span class="responsive-text-sm fw-bold"><?php echo e(Auth::user()->profile_completion_percentage); ?>%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-<?php echo e(Auth::user()->profile_completion_percentage >= 80 ? 'success' : (Auth::user()->profile_completion_percentage >= 50 ? 'warning' : 'danger')); ?>"
                                             role="progressbar"
                                             style="width: <?php echo e(Auth::user()->profile_completion_percentage); ?>%">
                                        </div>
                                    </div>
                                    <?php if(Auth::user()->profile_completion_percentage < 100): ?>
                                        <small class="text-muted responsive-text-sm">Complete your profile to unlock all features</small>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-lg-4 text-center">
                                <div class="welcome-illustration">
                                    <i class="fas fa-network-wired responsive-empty-icon text-primary opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards - Dynamic Grid -->
        <div class="row g-2 g-sm-3 g-md-4 mb-3 mb-sm-4">
            <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(is_array($stat) && isset($stat['color']) && isset($stat['title']) && isset($stat['value'])): ?>
                <div class="col-6 col-md-4 col-lg-2 mb-2 mb-sm-3">
                    <div class="stat-card bg-white rounded-lg shadow-sm border-0 h-100">
                        <div class="stat-card-body p-2 p-sm-3">
                            <div class="d-flex justify-content-between align-items-start mb-2 mb-sm-3">
                                <div class="stat-icon bg-<?php echo e($stat['color']); ?>-light rounded-circle responsive-stat-icon">
                                    <i class="fas fa-<?php echo e($stat['icon'] ?? 'chart-bar'); ?> text-<?php echo e($stat['color']); ?>"></i>
                                </div>
                                <?php if(isset($stat['trend'])): ?>
                                <div class="trend-indicator">
                                    <span class="badge bg-<?php echo e($stat['trend']['color'] ?? 'muted'); ?> responsive-badge">
                                        <i class="fas fa-<?php echo e($stat['trend']['icon'] ?? 'chart-line'); ?> me-1"></i>
                                        <?php echo e($stat['trend']['value'] ?? 0); ?>%
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <h6 class="stat-title text-muted text-uppercase small mb-1 mb-sm-2"><?php echo e($stat['title']); ?></h6>
                            <div class="stat-value fw-bold mb-2 mb-sm-3 text-dark responsive-stat-value">
                                <?php if(in_array($key, ['total_revenue', 'revenue_this_month', 'pending_payments', 'overdue_payments', 'revenue_managed', 'average_deal_size', 'quoted_amount', 'monthly_revenue'])): ?>
                                    KSh <?php echo e(number_format($stat['value'], 2)); ?>

                                <?php elseif(in_array($key, ['conversion_rate', 'collection_rate', 'network_uptime', 'equipment_health', 'customer_growth_rate', 'satisfaction_score'])): ?>
                                    <?php echo e($stat['value']); ?>%
                                <?php else: ?>
                                    <?php echo e(number_format($stat['value'])); ?>

                                <?php endif; ?>
                            </div>
                            <?php if(isset($stat['subtitle'])): ?>
                            <div class="stat-subtitle responsive-text-sm text-muted"><?php echo e($stat['subtitle']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- Debug: Invalid stat format -->
                <div class="col-6 col-md-4 col-lg-2 mb-2 mb-sm-3">
                    <div class="stat-card bg-danger-light border-danger rounded-lg shadow-sm h-100">
                        <div class="stat-card-body p-2 p-sm-3">
                            <div class="d-flex justify-content-between align-items-start mb-2 mb-sm-3">
                                <div class="stat-icon bg-danger rounded-circle responsive-stat-icon">
                                    <i class="fas fa-exclamation-triangle text-white"></i>
                                </div>
                            </div>
                            <h6 class="stat-title text-danger text-uppercase small mb-1 mb-sm-2">Invalid Stat</h6>
                            <div class="stat-value h5 fw-bold mb-2 mb-sm-3 text-dark responsive-stat-value">Error in <?php echo e($key); ?></div>
                            <div class="text-danger responsive-text-sm">
                                Expected array, got: <?php echo e(gettype($stat)); ?>

                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Quick Actions Section - Dynamic Grid -->
        <div class="row mb-3 mb-sm-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-3 mb-sm-4">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 responsive-subheading">
                                <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                            </h5>
                            <span class="badge bg-warning responsive-badge d-none d-sm-inline"><?php echo e(Auth::user()->getQuickActionCount()); ?> Actions</span>
                        </div>
                    </div>
                    <div class="card-body p-2 p-sm-3 p-sm-4">
                        <?php
                            $quickActions = [];

                            // System Admin Actions
                            if (Auth::user()->isSystemAdmin()) {
                                $quickActions = [
                                    [
                                        'title' => 'Add User',
                                        'icon' => 'user-plus',
                                        'color' => 'primary',
                                        'link' => route('admin.users.create'),
                                        'desc' => 'Create new system user accounts'
                                    ],
                                    [
                                        'title' => 'System Settings',
                                        'icon' => 'cogs',
                                        'color' => 'success',
                                        'link' => route('admin.settings'),
                                        'desc' => 'Configure system parameters'
                                    ],
                                    [
                                        'title' => 'System Reports',
                                        'icon' => 'chart-bar',
                                        'color' => 'info',
                                        'link' => route('admin.reports'),
                                        'desc' => 'View analytics and insights'
                                    ],
                                    [
                                        'title' => 'Manage Users',
                                        'icon' => 'users',
                                        'color' => 'warning',
                                        'link' => route('admin.users'),
                                        'desc' => 'View and manage all users'
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
                                                'route' => route('conversion-data.summary'),
                                                'icon' => 'chart-bar'
                                            ],
                                            [
                                                'label' => 'Add New',
                                                'route' => route('conversion-data.create'),
                                                'icon' => 'plus'
                                            ]
                                        ]
                                    ]
                                ];
                            }
                            // Technical Admin Actions
                            elseif (Auth::user()->isTechnicalAdmin()) {
                                $quickActions = [
                                    [
                                        'title' => 'Manage Leases',
                                        'icon' => 'network-wired',
                                        'color' => 'primary',
                                        'link' => route('admin.leases.index'),
                                        'desc' => 'View and manage all leases'
                                    ],
                                    [
                                        'title' => 'Design Requests',
                                        'icon' => 'pencil-ruler',
                                        'color' => 'success',
                                        'link' => route('admin.design-requests.index'),
                                        'desc' => 'Handle design requests'
                                    ],
                                    [
                                        'title' => 'Quotations',
                                        'icon' => 'file-invoice',
                                        'color' => 'info',
                                        'link' => route('admin.quotations.index'),
                                        'desc' => 'Manage all quotations'
                                    ],
                                    [
                                        'title' => 'Manage Users',
                                        'icon' => 'users',
                                        'color' => 'warning',
                                        'link' => route('admin.users'),
                                        'desc' => 'View and manage all users'
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
                                                'route' => route('conversion-data.summary'),
                                                'icon' => 'chart-bar'
                                            ],
                                            [
                                                'label' => 'Add New',
                                                'route' => route('conversion-data.create'),
                                                'icon' => 'plus'
                                            ]
                                        ]
                                    ]
                                ];
                            }
                            // Account Manager Actions
                            elseif (Auth::user()->isAccountManager()) {
                                $quickActions = [
                                    [
                                        'title' => 'My Customers',
                                        'icon' => 'users',
                                        'color' => 'primary',
                                        'link' => route('admin.customers.assign'),
                                        'desc' => 'Manage assigned customers'
                                    ],
                                    [
                                        'title' => 'New Lease',
                                        'icon' => 'plus-circle',
                                        'color' => 'success',
                                        'link' => route('admin.leases.create'),
                                        'desc' => 'Create new lease agreements'
                                    ],
                                    [
                                        'title' => 'Support Tickets',
                                        'icon' => 'ticket-alt',
                                        'color' => 'info',
                                        'link' => route('admin.design-requests.index'),
                                        'desc' => 'Handle customer support'
                                    ],
                                    [
                                        'title' => 'Payments',
                                        'icon' => 'credit-card',
                                        'color' => 'warning',
                                        'link' => route('admin.payments.index'),
                                        'desc' => 'Manage payments and invoices'
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
                                                'route' => route('conversion-data.summary'),
                                                'icon' => 'chart-bar'
                                            ],
                                            [
                                                'label' => 'Add New',
                                                'route' => route('conversion-data.create'),
                                                'icon' => 'plus'
                                            ]
                                        ]
                                    ]
                                ];
                            }
                            // Default Admin Actions
                            else {
                                $quickActions = [
                                    [
                                        'title' => 'Manage Leases',
                                        'icon' => 'network-wired',
                                        'color' => 'primary',
                                        'link' => route('admin.leases.index'),
                                        'desc' => 'Approve, view, and send leases'
                                    ],
                                    [
                                        'title' => 'Manage Users',
                                        'icon' => 'users',
                                        'color' => 'success',
                                        'link' => route('admin.users'),
                                        'desc' => 'User management'
                                    ],
                                    [
                                        'title' => 'Design Requests',
                                        'icon' => 'pencil-ruler',
                                        'color' => 'info',
                                        'link' => route('admin.design-requests.index'),
                                        'desc' => 'Assign engineers & view requests'
                                    ],
                                    [
                                        'title' => 'Quotations',
                                        'icon' => 'file-invoice',
                                        'color' => 'warning',
                                        'link' => route('admin.quotations.index'),
                                        'desc' => 'Approve and send quotations'
                                    ],
                                    [
                                        'title' => 'Manage Contracts',
                                        'icon' => 'file-contract',
                                        'color' => 'purple',
                                        'link' => route('admin.contracts.index'),
                                        'desc' => 'Approve, view, and send contracts'
                                    ],
                                    [
                                        'title' => 'Customer Listing',
                                        'icon' => 'users',
                                        'color' => 'dark',
                                        'link' => route('admin.customers.index'),
                                        'desc' => 'View profiles & assign managers'
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
                                    ]
                                ];
                            }
                        ?>

                        <div class="row g-2 g-sm-3 g-md-4">
                            <?php $__currentLoopData = $quickActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2 col-xxl-2">
                                <?php if(isset($action['links'])): ?>
                                    
                                    <div class="action-card">
                                        <div class="action-icon bg-<?php echo e($action['color']); ?>">
                                            <i class="fas fa-<?php echo e($action['icon']); ?>"></i>
                                        </div>
                                        <div class="action-content">
                                            <h6 class="responsive-text"><?php echo e($action['title']); ?></h6>
                                            <p class="text-muted small mb-2"><?php echo e($action['desc']); ?></p>

                                            
                                            <div class="action-links mt-2">
                                                <?php $__currentLoopData = $action['links']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <a href="<?php echo e($link['route']); ?>" class="btn btn-sm btn-outline-<?php echo e($action['color']); ?> w-100 mb-1">
                                                        <i class="fas fa-<?php echo e($link['icon']); ?> me-1"></i> <?php echo e($link['label']); ?>

                                                    </a>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    
                                    <a href="<?php echo e($action['link']); ?>" class="action-card">
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

        <!-- Main Content Area - Responsive Grid -->
        <div class="row g-3 g-sm-4">
            <!-- Left Column: Recent Activity & Items -->
            <div class="col-12 col-lg-6">
                <!-- Recent Activity -->
                <?php if(isset($recentActivities) && count($recentActivities) > 0): ?>
                <div class="card border-0 shadow-sm mb-3 mb-sm-4">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="mb-0 responsive-subheading">
                                <i class="fas fa-clock text-primary me-2"></i>Recent Activity
                            </h5>
                            <a href="#" class="btn btn-sm btn-outline-primary responsive-btn mt-1 mt-sm-0">
                                <span class="d-none d-sm-inline">View All</span>
                                <span class="d-inline d-sm-none">All</span>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="activity-timeline p-2 p-sm-3">
                            <?php $__currentLoopData = $recentActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="timeline-item mb-2 mb-sm-3">
                                <div class="timeline-marker bg-<?php echo e($activity['color']); ?>">
                                    <i class="fas fa-<?php echo e($activity['icon']); ?> responsive-icon-sm"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-text responsive-text"><?php echo $activity['text']; ?></div>
                                    <div class="timeline-time text-muted responsive-text-sm"><?php echo e($activity['time']); ?></div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Recent Items -->
                <?php if(isset($recentItems) && $recentItems->count() > 0): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="mb-0 responsive-subheading">
                                <i class="fas fa-list text-<?php echo e(Auth::user()->getRoleBadgeColor()); ?> me-2"></i><?php echo e($recentItemsTitle); ?>

                            </h5>
                            <a href="<?php echo e($recentItemsLink); ?>" class="btn btn-sm btn-outline-<?php echo e(Auth::user()->getRoleBadgeColor()); ?> responsive-btn mt-1 mt-sm-0">
                                <span class="d-none d-sm-inline">View All</span>
                                <span class="d-inline d-sm-none">All</span>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <?php $__currentLoopData = $recentItemsColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th class="border-0 responsive-table-header"><?php echo e($column); ?></th>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $recentItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="border-bottom">
                                        <?php $__currentLoopData = $recentItemsColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $columnKey => $columnName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="responsive-text-sm">
                                            <?php if($columnKey === 'status'): ?>
                                                <span class="badge bg-<?php echo e($item->getStatusColor()); ?> responsive-badge">
                                                    <?php echo e(ucfirst($item->status)); ?>

                                                </span>
                                            <?php elseif($columnKey === 'amount'): ?>
                                                <span class="fw-bold">KSh <?php echo e(number_format($item->amount, 2)); ?></span>
                                            <?php elseif($columnKey === 'created_at'): ?>
                                                <span class="text-muted"><?php echo e($item->created_at->format('M d, Y')); ?></span>
                                            <?php else: ?>
                                                <?php echo e($item->{$columnKey} ?? 'N/A'); ?>

                                            <?php endif; ?>
                                        </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: System Status & Notifications -->
            <div class="col-12 col-lg-6">
                <!-- System Health -->
                <?php if(isset($systemHealth) && count($systemHealth) > 0): ?>
                <div class="card border-0 shadow-sm mb-3 mb-sm-4">
                    <div class="card-header bg-<?php echo e(Auth::user()->getRoleBadgeColor()); ?> text-white border-0 py-2 py-sm-3">
                        <h5 class="mb-0 responsive-subheading">
                            <i class="fas fa-heartbeat me-2"></i>System Status
                        </h5>
                    </div>
                    <div class="card-body p-3 p-sm-4">
                        <?php $__currentLoopData = $systemHealth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $healthItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="system-health-item mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold responsive-text"><?php echo e($healthItem['label']); ?></span>
                                <span class="badge bg-<?php echo e($healthItem['status_color']); ?> responsive-badge">
                                    <?php echo e($healthItem['status']); ?>

                                </span>
                            </div>
                            <?php if(isset($healthItem['value'])): ?>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-<?php echo e($healthItem['status_color']); ?>"
                                     role="progressbar"
                                     style="width: <?php echo e($healthItem['value']); ?>%">
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Notifications & Alerts -->
                <div class="card border-0 shadow-sm mb-3 mb-sm-4">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <h5 class="mb-0 responsive-subheading">
                            <i class="fas fa-bell text-warning me-2"></i>Notifications & Alerts
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if(isset($notifications) && count($notifications) > 0): ?>
                            <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="alert alert-<?php echo e($notification['type']); ?> alert-dismissible fade show m-3 rounded">
                                <div class="d-flex flex-column flex-sm-row">
                                    <div class="alert-icon me-0 me-sm-3 mb-2 mb-sm-0">
                                        <i class="fas fa-<?php echo e($notification['icon']); ?> responsive-icon-sm"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="responsive-text"><?php echo $notification['message']; ?></div>
                                    </div>
                                    <button type="button" class="btn-close mt-2 mt-sm-0 align-self-start" data-bs-dismiss="alert"></button>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <div class="text-center py-4 py-sm-5">
                                <i class="fas fa-check-circle text-success responsive-empty-icon mb-3"></i>
                                <p class="text-muted mb-0 responsive-text">All systems are operating normally</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <?php if(isset($performanceMetrics) && count($performanceMetrics) > 0): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <h5 class="mb-0 responsive-subheading">
                            <i class="fas fa-chart-line text-info me-2"></i>Performance Metrics
                        </h5>
                    </div>
                    <div class="card-body p-3 p-sm-4">
                        <?php $__currentLoopData = $performanceMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="metric-item mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="responsive-text-sm"><?php echo e($metric['label']); ?></span>
                                <span class="fw-bold responsive-text"><?php echo e($metric['value']); ?><?php echo e($metric['unit'] ?? ''); ?></span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-<?php echo e($metric['color']); ?>"
                                     role="progressbar"
                                     style="width: <?php echo e($metric['percentage']); ?>%">
                                </div>
                            </div>
                            <small class="text-muted responsive-text-sm">Target: <?php echo e($metric['target']); ?><?php echo e($metric['unit'] ?? ''); ?></small>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Charts Section -->
        <?php if(isset($charts) && count($charts) > 0): ?>
        <div class="row g-3 g-sm-4 mt-3 mt-sm-4">
            <?php $__currentLoopData = $charts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-2 py-sm-3">
                        <h5 class="mb-0 responsive-subheading">
                            <i class="fas fa-chart-<?php echo e($chart['type']); ?> text-primary me-2"></i><?php echo e($chart['title']); ?>

                        </h5>
                    </div>
                    <div class="card-body p-2 p-sm-3">
                        <div class="chart-container">
                            <canvas id="<?php echo e($chart['id']); ?>" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        <!-- Additional Sections -->
        <?php if(isset($additionalSections) && count($additionalSections) > 0): ?>
        <div class="row g-3 g-sm-4 mt-3 mt-sm-4">
            <?php $__currentLoopData = $additionalSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-12 col-lg-<?php echo e($section['size'] ?? 6); ?>">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-<?php echo e($section['color'] ?? 'primary'); ?> text-white border-0 py-2 py-sm-3">
                        <h5 class="mb-0 responsive-subheading">
                            <i class="fas fa-<?php echo e($section['icon']); ?> me-2"></i><?php echo e($section['title']); ?>

                        </h5>
                    </div>
                    <div class="card-body p-3 p-sm-4">
                        <div class="responsive-text"><?php echo $section['content']; ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php if(isset($charts)): ?>
    document.addEventListener('DOMContentLoaded', function() {
        <?php $__currentLoopData = $charts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        var ctx = document.getElementById('<?php echo e($chart['id']); ?>');
        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: '<?php echo e($chart['type']); ?>',
                data: {
                    labels: <?php echo json_encode($chart['labels']); ?>,
                    datasets: [{
                        label: '<?php echo e($chart['dataset']['label']); ?>',
                        data: <?php echo json_encode($chart['dataset']['data']); ?>,
                        backgroundColor: '<?php echo e($chart['dataset']['backgroundColor']); ?>',
                        borderColor: '<?php echo e($chart['dataset']['borderColor']); ?>',
                        borderWidth: 2,
                        fill: <?php echo e($chart['dataset']['fill'] ?? 'true'); ?>,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    });
<?php endif; ?>
</script>

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
    font-size: clamp(1.25rem, 3.5vw, 1.75rem);
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
}

.action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #4e73df;
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

/* Activity Timeline */
.activity-timeline {
    position: relative;
}

.timeline-item {
    display: flex;
    padding-bottom: clamp(1rem, 2vw, 1.5rem);
    position: relative;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    width: clamp(2rem, 4vw, 2.5rem);
    height: clamp(2rem, 4vw, 2.5rem);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    margin-right: clamp(0.75rem, 1.5vw, 1rem);
}

.timeline-content {
    flex-grow: 1;
    padding-top: clamp(0.25rem, 0.5vw, 0.5rem);
}

.timeline-text {
    margin-bottom: 0.25rem;
    color: #2d3748;
}

/* Table Responsive */
.responsive-table-header {
    font-size: clamp(0.7rem, 1.5vw, 0.8rem);
    padding: clamp(0.75rem, 1.5vw, 1rem) clamp(0.5rem, 1vw, 0.75rem);
}

.table td {
    padding: clamp(0.75rem, 1.5vw, 1rem) clamp(0.5rem, 1vw, 0.75rem);
    vertical-align: middle;
}

/* Chart Container */
.chart-container {
    position: relative;
    height: clamp(180px, 30vw, 250px);
    width: 100%;
}

/* Color Classes */
.bg-primary-light { background-color: rgba(78, 115, 223, 0.1); }
.bg-success-light { background-color: rgba(28, 200, 138, 0.1); }
.bg-info-light { background-color: rgba(54, 185, 204, 0.1); }
.bg-warning-light { background-color: rgba(246, 194, 62, 0.1); }
.bg-danger-light { background-color: rgba(231, 74, 59, 0.1); }
.bg-purple-light { background-color: rgba(111, 66, 193, 0.1); }
.bg-dark-light { background-color: rgba(52, 58, 64, 0.1); }

.bg-purple { background-color: #6f42c1 !important; }

/* Touch Device Optimization */
@media (hover: none) and (pointer: coarse) {
    .stat-card:hover,
    .action-card:hover {
        transform: none;
    }

    .btn, .action-card {
        min-height: 44px;
    }

    .btn-sm {
        min-height: 36px;
    }

    /* Increase tap target sizes */
    .responsive-badge {
        padding: 0.5em 0.75em;
    }

    .table td {
        padding: 0.75rem 0.5rem;
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

    .chart-container {
        height: 150px;
    }
}

/* Print Styles */
@media print {
    .dashboard-header,
    .action-card,
    .btn,
    .badge {
        display: none !important;
    }

    .stat-card,
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }

    .chart-container {
        height: 200px;
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

    .responsive-table-header {
        font-size: 0.65rem;
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
}

/* Alert adjustments for mobile */
@media (max-width: 767.98px) {
    .alert {
        margin: 0.5rem;
    }

    .alert .d-flex {
        flex-direction: column;
    }

    .alert-icon {
        margin-bottom: 0.5rem;
        text-align: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap components
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Auto-dismiss alerts after 10 seconds
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

    // Update date and role format based on screen size
    function updateTextDisplay() {
        const dateElements = document.querySelectorAll('.date-text');
        const roleElements = document.querySelectorAll('.role-text');
        const nameElements = document.querySelectorAll('.name-text');
        const now = new Date();

        dateElements.forEach(el => {
            if (window.innerWidth < 576) {
                el.textContent = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            } else if (window.innerWidth < 768) {
                el.textContent = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            } else if (window.innerWidth < 992) {
                el.textContent = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
            } else {
                el.textContent = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
            }
        });

        roleElements.forEach(el => {
            const role = el.textContent;
            if (window.innerWidth < 576) {
                // Shorten role name on very small screens
                el.textContent = role.replace('Administrator', 'Admin').replace('Manager', 'Mgr').replace('Technician', 'Tech');
            } else if (window.innerWidth < 768) {
                // Keep reasonable length
                el.textContent = role.length > 20 ? role.substring(0, 18) + '...' : role;
            }
        });

        nameElements.forEach(el => {
            const name = el.textContent;
            if (window.innerWidth < 400) {
                // Show only first name on very small screens
                el.textContent = name.split(' ')[0];
            } else if (window.innerWidth < 576) {
                // Truncate long names
                el.textContent = name.length > 15 ? name.substring(0, 13) + '...' : name;
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
                if (text === 'Logout') el.textContent = 'Logout';
                if (text === 'View All') el.textContent = 'All';
            } else {
                // Restore full text on larger screens
                if (text === 'All') el.textContent = 'View All';
            }
        });
    }

    // Optimize for touch devices
    if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
        document.body.classList.add('touch-device');

        // Increase touch targets
        document.querySelectorAll('.btn, .action-card, .badge').forEach(el => {
            if (el.classList.contains('btn') || el.classList.contains('action-card')) {
                el.style.minHeight = '44px';
            }
        });

        // Make table rows more tappable on mobile
        if (window.innerWidth < 768 && document.querySelector('.table tbody')) {
            document.querySelectorAll('.table tbody tr').forEach(row => {
                row.style.cursor = 'pointer';
                const firstLink = row.querySelector('a');
                if (firstLink) {
                    row.addEventListener('click', function(e) {
                        if (!e.target.closest('a') && !e.target.closest('button') && !e.target.closest('.badge')) {
                            window.location = firstLink.href;
                        }
                    });
                }
            });
        }
    }

    // Prevent horizontal scroll
    document.body.style.overflowX = 'hidden';
    document.documentElement.style.overflowX = 'hidden';

    // Initialize and update on resize
    updateScaleFactor();
    updateTextDisplay();
    updateButtonText();

    // Debounced resize handler
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            updateScaleFactor();
            updateTextDisplay();
            updateButtonText();
        }, 100);
    });

    // Handle orientation changes
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            updateScaleFactor();
            updateTextDisplay();
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
    document.querySelectorAll('.stat-card, .action-card, .card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });

    // Auto-refresh dashboard every 5 minutes
    const refreshInterval = 5 * 60 * 1000; // 5 minutes
    let refreshTimer = setTimeout(function() {
        window.location.reload();
    }, refreshInterval);

    // Reset timer on user activity
    ['click', 'scroll', 'keypress', 'mousemove'].forEach(event => {
        document.addEventListener(event, function() {
            clearTimeout(refreshTimer);
            refreshTimer = setTimeout(function() {
                window.location.reload();
            }, refreshInterval);
        }, { passive: true });
    });

    // Handle chart resizing on window resize
    let chartResizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(chartResizeTimer);
        chartResizeTimer = setTimeout(function() {
            // Trigger Chart.js resize
            if (window.Chart && window.Chart.instances) {
                Object.values(window.Chart.instances).forEach(chart => {
                    chart.resize();
                });
            }
        }, 250);
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\project\darkfibre-crm\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>