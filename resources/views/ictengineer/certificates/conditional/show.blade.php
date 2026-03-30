@extends('layouts.app')

@section('title', 'View Conditional Certificate - ICT Engineer')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-file-contract text-warning me-2"></i>Conditional Certificate
                </h1>
                <p class="text-muted mb-0">ICT Engineer - View Certificate Details</p>
            </div>
            <div class="btn-group">
                <!-- FIXED: Changed to JavaScript back button -->
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary text-decoration-none">
    <i class="fas fa-arrow-left me-2"></i>Back
</a>
                <a href="{{ route('ictengineer.certificates.conditional.download', $certificate) }}"
                   class="btn btn-warning">
                    <i class="fas fa-download me-2"></i>Download Certificate
                </a>
            </div>
        </div>
    </div>
</div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-file-contract me-2"></i>
                            Conditional Certificate Details
                        </h5>
                        <span class="badge bg-white text-warning fs-6">
                            {{ $certificate->status ?? 'Issued' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Certificate Header -->
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-primary">Conditional Certificate of Inspection</h4>
                        <p class="text-muted">THE KENYA POWER & LIGHTING COMPANY PLC</p>
                    </div>

                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-card border rounded p-3 mb-3">
                                <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Certificate Information</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="40%"><strong>Certificate No:</strong></td>
                                        <td>{{ $certificate->certificate_number ?? $certificate->ref_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Reference No:</strong></td>
                                        <td>{{ $certificate->ref_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Issued Date:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($certificate->issued_date ?? $certificate->certificate_date)->format('F d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Valid Until:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($certificate->commissioning_end_date)->format('F d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $certificate->status == 'issued' ? 'success' : 'warning' }}">
                                                {{ ucfirst($certificate->status ?? 'issued') }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card border rounded p-3 mb-3">
                                <h6 class="text-primary mb-3"><i class="fas fa-building me-2"></i>Parties Information</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="40%"><strong>Lessor:</strong></td>
                                        <td>{{ $certificate->lessor }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Lessee:</strong></td>
                                        <td>{{ $certificate->lessee }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Link Name:</strong></td>
                                        <td>{{ $certificate->link_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Engineer:</strong></td>
                                        <td>{{ $certificate->engineer_name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Technical Details -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Technical Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="50%"><strong>Site A:</strong></td>
                                            <td>{{ $certificate->site_a }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Site B:</strong></td>
                                            <td>{{ $certificate->site_b }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Fibre Technology:</strong></td>
                                            <td>{{ $certificate->fibre_technology }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>ODF Connector Type:</strong></td>
                                            <td>{{ $certificate->odf_connector_type }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="50%"><strong>Total Length:</strong></td>
                                            <td>{{ number_format($certificate->total_length, 3) }} KM</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Average Loss:</strong></td>
                                            <td>{{ number_format($certificate->average_loss, 2) }} dB</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Splice Joints:</strong></td>
                                            <td>{{ $certificate->splice_joints }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Test Wavelength:</strong></td>
                                            <td>{{ $certificate->test_wavelength }} nm</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Test Equipment -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Test Equipment Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>OTDR Serial Number:</strong> {{ $certificate->otdr_serial }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Calibration Date:</strong> {{ \Carbon\Carbon::parse($certificate->calibration_date)->format('F d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Conditions -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Conditions for Full Certification</h6>
                        </div>
                        <div class="card-body">
                            <div class="conditions-content">
                                {!! nl2br(e($certificate->conditions)) !!}
                            </div>

                            @if($certificate->remarks)
                            <div class="mt-4">
                                <h6><i class="fas fa-sticky-note me-2"></i>Remarks</h6>
                                <p class="mb-0">{!! nl2br(e($certificate->remarks)) !!}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Attachments -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-paperclip me-2"></i>Attachments</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($certificate->inspection_report_path)
                                <div class="col-md-6 mb-3">
                                    <div class="attachment-card border rounded p-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                            <div>
                                                <h6 class="mb-1">Inspection Report</h6>
                                                <p class="text-muted mb-0 small">Uploaded on {{ \Carbon\Carbon::parse($certificate->created_at)->format('M d, Y') }}</p>
                                                <a href="{{ Storage::url($certificate->inspection_report_path) }}"
                                                   target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                                    <i class="fas fa-eye me-1"></i>View Report
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
@if(!empty($certificate->engineer_signature_path))
    @php
        // Check if file exists
        try {
            $fileExists = Storage::exists($certificate->engineer_signature_path);
            $fileUrl = Storage::url($certificate->engineer_signature_path);
        } catch (Exception $e) {
            $fileExists = false;
            $fileUrl = null;
        }
    @endphp

    @if($fileExists)
        <div class="col-md-6 mb-3">
            <div class="attachment-card border rounded p-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-signature fa-2x text-primary me-3"></i>
                    <div>
                        <h6 class="mb-1">Engineer Signature</h6>
                        <p class="text-muted mb-0 small">Digital signature</p>
                        <a href="{{ $fileUrl }}"
                           target="_blank"
                           class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-eye me-1"></i>View Signature
                        </a>
                        <a href="{{ $fileUrl }}"
                           download
                           class="btn btn-sm btn-outline-secondary mt-2">
                            <i class="fas fa-download me-1"></i>Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-6 mb-3">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Signature file not found at: {{ $certificate->engineer_signature_path }}
            </div>
        </div>
    @endif
@else
    <div class="col-md-6 mb-3">
        <div class="attachment-card border rounded p-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-signature fa-2x text-secondary me-3"></i>
                <div>
                    <h6 class="mb-1">Engineer Signature</h6>
                    <p class="text-muted mb-0 small">No signature uploaded</p>
                    <span class="badge bg-warning mt-2">Pending</span>
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
                                <span class="badge bg-{{ $certificate->designRequest->status == 'conditional_certificate_issued' ? 'success' : 'warning' }}">
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
                        <a href="{{ route('ictengineer.certificates.conditional.download', $certificate) }}"
                           class="btn btn-warning">
                            <i class="fas fa-file-archive me-2"></i>Download ZIP Package
                        </a>

                        <a href="{{ route('ictengineer.certificates.conditional.preview', $certificate) }}"
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
                                <h6 class="mb-1">Valid Until</h6>
                                <p class="text-muted mb-0 small">
                                    {{ \Carbon\Carbon::parse($certificate->commissioning_end_date)->format('F d, Y') }}
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
@endsection

@push('styles')
<style>
    .info-card {
        background: #f8f9fa;
        border-left: 4px solid #ffc107 !important;
    }

    .attachment-card {
        background: #fff;
        transition: all 0.3s;
    }

    .attachment-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .conditions-content {
        line-height: 1.8;
        white-space: pre-line;
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

    .btn-group .btn {
        border-radius: 0.375rem;
    }

    @media (max-width: 768px) {
        .btn-group {
            flex-direction: column;
            width: 100%;
        }

        .btn-group .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush
