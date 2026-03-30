{{-- resources/views/finance/debt/overdue-invoices.blade.php --}}
@if(isset($error))
    <tr>
        <td colspan="8" class="text-center py-4 text-danger">
            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>
            Error: {{ $error }}
        </td>
    </tr>
@elseif($overdueBillings->count() > 0)
    @foreach($overdueBillings as $billing)
    @php
        // Calculate days overdue
        $daysOverdue = 0;
        $daysText = 'Not due';
        $daysBadgeClass = 'bg-success';

        if ($billing->due_date) {
            $dueDate = \Carbon\Carbon::parse($billing->due_date);
            $now = \Carbon\Carbon::now();

            if ($dueDate->isPast()) {
                $daysOverdue = $now->diffInDays($dueDate);
                $daysText = $daysOverdue . ' days';
                $daysBadgeClass = $daysOverdue > 90 ? 'bg-danger' : ($daysOverdue > 30 ? 'bg-warning' : 'bg-secondary');
            }
        } else {
            $daysText = 'No due date';
            $daysBadgeClass = 'bg-light text-dark';
        }

        // Determine status
        $displayStatus = $billing->status;
        $statusColor = 'warning';

        if ($billing->status == 'overdue' ||
            ($billing->due_date && $billing->due_date->isPast() && in_array($billing->status, ['pending', 'unpaid']))) {
            $displayStatus = 'Overdue';
            $statusColor = 'danger';
        } elseif ($billing->status == 'paid') {
            $statusColor = 'success';
        } elseif ($billing->status == 'partial') {
            $statusColor = 'info';
        }
    @endphp
    <tr>
        <td style="width: 50px; text-align: center; white-space: nowrap;">
            <input type="checkbox" class="invoice-checkbox" value="{{ $billing->id }}">
        </td>
        <td style="width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            <strong class="text-primary">#{{ $billing->billing_number ?? 'CONS-' . $billing->id }}</strong>
        </td>
        <td style="width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            @if($billing->user)
                {{ $billing->user->name }}
                @if($billing->user->company)
                    <br><small class="text-muted">{{ $billing->user->company }}</small>
                @endif
            @elseif($billing->User)
                {{ $billing->User->name }}
                @if($billing->User->company)
                    <br><small class="text-muted">{{ $billing->User->company }}</small>
                @endif
            @else
                <span class="text-muted">Unknown Customer</span>
            @endif
        </td>
        <td style="width: 120px; text-align: right; font-weight: 600; color: #e74a3b; white-space: nowrap;">
            ${{ number_format($billing->total_amount, 2) }}
        </td>
        <td style="width: 120px; white-space: nowrap;">
            @if($billing->due_date)
                {{ $billing->due_date->format('M d, Y') }}
            @else
                <span class="text-muted">N/A</span>
            @endif
        </td>
        <td style="width: 120px; white-space: nowrap;">
            <span class="badge {{ $daysBadgeClass }}">{{ $daysText }}</span>
        </td>
        <td style="width: 100px; white-space: nowrap;">
            <span class="badge bg-{{ $statusColor }}">
                {{ ucfirst($displayStatus) }}
            </span>
        </td>
        <td style="width: 140px; white-space: nowrap;">
            <div class="btn-group btn-group-sm" style="display: flex; gap: 2px;">
                <button class="btn btn-outline-warning btn-sm send-reminder"
                        data-invoice-id="{{ $billing->id }}"
                        title="Send Reminder"
                        data-bs-toggle="tooltip"
                        style="padding: 0.25rem 0.5rem;">
                    <i class="fas fa-envelope"></i>
                </button>
                <button class="btn btn-outline-info btn-sm create-payment-plan"
                        data-invoice-id="{{ $billing->id }}"
                        title="Payment Plan"
                        data-bs-toggle="tooltip"
                        style="padding: 0.25rem 0.5rem;">
                    <i class="fas fa-calendar-alt"></i>
                </button>
                <a href="{{ route('finance.debt.invoice.details', $billing->id) }}"
                   class="btn btn-outline-primary btn-sm"
                   title="View Details"
                   data-bs-toggle="tooltip"
                   style="padding: 0.25rem 0.5rem;">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
        </td>
    </tr>
    @endforeach
@else
    <tr>
        <td colspan="8" class="text-center py-4 text-muted">
            <div class="py-4">
                <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                <h5>All Clear!</h5>
                <p class="mb-0">No overdue invoices found.</p>
            </div>
        </td>
    </tr>
@endif

