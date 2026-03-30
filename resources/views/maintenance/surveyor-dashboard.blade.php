{{-- resources/views/maintenance/surveyor-dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Maintenance Dashboard - Surveyor')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tools me-2"></i>Maintenance Dashboard
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Maintenance Overview Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Assigned Work Orders</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $assignedWorkOrders->count() }}</div>
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
                                                Recent Surveys</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $recentSurveys->count() }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-route fa-2x text-gray-300"></i>
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
                                                Maintenance Requests</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $recentSurveys->sum('maintenance_requests_count') }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assigned Work Orders -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-clipboard-list me-2"></i>My Assigned Work Orders
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if($assignedWorkOrders->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Work Order #</th>
                                                        <th>Priority</th>
                                                        <th>Scheduled Start</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($assignedWorkOrders as $workOrder)
                                                        <tr>
                                                            <td>#{{ $workOrder->id }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $workOrder->maintenanceRequest->priority === 'high' ? 'danger' : ($workOrder->maintenanceRequest->priority === 'medium' ? 'warning' : 'secondary') }}">
                                                                    {{ ucfirst($workOrder->maintenanceRequest->priority) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $workOrder->scheduled_start ? $workOrder->scheduled_start->format('M j, Y H:i') : 'Not scheduled' }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $workOrder->status === 'in_progress' ? 'primary' : ($workOrder->status === 'completed' ? 'success' : 'warning') }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $workOrder->status)) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                            <p>No work orders assigned to you.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Recent Surveys with Maintenance Issues -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-route me-2"></i>Recent Surveys
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if($recentSurveys->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($recentSurveys as $survey)
                                                <div class="list-group-item px-0">
                                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                                        <h6 class="mb-1">{{ $survey->name }}</h6>
                                                        <span class="badge bg-{{ $survey->maintenance_requests_count > 0 ? 'warning' : 'success' }}">
                                                            {{ $survey->maintenance_requests_count }} issues
                                                        </span>
                                                    </div>
                                                    <p class="mb-1 text-muted small">
                                                        Created: {{ $survey->created_at->format('M j, Y') }}
                                                    </p>
                                                    @if($survey->maintenance_requests_count > 0)
                                                        <small class="text-warning">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                                            Requires maintenance attention
                                                        </small>
                                                    @else
                                                        <small class="text-success">
                                                            <i class="fas fa-check-circle me-1"></i>
                                                            No maintenance issues
                                                        </small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-route fa-3x mb-3"></i>
                                            <p>No recent surveys found.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Quick Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('surveyor.assignments') }}" class="btn btn-outline-primary w-100">
                                                <i class="fas fa-tasks me-2"></i>View Assignments
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('surveyor.routes') }}" class="btn btn-outline-success w-100">
                                                <i class="fas fa-route me-2"></i>Manage Routes
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('maintenance.requests.create') }}" class="btn btn-outline-warning w-100">
                                                <i class="fas fa-plus-circle me-2"></i>Report Issue
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('surveyor.profile') }}" class="btn btn-outline-info w-100">
                                                <i class="fas fa-user me-2"></i>My Profile
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
