@extends('layouts.app')

@section('title', 'Edit User: ' . $user->name)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 text-primary">
                <i class="fas fa-user-edit me-2"></i>Edit User: {{ $user->name }}
            </h1>
            <p class="text-muted mb-0">Update user information and settings</p>
        </div>
        <a href="{{ route('admin.users') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Users
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>

                        <div class="form-group mb-3">
                            <label for="name" class="required">Full Name</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="company_name">Company Name</label>
                            <input type="text" name="company_name" id="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $user->company_name) }}">
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email" class="required">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="phone">Phone Number</label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
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
                                <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Customer</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="finance" {{ old('role', $user->role) == 'finance' ? 'selected' : '' }}>Finance</option>
                                <option value="designer" {{ old('role', $user->role) == 'designer' ? 'selected' : '' }}>Designer</option>
                                <option value="surveyor" {{ old('role', $user->role) == 'surveyor' ? 'selected' : '' }}>Surveyor</option>
                                <option value="technician" {{ old('role', $user->role) == 'technician' ? 'selected' : '' }}>Technician</option>
                                <option value="account_manager" {{ old('role', $user->role) == 'account_manager' ? 'selected' : '' }}>Account Manager</option>
                                <option value="system_admin" {{ old('role', $user->role) == 'system_admin' ? 'selected' : '' }}>System Admin</option>
                                <option value="debt_manager" {{ old('role', $user->role) == 'debt_manager' ? 'selected' : '' }}>Debt Manager</option>
                                <option value="ict_engineer" {{ old('role', $user->role) == 'ict_engineer' ? 'selected' : '' }}>ICT Engineer</option>
                                <option value="guest" {{ old('role', $user->role) == 'guest' ? 'selected' : '' }}>Guest</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="status" class="required">Status</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="">Select Status</option>
                                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password">Password (Leave blank to keep current)</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                        </div>
                        <!-- In your edit.blade.php -->
<div class="row mb-3" id="countyField" style="display: {{ $user->role == 'county_ict_engineer' ? 'block' : 'none' }};">
    <div class="col-md-6">
        <label for="county_id" class="form-label">County {{ $user->role == 'county_ict_engineer' ? '<span class="text-danger">*</span>' : '' }}</label>
        <select class="form-select @error('county_id') is-invalid @enderror"
                id="county_id" name="county_id" {{ $user->role == 'county_ict_engineer' ? 'required' : '' }}>
            <option value="">Select County</option>
            @foreach($counties as $county)
                <option value="{{ $county->id }}" {{ old('county_id', $user->county_id) == $county->id ? 'selected' : '' }}>
                    {{ $county->name }}
                </option>
            @endforeach
        </select>
        @error('county_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="county_notes" class="form-label">County Notes</label>
        <textarea class="form-control @error('county_notes') is-invalid @enderror"
                  id="county_notes" name="county_notes" rows="2">{{ old('county_notes', $user->county_notes) }}</textarea>
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
                                        <option value="{{ $manager->id }}" {{ old('account_manager_id', $user->account_manager_id) == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('account_manager_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Customer Specific Fields -->
                <div id="customerFields" style="display: {{ $user->role == 'customer' ? 'block' : 'none' }};">
                    <hr>
                    <h6 class="text-primary mb-3"><i class="fas fa-file-contract me-2"></i>Customer Information</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="lease_start_date">Lease Start Date</label>
                                <input type="date" name="lease_start_date" id="lease_start_date" class="form-control @error('lease_start_date') is-invalid @enderror"
                                    value="{{ old('lease_start_date', optional($user->customerDetails)->lease_start_date?->format('Y-m-d')) }}">
                                @error('lease_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="billing_frequency">Billing Frequency</label>
                                <select name="billing_frequency" id="billing_frequency" class="form-control @error('billing_frequency') is-invalid @enderror">
                                    <option value="">Select Frequency</option>
                                    <option value="monthly" {{ old('billing_frequency', optional($user->customerDetails)->billing_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('billing_frequency', optional($user->customerDetails)->billing_frequency) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                    <option value="annually" {{ old('billing_frequency', optional($user->customerDetails)->billing_frequency) == 'annually' ? 'selected' : '' }}>Annually</option>
                                </select>
                                @error('billing_frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="monthly_rate">Monthly Rate</label>
                                <input type="number" name="monthly_rate" id="monthly_rate" class="form-control @error('monthly_rate') is-invalid @enderror"
                                    step="0.01" min="0" value="{{ old('monthly_rate', optional($user->customerDetails)->monthly_rate) }}">
                                @error('monthly_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="auto_billing_enabled" id="auto_billing_enabled" class="form-check-input @error('auto_billing_enabled') is-invalid @enderror"
                            value="1" {{ old('auto_billing_enabled', optional($user->customerDetails)->auto_billing_enabled ?? true) ? 'checked' : '' }}>
                        <label for="auto_billing_enabled" class="form-check-label">Enable Auto Billing</label>
                        @error('auto_billing_enabled')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Documents Section -->
                @if($documentTypes && $documentTypes->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-file-upload me-2"></i>Documents</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Documents can be managed from the user's profile page.</p>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info">
                            <i class="fas fa-folder-open me-1"></i>Manage User Documents
                        </a>
                    </div>
                </div>
                @endif

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update User
                    </button>
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide customer fields based on role selection
    const roleSelect = document.getElementById('role');
    const customerFields = document.getElementById('customerFields');

    function toggleCustomerFields() {
        if (roleSelect && customerFields) {
            if (roleSelect.value === 'customer') {
                customerFields.style.display = 'block';
            } else {
                customerFields.style.display = 'none';
            }
        }
    }

    if (roleSelect && customerFields) {
        roleSelect.addEventListener('change', toggleCustomerFields);
    }
    document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const countyField = document.getElementById('countyField');
    const accountManagerField = document.getElementById('accountManagerField');
    const billingInfoField = document.getElementById('billingInfoField');

    function toggleRoleSpecificFields() {
        const role = roleSelect.value;

        // County field
        if (role === 'county_ict_engineer') {
            countyField.style.display = 'block';
            document.getElementById('county_id').required = true;
        } else {
            countyField.style.display = 'none';
            document.getElementById('county_id').required = false;
        }

        // Account Manager field
        if (role === 'customer') {
            accountManagerField.style.display = 'block';
        } else {
            accountManagerField.style.display = 'none';
        }

        // Billing info field
        if (role === 'customer') {
            billingInfoField.style.display = 'block';
        } else {
            billingInfoField.style.display = 'none';
        }
    }

    roleSelect.addEventListener('change', toggleRoleSpecificFields);
    // Initial call on page load
    toggleRoleSpecificFields();
});
</script>
@endpush

<style>
.required::after {
    content: " *";
    color: #dc3545;
}
</style>
@endsection
