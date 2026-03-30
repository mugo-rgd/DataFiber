@php
use App\Models\LeaseBilling;
@endphp

@extends('layouts.app')

@section('title', 'Manage Lease Billings')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Manage Lease Billings
                </h1>
                <div class="btn-group">
                    <a href="{{ route('finance.billing.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Billing
                    </a>
                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Billings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $billings->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Paid Billings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ LeaseBilling::where('status', 'paid')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Billings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ LeaseBilling::where('status', 'pending')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Overdue Billings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ LeaseBilling::where('status', 'overdue')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Billings Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>All Lease Billings
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="120">Billing #</th>
                            <th>Customer</th>
                            <th>Lease</th>
                            <th width="120" class="text-end">Amount</th>
                            <th width="100">Billing Date</th>
                            <th width="100">Due Date</th>
                            <th width="120">Billing Cycle</th>
                            <th width="100">Status</th>
                            <th width="150" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($billings as $billing)
                        <tr>
                            <td>
                                <strong>{{ $billing->billing_number }}</strong>
                            </td>
                            <td>
                                @if($billing->customer)
                                <div>
                                    <strong>{{ $billing->customer->name }}</strong>
                                    @if($billing->customer->company)
                                    <br><small class="text-muted">{{ $billing->customer->company }}</small>
                                    @endif
                                </div>
                                @else
                                <span class="text-muted">No Customer</span>
                                @endif
                            </td>
                            <td>
                                @if($billing->lease)
                                <span class="badge bg-light text-dark">{{ $billing->lease->title }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <strong>{{ $billing->currency }} {{ number_format($billing->total_amount, 2) }}</strong>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($billing->billing_date)->format('M j, Y') }}</td>
                            <td>
                                <span class="{{ $billing->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                    {{ \Carbon\Carbon::parse($billing->due_date)->format('M j, Y') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ ucfirst(str_replace('_', ' ', $billing->billing_cycle)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $billing->status === 'paid' ? 'success' : ($billing->status === 'overdue' ? 'danger' : ($billing->status === 'draft' ? 'secondary' : 'warning')) }}">
                                    {{ ucfirst($billing->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('finance.billing.show', $billing->id) }}"
                                       class="btn btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('finance.billing.download', $billing->id) }}"
                                       class="btn btn-outline-secondary" title="Download PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-success dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-expanded="false" title="More Actions">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if($billing->status !== 'paid')
                                        <li>
                                            <a class="dropdown-item text-success" href="#"
                                               onclick="markAsPaid({{ $billing->id }})">
                                                <i class="fas fa-check me-2"></i>Mark as Paid
                                            </a>
                                        </li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item" href="#"
                                               onclick="sendReminder({{ $billing->id }})">
                                                <i class="fas fa-envelope me-2"></i>Send Reminder
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-info"
                                               href="{{ route('finance.billing.edit', $billing->id) }}">
                                                <i class="fas fa-edit me-2"></i>Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-warning" href="#">
                                                <i class="fas fa-copy me-2"></i>Duplicate
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#"
                                               onclick="deleteBilling({{ $billing->id }})">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No lease billings found</h5>
                                <p class="text-muted">Get started by creating your first lease billing.</p>
                                <a href="{{ route('finance.billing.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Create Billing
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing {{ $billings->firstItem() ?? 0 }} to {{ $billings->lastItem() ?? 0 }} of {{ $billings->total() }} entries
                </div>
                {{ $billings->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Lease Billings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm" method="GET">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="draft">Draft</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="billing_cycle" class="form-label">Billing Cycle</label>
                        <select class="form-select" id="billing_cycle" name="billing_cycle">
                            <option value="">All Cycles</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="annually">Annually</option>
                            <option value="one_time">One Time</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from">
                    </div>
                    <div class="mb-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function markAsPaid(billingId) {
    if (confirm('Are you sure you want to mark this billing as paid?')) {
        fetch(`/finance/billing/${billingId}/mark-paid`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while marking the billing as paid.');
        });
    }
}

function sendReminder(billingId) {
    if (confirm('Send payment reminder to customer?')) {
        fetch(`/finance/billing/${billingId}/send-reminder`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Reminder sent successfully!');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while sending the reminder.');
        });
    }
}

function deleteBilling(billingId) {
    if (confirm('Are you sure you want to delete this billing? This action cannot be undone.')) {
        fetch(`/finance/billing/${billingId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the billing.');
        });
    }
}

function applyFilters() {
    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form));
    window.location.href = '{{ route("finance.billing.index") }}?' + params.toString();
}
</script>
@endsection

@section('styles')
<style>
.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}
.table td {
    vertical-align: middle;
}
.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
.border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
</style>
@endsection
