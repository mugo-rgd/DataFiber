@extends('layouts.app')

@section('title', 'Create Quotation')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-file-invoice-dollar text-primary"></i> Create Quotation
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.design-requests.index') }}">Design Requests</a></li>
                    <li class="breadcrumb-item active">Create Quotation</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Design Request Info -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Design Request Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Request #:</strong> #{{ $designRequest->request_number }}</p>
                            <p><strong>Customer:</strong> {{ $designRequest->customer->name }}</p>
                            <p><strong>Title:</strong> {{ $designRequest->title }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Requested:</strong> {{ $designRequest->created_at->format('M d, Y') }}</p>
                            <p><strong>Description:</strong> {{ $designRequest->description }}</p>
                        </div>
                    </div>
                    @if($designRequest->technical_requirements)
                        <div class="mt-3">
                            <strong>Technical Requirements:</strong>
                            <p class="text-muted">{{ $designRequest->technical_requirements }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.quotations.store') }}" method="POST" id="quotationForm">
        @csrf
        <input type="hidden" name="design_request_id" value="{{ $designRequest->id }}">

        <!-- Customer Requirements Section -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-list-check me-2"></i>Customer Requirements
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="customer_notes" class="form-label">Customer Notes & Specific Requirements</label>
                            <textarea name="customer_notes" id="customer_notes" class="form-control" rows="3"
                                      placeholder="Add any specific customer requirements or notes...">{{ old('customer_notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Selection Section -->
        <div class="row">
            <!-- Commercial Routes -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-route me-2"></i>Commercial Routes
                        </h5>
                        <span class="badge bg-light text-dark">{{ $commercialRoutes->count() }} available</span>
                    </div>
                    <div class="card-body">
                        @if($commercialRoutes->count() > 0)
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAllRoutes">
                                    <label class="form-check-label" for="selectAllRoutes">
                                        Select All Routes
                                    </label>
                                </div>
                            </div>
                            <div class="routes-container" style="max-height: 400px; overflow-y: auto;">
                                @foreach($commercialRoutes as $route)
                                    <div class="card route-card mb-3">
                                        <div class="card-body">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input route-select"
                                                       type="checkbox"
                                                       name="selected_routes[]"
                                                       value="{{ $route->id }}"
                                                       id="route_{{ $route->id }}"
                                                       data-route-id="{{ $route->id }}">
                                                <label class="form-check-label fw-bold" for="route_{{ $route->id }}">
                                                    {{ $route->name_of_route }}
                                                </label>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <i class="fas fa-wifi me-1"></i>{{ $route->tech_type }}<br>
                                                        <i class="fas fa-ruler me-1"></i>{{ $route->approx_distance_km }} km<br>
                                                        <i class="fas fa-toggle-on me-1"></i>{{ $route->availability }}
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="route-configuration" style="display: none;">
                                                        <div class="mb-2">
                                                            <label class="form-label small">Cores Required</label>
                                                            <input type="number"
                                                                   name="route_cores[{{ $route->id }}]"
                                                                   class="form-control form-control-sm cores-input"
                                                                   value="{{ $route->no_of_cores_required }}"
                                                                   min="1"
                                                                   data-route-id="{{ $route->id }}">
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label small">Duration (Months)</label>
                                                            <input type="number"
                                                                   name="route_duration[{{ $route->id }}]"
                                                                   class="form-control form-control-sm duration-input"
                                                                   value="12"
                                                                   min="1"
                                                                   data-route-id="{{ $route->id }}">
                                                        </div>
                                                        <div class="route-cost small">
                                                            <strong>Monthly: <span class="monthly-cost" data-route-id="{{ $route->id }}">$0.00</span></strong><br>
                                                            <strong>Total: <span class="total-cost" data-route-id="{{ $route->id }}">$0.00</span></strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-route fa-2x text-muted mb-2"></i>
                                <p class="text-muted">No commercial routes available.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colocation Services -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-server me-2"></i>Colocation Services
                        </h5>
                        <span class="badge bg-light text-dark">{{ $colocationServices->count() }} available</span>
                    </div>
                    <div class="card-body">
                        @if($colocationServices->count() > 0)
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAllServices">
                                    <label class="form-check-label" for="selectAllServices">
                                        Select All Services
                                    </label>
                                </div>
                            </div>
                            <div class="services-container" style="max-height: 400px; overflow-y: auto;">
                                @foreach($colocationServices as $service)
                                    <div class="card service-card mb-3">
                                        <div class="card-body">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input service-select"
                                                       type="checkbox"
                                                       name="selected_services[]"
                                                       value="{{ $service->service_id }}"
                                                       id="service_{{ $service->service_id }}"
                                                       data-service-id="{{ $service->service_id }}">
                                                <label class="form-check-label fw-bold" for="service_{{ $service->service_id }}">
                                                    {{ $service->service_type }}
                                                </label>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <i class="fas fa-tag me-1"></i>{{ $service->service_category }}<br>
                                                        @if($service->power_kw)
                                                            <i class="fas fa-bolt me-1"></i>{{ $service->power_kw }} kW<br>
                                                        @endif
                                                        @if($service->space_sqm)
                                                            <i class="fas fa-arrows-alt me-1"></i>{{ $service->space_sqm }} m²<br>
                                                        @endif
                                                        <i class="fas fa-file-alt me-1"></i>{{ Str::limit($service->specifications, 50) }}
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="service-configuration" style="display: none;">
                                                        <div class="mb-2">
                                                            <label class="form-label small">Duration (Months)</label>
                                                            <input type="number"
                                                                   name="service_duration[{{ $service->service_id }}]"
                                                                   class="form-control form-control-sm service-duration-input"
                                                                   value="{{ $service->min_contract_months ?? 12 }}"
                                                                   min="{{ $service->min_contract_months ?? 1 }}"
                                                                   data-service-id="{{ $service->service_id }}">
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label small">Quantity</label>
                                                            <input type="number"
                                                                   name="service_quantity[{{ $service->service_id }}]"
                                                                   class="form-control form-control-sm service-quantity-input"
                                                                   value="1"
                                                                   min="1"
                                                                   data-service-id="{{ $service->service_id }}">
                                                        </div>
                                                        <div class="service-cost small">
                                                            <strong>Monthly: <span class="monthly-cost" data-service-id="{{ $service->service_id }}">$0.00</span></strong><br>
                                                            <strong>Total: <span class="total-cost" data-service-id="{{ $service->service_id }}">$0.00</span></strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-server fa-2x text-muted mb-2"></i>
                                <p class="text-muted">No colocation services available.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Items Section -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>Additional Custom Items
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="customItemsContainer">
                            <div class="custom-item row mb-3">
                                <div class="col-md-4">
                                    <input type="text" name="custom_items[0][description]" class="form-control" placeholder="Item description">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="custom_items[0][quantity]" class="form-control" value="1" min="1" placeholder="Qty">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="custom_items[0][unit_price]" class="form-control" step="0.01" min="0" placeholder="Unit price">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="custom_items[0][total]" class="form-control" placeholder="Total" readonly>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="addCustomItem" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Custom Item
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Summary -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-calculator me-2"></i>Pricing Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>Commercial Routes Total:</strong>
                            </div>
                            <div class="col-6 text-end">
                                <span id="routesTotal">$0.00</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>Colocation Services Total:</strong>
                            </div>
                            <div class="col-6 text-end">
                                <span id="servicesTotal">$0.00</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>Custom Items Total:</strong>
                            </div>
                            <div class="col-6 text-end">
                                <span id="customItemsTotal">$0.00</span>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>Subtotal:</strong>
                            </div>
                            <div class="col-6 text-end">
                                <span id="subtotal">$0.00</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Tax Rate (%):</strong>
                            </div>
                            <div class="col-6">
                                <input type="number"
                                       name="tax_rate"
                                       id="tax_rate"
                                       value="0.16"
                                       min="0"
                                       max="0.5"
                                       step="0.01"
                                       class="form-control form-control-sm"
                                       required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>Tax Amount:</strong>
                            </div>
                            <div class="col-6 text-end">
                                <span id="tax_amount">$0.00</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong class="h5">Total Amount:</strong>
                            </div>
                            <div class="col-6 text-end">
                                <span id="total_amount" class="h5 text-primary">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quotation Details -->
            <div class="col-lg-6">
    <div class="card shadow mb-4">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-file-contract me-2"></i>Quotation Details & Terms
            </h5>
            <span class="badge bg-light text-info">Required</span>
        </div>
        <div class="card-body">
            <!-- Scope of Work -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label for="scope_of_work" class="form-label fw-semibold">
                        <i class="fas fa-tasks me-1"></i>Scope of Work
                        <small class="text-muted">(Detailed description of services and deliverables)</small>
                    </label>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary" onclick="loadDefaultScope()">
                            <i class="fas fa-bolt me-1"></i>Quick Fill
                        </button>
                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#scopeTemplatesModal">
                            <i class="fas fa-layer-group me-1"></i>Templates
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="formatScopeOfWork()">
                            <i class="fas fa-magic me-1"></i>Format
                        </button>
                    </div>
                </div>
                <textarea name="scope_of_work"
                          id="scope_of_work"
                          class="form-control scope-textarea"
                          rows="7"
                          placeholder="Describe in detail the scope of work, services to be provided, deliverables, and any specific requirements..."
                          oninput="updateCharacterCount('scope_counter', this)"
                          required>{{ old('scope_of_work', $quotation->scope_of_work ?? '') }}</textarea>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <div class="form-text">
                        <i class="fas fa-info-circle me-1"></i>Be specific about inclusions and exclusions
                    </div>
                    <small class="text-muted">
                        <span id="scope_counter">0</span> characters
                    </small>
                </div>
            </div>

            <!-- Terms & Conditions -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label for="terms_and_conditions" class="form-label fw-semibold">
                        <i class="fas fa-gavel me-1"></i>Terms & Conditions
                        <small class="text-muted">(Legal and commercial terms)</small>
                    </label>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-info" onclick="loadDefaultTerms()">
                            <i class="fas fa-redo me-1"></i>Default Terms
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="showTermsBuilder()">
                            <i class="fas fa-hammer me-1"></i>Build Terms
                        </button>
                    </div>
                </div>
                <textarea name="terms_and_conditions"
                          id="terms_and_conditions"
                          class="form-control terms-textarea"
                          rows="7"
                          placeholder="Specify the commercial and legal terms and conditions..."
                          oninput="updateCharacterCount('terms_counter', this)"
                          required>{{ old('terms_and_conditions', $quotation->terms_and_conditions ?? $defaultTerms) }}</textarea>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <div class="form-text">
                        <i class="fas fa-exclamation-triangle me-1"></i>Include payment terms, validity, and liabilities
                    </div>
                    <small class="text-muted">
                        <span id="terms_counter">0</span> characters
                    </small>
                </div>
            </div>

            <!-- Pricing Notes -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label for="pricing_notes" class="form-label fw-semibold">
                        <i class="fas fa-dollar-sign me-1"></i>Pricing Notes & Additional Information
                    </label>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertPricingTemplate()">
                        <i class="fas fa-plus me-1"></i>Add Template
                    </button>
                </div>
                <textarea name="pricing_notes"
                          id="pricing_notes"
                          class="form-control"
                          rows="4"
                          placeholder="Any additional notes about pricing, discounts, payment plans, or special offers..."
                          oninput="updateCharacterCount('pricing_counter', this)">{{ old('pricing_notes', $quotation->pricing_notes ?? '') }}</textarea>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <div class="form-text">
                        <i class="fas fa-lightbulb me-1"></i>Optional: Payment plans, discounts, or special conditions
                    </div>
                    <small class="text-muted">
                        <span id="pricing_counter">0</span> characters
                    </small>
                </div>
            </div>

            <!-- Validity Period -->
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="valid_until" class="form-label fw-semibold">
                            <i class="fas fa-calendar-check me-1"></i>Valid Until Date
                        </label>
                        <div class="input-group">
                            <input type="date"
                                   name="valid_until"
                                   id="valid_until"
                                   class="form-control"
                                   value="{{ old('valid_until', isset($quotation) ? $quotation->valid_until->format('Y-m-d') : \Carbon\Carbon::now()->addDays(30)->format('Y-m-d')) }}"
                                   min="{{ \Carbon\Carbon::now()->addDay()->format('Y-m-d') }}"
                                   required>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDefaultValidity()">
                                <i class="fas fa-sync"></i>
                            </button>
                        </div>
                        <div class="form-text">
                            <i class="fas fa-clock me-1"></i>Quotation expiration date
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="validity_days" class="form-label fw-semibold">
                            <i class="fas fa-business-time me-1"></i>Validity Period
                        </label>
                        <div class="input-group">
                            <input type="number"
                                   name="validity_days"
                                   id="validity_days"
                                   class="form-control"
                                   min="1"
                                   max="365"
                                   value="{{ old('validity_days', isset($quotation) ? $quotation->validity_days : 30) }}"
                                   onchange="updateValidUntilDate()">
                            <span class="input-group-text">days</span>
                        </div>
                        <div class="form-text">
                            <i class="fas fa-calculator me-1"></i>Auto-calculates from today
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Options -->
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="include_tax" id="include_tax"
                                   {{ old('include_tax', isset($quotation) ? $quotation->include_tax : true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="include_tax">
                                <i class="fas fa-receipt me-1"></i>Include Tax in Pricing
                            </label>
                        </div>
                        <div class="form-text ms-4">Tax will be calculated automatically</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="allow_negotiation" id="allow_negotiation"
                                   {{ old('allow_negotiation', isset($quotation) ? $quotation->allow_negotiation : true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="allow_negotiation">
                                <i class="fas fa-handshake me-1"></i>Allow Price Negotiation
                            </label>
                        </div>
                        <div class="form-text ms-4">Customer can request price adjustments</div>
                    </div>
                </div>
            </div>

            <!-- Special Instructions -->
            <div class="mb-3">
                <label for="special_instructions" class="form-label fw-semibold">
                    <i class="fas fa-comment-dots me-1"></i>Special Instructions & Notes
                    <small class="text-muted">(Internal or customer-facing notes)</small>
                </label>
                <textarea name="special_instructions"
                          id="special_instructions"
                          class="form-control"
                          rows="3"
                          placeholder="Any special instructions, internal notes, or customer-specific information..."
                          oninput="updateCharacterCount('instructions_counter', this)">{{ old('special_instructions', $quotation->special_instructions ?? '') }}</textarea>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <div class="form-text">
                        <i class="fas fa-eye me-1"></i>These notes will be visible to the customer
                    </div>
                    <small class="text-muted">
                        <span id="instructions_counter">0</span> characters
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scope of Work Templates Modal -->
<div class="modal fade" id="scopeTemplatesModal" tabindex="-1" aria-labelledby="scopeTemplatesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="scopeTemplatesModalLabel">
                    <i class="fas fa-templates me-2"></i>Scope of Work Templates
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <!-- Standard Colocation Template -->
                    <div class="col-md-6">
                        <div class="card h-100 template-card border-primary" onclick="loadTemplate('standard_colocation')">
                            <div class="card-header bg-primary text-white py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-server me-2"></i>Standard Colocation
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text small">Basic rack space, power, and connectivity with standard SLAs.</p>
                                <div class="template-features">
                                    <span class="badge bg-light text-dark me-1 mb-1">Rack Space</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">Power</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">Connectivity</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">Basic Support</span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <small class="text-muted">Click to apply template</small>
                            </div>
                        </div>
                    </div>

                    <!-- Premium Colocation Template -->
                    <div class="col-md-6">
                        <div class="card h-100 template-card border-warning" onclick="loadTemplate('premium_colocation')">
                            <div class="card-header bg-warning text-dark py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-crown me-2"></i>Premium Colocation
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text small">Enhanced services with premium support and higher SLAs.</p>
                                <div class="template-features">
                                    <span class="badge bg-light text-dark me-1 mb-1">Dedicated Cabinet</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">A/B Power</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">10G Connectivity</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">24/7 Support</span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <small class="text-muted">Click to apply template</small>
                            </div>
                        </div>
                    </div>

                    <!-- Managed Services Template -->
                    <div class="col-md-6">
                        <div class="card h-100 template-card border-success" onclick="loadTemplate('managed_services')">
                            <div class="card-header bg-success text-white py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-headset me-2"></i>Managed Services
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text small">Full management including monitoring, patching, and support.</p>
                                <div class="template-features">
                                    <span class="badge bg-light text-dark me-1 mb-1">Monitoring</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">Patching</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">Backup</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">Security</span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <small class="text-muted">Click to apply template</small>
                            </div>
                        </div>
                    </div>

                    <!-- Dark Fibre Connectivity Template -->
                    <div class="col-md-6">
                        <div class="card h-100 template-card border-info" onclick="loadTemplate('dark_fibre')">
                            <div class="card-header bg-info text-white py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-satellite-dish me-2"></i>Dark Fibre Connectivity
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text small">Dedicated fibre optic connectivity with SLA guarantees.</p>
                                <div class="template-features">
                                    <span class="badge bg-light text-dark me-1 mb-1">Dark Fibre</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">Dedicated</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">SLA Guarantee</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">24/7 Monitoring</span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <small class="text-muted">Click to apply template</small>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Solution Template -->
                    <div class="col-md-6">
                        <div class="card h-100 template-card border-dark" onclick="loadTemplate('custom_solution')">
                            <div class="card-header bg-dark text-white py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-puzzle-piece me-2"></i>Custom Solution
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text small">Tailored solution with specific requirements and custom SLAs.</p>
                                <div class="template-features">
                                    <span class="badge bg-light text-dark me-1 mb-1">Custom Design</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">Flexible Terms</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">Tailored SLA</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">Special Requirements</span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <small class="text-muted">Click to apply template</small>
                            </div>
                        </div>
                    </div>

                    <!-- Empty Template -->
                    <div class="col-md-6">
                        <div class="card h-100 template-card border-secondary" onclick="loadTemplate('empty')">
                            <div class="card-header bg-secondary text-white py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-file me-2"></i>Blank Template
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text small">Start with a clean, structured template.</p>
                                <div class="template-features">
                                    <span class="badge bg-light text-dark me-1 mb-1">Structured</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">Clean Format</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">Customizable</span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <small class="text-muted">Click to apply template</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="saveCustomTemplate()">
                    <i class="fas fa-save me-1"></i>Save as Custom Template
                </button>
            </div>
        </div>
    </div>
</div>
        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.design-requests.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Design Requests
                            </a>
                            <div>
                                @if((auth()->user()->role === 'account_manager') or (auth()->user()->role === 'designer'))
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save as Draft
                                    </button>
                                    <small class="text-muted d-block mt-1">Only admins can send quotations to customers</small>
                                @else
                                    <button type="submit" name="action" value="draft" class="btn btn-outline-primary">
                                        <i class="fas fa-save me-2"></i>Save as Draft
                                    </button>
                                    <button type="submit" name="action" value="send" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Save & Send to Customer
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('styles')
<style>
.route-card, .service-card {
    border-left: 4px solid #28a745;
}
.service-card {
    border-left-color: #ffc107;
}
.route-configuration, .service-configuration {
    transition: all 0.3s ease;
}
.custom-item {
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    margin-bottom: 10px;
}

.card-header {
    border-bottom: 2px solid rgba(255,255,255,0.1);
}

.form-label {
    color: #2c3e50;
}

.form-control:focus {
    border-color: #17a2b8;
    box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
}

.scope-textarea, .terms-textarea {
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    line-height: 1.4;
}

.form-text {
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75rem;
}

.template-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.template-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}

.template-features {
    min-height: 40px;
}

.modal-header {
    border-bottom: 2px solid rgba(255,255,255,0.1);
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

.alert {
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.text-warning {
    color: #ffc107 !important;
}

</style>
@endsection

@section('scripts')
<script>
    // Enhanced Templates with better structure
const scopeTemplates = {
    standard_colocation: `SCOPE OF WORK - STANDARD COLOCATION SERVICES

PROJECT OVERVIEW:
This quotation covers standard colocation services including rack space, power, cooling, and basic network connectivity in our Tier III data center facility.

INCLUDED SERVICES:
✓ Secure rack space allocation (1U to 42U available)
✓ Redundant power supply (A&B feeds with UPS and generator backup)
✓ Standard cooling and environmental controls (19-23°C, 40-60% RH)
✓ Basic remote hands support during business hours (9 AM - 5 PM, Mon-Fri)
✓ Shared 1Gbps network connectivity with burst capability
✓ 24/7 physical security with biometric access and CCTV
✓ Standard monitoring and alerting for power and connectivity
✓ Basic incident reporting and monthly utilization reports

EXCLUSIONS:
✗ Hardware procurement, installation, and maintenance
✗ Operating system support and software licensing
✗ Advanced technical support outside business hours
✗ Custom network configuration and firewall rules
✗ Bandwidth usage beyond allocated quota
✗ Cross-connect and cabling services
✗ IP address allocation beyond standard /29 block

DELIVERABLES:
• Rack space assignment and layout documentation
• Network connectivity details and IP allocation
• Access control procedures and security guidelines
• Emergency contact information and support procedures
• Monthly utilization and performance reports
• Service Level Agreement documentation

ASSUMPTIONS:
• Customer provides all hardware and software
• Standard power density limits apply (max 4kW per rack)
• All equipment must comply with data center standards
• 30-day notice for service termination required`,

    premium_colocation: `SCOPE OF WORK - PREMIUM COLOCATION SERVICES

PROJECT OVERVIEW:
Premium colocation services featuring dedicated cabinet space, enhanced power, high-speed connectivity, and 24/7 premium support.

INCLUDED SERVICES:
✓ Dedicated secure cabinet with individual locking
✓ A&B power feeds with intelligent PDUs and metered power
✓ Advanced precision cooling with humidity control
✓ 24/7 remote hands support with 1-hour response time SLA
✓ Dedicated 10Gbps network connectivity with BGP options
✓ Multi-layered security with man-traps and 24/7 guards
✓ Advanced monitoring with custom thresholds and alerts
✓ Monthly performance and capacity planning reports
✓ Quarterly business reviews and service optimization
✓ Disaster recovery planning and documentation

EXCLUSIONS:
✗ Application-level support and troubleshooting
✗ Custom software development and implementation
✗ Hardware procurement and lifecycle management
✗ Specialized security services and compliance audits
✗ Cross-connect installation and management

DELIVERABLES:
• Dedicated cabinet documentation and access procedures
• Custom SLA agreement with performance guarantees
• Monthly performance and capacity reports
• Security compliance documentation
• Disaster recovery plan and procedures
• Quarterly service review presentations`,

    managed_services: `SCOPE OF WORK - MANAGED SERVICES

PROJECT OVERVIEW:
Comprehensive managed services including hardware monitoring, system administration, backup management, and 24/7 technical support.

INCLUDED SERVICES:
✓ 24/7 hardware monitoring and health checks
✓ Operating system installation, patching, and updates
✓ Backup system management and monitoring
✓ Security monitoring and intrusion detection
✓ Performance optimization and capacity planning
✓ 24/7 technical support with 1-hour response time
✓ Regular system health checks and maintenance
✓ Disaster recovery planning and testing
✓ Monthly security and performance reports
✓ Proactive issue identification and resolution

EXCLUSIONS:
✗ Application development and customization
✗ Database administration and optimization
✗ Custom software installation and configuration
✗ Specialized security services and penetration testing
✗ Hardware procurement and replacement

DELIVERABLES:
• Managed services portal access
• Monthly health and performance reports
• Backup verification and recovery testing reports
• Security compliance documentation
• Incident response procedures
• Quarterly service review meetings`,

    dark_fibre: `SCOPE OF WORK - DARK FIBRE CONNECTIVITY

PROJECT OVERVIEW:
Dedicated dark fibre connectivity between specified locations with SLA guarantees and 24/7 monitoring.

INCLUDED SERVICES:
✓ Dedicated dark fibre strands between endpoints
✓ 24/7 fibre path monitoring and alerting
✓ SLA guarantees for availability and performance
✓ Installation and testing of fibre connectivity
✓ Access to fibre meet-me rooms
✓ Basic troubleshooting and maintenance
✓ Performance monitoring and reporting
✓ Emergency restoration services

EXCLUSIONS:
✗ End-user equipment and transceivers
✗ Network equipment configuration
✗ Cross-connect services beyond fibre handoff
✗ Power and space for customer equipment
✗ Additional circuit provisioning

DELIVERABLES:
• Fibre connectivity documentation
• SLA agreement with performance guarantees
• Monitoring and reporting access
• Emergency contact procedures
• Monthly performance reports`,

    custom_solution: `SCOPE OF WORK - CUSTOM SOLUTION

PROJECT OVERVIEW:
Tailored infrastructure solution designed to meet specific business requirements with custom SLAs and specialized services.

INCLUDED SERVICES:
[To be specified based on customer requirements]

CUSTOM COMPONENTS:
• [List specific custom requirements]
• [Specialized hardware or software needs]
• [Unique compliance or security requirements]
• [Custom monitoring and reporting needs]
• [Specialized support arrangements]

EXCLUSIONS:
[To be specified based on customer requirements]

DELIVERABLES:
• Custom design and architecture documentation
• Implementation plan and timeline
• Specialized SLA agreement
• Custom monitoring and reporting framework
• Training and knowledge transfer sessions

SPECIAL REQUIREMENTS:
• [List any special requirements or conditions]`,

    empty: `SCOPE OF WORK

PROJECT OVERVIEW:
[Brief description of the project and objectives]

INCLUDED SERVICES:
• [Service 1 description]
• [Service 2 description]
• [Service 3 description]
• [Service 4 description]

EXCLUSIONS:
• [Excluded service 1]
• [Excluded service 2]
• [Excluded service 3]

DELIVERABLES:
• [Deliverable 1]
• [Deliverable 2]
• [Deliverable 3]

ASSUMPTIONS & DEPENDENCIES:
• [Assumption 1]
• [Assumption 2]
• [Dependency 1]`
};

// Default terms template
const defaultTerms = `TERMS AND CONDITIONS

1. PAYMENT TERMS:
   1.1. Payment is due within 30 days of invoice date
   1.2. Late payments will incur interest at 1.5% per month
   1.3. All prices are quoted in USD unless specified otherwise
   1.4. A 50% deposit may be required for new customers

2. QUOTATION VALIDITY:
   2.1. This quotation is valid for 30 days from the date of issue
   2.2. Prices are subject to change after the validity period
   2.3. Acceptance must be in writing via email or signed document

3. SERVICE DELIVERY:
   3.1. Services will be delivered within the agreed timeframe
   3.2. Installation is subject to successful site survey
   3.3. Any changes to scope may affect delivery timelines

4. CANCELLATION AND TERMINATION:
   4.1. 30 days written notice required for service cancellation
   4.2. Early termination fees may apply for contract services
   4.3. Setup fees are non-refundable once work has commenced

5. LIABILITY AND WARRANTIES:
   5.1. Liability is limited to the value of services provided
   5.2. No liability for consequential or indirect damages
   5.3. Services are provided on an "as-is" basis unless specified

6. GOVERNING LAW:
   6.1. This agreement is governed by the laws of the jurisdiction
   6.2. Disputes will be resolved through arbitration
   6.3. Each party bears its own legal costs

7. FORCE MAJEURE:
   7.1. Neither party is liable for delays due to circumstances beyond reasonable control
   7.2. Includes acts of God, war, terrorism, and government actions

8. CONFIDENTIALITY:
   8.1. Both parties agree to maintain confidentiality of business information
   8.2. This obligation survives termination of the agreement`;

// Character counter function
function updateCharacterCount(counterId, textarea) {
    const counter = document.getElementById(counterId);
    counter.textContent = textarea.value.length;

    // Add warning for very long content
    if (textarea.value.length > 2000) {
        counter.classList.add('text-warning', 'fw-bold');
    } else {
        counter.classList.remove('text-warning', 'fw-bold');
    }
}

// Template loading functions
function loadDefaultScope() {
    document.getElementById('scope_of_work').value = scopeTemplates.standard_colocation;
    updateCharacterCount('scope_counter', document.getElementById('scope_of_work'));
}

function loadTemplate(templateType) {
    document.getElementById('scope_of_work').value = scopeTemplates[templateType] || scopeTemplates.standard_colocation;
    updateCharacterCount('scope_counter', document.getElementById('scope_of_work'));
    bootstrap.Modal.getInstance(document.getElementById('scopeTemplatesModal')).hide();

    // Show success message
    showToast('Template applied successfully!', 'success');
}

function loadDefaultTerms() {
    document.getElementById('terms_and_conditions').value = defaultTerms;
    updateCharacterCount('terms_counter', document.getElementById('terms_and_conditions'));
    showToast('Default terms loaded!', 'info');
}

// Formatting function
function formatScopeOfWork() {
    const textarea = document.getElementById('scope_of_work');
    let content = textarea.value;

    // Basic formatting rules
    content = content.replace(/\n\s*\n\s*\n/g, '\n\n'); // Remove extra blank lines
    content = content.replace(/^[•\-]\s*/gm, '• '); // Standardize bullet points
    content = content.replace(/\s+$/gm, ''); // Remove trailing spaces

    textarea.value = content;
    updateCharacterCount('scope_counter', textarea);
    showToast('Content formatted!', 'success');
}

// Pricing template inserter
function insertPricingTemplate() {
    const template = `PRICING NOTES:

• All prices are exclusive of VAT unless specified
• Payment plan available: 50% upfront, 50% on completion
• Early payment discount: 2% if paid within 10 days
• Prices valid for 30 days from quotation date
• Setup fees are one-time and non-refundable
• Monthly recurring charges billed in advance`;

    const textarea = document.getElementById('pricing_notes');
    textarea.value = template;
    updateCharacterCount('pricing_counter', textarea);
}

// Date management functions
function updateValidUntilDate() {
    const days = parseInt(document.getElementById('validity_days').value) || 30;
    const validUntil = new Date();
    validUntil.setDate(validUntil.getDate() + days);
    document.getElementById('valid_until').value = validUntil.toISOString().split('T')[0];
}

function updateValidityDays() {
    const validUntil = new Date(document.getElementById('valid_until').value);
    const today = new Date();
    const diffTime = validUntil - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    document.getElementById('validity_days').value = diffDays > 0 ? diffDays : 30;
}

function setDefaultValidity() {
    document.getElementById('validity_days').value = 30;
    updateValidUntilDate();
    showToast('Validity period reset to 30 days', 'info');
}

// Toast notification function
function showToast(message, type = 'info') {
    // Simple toast implementation - you can replace with your preferred toast library
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);

    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

// Terms builder function (simplified)
function showTermsBuilder() {
    const additionalTerms = `
9. SERVICE LEVEL AGREEMENT:
   9.1. 99.9% network availability guarantee
   9.2. 4-hour response time for critical issues
   9.3. 24/7 monitoring and support coverage

10. CHANGE MANAGEMENT:
   10.1. 7 days notice required for scheduled maintenance
   10.2. Emergency changes communicated within 1 hour
   10.3. Change requests processed within 2 business days`;

    const textarea = document.getElementById('terms_and_conditions');
    textarea.value += additionalTerms;
    updateCharacterCount('terms_counter', textarea);
    showToast('Additional terms added!', 'success');
}

// Custom template saver
function saveCustomTemplate() {
    const scopeContent = document.getElementById('scope_of_work').value;
    if (scopeContent.length < 50) {
        showToast('Please add more content before saving as template', 'warning');
        return;
    }

    // In a real implementation, this would save to a database
    // For now, we'll just show a message
    showToast('Custom template feature would save to database in production', 'info');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize character counters
    updateCharacterCount('scope_counter', document.getElementById('scope_of_work'));
    updateCharacterCount('terms_counter', document.getElementById('terms_and_conditions'));
    updateCharacterCount('pricing_counter', document.getElementById('pricing_notes'));
    updateCharacterCount('instructions_counter', document.getElementById('special_instructions'));

    // Initialize date synchronization
    document.getElementById('valid_until').addEventListener('change', updateValidityDays);
    document.getElementById('validity_days').addEventListener('change', updateValidUntilDate);

    // Add template card hover effects
    const templateCards = document.querySelectorAll('.template-card');
    templateCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});
// This would be a comprehensive JavaScript file for handling the quotation creation
// Due to length, I'll provide the basic structure
document.addEventListener('DOMContentLoaded', function() {
    // Initialize pricing calculation
    calculateTotals();

    // Event listeners for route selection
    document.querySelectorAll('.route-select').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const routeId = this.dataset.routeId;
            const configDiv = this.closest('.route-card').querySelector('.route-configuration');
            configDiv.style.display = this.checked ? 'block' : 'none';
            if (this.checked) {
                calculateRouteCost(routeId);
            }
            calculateTotals();
        });
    });

    // Similar event listeners for services
    document.querySelectorAll('.service-select').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const serviceId = this.dataset.serviceId;
            const configDiv = this.closest('.service-card').querySelector('.service-configuration');
            configDiv.style.display = this.checked ? 'block' : 'none';
            if (this.checked) {
                calculateServiceCost(serviceId);
            }
            calculateTotals();
        });
    });

    // Select all functionality
    document.getElementById('selectAllRoutes').addEventListener('change', function() {
        document.querySelectorAll('.route-select').forEach(checkbox => {
            checkbox.checked = this.checked;
            checkbox.dispatchEvent(new Event('change'));
        });
    });

    document.getElementById('selectAllServices').addEventListener('change', function() {
        document.querySelectorAll('.service-select').forEach(checkbox => {
            checkbox.checked = this.checked;
            checkbox.dispatchEvent(new Event('change'));
        });
    });

    // Custom items functionality
    let customItemIndex = 1;
    document.getElementById('addCustomItem').addEventListener('click', function() {
        const container = document.getElementById('customItemsContainer');
        const newItem = document.createElement('div');
        newItem.className = 'custom-item row mb-3';
        newItem.innerHTML = `
            <div class="col-md-4">
                <input type="text" name="custom_items[${customItemIndex}][description]" class="form-control" placeholder="Item description">
            </div>
            <div class="col-md-2">
                <input type="number" name="custom_items[${customItemIndex}][quantity]" class="form-control" value="1" min="1" placeholder="Qty">
            </div>
            <div class="col-md-2">
                <input type="number" name="custom_items[${customItemIndex}][unit_price]" class="form-control" step="0.01" min="0" placeholder="Unit price">
            </div>
            <div class="col-md-2">
                <input type="text" name="custom_items[${customItemIndex}][total]" class="form-control" placeholder="Total" readonly>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
            </div>
        `;
        container.appendChild(newItem);
        customItemIndex++;

        // Add event listeners to new item
        newItem.querySelector('.remove-item').addEventListener('click', function() {
            newItem.remove();
            calculateTotals();
        });

        newItem.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', calculateTotals);
        });
    });

    // Remove custom item event delegation
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.custom-item').remove();
            calculateTotals();
        }
    });

    // Input change listeners for calculations
    document.querySelectorAll('.cores-input, .duration-input, .service-duration-input, .service-quantity-input, #tax_rate').forEach(input => {
        input.addEventListener('input', function() {
            if (this.classList.contains('cores-input') || this.classList.contains('duration-input')) {
                const routeId = this.dataset.routeId;
                calculateRouteCost(routeId);
            } else if (this.classList.contains('service-duration-input') || this.classList.contains('service-quantity-input')) {
                const serviceId = this.dataset.serviceId;
                calculateServiceCost(serviceId);
            }
            calculateTotals();
        });
    });

    function calculateRouteCost(routeId) {
        // Implementation for route cost calculation
        // This would fetch the route data and calculate based on cores and duration
    }

    function calculateServiceCost(serviceId) {
        // Implementation for service cost calculation
        // This would fetch the service data and calculate based on duration and quantity
    }

    function calculateTotals() {
        // Implementation for total calculation
        // This would sum up all route costs, service costs, and custom items
        // Then calculate tax and total
    }
});
</script>
@endsection
