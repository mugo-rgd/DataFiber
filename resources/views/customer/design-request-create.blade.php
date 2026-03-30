@extends('layouts.app')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Fibre Route Design Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-radius: 0.35rem;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e3e6f0;
        }
        .breadcrumb {
            background-color: transparent;
            padding: 0;
        }
        .map-container {
            height: 500px;
            border-radius: 0.35rem;
            overflow: hidden;
            position: relative;
            border: 1px solid #e3e6f0;
        }
        #google-map {
            height: 100%;
            width: 100%;
        }
        .map-overlay {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            max-width: 300px;
        }
        .route-point {
            background-color: #4e73df;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .point-list {
            max-height: 200px;
            overflow-y: auto;
        }
        .point-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-bottom: 1px solid #e3e6f0;
        }
        .point-item:last-child {
            border-bottom: none;
        }
        .btn-remove-point {
            color: #e74a3b;
            background: none;
            border: none;
            padding: 0;
            margin-left: auto;
            cursor: pointer;
        }
        .distance-display {
            background-color: #1cc88a;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
        .instructions {
            background-color: #f8f9fc;
            border-left: 3px solid #4e73df;
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 0 4px 4px 0;
        }
        .legend {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e3e6f0;
        }
        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 0.85rem;
        }
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .substation-marker {
            background-color: #e74a3b;
        }
        .fibre-marker {
            background-color: #36b9cc;
        }
        .route-marker {
            background-color: #4e73df;
        }
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1001;
        }
        .btn-group-vertical {
            width: 100%;
        }
        .btn-map-control {
            margin-bottom: 5px;
            font-size: 0.8rem;
        }
        .gm-style .gm-style-iw-c {
            padding: 12px;
            border-radius: 8px;
        }
        .substation-info {
            max-width: 250px;
        }
        .capacity-badge {
            font-size: 0.7rem;
            margin-left: 5px;
        }
        .toggle-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.95);
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
        }
        .substation-filter {
            margin-top: 10px;
        }
        .filter-badge {
            font-size: 0.7rem;
            cursor: pointer;
            margin: 2px;
        }
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .validation-alert {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header with Back Button -->
        <div class="header-actions">
            <div>
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-drafting-compass text-primary"></i> New Fibre Route Design Request
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customer.design-requests') }}">Design Requests</a></li>
                        <li class="breadcrumb-item active">New Request</li>
                    </ol>
                </nav>
            </div>
            <!-- Go Back Button -->
            <button type="button" class="btn btn-outline-secondary" onclick="goBack()">
                <i class="fas fa-arrow-left me-2"></i>Go Back
            </button>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-plus-circle text-success"></i> Create New Fibre Route Design Request</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('customer.design-requests.store') }}" method="POST" id="design-request-form">
                            @csrf

                            <div class="instructions">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                <strong>Instructions:</strong> Define your route either by clicking on the map OR by entering route details manually below. Substations with available dark fibre connections are shown in red.
                            </div>

                            <div class="mb-4">
                                <label for="title" class="form-label">Request Title *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" required
                                       placeholder="e.g., Nairobi CBD to Industrial Area Fibre Connection"
                                       value="{{ old('title') }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">A clear, descriptive title for your request</div>
                            </div>

                            <!-- Route Information Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">
                                                <i class="fas fa-route me-2"></i>Route Information
                                                <small class="text-muted">(Define route on map OR enter details manually)</small>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="cores_required" class="form-label">Cores Required</label>
                                                    <input type="number" class="form-control @error('cores_required') is-invalid @enderror"
                                                           id="cores_required" name="cores_required"
                                                           value="{{ old('cores_required') }}"
                                                           placeholder="e.g., 24"
                                                           min="1">
                                                    @error('cores_required')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="distance" class="form-label">Distance (km)</label>
                                                    <input type="number" step="0.01" class="form-control @error('distance') is-invalid @enderror"
                                                           id="distance" name="distance"
                                                           value="{{ old('distance') }}"
                                                           placeholder="e.g., 5.2"
                                                           min="0">
                                                    @error('distance')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="terms" class="form-label">Terms</label>
                                                    <input type="text" class="form-control @error('terms') is-invalid @enderror"
                                                           id="terms" name="terms"
                                                           value="{{ old('terms') }}"
                                                           placeholder="e.g., 12 months">
                                                    @error('terms')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="alert alert-info mt-3">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Note:</strong> If you define the route on the map, the distance will be automatically calculated and manual distance entry will be ignored.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Map Section -->
                            <div class="mb-4">
                                <label class="form-label">Define Fibre Route on Map (Optional)</label>
                                <div class="alert alert-warning validation-alert" id="route-validation-alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Please either define your route on the map OR enter route details manually above.
                                </div>
                                <div class="map-container mb-3">
                                    <div id="google-map"></div>
                                    <div class="loading-overlay" id="loading-overlay">
                                        <div class="text-center">
                                            <div class="spinner-border text-primary mb-2" role="status"></div>
                                            <p>Loading Google Maps and Substation Data...</p>
                                        </div>
                                    </div>
                                    <div class="map-overlay">
                                        <div class="distance-display" id="total-distance">0 km</div>
                                        <div class="btn-group-vertical">
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-map-control" id="add-point-mode">
                                                <i class="fas fa-map-marker-alt me-1"></i>Add Points Mode
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-map-control" id="clear-route">
                                                <i class="fas fa-trash-alt me-1"></i>Clear Route
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info btn-map-control" id="reset-view">
                                                <i class="fas fa-globe-africa me-1"></i>Reset to Nairobi
                                            </button>
                                        </div>

                                        <div class="legend">
                                            <h6 class="mb-2">Map Legend</h6>
                                            <div class="legend-item">
                                                <div class="legend-color substation-marker"></div>
                                                <span>Substation (Dark Fibre Available)</span>
                                            </div>
                                            <div class="legend-item">
                                                <div class="legend-color route-marker"></div>
                                                <span>Your Route Point</span>
                                            </div>
                                        </div>

                                        <div class="substation-filter">
                                            <h6 class="mb-2">Filter by Owner:</h6>
                                            <div id="owner-filters">
                                                <!-- Owner filters will be dynamically added here -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="toggle-controls">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="toggle-substations" checked>
                                            <label class="form-check-label" for="toggle-substations">Show Substations</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header py-2">
                                        <h6 class="mb-0"><i class="fas fa-map-marker-alt me-1"></i>Route Points</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="point-list" id="point-list">
                                            <div class="point-item text-muted">
                                                No points added yet. Click on the map to add points.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @error('route_points')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Hidden input to store route points -->
                            <input type="hidden" name="route_points" id="route-points-input">

                            <div class="mb-4">
                                <label for="description" class="form-label">Project Description *</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required
                                          placeholder="Describe your fibre route requirements...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Detailed description of what you need</div>
                            </div>

                            <div class="mb-4">
                                <label for="technical_requirements" class="form-label">Technical Requirements *</label>
                                <textarea class="form-control @error('technical_requirements') is-invalid @enderror" id="technical_requirements" name="technical_requirements" rows="6" required
                                          placeholder="Specify technical requirements...">{{ old('technical_requirements') }}</textarea>
                                @error('technical_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Include: required bandwidth, distance, number of connections,
                                    existing infrastructure, and any special requirements
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" id="submit-btn">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Request444
                                </button>
                                <a href="{{ route('customer.design-requests') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <!-- Additional Go Back Button at Bottom -->
                                <button type="button" class="btn btn-outline-secondary" onclick="goBack()">
                                    <i class="fas fa-arrow-left me-2"></i>Go Back
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Go Back Function
        function goBack() {
            if (document.referrer && document.referrer.includes(window.location.host)) {
                // If there's a previous page in the same domain, go back
                window.history.back();
            } else {
                // Otherwise, redirect to design requests list
                window.location.href = "{{ route('customer.design-requests') }}";
            }
        }

        // Initialize Google Map with Nairobi, Kenya as center
        let map;
        let points = [];
        let pointCounter = 1;
        let addPointMode = true;
        let clickListener;
        let routePolyline;
        let substationMarkers = [];
        let activeOwnerFilter = 'all';

        // Nairobi coordinates
        const NAIROBI_CENTER = { lat: -1.2921, lng: 36.8219 };

        function initMap() {
            // Create Google Map
            map = new google.maps.Map(document.getElementById('google-map'), {
                center: NAIROBI_CENTER,
                zoom: 12,
                mapTypeId: 'hybrid',
                tilt: 45,
                styles: [
                    {
                        featureType: "poi",
                        elementType: "labels",
                        stylers: [{ visibility: "on" }]
                    }
                ]
            });

            // Initialize route polyline
            routePolyline = new google.maps.Polyline({
                path: [],
                geodesic: true,
                strokeColor: '#4e73df',
                strokeOpacity: 0.8,
                strokeWeight: 4
            });
            routePolyline.setMap(map);

            // Load substation data from database
            loadSubstationData();

            // Set up control buttons
            document.getElementById('add-point-mode').addEventListener('click', function() {
                addPointMode = !addPointMode;
                this.innerHTML = addPointMode ?
                    '<i class="fas fa-map-marker-alt me-1"></i>Add Points Mode' :
                    '<i class="fas fa-hand-paper me-1"></i>Navigation Mode';
                this.classList.toggle('btn-outline-primary');
                this.classList.toggle('btn-outline-secondary');
            });

            document.getElementById('clear-route').addEventListener('click', clearRoute);
            document.getElementById('reset-view').addEventListener('click', resetView);

            // Toggle substations visibility
            document.getElementById('toggle-substations').addEventListener('change', function() {
                substationMarkers.forEach(marker => {
                    marker.setVisible(this.checked && (activeOwnerFilter === 'all' || marker.owner === activeOwnerFilter));
                });
            });

            // Add keyboard shortcut for going back (Alt + Left Arrow)
            document.addEventListener('keydown', function(e) {
                if (e.altKey && e.key === 'ArrowLeft') {
                    e.preventDefault();
                    goBack();
                }
            });

            // Update validation when manual fields change
            document.getElementById('cores_required').addEventListener('input', validateForm);
            document.getElementById('distance').addEventListener('input', validateForm);
            document.getElementById('terms').addEventListener('input', validateForm);
        }

        function loadSubstationData() {
            // Fetch substation data from your backend API
            fetch('/api/fibre-stations')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(substations => {
                    addSubstationMarkers(substations);
                    setupOwnerFilters(substations);
                    document.getElementById('loading-overlay').style.display = 'none';
                    setupClickListener();
                })
                .catch(error => {
                    console.error('Error loading substation data:', error);
                    // Fallback to sample data if API fails
                    loadSampleData();
                });
        }

        function loadSampleData() {
            // Sample data as fallback
            const sampleSubstations = [
                {
                    id: 1,
                    lat: -1.2833,
                    lng: 36.8167,
                    name: "Nairobi Central Substation",
                    capacity: "132kV",
                    fibreStatus: "Available",
                    darkFibreCores: 24,
                    connectionType: "Direct Tap",
                    owner: "KETRACO",
                    area: "Nairobi Central",
                    location: "CBD"
                },
                {
                    id: 2,
                    lat: -1.3000,
                    lng: 36.8000,
                    name: "Industrial Area Substation",
                    capacity: "66kV",
                    fibreStatus: "Available",
                    darkFibreCores: 12,
                    connectionType: "Patch Panel",
                    owner: "KPLC",
                    area: "Industrial Area",
                    location: "Nairobi"
                }
            ];

            addSubstationMarkers(sampleSubstations);
            setupOwnerFilters(sampleSubstations);
            document.getElementById('loading-overlay').style.display = 'none';
            setupClickListener();
        }

        function addSubstationMarkers(substations) {
            substationMarkers = [];

            substations.forEach(substation => {
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(substation.lat), lng: parseFloat(substation.lng) },
                    map: map,
                    title: substation.name,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 10,
                        fillColor: '#e74a3b',
                        fillOpacity: 0.9,
                        strokeColor: '#ffffff',
                        strokeWeight: 2
                    },
                    animation: google.maps.Animation.DROP
                });

                // Store owner information with marker for filtering
                marker.owner = substation.owner;

                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div class="substation-info">
                            <h6 class="mb-1"><i class="fas fa-bolt text-warning"></i> ${substation.name}</h6>
                            <div class="mb-2">
                                <span class="badge bg-success">${substation.fibreStatus}</span>
                                <span class="badge bg-primary capacity-badge">${substation.capacity}</span>
                            </div>
                            <table class="table table-sm table-borderless mb-2">
                                <tr>
                                    <td><strong>Fibre Cores:</strong></td>
                                    <td>${substation.darkFibreCores}</td>
                                </tr>
                                <tr>
                                    <td><strong>Connection:</strong></td>
                                    <td>${substation.connectionType}</td>
                                </tr>
                                <tr>
                                    <td><strong>Owner:</strong></td>
                                    <td>${substation.owner}</td>
                                </tr>
                                <tr>
                                    <td><strong>Area:</strong></td>
                                    <td>${substation.area || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Location:</strong></td>
                                    <td>${substation.location || 'N/A'}</td>
                                </tr>
                            </table>
                            <p class="text-muted mb-1"><small>Lat: ${parseFloat(substation.lat).toFixed(6)}</small></p>
                            <p class="text-muted mb-0"><small>Lng: ${parseFloat(substation.lng).toFixed(6)}</small></p>
                            <button class="btn btn-sm btn-primary mt-2 w-100" onclick="selectSubstation(${parseFloat(substation.lat)}, ${parseFloat(substation.lng)})">
                                <i class="fas fa-plug me-1"></i>Use This Substation
                            </button>
                        </div>
                    `
                });

                marker.addListener('click', function() {
                    infoWindow.open(map, marker);
                });

                substationMarkers.push(marker);
            });
        }

        function setupOwnerFilters(substations) {
            const ownerFilters = document.getElementById('owner-filters');
            const owners = [...new Set(substations.map(s => s.owner))];

            // Add "All" filter
            const allBadge = document.createElement('span');
            allBadge.className = 'badge bg-primary filter-badge';
            allBadge.textContent = 'All';
            allBadge.onclick = () => filterByOwner('all');
            ownerFilters.appendChild(allBadge);

            // Add owner-specific filters
            owners.forEach(owner => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-secondary filter-badge';
                badge.textContent = owner;
                badge.onclick = () => filterByOwner(owner);
                ownerFilters.appendChild(badge);
            });
        }

        function filterByOwner(owner) {
            activeOwnerFilter = owner;
            substationMarkers.forEach(marker => {
                const shouldShow = owner === 'all' || marker.owner === owner;
                marker.setVisible(shouldShow && document.getElementById('toggle-substations').checked);
            });

            // Update active filter badges
            document.querySelectorAll('#owner-filters .badge').forEach(badge => {
                if (badge.textContent === owner || (owner === 'all' && badge.textContent === 'All')) {
                    badge.classList.remove('bg-secondary');
                    badge.classList.add('bg-primary');
                } else {
                    badge.classList.remove('bg-primary');
                    badge.classList.add('bg-secondary');
                }
            });
        }

        function setupClickListener() {
            clickListener = map.addListener('click', function(event) {
                if (addPointMode) {
                    addPoint(event.latLng);
                }
            });
        }

        function addPoint(latLng) {
            // Create marker for the point
            const marker = new google.maps.Marker({
                position: latLng,
                map: map,
                title: `Point ${pointCounter}`,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 8,
                    fillColor: '#4e73df',
                    fillOpacity: 1,
                    strokeColor: '#ffffff',
                    strokeWeight: 2
                },
                zIndex: 1000
            });

            // Add info window for the point
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div class="p-2">
                        <h6 class="mb-1">Route Point ${pointCounter}</h6>
                        <p class="mb-1">Lat: ${latLng.lat().toFixed(6)}</p>
                        <p class="mb-1">Lng: ${latLng.lng().toFixed(6)}</p>
                        <button class="btn btn-sm btn-danger mt-1" onclick="removePoint(${pointCounter})">Remove</button>
                    </div>
                `
            });

            marker.addListener('click', function() {
                infoWindow.open(map, marker);
            });

            // Add to points array
            points.push({
                id: pointCounter,
                lat: latLng.lat(),
                lng: latLng.lng(),
                marker: marker,
                infoWindow: infoWindow
            });

            // Update route polyline
            updateRoutePolyline();

            // Update point list
            updatePointList();

            // Update distance
            updateDistance();

            // Update hidden input with route points
            updateRoutePointsInput();

            // Validate form after adding point
            validateForm();

            pointCounter++;
        }

        function selectSubstation(lat, lng) {
            // Add the substation as a route point
            const latLng = new google.maps.LatLng(lat, lng);
            addPoint(latLng);

            // Update the title to include substation reference
            const titleInput = document.getElementById('title');
            if (!titleInput.value.includes('Substation')) {
                titleInput.value += ' (Substation Connection)';
            }
        }

        function updateRoutePolyline() {
            const path = points.map(point => ({
                lat: point.lat,
                lng: point.lng
            }));
            routePolyline.setPath(path);
        }

        function updatePointList() {
            const pointList = document.getElementById('point-list');
            pointList.innerHTML = '';

            if (points.length === 0) {
                pointList.innerHTML = '<div class="point-item text-muted">No points added yet. Click on the map to add points.</div>';
                return;
            }

            points.forEach(point => {
                const pointItem = document.createElement('div');
                pointItem.className = 'point-item';

                pointItem.innerHTML = `
                    <div class="route-point me-2">${point.id}</div>
                    <div>Point ${point.id} (${point.lat.toFixed(4)}, ${point.lng.toFixed(4)})</div>
                    <button type="button" class="btn-remove-point" data-id="${point.id}">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                pointList.appendChild(pointItem);
            });

            // Add event listeners to remove buttons
            document.querySelectorAll('.btn-remove-point').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = parseInt(this.dataset.id);
                    removePoint(id);
                });
            });
        }

        function updateRoutePointsInput() {
            const routePointsInput = document.getElementById('route-points-input');
            routePointsInput.value = JSON.stringify(points.map(point => ({
                lat: point.lat,
                lng: point.lng,
                order: point.id
            })));
        }

        function removePoint(id) {
            // Remove from array
            const pointIndex = points.findIndex(p => p.id === id);
            if (pointIndex !== -1) {
                // Remove marker from map and close info window
                points[pointIndex].marker.setMap(null);
                points[pointIndex].infoWindow.close();
                points.splice(pointIndex, 1);

                // Update route polyline
                updateRoutePolyline();

                // Update point list and distance
                updatePointList();
                updateDistance();

                // Update hidden input
                updateRoutePointsInput();

                // Renumber remaining points
                renumberPoints();

                // Validate form after removing point
                validateForm();
            }
        }

        function renumberPoints() {
            points.forEach((point, index) => {
                point.id = index + 1;
                point.marker.setTitle(`Point ${point.id}`);

                // Update info window content
                point.infoWindow.setContent(`
                    <div class="p-2">
                        <h6 class="mb-1">Route Point ${point.id}</h6>
                        <p class="mb-1">Lat: ${point.lat.toFixed(6)}</p>
                        <p class="mb-1">Lng: ${point.lng.toFixed(6)}</p>
                        <button class="btn btn-sm btn-danger mt-1" onclick="removePoint(${point.id})">Remove</button>
                    </div>
                `);
            });
            pointCounter = points.length + 1;
            updatePointList();
        }

        function updateDistance() {
            const totalDistance = document.getElementById('total-distance');
            const distanceInput = document.getElementById('distance');

            if (points.length < 2) {
                totalDistance.textContent = '0 km';
                return;
            }

            // Calculate total distance using Haversine formula
            let distance = 0;
            for (let i = 1; i < points.length; i++) {
                distance += calculateDistance(
                    points[i-1].lat, points[i-1].lng,
                    points[i].lat, points[i].lng
                );
            }

            const calculatedDistance = parseFloat(distance.toFixed(2));
            totalDistance.textContent = `${calculatedDistance} km`;

            // Auto-fill distance field if route is defined on map
            if (points.length >= 2) {
                distanceInput.value = calculatedDistance;
                distanceInput.readOnly = true;
                distanceInput.title = "Distance automatically calculated from map route";
            } else {
                distanceInput.readOnly = false;
                distanceInput.title = "";
            }
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Earth's radius in km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a =
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        function clearRoute() {
            // Remove all point markers
            points.forEach(point => {
                point.marker.setMap(null);
                point.infoWindow.close();
            });

            // Reset points array
            points = [];
            pointCounter = 1;

            // Clear route polyline
            routePolyline.setPath([]);

            // Update UI
            updatePointList();
            updateDistance();
            updateRoutePointsInput();

            // Reset distance input
            const distanceInput = document.getElementById('distance');
            distanceInput.readOnly = false;
            distanceInput.title = "";

            // Validate form after clearing route
            validateForm();
        }

        function resetView() {
            map.setCenter(NAIROBI_CENTER);
            map.setZoom(12);
            map.setTilt(45);
        }

        // Form validation function
        function validateForm() {
            const hasMapRoute = points.length >= 2;
            const hasManualEntry = document.getElementById('cores_required').value ||
                                  document.getElementById('distance').value ||
                                  document.getElementById('terms').value;

            const validationAlert = document.getElementById('route-validation-alert');

            if (!hasMapRoute && !hasManualEntry) {
                validationAlert.style.display = 'block';
                return false;
            } else {
                validationAlert.style.display = 'none';
                return true;
            }
        }

        // Form submission
        document.getElementById('design-request-form').addEventListener('submit', function(e) {
            const hasMapRoute = points.length >= 2;
            const hasManualEntry = document.getElementById('cores_required').value ||
                                  document.getElementById('distance').value ||
                                  document.getElementById('terms').value;

            // Validate that user has either defined route on map OR entered manual details
            if (!hasMapRoute && !hasManualEntry) {
                e.preventDefault();
                document.getElementById('route-validation-alert').style.display = 'block';
                // Scroll to validation message
                document.getElementById('route-validation-alert').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return;
            }

            // If user defined route on map, clear manual distance (we'll use calculated one)
            if (hasMapRoute && document.getElementById('distance').value) {
                // Distance is already auto-filled and made read-only in updateDistance()
            }

            // Debug: Log what's being sent
            console.log('Route points being submitted:', document.getElementById('route-points-input').value);
            console.log('Point count:', points.length);
            console.log('Manual entries:', {
                cores: document.getElementById('cores_required').value,
                distance: document.getElementById('distance').value,
                terms: document.getElementById('terms').value
            });

            // Show loading state
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
        });

        // Initial form validation
        document.addEventListener('DOMContentLoaded', function() {
            validateForm();
        });
    </script>
    <!-- Google Maps API with provided API key -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB77eGv2kN5Lo-ZpD01-a277yCr2u-9Fto&callback=initMap&libraries=geometry" async defer></script>
</body>
</html>
@endsection
