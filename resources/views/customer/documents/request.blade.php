{{-- resources/views/customer/documents/request.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('customer.customer-dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customer.documents.index') }}">Documents</a></li>
                        <li class="breadcrumb-item active">Request Documents</li>
                    </ol>
                </div>
                <h4 class="page-title">Request Missing Documents</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">My Projects</h4>
                    <p class="text-muted mb-4">
                        Select a project to request missing documents
                    </p>

                    @if($leases->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-centered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Project Name</th>
                                        <th>Service Type</th>
                                        <th>Route</th>
                                        <th>Status</th>
                                        <th>Missing Documents</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leases as $lease)
                                    @php
                                        // Check for missing documents using CORRECT relationships
                                        $missingDocs = [];

                                    $leaseDocExists = \App\Models\Document::where('lease_id', $lease->id)
                                            ->where('document_type', 'quotations')
                                            ->exists();
                                        if (!$leaseDocExists) {
                                            $missingDocs[] = 'Quotations';
                                        }


                                        // Check for contract (via quotation)
                                       $leaseDocExists = \App\Models\Document::where('lease_id', $lease->id)
                                            ->where('document_type', 'contract_agreements')
                                            ->exists();
                                        if (!$leaseDocExists) {
                                            $missingDocs[] = 'Contract Agreements';
                                        }

                                        // Check for acceptance certificate (via design_request)
                                        $leaseDocExists = \App\Models\Document::where('lease_id', $lease->id)
                                            ->where('document_type', 'certificate_of_acceptance')
                                            ->exists();
                                        if (!$leaseDocExists) {
                                            $missingDocs[] = 'Acceptance Certificate';
                                        }

                                        // Check for conditional certificate (via design_request)
                                        $leaseDocExists = \App\Models\Document::where('lease_id', $lease->id)
                                            ->where('document_type', 'conditional_certificates')
                                            ->exists();
                                        if (!$leaseDocExists) {
                                            $missingDocs[] = 'Conditional Certificate';
                                        }

                                        // Check for lease agreement document
                                        $leaseDocExists = \App\Models\Document::where('lease_id', $lease->id)
                                            ->where('document_type', 'iru_lease_order')
                                            ->exists();
                                        if (!$leaseDocExists) {
                                            $missingDocs[] = 'Lease Agreement';
                                        }

                                        // Check for reports
                                        $reportExists = \App\Models\Document::where('lease_id', $lease->id)
                                            ->where('document_type', 'like', '%test_certificate%')
                                            ->exists();
                                        if (!$reportExists) {
                                            $missingDocs[] = 'Test Certificate';
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <h5 class="font-15 mb-1 fw-semibold">{{ $lease->title }}</h5>
                                            <span class="text-muted font-12">#{{ $lease->lease_number }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $lease->start_location }} → {{ $lease->end_location }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $lease->status == 'active' ? 'success' : ($lease->status == 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($lease->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if(count($missingDocs) > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($missingDocs as $doc)
                                                        <span class="badge bg-danger">{{ $doc }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="badge bg-success">All Documents Available</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(count($missingDocs) > 0)
                                                <button type="button" class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#requestModal{{ $lease->id }}">
                                                    <i class="fas fa-file-import me-1"></i> Request
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-success" disabled>
                                                    <i class="fas fa-check me-1"></i> Complete
                                                </button>
                                            @endif
                                            <a href="{{ route('customer.documents.lease.show', $lease->id) }}"
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Request Modal -->
                                    <div class="modal fade" id="requestModal{{ $lease->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('customer.documents.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="lease_id" value="{{ $lease->id }}">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Request Documents for {{ $lease->title }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Select Missing Documents</label>
                                                            <div>
                                                                <!-- Quotation Checkbox -->

                                                                  @if(!\App\Models\Document::where('lease_id', $lease->id)->where('document_type', 'quotations')->exists())
                                                                <div class="form-check mb-2">
                                                                    <input type="checkbox" class="form-check-input"
                                                                           id="quotation_{{ $lease->id }}"
                                                                           name="document_types[]" value="contract" checked>
                                                                    <label class="form-check-label" for="quotation_{{ $lease->id }}">
                                                                        <i class="fas fa-file-invoice me-1"></i> Quotation
                                                                    </label>
                                                                </div>
                                                                @endif

                                                                <!-- Contract Checkbox (only if quotation exists) -->

                                                                  @if(!\App\Models\Document::where('lease_id', $lease->id)->where('document_type', 'contract_agreements')->exists())
                                                                <div class="form-check mb-2">
                                                                    <input type="checkbox" class="form-check-input"
                                                                           id="contract_{{ $lease->id }}"
                                                                           name="document_types[]" value="contract" checked>
                                                                    <label class="form-check-label" for="contract_{{ $lease->id }}">
                                                                        <i class="fas fa-file-contract me-1"></i> Contract
                                                                    </label>
                                                                </div>
                                                                @endif

                                                                <!-- Acceptance Certificate Checkbox -->

                                                                  @if(!\App\Models\Document::where('lease_id', $lease->id)->where('document_type', 'certificate_of_acceptance')->exists())
                                                                <div class="form-check mb-2">
                                                                    <input type="checkbox" class="form-check-input"
                                                                           id="acceptance_{{ $lease->id }}"
                                                                           name="document_types[]" value="acceptance_certificate" checked>
                                                                    <label class="form-check-label" for="acceptance_{{ $lease->id }}">
                                                                        <i class="fas fa-file-certificate me-1"></i> Acceptance Certificate
                                                                    </label>
                                                                </div>
                                                                @endif

                                                                <!-- Conditional Certificate Checkbox -->

                                                                 @if(!\App\Models\Document::where('lease_id', $lease->id)->where('document_type', 'conditional_certificates')->exists())
                                                                <div class="form-check mb-2">
                                                                    <input type="checkbox" class="form-check-input"
                                                                           id="conditional_{{ $lease->id }}"
                                                                           name="document_types[]" value="conditional_certificate" checked>
                                                                    <label class="form-check-label" for="conditional_{{ $lease->id }}">
                                                                        <i class="fas fa-file-certificate me-1"></i> Conditional Certificate
                                                                    </label>
                                                                </div>
                                                                @endif

                                                                <!-- Lease Agreement Checkbox -->
                                                                @if(!\App\Models\Document::where('lease_id', $lease->id)->where('document_type', 'iru_lease_order')->exists())
                                                                <div class="form-check mb-2">
                                                                    <input type="checkbox" class="form-check-input"
                                                                           id="lease_{{ $lease->id }}"
                                                                           name="document_types[]" value="lease" checked>
                                                                    <label class="form-check-label" for="lease_{{ $lease->id }}">
                                                                        <i class="fas fa-file-signature me-1"></i> Lease Agreement
                                                                    </label>
                                                                </div>
                                                                @endif

                                                                <!-- Reports Checkbox -->
                                                                @if(!\App\Models\Document::where('lease_id', $lease->id)->where('document_type', 'like', '%test_certificate%')->exists())
                                                                <div class="form-check mb-2">
                                                                    <input type="checkbox" class="form-check-input"
                                                                           id="report_{{ $lease->id }}"
                                                                           name="document_types[]" value="report" checked>
                                                                    <label class="form-check-label" for="report_{{ $lease->id }}">
                                                                        <i class="fas fa-chart-line me-1"></i> Test Reports
                                                                    </label>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="notes{{ $lease->id }}" class="form-label">Additional Notes</label>
                                                            <textarea class="form-control" id="notes{{ $lease->id }}"
                                                                      name="notes" rows="3"
                                                                      placeholder="Please specify any additional requirements..."></textarea>
                                                        </div>

                                                        <div class="alert alert-info">
                                                            <i class="fas fa-info-circle me-2"></i>
                                                            Requested documents will be processed within 2-3 business days.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Submit Request</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            You don't have any active projects. Please create a lease request first.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Document Types Available</h4>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-invoice text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Quotations</h6>
                                <small class="text-muted">Pricing and scope of work</small>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-contract text-warning"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Contracts</h6>
                                <small class="text-muted">Service agreements and terms</small>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-certificate text-success"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Acceptance Certificates</h6>
                                <small class="text-muted">Service acceptance documentation</small>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-certificate text-info"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Conditional Certificates</h6>
                                <small class="text-muted">Technical compliance certificates</small>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-signature text-danger"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Lease Agreements</h6>
                                <small class="text-muted">Official lease documents</small>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-chart-line text-dark"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Test Reports</h6>
                                <small class="text-muted">Performance and quality reports</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5 class="font-16">Need Help?</h5>
                        <p class="text-muted">
                            Contact our document support team for assistance.
                        </p>
                        <div class="d-grid gap-2">
                            <a href="mailto:documents@darkfibre-crm.test" class="btn btn-outline-dark">
                                <i class="fas fa-envelope me-1"></i> Email Support
                            </a>
                            <a href="tel:+254700000000" class="btn btn-outline-primary">
                                <i class="fas fa-phone me-1"></i> Call Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
