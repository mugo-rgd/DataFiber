@extends('layouts.app')

@section('title', 'Customer Details - ' . $customer->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb" class="mt-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/customers') }}">Customers</a></li>
                    <li class="breadcrumb-item active">{{ $customer->name }}</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>Customer Details
                        </h3>
                        <div class="btn-group">
                            <a href="{{ route('finance.billing.create', ['customer_id' => $customer->id]) }}"
                               class="btn btn-light btn-sm">
                                <i class="fas fa-file-invoice me-1"></i>Create Bill
                            </a>
                            <a href="{{ url('/customers') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Customer Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Basic Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Name:</th>
                                            <td>{{ $customer->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td>{{ $customer->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone:</th>
                                            <td>{{ $customer->phone ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Company:</th>
                                            <td>{{ $customer->company ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Auto Billing:</th>
                                            <td>
                                                <span class="badge bg-{{ $customer->auto_billing_enabled ? 'success' : 'secondary' }}">
                                                    <i class="fas fa-{{ $customer->auto_billing_enabled ? 'check' : 'times' }} me-1"></i>
                                                    {{ $customer->auto_billing_enabled ? 'Enabled' : 'Disabled' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($customer->next_billing_date)
                                        <tr>
                                            <th>Next Billing:</th>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ \Carbon\Carbon::parse($customer->next_billing_date)->format('M d, Y') }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>Created:</th>
                                            <td>{{ $customer->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Summary -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-bar me-2"></i>Billing Summary
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <div class="border rounded p-3">
                                                <h4 class="text-primary mb-1">{{ $customer->leaseBillings->count() }}</h4>
                                                <small class="text-muted">Total Billings</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="border rounded p-3">
                                                <h4 class="text-warning mb-1">{{ $customer->leaseBillings->where('status', 'pending')->count() }}</h4>
                                                <small class="text-muted">Pending</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border rounded p-3">
                                                <h4 class="text-success mb-1">{{ $customer->leaseBillings->where('status', 'paid')->count() }}</h4>
                                                <small class="text-muted">Paid</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border rounded p-3">
                                                <h4 class="text-danger mb-1">{{ $customer->leaseBillings->where('status', 'overdue')->count() }}</h4>
                                                <small class="text-muted">Overdue</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Billings -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-file-invoice me-2"></i>Recent Billings
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($customer->leaseBillings->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Invoice #</th>
                                                        <th>Lease</th>
                                                        <th>Amount</th>
                                                        <th>Due Date</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($customer->leaseBillings->sortByDesc('created_at')->take(10) as $billing)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $billing->billing_number ?? 'N/A' }}</strong>
                                                        </td>
                                                        <td>{{ $billing->lease->title ?? 'N/A' }}</td>
                                                        <td>${{ number_format($billing->total_amount, 2) }}</td>
                                                        <td>
                                                            @if($billing->due_date)
                                                                {{ \Carbon\Carbon::parse($billing->due_date)->format('M d, Y') }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $billing->status === 'pending' ? 'warning' : ($billing->status === 'paid' ? 'success' : 'danger') }}">
                                                                {{ ucfirst($billing->status) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('finance.billing.show', $billing->id) }}"
                                                               class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i> View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if($customer->leaseBillings->count() > 10)
                                        <div class="text-center mt-3">
                                            <a href="{{ route('finance.billing.index') }}?customer_id={{ $customer->id }}"
                                               class="btn btn-outline-secondary btn-sm">
                                                View All Billings ({{ $customer->leaseBillings->count() }})
                                            </a>
                                        </div>
                                        @endif
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No billing records found for this customer.</p>
                                            <a href="{{ route('finance.billing.create', ['customer_id' => $customer->id]) }}"
                                               class="btn btn-primary mt-2">
                                                <i class="fas fa-plus me-2"></i>Create First Bill
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ url('/customers') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Customers
                                </a>
                                <a href="{{ route('finance.billing.create', ['customer_id' => $customer->id]) }}"
                                   class="btn btn-primary">
                                    <i class="fas fa-file-invoice me-2"></i>Create New Bill
                                </a>
                                <a href="{{ url('/customers/' . $customer->id . '/edit') }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Edit Customer
                                </a>
                                <button type="button" class="btn btn-info" onclick="toggleAutoBilling({{ $customer->id }})">
                                    <i class="fas fa-robot me-2"></i>
                                    {{ $customer->auto_billing_enabled ? 'Disable' : 'Enable' }} Auto Billing
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleAutoBilling(customerId) {
    if (confirm('Are you sure you want to toggle auto billing for this customer?')) {
        // You would typically make an AJAX request here
        // For now, we'll just show an alert
        alert('Auto billing toggle functionality would be implemented here. Customer ID: ' + customerId);

        // Example AJAX implementation:
        /*
        fetch('/customers/' + customerId + '/toggle-auto-billing', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
        */
    }
}

// Add some interactive features
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to billing summary cards
    const summaryCards = document.querySelectorAll('.border.rounded');
    summaryCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
            this.style.transition = 'all 0.2s ease';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
});
</script>

<style>
.border.rounded {
    transition: all 0.2s ease;
}
.border.rounded:hover {
    cursor: pointer;
}
</style>
@endsection
