@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="header-actions">
        <div>
            <h1 class="h3 text-gray-800 mb-2">
                <i class="fas fa-users text-primary me-2"></i> Customers Management
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active text-primary"><i class="fas fa-users me-1"></i>Customers</li>
                </ol>
            </nav>
        </div>
        <div>
            <button type="button" class="btn btn-outline-secondary" onclick="goBack()">
                <i class="fas fa-arrow-left me-2"></i>Go Back
            </button>
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
                                Total Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['totalCustomers'] }}</div>
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
                                With Account Manager</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['customersWithManager'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
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
                                Without Account Manager</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['customersWithoutManager'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
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
                                Active This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['activeThisMonth']  }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="accountManagerFilter" class="form-label">Filter by Account Manager</label>
                    <select class="form-select" id="accountManagerFilter">
                        <option value="">All Managers</option>
                        <option value="assigned">With Manager</option>
                        <option value="unassigned">Without Manager</option>
                        @foreach($accountManagers as $manager)
                            <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">Filter by Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" placeholder="Search by name or email...">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary w-100" id="resetFilters">
                        <i class="fas fa-refresh me-2"></i>Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="card shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i> Customers List</h5>
            <span class="badge bg-primary">{{ $customers->count() }} customers</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="customersTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Contact Info</th>
                            <th>Account Manager</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr data-manager="{{ $customer->account_manager_id ? 'assigned' : 'unassigned' }}"
                            data-status="{{ $customer->is_active ? 'active' : 'inactive' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3">
                                        <div class="avatar-title bg-primary text-white rounded-circle">
                                            {{ substr($customer->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $customer->name }}</h6>
                                        <small class="text-muted">ID: {{ $customer->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div><i class="fas fa-envelope me-2 text-muted"></i>{{ $customer->email }}</div>
                                    @if($customer->phone)
                                    <div><i class="fas fa-phone me-2 text-muted"></i>{{ $customer->phone }}</div>
                                    @endif
                                    @if($customer->company)
                                    <div><i class="fas fa-building me-2 text-muted"></i>{{ $customer->company }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($customer->accountManager)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <div class="avatar-title bg-success text-white rounded-circle">
                                                {{ substr($customer->accountManager->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="fw-bold">{{ $customer->accountManager->name }}</span>
                                            <br>
                                            <small class="text-muted">{{ $customer->accountManager->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-warning">Not Assigned</span>
                                @endif
                            </td>
                            <td>
                                @if($customer->status ==='active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $customer->created_at->format('M d, Y') }}</small>
                                <br>
                                <small class="text-muted">{{ $customer->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if($customer->last_login_at)
                                    <small>{{ $customer->last_login_at->format('M d, Y') }}</small>
                                    <br>
                                    <small class="text-muted">{{ $customer->last_login_at->diffForHumans() }}</small>
                                @else
                                    <span class="badge bg-secondary">Never</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewProfileModal"
                                               onclick="viewProfile({{ $customer->id }})">
                                                <i class="fas fa-eye me-2"></i>View Profile
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#assignManagerModal"
                                               onclick="prepareAssignManager({{ $customer->id }}, '{{ $customer->name }}')">
                                                <i class="fas fa-user-tie me-2"></i>
                                                {{ $customer->accountManager ? 'Change' : 'Assign' }} Manager
                                            </a>
                                        </li>
                                        @if($customer->accountManager)
                                        <li>
                                            <a class="dropdown-item text-danger" href="#"
                                               onclick="disassignManager({{ $customer->id }}, '{{ $customer->name }}')">
                                                <i class="fas fa-user-times me-2"></i>Disassign Manager
                                            </a>
                                        </li>
                                        @endif
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.customers.quotations', $customer->id) }}">
                                                <i class="fas fa-file-invoice me-2"></i>View Quotations
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.customers.requests', $customer->id) }}">
                                                <i class="fas fa-drafting-compass me-2"></i>Design Requests
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            @if($customer->is_active)
                                                <a class="dropdown-item text-warning" href="#"
                                                   onclick="toggleStatus({{ $customer->id }}, 0)">
                                                    <i class="fas fa-ban me-2"></i>Deactivate
                                                </a>
                                            @else
                                                <a class="dropdown-item text-success" href="#"
                                                   onclick="toggleStatus({{ $customer->id }}, 1)">
                                                    <i class="fas fa-check me-2"></i>Activate
                                                </a>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No customers found</h5>
                                <p class="text-muted">There are no customers registered in the system yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($customers->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} entries
                    </div>
                    <div>
                        {{ $customers->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- View Profile Modal -->
<div class="modal fade" id="viewProfileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user me-2"></i>Customer Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="profileContent">
                <!-- Profile content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Assign Manager Modal -->
<div class="modal fade" id="assignManagerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-tie me-2"></i>Assign Account Manager</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignManagerForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="customer_id" id="customer_id">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Customer</label>
                        <input type="text" class="form-control" id="customer_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="account_manager_id" class="form-label">Select Account Manager</label>
                        <select class="form-select" id="account_manager_id" name="account_manager_id" required>
                            <option value="">Select Manager...</option>
                            @foreach($accountManagers as $manager)
                                <option value="{{ $manager->id }}">{{ $manager->name }} - {{ $manager->email }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Manager</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #6c757d;
}

.btn-group .dropdown-toggle::after {
    margin-left: 0.5em;
}
.avatar-xl {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 32px;
}
.avatar-sm {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}
/* Force modal backdrop to be removed properly */
.modal-backdrop {
    transition: opacity 0.15s linear;
}

.modal-backdrop.show {
    opacity: 0.5;
}

/* Ensure body scrolling is restored */
body.modal-open {
    overflow: auto !important;
    padding-right: 0 !important;
}
</style>

<script>
function goBack() {
    if (document.referrer && document.referrer.includes(window.location.host)) {
        window.history.back();
    } else {
        window.location.href = "{{ route('admin.dashboard') }}";
    }
}

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const accountManagerFilter = document.getElementById('accountManagerFilter');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('search');
    const resetFilters = document.getElementById('resetFilters');
    const table = document.getElementById('customersTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    function filterTable() {
        const managerValue = accountManagerFilter.value;
        const statusValue = statusFilter.value;
        const searchValue = searchInput.value.toLowerCase();

        for (let row of rows) {
            const manager = row.getAttribute('data-manager');
            const status = row.getAttribute('data-status');
            const text = row.textContent.toLowerCase();

            const managerMatch = !managerValue ||
                               (managerValue === 'assigned' && manager === 'assigned') ||
                               (managerValue === 'unassigned' && manager === 'unassigned') ||
                               manager === managerValue;

            const statusMatch = !statusValue || status === statusValue;
            const searchMatch = !searchValue || text.includes(searchValue);

            row.style.display = (managerMatch && statusMatch && searchMatch) ? '' : 'none';
        }
    }

    accountManagerFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('input', filterTable);

    resetFilters.addEventListener('click', function() {
        accountManagerFilter.value = '';
        statusFilter.value = '';
        searchInput.value = '';
        filterTable();
    });
});
// Manual cleanup function (call this if screen freezes)
function forceCleanupModal() {
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(backdrop => backdrop.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    console.log('Modal cleanup performed');
}

// You can call this from browser console if needed: forceCleanupModal()
// // View Profile
// View Profile - Updated for User model
// View Profile - Updated to handle debug response
function viewProfile(customerId) {
    console.log('View Profile clicked for customer:', customerId);

    // Get the modal element
    const modalElement = document.getElementById('viewProfileModal');

    // Properly dispose of any existing modal instance
    const existingModal = bootstrap.Modal.getInstance(modalElement);
    if (existingModal) {
        existingModal.dispose();
    }

    // Create new modal instance
    const profileModal = new bootstrap.Modal(modalElement);
    const profileContent = document.getElementById('profileContent');

    // Show loading state
    profileContent.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading customer profile...</p>
        </div>
    `;

    profileModal.show();

    // Make the AJAX request
    fetch(`/admin/customers/${customerId}/profile`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);

        // Handle debug response format
        if (data.debug && data.customer_found) {
            // Format the customer data into HTML
            const customer = data.customer;
            const accountManager = data.account_manager;

            // Create HTML for the profile
            const profileHtml = `
                <div class="customer-profile p-3">
                    <!-- Profile Header -->
                    <div class="text-center mb-4">
                        <div class="avatar-xl mx-auto mb-3">
                            <div class="avatar-title bg-primary text-white rounded-circle">
                                ${customer.name ? customer.name.charAt(0).toUpperCase() : '?'}
                            </div>
                        </div>
                        <h4 class="mb-1">${customer.name || 'N/A'}</h4>
                        ${customer.company_name ? `<p class="text-muted mb-2">${customer.company_name}</p>` : ''}
                        <p class="text-muted mb-2">Customer ID: #${customer.id}</p>
                        <div class="d-flex justify-content-center gap-2">
                            <span class="badge bg-${customer.status === 'active' ? 'success' : 'secondary'}">
                                ${customer.status ? customer.status.toUpperCase() : 'INACTIVE'}
                            </span>
                            <span class="badge bg-info">Member since ${new Date(customer.created_at).toLocaleDateString('en-US', { month: 'short', year: 'numeric' })}</span>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0">
                            <h6 class="mb-0"><i class="fas fa-address-card text-primary me-2"></i>Contact Information</h6>
                        </div>
                        <div class="card-body pt-0">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted" width="100">Email:</td>
                                    <td><a href="mailto:${customer.email}">${customer.email}</a></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Phone:</td>
                                    <td>${customer.phone ? `<a href="tel:${customer.phone}">${customer.phone}</a>` : 'Not provided'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Company:</td>
                                    <td>${customer.company_name || 'Not provided'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Address:</td>
                                    <td>${[customer.address, customer.city, customer.country].filter(Boolean).join(', ') || 'Not provided'}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Account Manager -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0">
                            <h6 class="mb-0"><i class="fas fa-user-tie text-primary me-2"></i>Account Manager</h6>
                        </div>
                        <div class="card-body pt-0">
                            ${accountManager ? `
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3">
                                        <div class="avatar-title bg-success text-white rounded-circle">
                                            ${accountManager.name ? accountManager.name.charAt(0).toUpperCase() : '?'}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">${accountManager.name}</h6>
                                        <small class="text-muted">${accountManager.email}</small>
                                    </div>
                                </div>
                            ` : `
                                <div class="text-center py-3">
                                    <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No account manager assigned</p>
                                </div>
                            `}
                        </div>
                    </div>

                    <!-- Billing Information -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0">
                            <h6 class="mb-0"><i class="fas fa-chart-bar text-primary me-2"></i>Billing Information</h6>
                        </div>
                        <div class="card-body pt-0">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Billing Frequency:</td>
                                    <td><span class="badge bg-info">${customer.billing_frequency || 'monthly'}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Monthly Rate:</td>
                                    <td class="fw-bold">$${parseFloat(customer.monthly_rate || 0).toFixed(2)}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Auto Billing:</td>
                                    <td>
                                        <span class="badge bg-${customer.auto_billing_enabled ? 'success' : 'secondary'}">
                                            ${customer.auto_billing_enabled ? 'Enabled' : 'Disabled'}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            `;

            profileContent.innerHTML = profileHtml;

        } else if (data.success && data.html) {
            profileContent.innerHTML = data.html;
        } else if (data.debug && !data.customer_found) {
            profileContent.innerHTML = `
                <div class="alert alert-warning m-3">
                    <h5>Customer Not Found</h5>
                    <p>${data.message || 'Customer ID: ' + customerId}</p>
                    <p>Available customer IDs: ${data.all_customer_ids ? data.all_customer_ids.join(', ') : 'None'}</p>
                </div>
            `;
        } else {
            throw new Error(data.message || 'Failed to load profile');
        }
    })
    .catch(error => {
        console.error('Profile Error:', error);
        profileContent.innerHTML = `
            <div class="alert alert-danger m-3">
                <h5 class="alert-heading">Failed to Load Profile</h5>
                <p>${error.message}</p>
                <hr>
                <button class="btn btn-sm btn-outline-danger" onclick="viewProfile(${customerId})">
                    <i class="fas fa-sync-alt me-1"></i>Try Again
                </button>
            </div>
        `;
    });
}
// Assign Manager
function prepareAssignManager(customerId, customerName) {
    document.getElementById('customer_id').value = customerId;
    document.getElementById('customer_name').value = customerName;
}

// Disassign Manager
function disassignManager(customerId, customerName) {
    if (confirm(`Are you sure you want to disassign account manager from ${customerName}?`)) {
        fetch(`/admin/customers/${customerId}/disassign-manager`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while disassigning the manager.');
        });
    }
}

// Toggle Status
function toggleStatus(customerId, status) {
    const action = status ? 'activate' : 'deactivate';
    if (confirm(`Are you sure you want to ${action} this customer?`)) {
        fetch(`/admin/customers/${customerId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating customer status.');
        });
    }
}

// Assign Manager Form Submission
document.getElementById('assignManagerForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/admin/customers/assign-manager', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while assigning the manager.');
    });
});
// Fix for modal backdrop not being removed
document.addEventListener('hidden.bs.modal', function (event) {
    // Remove any lingering backdrops
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(backdrop => backdrop.remove());

    // Remove modal-open class from body
    document.body.classList.remove('modal-open');

    // Reset body styles
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
});

// Also handle when modal is closed via the close button
document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
    button.addEventListener('click', function() {
        setTimeout(() => {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }, 100);
    });
});
</script>
@endsection
