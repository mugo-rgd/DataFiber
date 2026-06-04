@extends('layouts.app')

@section('title', 'Acceptance Certificate Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-file-signature text-success me-2"></i>Acceptance Certificate Details
                    </h1>
                    <p class="text-muted mb-0">Certificate #: {{ $certificate->certificate_ref }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('designer.certificates.acceptance.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                    <a href="{{ route('designer.certificates.acceptance.download', $certificate) }}" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Download Certificate
                    </a>
                    @if($certificate->test_report_path)
                        <a href="{{ Storage::disk('public')->url($certificate->test_report_path) }}" class="btn btn-info" target="_blank">
                            <i class="fas fa-file-pdf me-2"></i>View Test Report
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Certificate Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-info-circle text-kp-blue me-2"></i>Certificate Information
                    </h5>
                    <span class="badge bg-{{ $certificate->status_badge_color }} rounded-pill px-3 py-2">
                        <i class="fas fa-{{ $certificate->status === 'issued' ? 'check-circle' : 'clock' }} me-1"></i>
                        {{ $certificate->status_label }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%" class="text-muted"><strong>Certificate Ref:</strong></td>
                                    <td><code>{{ $certificate->certificate_ref }}</code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Request Number:</strong></td>
                                    <td>
                                        <a href="{{ route('designer.requests.show', $certificate->designRequest) }}" class="text-decoration-none">
                                            #{{ $certificate->designRequest->request_number ?? 'N/A' }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Customer:</strong></td>
                                    <td>{{ $certificate->designRequest->customer->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Route Name:</strong></td>
                                    <td>{{ $certificate->route_name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Link Name:</strong></td>
                                    <td>{{ $certificate->link_name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Cable Type:</strong></td>
                                    <td><span class="badge bg-secondary">{{ $certificate->cable_type }}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%" class="text-muted"><strong>Effective Date:</strong></td>
                                    <td>
                                        <i class="fas fa-calendar-alt me-1 text-muted"></i>
                                        {{ $certificate->effective_date ? Carbon\Carbon::parse($certificate->effective_date)->format('F d, Y') : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Distance:</strong></td>
                                    <td><i class="fas fa-ruler me-1 text-muted"></i>{{ number_format($certificate->distance, 3) }} km</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Cores Count:</strong></td>
                                    <td><i class="fas fa-microchip me-1 text-muted"></i>{{ $certificate->cores_count }} cores</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Lessee:</strong></td>
                                    <td>{{ $certificate->lessee }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Lessee Address:</strong></td>
                                    <td>{{ $certificate->lessee_address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Lessee Contact:</strong></td>
                                    <td>{{ $certificate->lessee_contact ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="alert alert-light mt-3">
                        <div class="row text-center">
                            <div class="col-3">
                                <div class="small text-muted">Issued By</div>
                                <div class="fw-bold">{{ $certificate->designer->name ?? Auth::user()->name }}</div>
                            </div>
                            <div class="col-3">
                                <div class="small text-muted">Issued On</div>
                                <div class="fw-bold">{{ $certificate->created_at->format('M d, Y') }}</div>
                            </div>
                            <div class="col-3">
                                <div class="small text-muted">Last Updated</div>
                                <div class="fw-bold">{{ $certificate->updated_at->format('M d, Y') }}</div>
                            </div>
                            <div class="col-3">
                                <div class="small text-muted">Status</div>
                                <div class="fw-bold text-{{ $certificate->status_badge_color }}">
                                    {{ $certificate->status_label }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kenya Power Signatories -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-signature text-kp-blue me-2"></i>Kenya Power Signatories
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $witnesses = [
                                1 => ['name' => $certificate->witness1_name, 'date' => $certificate->witness1_date, 'title' => 'INFRASTRUCTURE SUPPORT ENGINEER - TBU (WITNESS)'],
                                2 => ['name' => $certificate->witness2_name, 'date' => $certificate->witness2_date, 'title' => 'TELECOM LEAD ENGINEER'],
                                3 => ['name' => $certificate->witness3_name, 'date' => $certificate->witness3_date, 'title' => 'TELECOM MANAGER'],
                            ];
                        @endphp
                        @foreach($witnesses as $num => $witness)
                            <div class="col-md-4 mb-3">
                                <div class="signatory-card p-3 border rounded h-100">
                                    <div class="signatory-header bg-kp-blue text-white p-2 rounded mb-3">
                                        <i class="fas fa-user-check me-2"></i> Witness {{ $num }}
                                    </div>
                                    <div class="signatory-body">
                                        <p class="mb-1"><strong>Name:</strong> {{ $witness['name'] }}</p>
                                        <p class="mb-1"><strong>Title:</strong> {{ $witness['title'] }}</p>
                                        <p class="mb-0"><strong>Date:</strong> {{ $witness['date'] ? Carbon\Carbon::parse($witness['date'])->format('F d, Y') : 'N/A' }}</p>
                                    </div>
                                    @if($certificate->{'witness' . $num . '_signature_path'})
                                        <div class="signature-area mt-2 pt-2 border-top">
                                            <small class="text-muted">Signature:</small>
                                            <div class="signature-preview mt-1">
                                                <img src="{{ Storage::disk('public')->url($certificate->{'witness' . $num . '_signature_path'}) }}"
                                                     alt="Signature" class="img-fluid" style="max-height: 50px;">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Lessee Signatories -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-user-tie text-kp-green me-2"></i>Lessee Signatories
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $lessees = [
                                1 => ['name' => $certificate->lessee1_name, 'date' => $certificate->lessee1_date, 'title' => 'LEAD ENGINEER / TECHNICAL REPRESENTATIVE'],
                                2 => ['name' => $certificate->lessee2_name, 'date' => $certificate->lessee2_date, 'title' => 'MANAGER'],
                            ];
                        @endphp
                        @foreach($lessees as $num => $lessee)
                            <div class="col-md-6 mb-3">
                                <div class="signatory-card p-3 border rounded">
                                    <div class="signatory-header bg-kp-green text-white p-2 rounded mb-3">
                                        <i class="fas fa-user-tie me-2"></i> Lessee Representative {{ $num }}
                                    </div>
                                    <div class="signatory-body">
                                        <p class="mb-1"><strong>Name:</strong> {{ $lessee['name'] }}</p>
                                        <p class="mb-1"><strong>Title:</strong> {{ $lessee['title'] }}</p>
                                        <p class="mb-0"><strong>Date:</strong> {{ $lessee['date'] ? Carbon\Carbon::parse($lessee['date'])->format('F d, Y') : 'N/A' }}</p>
                                    </div>
                                    @if($certificate->{'lessee' . $num . '_signature_path'})
                                        <div class="signature-area mt-2 pt-2 border-top">
                                            <small class="text-muted">Signature:</small>
                                            <div class="signature-preview mt-1">
                                                <img src="{{ Storage::disk('public')->url($certificate->{'lessee' . $num . '_signature_path'}) }}"
                                                     alt="Signature" class="img-fluid" style="max-height: 50px;">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Test Report -->
            @if($certificate->test_report_path)
                <div class="card shadow mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-file-pdf text-danger me-2"></i>Test Report
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <iframe src="{{ Storage::disk('public')->url($certificate->test_report_path) }}"
                                style="width: 100%; height: 400px; border: none;"
                                title="Test Report">
                        </iframe>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ Storage::disk('public')->url($certificate->test_report_path) }}" class="btn btn-sm btn-danger w-100" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>Open Full Report
                        </a>
                    </div>
                </div>
            @endif

            <!-- Additional Documents -->
            @if($certificate->additional_documents_path && count($certificate->additional_documents_urls) > 0)
                <div class="card shadow mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-paperclip text-secondary me-2"></i>Additional Documents
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($certificate->additional_documents_urls as $url)
                                <a href="{{ $url }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" target="_blank">
                                    <span>
                                        <i class="fas fa-file-{{ str_contains($url, '.pdf') ? 'pdf text-danger' : (str_contains($url, '.jpg') ? 'image text-info' : 'alt') }} me-2"></i>
                                        {{ basename($url) }}
                                    </span>
                                    <i class="fas fa-download text-muted"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-cog text-secondary me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('designer.requests.show', $certificate->designRequest) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i>View Design Request
                        </a>
                        @if($certificate->lease_id)
                            <a href="{{ route('designer.leases.show', $certificate->lease_id) }}" class="btn btn-outline-info">
                                <i class="fas fa-file-contract me-2"></i>View Lease Agreement
                            </a>
                        @endif
                        <a href="{{ route('designer.certificates.acceptance.download', $certificate) }}" class="btn btn-success">
                            <i class="fas fa-download me-2"></i>Download Certificate
                        </a>
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print();">
                            <i class="fas fa-print me-2"></i>Print Certificate
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.signatory-card {
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.signatory-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.signatory-header {
    font-size: 0.85rem;
    font-weight: 600;
    text-align: center;
}

.bg-kp-blue {
    background-color: #0066B3 !important;
}

.bg-kp-green {
    background-color: #009639 !important;
}

.text-kp-blue {
    color: #0066B3 !important;
}

.text-kp-green {
    color: #009639 !important;
}

.signature-preview {
    background: white;
    border-radius: 4px;
    padding: 5px;
    text-align: center;
}

.table-sm td, .table-sm th {
    padding: 0.5rem;
}

@media print {
    .btn, .btn-group, .d-flex.gap-2, .card-footer, .sidebar-actions {
        display: none !important;
    }

    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }

    body {
        background: white;
        padding: 0;
        margin: 0;
    }
}
</style>
@endsection
