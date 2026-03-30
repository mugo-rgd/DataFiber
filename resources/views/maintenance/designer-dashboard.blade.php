<!-- resources/views/maintenance/designer-dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Maintenance Dashboard - Designer')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Maintenance Management</h1>
        <div>
            <a href="{{ route('maintenance.requests.create') }}" class="btn btn-primary mr-2">
                <i class="fas fa-plus-circle mr-2"></i> New Request
            </a>
            <a href="{{ route('maintenance.work-orders.create') }}" class="btn btn-success">
                <i class="fas fa-clipboard-check mr-2"></i> Create Work Order
            </a>
        </div>
    </div>

    <!-- Designer-specific overview -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Network Health Overview -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-network-wired mr-2"></i> Network Health Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($networkHealth as $route)
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6>{{ $route->route_name }}</h6>
                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-{{ $route->health_color }}"
                                             style="width: {{ $route->health_percentage }}%">
                                            {{ $route->health_percentage }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        {{ $route->open_issues }} open issues
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Assignments -->
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-user-clock mr-2"></i> Pending Assignments</h5>
                </div>
                <div class="card-body">
                    @foreach($pendingAssignments as $assignment)
                    <div class="mb-3 p-2 border rounded">
                        <strong>{{ $assignment->request_number }}</strong>
                        <p class="mb-1 small">{{ Str::limit($assignment->title, 50) }}</p>
                        <div class="d-flex justify-content-between">
                            <span class="badge bg-secondary">{{ $assignment->priority }}</span>
                            <a href="{{ route('maintenance.work-orders.create', ['request' => $assignment->id]) }}"
                               class="btn btn-sm btn-outline-primary">Assign</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
