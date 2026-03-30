@extends('layouts.app')

@section('title', 'My Customers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">My Customers</h1>
                <span class="badge badge-primary badge-pill py-2 px-3">
                    Total: {{ $customers->count() }} Customers
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse($customers as $customer)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h5 font-weight-bold text-primary mb-1">{{ $customer->name }}</div>
                            <div class="text-xs mb-1 text-gray-800">
                                <i class="fas fa-envelope"></i> {{ $customer->email }}
                            </div>
                            @if($customer->phone)
                            <div class="text-xs mb-1 text-gray-800">
                                <i class="fas fa-phone"></i> {{ $customer->phone }}
                            </div>
                            @endif
                            <div class="text-xs text-muted">
                                Assigned: {{ $customer->assigned_at ? $customer->assigned_at->format('M d, Y') : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h5 mb-0 font-weight-bold text-warning">{{ $customer->open_tickets_count }}</div>
                                <div class="text-xs text-warning">Open Tickets</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h5 mb-0 font-weight-bold text-info">{{ $customer->pending_payments_count }}</div>
                                <div class="text-xs text-info">Pending Payments</div>
                            </div>
                        </div>
                    </div>

                    <!-- Document Management Buttons -->
                    <div class="mt-3">
                        <!-- View Details Button -->
                        <a href="{{ route('account-manager.customers.show', $customer) }}"
                           class="btn btn-primary btn-sm btn-block mb-2">
                            <i class="fas fa-eye"></i> View Details
                        </a>

                        <!-- Approve Documents Button (For customer-uploaded documents) -->
                        @php
                            $pendingCount = \App\Models\Document::where('user_id', $customer->id)
                                ->where('source', 'customer') // Only customer-uploaded docs
                                ->where('status', 'pending_review')
                                ->count();
                        @endphp
                        <a href="{{ route('account-manager.documents.approve', $customer) }}"
                           class="btn btn-warning btn-sm btn-block mb-2">
                            <i class="fas fa-check-circle me-1"></i> Approve Documents
                            @if($pendingCount > 0)
                                <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
                            @endif
                        </a>

                        <!-- Manage Documents Button (For manually uploading signed docs) -->
                        <a href="{{ route('account-manager.customers.documents.manage', $customer) }}"
                           class="btn btn-info btn-sm btn-block">
                            <i class="fas fa-file-alt me-1"></i> Manage Documents
                            <small class="d-block text-muted mt-1">Upload signed documents</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                    <h4 class="text-gray-500">No Customers Assigned</h4>
                    <p class="text-gray-500">You don't have any customers assigned to you yet.</p>
                    <p class="text-muted">Please contact administrator to get customers assigned.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
