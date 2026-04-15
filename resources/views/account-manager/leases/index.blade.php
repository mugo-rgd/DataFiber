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

<a href="{{ route('account-manager.leases.create',
     $customerId ) }}" class="btn btn-primary">
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

                                        <button class="btn btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $lease->id }}"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $lease->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete lease <strong>#{{ $lease->lease_number }}</strong>?
                                                    This action cannot be undone.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('account-manager.leases.destroy', $lease) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete Lease</button>
                                                    </form>
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
