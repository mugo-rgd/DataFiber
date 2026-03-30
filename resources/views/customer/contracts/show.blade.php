{{-- resources/views/customer/contracts/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Contract: {{ $contract->contract_number }}</h2>
                <div>
                    <a href="{{ route('customer.contracts.download', $contract) }}"
                       class="btn btn-outline-primary me-2">
                        <i class="fas fa-download me-1"></i>Download PDF
                    </a>
                    <a href="{{ route('customer.contracts.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Back to Contracts
                    </a>
                    <a href="{{ route('customer.quotations.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-file-invoice me-1"></i>View Quotations
                    </a>
                </div>
            </div>

            <!-- Contract Status Card -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Contract Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Contract Number:</strong> {{ $contract->contract_number }}</p>
                                    <p><strong>Quotation:</strong> {{ $contract->quotation->quotation_number }}</p>
                                    <p><strong>Project:</strong> {{ $contract->quotation->designRequest->title ?? 'N/A' }}</p>
                                    <p><strong>Amount:</strong> {{ $contract->quotation->formatted_total_amount }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Generated:</strong> {{ $contract->created_at->format('M j, Y g:i A') }}</p>
                                    <p><strong>Customer Approved:</strong>
                                        @if($contract->customer_approved_at)
                                            <span class="text-success">{{ $contract->customer_approved_at->format('M j, Y g:i A') }}</span>
                                        @else
                                            <span class="text-warning">Pending</span>
                                        @endif
                                    </p>
                                    <p><strong>Admin Approved:</strong>
                                        @if($contract->admin_approved_at)
                                            <span class="text-success">{{ $contract->admin_approved_at->format('M j, Y g:i A') }}</span>
                                        @else
                                            <span class="text-warning">Pending</span>
                                        @endif
                                    </p>
                                    <p><strong>Design Completed:</strong>
                                        @if($contract->design_completed_at)
                                            <span class="text-success">{{ $contract->design_completed_at->format('M j, Y g:i A') }}</span>
                                        @else
                                            <span class="text-warning">Pending</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Workflow Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="progress mb-3" style="height: 25px;">
                                @php
                                    $progress = 0;
                                    if($contract->status == 'approved') $progress = 100;
                                    elseif($contract->admin_approved_at) $progress = 75;
                                    elseif($contract->customer_approved_at) $progress = 50;
                                    else $progress = 25;
                                @endphp
                                <div class="progress-bar bg-success" style="width: {{ $progress }}%">
                                    {{ $progress }}%
                                </div>
                            </div>

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center {{ $contract->quotation->status == 'approved' ? 'list-group-item-success' : '' }}">
                                    Quotation Approved
                                    @if($contract->quotation->status == 'approved')
                                    <i class="fas fa-check text-success"></i>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center {{ $contract->customer_approved_at ? 'list-group-item-success' : '' }}">
                                    Customer Approved Contract
                                    @if($contract->customer_approved_at)
                                    <i class="fas fa-check text-success"></i>
                                    @elseif($contract->canBeApprovedByCustomer())
                                    <button type="button"
                                            class="btn btn-sm btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#approveContractModal">
                                        Approve
                                    </button>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center {{ $contract->admin_approved_at ? 'list-group-item-success' : '' }}">
                                    Admin Approved
                                    @if($contract->admin_approved_at)
                                    <i class="fas fa-check text-success"></i>
                                    @else
                                    <i class="fas fa-clock text-warning"></i>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center {{ $contract->design_completed_at ? 'list-group-item-success' : '' }}">
                                    Design Completed
                                    @if($contract->design_completed_at)
                                    <i class="fas fa-check text-success"></i>
                                    @else
                                    <i class="fas fa-clock text-warning"></i>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contract Content -->
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Contract Document</h5>
                    <span class="badge bg-{{ $contract->getStatusBadgeColor() }}">
                        {{ $contract->getStatusDisplayText() }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="contract-content">
                        {!! $contract->contract_content !!}
                    </div>
                </div>
            </div>

            <!-- Approval History -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Approval History</h5>
                </div>
                <div class="card-body">
                    @if($contract->approvals->isEmpty())
                        <p class="text-muted">No approval history yet.</p>
                    @else
                        <div class="timeline">
                            @foreach($contract->approvals as $approval)
                            <div class="timeline-item mb-3">
                                <div class="d-flex">
                                    <div class="timeline-marker bg-{{ $approval->approved_by == 'customer' ? 'info' : ($approval->approved_by == 'admin' ? 'success' : 'secondary') }} rounded-circle me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-{{ $approval->approved_by == 'customer' ? 'user' : ($approval->approved_by == 'admin' ? 'user-tie' : 'robot') }} text-white"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 text-capitalize">{{ $approval->approved_by }} Approval</h6>
                                        <p class="mb-1">{{ $approval->notes }}</p>
                                        <small class="text-muted">{{ $approval->created_at->format('M j, Y g:i A') }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            @if($contract->canBeApprovedByCustomer())
            <div class="card mt-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Action Required</h5>
                </div>
                <div class="card-body">
                    <p>Please review the contract above and approve it to proceed with the project.</p>
                    <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#approveContractModal">
                        <i class="fas fa-check me-2"></i>Approve Contract
                    </button>
                </div>
            </div>
            @endif

            @if($contract->status == 'approved')
            <div class="alert alert-success mt-4">
                <h5><i class="fas fa-check-circle me-2"></i>Contract Fully Approved</h5>
                <p class="mb-0">The contract has been approved by all parties. Design request is completed and work execution can now start.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Contract Modal -->
@if($contract->canBeApprovedByCustomer())
<div class="modal fade" id="approveContractModal" tabindex="-1" aria-labelledby="approveContractModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="approveContractModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Approve Contract
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this contract?</p>
                <div class="alert alert-info">
                    <strong>Contract #:</strong> {{ $contract->contract_number }}<br>
                    <strong>Quotation #:</strong> {{ $contract->quotation->quotation_number }}<br>
                    <strong>Amount:</strong> {{ $contract->quotation->formatted_total_amount }}
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Important:</strong> By approving this contract, you are agreeing to all terms and conditions. The contract will be sent to admin for final approval.
                </div>

                <p class="text-muted">Please ensure you have reviewed the entire contract before approving.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('customer.contracts.approve', $contract) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>Confirm Contract Approval
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<style>
.contract-content {
    line-height: 1.8;
    font-size: 14px;
    font-family: 'Times New Roman', serif;
}

.contract-content h4 {
    color: #2c3e50;
    border-bottom: 2px solid #3498db;
    padding-bottom: 5px;
    margin-top: 20px;
}

.contract-content h5 {
    color: #34495e;
    margin-top: 15px;
}

.timeline-marker {
    flex-shrink: 0;
}

.progress {
    background-color: #e9ecef;
}

.list-group-item {
    border: none;
    padding: 0.75rem 0;
}

.contract-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
}

.contract-content table, .contract-content th, .contract-content td {
    border: 1px solid #ddd;
}

.contract-content th, .contract-content td {
    padding: 8px;
    text-align: left;
}

.contract-content th {
    background-color: #f8f9fa;
}
</style>
@endsection
