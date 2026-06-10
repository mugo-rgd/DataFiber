@extends('layouts.app')

@section('title', 'Customer Details - ' . ($customer->company_name ?? $customer->name))

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">
                        <i class="fas fa-user-circle me-2" style="color: #0066B3;"></i>
                        Customer Details
                    </h1>
                    <p class="text-muted mb-0">
                        {{ $customer->company_name ?? $customer->name }}
                        <span class="badge bg-secondary ms-2">#{{ $customer->id }}</span>
                    </p>
                </div>
                <div>
                    <a href="{{ route('account-manager.customers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Customers
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Info Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-envelope fa-2x" style="color: #0066B3;"></i>
                        </div>
                        <div>
                            <small class="text-muted">Email</small>
                            <p class="mb-0 fw-bold">{{ $customer->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-phone fa-2x" style="color: #009639;"></i>
                        </div>
                        <div>
                            <small class="text-muted">Phone</small>
                            <p class="mb-0 fw-bold">{{ $customer->phone ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-building fa-2x" style="color: #FFD700;"></i>
                        </div>
                        <div>
                            <small class="text-muted">Company</small>
                            <p class="mb-0 fw-bold">{{ $customer->company_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-calendar fa-2x" style="color: #6f42c1;"></i>
                        </div>
                        <div>
                            <small class="text-muted">Member Since</small>
                            <p class="mb-0 fw-bold">{{ $customer->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small>Total Outstanding</small>
                            <h3 class="mb-0">${{ number_format($debtSummary->outstanding ?? 0, 2) }}</h3>
                        </div>
                        <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small>Overdue Amount</small>
                            <h3 class="mb-0">${{ number_format($debtSummary->overdue_amount ?? 0, 2) }}</h3>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small>Total Paid</small>
                            <h3 class="mb-0">${{ number_format($debtSummary->total_paid ?? 0, 2) }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Invoices</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($billings as $billing)
                            @php
                                $balance = $billing->total_amount - ($billing->paid_amount ?? 0);
                                $isOverdue = $billing->due_date && $billing->due_date < now() && $balance > 0;
                            @endphp
                            <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                <td class="fw-bold">{{ $billing->billing_number }}</td>
                                <td>{{ $billing->billing_date ? $billing->billing_date->format('M d, Y') : 'N/A' }}</td>
                                <td class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                    {{ $billing->due_date ? $billing->due_date->format('M d, Y') : 'N/A' }}
                                    @if($isOverdue)
                                        <span class="badge bg-danger ms-1">Overdue</span>
                                    @endif
                                </td>
                                <td>{{ $billing->currency }} {{ number_format($billing->total_amount, 2) }}</td>
                                <td>{{ $billing->currency }} {{ number_format($billing->paid_amount ?? 0, 2) }}</td>
                                <td class="fw-bold {{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $billing->currency }} {{ number_format($balance, 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $billing->status === 'paid' ? 'success' : ($billing->status === 'overdue' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($billing->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('account-manager.billing.show', $billing->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-receipt fa-3x text-muted mb-2"></i>
                                    <p class="mb-0">No invoices found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($billings->hasPages())
            <div class="card-footer bg-white">
                {{ $billings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
