@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-upload me-2"></i>Upload Signed Document for {{ $customer->name }}
                    </h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('account-manager.customers.documents.store', $customer) }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <!-- Document Type -->
                        <div class="mb-3">
                            <label for="document_type" class="form-label required">Document Type</label>
                            <select class="form-select @error('document_type') is-invalid @enderror"
                                    id="document_type"
                                    name="document_type" required>
                                <option value="">Select Document Type</option>
                                @foreach($documentTypes as $type)
                                    <option value="{{ $type->document_type }}"
                                            {{ old('document_type') == $type->document_type ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('document_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Related Lease - Improved Version -->
                        <div class="mb-3">
                            <label for="lease_id" class="form-label">Related Lease (Optional)</label>

                            @if($leases->isNotEmpty())
                                <select class="form-select @error('lease_id') is-invalid @enderror"
                                        id="lease_id"
                                        name="lease_id">
                                    <option value="">No specific lease</option>
                                    @foreach($leases as $lease)
                                        <option value="{{ $lease->id }}"
                                                data-customer-id="{{ $lease->customer_id }}"
                                                {{ old('lease_id') == $lease->id ? 'selected' : '' }}>
                                            {{ $lease->lease_number }} -
                                            {{ $lease->title ?? $lease->property_name ?? 'Untitled' }}
                                            ({{ ucfirst($lease->status) }})
                                            @if($lease->start_date)
                                                - Started: {{ \Carbon\Carbon::parse($lease->start_date)->format('M Y') }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    <i class="fas fa-file-contract me-1"></i>
                                    {{ $leases->count() }} lease(s) available for this customer
                                </small>
                            @else
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No leases found for this customer.
                                    <a href="{{ route('account-manager.leases.create', ['customer_id' => $customer->id]) }}"
                                       class="alert-link">
                                        Create a lease
                                    </a>
                                </div>
                                <input type="hidden" name="lease_id" value="">
                            @endif

                            @error('lease_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div class="mb-3">
                            <label for="document_file" class="form-label required">Document File</label>
                            <input type="file"
                                   class="form-control @error('document_file') is-invalid @enderror"
                                   id="document_file"
                                   name="document_file"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Accepted formats: PDF, DOC, DOCX, JPG, PNG. Max size: 10MB
                            </div>
                            @error('document_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="Enter any additional notes about this document...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Auto Approve Option -->
                        <div class="mb-3 form-check">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="auto_approve"
                                   name="auto_approve"
                                   value="1"
                                   {{ old('auto_approve') ? 'checked' : '' }}>
                            <label class="form-check-label" for="auto_approve">
                                <i class="fas fa-check-circle me-1 text-success"></i>
                                Auto-approve this document (for signed documents)
                            </label>
                            <small class="form-text text-muted d-block mt-1">
                                If checked, the document will be automatically approved without requiring manual review.
                            </small>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('account-manager.customers.documents.manage', $customer) }}"
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-1"></i> Upload Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Optional: Add JavaScript for enhanced functionality --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Optional: Add file size validation
    const fileInput = document.getElementById('document_file');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const maxSize = 10 * 1024 * 1024; // 10MB in bytes
            if (this.files[0] && this.files[0].size > maxSize) {
                alert('File size exceeds 10MB. Please choose a smaller file.');
                this.value = '';
            }
        });
    }

    // Optional: Add confirmation for auto-approve
    const autoApproveCheckbox = document.getElementById('auto_approve');
    if (autoApproveCheckbox) {
        autoApproveCheckbox.addEventListener('change', function() {
            if (this.checked) {
                console.log('Document will be auto-approved');
            }
        });
    }
});
</script>
@endpush
@endsection

@push('styles')
<style>
    .required:after {
        content: " *";
        color: #dc3545;
        font-weight: bold;
    }

    .card {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
    }

    .card-header h5 {
        font-weight: 600;
        color: #333;
    }

    .form-label {
        font-weight: 500;
        color: #555;
    }

    .form-control:focus, .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffeeba;
        color: #856404;
        padding: 0.75rem 1rem;
        border-radius: 5px;
    }

    .alert-warning a {
        color: #533f03;
        font-weight: 600;
        text-decoration: underline;
    }

    .alert-warning a:hover {
        color: #856404;
    }

    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    .btn {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0069d9;
        border-color: #0062cc;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    /* Style for the lease dropdown */
    #lease_id option {
        padding: 8px;
    }

    /* Style for the file input */
    input[type="file"] {
        padding: 0.375rem 0.75rem;
    }

    /* Style for the checkbox */
    .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .d-flex {
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            width: 100%;
        }
    }
</style>
@endpush
