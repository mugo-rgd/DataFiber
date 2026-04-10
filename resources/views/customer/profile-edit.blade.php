@extends('layouts.app')

@section('title', 'Edit Company Profile - Customer')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-xxl-10">

            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h2 text-primary mb-1">
                        <i class="fas fa-building me-2"></i>Edit Company Profile
                    </h1>
                    <p class="text-muted mb-0">Manage your company information and documents</p>
                </div>

                <a href="{{ route('customer.customer-dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
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

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Company Profile Edit Form -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i>Company Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information Section -->
                        <div class="row">
                            <div class="col-12 mb-4">
                                <h6 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-info-circle me-2"></i>Basic Information
                                </h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label required-field">Company Name</label>
                                <input type="text" name="company_name" id="company_name"
                                       value="{{ old('company_name', $companyProfile->company_name ?? Auth::user()->name ?? '') }}"
                                       class="form-control @error('company_name') is-invalid @enderror"
                                       placeholder="Enter your company name" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="company_email" class="form-label">Company Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="company_email" id="company_email"
                                           value="{{ old('company_email', Auth::user()->email ?? '') }}"
                                           class="form-control @error('company_email') is-invalid @enderror"
                                           placeholder="company@example.com">
                                </div>
                                @error('company_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="kra_pin" class="form-label required-field">KRA PIN</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-receipt"></i></span>
                                    <input type="text" name="kra_pin" id="kra_pin"
                                           value="{{ old('kra_pin', $companyProfile->kra_pin ?? '') }}"
                                           class="form-control @error('kra_pin') is-invalid @enderror"
                                           placeholder="A123456789Z" required>
                                </div>
                                <small class="form-text text-muted">Format: A followed by 9 digits and ending with Z (e.g., A123456789Z)</small>
                                @error('kra_pin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label required-field">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" name="phone_number" id="phone_number"
                                           value="{{ old('phone_number', $companyProfile->phone_number ?? '') }}"
                                           class="form-control @error('phone_number') is-invalid @enderror"
                                           placeholder="+254 XXX XXX XXX" required>
                                </div>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="registration_number" class="form-label required-field">Registration Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="registration_number" id="registration_number"
                                           value="{{ old('registration_number', $companyProfile->registration_number ?? '') }}"
                                           class="form-control @error('registration_number') is-invalid @enderror"
                                           placeholder="Company registration certificate number" required>
                                </div>
                                @error('registration_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- SAP Account - READ ONLY (not editable) -->
                            <div class="col-md-6 mb-3">
                                <label for="sap_account" class="form-label">SAP Account</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="text" name="sap_account" id="sap_account"
                                           value="{{ old('sap_account', $companyProfile->sap_account ?? 'Not Assigned') }}"
                                           class="form-control bg-light"
                                           readonly disabled>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    SAP account is system-generated and cannot be edited
                                </small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="company_type" class="form-label required-field">Company Type</label>
                                <select name="company_type" id="company_type" class="form-select @error('company_type') is-invalid @enderror" required>
                                    <option value="">Select Company Type</option>
                                    <option value="public" {{ old('company_type', $companyProfile->company_type ?? '') == 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="parastatal" {{ old('company_type', $companyProfile->company_type ?? '') == 'parastatal' ? 'selected' : '' }}>Parastatal</option>
                                    <option value="county government" {{ old('company_type', $companyProfile->company_type ?? '') == 'county government' ? 'selected' : '' }}>County Government</option>
                                    <option value="private" {{ old('company_type', $companyProfile->company_type ?? '') == 'private' ? 'selected' : '' }}>Private</option>
                                    <option value="NGO" {{ old('company_type', $companyProfile->company_type ?? '') == 'NGO' ? 'selected' : '' }}>NGO</option>
                                </select>
                                @error('company_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Persons Section -->
                        <div class="row mt-3">
                            <div class="col-12 mb-4">
                                <h6 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-users me-2"></i>Contact Persons
                                </h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contact_name_1" class="form-label required-field">Primary Contact Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="contact_name_1" id="contact_name_1"
                                           value="{{ old('contact_name_1', $companyProfile->contact_name_1 ?? '') }}"
                                           class="form-control @error('contact_name_1') is-invalid @enderror"
                                           placeholder="Full name of primary contact" required>
                                </div>
                                @error('contact_name_1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contact_phone_1" class="form-label required-field">Primary Contact Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                    <input type="text" name="contact_phone_1" id="contact_phone_1"
                                           value="{{ old('contact_phone_1', $companyProfile->contact_phone_1 ?? '') }}"
                                           class="form-control @error('contact_phone_1') is-invalid @enderror"
                                           placeholder="+254 XXX XXX XXX" required>
                                </div>
                                @error('contact_phone_1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contact_name_2" class="form-label required-field">Secondary Contact Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="contact_name_2" id="contact_name_2"
                                           value="{{ old('contact_name_2', $companyProfile->contact_name_2 ?? '') }}"
                                           class="form-control @error('contact_name_2') is-invalid @enderror"
                                           placeholder="Full name of secondary contact" required>
                                </div>
                                @error('contact_name_2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contact_phone_2" class="form-label">Secondary Contact Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                    <input type="text" name="contact_phone_2" id="contact_phone_2"
                                           value="{{ old('contact_phone_2', $companyProfile->contact_phone_2 ?? '') }}"
                                           class="form-control @error('contact_phone_2') is-invalid @enderror"
                                           placeholder="+254 XXX XXX XXX">
                                </div>
                                @error('contact_phone_2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Address Information Section -->
                        <div class="row mt-3">
                            <div class="col-12 mb-4">
                                <h6 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-map-marker-alt me-2"></i>Address Information
                                </h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="physical_location" class="form-label required-field">Physical Location</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-location-dot"></i></span>
                                    <input type="text" name="physical_location" id="physical_location"
                                           value="{{ old('physical_location', $companyProfile->physical_location ?? '') }}"
                                           class="form-control @error('physical_location') is-invalid @enderror"
                                           placeholder="e.g., Upper Hill, Nairobi" required>
                                </div>
                                @error('physical_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="road" class="form-label required-field">Road/Street</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-road"></i></span>
                                    <input type="text" name="road" id="road"
                                           value="{{ old('road', $companyProfile->road ?? '') }}"
                                           class="form-control @error('road') is-invalid @enderror"
                                           placeholder="e.g., Mombasa Road" required>
                                </div>
                                @error('road')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="town" class="form-label required-field">Town/City</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                                    <input type="text" name="town" id="town"
                                           value="{{ old('town', $companyProfile->town ?? '') }}"
                                           class="form-control @error('town') is-invalid @enderror"
                                           placeholder="e.g., Nairobi" required>
                                </div>
                                @error('town')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label required-field">Postal Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-mail-bulk"></i></span>
                                    <input type="text" name="address" id="address"
                                           value="{{ old('address', $companyProfile->address ?? '') }}"
                                           class="form-control @error('address') is-invalid @enderror"
                                           placeholder="P.O. Box 12345" required>
                                </div>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label required-field">Postal Code</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    <input type="text" name="code" id="code"
                                           value="{{ old('code', $companyProfile->code ?? '') }}"
                                           class="form-control @error('code') is-invalid @enderror"
                                           placeholder="e.g., 00100" required>
                                </div>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Information Section -->
                        <div class="row mt-3">
                            <div class="col-12 mb-4">
                                <h6 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-file-alt me-2"></i>Additional Information
                                </h6>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="description" class="form-label required-field">Company Description</label>
                                <textarea name="description" id="description"
                                          class="form-control @error('description') is-invalid @enderror"
                                          rows="4"
                                          placeholder="Describe your company's core business, services, and products...">{{ old('description', $companyProfile->description ?? '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="profile_photo" class="form-label">Company Logo/Profile Photo</label>
                                <input type="file" name="profile_photo" id="profile_photo"
                                       class="form-control @error('profile_photo') is-invalid @enderror"
                                       accept="image/jpeg,image/png,image/jpg,image/gif">
                                @if(isset($companyProfile->profile_photo) && $companyProfile->profile_photo)
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($companyProfile->profile_photo) }}"
                                             alt="Current Logo"
                                             style="max-height: 100px; max-width: 200px;"
                                             class="img-thumbnail">
                                        <small class="d-block text-muted mt-1">Current logo. Upload new to replace.</small>
                                    </div>
                                @endif
                                @error('profile_photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="text-end mt-4 pt-3 border-top">
                            <button type="reset" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-undo me-1"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Upload New Documents Section -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-upload me-2"></i>Upload New Documents
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('customer.documents.store', ['customer' => auth()->id()]) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        <div class="upload-area mb-4" id="uploadArea">
                            <div class="upload-area-content">
                                <div class="document-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <h5>Drag & Drop or Click to Upload</h5>
                                <p class="text-muted mb-3">Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG</p>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="document_type" class="form-label required-field">Document Type</label>
                                        <select name="document_type" id="document_type" class="form-select" required>
                                            <option value="">Select Document Type</option>
                                            @isset($documentTypes)
                                                @foreach($documentTypes as $documentType)
                                                    <option value="{{ $documentType->document_type }}"
                                                            data-max-size="{{ $documentType->max_file_size }}"
                                                            data-extensions="{{ json_encode($documentType->allowed_extensions) }}"
                                                            {{ old('document_type') == $documentType->document_type ? 'selected' : '' }}>
                                                        {{ $documentType->name }}
                                                        @if($documentType->is_required)
                                                            <span class="badge bg-danger required-badge">Required</span>
                                                        @endif
                                                    </option>
                                                @endforeach
                                            @endisset
                                        </select>
                                        @error('document_type')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="document_file" class="form-label required-field">Select File</label>
                                        <input type="file" name="document_file" id="document_file"
                                               class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                                        <div class="form-text" id="fileRequirements">
                                            Maximum file size: 2MB | Allowed formats: PDF, DOC, DOCX, JPG, JPEG, PNG
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="fileInfo" class="file-info" style="display: none;">
                                <i class="fas fa-file me-2"></i>
                                <span id="fileName"></span>
                                <span id="fileSize" class="text-muted ms-2"></span>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-upload me-1"></i>Upload Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Uploaded Documents Section -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-folder me-2"></i>Uploaded Documents
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(isset($documents) && count($documents) > 0)
                        @foreach($documents as $documentType => $docs)
                            <div class="document-type-section">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-primary mb-0">
                                        <i class="fas fa-folder-open me-2"></i>
                                        {{ ucwords(str_replace('_', ' ', $documentType)) }}
                                    </h6>
                                    <span class="badge bg-primary rounded-pill">{{ count($docs) }}</span>
                                </div>

                                <div class="row g-3">
                                    @foreach($docs as $document)
                                        <div class="col-xl-6">
                                            <div class="card document-card h-100" id="document-{{ $document->id }}">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div class="flex-grow-1">
                                                            <h6 class="card-title text-truncate mb-1">
                                                                @php
                                                                    $icon = 'file';
                                                                    if (str_contains($document->file_name, '.pdf')) {
                                                                        $icon = 'file-pdf';
                                                                    } elseif (str_contains($document->file_name, ['.doc', '.docx'])) {
                                                                        $icon = 'file-word';
                                                                    } elseif (str_contains($document->file_name, ['.jpg', '.jpeg', '.png', '.gif'])) {
                                                                        $icon = 'file-image';
                                                                    }
                                                                @endphp
                                                                <i class="fas fa-{{ $icon }} text-danger me-2"></i>
                                                                {{ $document->file_name }}
                                                            </h6>
                                                            <div class="d-flex flex-wrap gap-2 mb-2">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-calendar me-1"></i>
                                                                    {{ $document->created_at->format('M d, Y') }}
                                                                </small>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-weight me-1"></i>
                                                                    {{ round($document->file_size / 1024, 1) }} KB
                                                                </small>
                                                                @if($document->status)
                                                                    <span class="badge bg-success">{{ ucfirst($document->status) }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="action-buttons d-flex justify-content-end">
                                                        <form action="{{ route('customer.documents.profile.destroy', $document->id) }}" method="POST" class="d-inline delete-document-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Delete Document">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <div class="document-icon text-muted">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <h5 class="text-muted mb-3">No Documents Uploaded</h5>
                            <p class="text-muted mb-4">Get started by uploading your first document</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .document-card {
        border-left: 4px solid #007bff;
        transition: transform 0.2s ease-in-out;
    }
    .document-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .document-type-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        border: 1px solid #dee2e6;
    }
    .upload-area {
        border: 2px dashed #007bff;
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .upload-area:hover {
        background: #e3f2fd;
        border-color: #0056b3;
    }
    .upload-area-content {
        pointer-events: none;
    }
    .file-info {
        background: #e7f3ff;
        border-radius: 6px;
        padding: 10px 15px;
        margin-top: 10px;
    }
    .action-buttons .btn {
        margin-left: 5px;
    }
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    .document-icon {
        font-size: 2rem;
        color: #007bff;
        margin-bottom: 10px;
    }
    .form-control, .form-select {
        pointer-events: auto;
    }
    .required-badge {
        font-size: 0.7em;
        margin-left: 5px;
    }
    .border-bottom {
        border-bottom: 2px solid #e9ecef !important;
        padding-bottom: 8px;
    }
    input:read-only, input:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
    }
    .img-thumbnail {
        max-width: 150px;
        padding: 5px;
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

        // Auto-dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            });
        }, 5000);

        // KRA PIN validation
        const kraPinInput = document.getElementById('kra_pin');
        if (kraPinInput) {
            kraPinInput.addEventListener('input', function(e) {
                let value = e.target.value.toUpperCase();
                const pattern = /^A\d{9}Z$/;
                if (value && !pattern.test(value)) {
                    e.target.setCustomValidity('KRA PIN must be in format: A123456789Z');
                } else {
                    e.target.setCustomValidity('');
                }
            });
        }

        // Phone number validation for Kenyan numbers
        const phoneInputs = document.querySelectorAll('#phone_number, #contact_phone_1, #contact_phone_2');
        phoneInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                let value = e.target.value;
                const pattern = /^(254|\+254|0)?[17]\d{8}$/;
                if (value && !pattern.test(value)) {
                    e.target.setCustomValidity('Please enter a valid Kenyan phone number');
                } else {
                    e.target.setCustomValidity('');
                }
            });
        });

        // Initialize file requirements display
        updateFileRequirements();

        // Add event listeners for delete forms
        initializeDeleteHandlers();
    });

    // Update file requirements when document type changes
    const documentTypeSelect = document.getElementById('document_type');
    if (documentTypeSelect) {
        documentTypeSelect.addEventListener('change', function() {
            updateFileRequirements();
        });
    }

    function updateFileRequirements() {
        const documentTypeSelect = document.getElementById('document_type');
        const fileRequirements = document.getElementById('fileRequirements');

        if (documentTypeSelect && documentTypeSelect.selectedIndex > 0) {
            const selectedOption = documentTypeSelect.options[documentTypeSelect.selectedIndex];
            const maxSize = selectedOption.getAttribute('data-max-size');
            const extensions = JSON.parse(selectedOption.getAttribute('data-extensions') || '[]');

            let requirementsText = `Maximum file size: ${maxSize}KB`;
            if (extensions.length > 0) {
                requirementsText += ` | Allowed formats: ${extensions.join(', ')}`;
            }

            if (fileRequirements) {
                fileRequirements.textContent = requirementsText;
            }
        } else if (fileRequirements) {
            fileRequirements.textContent = 'Maximum file size: 2MB | Allowed formats: PDF, DOC, DOCX, JPG, JPEG, PNG';
        }
    }

    // File size validation and info display
    const fileInput = document.getElementById('document_file');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const documentTypeSelect = document.getElementById('document_type');
            const selectedOption = documentTypeSelect ? documentTypeSelect.options[documentTypeSelect.selectedIndex] : null;
            const maxSize = (selectedOption && selectedOption.value) ? (selectedOption.getAttribute('data-max-size') * 1024) : (2 * 1024 * 1024);
            const allowedExtensions = (selectedOption && selectedOption.value) ? JSON.parse(selectedOption.getAttribute('data-extensions') || '[]') : ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            const fileInfo = document.getElementById('fileInfo');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');

            if (file) {
                // Check file size
                if (file.size > maxSize) {
                    alert(`File size must be less than ${maxSize / 1024}KB`);
                    e.target.value = '';
                    if (fileInfo) fileInfo.style.display = 'none';
                    return;
                }

                // Check file extension
                const fileExtension = file.name.split('.').pop().toLowerCase();
                if (allowedExtensions.length > 0 && !allowedExtensions.includes(fileExtension)) {
                    alert(`File type not allowed. Allowed types: ${allowedExtensions.join(', ')}`);
                    e.target.value = '';
                    if (fileInfo) fileInfo.style.display = 'none';
                    return;
                }

                // Display file info
                if (fileName) fileName.textContent = file.name;
                if (fileSize) fileSize.textContent = `(${(file.size / 1024).toFixed(2)} KB)`;
                if (fileInfo) fileInfo.style.display = 'block';
            } else {
                if (fileInfo) fileInfo.style.display = 'none';
            }
        });
    }

    // Drag and drop functionality
    const uploadArea = document.getElementById('uploadArea');
    const fileInputElement = document.getElementById('document_file');

    if (uploadArea && fileInputElement) {
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.style.background = '#e3f2fd';
            uploadArea.style.borderColor = '#0056b3';
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.style.background = '#f8f9fa';
            uploadArea.style.borderColor = '#007bff';
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.style.background = '#f8f9fa';
            uploadArea.style.borderColor = '#007bff';

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInputElement.files = files;
                fileInputElement.dispatchEvent(new Event('change'));
            }
        });

        uploadArea.addEventListener('click', function(e) {
            if (!e.target.closest('.form-control') && !e.target.closest('.form-select') && !e.target.closest('.btn')) {
                fileInputElement.click();
            }
        });
    }

    // Form validation for upload
    const uploadForm = document.getElementById('uploadForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            const documentType = document.getElementById('document_type');
            const documentFile = document.getElementById('document_file');

            if ((!documentType || !documentType.value) || (!documentFile || !documentFile.value)) {
                e.preventDefault();
                alert('Please select both document type and file.');
            }
        });
    }

    // AJAX Delete functionality
    function initializeDeleteHandlers() {
        const deleteForms = document.querySelectorAll('.delete-document-form');

        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
                    const form = this;
                    const documentId = form.getAttribute('action').split('/').pop();
                    const documentCard = document.getElementById('document-' + documentId);

                    // Show loading state
                    const deleteButton = form.querySelector('button[type="submit"]');
                    const originalText = deleteButton.innerHTML;
                    deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    deleteButton.disabled = true;

                    // Send AJAX request
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            _method: 'DELETE'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the document card with animation
                            if (documentCard) {
                                documentCard.style.transition = 'all 0.3s ease';
                                documentCard.style.opacity = '0';
                                documentCard.style.transform = 'translateX(-100px)';

                                setTimeout(() => {
                                    documentCard.remove();
                                    updateDocumentCount();
                                    showFlashMessage('Document deleted successfully!', 'success');
                                }, 300);
                            }
                        } else {
                            showFlashMessage(data.message || 'Error deleting document', 'error');
                            deleteButton.innerHTML = originalText;
                            deleteButton.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showFlashMessage('Error deleting document. Please try again.', 'error');
                        deleteButton.innerHTML = originalText;
                        deleteButton.disabled = false;
                    });
                }
            });
        });
    }

    function updateDocumentCount() {
        const documentTypeSections = document.querySelectorAll('.document-type-section');

        documentTypeSections.forEach(section => {
            const documentCards = section.querySelectorAll('.document-card');
            const countBadge = section.querySelector('.badge.rounded-pill');

            if (countBadge) {
                countBadge.textContent = documentCards.length;

                if (documentCards.length === 0) {
                    section.style.transition = 'all 0.3s ease';
                    section.style.opacity = '0';
                    setTimeout(() => {
                        section.remove();
                    }, 300);
                }
            }
        });

        const allDocumentCards = document.querySelectorAll('.document-card');
        const emptyState = document.querySelector('.text-center.py-5');

        if (allDocumentCards.length === 0 && !emptyState) {
            showEmptyState();
        }
    }

    function showEmptyState() {
        const cardBody = document.querySelector('#uploaded-documents-section .card-body');
        if (cardBody) {
            cardBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="document-icon text-muted">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <h5 class="text-muted mb-3">No Documents Uploaded</h5>
                    <p class="text-muted mb-4">Get started by uploading your first document</p>
                </div>
            `;
        }
    }

    function showFlashMessage(message, type) {
        const existingAlerts = document.querySelectorAll('.flash-message');
        existingAlerts.forEach(alert => alert.remove());

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show flash-message d-flex align-items-center`;
        alertDiv.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i>
            <div class="flex-grow-1">${message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        const header = document.querySelector('.d-flex.justify-content-between.align-items-center.mb-4');
        if (header && header.parentNode) {
            header.parentNode.insertBefore(alertDiv, header.nextSibling);
        }

        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
</script>
@endpush
