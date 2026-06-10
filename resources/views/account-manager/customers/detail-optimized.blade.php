@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">
                        <i class="fas fa-user-circle me-2" style="color: #0066B3;"></i>
                        {{ $customer->company_name ?? $customer->name }}
                    </h4>
                    <small class="text-muted">Customer ID: #{{ $customer->id }}</small>
                </div>
                <div>
                    <a href="{{ route('account-manager.customers.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                    <button onclick="sendReminder()" class="btn btn-sm btn-danger ms-1">
                        <i class="fas fa-bell me-1"></i> Send Reminder
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Info Row -->
    <div class="row mb-3">
        <div class="col-md-3 mb-2">
            <div class="card bg-light">
                <div class="card-body p-2 text-center">
                    <small class="text-muted">Email</small>
                    <p class="mb-0 small">{{ $customer->email }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card bg-light">
                <div class="card-body p-2 text-center">
                    <small class="text-muted">Phone</small>
                    <p class="mb-0 small">{{ $customer->phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card bg-light">
                <div class="card-body p-2 text-center">
                    <small class="text-muted">Status</small>
                    <p class="mb-0 small">
                        <span class="badge bg-{{ $customer->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($customer->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card bg-light">
                <div class="card-body p-2 text-center">
                    <small class="text-muted">Member Since</small>
                    <p class="mb-0 small">{{ date('M d, Y', strtotime($customer->created_at)) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="row mb-3">
        <div class="col-md-3 mb-2">
            <div class="card bg-danger text-white">
                <div class="card-body p-2 text-center">
                    <small>Outstanding</small>
                    <h5 class="mb-0">${{ number_format($debtSummary->outstanding ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card bg-warning text-dark">
                <div class="card-body p-2 text-center">
                    <small>Overdue</small>
                    <h5 class="mb-0">${{ number_format($debtSummary->overdue_amount ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card bg-success text-white">
                <div class="card-body p-2 text-center">
                    <small>Total Paid</small>
                    <h5 class="mb-0">${{ number_format($debtSummary->total_paid ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card bg-primary text-white">
                <div class="card-body p-2 text-center">
                    <small>Open Tickets</small>
                    <h5 class="mb-0">{{ $stats->open_tickets ?? 0 }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Stats -->
    <div class="row mb-3">
        <div class="col-md-3 mb-2">
            <div class="card border">
                <div class="card-body p-2 text-center">
                    <small>Active Leases</small>
                    <h5 class="mb-0">{{ $stats->active_leases ?? 0 }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card border">
                <div class="card-body p-2 text-center">
                    <small>Pending Payments</small>
                    <h5 class="mb-0">{{ $stats->pending_payments ?? 0 }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card border">
                <div class="card-body p-2 text-center">
                    <small>Pending Docs</small>
                    <h5 class="mb-0">{{ $stats->pending_documents ?? 0 }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card border">
                <div class="card-body p-2 text-center">
                    <small>Total Tickets</small>
                    <h5 class="mb-0">{{ $stats->total_tickets ?? 0 }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Invoices Table -->
    <div class="card mb-3">
        <div class="card-header py-2">
            <small class="fw-bold">RECENT INVOICES (Last 10)</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentInvoices as $inv)
                        <tr>
                            <td><small>{{ $inv->billing_number }}</small></td>
                            <td><small>{{ $inv->billing_date }}</small></td>
                            <td><small>{{ $inv->due_date }}</small></td>
                            <td><small>{{ $inv->currency }} {{ number_format($inv->total_amount, 2) }}</small></td>
                            <td>
                                @php
                                    $badge = $inv->status === 'paid' ? 'success' : ($inv->status === 'overdue' ? 'danger' : 'warning');
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ $inv->status }}</span>
                            </td>
                        </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">No invoices</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Overdue Invoices Section (if any) -->
    @if(count($overdueInvoices) > 0)
    <div class="card mb-3 border-danger">
        <div class="card-header bg-danger text-white py-2">
            <small class="fw-bold">OVERDUE INVOICES</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overdueInvoices as $inv)
                        <tr>
                            <td><small>{{ $inv->billing_number }}</small></td>
                            <td><small class="text-danger">{{ $inv->due_date }}</small></td>
                            <td><small>{{ $inv->currency }} {{ number_format($inv->total_amount, 2) }}</small></td>
                            <td><small class="text-danger">{{ $inv->currency }} {{ number_format($inv->total_amount - ($inv->paid_amount ?? 0), 2) }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function sendReminder() {
    if (confirm('Send payment reminder to {{ $customer->name }}?')) {
        fetch('{{ route("account-manager.customers.send-reminder", $customer->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(r => r.json())
        .then(d => alert(d.message))
        .catch(e => alert('Failed to send reminder'));
    }
}
</script>
@endsection
