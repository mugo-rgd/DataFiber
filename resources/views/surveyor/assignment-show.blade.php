<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Details - {{ $designRequest->request_number }}</title>

    <!-- Include your main layout -->
    @extends('layouts.app')

    @section('styles')
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline-marker {
            position: absolute;
            left: -30px;
            top: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #dee2e6;
        }
        .timeline-marker.bg-primary,
        .timeline-marker.bg-info,
        .timeline-marker.bg-warning,
        .timeline-marker.bg-success {
            background-color: var(--bs-primary) !important;
        }
        .timeline-item.active .timeline-marker {
            background-color: var(--bs-success) !important;
        }
        .timeline-content {
            padding-bottom: 10px;
        }
        .survey-checklist-item {
            padding: 10px;
            border-left: 3px solid #dee2e6;
            margin-bottom: 10px;
        }
        .survey-checklist-item.completed {
            border-left-color: #198754;
            background-color: #f8fff9;
        }
        .survey-checklist-item.in-progress {
            border-left-color: #0dcaf0;
            background-color: #f0fdff;
        }
        .infrastructure-icon {
            font-size: 1.5rem;
            margin-right: 10px;
            color: #6c757d;
        }
        .segment-visualization {
            height: 200px;
            background: linear-gradient(to right, #e9ecef, #dee2e6);
            border-radius: 5px;
            position: relative;
            overflow: hidden;
        }
        .segment-node {
            position: absolute;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #0d6efd;
            transform: translate(-50%, -50%);
        }
        .segment-node.manhole {
            background-color: #6f42c1;
        }
        .segment-node.pole {
            background-color: #fd7e14;
        }
        .segment-node.building {
            background-color: #198754;
        }
        .segment-line {
            position: absolute;
            height: 2px;
            background-color: #0d6efd;
            transform-origin: left center;
        }
        .dark-fibre-specs {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
        }
        .technical-parameter {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .technical-parameter:last-child {
            border-bottom: none;
        }
        .hazard-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .hazard-high {
            background-color: #dc3545;
        }
        .hazard-medium {
            background-color: #ffc107;
        }
        .hazard-low {
            background-color: #198754;
        }
        .attachment-thumbnail {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 10px;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
        }
        .btn-group-vertical .btn {
            margin-bottom: 5px;
        }
    </style>
    @endsection
</head>
<body>
    @section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
            <h1 class="h3 mb-0 text-gray-800">Assignment Details</h1>
            <div class="d-flex">
                <a href="{{ route('surveyor.assignments.index') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Assignments
                </a>
                <a href="{{ route('surveyor.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-tachometer-alt fa-sm text-white-50"></i> Dashboard
                </a>
            </div>
        </div>

        <!-- Assignment Details -->
        <div class="row">
            <div class="col-lg-8">
                <!-- Design Request Card -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>
                            Design Request #{{ $designRequest->request_number }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-primary">Basic Information</h6>
                                <p><strong>Request Number:</strong> {{ $designRequest->request_number }}</p>
                                <p><strong>Title:</strong> {{ $designRequest->title }}</p>
                                <p><strong>Description:</strong> {{ $designRequest->description }}</p>
                                <p><strong>Customer:</strong> {{ $designRequest->customer->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-primary">Status & Priority</h6>
                                <p><strong>Status:</strong>
                                    <span class="badge bg-{{ $designRequest->status === 'completed' ? 'success' : ($designRequest->status === 'in_progress' ? 'primary' : 'warning') }}">
                                        {{ ucfirst($designRequest->status) }}
                                    </span>
                                </p>
                                <p><strong>Priority:</strong>
                                    <span class="badge bg-{{ $designRequest->priority == 'high' ? 'danger' : ($designRequest->priority == 'medium' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($designRequest->priority) }}
                                    </span>
                                </p>
                                <p><strong>Requested At:</strong>
                                    {{ $designRequest->created_at->format('M d, Y H:i') }}
                                </p>
                                <p><strong>Assigned At:</strong>
                                    @if($designRequest->assigned_at)
                                        {{ $designRequest->assigned_at->format('M d, Y H:i') }}
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Specifications -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            Technical Specifications
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Technology Type:</strong> {{ $designRequest->technology_type ?? 'N/A' }}</p>
                                <p><strong>Link Class:</strong> {{ $designRequest->link_class ?? 'N/A' }}</p>
                                <p><strong>Cores Required:</strong> {{ $designRequest->cores_required ?? 'N/A' }}</p>
                                <p><strong>Distance:</strong> {{ $designRequest->distance ?? 'N/A' }} km</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Unit Cost:</strong> ${{ $designRequest->unit_cost ?? '0.00' }}</p>
                                <p><strong>Tax Rate:</strong> {{ $designRequest->tax_rate ?? '0' }}%</p>
                                <p><strong>Terms:</strong> {{ $designRequest->terms ?? 'N/A' }} months</p>
                                <p><strong>Route Name:</strong> {{ $designRequest->route_name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($designRequest->technical_requirements)
                        <div class="mt-3">
                            <h6 class="font-weight-bold text-primary">Technical Requirements</h6>
                            <p class="border p-3 rounded bg-light">{{ $designRequest->technical_requirements }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Enhanced Survey Details for Dark Fibre -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-map-marked-alt me-2"></i>
                            Dark Fibre Survey Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-primary mb-3">Infrastructure Assessment</h6>

                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-road infrastructure-icon"></i>
                                        <span class="fw-bold">Existing Conduit Assessment</span>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="conduit-existing">
                                        <label class="form-check-label" for="conduit-existing">
                                            Existing conduit available
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="conduit-space">
                                        <label class="form-check-label" for="conduit-space">
                                            Sufficient space in conduit
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="conduit-condition">
                                        <label class="form-check-label" for="conduit-condition">
                                            Conduit in good condition
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-draw-polygon infrastructure-icon"></i>
                                        <span class="fw-bold">Path Characteristics</span>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Route Complexity</label>
                                        <div class="progress mb-2">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">Medium complexity - Mixed underground/aerial route</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-primary mb-3">Hazard Identification</h6>

                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-exclamation-triangle infrastructure-icon"></i>
                                        <span class="fw-bold">Identified Hazards</span>
                                    </div>
                                    <div class="alert alert-warning py-2">
                                        <span class="hazard-indicator hazard-medium"></span>
                                        <strong>Railway crossing</strong> - Requires specialized boring
                                    </div>
                                    <div class="alert alert-danger py-2">
                                        <span class="hazard-indicator hazard-high"></span>
                                        <strong>High-voltage power lines</strong> - Parallel for 350m
                                    </div>
                                    <div class="alert alert-success py-2">
                                        <span class="hazard-indicator hazard-low"></span>
                                        <strong>Historic district</strong> - Special permits required
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h6 class="font-weight-bold text-primary mb-3">Route Visualization</h6>
                                <div class="segment-visualization mb-3">
                                    <!-- This would be a dynamic visualization in a real application -->
                                    <div class="segment-node" style="top: 50%; left: 10%;" title="Data Center A"></div>
                                    <div class="segment-node manhole" style="top: 30%; left: 30%;" title="Manhole #42"></div>
                                    <div class="segment-node pole" style="top: 70%; left: 50%;" title="Utility Pole #127"></div>
                                    <div class="segment-node building" style="top: 50%; left: 70%;" title="Financial Tower"></div>
                                    <div class="segment-node" style="top: 50%; left: 90%;" title="Data Center B"></div>

                                    <div class="segment-line" style="top: 50%; left: 10%; width: 20%; transform: rotate(0deg);"></div>
                                    <div class="segment-line" style="top: 39%; left: 30%; width: 22%; transform: rotate(20deg);"></div>
                                    <div class="segment-line" style="top: 61%; left: 50%; width: 20%; transform: rotate(-20deg);"></div>
                                    <div class="segment-line" style="top: 50%; left: 70%; width: 20%; transform: rotate(0deg);"></div>
                                </div>
                                <div class="text-center">
                                    <small class="text-muted">Visual representation of the proposed dark fibre route with key infrastructure points</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Survey Information -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-map-marked-alt me-2"></i>
                            Survey Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Survey Status:</strong>
                            <span class="badge bg-{{ $designRequest->survey_status == 'completed' ? 'success' : ($designRequest->survey_status == 'in_progress' ? 'info' : 'warning') }}">
                                {{ ucfirst(str_replace('_', ' ', $designRequest->survey_status)) }}
                            </span>
                        </p>

                        @if($designRequest->survey_scheduled_at)
                        <p><strong>Scheduled Date:</strong>
                            @php
                                $scheduledAt = \Carbon\Carbon::parse($designRequest->survey_scheduled_at);
                            @endphp
                            {{ $scheduledAt->format('M d, Y H:i') }}
                        </p>
                        @endif

                        @if($designRequest->survey_estimated_hours)
                        <p><strong>Estimated Hours:</strong> {{ $designRequest->survey_estimated_hours }} hours</p>
                        @endif

                        @if($designRequest->survey_requirements)
                        <div class="mt-3">
                            <h6 class="font-weight-bold text-primary">Survey Requirements</h6>
                            <p class="border p-3 rounded bg-light small">{{ $designRequest->survey_requirements }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>
                            Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <!-- Update Survey Status -->
                            <button type="button" class="btn btn-info btn-block" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                <i class="fas fa-sync-alt me-2"></i> Update Status
                            </button>

                            <!-- Route Management Button -->
                            @if($designRequest->surveyRoute)
                                <a href="{{ route('surveyor.routes.show', $designRequest->surveyRoute->id) }}"
                                   class="btn btn-success btn-block">
                                    <i class="fas fa-route me-2"></i> Manage Route
                                </a>
                                <a href="{{ route('surveyor.route-segments.create', $designRequest->surveyRoute->id) }}"
                                   class="btn btn-primary btn-block">
                                    <i class="fas fa-plus-circle me-2"></i> Add Segment
                                </a>
                            @else
                                <button type="button" class="btn btn-success btn-block" data-bs-toggle="modal" data-bs-target="#createRouteModal">
                                    <i class="fas fa-route me-2"></i> Create Route
                                </button>
                            @endif

                            <!-- Submit Survey Report -->
                            <button type="button" class="btn btn-warning btn-block" data-bs-toggle="modal" data-bs-target="#submitReportModal">
                                <i class="fas fa-file-alt me-2"></i> Submit Report
                            </button>

                            <!-- Mark as Complete -->
                            @if($designRequest->survey_status !== 'completed')
                            <form action="{{ route('surveyor.assignments.complete', $designRequest->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-block"
                                        onclick="return confirm('Mark this survey as completed?')">
                                    <i class="fas fa-check-circle me-2"></i> Mark Complete
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Customer Contact -->
                <div class="card shadow mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            Customer Contact
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> {{ $designRequest->customer->name ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $designRequest->customer->email ?? 'N/A' }}</p>
                        <p><strong>Phone:</strong> {{ $designRequest->customer->phone ?? 'N/A' }}</p>

                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-envelope me-1"></i> Email Customer
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm ms-1">
                                <i class="fas fa-phone me-1"></i> Call Customer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Dark Fibre Specifications -->
                <div class="card shadow mt-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-fiber me-2"></i>
                            Fibre Specifications
                        </h5>
                    </div>
                    <div class="card-body dark-fibre-specs">
                        <div class="technical-parameter">
                            <span>Fibre Type:</span>
                            <strong>{{ $designRequest->technology_type ?? 'Single Mode (G.652.D)' }}</strong>
                        </div>
                        <div class="technical-parameter">
                            <span>Core Count:</span>
                            <strong>{{ $designRequest->cores_required ?? 'N/A' }}</strong>
                        </div>
                        <div class="technical-parameter">
                            <span>Max Attenuation:</span>
                            <strong>0.35 dB/km @ 1550nm</strong>
                        </div>
                        <div class="technical-parameter">
                            <span>Chromatic Dispersion:</span>
                            <strong>≤ 18 ps/(nm·km)</strong>
                        </div>
                        <div class="technical-parameter">
                            <span>Bend Radius:</span>
                            <strong>30mm (static)</strong>
                        </div>
                        <div class="technical-parameter">
                            <span>Tensile Load:</span>
                            <strong>≥ 2000 N (short-term)</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Survey Progress Section -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-tasks me-2"></i>
                            Survey Progress & Checklist
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="font-weight-bold">Survey Completion</span>
                                <span>
                                    @php
                                        $progress = match($designRequest->survey_status) {
                                            'assigned' => 25,
                                            'in_progress' => 50,
                                            'completed' => 100,
                                            default => 0
                                        };
                                    @endphp
                                    {{ $progress }}%
                                </span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar
                                    @if($designRequest->survey_status == 'completed') bg-success
                                    @elseif($designRequest->survey_status == 'in_progress') bg-info
                                    @else bg-warning @endif"
                                    role="progressbar"
                                    style="width: {{ $progress }}%;"
                                    aria-valuenow="{{ $progress }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        <!-- Survey Checklist -->
                        <h6 class="font-weight-bold text-primary mb-3">Survey Checklist</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="survey-checklist-item completed">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" checked id="check1">
                                        <label class="form-check-label" for="check1">
                                            <strong>Site Documentation</strong> - Photograph all access points
                                        </label>
                                    </div>
                                </div>
                                <div class="survey-checklist-item completed">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" checked id="check2">
                                        <label class="form-check-label" for="check2">
                                            <strong>GPS Mapping</strong> - Record coordinates at key points
                                        </label>
                                    </div>
                                </div>
                                <div class="survey-checklist-item in-progress">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="check3">
                                        <label class="form-check-label" for="check3">
                                            <strong>Infrastructure Assessment</strong> - Document existing conduits
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="survey-checklist-item">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="check4">
                                        <label class="form-check-label" for="check4">
                                            <strong>Hazard Identification</strong> - Note all obstacles and risks
                                        </label>
                                    </div>
                                </div>
                                <div class="survey-checklist-item">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="check5">
                                        <label class="form-check-label" for="check5">
                                            <strong>Measurements</strong> - Record distances and elevations
                                        </label>
                                    </div>
                                </div>
                                <div class="survey-checklist-item">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="check6">
                                        <label class="form-check-label" for="check6">
                                            <strong>Permit Requirements</strong> - Identify needed approvals
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Route Management Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-route me-2"></i>
                            Route Management
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $surveyRoute = $designRequest->surveyRoute;
                        @endphp

                        @if($surveyRoute)
                            <!-- Existing Route Details -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold text-primary">Route Information</h6>
                                    <p><strong>Route Name:</strong> {{ $surveyRoute->route_name }}</p>
                                    <p><strong>Total Distance:</strong> {{ $surveyRoute->total_distance ?? '0' }} km</p>
                                    <p><strong>Estimated Cost:</strong> ${{ number_format($surveyRoute->estimated_cost ?? 0, 2) }}</p>
                                    <p><strong>Status:</strong>
                                        <span class="badge bg-{{ $surveyRoute->status == 'completed' ? 'success' : ($surveyRoute->status == 'in_progress' ? 'info' : 'warning') }}">
                                            {{ ucfirst($surveyRoute->status) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold text-primary">Route Segments</h6>
                                    <p><strong>Total Segments:</strong> {{ $surveyRoute->routeSegments ? $surveyRoute->routeSegments->count() : 0 }}</p>
                                    <p><strong>Last Updated:</strong> {{ $surveyRoute->updated_at->format('M d, Y H:i') }}</p>

                                    <!-- Route Actions -->
                                    <div class="mt-3">
                                        <a href="{{ route('surveyor.routes.show', $surveyRoute->id) }}"
                                           class="btn btn-info btn-sm me-2">
                                            <i class="fas fa-eye me-1"></i> View Route Details
                                        </a>
                                        <a href="{{ route('surveyor.route-segments.create', $surveyRoute->id) }}"
                                           class="btn btn-success btn-sm">
                                            <i class="fas fa-plus me-1"></i> Add Segment
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Segments Table -->
                            @if($surveyRoute->routeSegments && $surveyRoute->routeSegments->count() > 0)
                                <h6 class="font-weight-bold text-primary mb-3">Recent Route Segments</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Segment Name</th>
                                                <th>Type</th>
                                                <th>Distance</th>
                                                <th>Complexity</th>
                                                <th>Cost Multiplier</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($surveyRoute->routeSegments->take(3) as $segment)
                                            <tr>
                                                <td>{{ $segment->segment_number }}</td>
                                                <td>{{ $segment->segment_name }}</td>
                                                <td>
                                                    <span class="badge bg-light text-dark text-capitalize">
                                                        {{ $segment->installation_type }}
                                                    </span>
                                                </td>
                                                <td>{{ $segment->distance_km }} km</td>
                                                <td>
                                                    <span class="badge bg-{{ $segment->complexity == 'high' ? 'danger' : ($segment->complexity == 'medium' ? 'warning' : 'success') }}">
                                                        {{ ucfirst($segment->complexity) }}
                                                    </span>
                                                </td>
                                                <td>{{ $segment->cost_multiplier }}x</td>
                                                <td>
                                                    <a href="#" class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($surveyRoute->routeSegments->count() > 3)
                                    <div class="text-center mt-2">
                                        <a href="{{ route('surveyor.routes.show', $surveyRoute->id) }}" class="btn btn-outline-primary btn-sm">
                                            View All {{ $surveyRoute->routeSegments->count() }} Segments
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-route fa-3x text-gray-300 mb-3"></i>
                                    <h5 class="text-gray-500">No Route Segments Created</h5>
                                    <p class="text-gray-400">Start by creating your first route segment.</p>
                                    <a href="{{ route('surveyor.route-segments.create', $surveyRoute->id) }}"
                                       class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Create First Segment
                                    </a>
                                </div>
                            @endif

                        @else
                            <!-- No Route Created Yet -->
                            <div class="text-center py-4">
                                <i class="fas fa-route fa-4x text-gray-300 mb-3"></i>
                                <h4 class="text-gray-500">No Route Created Yet</h4>
                                <p class="text-gray-400 mb-4">Create a route to start documenting your survey segments and infrastructure details.</p>

                                <!-- Create Route Button -->
                                <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#createRouteModal">
                                    <i class="fas fa-plus-circle me-2"></i> Create Survey Route
                                </button>

                                <div class="mt-3">
                                    <small class="text-muted">
                                        You'll be able to add multiple segments with detailed infrastructure information
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Route Modal -->
    <div class="modal fade" id="createRouteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Dark Fibre Survey Route</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('surveyor.routes.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="design_request_id" value="{{ $designRequest->id }}">

                    <div class="modal-body">
                        <!-- Route Basic Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="route_name" class="form-label fw-bold">Route Name *</label>
                                    <input type="text" class="form-control" id="route_name" name="route_name"
                                           value="Dark Fibre Route for {{ $designRequest->request_number }}" required>
                                    <div class="form-text">Give your route a descriptive name</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="route_type" class="form-label fw-bold">Primary Installation Type *</label>
                                    <select class="form-control" id="route_type" name="route_type" required>
                                        <option value="">Select installation type</option>
                                        <option value="underground">Underground Conduit</option>
                                        <option value="aerial">Aerial</option>
                                        <option value="direct_burial">Direct Burial</option>
                                        <option value="mixed">Mixed (Multiple Types)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Route Description -->
                        <div class="mb-3">
                            <label for="route_description" class="form-label fw-bold">Route Description</label>
                            <textarea class="form-control" id="route_description" name="route_description"
                                      rows="3" placeholder="Describe the overall route, key landmarks, special considerations, or challenges...">{{ $designRequest->description ?? '' }}</textarea>
                        </div>

                        <!-- Start and End Points -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_point" class="form-label fw-bold">Start Point *</label>
                                    <input type="text" class="form-control" id="start_point" name="start_point"
                                           placeholder="e.g., Data Center A, 123 Main St" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_point" class="form-label fw-bold">End Point *</label>
                                    <input type="text" class="form-control" id="end_point" name="end_point"
                                           placeholder="e.g., Financial Tower, 456 Oak Ave" required>
                                </div>
                            </div>
                        </div>

                        <!-- Technical Specifications -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="estimated_distance" class="form-label fw-bold">Estimated Distance (km) *</label>
                                    <input type="number" class="form-control" id="estimated_distance" name="estimated_distance"
                                           step="0.001" min="0.001"
                                           value="{{ $designRequest->distance ?? '0.000' }}" required>
                                    <div class="form-text">Total route length in kilometers</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fibre_type" class="form-label fw-bold">Fibre Type *</label>
                                    <select class="form-control" id="fibre_type" name="fibre_type" required>
                                        <option value="single_mode" {{ ($designRequest->technology_type ?? '') == 'Single Mode Fibre' ? 'selected' : '' }}>
                                            Single Mode (G.652.D)
                                        </option>
                                        <option value="os2" {{ ($designRequest->technology_type ?? '') == 'OS2' ? 'selected' : '' }}>
                                            OS2 Single Mode
                                        </option>
                                        <option value="multimode_om4">Multimode OM4</option>
                                        <option value="multimode_om5">Multimode OM5</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="core_count" class="form-label fw-bold">Core Count *</label>
                                    <input type="number" class="form-control" id="core_count" name="core_count"
                                           value="{{ $designRequest->cores_required ?? '48' }}"
                                           min="1" max="144" required>
                                    <div class="form-text">Number of fibre cores</div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Route Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="terrain_type" class="form-label fw-bold">Primary Terrain Type</label>
                                    <select class="form-control" id="terrain_type" name="terrain_type">
                                        <option value="">Select terrain type</option>
                                        <option value="urban">Urban</option>
                                        <option value="suburban">Suburban</option>
                                        <option value="rural">Rural</option>
                                        <option value="mountainous">Mountainous</option>
                                        <option value="coastal">Coastal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="complexity" class="form-label fw-bold">Route Complexity</label>
                                    <select class="form-control" id="complexity" name="complexity">
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Hazard Assessment -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Known Hazards (Check all that apply)</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="hazards[]" value="railway_crossing" id="hazard_railway">
                                        <label class="form-check-label" for="hazard_railway">
                                            Railway Crossing
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="hazards[]" value="power_lines" id="hazard_power">
                                        <label class="form-check-label" for="hazard_power">
                                            High Voltage Power Lines
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="hazards[]" value="water_crossing" id="hazard_water">
                                        <label class="form-check-label" for="hazard_water">
                                            Water Body Crossing
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="hazards[]" value="highway" id="hazard_highway">
                                        <label class="form-check-label" for="hazard_highway">
                                            Highway/Expressway
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="hazards[]" value="historic_area" id="hazard_historic">
                                        <label class="form-check-label" for="hazard_historic">
                                            Historic/Protected Area
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="hazards[]" value="other" id="hazard_other">
                                        <label class="form-check-label" for="hazard_other">
                                            Other Obstacles
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div class="mb-3">
                            <label for="special_requirements" class="form-label fw-bold">Special Requirements</label>
                            <textarea class="form-control" id="special_requirements" name="special_requirements"
                                      rows="2" placeholder="Any special permits, access requirements, or additional considerations...">{{ $designRequest->technical_requirements ?? '' }}</textarea>
                        </div>

                        <!-- Information Alert -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Next Steps:</strong> After creating the route, you'll be able to add detailed segments with infrastructure counts, GPS coordinates, obstacles, and cost calculations.
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-route me-2"></i> Create Dark Fibre Route
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Survey Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('surveyor.assignments.update-status', $designRequest->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="survey_status" class="form-label">Status</label>
                            <select class="form-control" id="survey_status" name="survey_status" required>
                                <option value="assigned" {{ $designRequest->survey_status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="in_progress" {{ $designRequest->survey_status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ $designRequest->survey_status == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="survey_notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="survey_notes" name="survey_notes" rows="3" placeholder="Add any notes about the survey..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Submit Report Modal -->
    <div class="modal fade" id="submitReportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Survey Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('surveyor.reports.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="design_request_id" value="{{ $designRequest->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="report_title" class="form-label">Report Title</label>
                            <input type="text" class="form-control" id="report_title" name="title" value="Survey Report - {{ $designRequest->request_number }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="report_content" class="form-label">Survey Findings</label>
                            <textarea class="form-control" id="report_content" name="content" rows="5" placeholder="Describe your survey findings, measurements, and observations..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="report_attachments" class="form-label">Attachments</label>
                            <input type="file" class="form-control" id="report_attachments" name="attachments[]" multiple>
                            <div class="form-text">You can upload multiple files (photos, documents, etc.)</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Submit Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection

    @section('scripts')
    <script>
        // Auto-save draft functionality
        let autoSaveTimer;
        document.getElementById('report_content')?.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                localStorage.setItem('survey_report_draft', document.getElementById('report_content').value);
                console.log('Draft auto-saved');
            }, 2000);
        });

        // Load draft on page load
        document.addEventListener('DOMContentLoaded', function() {
            const draft = localStorage.getItem('survey_report_draft');
            const reportContent = document.getElementById('report_content');
            if (draft && reportContent) {
                reportContent.value = draft;
            }

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });

        // Handle route type change
        document.getElementById('route_type')?.addEventListener('change', function() {
            const complexity = document.getElementById('complexity');
            if (this.value === 'mixed') {
                complexity.value = 'high';
            } else if (this.value === 'aerial') {
                complexity.value = 'medium';
            } else {
                complexity.value = 'low';
            }
        });
    </script>
    @endsection
</body>
</html>
