{{-- resources/views/customer/profile-create.blade.php --}}
@extends('layouts.app')

@section('title', 'Complete Company Profile')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-warning shadow-lg">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                        <div>
                            <h4 class="mb-0">Complete Your Company Profile</h4>
                            <p class="mb-0">Required to access all features</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Important:</strong> Please complete your company profile and upload all required documents in PDF format to proceed.
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('customer.profile.store') }}" enctype="multipart/form-data" id="profileForm">
                        @csrf

                        <!-- Progress Indicator -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Step 1 of 4</small>
                                <small class="text-muted">Company Details</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: 25%"></div>
                            </div>
                        </div>

                        <!-- Company Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 text-primary">
                                    <i class="fas fa-building me-2"></i>Company Information
                                </h5>
                                <p class="text-muted mb-3">Basic details about your company</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kra_pin" class="form-label">KRA Pin *</label>
                                <input type="text" class="form-control @error('kra_pin') is-invalid @enderror"
                                       id="kra_pin" name="kra_pin" value="{{ old('kra_pin') }}"
                                       placeholder="Enter KRA Pin" required>
                                @error('kra_pin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">Phone Number *</label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                       id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                                       placeholder="Enter phone number" required>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="registration_number" class="form-label">Registration Number *</label>
                                <input type="text" class="form-control @error('registration_number') is-invalid @enderror"
                                       id="registration_number" name="registration_number" value="{{ old('registration_number') }}"
                                       placeholder="Enter registration number" required>
                                @error('registration_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="company_type" class="form-label">Company Type *</label>
                                <select class="form-select @error('company_type') is-invalid @enderror"
                                        id="company_type" name="company_type" required>
                                    <option value="">Select Company Type</option>
                                    <option value="public" {{ old('company_type') == 'public' ? 'selected' : '' }}>Public Limited Company</option>
                                    <option value="parastatal" {{ old('company_type') == 'parastatal' ? 'selected' : '' }}>Parastatal</option>
                                    <option value="county government" {{ old('company_type') == 'county government' ? 'selected' : '' }}>County Government</option>
                                    <option value="private" {{ old('company_type') == 'private' ? 'selected' : '' }}>Private Limited Company</option>
                                    <option value="NGO" {{ old('company_type') == 'NGO' ? 'selected' : '' }}>Non-Governmental Organization</option>
                                </select>
                                @error('company_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Persons -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 text-primary">
                                    <i class="fas fa-users me-2"></i>Contact Persons
                                </h5>
                                <p class="text-muted mb-3">Primary and secondary contacts for your company</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contact_name_1" class="form-label">Primary Contact Name *</label>
                                <input type="text" class="form-control @error('contact_name_1') is-invalid @enderror"
                                       id="contact_name_1" name="contact_name_1" value="{{ old('contact_name_1') }}"
                                       placeholder="Full name of primary contact" required>
                                @error('contact_name_1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contact_phone_1" class="form-label">Primary Contact Phone *</label>
                                <input type="text" class="form-control @error('contact_phone_1') is-invalid @enderror"
                                       id="contact_phone_1" name="contact_phone_1" value="{{ old('contact_phone_1') }}"
                                       placeholder="Phone number of primary contact" required>
                                @error('contact_phone_1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contact_name_2" class="form-label">Secondary Contact Name</label>
                                <input type="text" class="form-control @error('contact_name_2') is-invalid @enderror"
                                       id="contact_name_2" name="contact_name_2" value="{{ old('contact_name_2') }}"
                                       placeholder="Full name of secondary contact">
                                @error('contact_name_2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contact_phone_2" class="form-label">Secondary Contact Phone</label>
                                <input type="text" class="form-control @error('contact_phone_2') is-invalid @enderror"
                                       id="contact_phone_2" name="contact_phone_2" value="{{ old('contact_phone_2') }}"
                                       placeholder="Phone number of secondary contact">
                                @error('contact_phone_2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 text-primary">
                                    <i class="fas fa-map-marker-alt me-2"></i>Address Information
                                </h5>
                                <p class="text-muted mb-3">Physical and postal address details</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="physical_location" class="form-label">Physical Location *</label>
                                <input type="text" class="form-control @error('physical_location') is-invalid @enderror"
                                       id="physical_location" name="physical_location" value="{{ old('physical_location') }}"
                                       placeholder="Building name, floor, office number" required>
                                @error('physical_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="road" class="form-label">Road/Street *</label>
                                <input type="text" class="form-control @error('road') is-invalid @enderror"
                                       id="road" name="road" value="{{ old('road') }}"
                                       placeholder="Road or street name" required>
                                @error('road')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="town" class="form-label">Town/City *</label>
                                <input type="text" class="form-control @error('town') is-invalid @enderror"
                                       id="town" name="town" value="{{ old('town') }}"
                                       placeholder="Town or city" required>
                                @error('town')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="address" class="form-label">Postal Address *</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                       id="address" name="address" value="{{ old('address') }}"
                                       placeholder="P.O. Box number" required>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="code" class="form-label">Postal Code *</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                       id="code" name="code" value="{{ old('code') }}"
                                       placeholder="Postal code" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Document Uploads -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 text-danger">
                                    <i class="fas fa-file-pdf me-2"></i>Required Documents
                                </h5>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Important:</strong> All documents must be uploaded in PDF format (max 5MB each). You cannot proceed without these documents.
                                </div>
                                <p class="text-muted mb-3">Upload clear scanned copies of the following documents</p>
                            </div>
                        </div>

                        <!-- Document Upload Grid -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-light">
                                    <div class="card-body">
                                        <label for="kra_pin_certificate" class="form-label fw-bold">KRA Pin Certificate *</label>
                                        <input type="file" class="form-control @error('kra_pin_certificate') is-invalid @enderror"
                                               id="kra_pin_certificate" name="kra_pin_certificate" accept=".pdf" required>
                                        @error('kra_pin_certificate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Current KRA PIN certificate in PDF format</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-light">
                                    <div class="card-body">
                                        <label for="business_registration_certificate" class="form-label fw-bold">Business Registration Certificate *</label>
                                        <input type="file" class="form-control @error('business_registration_certificate') is-invalid @enderror"
                                               id="business_registration_certificate" name="business_registration_certificate" accept=".pdf" required>
                                        @error('business_registration_certificate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Certificate of incorporation or business registration</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-light">
                                    <div class="card-body">
                                        <label for="id_copy" class="form-label fw-bold">Trade Licence *</label>
                                        <input type="file" class="form-control @error('id_copy') is-invalid @enderror"
                                               id="id_copy" name="id_copy" accept=".pdf" required>
                                        @error('id_copy')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Valid trade/business licence</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-light">
                                    <div class="card-body">
                                        <label for="ca_licence" class="form-label fw-bold">CA Licence *</label>
                                        <input type="file" class="form-control @error('ca_licence') is-invalid @enderror"
                                               id="ca_licence" name="ca_licence" accept=".pdf" required>
                                        @error('ca_licence')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Communication Authority of Kenya licence</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-light">
                                    <div class="card-body">
                                        <label for="tax_compliance_certificate" class="form-label fw-bold">Tax Compliance Certificate *</label>
                                        <input type="file" class="form-control @error('tax_compliance_certificate') is-invalid @enderror"
                                               id="tax_compliance_certificate" name="tax_compliance_certificate" accept=".pdf" required>
                                        @error('tax_compliance_certificate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Valid tax compliance certificate from KRA</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-light">
                                    <div class="card-body">
                                        <label for="cr12_certificate" class="form-label fw-bold">CR12 Certificate *</label>
                                        <input type="file" class="form-control @error('cr12_certificate') is-invalid @enderror"
                                               id="cr12_certificate" name="cr12_certificate" accept=".pdf" required>
                                        @error('cr12_certificate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Current CR12 from Registrar of Companies</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Optional Documents -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 text-success">
                                    <i class="fas fa-file-upload me-2"></i>Additional Information
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="card border-light">
                                    <div class="card-body">
                                        <label for="other_documents" class="form-label fw-bold">Other Supporting Documents (Optional)</label>
                                        <input type="file" class="form-control"
                                               id="other_documents" name="other_documents[]" accept=".pdf" multiple>
                                        <small class="form-text text-muted">Additional supporting documents in PDF format (max 5MB each)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Photo -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 text-success">
                                    <i class="fas fa-camera me-2"></i>Company Branding
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="card border-light">
                                    <div class="card-body">
                                        <label for="profile_photo" class="form-label fw-bold">Company Logo/Photo</label>
                                        <input type="file" class="form-control @error('profile_photo') is-invalid @enderror"
                                               id="profile_photo" name="profile_photo" accept="image/*">
                                        @error('profile_photo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">JPG, PNG, or GIF format (max 2MB). Recommended size: 300x300 pixels</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-light">
                                    <div class="card-body">
                                        <label for="description" class="form-label fw-bold">Company Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                                  id="description" name="description" rows="4"
                                                  placeholder="Brief description of your company's activities, mission, and values...">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Tell us about your company (optional)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Section -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg py-3">
                                        <i class="fas fa-check-circle me-2"></i>Complete Profile & Continue
                                    </button>
                                </div>
                                <p class="text-muted text-center mt-3">
                                    <small>
                                        <i class="fas fa-shield-alt me-1"></i>
                                        By submitting, you confirm that all information provided is accurate and up-to-date
                                    </small>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('profileForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
});
</script>
@endsection
