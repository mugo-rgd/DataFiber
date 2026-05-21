@extends('layouts.app')

@section('title', 'Contracts')
<div class="mb-3">
    <a href="{{ route('contracts.index') }}"
       class="btn btn-sm {{ request('status') ? 'btn-outline-secondary' : 'btn-secondary' }}">
        All
    </a>

    <a href="{{ route('contracts.index', ['status' => 'draft']) }}"
       class="btn btn-sm {{ request('status') === 'draft' ? 'btn-secondary' : 'btn-outline-secondary' }}">
        Draft
    </a>

    <a href="{{ route('contracts.index', ['status' => 'sent']) }}"
       class="btn btn-sm {{ request('status') === 'sent' ? 'btn-info' : 'btn-outline-info' }}">
        Sent
    </a>

    <a href="{{ route('contracts.index', ['status' => 'customer_approved']) }}"
       class="btn btn-sm {{ request('status') === 'customer_approved' ? 'btn-warning' : 'btn-outline-warning' }}">
        Customer Approved
    </a>

    <a href="{{ route('contracts.index', ['status' => 'approved']) }}"
       class="btn btn-sm {{ request('status') === 'approved' ? 'btn-success' : 'btn-outline-success' }}">
        Approved
    </a>

    <a href="{{ route('contracts.index', ['status' => 'active']) }}"
       class="btn btn-sm {{ request('status') === 'active' ? 'btn-primary' : 'btn-outline-primary' }}">
        Active
    </a>
</div>
@section('content')

<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Contracts</h1>
            <p class="text-muted mb-0">Manage contract drafts, customer approvals, final approvals, and activation.</p>
        </div>

        <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-file-invoice-dollar me-1"></i>Quotations
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <strong>All Contracts</strong>
        </div>

        <div class="card-body p-0">
            @if($contracts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Contract #</th>
                                <th>Customer</th>
                                <th>Quotation</th>
                                <th>Account Manager</th>
                                <th>Status</th>
                                <th>Customer Approval</th>
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
                                        {{ $contract->customer->name ?? $contract->quotation->customer->name ?? 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $contract->quotation->quotation_number ?? 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $contract->accountManager->name ?? 'N/A' }}
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
                                        {{ $contract->created_at->format('M d, Y') }}
                                    </td>

                                    <td class="text-end pe-4">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('contracts.show', $contract) }}"
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($contract->canBeSent())
                                                <a href="{{ route('contracts.edit', $contract) }}"
                                                   class="btn btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif

                                            @if($contract->pdf_path)
    <a href="{{ asset('storage/' . $contract->pdf_path) }}"
       target="_blank"
       class="btn btn-outline-dark">
        <i class="fas fa-print me-1"></i>Print / Download PDF
    </a>
@endif

                                            @if($contract->canBeApprovedByAdmin())
                                                <a href="{{ route('contracts.show', $contract) }}"
                                                   class="btn btn-outline-success">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            @endif

                                            @if($contract->canBeActivated())
                                                <a href="{{ route('contracts.show', $contract) }}"
                                                   class="btn btn-outline-info">
                                                    <i class="fas fa-bolt"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3">
                    {{ $contracts->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-contract fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No contracts found</h5>
                    <p class="text-muted mb-0">Approved quotations can be converted into contract drafts.</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
