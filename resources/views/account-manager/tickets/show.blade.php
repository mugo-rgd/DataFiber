@extends('layouts.app')

@section('title', $ticket->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Support Ticket Details</h1>
                <div class="btn-group">
                    <a href="{{ route('account-manager.tickets.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Tickets
                    </a>
                    <a href="{{ route('account-manager.tickets.create') }}?customer_id={{ $ticket->customer_id }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Ticket for {{ $ticket->customer->name }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Ticket Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Ticket Information</h6>
                    <div>
                        @if($ticket->isOverdue())
                        <span class="badge badge-danger mr-2">Overdue</span>
                        @endif
                        <span class="badge {{ $ticket->getPriorityBadgeClass() }} mr-2">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                        <span class="badge {{ $ticket->getStatusBadgeClass() }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="text-gray-800">{{ $ticket->title }}</h4>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Customer:</strong> {{ $ticket->customer->name }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $ticket->customer->email }}</p>
                            @if($ticket->customer->phone)
                            <p class="mb-1"><strong>Phone:</strong> {{ $ticket->customer->phone }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Type:</strong> {{ ucfirst($ticket->type) }}</p>
                            <p class="mb-1"><strong>Created:</strong> {{ $ticket->created_at->format('M d, Y g:i A') }}</p>
                            @if($ticket->due_date)
                            <p class="mb-1 {{ $ticket->isOverdue() ? 'text-danger font-weight-bold' : '' }}">
                                <strong>Due Date:</strong> {{ $ticket->due_date->format('M d, Y g:i A') }}
                            </p>
                            @endif
                            @if($ticket->resolved_at)
                            <p class="mb-1 text-success">
                                <strong>Resolved:</strong> {{ $ticket->resolved_at->format('M d, Y g:i A') }}
                            </p>
                            @endif
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <h6 class="font-weight-bold">Description</h6>
                        <p class="text-gray-800" style="white-space: pre-wrap;">{{ $ticket->description }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions & Status -->
        <div class="col-lg-4">
            <!-- Status Update -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Status</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('account-manager.tickets.update-status', $ticket) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('account-manager.customers.show', $ticket->customer) }}" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-user"></i> View Customer Profile
                        </a>
                        <a href="{{ route('account-manager.payments.create') }}?customer_id={{ $ticket->customer_id }}" class="btn btn-outline-success btn-block">
                            <i class="fas fa-money-bill-wave"></i> Create Payment Followup
                        </a>
                        <a href="{{ route('account-manager.tickets.create') }}?customer_id={{ $ticket->customer_id }}" class="btn btn-outline-warning btn-block">
                            <i class="fas fa-plus"></i> New Ticket for Customer
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ticket Statistics -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ticket Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <div class="text-xs font-weight-bold text-primary text-uppercase">Age</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $ticket->created_at->diffInDays(now()) }} days
                            </div>
                        </div>
                        @if($ticket->due_date)
                        <div class="mb-3">
                            <div class="text-xs font-weight-bold text-warning text-uppercase">Days Until Due</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 {{ $ticket->isOverdue() ? 'text-danger' : '' }}">
                                {{ max(0, now()->diffInDays($ticket->due_date, false)) }} days
                            </div>
                        </div>
                        @endif
                        <div>
                            <div class="text-xs font-weight-bold text-info text-uppercase">Last Updated</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $ticket->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
