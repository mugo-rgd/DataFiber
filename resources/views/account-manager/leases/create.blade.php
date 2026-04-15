@extends('layouts.app')

@section('title', 'Create New Lease')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-file-contract text-primary"></i> Create New Lease
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('account-manager.leases.index') }}">Lease Management</a>
                            </li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('account-manager.leases.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Leases
                </a>
            </div>
        </div>
    </div>

    <!-- Debug Section (Hidden) -->
    <div style="display: none;">
        Customer ID: {{ $customerId ?? 'Not set' }}<br>
        Design Request ID from URL: {{ request('design_request_id') ?? 'Not set' }}
    </div>

    @if($errors->any())
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show">
                <h5 class="alert-heading">
                    <i class="fas fa-exclamation-triangle"></i> Validation Errors
                </h5>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-plus-circle me-2"></i>Lease Information
                    </h6>
                </div>

                <div class="card-body">
                    <form action="{{ route('account-manager.leases.store') }}" method="POST" id="leaseForm">
                        @csrf

                        <!-- Customer Selection -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                @if($customerId && $selectedCustomer)
                                    <div class="mb-3">
                                        <label class="form-label">Customer <span class="text-danger">*</span></label>
                                        <div class="form-control bg-light">
                                            <strong>{{ $selectedCustomer->name }}</strong>
                                            @if($selectedCustomer->company_name)
                                                <br><small class="text-muted">Company: {{ $selectedCustomer->company_name }}</small>
                                            @endif
                                            <br><small class="text-muted">Email: {{ $selectedCustomer->email }}</small>
                                        </div>
                                        <input type="hidden" name="customer_id" value="{{ $customerId }}">
                                        <div class="form-text text-success">
                                            <i class="fas fa-check-circle"></i> Customer automatically assigned.
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle"></i> No customer selected. Please go back and select a customer first.
                                        <div class="mt-2">
                                            <a href="{{ route('account-manager.customers.index') }}" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-arrow-left me-1"></i>Back to Customers
                                            </a>
                                        </div>
                                    </div>
                                @endif
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Only show the rest of the form if customer is selected -->
                        @if($customerId && $selectedCustomer)

                        <!-- Lease Identification -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="lease_number" class="form-label">Lease Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('lease_number') is-invalid @enderror"
                                       id="lease_number" name="lease_number" value="{{ old('lease_number', $leaseNumber) }}" required readonly>
                                <small class="text-muted">Auto-generated lease number</small>
                                @error('lease_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="title" class="form-label">Lease Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       id="title" name="title"
                                       value="{{ old('title', $prefilledTitle ?? '') }}"
                                       placeholder="e.g., Dark Fibre Connection - NYC to DC">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Service Type -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="service_type" class="form-label">Service Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('service_type') is-invalid @enderror" id="service_type" name="service_type" required>
                                    <option value="">Select Service Type</option>
                                    <option value="dark_fibre" {{ old('service_type', 'dark_fibre') == 'dark_fibre' ? 'selected' : '' }}>Dark Fibre IRU/Lease</option>
                                    <option value="colocation" {{ old('service_type') == 'colocation' ? 'selected' : '' }}>Colocation (Dark Fiber IRU/Lease)</option>
                                    <option value="wavelength" {{ old('service_type') == 'wavelength' ? 'selected' : '' }}>Wavelength Service (Lit Service)</option>
                                </select>
                                @error('service_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Technology Type -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="technology" class="form-label">Technology Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('technology') is-invalid @enderror" id="technology" name="technology" required>
                                    <option value="">-- Select Technology --</option>
                                    <option value="metro" {{ old('technology') == 'metro' ? 'selected' : '' }}>METRO</option>
                                    <option value="non_premium" {{ old('technology') == 'non_premium' ? 'selected' : '' }}>NON PREMIUM</option>
                                    <option value="premium" {{ old('technology') == 'premium' ? 'selected' : '' }}>PREMIUM</option>
                                    <option value="colocation" {{ old('technology') == 'colocation' ? 'selected' : '' }}>COLOCATION</option>
                                    <option value="dwdm" {{ old('technology') == 'dwdm' ? 'selected' : '' }}>DWDM (Dense Wavelength Division Multiplexing)</option>
                                </select>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <span id="tech-hint">Select a service type above to see available technologies</span>
                                </small>
                                @error('technology')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Route Information -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="start_location" id="start_location_label" class="form-label">Start Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('start_location') is-invalid @enderror"
                                       id="start_location" name="start_location" value="{{ old('start_location') }}"
                                       placeholder="e.g., Data Center A">
                                @error('start_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="end_location" id="end_location_label" class="form-label">End Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('end_location') is-invalid @enderror"
                                       id="end_location" name="end_location" value="{{ old('end_location') }}"
                                       placeholder="e.g., Data Center B">
                                @error('end_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="host_location" id="host_location_label" class="form-label">Host Location</label>
                                <input type="text" class="form-control @error('host_location') is-invalid @enderror"
                                       id="host_location" name="host_location" value="{{ old('host_location') }}"
                                       placeholder="e.g., Colocation Facility">
                                @error('host_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Route Info -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="distance_km" class="form-label">Distance (KM)</label>
                                <input type="number" step="0.01" class="form-control @error('distance_km') is-invalid @enderror"
                                       id="distance_km" name="distance_km" value="{{ old('distance_km') }}"
                                       placeholder="0.00">
                                @error('distance_km')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="bandwidth" class="form-label">Bandwidth</label>
                                <input type="text" class="form-control @error('bandwidth') is-invalid @enderror"
                                       id="bandwidth" name="bandwidth" value="{{ old('bandwidth') }}"
                                       placeholder="e.g., 10Gbps, 1RU">
                                @error('bandwidth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="cores_required" class="form-label">Core(s)</label>
                                <input type="number" class="form-control @error('cores_required') is-invalid @enderror"
                                       id="cores_required" name="cores_required" value="{{ old('cores_required') }}"
                                       placeholder="e.g., 2" min="0">
                                @error('cores_required')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contract Details -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="contract_term_months" class="form-label">Contract Term (Months) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('contract_term_months') is-invalid @enderror"
                                       id="contract_term_months" name="contract_term_months" value="{{ old('contract_term_months', 12) }}" required
                                       min="1" placeholder="12">
                                @error('contract_term_months')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                       id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" required readonly>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                    <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="KSH" {{ old('currency') == 'KSH' ? 'selected' : '' }}>KSH</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Billing and Pricing -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="billing_cycle" class="form-label">Billing Cycle <span class="text-danger">*</span></label>
                                <select class="form-select @error('billing_cycle') is-invalid @enderror" id="billing_cycle" name="billing_cycle" required>
                                    <option value="monthly" {{ old('billing_cycle', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('billing_cycle') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                    <option value="annually" {{ old('billing_cycle') == 'annually' ? 'selected' : '' }}>Annually</option>
                                    <option value="one_time" {{ old('billing_cycle') == 'one_time' ? 'selected' : '' }}>One Time</option>
                                </select>
                                @error('billing_cycle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="monthly_cost" class="form-label">Monthly Cost <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('monthly_cost') is-invalid @enderror"
                                       id="monthly_cost" name="monthly_cost" value="{{ old('monthly_cost') }}" required
                                       placeholder="0.00">
                                <small class="text-muted" id="costHelpText">Monthly recurring charge</small>
                                @error('monthly_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="installation_fee" class="form-label">Installation Fee</label>
                                <input type="number" step="0.01" class="form-control @error('installation_fee') is-invalid @enderror"
                                       id="installation_fee" name="installation_fee" value="{{ old('installation_fee', 0) }}"
                                       placeholder="0.00">
                                @error('installation_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="total_contract_value" class="form-label">Total Contract Value</label>
                                <input type="number" step="0.01" class="form-control @error('total_contract_value') is-invalid @enderror"
                                       id="total_contract_value" name="total_contract_value" value="{{ old('total_contract_value') }}"
                                       placeholder="0.00" readonly>
                                @error('total_contract_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="terminated" {{ old('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Technical Specifications with Preloaded Content -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="technical_specifications" class="form-label">Technical Specifications</label>
                                <textarea class="form-control @error('technical_specifications') is-invalid @enderror"
                                          id="technical_specifications" name="technical_specifications" rows="6"
                                          placeholder="Enter technical specifications...">{{ old('technical_specifications', "• Fibre Type: ITU-T G.652.D Single Mode Fibre
• Wavelength: 1310nm / 1550nm
• Maximum Distance: 80km without amplification
• Connector Type: APC/PC as required
• Insertion Loss: ≤ 0.3dB per connector pair
• Return Loss: ≥ 55dB (APC), ≥ 40dB (PC)
• Operating Temperature: -40°C to +75°C
• Cable Construction: Loose tube, gel-filled, double-jacketed
• Installation: Underground duct or aerial as specified
• Testing: OTDR testing with results provided
• Splice Points: All splice points documented with loss measurements
• Documentation: As-built drawings and fibre characterization report") }}</textarea>
                                @error('technical_specifications')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Service Level Agreement with Preloaded Content -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="service_level_agreement" class="form-label">Service Level Agreement (SLA)</label>
                                <textarea class="form-control @error('service_level_agreement') is-invalid @enderror"
                                          id="service_level_agreement" name="service_level_agreement" rows="6"
                                          placeholder="Enter SLA terms...">{{ old('service_level_agreement', "• Service Availability: 99.99% monthly uptime guarantee
• Mean Time To Repair (MTTR): 4 hours for critical faults
• Emergency Response: 24/7/365 network operations center
• Scheduled Maintenance: 30 days advance notice for planned maintenance
• Performance Monitoring: Continuous link monitoring and alerting
• Fault Reporting: Dedicated hotline and online portal for fault reporting
• Credit Policy: Service credits for SLA violations as per agreement
• Escalation Procedure: Defined escalation path for unresolved issues
• Response Times:
  - Critical (Service Down): 15 minutes
  - High (Major Impact): 1 hour
  - Normal (Minor Impact): 4 hours
  - Low (Information): 24 hours") }}</textarea>
                                @error('service_level_agreement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Terms and Conditions with Preloaded Content -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="terms_and_conditions" class="form-label">Terms & Conditions</label>
                                <textarea class="form-control @error('terms_and_conditions') is-invalid @enderror"
                                          id="terms_and_conditions" name="terms_and_conditions" rows="8"
                                          placeholder="Enter lease terms and conditions...">{{ old('terms_and_conditions', "1. TERM: This agreement shall commence on the start date and continue for the contract term specified. Either party may terminate this agreement with 30 days written notice upon expiration of the initial term.

2. PAYMENT: Monthly invoices payable within 30 days of invoice date. Late payments subject to 1.5% monthly interest. Invoices shall be delivered via email to customer's designated billing contact.

3. TAXES: Customer is responsible for all applicable taxes, fees, and duties associated with the services provided.

4. TERMINATION: Either party may terminate for material breach with 30 days written notice. Upon termination, customer shall return all equipment and pay all outstanding amounts.

5. CONFIDENTIALITY: Both parties agree to maintain confidentiality of proprietary information, including pricing, network topology, and customer data.

6. LIMITATION OF LIABILITY: Liability limited to direct damages, excluding consequential damages, not exceeding total amounts paid in prior 12 months.

7. FORCE MAJEURE: Neither party liable for delays due to circumstances beyond reasonable control including natural disasters, war, strikes, or government actions.

8. GOVERNING LAW: This agreement shall be governed by the laws of the jurisdiction specified in the service order.

9. INSURANCE: Service provider maintains appropriate insurance coverage including general liability and professional indemnity.

10. ACCESS: Customer shall provide reasonable access to premises for installation, maintenance, and repair of services.

11. ASSIGNMENT: Neither party may assign this agreement without prior written consent, except to affiliates or in merger/acquisition.

12. ENTIRE AGREEMENT: This document constitutes the entire agreement between parties, superseding all prior negotiations and understandings.") }}</textarea>
                                @error('terms_and_conditions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Special Requirements -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="special_requirements" class="form-label">Special Requirements</label>
                                <textarea class="form-control @error('special_requirements') is-invalid @enderror"
                                          id="special_requirements" name="special_requirements" rows="3"
                                          placeholder="Any special requirements or notes...">{{ old('special_requirements') }}</textarea>
                                @error('special_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                          id="notes" name="notes" rows="2"
                                          placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('account-manager.leases.index', ['customer_id' => $customerId]) }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Create Lease
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle me-2"></i>Lease Information
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        <strong>Service Types:</strong>
                        <ul class="small">
                            <li><strong>Dark Fibre IRU/Lease</strong> - Requires Start/End Locations</li>
                            <li><strong>Colocation</strong> - Requires Host Location</li>
                            <li><strong>Wavelength Service</strong> - No location requirements</li>
                        </ul>
                    </p>
                    <p class="small text-muted">
                        <strong>Auto-Calculation:</strong> Total Contract Value = (Recurring Cost × Term) + Installation Fee
                    </p>
                    <p class="small text-muted">
                        <strong>Note:</strong> All fields marked with <span class="text-danger">*</span> are required.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // =============================================
    // Service Type to Technology Type Mapping & Field Disabling
    // =============================================
    const serviceTypeSelect = document.getElementById('service_type');
    const technologySelect = document.getElementById('technology');
    const techHint = document.getElementById('tech-hint');

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

    // Get billing cycle and cost elements
    const billingCycleSelect = document.getElementById('billing_cycle');
    const costLabel = document.querySelector('label[for="monthly_cost"]');
    const costInput = document.getElementById('monthly_cost');
    const costHelpText = document.getElementById('costHelpText');

    function updateCostLabel() {
        if (!billingCycleSelect) return;

        const selectedCycle = billingCycleSelect.value;
        let labelText = '';
        let placeholderText = '';
        let helpText = '';

        switch(selectedCycle) {
            case 'monthly':
                labelText = 'Monthly Cost';
                placeholderText = '0.00 (per month)';
                helpText = 'Monthly recurring charge';
                break;
            case 'quarterly':
                labelText = 'Quarterly Cost';
                placeholderText = '0.00 (per quarter)';
                helpText = 'Quarterly recurring charge (every 3 months)';
                break;
            case 'annually':
                labelText = 'Annual Cost';
                placeholderText = '0.00 (per year)';
                helpText = 'Annual recurring charge (once per year)';
                break;
            case 'one_time':
                labelText = 'One-Time Cost';
                placeholderText = '0.00 (one-time fee)';
                helpText = 'One-time setup or installation fee';
                break;
            default:
                labelText = 'Cost';
                placeholderText = '0.00';
                helpText = 'Enter the cost amount';
        }

        if (costLabel) costLabel.innerHTML = `${labelText} <span class="text-danger">*</span>`;
        if (costInput) costInput.placeholder = placeholderText;
        if (costHelpText) costHelpText.textContent = helpText;

        calculateTotalContractValue();
    }

    function updateTechnologyByServiceType() {
    if (!serviceTypeSelect || !technologySelect) return;

    const serviceType = serviceTypeSelect.value;

    // Remove any existing hidden input
    const existingHidden = document.getElementById('hidden_technology_input');
    if (existingHidden) existingHidden.remove();

    // Clear current options
    technologySelect.innerHTML = '';

    if (serviceType === 'colocation') {
        const option = document.createElement('option');
        option.value = 'colocation';
        option.textContent = 'COLOCATION';
        option.selected = true;
        technologySelect.appendChild(option);
        technologySelect.disabled = true;
        technologySelect.required = false;  // Remove required
        if (techHint) {
            techHint.innerHTML = 'COLOCATION: Physical space, power, and cooling for optical equipment';
        }
        // Add hidden input
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'technology';
        hidden.id = 'hidden_technology_input';
        hidden.value = 'colocation';
        technologySelect.parentNode.appendChild(hidden);

    } else if (serviceType === 'wavelength') {
        const option = document.createElement('option');
        option.value = 'dwdm';
        option.textContent = 'DWDM (Dense Wavelength Division Multiplexing)';
        option.selected = true;
        technologySelect.appendChild(option);
        technologySelect.disabled = true;
        technologySelect.required = false;  // Remove required
        if (techHint) {
            techHint.innerHTML = 'DWDM: Dense Wavelength Division Multiplexing for high-capacity lit service';
        }
        // Add hidden input
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'technology';
        hidden.id = 'hidden_technology_input';
        hidden.value = 'dwdm';
        technologySelect.parentNode.appendChild(hidden);

    } else if (serviceType === 'dark_fibre') {
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

        technologySelect.disabled = false;
        technologySelect.required = true;  // Add required back
        if (techHint) {
            techHint.innerHTML = 'Select one: METRO (urban/short distance), NON PREMIUM (standard service), or PREMIUM (high-priority service)';
        }

    } else {
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = '-- Select Technology --';
        placeholder.disabled = true;
        placeholder.selected = true;
        technologySelect.appendChild(placeholder);
        technologySelect.disabled = false;
        technologySelect.required = true;  // Add required back
        if (techHint) {
            techHint.innerHTML = 'Select a service type above to see available technologies';
        }
    }

        // =============================================
        // FIELD DISABLING BASED ON SERVICE TYPE
        // =============================================

        // Reset all fields to enabled first
        if (startLocation) startLocation.disabled = false;
        if (endLocation) endLocation.disabled = false;
        if (hostLocation) hostLocation.disabled = false;
        if (distanceKm) distanceKm.disabled = false;
        if (bandwidth) bandwidth.disabled = false;
        if (coresRequired) coresRequired.disabled = false;

        // Reset required labels
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
                // Disable: Core(s), Distance (KM), Host Location
                // Start Location and End Location remain ENABLED
                if (coresRequired) coresRequired.disabled = true;
                if (distanceKm) distanceKm.disabled = true;
                if (hostLocation) hostLocation.disabled = true;
                // No required fields for wavelength
                break;

            default:
                // No fields disabled
                break;
        }

        // Add visual feedback for disabled fields
        const allFields = [startLocation, endLocation, hostLocation, distanceKm, bandwidth, coresRequired];
        allFields.forEach(field => {
            if (field) {
                if (field.disabled) {
                    field.classList.add('bg-light');
                    field.required = false;
                } else {
                    field.classList.remove('bg-light');
                }
            }
        });
    }

    function calculateEndDate() {
        const startDateField = document.getElementById('start_date');
        const contractTermField = document.getElementById('contract_term_months');
        const endDateField = document.getElementById('end_date');

        if (!startDateField || !contractTermField || !endDateField) return;

        try {
            const startDate = new Date(startDateField.value);
            const months = parseInt(contractTermField.value) || 0;

            if (months > 0 && startDateField.value) {
                const endDate = new Date(startDate);
                endDate.setMonth(startDate.getMonth() + months);
                const year = endDate.getFullYear();
                const month = String(endDate.getMonth() + 1).padStart(2, '0');
                const day = String(endDate.getDate()).padStart(2, '0');
                endDateField.value = `${year}-${month}-${day}`;
            }
        } catch (error) {
            console.error('Error calculating end date:', error);
        }
    }

    function calculateTotalContractValue() {
        const costField = document.getElementById('monthly_cost');
        const contractTermField = document.getElementById('contract_term_months');
        const billingCycleField = document.getElementById('billing_cycle');
        const installationFeeField = document.getElementById('installation_fee');
        const totalValueField = document.getElementById('total_contract_value');

        if (!costField || !contractTermField || !billingCycleField || !totalValueField) return;

        try {
            let costAmount = parseFloat(costField.value) || 0;
            const termMonths = parseInt(contractTermField.value) || 0;
            const billingCycle = billingCycleField.value;
            const installationFee = parseFloat(installationFeeField?.value) || 0;

            let totalRecurringCost = 0;

            switch (billingCycle) {
                case 'monthly':
                    totalRecurringCost = costAmount * termMonths;
                    break;
                case 'quarterly':
                    totalRecurringCost = costAmount * Math.ceil(termMonths / 3);
                    break;
                case 'annually':
                    totalRecurringCost = costAmount * Math.ceil(termMonths / 12);
                    break;
                case 'one_time':
                    totalRecurringCost = costAmount;
                    break;
                default:
                    totalRecurringCost = costAmount * termMonths;
            }

            const totalValue = totalRecurringCost + installationFee;
            totalValueField.value = totalValue.toFixed(2);
        } catch (error) {
            console.error('Error calculating total contract value:', error);
        }
    }

    // Set default start date to today if empty
    const startDateField = document.getElementById('start_date');
    if (startDateField && !startDateField.value) {
        const today = new Date().toISOString().split('T')[0];
        startDateField.value = today;
    }

    // Initialize
    if (serviceTypeSelect) {
        serviceTypeSelect.addEventListener('change', updateTechnologyByServiceType);
        updateTechnologyByServiceType();
    }

    if (billingCycleSelect) {
        updateCostLabel();
        billingCycleSelect.addEventListener('change', updateCostLabel);
        billingCycleSelect.addEventListener('change', calculateTotalContractValue);
    }

    // Event Listeners for calculations
    const contractTermField = document.getElementById('contract_term_months');
    if (contractTermField) {
        contractTermField.addEventListener('input', function() {
            calculateEndDate();
            calculateTotalContractValue();
        });
    }

    if (costInput) {
        costInput.addEventListener('input', calculateTotalContractValue);
    }

    const installationFeeField = document.getElementById('installation_fee');
    if (installationFeeField) {
        installationFeeField.addEventListener('input', calculateTotalContractValue);
    }

    if (startDateField) {
        startDateField.addEventListener('change', calculateEndDate);
    }

    // Initialize calculations
    calculateEndDate();
    calculateTotalContractValue();
});
</script>
@endpush
