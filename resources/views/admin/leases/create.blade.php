<!-- Debug: Check what variables are available -->
<div style="display: none;">
    Customer ID: {{ $customerId ?? 'Not set' }}<br>
    Design Request ID from URL: {{ request('design_request_id') ?? 'Not set' }}<br>
    Design Request Title from URL: {{ request('design_request_title') ?? 'Not set' }}<br>
    Request Number: {{ $requestNumber ?? 'Not set' }}<br>
    Design Request ID variable: {{ $designRequestId ?? 'Not set' }}
</div>

@extends('layouts.app')

@section('title', 'Create New Lease')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-file-contract text-primary"></i> Create New Lease
            </h1>
            <p class="text-muted">Create a new dark fibre lease agreement</p>
        </div>
    </div>

    <!-- Debug Section -->
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

                        <!-- Customer and Quotation Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6">
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
                                            <i class="fas fa-check-circle"></i> Customer automatically assigned from customer list.
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

                            <div class="col-md-6">
                                <label for="quotation_id" class="form-label">Related Quotation</label>

                                @if($approvedQuotation)
                                {{-- Show auto-selected approved quotation --}}
                                <div class="form-control bg-light">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <strong>Quotation #{{ $approvedQuotation->quotation_number }}</strong>
                                    <span class="text-muted">- Approved</span>
                                    <br>
                                    <small class="text-muted">Amount: ${{ number_format($approvedQuotation->total_amount, 2) }}</small>
                                    @if($designRequest)
                                        <br>
                                        <small class="text-muted">For: {{ $designRequest->title }}</small>
                                    @endif
                                </div>
                                <input type="hidden" name="quotation_id" value="{{ $approvedQuotation->id }}">
                                <small class="text-success mt-1">
                                    <i class="fas fa-check-circle"></i> Approved quotation auto-loaded
                                </small>

                                {{-- Hidden div with quotation data for JavaScript --}}
                                <div id="auto-selected-quotation-data"
                                     data-id="{{ $approvedQuotation->id }}"
                                     data-quotation-number="{{ $approvedQuotation->quotation_number }}"
                                     data-total-amount="{{ $approvedQuotation->total_amount }}"
                                     data-design-request-title="{{ $designRequest->title ?? 'Untitled' }}"
                                     data-scope-of-work="{{ $approvedQuotation->scope_of_work }}"
                                     data-terms-and-conditions="{{ $approvedQuotation->terms_and_conditions }}">
                                </div>

                                {{-- Hidden div with design item data for JavaScript --}}
                                @if($designItems->isNotEmpty())
                                    @php
                                        // Get the first design item (or aggregate if multiple)
                                        $designItem = $designItems->first();
                                        $totalCores = $designItems->sum('cores_required');
                                        $totalDistance = $designItems->sum('distance');
                                        // Get the most common technology type
                                        $technologyType = $designItems->countBy('technology_type')->sortDesc()->keys()->first();
                                    @endphp

                                    <div id="design-item-data"
                                         data-cores-required="{{ $totalCores }}"
                                         data-distance="{{ $totalDistance }}"
                                         data-technology-type="{{ $technologyType }}"
                                         data-unit-cost="{{ $designItem->unit_cost ?? 0 }}"
                                         data-terms="{{ $designItem->terms ?? 12 }}"
                                         data-link-class="{{ $designItem->link_class ?? '' }}"
                                         data-route-name="{{ $designItem->route_name ?? '' }}"
                                         data-tax-rate="{{ $designItem->tax_rate ?? 0.16 }}">
                                    </div>
                                @endif
                            @else
                                {{-- Show dropdown if no approved quotation found --}}
                                <select class="form-select @error('quotation_id') is-invalid @enderror" id="quotation_id" name="quotation_id">
                                    <option value="">Select Approved Quotation (Optional)</option>
                                    @foreach($quotations as $quotation)
                                        <option value="{{ $quotation->id }}"
                                            {{ $designRequest && $quotation->design_request_id == $designRequest->id ? 'selected' : '' }}
                                            data-quotation-data="{{ json_encode([
                                                'id' => $quotation->id,
                                                'quotation_number' => $quotation->quotation_number,
                                                'total_amount' => $quotation->total_amount,
                                                'scope_of_work' => $quotation->scope_of_work,
                                                'terms_and_conditions' => $quotation->terms_and_conditions,
                                                'customer_notes' => $quotation->customer_notes ?? '',
                                                'line_items' => $quotation->line_items ?? []
                                            ]) }}">
                                            Quotation #{{ $quotation->quotation_number }} - ${{ number_format($quotation->total_amount, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Select an approved quotation to auto-fill lease details</small>

                                @if($designRequest && $quotations->isEmpty())
                                    <div class="alert alert-warning mt-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        No approved quotations found for this design request
                                    </div>
                                @endif
                            @endif

                                @error('quotation_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Quotation Details Preview -->
                        <div class="row mb-3 d-none" id="quotation-details">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-file-invoice-dollar me-2"></i>Selected Quotation Details
                                    </h6>
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <strong>Quotation #:</strong> <span id="quotation-number">-</span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Total Amount:</strong> $<span id="quotation-amount">0.00</span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Design Request:</strong> <span id="quotation-request">-</span>
                                        </div>
                                    </div>
                                </div>
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
                        </div>

                        <div class="row mb-3">
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

                        <!-- Service Details - UPDATED -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="service_type" class="form-label">Service Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('service_type') is-invalid @enderror" id="service_type" name="service_type" required>
                                    <option value="">Select Service Type</option>
                                    <option value="dark_fibre" {{ old('service_type', 'dark_fibre') == 'dark_fibre' ? 'selected' : '' }}>Dark Fibre</option>
                                    <option value="wavelength" {{ old('service_type') == 'wavelength' ? 'selected' : '' }}>Wavelength</option>
                                    <option value="ethernet" {{ old('service_type') == 'ethernet' ? 'selected' : '' }}>Ethernet</option>
                                    <option value="ip_transit" {{ old('service_type') == 'ip_transit' ? 'selected' : '' }}>IP Transit</option>
                                    <option value="colocation" {{ old('service_type') == 'colocation' ? 'selected' : '' }}>Colocation</option>
                                </select>
                                @error('service_type')
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
                                <label for="cores_required" class="form-label">Cores Required</label>
                                <input type="number" class="form-control @error('cores_required') is-invalid @enderror"
                                       id="cores_required" name="cores_required" value="{{ old('cores_required') }}"
                                       placeholder="e.g., 2" min="0">
                                @error('cores_required')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Technology Field - UPDATED -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="technology" class="form-label">Technology</label>
                                <select class="form-select @error('technology') is-invalid @enderror" id="technology" name="technology">
                                    <option value="">Select Technology</option>
                                    <option value="single_mode" {{ old('technology') == 'single_mode' ? 'selected' : '' }}>Single Mode</option>
                                    <option value="multimode" {{ old('technology') == 'multimode' ? 'selected' : '' }}>Multimode</option>
                                    <option value="dwdm" {{ old('technology') == 'dwdm' ? 'selected' : '' }}>DWDM</option>
                                    <option value="cwdm" {{ old('technology') == 'cwdm' ? 'selected' : '' }}>CWDM</option>
                                    <option value="ADSS" {{ old('technology') == 'ADSS' ? 'selected' : '' }}>ADSS (All-Dielectric Self-Supporting)</option>
                                    <option value="OPGW" {{ old('technology') == 'OPGW' ? 'selected' : '' }}>OPGW (Optical Ground Wire)</option>
                                    <option value="other" {{ old('technology') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('technology')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Route Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_location" class="form-label">Start Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('start_location') is-invalid @enderror"
                                       id="start_location" name="start_location" value="{{ old('start_location') }}" required
                                       placeholder="e.g., Data Center A">
                                @error('start_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_location" class="form-label">End Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('end_location') is-invalid @enderror"
                                       id="end_location" name="end_location" value="{{ old('end_location') }}" required
                                       placeholder="e.g., Data Center B">
                                @error('end_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Distance and Contract Term -->
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
                                <label for="contract_term_months" class="form-label">Contract Term (Months) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('contract_term_months') is-invalid @enderror"
                                       id="contract_term_months" name="contract_term_months" value="{{ old('contract_term_months', 12) }}" required
                                       min="1" placeholder="12">
                                @error('contract_term_months')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="currency" class="form-label">Currency</label>
                                <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency">
                                    <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                                    <option value="KES" {{ old('currency') == 'KES' ? 'selected' : '' }}>KES</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Pricing Information with Dynamic Label -->
                        <div class="row mb-3">
                             <div class="col-md-4">
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
                            <div class="col-md-4">
                                <label for="monthly_cost" class="form-label">Monthly Cost <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('monthly_cost') is-invalid @enderror"
                                       id="monthly_cost" name="monthly_cost" value="{{ old('monthly_cost') }}" required
                                       placeholder="0.00 (per month)">
                                <small class="text-muted" id="costHelpText">Monthly recurring charge</small>
                                <div class="invalid-feedback" id="costValidationMessage">
                                    Please enter a valid monthly cost
                                </div>
                                @error('monthly_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="installation_fee" class="form-label">Installation Fee</label>
                                <input type="number" step="0.01" class="form-control @error('installation_fee') is-invalid @enderror"
                                       id="installation_fee" name="installation_fee" value="{{ old('installation_fee', 0) }}"
                                       placeholder="0.00">
                                @error('installation_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="total_contract_value" class="form-label">Total Contract Value</label>
                                <input type="number" step="0.01" class="form-control @error('total_contract_value') is-invalid @enderror"
                                       id="total_contract_value" name="total_contract_value" value="{{ old('total_contract_value') }}"
                                       placeholder="0.00" readonly>
                                @error('total_contract_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contract Period -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                       id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" required readonly>
                                @error('end_date')
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
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Technical Specifications -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="technical_specifications" class="form-label">Technical Specifications</label>
                                <textarea class="form-control @error('technical_specifications') is-invalid @enderror"
                                          id="technical_specifications" name="technical_specifications" rows="4"
                                          placeholder="Enter technical specifications...">{{ old('technical_specifications', "• Fibre Type: ITU-T G.652.D Single Mode Fibre\n• Wavelength: 1310nm / 1550nm\n• Maximum Distance: 80km without amplification\n• Connector Type: APC/PC as required\n• Insertion Loss: ≤ 0.3dB per connector pair\n• Return Loss: ≥ 55dB (APC), ≥ 40dB (PC)\n• Operating Temperature: -40°C to +75°C\n• Cable Construction: Loose tube, gel-filled, double-jacketed\n• Installation: Underground duct or aerial as specified\n• Testing: OTDR testing with results provided") }}</textarea>
                                @error('technical_specifications')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Service Level Agreement -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="service_level_agreement" class="form-label">Service Level Agreement (SLA)</label>
                                <textarea class="form-control @error('service_level_agreement') is-invalid @enderror"
                                          id="service_level_agreement" name="service_level_agreement" rows="4"
                                          placeholder="Enter SLA terms...">{{ old('service_level_agreement', "• Service Availability: 99.99% monthly uptime guarantee\n• Mean Time To Repair (MTTR): 4 hours for critical faults\n• Emergency Response: 24/7/365 network operations center\n• Scheduled Maintenance: 30 days advance notice for planned maintenance\n• Performance Monitoring: Continuous link monitoring and alerting\n• Fault Reporting: Dedicated hotline and online portal for fault reporting\n• Credit Policy: Service credits for SLA violations as per agreement\n• Escalation Procedure: Defined escalation path for unresolved issues") }}</textarea>
                                @error('service_level_agreement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="terms_and_conditions" class="form-label">Terms & Conditions</label>
                                <textarea class="form-control @error('terms_and_conditions') is-invalid @enderror"
                                          id="terms_and_conditions" name="terms_and_conditions" rows="5"
                                          placeholder="Enter lease terms and conditions...">{{ old('terms_and_conditions', "1. TERM: This agreement shall commence on the start date and continue for the contract term specified.\n2. PAYMENT: Monthly invoices payable within 30 days of invoice date. Late payments subject to 1.5% monthly interest.\n3. TERMINATION: Either party may terminate for material breach with 30 days written notice.\n4. CONFIDENTIALITY: Both parties agree to maintain confidentiality of proprietary information.\n5. LIMITATION OF LIABILITY: Liability limited to direct damages, excluding consequential damages.\n6. FORCE MAJEURE: Neither party liable for delays due to circumstances beyond reasonable control.\n7. GOVERNING LAW: This agreement shall be governed by the laws of the jurisdiction specified.\n8. INSURANCE: Service provider maintains appropriate insurance coverage for services rendered.\n9. ACCESS: Customer shall provide reasonable access for installation and maintenance.\n10. ASSIGNMENT: Neither party may assign this agreement without prior written consent.") }}</textarea>
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
        <strong>Quotation Integration:</strong> Select an approved quotation to automatically populate all lease details.
    </p>
    <p class="small text-muted">
        <strong>Auto-Calculation:</strong> Total Contract Value = (Recurring Cost × Term) + Installation Fee
    </p>
    <p class="small text-muted">
        <strong>Recurring Cost:</strong> Depends on billing cycle (Monthly/Quarterly/Annual/One-time)
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
    console.log('=== LEASE FORM LOADED ===');

    // ===== DYNAMIC BILLING CYCLE LABEL =====
    const billingCycleSelect = document.getElementById('billing_cycle');
    const costLabel = document.querySelector('label[for="monthly_cost"]');
    const costInput = document.getElementById('monthly_cost');
    const costHelpText = document.getElementById('costHelpText');
    const costValidationMessage = document.getElementById('costValidationMessage');

    function updateCostLabel() {
    const selectedCycle = billingCycleSelect.value;
    let labelText = '';
    let placeholderText = '';
    let helpText = '';
    let validationMessage = '';

    switch(selectedCycle) {
        case 'monthly':
            labelText = 'Monthly Cost';
            placeholderText = '0.00 (per month)';
            helpText = 'Monthly recurring charge';
            validationMessage = 'Please enter a valid monthly cost';
            break;
        case 'quarterly':
            labelText = 'Quarterly Cost';
            placeholderText = '0.00 (per quarter)';
            helpText = 'Quarterly recurring charge (every 3 months)';
            validationMessage = 'Please enter a valid quarterly cost';
            break;
        case 'annually':
            labelText = 'Annual Cost';
            placeholderText = '0.00 (per year)';
            helpText = 'Annual recurring charge (once per year)';
            validationMessage = 'Please enter a valid annual cost';
            break;
        case 'one_time':
            labelText = 'One-Time Cost';
            placeholderText = '0.00 (one-time fee)';
            helpText = 'One-time setup or installation fee';
            validationMessage = 'Please enter a valid one-time fee';
            break;
        default:
            labelText = 'Cost';
            placeholderText = '0.00';
            helpText = 'Enter the cost amount';
            validationMessage = 'Please enter a valid cost amount';
    }

    // Update label with asterisk
    costLabel.innerHTML = `${labelText} <span class="text-danger">*</span>`;

    // Update placeholder
    costInput.placeholder = placeholderText;

    // Update help text if it exists
    if (costHelpText) {
        costHelpText.textContent = helpText;
    }

    // Update validation message
    if (costValidationMessage) {
        costValidationMessage.textContent = validationMessage;
    }

    // Recalculate total when label changes (since the meaning of the cost field changes)
    calculateTotalContractValue();

    console.log(`Billing cycle changed to: ${selectedCycle}, label updated to: ${labelText}`);
}

    // Initial update
    if (billingCycleSelect) {
        updateCostLabel();
        billingCycleSelect.addEventListener('change', updateCostLabel);
    }

    // ===== AUTO-FILL FROM QUOTATION =====
    const autoQuotationData = document.getElementById('auto-selected-quotation-data');
    const designItemData = document.getElementById('design-item-data');

    if (autoQuotationData) {
        console.log('Found auto-selected quotation, auto-filling form...');
        autoFillForm(autoQuotationData, designItemData);
    } else {
        console.log('No auto-selected quotation found');
    }

    // Initialize form calculations and events
    setupForm();
});

function autoFillForm(quotationElement, designItemElement) {
    // Extract all data from the quotation element
    const quotationData = {
        id: quotationElement.getAttribute('data-id'),
        quotation_number: quotationElement.getAttribute('data-quotation-number'),
        total_amount: quotationElement.getAttribute('data-total-amount'),
        design_request_title: quotationElement.getAttribute('data-design-request-title'),
        scope_of_work: quotationElement.getAttribute('data-scope-of-work'),
        terms_and_conditions: quotationElement.getAttribute('data-terms-and-conditions')
    };

    console.log('Quotation Data:', quotationData);

    // Extract design item data if exists
    let designData = null;
    if (designItemElement) {
        designData = {
            cores_required: designItemElement.getAttribute('data-cores-required'),
            distance: designItemElement.getAttribute('data-distance'),
            technology_type: designItemElement.getAttribute('data-technology-type'),
            unit_cost: designItemElement.getAttribute('data-unit-cost'),
            terms: designItemElement.getAttribute('data-terms'),
            route_name: designItemElement.getAttribute('data-route-name')
        };
        console.log('Design Item Data:', designData);
    }

    // ========== FILL THE FORM FIELDS ==========

    // 1. Title
    const titleField = document.getElementById('title');
    if (titleField && !titleField.value && quotationData.design_request_title) {
        titleField.value = `Lease for ${quotationData.design_request_title}`;
        console.log('Set title:', titleField.value);
    }

    // 2. Technical Specifications
    const techSpecsField = document.getElementById('technical_specifications');
    if (techSpecsField && !techSpecsField.value && quotationData.scope_of_work) {
        techSpecsField.value = quotationData.scope_of_work;
        console.log('Set technical specifications');
    }

    // 3. Terms and Conditions
    const termsField = document.getElementById('terms_and_conditions');
    if (termsField && !termsField.value && quotationData.terms_and_conditions) {
        termsField.value = quotationData.terms_and_conditions;
        console.log('Set terms and conditions');
    }

    // 4. Monthly Cost (calculate from total amount / 12 months)
    const monthlyCostField = document.getElementById('monthly_cost');
    if (monthlyCostField && !monthlyCostField.value && quotationData.total_amount) {
        const monthlyCost = parseFloat(quotationData.total_amount) / 12;
        monthlyCostField.value = monthlyCost.toFixed(2);
        console.log('Set monthly cost:', monthlyCostField.value);
    }

    // ========== FILL FROM DESIGN ITEMS ==========
    if (designData) {
        // 5. Cores Required
        const coresField = document.getElementById('cores_required');
        if (coresField && !coresField.value && designData.cores_required) {
            coresField.value = designData.cores_required;
            console.log('Set cores required:', coresField.value);
        }

        // 6. Distance (KM)
        const distanceField = document.getElementById('distance_km');
        if (distanceField && !distanceField.value && designData.distance) {
            distanceField.value = parseFloat(designData.distance).toFixed(2);
            console.log('Set distance:', distanceField.value);
        }

        // 7. Technology
        const technologyField = document.getElementById('technology');
        if (technologyField && !technologyField.value && designData.technology_type) {
            const techType = designData.technology_type.toLowerCase();

            // Map design item technology to form options
            let selectedValue = '';

            if (techType.includes('single') || techType.includes('sm')) {
                selectedValue = 'single_mode';
            } else if (techType.includes('multi') || techType.includes('mm')) {
                selectedValue = 'multimode';
            } else if (techType.includes('dwdm')) {
                selectedValue = 'dwdm';
            } else if (techType.includes('cwdm')) {
                selectedValue = 'cwdm';
            } else if (techType.includes('adss')) {
                selectedValue = 'ADSS';
            } else if (techType.includes('opgw')) {
                selectedValue = 'OPGW';
            } else {
                selectedValue = 'other';
            }

            technologyField.value = selectedValue;
            console.log('Set technology to:', selectedValue);
        }

        // 8. Contract Term
        const contractTermField = document.getElementById('contract_term_months');
        if (contractTermField && !contractTermField.value && designData.terms) {
            contractTermField.value = designData.terms;
            console.log('Set contract term:', contractTermField.value);
        }

        // 9. Service Type (auto-set to dark_fibre)
        const serviceTypeField = document.getElementById('service_type');
        if (serviceTypeField && !serviceTypeField.value) {
            serviceTypeField.value = 'dark_fibre';
            console.log('Set service type to dark_fibre');
        }

        // 10. Route Information
        if (designData.route_name && designData.route_name.includes(' to ')) {
            const parts = designData.route_name.split(' to ');
            const startLocationField = document.getElementById('start_location');
            const endLocationField = document.getElementById('end_location');

            if (startLocationField && !startLocationField.value && parts[0]) {
                startLocationField.value = parts[0].trim();
                console.log('Set start location:', startLocationField.value);
            }

            if (endLocationField && !endLocationField.value && parts[1]) {
                endLocationField.value = parts[1].trim();
                console.log('Set end location:', endLocationField.value);
            }
        }

        // 11. Installation Fee (10% of unit cost)
        const installationFeeField = document.getElementById('installation_fee');
        if (installationFeeField && !installationFeeField.value && designData.unit_cost) {
            const installationFee = parseFloat(designData.unit_cost) * 0.1;
            installationFeeField.value = installationFee.toFixed(2);
            console.log('Set installation fee:', installationFeeField.value);
        }
    }

    // Show success message
    showSuccessMessage('Form auto-filled from approved quotation!');

    // Trigger calculations after a delay
    setTimeout(() => {
        calculateTotalContractValue();
        calculateEndDate();
    }, 500);
}

function setupForm() {
    console.log('Setting up form calculations and events...');

    // Set default start date to today if empty
    const startDateField = document.getElementById('start_date');
    if (startDateField && !startDateField.value) {
        const today = new Date().toISOString().split('T')[0];
        startDateField.value = today;
        console.log('Set default start date:', today);
    }

    // Initialize calculations
    calculateEndDate();
    calculateTotalContractValue();

    // Event Listeners
    const contractTermField = document.getElementById('contract_term_months');
    const costField = document.getElementById('monthly_cost');
    const installationFeeField = document.getElementById('installation_fee');
    const billingCycleField = document.getElementById('billing_cycle');

    if (contractTermField) {
        contractTermField.addEventListener('input', function() {
            calculateEndDate();
            calculateTotalContractValue();
        });
    }

    if (costField) {
        costField.addEventListener('input', calculateTotalContractValue);
    }

    if (installationFeeField) {
        installationFeeField.addEventListener('input', calculateTotalContractValue);
    }

    // Add event listener for billing cycle changes
    if (billingCycleField) {
        billingCycleField.addEventListener('change', calculateTotalContractValue);
    }

    // Handle manual quotation selection
    const quotationSelect = document.getElementById('quotation_id');
    if (quotationSelect) {
        quotationSelect.addEventListener('change', handleManualQuotationSelect);
    }
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

            // Format as YYYY-MM-DD
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

        // Calculate based on billing cycle
        switch (billingCycle) {
            case 'monthly':
                // costAmount is monthly price
                totalRecurringCost = costAmount * termMonths;
                break;

            case 'quarterly':
                // costAmount is quarterly price
                const numberOfQuarters = Math.ceil(termMonths / 3);
                totalRecurringCost = costAmount * numberOfQuarters;
                break;

            case 'annually':
                // costAmount is annual price
                const numberOfYears = Math.ceil(termMonths / 12);
                totalRecurringCost = costAmount * numberOfYears;
                break;

            case 'one_time':
                // costAmount is one-time price
                totalRecurringCost = costAmount;
                break;

            default:
                totalRecurringCost = costAmount * termMonths;
        }

        const totalValue = totalRecurringCost + installationFee;
        totalValueField.value = totalValue.toFixed(2);

        console.log('Total contract value calculated:', {
            cost_amount: costAmount,
            term_months: termMonths,
            billing_cycle: billingCycle,
            total_recurring_cost: totalRecurringCost,
            installation_fee: installationFee,
            total_value: totalValue
        });

    } catch (error) {
        console.error('Error calculating total contract value:', error);
    }
}

function handleManualQuotationSelect(event) {
    const selectedOption = event.target.options[event.target.selectedIndex];
    const quotationDataJson = selectedOption.getAttribute('data-quotation-data');

    if (!quotationDataJson) return;

    try {
        const quotationData = JSON.parse(quotationDataJson);

        // Create temporary element to use autoFillForm logic
        const tempDiv = document.createElement('div');
        tempDiv.id = 'temp-quotation-data';
        tempDiv.setAttribute('data-id', quotationData.id);
        tempDiv.setAttribute('data-quotation-number', quotationData.quotation_number);
        tempDiv.setAttribute('data-total-amount', quotationData.total_amount);
        tempDiv.setAttribute('data-scope-of-work', quotationData.scope_of_work);
        tempDiv.setAttribute('data-terms-and-conditions', quotationData.terms_and_conditions);

        // Extract title from option text
        const optionText = selectedOption.textContent;
        const titleMatch = optionText.match(/ - (.+)$/);
        tempDiv.setAttribute('data-design-request-title', titleMatch ? titleMatch[1] : 'Untitled');

        // Fill form with this data
        autoFillForm(tempDiv, null);

        showSuccessMessage('Form filled from selected quotation!');

    } catch (error) {
        console.error('Error parsing quotation data:', error);
        showErrorMessage('Error loading quotation data');
    }
}

function showSuccessMessage(message) {
    // Create and show a simple alert
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function showErrorMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3';
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        <i class="fas fa-exclamation-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Debug: Check what data is available
console.log('Auto-selected quotation element:', document.getElementById('auto-selected-quotation-data'));
console.log('Design item element:', document.getElementById('design-item-data'));

// Also check the data attributes
const autoQuotation = document.getElementById('auto-selected-quotation-data');
if (autoQuotation) {
    console.log('Auto quotation data attributes:');
    console.log('ID:', autoQuotation.getAttribute('data-id'));
    console.log('Quotation Number:', autoQuotation.getAttribute('data-quotation-number'));
    console.log('Total Amount:', autoQuotation.getAttribute('data-total-amount'));
    console.log('Design Request Title:', autoQuotation.getAttribute('data-design-request-title'));
}
</script>
@endpush
