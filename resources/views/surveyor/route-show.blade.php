<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Details - {{ $surveyRoute->route_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
            <h1 class="h3 mb-0 text-gray-800">Route Details</h1>
            <div class="d-flex">
                <a href="{{ route('surveyor.assignment.show', $surveyRoute->designRequest->id) }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Assignment
                </a>
                <a href="{{ route('surveyor.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-tachometer-alt fa-sm text-white-50"></i> Dashboard
                </a>
            </div>
        </div>

        <!-- Route Information -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-route mr-2"></i>
                            {{ $surveyRoute->route_name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-primary">Route Information</h6>
                                <p><strong>Route Code:</strong> {{ $surveyRoute->route_code }}</p>
                                <p><strong>Design Request:</strong> {{ $surveyRoute->designRequest->request_number }}</p>
                                <p><strong>Total Distance:</strong> {{ $surveyRoute->total_distance }} km</p>
                                <p><strong>Estimated Cost:</strong> ${{ number_format($surveyRoute->estimated_cost, 2) }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-primary">Status & Details</h6>
                                <p><strong>Status:</strong>
                                    <span class="badge bg-{{ $surveyRoute->status == 'completed' ? 'success' : ($surveyRoute->status == 'in_progress' ? 'info' : 'warning') }}">
                                        {{ ucfirst($surveyRoute->status) }}
                                    </span>
                                </p>
                                <p><strong>Created:</strong> {{ $surveyRoute->created_at->format('M d, Y H:i') }}</p>
                                <p><strong>Last Updated:</strong> {{ $surveyRoute->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        @if($surveyRoute->route_description)
                        <div class="mt-3">
                            <h6 class="font-weight-bold text-primary">Route Description</h6>
                            <p class="border p-3 rounded bg-light">{{ $surveyRoute->route_description }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Route Segments -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-map-signs mr-2"></i>
                            Route Segments
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($surveyRoute->routeSegments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Segment Name</th>
                                            <th>Type</th>
                                            <th>Distance</th>
                                            <th>Complexity</th>
                                            <th>Infrastructure</th>
                                            <th>Cost Multiplier</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($surveyRoute->routeSegments as $segment)
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
                                            <td>
                                                @if($segment->pole_count > 0)
                                                    <small>Poles: {{ $segment->pole_count }}</small><br>
                                                @endif
                                                @if($segment->manhole_count > 0)
                                                    <small>Manholes: {{ $segment->manhole_count }}</small><br>
                                                @endif
                                                @if($segment->splice_count > 0)
                                                    <small>Splices: {{ $segment->splice_count }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $segment->cost_multiplier }}x</td>
                                            <td>
                                                <button class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-map-signs fa-3x text-gray-300 mb-3"></i>
                                <h5 class="text-gray-500">No Route Segments Created</h5>
                                <p class="text-gray-400">Start by creating your first route segment.</p>
                                <a href="{{ route('surveyor.route-segments.create', $surveyRoute->id) }}"
                                   class="btn btn-primary">
                                    <i class="fas fa-plus mr-2"></i> Create First Segment
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt mr-2"></i>
                            Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('surveyor.route-segments.create', $surveyRoute->id) }}"
                               class="btn btn-primary btn-block">
                                <i class="fas fa-plus-circle mr-2"></i> Add Segment
                            </a>

                            <button type="button" class="btn btn-info btn-block">
                                <i class="fas fa-sync-alt mr-2"></i> Update Status
                            </button>

                            <button type="button" class="btn btn-success btn-block">
                                <i class="fas fa-check-circle mr-2"></i> Mark Complete
                            </button>

                            <button type="button" class="btn btn-danger btn-block">
                                <i class="fas fa-file-pdf mr-2"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Route Statistics -->
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Route Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Total Segments:</strong> {{ $surveyRoute->routeSegments->count() }}
                        </div>
                        <div class="mb-3">
                            <strong>Total Distance:</strong> {{ $surveyRoute->total_distance }} km
                        </div>
                        <div class="mb-3">
                            <strong>Average Complexity:</strong>
                            @php
                                $complexities = $surveyRoute->routeSegments->pluck('complexity');
                                $avgComplexity = $complexities->count() > 0 ? $complexities->map(function($c) {
                                    return match($c) {
                                        'low' => 1,
                                        'medium' => 2,
                                        'high' => 3,
                                        default => 1
                                    };
                                })->avg() : 0;
                            @endphp
                            <span class="badge bg-{{ $avgComplexity >= 2.5 ? 'danger' : ($avgComplexity >= 1.5 ? 'warning' : 'success') }}">
                                {{ $avgComplexity >= 2.5 ? 'High' : ($avgComplexity >= 1.5 ? 'Medium' : 'Low') }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong>Infrastructure Count:</strong>
                            <small class="d-block">Poles: {{ $surveyRoute->routeSegments->sum('pole_count') }}</small>
                            <small class="d-block">Manholes: {{ $surveyRoute->routeSegments->sum('manhole_count') }}</small>
                            <small class="d-block">Splices: {{ $surveyRoute->routeSegments->sum('splice_count') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
