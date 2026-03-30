{{-- resources/views/customer/documents/upload.blade.php --}}
@extends('layouts.app')

@section('title', 'Upload Documents')

@section('content')

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3">Document Management</h1>
                    <p class="text-muted">Upload and manage your required documents</p>
                </div>
                <a href="{{ route('customer.customer-dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Document Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pending Review
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $documentStats['pending'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approved
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $documentStats['approved'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Rejected
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $documentStats['rejected'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Expired
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $documentStats['expired'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Upload Form -->
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-upload me-2"></i>Upload Document
                    </h5>
                </div>
                <div class="card-body">
                    @if($requiredDocuments->count() > 0)
                        <<form action="{{ route('customer.documents.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label for="document_type" class="form-label">Document Type</label>
        <select name="document_type" id="document_type" class="form-select" required>
            <option value="">Select Document Type</option>
            @foreach($documentTypes as $type)
                <option value="{{ $type->document_type }}">{{ $type->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="document_file" class="form-label">Document File</label>
        <input type="file" name="document_file" id="document_file" class="form-control" required accept=".pdf">
    </div>

    <div class="mb-3">
        <label for="notes" class="form-label">Notes (Optional)</label>
        <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary">Upload Document</button>
</form>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>All Required Documents Uploaded</h5>
                            <p class="text-muted">You have uploaded all required documents. You can still upload additional documents if needed.</p>
                            <button class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#additionalUpload">
                                <i class="fas fa-plus me-2"></i>Upload Additional Document
                            </button>
                        </div>

                        <!-- Additional Upload Form (Collapsed) -->
                        <div class="collapse mt-3" id="additionalUpload">
                            <div class="card card-body">
                                <form method="POST" action="{{ route('customer.documents.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="document_type" value="other">

                                    <div class="mb-3">
                                        <label for="document_file" class="form-label">Document File</label>
                                        <input type="file" class="form-control"
                                               id="document_file" name="document_file" accept=".pdf" required>
                                        <small class="form-text text-muted">PDF files only, max 5MB</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Description</label>
                                        <input type="text" class="form-control"
                                               id="notes" name="notes"
                                               placeholder="Describe this document..." required>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">Upload</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Uploaded Documents List -->
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>My Documents
                    </h5>
                </div>
                <div class="card-body">
                    @if($uploadedDocuments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Document</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Uploaded</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($uploadedDocuments as $document)
                                        <tr>
                                            <td>
                                                <strong>{{ $document->name }}</strong>
                                                @if($document->is_required)
                                                    <span class="badge bg-primary ms-1">Required</span>
                                                @endif
                                                <br>
                                                <small class="text-muted">{{ $document->file_size_human }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ ucfirst(str_replace('_', ' ', $document->document_type)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $document->status_badge_class }}">
                                                    {{ ucfirst($document->status) }}
                                                    @if($document->isExpired())
                                                        (Expired)
                                                    @endif
                                                </span>
                                                @if($document->rejection_reason)
                                                    <br>
                                                    <small class="text-danger">
                                                        Reason: {{ $document->rejection_reason }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $document->created_at->format('M d, Y') }}</small>
                                                <br>
                                                <small class="text-muted">{{ $document->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('customer.documents.view', $document->id) }}"
                                                       class="btn btn-outline-primary" target="_blank"
                                                       title="View Document">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('customer.documents.download', $document->id) }}"
                                                       class="btn btn-outline-secondary"
                                                       title="Download Document">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @if(in_array($document->status, ['pending_review', 'rejected']))
                                                        <button type="button"
                                                                class="btn btn-outline-warning"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#reuploadModal{{ $document->id }}"
                                                                title="Re-upload">
                                                            <i class="fas fa-redo"></i>
                                                        </button>
                                                        <form method="POST"
                                                              action="{{ route('customer.documents.destroy', $document->id) }}"
                                                              class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-outline-danger"
                                                                    onclick="return confirm('Are you sure you want to delete this document?')"
                                                                    title="Delete Document">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>

                                                <!-- Re-upload Modal -->
                                                <div class="modal fade" id="reuploadModal{{ $document->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Re-upload Document</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="POST"
                                                                  action="{{ route('customer.documents.update', $document->id) }}"
                                                                  enctype="multipart/form-data">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="document_file{{ $document->id }}" class="form-label">
                                                                            New Document File
                                                                        </label>
                                                                        <input type="file"
                                                                               class="form-control"
                                                                               id="document_file{{ $document->id }}"
                                                                               name="document_file"
                                                                               accept=".pdf" required>
                                                                        <small class="form-text text-muted">PDF files only, max 5MB</small>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="notes{{ $document->id }}" class="form-label">Notes</label>
                                                                        <textarea class="form-control"
                                                                                  id="notes{{ $document->id }}"
                                                                                  name="notes"
                                                                                  rows="3"
                                                                                  placeholder="Any updates or changes...">{{ $document->description }}</textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-primary">Update Document</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h5>No Documents Uploaded</h5>
                            <p class="text-muted">You haven't uploaded any documents yet.</p>
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
.badge.badge-pending { background-color: #ffc107; color: #000; }
.badge.badge-approved { background-color: #28a745; color: #fff; }
.badge.badge-rejected { background-color: #dc3545; color: #fff; }
.badge.badge-expired { background-color: #6c757d; color: #fff; }
</style>
@endpush

@push('scripts')
<script>

    document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');

    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('=== FORM SUBMISSION DEBUG ===');
            console.log('Form action:', this.action);
            console.log('Form method:', this.method);

            const formData = new FormData(this);
            console.log('Form data being submitted:');
            for (let [key, value] of formData.entries()) {
                if (key === 'document_file') {
                    console.log('  ' + key + ': ' + value.name + ' (' + value.size + ' bytes)');
                } else {
                    console.log('  ' + key + ': ' + value);
                }
            }

            // Check if required fields are filled
            const documentType = form.querySelector('#document_type').value;
            const documentFile = form.querySelector('#document_file').files[0];

            if (!documentType) {
                console.error('❌ Document type is empty!');
            }
            if (!documentFile) {
                console.error('❌ Document file is empty!');
            }

            console.log('=== END DEBUG ===');
        });
    }
});
// Add badge class helper to document status
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[class*="badge-"]').forEach(badge => {
        const status = badge.textContent.trim().toLowerCase();
        if (status.includes('pending')) {
            badge.classList.add('badge-pending');
        } else if (status.includes('approved')) {
            badge.classList.add('badge-approved');
        } else if (status.includes('rejected')) {
            badge.classList.add('badge-rejected');
        } else if (status.includes('expired')) {
            badge.classList.add('badge-expired');
        }
    });
});
</script>
@endpush
