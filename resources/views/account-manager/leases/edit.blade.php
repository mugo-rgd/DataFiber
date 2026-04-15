@extends('layouts.app')

@section('title', 'Edit Lease - ' . $lease->lease_number)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-edit text-primary"></i> Edit Lease
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('account-manager.leases.index') }}">Lease Management</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('account-manager.leases.show', $lease) }}">{{ $lease->lease_number }}</a>
                            </li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('account-manager.leases.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Leases
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            Please fix the following errors:
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-file-contract me-2"></i>Lease Details
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('account-manager.leases.update', $lease) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Basic Information</h5>

                        <div class="mb-3">
                            <label for="lease_number" class="form-label">Lease Number *</label>
                            <input type="text" class="form-control @error('lease_number') is-invalid @enderror"
                                   id="lease_number" name="lease_number"
                                   value="{{ old('lease_number', $lease->lease_number) }}" required>
                            @error('lease_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Customer *</label>
                            <select class="form-select @error('customer_id') is-invalid @enderror"
                                    id="customer_id" name="customer_id" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ old('customer_id', $lease->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="service_type" class="form-label">Service Type *</label>
                            <select class="form-select @error('service_type') is-invalid @enderror"
                                    id="service_type" name="service_type" required>
                                <option value="dark_fibre" {{ old('service_type', $lease->service_type) == 'dark_fibre' ? 'selected' : '' }}>Dark Fibre IRU/Lease</option>
                                <option value="colocation" {{ old('service_type', $lease->service_type) == 'colocation' ? 'selected' : '' }}>Colocation (Dark Fiber IRU/Lease)</option>
                                <option value="wavelength" {{ old('service_type', $lease->service_type) == 'wavelength' ? 'selected' : '' }}>Wavelength Service (Lit Service)</option>
                            </select>
                            @error('service_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @php
                            $currentService = old('service_type', $lease->service_type ?? '');
                            $currentTech = old('technology', $lease->technology ?? '');

                            // Auto-set technology based on service type for existing data
                            if ($currentService == 'colocation' && empty($currentTech)) {
                                $currentTech = 'colocation';
                            }
                            if ($currentService == 'wavelength' && empty($currentTech)) {
                                $currentTech = 'dwdm';
                            }

                            // Determine if technology select should be disabled
                            $techDisabled = ($currentService == 'colocation' || $currentService == 'wavelength');

                            // Determine which options to show
                            $showDarkFibreOptions = ($currentService == 'dark_fibre' || $currentService == '');

                            // Selected values for options
                            $selectedMetro = $currentTech == 'metro' ? 'selected' : '';
                            $selectedNonPremium = $currentTech == 'non_premium' ? 'selected' : '';
                            $selectedPremium = $currentTech == 'premium' ? 'selected' : '';
                            $selectedColocation = $currentTech == 'colocation' ? 'selected' : '';
                            $selectedDwdm = $currentTech == 'dwdm' ? 'selected' : '';
                        @endphp

                        <div class="mb-3">
                            <label for="technology" class="form-label">Technology Type *</label>
                            <select class="form-select @error('technology') is-invalid @enderror"
                                    id="technology"
                                    name="technology"
                                    {{ $techDisabled ? 'disabled' : '' }}
                                    required>
                                @if($showDarkFibreOptions)
                                    <option value="">-- Select Technology --</option>
                                    <option value="metro" {{ $selectedMetro }}>METRO</option>
                                    <option value="non_premium" {{ $selectedNonPremium }}>NON PREMIUM</option>
                                    <option value="premium" {{ $selectedPremium }}>PREMIUM</option>
                                @elseif($currentService == 'colocation')
                                    <option value="colocation" {{ $selectedColocation }}>COLOCATION</option>
                                @elseif($currentService == 'wavelength')
                                    <option value="dwdm" {{ $selectedDwdm }}>DWDM (Dense Wavelength Division Multiplexing)</option>
                                @else
                                    <option value="">-- Select Technology --</option>
                                    <option value="metro">METRO</option>
                                    <option value="non_premium">NON PREMIUM</option>
                                    <option value="premium">PREMIUM</option>
                                    <option value="colocation">COLOCATION</option>
                                    <option value="dwdm">DWDM (Dense Wavelength Division Multiplexing)</option>
                                @endif
                            </select>

                            {{-- Hidden input to submit value when select is disabled --}}
                            @if($techDisabled)
                                <input type="hidden" name="technology" value="{{ $currentTech }}">
                            @endif

                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                <span id="tech-hint">
                                    @if($currentService == 'dark_fibre')
                                        Select one: METRO (urban/short distance), NON PREMIUM (standard service), or PREMIUM (high-priority service)
                                    @elseif($currentService == 'colocation')
                                        COLOCATION: Physical space, power, and cooling for optical equipment
                                    @elseif($currentService == 'wavelength')
                                        DWDM: Dense Wavelength Division Multiplexing for high-capacity lit service
                                    @else
                                        Select a service type above to see available technologies
                                    @endif
                                </span>
                            </small>
                            @error('technology')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status" name="status" required>
                                <option value="draft" {{ old('status', $lease->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="pending" {{ old('status', $lease->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="active" {{ old('status', $lease->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="expired" {{ old('status', $lease->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="terminated" {{ old('status', $lease->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Route Information -->
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Route Information</h5>

                        <div class="mb-3">
                            <label for="start_location" id="start_location_label" class="form-label">Start Location *</label>
                            <input type="text" class="form-control @error('start_location') is-invalid @enderror"
                                   id="start_location" name="start_location"
                                   value="{{ old('start_location', $lease->start_location) }}">
                            @error('start_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="end_location" id="end_location_label" class="form-label">End Location *</label>
                            <input type="text" class="form-control @error('end_location') is-invalid @enderror"
                                   id="end_location" name="end_location"
                                   value="{{ old('end_location', $lease->end_location) }}">
                            @error('end_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="host_location" id="host_location_label" class="form-label">Host Location *</label>
                            <input type="text" class="form-control @error('host_location') is-invalid @enderror"
                                   id="host_location" name="host_location"
                                   value="{{ old('host_location', $lease->host_location) }}">
                            @error('host_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="distance_km" class="form-label">Distance (KM)</label>
                            <input type="number" step="0.01" class="form-control @error('distance_km') is-invalid @enderror"
                                   id="distance_km" name="distance_km"
                                   value="{{ old('distance_km', $lease->distance_km) }}">
                            @error('distance_km')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="bandwidth" class="form-label">Bandwidth</label>
                            <input type="text" class="form-control @error('bandwidth') is-invalid @enderror"
                                   id="bandwidth" name="bandwidth"
                                   value="{{ old('bandwidth', $lease->bandwidth) }}"
                                   placeholder="e.g., 2 cores, 10Gbps, etc.">
                            @error('bandwidth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="cores_required" class="form-label">Core(s)</label>
                            <input type="number" class="form-control @error('cores_required') is-invalid @enderror"
                                   id="cores_required" name="cores_required"
                                   value="{{ old('cores_required', $lease->cores_required) }}"
                                   placeholder="e.g., 1, 2, 3 etc.">
                            @error('cores_required')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <!-- Financial Information -->
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Financial Information</h5>

                        <div class="mb-3">
                            <label for="monthly_cost" class="form-label">Monthly Cost *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control @error('monthly_cost') is-invalid @enderror"
                                       id="monthly_cost" name="monthly_cost"
                                       value="{{ old('monthly_cost', $lease->monthly_cost) }}" required>
                            </div>
                            @error('monthly_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="installation_fee" class="form-label">Installation Fee</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control @error('installation_fee') is-invalid @enderror"
                                       id="installation_fee" name="installation_fee"
                                       value="{{ old('installation_fee', $lease->installation_fee) }}">
                            </div>
                            @error('installation_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="currency" class="form-label">Currency *</label>
                            <select class="form-select @error('currency') is-invalid @enderror"
                                    id="currency" name="currency" required>
                                <option value="USD" {{ old('currency', $lease->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="KSH" {{ old('currency', $lease->currency) == 'KSH' ? 'selected' : '' }}>KSH</option>
                            </select>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Contract Dates -->
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Contract Dates</h5>

                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date *</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                   id="start_date" name="start_date"
                                   value="{{ old('start_date', $lease->start_date instanceof \Carbon\Carbon ? $lease->start_date->format('Y-m-d') : $lease->start_date) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date *</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                   id="end_date" name="end_date"
                                   value="{{ old('end_date', $lease->end_date instanceof \Carbon\Carbon ? $lease->end_date->format('Y-m-d') : $lease->end_date) }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contract_term_months" class="form-label">Contract Term (Months) *</label>
                            <input type="number" class="form-control @error('contract_term_months') is-invalid @enderror"
                                   id="contract_term_months" name="contract_term_months"
                                   value="{{ old('contract_term_months', $lease->contract_term_months) }}" required>
                            @error('contract_term_months')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="billing_cycle" class="form-label">Billing Cycle *</label>
                            <select class="form-select @error('billing_cycle') is-invalid @enderror"
                                    id="billing_cycle" name="billing_cycle" required>
                                <option value="monthly" {{ old('billing_cycle', $lease->billing_cycle) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ old('billing_cycle', $lease->billing_cycle) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="annually" {{ old('billing_cycle', $lease->billing_cycle) == 'annually' ? 'selected' : '' }}>Annually</option>
                            </select>
                            @error('billing_cycle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <!-- Technical Specifications -->
                    <div class="col-12 mb-4">
                        <h5 class="text-primary mb-3">Technical Specifications</h5>
                        <textarea class="form-control @error('technical_specifications') is-invalid @enderror"
                                  id="technical_specifications" name="technical_specifications"
                                  rows="4" placeholder="Enter technical specifications...">{{ old('technical_specifications', $lease->technical_specifications) }}</textarea>
                        @error('technical_specifications')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Service Level Agreement -->
                    <div class="col-12 mb-4">
                        <h5 class="text-primary mb-3">Service Level Agreement (SLA)</h5>
                        <textarea class="form-control @error('service_level_agreement') is-invalid @enderror"
                                  id="service_level_agreement" name="service_level_agreement"
                                  rows="4" placeholder="Enter service level agreement terms...">{{ old('service_level_agreement', $lease->service_level_agreement) }}</textarea>
                        @error('service_level_agreement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="col-12 mb-4">
                        <h5 class="text-primary mb-3">Terms and Conditions</h5>
                        <textarea class="form-control @error('terms_and_conditions') is-invalid @enderror"
                                  id="terms_and_conditions" name="terms_and_conditions"
                                  rows="4" placeholder="Enter terms and conditions...">{{ old('terms_and_conditions', $lease->terms_and_conditions) }}</textarea>
                        @error('terms_and_conditions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="col-12 mb-4">
                        <h5 class="text-primary mb-3">Notes</h5>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes" name="notes"
                                  rows="3" placeholder="Enter any additional notes...">{{ old('notes', $lease->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="{{ route('account-manager.leases.show', $lease) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Lease
                        </button>

                        @if(in_array($lease->status, ['pending', 'draft']))
                            <button type="button" class="btn btn-success" onclick="confirmApproval()">
                                <i class="fas fa-check me-2"></i>Approve & Save
                            </button>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Hidden form for approve action -->
            @if(in_array($lease->status, ['pending', 'draft']))
                <form id="approve-form" action="{{ route('account-manager.leases.approve', $lease) }}" method="POST" class="d-none">
                    @csrf
                    @method('PATCH')
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calculate contract term based on dates
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const contractTerm = document.getElementById('contract_term_months');

    function calculateTerm() {
        if (startDate.value && endDate.value) {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);
            const months = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
            if (months > 0) {
                contractTerm.value = months;
            }
        }
    }

    if (startDate && endDate && contractTerm) {
        startDate.addEventListener('change', calculateTerm);
        endDate.addEventListener('change', calculateTerm);
    }

    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // =============================================
    // Service Type to Technology Type Mapping & Field Disabling
    // =============================================
    const serviceTypeSelect = document.getElementById('service_type');
    const technologySelect = document.getElementById('technology');
    const techHint = document.getElementById('tech-hint');

    // Get the existing technology value from the server
    const existingTechnology = '{{ old('technology', $lease->technology) }}';
    const existingServiceType = '{{ old('service_type', $lease->service_type) }}';

    // Get all fields
    const startLocation = document.getElementById('start_location');
    const endLocation = document.getElementById('end_location');
    const hostLocation = document.getElementById('host_location');
    const distanceKm = document.getElementById('distance_km');
    const bandwidth = document.getElementById('bandwidth');
    const coresRequired = document.getElementById('cores_required');

    // Get labels for required field indicators
    const startLocationLabel = document.getElementById('start_location_label');
    const endLocationLabel = document.getElementById('end_location_label');
    const hostLocationLabel = document.getElementById('host_location_label');

    function updateFieldsByServiceType() {
        if (!serviceTypeSelect) return;

        const serviceType = serviceTypeSelect.value;

        // Remove any existing hidden input first
        const existingHidden = document.getElementById('hidden_technology_input');
        if (existingHidden) existingHidden.remove();

        // Clear current options
        if (technologySelect) {
            technologySelect.innerHTML = '';
        }

        // =============================================
        // TECHNOLOGY DROPDOWN OPTIONS
        // =============================================
        if (serviceType === 'colocation') {
            // Only show COLOCATION option
            if (technologySelect) {
                const option = document.createElement('option');
                option.value = 'colocation';
                option.textContent = 'COLOCATION';
                option.selected = true;
                technologySelect.appendChild(option);
                technologySelect.disabled = true;
                if (techHint) {
                    techHint.innerHTML = 'COLOCATION: Physical space, power, and cooling for optical equipment';
                }
                // Add hidden input to submit the value
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'technology';
                hidden.id = 'hidden_technology_input';
                hidden.value = 'colocation';
                technologySelect.parentNode.appendChild(hidden);
            }

        } else if (serviceType === 'wavelength') {
            // Only show DWDM option
            if (technologySelect) {
                const option = document.createElement('option');
                option.value = 'dwdm';
                option.textContent = 'DWDM (Dense Wavelength Division Multiplexing)';
                option.selected = true;
                technologySelect.appendChild(option);
                technologySelect.disabled = true;
                if (techHint) {
                    techHint.innerHTML = 'DWDM: Dense Wavelength Division Multiplexing for high-capacity lit service';
                }
                // Add hidden input to submit the value
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'technology';
                hidden.id = 'hidden_technology_input';
                hidden.value = 'dwdm';
                technologySelect.parentNode.appendChild(hidden);
            }

        } else if (serviceType === 'dark_fibre') {
            // Only show METRO, NON PREMIUM, PREMIUM options
            if (technologySelect) {
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = '-- Select Technology --';
                placeholder.disabled = true;
                technologySelect.appendChild(placeholder);

                const metro = document.createElement('option');
                metro.value = 'metro';
                metro.textContent = 'METRO';
                technologySelect.appendChild(metro);

                const nonPremium = document.createElement('option');
                nonPremium.value = 'non_premium';
                nonPremium.textContent = 'NON PREMIUM';
                technologySelect.appendChild(nonPremium);

                const premium = document.createElement('option');
                premium.value = 'premium';
                premium.textContent = 'PREMIUM';
                technologySelect.appendChild(premium);

                technologySelect.disabled = false;

                // Preserve the existing technology value if it exists
                if (existingTechnology && (existingTechnology === 'metro' || existingTechnology === 'non_premium' || existingTechnology === 'premium')) {
                    technologySelect.value = existingTechnology;
                }

                if (techHint) {
                    techHint.innerHTML = 'Select one: METRO (urban/short distance), NON PREMIUM (standard service), or PREMIUM (high-priority service)';
                }
            }

        } else {
            // No service type selected - show all options (default)
            if (technologySelect) {
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = '-- Select Technology --';
                placeholder.disabled = true;
                placeholder.selected = true;
                technologySelect.appendChild(placeholder);

                const metro = document.createElement('option');
                metro.value = 'metro';
                metro.textContent = 'METRO';
                technologySelect.appendChild(metro);

                const nonPremium = document.createElement('option');
                nonPremium.value = 'non_premium';
                nonPremium.textContent = 'NON PREMIUM';
                technologySelect.appendChild(nonPremium);

                const premium = document.createElement('option');
                premium.value = 'premium';
                premium.textContent = 'PREMIUM';
                technologySelect.appendChild(premium);

                const colocation = document.createElement('option');
                colocation.value = 'colocation';
                colocation.textContent = 'COLOCATION';
                technologySelect.appendChild(colocation);

                const dwdm = document.createElement('option');
                dwdm.value = 'dwdm';
                dwdm.textContent = 'DWDM (Dense Wavelength Division Multiplexing)';
                technologySelect.appendChild(dwdm);

                technologySelect.disabled = false;

                // Preserve the existing technology value
                if (existingTechnology) {
                    technologySelect.value = existingTechnology;
                }

                if (techHint) {
                    techHint.innerHTML = 'Select a service type above to see available technologies';
                }
            }
        }

        // =============================================
        // FIELD DISABLING BASED ON SERVICE TYPE
        // =============================================

        // Reset all fields to enabled first (remove disabled attribute)
        if (startLocation) startLocation.disabled = false;
        if (endLocation) endLocation.disabled = false;
        if (hostLocation) hostLocation.disabled = false;
        if (distanceKm) distanceKm.disabled = false;
        if (bandwidth) bandwidth.disabled = false;
        if (coresRequired) coresRequired.disabled = false;

        // Reset required labels (remove asterisk)
        if (startLocationLabel) startLocationLabel.innerHTML = 'Start Location';
        if (endLocationLabel) endLocationLabel.innerHTML = 'End Location';
        if (hostLocationLabel) hostLocationLabel.innerHTML = 'Host Location';

        // Reset required attributes
        if (startLocation) startLocation.required = false;
        if (endLocation) endLocation.required = false;
        if (hostLocation) hostLocation.required = false;

        // Apply specific disabling rules
        switch (serviceType) {
            case 'dark_fibre':
                // Disable: Host Location, Bandwidth
                if (hostLocation) hostLocation.disabled = true;
                if (bandwidth) bandwidth.disabled = true;
                // Make Start Location and End Location required
                if (startLocation) startLocation.required = true;
                if (endLocation) endLocation.required = true;
                if (startLocationLabel) startLocationLabel.innerHTML = 'Start Location *';
                if (endLocationLabel) endLocationLabel.innerHTML = 'End Location *';
                break;

            case 'colocation':
                // Disable: Core(s), Bandwidth, Start Location, End Location, Distance (KM)
                if (coresRequired) coresRequired.disabled = true;
                if (bandwidth) bandwidth.disabled = true;
                if (startLocation) startLocation.disabled = true;
                if (endLocation) endLocation.disabled = true;
                if (distanceKm) distanceKm.disabled = true;
                // Make Host Location required
                if (hostLocation) hostLocation.required = true;
                if (hostLocationLabel) hostLocationLabel.innerHTML = 'Host Location *';
                break;

            case 'wavelength':
                // ONLY disable Core(s) and Distance (KM)
                // Start Location and End Location remain ENABLED and VISIBLE
                // Host Location is disabled
                if (coresRequired) coresRequired.disabled = true;
                if (distanceKm) distanceKm.disabled = true;
                if (hostLocation) hostLocation.disabled = true;
                // Start Location and End Location are NOT disabled - they stay enabled
                // No required fields for wavelength
                break;

            default:
                // No fields disabled (all enabled)
                break;
        }

        // Add visual feedback for disabled fields
        const allFields = [startLocation, endLocation, hostLocation, distanceKm, bandwidth, coresRequired];
        allFields.forEach(field => {
            if (field) {
                if (field.disabled) {
                    field.classList.add('bg-light');
                    // Remove required attribute if field is disabled
                    field.required = false;
                } else {
                    field.classList.remove('bg-light');
                }
            }
        });
    }

    // Add event listener to service type dropdown
    if (serviceTypeSelect) {
        // First, set the initial service type value
        if (existingServiceType) {
            serviceTypeSelect.value = existingServiceType;
        }

        // Then update fields based on the existing service type
        updateFieldsByServiceType();

        // Add change event listener
        serviceTypeSelect.addEventListener('change', updateFieldsByServiceType);
    }
});

function confirmApproval() {
    if (confirm('Are you sure you want to approve this lease? This will make it active.')) {
        document.getElementById('approve-form').submit();
    }
}
</script>
@endpush
