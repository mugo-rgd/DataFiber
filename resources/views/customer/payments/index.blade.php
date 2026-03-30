@extends('layouts.app')

@section('title', 'Payment Followups')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Payment Followups</h1>
                <a href="{{ route('account-manager.payments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Payment
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" class="row">
                        <div class="col-md-4">
                            <label>Status</label>
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="reminded" {{ request('status') == 'reminded' ? 'selected' : '' }}>Reminded</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Date Range</label>
                            <select name="date_range" class="form-control" onchange="this.form.submit()">
                                <option value="">All Time</option>
                                <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                                <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                                <option value="overdue" {{ request('date_range') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label>
                            <a href="{{ route('account-manager.payments.index') }}" class="btn btn-secondary btn-block">Reset Filters</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments List -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    @if($followups->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($followups as $payment)
                                <tr class="{{ $payment->isOverdue() ? 'table-danger' : ($payment->isDueSoon() ? 'table-warning' : '') }}">
                                    <td>
                                        <strong>{{ $payment->customer->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $payment->customer->email }}</small>
                                    </td>
                                    <td class="font-weight-bold text-primary">${{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        <div class="{{ $payment->isOverdue() ? 'text-danger font-weight-bold' : '' }}">
                                            {{ $payment->due_date->format('M d, Y') }}
                                        </div>
                                        @if($payment->isDueSoon() && !$payment->isOverdue())
                                        <small class="text-warning"><i class="fas fa-clock"></i> Due soon</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $payment->getStatusBadgeClass() }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                        @if($payment->reminded_at)
                                        <br>
                                        <small class="text-muted">Reminded: {{ $payment->reminded_at->format('M d') }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $payment->notes ? Str::limit($payment->notes, 50) : 'N/A' }}</td>
                                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($payment->status === 'pending')
                                        <form action="{{ route('account-manager.payments.remind', $payment) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning" title="Mark as Reminded">
                                                <i class="fas fa-bell"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('account-manager.payments.paid', $payment) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Mark as Paid">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $followups->firstItem() }} to {{ $followups->lastItem() }} of {{ $followups->total() }} entries
                        </div>
                        {{ $followups->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-money-bill-wave fa-3x text-gray-300 mb-3"></i>
                        <h4 class="text-gray-500">No Payment Followups Found</h4>
                        <p class="text-gray-500">You don't have any payment followups matching your criteria.</p>
                        <a href="{{ route('account-manager.payments.create') }}" class="btn btn-primary">Create Your First Payment</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mt-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pending Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($followups->whereIn('status', ['pending', 'reminded'])->sum('amount'), 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                                Overdue Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($followups->where('status', 'overdue')->sum('amount'), 2) }}
                            </div>
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
                                Total Paid</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($followups->where('status', 'paid')->sum('amount'), 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Due Soon</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $followups->where('status', 'pending')->filter(function($payment) {
                                    return $payment->isDueSoon();
                                })->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
