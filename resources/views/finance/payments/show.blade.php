@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-money-bill-wave text-success me-2"></i>Payment Details
                    </h1>
                    <p class="text-muted mb-0">Payment #{{ $payment->payment_number }}</p>
                </div>
                <div>
                    <a href="{{ route('finance.payments.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                    @if($payment->status === 'pending')
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#validateModal">
                            <i class="fas fa-check-circle me-2"></i>Validate Payment
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times-circle me-2"></i>Reject Payment
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Payment Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-info-circle text-kp-blue me-2"></i>Payment Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%" class="text-muted"><strong>Payment Number:</strong></td>
                                    <td><code>{{ $payment->payment_number }}</code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Customer:</strong></td>
                                    <td>{{ $payment->customer->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Customer Email:</strong></td>
                                    <td>{{ $payment->customer->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Amount:</strong></td>
                                    <td class="fw-bold text-success">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                </tr>
                                @if($payment->amount_kes)
                                <tr>
                                    <td class="text-muted"><strong>Amount (KES):</strong></td>
                                    <td>KES {{ number_format($payment->amount_kes, 2) }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%" class="text-muted"><strong>Payment Date:</strong></td>
                                    <td>{{ $payment->payment_date ? $payment->payment_date->format('F d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Payment Method:</strong></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <i class="fas fa-credit-card me-1"></i>
                                            {{ $payment->payment_method }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Reference Number:</strong></td>
                                    <td><code>{{ $payment->reference_number ?? 'N/A' }}</code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Bank Name:</strong></td>
                                    <td>{{ $payment->bank_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Bank Branch:</strong></td>
                                    <td>{{ $payment->bank_branch ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($payment->notes)
                        <hr>
                        <div class="alert alert-secondary">
                            <strong><i class="fas fa-sticky-note me-2"></i>Notes:</strong>
                            <p class="mb-0 mt-1">{{ $payment->notes }}</p>
                        </div>
                    @endif

                    @if($payment->validation_notes && $payment->status === 'validated')
                        <div class="alert alert-success">
                            <strong><i class="fas fa-check-circle me-2"></i>Validation Notes:</strong>
                            <p class="mb-0 mt-1">{{ $payment->validation_notes }}</p>
                        </div>
                    @endif

                    @if($payment->validation_notes && $payment->status === 'rejected')
                        <div class="alert alert-danger">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Rejection Reason:</strong>
                            <p class="mb-0 mt-1">{{ $payment->validation_notes }}</p>
                        </div>
                    @endif

                    @if($payment->deposit_slip_path)
                        <hr>
                        <div class="text-center">
                            <a href="{{ route('finance.payments.download-slip', $payment) }}" class="btn btn-outline-info">
                                <i class="fas fa-download me-2"></i>Download Deposit Slip
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Related Invoice -->
            @if($payment->billing)
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-file-invoice text-kp-blue me-2"></i>Related Invoice
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Invoice Number:</strong> {{ $payment->billing->billing_number }}</p>
                            <p><strong>Invoice Date:</strong> {{ $payment->billing->billing_date ? $payment->billing->billing_date->format('F d, Y') : 'N/A' }}</p>
                            <p><strong>Due Date:</strong>
                                <span class="{{ $payment->billing->due_date && $payment->billing->due_date < now() ? 'text-danger' : '' }}">
                                    {{ $payment->billing->due_date ? $payment->billing->due_date->format('F d, Y') : 'N/A' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Amount:</strong> {{ $payment->billing->currency }} {{ number_format($payment->billing->total_amount, 2) }}</p>
                            <p><strong>Paid Amount:</strong> <span class="text-success">{{ $payment->billing->currency }} {{ number_format($payment->billing->paid_amount ?? 0, 2) }}</span></p>
                            <p><strong>Balance:</strong>
                                <span class="{{ ($payment->billing->total_amount - ($payment->billing->paid_amount ?? 0)) > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $payment->billing->currency }} {{ number_format($payment->billing->total_amount - ($payment->billing->paid_amount ?? 0), 2) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('finance.billing.show', $payment->billing) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>View Invoice Details
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card shadow mb-4">
                @php
                    $statusColors = [
                        'pending' => 'warning',
                        'validated' => 'success',
                        'rejected' => 'danger'
                    ];
                    $statusIcons = [
                        'pending' => 'clock',
                        'validated' => 'check-circle',
                        'rejected' => 'times-circle'
                    ];
                    $color = $statusColors[$payment->status] ?? 'secondary';
                    $icon = $statusIcons[$payment->status] ?? 'circle';
                @endphp
                <div class="card-header bg-{{ $color }} text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-{{ $icon }} me-2"></i>Status Information
                    </h6>
                </div>
                <div class="card-body">
                    <p><strong>Status:</strong>
                        <span class="badge bg-{{ $color }} rounded-pill">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </p>
                    <p><strong>Recorded By:</strong> {{ $payment->creator->name ?? 'N/A' }}</p>
                    <p><strong>Recorded At:</strong> {{ $payment->created_at ? $payment->created_at->format('F d, Y H:i') : 'N/A' }}</p>

                    @if($payment->validated_by)
                        <hr>
                        <p><strong>Validated By:</strong> {{ $payment->validator->name ?? 'N/A' }}</p>
                        <p><strong>Validated At:</strong> {{ $payment->validated_at ? $payment->validated_at->format('F d, Y H:i') : 'N/A' }}</p>
                        @if($payment->validation_notes)
                            <p><strong>Notes:</strong> {{ $payment->validation_notes }}</p>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Transaction Details -->
            @if($payment->transaction)
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>Transaction Details
                    </h6>
                </div>
                <div class="card-body">
                    <p><strong>Transaction #:</strong> {{ $payment->transaction->transaction_number }}</p>
                    <p><strong>New Balance:</strong> {{ $payment->transaction->currency }} {{ number_format($payment->transaction->balance, 2) }}</p>
                    <p><strong>Completed:</strong> {{ $payment->transaction->completed_at ? $payment->transaction->completed_at->format('F d, Y H:i') : 'N/A' }}</p>
                </div>
            </div>
            @endif

            <!-- Help Card -->
            <div class="card shadow mt-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-life-ring me-2"></i>Need Help?
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">If you have questions about this payment, contact support.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.show', $payment->user_id ?? '#') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-user me-2"></i>View Customer Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Validate Modal -->
<div class="modal fade" id="validateModal" tabindex="-1">
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
                        <input type="checkbox" name="apply_to_billing" class="form-check-input" id="applyToBilling" value="1" checked>
                        <label class="form-check-label" for="applyToBilling">
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
<div class="modal fade" id="rejectModal" tabindex="-1">
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

<script>
document.getElementById('validateForm')?.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    }
});

document.getElementById('rejectForm')?.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    }
});
</script>

<style>
.text-kp-blue { color: #0066B3 !important; }
.bg-kp-primary { background-color: #0066B3 !important; }
.btn-outline-kp-primary { border-color: #0066B3; color: #0066B3; }
.btn-outline-kp-primary:hover { background-color: #0066B3; color: white; }
</style>
@endsection
