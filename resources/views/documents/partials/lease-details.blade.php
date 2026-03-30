<div class="document-details">
    <h5 class="mb-4"><i class="fas fa-file-contract text-primary mr-2"></i>Lease Details</h5>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Lease Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Lease Number:</th>
                            <td><strong>{{ $document->lease_number }}</strong></td>
                        </tr>
                        <tr>
                            <th>Title:</th>
                            <td>{{ $document->title ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Customer:</th>
                            <td>{{ $document->customer->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Customer Company:</th>
                            <td>
                                <span class="badge badge-info">
                                    {{ $document->customer->company_name ?? 'N/A' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Design Request:</th>
                            <td>
                                @if($document->designRequest)
                                    <a href="{{ route('design-requests.show', $document->designRequest->id) }}" class="text-primary">
                                        {{ $document->designRequest->request_number }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Service Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Service Type:</th>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ str_replace('_', ' ', $document->service_type) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Technology:</th>
                            <td>{{ $document->technology ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Cores Required:</th>
                            <td>{{ $document->cores_required ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Bandwidth:</th>
                            <td>{{ $document->bandwidth ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Billing Cycle:</th>
                            <td>{{ $document->billing_cycle }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Location Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Start Location:</th>
                            <td>{{ $document->start_location }}</td>
                        </tr>
                        <tr>
                            <th>End Location:</th>
                            <td>{{ $document->end_location }}</td>
                        </tr>
                        <tr>
                            <th>Distance:</th>
                            <td>{{ $document->distance_km }} km</td>
                        </tr>
                        @if($document->county)
                            <tr>
                                <th>County:</th>
                                <td>{{ $document->county->name ?? 'N/A' }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Financial Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Monthly Cost:</th>
                            <td><strong>{{ number_format($document->monthly_cost, 2) }} {{ $document->currency }}</strong></td>
                        </tr>
                        <tr>
                            <th>Installation Fee:</th>
                            <td>{{ number_format($document->installation_fee, 2) }} {{ $document->currency }}</td>
                        </tr>
                        <tr>
                            <th>Total Contract Value:</th>
                            <td class="text-success">
                                <strong>{{ number_format($document->total_contract_value, 2) }} {{ $document->currency }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Contract Term:</th>
                            <td>{{ $document->contract_term_months }} months</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Contract Period</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Start Date:</th>
                            <td>{{ $document->start_date }}</td>
                        </tr>
                        <tr>
                            <th>End Date:</th>
                            <td>{{ $document->end_date }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge badge-{{ getStatusColor($document->status) }}">
                                    {{ $document->status }}
                                </span>
                            </td>
                        </tr>
                        @if($document->next_billing_date)
                            <tr>
                                <th>Next Billing Date:</th>
                                <td>{{ $document->next_billing_date }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Timestamps</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Created:</th>
                            <td>{{ $document->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $document->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @if($document->sent_at)
                            <tr>
                                <th>Sent to Customer:</th>
                                <td>{{ $document->sent_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endif
                        @if($document->accepted_at)
                            <tr>
                                <th>Accepted:</th>
                                <td>{{ $document->accepted_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endif
                        @if($document->activated_at)
                            <tr>
                                <th>Activated:</th>
                                <td>{{ $document->activated_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($document->test_report_path || $document->acceptance_certificate_path)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Attachments</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($document->test_report_path)
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Test Report:</strong></p>
                            <a href="{{ Storage::url($document->test_report_path) }}"
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-file-pdf mr-1"></i> View Report
                            </a>
                            @if($document->test_date)
                                <br><small class="text-muted">Test Date: {{ $document->test_date }}</small>
                            @endif
                        </div>
                    @endif
                    @if($document->acceptance_certificate_path)
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Acceptance Certificate:</strong></p>
                            <a href="{{ Storage::url($document->acceptance_certificate_path) }}"
                               target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-file-certificate mr-1"></i> View Certificate
                            </a>
                            @if($document->acceptance_certificate_generated_at)
                                <br><small class="text-muted">Generated: {{ $document->acceptance_certificate_generated_at->format('Y-m-d H:i') }}</small>
                            @endif
                        </div>
                    @endif
                </div>

                @if($document->test_report_description)
                    <div class="mt-3">
                        <p class="mb-1"><strong>Test Report Description:</strong></p>
                        <p class="text-muted">{{ $document->test_report_description }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($document->notes)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Notes</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $document->notes }}</p>
            </div>
        </div>
    @endif

    <div class="mt-4 text-right">
        @if($document->designRequest)
            <a href="{{ route('design-requests.documents', $document->designRequest->id) }}"
               class="btn btn-secondary btn-sm">
                <i class="fas fa-folder mr-1"></i> All Documents
            </a>
        @endif

        @if(auth()->user()->can('view', $document))
            <a href="{{ route('leases.show', $document->id) }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-external-link-alt mr-1"></i> Open Full Details
            </a>

            @if(in_array($document->status, ['active', 'accepted']))
                <a href="{{ route('leases.download', $document->id) }}"
                   class="btn btn-success btn-sm">
                    <i class="fas fa-download mr-1"></i> Download PDF
                </a>
            @endif
        @endif
    </div>
</div>

<style>
   /* Add to your CSS file or <style> tag */
.document-details .card {
    margin-bottom: 0;
    border: 1px solid #e3e6f0;
}

.document-details .card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
    padding: 0.75rem 1.25rem;
}

.document-details .card-header .card-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #4e73df;
}

.document-details .table-sm td,
.document-details .table-sm th {
    padding: 0.5rem;
    vertical-align: middle;
}

.document-details .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.contract-preview {
    max-height: 200px;
    overflow: hidden;
    position: relative;
}

.contract-preview:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 50px;
    background: linear-gradient(transparent, white);
}

.read-more {
    color: #4e73df;
    text-decoration: none;
    font-weight: 600;
}

.read-more:hover {
    text-decoration: underline;
}
</style>
