@extends('layouts.app')

@section('title', 'My Customers')

@section('content')

@php
    if (!function_exists('formatPhoneNumber')) {
        function formatPhoneNumber($phone)
        {
            if (empty($phone)) {
                return null;
            }

            // convert scientific notation
            if (is_numeric($phone)) {
                $phone = number_format((float)$phone, 0, '', '');
            }

            $phone = trim((string)$phone);

            // remove everything except digits
            $digits = preg_replace('/[^0-9]/', '', $phone);

            // 254712345678
            if (strlen($digits) === 12 && substr($digits,0,3) === '254') {
                return '+254 '
                    . substr($digits,3,3)
                    . ' '
                    . substr($digits,6,3)
                    . ' '
                    . substr($digits,9,3);
            }

            // 0712345678
            if (strlen($digits) === 10 && substr($digits,0,1) === '0') {
                return '+254 '
                    . substr($digits,1,3)
                    . ' '
                    . substr($digits,4,3)
                    . ' '
                    . substr($digits,7,3);
            }

            return $phone;
        }
    }
@endphp
<div class="container-fluid">

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">
                        <i class="fas fa-users me-2" style="color: #0066B3;"></i>
                        My Customers
                    </h1>
                    <p class="text-muted mb-0">Manage and support assigned customers</p>
                </div>
                <span class="badge rounded-pill px-4 py-2" style="background: #0066B3;">
                    Total: {{ $customers->total() }} Customers
                </span>
            </div>
        </div>
    </div>

    {{-- Search and Filter Bar --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('account-manager.customers.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, email or company..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('account-manager.customers.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-sync-alt me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @forelse($customers as $customer)
            @php
                // Use pre-loaded data from controller - NO NEW QUERIES HERE!
                $formattedPhone = formatPhoneNumber($customer->phone);
                $totalDebt = $customer->total_debt ?? 0;
                $overdueDebt = $customer->overdue_debt ?? 0;
                $pendingInvoices = $customer->pending_invoices ?? 0;
                $overdueInvoices = $customer->overdue_invoices ?? 0;
                $pendingCount = $customer->pending_documents_count ?? 0;
                $openTickets = $customer->open_tickets_count ?? 0;
                $pendingPayments = $customer->pending_payments_count ?? 0;
                $oldestOverdueDate = $customer->oldest_overdue_date ?? null;
            @endphp

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card customer-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        {{-- Customer Header --}}
                        <div class="d-flex align-items-start mb-3">
                            <div class="customer-avatar">
                                {{ strtoupper(substr($customer->name,0,1)) }}
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <h5 class="fw-bold mb-1" style="color: #0066B3;">
                                    {{ $customer->company_name ?? $customer->name }}
                                </h5>
                                <div class="small text-muted">Customer ID: #{{ $customer->id }}</div>
                            </div>
                        </div>

                        {{-- Customer Details --}}
                        <div class="customer-details">
                            <div class="detail-item">
                                <i class="fas fa-envelope" style="color: #0066B3;"></i>
                                <span>{{ $customer->email }}</span>
                            </div>
                            @if($formattedPhone)
                            <div class="detail-item">
                                <i class="fas fa-phone" style="color: #009639;"></i>
                                <span>{{ $formattedPhone }}</span>
                            </div>
                            @endif
                            <div class="detail-item">
                                <i class="fas fa-calendar" style="color: #FFD700;"></i>
                                <span>Assigned: {{ $customer->assigned_at ? \Carbon\Carbon::parse($customer->assigned_at)->format('M d, Y') : 'N/A' }}</span>
                            </div>
                        </div>

                        {{-- Debt Summary --}}
                        <div class="debt-summary mt-3 p-2 rounded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-white">
                                    <small class="opacity-75">Total Outstanding</small>
                                    <div class="fw-bold fs-5">${{ number_format($totalDebt, 2) }}</div>
                                </div>
                                @if($overdueDebt > 0)
                                    <div class="text-warning">
                                        <small class="opacity-75">Overdue</small>
                                        <div class="fw-bold">${{ number_format($overdueDebt, 2) }}</div>
                                    </div>
                                @endif
                                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#debtModal{{ $customer->id }}">
                                    <i class="fas fa-chart-line me-1"></i> Details
                                </button>
                            </div>
                            <div class="mt-2 d-flex justify-content-between text-white-50 small">
                                <span><i class="fas fa-file-invoice me-1"></i> {{ $pendingInvoices }} Pending</span>
                                @if($overdueInvoices > 0)
                                    <span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i> {{ $overdueInvoices }} Overdue</span>
                                @endif
                            </div>
                        </div>

                        {{-- Statistics --}}
                        <div class="row text-center mt-3">
                            <div class="col-4">
                                <div class="stats-box" style="background: #FFF8E1;">
                                    <div class="fw-bold" style="color: #FFD700;">{{ $openTickets }}</div>
                                    <small>Open Tickets</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stats-box" style="background: #EAF6FF;">
                                    <div class="fw-bold" style="color: #0066B3;">{{ $pendingPayments }}</div>
                                    <small>Pending Payments</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stats-box" style="background: #FFE8E8;">
                                    <div class="fw-bold" style="color: #dc3545;">{{ $pendingInvoices }}</div>
                                    <small>Unpaid Invoices</small>
                                </div>
                            </div>
                        </div>

                        {{-- Warning for overdue --}}
                        @if($overdueDebt > 0 && $oldestOverdueDate)
                            <div class="alert alert-warning alert-sm mt-2 mb-0 py-1 px-2" style="font-size: 0.7rem;">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Overdue since {{ \Carbon\Carbon::parse($oldestOverdueDate)->format('M d, Y') }}
                            </div>
                        @endif

                        {{-- Buttons --}}
                        <div class="mt-3 d-grid gap-2">
                            @if($totalDebt > 0)
                                <button type="button" class="btn btn-sm text-white" style="background: #dc3545;"
                                        onclick="sendReminder({{ $customer->id }}, '{{ addslashes($customer->name) }}', {{ $totalDebt }}, {{ $overdueDebt }})">
                                    <i class="fas fa-bell me-1"></i> Send Payment Reminder
                                </button>
                            @endif
                            <a href="{{ route('account-manager.customers.show', $customer->id) }}"
                               class="btn btn-sm text-white" style="background: #0066B3;">
                                <i class="fas fa-eye me-1"></i> View Details
                            </a>
                          <div class="row g-1">
    <div class="col-6">
        <a href="{{ route('account-manager.documents.approve', ['user' => $customer->id]) }}"
           class="btn btn-sm text-dark w-100" style="background: #FFD700;">
            <i class="fas fa-check-circle me-1"></i> Documents
            @if($customer->pending_documents_count ?? 0 > 0)
                <span class="badge bg-danger ms-1">{{ $customer->pending_documents_count }}</span>
            @endif
        </a>
    </div>
    <div class="col-6">
        <a href="{{ route('account-manager.customers.documents.manage', ['customer' => $customer->id]) }}"
           class="btn btn-sm text-white w-100" style="background: #009639;">
            <i class="fas fa-file-upload me-1"></i> Manage
        </a>
    </div>
</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Debt Details Modal - Only load once with pre-fetched data --}}
            <div class="modal fade" id="debtModal{{ $customer->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <h5 class="modal-title text-white">
                                <i class="fas fa-chart-line me-2"></i> Debt Summary - {{ $customer->company_name ?? $customer->name }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-light border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted">Total Outstanding</h6>
                                            <h3 class="text-danger fw-bold">${{ number_format($totalDebt, 2) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted">Overdue Amount</h6>
                                            <h3 class="text-warning fw-bold">${{ number_format($overdueDebt, 2) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted">Pending Invoices</h6>
                                            <h3 class="text-primary fw-bold">{{ $pendingInvoices }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted">Overdue Invoices</h6>
                                            <h3 class="text-danger fw-bold">{{ $overdueInvoices }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center text-muted py-3">
                                <i class="fas fa-info-circle me-1"></i>
                                Click "View Details" to see complete invoice history
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('account-manager.customers.show', $customer->id) }}" class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i> View Full Details
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Customers Assigned</h4>
                        <p class="text-muted">You currently have no assigned customers.</p>
                        <small class="text-muted">Contact your administrator.</small>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($customers->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                {{ $customers->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Success/Error Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
    <div id="reminderToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">
                Reminder sent successfully!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
function sendReminder(customerId, customerName, totalDebt, overdueDebt) {
    if (!confirm(`Send payment reminder to ${customerName}?\n\nTotal Outstanding: $${totalDebt.toFixed(2)}\nOverdue: $${overdueDebt.toFixed(2)}\n\nThe customer will receive an email notification about their pending payments.`)) {
        return;
    }

    fetch(`/account-manager/customers/${customerId}/send-reminder`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            total_debt: totalDebt,
            overdue_debt: overdueDebt,
            reminder_type: 'general'
        })
    })
    .then(response => response.json())
    .then(data => {
        showToast(data.message || 'Reminder sent successfully!', data.success ? 'success' : 'danger');
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to send reminder. Please try again.', 'danger');
    });
}

function showToast(message, type = 'success') {
    const toastEl = document.getElementById('reminderToast');
    toastEl.classList.remove('bg-success', 'bg-danger');
    toastEl.classList.add(type === 'success' ? 'bg-success' : 'bg-danger');
    document.getElementById('toastMessage').textContent = message;
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();
}
</script>

<style>
/* Kenya Power Corporate Colors */
:root {
    --kp-blue: #0066B3;
    --kp-green: #009639;
    --kp-yellow: #FFD700;
    --kp-blue-light: #EAF6FF;
    --kp-yellow-light: #FFF8E1;
}

.customer-card {
    border-radius: 16px;
    transition: all 0.3s ease;
    border-top: 4px solid #0066B3;
}

.customer-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}

.customer-avatar {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0066B3, #009639);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    box-shadow: 0 2px 8px rgba(0,102,179,0.3);
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    font-size: .85rem;
    padding: 6px 8px;
    border-radius: 8px;
    transition: background 0.2s ease;
}

.detail-item:hover {
    background: rgba(0,102,179,0.05);
}

.detail-item i {
    width: 18px;
}

.stats-box {
    border-radius: 12px;
    padding: 8px;
    transition: transform 0.2s ease;
}

.stats-box:hover {
    transform: scale(1.02);
}

.debt-summary {
    border-radius: 12px;
    transition: all 0.3s ease;
}

.debt-summary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn {
    transition: all 0.2s ease;
    font-weight: 500;
    border: none;
}

.btn:hover {
    transform: translateY(-1px);
    filter: brightness(95%);
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

/* Alert small */
.alert-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .debt-summary .fs-5 {
        font-size: 1rem;
    }
    .stats-box .fw-bold {
        font-size: 1rem;
    }
}
</style>

@endsection
