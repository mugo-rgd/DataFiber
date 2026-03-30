<div class="document-details">
    <h5 class="mb-4"><i class="fas fa-file-contract text-success mr-2"></i>Acceptance Certificate Details</h5>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Certificate Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Certificate Ref:</th>
                            <td><strong>{{ $document->certificate_ref }}</strong></td>
                        </tr>
                        <tr>
                            <th>To Company:</th>
                            <td>{{ $document->to_company }}</td>
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
                            <th width="40%">Route Name:</th>
                            <td>{{ $document->route_name }}</td>
                        </tr>
                        <tr>
                            <th>Link Name:</th>
                            <td>{{ $document->link_name }}</td>
                        </tr>
                        <tr>
                            <th>Cable Type:</th>
                            <td>{{ $document->cable_type }}</td>
                        </tr>
                        <tr>
                            <th>Distance:</th>
                            <td>{{ $document->distance }} km</td>
                        </tr>
                        <tr>
                            <th>Cores Count:</th>
                            <td>{{ $document->cores_count }}</td>
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
                    <h6 class="card-title mb-0">Lessee Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Lessee Address:</th>
                            <td>{{ $document->lessee_address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Lessee Contact:</th>
                            <td>{{ $document->lessee_contact ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Effective Date:</th>
                            <td>{{ $document->effective_date }}</td>
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
                            <th width="40%">Status:</th>
                            <td>
                                <span class="badge badge-{{ getStatusColor($document->status) }}">
                                    {{ $document->status }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $document->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $document->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Signatories</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Witnesses</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Witness 1:</th>
                                    <td>{{ $document->witness1_name }}</td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td>{{ $document->witness1_date }}</td>
                                </tr>
                                <tr>
                                    <th>Witness 2:</th>
                                    <td>{{ $document->witness2_name }}</td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td>{{ $document->witness2_date }}</td>
                                </tr>
                                @if($document->witness3_name)
                                    <tr>
                                        <th>Witness 3:</th>
                                        <td>{{ $document->witness3_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date:</th>
                                        <td>{{ $document->witness3_date }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-primary">Lessee Signatories</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Lessee 1:</th>
                                    <td>{{ $document->lessee1_name }}</td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td>{{ $document->lessee1_date }}</td>
                                </tr>
                                <tr>
                                    <th>Lessee 2:</th>
                                    <td>{{ $document->lessee2_name }}</td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td>{{ $document->lessee2_date }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($document->test_report_path || $document->additional_documents_path)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Attachments</h6>
            </div>
            <div class="card-body">
                @if($document->test_report_path)
                    <p class="mb-1"><strong>Test Report:</strong></p>
                    <a href="{{ Storage::url($document->test_report_path) }}"
                       target="_blank" class="btn btn-sm btn-outline-primary mb-2">
                        <i class="fas fa-file-pdf mr-1"></i> View Test Report
                    </a>
                @endif

                @if($document->additional_documents_path)
                    <p class="mb-1 mt-3"><strong>Additional Documents:</strong></p>
                    @foreach(json_decode($document->additional_documents_path, true) as $index => $path)
                        <a href="{{ Storage::url($path) }}"
                           target="_blank" class="btn btn-sm btn-outline-secondary mb-1 mr-1">
                            <i class="fas fa-paperclip mr-1"></i> Document {{ $index + 1 }}
                        </a>
                    @endforeach
                @endif
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
            @if($document->test_report_path)
                <a href="{{ route('certificates.download', ['type' => 'acceptance', 'id' => $document->id]) }}"
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
