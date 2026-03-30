@extends('layouts.app')

@section('title', 'Upload Document')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-gray-800 mb-0">
                    <i class="fas fa-upload me-2"></i>Upload Document
                </h1>
                <a href="{{ route('customer.documents.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Documents
                </a>
            </div>

            <div class="card shadow">
                <div class="card-body">
                    <form action="{{ route('customer.documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="document_type" class="form-label">Document Type *</label>
                                    <select name="document_type" id="document_type" class="form-select" required>
                                        <option value="">Select Document Type</option>
                                        @foreach($documentTypes as $type)
                                            <option value="{{ $type->document_type }}" {{ old('document_type') == $type->document_type ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="document_file" class="form-label">Document File *</label>
                                    <input type="file" name="document_file" id="document_file" class="form-control"
                                           required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <div class="form-text">
                                        Maximum file size: 10MB. Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"
                                      placeholder="Any additional notes about this document...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            After upload, your document will be reviewed by our team. You will be notified once it's approved.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('customer.documents.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Upload Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
