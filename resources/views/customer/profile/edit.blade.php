{{-- resources/views/customer/profile/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customer.profile.show') }}">Profile</a></li>
                        <li class="breadcrumb-item active">Edit Profile</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Company Profile</h4>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
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

    <div class="row">
        <!-- Company Information Form (Left Column) -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('customer.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h4 class="header-title mb-3">Company Information</h4>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                           id="company_name" name="company_name"
                                           value="{{ old('company_name', $companyProfile->company_name ?? '') }}" required>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kra_pin" class="form-label">KRA PIN <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('kra_pin') is-invalid @enderror"
                                           id="kra_pin" name="kra_pin"
                                           value="{{ old('kra_pin', $companyProfile->kra_pin ?? '') }}" required>
                                    @error('kra_pin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="registration_number" class="form-label">Registration Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('registration_number') is-invalid @enderror"
                                           id="registration_number" name="registration_number"
                                           value="{{ old('registration_number', $companyProfile->registration_number ?? '') }}" required>
                                    @error('registration_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_type" class="form-label">Company Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('company_type') is-invalid @enderror"
                                            id="company_type" name="company_type" required>
                                        <option value="">Select type</option>
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

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                           id="phone_number" name="phone_number"
                                           value="{{ old('phone_number', $companyProfile->phone_number ?? '') }}" required>
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_name_1" class="form-label">Primary Contact Person <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('contact_name_1') is-invalid @enderror"
                                           id="contact_name_1" name="contact_name_1"
                                           value="{{ old('contact_name_1', $companyProfile->contact_name_1 ?? '') }}" required>
                                    @error('contact_name_1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_phone_1" class="form-label">Primary Contact Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('contact_phone_1') is-invalid @enderror"
                                           id="contact_phone_1" name="contact_phone_1"
                                           value="{{ old('contact_phone_1', $companyProfile->contact_phone_1 ?? '') }}" required>
                                    @error('contact_phone_1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_name_2" class="form-label">Secondary Contact Person</label>
                                    <input type="text" class="form-control @error('contact_name_2') is-invalid @enderror"
                                           id="contact_name_2" name="contact_name_2"
                                           value="{{ old('contact_name_2', $companyProfile->contact_name_2 ?? '') }}">
                                    @error('contact_name_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_phone_2" class="form-label">Secondary Contact Phone</label>
                                    <input type="text" class="form-control @error('contact_phone_2') is-invalid @enderror"
                                           id="contact_phone_2" name="contact_phone_2"
                                           value="{{ old('contact_phone_2', $companyProfile->contact_phone_2 ?? '') }}">
                                    @error('contact_phone_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Physical Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror"
                                           id="address" name="address"
                                           value="{{ old('address', $companyProfile->address ?? '') }}" required>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="road" class="form-label">Road/Street <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('road') is-invalid @enderror"
                                           id="road" name="road"
                                           value="{{ old('road', $companyProfile->road ?? '') }}" required>
                                    @error('road')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="town" class="form-label">Town/City <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('town') is-invalid @enderror"
                                           id="town" name="town"
                                           value="{{ old('town', $companyProfile->town ?? '') }}" required>
                                    @error('town')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                           id="code" name="code"
                                           value="{{ old('code', $companyProfile->code ?? '') }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Company Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3">{{ old('description', $companyProfile->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('customer.profile.show') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column with Profile Requirements and Document Upload -->
        <div class="col-lg-4">
            <!-- Profile Requirements Card -->
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Profile Requirements</h4>

                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Important Information
                        </h6>
                        <p class="mb-0 small">
                            Ensure all information is accurate as it will be used for official communications and documentation.
                        </p>
                    </div>

                    <div class="mt-3">
                        <h5 class="font-16">Required Fields</h5>
                        <ul class="ps-3 mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Company Name
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                KRA PIN
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Registration Number
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Contact Information
                            </li>
                            <li>
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Physical Address
                            </li>
                        </ul>
                    </div>

                    <div class="mt-4">
                        <h5 class="font-16">Support</h5>
                        <p class="text-muted small">
                            Need help updating your profile? Contact our support team.
                        </p>
                        <a href="mailto:support@darkfibre-crm.test" class="btn btn-outline-primary w-100">
                            <i class="fas fa-envelope me-2"></i>Email Support
                        </a>
                    </div>
                </div>
            </div>

            <!-- Document Upload Card - NEW SECTION -->
            <div class="card mt-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-upload me-2"></i>Upload Documents
                    </h5>
                </div>
                <div class="card-body">
                    <!-- IMPORTANT: Fixed route name -->
                    <form action="{{ route('customer.documents.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        <div class="upload-area mb-3" id="uploadArea">
                            <div class="upload-area-content text-center p-4">
                                <div class="document-icon mb-3">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                                </div>
                                <h6>Drag & Drop or Click to Upload</h6>
                                <p class="text-muted small mb-0">Supported formats: PDF, DOC, DOCX, JPG, PNG</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="document_type" class="form-label">Document Type <span class="text-danger">*</span></label>
                            <select name="document_type" id="document_type" class="form-select" required>
                                <option value="">Select Document Type</option>
                                @foreach($documentTypes ?? [] as $documentType)
                                    <option value="{{ $documentType->document_type }}"
                                            data-max-size="{{ $documentType->max_file_size ?? 2048 }}"
                                            data-extensions='{{ json_encode($documentType->allowed_extensions ?? ['pdf','doc','docx','jpg','jpeg','png']) }}'>
                                        {{ $documentType->name }}
                                        @if($documentType->is_required ?? false)
                                            <span class="badge bg-danger">Required</span>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="document_file" class="form-label">Select File <span class="text-danger">*</span></label>
                            <input type="file" name="document_file" id="document_file"
                                   class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                            <div class="form-text" id="fileRequirements">
                                Maximum file size: 2MB | Allowed formats: PDF, DOC, DOCX, JPG, PNG
                            </div>
                        </div>

                        <div id="fileInfo" class="alert alert-info p-2 small" style="display: none;">
                            <i class="fas fa-file me-2"></i>
                            <span id="fileName"></span>
                            <span id="fileSize" class="text-muted ms-2"></span>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-upload me-2"></i>Upload Document
                        </button>
                    </form>
                </div>
            </div>

            <!-- Uploaded Documents Card - NEW SECTION -->
            @if(isset($documents) && count($documents) > 0)
                <div class="card mt-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-folder me-2"></i>Uploaded Documents
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($documents as $document)
                                <div class="list-group-item" id="document-{{ $document->id }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            @php
                                                $icon = 'file';
                                                if (str_contains($document->file_name, '.pdf')) {
                                                    $icon = 'file-pdf text-danger';
                                                } elseif (str_contains($document->file_name, ['.doc', '.docx'])) {
                                                    $icon = 'file-word text-primary';
                                                } elseif (str_contains($document->file_name, ['.jpg', '.jpeg', '.png'])) {
                                                    $icon = 'file-image text-success';
                                                }
                                            @endphp
                                            <i class="fas fa-{{ $icon }} me-2"></i>
                                            <small>{{ Str::limit($document->file_name, 25) }}</small>
                                            <br>
                                            <small class="text-muted">
                                                {{ $document->created_at->format('d M Y') }} |
                                                {{ round($document->file_size / 1024, 1) }} KB
                                                @if($document->status)
                                                    | <span class="badge bg-warning">{{ $document->status }}</span>
                                                @endif
                                            </small>
                                        </div>
                                        <form action="{{ route('customer.documents.profile.destroy', $document->id) }}"
                                              method="POST" class="d-inline delete-document-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .upload-area {
        border: 2px dashed #0d6efd;
        border-radius: 8px;
        background: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .upload-area:hover {
        background: #e7f1ff;
        border-color: #0a58ca;
    }
    .upload-area-content {
        pointer-events: none;
    }
    .document-icon i {
        transition: transform 0.3s ease;
    }
    .upload-area:hover .document-icon i {
        transform: translateY(-5px);
    }
    #fileInfo {
        font-size: 0.85rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize file upload functionality
        initializeFileUpload();

        // Initialize delete handlers
        initializeDeleteHandlers();
    });

    function initializeFileUpload() {
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('document_file');
        const documentTypeSelect = document.getElementById('document_type');
        const fileRequirements = document.getElementById('fileRequirements');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');

        // Update file requirements when document type changes
        documentTypeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];

            if (selectedOption.value) {
                const maxSize = selectedOption.dataset.maxSize || 2048;
                const extensions = JSON.parse(selectedOption.dataset.extensions || '[]');

                let requirementsText = `Maximum file size: ${maxSize}KB`;
                if (extensions.length > 0) {
                    requirementsText += ` | Allowed formats: ${extensions.join(', ')}`;
                }
                fileRequirements.textContent = requirementsText;
            } else {
                fileRequirements.textContent = 'Maximum file size: 2MB | Allowed formats: PDF, DOC, DOCX, JPG, PNG';
            }
        });

        // Handle file selection
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                // Validate file
                const selectedOption = documentTypeSelect.options[documentTypeSelect.selectedIndex];
                const maxSize = (selectedOption.value ? (selectedOption.dataset.maxSize || 2048) : 2048) * 1024;
                const allowedExtensions = selectedOption.value ?
                    JSON.parse(selectedOption.dataset.extensions || '[]') :
                    ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

                // Check file size
                if (file.size > maxSize) {
                    alert(`File size must be less than ${maxSize / 1024}KB`);
                    this.value = '';
                    fileInfo.style.display = 'none';
                    return;
                }

                // Check file extension
                const fileExtension = file.name.split('.').pop().toLowerCase();
                if (allowedExtensions.length > 0 && !allowedExtensions.includes(fileExtension)) {
                    alert(`File type not allowed. Allowed types: ${allowedExtensions.join(', ')}`);
                    this.value = '';
                    fileInfo.style.display = 'none';
                    return;
                }

                // Display file info
                fileName.textContent = file.name;
                fileSize.textContent = `(${(file.size / 1024).toFixed(2)} KB)`;
                fileInfo.style.display = 'block';
            } else {
                fileInfo.style.display = 'none';
            }
        });

        // Drag and drop functionality
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.background = '#e7f1ff';
            this.style.borderColor = '#0a58ca';
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.background = '#f8f9fa';
            this.style.borderColor = '#0d6efd';
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.background = '#f8f9fa';
            this.style.borderColor = '#0d6efd';

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });

        uploadArea.addEventListener('click', function(e) {
            if (!e.target.closest('select') && !e.target.closest('input')) {
                fileInput.click();
            }
        });

        // Form validation
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            const documentType = documentTypeSelect.value;
            const documentFile = fileInput.value;

            if (!documentType || !documentFile) {
                e.preventDefault();
                alert('Please select both document type and file.');
            }
        });
    }

    function initializeDeleteHandlers() {
        const deleteForms = document.querySelectorAll('.delete-document-form');

        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (confirm('Are you sure you want to delete this document?')) {
                    const form = this;
                    const documentItem = form.closest('.list-group-item');

                    // Show loading state
                    const deleteButton = form.querySelector('button');
                    const originalText = deleteButton.innerHTML;
                    deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    deleteButton.disabled = true;

                    // Send AJAX request
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ _method: 'DELETE' })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the document item with animation
                            documentItem.style.transition = 'all 0.3s ease';
                            documentItem.style.opacity = '0';
                            documentItem.style.transform = 'translateX(-100px)';

                            setTimeout(() => {
                                documentItem.remove();
                                location.reload(); // Refresh to update counts
                            }, 300);
                        } else {
                            alert(data.message || 'Error deleting document');
                            deleteButton.innerHTML = originalText;
                            deleteButton.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting document. Please try again.');
                        deleteButton.innerHTML = originalText;
                        deleteButton.disabled = false;
                    });
                }
            });
        });
    }
</script>
@endpush
