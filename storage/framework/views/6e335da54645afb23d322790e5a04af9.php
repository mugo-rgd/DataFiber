<?php $__env->startSection('title', 'Manage Quotations'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-file-invoice-dollar fa-2x text-primary me-3"></i>
            <div>
                <h1 class="h3 mb-0 text-gray-800">Manage Quotations</h1>
                <small class="text-muted">Create, approve, and send quotations to customers</small>
            </div>
        </div>
        <a href="<?php echo e(route('admin.quotations.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Quotation
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?php echo e($quotations->total()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
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
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Draft</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?php echo e($quotations->where('status', 'draft')->count()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-edit fa-2x text-gray-300"></i>
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
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Approved</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?php echo e($quotations->where('status', 'approved')->count()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Sent</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?php echo e($quotations->where('status', 'sent')->count()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-paper-plane fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quotations Table -->
    <div class="card shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Quotations</h5>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo e(request()->fullUrlWithQuery(['status' => ''])); ?>">All Status</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo e(request()->fullUrlWithQuery(['status' => 'draft'])); ?>">Draft</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(request()->fullUrlWithQuery(['status' => 'approved'])); ?>">Approved</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(request()->fullUrlWithQuery(['status' => 'sent'])); ?>">Sent</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(request()->fullUrlWithQuery(['status' => 'rejected'])); ?>">Rejected</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <?php if($quotations->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Quotation #</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Valid Until</th>
                                <th>Created</th>
                                <th class="pe-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo $__env->make('admin.quotations.partials.quotation-row', ['quotation' => $quotation, 'isAdmin' => $isAdmin], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                    <div class="text-muted small">
                        Showing <?php echo e($quotations->firstItem()); ?> to <?php echo e($quotations->lastItem()); ?> of <?php echo e($quotations->total()); ?> quotations
                    </div>
                    <div>
                        <?php echo e($quotations->withQueryString()->links()); ?>

                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice-dollar fa-4x text-muted opacity-25 mb-4"></i>
                    <h5 class="text-muted mb-3">No quotations found</h5>
                    <p class="text-muted mb-4">
                        <?php if(request('status')): ?>
                            No <?php echo e(request('status')); ?> quotations found.
                        <?php else: ?>
                            Create your first quotation to get started.
                        <?php endif; ?>
                    </p>
                    <a href="<?php echo e(route('admin.quotations.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Quotation
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Action Modals -->
<?php echo $__env->make('admin.quotations.modals.approve-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('admin.quotations.modals.reject-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('admin.quotations.modals.send-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Fix for modal aria-hidden */
    .modal[aria-hidden="true"] {
        pointer-events: none;
    }

    .modal[aria-hidden="false"] {
        pointer-events: auto;
    }

    /* Ensure modal backdrop doesn't interfere */
    .modal-backdrop {
        z-index: 1040;
    }

    .modal {
        z-index: 1050;
    }

    /* Quotation specific styles */
    .quotation-status-badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
        border-radius: 0.25rem;
    }

    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    /* Modal styles */
    .modal-header {
        border-bottom: none;
    }

    .modal-footer {
        border-top: 1px solid #dee2e6;
    }

    /* Button states */
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Table hover effects */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    /* Loading spinner */
    .fa-spinner {
        animation: fa-spin 1s infinite linear;
    }

    @keyframes fa-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Alert positioning */
    .fixed-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 500px;
    }

    /* Debug styles */
    .btn-approve {
        border: 2px solid #28a745 !important;
        background-color: rgba(40, 167, 69, 0.1) !important;
        cursor: pointer !important;
    }

    .btn-approve:hover {
        background-color: #28a745 !important;
        color: white !important;
    }

    /* Make modals more visible */
    .modal-header {
        border-bottom: 2px solid;
    }

    #approveQuotationModal .modal-header {
        border-color: #28a745;
    }

    #rejectQuotationModal .modal-header {
        border-color: #dc3545;
    }

    #sendQuotationModal .modal-header {
        border-color: #17a2b8;
    }

    /* Character counters */
    .form-text.text-end {
        font-size: 0.75rem;
        color: #6c757d;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('styles'); ?>
<style>
    /* Fix for modal aria-hidden */
    .modal[aria-hidden="true"] {
        pointer-events: none;
    }

    .modal[aria-hidden="false"] {
        pointer-events: auto;
    }

    /* Ensure modal backdrop doesn't interfere */
    .modal-backdrop {
        z-index: 1040;
    }

    .modal {
        z-index: 1050;
    }

    /* Quotation specific styles */
    .quotation-status-badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
        border-radius: 0.25rem;
    }

    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    /* Modal styles */
    .modal-header {
        border-bottom: none;
    }

    .modal-footer {
        border-top: 1px solid #dee2e6;
    }

    /* Button states */
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Table hover effects */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    /* Loading spinner */
    .fa-spinner {
        animation: fa-spin 1s infinite linear;
    }

    @keyframes fa-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Alert positioning */
    .fixed-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 500px;
    }

    /* Debug styles */
    .btn-approve {
        border: 2px solid #28a745 !important;
        background-color: rgba(40, 167, 69, 0.1) !important;
        cursor: pointer !important;
    }

    .btn-approve:hover {
        background-color: #28a745 !important;
        color: white !important;
    }

    /* Make modals more visible */
    .modal-header {
        border-bottom: 2px solid;
    }

    #approveQuotationModal .modal-header {
        border-color: #28a745;
    }

    #rejectQuotationModal .modal-header {
        border-color: #dc3545;
    }

    #sendQuotationModal .modal-header {
        border-color: #17a2b8;
    }

    /* Character counters */
    .form-text.text-end {
        font-size: 0.75rem;
        color: #6c757d;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\project\darkfibre-crm\resources\views/admin/quotations/index.blade.php ENDPATH**/ ?>