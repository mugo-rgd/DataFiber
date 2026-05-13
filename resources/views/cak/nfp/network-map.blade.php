<!DOCTYPE html>
<html>
<head>
    <title>Network Map - {{ $record->licensee_name }}</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        * { margin: 0; padding: 0; }
        #map { height: 100vh; width: 100%; }
        .info-panel {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            min-width: 250px;
            pointer-events: none;
        }
        .close-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: white;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            z-index: 1000;
            pointer-events: auto;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <div id="map"></div>

    <button class="close-btn" onclick="window.close()">
        <i class="fas fa-times"></i> Close
    </button>

    <div class="info-panel">
        <h5><i class="fas fa-network-wired"></i> {{ $record->licensee_name }}</h5>
        <hr>
        <p><strong>License No:</strong> {{ $record->license_no }}</p>
        <p><strong>Fibre Optic:</strong> {{ number_format($record->fibre_km ?? 0, 2) }} km</p>
        <p><strong>Towers:</strong> {{ $record->tower_count ?? 0 }}</p>
        <p><strong>Coordinates:</strong><br>
            Lat: {{ $latitude }}<br>
            Lon: {{ $longitude }}
        </p>
        <hr>
        <small class="text-muted">Q{{ $record->quarter }} {{ $record->financial_year }}</small>
    </div>

    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js"></script>
    <script>
        const latitude = {{ $latitude }};
        const longitude = {{ $longitude }};

        const map = L.map('map').setView([latitude, longitude], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Main marker for network location
        L.marker([latitude, longitude], {
            icon: L.divIcon({
                html: '<i class="fas fa-tower-broadcast" style="font-size: 30px; color: #ff9800;"></i>',
                iconSize: [30, 30],
                className: 'custom-marker'
            })
        }).addTo(map).bindPopup(`
            <b>{{ $record->licensee_name }}</b><br>
            Network Facility Location<br>
            <a href="https://www.google.com/maps?q=${latitude},${longitude}" target="_blank">Open in Google Maps</a>
        `).openPopup();

        // Add radius circle (10km coverage)
        L.circle([latitude, longitude], {
            color: '#ff9800',
            fillColor: '#ff9800',
            fillOpacity: 0.1,
            radius: 10000
        }).addTo(map).bindPopup('Estimated Coverage Area (10km)');

        // Example fibre route (simulated)
        @if($record->fibre_km > 0)
            const points = [
                [latitude, longitude],
                [latitude + 0.05, longitude + 0.03],
                [latitude + 0.08, longitude + 0.01],
                [latitude + 0.1, longitude - 0.02],
                [latitude + 0.12, longitude - 0.05]
            ];
            L.polyline(points, {
                color: '#2196f3',
                weight: 4,
                opacity: 0.7
            }).addTo(map).bindPopup('Fibre Optic Route');

            points.forEach(point => {
                L.circleMarker(point, {
                    radius: 3,
                    color: '#2196f3',
                    fillColor: '#2196f3'
                }).addTo(map);
            });
        @endif
    </script>
</body>
</html>
