@extends('layouts.app')

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
                            <h3 class="mb-0">${{ number_format($summary['total_balance'], 2) }}</h3>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Balance (KES)</h6>
                            <h3 class="mb-0">KSh {{ number_format($summary['total_balance_kes'], 2) }}</h3>
                        </div>
                        <i class="fas fa-money-bill-wave fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
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
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer Name</th>
                                    <th>Contact</th>
                                    <th class="text-end">Invoices</th>
                                    <th class="text-end">Total Amount (USD)</th>
                                    <th class="text-end">Paid (USD)</th>
                                    <th class="text-end">Balance (USD)</th>
                                    <th class="text-end">Balance (KES)</th>
                                    <th class="text-center">Last Due Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($billings as $index => $billing)
                                @php
                                    $dueClass = '';
                                    if ($billing->last_due_date) {
                                        $dueDate = \Carbon\Carbon::parse($billing->last_due_date);
                                        $today = \Carbon\Carbon::today();
                                        if ($dueDate->lt($today)) {
                                            $dueClass = 'table-danger';
                                        } elseif ($dueDate->diffInDays($today) <= 7) {
                                            $dueClass = 'table-warning';
                                        }
                                    }
                                @endphp
                                <tr class="{{ $dueClass }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $billing->customer_name }}</strong>
                                        @if($billing->company_name && $billing->company_name != $billing->customer_name)
                                        <br><small class="text-muted">{{ $billing->company_name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $billing->phone }}</div>
                                        <small class="text-muted">{{ $billing->email }}</small>
                                    </td>
                                    <td class="text-end">{{ $billing->billing_count }}</td>
                                    <td class="text-end">${{ number_format($billing->total_amount, 2) }}</td>
                                    <td class="text-end">${{ number_format($billing->total_paid, 2) }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-danger">${{ number_format($billing->balance, 2) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-warning">KSh {{ number_format($billing->balance_kes, 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($billing->last_due_date)
                                            {{ \Carbon\Carbon::parse($billing->last_due_date)->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('customer.debt', ['id' => $billing->id]) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> View Details
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
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
                                    <th class="text-end">${{ number_format($billings->sum('total_amount'), 2) }}</th>
                                    <th class="text-end">${{ number_format($billings->sum('total_paid'), 2) }}</th>
                                    <th class="text-end">${{ number_format($summary['total_balance'], 2) }}</th>
                                    <th class="text-end">KSh {{ number_format($summary['total_balance_kes'], 2) }}</th>
                                    <th colspan="2"></th>
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
@endsection
