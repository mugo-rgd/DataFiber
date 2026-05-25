{{-- resources/views/finance/debt/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Debt Management Dashboard - Dark Fibre CRM')

@php
    if ($currency == 'all') {
        $usdSummary = $currencySummary->where('currency', 'USD')->first();
        $kshSummary = $currencySummary->where('currency', 'KSH')->first();
    }
@endphp

@section('content')
<div class="container-fluid px-0">

    {{-- Hero Section --}}
    <div class="dashboard-hero text-white py-4 py-md-5">
        <div class="container-fluid px-3 px-sm-4 px-md-5">
            <div class="row align-items-center g-4">

                {{-- Left Column - Title --}}
                <div class="col-12 col-lg-7">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="hero-icon">
                            <i class="fas fa-exclamation-triangle fa-3x fa-fw"></i>
                        </div>
                        <div>
                            <h1 class="display-5 fw-bold mb-2">Debt Management Dashboard</h1>
                            <p class="lead mb-0 opacity-90">
                                Track and manage overdue invoices and collections
                            </p>
                        </div>
                    </div>

                    {{-- Meta Info --}}
                    <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
                        <span class="badge bg-white text-kp-blue px-3 py-2 rounded-pill">
                            <i class="far fa-calendar-alt me-1"></i>
                            {{ now()->format('l, F j, Y') }}
                        </span>
                        <span class="badge bg-white text-kp-blue px-3 py-2 rounded-pill">
                            <i class="fas fa-chart-line me-1"></i>
                            Real-time Data
                        </span>
                    </div>
                </div>

                {{-- Right Column - Actions --}}
                <div class="col-12 col-lg-5">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        @include('partials.role-help-widget')

                        <div class="btn-group" role="group">
                            <button class="btn btn-light btn-dashboard-action" id="exportReportBtn">
                                <i class="fas fa-download me-2"></i>Export Report
                            </button>
                            <button class="btn btn-kp-primary btn-dashboard-action" data-bs-toggle="modal" data-bs-target="#sendReminderModal">
                                <i class="fas fa-envelope me-2"></i>Send Reminders
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container-fluid px-3 px-sm-4 px-md-5 py-4">

        {{-- Currency Filter --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="filter-card p-3 rounded-4">
                    <label class="form-label fw-semibold mb-2">
                        <i class="fas fa-filter me-1 text-kp-blue"></i>Filter by Currency
                    </label>
                    <select class="form-select rounded-pill" id="currencyFilter" onchange="filterByCurrency(this.value)">
                        <option value="all" {{ ($currency ?? 'all') == 'all' ? 'selected' : '' }}>All Currencies</option>
                        <option value="USD" {{ ($currency ?? '') == 'USD' ? 'selected' : '' }}>USD Only</option>
                        <option value="KSH" {{ ($currency ?? '') == 'KSH' ? 'selected' : '' }}>KSH Only</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Summary Cards Row --}}
        <div class="row g-4 mb-5">

            {{-- Total Overdue Card --}}
            <div class="col-md-3">
                <div class="metric-card metric-card-danger rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-white-70 mb-2">TOTAL OVERDUE</h6>
                            @if($currency == 'all')
                                <div class="metric-value-large fw-bold text-white mb-1">
                                    ${{ number_format($overdueSummary->total_overdue_usd ?? 0, 2) }}
                                </div>
                                <div class="metric-value-medium fw-bold text-kp-yellow">
                                    KSH {{ number_format($overdueSummary->total_overdue_ksh ?? 0, 2) }}
                                </div>
                            @else
                                <div class="metric-value-large fw-bold text-white">
                                    @if($currency == 'USD')
                                        ${{ number_format($overdueSummary->total_overdue ?? 0, 2) }}
                                    @else
                                        KSH {{ number_format($overdueSummary->total_overdue ?? 0, 2) }}
                                    @endif
                                </div>
                            @endif
                            <small class="text-white-50">
                                Across {{ $overdueSummary->overdue_invoices ?? 0 }} invoices
                            </small>
                        </div>
                        <div class="metric-icon-large bg-white-20 rounded-3">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Collection Rate Card --}}
            <div class="col-md-3">
                <div class="metric-card metric-card-success rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-white-70 mb-2">COLLECTION RATE</h6>
                            <div class="metric-value-large fw-bold text-white mb-1">
                                {{ number_format($collectionRate ?? 0, 1) }}%
                            </div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-white" style="width: {{ $collectionRate ?? 0 }}%"></div>
                            </div>
                        </div>
                        <div class="metric-icon-large bg-white-20 rounded-3">
                            <i class="fas fa-percentage fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Avg Days Overdue Card --}}
            <div class="col-md-3">
                <div class="metric-card metric-card-warning rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-kp-dark-70 mb-2">AVG DAYS OVERDUE</h6>
                            <div class="metric-value-large fw-bold text-kp-dark mb-1">
                                {{ number_format($avgDaysOverdue ?? 0) }} days
                            </div>
                            <small class="text-kp-dark-50">
                                Average overdue period
                            </small>
                        </div>
                        <div class="metric-icon-large bg-dark-20 rounded-3">
                            <i class="fas fa-calendar-times fa-2x text-kp-dark"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Outstanding Card --}}
            <div class="col-md-3">
                <div class="metric-card metric-card-info rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-white-70 mb-2">TOTAL OUTSTANDING</h6>
                            @if($currency == 'all')
                                <div class="metric-value-large fw-bold text-white mb-1">
                                    ${{ number_format($totalOutstandingUsd ?? 0, 2) }}
                                </div>
                                <div class="metric-value-medium fw-bold text-kp-yellow">
                                    KSH {{ number_format($totalOutstandingKsh ?? 0, 2) }}
                                </div>
                            @else
                                <div class="metric-value-large fw-bold text-white">
                                    @if($currency == 'USD')
                                        ${{ number_format($totalOutstanding ?? 0, 2) }}
                                    @else
                                        KSH {{ number_format($totalOutstanding ?? 0, 2) }}
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="metric-icon-large bg-white-20 rounded-3">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Aging Analysis & Top Debtors Row --}}
        <div class="row g-4 mb-5">

            {{-- Aging Analysis --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-chart-line text-kp-blue me-2"></i>Aging Analysis
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3">Age Bucket</th>
                                        <th class="py-3">Invoices</th>
                                        @if($currency == 'all')
                                            <th class="py-3">USD Amount</th>
                                            <th class="py-3">USD Paid</th>
                                            <th class="py-3">USD Outstanding</th>
                                            <th class="py-3">KSH Amount</th>
                                            <th class="py-3">KSH Paid</th>
                                            <th class="py-3">KSH Outstanding</th>
                                        @else
                                            <th class="py-3">Amount</th>
                                            <th class="py-3">Paid</th>
                                            <th class="py-3">Outstanding</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($agingAnalysis as $bucket)
                                        <tr>
                                            <td class="px-4 py-3">
                                                @php
                                                    $ageBucket = is_object($bucket) ? $bucket->age_bucket : $bucket['age_bucket'];
                                                    $invoiceCount = is_object($bucket) ? $bucket->invoice_count : $bucket['invoice_count'];
                                                    $badgeColor = match(true) {
                                                        str_contains($ageBucket, '0-30') => 'bg-kp-yellow text-dark',
                                                        str_contains($ageBucket, '31-60') => 'bg-orange',
                                                        str_contains($ageBucket, '61-90') => 'bg-danger',
                                                        default => 'bg-dark'
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeColor }} rounded-pill px-3 py-2">
                                                    {{ $ageBucket }}
                                                </span>
                                            </td>
                                            <td class="py-3 fw-bold">{{ $invoiceCount }}</td>

                                            @if($currency == 'all')
                                                @php
                                                    $usdAmount = is_object($bucket) ? ($bucket->usd_amount ?? 0) : ($bucket['usd_amount'] ?? 0);
                                                    $usdPaid = is_object($bucket) ? ($bucket->usd_paid ?? 0) : ($bucket['usd_paid'] ?? 0);
                                                    $usdOutstanding = is_object($bucket) ? ($bucket->usd_outstanding ?? 0) : ($bucket['usd_outstanding'] ?? 0);
                                                    $kshAmount = is_object($bucket) ? ($bucket->ksh_amount ?? 0) : ($bucket['ksh_amount'] ?? 0);
                                                    $kshPaid = is_object($bucket) ? ($bucket->ksh_paid ?? 0) : ($bucket['ksh_paid'] ?? 0);
                                                    $kshOutstanding = is_object($bucket) ? ($bucket->ksh_outstanding ?? 0) : ($bucket['ksh_outstanding'] ?? 0);
                                                @endphp
                                                <td class="py-3">${{ number_format($usdAmount, 2) }}</td>
                                                <td class="py-3">${{ number_format($usdPaid, 2) }}</td>
                                                <td class="py-3 text-danger fw-bold">${{ number_format($usdOutstanding, 2) }}</td>
                                                <td class="py-3">KSH {{ number_format($kshAmount, 2) }}</td>
                                                <td class="py-3">KSH {{ number_format($kshPaid, 2) }}</td>
                                                <td class="py-3 text-danger fw-bold">KSH {{ number_format($kshOutstanding, 2) }}</td>
                                            @else
                                                @php
                                                    $totalAmount = is_object($bucket) ? $bucket->total_amount : $bucket['total_amount'];
                                                    $paidAmount = is_object($bucket) ? $bucket->paid_amount : $bucket['paid_amount'];
                                                    $outstanding = is_object($bucket) ? $bucket->outstanding : $bucket['outstanding'];
                                                @endphp
                                                <td class="py-3">
                                                    @if($currency == 'USD') ${{ number_format($totalAmount, 2) }} @else KSH {{ number_format($totalAmount, 2) }} @endif
                                                </td>
                                                <td class="py-3">
                                                    @if($currency == 'USD') ${{ number_format($paidAmount, 2) }} @else KSH {{ number_format($paidAmount, 2) }} @endif
                                                </td>
                                                <td class="py-3 text-danger fw-bold">
                                                    @if($currency == 'USD') ${{ number_format($outstanding, 2) }} @else KSH {{ number_format($outstanding, 2) }} @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $currency == 'all' ? 8 : 5 }}" class="text-center py-5">
                                                <i class="fas fa-chart-line fa-3x text-muted opacity-25 mb-2"></i>
                                                <p class="text-muted">No aging data found</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Debtors --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-trophy text-kp-yellow me-2"></i>Top Debtors
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3">Customer</th>
                                        <th class="py-3">Invoices</th>
                                        <th class="py-3">Outstanding</th>
                                        <th class="py-3">Max Days</th>
                                        <th class="px-4 py-3 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topDebtors as $debtor)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="customer-avatar bg-kp-blue-light rounded-circle">
                                                        <i class="fas fa-user fa-sm text-kp-blue"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $debtor->name }}</div>
                                                        <div class="small text-muted">{{ $debtor->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3">{{ $debtor->overdue_invoices }}</td>
                                            <td class="py-3">
                                                <strong class="text-danger">
                                                    @if($debtor->currency == 'USD')
                                                        ${{ number_format($debtor->total_outstanding, 2) }}
                                                    @else
                                                        KSH {{ number_format($debtor->total_outstanding, 2) }}
                                                    @endif
                                                </strong>
                                            </td>
                                            <td class="py-3">
                                                <span class="badge {{ $debtor->max_days_overdue > 90 ? 'bg-danger' : 'bg-warning text-dark' }} rounded-pill px-3 py-1">
                                                    {{ $debtor->max_days_overdue }} days
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button class="btn btn-sm btn-outline-kp-primary rounded-pill px-3"
                                                        onclick="viewCustomerDebt({{ $debtor->id }})"
                                                        data-bs-toggle="tooltip" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <i class="fas fa-users fa-3x text-muted opacity-25 mb-2"></i>
                                                <p class="text-muted">No top debtors found</p>
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

        {{-- Overdue Invoices Table --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-file-invoice text-danger me-2"></i>Overdue Invoices
                        </h5>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="filterBtn">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="sortBtn">
                                <i class="fas fa-sort me-1"></i>Sort
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="overdueInvoicesTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3" style="width: 50px;">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th class="py-3">Invoice #</th>
                                        <th class="py-3">Customer</th>
                                        <th class="py-3">Amount</th>
                                        <th class="py-3">Due Date</th>
                                        <th class="py-3">Days Overdue</th>
                                        <th class="py-3">Status</th>
                                        <th class="px-4 py-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="overdueInvoicesBody">
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="spinner-border spinner-border-sm text-kp-blue" role="status"></div>
                                            <span class="ms-2 text-muted">Loading overdue invoices...</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Trend Chart --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-chart-line text-kp-green me-2"></i>Payment Trend (Last 6 Months)
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="paymentTrendChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modals --}}
@include('finance.debt.modals.reminder')
@include('finance.debt.modals.payment-plan')
@include('finance.debt.modals.write-off')

{{-- Toast Container --}}
<div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

<style>
:root {
    --kp-blue: #0066B3;
    --kp-green: #009639;
    --kp-yellow: #FFD700;
    --kp-dark: #003f20;
    --kp-orange: #fd7e14;
}

/* Hero Section */
.dashboard-hero {
    background: linear-gradient(135deg, var(--kp-blue) 0%, var(--kp-green) 100%);
}

/* Filter Card */
.filter-card {
    background: white;
    border: 1px solid rgba(0,0,0,0.08);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

/* Metric Cards */
.metric-card-danger {
    background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
}

.metric-card-success {
    background: linear-gradient(135deg, var(--kp-green) 0%, #00802c 100%);
}

.metric-card-warning {
    background: linear-gradient(135deg, var(--kp-yellow) 0%, #e6c300 100%);
}

.metric-card-info {
    background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
}

.metric-value-large {
    font-size: 1.75rem;
    line-height: 1.2;
}

.metric-value-medium {
    font-size: 1.25rem;
}

.metric-icon-large {
    width: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Customer Avatar */
.customer-avatar {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Button Styles */
.btn-dashboard-action {
    padding: 8px 20px;
    border-radius: 50px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-dashboard-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-kp-primary {
    background: var(--kp-blue);
    border-color: var(--kp-blue);
    color: white;
}

.btn-kp-primary:hover {
    background: #005499;
    border-color: #005499;
}

.btn-outline-kp-primary {
    border: 1px solid var(--kp-blue);
    color: var(--kp-blue);
}

.btn-outline-kp-primary:hover {
    background: var(--kp-blue);
    color: white;
}

/* Color Classes */
.bg-kp-blue-light { background: rgba(0, 102, 179, 0.1); }
.bg-orange { background-color: var(--kp-orange) !important; color: white !important; }
.bg-white-20 { background: rgba(255, 255, 255, 0.2); }
.bg-dark-20 { background: rgba(0, 0, 0, 0.2); }

.text-white-70 { color: rgba(255, 255, 255, 0.7); }
.text-white-50 { color: rgba(255, 255, 255, 0.5); }
.text-kp-dark-70 { color: rgba(0, 63, 32, 0.7); }
.text-kp-dark-50 { color: rgba(0, 63, 32, 0.5); }

/* Table Styles */
.table th {
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #5a5c69;
}

.table td {
    vertical-align: middle;
}

/* Progress Bar */
.progress {
    background-color: rgba(255, 255, 255, 0.3);
    border-radius: 9999px;
}

.progress-bar {
    border-radius: 9999px;
}

/* Rounded Utilities */
.rounded-4 { border-radius: 1rem !important; }
.rounded-pill { border-radius: 9999px !important; }

/* Toast */
.toast {
    min-width: 280px;
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Responsive */
@media (max-width: 768px) {
    .metric-value-large { font-size: 1.25rem; }
    .btn-dashboard-action { padding: 6px 16px; font-size: 0.875rem; }
}

@media (max-width: 576px) {
    .dashboard-hero { text-align: center; }
    .hero-icon { display: none; }
}
</style>

<script>
function filterByCurrency(currency) {
    window.location.href = '{{ route("finance.debt.dashboard") }}?currency=' + currency;
}

// Wait for DOM to be ready
$(document).ready(function() {
    console.log('Debt Dashboard script loaded');

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

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
            new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($paymentTrend->pluck('month')),
                    datasets: [
                        {
                            label: 'Total Billed',
                            data: @json($paymentTrend->pluck('total_billed')),
                            borderColor: '#0066B3',
                            backgroundColor: 'rgba(0, 102, 179, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Total Paid',
                            data: @json($paymentTrend->pluck('total_paid')),
                            borderColor: '#009639',
                            backgroundColor: 'rgba(0, 150, 57, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Overdue Invoices',
                            data: @json($paymentTrend->pluck('overdue_invoices')),
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#e9ecef' } },
                        x: { grid: { display: false } }
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
        $.ajax({
            url: '{{ route("finance.debt.overdue-invoices") }}',
            method: 'GET',
            beforeSend: function() {
                $('#overdueInvoicesBody').html(`
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="spinner-border spinner-border-sm text-kp-blue" role="status"></div>
                            <span class="ms-2 text-muted">Loading...</span>
                        </td>
                    </tr>
                `);
            },
            success: function(data) {
                if (data && data.trim().length > 0) {
                    $('#overdueInvoicesBody').html(data);
                    // Re-initialize tooltips for new content
                    $('[data-bs-toggle="tooltip"]').tooltip();
                    console.log('Overdue invoices loaded successfully');
                } else {
                    $('#overdueInvoicesBody').html(`
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-success opacity-25 mb-2"></i>
                                <p class="text-muted">No overdue invoices found</p>
                            </td>
                        </tr>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading overdue invoices:', error);
                $('#overdueInvoicesBody').html(`
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger opacity-25 mb-2"></i>
                            <p class="text-danger">Error loading data. Please refresh the page.</p>
                        </td>
                    </tr>
                `);
            }
        });
    }

    // Select all checkbox functionality
    $('#selectAll').change(function() {
        const isChecked = $(this).prop('checked');
        $('.invoice-checkbox').prop('checked', isChecked);
    });

    // Handle invoice row selection
    $(document).on('change', '.invoice-checkbox', function() {
        const totalCheckboxes = $('.invoice-checkbox').length;
        const checkedCheckboxes = $('.invoice-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
    });

    // Show toast notification
    window.showToast = function(message, type = 'success') {
        const toast = $(`
            <div class="toast align-items-center text-bg-${type} border-0 mb-2" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);

        $('#toastContainer').append(toast);
        const bsToast = new bootstrap.Toast(toast[0], { autohide: true, delay: 3000 });
        bsToast.show();

        toast.on('hidden.bs.toast', function() { $(this).remove(); });
    };

    // Handle send reminder button
    $(document).on('click', '.send-reminder', function() {
        const invoiceId = $(this).data('invoice-id');
        const $button = $(this);

        if (confirm('Send reminder for this invoice?')) {
            $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            const url = '{{ route("finance.debt.invoice.send-reminder", ":id") }}'.replace(':id', invoiceId);

            $.ajax({
                url: url,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    $button.html('<i class="fas fa-check"></i>');
                    setTimeout(() => {
                        $button.prop('disabled', false).html('<i class="fas fa-envelope"></i>');
                    }, 2000);
                    showToast('Reminder sent successfully!', 'success');
                },
                error: function(xhr) {
                    $button.prop('disabled', false).html('<i class="fas fa-envelope"></i>');
                    const errorMsg = xhr.responseJSON?.message || 'Error sending reminder';
                    showToast(errorMsg, 'danger');
                }
            });
        }
    });

    // Handle view customer debt
    window.viewCustomerDebt = function(customerId) {
        window.location.href = '{{ route("finance.debt.customer", ":id") }}'.replace(':id', customerId);
    };

    // Export report button
    $('#exportReportBtn').click(function() {
        const currency = $('#currencyFilter').val();
        window.location.href = '{{ route("finance.debt.export") }}?currency=' + currency;
    });

    // Initialize everything
    initializeChart();
    loadOverdueInvoices();

    // Auto-refresh every 60 seconds
    setInterval(loadOverdueInvoices, 60000);
});
</script>
@endsection
