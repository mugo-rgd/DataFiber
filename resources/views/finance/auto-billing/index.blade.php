{{-- resources/views/finance/auto-billing/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Auto Billing Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb" class="mt-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('finance.dashboard') }}">Finance</a></li>
                    <li class="breadcrumb-item active">Auto Billing</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-robot me-2"></i>Auto Billing Management
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['due_customers_count'] ?? 0 }}</h4>
                                            <p class="mb-0 small">Due Customers</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['auto_billing_count'] ?? 0 }}</h4>
                                            <p class="mb-0 small">Auto Billing Enabled</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-bolt fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['overdue_count'] ?? 0 }}</h4>
                                            <p class="mb-0 small">Overdue Bills</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">${{ number_format($stats['monthly_revenue'] ?? 0, 2) }}</h4>
                                            <p class="mb-0 small">Monthly Revenue</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['total_auto_billing'] ?? 0 }}</h4>
                                            <p class="mb-0 small">Total Auto Billing</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                            <div class="card bg-dark text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['scheduled_count'] ?? 0 }}</h4>
                                            <p class="mb-0 small">Scheduled</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-calendar fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Due Customers Section -->
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-clock me-2"></i>Due Customers
                                        <span class="badge bg-dark ms-2">{{ $dueCustomers->count() ?? 0 }}</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($dueCustomers && $dueCustomers->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Customer</th>
                                                        <th>Due Date</th>
                                                        <th>Amount</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($dueCustomers as $customer)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm bg-light rounded me-2">
                                                                    <i class="fas fa-user text-primary p-2"></i>
                                                                </div>
                                                                <div>
                                                                    <strong>{{ $customer->name }}</strong>
                                                                    <br>
                                                                    <small class="text-muted">{{ $customer->email }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($customer->next_billing_date)
                                                                <span class="badge bg-danger">
                                                                    {{ \Carbon\Carbon::parse($customer->next_billing_date)->format('M d, Y') }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                                $pendingAmount = $customer->leaseBillings->where('status', 'pending')->sum('total_amount');
                                                            @endphp
                                                            <strong>${{ number_format($pendingAmount, 2) }}</strong>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('finance.billing.create', ['customer_id' => $customer->id]) }}"
                                                               class="btn btn-sm btn-primary" title="Create Bill">
                                                                <i class="fas fa-plus"></i> Bill
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                            <p class="mb-0">No due customers at the moment</p>
                                            <small>All customers are up to date with their payments</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Auto Billing Customers Section -->
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-bolt me-2"></i>Auto Billing Customers
                                        <span class="badge bg-light text-dark ms-2">{{ $autoBillingCustomers->count() ?? 0 }}</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($autoBillingCustomers && $autoBillingCustomers->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Customer</th>
                                                        <th>Next Billing</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($autoBillingCustomers as $customer)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm bg-light rounded me-2">
                                                                    <i class="fas fa-user text-success p-2"></i>
                                                                </div>
                                                                <div>
                                                                    <strong>{{ $customer->name }}</strong>
                                                                    <br>
                                                                    <small class="text-muted">{{ $customer->email }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($customer->next_billing_date)
                                                                <span class="badge bg-info">
                                                                    {{ \Carbon\Carbon::parse($customer->next_billing_date)->format('M d, Y') }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">Not Set</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $customer->auto_billing_enabled ? 'success' : 'secondary' }}">
                                                                <i class="fas fa-{{ $customer->auto_billing_enabled ? 'check' : 'times' }} me-1"></i>
                                                                {{ $customer->auto_billing_enabled ? 'Enabled' : 'Disabled' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <!-- Link to billing creation -->
                                                                <a href="{{ route('finance.billing.createSingle', ['customer_id' => $customer->id]) }}"
                                                                   class="btn btn-outline-success" title="Create Bill">
                                                                    <i class="fas fa-file-invoice"></i>
                                                                </a>
                                                                <!-- Use URL helper for customer view -->
                                                                <a href="{{ url('/customers/' . $customer->id) }}"
                                                                   class="btn btn-outline-primary" title="View Customer">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Pagination -->
                                        @if($autoBillingCustomers->hasPages())
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $autoBillingCustomers->links() }}
                                        </div>
                                        @endif
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-robot fa-3x mb-3 text-secondary"></i>
                                            <p class="mb-0">No auto billing customers configured</p>
                                            <small>Enable auto billing for customers to see them here</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scheduled Billings Section -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-calendar-alt me-2"></i>Scheduled Billings
                                        <span class="badge bg-light text-dark ms-2">{{ $scheduledBillings->count() ?? 0 }}</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($scheduledBillings && $scheduledBillings->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Invoice #</th>
                                                        <th>Customer</th>
                                                        <th>Lease</th>
                                                        <th>Due Date</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($scheduledBillings as $billing)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $billing->billing_number ?? 'N/A' }}</strong>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm bg-light rounded me-2">
                                                                    <i class="fas fa-user text-info p-2"></i>
                                                                </div>
                                                                <div>
                                                                    <strong>{{ $billing->customer->name ?? 'N/A' }}</strong>
                                                                    @if($billing->customer->email ?? false)
                                                                    <br>
                                                                    <small class="text-muted">{{ $billing->customer->email }}</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $billing->lease->title ?? 'N/A' }}</td>
                                                        <td>
                                                            @if($billing->due_date)
                                                                <span class="badge bg-{{ $billing->due_date < now() ? 'danger' : 'primary' }}">
                                                                    {{ \Carbon\Carbon::parse($billing->due_date)->format('M d, Y') }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <strong>${{ number_format($billing->total_amount, 2) }}</strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $billing->status === 'pending' ? 'warning' : ($billing->status === 'paid' ? 'success' : 'danger') }}">
                                                                {{ ucfirst($billing->status) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="{{ route('finance.billing.show', $billing->id) }}"
                                                                   class="btn btn-outline-primary" title="View Billing">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('finance.billing.edit', $billing->id) }}"
                                                                   class="btn btn-outline-secondary" title="Edit Billing">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-calendar-times fa-3x mb-3 text-secondary"></i>
                                            <p class="mb-0">No scheduled billings</p>
                                            <small>All billings are either processed or not scheduled yet</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                <a href="{{ route('finance.billing.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-list me-2"></i>View All Billings
                                </a>
                                <a href="{{ route('finance.billing.createSingle') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-plus me-2"></i>Create Manual Billing
                                </a>

                                <!-- FIXED: Disabled auto-generation button instead of missing route -->
                                <button type="button" class="btn btn-success" disabled title="Auto-generation feature coming soon">
                                    <i class="fas fa-play me-2"></i>Generate Due Invoices
                                </button>

                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#settingsModal">
                                    <i class="fas fa-cog me-2"></i>Auto Billing Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="settingsModalLabel">
                    <i class="fas fa-cog me-2"></i>Auto Billing Settings
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-bell me-2"></i>Notification Settings
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                    <label class="form-check-label" for="emailNotifications">
                                        Email Notifications
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="overdueAlerts" checked>
                                    <label class="form-check-label" for="overdueAlerts">
                                        Overdue Alerts
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="autoRetry" checked>
                                    <label class="form-check-label" for="autoRetry">
                                        Auto Retry Failed Payments
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-clock me-2"></i>Schedule Settings
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Billing Run Time</label>
                                    <select class="form-select">
                                        <option>Midnight (12:00 AM)</option>
                                        <option selected>Early Morning (2:00 AM)</option>
                                        <option>Morning (6:00 AM)</option>
                                        <option>Business Hours (9:00 AM)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Grace Period</label>
                                    <select class="form-select">
                                        <option>1 Day</option>
                                        <option selected>3 Days</option>
                                        <option>5 Days</option>
                                        <option>7 Days</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save Settings</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}
.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}
.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}
</style>
@endsection

@section('scripts')
<script>
// Auto-refresh the page every 5 minutes to keep data updated
setTimeout(function() {
    window.location.reload();
}, 300000); // 5 minutes

// Add some interactive features
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to cards
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'transform 0.2s ease';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endsection
