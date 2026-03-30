@extends('layouts.app')

@section('title', 'Work Orders Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>Work Orders Management
                    </h5>
                    @can('assign-work-orders')
                        <a href="{{ route('maintenance.work-orders.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Create Work Order
                        </a>
                    @endcan
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('maintenance.work-orders.index') }}" class="row g-2">
                                <div class="col-md-2">
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Status</option>
                                        <option value="assigned" {{ $status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                        <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="priority" class="form-select" onchange="this.form.submit()">
                                        <option value="all" {{ $priority == 'all' ? 'selected' : '' }}>All Priority</option>
                                        <option value="low" {{ $priority == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ $priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ $priority == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="critical" {{ $priority == 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="technician" class="form-select" onchange="this.form.submit()">
                                        <option value="all" {{ $technician == 'all' ? 'selected' : '' }}>All Technicians</option>
                                        @foreach($technicians as $tech)
                                            <option value="{{ $tech->id }}" {{ $technician == $tech->id ? 'selected' : '' }}>
                                                {{ $tech->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Search work orders..." value="{{ request('search') }}">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <a href="{{ route('maintenance.work-orders.index') }}" class="btn btn-outline-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Work Orders Table -->
                    @if($workOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Work Order #</th>
                                        <th>Maintenance Request</th>
                                        <th>Equipment</th>
                                        <th>Assigned Technician</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Assigned Date</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($workOrders as $workOrder)
                                        <tr>
                                            <td>
                                                <strong>WO-{{ $workOrder->id }}</strong>
                                            </td>
                                            <td>
                                                @if($workOrder->maintenanceRequest)
                                                    <strong>{{ $workOrder->maintenanceRequest->title }}</strong>
                                                    @if($workOrder->maintenanceRequest->designRequest && $workOrder->maintenanceRequest->designRequest->customer)
                                                        <br>
                                                        <small class="text-muted">
                                                            Customer: {{ $workOrder->maintenanceRequest->designRequest->customer->name }}
                                                        </small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No Request</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($workOrder->maintenanceRequest && $workOrder->maintenanceRequest->equipment)
                                                    {{ $workOrder->maintenanceRequest->equipment->name }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($workOrder->technician)
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-user me-2 text-muted"></i>
                                                        {{ $workOrder->technician->name }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{
                                                    $workOrder->status == 'completed' ? 'success' :
                                                    ($workOrder->status == 'in_progress' ? 'primary' :
                                                    ($workOrder->status == 'assigned' ? 'warning' : 'secondary'))
                                                }}">
                                                    {{ ucfirst($workOrder->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($workOrder->maintenanceRequest)
                                                    <span class="badge bg-{{
                                                        $workOrder->maintenanceRequest->priority == 'critical' ? 'danger' :
                                                        ($workOrder->maintenanceRequest->priority == 'high' ? 'warning' :
                                                        ($workOrder->maintenanceRequest->priority == 'medium' ? 'info' : 'success'))
                                                    }}">
                                                        {{ ucfirst($workOrder->maintenanceRequest->priority) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $workOrder->created_at->format('M j, Y') }}
                                            </td>
                                            <td>
                                                @if($workOrder->due_date)
                                                    @if($workOrder->due_date->isPast() && $workOrder->status != 'completed')
                                                        <span class="badge bg-danger">Overdue</span>
                                                    @endif
                                                    {{ $workOrder->due_date->format('M j, Y') }}
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('maintenance.work-orders.show', $workOrder->id) }}"
                                                       class="btn btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @can('assign-work-orders')
                                                        <a href="{{ route('maintenance.work-orders.edit', $workOrder->id) }}"
                                                           class="btn btn-outline-secondary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @if($workOrder->status == 'assigned' || $workOrder->status == 'in_progress')
                                                        <form action="{{ route('maintenance.work-orders.complete', $workOrder->id) }}"
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-success" title="Complete"
                                                                    onclick="return confirm('Mark this work order as completed?')">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
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
                                Showing {{ $workOrders->firstItem() }} to {{ $workOrders->lastItem() }} of {{ $workOrders->total() }} results
                            </div>
                            {{ $workOrders->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5>No Work Orders Found</h5>
                            <p class="text-muted">
                                @if($status !== 'all' || $priority !== 'all' || $technician !== 'all' || request('search'))
                                    Try adjusting your filters or search terms.
                                @else
                                    No work orders have been created yet.
                                @endif
                            </p>
                            @can('assign-work-orders')
                                <a href="{{ route('maintenance.work-orders.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Create First Work Order
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
