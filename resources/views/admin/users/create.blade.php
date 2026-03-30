@extends('layouts.app')

@section('title', 'Create New User - Dark Fibre CRM')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-user-plus text-primary"></i> Create New User
            </h1>
            <p class="text-muted">Add a new user to the Dark Fibre CRM system</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-user me-2"></i>User Information
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST" id="userCreateForm">
                        @csrf

                        <!-- Personal Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required
                                       placeholder="Enter full name"
                                       autocomplete="name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required
                                       placeholder="Enter email address"
                                       autocomplete="email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}"
                                       placeholder="Enter phone number"
                                       autocomplete="tel">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                       id="company_name" name="company_name" value="{{ old('company_name') }}"
                                       placeholder="Enter company name"
                                       autocomplete="organization">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Role and Access -->
                        <div class="row mb-3">
                           <div class="form-group mb-3">
    <label for="role" class="required">Role</label>
    <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
        <option value="">Select Role</option>
        @foreach($roles as $role)
            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                {{ $roleDisplayNames[$role->name] ?? ucwords(str_replace('_', ' ', $role->name)) }}
            </option>
        @endforeach
    </select>
    @error('role')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
                            <div class="col-md-6">
                                <label for="account_status" class="form-label">Account Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('account_status') is-invalid @enderror" id="account_status" name="account_status" required>
                                    <option value="active" {{ old('account_status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('account_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ old('account_status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('account_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
<!-- County Assignment (for county_ict_engineer role) -->
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
                        <!-- Account Manager Assignment (for customers) -->
                        <div class="row mb-3" id="accountManagerField" style="display: none;">
                            <div class="col-12">
                                <label for="account_manager_id" class="form-label">Assign Account Manager</label>
                                <select class="form-select @error('account_manager_id') is-invalid @enderror"
                                        id="account_manager_id" name="account_manager_id">
                                    <option value="">No Account Manager</option>
                                    @foreach($accountManagers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('account_manager_id') == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }} ({{ $manager->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('account_manager_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Optional: Assign an account manager to this customer for better support.
                                </div>
                            </div>
                        </div>

                        <!-- Specialization (for technical roles) -->
                        <div class="row mb-3" id="specializationField" style="display: none;">
                            <div class="col-12">
                                <label for="specialization" class="form-label">Specialization</label>
                                <input type="text" class="form-control @error('specialization') is-invalid @enderror"
                                       id="specialization" name="specialization" value="{{ old('specialization') }}"
                                       placeholder="e.g., Fiber Optic, Network Infrastructure, Wireless">
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Enter the technical specialization for this user.
                                </div>
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password" required
                                           placeholder="Enter password"
                                           autocomplete="new-password">
                                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="password-strength mt-1">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar" id="passwordStrengthBar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted" id="passwordStrengthText">Password strength</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                           id="password_confirmation" name="password_confirmation" required
                                           placeholder="Confirm password"
                                           autocomplete="new-password">
                                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password_confirmation">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="generate_password">
                                    <label class="form-check-label" for="generate_password">
                                        Generate strong password
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Information (for customers) -->
                        <div class="row mb-3" id="billingInfoField" style="display: none;">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">Billing Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="billing_frequency" class="form-label">Billing Frequency</label>
                                        <select class="form-select" id="billing_frequency" name="billing_frequency">
                                            <option value="monthly" {{ old('billing_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                            <option value="quarterly" {{ old('billing_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                            <option value="annually" {{ old('billing_frequency') == 'annually' ? 'selected' : '' }}>Annually</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="monthly_rate" class="form-label">Monthly Rate (KSh)</label>
                                        <input type="number" step="0.01" class="form-control"
                                               id="monthly_rate" name="monthly_rate" value="{{ old('monthly_rate') }}"
                                               placeholder="0.00">
                                    </div>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="auto_billing_enabled" name="auto_billing_enabled" value="1" {{ old('auto_billing_enabled') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_billing_enabled">
                                        Enable automatic billing
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                          id="address" name="address" rows="2"
                                          placeholder="Enter full address"
                                          autocomplete="street-address">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                       id="city" name="city" value="{{ old('city') }}"
                                       placeholder="Enter city"
                                       autocomplete="address-level2">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror"
                                       id="country" name="country" value="{{ old('country') }}"
                                       placeholder="Enter country"
                                       autocomplete="country">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Assignment Notes -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="assignment_notes" class="form-label">Assignment Notes</label>
                                <textarea class="form-control @error('assignment_notes') is-invalid @enderror"
                                          id="assignment_notes" name="assignment_notes" rows="2"
                                          placeholder="Any notes about this user assignment...">{{ old('assignment_notes') }}</textarea>
                                @error('assignment_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Users
                                    </a>
                                    <div>
                                        <button type="reset" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-redo me-2"></i>Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="fas fa-save me-2"></i>Create User
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="col-lg-4">
            <!-- Role Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle me-2"></i>Role Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="role-info">
                        <p class="small mb-2"><strong>System Administrator:</strong> Full system access and user management</p>
                        <p class="small mb-2"><strong>Marketing Administrator:</strong> Analytics, campaigns, and customer insights</p>
                        <p class="small mb-2"><strong>Technical Administrator:</strong> Network monitoring and infrastructure</p>
                        <p class="small mb-2"><strong>Finance Manager:</strong> Billing, payments, and financial reports</p>
                        <p class="small mb-2"><strong>Network Designer:</strong> Design requests and quotations</p>
                        <p class="small mb-2"><strong>Field Surveyor:</strong> Site surveys and field reports</p>
                        <p class="small mb-2"><strong>Field Technician:</strong> Maintenance and installations</p>
                        <p class="small mb-2"><strong>Account Manager:</strong> Customer relationship management</p>
                        <p class="small mb-0"><strong>Customer:</strong> Design requests and service management</p>
                    </div>
                </div>
            </div>

            <!-- Quick Tips -->
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-lightbulb me-2"></i>Quick Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="small text-muted mb-0">
                        <li>Use strong passwords with mixed characters</li>
                        <li>Verify email addresses before activation</li>
                        <li>Assign appropriate roles based on responsibilities</li>
                        <li>Customers can be assigned to account managers</li>
                        <li>Technical roles benefit from specialization details</li>
                        <li>Set billing information for customer accounts</li>
                    </ul>
                </div>
            </div>

            <!-- Form Progress -->
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-tasks me-2"></i>Form Progress
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-progress">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Required Fields</small>
                            <small><span id="completedFields">0</span>/6</small>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-success" id="progressBar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="form-checklist">
                            <div class="form-check-item" data-field="name">
                                <i class="fas fa-times text-danger me-1"></i>
                                <small>Full Name</small>
                            </div>
                            <div class="form-check-item" data-field="email">
                                <i class="fas fa-times text-danger me-1"></i>
                                <small>Email Address</small>
                            </div>
                            <div class="form-check-item" data-field="role">
                                <i class="fas fa-times text-danger me-1"></i>
                                <small>User Role</small>
                            </div>
                            <div class="form-check-item" data-field="account_status">
                                <i class="fas fa-times text-danger me-1"></i>
                                <small>Account Status</small>
                            </div>
                            <div class="form-check-item" data-field="password">
                                <i class="fas fa-times text-danger me-1"></i>
                                <small>Password</small>
                            </div>
                            <div class="form-check-item" data-field="password_confirmation">
                                <i class="fas fa-times text-danger me-1"></i>
                                <small>Confirm Password</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.password-strength .progress {
    background-color: #e9ecef;
}

.form-check-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.25rem;
}

.form-check-item.valid {
    color: #198754;
}

.form-check-item.valid i {
    color: #198754;
}

.role-info p {
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.toggle-password {
    border-left: none;
}

.input-group .form-control:focus + .toggle-password {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const accountManagerField = document.getElementById('accountManagerField');
    const specializationField = document.getElementById('specializationField');
    const billingInfoField = document.getElementById('billingInfoField');
    const generatePasswordCheckbox = document.getElementById('generate_password');
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('password_confirmation');
    const progressBar = document.getElementById('progressBar');
    const completedFields = document.getElementById('completedFields');
    const form = document.getElementById('userCreateForm');

    // Role-based field visibility
    // Add this variable at the top with other field references
const countyField = document.getElementById('countyField');

// Update the toggleRoleSpecificFields function
function toggleRoleSpecificFields() {
    const role = roleSelect.value;

    // Account Manager field (for customers)
    if (role === 'customer') {
        accountManagerField.style.display = 'block';
    } else {
        accountManagerField.style.display = 'none';
    }

    // Specialization field (for technical roles)
    const technicalRoles = ['designer', 'surveyor', 'technician', 'ict_engineer'];
    if (technicalRoles.includes(role)) {
        specializationField.style.display = 'block';
    } else {
        specializationField.style.display = 'none';
    }

    // Billing info field (for customers)
    if (role === 'customer') {
        billingInfoField.style.display = 'block';
    } else {
        billingInfoField.style.display = 'none';
    }

    // County field (for county_ict_engineer role)
    if (role === 'county_ict_engineer') {
        countyField.style.display = 'block';
        // Make county_id required when this role is selected
        document.getElementById('county_id').required = true;
    } else {
        countyField.style.display = 'none';
        document.getElementById('county_id').required = false;
    }
}

    roleSelect.addEventListener('change', toggleRoleSpecificFields);
    toggleRoleSpecificFields(); // Initial call

    // Password generation
    generatePasswordCheckbox.addEventListener('change', function() {
        if (this.checked) {
            const generatedPassword = generateStrongPassword();
            passwordField.value = generatedPassword;
            confirmPasswordField.value = generatedPassword;
            updatePasswordStrength(generatedPassword);
            validatePasswordConfirmation();
        }
    });

    function generateStrongPassword() {
        const length = 12;
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
        let password = "";
        for (let i = 0; i < length; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        return password;
    }

    // Password strength indicator
    function updatePasswordStrength(password) {
        let strength = 0;
        const bar = document.getElementById('passwordStrengthBar');
        const text = document.getElementById('passwordStrengthText');

        if (password.length >= 8) strength += 25;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 25;
        if (password.match(/\d/)) strength += 25;
        if (password.match(/[!@#$%^&*]/)) strength += 25;

        bar.style.width = strength + '%';

        if (strength < 50) {
            bar.className = 'progress-bar bg-danger';
            text.textContent = 'Weak password';
        } else if (strength < 75) {
            bar.className = 'progress-bar bg-warning';
            text.textContent = 'Medium password';
        } else {
            bar.className = 'progress-bar bg-success';
            text.textContent = 'Strong password';
        }
    }

    passwordField.addEventListener('input', function() {
        updatePasswordStrength(this.value);
        validateFormProgress();
    });

    // Password confirmation validation
    function validatePasswordConfirmation() {
        const password = passwordField.value;
        const confirmPassword = confirmPasswordField.value;

        if (confirmPassword && password !== confirmPassword) {
            confirmPasswordField.classList.add('is-invalid');
        } else {
            confirmPasswordField.classList.remove('is-invalid');
        }
    }

    confirmPasswordField.addEventListener('input', validatePasswordConfirmation);

    // Password visibility toggle
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const targetInput = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                targetInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Form progress tracking
    function validateFormProgress() {
        const requiredFields = [
            { field: 'name', value: document.getElementById('name').value },
            { field: 'email', value: document.getElementById('email').value },
            { field: 'role', value: document.getElementById('role').value },
            { field: 'account_status', value: document.getElementById('account_status').value },
            { field: 'password', value: document.getElementById('password').value },
            { field: 'password_confirmation', value: document.getElementById('password_confirmation').value }
        ];

        let completed = 0;
        requiredFields.forEach(item => {
            const isValid = item.value.trim() !== '';
            const checkItem = document.querySelector(`[data-field="${item.field}"]`);

            if (isValid) {
                completed++;
                checkItem.classList.add('valid');
                checkItem.innerHTML = '<i class="fas fa-check text-success me-1"></i><small>' + checkItem.textContent + '</small>';
            } else {
                checkItem.classList.remove('valid');
                checkItem.innerHTML = '<i class="fas fa-times text-danger me-1"></i><small>' + checkItem.textContent + '</small>';
            }
        });

        const progress = (completed / requiredFields.length) * 100;
        progressBar.style.width = progress + '%';
        completedFields.textContent = completed;

        // Update submit button state
        const submitBtn = document.getElementById('submitBtn');
        if (completed === requiredFields.length) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }

    // Add event listeners for all required fields
    document.querySelectorAll('#userCreateForm input, #userCreateForm select').forEach(field => {
        field.addEventListener('input', validateFormProgress);
        field.addEventListener('change', validateFormProgress);
    });

    // Initial validation
    validateFormProgress();

    // Form submission loading state
    form.addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating User...';
    });
});
</script>
@endpush
@endsection
