<div class="document-details">
    <h5 class="mb-4"><i class="fas fa-file-invoice-dollar text-primary mr-2"></i>Quotation Details</h5>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Basic Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Quotation Number:</th>
                            <td><strong>{{ $document->quotation_number }}</strong></td>
                        </tr>
                        <tr>
                            <th>Customer:</th>
                            <td>{{ $document->customer->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Company:</th>
                            <td>
                                <span class="badge badge-info">
                                    {{ $document->customer->company_name ?? 'N/A' }}
                                </span>
                            </td>
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
                            <th>Route Name:</th>
                            <td>{{ $document->designRequest->route_name ?? 'N/A' }}</td>
                        </tr>
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
                            <th width="40%">Subtotal:</th>
                            <td>{{ number_format($document->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Tax ({{ ($document->tax_rate * 100) }}%):</th>
                            <td>{{ number_format($document->tax_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Total Amount:</th>
                            <td><strong class="text-success">{{ number_format($document->total_amount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Created By:</th>
                            <td>
                                @if($document->accountManager)
                                    {{ $document->accountManager->name }}
                                @else
                                    System
                                @endif
                            </td>
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
                    <h6 class="card-title mb-0">Status & Validity</h6>
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
                            <th>Customer Approval:</th>
                            <td>
                                <span class="badge badge-{{
                                    $document->customer_approval_status == 'approved' ? 'success' :
                                    ($document->customer_approval_status == 'rejected' ? 'danger' : 'warning')
                                }}">
                                    {{ $document->customer_approval_status }}
                                </span>
                                @if($document->customer_approved_at)
                                    <br><small>{{ $document->customer_approved_at->format('Y-m-d H:i') }}</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Valid Until:</th>
                            <td>
                                <span class="{{
                                    \Carbon\Carbon::parse($document->valid_until)->isPast() ? 'text-danger' : 'text-success'
                                }}">
                                    {{ $document->valid_until }}
                                    @if(\Carbon\Carbon::parse($document->valid_until)->isPast())
                                        <br><small class="text-danger">(Expired)</small>
                                    @endif
                                </span>
                            </td>
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
                        @if($document->sent_at)
                            <tr>
                                <th>Sent to Customer:</th>
                                <td>{{ $document->sent_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endif
                        @if($document->approved_at)
                            <tr>
                                <th>Approved:</th>
                                <td>{{ $document->approved_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($document->customer_notes)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Customer Notes</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $document->customer_notes }}</p>
            </div>
        </div>
    @endif

    @if($document->rejection_reason && $document->customer_approval_status == 'rejected')
        <div class="card mt-3 border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="card-title mb-0">Rejection Reason</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $document->rejection_reason }}</p>
            </div>
        </div>
    @endif

    <div class="mt-4 text-right">
        @if(auth()->user()->role !== 'customer' || $document->customer_approval_status == 'approved')
            <a href="{{ route('quotations.download', $document->id) }}"
               class="btn btn-success btn-sm">
                <i class="fas fa-download mr-1"></i> Download PDF
            </a>
        @endif

        @if(auth()->user()->can('view', $document))
            <a href="{{ route('quotations.show', $document->id) }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-external-link-alt mr-1"></i> Open Full Details
            </a>
        @endif

        @if($document->designRequest)
            <a href="{{ route('design-requests.documents', $document->designRequest->id) }}"
               class="btn btn-secondary btn-sm">
                <i class="fas fa-folder mr-1"></i> All Documents
            </a>
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
