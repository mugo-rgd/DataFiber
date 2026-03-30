@extends('layouts.app')

@section('title', 'Edit Fibre Link - Dark Fibre CRM')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="dashboard-header bg-gradient-warning text-white py-2 py-sm-3 py-md-4">
        <div class="container-fluid px-3 px-sm-4 px-md-5">
            <div class="row align-items-center">
                <div class="col-12 col-lg-8 mb-2 mb-lg-0">
                    <div class="d-flex align-items-center">
                        <div class="header-icon me-2 me-sm-3">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h1 class="mb-1">Edit Fibre Link</h1>
                            <p class="mb-0 opacity-75">Update fibre link conversion data for ID: {{ $item->id }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-lg-end">
                        <a href="{{ route('conversion-data.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                        <a href="{{ route('conversion-data.show', $item->id) }}" class="btn btn-outline-light">
                            <i class="fas fa-eye me-1"></i> View Details
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
                        <form action="{{ route('conversion-data.update', $item->id) }}" method="POST" id="conversionForm">
                            @csrf
                            @method('PUT')

                            <!-- Customer Information Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-user text-primary me-2"></i>Customer Information
                                    </h5>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="customer_ref" class="form-label">Customer Reference</label>
                                    <input type="text" class="form-control @error('customer_ref') is-invalid @enderror"
                                           id="customer_ref" name="customer_ref"
                                           value="{{ old('customer_ref', $item->customer_ref) }}"
                                           placeholder="e.g., CUST-001">
                                    @error('customer_ref')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Optional internal reference code</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="customer_id" class="form-label">Customer ID</label>
                                    <input type="text" class="form-control @error('customer_id') is-invalid @enderror"
                                           id="customer_id" name="customer_id"
                                           value="{{ old('customer_id', $item->customer_id) }}"
                                           placeholder="e.g., 12345">
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                           id="customer_name" name="customer_name"
                                           value="{{ old('customer_name', $item->customer_name) }}" required
                                           placeholder="Enter full customer name">
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Link Information Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-network-wired text-success me-2"></i>Link Information
                                    </h5>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="route_name" class="form-label">Route Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('route_name') is-invalid @enderror"
                                           id="route_name" name="route_name"
                                           value="{{ old('route_name', $item->route_name) }}" required
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
                                        <option value="PREMIUM" {{ old('link_class', $item->link_class) == 'PREMIUM' ? 'selected' : '' }}>PREMIUM</option>
                                        <option value="METRO" {{ old('link_class', $item->link_class) == 'METRO' ? 'selected' : '' }}>METRO</option>
                                        <option value="STANDARD" {{ old('link_class', $item->link_class) == 'STANDARD' ? 'selected' : '' }}>STANDARD</option>
                                        <option value="BASIC" {{ old('link_class', $item->link_class) == 'BASIC' ? 'selected' : '' }}>BASIC</option>
                                        <option value="LEGACY" {{ old('link_class', $item->link_class) == 'LEGACY' ? 'selected' : '' }}>LEGACY</option>
                                        <option value="NON PREMIUM" {{ old('link_class', $item->link_class) == 'NON PREMIUM' ? 'selected' : '' }}>NON PREMIUM</option>
                                    </select>
                                    @error('link_class')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="links_name" class="form-label">Link Name(s) <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('links_name') is-invalid @enderror"
                                              id="links_name" name="links_name" rows="2" required
                                              placeholder="Enter link names, separated by commas if multiple">{{ old('links_name', $item->links_name) }}</textarea>
                                    @error('links_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">e.g., FibreLink-001, FibreLink-002 or describe the link</small>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="cores_leased" class="form-label">Cores Leased</label>
                                    <input type="number" class="form-control @error('cores_leased') is-invalid @enderror"
                                           id="cores_leased" name="cores_leased"
                                           value="{{ old('cores_leased', $item->cores_leased) }}" min="0"
                                           placeholder="e.g., 2">
                                    @error('cores_leased')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="bandwidth" class="form-label">Bandwidth</label>
                                    <input type="text" class="form-control @error('bandwidth') is-invalid @enderror"
                                           id="bandwidth" name="bandwidth"
                                           value="{{ old('bandwidth', $item->bandwidth) }}"
                                           placeholder="e.g., 10Gbps, 100Gbps">
                                    @error('bandwidth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="distance_km" class="form-label">Distance (KM)</label>
                                    <input type="number" class="form-control @error('distance_km') is-invalid @enderror"
                                           id="distance_km" name="distance_km"
                                           value="{{ old('distance_km', $item->distance_km) }}" step="0.01" min="0"
                                           placeholder="e.g., 500.50">
                                    @error('distance_km')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="contract_duration_yrs" class="form-label">Contract Duration (Years)</label>
                                    <input type="number" class="form-control @error('contract_duration_yrs') is-invalid @enderror"
                                           id="contract_duration_yrs" name="contract_duration_yrs"
                                           value="{{ old('contract_duration_yrs', $item->contract_duration_yrs) }}" min="0"
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
                                        <i class="fas fa-money-bill-wave text-warning me-2"></i>Pricing Information
                                    </h5>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="price_per_core_per_km_per_month_usd" class="form-label">Price/Core/KM/Month (USD)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('price_per_core_per_km_per_month_usd') is-invalid @enderror"
                                               id="price_per_core_per_km_per_month_usd" name="price_per_core_per_km_per_month_usd"
                                               value="{{ old('price_per_core_per_km_per_month_usd', $item->price_per_core_per_km_per_month_usd) }}" step="0.01" min="0"
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
                                               value="{{ old('monthly_link_value_usd', $item->monthly_link_value_usd) }}" step="0.01" min="0"
                                               placeholder="e.g., 15500.00">
                                        @error('monthly_link_value_usd')
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
                                               value="{{ old('monthly_link_kes', $item->monthly_link_kes) }}" step="0.01" min="0"
                                               placeholder="e.g., 2015000.00">
                                        @error('monthly_link_kes')
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
                                               value="{{ old('total_contract_value_usd', $item->total_contract_value_usd) }}" step="0.01" min="0"
                                               placeholder="e.g., 930000.00">
                                        @error('total_contract_value_usd')
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
                                               value="{{ old('total_contract_value_kes', $item->total_contract_value_kes) }}" step="0.01" min="0"
                                               placeholder="e.g., 120900000.00">
                                        @error('total_contract_value_kes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Auto-calculate Button -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calculator fa-lg me-3"></i>
                                            <div>
                                                <h6 class="mb-1">Auto-calculate Values</h6>
                                                <p class="mb-0">Fill in the basic fields and let the system calculate the financial values automatically.</p>
                                            </div>
                                            <button type="button" class="btn btn-outline-info ms-auto" id="calculateBtn">
                                                <i class="fas fa-calculator me-1"></i> Calculate Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Audit Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-secondary">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small class="text-muted">Created: {{ $item->created_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                            <div class="col-md-6 text-md-end">
                                                <small class="text-muted">Last Updated: {{ $item->updated_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <a href="{{ route('conversion-data.index') }}" class="btn btn-secondary me-2">
                                                <i class="fas fa-times me-1"></i> Cancel
                                            </a>
                                            <button type="button" class="btn btn-outline-danger"
                                                    onclick="if(confirm('Are you sure you want to delete this item?')) { document.getElementById('deleteForm').submit(); }">
                                                <i class="fas fa-trash me-1"></i> Delete
                                            </button>
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i> Update Fibre Link
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Delete Form -->
                        <form id="deleteForm" action="{{ route('conversion-data.destroy', $item->id) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate functionality
    document.getElementById('calculateBtn').addEventListener('click', function() {
        calculateValues();
    });

    // Auto-calculate when certain fields change
    document.getElementById('cores_leased').addEventListener('change', calculateValues);
    document.getElementById('distance_km').addEventListener('change', calculateValues);
    document.getElementById('price_per_core_per_km_per_month_usd').addEventListener('change', calculateValues);
    document.getElementById('contract_duration_yrs').addEventListener('change', calculateValues);

    function calculateValues() {
        const cores = parseFloat(document.getElementById('cores_leased').value) || 0;
        const distance = parseFloat(document.getElementById('distance_km').value) || 0;
        const pricePerCoreKm = parseFloat(document.getElementById('price_per_core_per_km_per_month_usd').value) || 0;
        const contractYears = parseFloat(document.getElementById('contract_duration_yrs').value) || 0;

        // Calculate monthly USD value
        if (cores > 0 && distance > 0 && pricePerCoreKm > 0) {
            const monthlyUSD = cores * distance * pricePerCoreKm;
            document.getElementById('monthly_link_value_usd').value = monthlyUSD.toFixed(2);

            // Calculate KES value (assuming 1 USD = 130 KES)
            const exchangeRate = 130;
            const monthlyKES = monthlyUSD * exchangeRate;
            document.getElementById('monthly_link_kes').value = monthlyKES.toFixed(2);

            // Calculate total contract values
            if (contractYears > 0) {
                const months = contractYears * 12;
                const totalUSD = monthlyUSD * months;
                const totalKES = monthlyKES * months;

                document.getElementById('total_contract_value_usd').value = totalUSD.toFixed(2);
                document.getElementById('total_contract_value_kes').value = totalKES.toFixed(2);
            }
        }
    }

    // Form validation
    document.getElementById('conversionForm').addEventListener('submit', function(e) {
        const requiredFields = ['customer_name', 'route_name', 'links_name'];
        let isValid = true;

        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields marked with *');
        }
    });

    // Clear validation on input
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });

    // Show current values for reference
    console.log('Editing item ID:', {{ $item->id }});
});
</script>
@endpush

<style>
.dashboard-header {
    background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
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
    border-color: #f6d365;
    box-shadow: 0 0 0 0.25rem rgba(246, 211, 101, 0.25);
}

.alert-info {
    background-color: #e7f1ff;
    border-color: #b3d7ff;
    color: #004085;
}

.alert-secondary {
    background-color: #f8f9fa;
    border-color: #e9ecef;
}

.btn-primary {
    background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
    border: none;
    color: #212529;
    padding: 0.5rem 2rem;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #f4c152 0%, #fc8c66 100%);
    transform: translateY(-1px);
    color: #212529;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    color: white;
}

.input-group-text {
    background-color: #f8f9fa;
    border-right: none;
}

.input-group .form-control {
    border-left: none;
}

.input-group .form-control:focus {
    border-left: 1px solid #f6d365;
}

h5.border-bottom {
    border-bottom: 2px solid #f6d365 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.badge {
    font-size: 0.75em;
}
</style>
@endsection
