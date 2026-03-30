@extends('layouts.app')

@section('title', 'Customer Debt List')

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Customers with Debt</h6>
                            <h3 class="mb-0">{{ $summary['total_customers'] }}</h3>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Balance (USD)</h6>
                            <h3 class="mb-0">${{ number_format($summary['total_balance_usd'] ?? 0, 2) }}</h3>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Balance (KSH)</h6>
                            <h3 class="mb-0">KSH {{ number_format($summary['total_balance_ksh'] ?? 0, 2) }}</h3>
                        </div>
                        <i class="fas fa-shilling-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Invoices</h6>
                            <h3 class="mb-0">{{ $summary['total_invoices'] }}</h3>
                        </div>
                        <i class="fas fa-file-invoice fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Customer Debt List</h1>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print Report
                    </button>
                    <a href="{{ route('finance.debt.collection.report') }}" class="btn btn-secondary">
                        <i class="fas fa-file-invoice-dollar me-1"></i> Collection Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="customersTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer Name</th>
                                    <th>Contact</th>
                                    <th class="text-end">Invoices</th>
                                    <th class="text-end">USD Amount</th>
                                    <th class="text-end">USD Paid</th>
                                    <th class="text-end">USD Balance</th>
                                    <th class="text-end">KSH Amount</th>
                                    <th class="text-end">KSH Paid</th>
                                    <th class="text-end">KSH Balance</th>
                                    <th class="text-center">Last Due Date</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($billings as $index => $billing)
                                @php
                                    $dueClass = '';
                                    $statusBadge = 'badge-success';
                                    $statusText = 'Active';

                                    if ($billing->last_due_date) {
                                        $dueDate = \Carbon\Carbon::parse($billing->last_due_date);
                                        $today = \Carbon\Carbon::today();
                                        if ($dueDate->lt($today)) {
                                            $dueClass = 'table-danger';
                                            $statusBadge = 'badge-danger';
                                            $statusText = 'Overdue';
                                        } elseif ($dueDate->diffInDays($today) <= 7) {
                                            $dueClass = 'table-warning';
                                            $statusBadge = 'badge-warning';
                                            $statusText = 'Due Soon';
                                        }
                                    }
                                @endphp
                                <tr class="{{ $dueClass }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $billing->customer_name }}</strong>
                                    </td>
                                    <td>
                                        @if($billing->phone)
                                            <div>{{ $billing->phone }}</div>
                                        @endif
                                        <small class="text-muted">{{ $billing->email }}</small>
                                    </td>
                                    <td class="text-end">{{ $billing->billing_count }}</td>
                                    <td class="text-end">${{ number_format($billing->total_amount_usd ?? 0, 2) }}</td>
                                    <td class="text-end">${{ number_format($billing->total_paid_usd ?? 0, 2) }}</td>
                                    <td class="text-end">
                                        @if(($billing->balance_usd ?? 0) > 0)
                                            <span class="badge bg-danger">${{ number_format($billing->balance_usd, 2) }}</span>
                                        @else
                                            <span class="text-muted">$0.00</span>
                                        @endif
                                    </td>
                                    <td class="text-end">KSH {{ number_format($billing->total_amount_ksh ?? 0, 2) }}</td>
                                    <td class="text-end">KSH {{ number_format($billing->total_paid_ksh ?? 0, 2) }}</td>
                                    <td class="text-end">
                                        @if(($billing->balance_ksh ?? 0) > 0)
                                            <span class="badge bg-danger">KSH {{ number_format($billing->balance_ksh, 2) }}</span>
                                        @else
                                            <span class="text-muted">KSH 0.00</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($billing->last_due_date)
                                            {{ \Carbon\Carbon::parse($billing->last_due_date)->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('finance.debt.customer', ['id' => $billing->id]) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> View Details
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="13" class="text-center text-muted py-4">
                                        <i class="fas fa-check-circle fa-2x mb-3"></i>
                                        <p class="mb-0">No outstanding customer debts found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($billings->count() > 0)
                            <tfoot>
                                <tr class="table-active">
                                    <th colspan="3" class="text-end">TOTALS:</th>
                                    <th class="text-end">{{ $summary['total_invoices'] }}</th>
                                    <th class="text-end">${{ number_format($billings->sum('total_amount_usd'), 2) }}</th>
                                    <th class="text-end">${{ number_format($billings->sum('total_paid_usd'), 2) }}</th>
                                    <th class="text-end">${{ number_format($summary['total_balance_usd'], 2) }}</th>
                                    <th class="text-end">KSH {{ number_format($billings->sum('total_amount_ksh'), 2) }}</th>
                                    <th class="text-end">KSH {{ number_format($billings->sum('total_paid_ksh'), 2) }}</th>
                                    <th class="text-end">KSH {{ number_format($summary['total_balance_ksh'], 2) }}</th>
                                    <th colspan="3"></th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        @if($billings->count() > 0)
            $('#customersTable').DataTable({
                "pageLength": 25,
                "order": [[6, 'desc']], // Sort by USD balance column
                "language": {
                    "search": "Search customers:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ customers"
                }
            });
        @endif
    });
</script>
@endpush
@endsection
