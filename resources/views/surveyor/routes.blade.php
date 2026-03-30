@extends('layouts.app')

@section('title', 'Survey Routes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Survey Routes</h1>
                <div class="btn-group">
                    <button class="btn btn-outline-primary" id="printRoutes">
                        <i class="fas fa-print me-1"></i>Print Routes
                    </button>
                    <button class="btn btn-outline-success" id="exportRoutes">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                </div>
            </div>

            <!-- Route Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total Routes</h6>
                                    <h4>{{ $assignedRoutes->count() }}</h4>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-route fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">High Priority</h6>
                                    <h4>{{ $assignedRoutes->where('priority', 'high')->count() }}</h4>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">In Progress</h6>
                                    <h4>{{ $assignedRoutes->where('status', 'in_progress')->count() }}</h4>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-spinner fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Pending</h6>
                                    <h4>{{ $assignedRoutes->where('status', 'pending')->count() }}</h4>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Route List -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Route Planning</h3>
                            <div class="card-tools">
                                <button class="btn btn-sm btn-outline-secondary" id="sortByPriority">
                                    <i class="fas fa-sort me-1"></i>Sort by Priority
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($assignedRoutes->count() > 0)
                                <div class="list-group">
                                    @foreach($assignedRoutes as $index => $route)
                                        <div class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between align-items-start">
                                                <div class="me-3">
                                                    <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        {{ $route->request_number }} - {{ $route->customer->name ?? 'Unknown Customer' }}
                                                    </h6>
                                                    <p class="mb-1 text-muted small">
                                                        {{ Str::limit($route->description, 80) }}
                                                    </p>
                                                    <small class="text-muted">
                                                        @if($route->location_address)
                                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $route->location_address }}
                                                        @else
                                                            <i class="fas fa-map-marker-alt me-1"></i>Address not specified
                                                        @endif
                                                    </small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-{{ $route->priority === 'high' ? 'danger' : ($route->priority === 'medium' ? 'warning' : 'info') }} mb-1">
                                                        {{ ucfirst($route->priority) }}
                                                    </span>
                                                    <br>
                                                    <span class="badge bg-{{ $route->status === 'in_progress' ? 'info' : 'warning' }}">
                                                        {{ ucfirst($route->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <a href="{{ route('surveyor.design-requests.show', $route->id) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>View Details
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-map-marked-alt me-1"></i>Get Directions
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-route fa-3x text-muted mb-3"></i>
                                    <h4>No Routes Planned</h4>
                                    <p class="text-muted">You don't have any active survey assignments for route planning.</p>
                                    <a href="{{ route('surveyor.assignments') }}" class="btn btn-primary">
                                        <i class="fas fa-tasks me-1"></i>View Assignments
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Map/Route Visualization -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Route Map</h3>
                        </div>
                        <div class="card-body">
                            <div class="bg-light rounded p-4 text-center">
                                <i class="fas fa-map fa-4x text-muted mb-3"></i>
                                <h5>Map Integration</h5>
                                <p class="text-muted">Map visualization would appear here when integrated with mapping services like Google Maps or Mapbox.</p>
                                <div class="mt-3">
                                    <button class="btn btn-outline-primary me-2">
                                        <i class="fas fa-map-marked-alt me-1"></i>Open in Google Maps
                                    </button>
                                    <button class="btn btn-outline-success">
                                        <i class="fas fa-download me-1"></i>Download GPX
                                    </button>
                                </div>
                            </div>

                            <!-- Route Optimization -->
                            <div class="mt-4">
                                <h6>Route Optimization</h6>
                                <div class="btn-group w-100">
                                    <button class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-clock me-1"></i>Fastest Route
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-road me-1"></i>Shortest Distance
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-dollar-sign me-1"></i>Most Efficient
                                    </button>
                                </div>
                            </div>

                            <!-- Route Summary -->
                            <div class="mt-4">
                                <h6>Route Summary</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="text-muted small">Total Distance</div>
                                            <div class="fw-bold">-- km</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="text-muted small">Estimated Time</div>
                                            <div class="fw-bold">-- hrs</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="text-muted small">Stops</div>
                                            <div class="fw-bold">{{ $assignedRoutes->count() }}</div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Print Routes
    document.getElementById('printRoutes').addEventListener('click', function() {
        window.print();
    });

    // Export Routes
    document.getElementById('exportRoutes').addEventListener('click', function() {
        alert('Export functionality would be implemented here.');
    });

    // Sort by Priority
    document.getElementById('sortByPriority').addEventListener('click', function() {
        alert('Sorting by priority would be implemented here.');
    });
});
</script>
@endpush

<style>
@media print {
    .btn, .card-tools, .navbar {
        display: none !important;
    }
}
</style>
@endsection
