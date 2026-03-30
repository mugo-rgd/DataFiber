@php
    $previousUrl = url()->previous();
    $currentUrl = url()->current();

    // If previous URL is the same as current or empty, use customer show route as fallback
    if ($previousUrl === $currentUrl || empty($previousUrl)) {
        $backUrl = route('account-manager.customers.show', $customer);
    } else {
        $backUrl = $previousUrl;
    }
@endphp

@extends('layouts.app')

@section('title', $customer->name . ' - Customer Details')

@section('content')
<div class="container-fluid">
    <!-- Customer Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Customer Details</h1>
                <div class="btn-group">
                    <a href="{{ route('account-manager.tickets.create') }}?customer_id={{ $customer->id }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Ticket
                    </a>
                    <a href="{{ route('account-manager.payments.create') }}?customer_id={{ $customer->id }}" class="btn btn-success">
                        <i class="fas fa-money-bill-wave"></i> New Payment
                    </a>
                    <a href="{{ route('account-manager.leases.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary ms-2">
                        <i class="fas fa-file-contract"></i> New Lease
                    </a>
                </div>
                <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    @if($backUrl === route('account-manager.customers.index', $customer))
                        Back to Customer
                    @else
                        Back to Previous
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td>{{ $customer->phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Assigned Since:</strong></td>
                            <td>{{ $customer->assigned_at ? $customer->assigned_at->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                        @if($customer->assignment_notes)
                        <tr>
                            <td><strong>Notes:</strong></td>
                            <td>{{ $customer->assignment_notes }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-xl-8 col-md-6 mb-4">
            <div class="row">
                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Open Tickets</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $customer->supportTickets->whereIn('status', ['open', 'in_progress'])->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Pending Payments</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $customer->paymentFollowups->whereIn('status', ['pending', 'reminded'])->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Tickets</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $customer->supportTickets->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Leases
                                    </div>
                                    <div class="h5 mb-2 font-weight-bold text-gray-800">
                                        {{ App\Models\Lease::where('customer_id', $customer->id)->count() }}
                                    </div>
                                    <a href="{{ route('account-manager.leases.index', ['customer_id' => $customer->id]) }}" class="btn btn-outline-success btn-sm">Manage Leases</a>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Tickets -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Support Tickets</h6>
                    <a href="{{ route('account-manager.tickets.create') }}?customer_id={{ $customer->id }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> New Ticket
                    </a>
                </div>
                <div class="card-body">
                    @if($customer->supportTickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Type</th>
                                    <th>Due Date</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->supportTickets as $ticket)
                                <tr>
                                    <td>{{ Str::limit($ticket->title, 50) }}</td>
                                    <td>
                                        <span class="badge {{ $ticket->getPriorityBadgeClass() }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $ticket->getStatusBadgeClass() }}">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ ucfirst($ticket->type) }}</td>
                                    <td>
                                        @if($ticket->due_date)
                                        <span class="{{ $ticket->isOverdue() ? 'text-danger' : '' }}">
                                            {{ $ticket->due_date->format('M d, Y') }}
                                        </span>
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                    <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('account-manager.tickets.show', $ticket) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-ticket-alt fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">No support tickets for this customer.</p>
                        <a href="{{ route('account-manager.tickets.create') }}?customer_id={{ $customer->id }}" class="btn btn-primary">Create First Ticket</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Followups -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Followups</h6>
                    <a href="{{ route('account-manager.payments.create') }}?customer_id={{ $customer->id }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> New Payment
                    </a>
                </div>
                <div class="card-body">
                    @if($customer->paymentFollowups->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->paymentFollowups as $payment)
                                <tr>
                                    <td class="font-weight-bold">${{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        <span class="{{ $payment->isOverdue() ? 'text-danger' : ($payment->isDueSoon() ? 'text-warning' : '') }}">
                                            {{ $payment->due_date->format('M d, Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $payment->getStatusBadgeClass() }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $payment->notes ? Str::limit($payment->notes, 50) : 'N/A' }}</td>
                                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($payment->status === 'pending')
                                        <form action="{{ route('account-manager.payments.remind', $payment) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning" title="Mark as Reminded">
                                                <i class="fas fa-bell"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('account-manager.payments.paid', $payment) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Mark as Paid">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-money-bill-wave fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">No payment followups for this customer.</p>
                        <a href="{{ route('account-manager.payments.create') }}?customer_id={{ $customer->id }}" class="btn btn-primary">Create First Payment</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
