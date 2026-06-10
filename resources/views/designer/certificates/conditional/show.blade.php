@extends('layouts.app')

@section('title', 'Conditional Certificate Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-file-contract text-info me-2"></i>Conditional Certificate Details
                    </h1>
                    <p class="text-muted mb-0">Certificate #: {{ $certificate->ref_number }}</p>
                </div>
                <div>
                    <a href="{{ route('designer.certificates.conditional.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                    <a href="{{ route('ictengineer.certificates.conditional.download', $certificate) }}" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Download certificate
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Certificate Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Reference Number:</strong> {{ $certificate->ref_number }}</p>
                            <p><strong>Request Number:</strong> #{{ $certificate->designRequest->request_number ?? 'N/A' }}</p>
                            <p><strong>Customer:</strong> {{ $certificate->designRequest->customer->name ?? 'N/A' }}</p>
                            <p><strong>Link Name:</strong> {{ $certificate->link_name }}</p>
                            <p><strong>Lessor:</strong> {{ $certificate->lessor }}</p>
                            <p><strong>Lessee:</strong> {{ $certificate->lessee }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Issue Date:</strong> {{ $certificate->certificate_date ? Carbon\Carbon::parse($certificate->certificate_date)->format('F d, Y') : 'N/A' }}</p>
                            <p><strong>Commissioning End Date:</strong> {{ $certificate->commissioning_end_date ? Carbon\Carbon::parse($certificate->commissioning_end_date)->format('F d, Y') : 'N/A' }}</p>
                            <p><strong>Engineer Name:</strong> {{ $certificate->engineer_name }}</p>
                            <p><strong>Status:</strong>
                                <span class="badge bg-{{ $certificate->certificate_status === 'acknowledged' ? 'success' : ($certificate->certificate_status === 'rejected' ? 'danger' : 'warning') }} rounded-pill px-3 py-1">
                                    {{ ucfirst(str_replace('_', ' ', $certificate->certificate_status)) }}
                                </span>
                            </p>
                            <p><strong>ICT Engineer:</strong> {{ $certificate->ictEngineer->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Site Information</h6>
                            <p><strong>Site A:</strong> {{ $certificate->site_a }}</p>
                            <p><strong>Site B:</strong> {{ $certificate->site_b }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Technical Specifications</h6>
                            <p><strong>Fibre Technology:</strong> {{ $certificate->fibre_technology }}</p>
                            <p><strong>Total Length:</strong> {{ number_format($certificate->total_length, 3) }} km</p>
                            <p><strong>Average Loss:</strong> {{ number_format($certificate->average_loss, 2) }} dB</p>
                            <p><strong>Splice Joints:</strong> {{ $certificate->splice_joints }}</p>
                        </div>
                    </div>

                    @if($certificate->conditions)
                    <hr>
                    <div class="alert alert-info">
                        <h6 class="fw-bold">Conditions:</h6>
                        <p class="mb-0">{{ $certificate->conditions }}</p>
                    </div>
                    @endif

                    @if($certificate->remarks)
                    <div class="alert alert-secondary">
                        <h6 class="fw-bold">Remarks:</h6>
                        <p class="mb-0">{{ $certificate->remarks }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Actions</h5>
                </div>
                <div class="card-body">
                    @if($certificate->certificate_status === 'sent_to_designer')
                        <form action="{{ route('designer.certificates.conditional.acknowledge', $certificate) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-check-circle me-2"></i>Acknowledge Certificate
                            </button>
                        </form>
                    @endif

                    @if($certificate->inspection_report_path)
                        <a href="{{ Storage::disk('public')->url($certificate->inspection_report_path) }}" target="_blank" class="btn btn-outline-info w-100 mb-2">
                            <i class="fas fa-file-pdf me-2"></i>View Inspection Report
                        </a>
                    @endif

                    @php
                        $daysSince = $certificate->commissioning_end_date ? Carbon\Carbon::parse($certificate->commissioning_end_date)->diffInDays(now()) : 0;
                        $acceptanceReady = $daysSince >= 30;
                    @endphp

                    @if($acceptanceReady && !$certificate->designRequest->acceptanceCertificate)
                        <a href="{{ route('designer.certificates.acceptance.create', $certificate->designRequest) }}" class="btn btn-success w-100">
                            <i class="fas fa-file-signature me-2"></i>Generate Acceptance Certificate
                        </a>
                    @elseif(!$acceptanceReady && $certificate->certificate_status === 'acknowledged')
                        <div class="alert alert-warning text-center mb-0">
                            <i class="fas fa-clock me-2"></i>
                            Acceptance certificate available in {{ 30 - $daysSince }} days
                        </div>
                    @endif
                </div>
            </div>

            @if($certificate->inspection_report_path)
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Inspection Report</h5>
                </div>
                <div class="card-body p-0">
                    <iframe src="{{ Storage::disk('public')->url($certificate->inspection_report_path) }}"
                            style="width: 100%; height: 400px; border: none;"
                            title="Inspection Report">
                    </iframe>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
