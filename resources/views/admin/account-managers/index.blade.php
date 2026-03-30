@extends('layouts.app')

@section('title', 'Account Managers - Admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-tie me-2"></i>Account Managers
        </h1>
        <div>
            <a href="{{ route('admin.account-managers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Add New Manager
            </a>
            <a href="{{ route('admin.account-managers.analytics') }}" class="btn btn-info">
                <i class="fas fa-chart-bar me-2"></i>Analytics
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Managers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_managers'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Active Managers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_managers'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                                Total Customers Managed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_customers_managed'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users-cog fa-2x text-gray-300"></i>
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
                                Avg Customers/Manager
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['avg_customers_per_manager'], 1) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.account-managers.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Name, email, company...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('admin.account-managers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Account Managers Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Account Managers List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Company</th>
                            <th>Phone</th>
                            <th>Customers</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($managers as $manager)
                        <tr>
                            <td>{{ $manager->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-2"
                                         style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        {{ strtoupper(substr($manager->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $manager->name }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $manager->email }}</td>
                            <td>{{ $manager->company_name ?? 'N/A' }}</td>
                            <td>{{ $manager->phone ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $manager->customers_count }}</span>
                                <button type="button" class="btn btn-sm btn-link"
                                        onclick="showCustomerList({{ $manager->id }}, '{{ addslashes($manager->name) }}')">
                                    View
                                </button>
                            </td>
                            <td>
                                @if($manager->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $manager->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.account-managers.show', $manager->id) }}"
                                       class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.account-managers.edit', $manager->id) }}"
                                       class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm {{ $manager->status === 'active' ? 'btn-warning' : 'btn-success' }}"
                                            onclick="toggleStatus({{ $manager->id }}, '{{ addslashes($manager->name) }}')"
                                            title="{{ $manager->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas {{ $manager->status === 'active' ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No account managers found</h5>
                                <a href="{{ route('admin.account-managers.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus-circle me-2"></i>Add Your First Manager
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($managers->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $managers->firstItem() ?? 0 }} to {{ $managers->lastItem() ?? 0 }}
                    of {{ $managers->total() }} entries
                </div>
                <div>
                    {{ $managers->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Customer List Modal -->
<div class="modal fade" id="customerListModal" tabindex="-1" aria-labelledby="customerListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title" id="customerListModalLabel">
                    <i class="fas fa-users me-2"></i>
                    Customers for <span id="managerName" class="fw-bold"></span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.8rem;"></button>
            </div>
            <div class="modal-body p-0 small" id="customerListContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeCustomerModal()">
                    <i class="fas fa-times me-2"></i>Close
                </button>
                <a href="#" id="assignCustomersBtn" class="btn btn-primary btn-sm d-none">
                    <i class="fas fa-user-plus me-2"></i>Assign More Customers
                </a>
            </div>
        </div>
    </div>
</div>


@endsection

@push('styles')
<style>
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    .table-sm td, .table-sm th {
        padding: 0.5rem;
        vertical-align: middle;
    }

     /* Smaller modal text */
    #customerListModal .modal-body {
        font-size: 0.85rem;
    }

    #customerListModal .modal-body table {
        font-size: 0.8rem;
    }

    #customerListModal .modal-body .badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.4rem;
    }

    #customerListModal .modal-body .btn-sm {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
</style>
@endpush

{{-- SCRIPTS PLACED DIRECTLY HERE - NOT IN A SECTION --}}
<script>
// ============================================
// ACCOUNT MANAGER FUNCTIONS - DIRECT IN VIEW
// ============================================

// Log that scripts are loaded
console.log('✅ Account Manager scripts loading...');

// Test function
function testFunctions() {
    alert('✅ JavaScript is working!');
    console.log('Test function executed');
    console.log('showCustomerList defined:', typeof showCustomerList !== 'undefined');
    console.log('closeCustomerModal defined:', typeof closeCustomerModal !== 'undefined');
    console.log('toggleStatus defined:', typeof toggleStatus !== 'undefined');
}

// Test modal function
function testModal() {
    showCustomerList(98, 'Test Manager');
}

// Show customer list modal
function showCustomerList(managerId, managerName) {
    console.log('✅ showCustomerList called with:', managerId, managerName);

    // Get modal elements
    const modalEl = document.getElementById('customerListModal');
    const titleSpan = document.getElementById('managerName');
    const contentDiv = document.getElementById('customerListContent');

    if (!modalEl) {
        console.error('Modal element not found');
        alert('Error: Modal element not found!');
        return;
    }

    // Update title
    if (titleSpan) titleSpan.textContent = managerName;

    // Show loading
    if (contentDiv) {
        contentDiv.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading customers for ${managerName}...</p>
            </div>
        `;
    }

    // Show modal
    modalEl.classList.add('show');
    modalEl.style.display = 'block';
    document.body.classList.add('modal-open');

    // Add backdrop if not exists
    if (!document.querySelector('.modal-backdrop')) {
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);
    }

    // Fetch customers
    fetch(`/admin/account-managers/${managerId}/customers`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Data received:', data);

        if (data.count === 0) {
            contentDiv.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-users-slash fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Customers Found</h5>
                    <p class="text-muted">This manager has no customers assigned.</p>
                    <a href="/admin/customers/assign?manager=${managerId}" class="btn btn-primary mt-3">
                        <i class="fas fa-user-plus me-2"></i>Assign Customers
                    </a>
                </div>
            `;
        } else if (data.html) {
            contentDiv.innerHTML = data.html;
        } else {
            contentDiv.innerHTML = `
                <div class="p-4 text-center">
                    <i class="fas fa-info-circle fa-3x text-info mb-3"></i>
                    <h5>${data.count || 0} Customers Found</h5>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        contentDiv.innerHTML = `
            <div class="alert alert-danger m-3">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Error loading customers:</strong> ${error.message}
            </div>
        `;
    });
}

// Close modal function
function closeCustomerModal() {
    console.log('Closing modal');
    const modalEl = document.getElementById('customerListModal');
    if (modalEl) {
        modalEl.classList.remove('show');
        modalEl.style.display = 'none';
    }
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
}

// Toggle status function
function toggleStatus(managerId, managerName) {
    console.log('Toggle status for:', managerId, managerName);

    if (!confirm(`Are you sure you want to toggle status for ${managerName}?`)) {
        return;
    }

    alert('Status toggle functionality - API call would go here');
    // Add your actual API call here
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM fully loaded');
    console.log('✅ Functions available:', {
        showCustomerList: typeof showCustomerList,
        closeCustomerModal: typeof closeCustomerModal,
        toggleStatus: typeof toggleStatus
    });

    // Add click handler for backdrop
    const modal = document.getElementById('customerListModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeCustomerModal();
            }
        });
    }
});

// Make functions globally available
window.showCustomerList = showCustomerList;
window.closeCustomerModal = closeCustomerModal;
window.toggleStatus = toggleStatus;
window.testFunctions = testFunctions;
window.testModal = testModal;
</script>
