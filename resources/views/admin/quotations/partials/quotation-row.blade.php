@php
    $statusConfig = [
        'draft' => ['color' => 'secondary', 'icon' => 'edit'],
        'approved' => ['color' => 'success', 'icon' => 'check-circle'],
        'sent' => ['color' => 'info', 'icon' => 'paper-plane'],
        'rejected' => ['color' => 'danger', 'icon' => 'times-circle']
    ];

    $status = $quotation->status;
    $config = $statusConfig[$status] ?? ['color' => 'secondary', 'icon' => 'question'];

    $isAdmin = auth()->user()->hasRole(['admin', 'system_admin', 'accountmanager_admin']);
    $isAccountManager = auth()->user()->hasRole(['account_manager', 'accountmanager_admin']);
@endphp

<tr data-quotation-id="{{ $quotation->id }}">
    <td class="ps-4 align-middle">
        <div>
            <strong class="d-block">{{ $quotation->quotation_number }}</strong>
            <small class="text-muted">#{{ $quotation->id }}</small>
        </div>
    </td>

    <td class="align-middle">
        @if($quotation->customer)
            <div>
                <strong class="d-block">{{ $quotation->customer->name }}</strong>
                <small class="text-muted">{{ $quotation->customer->email }}</small>
            </div>
        @else
            <span class="text-muted">N/A</span>
        @endif
    </td>

    <td class="align-middle">
        <span class="fw-bold">${{ number_format($quotation->total_amount, 2) }}</span>
    </td>

    <td class="align-middle">
        <span class="badge bg-{{ $config['color'] }}">
            <i class="fas fa-{{ $config['icon'] }} me-1"></i>
            {{ ucfirst($status) }}
        </span>
        @if($quotation->approved_at && $status === 'approved')
            <div class="small text-muted mt-1">
                {{ $quotation->approved_at->format('M d, Y') }}
            </div>
        @endif
    </td>

    <td class="align-middle">
        {{ $quotation->valid_until->format('M d, Y') }}
        @if($quotation->valid_until->isPast())
            <div class="small text-danger">
                <i class="fas fa-exclamation-triangle me-1"></i>Expired
            </div>
        @elseif($quotation->valid_until->diffInDays(now()) <= 7)
            <div class="small text-warning">
                {{ $quotation->valid_until->diffForHumans() }}
            </div>
        @endif
    </td>

    <td class="align-middle">
        {{ $quotation->created_at->format('M d, Y') }}
        <div class="small text-muted">{{ $quotation->created_at->format('h:i A') }}</div>
    </td>

    <td class="pe-4 align-middle text-end">
        <div class="btn-group btn-group-sm" role="group">
            <!-- View Button -->
            <a href="{{ route('admin.quotations.show', $quotation) }}"
               class="btn btn-outline-primary"
               title="View Quotation">
                <i class="fas fa-eye"></i>
            </a>
                                                @if($isAdmin)
 <a href="{{ route('admin.quotations.download', $quotation) }}"
                                                  class="btn btn-outline-primary"
                                                   title="Download PDF">
                                                    <i class="fas fa-download me-1"></i>PDF
                                                </a>@else
<a href="{{ route('account-manager.quotations.download', $quotation) }}"
                                                  class="btn btn-outline-primary"
                                                   title="Download PDF">
                                                    <i class="fas fa-download me-1"></i>PDF
                                               </a> @endif


            <!-- Draft Actions -->
            @if($status === 'draft')
                <!-- Edit -->
                <a href="{{ route('admin.quotations.edit', $quotation) }}"
                   class="btn btn-outline-warning"
                   title="Edit Quotation">
                    <i class="fas fa-edit"></i>
                </a>

                <!-- Approve Button (Admin only) -->
                @if($isAdmin)
                        <button type="button"
                            class="btn btn-outline-success btn-approve"
                            data-bs-toggle="modal"
                            data-bs-target="#approveQuotationModal"
                            data-quotation-id="{{ $quotation->id }}"
                            title="Approve Quotation">
                         <i class="fas fa-check"></i>
                        </button>

                                        <!-- Reject Button -->
                            <button type="button"
                                    class="btn btn-outline-danger btn-reject"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectQuotationModal"
                                    data-quotation-id="{{ $quotation->id }}"
                                    title="Reject Quotation">
                                <i class="fas fa-times"></i>
                            </button>

                    @endif
            @endif

                            <!-- Send Button (for approved quotations) -->
                @if($status === 'approved' && $isAdmin)
                    <button type="button"
                        class="btn btn-outline-info btn-send"
                        data-bs-toggle="modal"
                        data-bs-target="#sendQuotationModal"
                        data-quotation-id="{{ $quotation->id }}"
                        title="Send to Customer">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                 @endif

            <!-- Duplicate Button -->
            <button type="button"
                    class="btn btn-outline-secondary"
                    onclick="window.duplicateQuotation({{ $quotation->id }})"
                    title="Duplicate Quotation">
                <i class="fas fa-copy"></i>
            </button>
        </div>
    </td>
</tr>

<script>
function duplicateQuotation(id) {
    if (confirm('Duplicate this quotation?')) {
        fetch(`/admin/quotations/${id}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Quotation duplicated successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error. Please try again.');
        });
    }
}
</script>
