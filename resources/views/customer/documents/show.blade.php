@extends('layouts.app')

@section('title', 'Document Details - ' . $document->name)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-gray-800 mb-0">
                    <i class="fas fa-file me-2"></i>Document Details
                </h1>
                <div>
                    <a href="{{ route('customer.documents.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Documents
                    </a>
                    <a href="{{ route('customer.documents.download', $document) }}" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Download
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Document Information -->
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Document Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Document Name:</strong></td>
                                        <td>{{ $document->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Document Type:</strong></td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $document->document_type }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge status-badge bg-{{ $document->status === 'approved' ? 'success' : ($document->status === 'pending_review' ? 'warning' : 'danger') }}">
                                                {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Upload Date:</strong></td>
                                        <td>{{ $document->created_at->format('M d, Y \a\t h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>File Size:</strong></td>
                                        <td>{{ number_format($document->file_size / 1024, 1) }} KB</td>
                                    </tr>
                                    <tr>
                                        <td><strong>File Type:</strong></td>
                                        <td>{{ $document->mime_type }}</td>
                                    </tr>
                                    @if($document->description)
                                    <tr>
                                        <td><strong>Description:</strong></td>
                                        <td>{{ $document->description }}</td>
                                    </tr>
                                    @endif
                                    @if($document->rejection_reason)
                                    <tr>
                                        <td><strong>Rejection Reason:</strong></td>
                                        <td class="text-danger">{{ $document->rejection_reason }}</td>
                                    </tr>
                                    @endif
                                    @if($document->expiry_date)
                                    <tr>
                                        <td><strong>Expiry Date:</strong></td>
                                        <td>{{ $document->expiry_date->format('M d, Y') }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Document Preview -->
                    <div class="card shadow">
                        <div class="card-header bg-info text-white py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-eye me-2"></i>Document Preview
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            @if(str_starts_with($document->mime_type, 'image/'))
                                <img src="{{ Storage::url($document->file_path) }}" alt="{{ $document->file_name }}">
                                     alt="{{ $document->name }}"
                                     class="img-fluid rounded shadow"
                                     style="max-height: 500px;">
                            @elseif($document->mime_type === 'application/pdf')
                                <div class="alert alert-info">
                                    <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                                    <h5>PDF Document</h5>
                                    <p class="mb-3">This is a PDF document. You can download it to view the contents.</p>
                                    <a href="{{ route('customer.documents.download', $document) }}" class="btn btn-danger">
                                        <i class="fas fa-download me-2"></i>Download PDF
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-file fa-3x text-warning mb-3"></i>
                                    <h5>Document File</h5>
                                    <p class="mb-3">This document type cannot be previewed in the browser.</p>
                                    <a href="{{ route('customer.documents.download', $document) }}" class="btn btn-warning">
                                        <i class="fas fa-download me-2"></i>Download File
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions Sidebar -->
                <div class="col-md-4">
                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-cog me-2"></i>Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('customer.documents.download', $document) }}"
                                   class="btn btn-success mb-2">
                                    <i class="fas fa-download me-2"></i>Download Document
                                </a>

                                @if(in_array($document->status, ['pending_review', 'rejected']))
                                <form action="{{ route('customer.documents.destroy', $document) }}" method="POST" class="d-grid">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this document? This action cannot be undone.')">
                                        <i class="fas fa-trash me-2"></i>Delete Document
                                    </button>
                                </form>
                                @endif

                                <a href="{{ route('customer.documents.index') }}" class="btn btn-outline-secondary mt-2">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Documents
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Status Information -->
                    <div class="card shadow mt-4">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Status Information
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($document->status === 'pending_review')
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock me-2"></i>
                                    <strong>Pending Review</strong>
                                    <p class="mb-0 mt-2">Your document is awaiting review by our team. You will be notified once it's processed.</p>
                                </div>
                            @elseif($document->status === 'approved')
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Approved</strong>
                                    @if($document->approved_at)
                                        <p class="mb-0 mt-2">Approved on: {{ $document->approved_at->format('M d, Y') }}</p>
                                    @endif
                                </div>
                            @elseif($document->status === 'rejected')
                                <div class="alert alert-danger">
                                    <i class="fas fa-times-circle me-2"></i>
                                    <strong>Rejected</strong>
                                    <p class="mb-0 mt-2">Please check the rejection reason above and upload a corrected version.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
