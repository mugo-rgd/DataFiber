@extends('layouts.app')

@section('title', 'My Billings')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-invoice-dollar me-2"></i>My Billings
        </h1>
        <div>
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-download me-2"></i>Export
            </button>
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
    $currency = !empty($billings) ? (is_array($billings) ? $billings[0]->currency ?? 'USD' : $billings->first()->currency) : 'USD';
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
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Billing History</h6>
        </div>
        <div class="card-body">
            @if(count($billings) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Billing #</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                                <th>Currency</th>
                                <th>Status</th>
                                <th>KRA Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($billings as $billing)
                                @php
                                    $overdue = $billing->isOverdue();
                                    $statusClass = match($billing->status) {
                                        'paid' => 'success',
                                        'pending', 'sent' => 'warning',
                                        'overdue' => 'danger',
                                        'cancelled' => 'secondary',
                                        default => 'secondary'
                                    };
                                    $kraStatusClass = match($billing->tevin_status) {
                                        'submitted', 'validated' => 'success',
                                        'queued', 'processing' => 'info',
                                        'failed', 'job_failed' => 'danger',
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
                                    <td>{{ Str::limit($billing->description ?? 'N/A', 50) }}</td>
                                    <td class="text-end">{{ number_format($billing->total_amount, 2) }}</td>
                                    <td>{{ $billing->currency }}</td>
                                    <td>
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ ucfirst($billing->status) }}
                                            @if($overdue) (Overdue) @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if($billing->tevin_control_code)
                                            <span class="badge bg-success" title="Control Code: {{ $billing->tevin_control_code }}">
                                                <i class="fas fa-check-circle"></i> Validated
                                            </span>
                                        @else
                                            <span class="badge bg-{{ $kraStatusClass }}">
                                                {{ ucfirst($billing->tevin_status ?? 'pending') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('customer.billing.show', $billing->id) }}"
                                               class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('customer.billing.download', $billing->id) }}"
                                               class="btn btn-sm btn-primary" title="Download PDF">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="{{ route('customer.billing.preview', $billing->id) }}"
                                               class="btn btn-sm btn-secondary" title="Preview" target="_blank">
                                                <i class="fas fa-file-pdf"></i>
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
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No billings found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end mt-3">
    @if($billings instanceof \Illuminate\Pagination\LengthAwarePaginator)
        {{ $billings->links() }}
    @else
        <p class="text-muted">No pagination available</p>
    @endif
</div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No billings found</h5>
                    <p class="text-muted">Your billing history will appear here once invoices are generated.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Billings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('customer.billings.export') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" class="form-control" name="date_from" placeholder="From">
                            </div>
                            <div class="col-md-6">
                                <input type="date" class="form-control" name="date_to" placeholder="To">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }
    .border-left-success {
        border-left: 4px solid #1cc88a !important;
    }
    .border-left-warning {
        border-left: 4px solid #f6c23e !important;
    }
    .border-left-danger {
        border-left: 4px solid #e74a3b !important;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
    .bg-primary {
        background-color: #4e73df !important;
    }
    .bg-success {
        background-color: #1cc88a !important;
    }
    .bg-warning {
        background-color: #f6c23e !important;
    }
    .text-white-50 {
        color: rgba(255, 255, 255, 0.5) !important;
    }
</style>
@endpush
