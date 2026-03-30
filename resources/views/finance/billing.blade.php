@extends('layouts.app')

@section('title', 'Billing Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Billing Management
                    </h5>

                    <div class="row mb-3">
    <div class="col-md-12">
        <x-back-button
            :url="URL::previous()"
            text="Back to Previous"
        />
    </div>
</div>
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Print
                        </button>
                        <a href="{{ route('finance.billing.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Create Invoice
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Billing Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center p-3">
                                    <h6 class="card-title">Total Invoices</h6>
                                    <h3 class="mb-0">{{ $billingStats['total'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center p-3">
                                    <h6 class="card-title">Pending</h6>
                                    <h3 class="mb-0">{{ $billingStats['pending'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center p-3">
                                    <h6 class="card-title">Paid</h6>
                                    <h3 class="mb-0">{{ $billingStats['paid'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center p-3">
                                    <h6 class="card-title">Overdue</h6>
                                    <h3 class="mb-0">{{ $billingStats['overdue'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center p-3">
                                    <h6 class="card-title">Total Revenue</h6>
                                    <h5 class="mb-0">${{ number_format($billingStats['total_amount'], 2) }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center p-3">
                                    <h6 class="card-title">Pending Amount</h6>
                                    <h5 class="mb-0">${{ number_format($billingStats['pending_amount'], 2) }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('finance.billing') }}" class="row g-2">
                                <div class="col-md-2">
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Status</option>
                                        <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="overdue" {{ $status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                        <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="type" class="form-select" onchange="this.form.submit()">
                                        <option value="all" {{ $type == 'all' ? 'selected' : '' }}>All Types</option>
                                        <option value="lease" {{ $type == 'lease' ? 'selected' : '' }}>Lease</option>
                                        <option value="maintenance" {{ $type == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="installation" {{ $type == 'installation' ? 'selected' : '' }}>Installation</option>
                                        <option value="consultation" {{ $type == 'consultation' ? 'selected' : '' }}>Consultation</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="date_range" class="form-select" onchange="this.form.submit()">
                                        <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Today</option>
                                        <option value="this_week" {{ $dateRange == 'this_week' ? 'selected' : '' }}>This Week</option>
                                        <option value="this_month" {{ $dateRange == 'this_month' ? 'selected' : '' }}>This Month</option>
                                        <option value="last_month" {{ $dateRange == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                        <option value="this_quarter" {{ $dateRange == 'this_quarter' ? 'selected' : '' }}>This Quarter</option>
                                        <option value="this_year" {{ $dateRange == 'this_year' ? 'selected' : '' }}>This Year</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Search invoices, customers..." value="{{ request('search') }}">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('finance.billing') }}" class="btn btn-outline-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Billing Table -->
                    @if($billings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Customer</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Billing Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Lease/Reference</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($billings as $billing)
                                        <tr>
                                            <td>
                                                <strong>{{ $billing->invoice_number }}</strong>
                                                @if($billing->billing_type == 'lease' && $billing->lease)
                                                    <br>
                                                    <small class="text-muted">Lease: {{ $billing->lease->lease_number }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($billing->customer)
                                                    <strong>{{ $billing->customer->name }}</strong>
                                                    @if($billing->customer->company_name)
                                                        <br>
                                                        <small class="text-muted">{{ $billing->customer->company_name }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No Customer</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info text-capitalize">
                                                    {{ $billing->billing_type }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>${{ number_format($billing->total_amount ?? $billing->amount ?? 0, 2) }}</strong>
                                            </td>
                                            <td>
                                                @if($billing->invoice_date)
                                                    {{ $billing->invoice_date->format('M j, Y') }}
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($billing->due_date)
                                                    @if($billing->due_date->isPast() && $billing->status != 'paid')
                                                        <span class="badge bg-danger me-1">Overdue</span>
                                                    @endif
                                                    {{ $billing->due_date->format('M j, Y') }}
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>
                                            <td>
                                                <!-- ENHANCED STATUS DISPLAY WITH QUICK ACTIONS -->
                                                <div class="dropdown">
                                                    <button class="btn btn-sm dropdown-toggle
                                                        @if($billing->status == 'paid') btn-success
                                                        @elseif($billing->status == 'overdue') btn-danger
                                                        @elseif($billing->status == 'pending') btn-warning
                                                        @elseif($billing->status == 'draft') btn-secondary
                                                        @else btn-dark
                                                        @endif"
                                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        {{ ucfirst($billing->status) }}
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @if($billing->status != 'pending')
                                                        <li>
                                                            <form action="{{ route('finance.billing.mark-pending', $billing->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-clock text-warning"></i> Mark as Pending
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endif

                                                        @if($billing->status != 'paid')
                                                        <li>
                                                            <form action="{{ route('finance.billing.mark-paid', $billing->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-check text-success"></i> Mark as Paid
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endif

                                                        @if($billing->status != 'overdue')
                                                        <li>
                                                            <form action="{{ route('finance.billing.mark-overdue', $billing->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-exclamation-triangle text-danger"></i> Mark as Overdue
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endif

                                                        @if($billing->status != 'cancelled')
                                                        <li>
                                                            <form action="{{ route('finance.billing.update-status', $billing->id) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="status" value="cancelled">
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-times text-secondary"></i> Mark as Cancelled
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endif

                                                        @if($billing->status != 'draft')
                                                        <li>
                                                            <form action="{{ route('finance.billing.update-status', $billing->id) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="status" value="draft">
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-file text-muted"></i> Mark as Draft
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                @if($billing->billing_type == 'lease' && $billing->lease)
                                                    {{ $billing->lease->lease_number }}
                                                @elseif($billing->reference_number)
                                                    {{ $billing->reference_number }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('finance.billing.show', $billing->id) }}"
                                                       class="btn btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('finance.billing.edit', $billing->id) }}"
                                                       class="btn btn-outline-secondary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('finance.billing.download', $billing->id) }}"
                                                       class="btn btn-outline-info" title="Download PDF">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <form action="{{ route('finance.billing.delete', $billing->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Delete"
                                                                onclick="return confirm('Are you sure you want to delete this invoice?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $billings->firstItem() }} to {{ $billings->lastItem() }} of {{ $billings->total() }} results
                            </div>
                            {{ $billings->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                            <h5>No Billing Records Found</h5>
                            <p class="text-muted">
                                @if($status !== 'all' || $type !== 'all' || $dateRange !== 'this_month' || request('search'))
                                    Try adjusting your filters or search terms.
                                @else
                                    No billing records have been created yet.
                                @endif
                            </p>
                            <a href="{{ route('finance.billing.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Create First Invoice
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
