{{-- resources/views/finance/debt/reports/aging.blade.php --}}
@extends('layouts.app')

@section('title', 'Aging Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('finance.debt.dashboard') }}">Debt Dashboard</a></li>
                    <li class="breadcrumb-item active">Aging Report</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="fas fa-chart-bar text-primary me-2"></i>Accounts Receivable Aging Report
            </h1>
            <p class="text-muted mb-0">Aging analysis by currency - USD and KSH</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary" onclick="printReport()">
                <i class="fas fa-print me-2"></i>Print
            </button>
            <button class="btn btn-primary" onclick="exportToExcel()">
                <i class="fas fa-file-excel me-2"></i>Export to Excel
            </button>
            <a href="{{ route('finance.debt.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Report Filters -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Report Filters</h6>
        </div>
        <div class="card-body">
            <form id="agingReportFilters" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">As of Date</label>
                    <input type="date" class="form-control" name="as_of_date"
                           value="{{ request('as_of_date', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Customer</label>
                    <select class="form-select" name="customer_id">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="payment_plan" {{ request('status') == 'payment_plan' ? 'selected' : '' }}>Payment Plan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Currency</label>
                    <select class="form-select" name="currency">
                        <option value="all" {{ request('currency') == 'all' ? 'selected' : '' }}>All Currencies</option>
                        <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD Only</option>
                        <option value="KSH" {{ request('currency') == 'KSH' ? 'selected' : '' }}>KSH Only</option>
                    </select>
                </div>
                <div class="col-md-3 offset-md-9">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Statistics - USD -->
    <div class="row mb-2">
        <div class="col-12">
            <h5 class="text-primary">
                <i class="fas fa-dollar-sign me-2"></i>USD Summary
            </h5>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Total Outstanding (USD)
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        ${{ number_format($summary->total_outstanding_usd ?? 0, 2) }}
                    </div>
                    <div class="mt-2 text-xs text-muted">
                        {{ $summary->total_invoices_usd ?? 0 }} invoices
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Current (0-30)
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        ${{ number_format($summary->current_amount_usd ?? 0, 2) }}
                    </div>
                    <div class="mt-2 text-xs text-muted">
                        {{ $summary->current_count_usd ?? 0 }} invoices
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        31-60 Days
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        ${{ number_format($summary->days_31_60_amount_usd ?? 0, 2) }}
                    </div>
                    <div class="mt-2 text-xs text-muted">
                        {{ $summary->days_31_60_count_usd ?? 0 }} invoices
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                        61-90 Days
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        ${{ number_format($summary->days_61_90_amount_usd ?? 0, 2) }}
                    </div>
                    <div class="mt-2 text-xs text-muted">
                        {{ $summary->days_61_90_count_usd ?? 0 }} invoices
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-dark shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                        90+ Days
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        ${{ number_format($summary->days_90_plus_amount_usd ?? 0, 2) }}
                    </div>
                    <div class="mt-2 text-xs text-muted">
                        {{ $summary->days_90_plus_count_usd ?? 0 }} invoices
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics - KSH -->
    <div class="row mb-2">
        <div class="col-12">
            <h5 class="text-success">
                <i class="fas fa-shilling-sign me-2"></i>KSH Summary
            </h5>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Total Outstanding (KSH)
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        KSH {{ number_format($summary->total_outstanding_ksh ?? 0, 2) }}
                    </div>
                    <div class="mt-2 text-xs text-muted">
                        {{ $summary->total_invoices_ksh ?? 0 }} invoices
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Current (0-30)
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        KSH {{ number_format($summary->current_amount_ksh ?? 0, 2) }}
                    </div>
                    <div class="mt-2 text-xs text-muted">
                        {{ $summary->current_count_ksh ?? 0 }} invoices
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        31-60 Days
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        KSH {{ number_format($summary->days_31_60_amount_ksh ?? 0, 2) }}
                    </div>
                    <div class="mt-2 text-xs text-muted">
                        {{ $summary->days_31_60_count_ksh ?? 0 }} invoices
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                        61-90 Days
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        KSH {{ number_format($summary->days_61_90_amount_ksh ?? 0, 2) }}
                    </div>
                    <div class="mt-2 text-xs text-muted">
                        {{ $summary->days_61_90_count_ksh ?? 0 }} invoices
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-dark shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                        90+ Days
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        KSH {{ number_format($summary->days_90_plus_amount_ksh ?? 0, 2) }}
                    </div>
                    <div class="mt-2 text-xs text-muted">
                        {{ $summary->days_90_plus_count_ksh ?? 0 }} invoices
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aging Chart -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aging Distribution by Currency</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <canvas id="agingChart" height="100"></canvas>
                        </div>
                        <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Age Bucket</th>
                                            <th class="text-end">USD</th>
                                            <th class="text-end">KSH</th>
                                            <th class="text-end">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="badge bg-success">Current (0-30)</span></td>
                                            <td class="text-end">${{ number_format($summary->current_amount_usd ?? 0, 2) }}</td>
                                            <td class="text-end">KSH {{ number_format($summary->current_amount_ksh ?? 0, 2) }}</td>
                                            <td class="text-end">{{ number_format(($summary->current_amount_usd + $summary->current_amount_ksh/130) / max($summary->total_outstanding_usd + $summary->total_outstanding_ksh/130, 1) * 100, 1) }}%</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-warning">31-60 Days</span></td>
                                            <td class="text-end">${{ number_format($summary->days_31_60_amount_usd ?? 0, 2) }}</td>
                                            <td class="text-end">KSH {{ number_format($summary->days_31_60_amount_ksh ?? 0, 2) }}</td>
                                            <td class="text-end">{{ number_format(($summary->days_31_60_amount_usd + $summary->days_31_60_amount_ksh/130) / max($summary->total_outstanding_usd + $summary->total_outstanding_ksh/130, 1) * 100, 1) }}%</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-danger">61-90 Days</span></td>
                                            <td class="text-end">${{ number_format($summary->days_61_90_amount_usd ?? 0, 2) }}</td>
                                            <td class="text-end">KSH {{ number_format($summary->days_61_90_amount_ksh ?? 0, 2) }}</td>
                                            <td class="text-end">{{ number_format(($summary->days_61_90_amount_usd + $summary->days_61_90_amount_ksh/130) / max($summary->total_outstanding_usd + $summary->total_outstanding_ksh/130, 1) * 100, 1) }}%</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-dark">90+ Days</span></td>
                                            <td class="text-end">${{ number_format($summary->days_90_plus_amount_usd ?? 0, 2) }}</td>
                                            <td class="text-end">KSH {{ number_format($summary->days_90_plus_amount_ksh ?? 0, 2) }}</td>
                                            <td class="text-end">{{ number_format(($summary->days_90_plus_amount_usd + $summary->days_90_plus_amount_ksh/130) / max($summary->total_outstanding_usd + $summary->total_outstanding_ksh/130, 1) * 100, 1) }}%</td>
                                        </tr>
                                        <tr class="table-light">
                                            <th>Total</th>
                                            <th class="text-end">${{ number_format($summary->total_outstanding_usd ?? 0, 2) }}</th>
                                            <th class="text-end">KSH {{ number_format($summary->total_outstanding_ksh ?? 0, 2) }}</th>
                                            <th class="text-end">100%</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Aging Report -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Detailed Aging Report by Customer</h6>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="showDetails" checked>
                        <label class="form-check-label" for="showDetails">Show Invoice Details</label>
                    </div>
                </div>
                <div class="card-body">
                    <!-- By Customer Summary -->
                    <div class="table-responsive mb-4">
                        <table class="table table-hover" id="agingByCustomer">
                            <thead class="bg-light">
                                <tr>
                                    <th>Customer</th>
                                    <th class="text-end" colspan="2">Current</th>
                                    <th class="text-end" colspan="2">31-60 Days</th>
                                    <th class="text-end" colspan="2">61-90 Days</th>
                                    <th class="text-end" colspan="2">90+ Days</th>
                                    <th class="text-end" colspan="2">Total</th>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th class="text-end small">USD</th>
                                    <th class="text-end small">KSH</th>
                                    <th class="text-end small">USD</th>
                                    <th class="text-end small">KSH</th>
                                    <th class="text-end small">USD</th>
                                    <th class="text-end small">KSH</th>
                                    <th class="text-end small">USD</th>
                                    <th class="text-end small">KSH</th>
                                    <th class="text-end small">USD</th>
                                    <th class="text-end small">KSH</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agingByCustomer as $customer)
                                <tr>
                                    <td>
                                        <a href="{{ route('finance.debt.customer', $customer->customer_id) }}" class="text-decoration-none">
                                            <strong>{{ $customer->customer_name }}</strong><br>
                                            <small class="text-muted">{{ $customer->invoices_count }} invoices</small>
                                        </a>
                                    </td>
                                    <td class="text-end">
                                        @if($customer->current_amount_usd > 0)
                                            <span class="text-success">${{ number_format($customer->current_amount_usd, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($customer->current_amount_ksh > 0)
                                            <span class="text-success">KSH {{ number_format($customer->current_amount_ksh, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($customer->days_31_60_amount_usd > 0)
                                            <span class="text-warning">${{ number_format($customer->days_31_60_amount_usd, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($customer->days_31_60_amount_ksh > 0)
                                            <span class="text-warning">KSH {{ number_format($customer->days_31_60_amount_ksh, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($customer->days_61_90_amount_usd > 0)
                                            <span class="text-danger">${{ number_format($customer->days_61_90_amount_usd, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($customer->days_61_90_amount_ksh > 0)
                                            <span class="text-danger">KSH {{ number_format($customer->days_61_90_amount_ksh, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($customer->days_90_plus_amount_usd > 0)
                                            <span class="text-dark">${{ number_format($customer->days_90_plus_amount_usd, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($customer->days_90_plus_amount_ksh > 0)
                                            <span class="text-dark">KSH {{ number_format($customer->days_90_plus_amount_ksh, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <strong>${{ number_format($customer->total_amount_usd, 2) }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <strong>KSH {{ number_format($customer->total_amount_ksh, 2) }}</strong>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewCustomerDetails({{ $customer->customer_id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th>Total</th>
                                    <th class="text-end">${{ number_format($summary->current_amount_usd ?? 0, 2) }}</th>
                                    <th class="text-end">KSH {{ number_format($summary->current_amount_ksh ?? 0, 2) }}</th>
                                    <th class="text-end">${{ number_format($summary->days_31_60_amount_usd ?? 0, 2) }}</th>
                                    <th class="text-end">KSH {{ number_format($summary->days_31_60_amount_ksh ?? 0, 2) }}</th>
                                    <th class="text-end">${{ number_format($summary->days_61_90_amount_usd ?? 0, 2) }}</th>
                                    <th class="text-end">KSH {{ number_format($summary->days_61_90_amount_ksh ?? 0, 2) }}</th>
                                    <th class="text-end">${{ number_format($summary->days_90_plus_amount_usd ?? 0, 2) }}</th>
                                    <th class="text-end">KSH {{ number_format($summary->days_90_plus_amount_ksh ?? 0, 2) }}</th>
                                    <th class="text-end">${{ number_format($summary->total_outstanding_usd ?? 0, 2) }}</th>
                                    <th class="text-end">KSH {{ number_format($summary->total_outstanding_ksh ?? 0, 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Invoice Details (Collapsible) -->
                    <div id="invoiceDetails" style="display: none;">
                        <hr>
                        <h6 class="mb-3">Invoice Details</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Customer</th>
                                        <th>Invoice Date</th>
                                        <th>Due Date</th>
                                        <th>Days Overdue</th>
                                        <th class="text-end">Amount</th>
                                        <th>Currency</th>
                                        <th class="text-end">Paid</th>
                                        <th class="text-end">Balance</th>
                                        <th>Age Bucket</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                    <tr>
                                        <td>
                                            <a href="{{ route('finance.billing.show', $invoice->id) }}" class="text-decoration-none">
                                                {{ $invoice->billing_number }}
                                            </a>
                                        </td>
                                        <td>{{ $invoice->customer_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($invoice->billing_date)->format('M d, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $invoice->days_overdue > 90 ? 'danger' : ($invoice->days_overdue > 60 ? 'warning' : 'info') }}">
                                                {{ $invoice->days_overdue }} days
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @if($invoice->currency == 'USD')
                                                ${{ number_format($invoice->total_amount, 2) }}
                                            @else
                                                KSH {{ number_format($invoice->total_amount, 2) }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $invoice->currency == 'USD' ? 'primary' : 'success' }}">
                                                {{ $invoice->currency }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @if($invoice->currency == 'USD')
                                                ${{ number_format($invoice->paid_amount, 2) }}
                                            @else
                                                KSH {{ number_format($invoice->paid_amount, 2) }}
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-danger">
                                                @if($invoice->currency == 'USD')
                                                    ${{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}
                                                @else
                                                    KSH {{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}
                                                @endif
                                            </strong>
                                        </td>
                                        <td>
                                            @if($invoice->days_overdue <= 30)
                                                <span class="badge bg-success">Current</span>
                                            @elseif($invoice->days_overdue <= 60)
                                                <span class="badge bg-warning">31-60</span>
                                            @elseif($invoice->days_overdue <= 90)
                                                <span class="badge bg-danger">61-90</span>
                                            @else
                                                <span class="badge bg-dark">90+</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $invoice->status == 'overdue' ? 'danger' : 'warning' }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Recommendations -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Action Recommendations</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-left-warning h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                        31-60 Days Overdue
                                    </h6>
                                    <ul class="small">
                                        <li>Send second reminder notice</li>
                                        <li>Make phone call follow-up</li>
                                        <li>Consider payment plan options</li>
                                        <li>Review account history</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-left-danger h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-exclamation-circle text-danger me-2"></i>
                                        61-90 Days Overdue
                                    </h6>
                                    <ul class="small">
                                        <li>Send final demand notice</li>
                                        <li>Escalate to collections manager</li>
                                        <li>Consider account suspension</li>
                                        <li>Prepare for legal action</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-left-dark h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-skull-crossbones text-dark me-2"></i>
                                        90+ Days Overdue
                                    </h6>
                                    <ul class="small">
                                        <li>Immediate collection agency referral</li>
                                        <li>Legal proceedings preparation</li>
                                        <li>Consider debt write-off</li>
                                        <li>Update credit bureau if applicable</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Aging Chart
// Aging Chart
const ctx = document.getElementById('agingChart').getContext('2d');
const agingChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Current (0-30)', '31-60 Days', '61-90 Days', '90+ Days'],
        datasets: [
            {
                label: 'USD Outstanding',
                data: [
                    {{ $summary->current_amount_usd ?? 0 }},
                    {{ $summary->days_31_60_amount_usd ?? 0 }},
                    {{ $summary->days_61_90_amount_usd ?? 0 }},
                    {{ $summary->days_90_plus_amount_usd ?? 0 }}
                ],
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1,
                yAxisID: 'y-usd'
            },
            {
                label: 'KSH Outstanding',
                data: [
                    {{ $summary->current_amount_ksh ?? 0 }},
                    {{ $summary->days_31_60_amount_ksh ?? 0 }},
                    {{ $summary->days_61_90_amount_ksh ?? 0 }},
                    {{ $summary->days_90_plus_amount_ksh ?? 0 }}
                ],
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderColor: 'rgb(40, 167, 69)',
                borderWidth: 1,
                yAxisID: 'y-ksh'
            }
        ]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.dataset.label || '';
                        const value = context.raw || 0;
                        const currency = label.includes('USD') ? '$' : 'KSH';
                        return label + ': ' + currency + ' ' + value.toLocaleString();
                    }
                }
            }
        },
        scales: {
            'y-usd': {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'USD Amount ($)'
                },
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            },
            'y-ksh': {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'KSH Amount'
                },
                ticks: {
                    callback: function(value) {
                        return 'KSH ' + value.toLocaleString();
                    }
                },
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
});

// Toggle details view
$('#showDetails').change(function() {
    if ($(this).is(':checked')) {
        $('#invoiceDetails').show();
    } else {
        $('#invoiceDetails').hide();
    }
});

// View customer details
function viewCustomerDetails(customerId) {
    window.location.href = `/finance/debt/customer/${customerId}`;
}

// Print report
function printReport() {
    window.print();
}

// Export to Excel
function exportToExcel() {
    // Simple export implementation
    const table = $('#agingByCustomer').clone();
    table.find('a, button').remove();

    // Create CSV content
    let csv = [];
    table.find('tr').each(function() {
        const row = [];
        $(this).find('th, td').each(function() {
            let text = $(this).text().trim();
            text = text.replace(/[$,]/g, '');
            row.push(text);
        });
        csv.push(row.join(','));
    });

    const csvContent = "data:text/csv;charset=utf-8," + csv.join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `aging_report_{{ now()->format('Y-m-d') }}.csv`);
    document.body.appendChild(link);
    link.click();
}

// Apply filters
$('#agingReportFilters').submit(function(e) {
    e.preventDefault();
    const formData = $(this).serialize();
    window.location.href = '{{ route("finance.debt.aging.report") }}?' + formData;
});
</script>

<style>
@media print {
    .btn, .form-control, .form-select, .card-header .d-flex, .card-header .form-check {
        display: none !important;
    }

    .card, .card-body {
        border: none !important;
        box-shadow: none !important;
    }

    .table {
        font-size: 12px;
    }

    h1, h2, h3, h4, h5, h6 {
        page-break-after: avoid;
    }

    .row {
        page-break-inside: avoid;
    }
}
</style>
@endpush
