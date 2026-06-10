@extends('layouts.app')

@section('title', 'Customer Details - ' . ($customer->company_name ?? $customer->name))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-user-circle me-2" style="color: #0066B3;"></i>
                    Customer Details: {{ $customer->company_name ?? $customer->name }}
                </h1>
                <div>
                    <a href="{{ route('account-manager.customers.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                    <button type="button" class="btn btn-danger" onclick="sendReminder()">
                        <i class="fas fa-bell me-2"></i>Send Reminder
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Outstanding Balance</h6>
                    <h3 class="mb-0">${{ number_format($debtSummary->outstanding ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">Overdue Amount</h6>
                    <h3 class="mb-0">${{ number_format($debtSummary->overdue_amount ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Paid</h6>
                    <h3 class="mb-0">${{ number_format($debtSummary->total_paid ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Open Tickets</h6>
                    <h3 class="mb-0">{{ $stats['open_tickets'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border">
                <div class="card-body text-center">
                    <h5 class="mb-0">{{ $stats['active_leases'] }}</h5>
                    <small class="text-muted">Active Leases</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border">
                <div class="card-body text-center">
                    <h5 class="mb-0">{{ $stats['pending_payments'] }}</h5>
                    <small class="text-muted">Pending Payments</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border">
                <div class="card-body text-center">
                    <h5 class="mb-0">{{ $stats['total_tickets'] }}</h5>
                    <small class="text-muted">Total Tickets</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border">
                <div class="card-body text-center">
                    <h5 class="mb-0">{{ $stats['profile_completion'] }}%</h5>
                    <small class="text-muted">Profile Complete</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Invoices</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($billings as $billing)
                            @php
                                $balance = $billing->total_amount - ($billing->paid_amount ?? 0);
                                $isOverdue = $billing->due_date && $billing->due_date < now() && $balance > 0;
                            @endphp
                            <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                <td class="fw-bold">{{ $billing->billing_number }}</td>
                                <td>{{ $billing->billing_date ? $billing->billing_date->format('M d, Y') : 'N/A' }}</td>
                                <td class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                    {{ $billing->due_date ? $billing->due_date->format('M d, Y') : 'N/A' }}
                                    @if($isOverdue)
                                        <span class="badge bg-danger ms-1">Overdue</span>
                                    @endif
                                </td>
                                <td>{{ $billing->currency }} {{ number_format($billing->total_amount, 2) }}</td>
                                <td>{{ $billing->currency }} {{ number_format($billing->paid_amount ?? 0, 2) }}</td>
                                <td class="fw-bold {{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $billing->currency }} {{ number_format($balance, 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $billing->status === 'paid' ? 'success' : ($billing->status === 'overdue' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($billing->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No invoices found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($billings->hasPages())
            <div class="card-footer bg-white">
                {{ $billings->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function sendReminder() {
    if (confirm('Send payment reminder to {{ $customer->name }}?')) {
        fetch('{{ route("account-manager.customers.send-reminder", $customer->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Reminder sent successfully!');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send reminder. Please try again.');
        });
    }
}
</script>
@endsection
