<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Route Segment - {{ $surveyRoute->route_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
            <h1 class="h3 mb-0 text-gray-800">Add Route Segment</h1>
            <div class="d-flex">
                <a href="{{ route('surveyor.routes.show', $surveyRoute->id) }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Route
                </a>
                <a href="{{ route('surveyor.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-tachometer-alt fa-sm text-white-50"></i> Dashboard
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-plus-circle mr-2"></i>
                            New Segment for {{ $surveyRoute->route_name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('surveyor.route-segments.store', $surveyRoute->id) }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="segment_name" class="form-label fw-bold">Segment Name *</label>
                                        <input type="text" class="form-control" id="segment_name" name="segment_name" required>
                                        <div class="form-text">Give this segment a descriptive name</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="installation_type" class="form-label fw-bold">Installation Type *</label>
                                        <select class="form-control" id="installation_type" name="installation_type" required>
                                            <option value="">Select type</option>
                                            <option value="aerial">Aerial</option>
                                            <option value="underground">Underground</option>
                                            <option value="conduit">Conduit</option>
                                            <option value="direct_burial">Direct Burial</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="distance_km" class="form-label fw-bold">Distance (km) *</label>
                                        <input type="number" class="form-control" id="distance_km" name="distance_km"
                                               step="0.001" min="0.001" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="terrain_type" class="form-label fw-bold">Terrain Type *</label>
                                        <select class="form-control" id="terrain_type" name="terrain_type" required>
                                            <option value="">Select terrain</option>
                                            <option value="urban">Urban</option>
                                            <option value="suburban">Suburban</option>
                                            <option value="rural">Rural</option>
                                            <option value="mountainous">Mountainous</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="complexity" class="form-label fw-bold">Complexity *</label>
                                        <select class="form-control" id="complexity" name="complexity" required>
                                            <option value="low">Low</option>
                                            <option value="medium" selected>Medium</option>
                                            <option value="high">High</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="pole_count" class="form-label">Pole Count</label>
                                        <input type="number" class="form-control" id="pole_count" name="pole_count"
                                               min="0" value="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="manhole_count" class="form-label">Manhole Count</label>
                                        <input type="number" class="form-control" id="manhole_count" name="manhole_count"
                                               min="0" value="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="splice_count" class="form-label">Splice Count</label>
                                        <input type="number" class="form-control" id="splice_count" name="splice_count"
                                               min="0" value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="cost_multiplier" class="form-label fw-bold">Cost Multiplier *</label>
                                <input type="number" class="form-control" id="cost_multiplier" name="cost_multiplier"
                                       step="0.1" min="1.0" value="1.0" required>
                                <div class="form-text">Multiplier for cost calculation based on complexity and terrain</div>
                            </div>

                            <div class="mb-3">
                                <label for="challenges" class="form-label">Challenges & Notes</label>
                                <textarea class="form-control" id="challenges" name="challenges" rows="3"
                                          placeholder="Describe any challenges, obstacles, or special considerations for this segment..."></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save mr-2"></i> Create Segment
                                </button>
                                <a href="{{ route('surveyor.routes.show', $surveyRoute->id) }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
