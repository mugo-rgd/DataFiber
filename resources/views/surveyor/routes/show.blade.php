@extends('layouts.app')

@section('title', 'Route Details - ' . $surveyRoute->route_name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Route Details</h1>
        <div class="d-flex">
            <a href="{{ route('surveyor.assignments') }}" class="btn btn-secondary mr-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Assignments
            </a>
            <a href="{{ route('surveyor.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-tachometer-alt fa-sm text-white-50"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Route Overview -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Route Information Card -->
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
                            <p><strong>Route Name:</strong> {{ $surveyRoute->route_name }}</p>
                            <p><strong>Design Request:</strong> {{ $surveyRoute->designRequest->request_number }}</p>
                            <p><strong>Customer:</strong> {{ $surveyRoute->designRequest->customer->name }}</p>
                            <p><strong>Status:</strong>
                                <span class="badge bg-{{ $surveyRoute->status == 'completed' ? 'success' : ($surveyRoute->status == 'in_progress' ? 'info' : 'warning') }}">
                                    {{ ucfirst($surveyRoute->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-primary">Route Statistics</h6>
                            <p><strong>Total Distance:</strong> {{ $surveyRoute->total_distance ?? '0' }} km</p>
                            <p><strong>Estimated Cost:</strong> ${{ number_format($surveyRoute->estimated_cost ?? 0, 2) }}</p>
                            <p><strong>Total Segments:</strong> {{ $surveyRoute->routeSegments->count() }}</p>
                            <p><strong>Created:</strong> {{ $surveyRoute->created_at->format('M d, Y H:i') }}</p>
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
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-map-signs mr-2"></i>
                        Route Segments ({{ $surveyRoute->routeSegments->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($surveyRoute->routeSegments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Segment Name</th>
                                        <th>Installation Type</th>
                                        <th>Distance (km)</th>
                                        <th>Terrain</th>
                                        <th>Complexity</th>
                                        <th>Cost Multiplier</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($surveyRoute->routeSegments as $segment)
                                    <tr>
                                        <td>{{ $segment->segment_number }}</td>
                                        <td>
                                            <strong>{{ $segment->segment_name }}</strong>
                                            @if($segment->challenges)
                                                <br><small class="text-muted">{{ Str::limit($segment->challenges, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark text-capitalize">
                                                {{ $segment->installation_type }}
                                            </span>
                                        </td>
                                        <td>{{ $segment->distance_km }}</td>
                                        <td>{{ $segment->terrain_type }}</td>
                                        <td>
                                            <span class="badge bg-{{ $segment->complexity == 'high' ? 'danger' : ($segment->complexity == 'medium' ? 'warning' : 'success') }}">
                                                {{ ucfirst($segment->complexity) }}
                                            </span>
                                        </td>
                                        <td>{{ $segment->cost_multiplier }}x</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="#" class="btn btn-outline-primary" title="Edit Segment">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" class="btn btn-outline-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-outline-danger" title="Delete Segment">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                @if($surveyRoute->routeSegments->count() > 0)
                                <tfoot>
                                    <tr class="table-active">
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td><strong>{{ $surveyRoute->routeSegments->sum('distance_km') }} km</strong></td>
                                        <td colspan="2"></td>
                                        <td><strong>{{ $surveyRoute->routeSegments->avg('cost_multiplier') }}x avg</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-route fa-4x text-gray-300 mb-3"></i>
                            <h4 class="text-gray-500">No Segments Created</h4>
                            <p class="text-gray-400 mb-4">Start by creating your first route segment to document the infrastructure details.</p>
                            <a href="{{ route('surveyor.route-segments.create', $surveyRoute->id) }}"
                               class="btn btn-primary btn-lg">
                                <i class="fas fa-plus-circle mr-2"></i> Create First Segment
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
                           class="btn btn-success btn-block">
                            <i class="fas fa-plus-circle mr-2"></i> Add New Segment
                        </a>

                        <a href="{{ route('surveyor.assignment.show', $surveyRoute->design_request_id) }}"
                           class="btn btn-info btn-block">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Assignment
                        </a>

                        <button type="button" class="btn btn-primary btn-block" data-bs-toggle="modal" data-bs-target="#updateRouteStatusModal">
                            <i class="fas fa-sync-alt mr-2"></i> Update Route Status
                        </button>

                        <button type="button" class="btn btn-warning btn-block">
                            <i class="fas fa-file-export mr-2"></i> Export Route Data
                        </button>

                        @if($surveyRoute->routeSegments->count() > 0)
                        <button type="button" class="btn btn-secondary btn-block">
                            <i class="fas fa-map mr-2"></i> Generate Route Map
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Route Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Route Statistics
                    </h5>
                </div>
                <div class="card-body">
                    @if($surveyRoute->routeSegments->count() > 0)
                        <div class="mb-3">
                            <strong>Installation Types:</strong>
                            <div class="mt-2">
                                @php
                                    $installationTypes = $surveyRoute->routeSegments->groupBy('installation_type');
                                @endphp
                                @foreach($installationTypes as $type => $segments)
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-capitalize">{{ $type }}</span>
                                    <span class="badge bg-primary">{{ $segments->count() }} segments</span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>Complexity Distribution:</strong>
                            <div class="mt-2">
                                @php
                                    $complexities = $surveyRoute->routeSegments->groupBy('complexity');
                                @endphp
                                @foreach($complexities as $complexity => $segments)
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-capitalize">{{ $complexity }}</span>
                                    <span class="badge bg-{{ $complexity == 'high' ? 'danger' : ($complexity == 'medium' ? 'warning' : 'success') }}">
                                        {{ $segments->count() }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>Infrastructure Summary:</strong>
                            <div class="mt-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Total Poles</span>
                                    <span class="badge bg-secondary">{{ $surveyRoute->routeSegments->sum('pole_count') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Total Manholes</span>
                                    <span class="badge bg-secondary">{{ $surveyRoute->routeSegments->sum('manhole_count') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Total Splices</span>
                                    <span class="badge bg-secondary">{{ $surveyRoute->routeSegments->sum('splice_count') }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                            <p>No statistics available</p>
                            <p class="small">Add segments to see route statistics</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history mr-2"></i>
                        Recent Activity
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item active">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="font-weight-bold">Route Created</h6>
                                <small class="text-muted">{{ $surveyRoute->created_at->format('M d, Y H:i') }}</small>
                            </div>
                        </div>

                        @if($surveyRoute->routeSegments->count() > 0)
                        <div class="timeline-item active">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="font-weight-bold">First Segment Added</h6>
                                <small class="text-muted">{{ $surveyRoute->routeSegments->first()->created_at->format('M d, Y H:i') }}</small>
                            </div>
                        </div>

                        <div class="timeline-item active">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="font-weight-bold">Last Updated</h6>
                                <small class="text-muted">{{ $surveyRoute->updated_at->format('M d, Y H:i') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Route Status Modal -->
<div class="modal fade" id="updateRouteStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Route Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('surveyor.routes.update-status', $surveyRoute->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="route_status" class="form-label">Route Status</label>
                        <select class="form-control" id="route_status" name="status" required>
                            <option value="draft" {{ $surveyRoute->status == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="in_progress" {{ $surveyRoute->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $surveyRoute->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="approved" {{ $surveyRoute->status == 'approved' ? 'selected' : '' }}>Approved</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status_notes" class="form-label">Status Notes (Optional)</label>
                        <textarea class="form-control" id="status_notes" name="status_notes" rows="3" placeholder="Add any notes about the route status..."></textarea>
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
@endsection

@push('styles')
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
    .table th {
        border-top: none;
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialize modals
    document.addEventListener('DOMContentLoaded', function() {
        const updateRouteStatusModal = new bootstrap.Modal(document.getElementById('updateRouteStatusModal'));
    });

    // Auto-calculate route totals when segments are added/updated
    function updateRouteTotals() {
        // This would typically be handled server-side
        console.log('Route totals would be updated here');
    }
</script>
@endpush
