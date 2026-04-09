<?php
use App\Models\ConsolidatedBilling;
use Carbon\Carbon;
?>



<?php $__env->startSection('title', 'Manage Consolidated Billings'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Manage Consolidated Billings
                </h1>
                <div class="btn-group">
                    <a href="<?php echo e(route('finance.billing.createSingle')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Manual Billing
                    </a>
                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    <button class="btn btn-info" onclick="runBillingProcess()" title="Run Automated Billing">
                        <i class="fas fa-robot me-2"></i>Run Auto Billing
                    </button>
                </div>
            </div>
            <p class="text-muted mb-0">
                <i class="fas fa-info-circle me-1"></i>
                Consolidated billings group multiple leases from the same customer into single invoices
            </p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Invoices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($billings->total()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid Invoices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(ConsolidatedBilling::where('status', 'paid')->count()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Invoices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(ConsolidatedBilling::where('status', 'pending')->count()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            <?php
                                $overdueCount = ConsolidatedBilling::where('status', 'pending')
                                    ->whereDate('due_date', '<', Carbon::today())
                                    ->count();
                            ?>
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Overdue Invoices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($overdueCount); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
    <div class="btn-group">
        <button type="button" class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown">
            <i class="fas fa-bell me-1"></i> Bulk Email Actions
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" onclick="sendOverdueNotices()">
                <i class="fas fa-exclamation-triangle me-2"></i> Send Overdue Notices
            </a></li>
            <li><a class="dropdown-item" href="#" onclick="sendDueReminders()">
                <i class="fas fa-clock me-2"></i> Send Due Reminders
            </a></li>
        </ul>
    </div>
</div>
    <!-- Billings Table -->
   <!-- Billings Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Consolidated Invoices</h5>
                <span class="badge bg-info">
                    <i class="fas fa-layer-group me-1"></i>
                    <?php echo e($totalLineItems ?? 0); ?> total lease items
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="120">Invoice #</th>
                            <th>Customer</th>
                            <th width="100">Leases</th>
                            <th width="120" class="text-end">Total Amount</th>
                            <th width="100">Invoice Date</th>
                            <th width="100">Due Date</th>
                            <th width="100">Currency</th>
                            <th width="100">Status</th>
                            <th width="120">KRA Status</th>
                            <th width="180" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $billings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $billing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $isOverdue = $billing->status === 'pending' &&
                                         $billing->due_date &&
                                         $billing->due_date < now();

                            // TEVIN status colors
                            $tevinStatusColors = [
                                'queued' => 'info',
                                'processing' => 'warning',
                                'submitted' => 'success',
                                'tev_submitted' => 'success',
                                'duplicate' => 'secondary',
                                'tev_duplicate' => 'secondary',
                                'failed' => 'danger',
                                'tev_failed' => 'danger',
                                'permanently_failed' => 'dark',
                            ];
                            $tevinStatus = $billing->tevin_status ?? $billing->status;
                            $tevinColor = $tevinStatusColors[$tevinStatus] ?? 'secondary';

                            // TEVIN status label
                            $tevinStatusLabels = [
                                'queued' => 'Queued',
                                'processing' => 'Processing',
                                'submitted' => 'Submitted',
                                'tev_submitted' => 'Submitted',
                                'duplicate' => 'Duplicate',
                                'tev_duplicate' => 'Duplicate',
                                'failed' => 'Failed',
                                'tev_failed' => 'Failed',
                                'permanently_failed' => 'Permanent Failed',
                            ];
                            $tevinLabel = $tevinStatusLabels[$tevinStatus] ?? 'Not Submitted';
                        ?>
                        <tr class="<?php echo e($isOverdue ? 'table-danger' : ''); ?>">
                            <td>
                                <strong><?php echo e($billing->billing_number); ?></strong>
                                <br>
                                <small class="text-muted">#<?php echo e($billing->id); ?></small>
                            </td>
                            <td>
                                <?php if($billing->user): ?>
                                    <strong><?php echo e($billing->user->name); ?></strong>
                                    <?php if($billing->user->company_name): ?>
                                        <br><small class="text-muted"><?php echo e($billing->user->company_name); ?></small>
                                    <?php endif; ?>
                                    <br><small class="text-muted"><?php echo e($billing->user->email); ?></small>
                                <?php else: ?>
                                    <span class="text-muted">Customer Not Found</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?php echo e($billing->lineItems->count()); ?></span>
                                <button type="button" class="btn btn-sm btn-link"
                                        data-bs-toggle="popover"
                                        data-bs-placement="right"
                                        data-bs-html="true"
                                        data-bs-content="
                                            <strong>Included Leases:</strong><br>
                                            <?php $__currentLoopData = $billing->lineItems->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($item->lease): ?>
                                                    • <?php echo e($item->lease->lease_number); ?> - <?php echo e($item->currency); ?> <?php echo e(number_format($item->amount, 2)); ?><br>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($billing->lineItems->count() > 5): ?>
                                                ... and <?php echo e($billing->lineItems->count() - 5); ?> more
                                            <?php endif; ?>
                                        ">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </td>
                            <td class="text-end">
                                <strong><?php echo e($billing->currency); ?> <?php echo e(number_format($billing->total_amount, 2)); ?></strong>
                                <?php if($billing->lineItems->count() > 1): ?>
                                    <br>
                                    <small class="text-muted">
                                        (Avg: <?php echo e($billing->currency); ?> <?php echo e(number_format($billing->total_amount / $billing->lineItems->count(), 2)); ?>/lease)
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($billing->billing_date->format('M j, Y')); ?></td>
                            <td>
                                <span class="<?php echo e($isOverdue ? 'text-danger fw-bold' : ''); ?>">
                                    <?php echo e($billing->due_date->format('M j, Y')); ?>

                                    <?php if($isOverdue): ?>
                                        <br>
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-circle"></i> Overdue
                                        </small>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark"><?php echo e($billing->currency); ?></span>
                            </td>
                            <td>
                                <?php
                                    $statusColor = $billing->status === 'paid' ? 'success' :
                                                  ($isOverdue ? 'danger' :
                                                  ($billing->status === 'draft' ? 'secondary' : 'warning'));
                                ?>
                                <span class="badge bg-<?php echo e($statusColor); ?>">
                                    <?php echo e(ucfirst($billing->status)); ?>

                                    <?php if($isOverdue): ?>
                                        (Overdue)
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($tevinColor); ?>" data-bs-toggle="tooltip" title="<?php echo e($billing->tevin_error_message ?? ''); ?>">
                                    <?php echo e($tevinLabel); ?>

                                </span>
                                <?php if($billing->tev_control_code): ?>
                                    <button type="button" class="btn btn-sm btn-link p-0" onclick="showTevinDetails(<?php echo e($billing->id); ?>)" title="View KRA Details">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <!-- View Details -->
                                    <a href="<?php echo e(route('finance.billing.show', $billing->id)); ?>"
                                       class="btn btn-outline-primary"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Download PDF -->
                                    <a href="<?php echo e(route('finance.billing.download', $billing->id)); ?>"
                                       class="btn btn-outline-info"
                                       title="Download PDF">
                                        <i class="fas fa-download"></i>
                                    </a>

                                    <a href="<?php echo e(route('finance.billing.preview', $billing->id)); ?>"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Preview PDF"
                                       data-bs-toggle="tooltip"
                                       target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>

                                    <!-- More Actions Dropdown -->
                                    <button type="button" class="btn btn-outline-success dropdown-toggle"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            title="More Actions">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <?php if($billing->status !== 'paid'): ?>
                                        <li>
                                            <a class="dropdown-item text-success" href="#"
                                               onclick="markAsPaid(<?php echo e($billing->id); ?>)">
                                                <i class="fas fa-check me-2"></i>Mark as Paid
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-primary" href="#"
                                               onclick="submitKra(<?php echo e($billing->id); ?>)">
                                                <i class="fas fa-dollar me-2"></i>Submit KRA
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);"
                                               onclick="sendReminder(<?php echo e($billing->id); ?>);">
                                                <i class="fas fa-envelope me-2"></i>Send Reminder
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-info"
                                               href="<?php echo e(route('finance.billing.edit', $billing->id)); ?>">
                                                <i class="fas fa-edit me-2"></i>Edit Invoice
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-warning" href="#"
                                               onclick="duplicateBilling(<?php echo e($billing->id); ?>)">
                                                <i class="fas fa-copy me-2"></i>Duplicate
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                               href="<?php echo e(route('finance.billing.show', $billing->id)); ?>"
                                               target="_blank">
                                                <i class="fas fa-file-pdf me-2"></i>View PDF
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#"
                                               onclick="deleteBilling(<?php echo e($billing->id); ?>)">
                                                <i class="fas fa-trash me-2"></i>Delete Invoice
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>

                            <td class="text-center">
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-sm btn-info" onclick="sendReminder(<?php echo e($billing->id); ?>)" title="Send Reminder">
            <i class="fas fa-bell"></i>
        </button>
        <button type="button" class="btn btn-sm btn-primary" onclick="sendInvoice(<?php echo e($billing->id); ?>)" title="Email Invoice">
            <i class="fas fa-envelope"></i>
        </button>
        <a href="<?php echo e(route('finance.billing.show', $billing->id)); ?>" class="btn btn-sm btn-secondary" title="View">
            <i class="fas fa-eye"></i>
        </a>
    </div>
</td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No consolidated invoices found</h5>
                                <p class="text-muted">Run the automated billing process or create a manual invoice.</p>
                                <div class="mt-3">
                                    <button class="btn btn-primary" onclick="runBillingProcess()">
                                        <i class="fas fa-robot me-2"></i>Run Auto Billing
                                    </button>
                                    
                                </div>
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
                    Showing <?php echo e($billings->firstItem() ?? 0); ?> to <?php echo e($billings->lastItem() ?? 0); ?> of <?php echo e($billings->total()); ?> invoices
                    (<?php echo e($totalLineItems ?? 0); ?> total lease items)
                </div>
                <?php echo e($billings->links()); ?>

            </div>
        </div>
    </div>
</div>

<!-- TEVIN Details Modal -->
<div class="modal fade" id="tevinDetailsModal" tabindex="-1" aria-labelledby="tevinDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tevinDetailsModalLabel">KRA eTIMS Submission Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="tevinDetailsContent">
                    <!-- Details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="retryKraSubmission()" id="retryKraBtn" style="display: none;">
                    <i class="fas fa-redo me-2"></i>Retry Submission
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Consolidated Invoices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm" method="GET">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="draft">Draft</option>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="overdue">Overdue</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="currency" class="form-label">Currency</label>
                            <select class="form-select" id="currency" name="currency">
                                <option value="">All Currencies</option>
                                <option value="USD">USD</option>
                                <option value="KES">KES</option>
                                <option value="EUR">EUR</option>
                                <option value="GBP">GBP</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_from" class="form-label">Invoice Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_to" class="form-label">Invoice Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="due_date_from" class="form-label">Due Date From</label>
                            <input type="date" class="form-control" id="due_date_from" name="due_date_from">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="due_date_to" class="form-label">Due Date To</label>
                            <input type="date" class="form-control" id="due_date_to" name="due_date_to">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="min_amount" class="form-label">Minimum Amount</label>
                            <input type="number" class="form-control" id="min_amount" name="min_amount" step="0.01" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="max_amount" class="form-label">Maximum Amount</label>
                            <input type="number" class="form-control" id="max_amount" name="max_amount" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Customer</label>
                        <select name="customer_id" class="form-control">
    <option value="">Select a customer</option>
    <?php if($customers && $customers->count() > 0): ?>
        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!$customer || !is_object($customer) || empty($customer->id)) continue; ?>

            <option value="<?php echo e($customer->id); ?>"
                <?php echo e(old('customer_id', $selectedCustomerId ?? '') == $customer->id ? 'selected' : ''); ?>>
                <?php echo e($customer->name); ?> (<?php echo e($customer->email); ?>)
            </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <option value="">No customers found</option>
    <?php endif; ?>
 </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-outline-danger" onclick="resetFilters()">Reset Filters</button>
                <button type="button" class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Initialize popovers and tooltips
document.addEventListener('DOMContentLoaded', function() {
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

var currentBillingId = null;

function showTevinDetails(billingId) {
    currentBillingId = billingId;
    showLoading();

    axios.get(`/billing/${billingId}/kra-status`)
        .then(response => {
            hideLoading();
            const data = response.data.data;

            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Submission Status</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <th width="140">Status:</th>
                                        <td>
                                            <span class="badge bg-${getTevinStatusColor(data.tevin_status)}">
                                                ${data.tevin_status_label}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Control Code:</th>
                                        <td><code>${data.tevin_control_code || 'Not available'}</code></td>
                                    </tr>
                                    <tr>
                                        <th>Invoice Number:</th>
                                        <td>${data.tevin_invoice_number || 'Not available'}</td>
                                    </tr>
                                    <tr>
                                        <th>Submitted At:</th>
                                        <td>${data.tevin_submitted_at || 'Not submitted'}</td>
                                    </tr>
                                    ${data.tevin_error_message ? `
                                    <tr>
                                        <th>Error:</th>
                                        <td class="text-danger">${data.tevin_error_message}</td>
                                    </tr>
                                    ` : ''}
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">KRA eTIMS Information</h6>
                            </div>
                            <div class="card-body">
            `;

            if (data.tevin_qr_code) {
                html += `
                    <div class="text-center mb-3">
                        <div class="mb-2">
                            <strong>QR Code:</strong>
                        </div>
                        <a href="${data.tevin_qr_code}" target="_blank" class="d-block">
                            <img src="${data.tevin_qr_code}" alt="KRA QR Code" style="max-width: 200px; max-height: 200px;" class="img-fluid border">
                        </a>
                        <small class="text-muted d-block mt-2">Click to open in new tab</small>
                    </div>
                `;
            } else {
                html += `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-qrcode fa-3x mb-3"></i>
                        <p>No QR Code available</p>
                    </div>
                `;
            }

            html += `
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('tevinDetailsContent').innerHTML = html;

            // Show retry button if applicable
            const retryBtn = document.getElementById('retryKraBtn');
            retryBtn.style.display = data.can_retry ? 'inline-block' : 'none';

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('tevinDetailsModal'));
            modal.show();
        })
        .catch(error => {
            hideLoading();
            console.error('Error loading TEVIN details:', error);
            alert('Failed to load TEVIN details. Please try again.');
        });
}

function getTevinStatusColor(status) {
    const colors = {
        'queued': 'info',
        'processing': 'warning',
        'submitted': 'success',
        'tev_submitted': 'success',
        'duplicate': 'secondary',
        'tev_duplicate': 'secondary',
        'failed': 'danger',
        'tev_failed': 'danger',
        'permanently_failed': 'dark'
    };
    return colors[status] || 'secondary';
}

function retryKraSubmission() {
    if (!currentBillingId) return;
    if (!confirm('Are you sure you want to retry KRA submission?')) return;

    const retryBtn = document.getElementById('retryKraBtn');
    retryBtn.disabled = true;
    retryBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Retrying...';

    axios.post(`/billing/${currentBillingId}/retry-kra`)
        .then(response => {
            alert('Retry queued successfully');
            location.reload();
        })
        .catch(error => {
            alert(error.response?.data?.message || 'Retry failed');
            retryBtn.disabled = false;
            retryBtn.innerHTML = '<i class="fas fa-redo me-2"></i>Retry Submission';
        });
}

function runBillingProcess() {
    if (!confirm('Run automated billing process? This will generate invoices for all due leases.')) {
        return;
    }

    showLoading();

    fetch("<?php echo e(route('finance.billing.run-process')); ?>", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            alert('✅ ' + data.message + '\n\nProcessed: ' + data.processed + ' invoices\nLine Items: ' + data.line_items + '\nErrors: ' + data.errors);
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(err => {
        hideLoading();
        console.error(err);
        alert('❌ An error occurred while running the billing process.');
    });
}

function submitToTevin(billingId) {
    axios.post(`/billing/${billingId}/submit-kra`)
        .then(response => {
            showSuccess('Invoice queued for KRA submission');
            setTimeout(() => location.reload(), 2000);
        })
        .catch(error => {
            showError(error.response.data.message || 'Submission failed');
        });
}

function retryTevinSubmission(billingId) {
    if (!confirm('Are you sure you want to retry KRA submission?')) return;

    axios.post(`/billing/${billingId}/retry-kra`)
        .then(response => {
            showSuccess('Retry queued');
            setTimeout(() => location.reload(), 2000);
        })
        .catch(error => {
            showError(error.response.data.message || 'Retry failed');
        });
}

function sendReminder(id) {
    if (!id) return;
    if (!confirm("Send payment reminder for this invoice?")) return;

    showLoading();

    const url = "<?php echo e(route('finance.billing.send-reminder', ':id')); ?>".replace(':id', id);

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            alert("✅ " + data.message);
        } else {
            alert("❌ " + data.message);
        }
    })
    .catch(err => {
        hideLoading();
        console.error(err);
        alert("❌ An error occurred while sending the reminder.");
    });
}

function markAsPaid(id) {
    if (!confirm('Mark this invoice as paid?')) return;

    showLoading();

    fetch("<?php echo e(route('finance.billing.mark-paid', ':id')); ?>".replace(':id', id), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(err => {
        hideLoading();
        console.error(err);
        alert('❌ An error occurred.');
    });
}

function submitKra(id) {
    if (!confirm('Submit this invoice to KRA eTIMS?')) return;

    showLoading();

    fetch("<?php echo e(route('finance.billing.submit-kra', ':id')); ?>".replace(':id', id), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(err => {
        hideLoading();
        console.error(err);
        alert('❌ An error occurred.');
    });
}

function duplicateBilling(id) {
    if (!confirm('Duplicate this invoice?')) return;

    showLoading();

    fetch("<?php echo e(route('finance.billing.duplicate', ':id')); ?>".replace(':id', id), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            alert('✅ ' + data.message);
            window.location.href = data.redirect;
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(err => {
        hideLoading();
        console.error(err);
        alert('❌ An error occurred.');
    });
}

function deleteBilling(billingId) {
    if (!confirm('Are you sure you want to delete this invoice? This action cannot be undone.')) {
        return;
    }

    showLoading();

    fetch(`/finance/billing/${billingId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(err => {
        hideLoading();
        console.error(err);
        alert('❌ Error deleting invoice.');
    });
}

function applyFilters() {
    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form));
    window.location.href = '<?php echo e(route("finance.billing.index")); ?>?' + params.toString();
}

function resetFilters() {
    window.location.href = '<?php echo e(route("finance.billing.index")); ?>';
}

function showLoading() {
    if (!document.getElementById('loadingOverlay')) {
        const overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        `;
        overlay.innerHTML = `
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        `;
        document.body.appendChild(overlay);
    }
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.remove();
    }
}

function showSuccess(message) {
    // Implement toast notification or alert
    alert('✅ ' + message);
}

function showError(message) {
    alert('❌ ' + message);
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.02);
    }
    .badge {
        font-size: 0.85em;
    }
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .popover {
        max-width: 400px;
    }
    .table-danger {
        background-color: rgba(220,53,69,.1) !important;
    }
    .tevin-status-badge {
        cursor: pointer;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function sendReminder(billingId) {
    if (confirm('Send payment reminder to customer?')) {
        fetch(`/finance/emails/billing/${billingId}/reminder`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error sending email. Check console for details.');
        });
    }
}

function sendInvoice(billingId) {
    if (confirm('Send invoice email to customer?')) {
        fetch(`/finance/emails/billing/${billingId}/invoice`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error sending email. Check console for details.');
        });
    }
}

function sendOverdueNotices() {
    if (confirm('Send overdue notices to ALL customers with overdue invoices?')) {
        fetch('/finance/emails/overdue-notices', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('✅ ' + data.message);
            if (data.errors && data.errors.length) {
                console.log('Errors:', data.errors);
            }
        });
    }
}

function sendDueReminders() {
    if (confirm('Send due reminders to customers with invoices due in next 3 days?')) {
        fetch('/finance/emails/due-reminders', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('✅ ' + data.message);
        });
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\project\darkfibre-crm\resources\views/finance/billing/index.blade.php ENDPATH**/ ?>