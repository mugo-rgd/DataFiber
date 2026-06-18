{{-- resources/views/maintenance/requests/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Maintenance Request')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    #map { width: 100%; height: 400px; border-radius: 12px; border: 2px solid #0066B3; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .lease-preview { transition: all 0.3s ease; border-radius: 12px; overflow: hidden; }
    .lease-preview:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
    .customer-btn.active { background-color: #0066B3 !important; color: white !important; border-color: #0066B3 !important; box-shadow: 0 2px 8px rgba(0,102,179,0.3); }
    .customer-btn { transition: all 0.2s ease; border-radius: 10px; padding: 10px 15px; font-weight: 500; }
    .customer-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .form-section { background: #f8f9fa; border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid #e9ecef; }
    .form-section-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #0066B3; display: inline-block; }
    .required-field::after { content: '*'; color: #dc3545; margin-left: 4px; }
    .loading-spinner { display: inline-block; width: 1rem; height: 1rem; border: 2px solid #f3f3f3; border-top: 2px solid #0066B3; border-radius: 50%; animation: spin 1s linear infinite; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .badge-sla { font-size: 0.75rem; padding: 0.35rem 0.65rem; border-radius: 50px; }
    .info-box { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; padding: 15px; margin-bottom: 20px; }
    .step-indicator { display: flex; justify-content: space-between; margin-bottom: 30px; position: relative; }
    .step { flex: 1; text-align: center; position: relative; z-index: 1; }
    .step .step-circle { width: 40px; height: 40px; background: #e9ecef; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; color: #6c757d; transition: all 0.3s ease; }
    .step.active .step-circle { background: #0066B3; color: white; box-shadow: 0 0 0 3px rgba(0,102,179,0.3); }
    .step.completed .step-circle { background: #28a745; color: white; }
    .step .step-label { font-size: 0.75rem; margin-top: 8px; color: #6c757d; }
    .step.active .step-label { color: #0066B3; font-weight: 600; }
    .step.completed .step-label { color: #28a745; }
    .step-line { position: absolute; top: 20px; left: 0; right: 0; height: 2px; background: #e9ecef; z-index: 0; }

    /* No customers message */
    .no-customers-message {
        text-align: center;
        padding: 40px;
        background: #f8f9fa;
        border-radius: 12px;
    }
    .no-customers-icon {
        width: 80px;
        height: 80px;
        background: #e9ecef;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4 py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-plus-circle me-2"></i>Create Maintenance Request
            </h5>
            <p class="mb-0 mt-1 small opacity-75">Submit a new maintenance request for fibre infrastructure issues</p>
        </div>

        <div class="card-body p-4">
            @if($customers->count() > 0)
                <!-- Step Indicator -->
                <div class="step-indicator mb-5">
                    <div class="step-line"></div>
                    <div class="step {{ $selectedCustomerId ? 'completed' : 'active' }}">
                        <div class="step-circle"><i class="fas fa-user-check"></i></div>
                        <div class="step-label">Select Customer</div>
                    </div>
                    <div class="step {{ $selectedCustomerId && $leases->count() > 0 ? 'active' : '' }}">
                        <div class="step-circle"><i class="fas fa-file-contract"></i></div>
                        <div class="step-label">Select Lease</div>
                    </div>
                    <div class="step">
                        <div class="step-circle"><i class="fas fa-check-circle"></i></div>
                        <div class="step-label">Submit Request</div>
                    </div>
                </div>

                <!-- Customer Selection Section -->
                <div class="form-section">
                    <h6 class="form-section-title">
                        <i class="fas fa-users me-2 text-primary"></i>Select Customer
                    </h6>
                    <p class="text-muted small mb-3">Choose the customer experiencing the fibre issue</p>

                    <div class="row g-2">
                        @foreach($customers as $customer)
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                <a href="{{ route('maintenance.requests.create', ['customer_id' => $customer->id]) }}"
                                   class="btn btn-outline-primary w-100 text-start customer-btn {{ $selectedCustomerId == $customer->id ? 'active' : '' }}"
                                   style="white-space: normal; word-wrap: break-word;">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <div class="avatar-title bg-primary text-white rounded-circle" style="width: 32px; height: 32px; font-size: 14px; display: inline-flex; align-items: center; justify-content: center;">
                                                {{ substr($customer->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div>
                                            <strong>{{ $customer->name }}</strong>
                                            @if($customer->company_name)
                                                <br><small class="text-muted">{{ $customer->company_name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($selectedCustomerId && $selectedCustomer)
                    <!-- Selected Customer Info -->
                    <div class="info-box">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div>
                                <i class="fas fa-check-circle fa-2x me-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <small class="opacity-75">SELECTED CUSTOMER</small>
                                <h5 class="mb-0">{{ $selectedCustomer->name ?? '' }}</h5>
                                @if(!empty($selectedCustomer->company_name))
                                    <small class="opacity-75">{{ $selectedCustomer->company_name }}</small>
                                @endif
                            </div>
                            <div>
                                <span class="badge bg-white text-primary px-3 py-2">ID: {{ $selectedCustomerId }}</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('maintenance.requests.store') }}" method="POST" id="maintenanceForm">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $selectedCustomerId }}">

                        <!-- Lease & Priority Section -->
                        <div class="form-section">
                            <h6 class="form-section-title">
                                <i class="fas fa-file-contract me-2 text-primary"></i>Lease & Priority
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="lease_id" class="form-label fw-semibold required-field">Lease</label>
                                    <select class="form-select form-select-lg @error('lease_id') is-invalid @enderror" id="lease_id" name="lease_id" required>
                                        <option value="">-- Select Lease --</option>
                                        @foreach($leases as $lease)
                                            <option value="{{ $lease->id }}" {{ old('lease_id') == $lease->id ? 'selected' : '' }}
                                                data-monthly-cost="{{ $lease->monthly_cost }}"
                                                data-currency="{{ $lease->currency }}"
                                                data-lease-number="{{ $lease->lease_number }}"
                                                data-lease-title="{{ $lease->title }}">
                                                {{ $lease->lease_number }} - {{ $lease->title }} ({{ $lease->currency }} {{ number_format($lease->monthly_cost, 2) }}/mo)
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($leases->count() == 0)
                                        <div class="text-danger mt-2">
                                            <i class="fas fa-exclamation-triangle me-1"></i> No active leases found for this customer.
                                        </div>
                                    @endif
                                    <div class="form-text" id="leaseHelpText">
                                        <i class="fas fa-info-circle me-1"></i> Select the lease affected by this issue
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="priority" class="form-label fw-semibold required-field">Priority Level</label>
                                    <select class="form-select form-select-lg" id="priority" name="priority" required>
                                        <option value="">-- Select Priority --</option>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>🟢 Low</option>
                                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>🟠 High</option>
                                        <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                                    </select>
                                    <div class="form-text">
                                        <i class="fas fa-clock me-1"></i> SLA Response Time: <strong><span id="slaHint">24 hours</span></strong>
                                        <span class="badge bg-info ms-2 badge-sla">SLA Guaranteed</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="issue_type" class="form-label fw-semibold required-field">Issue Type</label>
                                    <select class="form-select form-select-lg" id="issue_type" name="issue_type" required>
                                        <option value="">-- Select Issue Type --</option>
                                        <option value="fibre_cut" {{ old('issue_type') == 'fibre_cut' ? 'selected' : '' }}>🔌 Fibre Cut</option>
                                        <option value="equipment_failure" {{ old('issue_type') == 'equipment_failure' ? 'selected' : '' }}>⚙️ Equipment Failure</option>
                                        <option value="signal_degradation" {{ old('issue_type') == 'signal_degradation' ? 'selected' : '' }}>📡 Signal Degradation</option>
                                        <option value="power_issue" {{ old('issue_type') == 'power_issue' ? 'selected' : '' }}>⚡ Power Issue</option>
                                        <option value="environmental" {{ old('issue_type') == 'environmental' ? 'selected' : '' }}>🌍 Environmental</option>
                                        <option value="preventive_maintenance" {{ old('issue_type') == 'preventive_maintenance' ? 'selected' : '' }}>🛠️ Preventive Maintenance</option>
                                        <option value="other" {{ old('issue_type') == 'other' ? 'selected' : '' }}>📝 Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Lease Preview -->
                        <div id="leasePreview" class="card border-success mb-4" style="display:none;">
                            <div class="card-header bg-success text-white py-2">
                                <i class="fas fa-file-contract me-2"></i>Selected Lease Details
                            </div>
                            <div class="card-body" id="leasePreviewContent"></div>
                        </div>

                        <!-- Request Details Section -->
                        <div class="form-section">
                            <h6 class="form-section-title">
                                <i class="fas fa-edit me-2 text-primary"></i>Request Details
                            </h6>

                            <div class="mb-3">
                                <label for="title" class="form-label fw-semibold required-field">Request Title</label>
                                <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{ old('title') }}"
                                       placeholder="e.g., Fibre Cut on Mombasa Road near City Centre" required>
                                <div class="form-text">
                                    <i class="fas fa-lightbulb me-1"></i> Provide a clear, descriptive title for quick identification
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="location" class="form-label fw-semibold">Specific Location</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-lg" id="location" name="location" value="{{ old('location') }}"
                                           placeholder="e.g., Kasarani, Nairobi, Kenya">
                                    <button type="button" class="btn btn-outline-secondary" id="searchLocationBtn">
                                        <i class="fas fa-search me-1"></i> Search
                                    </button>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-map-pin me-1"></i> Enter the exact location where the issue occurred
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Location Map</label>
                                <div id="map"></div>
                                <div class="form-text mt-2">
                                    <i class="fas fa-hand-pointer me-1"></i> Click on the map or drag the marker to pin the exact location
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitude" class="form-label fw-semibold">Latitude</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-globe-africa"></i></span>
                                        <input type="number" step="0.000001" class="form-control" id="latitude" name="latitude" value="{{ old('latitude', '-1.292065') }}">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitude" class="form-label fw-semibold">Longitude</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-globe-africa"></i></span>
                                        <input type="number" step="0.000001" class="form-control" id="longitude" name="longitude" value="{{ old('longitude', '36.821946') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold required-field">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required
                                          placeholder="Describe the issue in detail... Include any relevant information about the problem, when it started, and any troubleshooting already performed.">{{ old('description') }}</textarea>
                                <div class="form-text d-flex justify-content-between">
                                    <span><i class="fas fa-info-circle me-1"></i> Provide as much detail as possible</span>
                                    <span id="descriptionCounter" class="text-muted">0 characters</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('maintenance.requests.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Submit Request
                            </button>
                        </div>
                    </form>
                @elseif($selectedCustomerId && !$selectedCustomer)
                    <div class="alert alert-danger text-center py-4 mt-3">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <h5 class="mb-1">Invalid Customer Selection</h5>
                        <p class="mb-0">The selected customer is not assigned to you or does not exist. Please select a valid customer.</p>
                    </div>
                @else
                    <div class="alert alert-warning text-center py-4 mt-3">
                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                        <h5 class="mb-1">No Customer Selected</h5>
                        <p class="mb-0">Please select a customer from the list above to continue.</p>
                    </div>
                @endif
            @else
                <!-- No Customers Assigned Message -->
                <div class="no-customers-message">
                    <div class="no-customers-icon">
                        <i class="fas fa-users fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted mb-2">No Customers Assigned</h5>
                    <p class="text-muted mb-0">You don't have any customers assigned to you yet.<br>Please contact your administrator.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const leaseSelect = document.getElementById('lease_id');
        const leasePreview = document.getElementById('leasePreview');
        const leasePreviewContent = document.getElementById('leasePreviewContent');
        const titleField = document.getElementById('title');
        const issueTypeSelect = document.getElementById('issue_type');
        const prioritySelect = document.getElementById('priority');
        const slaHint = document.getElementById('slaHint');
        const descriptionField = document.getElementById('description');
        const descriptionCounter = document.getElementById('descriptionCounter');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('maintenanceForm');

        // Lease preview
        function showLeasePreview() {
            const selectedOption = leaseSelect.options[leaseSelect.selectedIndex];

            if (!leaseSelect.value || !selectedOption || selectedOption.value === '') {
                leasePreview.style.display = 'none';
                return;
            }

            const monthlyCost = selectedOption.getAttribute('data-monthly-cost') || 0;
            const currency = selectedOption.getAttribute('data-currency') || 'USD';
            const leaseNumber = selectedOption.getAttribute('data-lease-number') || '';
            const leaseTitle = selectedOption.getAttribute('data-lease-title') || '';

            leasePreviewContent.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">Lease Number</small>
                        <p class="mb-2 fw-bold">${leaseNumber}</p>
                        <small class="text-muted">Title</small>
                        <p class="mb-2">${leaseTitle}</p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Monthly Cost</small>
                        <p class="mb-2 fw-bold text-success">${currency} ${parseFloat(monthlyCost).toFixed(2)}/month</p>
                    </div>
                </div>
            `;
            leasePreview.style.display = 'block';

            // Auto-fill title if empty
            if (titleField && !titleField.value) {
                const issueTypeText = issueTypeSelect?.options[issueTypeSelect.selectedIndex]?.text || 'Maintenance';
                titleField.value = `${issueTypeText} for Lease ${leaseNumber}`;
            }
        }

        if (leaseSelect) {
            leaseSelect.addEventListener('change', showLeasePreview);
            if (leaseSelect.value) showLeasePreview();
        }

        // SLA hint update
        function updateSlaHint() {
            const slaMap = { 'critical': '4 hours', 'high': '8 hours', 'medium': '24 hours', 'low': '48 hours' };
            slaHint.textContent = slaMap[prioritySelect.value] || '24 hours';
        }

        if (prioritySelect) {
            prioritySelect.addEventListener('change', updateSlaHint);
            updateSlaHint();
        }

        // Description counter
        if (descriptionField && descriptionCounter) {
            descriptionField.addEventListener('input', function() {
                descriptionCounter.textContent = this.value.length + ' characters';
            });
        }

        // Form submission with loading state
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!leaseSelect.value) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Lease Required',
                        text: 'Please select a lease before submitting.',
                        confirmButtonColor: '#0066B3'
                    });
                    return;
                }

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<div class="loading-spinner me-2"></div> Submitting...';
                }
            });
        }

        // Map initialization
        let map = null;
        let marker = null;

        function initMap() {
            const lat = parseFloat(document.getElementById('latitude').value) || -1.292065;
            const lng = parseFloat(document.getElementById('longitude').value) || 36.821946;

            map = L.map('map').setView([lat, lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            marker = L.marker([lat, lng], { draggable: true }).addTo(map);

            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                document.getElementById('latitude').value = pos.lat.toFixed(6);
                document.getElementById('longitude').value = pos.lng.toFixed(6);
            });

            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                document.getElementById('latitude').value = e.latlng.lat.toFixed(6);
                document.getElementById('longitude').value = e.latlng.lng.toFixed(6);
            });

            setTimeout(() => map.invalidateSize(), 200);
        }

        // Location search
        const searchBtn = document.getElementById('searchLocationBtn');
        const locationInput = document.getElementById('location');

        if (searchBtn && locationInput) {
            searchBtn.addEventListener('click', async function() {
                const address = locationInput.value.trim();
                if (!address) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Location Required',
                        text: 'Please enter a location to search.',
                        confirmButtonColor: '#0066B3'
                    });
                    return;
                }

                searchBtn.disabled = true;
                searchBtn.innerHTML = '<div class="loading-spinner me-1"></div> Searching...';

                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`);
                    const data = await response.json();

                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lng = parseFloat(data[0].lon);

                        document.getElementById('latitude').value = lat.toFixed(6);
                        document.getElementById('longitude').value = lng.toFixed(6);

                        if (map && marker) {
                            map.setView([lat, lng], 15);
                            marker.setLatLng([lat, lng]);
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Location Found',
                            text: 'Location has been pinned on the map.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Location Not Found',
                            text: 'Please try a different address or landmark.',
                            confirmButtonColor: '#0066B3'
                        });
                    }
                } catch (error) {
                    console.error('Search error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Search Failed',
                        text: 'Error searching location. Please check your internet connection.',
                        confirmButtonColor: '#0066B3'
                    });
                } finally {
                    searchBtn.disabled = false;
                    searchBtn.innerHTML = '<i class="fas fa-search me-1"></i> Search';
                }
            });
        }

        setTimeout(initMap, 500);
    });
</script>
@endsection
