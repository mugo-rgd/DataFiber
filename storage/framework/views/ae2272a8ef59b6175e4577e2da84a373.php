<?php if(auth()->check() && auth()->user()->role === 'debt_manager'): ?>
    <!-- Debt Manager Menu -->
    

    

    <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('finance.ai.dashboard')); ?>" >
                                    <i class="fas fa-brain"></i> AI Analytics
                                </a>
                            </li>

    <li class="nav-item">
        <a class="nav-link <?php echo e(Request::is('finance/debt/aging-report*') ? 'active' : ''); ?>" href="<?php echo e(route('finance.debt.aging.report')); ?>">
            <i class="fas fa-chart-bar me-2"></i>Aging Report
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo e(Request::is('finance/debt/collection-report*') ? 'active' : ''); ?>" href="<?php echo e(route('finance.debt.collection.report')); ?>">
            <i class="fas fa-file-invoice-dollar me-2"></i>Collection Report
        </a>
    </li>

  <li class="nav-item">
    <a class="nav-link <?php echo e(Request::is('finance/debt/customers*') ? 'active' : ''); ?>" href="<?php echo e(route('finance.debt.customers')); ?>">
        <i class="fas fa-users me-2"></i>Customer Debts
    </a>
</li>
<?php endif; ?>
<?php /**PATH G:\project\darkfibre-crm\resources\views/partials/menus/debt-manager.blade.php ENDPATH**/ ?>