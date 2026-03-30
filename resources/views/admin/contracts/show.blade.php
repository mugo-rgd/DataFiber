@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Contract Details - {{ $contract->contract_number }}</h5>
                    <div class="card-tools">
                        <a href="{{ route('admin.contracts.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Contract details and actions for admin -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Contract Information</h6>
                            <p><strong>Status:</strong>
                                @php
                                    $statusColors = [
                                        'draft' => 'secondary',
                                        'sent' => 'info',
                                        'sent_to_customer' => 'info',
                                        'pending_approval' => 'warning',
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'signed' => 'primary'
                                    ];
                                    $color = $statusColors[$contract->status] ?? 'warning';
                                @endphp
                                <span class="badge bg-{{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $contract->status)) }}
                                </span>
                            </p>
                            <p><strong>Customer:</strong> {{ $contract->quotation->customer->name ?? 'N/A' }}</p>
                            <p><strong>Quotation:</strong> {{ $contract->quotation->quotation_number }}</p>
                            <p><strong>Created:</strong> {{ $contract->created_at->format('M d, Y H:i') }}</p>
                            <p><strong>Last Updated:</strong> {{ $contract->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Actions</h6>
                            <div class="btn-group">
                                <!-- Send to Customer - Only for draft contracts -->
                                @if($contract->status === 'draft')
                                    <form action="{{ route('admin.contracts.send-to-customer', $contract) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-paper-plane"></i> Send to Customer
                                        </button>
                                    </form>
                                @endif

                                <!-- Approve/Reject - Only show when contract needs admin approval -->
                                @if(in_array($contract->status, ['pending_approval', 'sent_to_customer', 'sent']) && !$contract->admin_approved_at)
                                    <form action="{{ route('admin.contracts.approve', $contract) }}" method="POST" class="d-inline">
                                        @csrf
                                        <div class="mb-2">
                                            <label for="approval_notes" class="form-label small">Approval Notes (Optional)</label>
                                            <textarea name="notes" id="approval_notes" class="form-control form-control-sm" rows="2"
                                                      placeholder="Add approval notes...">{{ old('notes') }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-sm"
                                                onclick="return confirm('Are you sure you want to approve this contract?')">
                                            <i class="fas fa-check"></i> Approve Contract
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        <i class="fas fa-times"></i> Reject Contract
                                    </button>
                                @endif

                                <!-- Show approved status when contract is already approved -->
                                @if($contract->admin_approved_at)
                                    <div class="alert alert-success p-2 mb-2">
                                        <small>
                                            <i class="fas fa-check-circle"></i>
                                            <strong>Contract Approved</strong> on {{ $contract->admin_approved_at->format('M d, Y H:i') }}
                                            @if($contract->approvals->count() > 0)
                                                | Notes: {{ $contract->approvals->first()->notes }}
                                            @endif
                                        </small>
                                    </div>
                                @endif

                                <!-- Edit - Only for draft and pending contracts that haven't been admin approved -->
                                @if(in_array($contract->status, ['draft', 'pending_approval', 'sent_to_customer', 'sent']) && !$contract->admin_approved_at)
                                    <a href="{{ route('admin.contracts.edit', $contract) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                @endif

                                <!-- Download PDF - Always available -->
                                <a href="{{ route('admin.contracts.download', $contract) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-download"></i> Download PDF
                                </a>
                            </div>

                            <!-- Status Information -->
                            @if($contract->status === 'approved' && $contract->admin_approved_at)
                                <div class="mt-2">
                                    <small class="text-success">
                                        <i class="fas fa-check-circle"></i>
                                        Approved by admin on: {{ $contract->admin_approved_at->format('M d, Y H:i') }}
                                        @if($contract->approvals->count() > 0)
                                            | Notes: {{ $contract->approvals->first()->notes }}
                                        @endif
                                    </small>
                                </div>
                            @elseif($contract->status === 'rejected')
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-times-circle text-danger"></i>
                                        Rejected on: {{ $contract->rejected_at?->format('M d, Y H:i') ?? 'N/A' }}
                                        @if($contract->rejection_reason)
                                            <br>Reason: {{ $contract->rejection_reason }}
                                        @endif
                                    </small>
                                </div>
                            @elseif(in_array($contract->status, ['pending_approval', 'sent_to_customer', 'sent']))
                                <div class="mt-2">
                                    <small class="text-warning">
                                        <i class="fas fa-clock"></i>
                                        Waiting for admin approval
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Contract content preview -->
                    <div class="contract-preview border rounded p-4 bg-light">
                        {!! $contract->contract_content !!}
                    </div>

                    <!-- Contract History -->
                    @if($contract->approvals && $contract->approvals->count() > 0)
                    <div class="mt-4">
                        <h6>Contract History</h6>
                        <div class="timeline">
                            @foreach($contract->approvals as $approval)
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <strong>{{ $approval->approver->name ?? 'System' }}</strong>
                                    <span class="text-muted">- {{ $approval->created_at->format('M d, Y H:i') }}</span>
                                    <p class="mb-0">{{ $approval->notes }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.contracts.reject', $contract) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Contract</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Reason for Rejection</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required
                                  placeholder="Please provide a reason for rejecting this contract..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 20px;
}
.timeline-item {
    position: relative;
    margin-bottom: 15px;
}
.timeline-marker {
    position: absolute;
    left: -20px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #007bff;
}
.timeline-content {
    padding: 10px;
    background: white;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}
.contract-preview {
    max-height: 600px;
    overflow-y: auto;
}
</style>
@endsection
