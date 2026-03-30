@extends('layouts.app')

@section('title', 'Manage Users')

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
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-plus me-2"></i>Create New User
        </button>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users') }}">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search name/email..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="role" class="form-control">
                            <option value="">All Roles</option>
                            @foreach(['admin', 'customer', 'finance', 'designer', 'surveyor', 'technician', 'account_manager', 'system_admin', 'debt_manager', 'guest'] as $role)
                                <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            @foreach(['active', 'inactive', 'suspended'] as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.users') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Users</div>
                            <div class="h5 mb-0">{{ $users->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Active Users</div>
                            <div class="h5 mb-0">{{ $users->where('status', 'active')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Admins</div>
                            <div class="h5 mb-0">{{ $users->where('role', 'admin')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Customers</div>
                            <div class="h5 mb-0">{{ $users->where('role', 'customer')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Users List
            </h5>
        </div>
        <div class="card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                    @if($user->company_name)
                                        <br><small class="text-muted">{{ $user->company_name }}</small>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'customer' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->status == 'active' ? 'success' : ($user->status == 'inactive' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ url('/user/'.$user->id.'/assign-role/admin') }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-success" data-bs-toggle="tooltip" title="Make Admin" onclick="return confirm('Are you sure you want to make this user an admin?')">
                                                <i class="fas fa-user-shield"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ url('/user/'.$user->id.'/remove-role/admin') }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Remove Admin" onclick="return confirm('Are you sure you want to remove admin privileges from this user?')">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        </form>
                                        <button class="btn btn-outline-info" data-bs-toggle="tooltip" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
                    </div>
                    <div>
                        {{ $users->appends(request()->query())->links() }}
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createUserModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Create New User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" id="createUserForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>

                            <div class="form-group mb-3">
                                <label for="name" class="required">Full Name</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="company_name">Company Name</label>
                                <input type="text" name="company_name" id="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}">
                                @error('company_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="email" class="required">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="phone">Phone Number</label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Account Settings -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-cog me-2"></i>Account Settings</h6>

                            <div class="form-group mb-3">
                                <label for="role" class="required">Role</label>
                                <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                                    <option value="">Select Role</option>
                                    <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="finance" {{ old('role') == 'finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="designer" {{ old('role') == 'designer' ? 'selected' : '' }}>Designer</option>
                                    <option value="surveyor" {{ old('role') == 'surveyor' ? 'selected' : '' }}>Surveyor</option>
                                    <option value="technician" {{ old('role') == 'technician' ? 'selected' : '' }}>Technician</option>
                                    <option value="account_manager" {{ old('role') == 'account_manager' ? 'selected' : '' }}>Account Manager</option>
                                    <option value="system_admin" {{ old('role') == 'system_admin' ? 'selected' : '' }}>System Admin</option>
                                    <option value="debt_manager" {{ old('role') == 'debt_manager' ? 'selected' : '' }}>Debt Manager</option>
                                    <option value="ict_engineer" {{ old('role') == 'ict_engineer' ? 'selected' : '' }}>ICT Engineer</option>
                                     <option value="guest" {{ old('role') == 'guest' ? 'selected' : '' }}>Guest</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="status" class="required">Status</label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="password" class="required">Password</label>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="password_confirmation" class="required">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                            </div>
<div class="row mb-3" id="countyField" style="display: none;">
    <div class="col-md-6">
        <label for="county_id" class="form-label">County <span class="text-danger">*</span></label>
        <select class="form-select @error('county_id') is-invalid @enderror"
                id="county_id" name="county_id">
            <option value="">Select County</option>
            @foreach($counties as $county)
                <option value="{{ $county->id }}" {{ old('county_id') == $county->id ? 'selected' : '' }}>
                    {{ $county->name }}
                </option>
            @endforeach
        </select>
        @error('county_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">
            Required for County ICT Engineers.
        </div>
    </div>
    <div class="col-md-6">
        <label for="county_notes" class="form-label">County Notes</label>
        <textarea class="form-control @error('county_notes') is-invalid @enderror"
                  id="county_notes" name="county_notes" rows="2"
                  placeholder="Any notes about county assignment...">{{ old('county_notes') }}</textarea>
        @error('county_notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
                            <div class="form-group mb-3">
                                <label for="account_manager_id">Account Manager</label>
                                <select name="account_manager_id" id="account_manager_id" class="form-control @error('account_manager_id') is-invalid @enderror">
                                    <option value="">Select Account Manager</option>
                                    @foreach($accountManagers ?? [] as $manager)
                                        @if($manager)
                                            <option value="{{ $manager->id }}" {{ old('account_manager_id') == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('account_manager_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Customer Specific Fields -->
                    <div id="customerFields" style="display: none;">
                        <hr>
                        <h6 class="text-primary mb-3"><i class="fas fa-file-contract me-2"></i>Customer Information</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="lease_start_date">Lease Start Date</label>
                                    <input type="date" name="lease_start_date" id="lease_start_date" class="form-control @error('lease_start_date') is-invalid @enderror" value="{{ old('lease_start_date') }}">
                                    @error('lease_start_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="billing_frequency">Billing Frequency</label>
                                    <select name="billing_frequency" id="billing_frequency" class="form-control @error('billing_frequency') is-invalid @enderror">
                                        <option value="">Select Frequency</option>
                                        <option value="monthly" {{ old('billing_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="quarterly" {{ old('billing_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                        <option value="annually" {{ old('billing_frequency') == 'annually' ? 'selected' : '' }}>Annually</option>
                                    </select>
                                    @error('billing_frequency')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="monthly_rate">Monthly Rate</label>
                                    <input type="number" name="monthly_rate" id="monthly_rate" class="form-control @error('monthly_rate') is-invalid @enderror" step="0.01" min="0" value="{{ old('monthly_rate') }}">
                                    @error('monthly_rate')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="auto_billing_enabled" id="auto_billing_enabled" class="form-check-input @error('auto_billing_enabled') is-invalid @enderror" value="1" {{ old('auto_billing_enabled', true) ? 'checked' : '' }}>
                            <label for="auto_billing_enabled" class="form-check-label">Enable Auto Billing</label>
                            @error('auto_billing_enabled')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-file-upload me-2"></i>Documents</h6>
                        </div>
                        <div class="card-body">
                            <div id="documents-container">
                                <div class="document-row mb-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Document Type</label>
                                                <select name="documents[0][document_type]" class="form-control document-type-select @error('documents.0.document_type') is-invalid @enderror">
                                                    <option value="">Select Type</option>
                                                    @foreach($documentTypes ?? [] as $docType)
                                                        <option value="{{ $docType->document_type }}"
                                                                data-required="{{ $docType->is_required }}"
                                                                data-max-size="{{ $docType->max_file_size }}"
                                                                data-extensions='@json($docType->allowed_extensions ?? [])'>
                                                            {{ $docType->name }}
                                                            @if($docType->is_required)
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('documents.0.document_type')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>File</label>
                                                <input type="file" name="documents[0][file]" class="form-control document-file" data-index="0">
                                                <small class="form-text text-muted file-requirements" style="display: none;"></small>
                                                @error('documents.0.file')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <input type="text" name="documents[0][description]" class="form-control @error('documents.0.description') is-invalid @enderror" placeholder="Document description">
                                                @error('documents.0.description')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Expiry Date</label>
                                                <input type="date" name="documents[0][expiry_date]" class="form-control @error('documents.0.expiry_date') is-invalid @enderror">
                                                @error('documents.0.expiry_date')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                                <button type="button" class="btn btn-sm btn-danger mt-2 remove-document">
                                                    <i class="fas fa-trash me-1"></i>Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-secondary" id="add-document">
                                <i class="fas fa-plus me-1"></i>Add Another Document
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Get all form elements
    const roleSelect = document.getElementById('role');
    const customerFields = document.getElementById('customerFields');
    const countyField = document.getElementById('countyField');
    const accountManagerField = document.getElementById('accountManagerField');
    const billingInfoField = document.getElementById('billingInfoField');

    // Function to toggle all role-specific fields
    function toggleRoleSpecificFields() {
        const role = roleSelect ? roleSelect.value : '';

        console.log('Role selected:', role); // Debug log

        // Show/hide customer fields (for customer role)
        if (customerFields) {
            customerFields.style.display = role === 'customer' ? 'block' : 'none';
        }

        // Show/hide county field (for ict_engineer role)
        if (countyField) {
            if (role === 'ict_engineer') {
                countyField.style.display = 'block';
                console.log('Showing county field for ICT Engineer');

                // Make county_id required
                const countyIdSelect = document.getElementById('county_id');
                if (countyIdSelect) {
                    countyIdSelect.required = true;
                }
            } else {
                countyField.style.display = 'none';

                // Remove required from county_id
                const countyIdSelect = document.getElementById('county_id');
                if (countyIdSelect) {
                    countyIdSelect.required = false;
                }
            }
        }

        // Show/hide account manager field (for customer role)
        if (accountManagerField) {
            accountManagerField.style.display = role === 'customer' ? 'block' : 'none';
        }

        // Show/hide billing info field (for customer role)
        if (billingInfoField) {
            billingInfoField.style.display = role === 'customer' ? 'block' : 'none';
        }
    }

    // Add event listener for role change
    if (roleSelect) {
        roleSelect.addEventListener('change', toggleRoleSpecificFields);
        // Check initial value if form was submitted with errors
        toggleRoleSpecificFields();

        // Also handle form errors - if ict_engineer was selected but validation failed
        const oldRoleValue = "{{ old('role', '') }}";
        if (oldRoleValue === 'ict_engineer' && countyField) {
            countyField.style.display = 'block';
            const countyIdSelect = document.getElementById('county_id');
            if (countyIdSelect) {
                countyIdSelect.required = true;
            }
        }
    }

    // Form validation
    const form = document.getElementById('createUserForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Validate required documents
            const requiredDocs = document.querySelectorAll('.document-type-select option[data-required="1"]:checked');
            let hasMissingRequiredDoc = false;

            requiredDocs.forEach(doc => {
                const select = doc.parentElement;
                const documentRow = select.closest('.document-row');
                const fileInput = documentRow.querySelector('.document-file');

                if (!fileInput || !fileInput.files.length) {
                    e.preventDefault();
                    alert(`Please upload required document: ${doc.textContent.trim()}`);
                    fileInput.focus();
                    hasMissingRequiredDoc = true;
                    return;
                }
            });

            if (hasMissingRequiredDoc) {
                return;
            }

            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    }

    // Initialize document management with a slight delay
    setTimeout(() => {
        initializeDocumentManagement();
    }, 100);

    // Remove document row event delegation
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-document') || e.target.closest('.remove-document')) {
            const removeBtn = e.target.classList.contains('remove-document') ? e.target : e.target.closest('.remove-document');
            const row = removeBtn.closest('.document-row');
            if (row) {
                row.remove();
                updateDocumentIndexes();
            }
        }
    });
});

// Document management functions
function initializeDocumentManagement() {
    // Initialize all existing document type selects
    const documentTypeSelects = document.querySelectorAll('.document-type-select');
    documentTypeSelects.forEach(select => {
        initializeDocumentTypeSelect(select);
    });

    // Add document button event listener
    const addDocumentBtn = document.getElementById('add-document');
    if (addDocumentBtn) {
        addDocumentBtn.addEventListener('click', addDocumentRow);
    }
}

function addDocumentRow() {
    const container = document.getElementById('documents-container');
    if (!container) return;

    const index = container.children.length;

    const newRow = document.createElement('div');
    newRow.className = 'document-row mb-3';
    newRow.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Document Type</label>
                    <select name="documents[${index}][document_type]" class="form-control document-type-select">
                        <option value="">Select Type</option>
                        @foreach($documentTypes ?? [] as $docType)
                            <option value="{{ $docType->document_type }}"
                                    data-required="{{ $docType->is_required }}"
                                    data-max-size="{{ $docType->max_file_size }}"
                                    data-extensions='@json($docType->allowed_extensions ?? [])'>
                                {{ $docType->name }}
                                @if($docType->is_required)
                                    <span class="text-danger">*</span>
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>File</label>
                    <input type="file" name="documents[${index}][file]" class="form-control document-file" data-index="${index}">
                    <small class="form-text text-muted file-requirements" style="display: none;"></small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="documents[${index}][description]" class="form-control" placeholder="Document description">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Expiry Date</label>
                    <input type="date" name="documents[${index}][expiry_date]" class="form-control">
                    <button type="button" class="btn btn-sm btn-danger mt-2 remove-document">
                        <i class="fas fa-trash me-1"></i>Remove
                    </button>
                </div>
            </div>
        </div>
    `;

    container.appendChild(newRow);

    // Initialize the new document type select
    const newSelect = newRow.querySelector('.document-type-select');
    if (newSelect) {
        initializeDocumentTypeSelect(newSelect);
    }
}

// Initialize document type select functionality
function initializeDocumentTypeSelect(selectElement) {
    if (!selectElement) return;

    // Ensure the document row has all necessary elements
    const documentRow = selectElement.closest('.document-row');
    if (documentRow) {
        const fileInput = documentRow.querySelector('input[type="file"]');
        if (fileInput && !fileInput.classList.contains('document-file')) {
            fileInput.classList.add('document-file');
            const dataIndex = fileInput.getAttribute('data-index') ||
                            fileInput.name.match(/\[(\d+)\]/)?.[1] ||
                            '0';
            fileInput.setAttribute('data-index', dataIndex);
        }

        // Ensure requirements element exists
        let requirementsElement = documentRow.querySelector('.file-requirements');
        if (!requirementsElement) {
            requirementsElement = document.createElement('small');
            requirementsElement.className = 'form-text text-muted file-requirements';
            requirementsElement.style.display = 'none';
            if (fileInput && fileInput.parentNode) {
                fileInput.parentNode.appendChild(requirementsElement);
            }
        }
    }

    selectElement.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const documentRow = this.closest('.document-row');

        if (!documentRow) return;

        const fileInput = documentRow.querySelector('.document-file');
        const requirementsElement = documentRow.querySelector('.file-requirements');

        if (selectedOption && selectedOption.value) {
            const maxSize = selectedOption.getAttribute('data-max-size');
            const extensions = JSON.parse(selectedOption.getAttribute('data-extensions') || '[]');
            const isRequired = selectedOption.getAttribute('data-required') === '1';

            // Update file input requirements
            let requirements = [];
            if (maxSize) {
                requirements.push(`Max size: ${maxSize}KB`);
            }
            if (extensions.length > 0) {
                requirements.push(`Allowed: ${extensions.join(', ')}`);
            }
            if (isRequired) {
                requirements.push('Required document');
            }

            if (requirements.length > 0 && requirementsElement) {
                requirementsElement.textContent = requirements.join(' | ');
                requirementsElement.style.display = 'block';
            } else if (requirementsElement) {
                requirementsElement.style.display = 'none';
            }

            // Update file input attributes
            if (fileInput) {
                fileInput.required = isRequired;

                // Remove any existing change event listeners to prevent duplicates
                const newFileInput = fileInput.cloneNode(true);
                fileInput.parentNode.replaceChild(newFileInput, fileInput);

                // Add new event listener
                newFileInput.addEventListener('change', function() {
                    validateFile(this, extensions, maxSize);
                });
            }

        } else {
            if (requirementsElement) {
                requirementsElement.style.display = 'none';
            }
            if (fileInput) {
                fileInput.required = false;
            }
        }
    });

    // Trigger change event to initialize if value exists
    setTimeout(() => {
        if (selectElement.value) {
            selectElement.dispatchEvent(new Event('change'));
        }
    }, 100);
}

// File validation function
function validateFile(fileInput, allowedExtensions, maxSizeKB) {
    if (!fileInput) return;
    if (!fileInput.files.length) return;

    const file = fileInput.files[0];
    const errors = [];

    // Check file extension
    if (allowedExtensions.length > 0) {
        const fileExtension = file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(fileExtension)) {
            errors.push(`File type not allowed. Allowed types: ${allowedExtensions.join(', ')}`);
        }
    }

    // Check file size
    if (maxSizeKB) {
        const maxSizeBytes = maxSizeKB * 1024;
        if (file.size > maxSizeBytes) {
            errors.push(`File size exceeds maximum allowed size of ${maxSizeKB}KB`);
        }
    }

    // Display errors or clear them
    const existingError = fileInput.parentNode ? fileInput.parentNode.querySelector('.file-error') : null;
    if (errors.length > 0) {
        fileInput.setCustomValidity(errors.join(', '));

        if (existingError) {
            existingError.textContent = errors.join(', ');
        } else if (fileInput.parentNode) {
            const errorElement = document.createElement('div');
            errorElement.className = 'text-danger small mt-1 file-error';
            errorElement.textContent = errors.join(', ');
            fileInput.parentNode.appendChild(errorElement);
        }

        fileInput.classList.add('is-invalid');
    } else {
        fileInput.setCustomValidity('');
        if (existingError) {
            existingError.remove();
        }
        fileInput.classList.remove('is-invalid');
    }
}

// Update document indexes after removal
function updateDocumentIndexes() {
    const container = document.getElementById('documents-container');
    if (!container) return;

    const rows = container.querySelectorAll('.document-row');
    rows.forEach((row, index) => {
        // Update select name
        const select = row.querySelector('select[name^="documents"]');
        if (select) {
            select.name = `documents[${index}][document_type]`;
        }

        // Update file input name and data-index
        const fileInput = row.querySelector('input[type="file"]');
        if (fileInput) {
            fileInput.name = `documents[${index}][file]`;
            fileInput.setAttribute('data-index', index);
        }

        // Update description input name
        const descriptionInput = row.querySelector('input[type="text"]');
        if (descriptionInput) {
            descriptionInput.name = `documents[${index}][description]`;
        }

        // Update expiry date input name
        const expiryInput = row.querySelector('input[type="date"]');
        if (expiryInput) {
            expiryInput.name = `documents[${index}][expiry_date]`;
        }
    });
}
</script>
@endpush

<style>
.document-type-select option[data-required="1"] {
    font-weight: bold;
}

.file-requirements {
    font-size: 0.8rem;
    color: #6c757d;
}

.document-row {
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 1rem;
    background-color: #f8f9fa;
}

.document-row:not(:first-child) {
    margin-top: 1rem;
}

.required::after {
    content: " *";
    color: #dc3545;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.card-header h6 {
    margin-bottom: 0;
}

/* Modal scrollable */
.modal-body {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

/* Ensure file error messages are visible */
.file-error {
    display: block !important;
}
</style>
