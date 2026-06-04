@if(isset($error))
    <tr>
        <td colspan="8" class="text-center py-5 text-danger">
            <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
            Error: {{ $error }}
        </td>
    </tr>
@elseif($overdueBillings->count() > 0)
    @foreach($overdueBillings as $billing)
        @php
            $dueDate = $billing->due_date instanceof \Carbon\Carbon
                ? $billing->due_date
                : \Carbon\Carbon::parse($billing->due_date);

            $daysOverdue = $dueDate->isPast() ? $dueDate->diffInDays(now()) : 0;

            if ($daysOverdue >= 90) {
                $severityIcon = '🔥';
                $severityClass = 'badge-critical';
            } elseif ($daysOverdue >= 60) {
                $severityIcon = '🔴';
                $severityClass = 'badge-high';
            } elseif ($daysOverdue >= 30) {
                $severityIcon = '🟡';
                $severityClass = 'badge-medium';
            } else {
                $severityIcon = '🟢';
                $severityClass = 'badge-low';
            }

            $currency = strtoupper($billing->currency ?? 'KSH');
            $amount = (float) ($billing->total_amount ?? 0);

            $formattedAmount = $currency === 'USD'
                ? '$' . number_format($amount, 2)
                : 'KSH ' . number_format($amount, 2);

            $customer = $billing->user ?? null;
            $customerName = $customer->name ?? 'Unknown Customer';
            $companyName = $customer->company_name ?? $customer->company ?? '';
            $initial = strtoupper(substr($customerName, 0, 1));

            $avatarColors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
            $avatarColor = $avatarColors[abs(crc32($customerName)) % count($avatarColors)];

            $paymentStatus = strtolower($billing->status ?? 'overdue');
            $statusText = $paymentStatus === 'partial' ? 'Partially Paid' : 'Overdue';
            $statusIcon = $paymentStatus === 'partial' ? '⏳' : '⚠️';

            $billingNumber = $billing->billing_number ?? 'CONS-' . $billing->id;
        @endphp

        <tr data-currency="{{ $currency }}">
            <td>
                <input type="checkbox" class="invoice-select modern-checkbox" data-id="{{ $billing->id }}">
            </td>
            <td>
                <span class="invoice-number">{{ $billingNumber }}</span>
            </td>
            <td>
                <div class="d-flex align-items-center gap-3">
                    <div class="customer-avatar" style="background: {{ $avatarColor }};">
                        {{ $initial }}
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $customerName }}</div>
                        @if($companyName)
                            <small class="text-muted d-block">{{ $companyName }}</small>
                        @endif
                        <span class="badge bg-{{ $currency === 'USD' ? 'primary' : 'secondary' }} bg-opacity-10 text-{{ $currency === 'USD' ? 'primary' : 'secondary' }} mt-1">
                            {{ $currency }}
                        </span>
                    </div>
                </div>
            </td>
            <td class="{{ $currency === 'USD' ? 'text-danger' : 'text-warning' }} fw-bold">
                {{ $formattedAmount }}
            </td>
            <td class="text-muted">
                {{ $dueDate->format('M d, Y') }}
            </td>
            <td>
                <span class="{{ $severityClass }}">
                    {{ $severityIcon }} {{ number_format($daysOverdue) }} days
                </span>
            </td>
            <td>
                <span class="badge {{ $paymentStatus === 'partial' ? 'bg-warning' : 'bg-danger' }} rounded-pill px-3 py-2">
                    {{ $statusIcon }} {{ $statusText }}
                </span>
            </td>
            <td>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-warning action-btn send-reminder-btn"
                            data-id="{{ $billing->id }}"
                            data-customer="{{ $customerName }}"
                            data-invoice="{{ $billingNumber }}"
                            title="Send Reminder">
                        <i class="fas fa-envelope"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info action-btn payment-plan-btn"
                            data-id="{{ $billing->id }}"
                            data-amount="{{ $amount }}"
                            data-currency="{{ $currency }}"
                            title="Create Payment Plan">
                        <i class="fas fa-calendar-alt"></i>
                    </button>
                    <a href="{{ route('finance.debt.invoice.details', $billing->id) }}"
                       class="btn btn-sm btn-outline-primary action-btn"
                       title="View Details">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="8" class="text-center py-5">
            <i class="fas fa-check-circle fa-3x text-success opacity-25 mb-2 d-block"></i>
            <p class="text-muted">No overdue invoices found</p>
        </td>
    </tr>
@endif
