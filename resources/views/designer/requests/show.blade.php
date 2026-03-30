@extends('layouts.app')

@section('title', 'Design Request Details - ' . $designRequest->request_number)

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-drafting-compass text-primary"></i>
                        Design Request: {{ $designRequest->request_number }}
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.design-requests.index') }}">Design Requests</a></li>
                            <li class="breadcrumb-item active">{{ $designRequest->request_number }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="btn-group">
                    <button class="btn btn-warning" disabled title="You are not authorized to edit this design request">
        <i class="fas fa-edit me-2"></i>Edit
    </button>
                    <a href="{{ route('designer.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column - Main Details -->
        <div class="col-lg-8">
            <!-- Basic Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Basic Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Request Number:</th>
                                    <td>
                                        <span class="badge bg-dark fs-6">{{ $designRequest->request_number }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Title:</th>
                                    <td><strong>{{ $designRequest->title }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Customer:</th>
                                    <td>
                                        <i class="fas fa-user me-2 text-muted"></i>
                                        {{ $designRequest->customer->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Designer:</th>
                                    <td>
                                        @if($designRequest->designer)
                                            <i class="fas fa-user-tie me-2 text-muted"></i>
                                            {{ $designRequest->designer->name }}
                                        @else
                                            <span class="badge bg-warning">Not Assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Surveyor:</th>
                                    <td>
                                        @if($designRequest->surveyor)
                                            <i class="fas fa-ruler-combined me-2 text-muted"></i>
                                            {{ $designRequest->surveyor->name }}
                                        @else
                                            <span class="badge bg-secondary">Not Required</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Status:</th>
                                    <td>
                                        {{-- <span class="badge bg-{{ getStatusColor($designRequest->status) }} fs-6">
                                            {{ ucfirst($designRequest->status) }}
                                        </span> --}}
                                        <span class="badge bg-@statusColor($designRequest->status) fs-6"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Survey Status:</th>
                                    <td>
                                        {{-- <span class="badge bg-{{ getSurveyStatusColor($designRequest->survey_status) }}">
                                            {{ ucfirst(str_replace('_', ' ', $designRequest->survey_status)) }}
                                        </span> --}}
                                        <span class="badge bg-@statusColor($designRequest->status) fs-6"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Requested:</th>
                                    <td>
                                        <i class="fas fa-calendar me-2 text-muted"></i>
                                        {{ $designRequest->requested_at->format('M d, Y H:i') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Assigned:</th>
                                    <td>
                                        @if($designRequest->assigned_at)
                                            <i class="fas fa-calendar-check me-2 text-muted"></i>
                                            {{ $designRequest->assigned_at->format('M d, Y H:i') }}
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Design Completed:</th>
                                    <td>
                                        @if($designRequest->design_completed_at)
                                            <i class="fas fa-check-circle me-2 text-success"></i>
                                            {{ $designRequest->design_completed_at->format('M d, Y H:i') }}
                                        @else
                                            <span class="text-muted">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description & Requirements -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-align-left me-2"></i>Description
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="card-text">{{ $designRequest->description }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-cogs me-2"></i>Technical Requirements
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="card-text">{{ $designRequest->technical_requirements ?? 'No technical requirements specified' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Route Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-route me-2"></i>Route Information
                    </h5>
                </div>
                <div class="card-body">
                    @if($designRequest->route_points && count($designRequest->route_points) > 0)
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Map-Defined Route</h6>
                                <p><strong>Points:</strong> {{ $designRequest->point_count }}</p>
                                <p><strong>Distance:</strong> {{ $designRequest->total_distance }} km</p>

                                @if($designRequest->route_points)
                                    <button class="btn btn-sm btn-outline-primary" onclick="showRoutePoints()">
                                        <i class="fas fa-list me-1"></i>View Route Points
                                    </button>
                                    <a href="{{ route('admin.design-requests.generate-kml', $designRequest) }}"
                                       class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-download me-1"></i>Download KML
                                    </a>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div id="routeMap" style="height: 200px; background: #f8f9fa; border-radius: 5px;" class="d-flex align-items-center justify-content-center">
                                    <span class="text-muted">Map preview would be here</span>
                                </div>
                            </div>
                        </div>
                    @elseif($designRequest->cores_required || $designRequest->distance)
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Manual Entry Details</h6>
                                @if($designRequest->cores_required)
                                    <p><strong>Cores Required:</strong> {{ $designRequest->cores_required }}</p>
                                @endif
                                @if($designRequest->distance)
                                    <p><strong>Distance:</strong> {{ $designRequest->distance }} km</p>
                                @endif
                                @if($designRequest->technology_type)
                                    <p><strong>Technology:</strong> {{ $designRequest->technology_type }}</p>
                                @endif
                                @if($designRequest->link_class)
                                    <p><strong>Link Class:</strong> {{ $designRequest->link_class }}</p>
                                @endif
                                @if($designRequest->terms)
                                    <p><strong>Terms:</strong> {{ $designRequest->terms }} months</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-route fa-2x mb-2"></i>
                            <p>No route information available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Design Specifications -->
            @if($designRequest->design_specifications || $designRequest->design_notes)
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Design Specifications
                    </h5>
                </div>
                <div class="card-body">
                    @if($designRequest->design_specifications)
                        <h6>Specifications</h6>
                        <p>{{ $designRequest->design_specifications }}</p>
                    @endif

                    @if($designRequest->design_notes)
                        <h6>Design Notes</h6>
                        <p>{{ $designRequest->design_notes }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Actions & Related Information -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <!-- Quotation Actions -->
                        @include('partials.quotation-actions', ['request' => $designRequest])

                        <!-- Assign Designer -->
                        @if($designRequest->status === 'pending')
                            <a href="{{ route('admin.design-requests.assign-designer-form', $designRequest) }}"
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-user-tie me-2"></i>Assign Designer
                            </a>
                        @endif

                        <!-- Assign Surveyor -->
                        @if($designRequest->survey_status === 'not_required' || $designRequest->survey_status === 'requested')
                            <a href="{{ route('admin.design-requests.assign-surveyor-form', $designRequest) }}"
                               class="btn btn-outline-info btn-sm">
                                <i class="fas fa-ruler-combined me-2"></i>Assign Surveyor
                            </a>
                        @endif

                        <!-- Status Update -->
                        <div class="dropdown">
                            <button class="btn btn-outline-warning btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-sync me-2"></i>Update Status
                            </button>
                            <ul class="dropdown-menu">
                                @foreach(['pending', 'assigned', 'in_design', 'designed', 'completed', 'cancelled'] as $status)
                                    @if($designRequest->status !== $status)
                                        <li>
                                            <form action="{{ route('admin.design-requests.update-status', $designRequest) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $status }}">
                                                <button type="submit" class="dropdown-item">
                                                    Mark as {{ ucfirst($status) }}
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quotations Section -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Quotations
                        @if($designRequest->quotations && $designRequest->quotations->count() > 0)
                            <span class="badge bg-light text-dark ms-2">{{ $designRequest->quotations->count() }}</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($designRequest->quotations && $designRequest->quotations->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($designRequest->quotations as $quotation)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $quotation->quotation_number }}</h6>
                                        <small class="text-muted">
                                            ${{ number_format($quotation->total_amount, 2) }} •
                                            <span class="badge bg-{{ $quotation->status === 'sent' ? 'success' : 'warning' }}">
                                                {{ ucfirst($quotation->status) }}
                                            </span>
                                        </small>
                                    </div>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.quotations.show', $quotation) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $quotation)
                                            <a href="{{ route('admin.quotations.edit', $quotation) }}"
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-file-invoice-dollar fa-2x mb-2"></i>
                            <p>No quotations created yet</p>
                        </div>
                    @endif

                    <!-- Create New Quotation Button -->
                    @php
                        $hasQuotations = $designRequest->quotations && $designRequest->quotations->count() > 0;
                        $canGenerateQuote = !$hasQuotations && in_array($designRequest->status, ['assigned', 'designed']);
                    @endphp

                    @if($canGenerateQuote)
                        <div class="mt-3">
                            <a href="{{ route('admin.quotations.create', ['design_request_id' => $designRequest->id]) }}"
                               class="btn btn-success w-100">
                                <i class="fas fa-plus me-2"></i>Create Quotation
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cost Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-dollar-sign me-2"></i>Cost Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        @if($designRequest->estimated_cost)
                        <tr>
                            <th>Estimated Cost:</th>
                            <td class="text-end">${{ number_format($designRequest->estimated_cost, 2) }}</td>
                        </tr>
                        @endif
                        @if($designRequest->quoted_amount)
                        <tr>
                            <th>Quoted Amount:</th>
                            <td class="text-end text-success"><strong>${{ number_format($designRequest->quoted_amount, 2) }}</strong></td>
                        </tr>
                        @endif
                        @if($designRequest->unit_cost && $designRequest->cores_required)
                        <tr>
                            <th>Unit Cost:</th>
                            <td class="text-end">${{ number_format($designRequest->unit_cost, 2) }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Survey Information -->
            @if($designRequest->surveyor || $designRequest->survey_status !== 'not_required')
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-ruler-combined me-2"></i>Survey Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        @if($designRequest->surveyor)
                        <tr>
                            <th>Surveyor:</th>
                            <td>{{ $designRequest->surveyor->name }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge bg-{{ getSurveyStatusColor($designRequest->survey_status) }}">
                                    {{ ucfirst(str_replace('_', ' ', $designRequest->survey_status)) }}
                                </span>
                            </td>
                        </tr>
                        @if($designRequest->survey_requested_at)
                        <tr>
                            <th>Requested:</th>
                            <td>{{ $designRequest->survey_requested_at->format('M d, Y') }}</td>
                        </tr>
                        @endif
                        @if($designRequest->survey_scheduled_at)
                        <tr>
                            <th>Scheduled:</th>
                            <td>{{ $designRequest->survey_scheduled_at->format('M d, Y') }}</td>
                        </tr>
                        @endif
                        @if($designRequest->survey_estimated_hours)
                        <tr>
                            <th>Est. Hours:</th>
                            <td>{{ $designRequest->survey_estimated_hours }}h</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Route Points Modal -->
<div class="modal fade" id="routePointsModal" tabindex="-1" aria-labelledby="routePointsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="routePointsModalLabel">
                    <i class="fas fa-route me-2"></i>Route Points
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($designRequest->route_points)
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($designRequest->route_points as $index => $point)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $point['lat'] }}</td>
                                        <td>{{ $point['lng'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function showRoutePoints() {
    var modal = new bootstrap.Modal(document.getElementById('routePointsModal'));
    modal.show();
}

// Initialize any maps or charts here
document.addEventListener('DOMContentLoaded', function() {
    // You can initialize a map here using Leaflet or Google Maps
    console.log('Design request page loaded');
});
</script>
@endsection

@section('styles')
<style>
.getStatusColor {
    padding: 0.35em 0.65em;
    font-size: 0.75em;
    font-weight: 700;
}

.badge-pending { background-color: #6c757d; }
.badge-assigned { background-color: #fd7e14; }
.badge-in_design { background-color: #0dcaf0; }
.badge-designed { background-color: #20c997; }
.badge-completed { background-color: #198754; }
.badge-cancelled { background-color: #dc3545; }

.badge-not_required { background-color: #6c757d; }
.badge-requested { background-color: #fd7e14; }
.badge-assigned { background-color: #0dcaf0; }
.badge-in_progress { background-color: #ffc107; color: #000; }
.badge-completed { background-color: #198754; }
.badge-failed { background-color: #dc3545; }
</style>
@endsection

@php
// Helper functions for status colors
if (!function_exists('getStatusColor')) {
    function getStatusColor($status) {
        $colors = [
            'pending' => 'secondary',
            'assigned' => 'info',
            'in_design' => 'info',
            'designed' => 'success',
            'completed' => 'primary',
            'draft' => 'info',
            'cancelled' => 'danger'
        ];
        return $colors[$status] ?? 'info';
    }
}

if (!function_exists('getSurveyStatusColor')) {
    function getSurveyStatusColor($status) {
        $colors = [
            'not_required' => 'secondary',
            'requested' => 'warning',
            'assigned' => 'info',
            'in_progress' => 'primary',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'dark'
        ];
        return $colors[$status] ?? 'info';
    }
}
@endphp
