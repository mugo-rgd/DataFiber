@extends('layouts.app')

@section('title', 'Edit User - ' . $user->name)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 text-primary">
                <i class="fas fa-user-edit me-2"></i>Edit User: {{ $user->name }}
            </h1>
            <p class="text-muted mb-0">Update user information and permissions</p>
        </div>
        <div>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Users
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

    <div class="row">
        <div class="col-lg-8">
            <!-- User Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>User Information
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label required">Full Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                       id="company_name" name="company_name" value="{{ old('company_name', $user->company_name) }}">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label required">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label required">Role</label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Customer</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                                    <option value="finance" {{ old('role', $user->role) == 'finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="designer" {{ old('role', $user->role) == 'designer' ? 'selected' : '' }}>Designer</option>
                                    <option value="surveyor" {{ old('role', $user->role) == 'surveyor' ? 'selected' : '' }}>Surveyor</option>
                                    <option value="technician" {{ old('role', $user->role) == 'technician' ? 'selected' : '' }}>Technician</option>
                                    <option value="account_manager" {{ old('role', $user->role) == 'account_manager' ? 'selected' : '' }}>Account Manager</option>
                                    <option value="system_admin" {{ old('role', $user->role) == 'system_admin' ? 'selected' : '' }}>System Admin</option>
                                    <option value="guest" {{ old('role', $user->role) == 'guest' ? 'selected' : '' }}>Guest</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label required">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="account_manager_id" class="form-label">Account Manager</label>
                                <select class="form-select @error('account_manager_id') is-invalid @enderror"
                                        id="account_manager_id" name="account_manager_id">
                                    <option value="">No Account Manager</option>
                                    @foreach($accountManagers as $manager)
                                        <option value="{{ $manager->id }}"
                                                {{ old('account_manager_id', $user->account_manager_id) == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }} ({{ $manager->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('account_manager_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assignment_notes" class="form-label">Assignment Notes</label>
                                <textarea class="form-control @error('assignment_notes') is-invalid @enderror"
                                          id="assignment_notes" name="assignment_notes" rows="2">{{ old('assignment_notes', $user->assignment_notes) }}</textarea>
                                @error('assignment_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Customer Specific Fields -->
                        <div id="customerFields" style="{{ $user->role == 'customer' ? 'display: block;' : 'display: none;' }}">
                            <hr>
                            <h6 class="text-primary mb-3"><i class="fas fa-building me-2"></i>Customer Information</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="lease_start_date" class="form-label">Lease Start Date</label>
                                    <input type="date" class="form-control @error('lease_start_date') is-invalid @enderror"
                                           id="lease_start_date" name="lease_start_date"
                                           value="{{ old('lease_start_date', $user->lease_start_date ? $user->lease_start_date->format('Y-m-d') : '') }}">
                                    @error('lease_start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="billing_frequency" class="form-label">Billing Frequency</label>
                                    <select class="form-select @error('billing_frequency') is-invalid @enderror"
                                            id="billing_frequency" name="billing_frequency">
                                        <option value="monthly" {{ old('billing_frequency', $user->billing_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="quarterly" {{ old('billing_frequency', $user->billing_frequency) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                        <option value="annually" {{ old('billing_frequency', $user->billing_frequency) == 'annually' ? 'selected' : '' }}>Annually</option>
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
                                           id="monthly_rate" name="monthly_rate"
                                           value="{{ old('monthly_rate', $user->monthly_rate) }}">
                                    @error('monthly_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Auto Billing</label>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="auto_billing_enabled"
                                               name="auto_billing_enabled" value="1"
                                               {{ old('auto_billing_enabled', $user->auto_billing_enabled) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="auto_billing_enabled">
                                            Enable automatic billing
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- User Summary Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>User Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                        <h5 class="mt-3 mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-2">{{ $user->email }}</p>
                        <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'customer' ? 'primary' : 'secondary') }} mb-2">
                            {{ ucfirst($user->role) }}
                        </span>
                        <span class="badge bg-{{ $user->status == 'active' ? 'success' : ($user->status == 'inactive' ? 'warning' : 'danger') }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Member Since</span>
                            <strong>{{ $user->created_at->format('M d, Y') }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Last Updated</span>
                            <strong>{{ $user->updated_at->format('M d, Y') }}</strong>
                        </div>
                        @if($user->company_name)
                        <div class="list-group-item">
                            <span class="fw-bold">Company:</span><br>
                            {{ $user->company_name }}
                        </div>
                        @endif
                        @if($user->phone)
                        <div class="list-group-item">
                            <span class="fw-bold">Phone:</span><br>
                            {{ $user->phone }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($user->id !== auth()->id())
                            @if($user->status == 'active')
                                <form action="{{ route('users.update', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="suspended">
                                    <button type="submit" class="btn btn-outline-warning w-100"
                                            onclick="return confirm('Are you sure you want to suspend this user?')">
                                        <i class="fas fa-pause me-2"></i>Suspend User
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('users.update', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="btn btn-outline-success w-100">
                                        <i class="fas fa-play me-2"></i>Activate User
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('users.assign-role', ['user' => $user->id, 'role' => 'admin']) }}"
                               class="btn btn-outline-info"
                               onclick="return confirm('Make this user an admin?')">
                                <i class="fas fa-user-shield me-2"></i>Make Admin
                            </a>

                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100"
                                        onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                    <i class="fas fa-trash me-2"></i>Delete User
                                </button>
                            </form>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle me-2"></i>
                                You cannot modify your own account
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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

        // Form validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
</script>
@endpush

<style>
    .required::after {
        content: " *";
        color: #dc3545;
    }
</style>
