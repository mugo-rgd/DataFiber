<?php $__env->startSection('title', 'Kenya Fibre Dashboard - Dark Fibre CRM'); ?>
<?php $__env->startSection('page-title', 'Kenya Fibre Network Dashboard'); ?>

<?php $__env->startSection('styles'); ?>
<style>
    #fibre-map {
        height: 600px;
        width: 100%;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 1;
    }

    .stat-card {
        transition: transform 0.2s;
        cursor: pointer;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .stat-icon {
        font-size: 2rem;
        opacity: 0.6;
    }

    .fiber-status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .status-active {
        background-color: #28a745;
        color: white;
    }

    .status-damaged {
        background-color: #dc3545;
        color: white;
    }

    .status-planned {
        background-color: #ffc107;
        color: black;
    }

    .status-decommissioned {
        background-color: #6c757d;
        color: white;
    }

    .link-type-metro {
        background-color: #17a2b8;
        color: white;
    }

    .link-type-premium {
        background-color: #6f42c1;
        color: white;
    }

    .link-type-opgw {
        background-color: #fd7e14;
        color: white;
    }

    .link-type-adss {
        background-color: #20c997;
        color: white;
    }

    .link-type-opgw-adss {
        background-color: #6610f2;
        color: white;
    }

    .link-type-non-premium {
        background-color: #6c757d;
        color: white;
    }

    .filter-panel {
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .legend-color {
        width: 20px;
        height: 4px;
        margin-right: 8px;
        border-radius: 2px;
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .connection-line {
        stroke-dasharray: 5, 5;
        opacity: 0.6;
    }

    .green-dashed {
        stroke-dasharray: 10, 8;
        opacity: 0.9;
    }

    .node-marker {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .node-popup {
        min-width: 250px;
        padding: 10px;
    }

    .node-popup h6 {
        margin: 0 0 8px 0;
        color: #333;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
    }

    .node-popup table {
        width: 100%;
        font-size: 0.9rem;
    }

    .node-popup td {
        padding: 3px 0;
    }

    .node-popup td:first-child {
        font-weight: 600;
        width: 40%;
    }

    .network-popup {
        min-width: 250px;
        padding: 10px;
    }

    .network-popup h6 {
        margin: 0 0 8px 0;
        color: #333;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
    }

    .network-popup table {
        width: 100%;
        font-size: 0.9rem;
    }

    .network-popup td {
        padding: 3px 0;
    }

    .network-popup td:first-child {
        font-weight: 600;
        width: 40%;
    }

    .map-container {
        position: relative;
        margin-bottom: 20px;
    }

    .map-controls {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
        background: white;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        padding: 5px;
    }

    .map-type-selector {
        display: flex;
        gap: 5px;
    }

    .map-type-btn {
        padding: 8px 12px;
        border: none;
        background: #f8f9fa;
        cursor: pointer;
        border-radius: 4px;
        font-size: 14px;
        transition: all 0.2s;
    }

    .map-type-btn:hover {
        background: #e9ecef;
    }

    .map-type-btn.active {
        background: #007bff;
        color: white;
    }

    .btn-map-action {
        position: absolute;
        bottom: 20px;
        right: 20px;
        z-index: 10;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 8px 12px;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .btn-map-action:hover {
        background: #f8f9fa;
    }

    .region-stats {
        font-size: 0.85rem;
        margin-top: 10px;
    }

    .region-stats-item {
        display: flex;
        justify-content: space-between;
        padding: 2px 0;
    }

    .networks-container {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    .network-row {
        cursor: pointer;
        transition: background-color 0.2s;
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    .network-row:hover {
        background-color: #f8f9fa;
    }

    .network-row.selected {
        background-color: #e7f1ff;
        border-left: 3px solid #007bff;
    }

    .status-badge {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 5px;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Header Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Kenya Fibre Network Overview</h5>
                <div>
                    <button class="btn btn-sm btn-primary" onclick="refreshMap()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-sm btn-success" onclick="exportData()">
                        <i class="fas fa-download"></i> Export Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-left-primary h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Networks</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['total_networks'] ?? 0); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-network-wired fa-2x text-gray-300 stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-left-success h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Distance</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(number_format($stats['total_distance'] ?? 0, 2)); ?> km</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-road fa-2x text-gray-300 stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-left-info h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Network Nodes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['total_nodes'] ?? 0); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-map-marker-alt fa-2x text-gray-300 stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-left-warning h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Monthly Revenue</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo e(number_format($stats['total_monthly_revenue'] ?? 0, 2)); ?></div>
                        <small class="text-muted">≈ KES <?php echo e(number_format(($stats['total_monthly_revenue'] ?? 0) * 150, 2)); ?></small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300 stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Panel -->
<div class="row mb-4">
    <div class="col-12">
        <div class="filter-panel">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Filter by Region</label>
                    <select class="form-select" id="regionFilter" onchange="filterNetworks()">
                        <option value="">All Regions</option>
                        <?php $__currentLoopData = $regions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($region->region); ?>"><?php echo e($region->region); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filter by Link Type</label>
                    <select class="form-select" id="linkTypeFilter" onchange="filterNetworks()">
                        <option value="">All Types</option>
                        <option value="Metro">Metro</option>
                        <option value="Premium">Premium</option>
                        <option value="OPGW">OPGW</option>
                        <option value="ADSS">ADSS</option>
                        <option value="OPGW/ADSS">OPGW/ADSS</option>
                        <option value="Non Premium">Non Premium</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filter by Status</label>
                    <select class="form-select" id="statusFilter" onchange="filterNetworks()">
                        <option value="">All Status</option>
                        <option value="Active">Active</option>
                        <option value="Damaged">Damaged</option>
                        <option value="Planned">Planned</option>
                        <option value="Decommissioned">Decommissioned</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100" onclick="resetFilters()">
                        <i class="fas fa-redo-alt"></i> Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map and Networks List -->
<div class="row">
    <div class="col-md-8">
        <div class="map-container">
            <div id="fibre-map" style="height: 600px; width: 100%;"></div>
            <div class="map-controls">
                <div class="map-type-selector">
                    <button class="map-type-btn active" onclick="switchMapType('osm')">OpenStreetMap</button>
                    <button class="map-type-btn" onclick="switchMapType('google')">Google Maps</button>
                    <button class="map-type-btn" onclick="switchMapType('satellite')">Satellite</button>
                </div>
            </div>
            <div class="btn-map-action" onclick="locateMe()">
                <i class="fas fa-crosshairs"></i> Show My Location
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Network List</h5>
                <div>
                    <span class="badge bg-primary" id="networkCount"><?php echo e(count($networkPaths ?? [])); ?></span>
                    <button class="btn btn-sm btn-link" onclick="refreshList()" title="Refresh list">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="networks-container" id="networkList" style="max-height: 600px; overflow-y: auto;">
                    <?php $__empty_1 = true; $__currentLoopData = $networkPaths ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $network): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="network-row p-3 border-bottom"
                         onclick="focusNetwork('<?php echo e($network['network_id']); ?>')"
                         data-network-id="<?php echo e($network['network_id']); ?>"
                         style="cursor: pointer; transition: background-color 0.2s;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div style="flex: 1;">
                                <strong><?php echo e(Str::limit($network['name'] ?? 'Unnamed Network', 30)); ?></strong>
                                <div class="mt-1">
                                    <span class="badge bg-secondary"><?php echo e($network['region'] ?? 'Unknown'); ?></span>
                                    <span class="badge bg-info"><?php echo e(number_format($network['distance'] ?? 0, 1)); ?> km</span>
                                    <span class="badge bg-dark"><?php echo e($network['point_count'] ?? 0); ?> pts</span>
                                </div>
                                <div class="mt-2 small text-muted">
                                    <i class="fas fa-microchip"></i> <?php echo e($network['fiber_cores'] ?? 0); ?> cores
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="fiber-status-badge status-<?php echo e(strtolower($network['status'] ?? 'active')); ?> mb-2">
                                    <?php echo e($network['status'] ?? 'Active'); ?>

                                </span>
                                <div class="mt-2">
                                    <?php
                                        $linkType = $network['link_type'] ?? 'Non Premium';
                                        $linkTypeClass = strtolower(str_replace([' ', '/'], '-', $linkType));
                                    ?>
                                    <span class="badge link-type-<?php echo e($linkTypeClass); ?>">
                                        <?php echo e($linkType); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 small">
                            <span class="text-primary">
                                <i class="fas fa-dollar-sign"></i> <?php echo e($network['currency'] ?? 'USD'); ?> <?php echo e(number_format($network['cost'] ?? 0, 2)); ?>/mo
                            </span>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                        <p>No networks found</p>
                        <small>Check your database connection</small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-footer bg-white text-center p-2">
                <small class="text-muted">
                    Showing <?php echo e(count($networkPaths ?? [])); ?> networks • Click any row to focus on map
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Legend Panel -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>By Status:</strong>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #28a745;"></div>
                            <span>Active Network</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #dc3545;"></div>
                            <span>Damaged Network</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #ffc107;"></div>
                            <span>Planned Network</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #6c757d;"></div>
                            <span>Decommissioned</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <strong>By Link Type:</strong>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #17a2b8;"></div>
                            <span>Metro Link</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #6f42c1;"></div>
                            <span>Premium Link</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #fd7e14;"></div>
                            <span>OPGW</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #20c997;"></div>
                            <span>ADSS</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #6610f2;"></div>
                            <span>OPGW/ADSS</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #6c757d;"></div>
                            <span>Non Premium</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #28a745; background: linear-gradient(90deg, #28a745, #28a745);"></div>
                            <span>Inferred Connection</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <strong>By Node Type:</strong>
                        <div class="legend-item">
                            <div class="legend-dot" style="background: #007bff;"></div>
                            <span>Substation (SS)</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background: #28a745;"></div>
                            <span>Office</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background: #ffc107;"></div>
                            <span>Data Center</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background: #fd7e14;"></div>
                            <span>Junction</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background: #800080;"></div>
                            <span>Major Hub</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <?php if(isset($regionStats) && count($regionStats) > 0): ?>
                        <strong>Region Stats:</strong>
                        <?php $__currentLoopData = $regionStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="region-stats-item">
                            <span><?php echo e($stat->region); ?>:</span>
                            <span><?php echo e($stat->count); ?> nets</span>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB77eGv2kN5Lo-ZpD01-a277yCr2u-9Fto"></script>

<!-- Leaflet Google Mutant plugin for Google Maps -->
<script src="https://unpkg.com/leaflet.gridlayer.googlemutant@0.13.0/dist/Leaflet.GoogleMutant.js"></script>

<script>
// Network data from server
const networkPaths = <?php echo json_encode($networkPaths ?? [], 15, 512) ?>;
console.log('📊 Networks loaded:', networkPaths.length);

// Map variables
let map;
let currentMapType = 'osm';
let networkLayers = [];
let nodeMarkers = [];
let connectionLines = [];

// Track unique nodes to avoid duplicates
const nodeMap = new Map();

// Major Hubs - These are the core connection points that everything should connect to
const MAJOR_HUBS = [
    'NAIROBI WEST',
    'CITY CENTER SS',
    'NAIROBI NORTH',
    'RIRONI SS',
    'KIKUYU SS',
    'NAIVASHA 132',
    'ATHI RIVER',
    'VOI',
    'KIBOKO',
    'MOMBASA',
    'KISUMU',
    'ELDORET DEPOT',
    'MERU 132',
    'KAMBURU 132',
    'KIPEVU HT',
    'RABAI',
    'LESSOS',
    'MUSAGA'
];

// Map tile configurations
const mapTiles = {
    osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }),
    google: L.gridLayer.googleMutant({
        type: 'roadmap',
        attribution: '© Google Maps'
    }),
    satellite: L.gridLayer.googleMutant({
        type: 'satellite',
        attribution: '© Google Maps'
    })
};

// Calculate distance between two points in kilometers (Haversine formula)
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Earth's radius in kilometers
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a =
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

// Find node by name in network paths
function findNodeByName(nodeName) {
    for (const network of networkPaths) {
        if (network.path) {
            for (const point of network.path) {
                if (point.name && point.name.toLowerCase().includes(nodeName.toLowerCase())) {
                    return {
                        lat: parseFloat(point.lat),
                        lng: parseFloat(point.lng),
                        networkId: network.network_id,
                        name: point.name
                    };
                }
            }
        }
    }
    return null;
}

// Check if a node is a major hub
function isMajorHub(nodeName) {
    return MAJOR_HUBS.some(hub => nodeName.toLowerCase().includes(hub.toLowerCase()));
}

// Initialize map when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('🗺️ Creating map...');

    const mapElement = document.getElementById('fibre-map');
    if (!mapElement) {
        console.error('❌ Map element not found!');
        return;
    }

    try {
        // Create map centered on Kenya
        map = L.map('fibre-map').setView([-1.0, 37.0], 6);

        // Add default OSM tiles
        mapTiles.osm.addTo(map);

        console.log('✅ Map created');

        // Draw networks and nodes
        drawNetworks();
        drawNodes();

        // Connect all hanging endpoints to nearest major hub with green dashed lines
        connectHangingEndpoints();

        // Store map globally
        window.fibreMap = map;

    } catch (error) {
        console.error('❌ Error:', error);
    }
});

// Function to switch map type
window.switchMapType = function(type) {
    if (!map) return;

    // Remove all current tile layers
    map.eachLayer(layer => {
        if (layer instanceof L.TileLayer || layer instanceof L.GridLayer) {
            map.removeLayer(layer);
        }
    });

    // Add selected tile layer
    if (type === 'osm') {
        mapTiles.osm.addTo(map);
    } else if (type === 'google') {
        mapTiles.google.addTo(map);
    } else if (type === 'satellite') {
        mapTiles.satellite.addTo(map);
    }

    // Redraw networks, nodes, and connections
    drawNetworks();
    drawNodes();
    connectHangingEndpoints();

    // Update active button
    document.querySelectorAll('.map-type-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');

    currentMapType = type;
};

// Function to connect hanging endpoints to nearest major hub
function connectHangingEndpoints() {
    if (!map) return;

    // Clear existing connection lines
    connectionLines.forEach(line => map.removeLayer(line));
    connectionLines = [];

    // Collect all endpoints (first and last point of each network)
    const endpoints = [];

    networkPaths.forEach(network => {
        if (network.path && network.path.length >= 2) {
            // Add start point
            endpoints.push({
                lat: parseFloat(network.path[0].lat),
                lng: parseFloat(network.path[0].lng),
                networkId: network.network_id,
                networkName: network.name,
                name: network.path[0].name || network.name
            });

            // Add end point
            endpoints.push({
                lat: parseFloat(network.path[network.path.length - 1].lat),
                lng: parseFloat(network.path[network.path.length - 1].lng),
                networkId: network.network_id,
                networkName: network.name,
                name: network.path[network.path.length - 1].name || network.name
            });
        }
    });

    // Track connected endpoints to avoid duplicates
    const connectedEndpoints = new Set();
    let connectionCount = 0;

    // For each endpoint, find the nearest major hub and connect
    endpoints.forEach(endpoint => {
        const endpointKey = `${endpoint.lat},${endpoint.lng}`;

        // Skip if already connected
        if (connectedEndpoints.has(endpointKey)) return;

        // Skip if this endpoint is already a major hub
        if (isMajorHub(endpoint.name)) return;

        let nearestHub = null;
        let minDistance = Infinity;

        // Find nearest major hub
        MAJOR_HUBS.forEach(hubName => {
            const hubNode = findNodeByName(hubName);
            if (hubNode) {
                const dist = calculateDistance(
                    endpoint.lat, endpoint.lng,
                    hubNode.lat, hubNode.lng
                );

                // Don't connect to itself
                if (Math.abs(endpoint.lat - hubNode.lat) < 0.001 &&
                    Math.abs(endpoint.lng - hubNode.lng) < 0.001) return;

                if (dist < minDistance) {
                    minDistance = dist;
                    nearestHub = hubNode;
                }
            }
        });

        // If we found a nearby hub, connect to it with green dashed line
        if (nearestHub && minDistance < 150) { // Connect if within 150km
            const line = L.polyline([
                [endpoint.lat, endpoint.lng],
                [nearestHub.lat, nearestHub.lng]
            ], {
                color: '#28a745',  // Green
                weight: 3,
                opacity: 0.9,
                dashArray: '10, 8'  // Dashed line
            }).bindPopup(`
                <div class="network-popup">
                    <h6>🟢 Inferred Network Connection</h6>
                    <table>
                        <tr><td>From:</td><td>${endpoint.name}</td></tr>
                        <tr><td>To Hub:</td><td>${nearestHub.name}</td></tr>
                        <tr><td>Distance:</td><td>${minDistance.toFixed(2)} km</td></tr>
                        <tr><td>Network:</td><td>${endpoint.networkName}</td></tr>
                    </table>
                </div>
            `);

            line.addTo(map);
            connectionLines.push(line);
            connectedEndpoints.add(endpointKey);
            connectionCount++;

            console.log(`✅ Connected ${endpoint.name} to hub ${nearestHub.name} (${minDistance.toFixed(2)} km)`);
        }
    });

    console.log(`🔗 Added ${connectionCount} green dashed connections to major hubs`);
}

// Function to extract node name from path point
function getNodeName(point, networkName) {
    if (point.name) return point.name;

    // Try to extract from network name
    const parts = networkName.split(' to ');
    if (parts.length > 1) {
        if (point === networkName[0]) return parts[0];
        if (point === networkName[networkName.length - 1]) return parts[parts.length - 1];
        return 'Junction';
    }
    return 'Network Node';
}

// Function to determine node type based on name
function getNodeType(nodeName) {
    const name = nodeName.toLowerCase();

    // Check if it's a major hub first
    if (isMajorHub(nodeName)) {
        return 'Major Hub';
    }

    if (name.includes('ss') || name.includes('substation')) {
        return 'Substation';
    } else if (name.includes('office') || name.includes('kplc')) {
        return 'Office';
    } else if (name.includes('data') || name.includes('center')) {
        return 'Data Center';
    } else {
        return 'Junction';
    }
}

// Function to get node color based on type
function getNodeColor(nodeType) {
    switch(nodeType) {
        case 'Major Hub':
            return '#800080'; // Purple for major hubs
        case 'Substation':
            return '#007bff'; // Blue
        case 'Office':
            return '#28a745'; // Green
        case 'Data Center':
            return '#ffc107'; // Yellow
        default:
            return '#fd7e14'; // Orange for junctions
    }
}

// Function to draw nodes at each path point
function drawNodes() {
    if (!map) return;

    // Clear existing node markers
    nodeMarkers.forEach(marker => map.removeLayer(marker));
    nodeMarkers = [];
    nodeMap.clear();

    networkPaths.forEach(network => {
        if (network.path && network.path.length >= 2) {
            network.path.forEach((point, index) => {
                // Create a unique key for this node
                const nodeKey = `${point.lat},${point.lng}`;

                // Check if we've already added this node
                if (!nodeMap.has(nodeKey)) {
                    const lat = parseFloat(point.lat);
                    const lng = parseFloat(point.lng);

                    // Get node name
                    const nodeName = point.name || getNodeName(point, network.name);

                    // Determine node type
                    const nodeType = getNodeType(nodeName);

                    // Get color based on type
                    const color = getNodeColor(nodeType);

                    // Adjust radius for major hubs
                    const radius = nodeType === 'Major Hub' ? 8 : 6;

                    // Create marker with custom icon
                    const marker = L.circleMarker([lat, lng], {
                        radius: radius,
                        fillColor: color,
                        color: '#fff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).bindPopup(`
                        <div class="node-popup">
                            <h6>${nodeName}</h6>
                            <table>
                                <tr><td>Type:</td><td>${nodeType}</td></tr>
                                <tr><td>Coordinates:</td><td>${lat.toFixed(6)}, ${lng.toFixed(6)}</td></tr>
                                <tr><td>Connected Networks:</td><td id="connected-${nodeKey.replace(/[.,]/g, '_')}">Loading...</td></tr>
                            </table>
                        </div>
                    `);

                    marker.addTo(map);
                    nodeMarkers.push(marker);
                    nodeMap.set(nodeKey, {
                        marker: marker,
                        name: nodeName,
                        type: nodeType,
                        networks: []
                    });
                }

                // Add this network to the node's connected networks
                const nodeData = nodeMap.get(nodeKey);
                if (!nodeData.networks.includes(network.name)) {
                    nodeData.networks.push(network.name);
                }
            });
        }
    });

    // Update popup content with connected networks
    nodeMap.forEach((data, key) => {
        const connectedNetworks = data.networks.join('<br>');

        // Update popup content
        data.marker.getPopup().setContent(`
            <div class="node-popup">
                <h6>${data.name}</h6>
                <table>
                    <tr><td>Type:</td><td>${data.type}</td></tr>
                    <tr><td>Coordinates:</td><td>${data.marker.getLatLng().lat.toFixed(6)}, ${data.marker.getLatLng().lng.toFixed(6)}</td></tr>
                    <tr><td>Connected Networks:</td><td>${connectedNetworks}</td></tr>
                </table>
            </div>
        `);
    });

    console.log(`📍 Added ${nodeMarkers.length} unique node markers`);
}

// Function to draw networks
function drawNetworks() {
    if (!map) return;

    // Clear existing network layers
    networkLayers.forEach(layer => map.removeLayer(layer));
    networkLayers = [];

    let drawnCount = 0;
    const bounds = L.latLngBounds();

    networkPaths.forEach(network => {
        if (network.path && network.path.length >= 2) {
            // Convert to LatLng
            const points = network.path.map(p => [parseFloat(p.lat), parseFloat(p.lng)]);

            // Add points to bounds
            points.forEach(p => bounds.extend(p));

            // Color based on status
            const color = network.status === 'Damaged' ? '#dc3545' :
                        network.status === 'Planned' ? '#ffc107' :
                        network.status === 'Decommissioned' ? '#6c757d' : '#28a745';

            // Get node names for start and end points
            const startNode = network.path[0].name || 'Start';
            const endNode = network.path[network.path.length - 1].name || 'End';

            // Draw line
            const polyline = L.polyline(points, {
                color: color,
                weight: 3,
                opacity: 0.8
            }).bindPopup(`
                <div class="network-popup">
                    <h6>${network.name || 'Unnamed Network'}</h6>
                    <table>
                        <tr><td>Network ID:</td><td>${network.network_id || 'N/A'}</td></tr>
                        <tr><td>Region:</td><td>${network.region || 'Unknown'}</td></tr>
                        <tr><td>Distance:</td><td>${(network.distance || 0).toFixed(2)} km</td></tr>
                        <tr><td>Fiber Cores:</td><td>${network.fiber_cores || 0}</td></tr>
                        <tr><td>Type:</td><td>${network.link_type || 'Non Premium'}</td></tr>
                        <tr><td>Status:</td><td><span class="status-badge" style="background:${color};"></span>${network.status || 'Active'}</td></tr>
                        <tr><td>Start Node:</td><td>${startNode}</td></tr>
                        <tr><td>End Node:</td><td>${endNode}</td></tr>
                        <tr><td>Cost:</td><td>${network.currency || 'USD'} ${(network.cost || 0).toFixed(2)}/mo</td></tr>
                    </table>
                </div>
            `);

            polyline.addTo(map);
            networkLayers.push(polyline);
            drawnCount++;
        }
    });

    console.log(`✅ Drew ${drawnCount} of ${networkPaths.length} networks`);

    // Fit map to show all networks
    if (drawnCount > 0 && bounds.isValid()) {
        map.fitBounds(bounds, { padding: [50, 50] });
    }
}

// Global functions
window.locateMe = function() {
    if (!map) {
        alert('Map not initialized yet');
        return;
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                map.setView([lat, lng], 12);

                const marker = L.marker([lat, lng])
                    .bindPopup('You are here')
                    .addTo(map);
                networkLayers.push(marker);
            },
            function(error) {
                alert('Error getting location: ' + error.message);
            }
        );
    } else {
        alert('Geolocation is not supported by your browser');
    }
};

window.focusNetwork = function(networkId) {
    console.log('Focus network:', networkId);
    const network = networkPaths.find(n => n.network_id == networkId);

    if (network && network.path && network.path.length > 0 && map) {
        const points = network.path.map(p => [parseFloat(p.lat), parseFloat(p.lng)]);
        const bounds = L.latLngBounds(points);

        map.fitBounds(bounds, { padding: [50, 50] });

        // Highlight the row
        document.querySelectorAll('.network-row').forEach(row => {
            row.classList.remove('selected');
            if (row.dataset.networkId == networkId) {
                row.classList.add('selected');
                row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    }
};

window.filterNetworks = function() {
    const region = document.getElementById('regionFilter').value;
    const linkType = document.getElementById('linkTypeFilter').value;
    const status = document.getElementById('statusFilter').value;

    console.log('Filtering by:', { region, linkType, status });

    // Filter networkPaths
    const filtered = networkPaths.filter(network => {
        return (!region || network.region === region) &&
               (!linkType || network.link_type === linkType) &&
               (!status || network.status === status);
    });

    // Update network list
    const container = document.getElementById('networkList');
    if (container) {
        let html = '';
        filtered.forEach(network => {
            html += `
                <div class="network-row p-3 border-bottom"
                     onclick="focusNetwork('${network.network_id}')"
                     data-network-id="${network.network_id}"
                     style="cursor: pointer; transition: background-color 0.2s;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div style="flex: 1;">
                            <strong>${network.name || 'Unnamed Network'}</strong>
                            <div class="mt-1">
                                <span class="badge bg-secondary">${network.region || 'Unknown'}</span>
                                <span class="badge bg-info">${(network.distance || 0).toFixed(1)} km</span>
                                <span class="badge bg-dark">${network.point_count || 0} pts</span>
                            </div>
                            <div class="mt-2 small text-muted">
                                <i class="fas fa-microchip"></i> ${network.fiber_cores || 0} cores
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="fiber-status-badge status-${(network.status || 'active').toLowerCase()} mb-2">
                                ${network.status || 'Active'}
                            </span>
                            <div class="mt-2">
                                <span class="badge link-type-${(network.link_type || 'non-premium').toLowerCase().replace(/[\s/]/g, '-')}">
                                    ${network.link_type || 'Non Premium'}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 small">
                        <span class="text-primary">
                            <i class="fas fa-dollar-sign"></i> ${network.currency || 'USD'} ${(network.cost || 0).toFixed(2)}/mo
                        </span>
                    </div>
                </div>
            `;
        });

        if (filtered.length === 0) {
            html = '<div class="p-4 text-center text-muted">No networks match the selected filters</div>';
        }

        container.innerHTML = html;
        document.getElementById('networkCount').textContent = filtered.length;
    }

    // Update map
    if (map) {
        // Clear network layers, node markers, and connection lines
        networkLayers.forEach(layer => map.removeLayer(layer));
        nodeMarkers.forEach(marker => map.removeLayer(marker));
        connectionLines.forEach(line => map.removeLayer(line));

        networkLayers = [];
        nodeMarkers = [];
        connectionLines = [];
        nodeMap.clear();

        // Draw filtered networks
        filtered.forEach(network => {
            if (network.path && network.path.length >= 2) {
                const points = network.path.map(p => [parseFloat(p.lat), parseFloat(p.lng)]);
                const color = network.status === 'Damaged' ? '#dc3545' :
                            network.status === 'Planned' ? '#ffc107' :
                            network.status === 'Decommissioned' ? '#6c757d' : '#28a745';

                const polyline = L.polyline(points, {
                    color: color,
                    weight: 3,
                    opacity: 0.8
                }).bindPopup(`
                    <div class="network-popup">
                        <h6>${network.name || 'Unnamed Network'}</h6>
                        <table>
                            <tr><td>Region:</td><td>${network.region || 'Unknown'}</td></tr>
                            <tr><td>Distance:</td><td>${(network.distance || 0).toFixed(2)} km</td></tr>
                            <tr><td>Status:</td><td><span class="status-badge" style="background:${color};"></span>${network.status || 'Active'}</td></tr>
                        </table>
                    </div>
                `);

                polyline.addTo(map);
                networkLayers.push(polyline);
            }
        });

        // Redraw nodes and connections for filtered networks
        drawNodes();
        connectHangingEndpoints();
    }
};

window.resetFilters = function() {
    document.getElementById('regionFilter').value = '';
    document.getElementById('linkTypeFilter').value = '';
    document.getElementById('statusFilter').value = '';
    filterNetworks();
};

window.refreshMap = function() {
    location.reload();
};

window.exportData = function() {
    // Convert network data to CSV
    const csv = [
        ['Network ID', 'Name', 'Region', 'Distance (km)', 'Fiber Cores', 'Link Type', 'Cost', 'Currency', 'Status'],
        ...networkPaths.map(n => [
            n.network_id,
            n.name,
            n.region,
            n.distance,
            n.fiber_cores,
            n.link_type,
            n.cost,
            n.currency,
            n.status
        ])
    ].map(row => row.join(',')).join('\n');

    // Download CSV
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'kenya-fibre-networks.csv';
    a.click();
};

window.refreshList = function() {
    location.reload();
};

// Handle window resize
window.addEventListener('resize', function() {
    if (map) {
        map.invalidateSize();
    }
});

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\project\darkfibre-crm\resources\views/kenya-fibre/dashboard.blade.php ENDPATH**/ ?>