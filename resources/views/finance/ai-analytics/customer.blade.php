@extends('layouts.app')

@section('title', 'Customer Intelligence - ' . ($customer->name ?? 'Unknown'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="page-title mb-0">
                            <i class="fas fa-user-circle text-primary me-2"></i>Customer Intelligence
                        </h4>
                        <p class="text-muted mb-0">Detailed analysis and insights for {{ $customer->name ?? 'Unknown Customer' }}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('finance.ai.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-primary">
                            <i class="fas fa-print me-1"></i> Print Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Profile -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-primary rounded-circle text-white display-4">
                            {{ substr($customer->name ?? 'U', 0, 1) }}
                        </div>
                    </div>
                    <h4 class="mb-1">{{ $customer->name ?? 'Unknown' }}</h4>
                    <p class="text-muted mb-2">
                        <i class="fas fa-envelope me-1"></i> {{ $customer->email ?? 'No email' }}
                    </p>
                    <p class="text-muted mb-3">
                        <i class="fas fa-calendar me-1"></i> Customer since: {{ $customer->created_at ? $customer->created_at->format('M d, Y') : 'N/A' }}
                    </p>
                    <div class="d-flex justify-content-around">
                        <div>
                            <h5 class="mb-0 text-danger">{{ $outstandingUsd ?? 0 }} overdue</h5>
                            <small class="text-muted">Overdue Invoices</small>
                        </div>
                        <div>
                            <h5 class="mb-0 text-primary">{{ number_format(($outstandingUsd ?? 0) + ($outstandingKsh ?? 0) / 130, 0) }}</h5>
                            <small class="text-muted">Risk Score</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Financial Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block">Outstanding (USD)</small>
                                <h3 class="mb-0 text-primary">${{ number_format($outstandingUsd ?? 0, 2) }}</h3>
                                <small class="text-muted">Total unpaid amount in USD</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block">Outstanding (KSH)</small>
                                <h3 class="mb-0 text-success">KSH {{ number_format($outstandingKsh ?? 0, 2) }}</h3>
                                <small class="text-muted">Total unpaid amount in KSH</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block">Total Paid (All Time)</small>
                                <h3 class="mb-0 text-success">${{ number_format($customer->total_paid_usd ?? 0, 2) }}</h3>
                                <small class="text-muted">Lifetime payments in USD</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block">Payment Reliability</small>
                                <h3 class="mb-0 text-{{ ($customer->payment_reliability ?? 0) > 80 ? 'success' : (($customer->payment_reliability ?? 0) > 50 ? 'warning' : 'danger') }}">
                                    {{ number_format($customer->payment_reliability ?? 0, 1) }}%
                                </h3>
                                <small class="text-muted">On-time payment rate</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Outstanding Invoices -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-file-invoice text-warning me-2"></i>Outstanding Invoices
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Currency</th>
                                    <th>Status</th>
                                    <th>Days Overdue</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices ?? [] as $invoice)
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($invoice->due_date);
                                        $today = \Carbon\Carbon::now();
                                        $daysOverdue = $dueDate->lt($today) ? $dueDate->diffInDays($today) : 0;
                                        $outstanding = $invoice->total_amount - ($invoice->paid_amount ?? 0);
                                        $badgeClass = $daysOverdue == 0 ? 'success' : ($daysOverdue <= 30 ? 'warning' : 'danger');
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('finance.billing.show', $invoice->id) }}" class="text-primary">
                                                {{ $invoice->billing_number }}
                                            </a>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($invoice->billing_date)->format('M d, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</td>
                                        <td class="fw-bold">
                                            @if($invoice->currency == 'USD')
                                                ${{ number_format($outstanding, 2) }}
                                            @else
                                                KSH {{ number_format($outstanding, 2) }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $invoice->currency == 'USD' ? 'primary' : 'success' }}">
                                                {{ $invoice->currency }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $badgeClass }}">
                                                {{ $daysOverdue == 0 ? 'Current' : ($daysOverdue <= 30 ? '1-30 Days' : ($daysOverdue <= 60 ? '31-60 Days' : 'Over 60 Days')) }}
                                            </span>
                                        </td>
                                        <td class="text-{{ $daysOverdue > 0 ? 'danger' : 'success' }}">
                                            @if($daysOverdue > 0)
                                                {{ $daysOverdue }} days overdue
                                            @else
                                                Current
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="sendReminder({{ $invoice->id }})">
                                                <i class="fas fa-bell"></i> Remind
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                                            No outstanding invoices for this customer.
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

    <!-- Payment History -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-history text-info me-2"></i>Recent Payment History
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice #</th>
                                    <th>Amount</th>
                                    <th>Currency</th>
                                    <th>Method</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentHistory ?? [] as $payment)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($payment->transaction_date)->format('M d, Y') }}</td>
                                        <td>{{ $payment->billing_number ?? 'N/A' }}</td>
                                        <td class="fw-bold text-success">
                                            @if($payment->currency == 'USD')
                                                ${{ number_format($payment->amount, 2) }}
                                            @else
                                                KSH {{ number_format($payment->amount, 2) }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $payment->currency == 'USD' ? 'primary' : 'success' }}">
                                                {{ $payment->currency }}
                                            </span>
                                        </td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'N/A')) }}</td>
                                        <td><small class="text-muted">{{ $payment->reference_number ?? 'N/A' }}</small></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No payment history available.
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

    <!-- AI Insights for Customer -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-brain text-warning me-2"></i>AI-Powered Insights
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-primary">
                                    <i class="fas fa-chart-line me-2"></i>Payment Pattern
                                </h6>
                                <p class="mb-0">
                                    @php
                                        $paymentCount = count($paymentHistory ?? []);
                                        $avgPayment = $paymentCount > 0 ? (($outstandingUsd ?? 0) + ($outstandingKsh ?? 0) / 130) / $paymentCount : 0;
                                    @endphp
                                    This customer has made <strong>{{ $paymentCount }}</strong> payments averaging
                                    <strong>${{ number_format($avgPayment, 2) }}</strong> per transaction.
                                </p>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Risk Assessment
                                </h6>
                                <p class="mb-0">
                                    @if(($outstandingUsd ?? 0) > 50000 || ($outstandingKsh ?? 0) > 6500000)
                                        <span class="text-danger">High risk customer</span> - Large outstanding balance requires immediate attention.
                                    @elseif(($outstandingUsd ?? 0) > 10000 || ($outstandingKsh ?? 0) > 1300000)
                                        <span class="text-warning">Medium risk customer</span> - Monitor payment patterns closely.
                                    @else
                                        <span class="text-success">Low risk customer</span> - Consistent payment behavior observed.
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-success">
                                    <i class="fas fa-lightbulb me-2"></i>Recommendations
                                </h6>
                                <ul class="mb-0">
                                    @if(($outstandingUsd ?? 0) > 10000)
                                        <li>Consider offering a payment plan for the outstanding balance</li>
                                    @endif
                                    @if(($invoices->where('status', 'overdue')->count() ?? 0) > 2)
                                        <li>Schedule automated payment reminders for overdue invoices</li>
                                    @endif
                                    @if(($customer->payment_reliability ?? 0) < 70)
                                        <li>Review credit terms and consider reducing credit limit</li>
                                    @else
                                        <li>Maintain current credit terms based on good payment history</li>
                                    @endif
                                    <li>Send monthly statements to keep customer informed</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sendReminder(invoiceId) {
    if (confirm('Send payment reminder for this invoice?')) {
        fetch(`/finance/billing/${invoiceId}/remind`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Reminder sent successfully!');
            } else {
                alert('Failed to send reminder: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error sending reminder');
        });
    }
}
</script>
@endsection
