{{-- resources/views/customer/contracts/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>My Contracts</h2>
                <div>
                    <a href="{{ route('customer.quotations.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Back to Quotations
                    </a>
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-1"></i> Back to Home
                    </a>
                </div>
            </div>

            @if($contracts->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    You don't have any contracts yet. Approve a quotation to generate a contract.
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Contract #</th>
                                        <th>Quotation</th>
                                        <th>Project</th>
                                        <th>Customer Approved</th>
                                        <th>Admin Approved</th>
                                        <th>Status</th>
                                        <th>Generated Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contracts as $contract)
                                    <tr class="{{ $contract->status == 'approved' ? 'table-success' : '' }}">
                                        <td>
                                            <strong>{{ $contract->contract_number }}</strong>
                                        </td>
                                        <td>
                                            {{ $contract->quotation->quotation_number }}
                                        </td>
                                        <td>
                                            @if($contract->quotation->designRequest)
                                                {{ $contract->quotation->designRequest->title }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($contract->customer_approved_at)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>
                                                    {{ $contract->customer_approved_at->format('M j, Y') }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock me-1"></i>Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($contract->admin_approved_at)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>
                                                    {{ $contract->admin_approved_at->format('M j, Y') }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock me-1"></i>Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $contract->getStatusBadgeColor() }}">
                                                {{ $contract->getStatusDisplayText() }}
                                            </span>
                                            @if($contract->status == 'approved')
                                                <br>
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>Work Ready
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ $contract->created_at->format('M j, Y') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('customer.contracts.show', $contract) }}"
                                                   class="btn btn-outline-primary"
                                                   title="View Contract">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="{{ route('customer.contracts.download', $contract) }}"
                                                   class="btn btn-outline-info"
                                                   title="Download PDF">
                                                    <i class="fas fa-download"></i>
                                                </a>

                                                @if($contract->canBeApprovedByCustomer())
                                                    <button type="button"
                                                            class="btn btn-outline-success"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#approveContractModal{{ $contract->id }}"
                                                            title="Approve Contract">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Approve Contract Modal -->
                                    @if($contract->canBeApprovedByCustomer())
                                    <div class="modal fade" id="approveContractModal{{ $contract->id }}" tabindex="-1" aria-labelledby="approveContractModalLabel{{ $contract->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title" id="approveContractModalLabel{{ $contract->id }}">
                                                        <i class="fas fa-check-circle me-2"></i>Approve Contract
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to approve this contract?</p>
                                                    <div class="alert alert-info">
                                                        <strong>Contract #:</strong> {{ $contract->contract_number }}<br>
                                                        <strong>Quotation #:</strong> {{ $contract->quotation->quotation_number }}<br>
                                                        <strong>Project:</strong> {{ $contract->quotation->designRequest->title ?? 'N/A' }}<br>
                                                        <strong>Amount:</strong> {{ $contract->quotation->formatted_total_amount }}
                                                    </div>

                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        <strong>Important:</strong> By approving this contract, you are agreeing to all terms and conditions. The contract will be sent to admin for final approval before work can begin.
                                                    </div>

                                                    <p class="text-muted">Please review the contract carefully before approving.</p>
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
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    {{ $contracts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Quick Stats -->
@if($contracts->isNotEmpty())
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $contracts->where('status', 'draft')->count() }}</h4>
                        <p class="mb-0">Draft Contracts</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-contract fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $contracts->where('status', 'pending_approval')->count() }}</h4>
                        <p class="mb-0">Pending Approval</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $contracts->where('status', 'approved')->count() }}</h4>
                        <p class="mb-0">Approved</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $contracts->count() }}</h4>
                        <p class="mb-0">Total Contracts</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-copy fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<style>
.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}
</style>

<script>
// Auto-focus on modal buttons
document.addEventListener('DOMContentLoaded', function() {
    const approveModals = document.querySelectorAll('[id^="approveContractModal"]');
    approveModals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function () {
            const approveButton = this.querySelector('.btn-success');
            if (approveButton) {
                // Don't auto-focus for safety, but we can highlight it
                approveButton.classList.add('btn-pulse');
                setTimeout(() => approveButton.classList.remove('btn-pulse'), 2000);
            }
        });
    });
});

// Add pulse animation
const style = document.createElement('style');
style.textContent = `
    .btn-pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .table-success {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }
`;
document.head.appendChild(style);
</script>
@endsection
