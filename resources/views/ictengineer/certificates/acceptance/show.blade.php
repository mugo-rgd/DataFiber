@extends('layouts.app')

@section('title', 'View Acceptance Certificate - ICT Engineer')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-award text-success me-2"></i>Acceptance Certificate
                    </h1>
                    <p class="text-muted mb-0">ICT Engineer - View Certificate Details</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('ictengineer.requests') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Requests
                    </a>
                    <a href="{{ route('ictengineer.certificates.acceptance.download', $certificate) }}"
                       class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Download Certificate
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-award me-2"></i>
                            Acceptance Certificate Details
                        </h5>
                        <span class="badge bg-white text-success fs-6">
                            {{ $certificate->status ?? 'Issued' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Certificate Header -->
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-primary">Certificate of Acceptance</h4>
                        <p class="text-muted">THE KENYA POWER & LIGHTING COMPANY PLC</p>
                    </div>

                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-card border rounded p-3 mb-3">
                                <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Certificate Information</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="40%"><strong>Reference No:</strong></td>
                                        <td>{{ $certificate->certificate_ref }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>To:</strong></td>
                                        <td>{{ $certificate->to_company }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Effective Date:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($certificate->effective_date)->format('F d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cable Type:</strong></td>
                                        <td>{{ $certificate->cable_type }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card border rounded p-3 mb-3">
                                <h6 class="text-primary mb-3"><i class="fas fa-road me-2"></i>Link Information</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="40%"><strong>Route Name:</strong></td>
                                        <td>{{ $certificate->route_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Link Name:</strong></td>
                                        <td>{{ $certificate->link_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Distance:</strong></td>
                                        <td>{{ number_format($certificate->distance, 3) }} KM</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cores:</strong></td>
                                        <td>{{ $certificate->cores_count }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Parties Information -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-building me-2"></i>Parties Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="bg-primary text-white p-2 rounded mb-3">LESSOR</h6>
                                        <p class="mb-2"><strong>Company:</strong> {{ $certificate->lessor }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="bg-success text-white p-2 rounded mb-3">LESSEE</h6>
                                        <p class="mb-2"><strong>Company:</strong> {{ $certificate->lessee }}</p>
                                        @if($certificate->lessee_address)
                                        <p class="mb-2"><strong>Address:</strong> {{ $certificate->lessee_address }}</p>
                                        @endif
                                        @if($certificate->lessee_contact)
                                        <p class="mb-0"><strong>Contact:</strong> {{ $certificate->lessee_contact }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Signatories Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-signature me-2"></i>Signatories</h6>
                        </div>
                        <div class="card-body">
                            <!-- Kenya Power Signatories -->
                            <h6 class="text-primary mb-3">Kenya Power Signatories</h6>
                            <div class="row">
                                <!-- Witness 1 -->
                                <div class="col-md-6 mb-3">
                                    <div class="signatory-display border rounded p-3">
                                        <h6 class="bg-primary text-white p-2 rounded mb-3">
                                            1. INFRASTRUCTURE SUPPORT ENGINEER
                                        </h6>
                                        <p><strong>Name:</strong> {{ $certificate->witness1_name }}</p>
                                        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($certificate->witness1_date)->format('F d, Y') }}</p>
                                        @if($certificate->witness1_signature_path)
                                        <div class="signature-display mt-2">
                                            <p class="small text-muted mb-1">Signature:</p>
                                            <img src="{{ Storage::url($certificate->witness1_signature_path) }}"
                                                 alt="Signature" class="img-fluid border rounded" style="max-height: 80px;">
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Witness 2 -->
                                <div class="col-md-6 mb-3">
                                    <div class="signatory-display border rounded p-3">
                                        <h6 class="bg-primary text-white p-2 rounded mb-3">
                                            2. TELECOM LEAD ENGINEER
                                        </h6>
                                        <p><strong>Name:</strong> {{ $certificate->witness2_name }}</p>
                                        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($certificate->witness2_date)->format('F d, Y') }}</p>
                                        @if($certificate->witness2_signature_path)
                                        <div class="signature-display mt-2">
                                            <p class="small text-muted mb-1">Signature:</p>
                                            <img src="{{ Storage::url($certificate->witness2_signature_path) }}"
                                                 alt="Signature" class="img-fluid border rounded" style="max-height: 80px;">
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Witness 3 -->
                                <div class="col-md-6 mb-3">
                                    <div class="signatory-display border rounded p-3">
                                        <h6 class="bg-primary text-white p-2 rounded mb-3">
                                            3. TELECOM MANAGER
                                        </h6>
                                        <p><strong>Name:</strong> {{ $certificate->witness3_name }}</p>
                                        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($certificate->witness3_date)->format('F d, Y') }}</p>
                                        @if($certificate->witness3_signature_path)
                                        <div class="signature-display mt-2">
                                            <p class="small text-muted mb-1">Signature:</p>
                                            <img src="{{ Storage::url($certificate->witness3_signature_path) }}"
                                                 alt="Signature" class="img-fluid border rounded" style="max-height: 80px;">
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Lessee Signatories -->
                            <h6 class="text-success mt-4 mb-3">Lessee Signatories</h6>
                            <div class="row">
                                <!-- Lessee 1 -->
                                <div class="col-md-6 mb-3">
                                    <div class="signatory-display border rounded p-3">
                                        <h6 class="bg-success text-white p-2 rounded mb-3">
                                            1. LEAD ENGINEER / TECHNICAL REPRESENTATIVE
                                        </h6>
                                        <p><strong>Name:</strong> {{ $certificate->lessee1_name }}</p>
                                        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($certificate->lessee1_date)->format('F d, Y') }}</p>
                                        @if($certificate->lessee1_signature_path)
                                        <div class="signature-display mt-2">
                                            <p class="small text-muted mb-1">Signature:</p>
                                            <img src="{{ Storage::url($certificate->lessee1_signature_path) }}"
                                                 alt="Signature" class="img-fluid border rounded" style="max-height: 80px;">
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Lessee 2 -->
                                <div class="col-md-6 mb-3">
                                    <div class="signatory-display border rounded p-3">
                                        <h6 class="bg-success text-white p-2 rounded mb-3">
                                            2. MANAGER
                                        </h6>
                                        <p><strong>Name:</strong> {{ $certificate->lessee2_name }}</p>
                                        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($certificate->lessee2_date)->format('F d, Y') }}</p>
                                        @if($certificate->lessee2_signature_path)
                                        <div class="signature-display mt-2">
                                            <p class="small text-muted mb-1">Signature:</p>
                                            <img src="{{ Storage::url($certificate->lessee2_signature_path) }}"
                                                 alt="Signature" class="img-fluid border rounded" style="max-height: 80px;">
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supporting Documents -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-paperclip me-2"></i>Supporting Documents</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($certificate->test_report_path)
                                <div class="col-md-6 mb-3">
                                    <div class="attachment-card border rounded p-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                            <div>
                                                <h6 class="mb-1">Test Report</h6>
                                                <p class="text-muted mb-0 small">Final test report</p>
                                                <a href="{{ Storage::url($certificate->test_report_path) }}"
                                                   target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                                    <i class="fas fa-eye me-1"></i>View Report
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($certificate->additional_documents_path)
                                <div class="col-md-6 mb-3">
                                    <div class="attachment-card border rounded p-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-alt fa-2x text-primary me-3"></i>
                                            <div>
                                                <h6 class="mb-1">Additional Documents</h6>
                                                <p class="text-muted mb-0 small">Supporting documents</p>
                                                <a href="javascript:void(0)"
                                                   class="btn btn-sm btn-outline-primary mt-2"
                                                   onclick="showAdditionalDocuments()">
                                                    <i class="fas fa-folder-open me-1"></i>View All
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Request Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Design Request Information
                    </h6>
                </div>
                <div class="card-body">
                    @if($certificate->designRequest)
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%"><strong>Request #:</strong></td>
                            <td>{{ $certificate->designRequest->request_number ?? $certificate->designRequest->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Customer:</strong></td>
                            <td>{{ $certificate->designRequest->customer->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Title:</strong></td>
                            <td>{{ $certificate->designRequest->title ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge bg-{{ $certificate->designRequest->status == 'acceptance_certificate_issued' ? 'success' : 'warning' }}">
                                    {{ ucfirst(str_replace('_', ' ', $certificate->designRequest->status)) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Created:</strong></td>
                            <td>{{ $certificate->designRequest->created_at->format('M d, Y') }}</td>
                        </tr>
                    </table>

                    <a href="{{ route('ictengineer.requests.show', $certificate->designRequest) }}"
                       class="btn btn-outline-primary btn-sm w-100 mt-2">
                        <i class="fas fa-external-link-alt me-2"></i>View Request Details
                    </a>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Design request information not available
                    </div>
                    @endif
                </div>
            </div>

            <!-- Certificate Actions -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-download me-2"></i>Certificate Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('ictengineer.certificates.acceptance.download', $certificate) }}"
                           class="btn btn-success">
                            <i class="fas fa-file-archive me-2"></i>Download ZIP Package
                        </a>

                        <a href="{{ route('ictengineer.certificates.acceptance.preview', $certificate) }}"
                           target="_blank" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i>Preview PDF
                        </a>

                        <a href="javascript:window.print()" class="btn btn-outline-secondary">
                            <i class="fas fa-print me-2"></i>Print Certificate
                        </a>
                    </div>
                </div>
            </div>

            <!-- Certificate Timeline -->
            <div class="card shadow">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2"></i>Certificate Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Certificate Created</h6>
                                <p class="text-muted mb-0 small">
                                    {{ $certificate->created_at->format('F d, Y h:i A') }}
                                </p>
                            </div>
                        </div>

                        <div class="timeline-item mb-3">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Effective Date</h6>
                                <p class="text-muted mb-0 small">
                                    {{ \Carbon\Carbon::parse($certificate->effective_date)->format('F d, Y') }}
                                </p>
                            </div>
                        </div>

                        @if($certificate->updated_at != $certificate->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Last Updated</h6>
                                <p class="text-muted mb-0 small">
                                    {{ $certificate->updated_at->format('F d, Y h:i A') }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Documents Modal -->
@if($certificate->additional_documents_path)
<div class="modal fade" id="additionalDocumentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-folder-open me-2"></i>Additional Documents
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @php
                        $additionalDocs = json_decode($certificate->additional_documents_path, true) ?? [];
                    @endphp
                    @foreach($additionalDocs as $index => $docPath)
                    <div class="col-md-6 mb-3">
                        <div class="document-card border rounded p-3">
                            <div class="d-flex align-items-center">
                                @php
                                    $extension = pathinfo($docPath, PATHINFO_EXTENSION);
                                    $icon = 'fa-file';
                                    $color = 'text-secondary';

                                    if (in_array($extension, ['pdf'])) {
                                        $icon = 'fa-file-pdf';
                                        $color = 'text-danger';
                                    } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                        $icon = 'fa-file-image';
                                        $color = 'text-success';
                                    } elseif (in_array($extension, ['doc', 'docx'])) {
                                        $icon = 'fa-file-word';
                                        $color = 'text-primary';
                                    }
                                @endphp
                                <i class="fas {{ $icon }} fa-2x {{ $color }} me-3"></i>
                                <div>
                                    <h6 class="mb-1">Document {{ $index + 1 }}</h6>
                                    <p class="text-muted mb-0 small">{{ strtoupper($extension) }} File</p>
                                    <a href="{{ Storage::url($docPath) }}"
                                       target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="fas fa-eye me-1"></i>View Document
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    function showAdditionalDocuments() {
        const modal = new bootstrap.Modal(document.getElementById('additionalDocumentsModal'));
        modal.show();
    }
</script>
@endpush

@push('styles')
<style>
    .info-card {
        background: #f8f9fa;
        border-left: 4px solid #28a745 !important;
    }

    .signatory-display {
        background: #fff;
        border: 1px solid #dee2e6;
    }

    .attachment-card {
        background: #fff;
        transition: all 0.3s;
    }

    .attachment-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .document-card {
        background: #f8f9fa;
        transition: all 0.3s;
    }

    .document-card:hover {
        background: #fff;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }

    .signature-display img {
        max-width: 100%;
        max-height: 80px;
        object-fit: contain;
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }

    .timeline-item {
        position: relative;
    }

    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .timeline-content {
        padding-left: 10px;
    }
</style>
@endpush
