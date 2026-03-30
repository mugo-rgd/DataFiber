{{-- resources/views/ictengineer/reports.blade.php --}}
@extends('layouts.app')

@section('title', 'ICT Engineer Reports')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-chart-bar text-primary"></i> Reports & Analytics
                </h1>
                <a href="{{ route('ictengineer.requests.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Requests
                </a>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('ictengineer.requests.index') }}">Design Requests</a></li>
                    <li class="breadcrumb-item active">Reports</li>
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
                                Total Requests</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRequests }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
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
                                Completed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completed }}</div>
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
                                In Progress</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inProgress }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
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
                                Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pending }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ICT Status Overview -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">ICT Status Overview</h5>
                </div>
                <div class="card-body">
                    @php
                        // Calculate counts for each status safely
                        $statusCounts = [
                            'pending_assignment' => $requests->where('ict_status', 'pending_assignment')->count(),
                            'assigned' => $requests->where('ict_status', 'assigned')->count(),
                            'inspection_scheduled' => $requests->where('ict_status', 'inspection_scheduled')->count(),
                            'inspection_completed' => $requests->where('ict_status', 'inspection_completed')->count(),
                            'certificate_generated' => $requests->where('ict_status', 'certificate_generated')->count(),
                            'certificate_sent' => $requests->where('ict_status', 'certificate_sent')->count(),
                            'completed' => $requests->where('ict_status', 'completed')->count(),
                        ];
                    @endphp

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statusCounts as $status => $count)
                                @php
                                    $label = match($status) {
                                        'pending_assignment' => 'Pending Assignment',
                                        'assigned' => 'Assigned',
                                        'inspection_scheduled' => 'Inspection Scheduled',
                                        'inspection_completed' => 'Inspection Completed',
                                        'certificate_generated' => 'Certificate Generated',
                                        'certificate_sent' => 'Certificate Sent',
                                        'completed' => 'Completed',
                                        default => ucfirst(str_replace('_', ' ', $status))
                                    };

                                    $percentage = $totalRequests > 0 ? ($count / $totalRequests) * 100 : 0;
                                    $color = match($status) {
                                        'pending_assignment' => 'secondary',
                                        'assigned' => 'primary',
                                        'inspection_scheduled' => 'warning',
                                        'inspection_completed' => 'info',
                                        'certificate_generated' => 'success',
                                        'certificate_sent' => 'success',
                                        'completed' => 'success',
                                        default => 'secondary'
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $color }}">
                                            {{ $label }}
                                        </span>
                                    </td>
                                    <td>{{ $count }}</td>
                                    <td>{{ round($percentage, 1) }}%</td>
                                    <td>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-{{ $color }}"
                                                 style="width: {{ $percentage }}%;"
                                                 role="progressbar"
                                                 aria-valuenow="{{ $percentage }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-calendar-check text-primary"></i>
                                <span class="ms-2">This Month</span>
                            </div>
                            <span class="badge bg-primary rounded-pill">
                                {{ $requests->where('created_at', '>=', now()->startOfMonth())->count() }}
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-exclamation-triangle text-danger"></i>
                                <span class="ms-2">High Priority</span>
                            </div>
                            <span class="badge bg-danger rounded-pill">
                                {{ $requests->where('priority', 'high')->count() }}
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-user text-success"></i>
                                <span class="ms-2">With Customers</span>
                            </div>
                            <span class="badge bg-success rounded-pill">
                                {{ $requests->whereNotNull('customer_id')->count() }}
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-dollar-sign text-warning"></i>
                                <span class="ms-2">With Cost Estimate</span>
                            </div>
                            <span class="badge bg-warning rounded-pill">
                                {{ $requests->whereNotNull('estimated_cost')->count() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Requests -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Recent Design Requests</h5>
                </div>
                <div class="card-body">
                    @if($requests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Request #</th>
                                    <th>Customer</th>
                                    <th>Title</th>
                                    <th>ICT Status</th>
                                    <th>Priority</th>
                                    <th>Created</th>
                                    <th>Estimated Cost</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests->take(10) as $request)
                                <tr>
                                    <td>
                                        <strong>{{ $request->request_number }}</strong>
                                    </td>
                                    <td>{{ $request->customer->name ?? 'N/A' }}</td>
                                    <td>{{ Str::limit($request->title, 30) }}</td>
                                    <td>
                                        <span class="badge bg-{{ match($request->ict_status) {
                                            'pending_assignment' => 'secondary',
                                            'assigned' => 'primary',
                                            'inspection_scheduled' => 'warning',
                                            'inspection_completed' => 'info',
                                            'certificate_generated' => 'success',
                                            'certificate_sent' => 'success',
                                            'completed' => 'success',
                                            default => 'secondary'
                                        } }}">
                                            {{ ucfirst(str_replace('_', ' ', $request->ict_status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($request->priority)
                                        <span class="badge bg-{{ match($request->priority) {
                                            'low' => 'success',
                                            'medium' => 'warning',
                                            'high' => 'danger',
                                            default => 'secondary'
                                        } }}">
                                            {{ ucfirst($request->priority) }}
                                        </span>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($request->estimated_cost)
                                            ${{ number_format($request->estimated_cost, 2) }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('ictengineer.requests.show', $request->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No design requests found for reporting.</p>
                        <a href="{{ route('ictengineer.requests.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> View All Requests
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
