{{-- resources/views/nfp/partials/network-location.blade.php --}}

<div class="cak-subtitle">1.3.5 Network Facility Location (for Map)</div>
<table class="cak-form-table">
    <tbody>
        <tr>
            <th style="width: 25%;">Latitude</th>
            <td style="width: 25%;">
                <input type="text" name="latitude" id="latitude"
                       value="{{ old('latitude', $record->latitude ?? '') }}"
                       placeholder="-1.286389">
                <small class="text-muted d-block">Example: -1.286389 (Nairobi)</small>
            </td>
            <th style="width: 25%;">Longitude</th>
            <td style="width: 25%;">
                <input type="text" name="longitude" id="longitude"
                       value="{{ old('longitude', $record->longitude ?? '') }}"
                       placeholder="36.817223">
                <small class="text-muted d-block">Example: 36.817223 (Nairobi)</small>
            </td>
        </tr>
        <tr>
            <th>Fibre Optic Cable Length (km)</th>
            <td>
                <input type="number" step="0.01" name="fibre_km"
                       value="{{ old('fibre_km', $record->fibre_km ?? 0) }}">
            </td>
            <th>Number of Towers</th>
            <td>
                <input type="number" name="tower_count"
                       value="{{ old('tower_count', $record->tower_count ?? 0) }}">
            </td>
        </tr>
        <tr>
            <th colspan="4">
                <button type="button" class="btn btn-sm btn-info" onclick="getCurrentLocation()">
                    <i class="fas fa-location-dot"></i> Get Current Location
                </button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="openMapPicker()">
                    <i class="fas fa-map"></i> Pick from Map
                </button>
                <span id="locationStatus" class="ms-2 small"></span>
            </th>
        </tr>
    </tbody>
</table>

@push('scripts')
<script>
function getCurrentLocation() {
    const statusSpan = document.getElementById('locationStatus');
    statusSpan.textContent = 'Getting location...';
    statusSpan.className = 'ms-2 small text-info';

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude.toFixed(7);
                document.getElementById('longitude').value = position.coords.longitude.toFixed(7);
                statusSpan.textContent = '✓ Location captured';
                statusSpan.className = 'ms-2 small text-success';
                setTimeout(() => { statusSpan.textContent = ''; }, 3000);
            },
            function(error) {
                statusSpan.textContent = '✗ Error: ' + error.message;
                statusSpan.className = 'ms-2 small text-danger';
            }
        );
    } else {
        statusSpan.textContent = '✗ Geolocation not supported';
        statusSpan.className = 'ms-2 small text-danger';
    }
}

function openMapPicker() {
    const lat = document.getElementById('latitude').value || -1.286389;
    const lng = document.getElementById('longitude').value || 36.817223;
    const mapWindow = window.open(`/map-picker?lat=${lat}&lng=${lng}`, 'MapPicker', 'width=800,height=600');

    window.addEventListener('message', function(event) {
        if (event.data && event.data.latitude && event.data.longitude) {
            document.getElementById('latitude').value = event.data.latitude;
            document.getElementById('longitude').value = event.data.longitude;
        }
    });
}
</script>
@endpush
