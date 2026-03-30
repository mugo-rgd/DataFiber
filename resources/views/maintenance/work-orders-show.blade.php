@extends('layouts.app')

@section('title', 'Work Order #WO-' . $workOrder->id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Work Order Details -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>Work Order #WO-{{ $workOrder->id }}
                    </h5>
                    <div class="btn-group">
                        <a href="{{ route('maintenance.work-orders.edit', $workOrder->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('maintenance.work-orders.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Status:</th>
                                    <td>
                                        <span class="badge bg-{{
                                            $workOrder->status == 'completed' ? 'success' :
                                            ($workOrder->status == 'in_progress' ? 'primary' :
                                            ($workOrder->status == 'assigned' ? 'warning' : 'secondary'))
                                        }}">
                                            {{ ucfirst($workOrder->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Assigned Technician:</th>
                                    <td>
                                        @if($workOrder->technician)
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user me-2 text-muted"></i>
                                                {{ $workOrder->technician->name }}
                                                @if($workOrder->technician->employee_id)
                                                    <span class="badge bg-info ms-2">{{ $workOrder->technician->employee_id }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Due Date:</th>
                                    <td>
                                        @if($workOrder->due_date)
                                            @if($workOrder->due_date->isPast() && $workOrder->status != 'completed')
                                                <span class="badge bg-danger me-2">Overdue</span>
                                            @endif
                                            {{ $workOrder->due_date->format('M j, Y g:i A') }}
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Estimated Hours:</th>
                                    <td>
                                        {{ $workOrder->estimated_hours ?? 'Not specified' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Created:</th>
                                    <td>{{ $workOrder->created_at->format('M j, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Assigned By:</th>
                                    <td>
                                        @if($workOrder->assignedBy)
                                            {{ $workOrder->assignedBy->name }}
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $workOrder->updated_at->format('M j, Y g:i A') }}</td>
                                </tr>
                                @if($workOrder->status == 'completed')
                                <tr>
                                    <th>Completed At:</th>
                                    <td>
                                        @if($workOrder->completed_at)
                                            {{ $workOrder->completed_at->format('M j, Y g:i A') }}
                                        @else
                                            <span class="text-muted">Not recorded</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Actual Hours:</th>
                                    <td>
                                        {{ $workOrder->actual_hours ?? 'Not recorded' }}
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Request Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-tools me-2"></i>Maintenance Request Details
                    </h6>
                </div>
                <div class="card-body">
                    @if($workOrder->maintenanceRequest)
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">Request Title:</th>
                                        <td>{{ $workOrder->maintenanceRequest->title }}</td>
                                    </tr>
                                    <tr>
                                        <th>Priority:</th>
                                        <td>
                                            <span class="badge bg-{{
                                                $workOrder->maintenanceRequest->priority == 'critical' ? 'danger' :
                                                ($workOrder->maintenanceRequest->priority == 'high' ? 'warning' :
                                                ($workOrder->maintenanceRequest->priority == 'medium' ? 'info' : 'success'))
                                            }}">
                                                {{ ucfirst($workOrder->maintenanceRequest->priority) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Maintenance Type:</th>
                                        <td>{{ ucfirst($workOrder->maintenanceRequest->maintenance_type) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">Equipment:</th>
                                        <td>
                                            @if($workOrder->maintenanceRequest->equipment)
                                                {{ $workOrder->maintenanceRequest->equipment->name }}
                                                @if($workOrder->maintenanceRequest->equipment->model)
                                                    <br><small class="text-muted">Model: {{ $workOrder->maintenanceRequest->equipment->model }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">No equipment specified</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Customer:</th>
                                        <td>
                                            @if($workOrder->maintenanceRequest->designRequest && $workOrder->maintenanceRequest->designRequest->customer)
                                                {{ $workOrder->maintenanceRequest->designRequest->customer->name }}
                                                @if($workOrder->maintenanceRequest->designRequest->customer->company_name)
                                                    <br><small class="text-muted">{{ $workOrder->maintenanceRequest->designRequest->customer->company_name }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">No customer specified</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="mt-3">
                            <strong>Description:</strong>
                            <p class="mt-1">{{ $workOrder->maintenanceRequest->description }}</p>
                        </div>

                        @if($workOrder->maintenanceRequest->notes)
                        <div class="mt-3">
                            <strong>Additional Notes:</strong>
                            <p class="mt-1">{{ $workOrder->maintenanceRequest->notes }}</p>
                        </div>
                        @endif
                    @else
                        <p class="text-muted">No maintenance request associated with this work order.</p>
                    @endif
                </div>
            </div>

            <!-- Work Instructions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-list-check me-2"></i>Work Instructions
                    </h6>
                </div>
                <div class="card-body">
                    @if($workOrder->instructions)
                        <p>{{ $workOrder->instructions }}</p>
                    @else
                        <p class="text-muted">No specific instructions provided.</p>
                    @endif
                </div>
            </div>

            <!-- Technician Notes & Completion Details -->
            @if($workOrder->technician_notes || $workOrder->completion_notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-clipboard me-2"></i>Technician Notes & Completion Details
                    </h6>
                </div>
                <div class="card-body">
                    @if($workOrder->technician_notes)
                    <div class="mb-3">
                        <strong>Technician Notes:</strong>
                        <p class="mt-1">{{ $workOrder->technician_notes }}</p>
                    </div>
                    @endif

                    @if($workOrder->completion_notes)
                    <div>
                        <strong>Completion Notes:</strong>
                        <p class="mt-1">{{ $workOrder->completion_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar - Quick Actions -->
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($workOrder->status == 'assigned')
                            <form action="{{ route('maintenance.work-orders.update-status', $workOrder->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="in_progress">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-play me-1"></i>Start Work
                                </button>
                            </form>
                        @endif

                        @if($workOrder->status == 'in_progress')
                            <form action="{{ route('maintenance.work-orders.update-status', $workOrder->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-success w-100 mb-2">
                                    <i class="fas fa-check me-1"></i>Mark Complete
                                </button>
                            </form>
                        @endif

                        @if($workOrder->status != 'completed' && $workOrder->status != 'cancelled')
                            <form action="{{ route('maintenance.work-orders.update-status', $workOrder->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-danger w-100 mb-2"
                                        onclick="return confirm('Are you sure you want to cancel this work order?')">
                                    <i class="fas fa-times me-1"></i>Cancel Work Order
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('maintenance.work-orders.edit', $workOrder->id) }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-edit me-1"></i>Edit Details
                        </a>

                        @if($workOrder->maintenanceRequest)
                            <a href="{{ route('maintenance.requests.show', $workOrder->maintenanceRequest->id) }}"
                               class="btn btn-outline-info w-100 mb-2">
                                <i class="fas fa-external-link-alt me-1"></i>View Request
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Status Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item {{ $workOrder->status == 'assigned' ? 'active' : '' }}">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Assigned</h6>
                                <small class="text-muted">{{ $workOrder->created_at->format('M j, Y g:i A') }}</small>
                            </div>
                        </div>

                        @if($workOrder->status == 'in_progress' || $workOrder->status == 'completed')
                        <div class="timeline-item {{ $workOrder->status == 'in_progress' ? 'active' : '' }}">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">In Progress</h6>
                                <small class="text-muted">
                                    @if($workOrder->updated_at != $workOrder->created_at)
                                        {{ $workOrder->updated_at->format('M j, Y g:i A') }}
                                    @else
                                        Not started
                                    @endif
                                </small>
                            </div>
                        </div>
                        @endif

                        @if($workOrder->status == 'completed')
                        <div class="timeline-item active">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Completed</h6>
                                <small class="text-muted">
                                    @if($workOrder->completed_at)
                                        {{ $workOrder->completed_at->format('M j, Y g:i A') }}
                                    @else
                                        {{ $workOrder->updated_at->format('M j, Y g:i A') }}
                                    @endif
                                </small>
                            </div>
                        </div>
                        @endif

                        @if($workOrder->status == 'cancelled')
                        <div class="timeline-item active">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Cancelled</h6>
                                <small class="text-muted">{{ $workOrder->updated_at->format('M j, Y g:i A') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 20px;
}
.timeline-item {
    position: relative;
    margin-bottom: 20px;
}
.timeline-marker {
    position: absolute;
    left: -20px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}
.timeline-item.active .timeline-marker {
    box-shadow: 0 0 0 3px rgba(var(--bs-primary-rgb), 0.1);
}
.timeline-content {
    padding-bottom: 10px;
}
.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: -15px;
    top: 12px;
    bottom: -10px;
    width: 2px;
    background-color: #e9ecef;
}
</style>
@endsection
