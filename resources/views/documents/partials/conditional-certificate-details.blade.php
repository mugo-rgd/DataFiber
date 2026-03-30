<div class="document-details">
    <h5 class="mb-4"><i class="fas fa-file-certificate text-warning mr-2"></i>Conditional Certificate Details</h5>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Certificate Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Reference Number:</th>
                            <td><strong>{{ $document->ref_number }}</strong></td>
                        </tr>
                        <tr>
                            <th>Lessor:</th>
                            <td>{{ $document->lessor }}</td>
                        </tr>
                        <tr>
                            <th>Lessee:</th>
                            <td>{{ $document->lessee }}</td>
                        </tr>
                        <tr>
                            <th>Design Request:</th>
                            <td>
                                <a href="{{ route('design-requests.show', $document->designRequest->id) }}" class="text-primary">
                                    {{ $document->designRequest->request_number ?? 'N/A' }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Link Name:</th>
                            <td>{{ $document->link_name }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Technical Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Fibre Technology:</th>
                            <td>{{ $document->fibre_technology }}</td>
                        </tr>
                        <tr>
                            <th>Total Length:</th>
                            <td>{{ $document->total_length }} km</td>
                        </tr>
                        <tr>
                            <th>Average Loss:</th>
                            <td>{{ $document->average_loss }} dB</td>
                        </tr>
                        <tr>
                            <th>Splice Joints:</th>
                            <td>{{ $document->splice_joints }}</td>
                        </tr>
                        <tr>
                            <th>Test Wavelength:</th>
                            <td>{{ $document->test_wavelength }}</td>
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
                    <h6 class="card-title mb-0">Site Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Site A:</th>
                            <td>{{ $document->site_a }}</td>
                        </tr>
                        <tr>
                            <th>Site B:</th>
                            <td>{{ $document->site_b }}</td>
                        </tr>
                        <tr>
                            <th>ODF Connector Type:</th>
                            <td>{{ $document->odf_connector_type }}</td>
                        </tr>
                        <tr>
                            <th>OTDR Serial:</th>
                            <td>{{ $document->otdr_serial }}</td>
                        </tr>
                        <tr>
                            <th>Calibration Date:</th>
                            <td>{{ $document->calibration_date }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Status & Dates</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Certificate Status:</th>
                            <td>
                                <span class="badge badge-{{ getStatusColor($document->certificate_status) }}">
                                    {{ $document->certificate_status }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Engineer Name:</th>
                            <td>{{ $document->engineer_name }}</td>
                        </tr>
                        <tr>
                            <th>Certificate Date:</th>
                            <td>{{ $document->certificate_date }}</td>
                        </tr>
                        <tr>
                            <th>Certificate Issue Date:</th>
                            <td>{{ $document->certificate_issue_date ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Commissioning End Date:</th>
                            <td>{{ $document->commissioning_end_date }}</td>
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
                    <h6 class="card-title mb-0">Lessee Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Contact Name:</th>
                            <td>{{ $document->lessee_contact_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Designation:</th>
                            <td>{{ $document->lessee_designation ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Date:</th>
                            <td>{{ $document->lessee_date ?? 'N/A' }}</td>
                        </tr>
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
                        @if($document->email_sent_at)
                            <tr>
                                <th>Email Sent:</th>
                                <td>{{ $document->email_sent_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endif
                        @if($document->designer_acknowledged_at)
                            <tr>
                                <th>Designer Acknowledged:</th>
                                <td>{{ $document->designer_acknowledged_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($document->inspection_report_path || $document->otdr_trace_path)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Attachments</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($document->inspection_report_path)
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Inspection Report:</strong></p>
                            <a href="{{ Storage::url($document->inspection_report_path) }}"
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-file-pdf mr-1"></i> View Report
                            </a>
                        </div>
                    @endif
                    @if($document->otdr_trace_path)
                        <div class="col-md-6">
                            <p class="mb-1"><strong>OTDR Trace:</strong></p>
                            <a href="{{ Storage::url($document->otdr_trace_path) }}"
                               target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-chart-line mr-1"></i> View Trace
                            </a>
                        </div>
                    @endif
                </div>
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
            @if($document->inspection_report_path)
                <a href="{{ route('certificates.download', ['type' => 'conditional', 'id' => $document->id]) }}"
                   class="btn btn-success btn-sm">
                    <i class="fas fa-download mr-1"></i> Download Certificate
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
