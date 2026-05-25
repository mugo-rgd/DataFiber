@extends('layouts.app')

@section('title', 'GIS Fibre Analytics - Dark Fibre CRM')

@section('content')

<div class="container-fluid p-4">

    <div class="d-flex justify-content-between mb-4 align-items-center flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="fas fa-network-wired text-kp-blue me-2"></i>
                Dark Fibre Network GIS
            </h3>
            <small class="text-muted">
                Stations, nodes, segments and capacity utilization heatmap
            </small>
        </div>

        <a href="{{ route('executive.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient-kp-blue text-white">
                <div class="card-body">
                    <small class="text-uppercase fw-semibold opacity-75">Stations</small>
                    <h3 class="fw-bold mt-2">{{ number_format(count($stations)) }}</h3>
                    <small class="opacity-75">Network Points of Presence</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient-kp-green text-white">
                <div class="card-body">
                    <small class="text-uppercase fw-semibold opacity-75">Nodes</small>
                    <h3 class="fw-bold mt-2">{{ number_format(count($nodes)) }}</h3>
                    <small class="opacity-75">Network Interconnection Points</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient-info text-white">
                <div class="card-body">
                    <small class="text-uppercase fw-semibold opacity-75">Segments</small>
                    <h3 class="fw-bold mt-2">{{ number_format(count($segments)) }}</h3>
                    <small class="opacity-75">Fibre Routes</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient-warning text-dark">
                <div class="card-body">
                    <small class="text-uppercase fw-semibold opacity-75">Avg Utilization</small>
                    @php
                        $avgUtilization = count($stations) > 0 ? collect($stations)->avg('utilizationPercent') : 0;
                    @endphp
                    <h3 class="fw-bold mt-2">{{ number_format($avgUtilization, 2) }}%</h3>
                    <small class="opacity-75">Network Capacity Usage</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Map Controls Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary" id="satelliteView">
                        <i class="fas fa-satellite me-1"></i> Satellite
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="roadmapView">
                        <i class="fas fa-road me-1"></i> Roadmap
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="terrainView">
                        <i class="fas fa-mountain me-1"></i> Terrain
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="hybridView">
                        <i class="fas fa-layer-group me-1"></i> Hybrid
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="zoomInBtn">
                        <i class="fas fa-search-plus"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="zoomOutBtn">
                        <i class="fas fa-search-minus"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" id="resetViewBtn">
                        <i class="fas fa-home"></i> Reset
                    </button>
                </div>
                <div class="small text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    <span id="coordinatesDisplay">Click on map to get coordinates</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Map Container --}}
    <div id="leafletError" class="alert alert-danger d-none rounded-4 shadow-sm">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Leaflet map library failed to load. Check CDN access or install Leaflet locally.
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-map-marked-alt text-kp-green me-2"></i>
                    Network Capacity Map
                </h5>
                <div class="d-flex gap-3 flex-wrap">
                    <span><i class="fas fa-circle text-success"></i> Low (0-50%)</span>
                    <span><i class="fas fa-circle text-warning"></i> Medium (50-75%)</span>
                    <span><i class="fas fa-circle" style="color: #fd7e14;"></i> High (75-90%)</span>
                    <span><i class="fas fa-circle text-danger"></i> Critical (90%+)</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="networkMap" style="height: 65vh; min-height: 500px; width: 100%;"></div>
        </div>
    </div>

</div>

{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.css" />

{{-- Leaflet JavaScript --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

{{-- Google Maps Tiles Plugin --}}
<script src="https://unpkg.com/leaflet.gridlayer.googlemutant@latest/Leaflet.GoogleMutant.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Check if Leaflet is loaded
    if (typeof L === 'undefined') {
        document.getElementById('leafletError').classList.remove('d-none');
        console.error('Leaflet library not loaded');
        return;
    }

    // Get data from PHP
    var stations = @json($stations);
    var nodes = @json($nodes);
    var segments = @json($segments);

    console.log('Stations:', stations.length);
    console.log('Nodes:', nodes.length);
    console.log('Segments:', segments.length);

    // Initialize map centered on Kenya
    var map = L.map('networkMap').setView([-1.286389, 36.817223], 7);

    // Define tile layers
    var tileLayers = {
        Roadmap: L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19,
            minZoom: 6
        }),

        Satellite: L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps',
            maxZoom: 20,
            minZoom: 6
        }),

        Terrain: L.tileLayer('https://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}', {
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps',
            maxZoom: 20,
            minZoom: 6
        }),

        Hybrid: L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps',
            maxZoom: 20,
            minZoom: 6
        })
    };

    // Alternative OpenStreetMap layers (if Google tiles have issues)
    var osmLayers = {
        'OpenStreetMap': L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }),
        'Satellite (ESRI)': L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; <a href="https://www.esri.com">ESRI</a>'
        }),
        'Topographic': L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://opentopomap.org">OpenTopoMap</a>'
        }),
        'Dark Mode': L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>'
        })
    };

    // Add default layer
    tileLayers.Roadmap.addTo(map);

    // Create layer groups
    var stationLayer = L.layerGroup().addTo(map);
    var nodeLayer = L.layerGroup().addTo(map);
    var segmentLayer = L.layerGroup().addTo(map);

    var heatPoints = [];
    var bounds = [];

    // Helper functions
    function getCapacityColor(percent) {
        if (percent >= 90) return '#dc3545';      // Critical - Red
        if (percent >= 75) return '#fd7e14';      // High - Orange
        if (percent >= 50) return '#ffc107';      // Medium - Yellow
        return '#28a745';                          // Low - Green
    }

    function getRadius(percent) {
        if (percent >= 90) return 14;
        if (percent >= 75) return 11;
        if (percent >= 50) return 9;
        return 7;
    }

    // Add stations
    stations.forEach(function(station) {
        var lat = parseFloat(station.lat);
        var lng = parseFloat(station.lng);

        if (isNaN(lat) || isNaN(lng)) return;

        var percent = station.utilizationPercent || 0;
        var color = getCapacityColor(percent);
        var radius = getRadius(percent);

        var popupContent = `
            <div style="min-width: 240px;">
                <strong style="font-size: 14px; color: #0066B3;">🏢 ${station.name || 'Station'}</strong><br>
                <hr style="margin: 5px 0;">
                <table style="width: 100%; font-size: 12px;">
                    <tr><td style="padding: 2px;"><strong>Owner:</strong></td><td style="padding: 2px;">${station.owner || 'N/A'}</td></tr>
                    <tr><td style="padding: 2px;"><strong>Area:</strong></td><td style="padding: 2px;">${station.area || 'N/A'}</td></tr>
                    <tr><td style="padding: 2px;"><strong>Status:</strong></td><td style="padding: 2px;"><span class="badge bg-${station.fibreStatus === 'Active' ? 'success' : 'warning'}">${station.fibreStatus || 'N/A'}</span></td></tr>
                    <tr><td style="padding: 2px;"><strong>Total Cores:</strong></td><td style="padding: 2px;">${station.darkFibreCores}</td></tr>
                    <tr><td style="padding: 2px;"><strong>Used Cores:</strong></td><td style="padding: 2px;">${station.usedCores}</td></tr>
                    <tr><td style="padding: 2px;"><strong>Available Cores:</strong></td><td style="padding: 2px;">${station.availableCores}</td></tr>
                    <tr><td style="padding: 2px;"><strong>Utilization:</strong></td><td style="padding: 2px;"><strong style="color: ${color};">${percent.toFixed(2)}%</strong></td></tr>
                </table>
            </div>
        `;

        var marker = L.circleMarker([lat, lng], {
            radius: radius,
            color: color,
            fillColor: color,
            fillOpacity: 0.85,
            weight: 2,
            opacity: 1
        }).bindPopup(popupContent);

        marker.addTo(stationLayer);

        // Add to heatmap
        heatPoints.push([lat, lng, Math.max(percent / 100, 0.1)]);
        bounds.push([lat, lng]);
    });

    // Add nodes
    nodes.forEach(function(node) {
        var lat = parseFloat(node.latitude);
        var lng = parseFloat(node.longitude);

        if (isNaN(lat) || isNaN(lng)) return;

        var popupContent = `
            <div style="min-width: 180px;">
                <strong style="color: #0066B3;">📍 ${node.node_name || 'Node'}</strong><br>
                <hr style="margin: 5px 0;">
                <strong>Type:</strong> ${node.node_type || 'N/A'}<br>
                <strong>Region:</strong> ${node.region || 'N/A'}
            </div>
        `;

        var customIcon = L.divIcon({
            className: 'custom-node-icon',
            html: '<div style="background-color: #0066B3; width: 8px; height: 8px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 4px rgba(0,0,0,0.3);"></div>',
            iconSize: [12, 12],
            popupAnchor: [0, -6]
        });

        L.marker([lat, lng], { icon: customIcon })
            .bindPopup(popupContent)
            .addTo(nodeLayer);

        bounds.push([lat, lng]);
    });

    // Add segments
    segments.forEach(function(segment) {
        var sourceLat = parseFloat(segment.source_lat);
        var sourceLon = parseFloat(segment.source_lon);
        var destLat = parseFloat(segment.dest_lat);
        var destLon = parseFloat(segment.dest_lon);

        if (isNaN(sourceLat) || isNaN(sourceLon) || isNaN(destLat) || isNaN(destLon)) return;

        var status = segment.status || 'Active';
        var color = status === 'Damaged' ? '#dc3545' :
                    status === 'Planned' ? '#fd7e14' :
                    status === 'Decommissioned' ? '#6c757d' : '#28a745';

        var popupContent = `
            <div style="min-width: 220px;">
                <strong style="color: #0066B3;">🔗 ${segment.network_id || 'Network Segment'}</strong><br>
                <hr style="margin: 5px 0;">
                <strong>Route:</strong> ${segment.source_name || 'Unknown'} → ${segment.destination_name || 'Unknown'}<br>
                <strong>Distance:</strong> ${(segment.distance_km || 0).toFixed(2)} KM<br>
                <strong>Cores:</strong> ${segment.fiber_cores || 0}<br>
                <strong>Type:</strong> ${segment.link_type || 'N/A'}<br>
                <strong>Status:</strong> <span style="color: ${color};">${status}</span>
            </div>
        `;

        L.polyline([[sourceLat, sourceLon], [destLat, destLon]], {
            color: color,
            weight: 3,
            opacity: 0.8,
            smoothFactor: 1
        }).bindPopup(popupContent).addTo(segmentLayer);

        bounds.push([sourceLat, sourceLon]);
        bounds.push([destLat, destLon]);
    });

    // Add heatmap layer
    var heatLayer = L.heatLayer(heatPoints, {
        radius: 35,
        blur: 20,
        maxZoom: 12,
        minOpacity: 0.3
    });

    // Layer control with base maps
    var baseMaps = {
        "🗺️ Roadmap": tileLayers.Roadmap,
        "🛰️ Satellite": tileLayers.Satellite,
        "⛰️ Terrain": tileLayers.Terrain,
        "🌍 Hybrid": tileLayers.Hybrid
    };

    var overlayMaps = {
        "🏢 Fibre Stations": stationLayer,
        "📍 Network Nodes": nodeLayer,
        "🔗 Fibre Segments": segmentLayer,
        "🔥 Capacity Heatmap": heatLayer
    };

    L.control.layers(baseMaps, overlayMaps, {
        collapsed: false,
        position: 'topright',
        sortLayers: true
    }).addTo(map);

    // Add scale bar
    L.control.scale({ metric: true, imperial: false, position: 'bottomleft' }).addTo(map);

    // Add coordinate display on click
    map.on('click', function(e) {
        var lat = e.latlng.lat.toFixed(6);
        var lng = e.latlng.lng.toFixed(6);
        document.getElementById('coordinatesDisplay').innerHTML =
            `<i class="fas fa-map-marker-alt me-1"></i> Lat: ${lat}, Lng: ${lng}`;

        // Copy to clipboard on click
        var coordText = `${lat}, ${lng}`;
        navigator.clipboard.writeText(coordText).then(function() {
            var originalText = document.getElementById('coordinatesDisplay').innerHTML;
            document.getElementById('coordinatesDisplay').innerHTML =
                `<i class="fas fa-check-circle text-success me-1"></i> Copied: ${coordText}`;
            setTimeout(function() {
                document.getElementById('coordinatesDisplay').innerHTML = originalText;
            }, 2000);
        });
    });

    // Zoom controls
    document.getElementById('zoomInBtn').addEventListener('click', function() {
        map.zoomIn();
    });

    document.getElementById('zoomOutBtn').addEventListener('click', function() {
        map.zoomOut();
    });

    document.getElementById('resetViewBtn').addEventListener('click', function() {
        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50] });
        } else {
            map.setView([-1.286389, 36.817223], 7);
        }
    });

    // Layer switcher buttons
    document.getElementById('satelliteView').addEventListener('click', function() {
        tileLayers.Satellite.addTo(map);
        updateActiveButton('satelliteView');
    });

    document.getElementById('roadmapView').addEventListener('click', function() {
        tileLayers.Roadmap.addTo(map);
        updateActiveButton('roadmapView');
    });

    document.getElementById('terrainView').addEventListener('click', function() {
        tileLayers.Terrain.addTo(map);
        updateActiveButton('terrainView');
    });

    document.getElementById('hybridView').addEventListener('click', function() {
        tileLayers.Hybrid.addTo(map);
        updateActiveButton('hybridView');
    });

    function updateActiveButton(activeId) {
        ['satelliteView', 'roadmapView', 'terrainView', 'hybridView'].forEach(function(id) {
            var btn = document.getElementById(id);
            if (id === activeId) {
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-primary');
            } else {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-secondary');
            }
        });
    }

    // Fit bounds to show all data
    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [50, 50] });
    } else {
        map.setView([-1.286389, 36.817223], 7);
    }

    // Add legend control
    var legend = L.control({ position: 'bottomright' });
    legend.onAdd = function(map) {
        var div = L.DomUtil.create('div', 'info legend');
        div.innerHTML = `
            <div style="background: white; padding: 10px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); min-width: 140px;">
                <strong style="color: #0066B3;">📊 Utilization</strong><br>
                <i class="fas fa-circle" style="color: #28a745;"></i> 0-50% (Low)<br>
                <i class="fas fa-circle" style="color: #ffc107;"></i> 50-75% (Medium)<br>
                <i class="fas fa-circle" style="color: #fd7e14;"></i> 75-90% (High)<br>
                <i class="fas fa-circle" style="color: #dc3545;"></i> 90%+ (Critical)<br>
                <hr style="margin: 5px 0;">
                <small><i class="fas fa-info-circle"></i> Click on markers for details</small>
            </div>
        `;
        return div;
    };
    legend.addTo(map);

    console.log('Map initialized successfully with Google layers');
});
</script>

<style>
.rounded-4 {
    border-radius: 1rem !important;
}

.bg-gradient-kp-blue {
    background: linear-gradient(135deg, #0066B3 0%, #005499 100%);
}

.bg-gradient-kp-green {
    background: linear-gradient(135deg, #009639 0%, #00802c 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #d39e00 100%);
}

.leaflet-popup-content {
    font-size: 12px;
    min-width: 220px;
}

.leaflet-popup-content hr {
    margin: 5px 0;
}

.custom-node-icon {
    background: transparent;
}

/* Map container responsive */
#networkMap {
    width: 100%;
    height: 65vh;
    min-height: 500px;
    border-radius: 0 0 1rem 1rem;
}

/* Legend styling */
.info.legend {
    background: white;
    padding: 8px 12px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Button group styling */
.btn-group .btn {
    transition: all 0.2s ease;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    #networkMap {
        height: 50vh;
        min-height: 400px;
    }

    .btn-group {
        flex-wrap: wrap;
    }
}
</style>

@endsection
