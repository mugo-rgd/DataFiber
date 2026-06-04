@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-receipt text-success me-2"></i>Payment Details
                    </h1>
                    <p class="text-muted mb-0">Payment Reference: {{ $payment->payment_number ?? $payment->reference_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <a href="{{ route('customer.payments.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Payments
                    </a>
                    <a href="{{ route('customer.invoices.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-invoice me-2"></i>View Invoices
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
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
                                    <td width="40%" class="text-muted"><strong>Payment Reference:</strong></td>
                                    <td><code>{{ $payment->payment_number ?? $payment->reference_number ?? 'N/A' }}</code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Transaction ID:</strong></td>
                                    <td>{{ $payment->transaction_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Amount Paid:</strong></td>
                                    <td class="fw-bold text-success">
                                        {{ $payment->currency ?? 'KES' }} {{ number_format($payment->amount, 2) }}
                                        @if($payment->amount_kes && $payment->currency !== 'KES')
                                            <br>
                                            <small class="text-muted">(KES {{ number_format($payment->amount_kes, 2) }})</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Payment Method:</strong></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <i class="fas fa-{{ $payment->payment_method === 'M-Pesa' ? 'mobile-alt' : ($payment->payment_method === 'Bank Transfer' ? 'university' : 'credit-card') }} me-1"></i>
                                            {{ $payment->payment_method ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Payment Date:</strong></td>
                                    <td>
                                        <i class="fas fa-calendar-alt me-1 text-muted"></i>
                                        {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y') : 'N/A' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%" class="text-muted"><strong>Status:</strong></td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'validated' => 'success',
                                                'rejected' => 'danger',
                                                'completed' => 'success',
                                                'failed' => 'danger',
                                                'processing' => 'info'
                                            ];
                                            $statusIcons = [
                                                'pending' => 'clock',
                                                'validated' => 'check-circle',
                                                'rejected' => 'times-circle',
                                                'completed' => 'check-circle',
                                                'failed' => 'times-circle',
                                                'processing' => 'spinner'
                                            ];
                                            $color = $statusColors[$payment->status] ?? 'secondary';
                                            $icon = $statusIcons[$payment->status] ?? 'circle';
                                        @endphp
                                        <span class="badge bg-{{ $color }} rounded-pill px-3 py-2">
                                            <i class="fas fa-{{ $icon }} me-1"></i>
                                            {{ ucfirst($payment->status) }}
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
                                <tr>
                                    <td class="text-muted"><strong>Recorded On:</strong></td>
                                    <td>{{ $payment->created_at ? $payment->created_at->format('F d, Y H:i') : 'N/A' }}</td>
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
                </div>
            </div>

            <!-- Deposit Slip -->
            @if($payment->deposit_slip_path)
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-file-image text-info me-2"></i>Deposit Slip / Proof of Payment
                    </h5>
                </div>
                <div class="card-body text-center">
                    @php
                        $extension = pathinfo($payment->deposit_slip_path, PATHINFO_EXTENSION);
                    @endphp
                    @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <img src="{{ Storage::disk('public')->url($payment->deposit_slip_path) }}"
                             alt="Deposit Slip"
                             class="img-fluid rounded border"
                             style="max-height: 400px;">
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                            <p>PDF Document</p>
                            <a href="{{ Storage::disk('public')->url($payment->deposit_slip_path) }}"
                               class="btn btn-danger" target="_blank">
                                <i class="fas fa-file-pdf me-2"></i>View PDF Document
                            </a>
                        </div>
                    @endif
                    <div class="mt-3">
                        <a href="{{ Storage::disk('public')->url($payment->deposit_slip_path) }}"
                           class="btn btn-sm btn-outline-primary" download>
                            <i class="fas fa-download me-2"></i>Download Document
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Timeline -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-chart-line text-kp-blue me-2"></i>Payment Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Payment Recorded -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Payment Recorded</h6>
                                <p class="text-muted small mb-0">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ $payment->created_at ? $payment->created_at->format('F d, Y H:i') : 'N/A' }}
                                </p>
                                @if($payment->creator)
                                    <p class="text-muted small mb-0">By: {{ $payment->creator->name }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Validation (if applicable) -->
                        @if($payment->validated_at || $payment->status === 'validated' || $payment->status === 'rejected')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-{{ $payment->status === 'validated' ? 'success' : ($payment->status === 'rejected' ? 'danger' : 'warning') }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">
                                    @if($payment->status === 'validated')
                                        Payment Validated
                                    @elseif($payment->status === 'rejected')
                                        Payment Rejected
                                    @else
                                        Payment Processed
                                    @endif
                                </h6>
                                @if($payment->validated_at)
                                    <p class="text-muted small mb-0">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ \Carbon\Carbon::parse($payment->validated_at)->format('F d, Y H:i') }}
                                    </p>
                                @endif
                                @if($payment->validator)
                                    <p class="text-muted small mb-0">By: {{ $payment->validator->name }}</p>
                                @endif
                                @if($payment->validation_notes)
                                    <p class="text-muted small mt-1 mb-0">
                                        <i class="fas fa-comment me-1"></i>
                                        {{ Str::limit($payment->validation_notes, 100) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Completion (if applicable) -->
                        @if($payment->status === 'validated')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1 text-success">Payment Completed</h6>
                                <p class="text-muted small mb-0">
                                    Your payment has been successfully applied to your account.
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Invoice -->
            @if($payment->billing)
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-file-invoice text-kp-blue me-2"></i>Related Invoice
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>Invoice Number:</strong> {{ $payment->billing->billing_number }}</p>
                    <p><strong>Invoice Date:</strong> {{ $payment->billing->billing_date ? $payment->billing->billing_date->format('F d, Y') : 'N/A' }}</p>
                    <p><strong>Due Date:</strong>
                        <span class="{{ $payment->billing->due_date && $payment->billing->due_date < now() ? 'text-danger' : '' }}">
                            {{ $payment->billing->due_date ? $payment->billing->due_date->format('F d, Y') : 'N/A' }}
                        </span>
                    </p>
                    <p><strong>Total Amount:</strong> {{ $payment->billing->currency }} {{ number_format($payment->billing->total_amount, 2) }}</p>
                    <p><strong>Paid Amount:</strong> <span class="text-success">{{ $payment->billing->currency }} {{ number_format($payment->billing->paid_amount ?? 0, 2) }}</span></p>
                    <p><strong>Balance:</strong>
                        <span class="{{ ($payment->billing->total_amount - ($payment->billing->paid_amount ?? 0)) > 0 ? 'text-danger' : 'text-success' }}">
                            {{ $payment->billing->currency }} {{ number_format($payment->billing->total_amount - ($payment->billing->paid_amount ?? 0), 2) }}
                        </span>
                    </p>
                    <a href="{{ route('customer.invoices.show', $payment->billing) }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="fas fa-eye me-2"></i>View Invoice Details
                    </a>
                </div>
            </div>
            @endif

            <!-- Need Help -->
            <div class="card shadow">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-life-ring me-2"></i>Need Help?
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">If you have any questions about this payment, please contact our support team.</p>
                    <div class="d-grid gap-2">
                        <a href="tel:+254711111111" class="btn btn-outline-primary">
                            <i class="fas fa-phone me-2"></i>Call Support
                        </a>
                        <a href="mailto:support@kplc.co.ke" class="btn btn-outline-info">
                            <i class="fas fa-envelope me-2"></i>Email Support
                        </a>
                        <a href="{{ route('customer.tickets.create') }}" class="btn btn-outline-warning">
                            <i class="fas fa-ticket-alt me-2"></i>Create Support Ticket
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 25px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    top: 4px;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e3e6f0;
}

.timeline-content {
    padding-bottom: 10px;
    border-bottom: 1px solid #e3e6f0;
}

.timeline-item:last-child .timeline-content {
    border-bottom: none;
}

.timeline-marker.bg-success {
    background-color: #009639;
}

.timeline-marker.bg-warning {
    background-color: #FFD700;
}

.timeline-marker.bg-danger {
    background-color: #dc3545;
}

.timeline-marker.bg-info {
    background-color: #17a2b8;
}

@media (max-width: 768px) {
    .timeline {
        padding-left: 20px;
    }
    .timeline-marker {
        left: -20px;
        width: 12px;
        height: 12px;
    }
}
</style>
@endsection
