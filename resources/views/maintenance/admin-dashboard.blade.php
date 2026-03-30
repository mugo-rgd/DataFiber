@extends('layouts.app')

@section('title', 'Maintenance Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Maintenance Admin Dashboard</h1>
        <div class="d-flex">
            <a href="{{ route('maintenance.work-orders.create') }}" class="btn btn-primary mr-2">
                <i class="fas fa-plus-circle mr-2"></i> Create Work Order
            </a>
            <a href="{{ route('maintenance.requests.create') }}" class="btn btn-success">
                <i class="fas fa-plus mr-2"></i> New Request
    </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Request Statistics -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Requests</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_requests'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
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
                                Open Requests</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['open_requests'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                Critical Priority</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['critical_requests'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-fire fa-2x text-gray-300"></i>
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
                                Resolved This Week</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['resolved_this_week'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Statistics -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Work Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_work_orders'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Available Equipment</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['available_equipment'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-toolbox fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Total Repair Cost</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['total_repair_cost'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                Avg Resolution Time</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $hours = floor($stats['avg_resolution_time'] / 60);
                                    $minutes = $stats['avg_resolution_time'] % 60;
                                @endphp
                                {{ $hours }}h {{ $minutes }}m
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Critical Requests & Equipment Status -->
    <div class="row">
        <!-- Critical Requests -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Critical Priority Requests
                    </h5>
                </div>
                <div class="card-body">
                    @if($criticalRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Request #</th>
                                        <th>Title</th>
                                        <th>Customer</th>
                                        <th>Reported</th>
                                        <th>Assigned To</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($criticalRequests as $request)
                                    <tr>
                                        <td>{{ $request->request_number }}</td>
                                        <td>{{ Str::limit($request->title, 40) }}</td>
                                        <td>{{ $request->designRequest->customer->name ?? 'N/A' }}</td>
                                        <td>{{ $request->reported_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($request->workOrders->count() > 0)
                                                {{ $request->workOrders->first()->technician->name ?? 'Unassigned' }}
                                            @else
                                                <span class="badge bg-warning">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('maintenance.requests.show', $request->id) }}"
                                               class="btn btn-sm btn-outline-primary">
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
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-success">No Critical Requests</h5>
                            <p class="text-muted">All critical requests have been addressed!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Equipment Status -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-toolbox mr-2"></i> Equipment Status
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($equipmentStatus as $status)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-capitalize">{{ $status->status }}</span>
                            <span class="badge bg-{{ $status->status == 'available' ? 'success' : ($status->status == 'in_use' ? 'primary' : 'warning') }}">
                                {{ $status->count }}
                            </span>
                        </div>
                        <div class="progress mt-1" style="height: 8px;">
                            <div class="progress-bar bg-{{ $status->status == 'available' ? 'success' : ($status->status == 'in_use' ? 'primary' : 'warning') }}"
                                 style="width: {{ ($status->count / $stats['total_equipment']) * 100 }}%">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow mt-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie mr-2"></i> Quick Stats
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="text-primary font-weight-bold h5">{{ $stats['total_work_orders'] }}</div>
                            <small class="text-muted">Total Work Orders</small>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-success font-weight-bold h5">{{ $stats['completed_work_orders'] }}</div>
                            <small class="text-muted">Completed</small>
                        </div>
                        <div class="col-6">
                            <div class="text-warning font-weight-bold h5">{{ $stats['pending_work_orders'] }}</div>
                            <small class="text-muted">Pending</small>
                        </div>
                        <div class="col-6">
                            <div class="text-info font-weight-bold h5">{{ $stats['equipment_needing_calibration'] }}</div>
                            <small class="text-muted">Needs Calibration</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Work Orders -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history mr-2"></i> Recent Work Orders
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Work Order #</th>
                                    <th>Request</th>
                                    <th>Technician</th>
                                    <th>Work Type</th>
                                    <th>Status</th>
                                    <th>Scheduled</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentWorkOrders as $workOrder)
                                <tr>
                                    <td>{{ $workOrder->work_order_number }}</td>
                                    <td>{{ Str::limit($workOrder->maintenanceRequest->title, 30) }}</td>
                                    <td>{{ $workOrder->technician->name ?? 'Unassigned' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark text-capitalize">
                                            {{ str_replace('_', ' ', $workOrder->work_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $workOrder->status == 'completed' ? 'success' : ($workOrder->status == 'in_progress' ? 'primary' : 'warning') }}">
                                            {{ ucfirst($workOrder->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $workOrder->scheduled_start->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('maintenance.work-orders.show', $workOrder->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
