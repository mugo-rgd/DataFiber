@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <!-- Toast Notifications Container -->
    <div class="toast-container" id="toast-container"></div>

    <!-- Header with Back Button -->
    <div class="header-actions">
        <div>
            <h1 class="h3 text-gray-800 mb-2">
                <i class="fas fa-drafting-compass text-primary me-2"></i> New Fibre Route Design Request
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('customer.index') }}" class="text-decoration-none"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customer.design-requests.index') }}" class="text-decoration-none"><i class="fas fa-list me-1"></i>Design Requests</a></li>
                    <li class="breadcrumb-item active text-primary"><i class="fas fa-plus-circle me-1"></i>New Request</li>
                </ol>
            </nav>
        </div>
        <!-- Go Back Button -->
        <button type="button" class="btn btn-outline-secondary" onclick="goBack()">
            <i class="fas fa-arrow-left me-2"></i>Go Back
        </button>
    </div>

    <!-- Progress Indicator -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="progress-indicator">
                <div class="step active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Basic Info</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Route Details</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Services</div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-circle">4</div>
                    <div class="step-label">Review & Submit</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-plus-circle text-success me-2"></i> Create New Fibre Route Design Request</h5>
                    <div class="form-text text-muted">Fields marked with <span class="text-danger">*</span> are required</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.design-requests.store') }}" method="POST" id="design-request-form">
                        @csrf

                        <!-- Step 1: Basic Information -->
                        <div class="form-step active" id="step-1">
                            <div class="step-header mb-4">
                                <h4 class="text-primary mb-2"><i class="fas fa-info-circle me-2"></i>Basic Information</h4>
                                <p class="text-muted">Let's start with the basic details of your fibre route request</p>
                            </div>

                            <div class="mb-4">
                                <label for="title" class="form-label fw-bold">Request Title / Route Name (Point of Service Uptake) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" required
                                           placeholder="e.g., Nairobi CBD to Industrial Area Fibre Connection"
                                           value="{{ old('title') }}">
                                </div>
                                @error('title')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                               <div class="form-text">
            <small>This will be used as both the request title and route identifier</small>
        </div>
                            </div>
                                <input type="hidden" name="route_name" id="route_name" value="{{ old('route_name', old('title')) }}">

                            <div class="mb-4">
    <label for="description" class="form-label fw-bold">Project Description <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text"><i class="fas fa-file-alt"></i></span>
        <textarea class="form-control no-uppercase @error('description') is-invalid @enderror" id="description" name="description" rows="4" required
                  placeholder="Describe your fibre route requirements, purpose, and objectives...">{{ old('description') }}</textarea>
    </div>
    @error('description')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    <div class="form-text">
        <small>Describe what you need this fibre route for, your business objectives, and any specific requirements</small>
    </div>
</div>

                            <div class="mb-4">
   <label for="technical_requirements" class="form-label fw-bold">Technical Requirements <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text"><i class="fas fa-cogs"></i></span>
        <textarea class="form-control no-uppercase @error('technical_requirements') is-invalid @enderror" id="technical_requirements" name="technical_requirements" rows="5" required
                  placeholder="Specify your technical requirements including bandwidth, OPGW, ADSS...">{{ old('technical_requirements') }}</textarea>
    </div>
    @error('technical_requirements')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    <div class="form-text">
        <small>Include: Redundancy, required bandwidth, cores required, Diverse routing, Access points, Equipment space, and any special technical requirements</small>
    </div>
</div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" disabled>
                                    <i class="fas fa-arrow-left me-2"></i>Previous
                                </button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                                    Next <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Route Information -->
                        <div class="form-step" id="step-2">
                            <div class="step-header mb-4">
                                <h4 class="text-primary mb-2"><i class="fas fa-route me-2"></i>Route Information</h4>
                                <p class="text-muted">Define your fibre route using the map or enter details manually</p>
                            </div>

                            <!-- Hidden inputs for start/end points -->
                            <input type="hidden" name="start_point" id="start-point-input" value="">
                            <input type="hidden" name="end_point" id="end-point-input" value="">

                            <div class="instructions bg-light rounded p-3 mb-4">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-info-circle text-primary mt-1 me-3"></i>
                                    <div>
                                        <strong class="d-block mb-2">How to define your route:</strong>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge bg-primary me-2">Option 1</span>
                                                    <span>Click on the map to add route points (recommended)</span>
                                                </div>
                                                <ul class="small text-muted ps-3">
                                                    <li>Click "Add Points Mode" to start adding points</li>
                                                    <li>Click anywhere on the map to add route points</li>
                                                    <li>Add at least 2 points to define a route</li>
                                                    <li>Distance is automatically calculated</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge bg-secondary me-2">Option 2</span>
                                                    <span>Enter route details manually</span>
                                                </div>
                                                <ul class="small text-muted ps-3">
                                                    <li>Fill in the cores required, distance, and terms</li>
                                                    <li>Use this if you already know the route specifications</li>
                                                    <li>All manual fields are required if using this method</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Manual Entry Section -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-keyboard me-2"></i>Manual Route Entry
                                        <small class="text-muted">(Alternative to map entry)</small>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="cores_required" class="form-label">Cores Required</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-network-wired"></i></span>
                                                <input type="number" class="form-control @error('cores_required') is-invalid @enderror"
                                                       id="cores_required" name="cores_required"
                                                       value="{{ old('cores_required') }}"
                                                       placeholder="e.g., 24"
                                                       min="1" max="144">
                                            </div>
                                            @error('cores_required')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text small">Number of fibre cores needed (typically 1,2,,12, 24, 48, 72, 144)</div>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="distance" class="form-label">Distance (km)</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-road"></i></span>
                                                <input type="number" step="0.01" class="form-control @error('distance') is-invalid @enderror"
                                                       id="distance" name="distance"
                                                       value="{{ old('distance') }}"
                                                       placeholder="e.g., 5.2"
                                                       min="0.1" max="1000">
                                            </div>
                                            @error('distance')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text small">Total route distance in kilometers</div>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="terms" class="form-label">Contract Terms</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                <input type="text" class="form-control @error('terms') is-invalid @enderror"
                                                       id="terms" name="terms"
                                                       value="{{ old('terms') }}"
                                                       placeholder="e.g., 12 months">
                                            </div>
                                            @error('terms')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text small">Preferred contract duration (months)</div>
                                        </div>
                                    </div>

                                    <!-- Technology Details -->
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label for="technology_type" class="form-label">Technology Type</label>
                                            <select class="form-select @error('technology_type') is-invalid @enderror"
                                                   id="technology_type" name="technology_type">
                                                <option value="">Select Technology...</option>
                                                <option value="OPGW" {{ old('technology_type') == 'OPGW' ? 'selected' : '' }}>OPGW (Optical Ground Wire)</option>
                                                <option value="ADSS" {{ old('technology_type') == 'ADSS' ? 'selected' : '' }}>ADSS (All-Dielectric Self-Supporting Cable)</option>
                                                <option value="Other" {{ old('technology_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('technology_type')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="link_class" class="form-label">Link Class</label>
                                            <select class="form-select @error('link_class') is-invalid @enderror"
                                                   id="link_class" name="link_class" disabled>
                                                <option value="">Select Link Class...</option>
                                                <option value="Enterprise" {{ old('link_class') == 'Enterprise' ? 'selected' : '' }}>Unknown</option>
                                                <option value="Carrier" {{ old('link_class') == 'Carrier' ? 'selected' : '' }}>None Premium</option>
                                                <option value="Standard" {{ old('link_class') == 'Standard' ? 'selected' : '' }}>Metro</option>
                                                <option value="Economy" {{ old('link_class') == 'Economy' ? 'selected' : '' }}>Premium</option>
                                            </select>
                                            <input type="hidden" name="link_class" value="Unknown">
                                            @error('link_class')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="prevStep(2)">
                                    <i class="fas fa-arrow-left me-2"></i>Previous
                                </button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(4)">
                                    Next <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                            </div>

                            <!-- Map Section -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label mb-0">Define Fibre Route on Map</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="toggle-map-help" checked>
                                        <label class="form-check-label small" for="toggle-map-help">Show Help</label>
                                    </div>
                                </div>

                                <div class="alert alert-warning validation-alert" id="route-validation-alert" style="display: none;">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <span id="validation-message">Please either define your route on the map OR enter route details manually above.</span>
                                </div>

                                <div class="map-container mb-3">
                                    <div id="google-map" role="application" aria-label="Interactive map for fibre route planning" tabindex="0"></div>

                                    <!-- Loading Overlay -->
                                    <div class="loading-overlay" id="loading-overlay">
                                        <div class="text-center">
                                            <div class="spinner-border text-primary mb-2" role="status">
                                                <span class="visually-hidden">Loading map...</span>
                                            </div>
                                            <p class="mb-2">Loading Google Maps...</p>
                                            <div class="progress" style="width: 200px; height: 4px;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                     style="width: 100%"></div>
                                            </div>
                                            <small class="text-muted mt-2">If this takes too long, please refresh the page</small>
                                        </div>
                                    </div>

                                    <!-- Map Help Box -->
                                    <div id="mapHelp" class="map-help-content">
                                        <div class="map-help-header d-flex justify-content-between align-items-center mb-2" style="cursor: move;">
                                            <h6 class="mb-0"><i class="fas fa-lightbulb me-2 text-warning"></i>Quick Start Guide</h6>
                                            <button type="button" class="btn-close" onclick="closeMapHelp()" aria-label="Close"></button>
                                        </div>
                                        <ul class="small mb-2">
                                            <li>Click "Add Points Mode" to start placing points</li>
                                            <li>Click anywhere on the map to add route points</li>
                                            <li>Add at least 2 points to see the route and distance</li>
                                            <li>Click on substations (red markers) for connection options</li>
                                        </ul>
                                        <button class="btn btn-sm btn-outline-primary w-100" onclick="closeMapHelp()">
                                            Got it, let's start!
                                        </button>
                                    </div>

                                    <!-- Map Overlay Controls -->
                                    <div class="map-overlay">
                                        <div class="route-summary-item">
                                            <span>Distance:</span>
                                            <span id="route-distance-display" class="text-primary fw-bold">0 km</span>
                                        </div>
                                        <div class="btn-group-vertical">
                                            <button type="button" class="btn btn-sm btn-primary btn-map-control" id="add-point-mode">
                                                <i class="fas fa-map-marker-alt me-1"></i>Add Points Mode
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-map-control" id="clear-route">
                                                <i class="fas fa-trash-alt me-1"></i>Clear Route
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info btn-map-control" id="reset-view">
                                                <i class="fas fa-globe-africa me-1"></i>Reset View
                                            </button>
                                        </div>

                                        <div class="legend mt-3">
                                            <h6 class="mb-2"><i class="fas fa-list me-1"></i>Legend</h6>
                                            <div class="legend-item">
                                                <div class="legend-color" style="background-color: #e74a3b;"></div>
                                                <span class="small">Substation (Fibre Available)</span>
                                            </div>
                                            <div class="legend-item">
                                                <div class="legend-color" style="background-color: #95a5a6;"></div>
                                                <span class="small">Substation (No Fibre)</span>
                                            </div>
                                            <div class="legend-item">
                                                <div class="legend-color" style="background-color: #28a745;"></div>
                                                <span class="small">Start Point</span>
                                            </div>
                                            <div class="legend-item">
                                                <div class="legend-color" style="background-color: #dc3545;"></div>
                                                <span class="small">End Point</span>
                                            </div>
                                            <div class="legend-item">
                                                <div class="legend-color route-marker"></div>
                                                <span class="small">Route Point</span>
                                            </div>
                                            <div class="legend-item">
                                                <div class="legend-color" style="background-color: #4e73df;"></div>
                                                <span class="small">Route Line</span>
                                            </div>
                                        </div>

                                        <!-- Route Summary Panel -->
                                        <div class="route-summary-panel" id="route-summary-panel">
                                            <h6><i class="fas fa-route me-2"></i>Route Summary</h6>
                                            <div class="route-summary-item">
                                                <span>Start Point:</span>
                                                <span id="start-point-display" class="text-muted">Not set</span>
                                            </div>
                                            <div class="route-summary-item">
                                                <span>End Point:</span>
                                                <span id="end-point-display" class="text-muted">Not set</span>
                                            </div>
                                            <div class="route-summary-item">
                                                <span>Distance:</span>
                                                <span id="route-distance-display" class="text-primary fw-bold">0 km</span>
                                            </div>
                                            <div class="route-summary-item">
                                                <span>Points:</span>
                                                <span id="route-points-count" class="badge bg-primary">0</span>
                                            </div>
                                        </div>

                                        <!-- Dark Fibre Controls -->
                                        <div class="dark-fibre-control mt-3">
                                            <div class="btn-group-vertical">
                                                <button type="button" class="btn btn-sm btn-dark btn-map-control" id="set-start-point">
                                                    <i class="fas fa-play-circle me-1"></i>Set Start Point
                                                </button>
                                                <button type="button" class="btn btn-sm btn-dark btn-map-control" id="set-end-point">
                                                    <i class="fas fa-flag-checkered me-1"></i>Set End Point
                                                </button>
                                            </div>
                                        </div>

                                        <div class="substation-filter mt-3">
                                            <h6 class="mb-2"><i class="fas fa-filter me-1"></i>Filter Substations</h6>
                                            <div id="owner-filters" class="d-flex flex-wrap gap-1">
                                                <!-- Owner filters will be dynamically added here -->
                                            </div>
                                        </div>

                                        <div class="substation-search mt-3">
                                            <h6 class="mb-2"><i class="fas fa-search me-1"></i>Search Stations</h6>
                                            <div class="input-group input-group-sm">
                                                <input type="text"
                                                       class="form-control"
                                                       id="station-search"
                                                       placeholder="Search by name, area..."
                                                       aria-label="Search stations">
                                                <button class="btn btn-outline-secondary" type="button" id="clear-search">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="status-filter mt-3">
                                            <h6 class="mb-2"><i class="fas fa-filter me-1"></i>Filter by Status</h6>
                                            <div class="d-flex flex-wrap gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-success status-filter-btn" data-status="Available">
                                                    Available
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning status-filter-btn" data-status="Under Maintenance">
                                                    Maintenance
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary status-filter-btn" data-status="Unavailable">
                                                    Unavailable
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="show-all-status">
                                                    All
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="toggle-controls">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="toggle-substations" checked>
                                            <label class="form-check-label small" for="toggle-substations">Show Substations</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="toggle-satellite">
                                            <label class="form-check-label small" for="toggle-satellite">Satellite View</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Route Points List -->
                                <div class="card">
                                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0"><i class="fas fa-map-marker-alt me-1"></i>Route Points</h6>
                                        <span class="badge bg-primary" id="point-count">0 points</span>
                                    </div>

                                    <div class="distance-display" id="total-distance">
                                        <i class="fas fa-ruler-combined me-2"></i>
                                        <span id="distance-value">0 km</span>
                                        <small class="d-block" id="distance-label">No route defined</small>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="point-list" id="point-list">
                                            <div class="point-item text-muted text-center py-4">
                                                <i class="fas fa-map-marker-alt fa-2x mb-2 d-block"></i>
                                                No points added yet<br>
                                                <small class="text-muted">Click on the map to add your first point</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @error('route_points')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                                                        <!-- Hidden input to store route points -->
                            <input type="hidden" name="route_points" id="route-points-input" value="{{ old('route_points') }}">

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="prevStep(1)">
                                    <i class="fas fa-arrow-left me-2"></i>Previous
                                </button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                                    Next <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Colocation Services -->
                        <div class="form-step" id="step-3">
                            <div class="step-header mb-4">
                                <h4 class="text-primary mb-2"><i class="fas fa-server me-2"></i>Colocation Services</h4>
                                <p class="text-muted">Add your colocation site requirements and select any additional services (optional)</p>
                            </div>

                            <!-- Dynamic Colocation Sites Table -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-data-center me-2"></i>Colocation Sites</h6>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="addNewSite()">
                                        <i class="fas fa-plus me-1"></i>Add Site
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="colocation-sites-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="50px">#</th>
                                                    <th>Site Name <span class="text-danger">*</span></th>
                                                    <th>Service Type <span class="text-danger">*</span></th>
                                                    <th width="120px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sites-table-body">
                                                @if(old('colocation_sites') && count(old('colocation_sites')) > 0)
                                                    @foreach(old('colocation_sites') as $index => $site)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm site-name-input"
                                                                   name="colocation_sites[{{ $index }}][site_name]"
                                                                   value="{{ $site['site_name'] ?? '' }}"
                                                                   placeholder="Enter site name" required>
                                                        </td>
                                                        <td>
                                                            <select class="form-select form-control-sm service-type-select"
                                                                    name="colocation_sites[{{ $index }}][service_type]" required>
                                                                <option value="">Select Service Type...</option>
                                                                <option value="shelter_space" {{ ($site['service_type'] ?? '') == 'shelter_space' ? 'selected' : '' }}>Shelter Space</option>
                                                                <option value="rack" {{ ($site['service_type'] ?? '') == 'rack' ? 'selected' : '' }}>Rack</option>
                                                                <option value="cage" {{ ($site['service_type'] ?? '') == 'cage' ? 'selected' : '' }}>Cage</option>
                                                                <option value="suites" {{ ($site['service_type'] ?? '') == 'suites' ? 'selected' : '' }}>Suites</option>
                                                            </select>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSite(this)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr id="no-sites-row">
                                                        <td colspan="4" class="text-center text-muted py-4">
                                                            <i class="fas fa-plus-circle fa-2x mb-3"></i>
                                                            <div>No colocation sites added yet</div>
                                                            <small>Click "Add Site" to start adding your colocation requirements</small>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mb-4">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-info-circle mt-1 me-3"></i>
                                    <div>
                                        <strong>Colocation Services</strong><br>
                                        Add the sites where you need colocation services and specify whether you need shelter space, racks, or other services. You can add multiple sites as needed.
                                    </div>
                                </div>
                            </div>

                            <!-- Service Categories -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Additional Services by Category</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="category-filters">
                                        @foreach($serviceCategories as $category)
                                        <div class="col-md-3 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input category-filter" type="checkbox" value="{{ $category }}" id="filter-{{ Str::slug($category) }}">
                                                <label class="form-check-label" for="filter-{{ Str::slug($category) }}">
                                                    {{ $category }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Available Services -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Available Additional Services</h6>
                                    <span class="badge bg-primary" id="services-count">{{ count($colocationServices) }} services available</span>
                                </div>
                                <div class="row" id="services-grid">
                                    @foreach($colocationServices as $service)
                                    <div class="col-md-6 col-lg-4 mb-3 service-item" data-category="{{ $service->service_category }}">
                                        <div class="card service-card h-100" onclick="toggleService('{{ $service->service_id }}')">
                                            <div class="card-body position-relative">
                                                <div class="form-check service-checkbox">
                                                    <input type="checkbox" class="form-check-input service-checkbox-input"
                                                           id="service-{{ $service->service_id }}"
                                                           name="colocation_services[]"
                                                           value="{{ $service->service_id }}"
                                                           data-price="{{ $service->monthly_price_usd }}"
                                                           data-name="{{ $service->service_type }}"
                                                           data-category="{{ $service->service_category }}"
                                                           {{ in_array($service->service_id, old('colocation_services', [])) ? 'checked' : '' }}>
                                                </div>

                                                <h6 class="card-title mb-2">{{ $service->service_type }}</h6>
                                                <span class="badge service-category-badge bg-primary mb-2">{{ $service->service_category }}</span>

                                                <div class="service-specs mb-2">
                                                    <small class="text-muted">{{ $service->specifications }}</small>
                                                </div>

                                                @if($service->power_kw)
                                                <div class="mb-1">
                                                    <small><i class="fas fa-bolt text-warning me-1"></i><strong>Power:</strong> {{ $service->power_kw }} kW</small>
                                                </div>
                                                @endif

                                                @if($service->space_sqm)
                                                <div class="mb-1">
                                                    <small><i class="fas fa-arrows-alt text-info me-1"></i><strong>Space:</strong> {{ $service->space_sqm }} m²</small>
                                                </div>
                                                @endif

                                                <div class="service-price mb-2 text-success">
                                                    <i class="fas fa-dollar-sign me-1"></i>{{ number_format($service->monthly_price_usd, 2) }}/month
                                                </div>

                                                <div class="text-muted small">
                                                    <i class="fas fa-tools me-1"></i>Setup: ${{ number_format($service->setup_fee_usd, 2) }} •
                                                    <i class="fas fa-clock me-1"></i>Min: {{ $service->min_contract_months }} months
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- No Services Message -->
                                <div id="no-services-message" class="text-center py-5" style="display: none;">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No services found</h5>
                                    <p class="text-muted">Try adjusting your category filters</p>
                                </div>
                            </div>

                            <!-- Selected Services Summary -->
                            <div class="card mt-4" id="selected-services-card" style="display: none;">
                                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Selected Additional Services</h6>
                                    <span class="badge bg-light text-dark" id="selected-count">0 services</span>
                                </div>
                                <div class="card-body">
                                    <div class="selected-services-list mb-3" id="selected-services-list">
                                        <!-- Selected services will be listed here -->
                                    </div>
                                    <div class="total-cost text-center" id="total-cost">
                                        Total Monthly Cost: $0.00
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="prevStep(2)">
                                    <i class="fas fa-arrow-left me-2"></i>Previous
                                </button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(4)">
                                    Next <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 4: Review & Submit -->
                        <div class="form-step" id="step-4">
                            <div class="step-header mb-4">
                                <h4 class="text-primary mb-2"><i class="fas fa-clipboard-check me-2"></i>Review & Submit</h4>
                                <p class="text-muted">Review your information before submitting the request</p>
                            </div>

                            <!-- Summary Cards -->
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                <strong>Title:</strong><br>
                                                <span id="review-title" class="text-muted">-</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Description:</strong><br>
                                                <span id="review-description" class="text-muted">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="fas fa-route me-2"></i>Route Details</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                <strong>Route Type:</strong><br>
                                                <span id="review-route-type" class="text-muted">-</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Distance:</strong><br>
                                                <span id="review-distance" class="text-muted">-</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Cores Required:</strong><br>
                                                <span id="review-cores" class="text-muted">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Technical Details</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                <strong>Technology:</strong><br>
                                                <span id="review-technology" class="text-muted">-</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Link Class:</strong><br>
                                                <span id="review-link-class" class="text-muted">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0"><i class="fas fa-server me-2"></i>Colocation Sites & Additional Services</h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Colocation Sites Review -->
                                            <div class="mb-3">
                                                <h6 class="text-primary mb-2">Colocation Sites:</h6>
                                                <div id="review-colocation-sites" class="text-muted">
                                                    No colocation sites added
                                                </div>
                                            </div>

                                            <hr>

                                            <!-- Additional Services Review -->
                                            <div>
                                                <h6 class="text-primary mb-2">Additional Services:</h6>
                                                <div id="review-services" class="text-muted">
                                                    No additional services selected
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Final Validation -->
                            <div class="alert alert-warning" id="final-validation" style="display: none;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <span id="final-validation-message"></span>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="prevStep(3)">
                                    <i class="fas fa-arrow-left me-2"></i>Previous
                                </button>
                                <button type="submit" class="btn btn-success" id="submit-btn">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Request
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Fibre Stations Data --}}
@php
    $fibreStationsArray = $fibreStations->map(function($station) {
        return [
            'id' => $station->id ?? null,
            'lat' => isset($station->lat) ? (float) $station->lat : null,
            'lng' => isset($station->lng) ? (float) $station->lng : null,
            'name' => $station->name ?? '',
            'owner' => $station->owner ?? '',
            'hasFibre' => ($station->fibre_status ?? $station->fibreStatus ?? '') === 'Available',
            'capacity' => $station->capacity ?? 'N/A',
            'status' => $station->fibre_status ?? $station->fibreStatus ?? '',
            'fibreCores' => $station->darkFibreCores ?? 0,
            'fibreOwner' => $station->owner ?? '',
            'connectionType' => $station->connectionType ?? '',
            'area' => $station->area ?? '',
            'location' => $station->location ?? ''
        ];
    })->toArray();
@endphp

<input type="hidden" id="fibreStationsData" value="{{ json_encode($fibreStationsArray, JSON_HEX_APOS | JSON_HEX_QUOT) }}">
<!-- Additional CSS -->
<style>

    :root {
    --primary: #4e73df;
    --success: #1cc88a;
    --info: #36b9cc;
    --warning: #f6c23e;
    --danger: #e74a3b;
    --secondary: #858796;
}
    /* Progress Indicator */
    .progress-indicator {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        margin: 0 50px;
    }

    .progress-indicator::before {
        content: '';
        position: absolute;
        top: 15px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #e9ecef;
        z-index: 1;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .step-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-bottom: 8px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .step.active .step-circle {
        background-color: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .step.completed .step-circle {
        background-color: var(--success);
        color: white;
        border-color: var(--success);
    }

    .step-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
    }

    .step.active .step-label {
        color: var(--primary);
    }

    /* Form Steps */
    .form-step {
        display: none;
    }

    .form-step.active {
        display: block;
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .step-header {
        border-bottom: 2px solid var(--primary);
        padding-bottom: 1rem;
    }

    /* Map Container Styles */
    .map-container {
        position: relative;
        height: 500px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e3e6f0;
    }

    #google-map {
        height: 100%;
        width: 100%;
        position: relative;
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1100;
    }

    .no-uppercase {
    text-transform: none !important;
}

    /* Map Help Content */
    .map-help-content {
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    width: 300px;
    max-width: 90vw;
    cursor: default;
    position: absolute;
    top: 20px;
    left: 20px; /* Map Help stays top-left */
    z-index: 1000;
    display: block !important;
}

    .map-help-header {
        user-select: none;
        padding-bottom: 8px;
        border-bottom: 1px solid #eee;
        cursor: move;
    }

    /* Map Overlay Controls */
    .map-overlay {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 900;
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        max-width: 250px;
    }

    .distance-display {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 15px;
        border-radius: 8px;
        font-weight: bold;
        margin-bottom: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        text-align: center;
    }

    .distance-display #distance-value {
        font-size: 1.5rem;
        display: block;
        margin-bottom: 5px;
    }

    .distance-display #distance-label {
        font-size: 0.8rem;
        opacity: 0.9;
        font-weight: normal;
    }

    .btn-map-control {
        margin-bottom: 5px;
        font-size: 0.8rem;
    }

    .toggle-controls {
        position: absolute;
        bottom: 20px;
        left: 20px;
        z-index: 900;
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .legend {
        font-size: 0.75rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 4px;
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        margin-right: 8px;
        border: 1px solid #ddd;
    }

    /* Route Summary Panel */
   .route-summary-panel {
    position: absolute;
    bottom: 100px; /* Position above toggle controls */
    left: 20px; /* Align left with other controls */
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 900;
    max-width: 250px;
    cursor: move; /* Make it draggable */
    user-select: none; /* Prevent text selection when dragging */
}

.route-summary-panel.dragging {
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    opacity: 0.9;
}

    .route-summary-panel h6 {
        color: #4e73df;
        border-bottom: 2px solid #4e73df;
        padding-bottom: 5px;
    }

    .route-summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #e3e6f0;
    }

    .route-summary-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    /* Service Cards */
    .service-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
        cursor: pointer;
    }

    .service-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .service-card.selected {
        border-color: var(--primary);
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    }

    .service-checkbox {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    /* Point List Styles */
    .point-list {
        max-height: 200px;
        overflow-y: auto;
    }

    .point-item {
        border-bottom: 1px solid #e3e6f0;
        padding: 10px 15px;
    }

    .point-item:last-child {
        border-bottom: none;
    }

    /* Toast Notifications */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 350px;
    }

    .toast {
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .toast.info {
        background: linear-gradient(135deg, #e3f2fd 0%, #ffffff 100%);
        border-left: 4px solid #2196f3;
    }

    .toast.success {
        background: linear-gradient(135deg, #e8f5e9 0%, #ffffff 100%);
        border-left: 4px solid #4caf50;
    }

    .toast.warning {
        background: linear-gradient(135deg, #fff3e0 0%, #ffffff 100%);
        border-left: 4px solid #ff9800;
    }

    .toast.error {
        background: linear-gradient(135deg, #ffebee 0%, #ffffff 100%);
        border-left: 4px solid #f44336;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
    .progress-indicator {
        margin: 0;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
    }

    .progress-indicator::before {
        display: none;
    }

    .step {
        flex: 0 0 auto;
        margin: 5px;
    }
}

    /* Dark Fibre Control */
    .dark-fibre-control {
        margin-top: 15px;
    }

    /* Loading Animation */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .start-point-indicator {
        background-color: #28a745 !important;
        animation: pulse 2s infinite;
    }

    .end-point-indicator {
        background-color: #dc3545 !important;
        animation: pulse 2s infinite;
    }
</style>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Main JavaScript -->
<script>
//////////////////////
// Global variables
////////////////////
let siteCounter = {{ old('colocation_sites') ? count(old('colocation_sites')) : 0 }};
let stepManager;
let servicesManager;
let mapManager;
let map;
let isSubmitting = false;

////////////////////
// Toast Notification System
////////////////////
class ToastManager {
    static show(message, type = 'info', duration = 5000) {
        const toastContainer = document.getElementById('toast-container');
        const toastId = 'toast-' + Date.now();

        const toast = document.createElement('div');
        toast.className = `toast ${type} mb-2`;
        toast.id = toastId;
        toast.innerHTML = `
            <div class="toast-body d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-${this.getIcon(type)} me-3 text-${this.getColor(type)}"></i>
                    <span>${message}</span>
                </div>
                <button type="button" class="btn-close" onclick="this.closest('.toast').remove()"></button>
            </div>
        `;

        toastContainer.appendChild(toast);

        setTimeout(() => {
            if (document.getElementById(toastId)) {
                document.getElementById(toastId).remove();
            }
        }, duration);
    }

    static getIcon(type) {
        const icons = {
            'info': 'info-circle',
            'success': 'check-circle',
            'warning': 'exclamation-triangle',
            'error': 'exclamation-circle'
        };
        return icons[type] || 'info-circle';
    }

    static getColor(type) {
        const colors = {
            'info': 'primary',
            'success': 'success',
            'warning': 'warning',
            'error': 'danger'
        };
        return colors[type] || 'primary';
    }
}

////////////////////
// Step Navigation System
////////////////////
class StepManager {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 4;
        this.init();
    }

    init() {
        this.showStep(1);
        this.updateProgress();
    }

    showStep(stepNumber) {
        document.querySelectorAll('.form-step').forEach(step => {
            step.classList.remove('active');
        });

        const currentStepElement = document.getElementById(`step-${stepNumber}`);
        if (currentStepElement) {
            currentStepElement.classList.add('active');
            this.currentStep = stepNumber;
            this.updateProgress();

            // Update review summary when reaching step 4
            if (stepNumber === 4) {
                this.updateReviewSummary();
            }
        }
    }

    updateProgress() {
        document.querySelectorAll('.step').forEach((step, index) => {
            const stepNumber = index + 1;
            step.classList.remove('active', 'completed');

            if (stepNumber === this.currentStep) {
                step.classList.add('active');
            } else if (stepNumber < this.currentStep) {
                step.classList.add('completed');
            }
        });
    }

    validateStep(stepNumber) {
        switch (stepNumber) {
            case 1:
                return this.validateStep1();
            case 2:
                return this.validateStep2();
            case 3:
                return true;
            case 4:
                return this.validateStep4();
            default:
                return true;
        }
    }

    validateStep1() {
        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const technicalRequirements = document.getElementById('technical_requirements').value.trim();

        if (!title) {
            ToastManager.show('Please enter a request title', 'error');
            document.getElementById('title').focus();
            return false;
        }

        // Auto-copy title to route_name (hidden field)
document.getElementById('title').addEventListener('input', function() {
    document.getElementById('route_name').value = this.value;
});

// Also copy on form submit for safety
document.getElementById('design-request-form').addEventListener('submit', function() {
    const title = document.getElementById('title').value;
    document.getElementById('route_name').value = title;
});

        if (!description) {
            ToastManager.show('Please enter a project description', 'error');
            document.getElementById('description').focus();
            return false;
        }

        if (!technicalRequirements) {
            ToastManager.show('Please enter technical requirements', 'error');
            document.getElementById('technical_requirements').focus();
            return false;
        }

        return true;
    }

    validateStep2() {
        const hasMapRoute = mapManager && mapManager.points && mapManager.points.length >= 2;

        // Check for manual entry
        const distance = document.getElementById('distance').value.trim();
        const cores = document.getElementById('cores_required').value.trim();
        const terms = document.getElementById('terms').value.trim();
        const hasManualEntry = distance && cores && terms;

        // Show specific validation messages
        if (!hasMapRoute && !hasManualEntry) {
            const validationAlert = document.getElementById('route-validation-alert');
            const validationMessage = document.getElementById('validation-message');

            if (validationAlert && validationMessage) {
                validationMessage.textContent = 'Please either define your route on the map OR enter route details manually above.';
                validationAlert.style.display = 'block';

                // Scroll to the alert
                validationAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            ToastManager.show(
                'Please either define your route on the map OR fill all manual route details',
                'error'
            );
            return false;
        }

        const validationAlert = document.getElementById('route-validation-alert');
        if (validationAlert) {
            validationAlert.style.display = 'none';
        }

        // If using map route, ensure we have at least 2 points
        if (hasMapRoute && mapManager.points.length < 2) {
            ToastManager.show('Please add at least 2 points on the map', 'error');
            return false;
        }

        // If using manual entry, ensure all fields are valid
        if (hasManualEntry) {
            const distanceNum = parseFloat(distance);
            const coresNum = parseInt(cores);

            if (isNaN(distanceNum) || distanceNum <= 0) {
                ToastManager.show('Please enter a valid distance (greater than 0)', 'error');
                return false;
            }

            if (isNaN(coresNum) || coresNum <= 0) {
                ToastManager.show('Please enter a valid number of cores (greater than 0)', 'error');
                return false;
            }

            if (!terms) {
                ToastManager.show('Please enter contract terms', 'error');
                return false;
            }
        }

        return true;
    }

    validateStep4() {
        if (!this.validateStep1() || !this.validateStep2()) {
            ToastManager.show('Please complete all required fields before submitting', 'error');
            return false;
        }
        return true;
    }

    updateReviewSummary() {
        // Update basic information
        document.getElementById('review-title').textContent = document.getElementById('title').value || '-';
        document.getElementById('review-description').textContent =
            document.getElementById('description').value ?
            (document.getElementById('description').value.substring(0, 100) + '...') : '-';

        // Update route details
        const hasMapRoute = mapManager && mapManager.points && mapManager.points.length >= 2;
        document.getElementById('review-route-type').textContent = hasMapRoute ? 'Map-defined route' : 'Manual entry';

        // Use map distance if available, otherwise use manual distance
        let distance = document.getElementById('distance').value;
        if (hasMapRoute && mapManager.totalDistance) {
            distance = mapManager.totalDistance;
        }
        document.getElementById('review-distance').textContent = distance ? distance + ' km' : '-';

        document.getElementById('review-cores').textContent =
            document.getElementById('cores_required').value || '-';

        // Update technical details
        document.getElementById('review-technology').textContent =
            document.getElementById('technology_type').value || '-';
        document.getElementById('review-link-class').textContent =
            document.getElementById('link_class').value || '-';

        // Update services
        updateReviewSections();
    }
}

////////////////////
// Services Manager
////////////////////
class ServicesManager {
    constructor() {
        this.selectedServices = new Map();
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.updateServicesCount();
        this.updateSelectedServices();
    }

    setupEventListeners() {
        document.querySelectorAll('.category-filter').forEach(filter => {
            filter.addEventListener('change', () => this.filterServices());
        });

        document.querySelectorAll('.service-checkbox-input').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const serviceId = e.target.value;
                this.toggleService(serviceId);
            });
        });
    }

    toggleService(serviceId) {
        const checkbox = document.getElementById(`service-${serviceId}`);
        const card = checkbox.closest('.service-card');

        if (checkbox.checked) {
            const price = parseFloat(checkbox.dataset.price);
            const name = checkbox.dataset.name;
            this.selectedServices.set(serviceId, { price, name });
            card.classList.add('selected');
            ToastManager.show(`Added ${name} to services`, 'success', 3000);
        } else {
            this.selectedServices.delete(serviceId);
            card.classList.remove('selected');
        }

        this.updateSelectedServices();
        this.updateServicesCount();
    }

    updateSelectedServices() {
        const selectedList = document.getElementById('selected-services-list');
        const totalCostElement = document.getElementById('total-cost');
        const selectedCard = document.getElementById('selected-services-card');
        const selectedCount = document.getElementById('selected-count');

        selectedList.innerHTML = '';
        let totalCost = 0;

        if (this.selectedServices.size === 0) {
            selectedCard.style.display = 'none';
            return;
        }

        selectedCard.style.display = 'block';
        selectedCount.textContent = `${this.selectedServices.size} service${this.selectedServices.size > 1 ? 's' : ''}`;

        this.selectedServices.forEach((service, serviceId) => {
            const serviceElement = document.createElement('div');
            serviceElement.className = 'd-flex justify-content-between align-items-center mb-2 p-2 border-bottom';

            serviceElement.innerHTML = `
                <div>
                    <strong>${service.name}</strong>
                    <span class="badge bg-secondary service-category-badge ms-2">
                        ${document.querySelector(`#service-${serviceId}`).closest('.service-item').dataset.category}
                    </span>
                </div>
                <div>
                    <strong>$${service.price.toFixed(2)}</strong>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeService('${serviceId}')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            selectedList.appendChild(serviceElement);
            totalCost += service.price;
        });

        totalCostElement.textContent = `Total Monthly Cost: $${totalCost.toFixed(2)}`;
    }

    filterServices() {
        const selectedCategories = Array.from(document.querySelectorAll('.category-filter:checked'))
            .map(checkbox => checkbox.value);

        const serviceItems = document.querySelectorAll('.service-item');
        let visibleCount = 0;

        serviceItems.forEach(item => {
            const category = item.dataset.category;

            if (selectedCategories.length === 0 || selectedCategories.includes(category)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        this.updateServicesCount(visibleCount);
    }

    updateServicesCount(visibleCount = null) {
        const servicesCount = document.getElementById('services-count');
        const noServicesMessage = document.getElementById('no-services-message');

        if (visibleCount !== null) {
            servicesCount.textContent = `${visibleCount} services available`;
            noServicesMessage.style.display = visibleCount === 0 ? 'block' : 'none';
        } else {
            const totalServices = document.querySelectorAll('.service-item').length;
            servicesCount.textContent = `${totalServices} services available`;
        }
    }
}

////////////////////
// Map Manager
////////////////////
class MapManager {
    constructor(map) {
        this.map = map;
        this.points = [];
        this.isAddingPoints = false;
        this.substationMarkers = [];
        this.routeMarkers = [];
        this.routePolyline = null;
        this.totalDistance = 0;
        this.startPoint = null;
        this.endPoint = null;
        this.darkFibreMode = null;
        this.startMarker = null;
        this.endMarker = null;
        this.fibreStations = [];
        this.distanceMarkers = [];
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadSubstationsFromDatabase();
        this.updateDistanceDisplay();
        this.updatePointList();
    }

    setupEventListeners() {
        const addPointBtn = document.getElementById('add-point-mode');
        if (addPointBtn) {
            addPointBtn.addEventListener('click', () => {
                this.toggleAddPointsMode();
            });
        }

        const clearRouteBtn = document.getElementById('clear-route');
        if (clearRouteBtn) {
            clearRouteBtn.addEventListener('click', () => {
                this.clearRoute();
            });
        }

        const resetViewBtn = document.getElementById('reset-view');
        if (resetViewBtn) {
            resetViewBtn.addEventListener('click', () => {
                this.resetView();
            });
        }

        const toggleSubstations = document.getElementById('toggle-substations');
        if (toggleSubstations) {
            toggleSubstations.addEventListener('change', (e) => {
                this.toggleSubstations(e.target.checked);
            });
        }

        const toggleSatellite = document.getElementById('toggle-satellite');
        if (toggleSatellite) {
            toggleSatellite.addEventListener('change', (e) => {
                this.toggleSatelliteView(e.target.checked);
            });
        }

        this.map.addListener('click', (event) => {
            if (this.isAddingPoints) {
                this.addPoint(event.latLng);
            }
        });

        this.setupDarkFibreControls();
        this.setupSearchAndFilterControls();
    }

    setupDarkFibreControls() {
        const setStartBtn = document.getElementById('set-start-point');
        const setEndBtn = document.getElementById('set-end-point');

        if (setStartBtn) {
            setStartBtn.addEventListener('click', () => {
                this.setDarkFibreMode('start');
            });
        }

        if (setEndBtn) {
            setEndBtn.addEventListener('click', () => {
                this.setDarkFibreMode('end');
            });
        }
    }

    setupSearchAndFilterControls() {
        // Search functionality
        const searchInput = document.getElementById('station-search');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchStations(e.target.value);
            });
        }

        const clearSearchBtn = document.getElementById('clear-search');
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', () => {
                if (searchInput) {
                    searchInput.value = '';
                    this.searchStations('');
                }
            });
        }

        // Status filter buttons
        document.querySelectorAll('.status-filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const status = e.target.dataset.status;
                this.filterByStatus(status);

                // Update button states
                document.querySelectorAll('.status-filter-btn').forEach(b => {
                    b.classList.remove('active');
                });
                e.target.classList.add('active');
            });
        });

        const showAllBtn = document.getElementById('show-all-status');
        if (showAllBtn) {
            showAllBtn.addEventListener('click', () => {
                this.substationMarkers.forEach(({ marker }) => {
                    marker.setVisible(true);
                });

                document.querySelectorAll('.status-filter-btn').forEach(b => {
                    b.classList.remove('active');
                });
            });
        }
    }

    toggleAddPointsMode() {
        this.isAddingPoints = !this.isAddingPoints;
        const button = document.getElementById('add-point-mode');

        if (this.isAddingPoints) {
            button.classList.add('btn-success');
            button.classList.remove('btn-primary');
            button.innerHTML = '<i class="fas fa-map-marker-alt me-1"></i>Adding Points...';
            ToastManager.show('Click on the map to add route points', 'info');
        } else {
            button.classList.add('btn-primary');
            button.classList.remove('btn-success');
            button.innerHTML = '<i class="fas fa-map-marker-alt me-1"></i>Add Points Mode';
        }
    }

    addPoint(latLng) {
        this.points.push(latLng);

        const marker = new google.maps.Marker({
            position: latLng,
            map: this.map,
            title: `Point ${this.points.length}`,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                fillColor: '#4e73df',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 2,
                scale: 8
            }
        });

        this.routeMarkers.push(marker);
        this.updateRoutePolyline();
        this.updatePointList();

        // Calculate and show distance
        this.calculateAndShowDistance();

        // Show toast with distance info
        if (this.points.length === 2) {
            ToastManager.show(`Route defined! Distance: ${this.totalDistance} km`, 'success');
        } else if (this.points.length > 2) {
            ToastManager.show(`Point ${this.points.length} added. Current distance: ${this.totalDistance} km`, 'success');
        } else {
            ToastManager.show(`Point 1 added. Add another point to see distance.`, 'info');
        }
    }

    async loadSubstationsFromDatabase() {
    try {
        const fibreStationsData = document.getElementById('fibreStationsData');
        if (!fibreStationsData || !fibreStationsData.value) {
            console.warn('No fibre stations data found');
            await this.loadDefaultSubstations(); // FIXED: Added await
            return;
        }

        // Parse the stations data
        const stations = JSON.parse(fibreStationsData.value);
        this.fibreStations = stations;

        console.log(`Loaded ${stations.length} fibre stations from database`);

        // Create markers for all stations in parallel
        const markerPromises = stations.map(station => this.createSubstationMarker(station));
        await Promise.all(markerPromises);

        this.createEnhancedOwnerFilters();
        ToastManager.show(`Loaded ${stations.length} fibre stations`, 'success');

    } catch (error) {
        console.error('Error loading fibre stations:', error);
        ToastManager.show('Failed to load fibre stations. Using default data.', 'warning');
        await this.loadDefaultSubstations();
    }
}

async createSubstationMarker(station) {
    if (!station.lat || !station.lng) {
        console.warn('Skipping station with missing coordinates:', station.name);
        return;
    }

    const hasFibre = station.hasFibre === true ||
                     station.status === 'Available' ||
                     station.fibre_status === 'Available' ||
                     station.fibreStatus === 'Available';

    const markerColor = hasFibre ? '#e74a3b' : '#95a5a6';
    const zIndex = hasFibre ? 1000 : 500;

    try {
        // Import the marker library and create PinElement
        const { PinElement } = await google.maps.importLibrary("marker");

        // Create the pin element for the marker - USING STANDARD SIZES
        const pin = new PinElement({
            background: markerColor,
            borderColor: '#ffffff',
            glyphColor: '#ffffff',
            scale: 1.0, // STANDARD SIZE (was: iconScale * 0.5)
        });

        // Create the AdvancedMarkerElement
        const marker = new google.maps.marker.AdvancedMarkerElement({
            map: this.map,
            position: { lat: station.lat, lng: station.lng },
            title: station.name,
            content: pin.element,
            zIndex: zIndex
        });

        // Store the pin element for later modifications
        marker.pinElement = pin;
        marker.originalBackground = markerColor;
        marker.originalScale = 1.0; // Store standard scale

        // Mouseover event - show detailed tooltip
        let tooltip = null;

        marker.addEventListener('mouseover', () => {
            if (hasFibre) {
                // Change pin appearance on hover
                marker.pinElement.background = '#f39c12';
                marker.pinElement.scale = 1.3; // Slightly larger on hover (was: 14 * 0.5)
                marker.content = marker.pinElement.element; // Update the marker

                const tooltipContent = this.createSubstationTooltip(station, hasFibre);
                tooltip = new google.maps.InfoWindow({
                    content: tooltipContent,
                    disableAutoPan: true
                });
                tooltip.open(this.map, marker);
            }
        });

        marker.addEventListener('mouseout', () => {
            if (hasFibre) {
                // Revert pin appearance
                marker.pinElement.background = marker.originalBackground;
                marker.pinElement.scale = marker.originalScale; // Back to 1.0
                marker.content = marker.pinElement.element; // Update the marker

                if (tooltip) {
                    tooltip.close();
                    tooltip = null;
                }
            }
        });

        // Click event - add substation as route point
        marker.addEventListener('gmp-click', () => {
            if (!hasFibre) {
                ToastManager.show('This station has no fibre available', 'warning');
                return;
            }

            // Close tooltip if open
            if (tooltip) {
                tooltip.close();
                tooltip = null;
            }

            // Handle dark fibre mode
            if (this.darkFibreMode === 'start') {
                this.useAsStartPoint(station.id);
                return;
            } else if (this.darkFibreMode === 'end') {
                this.useAsEndPoint(station.id);
                return;
            }

            // Normal mode - add as route point
            const latLng = new google.maps.LatLng(station.lat, station.lng);

            // Add the station as a route point
            this.points.push(latLng);
            this.updateRoutePolyline();

            // Create a new marker for the station route point (also using AdvancedMarkerElement)
            const routePin = new PinElement({
                background: '#e74a3b',
                borderColor: '#ffffff',
                glyphColor: '#ffffff',
                scale: 1.0, // Standard size for route points
            });

            const stationMarker = new google.maps.marker.AdvancedMarkerElement({
                position: latLng,
                map: this.map,
                title: `Station: ${station.name}`,
                content: routePin.element
            });
            this.routeMarkers.push(stationMarker);

            this.updatePointList();
            this.calculateAndShowDistance();

            ToastManager.show(`Added "${station.name}" to route`, 'success');

            // Briefly change marker to show it's been added
            marker.pinElement.background = '#4e73df';
            marker.pinElement.scale = 1.2; // Slightly larger when selected (was: 12 * 0.5)
            marker.content = marker.pinElement.element;

            // Revert after 1 second
            setTimeout(() => {
                marker.pinElement.background = marker.originalBackground;
                marker.pinElement.scale = marker.originalScale; // Back to 1.0
                marker.content = marker.pinElement.element;
            }, 1000);
        });

        this.substationMarkers.push({ marker, station });

    } catch (error) {
        console.error('Error creating substation marker:', error);
        ToastManager.show('Failed to create marker for ' + station.name, 'warning');
    }
}

    createSubstationTooltip(station, hasFibre) {
        const statusClass = hasFibre ? 'bg-success' : 'bg-secondary';
        const statusText = hasFibre ? 'Fibre Available' : 'No Fibre';
        const capacity = station.capacity && station.capacity !== 'N/A' ? station.capacity : 'Not specified';
        const connectionType = station.connectionType || 'Not specified';
        const location = station.location || station.area || 'Not specified';

        return `
            <div class="substation-tooltip">
                <h6 class="mb-1">${station.name}</h6>
                <div class="mb-1">
                    <span class="badge ${statusClass}">
                        ${statusText}
                    </span>
                    <span class="badge bg-info ms-1">${station.owner}</span>
                </div>
                <table class="table table-sm mb-1">
                    <tr>
                        <td><small><strong>Location:</strong></small></td>
                        <td><small>${location}</small></td>
                    </tr>
                    <tr>
                        <td><small><strong>Capacity:</strong></small></td>
                        <td><small>${capacity}</small></td>
                    </tr>
                    <tr>
                        <td><small><strong>Fibre Cores:</strong></small></td>
                        <td><small>${station.fibreCores || station.darkFibreCores || 0}</small></td>
                    </tr>
                    <tr>
                        <td><small><strong>Connection:</strong></small></td>
                        <td><small>${connectionType}</small></td>
                    </tr>
                    <tr>
                        <td><small><strong>Status:</strong></small></td>
                        <td><small>${station.status || station.fibre_status || 'Unknown'}</small></td>
                    </tr>
                </table>
                <div class="text-center">
                    <small class="text-muted">Click to add to route</small>
                </div>
            </div>
        `;
    }

    async loadDefaultSubstations() {
    const defaultSubstations = [
        {
            id: 1,
            lat: -1.2921,
            lng: 36.8219,
            name: 'Nairobi West Substation',
            owner: 'KETRACO',
            hasFibre: true,
            capacity: '500 MVA',
            status: 'Available',
            fibreCores: 144,
            connectionType: 'Patch Panel',
            area: 'Nairobi West',
            location: 'Nairobi West'
        },
        {
            id: 2,
            lat: -1.2833,
            lng: 36.8232,
            name: 'Industrial Area Substation',
            owner: 'KPLC',
            hasFibre: true,
            capacity: '300 MVA',
            status: 'Available',
            fibreCores: 96,
            connectionType: 'Direct Tap',
            area: 'Industrial Area',
            location: 'Industrial Area, Nairobi'
        }
    ];

    // Use Promise.all to create all markers in parallel
    const markerPromises = defaultSubstations.map(station => this.createSubstationMarker(station));
    await Promise.all(markerPromises);

    console.log(`Loaded ${defaultSubstations.length} default substations`);
}

    updateRoutePolyline() {
        if (this.routePolyline) {
            this.routePolyline.setMap(null);
        }

        if (this.points.length >= 2) {
            this.routePolyline = new google.maps.Polyline({
                path: this.points,
                geodesic: true,
                strokeColor: '#4e73df',
                strokeOpacity: 1.0,
                strokeWeight: 4,
                zIndex: 1
            });
            this.routePolyline.setMap(this.map);
        }
    }

    calculateAndShowDistance() {
        if (this.points.length >= 2) {
            this.calculateTotalDistance();

            // Update the main distance display
            const distanceValue = document.getElementById('distance-value');
            const distanceLabel = document.getElementById('distance-label');

            if (distanceValue) {
                distanceValue.textContent = `${this.totalDistance} km`;
            }

            if (distanceLabel) {
                distanceLabel.textContent = `${this.points.length} points`;
                distanceLabel.className = 'd-block text-success';
            }

            // Update route summary panel
            const routeDistanceDisplay = document.getElementById('route-distance-display');
            if (routeDistanceDisplay) {
                routeDistanceDisplay.textContent = `${this.totalDistance} km`;
            }

            // Update route points count
            const routePointsCount = document.getElementById('route-points-count');
            if (routePointsCount) {
                routePointsCount.textContent = this.points.length;
            }

            // Auto-populate the distance field in manual entry
            const manualDistanceInput = document.getElementById('distance');
            if (manualDistanceInput) {
                manualDistanceInput.value = this.totalDistance;
            }

            // Store route points in hidden input
            const routePointsInput = document.getElementById('route-points-input');
            if (routePointsInput) {
                const routePoints = this.points.map(point => ({
                    lat: point.lat(),
                    lng: point.lng()
                }));
                routePointsInput.value = JSON.stringify(routePoints);
            }
        } else {
            // Reset displays when less than 2 points
            const distanceValue = document.getElementById('distance-value');
            const distanceLabel = document.getElementById('distance-label');

            if (distanceValue) {
                distanceValue.textContent = '0 km';
            }

            if (distanceLabel) {
                distanceLabel.textContent = 'Add at least 2 points';
                distanceLabel.className = 'd-block text-muted';
            }

            // Update route summary panel
            const routeDistanceDisplay = document.getElementById('route-distance-display');
            if (routeDistanceDisplay) {
                routeDistanceDisplay.textContent = '0 km';
            }
        }
    }

    calculateTotalDistance() {
        this.totalDistance = 0;
        if (this.points.length >= 2) {
            for (let i = 1; i < this.points.length; i++) {
                this.totalDistance += google.maps.geometry.spherical.computeDistanceBetween(
                    this.points[i-1],
                    this.points[i]
                );
            }
            this.totalDistance = (this.totalDistance / 1000).toFixed(2);
        } else {
            this.totalDistance = 0;
        }
    }

    updatePointList() {
        const pointList = document.getElementById('point-list');
        const pointCount = document.getElementById('point-count');

        if (this.points.length === 0) {
            pointList.innerHTML = `
                <div class="point-item text-muted text-center py-4">
                    <i class="fas fa-map-marker-alt fa-2x mb-2 d-block"></i>
                    No points added yet<br>
                    <small class="text-muted">Click on the map to add your first point</small>
                </div>
            `;
        } else {
            let pointsHtml = '';
            this.points.forEach((point, index) => {
                pointsHtml += `
                    <div class="point-item p-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="route-marker-icon me-3" style="width: 12px; height: 12px; background-color: #4e73df; border-radius: 50%;"></div>
                            <div>
                                <strong>Point ${index + 1}</strong><br>
                                <small class="text-muted">
                                    Lat: ${point.lat().toFixed(6)}, Lng: ${point.lng().toFixed(6)}
                                </small>
                            </div>
                        </div>
                    </div>
                `;
            });
            pointList.innerHTML = pointsHtml;
        }

        pointCount.textContent = `${this.points.length} point${this.points.length !== 1 ? 's' : ''}`;
    }

    updateDistanceDisplay() {
        const distanceDisplay = document.getElementById('total-distance');
        const routeDistanceDisplay = document.getElementById('route-distance-display');
        const routePointsCount = document.getElementById('route-points-count');

        if (this.points.length >= 2) {
            distanceDisplay.innerHTML = `
                <i class="fas fa-ruler-combined me-2"></i>
                <span>${this.totalDistance} km</span>
            `;

            if (routeDistanceDisplay) {
                routeDistanceDisplay.textContent = `${this.totalDistance} km`;
            }

            if (routePointsCount) {
                routePointsCount.textContent = this.points.length;
            }

            const routePoints = this.points.map(point => ({
                lat: point.lat(),
                lng: point.lng()
            }));
            const routePointsInput = document.getElementById('route-points-input');
            if (routePointsInput) {
                routePointsInput.value = JSON.stringify(routePoints);
            }
        } else {
            distanceDisplay.innerHTML = `
                <i class="fas fa-ruler-combined me-2"></i>
                <span>0 km</span>
            `;

            if (routeDistanceDisplay) {
                routeDistanceDisplay.textContent = '0 km';
            }

            if (routePointsCount) {
                routePointsCount.textContent = '0';
            }
        }
    }

    // Dark Fibre Methods
    setDarkFibreMode(mode) {
        this.darkFibreMode = mode;
        const startBtn = document.getElementById('set-start-point');
        const endBtn = document.getElementById('set-end-point');

        if (!startBtn || !endBtn) return;

        if (mode === 'start') {
            startBtn.classList.add('btn-success');
            startBtn.classList.remove('btn-dark');
            endBtn.classList.add('btn-dark');
            endBtn.classList.remove('btn-success');
            ToastManager.show('Click on a substation to set as start point', 'info');
        } else if (mode === 'end') {
            endBtn.classList.add('btn-success');
            endBtn.classList.remove('btn-dark');
            startBtn.classList.add('btn-dark');
            startBtn.classList.remove('btn-success');
            ToastManager.show('Click on a substation to set as end point', 'info');
        }

        this.highlightFibreSubstations();
    }

    useAsStartPoint(substationId) {
        const substationData = this.getSubstationById(substationId);
        if (!substationData) return;

        this.startPoint = {
            type: 'substation',
            id: substationId,
            lat: substationData.station.lat,
            lng: substationData.station.lng,
            name: substationData.station.name
        };

        this.clearStartMarker();
        this.addStartMarker(substationData.station.lat, substationData.station.lng);

        ToastManager.show(`Set "${substationData.station.name}" as start point`, 'success');
        this.updateRouteDisplay();
        this.darkFibreMode = null;
    }

    useAsEndPoint(substationId) {
        const substationData = this.getSubstationById(substationId);
        if (!substationData) return;

        this.endPoint = {
            type: 'substation',
            id: substationId,
            lat: substationData.station.lat,
            lng: substationData.station.lng,
            name: substationData.station.name
        };

        this.clearEndMarker();
        this.addEndMarker(substationData.station.lat, substationData.station.lng);

        ToastManager.show(`Set "${substationData.station.name}" as end point`, 'success');
        this.updateRouteDisplay();
        this.darkFibreMode = null;
    }

    addStartMarker(lat, lng) {
        this.startMarker = new google.maps.Marker({
            position: { lat, lng },
            map: this.map,
            title: 'Start Point',
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                fillColor: '#28a745',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 3,
                scale: 14
            },
            zIndex: 2000
        });
    }

    addEndMarker(lat, lng) {
        this.endMarker = new google.maps.Marker({
            position: { lat, lng },
            map: this.map,
            title: 'End Point',
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                fillColor: '#dc3545',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 3,
                scale: 14
            },
            zIndex: 2000
        });
    }

    clearStartMarker() {
        if (this.startMarker) {
            this.startMarker.setMap(null);
            this.startMarker = null;
        }
    }

    clearEndMarker() {
        if (this.endMarker) {
            this.endMarker.setMap(null);
            this.endMarker = null;
        }
    }

    highlightFibreSubstations() {
        this.substationMarkers.forEach(({ marker, station }) => {
            const hasFibre = station.hasFibre === true ||
                           station.status === 'Available' ||
                           station.fibre_status === 'Available' ||
                           station.fibreStatus === 'Available';

            if (hasFibre) {
                marker.setAnimation(google.maps.Animation.BOUNCE);
                setTimeout(() => {
                    marker.setAnimation(null);
                }, 2000);
            }
        });
    }

    getSubstationById(id) {
        return this.substationMarkers.find(item => item.station.id === id);
    }

    updateRouteDisplay() {
        const startPointElement = document.getElementById('start-point-display');
        const endPointElement = document.getElementById('end-point-display');

        if (startPointElement) {
            startPointElement.textContent = this.startPoint ? this.startPoint.name : 'Not set';
        }

        if (endPointElement) {
            endPointElement.textContent = this.endPoint ? this.endPoint.name : 'Not set';
        }

        const startPointInput = document.getElementById('start-point-input');
        const endPointInput = document.getElementById('end-point-input');

        if (startPointInput && this.startPoint) {
            startPointInput.value = JSON.stringify(this.startPoint);
        }

        if (endPointInput && this.endPoint) {
            endPointInput.value = JSON.stringify(this.endPoint);
        }
    }

    createEnhancedOwnerFilters() {
        const owners = [...new Set(this.substationMarkers.map(m => m.station.owner))];
        const filtersContainer = document.getElementById('owner-filters');

        if (!filtersContainer) return;

        filtersContainer.innerHTML = '';

        owners.forEach(owner => {
            const filterId = `filter-${owner.toLowerCase().replace(/\s+/g, '-')}`;
            const substationCount = this.substationMarkers.filter(m =>
                m.station.owner === owner && (
                    m.station.hasFibre === true ||
                    m.station.status === 'Available' ||
                    m.station.fibre_status === 'Available' ||
                    m.station.fibreStatus === 'Available'
                )
            ).length;

            if (substationCount === 0) return;

            const filterElement = document.createElement('div');
            filterElement.className = 'form-check form-check-inline mb-1';
            filterElement.innerHTML = `
                <input class="form-check-input owner-filter" type="checkbox"
                       value="${owner}" id="${filterId}" checked>
                <label class="form-check-label small" for="${filterId}">
                    ${owner} <span class="badge bg-secondary">${substationCount}</span>
                </label>
            `;
            filtersContainer.appendChild(filterElement);
        });

        document.querySelectorAll('.owner-filter').forEach(filter => {
            filter.addEventListener('change', (e) => {
                this.filterSubstationsByOwner(e.target.value, e.target.checked);
            });
        });
    }

    filterSubstationsByOwner(owner, show) {
        this.substationMarkers.forEach(({ marker, station }) => {
            if (station.owner === owner) {
                marker.setVisible(show);
            }
        });
    }

    filterByStatus(status) {
        this.substationMarkers.forEach(({ marker, station }) => {
            const stationStatus = station.status || station.fibre_status || station.fibreStatus;
            const shouldShow = stationStatus === status;
            marker.setVisible(shouldShow);
        });
    }

    searchStations(query) {
        if (!query) {
            this.substationMarkers.forEach(({ marker }) => {
                marker.setVisible(true);
            });
            return;
        }

        const searchTerm = query.toLowerCase();
        this.substationMarkers.forEach(({ marker, station }) => {
            const stationName = (station.name || '').toLowerCase();
            const stationArea = (station.area || '').toLowerCase();
            const stationLocation = (station.location || '').toLowerCase();

            const matches = stationName.includes(searchTerm) ||
                           stationArea.includes(searchTerm) ||
                           stationLocation.includes(searchTerm);

            marker.setVisible(matches);
        });
    }

    clearRoute() {
        this.points = [];

        // Clear route markers
        this.routeMarkers.forEach(marker => marker.setMap(null));
        this.routeMarkers = [];

        // Clear distance markers
        if (this.distanceMarkers) {
            this.distanceMarkers.forEach(marker => marker.setMap(null));
            this.distanceMarkers = [];
        }

        // Clear polyline
        if (this.routePolyline) {
            this.routePolyline.setMap(null);
            this.routePolyline = null;
        }

        // Clear start and end markers
        this.clearStartMarker();
        this.clearEndMarker();
        this.startPoint = null;
        this.endPoint = null;

        // Reset displays
        this.updatePointList();
        this.calculateAndShowDistance();
        this.updateRouteDisplay();

        ToastManager.show('Route cleared', 'info');
    }

    resetView() {
        this.map.setCenter({ lat: -1.2921, lng: 36.8219 });
        this.map.setZoom(12);
        ToastManager.show('Map view reset', 'info');
    }

    toggleSubstations(show) {
        this.substationMarkers.forEach(({ marker }) => {
            marker.setVisible(show);
        });
    }

    toggleSatelliteView(isSatellite) {
        this.map.setMapTypeId(isSatellite ? 'hybrid' : 'roadmap');
    }
}

////////////////////
// Helper Functions
////////////////////

// Map Help Functions
function makeDraggable(element) {
    let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    const header = element.querySelector('.map-help-header');

    if (header) {
        header.onmousedown = dragMouseDown;
        header.ontouchstart = dragMouseDown;
    }

    function dragMouseDown(e) {
        e = e || window.event;
        e.preventDefault();
        e.stopPropagation();

        if (e.type === 'touchstart') {
            pos3 = e.touches[0].clientX;
            pos4 = e.touches[0].clientY;
        } else {
            pos3 = e.clientX;
            pos4 = e.clientY;
        }

        document.onmouseup = closeDragElement;
        document.ontouchend = closeDragElement;
        document.onmousemove = elementDrag;
        document.ontouchmove = elementDrag;
    }

    function elementDrag(e) {
        e = e || window.event;
        e.preventDefault();
        e.stopPropagation();

        let clientX, clientY;
        if (e.type === 'touchmove') {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        } else {
            clientX = e.clientX;
            clientY = e.clientY;
        }

        pos1 = pos3 - clientX;
        pos2 = pos4 - clientY;
        pos3 = clientX;
        pos4 = clientY;

        const newTop = element.offsetTop - pos2;
        const newLeft = element.offsetLeft - pos1;

        const mapContainer = document.querySelector('.map-container');
        const containerRect = mapContainer.getBoundingClientRect();
        const elementRect = element.getBoundingClientRect();

        element.style.top = Math.max(10, Math.min(newTop, containerRect.height - elementRect.height - 10)) + "px";
        element.style.left = Math.max(10, Math.min(newLeft, containerRect.width - elementRect.width - 10)) + "px";
    }

    function closeDragElement() {
        document.onmouseup = null;
        document.ontouchend = null;
        document.onmousemove = null;
        document.ontouchmove = null;
    }
}

function closeMapHelp() {
    const helpBox = document.getElementById('mapHelp');
    const toggleCheckbox = document.getElementById('toggle-map-help');

    if (helpBox) {
        helpBox.style.display = 'none';
    }
    if (toggleCheckbox) {
        toggleCheckbox.checked = false;
    }
}

function showMapHelp() {
    const helpBox = document.getElementById('mapHelp');
    const toggleCheckbox = document.getElementById('toggle-map-help');

    if (helpBox) {
        helpBox.style.display = 'block';
    }
    if (toggleCheckbox) {
        toggleCheckbox.checked = true;
    }
}

// Step Navigation Functions
function nextStep(step) {
    console.log('📝 Next button clicked, moving to step', step);

    if (!stepManager) {
        console.error('❌ StepManager not initialized');
        document.querySelectorAll('.form-step').forEach(stepEl => {
            stepEl.classList.remove('active');
        });
        document.getElementById('step-' + step).classList.add('active');
        return;
    }

    if (stepManager.validateStep(stepManager.currentStep)) {
        console.log('✅ Step validation passed');
        stepManager.showStep(step);
        window.scrollTo({ top: 0, behavior: 'smooth' });
        ToastManager.show(`Moving to step ${step}`, 'success', 2000);
    } else {
        console.log('❌ Step validation failed');
        ToastManager.show('Please complete all required fields', 'error');
    }
}

function prevStep(step) {
    console.log('📝 Previous button clicked, moving to step', step);

    if (!stepManager) {
        console.error('❌ StepManager not initialized');
        document.querySelectorAll('.form-step').forEach(stepEl => {
            stepEl.classList.remove('active');
        });
        document.getElementById('step-' + step).classList.add('active');
        return;
    }

    stepManager.showStep(step);
    window.scrollTo({ top: 0, behavior: 'smooth' });
    ToastManager.show(`Moving to step ${step}`, 'info', 2000);
}

// Colocation Sites Management
function addNewSite() {
    const sitesTableBody = document.getElementById('sites-table-body');
    const noSitesRow = document.getElementById('no-sites-row');

    if (noSitesRow) {
        noSitesRow.remove();
    }

    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>${siteCounter + 1}</td>
        <td>
            <input type="text" class="form-control form-control-sm site-name-input"
                   name="colocation_sites[${siteCounter}][site_name]"
                   value=""
                   placeholder="Enter site name" required>
        </td>
        <td>
            <select class="form-select form-control-sm service-type-select"
                    name="colocation_sites[${siteCounter}][service_type]" required>
                <option value="">Select Service Type...</option>
                <option value="shelter_space">Shelter Space</option>
                <option value="rack">Rack</option>
                <option value="cage">Cage</option>
                <option value="suites">Suites</option>
            </select>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSite(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;

    sitesTableBody.appendChild(newRow);
    siteCounter++;

    const inputs = newRow.querySelectorAll('.site-name-input, .service-type-select');
    inputs.forEach(input => {
        input.addEventListener('input', updateSitesSummary);
        input.addEventListener('change', updateSitesSummary);
    });

    updateSitesSummary();
    updateReviewSections();
}

function removeSite(button) {
    const row = button.closest('tr');
    row.remove();

    updateRowNumbers();

    const sitesTableBody = document.getElementById('sites-table-body');
    if (sitesTableBody.children.length === 0) {
        sitesTableBody.innerHTML = `
            <tr id="no-sites-row">
                <td colspan="4" class="text-center text-muted py-4">
                    <i class="fas fa-plus-circle fa-2x mb-3"></i>
                    <div>No colocation sites added yet</div>
                    <small>Click "Add Site" to start adding your colocation requirements</small>
                </td>
            </tr>
        `;
    }

    updateSitesSummary();
    updateReviewSections();
}

function updateRowNumbers() {
    const rows = document.querySelectorAll('#sites-table-body tr:not(#no-sites-row)');
    rows.forEach((row, index) => {
        row.querySelector('td:first-child').textContent = index + 1;
    });
    siteCounter = rows.length;
}

function updateSitesSummary() {
    const siteInputs = document.querySelectorAll('.site-name-input');
    const serviceSelects = document.querySelectorAll('.service-type-select');

    let allValid = true;
    siteInputs.forEach(input => {
        if (!input.value.trim()) {
            allValid = false;
        }
    });

    serviceSelects.forEach(select => {
        if (!select.value) {
            allValid = false;
        }
    });

    return {
        isValid: allValid,
        message: allValid ? '' : 'Please fill all colocation site details'
    };
}

function updateColocationSitesReview(sites = null) {
    const reviewElement = document.getElementById('review-colocation-sites');
    if (!reviewElement) return;

    if (!sites) {
        const siteInputs = document.querySelectorAll('.site-name-input');
        const serviceSelects = document.querySelectorAll('.service-type-select');

        if (siteInputs.length === 0) {
            reviewElement.innerHTML = 'No colocation sites added';
            return;
        }

        let html = '';
        for (let i = 0; i < siteInputs.length; i++) {
            const siteName = siteInputs[i].value;
            const serviceType = serviceSelects[i].value;

            if (siteName && serviceType) {
                html += `
                    <div class="mb-1">
                        <span class="badge bg-info me-2">${serviceType}</span>
                        ${siteName}
                    </div>
                `;
            }
        }

        reviewElement.innerHTML = html || 'No colocation sites added';
    }
}

function updateAdditionalServicesReview() {
    const reviewElement = document.getElementById('review-services');
    if (!reviewElement) return;

    const selectedServices = Array.from(document.querySelectorAll('.service-checkbox-input:checked'));

    if (selectedServices.length === 0) {
        reviewElement.innerHTML = 'No additional services selected';
        return;
    }

    let html = '';
    selectedServices.forEach(checkbox => {
        const name = checkbox.dataset.name;
        const price = parseFloat(checkbox.dataset.price);
        const category = checkbox.dataset.category;

        html += `
            <div class="mb-1">
                <span class="badge bg-primary me-2">${category}</span>
                ${name} - $${price.toFixed(2)}/month
            </div>
        `;
    });

    reviewElement.innerHTML = html;
}

function updateReviewSections() {
    updateColocationSitesReview();
    updateAdditionalServicesReview();
}

function validateColocationSites() {
    const siteInputs = document.querySelectorAll('.site-name-input');
    const serviceSelects = document.querySelectorAll('.service-type-select');

    if (siteInputs.length === 0) {
        return {
            isValid: true,
            message: ''
        };
    }

    for (let i = 0; i < siteInputs.length; i++) {
        if (!siteInputs[i].value.trim() || !serviceSelects[i].value) {
            return {
                isValid: false,
                message: 'Please fill all colocation site details (site name and service type)'
            };
        }
    }

    return {
        isValid: true,
        message: ''
    };
}

function toggleService(serviceId) {
    if (servicesManager) {
        servicesManager.toggleService(serviceId);
    }
}

function removeService(serviceId) {
    if (servicesManager) {
        servicesManager.removeService(serviceId);
    }
}

function goBack() {
    const referrer = document.referrer;
    const currentHost = window.location.host;

    if (referrer && referrer.includes(currentHost) && !referrer.includes('create')) {
        window.history.back();
    } else {
        window.location.href = "{{ route('customer.design-requests.index') }}";
    }
}

////////////////////
// Automatic Uppercase Conversion for Input Fields
////////////////////
function setupUppercaseConversion() {
    // Select all text inputs and textareas
    const textInputs = document.querySelectorAll('input[type="text"], textarea');

    textInputs.forEach(input => {
        // Skip if it's a search input or any other that shouldn't be uppercase
        if (input.id === 'station-search' || input.classList.contains('no-uppercase')) {
            return;
        }

        // Add event listener for input event
        input.addEventListener('input', function() {
            const cursorPosition = this.selectionStart;
            this.value = this.value.toUpperCase();

            // Restore cursor position after conversion
            this.setSelectionRange(cursorPosition, cursorPosition);
        });

        // Also convert existing value on page load
        if (input.value) {
            input.value = input.value.toUpperCase();
        }
    });

    // Handle dynamic inputs (colocation sites)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        const newInputs = node.querySelectorAll ?
                            node.querySelectorAll('input[type="text"], textarea') : [];
                        newInputs.forEach(input => {
                            if (!input.id || !input.id.includes('station-search')) {
                                input.addEventListener('input', function() {
                                    const cursorPosition = this.selectionStart;
                                    this.value = this.value.toUpperCase();
                                    this.setSelectionRange(cursorPosition, cursorPosition);
                                });
                            }
                        });
                    }
                });
            }
        });
    });

    // Start observing the document body for added elements
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}
function makeRouteSummaryDraggable() {
    const routeSummaryPanel = document.getElementById('route-summary-panel');
    if (!routeSummaryPanel) return;

    let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    const header = routeSummaryPanel.querySelector('h6');

    // Make the entire panel header draggable
    routeSummaryPanel.style.cursor = 'move';

    header.onmousedown = dragMouseDown;
    header.ontouchstart = dragMouseDown;

    function dragMouseDown(e) {
        e = e || window.event;
        e.preventDefault();
        e.stopPropagation();

        if (e.type === 'touchstart') {
            pos3 = e.touches[0].clientX;
            pos4 = e.touches[0].clientY;
        } else {
            pos3 = e.clientX;
            pos4 = e.clientY;
        }

        // Add dragging class for visual feedback
        routeSummaryPanel.classList.add('dragging');

        document.onmouseup = closeDragElement;
        document.ontouchend = closeDragElement;
        document.onmousemove = elementDrag;
        document.ontouchmove = elementDrag;
    }

    function elementDrag(e) {
        e = e || window.event;
        e.preventDefault();
        e.stopPropagation();

        let clientX, clientY;
        if (e.type === 'touchmove') {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        } else {
            clientX = e.clientX;
            clientY = e.clientY;
        }

        pos1 = pos3 - clientX;
        pos2 = pos4 - clientY;
        pos3 = clientX;
        pos4 = clientY;

        const newTop = routeSummaryPanel.offsetTop - pos2;
        const newLeft = routeSummaryPanel.offsetLeft - pos1;

        const mapContainer = document.querySelector('.map-container');
        const containerRect = mapContainer.getBoundingClientRect();
        const elementRect = routeSummaryPanel.getBoundingClientRect();

        // Keep panel within map bounds
        routeSummaryPanel.style.top = Math.max(10, Math.min(newTop, containerRect.height - elementRect.height - 10)) + "px";
        routeSummaryPanel.style.left = Math.max(10, Math.min(newLeft, containerRect.width - elementRect.width - 10)) + "px";
        routeSummaryPanel.style.bottom = 'auto'; // Remove bottom positioning when dragging
    }

    function closeDragElement() {
        // Remove dragging class
        routeSummaryPanel.classList.remove('dragging');

        document.onmouseup = null;
        document.ontouchend = null;
        document.onmousemove = null;
        document.ontouchmove = null;
    }
}

// Map initialization
let mapInitialized = false;

function initMap() {
    // Prevent multiple initializations
    if (mapInitialized) {
        console.log('Map already initialized');
        return;
    }

    console.log('🗺️ Initializing map...');

    try {
        const mapContainer = document.getElementById('google-map');
        if (!mapContainer) {
            throw new Error('Map container not found');
        }

        // Hide loading overlay
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }

        // Check if Google Maps is loaded
        if (!window.google || !window.google.maps) {
            throw new Error('Google Maps API not loaded');
        }

        const defaultCenter = { lat: -1.2921, lng: 36.8219 };
        map = new google.maps.Map(mapContainer, {
            zoom: 12,
            center: defaultCenter,
            mapTypeId: 'roadmap',
            mapId: 'YOUR_MAP_ID_HERE', // Required for Advanced Markers
            streetViewControl: false,
            mapTypeControl: false,
            fullscreenControl: true,
            zoomControl: true
        });

        // Initialize map manager
        mapManager = new MapManager(map);
        mapInitialized = true;

        ToastManager.show('Map loaded successfully', 'success');

    } catch (error) {
        console.error('Map initialization error:', error);
        ToastManager.show('Failed to load map. Please refresh the page.', 'error');

        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }
    }
}

////////////////////
// Initialize application
////////////////////
document.addEventListener('DOMContentLoaded', function() {

        // Setup uppercase conversion for input fields
    setupUppercaseConversion();
    console.log('✅ Uppercase conversion enabled');
    console.log('🚀 Page loaded, initializing application...');

    try {
        stepManager = new StepManager();
        console.log('✅ StepManager initialized');

        servicesManager = new ServicesManager();
        console.log('✅ ServicesManager initialized');

        updateSitesSummary();
        updateReviewSections();

        // Initialize map help
        const helpBox = document.getElementById('mapHelp');
        if (helpBox) {
            makeDraggable(helpBox);
            showMapHelp();
        }

        // Make route summary panel draggable
        makeRouteSummaryDraggable();

        // Setup map help toggle
        const toggleCheckbox = document.getElementById('toggle-map-help');
        if (toggleCheckbox) {
            toggleCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    showMapHelp();
                } else {
                    closeMapHelp();
                }
            });
        }

        console.log('✅ Application initialized successfully');

    } catch (error) {
        console.error('❌ Error initializing application:', error);
        document.querySelectorAll('.form-step').forEach(step => step.classList.remove('active'));
        document.getElementById('step-1').classList.add('active');
    }

    // Add technology type change listener
    const technologyTypeSelect = document.getElementById('technology_type');
    if (technologyTypeSelect) {
        technologyTypeSelect.addEventListener('change', function() {
            const linkClassSelect = document.getElementById('link_class');
            if (this.value === 'OPGW' || this.value === 'ADSS') {
                linkClassSelect.disabled = false;
                linkClassSelect.closest('.form-group').style.display = 'block';
            } else {
                linkClassSelect.disabled = true;
                linkClassSelect.value = '';
                linkClassSelect.closest('.form-group').style.display = 'none';
            }
        });
    }

    // Form submission handler
    const form = document.getElementById('design-request-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                return;
            }

            console.log('🎪 Form submission triggered');

            // Validate all steps
            if (!stepManager.validateStep(1) || !stepManager.validateStep(2)) {
                e.preventDefault();
                ToastManager.show('Please complete all required fields before submitting', 'error');
                stepManager.showStep(1);
                return;
            }

            // Validate colocation sites
            const siteValidation = validateColocationSites();
            if (!siteValidation.isValid) {
                e.preventDefault();
                ToastManager.show(siteValidation.message, 'error');
                console.error('❌ Validation failed:', siteValidation.message);
                return;
            }

            isSubmitting = true;

            // Show loading state
            const submitBtn = document.getElementById('submit-btn');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            }

            ToastManager.show('Submitting your design request...', 'info');
            console.log('✅ Form submission proceeding...');
        });
    }
});

// Global initMap function for Google Maps
window.initMap = initMap;

// Handle Google Maps errors
window.gm_authFailure = function() {
    ToastManager.show('Google Maps authentication failed. Please contact support.', 'error');

    const loadingOverlay = document.getElementById('loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = 'none';
    }
};

// Handle if Google Maps doesn't load at all
setTimeout(() => {
    const loadingOverlay = document.getElementById('loading-overlay');
    if (loadingOverlay && loadingOverlay.style.display !== 'none') {
        ToastManager.show('Map loading is taking longer than expected. Please check your internet connection.', 'warning');
    }
}, 10000);

// Load Google Maps after DOM is ready
function loadGoogleMaps() {
    const API_KEY = '{{ config('services.google.maps_api_key') }}';
    const LIBRARIES = ['geometry', 'places', 'marker'];

    // Don't load if already loaded
    if (window.google && window.google.maps) {
        if (typeof initMap === 'function') {
            initMap();
        }
        return;
    }

    // Create the script element
    const script = document.createElement('script');
    let src = `https://maps.googleapis.com/maps/api/js?key=${API_KEY}&v=weekly&callback=initMap&loading=async`;

    // Add libraries
    if (LIBRARIES.length > 0) {
        src += `&libraries=${LIBRARIES.join(',')}`;
    }

    script.src = src;
    script.async = true;
    script.defer = true;

    // Set up error handling
    script.onerror = function() {
        console.error('Failed to load Google Maps API');
        ToastManager.show('Failed to load Google Maps. Please refresh the page or check your internet connection.', 'error');

        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }
    };

    document.head.appendChild(script);
}

// Load Google Maps when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Optional: Add a timeout to show loading message
    setTimeout(() => {
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay && loadingOverlay.style.display !== 'none') {
            ToastManager.show('Loading Google Maps...', 'info');
        }
    }, 2000);

    loadGoogleMaps();
});


</script>

@endsection
