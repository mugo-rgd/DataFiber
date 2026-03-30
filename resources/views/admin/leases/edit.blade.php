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
                                <a href="{{ route('admin.leases.index') }}">Lease Management</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.leases.show', $lease) }}">{{ $lease->lease_number }}</a>
                            </li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.leases.index') }}" class="btn btn-secondary">
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
            <form action="{{ route('admin.leases.update', $lease) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Basic Information</h5>

                        <div class="mb-3">
                            <label for="lease_number" class="form-label">Lease Number *</label>
                            <input type="text" class="form-control" id="lease_number" name="lease_number"
                                   value="{{ old('lease_number', $lease->lease_number) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Customer *</label>
                            <select class="form-select" id="customer_id" name="customer_id" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ old('customer_id', $lease->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="service_type" class="form-label">Service Type *</label>
                            <select class="form-select" id="service_type" name="service_type" required>
                                <option value="dark_fibre" {{ old('service_type', $lease->service_type) == 'dark_fibre' ? 'selected' : '' }}>Dark Fibre</option>
                                <option value="lit_fibre" {{ old('service_type', $lease->service_type) == 'lit_fibre' ? 'selected' : '' }}>Lit Fibre</option>
                                <option value="wavelength" {{ old('service_type', $lease->service_type) == 'wavelength' ? 'selected' : '' }}>Wavelength</option>
                                <option value="ethernet" {{ old('service_type', $lease->service_type) == 'ethernet' ? 'selected' : '' }}>Ethernet</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="draft" {{ old('status', $lease->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="pending" {{ old('status', $lease->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="active" {{ old('status', $lease->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="expired" {{ old('status', $lease->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="terminated" {{ old('status', $lease->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                            </select>
                        </div>
                    </div>

                    <!-- Route Information -->
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Route Information</h5>

                        <div class="mb-3">
                            <label for="start_location" class="form-label">Start Location *</label>
                            <input type="text" class="form-control" id="start_location" name="start_location"
                                   value="{{ old('start_location', $lease->start_location) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="end_location" class="form-label">End Location *</label>
                            <input type="text" class="form-control" id="end_location" name="end_location"
                                   value="{{ old('end_location', $lease->end_location) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="distance_km" class="form-label">Distance (KM)</label>
                            <input type="number" step="0.01" class="form-control" id="distance_km" name="distance_km"
                                   value="{{ old('distance_km', $lease->distance_km) }}">
                        </div>

                        <div class="mb-3">
                            <label for="bandwidth" class="form-label">Bandwidth</label>
                            <input type="text" class="form-control" id="bandwidth" name="bandwidth"
                                   value="{{ old('bandwidth', $lease->bandwidth) }}"
                                   placeholder="e.g., 10Gbps, etc.">
                        </div>


                        <div class="mb-3">
                            <label for="cores_required" class="form-label">Cores</label>
                            <input type="number" class="form-control" id="cores_required" name="cores_required"
                                   value="{{ old('cores_required', $lease->cores_required) }}"
                                   placeholder="e.g. 1, 2, 3, etc.">
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
                                <span class="input-group-text"> </span>
                                <input type="number" step="0.01" class="form-control" id="monthly_cost" name="monthly_cost"
                                       value="{{ old('monthly_cost', $lease->monthly_cost) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="installation_fee" class="form-label">Installation Fee</label>
                            <div class="input-group">
                                <span class="input-group-text"> </span>
                                <input type="number" step="0.01" class="form-control" id="installation_fee" name="installation_fee"
                                       value="{{ old('installation_fee', $lease->installation_fee) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="currency" class="form-label">Currency *</label>
                            <select class="form-select" id="currency" name="currency" required>
                                <option value="USD" {{ old('currency', $lease->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="KSH" {{ old('currency', $lease->currency) == 'KSH' ? 'selected' : '' }}>KSH</option>
                                {{-- <option value="GBP" {{ old('currency', $lease->currency) == 'GBP' ? 'selected' : '' }}>GBP</option>
                                <option value="ZAR" {{ old('currency', $lease->currency) == 'ZAR' ? 'selected' : '' }}>ZAR</option> --}}
                            </select>
                        </div>
                    </div>

                    <!-- Contract Dates -->
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Contract Dates</h5>

                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date *</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                   value="{{ old('start_date', $lease->start_date->format('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date *</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                   value="{{ old('end_date', $lease->end_date->format('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="contract_term_months" class="form-label">Contract Term (Months) *</label>
                            <input type="number" class="form-control" id="contract_term_months" name="contract_term_months"
                                   value="{{ old('contract_term_months', $lease->contract_term_months) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="billing_cycle" class="form-label">Billing Cycle *</label>
                            <select class="form-select" id="billing_cycle" name="billing_cycle" required>
                                <option value="monthly" {{ old('billing_cycle', $lease->billing_cycle) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ old('billing_cycle', $lease->billing_cycle) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="annually" {{ old('billing_cycle', $lease->billing_cycle) == 'annually' ? 'selected' : '' }}>Annually</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <!-- Technical Specifications -->
                    <div class="col-12 mb-4">
                        <h5 class="text-primary mb-3">Technical Specifications</h5>
                        <textarea class="form-control" id="technical_specifications" name="technical_specifications"
                                  rows="4" placeholder="Enter technical specifications...">{{ old('technical_specifications', $lease->technical_specifications) }}</textarea>
                    </div>

                    <!-- Service Level Agreement -->
                    <div class="col-12 mb-4">
                        <h5 class="text-primary mb-3">Service Level Agreement (SLA)</h5>
                        <textarea class="form-control" id="service_level_agreement" name="service_level_agreement"
                                  rows="4" placeholder="Enter service level agreement terms...">{{ old('service_level_agreement', $lease->service_level_agreement) }}</textarea>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="col-12 mb-4">
                        <h5 class="text-primary mb-3">Terms and Conditions</h5>
                        <textarea class="form-control" id="terms_and_conditions" name="terms_and_conditions"
                                  rows="4" placeholder="Enter terms and conditions...">{{ old('terms_and_conditions', $lease->terms_and_conditions) }}</textarea>
                    </div>

                    <!-- Notes -->
                    <div class="col-12 mb-4">
                        <h5 class="text-primary mb-3">Notes</h5>
                        <textarea class="form-control" id="notes" name="notes"
                                  rows="3" placeholder="Enter any additional notes...">{{ old('notes', $lease->notes) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="{{ route('admin.leases.show', $lease) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Lease
                        </button>

                        @if(in_array($lease->status, ['pending', 'draft']))
                            <a href="{{ route('admin.leases.approve', $lease) }}"
                               class="btn btn-success"
                               onclick="event.preventDefault(); document.getElementById('approve-form').submit();">
                                <i class="fas fa-check me-2"></i>Approve & Save
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Hidden form for approve action -->
            @if(in_array($lease->status, ['pending', 'draft']))
                <form id="approve-form" action="{{ route('admin.leases.approve', $lease) }}" method="POST" class="d-none">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="lease_number" value="{{ $lease->lease_number }}">
                    <input type="hidden" name="customer_id" value="{{ $lease->customer_id }}">
                    <input type="hidden" name="service_type" value="{{ $lease->service_type }}">
                    <input type="hidden" name="start_location" value="{{ $lease->start_location }}">
                    <input type="hidden" name="end_location" value="{{ $lease->end_location }}">
                    <input type="hidden" name="monthly_cost" value="{{ $lease->monthly_cost }}">
                    <input type="hidden" name="start_date" value="{{ $lease->start_date->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $lease->end_date->format('Y-m-d') }}">
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
            contractTerm.value = months > 0 ? months : 12;
        }
    }

    startDate.addEventListener('change', calculateTerm);
    endDate.addEventListener('change', calculateTerm);

    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endpush
