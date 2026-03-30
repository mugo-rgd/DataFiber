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

                <!-- Simple working back button -->
                <a href="/customer/customer-dashboard" class="btn btn-outline-primary">
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

            <!-- Profile Edit Form -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Company Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label required-field">Company Name</label>
                                <input type="text" name="company_name" id="company_name"
                                       value="{{ old('company_name', $companyProfile->company_name ?? '') }}"
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
                                           value="{{ old('company_email', $companyProfile->company_email ?? '') }}"
                                           class="form-control @error('company_email') is-invalid @enderror"
                                           placeholder="company@example.com">
                                </div>
                                @error('company_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company_phone" class="form-label">Company Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" name="company_phone" id="company_phone"
                                           value="{{ old('company_phone', $companyProfile->company_phone ?? '') }}"
                                           class="form-control @error('company_phone') is-invalid @enderror"
                                           placeholder="+254 XXX XXX XXX">
                                </div>
                                @error('company_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tax_id" class="form-label">Tax ID / VAT Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-receipt"></i></span>
                                    <input type="text" name="tax_id" id="tax_id"
                                           value="{{ old('tax_id', $companyProfile->tax_id ?? '') }}"
                                           class="form-control @error('tax_id') is-invalid @enderror"
                                           placeholder="Enter tax identification number">
                                </div>
                                @error('tax_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="contact_person" id="contact_person"
                                           value="{{ old('contact_person', $companyProfile->contact_person ?? '') }}"
                                           class="form-control @error('contact_person') is-invalid @enderror"
                                           placeholder="Full name of contact person">
                                </div>
                                @error('contact_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="company_address" class="form-label">Company Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <textarea name="company_address" id="company_address"
                                              class="form-control @error('company_address') is-invalid @enderror"
                                              rows="2"
                                              placeholder="Enter company physical address">{{ old('company_address', $companyProfile->company_address ?? '') }}</textarea>
                                </div>
                                @error('company_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="text-end mt-4">
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
                    <!-- Fixed route from customer.documents.store to customer.customer.documents.store -->
                    <form action="{{ route('customer.documents.store',  ['customer' => auth()->id()]) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
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
                                    <span class="badge bg-primary rounded-pill document-count-{{ $documentType }}">{{ count($docs) }}</span>
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
                                                                    <span class="badge bg-success status-badge">{{ ucfirst($document->status) }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="action-buttons d-flex justify-content-end">
                                                        <!-- Fixed route from customer.documents.destroy to customer.documents.profile.destroy -->
                                                        <form action="{{ route('customer.documents.profile.destroy', $document->id) }}" method="POST" class="d-inline delete-document-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Delete Document"
                                                                    onclick="return confirm('Are you sure you want to delete this document? This action cannot be undone.')">
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
                            <a href="#uploadNewDocuments" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Upload Your First Document
                            </a>
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
    .section-header {
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
        margin-bottom: 20px;
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
    .status-badge {
        font-size: 0.75em;
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
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Initialize file requirements display
        updateFileRequirements();

        // Add event listeners for delete forms
        initializeDeleteHandlers();
    });

    // Update file requirements when document type changes
    document.getElementById('document_type').addEventListener('change', function() {
        updateFileRequirements();
    });

    function updateFileRequirements() {
        const documentTypeSelect = document.getElementById('document_type');
        const selectedOption = documentTypeSelect.options[documentTypeSelect.selectedIndex];
        const fileRequirements = document.getElementById('fileRequirements');

        if (selectedOption.value) {
            const maxSize = selectedOption.getAttribute('data-max-size');
            const extensions = JSON.parse(selectedOption.getAttribute('data-extensions') || '[]');

            let requirementsText = `Maximum file size: ${maxSize}KB`;
            if (extensions.length > 0) {
                requirementsText += ` | Allowed formats: ${extensions.join(', ')}`;
            }

            fileRequirements.textContent = requirementsText;
        } else {
            fileRequirements.textContent = 'Maximum file size: 2MB | Allowed formats: PDF, DOC, DOCX, JPG, JPEG, PNG';
        }
    }

    // File size validation and info display
    document.getElementById('document_file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const documentTypeSelect = document.getElementById('document_type');
        const selectedOption = documentTypeSelect.options[documentTypeSelect.selectedIndex];
        const maxSize = selectedOption.value ? (selectedOption.getAttribute('data-max-size') * 1024) : (2 * 1024 * 1024);
        const allowedExtensions = selectedOption.value ? JSON.parse(selectedOption.getAttribute('data-extensions') || '[]') : ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');

        if (file) {
            // Check file size
            if (file.size > maxSize) {
                alert(`File size must be less than ${maxSize / 1024}KB`);
                e.target.value = '';
                fileInfo.style.display = 'none';
                return;
            }

            // Check file extension
            const fileExtension = file.name.split('.').pop().toLowerCase();
            if (allowedExtensions.length > 0 && !allowedExtensions.includes(fileExtension)) {
                alert(`File type not allowed. Allowed types: ${allowedExtensions.join(', ')}`);
                e.target.value = '';
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
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('document_file');

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
            fileInput.files = files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });

    uploadArea.addEventListener('click', function(e) {
        if (!e.target.closest('.form-control') && !e.target.closest('.form-select') && !e.target.closest('.btn')) {
            fileInput.click();
        }
    });

    // Form validation
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        const documentType = document.getElementById('document_type').value;
        const documentFile = document.getElementById('document_file').value;

        if (!documentType || !documentFile) {
            e.preventDefault();
            alert('Please select both document type and file.');
        }
    });

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
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            _method: 'DELETE'
                        })
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        throw new Error('Network response was not ok.');
                    })
                    .then(data => {
                        if (data.success) {
                            // Remove the document card with animation
                            if (documentCard) {
                                documentCard.style.transition = 'all 0.3s ease';
                                documentCard.style.opacity = '0';
                                documentCard.style.transform = 'translateX(-100px)';

                                setTimeout(() => {
                                    documentCard.remove();

                                    // Update document count badge
                                    updateDocumentCount();

                                    // Show success message
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
        // Update the document count badges for each document type
        const documentTypeSections = document.querySelectorAll('.document-type-section');

        documentTypeSections.forEach(section => {
            const documentType = Array.from(section.querySelector('h6').classList)
                .find(className => className.startsWith('document-count-'))
                ?.replace('document-count-', '');

            if (documentType) {
                const documentCards = section.querySelectorAll('.document-card');
                const countBadge = section.querySelector('.badge.rounded-pill');

                if (countBadge) {
                    countBadge.textContent = documentCards.length;

                    // If no documents left in this section, hide the section
                    if (documentCards.length === 0) {
                        section.style.transition = 'all 0.3s ease';
                        section.style.opacity = '0';
                        section.style.height = section.offsetHeight + 'px';

                        setTimeout(() => {
                            section.style.height = '0';
                            section.style.marginBottom = '0';
                            section.style.padding = '0';
                            section.style.overflow = 'hidden';
                        }, 50);

                        setTimeout(() => {
                            section.remove();
                        }, 350);
                    }
                }
            }
        });

        // Check if all documents are deleted, show empty state
        const allDocumentCards = document.querySelectorAll('.document-card');
        const emptyState = document.querySelector('.text-center.py-5');

        if (allDocumentCards.length === 0 && !emptyState) {
            showEmptyState();
        }
    }

    function showEmptyState() {
        const cardBody = document.querySelector('.card-body.p-4');
        cardBody.innerHTML = `
            <div class="text-center py-5">
                <div class="document-icon text-muted">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h5 class="text-muted mb-3">No Documents Uploaded</h5>
                <p class="text-muted mb-4">Get started by uploading your first document</p>
                <a href="#uploadNewDocuments" class="btn btn-primary">
                    <i class="fas fa-upload me-2"></i>Upload Your First Document
                </a>
            </div>
        `;
    }

    function showFlashMessage(message, type) {
        // Remove existing flash messages
        const existingAlerts = document.querySelectorAll('.flash-message');
        existingAlerts.forEach(alert => alert.remove());

        // Create new flash message
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show flash-message d-flex align-items-center`;
        alertDiv.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i>
            <div class="flex-grow-1">${message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Insert after the header
        const header = document.querySelector('.d-flex.justify-content-between.align-items-center.mb-4');
        header.parentNode.insertBefore(alertDiv, header.nextSibling);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
</script>
@endpush
