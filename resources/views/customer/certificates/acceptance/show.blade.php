@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Acceptance Certificate Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('customer.certificates.acceptance.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <a href="{{ route('customer.certificates.acceptance.preview', $certificate->id) }}" class="btn btn-sm btn-primary" target="_blank">
                            <i class="fas fa-file-pdf"></i> Preview PDF
                        </a>
                        <a href="{{ route('customer.certificates.acceptance.download', $certificate->id) }}" class="btn btn-sm btn-success">
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
                                            <th width="30%">Certificate Reference</th>
                                            <td>{{ $certificate->certificate_ref }}</td>
                                        </tr>
                                        <tr>
                                            <th>To Company</th>
                                            <td>{{ $certificate->to_company }}</td>
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
                                            <th>Effective Date</th>
                                            <td>{{ \Carbon\Carbon::parse($certificate->effective_date)->format('d-m-Y') }}</td>
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
                                    <h5 class="card-title">Route Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Route Name</th>
                                            <td>{{ $certificate->route_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Link Name</th>
                                            <td>{{ $certificate->link_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Cable Type</th>
                                            <td>{{ $certificate->cable_type }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-info card-outline">
                                <div class="card-header">
                                    <h5 class="card-title">Technical Details</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Distance</th>
                                            <td>{{ number_format($certificate->distance, 2) }} km</td>
                                        </tr>
                                        <tr>
                                            <th>Cores Count</th>
                                            <td>{{ $certificate->cores_count }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Witnesses Section -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-success card-outline">
                                <div class="card-header">
                                    <h5 class="card-title">Witnesses</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Witness 1 -->
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header bg-info text-white">
                                                    <h6 class="card-title">Witness 1</h6>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th>Name:</th>
                                                            <td>{{ $certificate->witness1_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Date:</th>
                                                            <td>{{ \Carbon\Carbon::parse($certificate->witness1_date)->format('d-m-Y') }}</td>
                                                        </tr>
                                                        @if($certificate->witness1_signature_path)
                                                        <tr>
                                                            <th>Signature:</th>
                                                            <td>
                                                                <a href="{{ Storage::url($certificate->witness1_signature_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-signature"></i> View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Witness 2 -->
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header bg-info text-white">
                                                    <h6 class="card-title">Witness 2</h6>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th>Name:</th>
                                                            <td>{{ $certificate->witness2_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Date:</th>
                                                            <td>{{ \Carbon\Carbon::parse($certificate->witness2_date)->format('d-m-Y') }}</td>
                                                        </tr>
                                                        @if($certificate->witness2_signature_path)
                                                        <tr>
                                                            <th>Signature:</th>
                                                            <td>
                                                                <a href="{{ Storage::url($certificate->witness2_signature_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-signature"></i> View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                        @if($certificate->witness2_stamp_path)
                                                        <tr>
                                                            <th>Stamp:</th>
                                                            <td>
                                                                <a href="{{ Storage::url($certificate->witness2_stamp_path) }}" target="_blank" class="btn btn-sm btn-warning">
                                                                    <i class="fas fa-stamp"></i> View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Witness 3 -->
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header bg-info text-white">
                                                    <h6 class="card-title">Witness 3</h6>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th>Name:</th>
                                                            <td>{{ $certificate->witness3_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Date:</th>
                                                            <td>{{ \Carbon\Carbon::parse($certificate->witness3_date)->format('d-m-Y') }}</td>
                                                        </tr>
                                                        @if($certificate->witness3_signature_path)
                                                        <tr>
                                                            <th>Signature:</th>
                                                            <td>
                                                                <a href="{{ Storage::url($certificate->witness3_signature_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-signature"></i> View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                        @if($certificate->witness3_stamp_path)
                                                        <tr>
                                                            <th>Stamp:</th>
                                                            <td>
                                                                <a href="{{ Storage::url($certificate->witness3_stamp_path) }}" target="_blank" class="btn btn-sm btn-warning">
                                                                    <i class="fas fa-stamp"></i> View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lessee Representatives Section -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-warning card-outline">
                                <div class="card-header">
                                    <h5 class="card-title">Lessee Representatives</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Lessee 1 -->
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header bg-warning">
                                                    <h6 class="card-title">Lessee Representative 1</h6>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th>Name:</th>
                                                            <td>{{ $certificate->lessee1_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Date:</th>
                                                            <td>{{ \Carbon\Carbon::parse($certificate->lessee1_date)->format('d-m-Y') }}</td>
                                                        </tr>
                                                        @if($certificate->lessee1_signature_path)
                                                        <tr>
                                                            <th>Signature:</th>
                                                            <td>
                                                                <a href="{{ Storage::url($certificate->lessee1_signature_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-signature"></i> View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                        @if($certificate->lessee1_stamp_path)
                                                        <tr>
                                                            <th>Stamp:</th>
                                                            <td>
                                                                <a href="{{ Storage::url($certificate->lessee1_stamp_path) }}" target="_blank" class="btn btn-sm btn-warning">
                                                                    <i class="fas fa-stamp"></i> View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Lessee 2 -->
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header bg-warning">
                                                    <h6 class="card-title">Lessee Representative 2</h6>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th>Name:</th>
                                                            <td>{{ $certificate->lessee2_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Date:</th>
                                                            <td>{{ \Carbon\Carbon::parse($certificate->lessee2_date)->format('d-m-Y') }}</td>
                                                        </tr>
                                                        @if($certificate->lessee2_signature_path)
                                                        <tr>
                                                            <th>Signature:</th>
                                                            <td>
                                                                <a href="{{ Storage::url($certificate->lessee2_signature_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-signature"></i> View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                        @if($certificate->lessee2_stamp_path)
                                                        <tr>
                                                            <th>Stamp:</th>
                                                            <td>
                                                                <a href="{{ Storage::url($certificate->lessee2_stamp_path) }}" target="_blank" class="btn btn-sm btn-warning">
                                                                    <i class="fas fa-stamp"></i> View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Section -->
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
                                                <span class="info-box-icon bg-info"><i class="fas fa-file-pdf"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Test Report</span>
                                                    <span class="info-box-number">
                                                        <a href="{{ Storage::url($certificate->test_report_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <a href="{{ Storage::url($certificate->test_report_path) }}" download class="btn btn-sm btn-success">
                                                            <i class="fas fa-download"></i> Download
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        @if($certificate->additional_documents_path)
                                            @php $additionalDocs = json_decode($certificate->additional_documents_path, true) ?? []; @endphp
                                            @foreach($additionalDocs as $index => $docPath)
                                            <div class="col-md-4">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-secondary"><i class="fas fa-file"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Additional Document {{ $index + 1 }}</span>
                                                        <span class="info-box-number">
                                                            <a href="{{ Storage::url($docPath) }}" target="_blank" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i> View
                                                            </a>
                                                            <a href="{{ Storage::url($docPath) }}" download class="btn btn-sm btn-success">
                                                                <i class="fas fa-download"></i> Download
                                                            </a>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related Design Request -->
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
                                            @if($certificate->designRequest->client)
                                            <tr>
                                                <th>Client Name</th>
                                                <td>{{ $certificate->designRequest->client->name }}</td>
                                            </tr>
                                            @endif
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
    .card-header.bg-info, .card-header.bg-warning {
        padding: 0.75rem 1.25rem;
    }
</style>
@endpush
