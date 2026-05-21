@extends('layouts.app')

@section('title', 'My Contracts')

@section('content')
<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">My Contracts</h1>
            <p class="text-muted mb-0">Review and track your contract approvals.</p>
        </div>

        <a href="{{ route('customer.quotations.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Quotations
        </a>
    </div>

    @if($contracts->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            You do not have any contracts yet.
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Contract #</th>
                                <th>Quotation</th>
                                <th>Status</th>
                                <th>Customer Approval</th>
                                <th>Sent Date</th>
                                <th>Created</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($contracts as $contract)
                                <tr>
                                    <td class="ps-4">
                                        <strong>{{ $contract->contract_number }}</strong>
                                    </td>

                                    <td>
                                        {{ $contract->quotation->quotation_number ?? 'N/A' }}
                                    </td>

                                    <td>
                                        <span class="badge bg-{{ $contract->getStatusBadgeColor() }}">
                                            {{ $contract->getStatusDisplayText() }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($contract->customer_approval_status === 'approved')
                                            <span class="badge bg-success">Accepted</span>
                                        @elseif($contract->customer_approval_status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ optional($contract->sent_at ?? $contract->sent_to_customer_at)->format('M d, Y') ?? 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $contract->created_at->format('M d, Y') }}
                                    </td>

                                    <td class="text-end pe-4">
                                        <a href="{{ route('customer.contracts.show', $contract) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>

                                        @if($contract->canBeApprovedByCustomer())
                                            <a href="{{ route('customer.contracts.show', $contract) }}"
                                               class="btn btn-sm btn-success">
                                                <i class="fas fa-check me-1"></i>Review
                                            </a>
                                        @endif

                                        @if($contract->pdf_path)
    <a href="{{ asset('storage/' . $contract->pdf_path) }}"
       target="_blank"
       class="btn btn-outline-dark">
        <i class="fas fa-print me-1"></i>Print / Download PDF
    </a>
@endif
                                    </td>
                                </tr>
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
@endsection
