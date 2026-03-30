<div class="document-details">
    <h5 class="mb-4"><i class="fas fa-handshake text-info mr-2"></i>Contract Details</h5>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Contract Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Contract Number:</th>
                            <td><strong>{{ $document->contract_number }}</strong></td>
                        </tr>
                        <tr>
                            <th>Related Quotation:</th>
                            <td>
                                <a href="{{ route('quotations.show', $document->quotation->id) }}" class="text-primary">
                                    {{ $document->quotation->quotation_number }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Design Request:</th>
                            <td>
                                <a href="{{ route('design-requests.show', $document->quotation->designRequest->id) }}" class="text-primary">
                                    {{ $document->quotation->designRequest->request_number ?? 'N/A' }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Customer:</th>
                            <td>{{ $document->quotation->customer->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Customer Company:</th>
                            <td>
                                <span class="badge badge-info">
                                    {{ $document->quotation->customer->company_name ?? 'N/A' }}
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
                        @if($document->sent_to_customer_at)
                            <tr>
                                <th>Sent to Customer:</th>
                                <td>{{ $document->sent_to_customer_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endif
                        @if($document->customer_approved_at)
                            <tr>
                                <th>Customer Approved:</th>
                                <td>{{ $document->customer_approved_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endif
                        @if($document->admin_approved_at)
                            <tr>
                                <th>Admin Approved:</th>
                                <td>{{ $document->admin_approved_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endif
                        @if($document->design_completed_at)
                            <tr>
                                <th>Design Completed:</th>
                                <td>{{ $document->design_completed_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Contract Overview</h6>
                </div>
                <div class="card-body">
                    <div class="contract-preview">
                        @if(strlen($document->contract_content) > 500)
                            <div class="mb-3">
                                <p class="text-muted">
                                    {{ Str::limit(strip_tags($document->contract_content), 500) }}
                                    <a href="#" class="read-more" data-full-content="{{ htmlspecialchars($document->contract_content) }}">
                                        ... Read more
                                    </a>
                                </p>
                            </div>
                        @else
                            <p>{!! nl2br(e($document->contract_content)) !!}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 text-right">
        @if($document->quotation->designRequest)
            <a href="{{ route('design-requests.documents', $document->quotation->designRequest->id) }}"
               class="btn btn-secondary btn-sm">
                <i class="fas fa-folder mr-1"></i> All Documents
            </a>
        @endif

        @if(auth()->user()->can('view', $document))
            <a href="{{ route('contracts.show', $document->id) }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-external-link-alt mr-1"></i> Open Full Details
            </a>

            @if(in_array($document->status, ['approved', 'sent_to_customer']))
                <a href="{{ route('contracts.download', $document->id) }}"
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
@push('scripts')
<script>
$(document).ready(function() {
    $('.read-more').click(function(e) {
        e.preventDefault();
        var fullContent = $(this).data('full-content');
        $(this).parent().html(fullContent);
    });
});
</script>
@endpush
