@extends('layouts.app')

@section('title', 'Support Tickets')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Support Tickets</h1>
                <a href="{{ route('account-manager.tickets.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Ticket
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" class="row">
                        <div class="col-md-3">
                            <label>Status</label>
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Priority</label>
                            <select name="priority" class="form-control" onchange="this.form.submit()">
                                <option value="">All Priorities</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Type</label>
                            <select name="type" class="form-control" onchange="this.form.submit()">
                                <option value="">All Types</option>
                                <option value="technical" {{ request('type') == 'technical' ? 'selected' : '' }}>Technical</option>
                                <option value="billing" {{ request('type') == 'billing' ? 'selected' : '' }}>Billing</option>
                                <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>General</option>
                                <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>Payment</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <a href="{{ route('account-manager.tickets.index') }}" class="btn btn-secondary btn-block">Reset Filters</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets List -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    @if($tickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Customer</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Type</th>
                                    <th>Due Date</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
                                <tr>
                                    <td>
                                        <strong>{{ Str::limit($ticket->title, 60) }}</strong>
                                        @if($ticket->isOverdue())
                                        <span class="badge badge-danger ml-1">Overdue</span>
                                        @endif
                                    </td>
                                    <td>{{ $ticket->customer->name }}</td>
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
                                        <span class="{{ $ticket->isOverdue() ? 'text-danger font-weight-bold' : '' }}">
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
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $tickets->firstItem() }} to {{ $tickets->lastItem() }} of {{ $tickets->total() }} entries
                        </div>
                        {{ $tickets->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-ticket-alt fa-3x text-gray-300 mb-3"></i>
                        <h4 class="text-gray-500">No Support Tickets Found</h4>
                        <p class="text-gray-500">You don't have any support tickets matching your criteria.</p>
                        <a href="{{ route('account-manager.tickets.create') }}" class="btn btn-primary">Create Your First Ticket</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
