@extends('layouts.app')

@section('title', 'Users Management - DarkFibre CRM')

@section('content')
<div class="container-fluid py-4">
    <!-- Header with Create Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 text-primary">
                <i class="fas fa-users me-2"></i>Users Management
            </h1>
            <p class="text-muted mb-0">Manage system users and their roles</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="fas fa-plus me-2"></i>Create New User
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <div class="flex-grow-1">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <div class="flex-grow-1">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted text-uppercase small fw-bold">Total Users</div>
                            <div class="h4 mb-0 text-primary">{{ $users->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted text-uppercase small fw-bold">Active Users</div>
                            <div class="h4 mb-0 text-success">{{ $users->where('status', 'active')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted text-uppercase small fw-bold">Admins</div>
                            <div class="h4 mb-0 text-warning">{{ $users->where('role', 'admin')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted text-uppercase small fw-bold">Customers</div>
                            <div class="h4 mb-0 text-info">{{ $users->where('role', 'customer')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Users List
            </h5>
            <div class="text-muted small">
                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries
            </div>
        </div>
        <div class="card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User Info</th>
                                <th>Contact</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <strong class="d-block">{{ $user->name }}</strong>
                                            @if($user->company_name)
                                                <small class="text-muted">{{ $user->company_name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div><i class="fas fa-envelope me-2 text-muted"></i>{{ $user->email }}</div>
                                        @if($user->phone)
                                            <div><i class="fas fa-phone me-2 text-muted"></i>{{ $user->phone }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge user-role-{{ $user->role }}">
                                        <i class="fas
                                            @if($user->role == 'admin') fa-user-shield
                                            @elseif($user->role == 'customer') fa-user-tie
                                            @elseif($user->role == 'finance') fa-calculator
                                            @elseif($user->role == 'designer') fa-palette
                                            @elseif($user->role == 'technician') fa-tools
                                            @elseif($user->role == 'account_manager') fa-user-check
                                            @else fa-user
                                            @endif me-1">
                                        </i>
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge status-{{ $user->status }}">
                                        <i class="fas
                                            @if($user->status == 'active') fa-check-circle
                                            @elseif($user->status == 'inactive') fa-pause-circle
                                            @else fa-ban
                                            @endif me-1">
                                        </i>
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $user->created_at->format('M d, Y') }}<br>
                                        <span class="text-muted">{{ $user->created_at->format('h:i A') }}</span>
                                    </small>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('users.edit', $user->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           data-bs-toggle="tooltip"
                                           title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->role !== 'admin')
                                            <a href="{{ route('users.assign-role', ['user' => $user->id, 'role' => 'admin']) }}"
                                               class="btn btn-sm btn-outline-success"
                                               data-bs-toggle="tooltip"
                                               title="Make Admin"
                                               onclick="return confirm('Are you sure you want to make this user an admin?')">
                                                <i class="fas fa-user-shield"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('users.assign-role', ['user' => $user->id, 'role' => 'customer']) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               data-bs-toggle="tooltip"
                                               title="Remove Admin"
                                               onclick="return confirm('Are you sure you want to remove admin privileges?')">
                                                <i class="fas fa-user-times"></i>
                                            </a>
                                        @endif
                                        <button class="btn btn-sm btn-outline-info view-user"
                                                data-bs-toggle="tooltip"
                                                title="View Details"
                                                data-user-id="{{ $user->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="tooltip"
                                                        title="Delete User"
                                                        onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
                    </div>
                    <div>
                        {{ $users->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="text-muted mb-3">
                        <i class="fas fa-users fa-4x"></i>
                    </div>
                    <h4 class="text-muted">No Users Found</h4>
                    <p class="text-muted">Get started by creating your first user.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                        <i class="fas fa-plus me-2"></i>Create First User
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createUserModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Create New User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST" id="createUserForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label required">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                   id="company_name" name="company_name" value="{{ old('company_name') }}">
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label required">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label required">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum 8 characters</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label required">Confirm Password</label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label required">Role</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="finance" {{ old('role') == 'finance' ? 'selected' : '' }}>Finance</option>
                                <option value="designer" {{ old('role') == 'designer' ? 'selected' : '' }}>Designer</option>
                                <option value="surveyor" {{ old('role') == 'surveyor' ? 'selected' : '' }}>Surveyor</option>
                                <option value="technician" {{ old('role') == 'technician' ? 'selected' : '' }}>Technician</option>
                                <option value="account_manager" {{ old('role') == 'account_manager' ? 'selected' : '' }}>Account Manager</option>
                                <option value="system_admin" {{ old('role') == 'system_admin' ? 'selected' : '' }}>System Admin</option>
                                <option value="debt_manager" {{ old('role') == 'debt_manager' ? 'selected' : '' }}>debt manager</option>
                                <option value="guest" {{ old('role') == 'guest' ? 'selected' : '' }}>Guest</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label required">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Additional fields for customer role -->
                    <div id="customerFields" style="display: none;">
                        <hr>
                        <h6 class="text-primary mb-3"><i class="fas fa-building me-2"></i>Customer Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="lease_start_date" class="form-label">Lease Start Date</label>
                                <input type="date" class="form-control @error('lease_start_date') is-invalid @enderror"
                                       id="lease_start_date" name="lease_start_date" value="{{ old('lease_start_date') }}">
                                @error('lease_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="billing_frequency" class="form-label">Billing Frequency</label>
                                <select class="form-select @error('billing_frequency') is-invalid @enderror"
                                        id="billing_frequency" name="billing_frequency">
                                    <option value="monthly" {{ old('billing_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('billing_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                    <option value="annually" {{ old('billing_frequency') == 'annually' ? 'selected' : '' }}>Annually</option>
                                </select>
                                @error('billing_frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="monthly_rate" class="form-label">Monthly Rate (KSh)</label>
                                <input type="number" step="0.01" class="form-control @error('monthly_rate') is-invalid @enderror"
                                       id="monthly_rate" name="monthly_rate" value="{{ old('monthly_rate', '0.00') }}">
                                @error('monthly_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Auto Billing</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="auto_billing_enabled"
                                           name="auto_billing_enabled" value="1" {{ old('auto_billing_enabled', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_billing_enabled">
                                        Enable automatic billing
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stats-card {
        border-left: 4px solid #007bff;
        transition: transform 0.2s ease-in-out;
    }
    .stats-card:hover {
        transform: translateY(-2px);
    }
    .stats-card.success { border-left-color: #28a745; }
    .stats-card.warning { border-left-color: #ffc107; }
    .stats-card.danger { border-left-color: #dc3545; }
    .stats-card.info { border-left-color: #17a2b8; }

    .user-role-admin { background-color: #f8d7da; color: #721c24; }
    .user-role-customer { background-color: #d1ecf1; color: #0c5460; }
    .user-role-finance { background-color: #d4edda; color: #155724; }
    .user-role-designer { background-color: #e2e3e5; color: #383d41; }
    .user-role-technician { background-color: #fff3cd; color: #856404; }
    .user-role-account_manager { background-color: #cce7ff; color: #004085; }

    .status-active { background-color: #d4edda; color: #155724; }
    .status-inactive { background-color: #fff3cd; color: #856404; }
    .status-suspended { background-color: #f8d7da; color: #721c24; }

    .table th {
        border-top: none;
        font-weight: 600;
        background-color: #f8f9fa;
    }

    .required::after {
        content: " *";
        color: #dc3545;
    }

    .action-buttons .btn {
        margin: 2px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Show/hide customer fields based on role selection
        const roleSelect = document.getElementById('role');
        const customerFields = document.getElementById('customerFields');

        function toggleCustomerFields() {
            if (roleSelect.value === 'customer') {
                customerFields.style.display = 'block';
            } else {
                customerFields.style.display = 'none';
            }
        }

        roleSelect.addEventListener('change', toggleCustomerFields);
        toggleCustomerFields(); // Initial check

        // Form validation
        const form = document.getElementById('createUserForm');
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // View user details (placeholder for future implementation)
        const viewButtons = document.querySelectorAll('.view-user');
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                // Implement view user functionality
                alert('View user details for ID: ' + userId);
            });
        });
    });
</script>
@endpush
