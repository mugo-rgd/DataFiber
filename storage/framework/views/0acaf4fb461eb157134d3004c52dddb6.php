<?php
    $previousUrl = url()->previous();
    $currentUrl = url()->current();

    // If previous URL is the same as current or empty, use customer show route as fallback
    if ($previousUrl === $currentUrl || empty($previousUrl)) {
        $backUrl = route('account-manager.customers.show', $user);
    } else {
        $backUrl = $previousUrl;
    }
?>



<?php $__env->startSection('title', 'Approve Documents - ' . $user->name); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2">Document Approval</h1>
            <p class="text-muted mb-0">Review and approve documents for <?php echo e($user->name); ?></p>
            <small class="text-muted">Customer Email: <?php echo e($user->email); ?> | Role: <span class="badge bg-info"><?php echo e($user->role); ?></span></small>
        </div>
        <div>
          <a href="<?php echo e($backUrl); ?>" class="btn btn-outline-secondary">
    <i class="fas fa-arrow-left me-2"></i>
    <?php if($backUrl === route('account-manager.customers.index', $user)): ?>
        Back to Customer
    <?php else: ?>
        Back to Previous
    <?php endif; ?>
</a>
            <?php if($documents->flatten()->where('status', 'pending_review')->count() > 0): ?>
                <form action="<?php echo e(route('account-manager.documents.bulk-approve', $user)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-success" onclick="return confirm('Approve all pending documents for this customer?')">
                        <i class="fas fa-check-double me-2"></i>Approve All
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo e($documents->flatten()->count()); ?></h4>
                            <small>Total Documents</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo e($documents->flatten()->where('status', 'pending_review')->count()); ?></h4>
                            <small>Pending Approval</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo e($documents->flatten()->where('status', 'approved')->count()); ?></h4>
                            <small>Approved</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo e($documents->flatten()->where('status', 'rejected')->count()); ?></h4>
                            <small>Rejected</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents by Type -->
    <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $documentType => $typeDocuments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">
                <i class="fas fa-folder me-2"></i>
                <?php echo e(ucfirst(str_replace('_', ' ', $documentType))); ?>

                <span class="badge bg-secondary"><?php echo e(count($typeDocuments)); ?></span>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Uploaded</th>
                            <th>Size</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $typeDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php
                                        $fileIcon = 'file';
                                        $fileName = strtolower($document->file_name);

                                        if (str_contains($fileName, '.pdf')) {
                                            $fileIcon = 'file-pdf';
                                        } elseif (str_contains($fileName, '.doc') || str_contains($fileName, '.docx')) {
                                            $fileIcon = 'file-word';
                                        } elseif (str_contains($fileName, '.jpg') || str_contains($fileName, '.jpeg') || str_contains($fileName, '.png') || str_contains($fileName, '.gif')) {
                                            $fileIcon = 'file-image';
                                        } elseif (str_contains($fileName, '.xls') || str_contains($fileName, '.xlsx')) {
                                            $fileIcon = 'file-excel';
                                        } elseif (str_contains($fileName, '.zip') || str_contains($fileName, '.rar')) {
                                            $fileIcon = 'file-archive';
                                        }
                                    ?>
                                    <i class="fas fa-<?php echo e($fileIcon); ?> text-primary me-2"></i>
                                    <div>
                                        <div class="fw-bold"><?php echo e($document->file_name); ?></div>
                                        <?php if($document->rejection_reason): ?>
                                            <small class="text-danger">Reason: <?php echo e($document->rejection_reason); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo e($document->created_at->format('M d, Y')); ?></td>
                            <td><?php echo e(round($document->file_size / 1024, 1)); ?> KB</td>
                            <td>
                                <?php if($document->status === 'approved'): ?>
                                    <span class="badge bg-success">Approved</span>
                                    <br>
                                    <small class="text-muted">
                                        by <?php echo e($document->approver->name ?? 'N/A'); ?>

                                        <br>
                                        on <?php echo e($document->approved_at ? \Carbon\Carbon::parse($document->approved_at)->format('M d, Y') : 'N/A'); ?>

                                    </small>
                                <?php elseif($document->status === 'rejected'): ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Pending</span>
                                <?php endif; ?>
                            </td>
                           <td>
    <div class="btn-group btn-group-sm">
        <!-- These should now work with the correct route names -->
        <a href="<?php echo e(route('account-manager.documents.view', $document)); ?>" class="btn btn-outline-primary" target="_blank" title="View">
            <i class="fas fa-eye"></i>
        </a>
        <a href="<?php echo e(route('account-manager.documents.download', $document)); ?>" class="btn btn-outline-success" title="Download">
            <i class="fas fa-download"></i>
        </a>

        <?php if($document->status === 'pending_review'): ?>
            <form action="<?php echo e(route('account-manager.documents.approve-single', $document)); ?>" method="POST" class="d-inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-outline-success" title="Approve" onclick="return confirm('Approve this document?')">
                    <i class="fas fa-check"></i>
                </button>
            </form>
            <button type="button" class="btn btn-outline-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#rejectModal<?php echo e($document->id); ?>"
                    title="Reject">
                <i class="fas fa-times"></i>
            </button>
        <?php endif; ?>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal<?php echo e($document->id); ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo e(route('account-manager.documents.reject', $document)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <p>Rejecting: <strong><?php echo e($document->file_name); ?></strong></p>
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Reason for Rejection</label>
                            <textarea class="form-control" id="rejection_reason"
                                      name="rejection_reason" rows="3"
                                      placeholder="Please provide a reason for rejection..."
                                      required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <!-- Empty State -->
    <?php if($documents->count() === 0): ?>
    <div class="text-center py-5">
        <i class="fas fa-file-upload fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No Documents Found</h4>
        <p class="text-muted">This customer hasn't uploaded any documents yet.</p>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\project\darkfibre-crm\resources\views/account-manager/documents-approval.blade.php ENDPATH**/ ?>