{{-- resources/views/finance/debt/partials/overdue-invoices-table.blade.php --}}
@if(isset($error))
    <tr>
        <td colspan="8" class="text-center py-4 text-danger">
            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>
            Error: {{ $error }}
        </td>
    </tr>
@elseif($overdueBillings->count() > 0)
    @foreach($overdueBillings as $billing)
    <tr>
        <td>
            <input type="checkbox" class="invoice-checkbox" value="{{ $billing->id }}">
        </td>
        <td>
            <strong class="text-primary">#{{ $billing->billing_number ?? 'CONS-' . $billing->id }}</strong>
        </td>
        <td>
            @if($billing->user)
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user text-primary"></i>
                    </div>
                    <div>
                        <strong>{{ $billing->user->name }}</strong>
                        @if($billing->user->company_name)
                            <br><small class="text-muted">{{ $billing->user->company_name }}</small>
                        @endif
                        <span class="badge bg-{{ $billing->currency == 'USD' ? 'primary' : 'success' }} ms-1">
                            {{ $billing->currency }}
                        </span>
                    </div>
                </div>
            @elseif($billing->User)
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user text-primary"></i>
                    </div>
                    <div>
                        <strong>{{ $billing->User->name }}</strong>
                        @if($billing->User->company_name)
                            <br><small class="text-muted">{{ $billing->User->company_name }}</small>
                        @endif
                        <span class="badge bg-{{ $billing->currency == 'USD' ? 'primary' : 'success' }} ms-1">
                            {{ $billing->currency }}
                        </span>
                    </div>
                </div>
            @else
                <span class="text-muted">Unknown Customer</span>
            @endif
        </td>
        <td class="font-weight-bold">
            @if($billing->currency == 'USD')
                ${{ number_format($billing->total_amount, 2) }}
            @else
                KSH {{ number_format($billing->total_amount, 2) }}
            @endif
        </td>
        <td>
            @if($billing->due_date)
                <div class="text-nowrap">
                    {{ $billing->due_date instanceof \Carbon\Carbon ? $billing->due_date->format('M d, Y') : \Carbon\Carbon::parse($billing->due_date)->format('M d, Y') }}
                </div>
            @else
                <span class="text-muted">N/A</span>
            @endif
        </td>
        <td>
            @if($billing->due_date)
                @php
                    // FIXED: Calculate days overdue correctly
                    $dueDate = $billing->due_date instanceof \Carbon\Carbon
                        ? $billing->due_date
                        : \Carbon\Carbon::parse($billing->due_date);
                    $now = \Carbon\Carbon::now();

                    if ($dueDate->isPast()) {
                        // Days overdue = current date - due date (positive number)
                        $daysOverdue = $dueDate->diffInDays($now);
                    } else {
                        // Not overdue yet
                        $daysOverdue = 0;
                    }
                @endphp

                @if($daysOverdue > 0)
                    <span class="badge bg-{{ $daysOverdue > 90 ? 'danger' : ($daysOverdue > 60 ? 'warning' : 'info') }}">
                        {{ number_format($daysOverdue, 1) }} days overdue
                    </span>
                @else
                    <span class="badge bg-secondary">Due today</span>
                @endif
            @else
                <span class="badge bg-light text-dark">No due date</span>
            @endif
        </td>
        <td>
            @php
                // Determine actual status - if due date is past and status is pending/unpaid, it's actually overdue
                $displayStatus = $billing->status;
                $statusColor = 'warning';

                if ($billing->status == 'overdue' ||
                    ($billing->due_date && $billing->due_date->isPast() && in_array($billing->status, ['pending', 'unpaid', 'sent']))) {
                    $displayStatus = 'Overdue';
                    $statusColor = 'danger';
                } elseif ($billing->status == 'paid') {
                    $statusColor = 'success';
                } elseif ($billing->status == 'partial') {
                    $statusColor = 'info';
                }
            @endphp
            <span class="badge bg-{{ $statusColor }}">
                {{ ucfirst($displayStatus) }}
            </span>
        </td>
        <td>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-warning btn-sm send-reminder"
                        data-invoice-id="{{ $billing->id }}"
                        title="Send Reminder"
                        data-bs-toggle="tooltip">
                    <i class="fas fa-envelope"></i>
                </button>
                <button class="btn btn-outline-info btn-sm create-payment-plan"
                        data-invoice-id="{{ $billing->id }}"
                        title="Payment Plan"
                        data-bs-toggle="tooltip">
                    <i class="fas fa-calendar-alt"></i>
                </button>
                <a href="{{ route('finance.debt.invoice.details', $billing->id) }}"
                   class="btn btn-outline-primary btn-sm"
                   title="View Details"
                   data-bs-toggle="tooltip">
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
