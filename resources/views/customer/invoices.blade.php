@extends('layouts.app')

@section('title', 'My Billings')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-file-invoice-dollar me-2"></i>My Billings
            </h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Billings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBillings }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Billings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingBillings }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Overdue Billings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overdueBillings }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Amount Due</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $currency = $billings->isNotEmpty() ? $billings->first()->currency : 'USD';
                                @endphp
                                {{ $currency }} {{ number_format((float)$totalAmountDue, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Billings Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Billing #</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($billings as $billing)
                           @php
    $controller = app('App\Http\Controllers\Customer\BillingController');
    $overdue = $controller->isOverdue($billing);
    $statusClass = match($billing->status) {
        'paid' => 'success',
        'pending', 'sent' => 'warning',
        'overdue' => 'danger',
        'cancelled' => 'secondary',
        default => 'secondary'
    };
@endphp
                            <tr class="{{ $overdue ? 'table-danger' : '' }}">
                                <td><strong>#{{ $billing->billing_number }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($billing->billing_date)->format('M d, Y') }}</td>
                                <td class="{{ $overdue ? 'text-danger fw-bold' : '' }}">
                                    {{ \Carbon\Carbon::parse($billing->due_date)->format('M d, Y') }}
                                    @if($overdue)
                                        <br><small class="text-danger">Overdue</small>
                                    @endif
                                </td>
                                <td>{{ number_format((float)$billing->total_amount, 2) }}</td>
                                <td>{{ $billing->currency }}</td>
                                <td>
                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ ucfirst($billing->status) }}
                                        @if($overdue) (Overdue) @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('customer.billings.show', $billing->id) }}"
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('customer.billings.download', $billing->id) }}"
                                           class="btn btn-sm btn-outline-secondary" title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @if($billing->status === 'pending' || $billing->status === 'sent')
                                            <a href="{{ route('customer.payments.create', $billing->id) }}"
                                               class="btn btn-sm btn-success" title="Make Payment">
                                                <i class="fas fa-credit-card"></i>
                                            </a>
                                        @endif
                                        @if($billing->tevin_qr_code)
                                            <button type="button" class="btn btn-sm btn-info"
                                                    onclick="window.open('{{ $billing->tevin_qr_code }}', '_blank')"
                                                    title="View KRA QR Code">
                                                <i class="fas fa-qrcode"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No billings found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $billings->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }
    .border-left-warning {
        border-left: 4px solid #f6c23e !important;
    }
    .border-left-danger {
        border-left: 4px solid #e74a3b !important;
    }
    .border-left-success {
        border-left: 4px solid #1cc88a !important;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
    .table-danger {
        background-color: #f8d7da;
    }
    .fw-bold {
        font-weight: 700;
    }
</style>
@endpush
