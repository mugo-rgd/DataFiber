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

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-3" id="showStatsBtn">
                <i class="fas fa-chart-bar me-2"></i>Show Stats
            </button>
            <a href="{{ route('executive.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4" id="statsPanel">
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
        <div class="card-body py-3">
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
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-success" id="showAllSegmentsBtn">
                        <i class="fas fa-eye me-1"></i> Show All
                    </button>
                    <button type="button" class="btn btn-outline-warning" id="showActiveOnlyBtn">
                        <i class="fas fa-play me-1"></i> Active Only
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="showDamagedOnlyBtn">
                        <i class="fas fa-exclamation-triangle me-1"></i> Damaged Only
                    </button>
                </div>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary" id="snapToNodesBtn">
                        <i class="fas fa-magnet me-1"></i> Snap to Nodes (50km)
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="showConnectionsBtn">
                        <i class="fas fa-link me-1"></i> Show Connections (50km)
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
                    <button type="button" class="btn btn-sm btn-outline-info" id="locateMeBtn">
                        <i class="fas fa-location-dot"></i>
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
                    <span><i class="fas fa-circle text-primary"></i> Active Segment</span>
                    <span><i class="fas fa-circle text-danger"></i> Damaged Segment</span>
                    <span><i class="fas fa-circle text-secondary"></i> Decommissioned</span>
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

    // Add default layer
    var currentBaseLayer = tileLayers.Roadmap;
    currentBaseLayer.addTo(map);

    // Create layer groups
    var stationLayer = L.layerGroup().addTo(map);
    var nodeLayer = L.layerGroup().addTo(map);
    var segmentLayer = L.layerGroup().addTo(map);
    var connectionsLayer = L.layerGroup().addTo(map);

    var allSegments = [];
    var bounds = [];
    var nodeCoordinates = [];
    var stationCoordinates = [];

    // Helper functions
    function getCapacityColor(percent) {
        if (percent >= 90) return '#dc3545';
        if (percent >= 75) return '#fd7e14';
        if (percent >= 50) return '#ffc107';
        return '#28a745';
    }

    function getRadius(percent) {
        if (percent >= 90) return 14;
        if (percent >= 75) return 11;
        if (percent >= 50) return 9;
        return 7;
    }

    function getSegmentColor(status) {
        switch(status) {
            case 'Damaged': return '#dc3545';
            case 'Planned': return '#fd7e14';
            case 'Decommissioned': return '#6c757d';
            default: return '#28a745';
        }
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        var R = 6371;
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLon = (lon2 - lon1) * Math.PI / 180;
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function findNearestNode(lat, lng, nodesList) {
        var nearest = null;
        var minDistance = Infinity;

        nodesList.forEach(function(node) {
            var distance = calculateDistance(lat, lng, node.lat, node.lng);
            if (distance < minDistance) {
                minDistance = distance;
                nearest = node;
            }
        });

        return { node: nearest, distance: minDistance };
    }

    // Add stations
    stations.forEach(function(station) {
        var lat = parseFloat(station.lat);
        var lng = parseFloat(station.lng);

        stationCoordinates.push({ lat: lat, lng: lng, name: station.name, station: station });

        var percent = station.utilizationPercent || 0;
        var color = getCapacityColor(percent);
        var radius = getRadius(percent);

        var popupContent = `
            <div style="min-width: 260px;">
                <div style="background: linear-gradient(135deg, #0066B3, #009639); color: white; padding: 8px; border-radius: 8px 8px 0 0; margin: -12px -12px 0 -12px;">
                    <strong><i class="fas fa-tower-cell me-2"></i> ${station.name || 'Station'}</strong>
                </div>
                <div style="padding: 10px 0;">
                    <table style="width: 100%; font-size: 12px;">
                        <tr><td style="padding: 2px;"><strong>Owner:</strong></td><td style="padding: 2px;">${station.owner || 'N/A'}</td></tr>
                        <tr><td style="padding: 2px;"><strong>Area:</strong></td><td style="padding: 2px;">${station.area || 'N/A'}</td></tr>
                        <tr><td style="padding: 2px;"><strong>Status:</strong></td><td style="padding: 2px;"><span class="badge bg-${station.fibreStatus === 'Active' ? 'success' : 'warning'}">${station.fibreStatus || 'N/A'}</span></td></tr>
                        <tr><td style="padding: 2px;"><strong>Total Cores:</strong></td><td style="padding: 2px;">${station.darkFibreCores || 0}</td></tr>
                        <tr><td style="padding: 2px;"><strong>Used Cores:</strong></td><td style="padding: 2px;">${station.usedCores || 0}</td></tr>
                        <tr><td style="padding: 2px;"><strong>Available Cores:</strong></td><td style="padding: 2px;">${station.availableCores || 0}</td></tr>
                        <tr><td style="padding: 2px;"><strong>Utilization:</strong></td><td style="padding: 2px;"><strong style="color: ${color};">${percent.toFixed(2)}%</strong></td></tr>
                    </table>
                </div>
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
        bounds.push([lat, lng]);
    });

    // Add nodes and collect coordinates for snapping
    nodes.forEach(function(node) {
        var lat = parseFloat(node.latitude);
        var lng = parseFloat(node.longitude);

        nodeCoordinates.push({ lat: lat, lng: lng, name: node.node_name, node: node });

        var popupContent = `
            <div style="min-width: 200px;">
                <div style="background: #0066B3; color: white; padding: 6px; border-radius: 6px 6px 0 0; margin: -6px -6px 0 -6px;">
                    <strong><i class="fas fa-microchip me-2"></i> ${node.node_name || 'Node'}</strong>
                </div>
                <div style="padding: 8px 0;">
                    <strong>Type:</strong> ${node.node_type || 'N/A'}<br>
                    <strong>Region:</strong> ${node.region || 'N/A'}<br>
                    <strong>Coordinates:</strong><br>
                    <small>Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}</small>
                </div>
            </div>
        `;

        var customIcon = L.divIcon({
            className: 'custom-node-icon',
            html: '<div style="background-color: #0066B3; width: 10px; height: 10px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 4px rgba(0,0,0,0.3);"></div>',
            iconSize: [14, 14],
            popupAnchor: [0, -7]
        });

        L.marker([lat, lng], { icon: customIcon })
            .bindPopup(popupContent)
            .addTo(nodeLayer);

        bounds.push([lat, lng]);
    });

    // Group segments by route and store original coordinates
    var routes = {};
    var segmentIdCounter = 0;

    segments.forEach(function(segment) {
        var routeId = segment.network_id || segment.segment_id || 'unknown';
        if (!routes[routeId]) {
            routes[routeId] = {
                id: routeId,
                name: segment.network_id || segment.segment_id || 'Route',
                status: segment.status || 'Active',
                fiber_cores: segment.fiber_cores || 0,
                points: [],
                polylineId: segmentIdCounter++
            };
        }

        var sourceLat = parseFloat(segment.source_lat);
        var sourceLon = parseFloat(segment.source_lon);
        var destLat = parseFloat(segment.dest_lat);
        var destLon = parseFloat(segment.dest_lon);

        routes[routeId].points.push({
            lat: sourceLat,
            lng: sourceLon,
            destLat: destLat,
            destLon: destLon
        });

        bounds.push([sourceLat, sourceLon]);
        bounds.push([destLat, destLon]);
    });

    // Create polylines for each route and store original coordinates
    Object.values(routes).forEach(function(route) {
        var allPoints = [];

        route.points.forEach(function(point) {
            allPoints.push([point.lat, point.lng]);
            allPoints.push([point.destLat, point.destLon]);
        });

        // Remove duplicate consecutive points
        var uniquePoints = [];
        for (var i = 0; i < allPoints.length; i++) {
            var point = allPoints[i];
            if (i === 0 || point[0] !== allPoints[i-1][0] || point[1] !== allPoints[i-1][1]) {
                uniquePoints.push(point);
            }
        }

        var color = getSegmentColor(route.status);
        var dashArray = route.status === 'Planned' ? '10, 10' : null;

        var totalDistance = 0;
        for (var i = 0; i < uniquePoints.length - 1; i++) {
            totalDistance += calculateDistance(
                uniquePoints[i][0], uniquePoints[i][1],
                uniquePoints[i+1][0], uniquePoints[i+1][1]
            );
        }

        var popupContent = `
            <div style="min-width: 260px;">
                <div style="background: linear-gradient(135deg, #0066B3, #009639); color: white; padding: 8px; border-radius: 8px 8px 0 0; margin: -12px -12px 0 -12px;">
                    <strong><i class="fas fa-network-wired me-2"></i> ${route.name}</strong>
                </div>
                <div style="padding: 10px 0;">
                    <table style="width: 100%; font-size: 12px;">
                        <tr><td style="padding: 2px;"><strong>Status:</strong></td><td style="padding: 2px;"><span style="color: ${color};">${route.status || 'Active'}</span></td></tr>
                        <tr><td style="padding: 2px;"><strong>Total Length:</strong></td><td style="padding: 2px;">${totalDistance.toFixed(2)} KM</span></td></tr>
                        <tr><td style="padding: 2px;"><strong>Fibre Cores:</strong></td><td style="padding: 2px;">${route.fiber_cores}</span></td></tr>
                        <tr><td style="padding: 2px;"><strong>Segments:</strong></td><td style="padding: 2px;">${route.points.length}</span></td></tr>
                    </table>
                </div>
            </div>
        `;

        if (uniquePoints.length >= 2) {
            var polyline = L.polyline(uniquePoints, {
                color: color,
                weight: 4,
                opacity: 0.9,
                smoothFactor: 1,
                dashArray: dashArray,
                lineJoin: 'round',
                lineCap: 'round',
                originalPoints: JSON.parse(JSON.stringify(uniquePoints)),
                routeId: route.id
            }).bindPopup(popupContent);

            polyline.addTo(segmentLayer);
            allSegments.push(polyline);
        }
    });

    // Create heatmap points
    var heatPoints = stations.map(function(station) {
        var lat = parseFloat(station.lat);
        var lng = parseFloat(station.lng);
        var intensity = Math.max((station.utilizationPercent || 0) / 100, 0.1);
        return [lat, lng, intensity];
    });

    var heatLayer = L.heatLayer(heatPoints, {
        radius: 35,
        blur: 20,
        maxZoom: 12,
        minOpacity: 0.3
    });

    // ============ SNAP TO NODES FUNCTIONALITY (50km) ============
    var snapEnabled = false;

    function snapSegmentsToNodes() {
        var snappedCount = 0;
        allSegments.forEach(function(polyline) {
            var latlngs = polyline.getLatLngs();
            var snappedLatlngs = [];
            var changed = false;

            latlngs.forEach(function(point) {
                var nearestInfo = findNearestNode(point.lat, point.lng, nodeCoordinates);
                if (nearestInfo.node && nearestInfo.distance < 50) {
                    snappedLatlngs.push(L.latLng(nearestInfo.node.lat, nearestInfo.node.lng));
                    changed = true;
                    snappedCount++;
                } else {
                    snappedLatlngs.push(point);
                }
            });

            if (changed) {
                polyline.setLatLngs(snappedLatlngs);
            }
        });

        console.log(`Snapped ${snappedCount} segment endpoints to nearest nodes within 50km`);
        showNotification(`Snapped segments to nearest nodes within 50km`, 'success');
    }

    function resetSegmentsToOriginal() {
        allSegments.forEach(function(polyline) {
            if (polyline.options.originalPoints) {
                var originalPoints = polyline.options.originalPoints.map(function(p) {
                    return L.latLng(p[0], p[1]);
                });
                polyline.setLatLngs(originalPoints);
            }
        });
        console.log('Reset segments to original positions');
        showNotification('Segments reset to original positions', 'info');
    }

    // Snap to nodes button handler
    document.getElementById('snapToNodesBtn').addEventListener('click', function() {
        snapEnabled = !snapEnabled;
        if (snapEnabled) {
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');
            this.innerHTML = '<i class="fas fa-magnet me-1"></i> Snap ON (50km)';
            snapSegmentsToNodes();
        } else {
            this.classList.remove('btn-primary');
            this.classList.add('btn-outline-primary');
            this.innerHTML = '<i class="fas fa-magnet me-1"></i> Snap to Nodes (50km)';
            resetSegmentsToOriginal();
        }
    });

    // ============ CONNECTION VISUALIZATION (50km) ============
    var connectionsVisible = false;
    var connectionLines = [];

    function drawConnections() {
        // Clear existing connections
        connectionLines.forEach(function(line) {
            map.removeLayer(line);
        });
        connectionLines = [];

        var connectedCount = 0;

        // Draw connections from stations to nearest nodes
        stationCoordinates.forEach(function(station) {
            var nearestInfo = findNearestNode(station.lat, station.lng, nodeCoordinates);

            if (nearestInfo.node && nearestInfo.distance < 50) {
                var distanceClass = nearestInfo.distance < 10 ? 'success' : (nearestInfo.distance < 30 ? 'warning' : 'info');
                var distanceText = nearestInfo.distance < 10 ? 'Direct' : (nearestInfo.distance < 30 ? 'Medium Range' : 'Long Range');

                var line = L.polyline([[station.lat, station.lng], [nearestInfo.node.lat, nearestInfo.node.lng]], {
                    color: '#6c757d',
                    weight: 2,
                    opacity: 0.7,
                    dashArray: '5, 5'
                }).bindPopup(`
                    <div style="min-width: 240px;">
                        <div style="background: linear-gradient(135deg, #0066B3, #009639); color: white; padding: 6px; border-radius: 6px 6px 0 0; margin: -6px -6px 0 -6px;">
                            <strong><i class="fas fa-link me-2"></i> Station Connection</strong>
                        </div>
                        <div style="padding: 8px 0;">
                            <table style="width: 100%; font-size: 12px;">
                                <tr><td style="padding: 2px;"><strong>🏢 Station:</strong></td><td style="padding: 2px;">${station.name || 'Unknown'}</td></tr>
                                <tr><td style="padding: 2px;"><strong>📍 Node:</strong></td><td style="padding: 2px;">${nearestInfo.node.name || 'Unknown'}</td></tr>
                                <tr><td style="padding: 2px;"><strong>📏 Distance:</strong></td><td style="padding: 2px;"><strong style="color: #0066B3;">${nearestInfo.distance.toFixed(2)} KM</strong></td></tr>
                                <tr><td style="padding: 2px;"><strong>⚡ Type:</strong></td><td style="padding: 2px;"><span class="badge bg-${distanceClass}">${distanceText}</span></td></tr>
                            </table>
                            <hr style="margin: 5px 0;">
                            <small><i class="fas fa-info-circle"></i> Connection within 50km radius</small>
                        </div>
                    </div>
                `);

                line.addTo(connectionsLayer);
                connectionLines.push(line);
                connectedCount++;
            }
        });

        console.log(`Drew ${connectedCount} station-node connections within 50km`);
        if (connectedCount > 0) {
            showNotification(`Found ${connectedCount} connections within 50km`, 'success');
        } else {
            showNotification(`No stations found within 50km of any node`, 'warning');
        }
    }

    function hideConnections() {
        connectionLines.forEach(function(line) {
            map.removeLayer(line);
        });
        connectionLines = [];
        console.log('Connections hidden');
    }

    function showNotification(message, type) {
        var notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
        notification.style.zIndex = '9999';
        notification.style.minWidth = '250px';
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : (type === 'warning' ? 'exclamation-triangle' : 'info-circle')} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(notification);
        setTimeout(function() { notification.remove(); }, 3000);
    }

    // Show connections button handler
    document.getElementById('showConnectionsBtn').addEventListener('click', function() {
        connectionsVisible = !connectionsVisible;
        if (connectionsVisible) {
            drawConnections();
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-info');
            this.innerHTML = '<i class="fas fa-link me-1"></i> Hide Connections (50km)';
        } else {
            hideConnections();
            this.classList.remove('btn-info');
            this.classList.add('btn-outline-secondary');
            this.innerHTML = '<i class="fas fa-link me-1"></i> Show Connections (50km)';
        }
    });

    // Layer control
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
        "🔥 Capacity Heatmap": heatLayer,
        "🔌 Station Connections": connectionsLayer
    };

    L.control.layers(baseMaps, overlayMaps, {
        collapsed: false,
        position: 'topright'
    }).addTo(map);

    // Add scale bar
    L.control.scale({ metric: true, imperial: false, position: 'bottomleft' }).addTo(map);

    // Add coordinate display on click
    map.on('click', function(e) {
        var lat = e.latlng.lat.toFixed(6);
        var lng = e.latlng.lng.toFixed(6);
        document.getElementById('coordinatesDisplay').innerHTML =
            `<i class="fas fa-map-marker-alt me-1"></i> Lat: ${lat}, Lng: ${lng}`;

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
    document.getElementById('zoomInBtn').addEventListener('click', function() { map.zoomIn(); });
    document.getElementById('zoomOutBtn').addEventListener('click', function() { map.zoomOut(); });

    document.getElementById('resetViewBtn').addEventListener('click', function() {
        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50] });
        } else {
            map.setView([-1.286389, 36.817223], 7);
        }
    });

    // ============ IMPROVED LOCATE ME BUTTON ============
    var userMarker = null;
    var locationCircle = null;

    function getUserLocation() {
        var btn = document.getElementById('locateMeBtn');
        var originalHtml = btn.innerHTML;

        // Show loading state
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Locating...';
        btn.disabled = true;

        // Remove existing markers
        if (userMarker) map.removeLayer(userMarker);
        if (locationCircle) map.removeLayer(locationCircle);

        // Check browser support
        if (!navigator.geolocation) {
            showNotification('Your browser does not support geolocation', 'warning');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            return;
        }

        // Request location with high accuracy
        navigator.geolocation.getCurrentPosition(
            // Success callback
            function(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                var accuracy = position.coords.accuracy;

                // Calculate zoom level based on accuracy
                var zoomLevel = 15;
                if (accuracy > 500) zoomLevel = 11;
                else if (accuracy > 200) zoomLevel = 13;
                else if (accuracy > 100) zoomLevel = 14;
                else zoomLevel = 15;

                // Center map on user location
                map.setView([lat, lng], zoomLevel);

                // Create custom marker with pulse animation
                var customIcon = L.divIcon({
                    className: 'user-location-pulse',
                    html: `<div style="
                        background-color: #0066B3;
                        width: 16px;
                        height: 16px;
                        border-radius: 50%;
                        border: 3px solid white;
                        box-shadow: 0 0 0 2px rgba(0,102,179,0.3);
                    "></div>`,
                    iconSize: [22, 22],
                    popupAnchor: [0, -11]
                });

                // Add user marker
                userMarker = L.marker([lat, lng], { icon: customIcon }).bindPopup(`
                    <div style="text-align: center; min-width: 180px;">
                        <strong><i class="fas fa-location-dot text-kp-blue"></i> Your Location</strong><br>
                        <hr class="my-1">
                        <small>
                            Lat: ${lat.toFixed(6)}<br>
                            Lng: ${lng.toFixed(6)}<br>
                            <strong>Accuracy:</strong> ±${Math.round(accuracy)} meters
                        </small>
                        <hr class="my-1">
                        <small class="text-muted">📍 Found your current position</small>
                    </div>
                `).openPopup();

                userMarker.addTo(map);

                // Add accuracy circle
                locationCircle = L.circle([lat, lng], {
                    radius: accuracy,
                    color: '#0066B3',
                    fillColor: '#0066B3',
                    fillOpacity: 0.1,
                    weight: 1.5,
                    opacity: 0.6
                }).addTo(map);

                // Success notification
                showNotification(`📍 Location found! Accuracy: ±${Math.round(accuracy)}m`, 'success');

                // Reset button
                btn.innerHTML = '<i class="fas fa-location-dot me-1"></i> Locate Me';
                btn.disabled = false;
            },
            // Error callback
            function(error) {
                var errorMsg = '';
                var showInstructions = false;

                switch(error.code) {
                    case 1: // PERMISSION_DENIED
                        errorMsg = 'Location permission denied. Please allow access in browser settings.';
                        showInstructions = true;
                        break;
                    case 2: // POSITION_UNAVAILABLE
                        errorMsg = 'GPS signal unavailable. Please ensure GPS is enabled.';
                        break;
                    case 3: // TIMEOUT
                        errorMsg = 'Location request timed out. Please try again.';
                        break;
                    default:
                        errorMsg = 'Unable to get your location. Please check GPS settings.';
                }

                showNotification(errorMsg, 'danger');
                console.error('Geolocation error:', error);

                if (showInstructions) {
                    showLocationInstructions();
                }

                // Reset button
                btn.innerHTML = '<i class="fas fa-location-dot me-1"></i> Locate Me';
                btn.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }
        );
    }

    function showLocationInstructions() {
        var instructionDiv = document.createElement('div');
        instructionDiv.className = 'alert alert-info alert-dismissible fade show position-fixed';
        instructionDiv.style.top = '50%';
        instructionDiv.style.left = '50%';
        instructionDiv.style.transform = 'translate(-50%, -50%)';
        instructionDiv.style.zIndex = '10001';
        instructionDiv.style.minWidth = '320px';
        instructionDiv.style.maxWidth = '400px';
        instructionDiv.style.boxShadow = '0 4px 20px rgba(0,0,0,0.2)';
        instructionDiv.innerHTML = `
            <strong><i class="fas fa-info-circle me-2"></i> How to Enable Location</strong>
            <hr class="my-2">
            <small>
                <strong>Chrome/Edge:</strong> Click the lock icon in address bar → Allow location<br>
                <strong>Firefox:</strong> Click the shield icon → Temporarily disable protection<br>
                <strong>Safari:</strong> Settings → Privacy → Location Services → Allow<br>
                <strong>Mobile:</strong> Settings → Apps → Browser → Permissions → Location
            </small>
            <hr class="my-2">
            <button class="btn btn-sm btn-primary w-100" onclick="this.parentElement.remove()">Got it, refresh page</button>
        `;
        document.body.appendChild(instructionDiv);

        setTimeout(function() {
            if (instructionDiv && instructionDiv.parentElement) instructionDiv.remove();
        }, 10000);
    }

    // Attach locate me event listener
    document.getElementById('locateMeBtn').addEventListener('click', getUserLocation);

    // Segment filter buttons
    document.getElementById('showAllSegmentsBtn').addEventListener('click', function() {
        allSegments.forEach(function(seg) { seg.setStyle({ opacity: 0.9 }); });
        showNotification('All segments visible', 'info');
    });

    document.getElementById('showActiveOnlyBtn').addEventListener('click', function() {
        allSegments.forEach(function(seg) {
            var opacity = seg.options.color === '#28a745' ? 0.9 : 0.15;
            seg.setStyle({ opacity: opacity });
        });
        showNotification('Showing only active segments', 'info');
    });

    document.getElementById('showDamagedOnlyBtn').addEventListener('click', function() {
        allSegments.forEach(function(seg) {
            var opacity = seg.options.color === '#dc3545' ? 0.9 : 0.15;
            seg.setStyle({ opacity: opacity });
        });
        showNotification('Showing only damaged segments', 'warning');
    });

    // Toggle stats panel
    document.getElementById('showStatsBtn').addEventListener('click', function() {
        var panel = document.getElementById('statsPanel');
        if (panel.style.display === 'none') {
            panel.style.display = '';
            this.innerHTML = '<i class="fas fa-chart-bar me-2"></i>Hide Stats';
        } else {
            panel.style.display = 'none';
            this.innerHTML = '<i class="fas fa-chart-bar me-2"></i>Show Stats';
        }
    });

    // Layer switcher
    function updateActiveButton(activeId) {
        ['satelliteView', 'roadmapView', 'terrainView', 'hybridView'].forEach(function(id) {
            var btn = document.getElementById(id);
            if (btn) {
                if (id === activeId) {
                    btn.classList.remove('btn-outline-secondary');
                    btn.classList.add('btn-primary');
                } else {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-secondary');
                }
            }
        });
    }

    document.getElementById('satelliteView').addEventListener('click', function() {
        currentBaseLayer.removeFrom(map);
        currentBaseLayer = tileLayers.Satellite;
        currentBaseLayer.addTo(map);
        updateActiveButton('satelliteView');
    });

    document.getElementById('roadmapView').addEventListener('click', function() {
        currentBaseLayer.removeFrom(map);
        currentBaseLayer = tileLayers.Roadmap;
        currentBaseLayer.addTo(map);
        updateActiveButton('roadmapView');
    });

    document.getElementById('terrainView').addEventListener('click', function() {
        currentBaseLayer.removeFrom(map);
        currentBaseLayer = tileLayers.Terrain;
        currentBaseLayer.addTo(map);
        updateActiveButton('terrainView');
    });

    document.getElementById('hybridView').addEventListener('click', function() {
        currentBaseLayer.removeFrom(map);
        currentBaseLayer = tileLayers.Hybrid;
        currentBaseLayer.addTo(map);
        updateActiveButton('hybridView');
    });

    // Fit bounds to show all data
    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [50, 50] });
    } else {
        map.setView([-1.286389, 36.817223], 7);
    }

    // ============ LEGEND - MOVED TO BOTTOM-LEFT ============
    var legend = L.control({ position: 'bottomleft' });
    legend.onAdd = function() {
        var div = L.DomUtil.create('div', 'info legend');
        div.innerHTML = `
            <div style="background: white; padding: 10px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); min-width: 170px; max-width: 200px; font-size: 11px;">
                <strong style="color: #0066B3;"><i class="fas fa-chart-pie me-1"></i> Legend</strong>
                <hr class="my-1">
                <div><i class="fas fa-circle" style="color: #28a745; font-size: 10px;"></i> Low (0-50%)</div>
                <div><i class="fas fa-circle" style="color: #ffc107; font-size: 10px;"></i> Medium (50-75%)</div>
                <div><i class="fas fa-circle" style="color: #fd7e14; font-size: 10px;"></i> High (75-90%)</div>
                <div><i class="fas fa-circle" style="color: #dc3545; font-size: 10px;"></i> Critical (90%+)</div>
                <hr class="my-1">
                <div><i class="fas fa-circle" style="color: #28a745; font-size: 10px;"></i> Active Segment</div>
                <div><i class="fas fa-circle" style="color: #dc3545; font-size: 10px;"></i> Damaged Segment</div>
                <div><i class="fas fa-circle" style="color: #fd7e14; font-size: 10px;"></i> Planned Segment</div>
                <div><i class="fas fa-circle" style="color: #6c757d; font-size: 10px;"></i> Decommissioned</div>
                <div><i class="fas fa-circle" style="color: #0066B3; font-size: 10px;"></i> Network Node</div>
                <div><i class="fas fa-circle" style="color: #6c757d; font-size: 10px;"></i> Station Connection</div>
                <hr class="my-1">
                <div><strong>🔗 Range (50km):</strong></div>
                <div><small>< 10km → Direct</small></div>
                <div><small>10-30km → Medium</small></div>
                <div><small>30-50km → Long</small></div>
                <hr class="my-1">
                <small><i class="fas fa-info-circle"></i> Click for details</small>
            </div>
        `;
        return div;
    };
    legend.addTo(map);

    console.log('GIS Map initialized successfully with 50km thresholds');
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
    min-width: 260px;
}

.leaflet-popup-content-wrapper {
    border-radius: 12px;
    padding: 12px;
}

.custom-node-icon {
    background: transparent;
}

#networkMap {
    width: 100%;
    height: 65vh;
    min-height: 500px;
    border-radius: 0 0 1rem 1rem;
}

/* User location pulse animation */
.user-location-pulse {
    background: transparent;
}

.user-location-pulse div {
    animation: locationPulse 1.5s ease-in-out infinite;
}

@keyframes locationPulse {
    0% {
        transform: scale(0.8);
        box-shadow: 0 0 0 0 rgba(0, 102, 179, 0.7);
    }
    70% {
        transform: scale(1.2);
        box-shadow: 0 0 0 10px rgba(0, 102, 179, 0);
    }
    100% {
        transform: scale(0.8);
        box-shadow: 0 0 0 0 rgba(0, 102, 179, 0);
    }
}

/* Legend moved to bottom-left - no overlap */
.info.legend {
    background: white;
    padding: 6px 10px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    max-width: 200px;
    font-size: 11px;
}

.btn-group .btn {
    transition: all 0.2s ease;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
}

#statsPanel {
    transition: all 0.3s ease;
}

/* Notification styling */
.alert {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    min-width: 280px;
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    #networkMap {
        height: 50vh;
        min-height: 400px;
    }
    .btn-group {
        flex-wrap: wrap;
    }
    .alert {
        top: 10px;
        right: 10px;
        left: 10px;
        min-width: auto;
    }
    .info.legend {
        max-width: 160px;
        font-size: 9px;
        padding: 4px 8px;
    }
}
</style>

@endsection
