@extends('layouts.app')

@section('title', 'Billing #' . $billing->billing_number)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Billing #{{ $billing->billing_number }}
                </h1>
                <div class="btn-group">
                    <a href="{{ route('customer.invoices.download', $billing->id) }}"
                       class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Download PDF
                    </a>
                    <a href="{{ route('customer.invoices.index') }}"
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Billings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Billing Details -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Billing Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Billing Number:</th>
                                    <td>#{{ $billing->billing_number }}</td>
                                </tr>
                                <tr>
                                    <th>Billing Date:</th>
                                    <td>{{ $billing->billing_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Due Date:</th>
                                    <td class="{{ $billing->isOverdue() ? 'text-danger' : '' }}">
                                        {{ $billing->due_date->format('M d, Y') }}
                                        @if($billing->isOverdue())
                                            <span class="badge badge-danger ml-2">Overdue</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge badge-{{ $billing->status === 'paid' ? 'success' : ($billing->isOverdue() ? 'danger' : 'warning') }}">
                                            {{ ucfirst($billing->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Amount:</th>
                                    <td>${{ number_format((float)$billing->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Currency:</th>
                                    <td>{{ $billing->currency }}</td>
                                </tr>
                                <tr>
                                    <th>Billing Cycle:</th>
                                    <td>{{ ucfirst($billing->billing_cycle) }}</td>
                                </tr>
                                <tr>
                                    <th>Period:</th>
                                    <td>
                                        {{ $billing->period_start->format('M d, Y') }} -
                                        {{ $billing->period_end->format('M d, Y') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($billing->description)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Description:</h6>
                            <p class="text-muted">{{ $billing->description }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Lease Information -->
            @if($billing->lease)
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Lease Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Lease Number:</th>
                                    <td>{{ $billing->lease->lease_number }}</td>
                                </tr>
                                <tr>
                                    <th>Service Type:</th>
                                    <td>{{ ucfirst(str_replace('_', ' ', $billing->lease->service_type)) }}</td>
                                </tr>
                                <tr>
                                    <th>Start Location:</th>
                                    <td>{{ $billing->lease->start_location }}</td>
                                </tr>
                                <tr>
                                    <th>Bandwidth:</th>
                                    <td>
                                        @if($billing->lease->bandwidth)
                                            {{ $billing->lease->bandwidth }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">End Location:</th>
                                    <td>{{ $billing->lease->end_location }}</td>
                                </tr>
                                <tr>
                                    <th>Distance:</th>
                                    <td>
                                        @if($billing->lease->distance_km)
                                            {{ number_format((float)$billing->lease->distance_km, 2) }} km
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Monthly Cost:</th>
                                    <td>${{ number_format((float)$billing->lease->monthly_cost, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Contract Term:</th>
                                    <td>{{ $billing->lease->contract_term_months }} months</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Lease Dates -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Start Date:</th>
                                    <td>{{ $billing->lease->start_date->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">End Date:</th>
                                    <td>{{ $billing->lease->end_date->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Service Information</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This billing is for design services or one-time charges.
                    </div>
                    @if($billing->description)
                    <p><strong>Service Description:</strong> {{ $billing->description }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Action Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    @if($billing->status !== 'paid')
                        <a href="{{ route('customer.payments.create', $billing->id) }}"
                           class="btn btn-success btn-block mb-3">
                            <i class="fas fa-credit-card me-2"></i>Pay Now
                        </a>
                        <div class="alert alert-warning">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Due:</strong>
                            @if($billing->isOverdue())
                                <span class="text-danger">Overdue by {{ $billing->due_date->diffForHumans() }}</span>
                            @else
                                Due in {{ $billing->due_date->diffForHumans() }}
                            @endif
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Paid:</strong>
                            @if($billing->paid_at)
                                Paid on {{ $billing->paid_at->format('M d, Y') }}
                            @else
                                Payment recorded (date not available)
                            @endif
                        </div>
                    @endif

                    <div class="list-group">
                        <a href="{{ route('customer.invoices.download', $billing->id) }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-download me-2"></i>Download PDF
                            </span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="{{ route('customer.invoices.index') }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-arrow-left me-2"></i>Back to Billings
                            </span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        @if($billing->lease)
                        <a href="{{ route('customer.leases.show', $billing->lease->id) }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-network-wired me-2"></i>View Lease
                            </span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status Information -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Billing Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Billing Created -->
                        <div class="timeline-item active">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Billing Created</h6>
                                <small class="text-muted">
                                    {{ $billing->billing_date->format('M d, Y') }}
                                </small>
                            </div>
                        </div>

                        <!-- Sent to Customer -->
                        <div class="timeline-item {{ $billing->sent_at ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Sent to Customer</h6>
                                @if($billing->sent_at)
                                    <small class="text-muted">
                                        {{ $billing->sent_at->format('M d, Y') }}
                                    </small>
                                @else
                                    <small class="text-muted text-warning">Pending</small>
                                @endif
                            </div>
                        </div>

                        <!-- Payment Received -->
                        <div class="timeline-item {{ $billing->paid_at ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Payment Received</h6>
                                @if($billing->paid_at)
                                    <small class="text-muted">
                                        {{ $billing->paid_at->format('M d, Y') }}
                                    </small>
                                @else
                                    <small class="text-muted text-warning">Awaiting Payment</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amount Summary -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Amount Summary</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td>Amount Due:</td>
                            <td class="text-right font-weight-bold">
                                ${{ number_format((float)$billing->total_amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Currency:</td>
                            <td class="text-right">{{ $billing->currency }}</td>
                        </tr>
                        @if($billing->isOverdue())
                        <tr class="text-danger">
                            <td>Overdue Fee:</td>
                            <td class="text-right font-weight-bold">
                                + ${{ number_format((float)$billing->total_amount * 0.1, 2) }}
                                <small class="d-block">(10% late fee)</small>
                            </td>
                        </tr>
                        @endif
                    </table>

                    @if($billing->isOverdue())
                    <div class="alert alert-danger mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Overdue Notice:</strong> Please pay immediately to avoid service interruption.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 20px;
}
.timeline:before {
    content: '';
    position: absolute;
    left: 7px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}
.timeline-item {
    position: relative;
    margin-bottom: 25px;
}
.timeline-marker {
    position: absolute;
    left: -20px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #dee2e6;
    border: 2px solid #fff;
    z-index: 1;
}
.timeline-item.active .timeline-marker {
    background-color: #28a745;
}
.timeline-content {
    padding-left: 10px;
}
.timeline-content h6 {
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    color: #495057;
}
.badge {
    font-size: 0.75em;
}
.list-group-item {
    border: 1px solid rgba(0,0,0,.125);
    margin-bottom: 5px;
    border-radius: 0.375rem;
}
.table-borderless th {
    font-weight: 600;
    color: #495057;
}
</style>
@endsection
