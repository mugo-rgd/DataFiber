@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-exchange-alt me-2"></i>Transactions
                </h1>
                <a href="{{ route('finance.transactions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>New Transaction
                </a>
            </div>
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
                                Total Transactions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $transactions->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
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
                                Total Income (USD)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($transactionStats['total_income_usd'] ?? 0, 2) }}
                            </div>
                            <div class="text-xs font-weight-bold text-success mt-2">
                                Total Income (KES)</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-600">
                                KES {{ number_format($transactionStats['total_income_ksh'] ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
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
                                Total Expenses (USD)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($transactionStats['total_expenses_usd'] ?? 0, 2) }}
                            </div>
                            <div class="text-xs font-weight-bold text-danger mt-2">
                                Total Expenses (KES)</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-600">
                                KES {{ number_format($transactionStats['total_expenses_ksh'] ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Net Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format(($transactionStats['total_income_usd'] ?? 0) - ($transactionStats['total_expenses_usd'] ?? 0), 2) }}
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-600">
                                KES {{ number_format(($transactionStats['total_income_ksh'] ?? 0) - ($transactionStats['total_expenses_ksh'] ?? 0), 2) }}
                            </div>
                            <div class="text-xs text-muted mt-2">
                                Pending: {{ $transactionStats['pending_transactions'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-md-12">
            <form method="GET" action="{{ route('finance.transactions.index') }}" class="row g-2">
                <div class="col-md-2">
                    <select name="type" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                        <option value="invoice" {{ request('type') == 'invoice' ? 'selected' : '' }}>Invoice</option>
                        <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>Payment</option>
                        <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Credit</option>
                        <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Debit</option>
                        <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Refund</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="direction" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ request('direction') == 'all' ? 'selected' : '' }}>All Directions</option>
                        <option value="in" {{ request('direction') == 'in' ? 'selected' : '' }}>Income (In)</option>
                        <option value="out" {{ request('direction') == 'out' ? 'selected' : '' }}>Expense (Out)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ request('category') == 'all' ? 'selected' : '' }}>All Categories</option>
                        <option value="invoice_payment" {{ request('category') == 'invoice_payment' ? 'selected' : '' }}>Invoice Payment</option>
                        <option value="refund" {{ request('category') == 'refund' ? 'selected' : '' }}>Refund</option>
                        <option value="fee" {{ request('category') == 'fee' ? 'selected' : '' }}>Fee</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="currency" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ request('currency') == 'all' ? 'selected' : '' }}>All Currencies</option>
                        <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="KSH" {{ request('currency') == 'KSH' ? 'selected' : '' }}>KES</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Start Date">
                </div>
                <div class="col-md-2">
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="End Date">
                </div>
                <div class="col-md-12 mt-2">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('finance.transactions.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>All Transactions
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Transaction #</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Direction</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Reference</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td><code>{{ $transaction->transaction_number }}</code></td>
                            <td>
                                @if($transaction->user)
                                    <span class="badge bg-info">#{{ $transaction->user_id }}</span>
                                    <br>
                                    <small>{{ $transaction->user->name ?? 'N/A' }}</small>
                                @else
                                    <span class="badge bg-secondary">#{{ $transaction->user_id }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $transaction->type === 'invoice' ? 'primary' : ($transaction->type === 'payment' ? 'success' : 'secondary') }}">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </td>
                            <td>
                                @if($transaction->direction === 'in')
                                    <span class="badge bg-success">
                                        <i class="fas fa-arrow-down me-1"></i> In
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-arrow-up me-1"></i> Out
                                    </span>
                                @endif
                            </td>
                            <td>{{ Str::limit($transaction->description, 50) }}</td>
                            <td class="fw-bold">
                                @if($transaction->currency === 'USD')
                                    <span class="text-primary">$</span>
                                @elseif($transaction->currency === 'KSH')
                                    <span class="text-success">KSh</span>
                                @endif
                                {{ number_format($transaction->amount, 2) }}
                            </td>
                            <td>
                                @if($transaction->currency === 'USD')
                                    <span class="badge bg-primary">USD</span>
                                @elseif($transaction->currency === 'KSH')
                                    <span class="badge bg-success">KES</span>
                                @else
                                    <span class="badge bg-secondary">{{ $transaction->currency }}</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M j, Y') }}</td>
                            <td>
                                @php
                                    $statusColor = match($transaction->status) {
                                        'completed' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger',
                                        default => 'info'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td>
                                @if($transaction->reference)
                                    <small class="text-muted">{{ $transaction->reference }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('finance.transactions.show', $transaction->id) }}" class="btn btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('finance.transactions.edit', $transaction->id) }}" class="btn btn-outline-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('finance.transactions.destroy', $transaction->id) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this transaction?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <i class="fas fa-exchange-alt fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No transactions found</h5>
                                <p class="text-muted mb-4">Get started by creating your first transaction.</p>
                                <a href="{{ route('finance.transactions.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Create First Transaction
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} entries
                </div>
                <div>
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
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
    .border-left-success {
        border-left: 4px solid #1cc88a !important;
    }
    .border-left-danger {
        border-left: 4px solid #e74a3b !important;
    }
    .border-left-info {
        border-left: 4px solid #36b9cc !important;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
    .table td {
        vertical-align: middle;
    }
</style>
@endpush
