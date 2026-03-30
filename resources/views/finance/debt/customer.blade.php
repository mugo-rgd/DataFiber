{{-- resources/views/finance/debt/customer.blade.php --}}
@extends('layouts.app')

@section('title', 'Customer Debt Details')

@section('content')
<div class="container-fluid">
    <!-- Customer Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('finance.debt.dashboard') }}">Debt Dashboard</a></li>
                    <li class="breadcrumb-item active">Customer Details</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="fas fa-user text-primary me-2"></i>{{ $customer->name }}
                <small class="text-muted ms-2">({{ $customer->email }})</small>
            </h1>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Print
            </button>
            <a href="{{ route('finance.debt.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Currency Summary Cards -->
    @if($summary->count() > 0)
        @foreach($summary as $currencySummary)
            @php
                $currency = $currencySummary->currency ?? 'USD';
                $symbol = $currency == 'USD' ? '$' : 'KSH';
            @endphp
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-left-primary shadow">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Outstanding ({{ $currency }})
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $symbol }} {{ number_format($currencySummary->total_outstanding ?? 0, 2) }}
                            </div>
                            <div class="mt-2 text-xs text-muted">
                                <i class="fas fa-file-invoice-dollar me-1"></i>{{ $currencySummary->overdue_count ?? 0 }} overdue invoices
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-left-warning shadow">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Avg Days Overdue ({{ $currency }})
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ round($currencySummary->avg_days_overdue ?? 0) }} days
                            </div>
                            <div class="mt-2 text-xs text-muted">
                                <i class="fas fa-clock me-1"></i>Max: {{ $currencySummary->max_days_overdue ?? 0 }} days
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-left-info shadow">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Payment Rate ({{ $currency }})
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($currencySummary->payment_rate ?? 0, 1) }}%
                            </div>
                            <div class="mt-2 text-xs text-muted">
                                @if($currencySummary->last_payment_date)
                                    @php
                                        $lastPayment = is_string($currencySummary->last_payment_date)
                                            ? \Carbon\Carbon::parse($currencySummary->last_payment_date)
                                            : $currencySummary->last_payment_date;
                                    @endphp
                                    Last: {{ $lastPayment->format('M d, Y') }}
                                @else
                                    Never
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-left-success shadow">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Paid ({{ $currency }})
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $symbol }} {{ number_format($currencySummary->total_paid ?? 0, 2) }}
                            </div>
                            <div class="mt-2 text-xs text-muted">
                                <i class="fas fa-check-circle me-1"></i>{{ $currencySummary->paid_invoices ?? 0 }} paid invoices
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i>No outstanding debt found for this customer.
        </div>
    @endif

    <!-- Customer Information -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th class="text-muted">Email:</th>
                                    <td>{{ $customer->email }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Phone:</th>
                                    <td>{{ $customer->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Joined:</th>
                                    <td>{{ $customer->created_at->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th class="text-muted">Total Invoices:</th>
                                    <td>{{ $summary->sum('total_invoices') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Active Leases:</th>
                                    <td>{{ $customer->leases_count ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Customer ID:</th>
                                    <td>#{{ str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#sendReminderModal">
                                <i class="fas fa-envelope me-2"></i>Send Reminder
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#paymentPlanModal">
                                <i class="fas fa-calendar-alt me-2"></i>Create Payment Plan
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#writeOffModal">
                                <i class="fas fa-file-contract me-2"></i>Write Off Debt
                            </button>
                        </div>
                        <div class="col-6">
                            <a href="mailto:{{ $customer->email }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-paper-plane me-2"></i>Send Email
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overdue Invoices -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Overdue Invoices</h6>
                </div>
                <div class="card-body">
                    @if($overdueInvoices->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Billing Date</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Currency</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Days Overdue</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overdueInvoices as $invoice)
                                @php
                                    $symbol = $invoice->currency == 'USD' ? '$' : 'KSH';
                                @endphp
                                <tr class="{{ $invoice->days_overdue > 90 ? 'table-danger' : ($invoice->days_overdue > 60 ? 'table-warning' : '') }}">
                                    <td>
                                        <a href="{{ route('finance.billing.show', $invoice->id) }}" class="text-decoration-none">
                                            {{ $invoice->billing_number }}
                                        </a>
                                    </td>
                                    <td>{{ $invoice->billing_date->format('M d, Y') }}</td>
                                    <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                                    <td>{{ $symbol }} {{ number_format($invoice->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $invoice->currency == 'USD' ? 'primary' : 'success' }}">
                                            {{ $invoice->currency }}
                                        </span>
                                    </td>
                                    <td>{{ $symbol }} {{ number_format($invoice->paid_amount, 2) }}</td>
                                    <td><strong class="text-danger">{{ $symbol }} {{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $invoice->days_overdue > 90 ? 'danger' : 'warning' }}">
                                            {{ $invoice->days_overdue }} days
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $invoice->status == 'overdue' ? 'danger' : 'warning' }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="sendReminderSingle({{ $invoice->id }})">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                            <a href="{{ route('finance.billing.show', $invoice->id) }}" class="btn btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="6" class="text-end"><strong>Total Outstanding:</strong></td>
                                    <td>
                                        @php
                                            $totalUSD = $overdueInvoices->where('currency', 'USD')->sum(fn($inv) => $inv->total_amount - $inv->paid_amount);
                                            $totalKSH = $overdueInvoices->where('currency', 'KSH')->sum(fn($inv) => $inv->total_amount - $inv->paid_amount);
                                        @endphp
                                        @if($totalUSD > 0)
                                            <div><strong class="text-danger">${{ number_format($totalUSD, 2) }}</strong></div>
                                        @endif
                                        @if($totalKSH > 0)
                                            <div><strong class="text-danger">KSH {{ number_format($totalKSH, 2) }}</strong></div>
                                        @endif
                                    </td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5>No Overdue Invoices</h5>
                        <p class="text-muted">This customer has no overdue invoices.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment History</h6>
                </div>
                <div class="card-body">
                    @if($paymentHistory->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice #</th>
                                    <th>Amount</th>
                                    <th>Currency</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentHistory as $payment)
                                @php
                                    $symbol = $payment->currency == 'USD' ? '$' : 'KSH';
                                @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date ?? $payment->updated_at)->format('M d, Y') }}</td>
                                    <td>{{ $payment->billing_number }}</td>
                                    <td>{{ $symbol }} {{ number_format($payment->paid_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payment->currency == 'USD' ? 'primary' : 'success' }}">
                                            {{ $payment->currency }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'cash')) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Paid
                                        </span>
                                    </td>
                                    <td>{{ $payment->notes ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-3">
                        <p class="text-muted">No payment history available.</p>
                    </div>
                    @endif
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

@push('scripts')
<script>
function sendReminderSingle(invoiceId) {
    // Set the invoice ID in the modal
    $('#invoice_id').val(invoiceId);
    $('#sendReminderModal').modal('show');
}

function initCustomerPage() {
    // Any initialization for customer page
    console.log('Customer page initialized');
}

$(document).ready(function() {
    initCustomerPage();
});
</script>
@endpush
