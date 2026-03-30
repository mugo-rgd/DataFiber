<?php $__env->startSection('title', 'Fibre Link Summary Report - Dark Fibre CRM'); ?>

<?php
    $isPdf = $isPdf ?? false;
?>

<?php
function pdfIcon($class) {
    return empty($GLOBALS['isPdf']) ? "<i class=\"$class\"></i>" : '';
}
?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Header with actions - Hide in PDF -->
    <?php if(!$isPdf): ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-bar text-primary me-2"></i>Summary Report
            </h1>
            <p class="mb-0 text-muted">Comprehensive overview of fibre network performance</p>
        </div>
        <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-lg-end">
            <a href="<?php echo e(route('conversion-data.index')); ?>" class="btn btn-light">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>

            <a href="<?php echo e(route('conversion-data.summary.pdf')); ?>"
               class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i> Download PDF
            </a>

            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="container-fluid px-4 py-3">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>Total Contracts</h6>
                        <h2><?php echo e(number_format($summary['total_contracts'])); ?></h2>
                        <small><?php echo e(number_format($summary['total_customers'])); ?> customers</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Monthly Revenue</h6>
                        <h5>$<?php echo e(number_format($summary['total_monthly_value_usd'], 2)); ?></h5>
                        <h5>KSh <?php echo e(number_format($summary['total_monthly_value_kes'], 2)); ?></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6>Total Contract Value</h6>
                        <h5>$<?php echo e(number_format($summary['total_contract_value_usd'], 2)); ?></h5>
                        <h5>KSh <?php echo e(number_format($summary['total_contract_value_kes'], 2)); ?></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Average / Month</h6>
                        <h5>$<?php echo e(number_format($summary['avg_monthly_usd'], 2)); ?></h5>
                        <h5>KSh <?php echo e(number_format($summary['avg_monthly_kes'], 2)); ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid px-4 pb-4">
        <div class="row g-3">

            <!-- Left Column -->
            <div class="col-lg-8">

                <!-- Link Class Distribution -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><?php echo pdfIcon('fas fa-chart-pie text-primary me-2'); ?>Link Class Distribution</h5>
                    </div>
                    <div class="card-body">
                        <?php if($isPdf): ?>
                            <!-- PDF Version - Table only -->
                            <?php $totalClassCount = $linkClassDistribution->sum('count'); ?>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th class="text-end">Count</th>
                                        <th class="text-end">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $linkClassDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-<?php echo e($row['label'] === 'PREMIUM' ? 'success' : ($row['label'] === 'METRO' ? 'info' : 'secondary')); ?>">
                                                <?php echo e($row['label']); ?>

                                            </span>
                                        </td>
                                        <td class="text-end"><?php echo e($row['count']); ?></td>
                                        <td class="text-end">
                                            <?php echo e($totalClassCount > 0 ? number_format(($row['count'] / $totalClassCount) * 100, 1) : 0); ?>%
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <!-- Web Version - Chart + Table -->
                            <div class="row">
                                <div class="col-md-7">
                                    <canvas id="linkClassChart" height="200"></canvas>
                                </div>
                                <div class="col-md-5">
                                    <?php $totalClassCount = $linkClassDistribution->sum('count'); ?>
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Class</th>
                                                <th class="text-end">Count</th>
                                                <th class="text-end">%</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $linkClassDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <span class="badge bg-<?php echo e($row['label'] === 'PREMIUM' ? 'success' : ($row['label'] === 'METRO' ? 'info' : 'secondary')); ?>">
                                                        <?php echo e($row['label']); ?>

                                                    </span>
                                                </td>
                                                <td class="text-end"><?php echo e($row['count']); ?></td>
                                                <td class="text-end">
                                                    <?php echo e($totalClassCount > 0 ? number_format(($row['count'] / $totalClassCount) * 100, 1) : 0); ?>%
                                                </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Top Customers -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><?php echo pdfIcon('fas fa-crown text-warning me-2'); ?>Top 10 Customers</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th class="text-end">USD</th>
                                    <th class="text-end">KES</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $topCustomers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($customer->customer_name); ?></td>
                                    <td class="text-end">$<?php echo e(number_format($customer->total_contract_value_usd, 2)); ?></td>
                                    <td class="text-end">KSh <?php echo e(number_format($customer->total_contract_value_kes, 2)); ?></td>
                                    <td class="text-end">
                                        <?php echo e($summary['total_contract_value_usd'] > 0
                                            ? number_format(($customer->total_contract_value_usd / $summary['total_contract_value_usd']) * 100, 1)
                                            : 0); ?>%
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">

                <!-- Contract Duration -->
                <div class="card">
                    <div class="card-header">
                        <h5>Contract Duration Distribution</h5>
                    </div>
                    <div class="card-body">
                        <?php if($isPdf): ?>
                            <!-- PDF Version - Table only -->
                            <?php
                                $totalDurationCount = 0;
                                foreach($contractDurationDistribution as $duration) {
                                    $totalDurationCount += $duration['count'];
                                }
                            ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Duration</th>
                                        <th class="text-end">Count</th>
                                        <th class="text-end">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $contractDurationDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $duration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($duration['label']); ?></td>
                                        <td class="text-end"><?php echo e(number_format($duration['count'])); ?></td>
                                        <td class="text-end"><?php echo e($totalDurationCount > 0 ? number_format(($duration['count'] / $totalDurationCount) * 100, 1) : 0); ?>%</td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <?php if($noDurationCount > 0): ?>
                                    <tr>
                                        <td>No duration specified</td>
                                        <td class="text-end"><?php echo e(number_format($noDurationCount)); ?></td>
                                        <td class="text-end"><?php echo e($totalDurationCount > 0 ? number_format(($noDurationCount / $totalDurationCount) * 100, 1) : 0); ?>%</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <!-- Web Version - Chart -->
                            <canvas id="durationChart" height="200"></canvas>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Monthly Trends -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><?php echo pdfIcon('fas fa-chart-line text-info me-2'); ?>Monthly Trends</h5>
                    </div>
                    <div class="card-body">
                        <?php if($isPdf): ?>
                            <!-- PDF Version - Table -->
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th class="text-end">Contracts</th>
                                        <th class="text-end">USD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $monthlyTrends; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($trend->month); ?></td>
                                        <td class="text-end"><?php echo e($trend->contracts); ?></td>
                                        <td class="text-end">$<?php echo e(number_format($trend->monthly_usd, 2)); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <!-- Web Version - Chart -->
                            <canvas id="trendChart" height="160"></canvas>
                        <?php endif; ?>
                        <div class="row text-center mt-3">
                            <div class="col-6">
                                <small class="text-muted">Avg Contracts</small>
                                <div class="fw-bold"><?php echo e(number_format($monthlyTrends->avg('contracts'), 1)); ?></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Avg Revenue (USD)</small>
                                <div class="fw-bold">$<?php echo e(number_format($monthlyTrends->avg('monthly_usd'), 2)); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Detailed Statistics -->
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-white">
                <h5 class="mb-0"><?php echo pdfIcon('fas fa-table me-2'); ?>Detailed Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <table class="table table-sm">
                            <tr><td>Avg Cores</td><td class="text-end"><?php echo e(number_format($detailedStats['avg_cores_leased'],1)); ?></td></tr>
                            <tr><td>Total Cores</td><td class="text-end"><?php echo e(number_format($detailedStats['total_cores_leased'])); ?></td></tr>
                            <tr><td>Avg Distance</td><td class="text-end"><?php echo e(number_format($detailedStats['avg_distance'],1)); ?> km</td></tr>
                            <tr><td>Total Distance</td><td class="text-end"><?php echo e(number_format($detailedStats['total_distance'],1)); ?> km</td></tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <table class="table table-sm">
                            <tr><td>Avg Contract Duration</td><td class="text-end"><?php echo e(number_format($detailedStats['avg_contract_duration'],1)); ?> yrs</td></tr>
                            <tr><td>Longest</td><td class="text-end"><?php echo e($detailedStats['max_contract_duration']); ?> yrs</td></tr>
                            <tr><td>Shortest</td><td class="text-end"><?php echo e($detailedStats['min_contract_duration']); ?> yrs</td></tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <table class="table table-sm">
                            <tr>
                                <td>Contracts w/ Pricing</td>
                                <td class="text-end"><?php echo e($detailedStats['contracts_with_pricing']); ?> / <?php echo e($summary['total_contracts']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<?php if(!$isPdf): ?>
<style>
    /* PDF-specific overrides - only apply when not in PDF mode */
    canvas {
        max-width: 100%;
        height: auto;
    }
</style>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<?php if(!$isPdf): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const linkClassData = <?php echo json_encode($linkClassDistribution, 15, 512) ?>;
    if (document.getElementById('linkClassChart')) {
        new Chart(document.getElementById('linkClassChart'), {
            type: 'pie',
            data: {
                labels: linkClassData.map(i => i.label),
                datasets: [{
                    data: linkClassData.map(i => i.count),
                    backgroundColor: [
                        '#0d6efd', '#198754', '#ffc107', '#0dcaf0', '#6c757d'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    const durationData = <?php echo json_encode($contractDurationDistribution, 15, 512) ?>;
    if (document.getElementById('durationChart')) {
        new Chart(document.getElementById('durationChart'), {
            type: 'pie',
            data: {
                labels: durationData.map(i => i.label),
                datasets: [{
                    data: durationData.map(i => i.count),
                    backgroundColor: [
                        '#0d6efd', '#198754', '#ffc107', '#0dcaf0', '#6c757d', '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    const trendData = <?php echo json_encode($monthlyTrends, 15, 512) ?>;
    if (document.getElementById('trendChart')) {
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: trendData.map(i => i.month),
                datasets: [{
                    label: 'Revenue (USD)',
                    data: trendData.map(i => i.monthly_usd),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
});
</script>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\project\darkfibre-crm\resources\views/conversion-data/summary-report.blade.php ENDPATH**/ ?>