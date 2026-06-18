@extends('layouts.app')

@section('title', 'Maintenance Request Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mt-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('maintenance.dashboard') }}">Maintenance</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('maintenance.requests.index') }}">Requests</a></li>
                    <li class="breadcrumb-item active">{{ $maintenanceRequest->request_number }}</li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-tools text-primary me-2"></i>
                        Maintenance Request #{{ $maintenanceRequest->request_number }}
                    </h1>
                    <p class="text-muted mb-0">Created {{ $maintenanceRequest->created_at->diffForHumans() }}</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('maintenance.requests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                    @if(isset($maintenanceRequest->can_be_edited) && $maintenanceRequest->can_be_edited)
                    <a href="{{ route('maintenance.requests.edit', $maintenanceRequest->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    @endif
                    @if($maintenanceRequest->status == 'open')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createWorkOrderModal">
                        <i class="fas fa-plus-circle me-2"></i>Create Work Order
                    </button>
                    @endif
                </div>
            </div>

            <div class="row">
                <!-- Main Details -->
                <div class="col-md-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Request Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <small class="text-muted">Title</small>
                                    <p class="mb-0 fw-bold">{{ $maintenanceRequest->title }}</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Request Number</small>
                                    <p class="mb-0">{{ $maintenanceRequest->request_number }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <small class="text-muted">Priority</small>
                                    <p class="mb-0">
                                        @php
                                            $priorityBadgeClass = match($maintenanceRequest->priority) {
                                                'critical' => 'danger',
                                                'high' => 'warning',
                                                'medium' => 'info',
                                                'low' => 'secondary',
                                                default => 'light'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $priorityBadgeClass }}">
                                            {{ ucfirst($maintenanceRequest->priority) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Status</small>
                                    <p class="mb-0">
                                        @php
                                            $statusBadgeClass = match($maintenanceRequest->status) {
                                                'open' => 'danger',
                                                'assigned' => 'warning',
                                                'in_progress' => 'info',
                                                'resolved' => 'success',
                                                'closed' => 'secondary',
                                                default => 'light'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusBadgeClass }}">
                                            {{ ucfirst($maintenanceRequest->status) }}
                                        </span>
                                        @php
                                            $isOverdue = false;
                                            if (!in_array($maintenanceRequest->status, ['resolved', 'closed'])) {
                                                $hoursLimit = match($maintenanceRequest->priority) {
                                                    'critical' => 24,
                                                    'high' => 48,
                                                    'medium' => 72,
                                                    'low' => 120,
                                                    default => 72
                                                };
                                                $isOverdue = $maintenanceRequest->created_at->diffInHours(now()) > $hoursLimit;
                                            }
                                        @endphp
                                        @if($isOverdue)
                                            <span class="badge bg-danger ms-2">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Overdue
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <small class="text-muted">Issue Type</small>
                                    <p class="mb-0">
                                        @php
                                            $issueTypeIcon = match($maintenanceRequest->issue_type) {
                                                'fibre_cut' => 'cut',
                                                'equipment_failure' => 'microchip',
                                                'signal_degradation' => 'signal',
                                                'power_issue' => 'bolt',
                                                'environmental' => 'tree',
                                                'preventive_maintenance' => 'tools',
                                                default => 'wrench'
                                            };
                                        @endphp
                                        <i class="fas fa-{{ $issueTypeIcon }} me-1"></i>
                                        {{ str_replace('_', ' ', ucfirst($maintenanceRequest->issue_type)) }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Reported By</small>
                                    <p class="mb-0">
                                        {{ $maintenanceRequest->reporter->name ?? 'N/A' }}
                                        <br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($maintenanceRequest->reported_at)->format('M d, Y H:i') }}</small>
                                    </p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Description</small>
                                <p class="mb-0">{{ $maintenanceRequest->description }}</p>
                            </div>

                            @if($maintenanceRequest->resolution_notes)
                            <div class="mb-3">
                                <small class="text-muted">Resolution Notes</small>
                                <p class="mb-0">{{ $maintenanceRequest->resolution_notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Location Details -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-map-marker-alt me-2"></i>Location Details
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($maintenanceRequest->location)
                            <div class="mb-3">
                                <small class="text-muted">Specific Location</small>
                                <p class="mb-0">{{ $maintenanceRequest->location }}</p>
                            </div>
                            @endif

                            @if($maintenanceRequest->latitude && $maintenanceRequest->longitude)
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Latitude</small>
                                    <p class="mb-0">{{ $maintenanceRequest->latitude }}</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Longitude</small>
                                    <p class="mb-0">{{ $maintenanceRequest->longitude }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Commercial Route Details -->
                            @if($maintenanceRequest->commercialRoute)
                            <div class="mt-3 pt-3 border-top">
                                <small class="text-muted">Commercial Route</small>
                                <p class="mb-0">
                                    <strong>{{ $maintenanceRequest->commercialRoute->name_of_route }}</strong><br>
                                    Region: {{ $maintenanceRequest->commercialRoute->region }}<br>
                                    Option: {{ $maintenanceRequest->commercialRoute->option }}<br>
                                    Distance: {{ number_format($maintenanceRequest->commercialRoute->approx_distance_km, 2) }} km
                                </p>
                            </div>
                            @endif
                            <!-- Customer Details -->
@if($maintenanceRequest->customer)
<div class="mt-3 pt-3 border-top">
    <small class="text-muted">Customer</small>
    <p class="mb-0">
        <strong>{{ $maintenanceRequest->customer->name }}</strong>
        @if($maintenanceRequest->customer->company_name)
            <br><small class="text-muted">{{ $maintenanceRequest->customer->company_name }}</small>
        @endif
        <br><small class="text-muted">{{ $maintenanceRequest->customer->email }}</small>
    </p>
</div>
@endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <!-- Metrics Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-line me-2"></i>Metrics
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">Age</small>
                                <p class="mb-0 fw-bold">{{ $maintenanceRequest->created_at->diffInDays(now()) }} days</p>
                            </div>
                            @if($maintenanceRequest->resolved_at)
                            <div class="mb-3">
                                <small class="text-muted">Resolution Time</small>
                                @php
                                    $hours = \Carbon\Carbon::parse($maintenanceRequest->reported_at)->diffInHours(\Carbon\Carbon::parse($maintenanceRequest->resolved_at));
                                    if ($hours < 24) {
                                        $resolutionTime = $hours . ' hours';
                                    } else {
                                        $days = floor($hours / 24);
                                        $remainingHours = $hours % 24;
                                        $resolutionTime = $days . ' days' . ($remainingHours > 0 ? ', ' . $remainingHours . ' hours' : '');
                                    }
                                @endphp
                                <p class="mb-0">{{ $resolutionTime }}</p>
                            </div>
                            @endif
                            @if($maintenanceRequest->downtime_minutes > 0)
                            <div class="mb-3">
                                <small class="text-muted">Downtime</small>
                                <p class="mb-0">{{ floor($maintenanceRequest->downtime_minutes / 60) }} hours {{ $maintenanceRequest->downtime_minutes % 60 }} minutes</p>
                            </div>
                            @endif
                            @if($maintenanceRequest->repair_cost > 0)
                            <div class="mb-3">
                                <small class="text-muted">Repair Cost</small>
                                <p class="mb-0 fw-bold text-danger">
                                    @if($maintenanceRequest->currency ?? false)
                                        {{ $maintenanceRequest->currency }}
                                    @else
                                        $
                                    @endif
                                    {{ number_format($maintenanceRequest->repair_cost, 2) }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Work Orders -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-clipboard-list me-2"></i>Work Orders
                                <span class="badge bg-secondary ms-2">{{ $maintenanceRequest->workOrders->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            @if($maintenanceRequest->workOrders->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($maintenanceRequest->workOrders as $workOrder)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">
                                                    <a href="{{ route('maintenance.work-orders.show', $workOrder->id) }}" class="text-decoration-none">
                                                        Work Order #{{ $workOrder->id }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">
                                                    Assigned to: {{ $workOrder->technician->name ?? 'N/A' }}
                                                </small>
                                            </div>
                                            @php
                                                $woStatusClass = match($workOrder->status) {
                                                    'completed' => 'success',
                                                    'in_progress' => 'warning',
                                                    'assigned' => 'info',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $woStatusClass }}">
                                                {{ ucfirst($workOrder->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                    <p class="mb-0">No work orders created yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Work Order Modal -->
<div class="modal fade" id="createWorkOrderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Create Work Order
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('maintenance.work-orders.store') }}" method="POST">
                @csrf
                <input type="hidden" name="maintenance_request_id" value="{{ $maintenanceRequest->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Assign to Technician *</label>
                        <select class="form-select" name="assigned_technician" required>
                            <option value="">Select Technician</option>
                            @foreach($technicians ?? [] as $technician)
                                <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="datetime-local" class="form-control" name="due_date">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instructions</label>
                        <textarea class="form-control" name="instructions" rows="3" placeholder="Provide instructions for the technician..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estimated Hours</label>
                        <input type="number" step="0.5" class="form-control" name="estimated_hours" placeholder="2.5">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Work Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}
.list-group-item:hover {
    background-color: #f8f9fa;
}
</style>
@endsection
