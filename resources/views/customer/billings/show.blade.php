@extends('layouts.app')

@section('title', 'Billing Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-invoice-dollar me-2"></i>Billing Details
        </h1>
        <div>
            <a href="{{ route('customer.billings.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Billings
            </a>
            <a href="{{ route('customer.billings.download', $billing->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-download me-2"></i>Download PDF
            </a>
            <a href="{{ route('customer.billings.preview', $billing->id) }}" class="btn btn-sm btn-info" target="_blank">
                <i class="fas fa-file-pdf me-2"></i>Preview
            </a>
        </div>
    </div>

    <!-- Company Header -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary">Kenya Power & Lighting Co. Ltd</h5>
                    <p class="mb-1">Dark Fibre Services Division</p>
                    <p class="mb-1">P.O. Box 30099 - 00100</p>
                    <p class="mb-1">Nairobi, Kenya</p>
                    <p class="mb-1">Tel: +254 20 320 1000</p>
                </div>
                <div class="col-md-6 text-end">
                    <h2 class="text-primary">INVOICE</h2>
                    <h4 class="mb-0">{{ $billing->billing_number }}</h4>
                    <p class="text-muted mb-1">Consolidated Billing</p>

                    <div class="mt-3">
                        <p class="mb-1"><strong>Invoice Date:</strong> {{ \Carbon\Carbon::parse($billing->billing_date)->format('M d, Y') }}</p>
                        <p class="mb-1"><strong>Due Date:</strong>
                            <span class="{{ $billing->status === 'pending' && $billing->due_date < now() ? 'text-danger fw-bold' : '' }}">
                                {{ \Carbon\Carbon::parse($billing->due_date)->format('M d, Y') }}
                            </span>
                        </p>
                        <p class="mb-0"><strong>Status:</strong>
                            @php
                                $statusClass = match($billing->status) {
                                    'paid' => 'success',
                                    'pending' => 'warning',
                                    'overdue' => 'danger',
                                    'cancelled' => 'secondary',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }} ms-2">{{ ucfirst($billing->status) }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="card shadow mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-user me-2"></i>Bill To</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>{{ $billing->user->name }}</strong></p>
                    <p class="mb-1">{{ $billing->user->email }}</p>
                    @if($billing->user->phone)
                        <p class="mb-0">Tel: {{ $billing->user->phone }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Customer ID:</strong> {{ $billing->user->id }}</p>
                    @if($billing->kra_pin)
                        <p class="mb-1"><strong>KRA PIN:</strong> {{ $billing->kra_pin }}</p>
                    @endif
                    @php
                        $periodStart = $billing->lineItems->min('period_start');
                        $periodEnd = $billing->lineItems->max('period_end');
                    @endphp
                    @if($periodStart && $periodEnd)
                        <p class="mb-0"><strong>Period:</strong>
                            {{ \Carbon\Carbon::parse($periodStart)->format('M d, Y') }} -
                            {{ \Carbon\Carbon::parse($periodEnd)->format('M d, Y') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Line Items -->
    <div class="card shadow mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-list me-2"></i>Line Items</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Lease Number</th>
                            <th>Description</th>
                            <th>Period</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($billing->lineItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $item->lease->lease_number ?? 'N/A' }}</strong>
                                </td>
                                <td>{{ $item->description ?? 'Dark Fibre Service' }}</td>
                                <td>
                                    @if($item->period_start && $item->period_end)
                                        {{ \Carbon\Carbon::parse($item->period_start)->format('d/m/Y') }} -
                                        {{ \Carbon\Carbon::parse($item->period_end)->format('d/m/Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($item->amount, 2) }} {{ $item->currency ?? $billing->currency }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">Subtotal:</th>
                            <th class="text-end">{{ number_format($billing->total_amount, 2) }} {{ $billing->currency }}</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">VAT (16%):</th>
                            <th class="text-end">{{ number_format($billing->total_amount * 0.16, 2) }} {{ $billing->currency }}</th>
                        </tr>
                        <tr class="table-active">
                            <th colspan="4" class="text-end">Total Amount Due:</th>
                            <th class="text-end">{{ number_format($billing->total_amount * 1.16, 2) }} {{ $billing->currency }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- KRA/TEVIN Information -->
    @if($billing->tevin_control_code || $billing->tevin_qr_code || $billing->kra_qr_code)
    <div class="card shadow mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-check-circle text-success me-2"></i>KRA Validation</h6>
        </div>
        <div class="card-body">
            <div class="row">
                @if($billing->tevin_control_code)
                <div class="col-md-6">
                    <p><strong>Control Code:</strong> {{ $billing->tevin_control_code }}</p>
                </div>
                @endif
                @if($billing->tevin_invoice_number)
                <div class="col-md-6">
                    <p><strong>Invoice Number:</strong> {{ $billing->tevin_invoice_number }}</p>
                </div>
                @endif
                @if($billing->tevin_submitted_at)
                <div class="col-md-6">
                    <p><strong>Submitted:</strong> {{ \Carbon\Carbon::parse($billing->tevin_submitted_at)->format('d M Y H:i') }}</p>
                </div>
                @endif
                @if($billing->kra_pin)
                <div class="col-md-6">
                    <p><strong>KRA PIN:</strong> {{ $billing->kra_pin }}</p>
                </div>
                @endif
            </div>

            @if($billing->tevin_qr_code || $billing->kra_qr_code)
            <div class="row mt-3">
                <div class="col-12">
                    <h6 class="font-weight-bold">QR Code Verification</h6>
                    @php
                        $qrCode = $billing->tevin_qr_code ?? $billing->kra_qr_code;
                    @endphp
                    <a href="{{ $qrCode }}" target="_blank" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-qrcode me-2"></i>Verify with KRA
                    </a>
                    <small class="text-muted ms-2">Click to verify this invoice on KRA portal</small>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Payment Section -->
    @if($billing->status === 'pending')
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Make Payment</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Total Amount Due:</h5>
                    <h3 class="text-success">{{ $billing->currency }} {{ number_format($billing->total_amount * 1.16, 2) }}</h3>
                    <p class="text-muted">(Including 16% VAT)</p>
                </div>
                <div class="col-md-6 text-end">
                    <form action="{{ route('customer.billings.pay', $billing->id) }}" method="POST" class="d-inline">
                        @csrf
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                <option value="">Select method</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-credit-card me-2"></i>Pay Now
                        </button>
                    </form>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-12">
                    <h6>Payment Instructions:</h6>
                    <ul class="text-muted">
                        <li><strong>M-Pesa Paybill:</strong> 123456, Account: {{ $billing->billing_number }}</li>
                        <li><strong>Bank Transfer:</strong> KCB Bank, Account: 1100123456789, Branch: Kipande House</li>
                        <li>Please include your billing number as reference</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @elseif($billing->status === 'paid')
    <div class="alert alert-success">
        <i class="fas fa-check-circle me-2"></i>
        This invoice has been paid. Thank you for your payment!
    </div>
    @endif
</div>
@endsection
