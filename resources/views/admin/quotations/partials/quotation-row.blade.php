@php
    $statusConfig = [
        'draft' => [
            'color' => 'secondary',
            'icon' => 'edit',
            'label' => 'Draft',
        ],
        'sent' => [
            'color' => 'info',
            'icon' => 'paper-plane',
            'label' => 'Sent to Customer',
        ],
        'customer_approved' => [
            'color' => 'warning',
            'icon' => 'user-check',
            'label' => 'Customer Accepted',
        ],
        'customer_rejected' => [
            'color' => 'danger',
            'icon' => 'user-times',
            'label' => 'Customer Rejected',
        ],
        'approved' => [
            'color' => 'success',
            'icon' => 'check-circle',
            'label' => 'Admin Approved',
        ],
        'rejected' => [
            'color' => 'danger',
            'icon' => 'times-circle',
            'label' => 'Rejected',
        ],
    ];

    $status = $quotation->status;

    $config = $statusConfig[$status] ?? [
        'color' => 'secondary',
        'icon' => 'question',
        'label' => ucfirst(str_replace('_', ' ', $status)),
    ];

    $user = auth()->user();

    $isAdmin = $user->Role([
        'admin',
        'system_admin',
        'technical_admin',
        'accountmanager_admin',
    ]);

    $isAccountManager = $user->Role([
        'account_manager',
        'accountmanager_admin',
    ]);

    $downloadRoute = $isAdmin
        ? route('admin.quotations.download', $quotation)
        : route('account-manager.quotations.download', $quotation);
@endphp

<tr data-quotation-id="{{ $quotation->id }}">
    <td class="ps-4 align-middle">
        <strong class="d-block">
            {{ $quotation->quotation_number }}
        </strong>

        <small class="text-muted">
            #{{ $quotation->id }}
        </small>
    </td>

    <td class="align-middle">
        @if($quotation->customer)
            <strong class="d-block">
                {{ $quotation->customer->name }}
            </strong>

            <small class="text-muted">
                {{ $quotation->customer->email }}
            </small>
        @else
            <span class="text-muted">N/A</span>
        @endif
    </td>

    <td class="align-middle">
        <span class="fw-bold">
            USD {{ number_format($quotation->total_amount, 2) }}
        </span>
    </td>

    <td class="align-middle">
        <span class="badge bg-{{ $config['color'] }}">
            <i class="fas fa-{{ $config['icon'] }} me-1"></i>
            {{ $config['label'] }}
        </span>

        @if($quotation->sent_at && $status === 'sent')
            <div class="small text-muted mt-1">
                Sent {{ $quotation->sent_at->format('M d, Y') }}
            </div>
        @endif

        @if($quotation->customer_approved_at && $status === 'customer_approved')
            <div class="small text-muted mt-1">
                Accepted {{ $quotation->customer_approved_at->format('M d, Y') }}
            </div>
        @endif

        @if($quotation->approved_at && $status === 'approved')
            <div class="small text-muted mt-1">
                Approved {{ $quotation->approved_at->format('M d, Y') }}
            </div>
        @endif

        @if($quotation->customer_rejected_at && $status === 'customer_rejected')
            <div class="small text-muted mt-1">
                Rejected {{ $quotation->customer_rejected_at->format('M d, Y') }}
            </div>
        @endif
    </td>

    <td class="align-middle">
        {{ $quotation->valid_until->format('M d, Y') }}

        @if($quotation->valid_until->isPast())
            <div class="small text-danger">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Expired
            </div>
        @elseif(now()->diffInDays($quotation->valid_until, false) <= 7)
            <div class="small text-warning">
                {{ $quotation->valid_until->diffForHumans() }}
            </div>
        @endif
    </td>

    <td class="align-middle">
        {{ $quotation->created_at->format('M d, Y') }}

        <div class="small text-muted">
            {{ $quotation->created_at->format('h:i A') }}
        </div>
    </td>

    <td class="pe-4 align-middle text-end">
        <div class="btn-group btn-group-sm" role="group">

            {{-- View --}}
            <a href="{{ route('admin.quotations.show', $quotation) }}"
               class="btn btn-outline-kp-primary"
               title="View Quotation">
                <i class="fas fa-eye"></i>
            </a>

            {{-- Download PDF --}}
            <a href="{{ $downloadRoute }}"
               class="btn btn-outline-kp-primary"
               title="Download PDF">
                <i class="fas fa-download me-1"></i>PDF
            </a>

          @if($status === 'rejected')
    {{-- Review Quotation - Best option --}}
    <a href="{{ route('admin.quotations.review', $quotation) }}"
       class="btn btn-outline-warning"
       title="Review Rejected Quotation">
        <i class="fas fa-clipboard-list"></i>
    </a>
@endif

            {{-- Draft Actions --}}
            @if($status === 'draft')

                {{-- Edit --}}
                <a href="{{ route('admin.quotations.edit', $quotation) }}"
                   class="btn btn-outline-warning"
                   title="Edit Quotation">
                    <i class="fas fa-edit"></i>
                </a>

                {{-- Account Manager sends draft to customer --}}
                @if($isAccountManager)
                    <button type="button"
                            class="btn btn-outline-info btn-send"
                            data-bs-toggle="modal"
                            data-bs-target="#sendQuotationModal"
                            data-quotation-id="{{ $quotation->id }}"
                            title="Send to Customer">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                @endif

            @endif

            {{-- Admin final approval after customer accepts --}}
            @if($status === 'customer_approved' && $isAdmin)
                <button type="button"
                        class="btn btn-outline-success btn-approve"
                        data-bs-toggle="modal"
                        data-bs-target="#approveQuotationModal"
                        data-quotation-id="{{ $quotation->id }}"
                        title="Final Admin Approval">
                    <i class="fas fa-check"></i>
                </button>
            @endif

            {{-- Admin can reject draft/sent/customer-approved quotations --}}
            @if(in_array($status, ['draft', 'sent', 'customer_approved']) && $isAdmin)
                <button type="button"
                        class="btn btn-outline-danger btn-reject"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectQuotationModal"
                        data-quotation-id="{{ $quotation->id }}"
                        title="Reject Quotation">
                    <i class="fas fa-times"></i>
                </button>
            @endif

            {{-- @if($quotation->status === 'approved' && !$quotation->contract)
    <a href="{{ route('contracts.create.from.quotation', $quotation) }}"
       class="btn btn-outline-success btn-sm"
       title="Create Contract Draft">
        <i class="fas fa-file-contract me-1"></i>
        Create Contract
    </a>
@endif --}}

@if($quotation->status === 'approved')
    @if($quotation->contract)
        <a href="{{ route('contracts.show', $quotation->contract) }}"
           class="btn btn-outline-info"
           title="View Contract">
            <i class="fas fa-file-contract"></i>
        </a>
    @else
        <a href="{{ route('contracts.create.from.quotation', $quotation) }}"
           class="btn btn-outline-success"
           title="Create Contract Draft">
            <i class="fas fa-file-signature"></i>
        </a>
    @endif
@endif

@if($quotation->contract)
    <a href="{{ route('contracts.show', $quotation->contract) }}"
       class="btn btn-outline-info btn-sm"
       title="View Contract">
        <i class="fas fa-eye me-1"></i>
        View Contract
    </a>
@endif

            {{-- Duplicate --}}
            <button type="button"
                    class="btn btn-outline-secondary"
                    onclick="window.duplicateQuotation({{ $quotation->id }})"
                    title="Duplicate Quotation">
                <i class="fas fa-copy"></i>
            </button>

        </div>
    </td>
</tr>

