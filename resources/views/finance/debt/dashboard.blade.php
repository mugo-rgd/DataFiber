{{-- resources/views/finance/debt/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Debt Management Dashboard')
@php
    if ($currency == 'all') {
        $usdSummary = $currencySummary->where('currency', 'USD')->first();
        $kshSummary = $currencySummary->where('currency', 'KSH')->first();
    }
@endphp
@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-exclamation-triangle text-warning me-2"></i>Debt Management Dashboard
        </h1>
        <div class="btn-group">
            <button class="btn btn-outline-primary">
                <i class="fas fa-download me-2"></i>Export Report
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendReminderModal">
                <i class="fas fa-envelope me-2"></i>Send Reminders
            </button>
        </div>
    </div>

    <!-- Add this after the page header -->
<div class="row mb-3">
    <div class="col-md-3">
        <select class="form-select" id="currencyFilter" onchange="filterByCurrency(this.value)">
            <option value="all" {{ ($currency ?? 'all') == 'all' ? 'selected' : '' }}>All Currencies</option>
            <option value="USD" {{ ($currency ?? '') == 'USD' ? 'selected' : '' }}>USD Only</option>
            <option value="KSH" {{ ($currency ?? '') == 'KSH' ? 'selected' : '' }}>KSH Only</option>
        </select>
    </div>
</div>

<script>
function filterByCurrency(currency) {
    window.location.href = '{{ route("finance.debt.dashboard") }}?currency=' + currency;
}
</script>


<!-- Summary Cards -->
<!-- Summary Cards -->
<!-- Summary Cards - First Card (Total Overdue) -->
<div class="col-md-3">
    <div class="card border-left-danger shadow h-100">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                        Total Overdue
                    </div>
                    @if($currency == 'all')
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            ${{ number_format($overdueSummary->total_overdue_usd ?? 0, 2) }}
                        </div>
                        <div class="h6 mb-0 font-weight-bold text-success">
                            KSH {{ number_format($overdueSummary->total_overdue_ksh ?? 0, 2) }}
                        </div>
                    @else
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @if($currency == 'USD')
                                ${{ number_format($overdueSummary->total_overdue ?? 0, 2) }}
                            @else
                                KSH {{ number_format($overdueSummary->total_overdue ?? 0, 2) }}
                            @endif
                        </div>
                    @endif
                    <small class="text-muted">Across {{ $overdueSummary->overdue_invoices ?? 0 }} invoices</small>
                </div>
                <div class="col-auto">
                    <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Main Content -->
    <div class="row">
<!-- Aging Analysis -->
<!-- Aging Analysis -->
<div class="col-lg-6 mb-4">
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Aging Analysis</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Age Bucket</th>
                            <th>Invoices</th>
                            @if($currency == 'all')
                                <th>USD Amount</th>
                                <th>USD Paid</th>
                                <th>USD Outstanding</th>
                                <th>KSH Amount</th>
                                <th>KSH Paid</th>
                                <th>KSH Outstanding</th>
                            @else
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Outstanding</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agingAnalysis as $bucket)
                        <tr>
                            <td>
                                @php
                                    // Handle both object and array access
                                    $ageBucket = is_object($bucket) ? $bucket->age_bucket : $bucket['age_bucket'];
                                    $invoiceCount = is_object($bucket) ? $bucket->invoice_count : $bucket['invoice_count'];
                                @endphp
                                <span class="badge
                                    @if(str_contains($ageBucket, '0-30')) bg-warning
                                    @elseif(str_contains($ageBucket, '31-60')) bg-orange
                                    @elseif(str_contains($ageBucket, '61-90')) bg-danger
                                    @else bg-dark @endif">
                                    {{ $ageBucket }}
                                </span>
                            </td>
                            <td>{{ $invoiceCount }}</td>

                            @if($currency == 'all')
                                @php
                                    $usdAmount = is_object($bucket) ? ($bucket->usd_amount ?? 0) : ($bucket['usd_amount'] ?? 0);
                                    $usdPaid = is_object($bucket) ? ($bucket->usd_paid ?? 0) : ($bucket['usd_paid'] ?? 0);
                                    $usdOutstanding = is_object($bucket) ? ($bucket->usd_outstanding ?? 0) : ($bucket['usd_outstanding'] ?? 0);
                                    $kshAmount = is_object($bucket) ? ($bucket->ksh_amount ?? 0) : ($bucket['ksh_amount'] ?? 0);
                                    $kshPaid = is_object($bucket) ? ($bucket->ksh_paid ?? 0) : ($bucket['ksh_paid'] ?? 0);
                                    $kshOutstanding = is_object($bucket) ? ($bucket->ksh_outstanding ?? 0) : ($bucket['ksh_outstanding'] ?? 0);
                                @endphp
                                <td>${{ number_format($usdAmount, 2) }}</td>
                                <td>${{ number_format($usdPaid, 2) }}</td>
                                <td class="text-danger">${{ number_format($usdOutstanding, 2) }}</td>
                                <td>KSH {{ number_format($kshAmount, 2) }}</td>
                                <td>KSH {{ number_format($kshPaid, 2) }}</td>
                                <td class="text-danger">KSH {{ number_format($kshOutstanding, 2) }}</td>
                            @else
                                @php
                                    $totalAmount = is_object($bucket) ? $bucket->total_amount : $bucket['total_amount'];
                                    $paidAmount = is_object($bucket) ? $bucket->paid_amount : $bucket['paid_amount'];
                                    $outstanding = is_object($bucket) ? $bucket->outstanding : $bucket['outstanding'];
                                @endphp
                                <td>
                                    @if($currency == 'USD')
                                        ${{ number_format($totalAmount, 2) }}
                                    @else
                                        KSH {{ number_format($totalAmount, 2) }}
                                    @endif
                                </td>
                                <td>
                                    @if($currency == 'USD')
                                        ${{ number_format($paidAmount, 2) }}
                                    @else
                                        KSH {{ number_format($paidAmount, 2) }}
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-danger">
                                        @if($currency == 'USD')
                                            ${{ number_format($outstanding, 2) }}
                                        @else
                                            KSH {{ number_format($outstanding, 2) }}
                                        @endif
                                    </strong>
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $currency == 'all' ? 8 : 5 }}" class="text-center py-3">
                                <span class="text-muted">No aging data found</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

        <!-- Top Debtors -->
        <!-- Top Debtors -->
<div class="col-lg-6 mb-4">
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Top Debtors</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Customer</th>
                            <th>Invoices</th>
                            <th>Outstanding</th>
                            <th>Max Days</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topDebtors as $debtor)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $debtor->name }}</strong><br>
                                        <small class="text-muted">{{ $debtor->email }}</small>
                                        <span class="badge bg-{{ $debtor->currency == 'USD' ? 'primary' : 'success' }} ms-1">
                                            {{ $debtor->currency }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $debtor->overdue_invoices }}</td>
                            <td>
                                <strong class="text-danger">
                                    @if($debtor->currency == 'USD')
                                        ${{ number_format($debtor->total_outstanding, 2) }}
                                    @else
                                        KSH {{ number_format($debtor->total_outstanding, 2) }}
                                    @endif
                                </strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ $debtor->max_days_overdue > 90 ? 'danger' : 'warning' }}">
                                    {{ $debtor->max_days_overdue }} days
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewCustomerDebt({{ $debtor->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3">
                                <span class="text-muted">No top debtors found</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
    </div>

     <!-- Overdue Invoices List -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Overdue Invoices</h6>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary">Filter</button>
                        <button class="btn btn-sm btn-outline-secondary">Sort</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="overdueInvoicesTable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Days Overdue</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="overdueInvoicesBody">
                                <!-- Will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
       <!-- Payment Trend Chart -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Trend (Last 6 Months)</h6>
                </div>
                <div class="card-body">
                    <canvas id="paymentTrendChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@include('finance.debt.modals.reminder')
@include('finance.debt.modals.payment-plan')
@include('finance.debt.modals.write-off')

@endsection
@push('styles')
<style>
/* Overdue Invoices Table Styling */
#overdueInvoicesTable {
    width: 100%;
    table-layout: fixed;
    font-size: 0.875rem;
    margin-bottom: 0;
}

/* Table Header */
#overdueInvoicesTable thead th {
    background-color: #f8f9fc;
    border-top: none;
    border-bottom: 2px solid #e3e6f0;
    font-weight: 600;
    color: #5a5c69;
    padding: 0.75rem 1rem;
    vertical-align: middle;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.75rem;
}

/* Column Widths */
#overdueInvoicesTable th:nth-child(1),
#overdueInvoicesTable td:nth-child(1) { width: 50px; text-align: center; } /* Checkbox */

#overdueInvoicesTable th:nth-child(2),
#overdueInvoicesTable td:nth-child(2) { width: 180px; } /* Invoice # */

#overdueInvoicesTable th:nth-child(3),
#overdueInvoicesTable td:nth-child(3) { width: 220px; white-space: normal; } /* Customer */

#overdueInvoicesTable th:nth-child(4),
#overdueInvoicesTable td:nth-child(4) { width: 130px; text-align: right; } /* Amount */

#overdueInvoicesTable th:nth-child(5),
#overdueInvoicesTable td:nth-child(5) { width: 130px; } /* Due Date */

#overdueInvoicesTable th:nth-child(6),
#overdueInvoicesTable td:nth-child(6) { width: 130px; } /* Days Overdue */

#overdueInvoicesTable th:nth-child(7),
#overdueInvoicesTable td:nth-child(7) { width: 100px; } /* Status */

#overdueInvoicesTable th:nth-child(8),
#overdueInvoicesTable td:nth-child(8) { width: 150px; text-align: center; } /* Actions */

/* Table Body */
#overdueInvoicesTable tbody td {
    padding: 0.75rem 1rem;
    vertical-align: middle;
    border-top: 1px solid #e3e6f0;
    background-color: #fff;
}

/* Amount Column */
#overdueInvoicesTable td:nth-child(4) {
    font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, monospace;
    font-weight: 600;
    color: #e74a3b;
}

/* Hover Effect */
#overdueInvoicesTable tbody tr:hover td {
    background-color: #f8f9fa;
}

/* Badge Styling */
#overdueInvoicesTable .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    font-weight: 500;
}

/* Action Buttons */
#overdueInvoicesTable .btn-group-sm {
    display: flex;
    gap: 2px;
}

#overdueInvoicesTable .btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    border-radius: 4px;
}

/* Customer Avatar */
.avatar-sm {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: #4e73df;
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .table-responsive {
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
    }

    #overdueInvoicesTable {
        min-width: 1100px;
    }
}

/* Loading State */
#overdueInvoicesBody tr.loading td {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
}

#overdueInvoicesBody tr.loading .spinner-border {
    width: 1.5rem;
    height: 1.5rem;
}

/* Empty State */
#overdueInvoicesBody tr.empty-state td {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
    font-style: italic;
}
</style>
@endpush
@push('scripts')
<script>
// Wait for DOM and libraries to be ready
$(document).ready(function() {
    console.log('Dashboard script loaded');
    console.log('jQuery version:', $.fn.jquery);
    console.log('Chart.js available:', typeof Chart !== 'undefined');

    // Payment Trend Chart
    function initializeChart() {
        const ctx = document.getElementById('paymentTrendChart');
        if (!ctx) {
            console.error('Chart canvas not found!');
            return;
        }

        if (typeof Chart === 'undefined') {
            console.error('Chart.js not loaded!');
            return;
        }

        try {
            const paymentTrendChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($paymentTrend->pluck('month')),
                    datasets: [{
                        label: 'Total Billed',
                        data: @json($paymentTrend->pluck('total_billed')),
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.1
                    }, {
                        label: 'Total Paid',
                        data: @json($paymentTrend->pluck('total_paid')),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }, {
                        label: 'Overdue Invoices',
                        data: @json($paymentTrend->pluck('overdue_invoices')),
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
            console.log('Chart initialized successfully');
        } catch (error) {
            console.error('Error initializing chart:', error);
        }
    }

    // Load overdue invoices via AJAX
    function loadOverdueInvoices() {
        if (typeof $ === 'undefined') {
            console.error('jQuery not available for AJAX');
            return;
        }

        $.ajax({
            url: '{{ route("finance.debt.overdue-invoices") }}',
            method: 'GET',
            beforeSend: function() {
                $('#overdueInvoicesBody').html('<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm" role="status"></div> Loading...</td></tr>');
            },
            success: function(data) {
                if (data && data.trim().length > 0) {
                    $('#overdueInvoicesBody').html(data);
                    console.log('Overdue invoices loaded successfully');
                } else {
                    $('#overdueInvoicesBody').html('<tr><td colspan="8" class="text-center py-4 text-muted">No overdue invoices found</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading overdue invoices:', error);
                $('#overdueInvoicesBody').html('<tr><td colspan="8" class="text-center py-4 text-danger">Error loading data. Please refresh the page.</td></tr>');
            }
        });
    }
 function loadOverdueInvoicesdash() {
    if (typeof $ === 'undefined') {
        console.error('jQuery not available for AJAX');
        return;
    }

    $.ajax({
        url: '{{ route("finance.debt.overdue-invoices") }}',
        method: 'GET',
        beforeSend: function() {
            $('#overdueInvoicesBody').html(
                '<tr><td colspan="8" class="text-center py-4">' +
                '<div class="spinner-border spinner-border-sm" role="status"></div> Loading...' +
                '</td></tr>'
            );
        },
        success: function(data) {
            if (data && data.trim().length > 0) {
                $('#overdueInvoicesBody').html(data);

                // Initialize tooltips for new content
                $('[data-bs-toggle="tooltip"]').tooltip();

                console.log('Overdue invoices loaded successfully');
            } else {
                $('#overdueInvoicesBody').html(
                    '<tr><td colspan="8" class="text-center py-4 text-muted">No overdue invoices found</td></tr>'
                );
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading overdue invoices:', error);
            $('#overdueInvoicesBody').html(
                '<tr><td colspan="8" class="text-center py-4 text-danger">Error loading data</td></tr>'
            );
        }
    });
}

// Call on page load
$(document).ready(function() {
    loadOverdueInvoicesdash();
});
    // Show toast notification
    function showToast(message, type = 'success') {
        const toast = $(`
            <div class="toast align-items-center text-bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);

        $('#toastContainer').append(toast);
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();

        // Remove after hiding
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }

    // Handle send reminder button (for AJAX-loaded content)
    $(document).on('click', '.send-reminder', function() {
        const invoiceId = $(this).data('invoice-id');
        const $button = $(this);

        if (confirm('Send reminder for this invoice?')) {
            $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            // CORRECTED: Use proper route parameter
            const url = '{{ route("finance.debt.invoice.send-reminder", ":id") }}'.replace(':id', invoiceId);

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $button.html('<i class="fas fa-check"></i> Sent');
                    setTimeout(() => {
                        $button.prop('disabled', false).html('<i class="fas fa-envelope"></i>');
                    }, 2000);

                    showToast('Reminder sent successfully!', 'success');
                },
                error: function(xhr) {
                    $button.prop('disabled', false).html('<i class="fas fa-envelope"></i>');

                    let errorMsg = 'Error sending reminder';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    showToast(errorMsg, 'error');
                }
            });
        }
    });
// Enable tooltips
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Re-initialize tooltips after AJAX load
    $(document).ajaxComplete(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
});
    // Handle payment plan button (for AJAX-loaded content)
    $(document).on('click', '.create-payment-plan', function() {
        const invoiceId = $(this).data('invoice-id');
        const $button = $(this);

        if (confirm('Create payment plan for this invoice?')) {
            $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            // CORRECTED: Use proper route parameter
            const url = '{{ route("finance.debt.invoice.create-payment-plan", ":id") }}'.replace(':id', invoiceId);

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    installment_count: 3,
                    start_date: '{{ now()->format("Y-m-d") }}'
                },
                success: function(response) {
                    $button.html('<i class="fas fa-check"></i> Created');
                    setTimeout(() => {
                        $button.prop('disabled', false).html('<i class="fas fa-calendar-alt"></i>');
                    }, 2000);

                    showToast('Payment plan created successfully!', 'success');
                    // Reload the invoices list
                    setTimeout(loadOverdueInvoicesdash, 1000);
                },
                error: function(xhr) {
                    $button.prop('disabled', false).html('<i class="fas fa-calendar-alt"></i>');

                    let errorMsg = 'Error creating payment plan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    showToast(errorMsg, 'error');
                }
            });
        }
    });

    // Handle view invoice button click
    $(document).on('click', '.view-invoice', function() {
        const invoiceId = $(this).data('invoice-id');
        const $button = $(this);

        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        // Check if route exists
        @if(Route::has('finance.debt.invoice.details'))
            const url = '{{ route("finance.debt.invoice.details", ":id") }}'.replace(':id', invoiceId);
            window.location.href = url;
        @else
            // Show modal with basic info
            $('#invoiceDetailsModal').modal('show');
            $('#invoiceDetailsContent').html(`
                <div class="text-center p-4">
                    <h5>Invoice #${invoiceId}</h5>
                    <p class="text-muted">Detailed view not available</p>
                    <button class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            `);
            $button.prop('disabled', false).html('<i class="fas fa-eye"></i>');
        @endif
    });

    // Initialize everything
    initializeChart();
    loadOverdueInvoices();

    // Select all checkbox functionality
    $('#selectAll').change(function() {
        const isChecked = $(this).prop('checked');
        $('.invoice-checkbox').prop('checked', isChecked);
        console.log('Select all:', isChecked);
    });

    // Handle invoice row selection
    $(document).on('change', '.invoice-checkbox', function() {
        const totalCheckboxes = $('.invoice-checkbox').length;
        const checkedCheckboxes = $('.invoice-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
    });

    // Auto-refresh every 60 seconds
    setInterval(loadOverdueInvoices, 60000);
});
</script>

<!-- Toast container -->
<div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

{{-- In your dashboard.blade.php --}}
<style>
    .toast {
    min-width: 250px;
    z-index: 9999;
}

.badge.bg-orange {
    background-color: #fd7e14;
    color: white;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: #4e73df;
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
}
</style>
@endpush
