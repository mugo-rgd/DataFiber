<?php $__env->startSection('title', 'Financial Reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Financial Reports & Analytics
                    </h5>
<div class="btn-group">
    <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
        <i class="fas fa-print me-1"></i>Print Report
    </button>
    <a href="<?php echo e(request()->fullUrlWithQuery(['export' => 'csv'])); ?>" class="btn btn-outline-primary btn-sm">
    <i class="fas fa-download me-1"></i>Export Data
</a>
</div>
                </div>
                <div class="card-body">
                    <!-- Report Filters -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="<?php echo e(route('finance.reports')); ?>" class="row g-3">
                                <div class="col-md-3">
                                    <label for="report_type" class="form-label">Report Type</label>
                                    <select name="report_type" class="form-select" id="report_type" onchange="this.form.submit()">
                                        <option value="financial_summary" <?php echo e($reportType == 'financial_summary' ? 'selected' : ''); ?>>Financial Summary</option>
                                        <option value="revenue_analysis" <?php echo e($reportType == 'revenue_analysis' ? 'selected' : ''); ?>>Revenue Analysis</option>
                                        <option value="customer_billing" <?php echo e($reportType == 'customer_billing' ? 'selected' : ''); ?>>Customer Billing</option>
                                        <option value="aging_report" <?php echo e($reportType == 'aging_report' ? 'selected' : ''); ?>>Aging Report</option>
                                        <option value="debt_aging" <?php echo e($reportType == 'debt_aging' ? 'selected' : ''); ?>>Debt Aging Analysis</option>
                                        <option value="cash_flow" <?php echo e($reportType == 'cash_flow' ? 'selected' : ''); ?>>Cash Flow Statement</option>
                                        <option value="profitability" <?php echo e($reportType == 'profitability' ? 'selected' : ''); ?>>Profitability Analysis</option>
                                        <option value="tax_report" <?php echo e($reportType == 'tax_report' ? 'selected' : ''); ?>>Tax Report</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="period" class="form-label">Period</label>
                                    <select name="period" class="form-select" id="period" onchange="this.form.submit()">
                                        <option value="today" <?php echo e($period == 'today' ? 'selected' : ''); ?>>Today</option>
                                        <option value="this_week" <?php echo e($period == 'this_week' ? 'selected' : ''); ?>>This Week</option>
                                        <option value="this_month" <?php echo e($period == 'this_month' ? 'selected' : ''); ?>>This Month</option>
                                        <option value="last_month" <?php echo e($period == 'last_month' ? 'selected' : ''); ?>>Last Month</option>
                                        <option value="this_quarter" <?php echo e($period == 'this_quarter' ? 'selected' : ''); ?>>This Quarter</option>
                                        <option value="this_year" <?php echo e($period == 'this_year' ? 'selected' : ''); ?>>This Year</option>
                                        <option value="last_year" <?php echo e($period == 'last_year' ? 'selected' : ''); ?>>Last Year</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control"
                                           value="<?php echo e($startDate); ?>" id="start_date">
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control"
                                           value="<?php echo e($endDate); ?>" id="end_date">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Report Period -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Showing report for period:
                                <strong><?php echo e(\Carbon\Carbon::parse($startDate)->format('M j, Y')); ?></strong> to
                                <strong><?php echo e(\Carbon\Carbon::parse($endDate)->format('M j, Y')); ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Summary Report with Both Currencies -->
                    <?php if($reportType === 'financial_summary'): ?>
                        <!-- KSH Summary -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <span class="badge bg-primary me-2">KSH</span> Kenyan Shilling Summary
                                </h5>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Revenue (KSH)</h6>
                                        <h3 class="mb-0">KSH <?php echo e(number_format($reportData['total_revenue_ksh'] ?? 0, 2)); ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Pending Invoices (KSH)</h6>
                                        <h3 class="mb-0"><?php echo e($reportData['pending_invoices_ksh'] ?? 0); ?></h3>
                                        <small>KSH <?php echo e(number_format($reportData['pending_amount_ksh'] ?? 0, 2)); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Overdue Invoices (KSH)</h6>
                                        <h3 class="mb-0"><?php echo e($reportData['overdue_invoices_ksh'] ?? 0); ?></h3>
                                        <small>KSH <?php echo e(number_format($reportData['overdue_amount_ksh'] ?? 0, 2)); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Avg. Invoice Value (KSH)</h6>
                                        <h3 class="mb-0">
                                            <?php
                                                $pendingInvoicesKsh = $reportData['pending_invoices_ksh'] ?? 0;
                                                $pendingAmountKsh = $reportData['pending_amount_ksh'] ?? 0;
                                                $avgValueKsh = $pendingInvoicesKsh > 0 ? $pendingAmountKsh / $pendingInvoicesKsh : 0;
                                            ?>
                                            KSH <?php echo e(number_format($avgValueKsh, 2)); ?>

                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- USD Summary -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <span class="badge bg-secondary me-2">USD</span> US Dollar Summary
                                </h5>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Revenue (USD)</h6>
                                        <h3 class="mb-0">$ <?php echo e(number_format($reportData['total_revenue_usd'] ?? 0, 2)); ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Pending Invoices (USD)</h6>
                                        <h3 class="mb-0"><?php echo e($reportData['pending_invoices_usd'] ?? 0); ?></h3>
                                        <small>$ <?php echo e(number_format($reportData['pending_amount_usd'] ?? 0, 2)); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Overdue Invoices (USD)</h6>
                                        <h3 class="mb-0"><?php echo e($reportData['overdue_invoices_usd'] ?? 0); ?></h3>
                                        <small>$ <?php echo e(number_format($reportData['overdue_amount_usd'] ?? 0, 2)); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Avg. Invoice Value (USD)</h6>
                                        <h3 class="mb-0">
                                            <?php
                                                $pendingInvoicesUsd = $reportData['pending_invoices_usd'] ?? 0;
                                                $pendingAmountUsd = $reportData['pending_amount_usd'] ?? 0;
                                                $avgValueUsd = $pendingInvoicesUsd > 0 ? $pendingAmountUsd / $pendingInvoicesUsd : 0;
                                            ?>
                                            $ <?php echo e(number_format($avgValueUsd, 2)); ?>

                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Currency Distribution -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Revenue by Currency</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php $__currentLoopData = ($reportData['revenue_by_currency'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between">
                                                            <span class="fw-bold"><?php echo e(strtoupper($currency->currency)); ?></span>
                                                            <span class="fw-bold"><?php echo e(strtoupper($currency->currency) == 'KSH' ? 'KSH' : '$'); ?> <?php echo e(number_format($currency->total_revenue ?? 0, 2)); ?></span>
                                                        </div>
                                                        <div class="progress" style="height: 8px;">
                                                            <?php
                                                                $totalAllRevenue = ($reportData['total_revenue_ksh'] ?? 0) + ($reportData['total_revenue_usd'] ?? 0);
                                                                $currencyRevenue = $currency->total_revenue ?? 0;
                                                                $width = $totalAllRevenue > 0 ? ($currencyRevenue / $totalAllRevenue) * 100 : 0;
                                                            ?>
                                                            <div class="progress-bar bg-<?php echo e($currency->currency == 'ksh' ? 'primary' : 'secondary'); ?>" style="width: <?php echo e($width); ?>%"></div>
                                                        </div>
                                                        <small class="text-muted"><?php echo e($currency->invoice_count ?? 0); ?> invoices | Avg: <?php echo e(strtoupper($currency->currency) == 'KSH' ? 'KSH' : '$'); ?> <?php echo e(number_format($currency->avg_invoice_amount ?? 0, 2)); ?></small>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Revenue by Type - Split by Currency -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Revenue by Service Type (KSH)</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php $__empty_1 = true; $__currentLoopData = ($reportData['revenue_by_type_ksh'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $revenue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-capitalize"><?php echo e($revenue->billing_cycle ?? 'unknown'); ?></span>
                                                    <span class="fw-bold">KSH <?php echo e(number_format($revenue->revenue ?? 0, 2)); ?></span>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <?php
                                                        $totalRevenueKsh = $reportData['total_revenue_ksh'] ?? 1;
                                                        $revenueAmount = $revenue->revenue ?? 0;
                                                        $width = $totalRevenueKsh > 0 ? ($revenueAmount / $totalRevenueKsh) * 100 : 0;
                                                    ?>
                                                    <div class="progress-bar" style="width: <?php echo e($width); ?>%"></div>
                                                </div>
                                                <small class="text-muted"><?php echo e($revenue->count ?? 0); ?> invoices</small>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <p class="text-muted">No KSH revenue data available.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Revenue by Service Type (USD)</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php $__empty_1 = true; $__currentLoopData = ($reportData['revenue_by_type_usd'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $revenue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-capitalize"><?php echo e($revenue->billing_cycle ?? 'unknown'); ?></span>
                                                    <span class="fw-bold">$ <?php echo e(number_format($revenue->revenue ?? 0, 2)); ?></span>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <?php
                                                        $totalRevenueUsd = $reportData['total_revenue_usd'] ?? 1;
                                                        $revenueAmount = $revenue->revenue ?? 0;
                                                        $width = $totalRevenueUsd > 0 ? ($revenueAmount / $totalRevenueUsd) * 100 : 0;
                                                    ?>
                                                    <div class="progress-bar" style="width: <?php echo e($width); ?>%"></div>
                                                </div>
                                                <small class="text-muted"><?php echo e($revenue->count ?? 0); ?> invoices</small>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <p class="text-muted">No USD revenue data available.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Monthly Revenue Trend (KSH)</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php $__empty_1 = true; $__currentLoopData = ($reportData['monthly_trend_ksh'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between">
                                                    <span><?php echo e(date('F Y', mktime(0, 0, 0, $trend->month ?? 1, 1, $trend->year ?? date('Y')))); ?></span>
                                                    <span class="fw-bold">KSH <?php echo e(number_format($trend->monthly_revenue ?? 0, 2)); ?></span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <?php
                                                        $monthlyTrendKsh = $reportData['monthly_trend_ksh'] ?? collect();
                                                        $maxRevenueKsh = $monthlyTrendKsh->max('monthly_revenue') ?? 1;
                                                        $trendRevenue = $trend->monthly_revenue ?? 0;
                                                        $width = $maxRevenueKsh > 0 ? ($trendRevenue / $maxRevenueKsh) * 100 : 0;
                                                    ?>
                                                    <div class="progress-bar bg-success" style="width: <?php echo e($width); ?>%"></div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <p class="text-muted">No KSH trend data available.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Monthly Revenue Trend (USD)</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php $__empty_1 = true; $__currentLoopData = ($reportData['monthly_trend_usd'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between">
                                                    <span><?php echo e(date('F Y', mktime(0, 0, 0, $trend->month ?? 1, 1, $trend->year ?? date('Y')))); ?></span>
                                                    <span class="fw-bold">$ <?php echo e(number_format($trend->monthly_revenue ?? 0, 2)); ?></span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <?php
                                                        $monthlyTrendUsd = $reportData['monthly_trend_usd'] ?? collect();
                                                        $maxRevenueUsd = $monthlyTrendUsd->max('monthly_revenue') ?? 1;
                                                        $trendRevenue = $trend->monthly_revenue ?? 0;
                                                        $width = $maxRevenueUsd > 0 ? ($trendRevenue / $maxRevenueUsd) * 100 : 0;
                                                    ?>
                                                    <div class="progress-bar bg-success" style="width: <?php echo e($width); ?>%"></div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <p class="text-muted">No USD trend data available.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Customers -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Top Customers (KSH)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Customer</th>
                                                        <th>Total Spent</th>
                                                        <th>Invoices</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__empty_1 = true; $__currentLoopData = ($reportData['top_customers_ksh'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                        <tr>
                                                            <td><?php echo e($customer->name); ?></td>
                                                            <td>KSH <?php echo e(number_format($customer->total_spent, 2)); ?></td>
                                                            <td><?php echo e($customer->invoices_count); ?></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted">No data available</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Top Customers (USD)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Customer</th>
                                                        <th>Total Spent</th>
                                                        <th>Invoices</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__empty_1 = true; $__currentLoopData = ($reportData['top_customers_usd'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                        <tr>
                                                            <td><?php echo e($customer->name); ?></td>
                                                            <td>$ <?php echo e(number_format($customer->total_spent, 2)); ?></td>
                                                            <td><?php echo e($customer->invoices_count); ?></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted">No data available</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Most Delayed Invoices -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Most Delayed Invoices (KSH)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Invoice #</th>
                                                        <th>Customer</th>
                                                        <th>Amount</th>
                                                        <th>Days Late</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__empty_1 = true; $__currentLoopData = ($reportData['most_delayed_invoices_ksh'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                        <tr>
                                                            <td><?php echo e($invoice->billing_number); ?></td>
                                                            <td><?php echo e($invoice->customer_name); ?></td>
                                                            <td>KSH <?php echo e(number_format($invoice->total_amount, 2)); ?></td>
                                                            <td><span class="badge bg-danger"><?php echo e($invoice->days_late); ?> days</span></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted">No delayed KSH invoices</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Most Delayed Invoices (USD)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Invoice #</th>
                                                        <th>Customer</th>
                                                        <th>Amount</th>
                                                        <th>Days Late</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__empty_1 = true; $__currentLoopData = ($reportData['most_delayed_invoices_usd'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                        <tr>
                                                            <td><?php echo e($invoice->billing_number); ?></td>
                                                            <td><?php echo e($invoice->customer_name); ?></td>
                                                            <td>$ <?php echo e(number_format($invoice->total_amount, 2)); ?></td>
                                                            <td><span class="badge bg-danger"><?php echo e($invoice->days_late); ?> days</span></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted">No delayed USD invoices</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Due Dates -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Upcoming Due Dates (KSH) - Next 7 Days</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Invoice #</th>
                                                        <th>Customer</th>
                                                        <th>Amount</th>
                                                        <th>Due Date</th>
                                                        <th>Days Left</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__empty_1 = true; $__currentLoopData = ($reportData['upcoming_due_dates_ksh'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                        <tr>
                                                            <td><?php echo e($invoice->billing_number); ?></td>
                                                            <td><?php echo e($invoice->customer_name); ?></td>
                                                            <td>KSH <?php echo e(number_format($invoice->total_amount, 2)); ?></td>
                                                            <td><?php echo e(\Carbon\Carbon::parse($invoice->due_date)->format('M j, Y')); ?></td>
                                                            <td><span class="badge bg-warning"><?php echo e($invoice->days_until_due); ?> days</span></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">No upcoming KSH invoices</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Upcoming Due Dates (USD) - Next 7 Days</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Invoice #</th>
                                                        <th>Customer</th>
                                                        <th>Amount</th>
                                                        <th>Due Date</th>
                                                        <th>Days Left</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__empty_1 = true; $__currentLoopData = ($reportData['upcoming_due_dates_usd'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                        <tr>
                                                            <td><?php echo e($invoice->billing_number); ?></td>
                                                            <td><?php echo e($invoice->customer_name); ?></td>
                                                            <td>$ <?php echo e(number_format($invoice->total_amount, 2)); ?></td>
                                                            <td><?php echo e(\Carbon\Carbon::parse($invoice->due_date)->format('M j, Y')); ?></td>
                                                            <td><span class="badge bg-warning"><?php echo e($invoice->days_until_due); ?> days</span></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">No upcoming USD invoices</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Revenue Analysis Report with Both Currencies -->
                    <?php elseif($reportType === 'revenue_analysis'): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Top Customers by Revenue (KSH)</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php $__empty_1 = true; $__currentLoopData = ($reportData['revenue_by_customer_ksh'] ?? collect())->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span><?php echo e($customer->customer_name ?? 'Unknown Customer'); ?></span>
                                                    <span class="fw-bold">KSH <?php echo e(number_format($customer->revenue ?? 0, 2)); ?></span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <?php
                                                        $revenueByCustomerKsh = $reportData['revenue_by_customer_ksh'] ?? collect();
                                                        $maxRevenueKsh = $revenueByCustomerKsh->max('revenue') ?? 1;
                                                        $customerRevenueKsh = $customer->revenue ?? 0;
                                                        $width = $maxRevenueKsh > 0 ? ($customerRevenueKsh / $maxRevenueKsh) * 100 : 0;
                                                    ?>
                                                    <div class="progress-bar bg-primary" style="width: <?php echo e($width); ?>%"></div>
                                                </div>
                                                <small class="text-muted"><?php echo e($customer->invoice_count ?? 0); ?> invoices</small>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <p class="text-muted">No KSH revenue data available for this period.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Top Customers by Revenue (USD)</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php $__empty_1 = true; $__currentLoopData = ($reportData['revenue_by_customer_usd'] ?? collect())->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span><?php echo e($customer->customer_name ?? 'Unknown Customer'); ?></span>
                                                    <span class="fw-bold">$ <?php echo e(number_format($customer->revenue ?? 0, 2)); ?></span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <?php
                                                        $revenueByCustomerUsd = $reportData['revenue_by_customer_usd'] ?? collect();
                                                        $maxRevenueUsd = $revenueByCustomerUsd->max('revenue') ?? 1;
                                                        $customerRevenueUsd = $customer->revenue ?? 0;
                                                        $width = $maxRevenueUsd > 0 ? ($customerRevenueUsd / $maxRevenueUsd) * 100 : 0;
                                                    ?>
                                                    <div class="progress-bar bg-primary" style="width: <?php echo e($width); ?>%"></div>
                                                </div>
                                                <small class="text-muted"><?php echo e($customer->invoice_count ?? 0); ?> invoices</small>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <p class="text-muted">No USD revenue data available for this period.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Revenue by Service Type (KSH)</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php $__empty_1 = true; $__currentLoopData = ($reportData['revenue_by_service_ksh'] ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-capitalize"><?php echo e($service->billing_cycle ?? 'Unknown'); ?></span>
                                                    <span class="fw-bold">KSH <?php echo e(number_format($service->revenue ?? 0, 2)); ?></span>
                                                </div>
                                                <small class="text-muted"><?php echo e($service->count ?? 0); ?> invoices</small>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <p class="text-muted">No KSH service data available.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Revenue by Service Type (USD)</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php $__empty_1 = true; $__currentLoopData = ($reportData['revenue_by_service_usd'] ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-capitalize"><?php echo e($service->billing_cycle ?? 'Unknown'); ?></span>
                                                    <span class="fw-bold">$ <?php echo e(number_format($service->revenue ?? 0, 2)); ?></span>
                                                </div>
                                                <small class="text-muted"><?php echo e($service->count ?? 0); ?> invoices</small>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <p class="text-muted">No USD service data available.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Customer Billing Report with Both Currencies -->
                    <?php elseif($reportType === 'customer_billing'): ?>
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Customer Billing Summary - KSH</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Customer</th>
                                                <th>Total Billings</th>
                                                <th>Paid Amount (KSH)</th>
                                                <th>Pending Amount (KSH)</th>
                                                <th>Overdue Amount (KSH)</th>
                                                <th>Total Billed (KSH)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = ($reportData['customer_billing_ksh'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $billing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><?php echo e($billing->customer_name ?? 'Unknown'); ?></td>
                                                    <td><?php echo e($billing->total_billings ?? 0); ?></td>
                                                    <td class="text-success">KSH <?php echo e(number_format($billing->paid_amount ?? 0, 2)); ?></td>
                                                    <td class="text-warning">KSH <?php echo e(number_format($billing->pending_amount ?? 0, 2)); ?></td>
                                                    <td class="text-danger">KSH <?php echo e(number_format($billing->overdue_amount ?? 0, 2)); ?></td>
                                                    <td class="fw-bold">
                                                        KSH <?php echo e(number_format(($billing->paid_amount ?? 0) + ($billing->pending_amount ?? 0) + ($billing->overdue_amount ?? 0), 2)); ?>

                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">No KSH customer billing data available.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Customer Billing Summary - USD</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Customer</th>
                                                <th>Total Billings</th>
                                                <th>Paid Amount (USD)</th>
                                                <th>Pending Amount (USD)</th>
                                                <th>Overdue Amount (USD)</th>
                                                <th>Total Billed (USD)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = ($reportData['customer_billing_usd'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $billing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><?php echo e($billing->customer_name ?? 'Unknown'); ?></td>
                                                    <td><?php echo e($billing->total_billings ?? 0); ?></td>
                                                    <td class="text-success">$ <?php echo e(number_format($billing->paid_amount ?? 0, 2)); ?></td>
                                                    <td class="text-warning">$ <?php echo e(number_format($billing->pending_amount ?? 0, 2)); ?></td>
                                                    <td class="text-danger">$ <?php echo e(number_format($billing->overdue_amount ?? 0, 2)); ?></td>
                                                    <td class="fw-bold">
                                                        $ <?php echo e(number_format(($billing->paid_amount ?? 0) + ($billing->pending_amount ?? 0) + ($billing->overdue_amount ?? 0), 2)); ?>

                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">No USD customer billing data available.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                   <!-- Aging Report with Both Currencies -->
<!-- Aging Report with Both Currencies -->
<?php elseif($reportType === 'aging_report'): ?>
    <?php
        // Get the data directly from reportData
        $kshAgingData = $reportData['aging_report_ksh'] ?? collect();
        $usdAgingData = $reportData['aging_report_usd'] ?? collect();
    ?>

    <!-- KSH Aging Report -->
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="fas fa-chart-pie me-2"></i>
                Accounts Receivable Aging Report - KSH
                <?php if($kshAgingData->count() > 0): ?>
                    <span class="badge bg-primary ms-2"><?php echo e($kshAgingData->count()); ?> customers</span>
                <?php endif; ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Customer</th>
                            <th class="text-end">Current (KSH)</th>
                            <th class="text-end">1-30 Days (KSH)</th>
                            <th class="text-end">31-60 Days (KSH)</th>
                            <th class="text-end">61-90+ Days (KSH)</th>
                            <th class="text-end">Total Outstanding (KSH)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $kshAgingData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                // Handle both object and array formats
                                if(is_object($customer)) {
                                    $customerName = $customer->customer_name ?? 'Unknown';
                                    $current = $customer->current ?? 0;
                                    $days30 = $customer->days_30 ?? 0;
                                    $days60 = $customer->days_60 ?? 0;
                                    $days90Plus = $customer->days_90_plus ?? 0;
                                } else {
                                    $customerName = $customer['customer_name'] ?? 'Unknown';
                                    $current = $customer['current'] ?? 0;
                                    $days30 = $customer['days_30'] ?? 0;
                                    $days60 = $customer['days_60'] ?? 0;
                                    $days90Plus = $customer['days_90_plus'] ?? 0;
                                }
                                $total = $current + $days30 + $days60 + $days90Plus;
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($customerName); ?></strong>
                                    <?php if($total > 0): ?>
                                        <br><small class="text-muted">Total: <?php echo e(number_format($total, 2)); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end <?php echo e($current > 0 ? 'text-success fw-bold' : 'text-secondary'); ?>">
                                    <?php echo e(number_format($current, 2)); ?>

                                </td>
                                <td class="text-end <?php echo e($days30 > 0 ? 'text-warning fw-bold' : 'text-secondary'); ?>">
                                    <?php echo e(number_format($days30, 2)); ?>

                                </td>
                                <td class="text-end <?php echo e($days60 > 0 ? 'text-warning fw-bold' : 'text-secondary'); ?>">
                                    <?php echo e(number_format($days60, 2)); ?>

                                </td>
                                <td class="text-end <?php echo e($days90Plus > 0 ? 'text-danger fw-bold' : 'text-secondary'); ?>">
                                    <?php echo e(number_format($days90Plus, 2)); ?>

                                </td>
                                <td class="text-end fw-bold text-primary">
                                    <?php echo e(number_format($total, 2)); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    No KSH aging data available for the selected period.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if($kshAgingData->count() > 0): ?>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td class="text-end bg-light">TOTAL:</td>
                            <td class="text-end bg-light"><?php echo e(number_format($kshAgingData->sum(function($item) {
                                return is_object($item) ? ($item->current ?? 0) : ($item['current'] ?? 0);
                            }), 2)); ?></td>
                            <td class="text-end bg-light"><?php echo e(number_format($kshAgingData->sum(function($item) {
                                return is_object($item) ? ($item->days_30 ?? 0) : ($item['days_30'] ?? 0);
                            }), 2)); ?></td>
                            <td class="text-end bg-light"><?php echo e(number_format($kshAgingData->sum(function($item) {
                                return is_object($item) ? ($item->days_60 ?? 0) : ($item['days_60'] ?? 0);
                            }), 2)); ?></td>
                            <td class="text-end bg-light"><?php echo e(number_format($kshAgingData->sum(function($item) {
                                return is_object($item) ? ($item->days_90_plus ?? 0) : ($item['days_90_plus'] ?? 0);
                            }), 2)); ?></td>
                            <td class="text-end bg-light text-primary fw-bold">
                                <?php echo e(number_format(
                                    $kshAgingData->sum(function($item) { return is_object($item) ? ($item->current ?? 0) : ($item['current'] ?? 0); }) +
                                    $kshAgingData->sum(function($item) { return is_object($item) ? ($item->days_30 ?? 0) : ($item['days_30'] ?? 0); }) +
                                    $kshAgingData->sum(function($item) { return is_object($item) ? ($item->days_60 ?? 0) : ($item['days_60'] ?? 0); }) +
                                    $kshAgingData->sum(function($item) { return is_object($item) ? ($item->days_90_plus ?? 0) : ($item['days_90_plus'] ?? 0); }), 2)); ?>

                            </td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- USD Aging Report -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="fas fa-chart-pie me-2"></i>
                Accounts Receivable Aging Report - USD
                <?php if($usdAgingData->count() > 0): ?>
                    <span class="badge bg-primary ms-2"><?php echo e($usdAgingData->count()); ?> customers</span>
                <?php endif; ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Customer</th>
                            <th class="text-end">Current (USD)</th>
                            <th class="text-end">1-30 Days (USD)</th>
                            <th class="text-end">31-60 Days (USD)</th>
                            <th class="text-end">61-90+ Days (USD)</th>
                            <th class="text-end">Total Outstanding (USD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $usdAgingData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                if(is_object($customer)) {
                                    $customerName = $customer->customer_name ?? 'Unknown';
                                    $current = $customer->current ?? 0;
                                    $days30 = $customer->days_30 ?? 0;
                                    $days60 = $customer->days_60 ?? 0;
                                    $days90Plus = $customer->days_90_plus ?? 0;
                                } else {
                                    $customerName = $customer['customer_name'] ?? 'Unknown';
                                    $current = $customer['current'] ?? 0;
                                    $days30 = $customer['days_30'] ?? 0;
                                    $days60 = $customer['days_60'] ?? 0;
                                    $days90Plus = $customer['days_90_plus'] ?? 0;
                                }
                                $total = $current + $days30 + $days60 + $days90Plus;
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($customerName); ?></strong>
                                    <?php if($total > 0): ?>
                                        <br><small class="text-muted">Total: $<?php echo e(number_format($total, 2)); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end <?php echo e($current > 0 ? 'text-success fw-bold' : 'text-secondary'); ?>">
                                    <?php echo e(number_format($current, 2)); ?>

                                </td>
                                <td class="text-end <?php echo e($days30 > 0 ? 'text-warning fw-bold' : 'text-secondary'); ?>">
                                    <?php echo e(number_format($days30, 2)); ?>

                                </td>
                                <td class="text-end <?php echo e($days60 > 0 ? 'text-warning fw-bold' : 'text-secondary'); ?>">
                                    <?php echo e(number_format($days60, 2)); ?>

                                </td>
                                <td class="text-end <?php echo e($days90Plus > 0 ? 'text-danger fw-bold' : 'text-secondary'); ?>">
                                    <?php echo e(number_format($days90Plus, 2)); ?>

                                </td>
                                <td class="text-end fw-bold text-primary">
                                    <?php echo e(number_format($total, 2)); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    No USD aging data available for the selected period.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if($usdAgingData->count() > 0): ?>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td class="text-end bg-light">TOTAL:</td>
                            <td class="text-end bg-light">$ <?php echo e(number_format($usdAgingData->sum(function($item) {
                                return is_object($item) ? ($item->current ?? 0) : ($item['current'] ?? 0);
                            }), 2)); ?></td>
                            <td class="text-end bg-light">$ <?php echo e(number_format($usdAgingData->sum(function($item) {
                                return is_object($item) ? ($item->days_30 ?? 0) : ($item['days_30'] ?? 0);
                            }), 2)); ?></td>
                            <td class="text-end bg-light">$ <?php echo e(number_format($usdAgingData->sum(function($item) {
                                return is_object($item) ? ($item->days_60 ?? 0) : ($item['days_60'] ?? 0);
                            }), 2)); ?></td>
                            <td class="text-end bg-light">$ <?php echo e(number_format($usdAgingData->sum(function($item) {
                                return is_object($item) ? ($item->days_90_plus ?? 0) : ($item['days_90_plus'] ?? 0);
                            }), 2)); ?></td>
                            <td class="text-end bg-light text-primary fw-bold">
                                $ <?php echo e(number_format(
                                    $usdAgingData->sum(function($item) { return is_object($item) ? ($item->current ?? 0) : ($item['current'] ?? 0); }) +
                                    $usdAgingData->sum(function($item) { return is_object($item) ? ($item->days_30 ?? 0) : ($item['days_30'] ?? 0); }) +
                                    $usdAgingData->sum(function($item) { return is_object($item) ? ($item->days_60 ?? 0) : ($item['days_60'] ?? 0); }) +
                                    $usdAgingData->sum(function($item) { return is_object($item) ? ($item->days_90_plus ?? 0) : ($item['days_90_plus'] ?? 0); }), 2)); ?>

                            </td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

                    <!-- Debt Aging Analysis Report with Both Currencies -->
<?php elseif($reportType === 'debt_aging'): ?>
    <!-- KSH Debt Aging Summary -->
    <?php if(isset($reportData['debt_summary_ksh'])): ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <h5 class="border-bottom pb-2 mb-3">
                <span class="badge bg-primary me-2">KSH</span> Kenyan Shilling Debt Summary
            </h5>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Total Receivables (KSH)</h6>
                    <h3 class="mb-0">KSH <?php echo e(number_format($reportData['debt_summary_ksh']['total_receivables'] ?? 0, 2)); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Current (KSH)</h6>
                    <h3 class="mb-0">KSH <?php echo e(number_format($reportData['debt_summary_ksh']['current'] ?? 0, 2)); ?></h3>
                    <small><?php echo e(number_format($reportData['debt_summary_ksh']['current_percentage'] ?? 0, 1)); ?>%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Overdue (KSH)</h6>
                    <h3 class="mb-0">KSH <?php echo e(number_format($reportData['debt_summary_ksh']['overdue'] ?? 0, 2)); ?></h3>
                    <small><?php echo e(number_format($reportData['debt_summary_ksh']['overdue_percentage'] ?? 0, 1)); ?>%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Bad Debt Provision (KSH)</h6>
                    <h3 class="mb-0">KSH <?php echo e(number_format($reportData['debt_summary_ksh']['bad_debt_provision'] ?? 0, 2)); ?></h3>
                    <small><?php echo e(number_format($reportData['debt_summary_ksh']['bad_debt_percentage'] ?? 0, 1)); ?>%</small>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- USD Debt Aging Summary -->
    <?php if(isset($reportData['debt_summary_usd'])): ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <h5 class="border-bottom pb-2 mb-3">
                <span class="badge bg-secondary me-2">USD</span> US Dollar Debt Summary
            </h5>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Total Receivables (USD)</h6>
                    <h3 class="mb-0">$ <?php echo e(number_format($reportData['debt_summary_usd']['total_receivables'] ?? 0, 2)); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Current (USD)</h6>
                    <h3 class="mb-0">$ <?php echo e(number_format($reportData['debt_summary_usd']['current'] ?? 0, 2)); ?></h3>
                    <small><?php echo e(number_format($reportData['debt_summary_usd']['current_percentage'] ?? 0, 1)); ?>%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Overdue (USD)</h6>
                    <h3 class="mb-0">$ <?php echo e(number_format($reportData['debt_summary_usd']['overdue'] ?? 0, 2)); ?></h3>
                    <small><?php echo e(number_format($reportData['debt_summary_usd']['overdue_percentage'] ?? 0, 1)); ?>%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Bad Debt Provision (USD)</h6>
                    <h3 class="mb-0">$ <?php echo e(number_format($reportData['debt_summary_usd']['bad_debt_provision'] ?? 0, 2)); ?></h3>
                    <small><?php echo e(number_format($reportData['debt_summary_usd']['bad_debt_percentage'] ?? 0, 1)); ?>%</small>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Detailed Debt Aging Table -->
    <div class="card">
    <div class="card-header">
        <h6 class="card-title mb-0">Detailed Debt Aging Analysis</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Customer</th>
                        <th>Currency</th>
                        <th class="text-end">Total Due</th>
                        <th class="text-end">Current</th>
                        <th class="text-end">Period</th>
                        <th class="text-end">1-30 Days</th>
                        <th class="text-end">31-60 Days</th>
                        <th class="text-end">61-90 Days</th>
                        <th class="text-end">&gt;90 Days</th>
                        <th>Risk Level</th>
                    </tr>
                </thead>
                <tbody>
    <?php $__empty_1 = true; $__currentLoopData = ($reportData['detailed_aging'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $currencySymbol = $debt->currency == 'KSH' ? 'KSh ' : '$ ';
            $isKsh = $debt->currency == 'KSH';
        ?>
        <tr>
            <td>
                <strong><?php echo e($debt->customer_name ?? 'Unknown'); ?></strong>
                <br>
                <small class="text-muted"><?php echo e($debt->billing_number ?? 'N/A'); ?></small>
            </td>
            <td>
                <span class="badge bg-<?php echo e($isKsh ? 'primary' : 'secondary'); ?>">
                    <?php echo e(strtoupper($debt->currency ?? 'KSH')); ?>

                </span>
            </td>
            <td class="text-end fw-bold"><?php echo e($currencySymbol); ?><?php echo e(number_format($debt->total_due ?? 0, 2)); ?></td>
            <td class="text-end <?php echo e(($debt->current ?? 0) > 0 ? 'text-success fw-bold' : ''); ?>">
                <?php echo e($currencySymbol); ?><?php echo e(number_format($debt->current ?? 0, 2)); ?>

            </td>
            <td class="text-center">
                <span class="badge bg-info" title="<?php echo e($debt->period_tooltip ?? ''); ?>">
                    <?php echo e($debt->period_display ?? 'Q1-2026'); ?>

                </span>
                <br>
                <small class="text-muted">Due: <?php echo e(\Carbon\Carbon::parse($debt->due_date)->format('M j, Y')); ?></small>
            </td>
            <td class="text-end <?php echo e(($debt->days_30 ?? 0) > 0 ? 'text-warning fw-bold' : ''); ?>">
                <?php echo e($currencySymbol); ?><?php echo e(number_format($debt->days_30 ?? 0, 2)); ?>

            </td>
            <td class="text-end <?php echo e(($debt->days_60 ?? 0) > 0 ? 'text-warning fw-bold' : ''); ?>">
                <?php echo e($currencySymbol); ?><?php echo e(number_format($debt->days_60 ?? 0, 2)); ?>

            </td>
            <td class="text-end <?php echo e(($debt->days_90 ?? 0) > 0 ? 'text-danger fw-bold' : ''); ?>">
                <?php echo e($currencySymbol); ?><?php echo e(number_format($debt->days_90 ?? 0, 2)); ?>

            </td>
            <td class="text-end <?php echo e(($debt->days_over_90 ?? 0) > 0 ? 'text-danger fw-bold bg-light' : ''); ?>">
                <?php echo e($currencySymbol); ?><?php echo e(number_format($debt->days_over_90 ?? 0, 2)); ?>

            </td>
            <td>
                <?php
                    $riskLevel = $debt->risk_level ?? 'low';
                    $badgeClass = [
                        'low' => 'bg-success',
                        'medium' => 'bg-warning text-dark',
                        'high' => 'bg-danger',
                        'critical' => 'bg-dark'
                    ][$riskLevel] ?? 'bg-secondary';
                ?>
                <span class="badge <?php echo e($badgeClass); ?> text-capitalize"><?php echo e($riskLevel); ?></span>
                <br>
                <small class="text-muted"><?php echo e($debt->days_overdue); ?> days overdue</small>
            </td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="10" class="text-center text-muted py-5">
                <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                <h5>No outstanding debts</h5>
                <p class="mb-0">All customers are current on their payments.</p>
            </td>
        </tr>
    <?php endif; ?>
</tbody>
                <?php if(($reportData['detailed_aging'] ?? collect())->count() > 0): ?>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="2" class="text-end">TOTAL:</td>
                        <?php
                            $totalKsh = collect($reportData['detailed_aging'] ?? [])->where('currency', 'KSH')->sum('total_due');
                            $totalUsd = collect($reportData['detailed_aging'] ?? [])->where('currency', 'USD')->sum('total_due');
                        ?>
                        <td class="text-end">
                            KSh <?php echo e(number_format($totalKsh, 2)); ?><br>
                            $ <?php echo e(number_format($totalUsd, 2)); ?>

                        </td>
                        <td colspan="7"></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

                     <!-- Tax Report with Both Currencies -->
                      <?php elseif($reportType === 'tax_report'): ?>
                        <?php
                            $taxSummaryKsh = $reportData['tax_summary_ksh'] ?? null;
                            $taxSummaryUsd = $reportData['tax_summary_usd'] ?? null;
                        ?>

                        <?php if($taxSummaryKsh): ?>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <span class="badge bg-primary me-2">KSH</span> Kenyan Shilling Tax Summary
                                    </h5>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Total Tax Collected (KSH)</h6>
                                            <h3 class="mb-0">KSH <?php echo e(number_format($taxSummaryKsh->total_tax ?? 0, 2)); ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Total Revenue (KSH)</h6>
                                            <h3 class="mb-0">KSH <?php echo e(number_format($taxSummaryKsh->total_amount ?? 0, 2)); ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Average Tax Rate (KSH)</h6>
                                            <h3 class="mb-0"><?php echo e(number_format($taxSummaryKsh->avg_tax_rate ?? 0, 2)); ?>%</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-secondary text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Total Invoices (KSH)</h6>
                                            <h3 class="mb-0"><?php echo e($taxSummaryKsh->invoice_count ?? 0); ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($taxSummaryUsd): ?>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <span class="badge bg-secondary me-2">USD</span> US Dollar Tax Summary
                                    </h5>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Total Tax Collected (USD)</h6>
                                            <h3 class="mb-0">$ <?php echo e(number_format($taxSummaryUsd->total_tax ?? 0, 2)); ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Total Revenue (USD)</h6>
                                            <h3 class="mb-0">$ <?php echo e(number_format($taxSummaryUsd->total_amount ?? 0, 2)); ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Average Tax Rate (USD)</h6>
                                            <h3 class="mb-0"><?php echo e(number_format($taxSummaryUsd->avg_tax_rate ?? 0, 2)); ?>%</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-secondary text-white">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Total Invoices (USD)</h6>
                                            <h3 class="mb-0"><?php echo e($taxSummaryUsd->invoice_count ?? 0); ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Tax Collection by Billing Cycle</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Billing Cycle</th>
                                                <th>Currency</th>
                                                <th>Tax Collected</th>
                                                <th>Number of Invoices</th>
                                                <th>Percentage of Total Tax</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = ($reportData['tax_by_type'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td class="text-capitalize"><?php echo e($tax->billing_cycle ?? 'unknown'); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo e($tax->currency == 'ksh' ? 'primary' : 'secondary'); ?>">
                                                            <?php echo e(strtoupper($tax->currency ?? 'KSH')); ?>

                                                        </span>
                                                    </td>
                                                    <td><?php echo e($tax->currency == 'ksh' ? 'KSH' : '$'); ?> <?php echo e(number_format($tax->tax_collected ?? 0, 2)); ?></td>
                                                    <td><?php echo e($tax->count ?? 0); ?></td>
                                                    <td>
                                                        <?php
                                                            $totalTax = $tax->currency == 'ksh' ? ($taxSummaryKsh->total_tax ?? 1) : ($taxSummaryUsd->total_tax ?? 1);
                                                            $taxCollected = $tax->tax_collected ?? 0;
                                                            $percentage = $totalTax > 0 ? ($taxCollected / $totalTax) * 100 : 0;
                                                        ?>
                                                        <?php echo e(number_format($percentage, 1)); ?>%
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No tax data available for this period.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    <!-- Cash Flow Statement -->
<?php elseif($reportType === 'cash_flow'): ?>
    <!-- Cash Flow Summary Cards - Combined -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h5 class="border-bottom pb-2 mb-3">
                <span class="badge bg-primary me-2">Combined Summary</span>
            </h5>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Operating Cash Flow</h6>
                    <h3 class="mb-0">$ <?php echo e(number_format($reportData['cash_flow_summary']['operating'] ?? 0, 2)); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Investing Cash Flow</h6>
                    <h3 class="mb-0">$ <?php echo e(number_format($reportData['cash_flow_summary']['investing'] ?? 0, 2)); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Financing Cash Flow</h6>
                    <h3 class="mb-0">$ <?php echo e(number_format($reportData['cash_flow_summary']['financing'] ?? 0, 2)); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Net Cash Flow</h6>
                    <h3 class="mb-0">$ <?php echo e(number_format($reportData['cash_flow_summary']['net_cash_flow'] ?? 0, 2)); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Flow Summary by Currency -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <span class="badge bg-primary me-2">KSH</span> Cash Flow Summary (KSH)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Operating</small>
                                <h5 class="text-success mb-0">KSH <?php echo e(number_format($reportData['cash_flow_summary_ksh']['operating'] ?? 0, 2)); ?></h5>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Investing</small>
                                <h5 class="text-info mb-0">KSH <?php echo e(number_format($reportData['cash_flow_summary_ksh']['investing'] ?? 0, 2)); ?></h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Financing</small>
                                <h5 class="text-warning mb-0">KSH <?php echo e(number_format($reportData['cash_flow_summary_ksh']['financing'] ?? 0, 2)); ?></h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Net Cash Flow</small>
                                <h5 class="text-primary mb-0">KSH <?php echo e(number_format($reportData['cash_flow_summary_ksh']['net_cash_flow'] ?? 0, 2)); ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <span class="badge bg-secondary me-2">USD</span> Cash Flow Summary (USD)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Operating</small>
                                <h5 class="text-success mb-0">$ <?php echo e(number_format($reportData['cash_flow_summary_usd']['operating'] ?? 0, 2)); ?></h5>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Investing</small>
                                <h5 class="text-info mb-0">$ <?php echo e(number_format($reportData['cash_flow_summary_usd']['investing'] ?? 0, 2)); ?></h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Financing</small>
                                <h5 class="text-warning mb-0">$ <?php echo e(number_format($reportData['cash_flow_summary_usd']['financing'] ?? 0, 2)); ?></h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Net Cash Flow</small>
                                <h5 class="text-primary mb-0">$ <?php echo e(number_format($reportData['cash_flow_summary_usd']['net_cash_flow'] ?? 0, 2)); ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Cash Flow Statements by Currency -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <span class="badge bg-primary me-2">KSH</span> Cash Flow Statement (KSH)
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr class="table-light">
                                <td colspan="2"><strong>Operating Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Cash from Customers</td>
                                <td class="text-success">KSH <?php echo e(number_format($reportData['cash_flow_details_ksh']['cash_from_customers'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Cash Paid to Suppliers</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['cash_to_suppliers'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Cash Paid for Expenses</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['cash_for_expenses'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Interest Paid</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['interest_paid'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Taxes Paid</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['taxes_paid'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Operating</strong></td>
                                <td class="fw-bold">KSH <?php echo e(number_format($reportData['cash_flow_summary_ksh']['operating'] ?? 0, 2)); ?></td>
                            </tr>

                            <tr class="table-light">
                                <td colspan="2"><strong>Investing Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Purchase of Equipment</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['equipment_purchase'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Infrastructure Investments</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['infrastructure_investment'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Property Purchase</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['property_purchase'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Investment Income</td>
                                <td class="text-success">KSH <?php echo e(number_format($reportData['cash_flow_details_ksh']['investment_income'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Asset Sales</td>
                                <td class="text-success">KSH <?php echo e(number_format($reportData['cash_flow_details_ksh']['asset_sales'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Investing</strong></td>
                                <td class="fw-bold">KSH <?php echo e(number_format($reportData['cash_flow_summary_ksh']['investing'] ?? 0, 2)); ?></td>
                            </tr>

                            <tr class="table-light">
                                <td colspan="2"><strong>Financing Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Loan Proceeds</td>
                                <td class="text-success">KSH <?php echo e(number_format($reportData['cash_flow_details_ksh']['loan_proceeds'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Equity Issuance</td>
                                <td class="text-success">KSH <?php echo e(number_format($reportData['cash_flow_details_ksh']['equity_issuance'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Debt Repayment</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['debt_repayment'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Dividends Paid</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['dividends_paid'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Financing</strong></td>
                                <td class="fw-bold">KSH <?php echo e(number_format($reportData['cash_flow_summary_ksh']['financing'] ?? 0, 2)); ?></td>
                            </tr>

                            <tr class="table-primary">
                                <td><strong>Net Increase in Cash</strong></td>
                                <td class="fw-bold">KSH <?php echo e(number_format($reportData['cash_flow_summary_ksh']['net_cash_flow'] ?? 0, 2)); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <span class="badge bg-secondary me-2">USD</span> Cash Flow Statement (USD)
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr class="table-light">
                                <td colspan="2"><strong>Operating Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Cash from Customers</td>
                                <td class="text-success">$ <?php echo e(number_format($reportData['cash_flow_details_usd']['cash_from_customers'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Cash Paid to Suppliers</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['cash_to_suppliers'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Cash Paid for Expenses</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['cash_for_expenses'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Interest Paid</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['interest_paid'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Taxes Paid</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['taxes_paid'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Operating</strong></td>
                                <td class="fw-bold">$ <?php echo e(number_format($reportData['cash_flow_summary_usd']['operating'] ?? 0, 2)); ?></td>
                            </tr>

                            <tr class="table-light">
                                <td colspan="2"><strong>Investing Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Purchase of Equipment</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['equipment_purchase'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Infrastructure Investments</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['infrastructure_investment'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Property Purchase</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['property_purchase'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Investment Income</td>
                                <td class="text-success">$ <?php echo e(number_format($reportData['cash_flow_details_usd']['investment_income'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Asset Sales</td>
                                <td class="text-success">$ <?php echo e(number_format($reportData['cash_flow_details_usd']['asset_sales'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Investing</strong></td>
                                <td class="fw-bold">$ <?php echo e(number_format($reportData['cash_flow_summary_usd']['investing'] ?? 0, 2)); ?></td>
                            </tr>

                            <tr class="table-light">
                                <td colspan="2"><strong>Financing Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Loan Proceeds</td>
                                <td class="text-success">$ <?php echo e(number_format($reportData['cash_flow_details_usd']['loan_proceeds'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Equity Issuance</td>
                                <td class="text-success">$ <?php echo e(number_format($reportData['cash_flow_details_usd']['equity_issuance'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Debt Repayment</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['debt_repayment'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Dividends Paid</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['dividends_paid'] ?? 0), 2)); ?>)</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Financing</strong></td>
                                <td class="fw-bold">$ <?php echo e(number_format($reportData['cash_flow_summary_usd']['financing'] ?? 0, 2)); ?></td>
                            </tr>

                            <tr class="table-primary">
                                <td><strong>Net Increase in Cash</strong></td>
                                <td class="fw-bold">$ <?php echo e(number_format($reportData['cash_flow_summary_usd']['net_cash_flow'] ?? 0, 2)); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Combined Cash Flow Statement -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Combined Cash Flow Statement (All Currencies)</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Activity</th>
                                <th>KSH</th>
                                <th>USD</th>
                                <th>Total (USD Equivalent)*</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-light">
                                <td colspan="4"><strong>Operating Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Cash from Customers</td>
                                <td class="text-success">KSH <?php echo e(number_format($reportData['cash_flow_details_ksh']['cash_from_customers'] ?? 0, 2)); ?></td>
                                <td class="text-success">$ <?php echo e(number_format($reportData['cash_flow_details_usd']['cash_from_customers'] ?? 0, 2)); ?></td>
                                <td class="text-success">
                                    <?php
                                        $exchangeRate = $reportData['exchange_rate'] ?? 130;
                                        $total = ($reportData['cash_flow_details_ksh']['cash_from_customers'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['cash_from_customers'] ?? 0);
                                    ?>
                                    $ <?php echo e(number_format($total, 2)); ?>

                                </td>
                            </tr>
                            <tr>
                                <td>Cash Paid to Suppliers</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['cash_to_suppliers'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['cash_to_suppliers'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">
                                    <?php
                                        $total = abs(($reportData['cash_flow_details_ksh']['cash_to_suppliers'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['cash_to_suppliers'] ?? 0));
                                    ?>
                                    ($ <?php echo e(number_format($total, 2)); ?>)
                                </td>
                            </tr>
                            <tr>
                                <td>Cash Paid for Expenses</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['cash_for_expenses'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['cash_for_expenses'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">
                                    <?php
                                        $total = abs(($reportData['cash_flow_details_ksh']['cash_for_expenses'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['cash_for_expenses'] ?? 0));
                                    ?>
                                    ($ <?php echo e(number_format($total, 2)); ?>)
                                </td>
                            </tr>
                            <tr>
                                <td>Interest Paid</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['interest_paid'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['interest_paid'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">
                                    <?php
                                        $total = abs(($reportData['cash_flow_details_ksh']['interest_paid'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['interest_paid'] ?? 0));
                                    ?>
                                    ($ <?php echo e(number_format($total, 2)); ?>)
                                </td>
                            </tr>
                            <tr>
                                <td>Taxes Paid</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['taxes_paid'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['taxes_paid'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">
                                    <?php
                                        $total = abs(($reportData['cash_flow_details_ksh']['taxes_paid'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['taxes_paid'] ?? 0));
                                    ?>
                                    ($ <?php echo e(number_format($total, 2)); ?>)
                                </td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Operating</strong></td>
                                <td class="fw-bold">KSH <?php echo e(number_format($reportData['cash_flow_summary_ksh']['operating'] ?? 0, 2)); ?></td>
                                <td class="fw-bold">$ <?php echo e(number_format($reportData['cash_flow_summary_usd']['operating'] ?? 0, 2)); ?></td>
                                <td class="fw-bold">
                                    <?php
                                        $total = ($reportData['cash_flow_summary_ksh']['operating'] ?? 0) / $exchangeRate + ($reportData['cash_flow_summary_usd']['operating'] ?? 0);
                                    ?>
                                    $ <?php echo e(number_format($total, 2)); ?>

                                </td>
                            </tr>

                            <tr class="table-light">
                                <td colspan="4"><strong>Investing Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Purchase of Equipment</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['equipment_purchase'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['equipment_purchase'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">
                                    <?php
                                        $total = abs(($reportData['cash_flow_details_ksh']['equipment_purchase'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['equipment_purchase'] ?? 0));
                                    ?>
                                    ($ <?php echo e(number_format($total, 2)); ?>)
                                </td>
                            </tr>
                            <tr>
                                <td>Infrastructure Investments</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['infrastructure_investment'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['infrastructure_investment'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">
                                    <?php
                                        $total = abs(($reportData['cash_flow_details_ksh']['infrastructure_investment'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['infrastructure_investment'] ?? 0));
                                    ?>
                                    ($ <?php echo e(number_format($total, 2)); ?>)
                                </td>
                            </tr>
                            <tr>
                                <td>Property Purchase</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['property_purchase'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['property_purchase'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">
                                    <?php
                                        $total = abs(($reportData['cash_flow_details_ksh']['property_purchase'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['property_purchase'] ?? 0));
                                    ?>
                                    ($ <?php echo e(number_format($total, 2)); ?>)
                                </td>
                            </tr>
                            <tr>
                                <td>Investment Income</td>
                                <td class="text-success">KSH <?php echo e(number_format($reportData['cash_flow_details_ksh']['investment_income'] ?? 0, 2)); ?></td>
                                <td class="text-success">$ <?php echo e(number_format($reportData['cash_flow_details_usd']['investment_income'] ?? 0, 2)); ?></td>
                                <td class="text-success">
                                    <?php
                                        $total = ($reportData['cash_flow_details_ksh']['investment_income'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['investment_income'] ?? 0);
                                    ?>
                                    $ <?php echo e(number_format($total, 2)); ?>

                                </td>
                            </tr>
                            <tr>
                                <td>Asset Sales</td>
                                <td class="text-success">KSH <?php echo e(number_format($reportData['cash_flow_details_ksh']['asset_sales'] ?? 0, 2)); ?></td>
                                <td class="text-success">$ <?php echo e(number_format($reportData['cash_flow_details_usd']['asset_sales'] ?? 0, 2)); ?></td>
                                <td class="text-success">
                                    <?php
                                        $total = ($reportData['cash_flow_details_ksh']['asset_sales'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['asset_sales'] ?? 0);
                                    ?>
                                    $ <?php echo e(number_format($total, 2)); ?>

                                </td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Investing</strong></td>
                                <td class="fw-bold">KSH <?php echo e(number_format($reportData['cash_flow_summary_ksh']['investing'] ?? 0, 2)); ?></td>
                                <td class="fw-bold">$ <?php echo e(number_format($reportData['cash_flow_summary_usd']['investing'] ?? 0, 2)); ?></td>
                                <td class="fw-bold">
                                    <?php
                                        $total = ($reportData['cash_flow_summary_ksh']['investing'] ?? 0) / $exchangeRate + ($reportData['cash_flow_summary_usd']['investing'] ?? 0);
                                    ?>
                                    $ <?php echo e(number_format($total, 2)); ?>

                                </td>
                            </tr>

                            <tr class="table-light">
                                <td colspan="4"><strong>Financing Activities</strong></td>
                            </tr>
                            <tr>
                                <td>Loan Proceeds</td>
                                <td class="text-success">KSH <?php echo e(number_format($reportData['cash_flow_details_ksh']['loan_proceeds'] ?? 0, 2)); ?></td>
                                <td class="text-success">$ <?php echo e(number_format($reportData['cash_flow_details_usd']['loan_proceeds'] ?? 0, 2)); ?></td>
                                <td class="text-success">
                                    <?php
                                        $total = ($reportData['cash_flow_details_ksh']['loan_proceeds'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['loan_proceeds'] ?? 0);
                                    ?>
                                    $ <?php echo e(number_format($total, 2)); ?>

                                </td>
                            </tr>
                            <tr>
                                <td>Equity Issuance</td>
                                <td class="text-success">KSH <?php echo e(number_format($reportData['cash_flow_details_ksh']['equity_issuance'] ?? 0, 2)); ?></td>
                                <td class="text-success">$ <?php echo e(number_format($reportData['cash_flow_details_usd']['equity_issuance'] ?? 0, 2)); ?></td>
                                <td class="text-success">
                                    <?php
                                        $total = ($reportData['cash_flow_details_ksh']['equity_issuance'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['equity_issuance'] ?? 0);
                                    ?>
                                    $ <?php echo e(number_format($total, 2)); ?>

                                </td>
                            </tr>
                            <tr>
                                <td>Debt Repayment</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['debt_repayment'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['debt_repayment'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">
                                    <?php
                                        $total = abs(($reportData['cash_flow_details_ksh']['debt_repayment'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['debt_repayment'] ?? 0));
                                    ?>
                                    ($ <?php echo e(number_format($total, 2)); ?>)
                                </td>
                            </tr>
                            <tr>
                                <td>Dividends Paid</td>
                                <td class="text-danger">(KSH <?php echo e(number_format(abs($reportData['cash_flow_details_ksh']['dividends_paid'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">($ <?php echo e(number_format(abs($reportData['cash_flow_details_usd']['dividends_paid'] ?? 0), 2)); ?>)</td>
                                <td class="text-danger">
                                    <?php
                                        $total = abs(($reportData['cash_flow_details_ksh']['dividends_paid'] ?? 0) / $exchangeRate + ($reportData['cash_flow_details_usd']['dividends_paid'] ?? 0));
                                    ?>
                                    ($ <?php echo e(number_format($total, 2)); ?>)
                                </td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Net Cash from Financing</strong></td>
                                <td class="fw-bold">KSH <?php echo e(number_format($reportData['cash_flow_summary_ksh']['financing'] ?? 0, 2)); ?></td>
                                <td class="fw-bold">$ <?php echo e(number_format($reportData['cash_flow_summary_usd']['financing'] ?? 0, 2)); ?></td>
                                <td class="fw-bold">
                                    <?php
                                        $total = ($reportData['cash_flow_summary_ksh']['financing'] ?? 0) / $exchangeRate + ($reportData['cash_flow_summary_usd']['financing'] ?? 0);
                                    ?>
                                    $ <?php echo e(number_format($total, 2)); ?>

                                </td>
                            </tr>

                            <tr class="table-primary">
                                <td><strong>Net Increase in Cash</strong></td>
                                <td class="fw-bold">KSH <?php echo e(number_format($reportData['cash_flow_summary_ksh']['net_cash_flow'] ?? 0, 2)); ?></td>
                                <td class="fw-bold">$ <?php echo e(number_format($reportData['cash_flow_summary_usd']['net_cash_flow'] ?? 0, 2)); ?></td>
                                <td class="fw-bold">
                                    <?php
                                        $total = ($reportData['cash_flow_summary_ksh']['net_cash_flow'] ?? 0) / $exchangeRate + ($reportData['cash_flow_summary_usd']['net_cash_flow'] ?? 0);
                                    ?>
                                    $ <?php echo e(number_format($total, 2)); ?>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <small class="text-muted">* USD equivalent using exchange rate: 1 USD = <?php echo e(number_format($reportData['exchange_rate'] ?? 130, 2)); ?> KSH</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Flow Chart -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Cash Flow Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="cashFlowChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

                    <!-- Profitability Analysis -->
                <?php elseif($reportType === 'profitability'): ?>
                    <!-- Profitability Metrics - Percentages (same for both currencies) -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2 mb-3">
                                <span class="badge bg-primary me-2">Profitability Ratios</span>
                            </h5>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                    <h6 class="card-title">Gross Profit Margin</h6>
                    <h3 class="mb-0"><?php echo e(number_format($reportData['profitability_metrics']['gross_margin'] ?? 0, 1)); ?>%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Operating Margin</h6>
                    <h3 class="mb-0"><?php echo e(number_format($reportData['profitability_metrics']['operating_margin'] ?? 0, 1)); ?>%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">Net Profit Margin</h6>
                    <h3 class="mb-0"><?php echo e(number_format($reportData['profitability_metrics']['net_margin'] ?? 0, 1)); ?>%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">ROI</h6>
                    <h3 class="mb-0"><?php echo e(number_format($reportData['profitability_metrics']['roi'] ?? 0, 1)); ?>%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- P&L Statement by Currency -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <span class="badge bg-primary me-2">KSH</span> Profit & Loss Statement (KSH)
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>Total Revenue</strong></td>
                                <td class="text-success">KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['revenue'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Cost of Services</td>
                                <td class="text-danger">(KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['cost_of_services'] ?? 0, 2)); ?>)</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Gross Profit</strong></td>
                                <td class="fw-bold">KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['gross_profit'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Operating Expenses</td>
                                <td class="text-danger">(KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['operating_expenses'] ?? 0, 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Depreciation</td>
                                <td class="text-danger">(KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['depreciation'] ?? 0, 2)); ?>)</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Operating Profit</strong></td>
                                <td class="fw-bold">KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['operating_profit'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Interest Expense</td>
                                <td class="text-danger">(KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['interest_expense'] ?? 0, 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Taxes</td>
                                <td class="text-danger">(KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['taxes'] ?? 0, 2)); ?>)</td>
                            </tr>
                            <tr class="table-primary">
                                <td><strong>Net Profit</strong></td>
                                <td class="fw-bold">KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['net_profit'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr class="table-info">
                                <td><strong>EBITDA</strong></td>
                                <td class="fw-bold">KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['ebitda'] ?? 0, 2)); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <span class="badge bg-secondary me-2">USD</span> Profit & Loss Statement (USD)
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>Total Revenue</strong></td>
                                <td class="text-success">$ <?php echo e(number_format($reportData['p_l_statement_usd']['revenue'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Cost of Services</td>
                                <td class="text-danger">($ <?php echo e(number_format($reportData['p_l_statement_usd']['cost_of_services'] ?? 0, 2)); ?>)</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Gross Profit</strong></td>
                                <td class="fw-bold">$ <?php echo e(number_format($reportData['p_l_statement_usd']['gross_profit'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Operating Expenses</td>
                                <td class="text-danger">($ <?php echo e(number_format($reportData['p_l_statement_usd']['operating_expenses'] ?? 0, 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Depreciation</td>
                                <td class="text-danger">($ <?php echo e(number_format($reportData['p_l_statement_usd']['depreciation'] ?? 0, 2)); ?>)</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Operating Profit</strong></td>
                                <td class="fw-bold">$ <?php echo e(number_format($reportData['p_l_statement_usd']['operating_profit'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr>
                                <td>Interest Expense</td>
                                <td class="text-danger">($ <?php echo e(number_format($reportData['p_l_statement_usd']['interest_expense'] ?? 0, 2)); ?>)</td>
                            </tr>
                            <tr>
                                <td>Taxes</td>
                                <td class="text-danger">($ <?php echo e(number_format($reportData['p_l_statement_usd']['taxes'] ?? 0, 2)); ?>)</td>
                            </tr>
                            <tr class="table-primary">
                                <td><strong>Net Profit</strong></td>
                                <td class="fw-bold">$ <?php echo e(number_format($reportData['p_l_statement_usd']['net_profit'] ?? 0, 2)); ?></td>
                            </tr>
                            <tr class="table-info">
                                <td><strong>EBITDA</strong></td>
                                <td class="fw-bold">$ <?php echo e(number_format($reportData['p_l_statement_usd']['ebitda'] ?? 0, 2)); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Combined P&L Statement (Optional) -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Combined Profit & Loss Statement (All Currencies)</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>KSH</th>
                                <th>USD</th>
                                <th>Total (USD Equivalent)*</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Revenue</strong></td>
                                <td class="text-success">KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['revenue'] ?? 0, 2)); ?></td>
                                <td class="text-success">$ <?php echo e(number_format($reportData['p_l_statement_usd']['revenue'] ?? 0, 2)); ?></td>
                                <td class="fw-bold">
                                    <?php
                                        $exchangeRate = 130; // You should get this from your settings or API
                                        $totalRevenueUsd = ($reportData['p_l_statement_ksh']['revenue'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['revenue'] ?? 0);
                                    ?>
                                    $ <?php echo e(number_format($totalRevenueUsd, 2)); ?>

                                </td>
                            </tr>
                            <tr>
                                <td>Cost of Services</td>
                                <td class="text-danger">(KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['cost_of_services'] ?? 0, 2)); ?>)</td>
                                <td class="text-danger">($ <?php echo e(number_format($reportData['p_l_statement_usd']['cost_of_services'] ?? 0, 2)); ?>)</td>
                                <td class="text-danger">
                                    <?php
                                        $totalCostUsd = ($reportData['p_l_statement_ksh']['cost_of_services'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['cost_of_services'] ?? 0);
                                    ?>
                                    ($ <?php echo e(number_format($totalCostUsd, 2)); ?>)
                                </td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Gross Profit</strong></td>
                                <td class="fw-bold">KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['gross_profit'] ?? 0, 2)); ?></td>
                                <td class="fw-bold">$ <?php echo e(number_format($reportData['p_l_statement_usd']['gross_profit'] ?? 0, 2)); ?></td>
                                <td class="fw-bold">
                                    <?php
                                        $totalGrossProfitUsd = ($reportData['p_l_statement_ksh']['gross_profit'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['gross_profit'] ?? 0);
                                    ?>
                                    $ <?php echo e(number_format($totalGrossProfitUsd, 2)); ?>

                                </td>
                            </tr>
                            <tr>
                                <td>Operating Expenses</td>
                                <td class="text-danger">(KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['operating_expenses'] ?? 0, 2)); ?>)</td>
                                <td class="text-danger">($ <?php echo e(number_format($reportData['p_l_statement_usd']['operating_expenses'] ?? 0, 2)); ?>)</td>
                                <td class="text-danger">
                                    <?php
                                        $totalExpensesUsd = ($reportData['p_l_statement_ksh']['operating_expenses'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['operating_expenses'] ?? 0);
                                    ?>
                                    ($ <?php echo e(number_format($totalExpensesUsd, 2)); ?>)
                                </td>
                            </tr>
                            <tr>
                                <td>Depreciation</td>
                                <td class="text-danger">(KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['depreciation'] ?? 0, 2)); ?>)</td>
                                <td class="text-danger">($ <?php echo e(number_format($reportData['p_l_statement_usd']['depreciation'] ?? 0, 2)); ?>)</td>
                                <td class="text-danger">
                                    <?php
                                        $totalDepreciationUsd = ($reportData['p_l_statement_ksh']['depreciation'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['depreciation'] ?? 0);
                                    ?>
                                    ($ <?php echo e(number_format($totalDepreciationUsd, 2)); ?>)
                                </td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Operating Profit</strong></td>
                                <td class="fw-bold">KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['operating_profit'] ?? 0, 2)); ?></td>
                                <td class="fw-bold">$ <?php echo e(number_format($reportData['p_l_statement_usd']['operating_profit'] ?? 0, 2)); ?></td>
                                <td class="fw-bold">
                                    <?php
                                        $totalOperatingProfitUsd = ($reportData['p_l_statement_ksh']['operating_profit'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['operating_profit'] ?? 0);
                                    ?>
                                    $ <?php echo e(number_format($totalOperatingProfitUsd, 2)); ?>

                                </td>
                            </tr>
                            <tr>
                                <td>Interest Expense</td>
                                <td class="text-danger">(KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['interest_expense'] ?? 0, 2)); ?>)</td>
                                <td class="text-danger">($ <?php echo e(number_format($reportData['p_l_statement_usd']['interest_expense'] ?? 0, 2)); ?>)</td>
                                <td class="text-danger">
                                    <?php
                                        $totalInterestUsd = ($reportData['p_l_statement_ksh']['interest_expense'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['interest_expense'] ?? 0);
                                    ?>
                                    ($ <?php echo e(number_format($totalInterestUsd, 2)); ?>)
                                </td>
                            </tr>
                            <tr>
                                <td>Taxes</td>
                                <td class="text-danger">(KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['taxes'] ?? 0, 2)); ?>)</td>
                                <td class="text-danger">($ <?php echo e(number_format($reportData['p_l_statement_usd']['taxes'] ?? 0, 2)); ?>)</td>
                                <td class="text-danger">
                                    <?php
                                        $totalTaxesUsd = ($reportData['p_l_statement_ksh']['taxes'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['taxes'] ?? 0);
                                    ?>
                                    ($ <?php echo e(number_format($totalTaxesUsd, 2)); ?>)
                                </td>
                            </tr>
                            <tr class="table-primary">
                                <td><strong>Net Profit</strong></td>
                                <td class="fw-bold">KSH <?php echo e(number_format($reportData['p_l_statement_ksh']['net_profit'] ?? 0, 2)); ?></td>
                                <td class="fw-bold">$ <?php echo e(number_format($reportData['p_l_statement_usd']['net_profit'] ?? 0, 2)); ?></td>
                                <td class="fw-bold">
                                    <?php
                                        $totalNetProfitUsd = ($reportData['p_l_statement_ksh']['net_profit'] ?? 0) / $exchangeRate + ($reportData['p_l_statement_usd']['net_profit'] ?? 0);
                                    ?>
                                    $ <?php echo e(number_format($totalNetProfitUsd, 2)); ?>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <small class="text-muted">* USD equivalent using exchange rate: 1 USD = <?php echo e(number_format($exchangeRate ?? 130, 2)); ?> KSH</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Profitability by Service -->
    <!-- Profitability by Service -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Profitability by Service Type</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Service Type</th>
                                <th>Currency</th>
                                <th>Revenue</th>
                                <th>Profit Margin</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = ($reportData['service_profitability'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    // Handle both object and array formats
                                    $serviceType = is_object($service) ? ($service->service_type ?? 'Unknown') : ($service['service_type'] ?? 'Unknown');
                                    $currency = is_object($service) ? ($service->currency ?? 'ksh') : ($service['currency'] ?? 'ksh');
                                    $revenue = is_object($service) ? ($service->revenue ?? 0) : ($service['revenue'] ?? 0);
                                    $profitMargin = is_object($service) ? ($service->profit_margin ?? 0) : ($service['profit_margin'] ?? 0);
                                ?>
                                <tr>
                                    <td class="text-capitalize"><?php echo e($serviceType); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($currency == 'ksh' ? 'primary' : 'secondary'); ?>">
                                            <?php echo e(strtoupper($currency)); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($currency == 'ksh' ? 'KSH' : '$'); ?> <?php echo e(number_format($revenue, 2)); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2"><?php echo e(number_format($profitMargin, 1)); ?>%</span>
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                <?php
                                                    $width = min($profitMargin, 100);
                                                    $color = $profitMargin >= 40 ? 'success' : ($profitMargin >= 20 ? 'warning' : 'danger');
                                                ?>
                                                <div class="progress-bar bg-<?php echo e($color); ?>" style="width: <?php echo e($width); ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $badgeClass = $profitMargin >= 40 ? 'success' : ($profitMargin >= 20 ? 'warning' : 'danger');
                                            $status = $profitMargin >= 40 ? 'High' : ($profitMargin >= 20 ? 'Medium' : 'Low');
                                        ?>
                                        <span class="badge bg-<?php echo e($badgeClass); ?>"><?php echo e($status); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No service profitability data available.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Profitability Chart -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Profitability by Service - Chart View</h6>
                </div>
                <div class="card-body">
                    <canvas id="profitabilityChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Safe chart initialization helper
        function initChart(chartId, type, data, options = {}) {
            const element = document.getElementById(chartId);
            if (!element) return null;

            try {
                return new Chart(element.getContext('2d'), {
                    type: type,
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        ...options
                    }
                });
            } catch (error) {
                console.error(`Error initializing chart ${chartId}:`, error);
                return null;
            }
        }

        <?php if($reportType === 'debt_aging'): ?>
            // Debt Aging Chart KSH
            const debtDataKsh = {
                labels: ['Current', '1-30 Days', '31-60 Days', '61-90 Days', 'Over 90 Days'],
                datasets: [{
                    data: [
                        <?php echo e($reportData['debt_summary_ksh']['current'] ?? 0); ?>,
                        <?php echo e($reportData['debt_summary_ksh']['days_30'] ?? 0); ?>,
                        <?php echo e($reportData['debt_summary_ksh']['days_60'] ?? 0); ?>,
                        <?php echo e($reportData['debt_summary_ksh']['days_90'] ?? 0); ?>,
                        <?php echo e($reportData['debt_summary_ksh']['days_over_90'] ?? 0); ?>

                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#fd7e14', '#dc3545', '#6f42c1']
                }]
            };
            initChart('debtAgingChartKsh', 'doughnut', debtDataKsh, { plugins: { legend: { position: 'bottom' } } });

            // Debt Aging Chart USD
            const debtDataUsd = {
                labels: ['Current', '1-30 Days', '31-60 Days', '61-90 Days', 'Over 90 Days'],
                datasets: [{
                    data: [
                        <?php echo e($reportData['debt_summary_usd']['current'] ?? 0); ?>,
                        <?php echo e($reportData['debt_summary_usd']['days_30'] ?? 0); ?>,
                        <?php echo e($reportData['debt_summary_usd']['days_60'] ?? 0); ?>,
                        <?php echo e($reportData['debt_summary_usd']['days_90'] ?? 0); ?>,
                        <?php echo e($reportData['debt_summary_usd']['days_over_90'] ?? 0); ?>

                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#fd7e14', '#dc3545', '#6f42c1']
                }]
            };
            initChart('debtAgingChartUsd', 'doughnut', debtDataUsd, { plugins: { legend: { position: 'bottom' } } });
        <?php endif; ?>

        <?php if($reportType === 'cash_flow'): ?>
            // Cash Flow Chart
            const cashFlowData = {
                labels: ['Operating', 'Investing', 'Financing', 'Net Cash Flow'],
                datasets: [{
                    label: 'Cash Flow ($)',
                    data: [
                        <?php echo e($reportData['cash_flow_summary']['operating'] ?? 0); ?>,
                        <?php echo e($reportData['cash_flow_summary']['investing'] ?? 0); ?>,
                        <?php echo e($reportData['cash_flow_summary']['financing'] ?? 0); ?>,
                        <?php echo e($reportData['cash_flow_summary']['net_cash_flow'] ?? 0); ?>

                    ],
                    backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#007bff']
                }]
            };
            initChart('cashFlowChart', 'bar', cashFlowData, { scales: { y: { beginAtZero: true } } });
        <?php endif; ?>

        <?php if($reportType === 'profitability' && isset($reportData['service_profitability']) && count($reportData['service_profitability']) > 0): ?>
            const serviceNames = <?php echo json_encode(collect($reportData['service_profitability'])->pluck('service_type')->toArray()); ?>;
            const profitMargins = <?php echo json_encode(collect($reportData['service_profitability'])->pluck('profit_margin')->toArray()); ?>;

            if (serviceNames.length > 0 && profitMargins.length > 0) {
                const profitabilityData = {
                    labels: serviceNames,
                    datasets: [{
                        label: 'Profit Margin (%)',
                        data: profitMargins,
                        backgroundColor: '#28a745'
                    }]
                };
                initChart('profitabilityChart', 'bar', profitabilityData, {
                    scales: { y: { beginAtZero: true, max: 100, title: { display: true, text: 'Profit Margin (%)' } } }
                });
            }
        <?php endif; ?>

        // ============================================
        // EXPORT FUNCTIONALITY - FIXED
        // ============================================
        const exportBtn = document.getElementById('exportBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function(e) {
                e.preventDefault();

                try {
                    // Find all tables on the page
                    const tables = document.querySelectorAll('.card .table');

                    if (!tables || tables.length === 0) {
                        alert('No table data available for export.');
                        return;
                    }

                    let csvRows = [];

                    // Add report header
                    const reportTitle = document.querySelector('.card-title')?.innerText || 'Financial Report';
                    const reportPeriod = document.querySelector('.alert-info')?.innerText || '';

                    csvRows.push(['"' + reportTitle + '"']);
                    csvRows.push(['"' + reportPeriod.replace(/"/g, '""') + '"']);
                    csvRows.push(['']); // Empty row
                    csvRows.push(['Generated: ' + new Date().toLocaleString()]);
                    csvRows.push(['']); // Empty row

                    // Process each table
                    tables.forEach((table, tableIndex) => {
                        // Get table title from card header
                        const cardHeader = table.closest('.card')?.querySelector('.card-header');
                        const tableTitle = cardHeader?.innerText?.replace(/[^\w\s]/g, '')?.trim() || `Table ${tableIndex + 1}`;

                        csvRows.push([`"${tableTitle}"`]);

                        // Get all rows from this table
                        const rows = table.querySelectorAll('tr');

                        rows.forEach(row => {
                            const rowData = [];
                            const cells = row.querySelectorAll('th, td');

                            cells.forEach(cell => {
                                // Get cell text, clean it up
                                let text = cell.innerText || '';
                                // Remove extra whitespace
                                text = text.replace(/\s+/g, ' ').trim();
                                // Escape quotes by doubling them
                                text = text.replace(/"/g, '""');
                                // Wrap in quotes
                                rowData.push(`"${text}"`);
                            });

                            if (rowData.length > 0) {
                                csvRows.push(rowData.join(','));
                            }
                        });

                        csvRows.push(['']); // Empty row between tables
                        csvRows.push(['']); // Extra spacing
                    });

                    // Create CSV content
                    const csvContent = csvRows.join('\n');

                    // Create blob with UTF-8 BOM for proper encoding
                    const blob = new Blob(["\uFEFF" + csvContent], {
                        type: 'text/csv;charset=utf-8;'
                    });

                    // Create download link
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    const reportType = '<?php echo e($reportType); ?>';
                    const startDate = '<?php echo e($startDate); ?>';
                    const endDate = '<?php echo e($endDate); ?>';
                    const filename = `financial_report_${reportType}_${startDate}_to_${endDate}.csv`;

                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);

                    // Clean up
                    URL.revokeObjectURL(url);

                    // Show success message
                    const successMsg = document.createElement('div');
                    successMsg.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                    successMsg.style.zIndex = '9999';
                    successMsg.innerHTML = `
                        <strong><i class="fas fa-check-circle me-2"></i>Export Complete!</strong><br>
                        File "${filename}" has been downloaded.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(successMsg);

                    setTimeout(() => {
                        successMsg.remove();
                    }, 3000);

                } catch (error) {
                    console.error('Export error:', error);
                    alert('Error exporting data: ' + error.message);
                }
            });
        }

        // Optional: Add print styling
        const printBtn = document.querySelector('[onclick="window.print()"]');
        if (printBtn) {
            printBtn.addEventListener('click', function(e) {
                // Add print-specific styles
                const style = document.createElement('style');
                style.textContent = `
                    @media print {
                        .btn-group, .row.mb-4:first-child, .card-header .btn-group {
                            display: none !important;
                        }
                        .card {
                            break-inside: avoid;
                            page-break-inside: avoid;
                        }
                        table {
                            font-size: 10pt;
                        }
                    }
                `;
                document.head.appendChild(style);

                setTimeout(() => {
                    window.print();
                    style.remove();
                }, 100);
            });
        }
    });

$(document).ready(function() {
    // Add tooltips to period badges
    $('td[title]').each(function() {
        $(this).find('.badge').attr('title', $(this).attr('title'));
    });

    // Initialize tooltips if using Bootstrap 5
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\project\darkfibre-crm\resources\views/finance/reports/reports.blade.php ENDPATH**/ ?>