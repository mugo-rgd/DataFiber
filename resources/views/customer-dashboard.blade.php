@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">Customer Dashboard</h1>
                    <p class="text-muted mb-0">
                        Welcome back, <strong>{{ Auth::user()->company_name ?? Auth::user()->name }}</strong>!
                        <span class="badge bg-secondary ms-2">{{ ucfirst(Auth::user()->role) }}</span>
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if(Auth::user()->companyProfile)
                    <div class="alert alert-success mb-0 py-2" style="max-width: 300px;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <div>
                                <strong class="d-block">Profile Complete</strong>
                                <small>
                                    @if(!Auth::user()->hasCompleteProfile())
                                        <span class="text-warning">
                                            <i class="fas fa-clock me-1"></i>Pending document approval
                                        </span>
                                    @else
                                        <span class="text-success">
                                            <i class="fas fa-check me-1"></i>All documents approved
                                        </span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
{{-- In your dashboard.blade.php --}}
@php
    // Get profile document counts
    $requiredProfileDocs = ['kra_pin_certificate', 'business_registration_certificate', 'trade_license', 'ca_license', 'cr12_certificate'];
    $uploadedDocs = Auth::user()->documents()->whereIn('document_type', $requiredProfileDocs)->pluck('document_type')->toArray();
    $missingDocs = array_diff($requiredProfileDocs, $uploadedDocs);
    $approvedDocs = Auth::user()->documents()->whereIn('document_type', $requiredProfileDocs)->where('status', 'approved')->count();
@endphp

<div class="alert alert-{{ count($missingDocs) == 0 ? 'success' : 'warning' }} mb-0 py-2" style="max-width: 300px;">
    <div class="d-flex align-items-center">
        <i class="fas fa-{{ count($missingDocs) == 0 ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
        <div>
            <strong class="d-block">
                {{ count($missingDocs) == 0 ? 'Profile Complete' : 'Profile Incomplete' }}
            </strong>
            <small>
                @if(count($missingDocs) > 0)
                    <span class="text-warning">
                        <i class="fas fa-clock me-1"></i>
                        {{ count($missingDocs) }} document(s) missing
                    </span>
                @elseif($approvedDocs == count($requiredProfileDocs))
                    <span class="text-success">
                        <i class="fas fa-check me-1"></i>All documents approved
                    </span>
                @else
                    <span class="text-warning">
                        <i class="fas fa-clock me-1"></i>Pending approval
                    </span>
                @endif
            </small>
        </div>
    </div>
</div>
    <!-- Quick Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Total Invoices
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $billingStats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Paid
                            </div>
                            <div class="h5 mb-0 fw-bold text-success">{{ $billingStats['paid'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                Pending
                            </div>
                            <div class="h5 mb-0 fw-bold text-warning">{{ $billingStats['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-danger border-4 h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                Overdue
                            </div>
                            <div class="h5 mb-0 fw-bold text-danger">{{ $billingStats['overdue'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Amount Summary -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-danger">
                        <i class="fas fa-money-bill-wave me-2"></i>Total Outstanding
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <div class="display-5 fw-bold text-danger mb-2">
                            ${{ number_format($billingStats['total_amount'], 2) }}
                        </div>
                        <p class="text-muted mb-0">Amount pending for payment</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-success">
                        <i class="fas fa-check-circle me-2"></i>Total Paid
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <div class="display-5 fw-bold text-success mb-2">
                            ${{ number_format($billingStats['paid_amount'], 2) }}
                        </div>
                        <p class="text-muted mb-0">Amount successfully paid</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Action Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-4">
            <div class="card border-primary shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-primary mb-3">
                        <i class="fas fa-network-wired fa-2x text-white"></i>
                    </div>
                    <h5 class="card-title text-primary">My Leases</h5>
                    <p class="card-text text-muted">Manage your fibre leases and connections</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.leases.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i>View Leases
                        </a>
                        <a href="{{ route('customer.design-requests.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>My Requests
                        </a>
                        <a href="{{ route('customer.quotations.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-file-invoice me-2"></i>My Quotations
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-success shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-success mb-3">
                        <i class="fas fa-plus-circle fa-2x text-white"></i>
                    </div>
                    <h5 class="card-title text-success">Request Fibre Routes</h5>
                    <p class="card-text text-muted">Submit new fibre connection requests</p>
                    <a href="{{ route('customer.design-requests.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>New Request
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-info shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-info mb-3">
                        <i class="fas fa-file-invoice-dollar fa-2x text-white"></i>
                    </div>
                    <h5 class="card-title text-info">My Billings</h5>
                    <p class="card-text text-muted">View and manage your billing</p>
                    <a href="{{ route('customer.billings.index') }}" class="btn btn-info text-white">
                        <i class="fas fa-file-alt me-2"></i>View Billings
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-warning shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-warning mb-3">
                        <i class="fas fa-ticket-alt fa-2x text-white"></i>
                    </div>
                    <h5 class="card-title text-warning">Support Tickets</h5>
                    <p class="card-text text-muted">Get help and support</p>
                    <a href="{{ route('customer.support.create') }}" class="btn btn-warning">
                        <i class="fas fa-plus me-2"></i>New Ticket
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Quick Actions -->
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="card border-secondary shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-secondary mb-3">
                        <i class="fas fa-user-cog fa-2x text-white"></i>
                    </div>
                    <h5 class="card-title text-secondary">Profile</h5>
                    <p class="card-text text-muted">Manage your account settings</p>
                    <a href="{{ route('customer.profile.show') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-cog me-2"></i>Manage Profile
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <!-- Update the document buttons section for better mobile display -->
<div class="card border-dark shadow-sm h-100">
    <div class="card-body text-center">
        <div class="icon-circle bg-dark mb-3">
            <i class="fas fa-file-contract fa-2x text-white"></i>
        </div>
        <h5 class="card-title text-dark">Documents</h5>
        <p class="card-text text-muted mb-3">Upload and manage documents</p>

        @if(Auth::user()->leases->count() > 0)
            <div class="d-grid gap-2 d-md-block">
                <a href="{{ route('customer.documents.index') }}" class="btn btn-primary mb-2 mb-md-0 me-md-2">
                    <i class="fas fa-folder-open me-2"></i>My Documents
                </a>
                <a href="{{ route('customer.documents.requests.index') }}" class="btn btn-success">
                    <i class="fas fa-file-import me-2"></i>Request Missing Documents
                </a>
            </div>
        @else
            <button class="btn btn-outline-dark w-100" disabled
                    data-bs-toggle="tooltip"
                    title="You need to have a lease to upload documents">
                <i class="fas fa-upload me-2"></i>Upload Documents
            </button>
        @endif
    </div>
</div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-purple shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-purple mb-3">
                        <i class="fas fa-handshake fa-2x text-white"></i>
                    </div>
                    <h5 class="card-title text-purple">Contracts</h5>
                    <p class="card-text text-muted">View and manage your contracts</p>
                    <a href="{{ route('customer.contracts.index') }}" class="btn btn-purple">
                        <i class="fas fa-file-signature me-2"></i>View Contracts
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Consolidated Billings Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-file-invoice me-2"></i>Consolidated Invoices
            </h6>
            <a href="{{ route('customer.billings.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-list me-1"></i>View All
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($consolidatedBillings as $billing)
                        <tr>
                            <td class="fw-bold">{{ $billing->billing_number }}</td>
                            <td>{{ $billing->billing_date->format('M d, Y') }}</td>
                            <td>
                                <span class="{{ $billing->due_date->lt(now()) && $billing->status != 'paid' ? 'text-danger fw-bold' : '' }}">
                                    {{ $billing->due_date->format('M d, Y') }}
                                    @if($billing->due_date->lt(now()) && $billing->status != 'paid')
                                        <br><small class="text-danger">Overdue</small>
                                    @endif
                                </span>
                            </td>
                            <td class="fw-bold">${{ number_format($billing->total_amount, 2) }}</td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'paid' => 'success',
                                        'pending' => 'warning',
                                        'overdue' => 'danger',
                                        'cancelled' => 'secondary',
                                    ];
                                    $class = $statusClasses[$billing->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $class }}">{{ ucfirst($billing->status) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $billing->lineItems->count() }}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('customer.billing.show', $billing->id) }}"
                                       class="btn btn-outline-primary"
                                       data-bs-toggle="tooltip"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('customer.billing.download', $billing->id) }}"
                                       class="btn btn-outline-success"
                                       data-bs-toggle="tooltip"
                                       title="Download PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @if($billing->status == 'pending')
                                        @php
                                            $leaseId = $billing->lineItems->first()->lease_id ?? null;
                                        @endphp
                                        @if($leaseId)
                                            <a href="{{ route('customer.payments.create', ['lease' => $leaseId]) }}"
                                               class="btn btn-outline-danger"
                                               data-bs-toggle="tooltip"
                                               title="Pay Now">
                                                <i class="fas fa-credit-card"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('customer.billing.show', $billing->id) }}"
                                               class="btn btn-outline-danger"
                                               data-bs-toggle="tooltip"
                                               title="View & Pay">
                                                <i class="fas fa-credit-card"></i>
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <!-- Line Items Expandable -->
                        @if($billing->lineItems->count() > 0)
                        <tr class="table-light">
                            <td colspan="7" class="p-3">
                                <div class="small fw-bold text-muted mb-2">
                                    <i class="fas fa-list me-1"></i>Line Items ({{ $billing->lineItems->count() }})
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Lease</th>
                                                <th>Description</th>
                                                <th>Period</th>
                                                <th class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($billing->lineItems as $item)
                                            <tr>
                                                <td>
                                                    @if($item->lease)
                                                        {{ $item->lease->name ?? 'Lease #' . $item->lease_id }}
                                                    @else
                                                        <em class="text-muted">N/A</em>
                                                    @endif
                                                </td>
                                                <td>{{ $item->description }}</td>
                                                <td>
                                                    {{ $item->period_start->format('M d') }} - {{ $item->period_end->format('M d, Y') }}
                                                </td>
                                                <td class="text-end fw-bold">
                                                    ${{ number_format($item->amount, 2) }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No invoices found</h5>
                                <p class="text-muted">You don't have any invoices yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($consolidatedBillings->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $consolidatedBillings->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    .btn-purple {
        background-color: #6f42c1;
        border-color: #6f42c1;
        color: white;
    }
    .btn-purple:hover {
        background-color: #5a32a3;
        border-color: #5a32a3;
        color: white;
    }
    .bg-purple {
        background-color: #6f42c1 !important;
    }
    .text-purple {
        color: #6f42c1 !important;
    }
    .border-purple {
        border-color: #6f42c1 !important;
    }
    .card {
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .border-start {
        border-left-width: 4px !important;
    }
</style>

<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
