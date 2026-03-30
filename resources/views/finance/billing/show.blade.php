@extends('layouts.app')

@section('title', 'Invoice Details - Kenya Power Dark Fibre')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice text-primary me-2"></i>
                Invoice Details
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('finance.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('finance.billing.index') }}">Billings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $billing->billing_number }}</li>
                </ol>
            </nav>
        </div>
        <div>
    <a href="{{ route('finance.billing.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Billings
    </a>
    <a href="{{ route('finance.billing.download', $billing->id) }}"
       class="btn btn-outline-primary ms-2">
        <i class="fas fa-download me-2"></i>Download PDF
    </a>
    <a href="{{ route('finance.billing.preview', $billing->id) }}"
       class="btn btn-outline-info ms-2" target="_blank">
        <i class="fas fa-eye me-2"></i>Preview PDF
    </a>
    <button onclick="window.print()" class="btn btn-outline-secondary ms-2">
        <i class="fas fa-print me-2"></i>Print
    </button>
</div>
    </div>

    <!-- Invoice Header -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary">Kenya Power & Lighting Co. Ltd</h5>
                    <p class="mb-1">Dark Fibre Services Division</p>
                    <p class="mb-1">P.O. Box 30099 - 00100</p>
                    <p class="mb-1">Nairobi, Kenya</p>
                    <p class="mb-1">Tel: +254 20 320 1000</p>
                    <p class="mb-0">Email: darkfibre@kplc.co.ke</p>
                </div>
                <div class="col-md-6 text-end">
                    <h2 class="text-primary">INVOICE</h2>
                    <h4 class="mb-0">{{ $billing->billing_number }}</h4>
                    <p class="text-muted mb-1">Consolidated Billing</p>

                    <div class="mt-3">
                        <p class="mb-1"><strong>Invoice Date:</strong> {{ $billing->billing_date->format('M d, Y') }}</p>
                        <p class="mb-1"><strong>Due Date:</strong>
                            <span class="{{ $billing->due_date < now() && $billing->status !== 'paid' ? 'text-danger fw-bold' : '' }}">
                                {{ $billing->due_date->format('M d, Y') }}
                            </span>
                        </p>
                        <p class="mb-0"><strong>Status:</strong>
                            <span class="badge bg-{{ $billing->status === 'paid' ? 'success' : ($billing->due_date < now() ? 'danger' : 'warning') }}">
                                {{ ucfirst($billing->status) }}
                            </span>
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
                    @if($billing->user->company_name)
                        <p class="mb-1">{{ $billing->user->company_name }}</p>
                    @endif
                    <p class="mb-1">{{ $billing->user->email }}</p>
                    @if($billing->user->phone)
                        <p class="mb-0">Tel: {{ $billing->user->phone }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Account Type:</strong>
                        <span class="badge bg-info">{{ ucfirst($billing->user->role) }}</span>
                    </p>
                    <p class="mb-1"><strong>Customer ID:</strong> {{ $billing->user->id }}</p>
                    <p class="mb-0"><strong>Invoice Period:</strong>
                        @php
                            $periodStart = $billing->lineItems->min('period_start');
                            $periodEnd = $billing->lineItems->max('period_end');
                        @endphp
                        @if($periodStart && $periodEnd)
                            {{ $periodStart->format('M d, Y') }} to {{ $periodEnd->format('M d, Y') }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Line Items -->
    <div class="card shadow mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-list me-2"></i>Lease Details</h6>
            <span class="badge bg-secondary">{{ $billing->lineItems->count() }} lease(s)</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Lease Number</th>
                            <th>Service Type</th>
                            <th>Billing Cycle</th>
                            <th>Period</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($billing->lineItems as $index => $lineItem)
                            @php
                                $lease = $lineItem->lease;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $lease->lease_number ?? 'N/A' }}</strong>
                                    @if($lease->title)
                                        <br><small class="text-muted">{{ $lease->title }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($lease)
                                        <span class="badge bg-info">
                                            {{ str_replace('_', ' ', ucfirst($lease->service_type)) }}
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $lineItem->billing_cycle === 'monthly' ? 'primary' : ($lineItem->billing_cycle === 'quarterly' ? 'warning' : 'success') }}">
                                        {{ ucfirst($lineItem->billing_cycle) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $lineItem->period_start->format('M d, Y') }}<br>
                                    to<br>
                                    {{ $lineItem->period_end->format('M d, Y') }}
                                </td>
                                <td class="text-end fw-bold">
                                    {{ $lineItem->currency }} {{ number_format($lineItem->amount, 2) }}
                                </td>
                            </tr>
                            @if($lineItem->description)
                                <tr class="table-light">
                                    <td colspan="6" class="small text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        {{ $lineItem->description }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end"><strong>Subtotal:</strong></td>
                            <td class="text-end">
                                <strong>{{ $billing->currency }} {{ number_format($billing->total_amount, 2) }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end"><strong>VAT (16%):</strong></td>
                            <td class="text-end">
                                {{ $billing->currency }} {{ number_format($billing->total_amount * 0.16, 2) }}
                            </td>
                        </tr>
                        <tr class="table-active">
                            <td colspan="5" class="text-end"><h5 class="mb-0">Total Amount Due:</h5></td>
                            <td class="text-end">
                                <h5 class="mb-0 text-primary">
                                    {{ $billing->currency }} {{ number_format($billing->total_amount * 1.16, 2) }}
                                </h5>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Instructions -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Instructions</h6>
                </div>
                <div class="card-body">
                    <h6>Bank Transfer</h6>
                    <p class="mb-1"><strong>Bank:</strong> Kenya Commercial Bank</p>
                    <p class="mb-1"><strong>Account Name:</strong> Kenya Power & Lighting Co. Ltd</p>
                    <p class="mb-1"><strong>Account Number:</strong> 1100 1234 5678</p>
                    <p class="mb-1"><strong>Branch:</strong> Kipande House</p>
                    <p class="mb-0"><strong>Swift Code:</strong> KCBLKENXXXX</p>

                    <hr>

                    <h6>M-Pesa</h6>
                    <ol>
                        <li>Go to M-Pesa menu on your phone</li>
                        <li>Select "Lipa Na M-Pesa"</li>
                        <li>Select "Pay Bill"</li>
                        <li>Enter Business Number: <strong>123456</strong></li>
                        <li>Enter Account Number: <strong>{{ $billing->billing_number }}</strong></li>
                        <li>Enter Amount: <strong>{{ $billing->total_amount * 1.16 }}</strong></li>
                        <li>Enter your M-Pesa PIN</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Terms & Notes</h6>
                </div>
                <div class="card-body">
                    <p><strong>Payment Terms:</strong> Payment due within 7 days of invoice date.</p>
                    <p><strong>Late Payment:</strong> A penalty of 2% per month will be applied to overdue amounts.</p>
                    <p><strong>Disputes:</strong> Any billing disputes must be raised within 7 days of invoice receipt.</p>
                    <p><strong>Contact:</strong> For billing inquiries, email billing@kplc.co.ke or call +254 20 320 2000.</p>

                    <hr>

                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-lightbulb me-1"></i>
                            This is a consolidated invoice covering multiple leases.
                            Payment of this invoice will update the status of all included leases.
                        </small>
                    </div>

                    @if($billing->status !== 'paid')
                        <div class="text-center mt-3">
                            <button class="btn btn-success btn-lg"
                                    data-bs-toggle="modal"
                                    data-bs-target="#paymentModal">
                                <i class="fas fa-credit-card me-2"></i>
                                Pay Invoice Now
                            </button>
                        </div>
                    @else
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle me-2"></i>
                            This invoice was paid on {{ $billing->updated_at->format('M d, Y') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
@if($billing->status !== 'paid')
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Invoice #:</strong> {{ $billing->billing_number }}</p>
                <p><strong>Total Amount:</strong> {{ $billing->currency }} {{ number_format($billing->total_amount * 1.16, 2) }}</p>
                <p><strong>Due Date:</strong> {{ $billing->due_date->format('M d, Y') }}</p>


            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('styles')
<style>
    @media print {
        .btn, .modal, .breadcrumb, .card-header.bg-light {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
        }
        .card-body {
            padding: 20px !important;
        }
    }
</style>
@endsection
