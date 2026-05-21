@extends('layouts.app')

@section('title', 'Add New Fibre Link - Dark Fibre CRM')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="dashboard-header bg-gradient-primary text-white py-2 py-sm-3 py-md-4">
        <div class="container-fluid px-3 px-sm-4 px-md-5">
            <div class="row align-items-center">
                <div class="col-12 col-lg-8 mb-2 mb-lg-0">
                    <div class="d-flex align-items-center">
                        <div class="header-icon me-2 me-sm-3">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h1 class="mb-1">Add New Fibre Link</h1>
                            <p class="mb-0 opacity-75">Create a new fibre link conversion data record</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-lg-end">
                        <a href="{{ route('conversion-data.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="container-fluid px-3 px-sm-4 px-md-5 py-3 py-sm-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 p-sm-4">
                        <form action="{{ route('conversion-data.store') }}" method="POST" id="conversionForm">
                            @csrf

                            <!-- Customer Information Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-user text-kp-blue me-2"></i>Customer Information
                                    </h5>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="customer_ref" class="form-label">Customer Reference</label>
                                    <input type="text" class="form-control @error('customer_ref') is-invalid @enderror"
                                           id="customer_ref" name="customer_ref"
                                           value="{{ old('customer_ref') }}"
                                           placeholder="e.g., CUST-001">
                                    @error('customer_ref')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Optional internal reference code</small>
                                </div>

                                <!-- Hidden Customer ID field (populated automatically when customer name is selected) -->
                                <input type="hidden" id="customer_id" name="customer_id" value="{{ old('customer_id') }}">

                                <div class="col-md-12 mb-3">
                                    <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                    <select class="form-select @error('customer_name') is-invalid @enderror"
                                            id="customer_name" name="customer_name" required>
                                        <option value="">-- Select Customer Name --</option>
                                        @forelse($customers as $customer)
                                            <option value="{{ $customer->name }}"
                                                    data-id="{{ $customer->id }}"
                                                    data-company="{{ $customer->company_name }}"
                                                    {{ old('customer_name') == $customer->name ? 'selected' : '' }}>
                                                {{ $customer->name }}
                                                @if($customer->company_name)
                                                    ({{ $customer->company_name }})
                                                @endif
                                            </option>
                                        @empty
                                            <option value="" disabled>No customers assigned to you. Please contact administrator.</option>
                                        @endforelse
                                    </select>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($customers->isEmpty())
                                        <small class="text-kp-yellow">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            No customers are currently assigned to your account.
                                            <a href="{{ route('customers.index') }}">Contact administrator</a> to get customers assigned.
                                        </small>
                                    @endif
                                </div>
                            </div>

                            <!-- Link Information Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-network-wired text-kp-green me-2"></i>Link Information
                                    </h5>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="route_name" class="form-label">Route Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('route_name') is-invalid @enderror"
                                           id="route_name" name="route_name"
                                           value="{{ old('route_name') }}" required
                                           placeholder="e.g., Nairobi-Mombasa">
                                    @error('route_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="link_class" class="form-label">Link Class</label>
                                    <select class="form-select @error('link_class') is-invalid @enderror"
                                            id="link_class" name="link_class">
                                        <option value="">Select Class</option>
                                        <option value="PREMIUM" {{ old('link_class') == 'PREMIUM' ? 'selected' : '' }}>PREMIUM</option>
                                        <option value="METRO" {{ old('link_class') == 'METRO' ? 'selected' : '' }}>METRO</option>
                                        <option value="STANDARD" {{ old('link_class') == 'STANDARD' ? 'selected' : '' }}>STANDARD</option>
                                        <option value="NON PREMIUM" {{ old('link_class') == 'NON PREMIUM' ? 'selected' : '' }}>NON PREMIUM</option>
                                        <option value="LEGACY" {{ old('link_class') == 'LEGACY' ? 'selected' : '' }}>LEGACY</option>
                                    </select>
                                    @error('link_class')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="links_name" class="form-label">Link Name(s) <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('links_name') is-invalid @enderror"
                                              id="links_name" name="links_name" rows="2" required
                                              placeholder="Enter link names, separated by commas if multiple">{{ old('links_name') }}</textarea>
                                    @error('links_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">e.g., FibreLink-001, FibreLink-002 or describe the link</small>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="cores_leased" class="form-label">Cores Leased</label>
                                    <input type="number" class="form-control @error('cores_leased') is-invalid @enderror"
                                           id="cores_leased" name="cores_leased"
                                           value="{{ old('cores_leased') }}" min="0"
                                           placeholder="e.g., 2">
                                    @error('cores_leased')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="bandwidth" class="form-label">Bandwidth</label>
                                    <input type="text" class="form-control @error('bandwidth') is-invalid @enderror"
                                           id="bandwidth" name="bandwidth"
                                           value="{{ old('bandwidth') }}"
                                           placeholder="e.g., 10Gbps, 100Gbps">
                                    @error('bandwidth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="distance_km" class="form-label">Distance (KM)</label>
                                    <input type="number" class="form-control @error('distance_km') is-invalid @enderror"
                                           id="distance_km" name="distance_km"
                                           value="{{ old('distance_km') }}" step="0.01" min="0"
                                           placeholder="e.g., 500.50">
                                    @error('distance_km')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="contract_duration_yrs" class="form-label">Contract Duration (Years)</label>
                                    <input type="number" class="form-control @error('contract_duration_yrs') is-invalid @enderror"
                                           id="contract_duration_yrs" name="contract_duration_yrs"
                                           value="{{ old('contract_duration_yrs') }}" min="0"
                                           placeholder="e.g., 5">
                                    @error('contract_duration_yrs')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Pricing Information Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-money-bill-wave text-kp-yellow me-2"></i>Pricing Information
                                    </h5>
                                </div>

                                <!-- Currency Selection -->
                                <div class="col-md-12 mb-3">
                                    <label for="currency" class="form-label">Select Currency <span class="text-danger">*</span></label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="currency" id="currency_usd" value="USD"
                                               {{ old('currency', 'USD') == 'USD' ? 'checked' : '' }} autocomplete="off">
                                        <label class="btn btn-outline-kp-primary" for="currency_usd">
                                            <i class="fas fa-dollar-sign me-1"></i> USD (US Dollar)
                                        </label>

                                        <input type="radio" class="btn-check" name="currency" id="currency_kes" value="KES"
                                               {{ old('currency') == 'KES' ? 'checked' : '' }} autocomplete="off">
                                        <label class="btn btn-outline-kp-primary" for="currency_kes">
                                            <i class="fas fa-shilling-sign me-1"></i> KES (Kenyan Shilling)
                                        </label>
                                    </div>
                                    @error('currency')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- USD Fields Group -->
                                <div id="usd-fields" class="currency-fields">
                                    <div class="col-md-4 mb-3">
                                        <label for="price_per_core_per_km_per_month_usd" class="form-label">Price/Core/KM/Month (USD)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('price_per_core_per_km_per_month_usd') is-invalid @enderror"
                                                   id="price_per_core_per_km_per_month_usd" name="price_per_core_per_km_per_month_usd"
                                                   value="{{ old('price_per_core_per_km_per_month_usd') }}" step="0.01" min="0"
                                                   placeholder="e.g., 15.50">
                                            @error('price_per_core_per_km_per_month_usd')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="monthly_link_value_usd" class="form-label">Monthly Link Value (USD)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('monthly_link_value_usd') is-invalid @enderror"
                                                   id="monthly_link_value_usd" name="monthly_link_value_usd"
                                                   value="{{ old('monthly_link_value_usd') }}" step="0.01" min="0"
                                                   placeholder="e.g., 15500.00">
                                            @error('monthly_link_value_usd')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="total_contract_value_usd" class="form-label">Total Contract Value (USD)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('total_contract_value_usd') is-invalid @enderror"
                                                   id="total_contract_value_usd" name="total_contract_value_usd"
                                                   value="{{ old('total_contract_value_usd') }}" step="0.01" min="0"
                                                   placeholder="e.g., 930000.00">
                                            @error('total_contract_value_usd')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- KES Fields Group -->
                                <div id="kes-fields" class="currency-fields">
                                    <div class="col-md-4 mb-3">
                                        <label for="price_per_core_per_km_per_month_kes" class="form-label">Price/Core/KM/Month (KES)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">KSh</span>
                                            <input type="number" class="form-control @error('price_per_core_per_km_per_month_kes') is-invalid @enderror"
                                                   id="price_per_core_per_km_per_month_kes" name="price_per_core_per_km_per_month_kes"
                                                   value="{{ old('price_per_core_per_km_per_month_kes') }}" step="0.01" min="0"
                                                   placeholder="e.g., 2000.00">
                                            @error('price_per_core_per_km_per_month_kes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="monthly_link_kes" class="form-label">Monthly Link Value (KES)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">KSh</span>
                                            <input type="number" class="form-control @error('monthly_link_kes') is-invalid @enderror"
                                                   id="monthly_link_kes" name="monthly_link_kes"
                                                   value="{{ old('monthly_link_kes') }}" step="0.01" min="0"
                                                   placeholder="e.g., 2015000.00">
                                            @error('monthly_link_kes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="total_contract_value_kes" class="form-label">Total Contract Value (KES)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">KSh</span>
                                            <input type="number" class="form-control @error('total_contract_value_kes') is-invalid @enderror"
                                                   id="total_contract_value_kes" name="total_contract_value_kes"
                                                   value="{{ old('total_contract_value_kes') }}" step="0.01" min="0"
                                                   placeholder="e.g., 120900000.00">
                                            @error('total_contract_value_kes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Exchange Rate Info (visible when USD is selected) -->
                                <div class="col-md-12 mb-3" id="exchange-rate-info" style="display: none;">
                                    <div class="alert alert-secondary small">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Exchange Rate:</strong> 1 USD = 130 KES (for reference only)
                                    </div>
                                </div>
                            </div>

                            <!-- Auto-calculate Button -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <div class="d-flex align-items-center flex-wrap gap-3">
                                            <i class="fas fa-calculator fa-lg me-3"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">Auto-calculate Values</h6>
                                                <p class="mb-0" id="calculation-hint">Fill in cores leased, distance, price per core, and contract duration to auto-calculate financial values.</p>
                                            </div>
                                            <button type="button" class="btn btn-outline-info" id="calculateBtn">
                                                <i class="fas fa-calculator me-1"></i> Calculate Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between flex-wrap gap-2">
                                        <a href="{{ route('conversion-data.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-kp-primary">
                                            <i class="fas fa-save me-1"></i> Save Fibre Link
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .card {
        border: 1px solid #e0e0e0;
        border-radius: 0.5rem;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }

    .alert-info {
        background-color: #e7f1ff;
        border-color: #b3d7ff;
        color: #004085;
    }

    .btn-kp-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 0.5rem 2rem;
    }

    .btn-kp-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4290 100%);
        transform: translateY(-1px);
    }

    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }

    .input-group .form-control {
        border-left: none;
    }

    .input-group .form-control:focus {
        border-left: 1px solid #667eea;
    }

    h5.border-bottom {
        border-bottom: 2px solid #667eea !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    /* Select2 custom styling */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: calc(2.5rem + 2px);
        border-radius: 0.375rem;
    }

    /* Loading spinner */
    .btn-loading {
        pointer-events: none;
        opacity: 0.7;
    }

    .btn-loading i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Currency fields transition */
    .currency-fields {
        transition: all 0.3s ease;
    }

    /* Radio button group styling */
    .btn-group .btn-outline-kp-primary.active,
    .btn-group .btn-check:checked + .btn-outline-kp-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
    }

    .btn-group .btn-outline-kp-primary:hover {
        background: linear-gradient(135deg, #7b8eed 0%, #8b5cb2 100%);
        color: white;
        border-color: #667eea;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for customer name dropdown with search functionality
    $('#customer_name').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Select or search customer name --',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return 'No customers found. Please contact administrator.';
            }
        }
    });

    // When customer name is selected, populate the hidden customer_id field
    $('#customer_name').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var customerId = selectedOption.data('id');

        if (customerId && customerId !== '') {
            $('#customer_id').val(customerId);
            $(this).removeClass('is-invalid');
            $(this).next('.select2-container').removeClass('is-invalid-border');
        } else {
            $('#customer_id').val('');
        }
    });

    // Trigger change on page load if old value exists
    if ($('#customer_name').val()) {
        $('#customer_name').trigger('change');
    }

    // Currency toggle functionality
    function toggleCurrencyFields() {
        const selectedCurrency = $('input[name="currency"]:checked').val();

        if (selectedCurrency === 'USD') {
            $('#usd-fields').fadeIn(200);
            $('#kes-fields').fadeOut(200);
            $('#exchange-rate-info').fadeIn(200);
            $('#calculation-hint').html('Fill in cores leased, distance, <strong>Price/Core/KM/Month (USD)</strong>, and contract duration to auto-calculate USD values.');

            // Enable USD inputs, disable KES inputs
            $('#usd-fields input').prop('disabled', false);
            $('#kes-fields input').prop('disabled', true);

            // Clear KES values when switching
            $('#price_per_core_per_km_per_month_kes, #monthly_link_kes, #total_contract_value_kes').val('');
        } else {
            $('#usd-fields').fadeOut(200);
            $('#kes-fields').fadeIn(200);
            $('#exchange-rate-info').fadeOut(200);
            $('#calculation-hint').html('Fill in cores leased, distance, <strong>Price/Core/KM/Month (KES)</strong>, and contract duration to auto-calculate KES values.');

            // Enable KES inputs, disable USD inputs
            $('#usd-fields input').prop('disabled', true);
            $('#kes-fields input').prop('disabled', false);

            // Clear USD values when switching
            $('#price_per_core_per_km_per_month_usd, #monthly_link_value_usd, #total_contract_value_usd').val('');
        }

        // Trigger recalculation
        calculateValues();
    }

    // Initial toggle
    toggleCurrencyFields();

    // Listen for currency changes
    $('input[name="currency"]').on('change', function() {
        toggleCurrencyFields();
    });

    // Auto-calculate functionality with debounce
    let calculateTimeout;

    function debouncedCalculate() {
        clearTimeout(calculateTimeout);
        calculateTimeout = setTimeout(calculateValues, 300);
    }

    // Watch all relevant inputs
    const usdInputs = ['#cores_leased', '#distance_km', '#price_per_core_per_km_per_month_usd', '#contract_duration_yrs'];
    const kesInputs = ['#cores_leased', '#distance_km', '#price_per_core_per_km_per_month_kes', '#contract_duration_yrs'];

    function setupCalculationListeners() {
        const selectedCurrency = $('input[name="currency"]:checked').val();
        const inputs = selectedCurrency === 'USD' ? usdInputs : kesInputs;

        inputs.forEach(selector => {
            $(selector).off('input change').on('input change', debouncedCalculate);
        });
    }

    // Initial setup
    setupCalculationListeners();

    // Re-setup listeners when currency changes
    $('input[name="currency"]').on('change', function() {
        setupCalculationListeners();
        calculateValues();
    });

    // Manual calculate button
    $('#calculateBtn').on('click', function() {
        calculateValues();
        const btn = $(this);
        btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Calculating...');
        setTimeout(() => {
            btn.html('<i class="fas fa-calculator me-1"></i> Calculate Now');
        }, 500);
    });

    function calculateValues() {
        const cores = parseFloat(document.getElementById('cores_leased').value) || 0;
        const distance = parseFloat(document.getElementById('distance_km').value) || 0;
        const contractYears = parseFloat(document.getElementById('contract_duration_yrs').value) || 0;
        const selectedCurrency = $('input[name="currency"]:checked').val();

        if (selectedCurrency === 'USD') {
            const pricePerCoreKm = parseFloat(document.getElementById('price_per_core_per_km_per_month_usd').value) || 0;

            if (cores > 0 && distance > 0 && pricePerCoreKm > 0) {
                const monthlyUSD = cores * distance * pricePerCoreKm;
                document.getElementById('monthly_link_value_usd').value = monthlyUSD.toFixed(2);

                if (contractYears > 0) {
                    const months = contractYears * 12;
                    const totalUSD = monthlyUSD * months;
                    document.getElementById('total_contract_value_usd').value = totalUSD.toFixed(2);
                } else {
                    document.getElementById('total_contract_value_usd').value = '';
                }
            } else {
                if (!cores || !distance || !pricePerCoreKm) {
                    document.getElementById('monthly_link_value_usd').value = '';
                    document.getElementById('total_contract_value_usd').value = '';
                }
            }
        } else {
            const pricePerCoreKmKes = parseFloat(document.getElementById('price_per_core_per_km_per_month_kes').value) || 0;

            if (cores > 0 && distance > 0 && pricePerCoreKmKes > 0) {
                const monthlyKES = cores * distance * pricePerCoreKmKes;
                document.getElementById('monthly_link_kes').value = monthlyKES.toFixed(2);

                if (contractYears > 0) {
                    const months = contractYears * 12;
                    const totalKES = monthlyKES * months;
                    document.getElementById('total_contract_value_kes').value = totalKES.toFixed(2);
                } else {
                    document.getElementById('total_contract_value_kes').value = '';
                }
            } else {
                if (!cores || !distance || !pricePerCoreKmKes) {
                    document.getElementById('monthly_link_kes').value = '';
                    document.getElementById('total_contract_value_kes').value = '';
                }
            }
        }
    }

    // Form validation with loading state
    const form = document.getElementById('conversionForm');
    const submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function(e) {
        const requiredFields = ['customer_name', 'route_name', 'links_name'];
        let isValid = true;

        // Validate currency is selected
        if (!$('input[name="currency"]:checked').val()) {
            showNotification('Please select a currency (USD or KES)', 'error');
            isValid = false;
        }

        // Validate at least one pricing field has value based on currency
        const selectedCurrency = $('input[name="currency"]:checked').val();
        if (selectedCurrency === 'USD') {
            const monthlyUSD = document.getElementById('monthly_link_value_usd').value;
            if (!monthlyUSD || parseFloat(monthlyUSD) <= 0) {
                showNotification('Please calculate or enter Monthly Link Value (USD)', 'error');
                isValid = false;
            }
        } else if (selectedCurrency === 'KES') {
            const monthlyKES = document.getElementById('monthly_link_kes').value;
            if (!monthlyKES || parseFloat(monthlyKES) <= 0) {
                showNotification('Please calculate or enter Monthly Link Value (KES)', 'error');
                isValid = false;
            }
        }

        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field.value.trim()) {
                if (fieldId === 'customer_name') {
                    $(field).next('.select2-container').addClass('is-invalid-border');
                }
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                if (fieldId === 'customer_name') {
                    $(field).next('.select2-container').removeClass('is-invalid-border');
                }
                field.classList.remove('is-invalid');
            }
        });

        if (!$('#customer_id').val()) {
            $('#customer_name').next('.select2-container').addClass('is-invalid-border');
            $('#customer_name').addClass('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        } else {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
            submitBtn.classList.add('btn-loading');
        }
    });

    function showNotification(message, type = 'error') {
        if (type === 'error') {
            alert(message);
        }
    }

    // Clear validation on input/change
    $('#customer_name').on('change select2:select', function() {
        $(this).removeClass('is-invalid');
        $(this).next('.select2-container').removeClass('is-invalid-border');
    });

    document.querySelectorAll('.form-control, .form-select').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            if (this.id === 'customer_name') {
                $(this).next('.select2-container').removeClass('is-invalid-border');
            }
        });
    });
});
</script>

<style>
    /* Custom class for Select2 validation error */
    .select2-container--bootstrap-5.is-invalid-border + .select2-container--bootstrap-5 .select2-selection,
    .is-invalid-border .select2-selection {
        border-color: #dc3545 !important;
    }
    .select2-container--bootstrap-5.is-invalid-border + .select2-container--bootstrap-5 .select2-selection:focus,
    .is-invalid-border .select2-selection:focus {
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
    }

    /* Smooth transitions */
    .btn-kp-primary, .btn-secondary {
        transition: all 0.3s ease;
    }

    /* Better focus states */
    .form-control:focus, .form-select:focus, .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }

    /* Currency fields fade effect */
    .currency-fields {
        transition: opacity 0.3s ease;
    }
</style>
@endpush
@endsection
