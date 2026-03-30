@php
use App\Models\Invoice;
@endphp

@extends('layouts.app')

@section('title', 'Invoice #' . $invoice->invoice_number)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Invoice #{{ $invoice->invoice_number }}
                </h1>
                <div class="btn-group">
                    <a href="{{ route('finance.invoices.download', $invoice->id) }}" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Download PDF
                    </a>
                    <a href="{{ route('finance.invoices') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Invoices
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Alert -->
    @if($invoice->status === 'paid')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Payment Received!</strong> This invoice was paid on {{ $invoice->paid_date?->format('F j, Y') }}.
    </div>
    @elseif($invoice->status === 'overdue')
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Overdue!</strong> This invoice was due on {{ $invoice->due_date->format('F j, Y') }}.
    </div>
    @elseif($invoice->isOverdue())
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-clock me-2"></i>
        <strong>Due Soon!</strong> This invoice is due on {{ $invoice->due_date->format('F j, Y') }}.
    </div>
    @endif

    <div class="row">
        <!-- Main Invoice Details -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-receipt me-2"></i>Invoice Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Invoice Number:</th>
                                    <td>{{ $invoice->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <th>Invoice Date:</th>
                                    <td>{{ $invoice->invoice_date->format('F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Due Date:</th>
                                    <td>
                                        <span class="{{ $invoice->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                            {{ $invoice->due_date->format('F j, Y') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Currency:</th>
                                    <td>{{ $invoice->currency }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Terms:</th>
                                    <td>{{ $invoice->payment_terms ?? 'Net 30' }}</td>
                                </tr>
                                @if($invoice->paid_date)
                                <tr>
                                    <th>Paid Date:</th>
                                    <td>{{ $invoice->paid_date->format('F j, Y') }}</td>
                                </tr>
                                @endif
                                @if($invoice->lease)
                                <tr>
                                    <th>Related Lease:</th>
                                    <td>{{ $invoice->lease->title }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($invoice->description)
                    <div class="mt-3">
                        <h6>Description:</h6>
                        <p class="text-muted">{{ $invoice->description }}</p>
                    </div>
                    @endif

                    <!-- Notes -->
                    @if($invoice->notes)
                    <div class="mt-3">
                        <h6>Notes:</h6>
                        <p class="text-muted">{{ $invoice->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Line Items -->
            @if($invoice->lineItems && count($invoice->lineItems) > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Line Items
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th width="120" class="text-end">Quantity</th>
                                    <th width="120" class="text-end">Unit Price</th>
                                    <th width="120" class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->lineItems as $item)
                                <tr>
                                    <td>{{ $item->description }}</td>
                                    <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="text-end">{{ $invoice->currency }} {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">{{ $invoice->currency }} {{ number_format($item->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Payment Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Payment Summary
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Subtotal:</th>
                            <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Tax Amount:</th>
                            <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->tax_amount, 2) }}</td>
                        </tr>
                        <tr class="border-top">
                            <th><strong>Total Amount:</strong></th>
                            <td class="text-end"><strong>{{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}</strong></td>
                        </tr>
                        @if($invoice->status === 'paid')
                        <tr class="border-top">
                            <th>Amount Paid:</th>
                            <td class="text-end text-success">{{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Balance:</th>
                            <td class="text-end">{{ $invoice->currency }} 0.00</td>
                        </tr>
                        @else
                        <tr class="border-top">
                            <th>Balance Due:</th>
                            <td class="text-end text-danger fw-bold">{{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                        @endif
                    </table>

                    @if($invoice->status !== 'paid')
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#paymentModal">
                            <i class="fas fa-credit-card me-2"></i>Mark as Paid
                        </button>
                        <a href="{{ route('finance.invoices.download', $invoice->id) }}"
                           class="btn btn-outline-primary">
                            <i class="fas fa-download me-2"></i>Download Invoice
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment History -->
            @if($invoice->payments && count($invoice->payments) > 0)
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Payment History
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($invoice->payments as $payment)
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <div>
                            <h6 class="mb-0">{{ $payment->payment_method }}</h6>
                            <small class="text-muted">{{ $payment->payment_date->format('M j, Y') }}</small>
                        </div>
                        <div class="text-end">
                            <strong class="text-success">{{ $invoice->currency }} {{ number_format($payment->amount, 2) }}</strong>
                            <br>
                            <small class="badge bg-success">{{ ucfirst($payment->status) }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Finance Actions -->
            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i>Finance Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($invoice->status !== 'paid')
                        <button class="btn btn-success" onclick="markAsPaid({{ $invoice->id }})">
                            <i class="fas fa-check me-2"></i>Mark as Paid
                        </button>
                        @endif
                        <button class="btn btn-warning" onclick="sendReminder({{ $invoice->id }})">
                            <i class="fas fa-envelope me-2"></i>Send Reminder
                        </button>
                        <a href="{{ route('finance.invoices.edit', $invoice->id) }}" class="btn btn-info">
                            <i class="fas fa-edit me-2"></i>Edit Invoice
                        </a>
                        <button class="btn btn-danger" onclick="deleteInvoice({{ $invoice->id }})">
                            <i class="fas fa-trash me-2"></i>Delete Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Paid Modal -->
@if($invoice->status !== 'paid')
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Mark Invoice as Paid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Invoice #{{ $invoice->invoice_number }}</p>
                <p class="h4 text-center text-success mb-4">
                    Total Amount: {{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}
                </p>

                <form id="paymentForm">
                    @csrf
                    <div class="mb-3">
                        <label for="paymentDate" class="form-label">Payment Date</label>
                        <input type="date" class="form-control" id="paymentDate" name="payment_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Payment Method</label>
                        <select class="form-select" id="paymentMethod" name="payment_method" required>
                            <option value="">Select payment method...</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="cash">Cash</option>
                            <option value="check">Check</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="paymentNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="paymentNotes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="processPayment({{ $invoice->id }})">
                    <i class="fas fa-check me-2"></i>Mark as Paid
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
function markAsPaid(invoiceId) {
    $('#paymentModal').modal('show');
}

function processPayment(invoiceId) {
    const paymentDate = document.getElementById('paymentDate').value;
    const paymentMethod = document.getElementById('paymentMethod').value;

    if (!paymentDate || !paymentMethod) {
        alert('Please fill in all required fields');
        return;
    }

    // Show loading state
    const btn = document.querySelector('#paymentModal .btn-success');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    btn.disabled = true;

    fetch(`/finance/invoices/${invoiceId}/mark-paid`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            payment_date: paymentDate,
            payment_method: paymentMethod,
            notes: document.getElementById('paymentNotes').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing the payment.');
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function sendReminder(invoiceId) {
    if (confirm('Send payment reminder to customer?')) {
        fetch(`/finance/invoices/${invoiceId}/send-reminder`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Reminder sent successfully!');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while sending the reminder.');
        });
    }
}

function deleteInvoice(invoiceId) {
    if (confirm('Are you sure you want to delete this invoice? This action cannot be undone.')) {
        fetch(`/finance/invoices/${invoiceId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("finance.invoices") }}';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the invoice.');
        });
    }
}
</script>
@endsection
