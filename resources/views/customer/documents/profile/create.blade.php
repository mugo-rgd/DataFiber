@extends('layouts.app')

@section('title', 'Upload Profile Document')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('customer.customer-dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customer.profile.edit') }}">Company Profile</a></li>
                        <li class="breadcrumb-item active">Upload Document</li>
                    </ol>
                </div>
                <h4 class="page-title">Upload Profile Document</h4>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Upload New Document</h4>

                    <form action="{{ route('customer.documents.profile.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="document_type" class="form-label">Document Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('document_type') is-invalid @enderror"
                                            id="document_type" name="document_type" required>
                                        <option value="">Select document type</option>
                                        <option value="kra_pin_certificate" {{ old('document_type') == 'kra_pin_certificate' ? 'selected' : '' }}>
                                            KRA PIN Certificate
                                        </option>
                                        <option value="business_registration_certificate" {{ old('document_type') == 'business_registration_certificate' ? 'selected' : '' }}>
                                            Business Registration Certificate
                                        </option>
                                        <option value="trade_license" {{ old('document_type') == 'trade_license' ? 'selected' : '' }}>
                                            Trade License
                                        </option>
                                        <option value="ca_license" {{ old('document_type') == 'ca_license' ? 'selected' : '' }}>
                                            CA License
                                        </option>
                                        <option value="cr12_certificate" {{ old('document_type') == 'cr12_certificate' ? 'selected' : '' }}>
                                            CR12 Certificate
                                        </option>
                                        <option value="tax_compliance_certificate" {{ old('document_type') == 'tax_compliance_certificate' ? 'selected' : '' }}>
                                            Tax Compliance Certificate
                                        </option>
                                        <option value="other" {{ old('document_type') == 'other' ? 'selected' : '' }}>
                                            Other Document
                                        </option>
                                    </select>
                                    @error('document_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Select the type of document you're uploading</small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="document" class="form-label">Document File <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('document') is-invalid @enderror"
                                           id="document" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                                    @error('document')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Allowed file types: PDF, JPG, JPEG, PNG (Max: 5MB)
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description (Optional)</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3"
                                              placeholder="Brief description of the document...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Add any relevant details about this document</small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="has_expiry" name="has_expiry" value="1">
                                        <label class="form-check-label" for="has_expiry">
                                            This document has an expiry date
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 expiry-date-field" style="display: none;">
                                <div class="mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date</label>
                                    <input type="date" class="form-control @error('expiry_date') is-invalid @enderror"
                                           id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}">
                                    @error('expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Date when this document expires</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('customer.profile.edit') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Upload Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Document Requirements</h4>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-pdf text-danger"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">File Format</h6>
                                <small class="text-muted">PDF, JPG, or PNG format only</small>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-weight text-warning"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">File Size</h6>
                                <small class="text-muted">Maximum 5MB per document</small>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-eye text-success"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Document Quality</h6>
                                <small class="text-muted">Clear, readable, and complete</small>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-info"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Processing Time</h6>
                                <small class="text-muted">24-48 hours for approval</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5 class="font-16">Required Documents</h5>
                        <ul class="ps-3 mb-0">
                            <li class="mb-2">
                                <strong>KRA PIN Certificate</strong>
                                <small class="d-block text-muted">Issued by Kenya Revenue Authority</small>
                            </li>
                            <li class="mb-2">
                                <strong>Business Registration Certificate</strong>
                                <small class="d-block text-muted">From the Registrar of Companies</small>
                            </li>
                            <li class="mb-2">
                                <strong>CR12 Certificate</strong>
                                <small class="d-block text-muted">Company directors information</small>
                            </li>
                            <li class="mb-2">
                                <strong>Trade License</strong>
                                <small class="d-block text-muted">Issued by local authorities</small>
                            </li>
                            <li>
                                <strong>CA License</strong>
                                <small class="d-block text-muted">Communications Authority of Kenya license</small>
                            </li>
                        </ul>
                    </div>

                    <div class="alert alert-warning mt-4">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>Important
                        </h6>
                        <p class="mb-0 small">
                            All documents are subject to verification and approval by our compliance team.
                            Expired documents will be rejected.
                        </p>
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
        // Show/hide expiry date field
        const expiryCheckbox = document.getElementById('has_expiry');
        const expiryDateField = document.querySelector('.expiry-date-field');
        const expiryDateInput = document.getElementById('expiry_date');

        if (expiryCheckbox && expiryDateField) {
            expiryCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    expiryDateField.style.display = 'block';
                    if (expiryDateInput) expiryDateInput.required = true;
                } else {
                    expiryDateField.style.display = 'none';
                    if (expiryDateInput) expiryDateInput.required = false;
                }
            });
        }

        // File size validation
        const fileInput = document.getElementById('document');
        const maxSize = 5 * 1024 * 1024; // 5MB in bytes

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    if (this.files[0].size > maxSize) {
                        alert('File size exceeds 5MB limit. Please select a smaller file.');
                        this.value = '';
                    }
                }
            });
        }

        // Set minimum date for expiry date (tomorrow)
        if (expiryDateInput) {
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            const minDate = tomorrow.toISOString().split('T')[0];
            expiryDateInput.min = minDate;
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
@endpush

@push('styles')
<style>
    .list-group-item {
        border-left: none;
        border-right: none;
        padding: 1rem 0;
    }
    .list-group-item:first-child {
        border-top: none;
        padding-top: 0;
    }
    .list-group-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .expiry-date-field {
        transition: all 0.3s ease;
    }
</style>
@endpush
