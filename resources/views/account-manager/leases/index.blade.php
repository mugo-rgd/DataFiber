@extends('layouts.app')

@section('title', 'Lease Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-file-contract text-primary"></i>
                        @if($selectedCustomer)
                            Leases for {{ $selectedCustomer->name }}
                        @else
                            Lease Management
                        @endif
                    </h1>
                    <p class="text-muted">
                        @if($selectedCustomer)
                            Managing dark fibre lease agreements for {{ $selectedCustomer->name }}
                        @else
                            Manage all dark fibre lease agreements
                        @endif
                    </p>
                </div>
                <div class="btn-group">

<a href="{{ route('account-manager.leases.create', ['customer_id' => $customerId]) }}" class="btn btn-primary">
    Create Lease
</a>

                    @if($selectedCustomer)
                        <a href="{{ route('account-manager.customers.show', $selectedCustomer) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Customer
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Filter (only show when not viewing specific customer) -->
    @if(!$selectedCustomer)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter me-2"></i>Filter by Customer
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('account-manager.leases.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="customer_id" class="form-label">Select Customer</label>
                            <select name="customer_id" id="customer_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Customers</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ $customerId == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            @if($customerId)
                                <a href="{{ route('account-manager.leases.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Clear Filter
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Leases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLeases }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
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
                                Active Leases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeLeases }}</div>
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
                                Pending Leases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingLeases }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
{{--
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Monthly Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format((float)($monthlyRevenue ?? 0), 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Leases Table -->
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>
                        @if($selectedCustomer)
                            Leases for {{ $selectedCustomer->name }}
                        @else
                            All Leases
                        @endif
                        <span class="badge bg-primary ms-2">{{ $leases->total() }}</span>
                    </h6>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Search leases..." id="searchInput">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Lease #</th>
                            @if(!$selectedCustomer)
                                <th>Customer</th>
                            @endif
                            <th>Service Type</th>
                            <th>Route</th>
                            <th>Monthly Cost</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leases as $lease)
   @php
        $canDelete = auth()->user()->role === 'account_manager' && $lease->status === 'draft';
        $statusColor = $lease->status === 'active' ? 'success' : ($lease->status === 'draft' ? 'secondary' : 'warning');
    @endphp

                            <tr>
                                <td>
                                    <strong>#{{ $lease->lease_number }}</strong>
                                </td>
                                @if(!$selectedCustomer)
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                {{ substr($lease->customer->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $lease->customer->name }}</div>
                                                <small class="text-muted">{{ $lease->customer->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                                <td>
       <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}</span>
<br>
@if($lease->service_type == 'colocation')
    <div class="mt-1">
        <span class="border border-primary rounded px-1 py-0 text-primary" style="font-size: 0.7rem;">
            <i class="fas fa-map-marker-alt me-1" style="font-size: 0.6rem;"></i>{{ strtoupper($lease->host_location) }}
        </span>
    </div>
@endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $lease->start_location }} → {{ $lease->end_location }}
                                        @if($lease->distance_km)
                                            <br><span class="text-primary">{{ $lease->distance_km }} km</span>
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <strong>{{ number_format((float)($lease->monthly_cost ?? 0), 2) }}</strong>
                                    <br>
                                    <small class="text-muted">{{ strtoupper($lease->currency) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ match($lease->status) {
                                        'draft' => 'secondary',
                                        'active' => 'success',
                                        'pending' => 'warning',
                                        'expired' => 'danger',
                                        'terminated' => 'dark',
                                        default => 'light'
                                    } }}">
                                        {{ ucfirst($lease->status) }}
                                    </span>
                                    @if($lease->isExpired() && $lease->status !== 'expired')
                                        <span class="badge bg-warning">Expired</span>
                                    @endif
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($lease->start_date)->format('M d, Y') }}
                                </td>
                                <td>
                                     {{ \Carbon\Carbon::parse($lease->end_date)->format('M d, Y') }}
                                    @if($lease->isExpired())
                                        <br><small class="text-danger">Expired</small>
                                    @elseif($lease->daysUntilExpiry() < 30)
                                        <br><small class="text-warning">{{ $lease->daysUntilExpiry() }} days left</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('account-manager.leases.pdf', $lease) }}" class="btn btn-outline-primary" title="PDF Download">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <a href="{{ route('account-manager.leases.show', $lease) }}" class="btn btn-outline-primary" title="View/More Actions">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('account-manager.leases.edit', $lease) }}" class="btn btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        @if($canDelete)
        <button type="button"
                class="btn btn-outline-danger btn-xs"
                data-bs-toggle="modal"
                data-bs-target="#deleteModal{{ $lease->id }}"
                title="Delete Lease"
                style="padding: 0.15rem 0.3rem; font-size: 0.7rem;">
            <i class="fas fa-trash"></i>
        </button>
    @else
        <button type="button"
                class="btn btn-outline-secondary btn-xs"
                disabled
                title="Only draft leases can be deleted"
                style="padding: 0.15rem 0.3rem; font-size: 0.7rem; opacity: 0.5;">
            <i class="fas fa-trash"></i>
        </button>
    @endif
                                    </div>

                                    <!-- Delete Modal -->
                                    @php
    $canDelete = auth()->user()->role === 'account_manager' && $lease->status === 'draft';
    $statusColors = [
        'draft' => 'secondary',
        'pending' => 'warning',
        'active' => 'success',
        'expired' => 'info',
        'terminated' => 'danger',
        'cancelled' => 'dark'
    ];
    $statusColor = $statusColors[$lease->status] ?? 'secondary';
@endphp

<div class="modal fade" id="deleteModal{{ $lease->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{ $canDelete ? 'bg-danger text-white' : 'bg-secondary text-white' }}">
                <h5 class="modal-title">
                    <i class="fas {{ $canDelete ? 'fa-trash' : 'fa-lock' }} me-2"></i>
                    {{ $canDelete ? 'Confirm Delete' : 'Cannot Delete Lease' }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if(!$canDelete)
                    <div class="text-center py-3">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <p class="mb-2">This lease cannot be deleted because:</p>
                        <ul class="text-start d-inline-block">
                            @if(auth()->user()->role !== 'account_manager')
                                <li>You don't have permission to delete leases</li>
                            @endif
                            @if($lease->status !== 'draft')
                                <li>Lease status is <strong class="text-{{ $statusColor }}">{{ ucfirst($lease->status) }}</strong></li>
                                <li>Only <strong>draft</strong> leases can be deleted</li>
                            @endif
                        </ul>
                    </div>
                @else
                    <div>
                        <p>Are you sure you want to delete lease <strong>#{{ $lease->lease_number }}</strong>?</p>
                        <div class="alert alert-warning py-2 small">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Warning:</strong> This action cannot be undone. All lease data will be permanently removed.
                        </div>
                        <p class="mb-0 text-muted small">Lease details:</p>
                        <ul class="small mb-0">
                            <li><strong>Customer:</strong> {{ $lease->customer->name ?? 'N/A' }}</li>
                            <li><strong>Status:</strong> <span class="badge bg-{{ $statusColor }}">{{ ucfirst($lease->status) }}</span></li>
                            <li><strong>Monthly Cost:</strong> {{ $lease->currency }} {{ number_format($lease->monthly_cost, 2) }}</li>
                        </ul>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>

                @if($canDelete)
                    <form action="{{ route('account-manager.leases.destroy', $lease) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Yes, Delete Lease
                        </button>
                    </form>
                @else
                    <button type="button" class="btn btn-secondary" disabled>
                        <i class="fas fa-lock me-1"></i> Delete Disabled
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $selectedCustomer ? 8 : 9 }}" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-file-contract fa-4x mb-3"></i>
                                        <h5>No leases found</h5>
                                        <p>
                                            @if($selectedCustomer)
                                                No leases found for {{ $selectedCustomer->name }}.
                                            @else
                                                No leases found for your customers.
                                            @endif
                                        </p>
                                        <a href="{{ route('account-manager.leases.create', ['customer_id' => $customerId]) }}" class="btn btn-primary">
                                            <i class="fas fa-plus-circle me-2"></i>Create New Lease
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($leases->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $leases->firstItem() }} to {{ $leases->lastItem() }} of {{ $leases->total() }} results
                    </div>
                    {{ $leases->appends(['customer_id' => $customerId])->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple search functionality
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('tbody tr');

    if (searchInput && tableRows.length > 0) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endpush
