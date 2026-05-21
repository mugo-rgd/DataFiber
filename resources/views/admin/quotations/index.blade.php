@extends('layouts.app')

@section('title', 'Manage Quotations')

@section('content')
<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-file-invoice-dollar fa-2x text-kp-blue me-3"></i>
            <div>
                <h1 class="h3 mb-0 text-gray-800">Manage Quotations</h1>
                <small class="text-muted">Create, send, and approve quotations</small>
            </div>
        </div>

        <a href="{{ route('admin.quotations.create') }}" class="btn btn-kp-primary">
            <i class="fas fa-plus me-2"></i>New Quotation
        </a>
    </div>

    <div class="row mb-4">
        @php
            $cards = [
                ['title' => 'Total', 'value' => $quotations->total(), 'color' => 'primary', 'icon' => 'file-invoice-dollar'],
                ['title' => 'Draft', 'value' => $quotations->where('status', 'draft')->count(), 'color' => 'warning', 'icon' => 'edit'],
                ['title' => 'Sent', 'value' => $quotations->where('status', 'sent')->count(), 'color' => 'info', 'icon' => 'paper-plane'],
                ['title' => 'Admin Approved', 'value' => $quotations->where('status', 'approved')->count(), 'color' => 'success', 'icon' => 'check-circle'],
            ];
        @endphp

        @foreach($cards as $card)
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-{{ $card['color'] }} shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col">
                                <div class="text-xs fw-bold text-{{ $card['color'] }} text-uppercase mb-1">
                                    {{ $card['title'] }}
                                </div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    {{ $card['value'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-{{ $card['icon'] }} fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Quotations</h5>

            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>

                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.quotations.index') }}">
                            All Status
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>

                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'draft']) }}">Draft</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'sent']) }}">Sent</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'customer_approved']) }}">Customer Accepted</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'customer_rejected']) }}">Customer Rejected</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}">Admin Approved</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}">Rejected</a></li>
                </ul>
            </div>
        </div>

        <div class="card-body p-0">
            @if($quotations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Quotation #</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Valid Until</th>
                                <th>Created</th>
                                <th class="pe-4 text-end">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($quotations as $quotation)
                                @include('admin.quotations.partials.quotation-row', [
                                    'quotation' => $quotation,
                                    'isAdmin' => $isAdmin
                                ])
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                    <div class="text-muted small">
                        Showing {{ $quotations->firstItem() }} to {{ $quotations->lastItem() }} of {{ $quotations->total() }} quotations
                    </div>

                    <div>
                        {{ $quotations->withQueryString()->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice-dollar fa-4x text-muted opacity-25 mb-4"></i>
                    <h5 class="text-muted mb-3">No quotations found</h5>

                    <p class="text-muted mb-4">
                        @if(request('status'))
                            No {{ str_replace('_', ' ', request('status')) }} quotations found.
                        @else
                            Create your first quotation to get started.
                        @endif
                    </p>

                    <a href="{{ route('admin.quotations.create') }}" class="btn btn-kp-primary">
                        <i class="fas fa-plus me-2"></i>Create Quotation
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@include('admin.quotations.modals.approve-modal')
@include('admin.quotations.modals.reject-modal')
@include('admin.quotations.modals.send-modal')
@endsection

@push('styles')
<style>
    .quotation-status-badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
        border-radius: 0.25rem;
    }

    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .modal-backdrop {
        z-index: 1040;
    }

    .modal {
        z-index: 1050;
    }

    .modal-header {
        border-bottom: 2px solid;
    }

    #approveQuotationModal .modal-header {
        border-color: #28a745;
    }

    #rejectQuotationModal .modal-header {
        border-color: #dc3545;
    }

    #sendQuotationModal .modal-header {
        border-color: #17a2b8;
    }

    .modal-footer {
        border-top: 1px solid #dee2e6;
    }

    .btn:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .fa-spinner {
        animation: fa-spin 1s infinite linear;
    }

    @keyframes fa-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .form-text.text-end {
        font-size: 0.75rem;
        color: #6c757d;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    setupQuotationModalData();
    setupQuotationCounters();
});

function setupQuotationModalData() {
    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-bs-toggle="modal"]');

        if (!trigger) return;

        const quotationId = trigger.getAttribute('data-quotation-id');
        const modalTarget = trigger.getAttribute('data-bs-target');

        if (!quotationId || !modalTarget) return;

        if (modalTarget === '#approveQuotationModal') {
            document.getElementById('approveQuotationId').value = quotationId;
        }

        if (modalTarget === '#rejectQuotationModal') {
            document.getElementById('rejectQuotationId').value = quotationId;
        }

        if (modalTarget === '#sendQuotationModal') {
            document.getElementById('sendQuotationId').value = quotationId;
        }
    });
}

function setupQuotationCounters() {
    [
        ['approveNotes', 'approveNotesCounter'],
        ['rejectReason', 'rejectReasonCounter'],
        ['sendNotes', 'sendNotesCounter']
    ].forEach(function ([textareaId, counterId]) {
        const textarea = document.getElementById(textareaId);
        const counter = document.getElementById(counterId);

        if (!textarea || !counter) return;

        counter.textContent = textarea.value.length;

        textarea.addEventListener('input', function () {
            counter.textContent = this.value.length;
        });
    });
}

async function quotationApprove(button) {
    const quotationId = document.getElementById('approveQuotationId').value;
    const notes = document.getElementById('approveNotes').value || '';

    if (!quotationId) {
        alert('No quotation selected.');
        return;
    }

    await quotationAction(button, {
        url: `/admin/quotations/${quotationId}/approve`,
        body: { notes },
        loadingText: 'Approving...',
        modalId: 'approveQuotationModal',
        defaultError: 'Failed to approve quotation.'
    });
}

async function quotationReject(button) {
    const quotationId = document.getElementById('rejectQuotationId').value;
    const reason = document.getElementById('rejectReason').value || '';

    if (!quotationId) {
        alert('No quotation selected.');
        return;
    }

    if (!reason.trim()) {
        alert('Please provide a rejection reason.');
        return;
    }

    await quotationAction(button, {
        url: `/admin/quotations/${quotationId}/reject`,
        body: { reason },
        loadingText: 'Rejecting...',
        modalId: 'rejectQuotationModal',
        defaultError: 'Failed to reject quotation.'
    });
}

async function quotationSend(button) {
    const quotationId = document.getElementById('sendQuotationId').value;
    const email_notes = document.getElementById('sendNotes').value || '';

    if (!quotationId) {
        alert('No quotation selected.');
        return;
    }

    await quotationAction(button, {
        url: `/admin/quotations/${quotationId}/send`,
        body: { email_notes },
        loadingText: 'Sending...',
        modalId: 'sendQuotationModal',
        defaultError: 'Failed to send quotation.'
    });
}

async function quotationAction(button, options) {
    if (button.disabled) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const originalHtml = button.innerHTML;

    button.disabled = true;
    button.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i>${options.loadingText}`;

    try {
        const response = await fetch(options.url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(options.body || {})
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            alert(data.message || options.defaultError);
            button.disabled = false;
            button.innerHTML = originalHtml;
            return;
        }

        const modalElement = document.getElementById(options.modalId);

        if (modalElement) {
            const modal = bootstrap.Modal.getInstance(modalElement);

            if (modal) {
                modal.hide();
            }
        }

        window.location.href = window.location.pathname + window.location.search;

    } catch (error) {
        console.error(error);
        alert('Network error. Please try again.');

        button.disabled = false;
        button.innerHTML = originalHtml;
    }
}

window.duplicateQuotation = async function (quotationId) {
    if (!confirm('Are you sure you want to duplicate this quotation?')) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        const response = await fetch(`/admin/quotations/${quotationId}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            alert(data.message || 'Failed to duplicate quotation.');
            return;
        }

        window.location.reload();

    } catch (error) {
        console.error(error);
        alert('Network error. Please try again.');
    }
};
</script>
@endpush
