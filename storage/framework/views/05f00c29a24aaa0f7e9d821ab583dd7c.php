<?php
    function formatCurrency($amount, $currency = 'USD') {
        if ($currency == 'USD') {
            return '$' . number_format($amount, 2);
        } elseif ($currency == 'KSH' || $currency == 'KES') {
            return 'KSh ' . number_format($amount, 2);
        } else {
            return $currency . ' ' . number_format($amount, 2);
        }
    }
?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Payment Statements Management</h1>
        </div>
    </div>

    <div class="row">
        <!-- Monthly View Card -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">View Statements by Month</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('statements.monthly')); ?>" method="POST" id="monthlyForm">
                        <?php echo csrf_field(); ?>
                        <div class="form-group mb-3">
                            <label for="month" class="form-label">Select Month</label>
                            <select name="month" id="month" class="form-control" required>
                                <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>" <?php echo e($value == date('Y-m') ? 'selected' : ''); ?>>
                                        <?php echo e($label); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calendar-alt"></i> View Statements
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Export Statements Card -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Generate & Export Statements</h5>
                </div>
                <div class="card-body">
                    <form id="exportForm" action="<?php echo e(route('statements.export')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                       value="<?php echo e(date('Y-m-01')); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                       value="<?php echo e(date('Y-m-d')); ?>" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="customer_ids" class="form-label">Select Customers</label>
                            <select name="customer_ids[]" id="customer_ids" class="form-control select2" multiple>
                                <option value="">All Customers</option>
                                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->name); ?> (<?php echo e($customer->company_name); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-muted">Leave empty to generate for all customers with transactions</small>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Statements will show amounts in their original currency (USD or KSH)
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success" id="generateBtn">
                            <i class="fas fa-file-export"></i> Generate Statements
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress and Results Section -->
    <div id="progressSection" class="row" style="display: none;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Generation Progress</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-3">
                        <div id="generationProgress" class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar" style="width: 0%">0%</div>
                    </div>
                    <div id="progressMessage" class="text-muted"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="statementsResult" class="row mt-4"></div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container {
        width: 100% !important;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .btn-group .btn {
        margin-right: 5px;
    }
    .currency-badge {
        font-size: 0.8rem;
        padding: 0.3rem 0.5rem;
        border-radius: 4px;
    }
    .currency-ksh {
        background-color: #ffc107;
        color: #212529;
    }
    .currency-usd {
        background-color: #007bff;
        color: white;
    }
    .summary-stats {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#customer_ids').select2({
        placeholder: 'Select customers',
        allowClear: true
    });

    // Handle Monthly View Form Submission
    $('#monthlyForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();

        // Show loading state
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Loading...').prop('disabled', true);

        // Show progress section for monthly view
        $('#progressSection').show();
        $('#statementsResult').empty();
        updateProgress(30, 'Fetching monthly statements...');

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            updateProgress(60, 'Processing response...');
            return response.json();
        })
        .then(data => {
            updateProgress(90, 'Rendering statements...');

            if (data.success) {
                displayMonthlyStatements(data);
                updateProgress(100, 'Completed!');
            } else {
                throw new Error(data.message || 'Error loading statements');
            }
        })
        .catch(error => {
            console.error('Monthly view error:', error);
            $('#statementsResult').html(`
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Error!</strong> ${error.message}
                    </div>
                </div>
            `);
        })
        .finally(() => {
            // Reset button
            submitBtn.html(originalText).prop('disabled', false);

            // Hide progress after 2 seconds
            setTimeout(() => {
                $('#progressSection').fadeOut();
            }, 2000);
        });
    });

    // Handle Export Form Submission
    $('#exportForm').on('submit', function(e) {
        e.preventDefault();

        // Validate dates
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();

        if (!startDate || !endDate) {
            alert('Please select both start and end dates');
            return;
        }

        if (new Date(startDate) > new Date(endDate)) {
            alert('End date must be after start date');
            return;
        }

        // Show progress section
        $('#progressSection').show();
        $('#statementsResult').empty();

        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();

        // Show loading state
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Generating...').prop('disabled', true);

        // Update progress
        updateProgress(10, 'Preparing statement generation...');

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            updateProgress(50, 'Processing response...');
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            updateProgress(90, 'Finalizing...');

            if (data.success) {
                displayExportResults(data);
                updateProgress(100, 'Completed!');

                // Show success message
                showNotification('success', data.message);
            } else {
                throw new Error(data.message || 'Error generating statements');
            }
        })
        .catch(error => {
            console.error('Export error:', error);
            $('#progressSection').hide();
            $('#statementsResult').html(`
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Error!</strong> ${error.message || 'An unexpected error occurred'}
                    </div>
                </div>
            `);

            // Show error notification
            showNotification('error', error.message || 'Failed to generate statements');
        })
        .finally(() => {
            // Reset button
            submitBtn.html(originalText).prop('disabled', false);

            // Hide progress after 3 seconds
            setTimeout(() => {
                $('#progressSection').fadeOut();
            }, 3000);
        });
    });

    // Set default dates if not set
    if (!$('#start_date').val()) {
        const firstDay = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
        $('#start_date').val(firstDay.toISOString().split('T')[0]);
    }

    if (!$('#end_date').val()) {
        const today = new Date().toISOString().split('T')[0];
        $('#end_date').val(today);
    }
});

// Update progress bar
function updateProgress(percentage, message) {
    $('#generationProgress')
        .css('width', percentage + '%')
        .attr('aria-valuenow', percentage)
        .text(percentage + '%');
    $('#progressMessage').text(message);
}

/**
 * Format currency based on type
 * Uses the currency value from the backend
 */
function formatCurrency(amount, currency = 'USD') {
    const formattedNumber = formatNumber(amount);

    if (currency === 'USD') {
        return '$' + formattedNumber;
    } else if (currency === 'KSH' || currency === 'KES') {
        return 'KSh ' + formattedNumber;
    } else {
        return currency + ' ' + formattedNumber;
    }
}

/**
 * Get currency badge class based on currency
 */
function getCurrencyBadgeClass(currency) {
    return currency === 'USD' ? 'currency-usd' : 'currency-ksh';
}

/**
 * Get currency display name
 */
function getCurrencyDisplay(currency) {
    return currency === 'USD' ? 'USD' : 'KSH';
}

/**
 * Display monthly statements with currency support
 * Uses the currency value directly from the backend
 */
function displayMonthlyStatements(data) {
    let html = '<div class="col-md-12">';

    // Debug: Log the raw data to see what we're getting
    console.log('Statements received:', data.statements);

    // Calculate summary stats by currency
    let totalKsh = 0;
    let totalUsd = 0;
    let countKsh = 0;
    let countUsd = 0;

    if (data.statements && data.statements.length > 0) {
        data.statements.forEach(statement => {
            // Use the currency directly from the backend - NO DETERMINATION
            const currency = statement.currency || 'USD'; // Default to USD if not provided

            if (currency === 'KSH') {
                totalKsh += statement.closing_balance;
                countKsh++;
            } else {
                totalUsd += statement.closing_balance;
                countUsd++;
            }
        });
    }

    // Summary Stats with proper styling
    html += `
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-warning bg-opacity-10 border-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-warning mb-1">
                                    <i class="fas fa-coins me-2"></i>KSH Summary
                                </h6>
                                <h3 class="mb-0">KSh ${formatNumber(totalKsh)}</h3>
                                <small class="text-muted">${countKsh} statements</small>
                            </div>
                            <i class="fas fa-coins fa-3x opacity-25 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-primary bg-opacity-10 border-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-primary mb-1">
                                    <i class="fas fa-dollar-sign me-2"></i>USD Summary
                                </h6>
                                <h3 class="mb-0">$${formatNumber(totalUsd)}</h3>
                                <small class="text-muted">${countUsd} statements</small>
                            </div>
                            <i class="fas fa-dollar-sign fa-3x opacity-25 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    html += `
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Statements for ${data.month}
                </h5>
                <span class="badge bg-light text-dark">Total: ${data.statements.length}</span>
            </div>
            <div class="card-body">
    `;

    if (data.statements && data.statements.length > 0) {
        html += `
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Statement #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Period</th>
                            <th>Currency</th>
                            <th class="text-end">Opening</th>
                            <th class="text-end">Closing</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        data.statements.forEach(statement => {
            const statusClass = {
                'draft': 'secondary',
                'generated': 'info',
                'sent': 'success',
                'viewed': 'primary',
                'paid': 'success',
                'overdue': 'danger'
            }[statement.status] || 'secondary';

            // Use the currency directly from the backend - NO DETERMINATION
            const currency = statement.currency || 'USD';
            const currencyClass = getCurrencyBadgeClass(currency);

            // Format amounts with correct currency
            const openingFormatted = formatCurrency(statement.opening_balance, currency);
            const closingFormatted = formatCurrency(statement.closing_balance, currency);
            const closingClass = statement.closing_balance >= 0 ? 'text-success' : 'text-danger';

            html += `
                <tr>
                    <td><strong>${statement.number}</strong></td>
                    <td>${escapeHtml(statement.customer)}</td>
                    <td>${statement.date}</td>
                    <td>${statement.period}</td>
                    <td>
                        <span class="badge ${currencyClass}">
                            <i class="fas ${currency === 'USD' ? 'fa-dollar-sign' : 'fa-coins'} me-1"></i>
                            ${getCurrencyDisplay(currency)}
                        </span>
                    </td>
                    <td class="text-end">${openingFormatted}</td>
                    <td class="text-end ${closingClass}">${closingFormatted}</td>
                    <td>
                        <span class="badge bg-${statusClass}">${statement.status}</span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="/statements/${statement.id}/download"
                               class="btn btn-info"
                               title="Download PDF"
                               target="_blank">
                                <i class="fas fa-download"></i>
                            </a>
                            <button type="button"
                                    class="btn btn-success"
                                    title="Send to Customer"
                                    onclick="sendStatement(${statement.id})"
                                    ${statement.status === 'sent' ? 'disabled' : ''}>
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;

        // Add pagination if available
        if (data.pagination && data.pagination.last_page > 1) {
            html += '<div class="d-flex justify-content-center mt-4">';
            html += '<nav><ul class="pagination">';

            // Previous button
            html += `<li class="page-item ${data.pagination.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${data.pagination.current_page - 1}">Previous</a>
            </li>`;

            // Page numbers
            for (let i = 1; i <= data.pagination.last_page; i++) {
                if (i === 1 || i === data.pagination.last_page ||
                    (i >= data.pagination.current_page - 2 && i <= data.pagination.current_page + 2)) {
                    html += `<li class="page-item ${i === data.pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>`;
                } else if (i === data.pagination.current_page - 3 || i === data.pagination.current_page + 3) {
                    html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }

            // Next button
            html += `<li class="page-item ${data.pagination.current_page === data.pagination.last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${data.pagination.current_page + 1}">Next</a>
            </li>`;

            html += '</ul></nav></div>';
        }
    } else {
        html += `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No statements found for ${data.month}.
            </div>
        `;
    }

    html += `
            </div>
        </div>
    </div>`;

    $('#statementsResult').html(html);

    // Add click handlers for pagination
    $('.pagination .page-link').on('click', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            loadMonthlyPage(page);
        }
    });
}

/**
 * Display export results with currency support
 */
function displayExportResults(data) {
    let html = '<div class="col-md-12">';

    // Calculate summary by currency
    let totalKsh = 0;
    let totalUsd = 0;
    let countKsh = 0;
    let countUsd = 0;

    if (data.statements && data.statements.length > 0) {
        data.statements.forEach(statement => {
            // Use the currency directly from the backend
            const currency = statement.currency || 'USD';

            if (currency === 'KSH') {
                totalKsh += statement.closing_balance;
                countKsh++;
            } else {
                totalUsd += statement.closing_balance;
                countUsd++;
            }
        });
    }

    html += `
        <div class="card">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-file-export me-2"></i>
                    Generated Statements
                </h5>
                <span class="badge bg-light text-dark">${data.statements.length} generated</span>
            </div>
            <div class="card-body">
    `;

    if (data.statements && data.statements.length > 0) {
        html += `
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                ${data.message}
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-warning bg-opacity-10 border-warning">
                        <div class="card-body">
                            <h6 class="text-warning">
                                <i class="fas fa-coins me-2"></i>KSH Summary
                            </h6>
                            <h4 class="mb-0">KSh ${formatNumber(totalKsh)}</h4>
                            <small class="text-muted">${countKsh} statements</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-primary bg-opacity-10 border-primary">
                        <div class="card-body">
                            <h6 class="text-primary">
                                <i class="fas fa-dollar-sign me-2"></i>USD Summary
                            </h6>
                            <h4 class="mb-0">$${formatNumber(totalUsd)}</h4>
                            <small class="text-muted">${countUsd} statements</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Statement #</th>
                            <th>Customer</th>
                            <th>Period</th>
                            <th>Currency</th>
                            <th class="text-end">Opening</th>
                            <th class="text-end">Closing</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        data.statements.forEach(statement => {
            // Use the currency directly from the backend
            const currency = statement.currency || 'USD';
            const currencyClass = getCurrencyBadgeClass(currency);

            const openingFormatted = formatCurrency(statement.opening_balance, currency);
            const closingFormatted = formatCurrency(statement.closing_balance, currency);

            html += `
                <tr>
                    <td><strong>${statement.statement_number}</strong></td>
                    <td>${escapeHtml(statement.customer_name || 'Customer #' + statement.customer_id)}</td>
                    <td>${statement.period_start} to ${statement.period_end}</td>
                    <td>
                        <span class="badge ${currencyClass}">
                            <i class="fas ${currency === 'USD' ? 'fa-dollar-sign' : 'fa-coins'} me-1"></i>
                            ${getCurrencyDisplay(currency)}
                        </span>
                    </td>
                    <td class="text-end">${openingFormatted}</td>
                    <td class="text-end fw-bold">${closingFormatted}</td>
                    <td class="text-center">
                        <a href="/statements/${statement.id}/download"
                           class="btn btn-sm btn-info">
                            <i class="fas fa-download me-1"></i> Download
                        </a>
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>

            <!-- Bulk Actions -->
            <div class="mt-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="mb-3">Bulk Actions</h6>
                        <button type="button" class="btn btn-success me-2" onclick="sendAllStatements()">
                            <i class="fas fa-envelope me-2"></i>Send All to Customers
                        </button>
                        <button type="button" class="btn btn-primary" onclick="downloadAllStatements()">
                            <i class="fas fa-download me-2"></i>Download All (ZIP)
                        </button>
                    </div>
                </div>
            </div>
        `;
    } else {
        html += `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${data.message || 'No statements were generated'}
            </div>
        `;
    }

    html += `
            </div>
        </div>
    </div>`;

    $('#statementsResult').html(html);
}

// Load monthly page
function loadMonthlyPage(page) {
    const month = $('#month').val();

    $('#progressSection').show();
    updateProgress(30, `Loading page ${page}...`);

    fetch('/statements/monthly', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            month: month,
            page: page
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayMonthlyStatements(data);
            updateProgress(100, 'Page loaded!');
        }
    })
    .catch(error => {
        console.error('Error loading page:', error);
    })
    .finally(() => {
        setTimeout(() => {
            $('#progressSection').fadeOut();
        }, 1000);
    });
}

// Send statement to customer
function sendStatement(statementId) {
    if (confirm('Are you sure you want to send this statement to the customer?')) {
        const btn = event.currentTarget;
        const originalHtml = $(btn).html();

        $(btn).html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        fetch(`/statements/${statementId}/send`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', 'Statement sent successfully!');
                // Update status badge
                $(btn).closest('tr').find('.badge')
                    .removeClass('bg-info bg-primary bg-secondary')
                    .addClass('bg-success')
                    .text('sent');
                $(btn).prop('disabled', true);
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            showNotification('error', 'Error sending statement: ' + error.message);
        })
        .finally(() => {
            $(btn).html(originalHtml).prop('disabled', false);
        });
    }
}

// Send all statements
function sendAllStatements() {
    if (confirm('Are you sure you want to send all statements to their customers?')) {
        showNotification('info', 'Sending statements... This may take a while.');
        // Implementation for bulk sending
    }
}

// Download all statements
function downloadAllStatements() {
    showNotification('info', 'Preparing ZIP file for download...');
    // Implementation for bulk download
}

// Show notification
function showNotification(type, message) {
    if (type === 'error') {
        alert('Error: ' + message);
    } else if (type === 'success') {
        console.log('Success: ' + message);
        // You could add a toast notification here
    } else {
        console.log(type + ': ' + message);
    }
}

// Helper function to format numbers
function formatNumber(number) {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(number || 0);
}

// Helper function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\project\darkfibre-crm\resources\views/statements/index.blade.php ENDPATH**/ ?>