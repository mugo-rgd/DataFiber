<!-- resources/views/technician/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Technician Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Technician Dashboard</h1>
        <div class="d-flex">
            <span class="badge bg-success mr-3 align-self-center">
                <i class="fas fa-id-badge mr-1"></i> {{ auth()->user()->employee_id }}
            </span>
            <a href="{{ route('technician.profile') }}" class="btn btn-outline-primary mr-2">
                <i class="fas fa-user mr-2"></i> My Profile
            </a>
            <a href="{{ route('maintenance.requests.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-2"></i> New Maintenance Request
            </a>
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
                                Assigned Work Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['assigned_work_orders'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                Completed This Week</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed_this_week'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Critical Priority</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['critical_priority'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                Available Equipment</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $availableEquipment->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-toolbox fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Work Orders -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks mr-2"></i> My Assigned Work Orders
                    </h5>
                </div>
                <div class="card-body">
                    @if($assignedWorkOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Work Order #</th>
                                        <th>Maintenance Request</th>
                                        <th>Customer</th>
                                        <th>Work Type</th>
                                        <th>Scheduled Start</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignedWorkOrders as $workOrder)
                                    <tr>
                                        <td>{{ $workOrder->work_order_number }}</td>
                                        <td>{{ $workOrder->maintenanceRequest->title }}</td>
                                        <td>{{ $workOrder->maintenanceRequest->designRequest->customer->name }}</td>
                                        <td>
                                            <span class="badge bg-info text-capitalize">
                                                {{ str_replace('_', ' ', $workOrder->work_type) }}
                                            </span>
                                        </td>
                                        <td>{{ $workOrder->scheduled_start->format('M d, Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $workOrder->status === 'in_progress' ? 'warning' : 'secondary' }}">
                                                {{ ucfirst($workOrder->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $workOrder->maintenanceRequest->priority === 'critical' ? 'danger' : 'warning' }}">
                                                {{ ucfirst($workOrder->maintenanceRequest->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('technician.work-orders.show', $workOrder->id) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            @if($workOrder->status === 'assigned')
                                            <form action="{{ route('technician.work-orders.start', $workOrder->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-play"></i> Start
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-500">No Assigned Work Orders</h5>
                            <p class="text-gray-400">You don't have any assigned work orders at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt mr-2"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                           <a href="{{ route('technician.work-orders.index') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-list mr-2"></i> All Work Orders
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('technician.equipment.index') }}" class="btn btn-info btn-block">
                                <i class="fas fa-toolbox mr-2"></i> Equipment
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('maintenance.requests.create') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-plus-circle mr-2"></i> New Request
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('technician.profile') }}" class="btn btn-success btn-block">
                                <i class="fas fa-user mr-2"></i> My Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
