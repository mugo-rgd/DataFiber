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
                   <form action="{{ url('/admin/users') }}" method="POST" id="userCreateForm" enctype="multipart/form-data">
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
                                <div class="email-feedback"></div>
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

                        <!-- Role Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        @php
                                            $roleName = $role;
                                            $roleDisplay = $roleDisplayNames[$roleName] ?? ucwords(str_replace('_', ' ', $roleName));
                                        @endphp
                                        <option value="{{ $roleName }}" {{ old('role') == $roleName ? 'selected' : '' }}>
                                            {{ $roleDisplay }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Account Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('status')
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
                                <div class="form-text text-muted small">
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
                                <div class="form-text text-muted small">
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
                                <div class="form-text text-muted small">
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
                                <div class="password-strength mt-2">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar" id="passwordStrengthBar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted" id="passwordStrengthText">Enter a password</small>
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
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-credit-card me-2"></i>Billing Information
                                </h6>
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
                                       id="country" name="country" value="{{ old('country', 'Kenya') }}"
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

                        <!-- Document Upload Section -->
                        <div class="row mb-3" id="documentsField" style="display: none;">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-file-alt me-2"></i>Required Documents
                                </h6>
                                <div id="documents-container">
                                    <!-- Dynamic document uploads will appear here -->
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addDocumentBtn">
                                    <i class="fas fa-plus me-1"></i>Add Document
                                </button>
                                <div class="form-text text-muted small mt-2">
                                    Upload required documents such as KRA PIN, Business Registration, etc.
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="{{ url('/admin/users') }}" class="btn btn-secondary">
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
                        <p class="small mb-2"><strong>Executive:</strong> Strategic dashboards and high-level analytics</p>
                        <p class="small mb-2"><strong>Technical Administrator:</strong> Network monitoring and infrastructure</p>
                        <p class="small mb-2"><strong>Finance Manager:</strong> Billing, payments, and financial reports</p>
                        <p class="small mb-2"><strong>Network Designer:</strong> Design requests and quotations</p>
                        <p class="small mb-2"><strong>Field Surveyor:</strong> Site surveys and field reports</p>
                        <p class="small mb-2"><strong>Field Technician:</strong> Maintenance and installations</p>
                        <p class="small mb-2"><strong>ICT Engineer:</strong> Technical support and network operations</p>
                        <p class="small mb-2"><strong>County ICT Engineer:</strong> County-specific technical operations</p>
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
                    <ul class="small text-muted mb-0 ps-3">
                        <li>Use strong passwords with mixed characters</li>
                        <li>Verify email addresses before activation</li>
                        <li>Assign appropriate roles based on responsibilities</li>
                        <li>Customers can be assigned to account managers</li>
                        <li>Technical roles benefit from specialization details</li>
                        <li>Set billing information for customer accounts</li>
                        <li>County ICT Engineers require county assignment</li>
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
                            <div class="form-check-item" data-field="status">
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
    border-radius: 4px;
}

.form-check-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
    padding: 0.25rem 0;
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

.toggle-password:hover {
    background-color: #e9ecef;
}

.input-group .form-control:focus + .toggle-password {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

#documents-container .document-item {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
    position: relative;
}

.btn-remove-document {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
}

/* Add to the styles section */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }

    .btn-group {
        flex-wrap: wrap;
        gap: 0.25rem;
    }

    .table-responsive {
        font-size: 0.875rem;
    }

    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }

    .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    .form-check-item small {
        font-size: 0.75rem;
    }
}
</style>
@endpush

// Replace the entire script section with this corrected version:

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const roleSelect = document.getElementById('role');
    const accountManagerField = document.getElementById('accountManagerField');
    const specializationField = document.getElementById('specializationField');
    const billingInfoField = document.getElementById('billingInfoField');
    const countyField = document.getElementById('countyField');
    const documentsField = document.getElementById('documentsField');
    const generatePasswordCheckbox = document.getElementById('generate_password');
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('password_confirmation');
    const progressBar = document.getElementById('progressBar');
    const completedFields = document.getElementById('completedFields');
    const form = document.getElementById('userCreateForm');
    const addDocumentBtn = document.getElementById('addDocumentBtn');
    const documentsContainer = document.getElementById('documents-container');
    const submitBtn = document.getElementById('submitBtn');

    let documentCounter = 0;

    // Initially enable the submit button (don't disable by default)
    if (submitBtn) {
        submitBtn.disabled = false;
    }

    // Role-based field visibility
    function toggleRoleSpecificFields() {
        const role = roleSelect.value;

        // Account Manager field (for customers)
        if (accountManagerField) {
            accountManagerField.style.display = role === 'customer' ? 'block' : 'none';
        }

        // Specialization field (for technical roles)
        const technicalRoles = ['designer', 'surveyor', 'technician', 'ict_engineer'];
        if (specializationField) {
            specializationField.style.display = technicalRoles.includes(role) ? 'block' : 'none';
        }

        // Billing info field (for customers)
        if (billingInfoField) {
            billingInfoField.style.display = role === 'customer' ? 'block' : 'none';
        }

        // County field (for county_ict_engineer role)
        if (countyField) {
            const isCountyEngineer = role === 'county_ict_engineer';
            countyField.style.display = isCountyEngineer ? 'block' : 'none';
            const countySelect = document.getElementById('county_id');
            if (countySelect) {
                countySelect.required = isCountyEngineer;
            }
        }

        // Documents field (for customers)
        if (documentsField) {
            documentsField.style.display = role === 'customer' ? 'block' : 'none';
        }

        // Re-validate form progress after role change
        validateFormProgress();
    }

    if (roleSelect) {
        roleSelect.addEventListener('change', toggleRoleSpecificFields);
        toggleRoleSpecificFields();
    }

    // Password generation
    if (generatePasswordCheckbox) {
        generatePasswordCheckbox.addEventListener('change', function() {
            if (this.checked) {
                const generatedPassword = generateStrongPassword();
                passwordField.value = generatedPassword;
                confirmPasswordField.value = generatedPassword;
                updatePasswordStrength(generatedPassword);
                validatePasswordConfirmation();
                validateFormProgress();
            }
        });
    }

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

        if (!bar || !text) return;

        if (!password) {
            bar.style.width = '0%';
            bar.className = 'progress-bar';
            text.textContent = 'Enter a password';
            return;
        }

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

    if (passwordField) {
        passwordField.addEventListener('input', function() {
            updatePasswordStrength(this.value);
            validateFormProgress();
        });
    }

    // Password confirmation validation
    function validatePasswordConfirmation() {
        if (!passwordField || !confirmPasswordField) return true;

        const password = passwordField.value;
        const confirmPassword = confirmPasswordField.value;

        if (confirmPassword && password !== confirmPassword) {
            confirmPasswordField.classList.add('is-invalid');
            return false;
        } else {
            confirmPasswordField.classList.remove('is-invalid');
            return true;
        }
    }

    if (confirmPasswordField) {
        confirmPasswordField.addEventListener('input', function() {
            validatePasswordConfirmation();
            validateFormProgress();
        });
    }

    // Password visibility toggle
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const targetInput = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (targetInput && icon) {
                if (targetInput.type === 'password') {
                    targetInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    targetInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    });

    // Form progress tracking - UPDATED to NOT disable the button
    function validateFormProgress() {
        const requiredFields = [
            { field: 'name', value: document.getElementById('name')?.value || '' },
            { field: 'email', value: document.getElementById('email')?.value || '' },
            { field: 'role', value: document.getElementById('role')?.value || '' },
            { field: 'status', value: document.getElementById('status')?.value || '' },
            { field: 'password', value: document.getElementById('password')?.value || '' },
            { field: 'password_confirmation', value: document.getElementById('password_confirmation')?.value || '' }
        ];

        let completed = 0;
        requiredFields.forEach(item => {
            const isValid = item.value.trim() !== '';
            const checkItem = document.querySelector(`.form-check-item[data-field="${item.field}"]`);

            if (checkItem) {
                const icon = checkItem.querySelector('i');

                if (isValid) {
                    completed++;
                    checkItem.classList.add('valid');
                    if (icon) {
                        icon.className = 'fas fa-check text-success me-1';
                    }
                } else {
                    checkItem.classList.remove('valid');
                    if (icon) {
                        icon.className = 'fas fa-times text-danger me-1';
                    }
                }
            }
        });

        const progress = (completed / requiredFields.length) * 100;
        if (progressBar) progressBar.style.width = progress + '%';
        if (completedFields) completedFields.textContent = completed;

        // Show warning but DON'T disable the submit button
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            const isPasswordValid = validatePasswordConfirmation();
            const allFieldsFilled = completed === requiredFields.length;

            // Only show visual feedback, don't disable
            if (!allFieldsFilled || !isPasswordValid) {
                submitBtn.classList.add('btn-warning');
                submitBtn.classList.remove('btn-primary');
                if (!allFieldsFilled) {
                    submitBtn.title = 'Please fill all required fields';
                } else if (!isPasswordValid) {
                    submitBtn.title = 'Passwords do not match';
                }
            } else {
                submitBtn.classList.remove('btn-warning');
                submitBtn.classList.add('btn-primary');
                submitBtn.title = 'Create User';
            }
        }
    }

    // Add event listeners for all required fields
    document.querySelectorAll('#userCreateForm input, #userCreateForm select, #userCreateForm textarea').forEach(field => {
        field.addEventListener('input', validateFormProgress);
        field.addEventListener('change', validateFormProgress);
    });

    // Email availability check
    let emailTimeout;
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            clearTimeout(emailTimeout);
            const email = this.value;

            // Remove existing feedback
            const existingFeedback = this.parentElement.querySelector('.email-feedback div');
            if (existingFeedback) existingFeedback.remove();

            if (email && email.includes('@') && email.includes('.')) {
                emailTimeout = setTimeout(() => {
                    fetch(`/admin/users/check-email?email=${encodeURIComponent(email)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                this.classList.add('is-invalid');
                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback d-block';
                                feedback.textContent = 'Email address already exists';
                                this.parentElement.querySelector('.email-feedback')?.appendChild(feedback);
                            } else {
                                this.classList.remove('is-invalid');
                            }
                        })
                        .catch(error => console.error('Error checking email:', error));
                }, 500);
            }
        });
    }

    // Document upload functionality
    function addDocumentField() {
        documentCounter++;
        const documentHtml = `
            <div class="document-item" data-doc-id="${documentCounter}">
                <button type="button" class="btn-close btn-remove-document" aria-label="Remove"></button>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label small">Document Type</label>
                        <select name="documents[${documentCounter}][document_type]" class="form-select form-select-sm" required>
                            <option value="">Select Document Type</option>
                            @php
                                $documentTypes = \App\Models\DocumentType::where('is_active', true)->get();
                            @endphp
                            @foreach($documentTypes as $docType)
                                <option value="{{ $docType->document_type }}">{{ $docType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label small">File</label>
                        <input type="file" name="documents[${documentCounter}][file]" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label small">Description (Optional)</label>
                        <textarea name="documents[${documentCounter}][description]" class="form-control form-control-sm" rows="2" placeholder="Document description"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">Expiry Date (Optional)</label>
                        <input type="date" name="documents[${documentCounter}][expiry_date]" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
        `;

        if (documentsContainer) {
            documentsContainer.insertAdjacentHTML('beforeend', documentHtml);

            const newDoc = documentsContainer.lastElementChild;
            const removeBtn = newDoc.querySelector('.btn-remove-document');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    newDoc.remove();
                });
            }
        }
    }

    if (addDocumentBtn) {
        addDocumentBtn.addEventListener('click', addDocumentField);
    }

    // Initial validation
    validateFormProgress();

    // Form submission - ensure it always works
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                // Double-check password match before submit
                if (!validatePasswordConfirmation()) {
                    e.preventDefault();
                    alert('Please make sure your passwords match.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Create User';
                    return false;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating User...';
            }
            return true;
        });
    }

    // Reset button handler
    const resetBtn = document.querySelector('button[type="reset"]');
    if (resetBtn) {
        resetBtn.addEventListener('click', function(e) {
            setTimeout(() => {
                validateFormProgress();
                if (passwordField) updatePasswordStrength('');
                if (documentsContainer) {
                    documentsContainer.innerHTML = '';
                }
                documentCounter = 0;
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('btn-warning');
                    submitBtn.classList.add('btn-primary');
                }
            }, 100);
        });
    }
});
</script>
@endpush
