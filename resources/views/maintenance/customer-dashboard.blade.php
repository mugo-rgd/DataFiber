<!-- resources/views/maintenance/customer-dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Maintenance Dashboard - Customer')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Maintenance Requests</h1>
        <a href="{{ route('maintenance.requests.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle mr-2"></i> Report Issue
        </a>
    </div>

    <!-- Customer-specific stats -->
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card border-left-primary shadow">
                <div class="card-body">
                    <div class="text-primary font-weight-bold">Open Requests</div>
                    <div class="h5">{{ $customerStats['open_requests'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-left-success shadow">
                <div class="card-body">
                    <div class="text-success font-weight-bold">Resolved</div>
                    <div class="h5">{{ $customerStats['resolved_requests'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-left-warning shadow">
                <div class="card-body">
                    <div class="text-warning font-weight-bold">In Progress</div>
                    <div class="h5">{{ $customerStats['in_progress'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-left-info shadow">
                <div class="card-body">
                    <div class="text-info font-weight-bold">Total Requests</div>
                    <div class="h5">{{ $customerStats['total_requests'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer's maintenance requests -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list mr-2"></i> My Maintenance Requests</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Request #</th>
                            <th>Title</th>
                            <th>Fibre Route</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Reported</th>
                            <th>Last Update</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customerRequests as $request)
                        <tr>
                            <td>{{ $request->request_number }}</td>
                            <td>{{ $request->title }}</td>
                            <td>{{ $request->designRequest->route_name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $request->priority === 'critical' ? 'danger' : ($request->priority === 'high' ? 'warning' : 'info') }}">
                                    {{ ucfirst($request->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $request->status === 'open' ? 'warning' : ($request->status === 'in_progress' ? 'primary' : 'success') }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td>{{ $request->reported_at->format('M d, Y') }}</td>
                            <td>{{ $request->updated_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('maintenance.requests.show', $request->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
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
@endsection
