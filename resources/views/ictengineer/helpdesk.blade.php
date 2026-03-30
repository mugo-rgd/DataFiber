{{-- resources/views/ictengineer/helpdesk.blade.php --}}
@extends('layouts.app')

@section('title', 'Helpdesk Tickets')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-headset text-primary"></i> Helpdesk Tickets
                </h1>
                <a href="{{ route('ictengineer.requests.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Requests
                </a>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('ictengineer.requests.index') }}">Design Requests</a></li>
                    <li class="breadcrumb-item active">Helpdesk</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Open Tickets</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $openTickets ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
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
                                High Priority</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $highPriorityTickets ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                Total Tickets</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tickets->total() ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Response Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">85%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Tickets</h5>
                    <a href="#" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> New Ticket
                    </a>
                </div>
                <div class="card-body">
                    @if($tickets && $tickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Customer</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Last Update</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                    <tr>
                                        <td>
                                            <strong>#{{ $ticket->id }}</strong>
                                        </td>
                                        <td>{{ Str::limit($ticket->title, 50) }}</td>
                                        <td>{{ $ticket->customer->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ match($ticket->priority) {
                                                'low' => 'success',
                                                'medium' => 'warning',
                                                'high' => 'danger',
                                                'urgent' => 'dark',
                                                default => 'secondary'
                                            } }}">
                                                {{ ucfirst($ticket->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ match($ticket->status) {
                                                'open' => 'primary',
                                                'pending' => 'warning',
                                                'resolved' => 'success',
                                                'closed' => 'secondary',
                                                default => 'secondary'
                                            } }}">
                                                {{ ucfirst($ticket->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($ticket->created_at)
                                                {{ $ticket->created_at->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $ticket->updated_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $tickets->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-headset fa-3x text-muted mb-3"></i>
                            <h4>No Helpdesk Tickets</h4>
                            <p class="text-muted">You don't have any helpdesk tickets assigned to you.</p>
                            <div class="mt-3">
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create New Ticket
                                </a>
                                <a href="{{ route('ictengineer.requests.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-list"></i> View Design Requests
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-plus text-primary"></i> Create New Ticket
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-filter text-warning"></i> Filter by Priority
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-download text-success"></i> Export Tickets
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-cog text-secondary"></i> Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Ticket Guidelines</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Response Time:</strong> Acknowledge tickets within 2 hours
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Priority:</strong> High priority tickets must be addressed within 4 hours
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Updates:</strong> Provide regular updates to customers
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Closure:</strong> Ensure customer satisfaction before closing tickets
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Documentation:</strong> Document all troubleshooting steps
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any JavaScript needed for helpdesk page
        console.log('Helpdesk page loaded');
    });
</script>
@endsection
