@extends('layouts.app')

@section('title', 'Create Route Segment - ' . $surveyRoute->route_name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Route Segment</h1>
        <div class="d-flex">
            <a href="{{ route('surveyor.routes.show', $surveyRoute->id) }}" class="btn btn-secondary mr-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Route
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-route mr-2"></i>
                        Segment Details - {{ $surveyRoute->route_name }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('surveyor.route-segments.store', $surveyRoute->id) }}" method="POST" id="routeSegmentForm">
                        @csrf

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3"><i class="fas fa-info-circle mr-2"></i>Basic Information</h5>

                                <div class="form-group">
                                    <label for="segment_number" class="font-weight-bold">Segment Number *</label>
                                    <input type="number" class="form-control @error('segment_number') is-invalid @enderror"
                                           id="segment_number" name="segment_number"
                                           value="{{ old('segment_number', $nextSegmentNumber) }}"
                                           min="1" required>
                                    @error('segment_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="segment_name" class="font-weight-bold">Segment Name *</label>
                                    <input type="text" class="form-control @error('segment_name') is-invalid @enderror"
                                           id="segment_name" name="segment_name"
                                           value="{{ old('segment_name') }}"
                                           placeholder="e.g., Downtown Section, Bridge Crossing, etc." required>
                                    @error('segment_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="installation_type" class="font-weight-bold">Installation Type *</label>
                                    <select class="form-control @error('installation_type') is-invalid @enderror"
                                            id="installation_type" name="installation_type" required>
                                        <option value="">Select Installation Type</option>
                                        <option value="aerial" {{ old('installation_type') == 'aerial' ? 'selected' : '' }}>Aerial</option>
                                        <option value="underground" {{ old('installation_type') == 'underground' ? 'selected' : '' }}>Underground</option>
                                        <option value="conduit" {{ old('installation_type') == 'conduit' ? 'selected' : '' }}>Conduit</option>
                                        <option value="direct_burial" {{ old('installation_type') == 'direct_burial' ? 'selected' : '' }}>Direct Burial</option>
                                    </select>
                                    @error('installation_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="distance_km" class="font-weight-bold">Distance (km) *</label>
                                    <input type="number" class="form-control @error('distance_km') is-invalid @enderror"
                                           id="distance_km" name="distance_km"
                                           value="{{ old('distance_km') }}"
                                           step="0.001" min="0.001" max="999.999"
                                           placeholder="0.000" required>
                                    @error('distance_km')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="text-primary mb-3"><i class="fas fa-mountain mr-2"></i>Terrain & Complexity</h5>

                                <div class="form-group">
                                    <label for="terrain_type" class="font-weight-bold">Terrain Type *</label>
                                    <select class="form-control @error('terrain_type') is-invalid @enderror"
                                            id="terrain_type" name="terrain_type" required>
                                        <option value="">Select Terrain Type</option>
                                        <option value="urban" {{ old('terrain_type') == 'urban' ? 'selected' : '' }}>Urban</option>
                                        <option value="suburban" {{ old('terrain_type') == 'suburban' ? 'selected' : '' }}>Suburban</option>
                                        <option value="rural" {{ old('terrain_type') == 'rural' ? 'selected' : '' }}>Rural</option>
                                        <option value="mountainous" {{ old('terrain_type') == 'mountainous' ? 'selected' : '' }}>Mountainous</option>
                                        <option value="forest" {{ old('terrain_type') == 'forest' ? 'selected' : '' }}>Forest</option>
                                        <option value="wetlands" {{ old('terrain_type') == 'wetlands' ? 'selected' : '' }}>Wetlands</option>
                                        <option value="desert" {{ old('terrain_type') == 'desert' ? 'selected' : '' }}>Desert</option>
                                        <option value="coastal" {{ old('terrain_type') == 'coastal' ? 'selected' : '' }}>Coastal</option>
                                    </select>
                                    @error('terrain_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="complexity" class="font-weight-bold">Complexity Level *</label>
                                    <select class="form-control @error('complexity') is-invalid @enderror"
                                            id="complexity" name="complexity" required>
                                        <option value="">Select Complexity</option>
                                        <option value="low" {{ old('complexity') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('complexity') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('complexity') == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                    @error('complexity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="cost_multiplier" class="font-weight-bold">Cost Multiplier *</label>
                                    <input type="number" class="form-control @error('cost_multiplier') is-invalid @enderror"
                                           id="cost_multiplier" name="cost_multiplier"
                                           value="{{ old('cost_multiplier', 1.00) }}"
                                           step="0.01" min="1.00" max="5.00"
                                           placeholder="1.00" required>
                                    <small class="form-text text-muted">Base cost multiplier based on complexity and terrain</small>
                                    @error('cost_multiplier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Infrastructure Counts -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3"><i class="fas fa-hard-hat mr-2"></i>Infrastructure Details</h5>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pole_count" class="font-weight-bold">Pole Count</label>
                                    <input type="number" class="form-control @error('pole_count') is-invalid @enderror"
                                           id="pole_count" name="pole_count"
                                           value="{{ old('pole_count', 0) }}"
                                           min="0" max="1000">
                                    <small class="form-text text-muted">Number of utility poles needed</small>
                                    @error('pole_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="manhole_count" class="font-weight-bold">Manhole Count</label>
                                    <input type="number" class="form-control @error('manhole_count') is-invalid @enderror"
                                           id="manhole_count" name="manhole_count"
                                           value="{{ old('manhole_count', 0) }}"
                                           min="0" max="500">
                                    <small class="form-text text-muted">Number of manholes required</small>
                                    @error('manhole_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="splice_count" class="font-weight-bold">Splice Count</label>
                                    <input type="number" class="form-control @error('splice_count') is-invalid @enderror"
                                           id="splice_count" name="splice_count"
                                           value="{{ old('splice_count', 0) }}"
                                           min="0" max="200">
                                    <small class="form-text text-muted">Number of fiber splices needed</small>
                                    @error('splice_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Obstacles -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3"><i class="fas fa-exclamation-triangle mr-2"></i>Obstacles & Challenges</h5>

                                <div class="form-group">
                                    <label class="font-weight-bold">Obstacles (Select all that apply)</label>
                                    <div class="row">
                                        @php
                                            $obstacles = [
                                                'rivers' => 'Rivers/Water Crossings',
                                                'highways' => 'Highways/Roads',
                                                'railways' => 'Railway Crossings',
                                                'buildings' => 'Buildings/Structures',
                                                'parks' => 'Parks/Protected Areas',
                                                'utilities' => 'Existing Utilities',
                                                'rock' => 'Rock Formations',
                                                'traffic' => 'Heavy Traffic',
                                                'permits' => 'Permit Requirements',
                                                'weather' => 'Weather Conditions'
                                            ];
                                            $oldObstacles = old('obstacles', []);
                                        @endphp
                                        @foreach($obstacles as $key => $label)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="obstacles[]" value="{{ $key }}"
                                                           id="obstacle_{{ $key }}"
                                                           {{ in_array($key, $oldObstacles) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="obstacle_{{ $key }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                                </div>
                                        @endforeach
                                    </div>
                                    @error('obstacles')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="challenges" class="font-weight-bold">Additional Challenges & Notes</label>
                                    <textarea class="form-control @error('challenges') is-invalid @enderror"
                                              id="challenges" name="challenges"
                                              rows="4"
                                              placeholder="Describe any additional challenges, access issues, environmental concerns, or special requirements...">{{ old('challenges') }}</textarea>
                                    @error('challenges')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- GPS Coordinates -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3"><i class="fas fa-map-marker-alt mr-2"></i>GPS Coordinates (Optional)</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Start Point</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="start_lat" class="font-weight-bold">Start Latitude</label>
                                                    <input type="number" class="form-control @error('start_lat') is-invalid @enderror"
                                                           id="start_lat" name="start_lat"
                                                           value="{{ old('start_lat') }}"
                                                           step="0.000001" min="-90" max="90"
                                                           placeholder="e.g., 40.712776">
                                                    @error('start_lat')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="start_lng" class="font-weight-bold">Start Longitude</label>
                                                    <input type="number" class="form-control @error('start_lng') is-invalid @enderror"
                                                           id="start_lng" name="start_lng"
                                                           value="{{ old('start_lng') }}"
                                                           step="0.000001" min="-180" max="180"
                                                           placeholder="e.g., -74.005974">
                                                    @error('start_lng')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="getCurrentLocation('start')">
                                            <i class="fas fa-location-arrow mr-1"></i> Use Current Location
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">End Point</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="end_lat" class="font-weight-bold">End Latitude</label>
                                                    <input type="number" class="form-control @error('end_lat') is-invalid @enderror"
                                                           id="end_lat" name="end_lat"
                                                           value="{{ old('end_lat') }}"
                                                           step="0.000001" min="-90" max="90"
                                                           placeholder="e.g., 40.712776">
                                                    @error('end_lat')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="end_lng" class="font-weight-bold">End Longitude</label>
                                                    <input type="number" class="form-control @error('end_lng') is-invalid @enderror"
                                                           id="end_lng" name="end_lng"
                                                           value="{{ old('end_lng') }}"
                                                           step="0.000001" min="-180" max="180"
                                                           placeholder="e.g., -74.005974">
                                                    @error('end_lng')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="getCurrentLocation('end')">
                                            <i class="fas fa-location-arrow mr-1"></i> Use Current Location
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cost Preview -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0"><i class="fas fa-calculator mr-2"></i>Cost Estimate Preview</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-md-4">
                                                <h6>Distance</h6>
                                                <h4 id="previewDistance">0.000 km</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <h6>Multiplier</h6>
                                                <h4 id="previewMultiplier">1.00x</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <h6>Estimated Cost</h6>
                                                <h4 id="previewCost">$0.00</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('surveyor.routes.show', $surveyRoute->id) }}" class="btn btn-secondary">
                                        <i class="fas fa-times mr-2"></i>Cancel
                                    </a>
                                    <div>
                                        <button type="submit" name="action" value="save_and_new" class="btn btn-primary mr-2">
                                            <i class="fas fa-save mr-2"></i>Save & Add Another
                                        </button>
                                        <button type="submit" name="action" value="save" class="btn btn-success">
                                            <i class="fas fa-check mr-2"></i>Save Segment
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Cost calculation and preview
    function calculateCost() {
        const distance = parseFloat(document.getElementById('distance_km').value) || 0;
        const multiplier = parseFloat(document.getElementById('cost_multiplier').value) || 1.00;
        const baseCostPerKm = 1000; // Base cost per km

        const estimatedCost = distance * baseCostPerKm * multiplier;

        document.getElementById('previewDistance').textContent = distance.toFixed(3) + ' km';
        document.getElementById('previewMultiplier').textContent = multiplier.toFixed(2) + 'x';
        document.getElementById('previewCost').textContent = '$' + estimatedCost.toFixed(2);
    }

    // Auto-calculate cost when inputs change
    document.getElementById('distance_km').addEventListener('input', calculateCost);
    document.getElementById('cost_multiplier').addEventListener('input', calculateCost);

    // Initialize calculation on page load
    document.addEventListener('DOMContentLoaded', calculateCost);

    // GPS Location functionality
    function getCurrentLocation(type) {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                document.getElementById(type + '_lat').value = lat.toFixed(6);
                document.getElementById(type + '_lng').value = lng.toFixed(6);

                // Show success message
                showAlert('Location captured successfully!', 'success');
            },
            function(error) {
                let message = 'Unable to retrieve your location';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        message = 'Location access denied by user';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = 'Location information unavailable';
                        break;
                    case error.TIMEOUT:
                        message = 'Location request timed out';
                        break;
                }
                showAlert(message, 'error');
            }
        );
    }

    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed"
                 style="top: 20px; right: 20px; z-index: 9999;">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', alertHtml);

        // Auto remove after 5 seconds
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) alert.remove();
        }, 5000);
    }

    // Auto-adjust cost multiplier based on complexity and terrain
    document.getElementById('complexity').addEventListener('change', function() {
        const complexity = this.value;
        const terrain = document.getElementById('terrain_type').value;
        let multiplier = 1.00;

        // Base multiplier from complexity
        if (complexity === 'medium') multiplier = 1.25;
        if (complexity === 'high') multiplier = 1.75;

        // Additional multiplier from terrain
        if (terrain === 'mountainous' || terrain === 'wetlands') multiplier += 0.25;
        if (terrain === 'urban') multiplier += 0.15;

        document.getElementById('cost_multiplier').value = multiplier.toFixed(2);
        calculateCost();
    });

    document.getElementById('terrain_type').addEventListener('change', function() {
        // Trigger complexity change to recalculate
        document.getElementById('complexity').dispatchEvent(new Event('change'));
    });

    // Form validation
    document.getElementById('routeSegmentForm').addEventListener('submit', function(e) {
        const distance = parseFloat(document.getElementById('distance_km').value);
        if (distance <= 0) {
            e.preventDefault();
            alert('Please enter a valid distance greater than 0 km');
            document.getElementById('distance_km').focus();
        }
    });
</script>
@endpush

@push('styles')
<style>
    .form-group {
        margin-bottom: 1.5rem;
    }
    .card {
        margin-bottom: 1.5rem;
    }
    .alert {
        min-width: 300px;
    }
</style>
@endpush
