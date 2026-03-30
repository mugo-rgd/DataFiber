@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h2 mb-1">Customer Dashboard</h1>
                    <p class="text-muted mb-0">
                        Welcome back, <strong>{{ $user->profile->company_name ?? $user->name }}</strong>!
                        <span class="badge bg-secondary ms-2">{{ ucfirst($user->role) }}</span>
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if($user->profile)
                        @php
                            $profileComplete = $user->profile_completion_percentage >= 100;
                            $statusClass = $profileComplete ? 'success' : 'warning';
                            $statusIcon = $profileComplete ? 'fa-check-circle' : 'fa-clock';
                            $statusText = $profileComplete ? 'All documents approved' : $user->uploaded_document_types_count . '/' . $user->required_document_types_count . ' documents';
                        @endphp
                        <div class="alert alert-{{ $statusClass }} mb-0 py-2" style="max-width: 300px;">
                            <div class="d-flex align-items-center">
                                <i class="fas {{ $statusIcon }} me-2"></i>
                                <div>
                                    <strong class="d-block">Profile {{ $profileComplete ? 'Complete' : 'Pending' }}</strong>
                                    <small class="text-{{ $statusClass }}">
                                        <i class="fas {{ $statusIcon }} me-1"></i>{{ $statusText }}
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
                            KES {{ number_format($billingStats['total_amount'] - $billingStats['paid_amount'], 2) }}
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
                            KES {{ number_format($billingStats['paid_amount'], 2) }}
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
                    <a href="{{ route('customer.tickets.create') }}" class="btn btn-warning">
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
            <div class="card border-dark shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-dark mb-3">
                        <i class="fas fa-file-contract fa-2x text-white"></i>
                    </div>
                    <h5 class="card-title text-dark">Documents</h5>
                    <p class="card-text text-muted">Upload and manage documents</p>
                    @if($user->leases->count() > 0)
                        <a href="{{ route('customer.documents.index') }}" class="btn btn-dark">
                            <i class="fas fa-folder me-2"></i>My Documents
                        </a>
                    @else
                        <button class="btn btn-outline-dark" disabled
                                data-bs-toggle="tooltip"
                                title="You need to have a lease to upload documents">
                            <i class="fas fa-folder me-2"></i>Documents (Locked)
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
                    <h5 class="card-title text-purple">Certificates</h5>
                    <p class="card-text text-muted">View your conditional and acceptance certificates</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.certificates.conditional.index') }}" class="btn btn-outline-purple">
                            <i class="fas fa-file-contract me-2"></i>Conditional
                        </a>
                        <a href="{{ route('customer.certificates.acceptance.index') }}" class="btn btn-outline-purple">
                            <i class="fas fa-check-circle me-2"></i>Acceptance
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Recent Activities</h6>
                </div>
                <div class="card-body">
                    @if($recentActivities->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent activities</p>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($recentActivities as $activity)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }} me-2"></i>
                                            <span>{{ $activity['description'] }}</span>
                                            @if($activity['status'] !== 'completed')
                                                <span class="badge bg-{{ $activity['color'] }} ms-2">{{ ucfirst($activity['status']) }}</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $activity['created_at']->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Consolidated Billings Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-file-invoice me-2"></i>Consolidated Invoices
            </h6>
            <a href="{{ route('customer.billings.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-list me-1"></i>View All
            </a>
        </div>
        <div class="card-body">
            @if($consolidatedBillings->isEmpty())
                <div class="text-center py-4">
                    <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No invoices found</h5>
                    <p class="text-muted">You don't have any invoices yet.</p>
                </div>
            @else
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
                            @foreach($consolidatedBillings as $billing)
                            <tr>
                                <td class="fw-bold">{{ $billing->billing_number ?? 'INV-' . str_pad($billing->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $billing->created_at->format('M d, Y') }}</td>
                                <td>
                                    @php
                                        $dueDate = $billing->due_date ?? $billing->created_at->addDays(30);
                                        $isOverdue = $dueDate->lt(now()) && $billing->status != 'paid';
                                    @endphp
                                    <span class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                        {{ $dueDate->format('M d, Y') }}
                                        @if($isOverdue)
                                            <br><small class="text-danger">Overdue</small>
                                        @endif
                                    </span>
                                </td>
                                <td class="fw-bold">KES {{ number_format($billing->total_amount, 2) }}</td>
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
                                        <a href="{{ route('customer.billings.show', $billing->id) }}"
                                           class="btn btn-outline-primary"
                                           data-bs-toggle="tooltip"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('customer.billings.download', $billing->id) }}"
                                           class="btn btn-outline-success"
                                           data-bs-toggle="tooltip"
                                           title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @if($billing->status == 'pending')
                                            <a href="{{ route('customer.payments.create', ['lease' => $billing->lineItems->first()->lease_id ?? 0]) }}"
                                               class="btn btn-outline-danger"
                                               data-bs-toggle="tooltip"
                                               title="Pay Now">
                                                <i class="fas fa-credit-card"></i>
                                            </a>
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
                                                        {{ $item->period_start ? $item->period_start->format('M d') : '' }}
                                                        {{ $item->period_end ? '- ' . $item->period_end->format('M d, Y') : '' }}
                                                    </td>
                                                    <td class="text-end fw-bold">
                                                        KES {{ number_format($item->amount, 2) }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($consolidatedBillings->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $consolidatedBillings->links() }}
                </div>
                @endif
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
    .btn-outline-purple {
        color: #6f42c1;
        border-color: #6f42c1;
    }
    .btn-outline-purple:hover {
        background-color: #6f42c1;
        border-color: #6f42c1;
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
