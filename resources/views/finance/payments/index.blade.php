@extends('layouts.app')

@section('title', 'Payment Management - Finance')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2" style="color: #0066B3;">
                        <i class="fas fa-credit-card me-2"></i>Payments Management
                    </h1>
                    <p class="text-muted mb-0">Record and validate offline customer payments</p>
                </div>
                <div>
                    <a href="{{ route('finance.payments.create') }}" class="btn btn-success me-2">
                        <i class="fas fa-plus me-2"></i>Record Payment
                    </a>
                    <a href="{{ route('finance.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Validation
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Validated
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['validated'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejected
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rejected'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Validated (KES)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">KES {{ number_format($stats['total_amount'] ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filters
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('finance.payments.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Validated</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select name="payment_method" id="payment_method" class="form-select">
                        <option value="">All Methods</option>
                        @foreach($paymentMethods ?? ['Bank Transfer', 'Cheque', 'Cash', 'M-Pesa', 'RTGS', 'EFT', 'Mobile Money'] as $method)
                            <option value="{{ $method }}" {{ request('payment_method') == $method ? 'selected' : '' }}>{{ $method }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label for="customer_id" class="form-label">Customer</label>
                    <select name="customer_id" id="customer_id" class="form-select">
                        <option value="">All Customers</option>
                        @foreach($customers ?? [] as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card shadow">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Payment Records
            </h5>
        </div>
        <div class="card-body p-0">
            @if(isset($payments) && $payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Payment #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Payment Date</th>
                                <th>Status</th>
                                <th>Recorded By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'validated' => 'success',
                                        'rejected' => 'danger'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Pending Validation',
                                        'validated' => 'Validated',
                                        'rejected' => 'Rejected'
                                    ];
                                    $color = $statusColors[$payment->status] ?? 'secondary';
                                    $label = $statusLabels[$payment->status] ?? ucfirst($payment->status);
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $payment->payment_number }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ $payment->id }}</small>
                                    </td>
                                    <td>
                                        @if($payment->customer)
                                            <strong>{{ $payment->customer->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $payment->customer->email }}</small>
                                        @else
                                            <span class="text-muted">Customer #{{ $payment->user_id }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</strong>
                                        @if($payment->amount_kes)
                                            <br>
                                            <small class="text-muted">KES {{ number_format($payment->amount_kes, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $payment->payment_method }}
                                        @if($payment->reference_number)
                                            <br>
                                            <small class="text-muted">Ref: {{ $payment->reference_number }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $color }} rounded-pill px-3 py-2">
                                            <i class="fas {{ $payment->status === 'validated' ? 'fa-check-circle' : ($payment->status === 'pending' ? 'fa-clock' : 'fa-times-circle') }} me-1"></i>
                                            {{ $label }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $payment->creator->name ?? 'N/A' }}
                                        <br>
                                        <small class="text-muted">{{ $payment->created_at ? $payment->created_at->format('M d, H:i') : 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group gap-1">
                                            <a href="{{ route('finance.payments.show', $payment) }}"
                                               class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($payment->deposit_slip_path)
                                                <a href="{{ route('finance.payments.download-slip', $payment) }}"
                                                   class="btn btn-sm btn-outline-info rounded-pill px-3"
                                                   title="Download Deposit Slip">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                            @if($payment->status === 'pending')
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-success rounded-pill px-3"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#validateModal{{ $payment->id }}"
                                                        title="Validate Payment">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#rejectModal{{ $payment->id }}"
                                                        title="Reject Payment">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </td>

                                <!-- Validate Modal -->
                                <div class="modal fade" id="validateModal{{ $payment->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title">Validate Payment</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('finance.payments.validate', $payment) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <p>You are about to validate payment <strong>{{ $payment->payment_number }}</strong>.</p>
                                                    <p><strong>Amount:</strong> {{ $payment->currency }} {{ number_format($payment->amount, 2) }}</p>
                                                    <p><strong>Customer:</strong> {{ $payment->customer->name ?? 'N/A' }}</p>

                                                    <div class="mb-3">
                                                        <label class="form-label">Validation Notes (Optional)</label>
                                                        <textarea name="validation_notes" class="form-control" rows="3" placeholder="Add any notes about this validation..."></textarea>
                                                    </div>

                                                    <div class="form-check mb-3">
                                                        <input type="checkbox" name="apply_to_billing" class="form-check-input" id="applyToBilling{{ $payment->id }}" value="1" checked>
                                                        <label class="form-check-label" for="applyToBilling{{ $payment->id }}">
                                                            Apply this payment to the related invoice
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Confirm Validation</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal{{ $payment->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Reject Payment</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('finance.payments.reject', $payment) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <p>You are about to <strong class="text-danger">REJECT</strong> payment <strong>{{ $payment->payment_number }}</strong>.</p>
                                                    <p><strong>Amount:</strong> {{ $payment->currency }} {{ number_format($payment->amount, 2) }}</p>
                                                    <p><strong>Customer:</strong> {{ $payment->customer->name ?? 'N/A' }}</p>

                                                    <div class="mb-3">
                                                        <label class="form-label required">Rejection Reason</label>
                                                        <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Why is this payment being rejected?"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} entries
                        </div>
                        <div>
                            {{ $payments->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="text-muted mb-3">
                        <i class="fas fa-credit-card fa-4x"></i>
                    </div>
                    <h4 class="text-muted">No Payments Found</h4>
                    <p class="text-muted mb-3">No payment records match your current filters.</p>
                    <a href="{{ route('finance.payments.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Record New Payment
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 4px solid #0066B3 !important; }
.border-left-success { border-left: 4px solid #009639 !important; }
.border-left-warning { border-left: 4px solid #FFD700 !important; }
.border-left-danger { border-left: 4px solid #dc3545 !important; }
.badge { font-size: 0.75rem; padding: 0.35rem 0.65rem; }
.table th { font-weight: 600; font-size: 0.875rem; }
.table td { vertical-align: middle; }
.card-header { background-color: #f8f9fa; border-bottom: 1px solid #e3e6f0; }
.modal-header .btn-close { filter: brightness(0) invert(1); }
.btn-outline-kp-primary { border-color: #0066B3; color: #0066B3; }
.btn-outline-kp-primary:hover { background-color: #0066B3; color: white; }
.btn-outline-kp-success { border-color: #009639; color: #009639; }
.btn-outline-kp-success:hover { background-color: #009639; color: white; }
.btn-outline-kp-warning { border-color: #FFD700; color: #856404; }
.btn-outline-kp-warning:hover { background-color: #FFD700; color: #856404; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(el => new bootstrap.Tooltip(el));
});

// Form submission loading state
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        }
    });
});
</script>
@endsection
