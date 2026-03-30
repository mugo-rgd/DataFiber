@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Conditional Certificate Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('customer.certificates.conditional.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <a href="{{ route('customer.certificates.conditional.preview', $certificate->id) }}" class="btn btn-sm btn-primary" target="_blank">
                            <i class="fas fa-file-pdf"></i> Preview PDF
                        </a>
                        <a href="{{ route('customer.certificates.conditional.download', $certificate->id) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-download"></i> Download Package
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h5 class="card-title">Certificate Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Reference Number</th>
                                            <td>{{ $certificate->ref_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>Lessor</th>
                                            <td>{{ $certificate->lessor }}</td>
                                        </tr>
                                        <tr>
                                            <th>Lessee</th>
                                            <td>{{ $certificate->lessee }}</td>
                                        </tr>
                                        <tr>
                                            <th>Link Name</th>
                                            <td>{{ $certificate->link_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Engineer Name</th>
                                            <td>{{ $certificate->engineer_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Certificate Date</th>
                                            <td>{{ \Carbon\Carbon::parse($certificate->certificate_date)->format('d-m-Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Commissioning End Date</th>
                                            <td>{{ \Carbon\Carbon::parse($certificate->commissioning_end_date)->format('d-m-Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Certificate Issue Date</th>
                                            <td>{{ \Carbon\Carbon::parse($certificate->certificate_issue_date)->format('d-m-Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card card-info card-outline">
                                <div class="card-header">
                                    <h5 class="card-title">Site Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Site A</th>
                                            <td>{{ $certificate->site_a }}</td>
                                        </tr>
                                        <tr>
                                            <th>Site B</th>
                                            <td>{{ $certificate->site_b }}</td>
                                        </tr>
                                        <tr>
                                            <th>Fibre Technology</th>
                                            <td>{{ $certificate->fibre_technology }}</td>
                                        </tr>
                                        <tr>
                                            <th>ODF Connector Type</th>
                                            <td>{{ $certificate->odf_connector_type }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-info card-outline">
                                <div class="card-header">
                                    <h5 class="card-title">Test Parameters</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">OTDR Serial</th>
                                            <td>{{ $certificate->otdr_serial }}</td>
                                        </tr>
                                        <tr>
                                            <th>Calibration Date</th>
                                            <td>{{ \Carbon\Carbon::parse($certificate->calibration_date)->format('d-m-Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Test Wavelength</th>
                                            <td>{{ $certificate->test_wavelength }}</td>
                                        </tr>
                                        <tr>
                                            <th>IOR</th>
                                            <td>{{ $certificate->ior }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-success card-outline">
                                <div class="card-header">
                                    <h5 class="card-title">Measurement Results</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead class="bg-success text-white">
                                            <tr>
                                                <th>Total Length (km)</th>
                                                <th>Average Loss (dB/km)</th>
                                                <th>Splice Joints</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ number_format($certificate->total_length, 3) }} km</td>
                                                <td>{{ number_format($certificate->average_loss, 3) }} dB/km</td>
                                                <td>{{ $certificate->splice_joints }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($certificate->lessee_contact_name || $certificate->lessee_designation)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-warning card-outline">
                                <div class="card-header">
                                    <h5 class="card-title">Lessee Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        @if($certificate->lessee_contact_name)
                                        <tr>
                                            <th width="30%">Contact Name</th>
                                            <td>{{ $certificate->lessee_contact_name }}</td>
                                        </tr>
                                        @endif
                                        @if($certificate->lessee_designation)
                                        <tr>
                                            <th>Designation</th>
                                            <td>{{ $certificate->lessee_designation }}</td>
                                        </tr>
                                        @endif
                                        @if($certificate->lessee_date)
                                        <tr>
                                            <th>Date</th>
                                            <td>{{ \Carbon\Carbon::parse($certificate->lessee_date)->format('d-m-Y') }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h5 class="card-title">Attached Documents</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info"><i class="fas fa-file-alt"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Inspection Report</span>
                                                    <span class="info-box-number">
                                                        <a href="{{ Storage::url($certificate->inspection_report_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <a href="{{ Storage::url($certificate->inspection_report_path) }}" download class="btn btn-sm btn-success">
                                                            <i class="fas fa-download"></i> Download
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        @if($certificate->engineer_signature_path)
                                        <div class="col-md-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning"><i class="fas fa-signature"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Engineer Signature</span>
                                                    <span class="info-box-number">
                                                        <a href="{{ Storage::url($certificate->engineer_signature_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-danger card-outline">
                                <div class="card-header">
                                    <h5 class="card-title">Related Design Request</h5>
                                </div>
                                <div class="card-body">
                                    @if($certificate->designRequest)
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="30%">Request ID</th>
                                                <td>{{ $certificate->designRequest->id }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    <span class="badge badge-{{ $certificate->designRequest->status == 'completed' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($certificate->designRequest->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Created At</th>
                                                <td>{{ $certificate->designRequest->created_at->format('d-m-Y H:i') }}</td>
                                            </tr>
                                        </table>
                                    @else
                                        <p class="text-muted">No related design request found.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Created: {{ $certificate->created_at->format('d-m-Y H:i:s') }} | Last Updated: {{ $certificate->updated_at->format('d-m-Y H:i:s') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        background-color: #f4f6f9;
    }
    .info-box {
        min-height: 100px;
    }
    .info-box-icon {
        border-radius: 0.25rem 0 0 0.25rem;
    }
    .card-outline {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    }
</style>
@endpush
