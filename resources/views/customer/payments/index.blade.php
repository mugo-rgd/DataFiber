@extends('layouts.app')

@section('title', 'My Payments')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-credit-card text-success me-2"></i>My Payments
                    </h1>
                    <p class="text-muted mb-0">View all your payment transactions</p>
                </div>
                <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Paid
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                KES {{ number_format($totalPaid ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Validation
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pendingCount ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Payments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $payments->total() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filter Payments
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('customer.payments.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Validated</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card shadow">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Payment History
            </h5>
        </div>
        <div class="card-body p-0">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Reference</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Payment Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>
                                        <strong>{{ $payment->payment_number ?? $payment->reference_number ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ $payment->id }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $payment->currency ?? 'KES' }} {{ number_format($payment->amount, 2) }}</strong>
                                        @if($payment->amount_kes && $payment->currency !== 'KES')
                                            <br>
                                            <small class="text-muted">KES {{ number_format($payment->amount_kes, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <i class="fas fa-{{ $payment->payment_method === 'M-Pesa' ? 'mobile-alt' : 'credit-card' }} me-1"></i>
                                            {{ $payment->payment_method ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'validated' => 'success',
                                                'rejected' => 'danger'
                                            ];
                                            $color = $statusColors[$payment->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }} rounded-pill px-3 py-2">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('customer.payments.show', $payment) }}"
                                           class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($payment->deposit_slip_path)
                                            <a href="{{ Storage::disk('public')->url($payment->deposit_slip_path) }}"
                                               class="btn btn-sm btn-outline-info rounded-pill px-3"
                                               target="_blank"
                                               title="View Receipt">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} entries
                        </div>
                        <div>
                            {{ $payments->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-credit-card fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No Payments Found</h4>
                    <p class="text-muted mb-0">You haven't made any payments yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 4px solid #0066B3 !important; }
.border-left-success { border-left: 4px solid #009639 !important; }
.border-left-warning { border-left: 4px solid #FFD700 !important; }
.border-left-danger { border-left: 4px solid #dc3545 !important; }
</style>
@endsection
