{{-- resources/views/technician/work-orders.blade.php --}}
@extends('layouts.app')

@section('title', 'My Work Orders - Technician')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-list me-2"></i>My Work Orders
                    </h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?status=all">All</a></li>
                            <li><a class="dropdown-item" href="?status=assigned">Assigned</a></li>
                            <li><a class="dropdown-item" href="?status=in_progress">In Progress</a></li>
                            <li><a class="dropdown-item" href="?status=completed">Completed</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    @if($workOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Work Order #</th>
                                        <th>Maintenance Request</th>
                                        <th>Equipment</th>
                                        <th>Priority</th>
                                        <th>Scheduled Start</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($workOrders as $workOrder)
                                        <tr>
                                            <td>
                                                <strong>#{{ $workOrder->id }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $workOrder->maintenanceRequest->title ?? 'N/A' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($workOrder->maintenanceRequest->description ?? '', 50) }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($workOrder->maintenanceRequest->equipment)
                                                    {{ $workOrder->maintenanceRequest->equipment->name }}
                                                    <br>
                                                    <small class="text-muted">{{ $workOrder->maintenanceRequest->equipment->model }}</small>
                                                @else
                                                    <span class="text-muted">No equipment</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $priorityColors = [
                                                        'low' => 'secondary',
                                                        'medium' => 'warning',
                                                        'high' => 'danger',
                                                        'critical' => 'dark'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $priorityColors[$workOrder->maintenanceRequest->priority] ?? 'secondary' }}">
                                                    {{ ucfirst($workOrder->maintenanceRequest->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($workOrder->scheduled_start)
                                                    {{ $workOrder->scheduled_start->format('M j, Y H:i') }}
                                                    <br>
                                                    <small class="text-muted">{{ $workOrder->scheduled_start->diffForHumans() }}</small>
                                                @else
                                                    <span class="text-muted">Not scheduled</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'assigned' => 'warning',
                                                        'in_progress' => 'primary',
                                                        'completed' => 'success',
                                                        'cancelled' => 'secondary'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$workOrder->status] ?? 'secondary' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $workOrder->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('technician.work-order.show', $workOrder->id) }}"
                                                       class="btn btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($workOrder->status === 'assigned')
                                                        <button type="button" class="btn btn-outline-success"
                                                                onclick="updateStatus({{ $workOrder->id }}, 'in_progress')"
                                                                title="Start Work">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    @elseif($workOrder->status === 'in_progress')
                                                        <button type="button" class="btn btn-outline-success"
                                                                onclick="updateStatus({{ $workOrder->id }}, 'completed')"
                                                                title="Mark Complete">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($workOrders->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $workOrders->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                            <h4>No Work Orders Found</h4>
                            <p class="text-muted">You don't have any work orders assigned to you at the moment.</p>
                            <a href="{{ route('technician.dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Work Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="status_notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="status_notes" name="notes" rows="3"
                                  placeholder="Add any notes about the status change..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitStatusUpdate()">Update Status</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentWorkOrderId = null;
let targetStatus = null;

function updateStatus(workOrderId, status) {
    currentWorkOrderId = workOrderId;
    targetStatus = status;

    const statusText = status === 'in_progress' ? 'Start Work' : 'Mark Complete';
    document.querySelector('.modal-title').textContent = `${statusText} - Work Order #${workOrderId}`;

    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

function submitStatusUpdate() {
    if (!currentWorkOrderId || !targetStatus) return;

    const form = document.getElementById('statusForm');
    form.action = `/technician/work-orders/${currentWorkOrderId}/status`;

    // Add status to form data
    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = targetStatus;
    form.appendChild(statusInput);

    form.submit();
}

// Filter work orders by status
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');

    if (status && status !== 'all') {
        document.querySelectorAll('tr').forEach(row => {
            const statusCell = row.querySelector('td:nth-child(6)');
            if (statusCell) {
                const rowStatus = statusCell.textContent.trim().toLowerCase().replace(' ', '_');
                if (rowStatus !== status) {
                    row.style.display = 'none';
                }
            }
        });
    }
});
</script>
@endsection
