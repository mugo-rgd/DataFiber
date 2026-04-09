<?php $__env->startSection('title', 'Transactions'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-exchange-alt me-2"></i>Transactions
                </h1>
                <a href="<?php echo e(route('finance.transactions.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>New Transaction
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Transactions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($transactions->total()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Income (USD)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?php echo e(number_format($transactionStats['total_income_usd'] ?? 0, 2)); ?>

                            </div>
                            <div class="text-xs font-weight-bold text-success mt-2">
                                Total Income (KES)</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-600">
                                KES <?php echo e(number_format($transactionStats['total_income_ksh'] ?? 0, 2)); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Expenses (USD)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?php echo e(number_format($transactionStats['total_expenses_usd'] ?? 0, 2)); ?>

                            </div>
                            <div class="text-xs font-weight-bold text-danger mt-2">
                                Total Expenses (KES)</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-600">
                                KES <?php echo e(number_format($transactionStats['total_expenses_ksh'] ?? 0, 2)); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Net Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?php echo e(number_format(($transactionStats['total_income_usd'] ?? 0) - ($transactionStats['total_expenses_usd'] ?? 0), 2)); ?>

                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-600">
                                KES <?php echo e(number_format(($transactionStats['total_income_ksh'] ?? 0) - ($transactionStats['total_expenses_ksh'] ?? 0), 2)); ?>

                            </div>
                            <div class="text-xs text-muted mt-2">
                                Pending: <?php echo e($transactionStats['pending_transactions'] ?? 0); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-md-12">
            <form method="GET" action="<?php echo e(route('finance.transactions.index')); ?>" class="row g-2">
                <div class="col-md-2">
                    <select name="type" class="form-select" onchange="this.form.submit()">
                        <option value="all" <?php echo e(request('type') == 'all' ? 'selected' : ''); ?>>All Types</option>
                        <option value="invoice" <?php echo e(request('type') == 'invoice' ? 'selected' : ''); ?>>Invoice</option>
                        <option value="payment" <?php echo e(request('type') == 'payment' ? 'selected' : ''); ?>>Payment</option>
                        <option value="credit" <?php echo e(request('type') == 'credit' ? 'selected' : ''); ?>>Credit</option>
                        <option value="debit" <?php echo e(request('type') == 'debit' ? 'selected' : ''); ?>>Debit</option>
                        <option value="refund" <?php echo e(request('type') == 'refund' ? 'selected' : ''); ?>>Refund</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="direction" class="form-select" onchange="this.form.submit()">
                        <option value="all" <?php echo e(request('direction') == 'all' ? 'selected' : ''); ?>>All Directions</option>
                        <option value="in" <?php echo e(request('direction') == 'in' ? 'selected' : ''); ?>>Income (In)</option>
                        <option value="out" <?php echo e(request('direction') == 'out' ? 'selected' : ''); ?>>Expense (Out)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="all" <?php echo e(request('category') == 'all' ? 'selected' : ''); ?>>All Categories</option>
                        <option value="invoice_payment" <?php echo e(request('category') == 'invoice_payment' ? 'selected' : ''); ?>>Invoice Payment</option>
                        <option value="refund" <?php echo e(request('category') == 'refund' ? 'selected' : ''); ?>>Refund</option>
                        <option value="fee" <?php echo e(request('category') == 'fee' ? 'selected' : ''); ?>>Fee</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="currency" class="form-select" onchange="this.form.submit()">
                        <option value="all" <?php echo e(request('currency') == 'all' ? 'selected' : ''); ?>>All Currencies</option>
                        <option value="USD" <?php echo e(request('currency') == 'USD' ? 'selected' : ''); ?>>USD</option>
                        <option value="KSH" <?php echo e(request('currency') == 'KSH' ? 'selected' : ''); ?>>KES</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>" placeholder="Start Date">
                </div>
                <div class="col-md-2">
                    <input type="date" name="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>" placeholder="End Date">
                </div>
                <div class="col-md-12 mt-2">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="<?php echo e(route('finance.transactions.index')); ?>" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>All Transactions
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Transaction #</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Direction</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Reference</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><code><?php echo e($transaction->transaction_number); ?></code></td>
                            <td>
                                <?php if($transaction->user): ?>
                                    <span class="badge bg-info">#<?php echo e($transaction->user_id); ?></span>
                                    <br>
                                    <small><?php echo e($transaction->user->name ?? 'N/A'); ?></small>
                                <?php else: ?>
                                    <span class="badge bg-secondary">#<?php echo e($transaction->user_id); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($transaction->type === 'invoice' ? 'primary' : ($transaction->type === 'payment' ? 'success' : 'secondary')); ?>">
                                    <?php echo e(ucfirst($transaction->type)); ?>

                                </span>
                            </td>
                            <td>
                                <?php if($transaction->direction === 'in'): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-arrow-down me-1"></i> In
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger">
                                        <i class="fas fa-arrow-up me-1"></i> Out
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e(Str::limit($transaction->description, 50)); ?></td>
                            <td class="fw-bold">
                                <?php if($transaction->currency === 'USD'): ?>
                                    <span class="text-primary">$</span>
                                <?php elseif($transaction->currency === 'KSH'): ?>
                                    <span class="text-success">KSh</span>
                                <?php endif; ?>
                                <?php echo e(number_format($transaction->amount, 2)); ?>

                            </td>
                            <td>
                                <?php if($transaction->currency === 'USD'): ?>
                                    <span class="badge bg-primary">USD</span>
                                <?php elseif($transaction->currency === 'KSH'): ?>
                                    <span class="badge bg-success">KES</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?php echo e($transaction->currency); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e(\Carbon\Carbon::parse($transaction->transaction_date)->format('M j, Y')); ?></td>
                            <td>
                                <?php
                                    $statusColor = match($transaction->status) {
                                        'completed' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger',
                                        default => 'info'
                                    };
                                ?>
                                <span class="badge bg-<?php echo e($statusColor); ?>">
                                    <?php echo e(ucfirst($transaction->status)); ?>

                                </span>
                            </td>
                            <td>
                                <?php if($transaction->reference): ?>
                                    <small class="text-muted"><?php echo e($transaction->reference); ?></small>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?php echo e(route('finance.transactions.show', $transaction->id)); ?>" class="btn btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('finance.transactions.edit', $transaction->id)); ?>" class="btn btn-outline-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="<?php echo e(route('finance.transactions.destroy', $transaction->id)); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this transaction?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <i class="fas fa-exchange-alt fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No transactions found</h5>
                                <p class="text-muted mb-4">Get started by creating your first transaction.</p>
                                <a href="<?php echo e(route('finance.transactions.create')); ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Create First Transaction
                                </a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing <?php echo e($transactions->firstItem() ?? 0); ?> to <?php echo e($transactions->lastItem() ?? 0); ?> of <?php echo e($transactions->total()); ?> entries
                </div>
                <div>
                    <?php echo e($transactions->appends(request()->query())->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }
    .border-left-success {
        border-left: 4px solid #1cc88a !important;
    }
    .border-left-danger {
        border-left: 4px solid #e74a3b !important;
    }
    .border-left-info {
        border-left: 4px solid #36b9cc !important;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
    .table td {
        vertical-align: middle;
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\project\darkfibre-crm\resources\views/finance/transactions/index.blade.php ENDPATH**/ ?>