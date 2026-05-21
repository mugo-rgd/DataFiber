<!DOCTYPE html>
<html>
<head>
    <title>Select Network Location</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map { height: 100vh; width: 100%; }
        .controls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .info {
            position: fixed;
            top: 20px;
            left: 20px;
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            z-index: 1000;
            font-size: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div id="map"></div>
    <div class="info">
        <strong>Network Facility Location</strong><br>
        Click on map or drag marker to set location
    </div>
    <div class="controls">
        <button onclick="selectLocation()" class="btn btn-kp-success">
            ✓ Select This Location
        </button>
        <button onclick="closeWindow()" class="btn btn-secondary">
            ✗ Cancel
        </button>
    </div>

    <script>
        let selectedLat = {{ $lat }};
        let selectedLng = {{ $lng }};

        const map = L.map('map').setView([selectedLat, selectedLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let marker = L.marker([selectedLat, selectedLng], { draggable: true }).addTo(map);

        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            selectedLat = position.lat;
            selectedLng = position.lng;
        });

        map.on('click', function(e) {
            selectedLat = e.latlng.lat;
            selectedLng = e.latlng.lng;
            marker.setLatLng([selectedLat, selectedLng]);
        });

        function selectLocation() {
            if (window.opener) {
                window.opener.postMessage({
                    latitude: selectedLat.toFixed(7),
                    longitude: selectedLng.toFixed(7)
                }, '*');
                window.close();
            }
        }

        function closeWindow() {
            window.close();
        }
    </script>
</body>
</html>
