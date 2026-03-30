{{-- resources/views/finance/debt/invoice-details.blade.php --}}
@extends('layouts.app')

@section('title', 'Invoice Details - #' . ($invoice->billing_number ?? $invoice->id))

@section('content')
<div class="container-fluid">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('finance.debt.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <!-- Invoice Header -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Invoice Details</h4>
                <div class="btn-group">
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Invoice Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Invoice Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Invoice #:</th>
                            <td><strong>#{{ $invoice->billing_number ?? 'CONS-' . $invoice->id }}</strong></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @php
                                    $statusColor = 'warning';
                                    if ($invoice->status == 'overdue') $statusColor = 'danger';
                                    if ($invoice->status == 'paid') $statusColor = 'success';
                                    if ($invoice->status == 'partial') $statusColor = 'info';
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Created Date:</th>
                            <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <th>Due Date:</th>
                            <td>
                                {{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : 'N/A' }}
                                @if($invoice->due_date && $invoice->due_date->isPast())
                                    <span class="badge bg-danger ms-2">
                                        Overdue by {{ now()->diffInDays($invoice->due_date) }} days
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Total Amount:</th>
                            <td class="h5 text-danger">${{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Paid Amount:</th>
                            <td>${{ number_format($invoice->paid_amount ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Balance:</th>
                            <td class="h6">
                                @php
                                    $balance = $invoice->total_amount - ($invoice->paid_amount ?? 0);
                                @endphp
                                @if($balance > 0)
                                    <span class="text-danger">${{ number_format($balance, 2) }}</span>
                                @else
                                    <span class="text-success">$0.00</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Customer Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Customer:</th>
                            <td>
                                @if($invoice->user)
                                    {{ $invoice->user->name }}
                                @elseif($invoice->User)
                                    {{ $invoice->User->name }}
                                @else
                                    Unknown
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>
                                @if($invoice->user)
                                    {{ $invoice->user->email }}
                                @elseif($invoice->User)
                                    {{ $invoice->User->email }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Company:</th>
                            <td>
                                @if($invoice->user && $invoice->user->company)
                                    {{ $invoice->user->company }}
                                @elseif($invoice->User && $invoice->User->company)
                                    {{ $invoice->User->company }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>
                                @if($invoice->user && $invoice->user->phone)
                                    {{ $invoice->user->phone }}
                                @elseif($invoice->User && $invoice->User->phone)
                                    {{ $invoice->User->phone }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Line Items -->
            <div class="row mb-4">
                <div class="col-12">
                    <h6>Billing Line Items</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Billing Cycle</th>
                                    <th>Period</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($invoice->billingLineItems && $invoice->billingLineItems->count() > 0)
                                    @foreach($invoice->billingLineItems as $item)
                                    <tr>
                                        <td>{{ $item->description }}</td>
                                        <td>
                                            <span class="badge bg-info text-capitalize">
                                                {{ $item->billing_cycle }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($item->period_start)->format('M d, Y') }}
                                            to
                                            {{ \Carbon\Carbon::parse($item->period_end)->format('M d, Y') }}
                                        </td>
                                        <td>${{ number_format($item->amount, 2) }}</td>
                                        <td>${{ number_format($item->paid_amount, 2) }}</td>
                                        <td>
                                            @php
                                                $itemBalance = $item->amount - $item->paid_amount;
                                            @endphp
                                            @if($itemBalance > 0)
                                                <span class="text-danger">${{ number_format($itemBalance, 2) }}</span>
                                            @else
                                                <span class="text-success">$0.00</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No line items found</td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="3" class="text-end"><strong>Totals:</strong></td>
                                    <td><strong>${{ number_format($invoice->total_amount, 2) }}</strong></td>
                                    <td><strong>${{ number_format($invoice->paid_amount ?? 0, 2) }}</strong></td>
                                    <td>
                                        @php
                                            $totalBalance = $invoice->total_amount - ($invoice->paid_amount ?? 0);
                                        @endphp
                                        <strong class="{{ $totalBalance > 0 ? 'text-danger' : 'text-success' }}">
                                            ${{ number_format($totalBalance, 2) }}
                                        </strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="row mb-4">
                <div class="col-12">
                    <h6>Payment History</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Reference</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($invoice->payments && $invoice->payments->count() > 0)
                                    @foreach($invoice->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                        <td>${{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ ucfirst($payment->payment_method) }}</td>
                                        <td>{{ $payment->reference }}</td>
                                        <td>
                                            <span class="badge bg-success">Paid</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No payments recorded</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <form action="{{ route('finance.debt.invoice.send-reminder', $invoice->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning" onclick="return confirm('Send reminder to customer?')">
                                <i class="fas fa-envelope me-2"></i>Send Reminder
                            </button>
                        </form>

                        <form action="{{ route('finance.debt.invoice.create-payment-plan', $invoice->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="installment_count" value="3">
                            <input type="hidden" name="start_date" value="{{ now()->format('Y-m-d') }}">
                            <button type="submit" class="btn btn-info" onclick="return confirm('Create payment plan for this invoice?')">
                                <i class="fas fa-calendar-alt me-2"></i>Payment Plan
                            </button>
                        </form>

                        <a href="{{ route('finance.debt.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Close
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
