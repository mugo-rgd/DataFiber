@extends('layouts.app')

@section('title', 'Edit Maintenance Request')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Maintenance Request #{{ $maintenanceRequest->request_number }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('maintenance.requests.update', $maintenanceRequest->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Customer Selection (Optional) -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Customer (Optional)</label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror"
                                            id="customer_id" name="customer_id">
                                        <option value="">-- Select Customer (Optional) --</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id', $maintenanceRequest->customer_id) == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}
                                                @if($customer->company_name)
                                                    ({{ $customer->company_name }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Commercial Route Selection -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="commercial_route_id" class="form-label">Commercial Route <span class="text-danger">*</span></label>
                                    <select class="form-select route-select @error('commercial_route_id') is-invalid @enderror"
                                            id="commercial_route_id" name="commercial_route_id" required>
                                        <option value="">-- Select Commercial Route --</option>
                                        @php
                                            $groupedRoutes = $routes->groupBy('region');
                                        @endphp
                                        @foreach($groupedRoutes as $region => $regionRoutes)
                                            <optgroup label="━━━━━━━━━━ {{ $region }} ━━━━━━━━━━">
                                                @foreach($regionRoutes as $route)
                                                    <option value="{{ $route->id }}" {{ old('commercial_route_id', $maintenanceRequest->commercial_route_id) == $route->id ? 'selected' : '' }}>
                                                        {{ $route->name_of_route }} ({{ $route->option }}) - {{ number_format($route->approx_distance_km, 2) }} km
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    @error('commercial_route_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select class="form-select @error('priority') is-invalid @enderror"
                                            id="priority" name="priority" required>
                                        <option value="low" {{ old('priority', $maintenanceRequest->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', $maintenanceRequest->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', $maintenanceRequest->priority) == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="critical" {{ old('priority', $maintenanceRequest->priority) == 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="issue_type" class="form-label">Issue Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('issue_type') is-invalid @enderror"
                                            id="issue_type" name="issue_type" required>
                                        <option value="fibre_cut" {{ old('issue_type', $maintenanceRequest->issue_type) == 'fibre_cut' ? 'selected' : '' }}>Fibre Cut</option>
                                        <option value="equipment_failure" {{ old('issue_type', $maintenanceRequest->issue_type) == 'equipment_failure' ? 'selected' : '' }}>Equipment Failure</option>
                                        <option value="signal_degradation" {{ old('issue_type', $maintenanceRequest->issue_type) == 'signal_degradation' ? 'selected' : '' }}>Signal Degradation</option>
                                        <option value="power_issue" {{ old('issue_type', $maintenanceRequest->issue_type) == 'power_issue' ? 'selected' : '' }}>Power Issue</option>
                                        <option value="environmental" {{ old('issue_type', $maintenanceRequest->issue_type) == 'environmental' ? 'selected' : '' }}>Environmental</option>
                                        <option value="preventive_maintenance" {{ old('issue_type', $maintenanceRequest->issue_type) == 'preventive_maintenance' ? 'selected' : '' }}>Preventive Maintenance</option>
                                        <option value="other" {{ old('issue_type', $maintenanceRequest->issue_type) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('issue_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Request Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title', $maintenanceRequest->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Specific Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror"
                                   id="location" name="location" value="{{ old('location', $maintenanceRequest->location) }}"
                                   placeholder="e.g., Near Junction A, Km marker 12">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="number" step="0.000001" class="form-control @error('latitude') is-invalid @enderror"
                                           id="latitude" name="latitude" value="{{ old('latitude', $maintenanceRequest->latitude) }}" placeholder="-1.2921">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="number" step="0.000001" class="form-control @error('longitude') is-invalid @enderror"
                                           id="longitude" name="longitude" value="{{ old('longitude', $maintenanceRequest->longitude) }}" placeholder="36.8219">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="5" required>{{ old('description', $maintenanceRequest->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('maintenance.requests.show', $maintenanceRequest->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>Update Maintenance Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
