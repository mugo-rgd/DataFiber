{{-- resources/views/customer-portal/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>Welcome, {{ $customer->name }}!
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card text-white bg-info mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-1">Current Balance</h6>
                                            <h3 class="mb-0">${{ number_format($customer->current_balance ?? 0, 2) }}</h3>
                                        </div>
                                        <i class="fas fa-wallet fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-1">Total Statements</h6>
                                            <h3 class="mb-0">{{ $recentStatements->count() }}</h3>
                                        </div>
                                        <i class="fas fa-file-invoice fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-warning mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-1">Recent Transactions</h6>
                                            <h3 class="mb-0">{{ $recentTransactions->count() }}</h3>
                                        </div>
                                        <i class="fas fa-exchange-alt fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('customer.statements.create') }}" class="btn btn-primary me-2">
                                        <i class="fas fa-file-invoice me-2"></i>Generate New Statement
                                    </a>
                                    <a href="{{ route('customer.statements') }}" class="btn btn-info me-2">
                                        <i class="fas fa-list me-2"></i>View All Statements
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Statements -->
                    @if($recentStatements->count() > 0)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Recent Statements</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Statement #</th>
                                                    <th>Date</th>
                                                    <th>Period</th>
                                                    <th class="text-end">Amount</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recentStatements as $statement)
                                                <tr>
                                                    <td>{{ $statement->statement_number }}</td>
                                                    <td>{{ $statement->statement_date->format('d M Y') }}</td>
                                                    <td>{{ $statement->period_start->format('d M Y') }}</td>
                                                    <td class="text-end">${{ number_format($statement->closing_balance, 2) }}</td>
                                                    <td>
                                                        @if($statement->status == 'generated')
                                                            <span class="badge bg-info">Generated</span>
                                                        @elseif($statement->status == 'sent')
                                                            <span class="badge bg-success">Sent</span>
                                                        @elseif($statement->status == 'viewed')
                                                            <span class="badge bg-primary">Viewed</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('statements.download', $statement->id) }}"
                                                           class="btn btn-sm btn-info"
                                                           title="Download">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Recent Transactions -->
                    @if($recentTransactions->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Recent Transactions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Description</th>
                                                    <th>Reference</th>
                                                    <th class="text-end">Amount</th>
                                                    <th class="text-end">Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recentTransactions as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                                                    <td>{{ $transaction->description }}</td>
                                                    <td>{{ $transaction->reference ?? '-' }}</td>
                                                    <td class="text-end {{ $transaction->direction == 'in' ? 'text-success' : 'text-danger' }}">
                                                        {{ $transaction->direction == 'in' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                                    </td>
                                                    <td class="text-end">${{ number_format($transaction->balance, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
