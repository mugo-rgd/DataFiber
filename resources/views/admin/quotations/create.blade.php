@extends('layouts.app')

@section('title', 'Create Quotation')

@section('content')
<div class="container-fluid">
    <!-- Header with Progress Steps -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-file-invoice-dollar text-kp-blue"></i> Create Quotation
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.design-requests.index') }}">Design Requests</a></li>
                            <li class="breadcrumb-item active">Create Quotation</li>
                        </ol>
                    </nav>
                </div>
                <div class="mt-2 mt-sm-0">
                    <span class="badge bg-kp-blue p-2">
                        <i class="fas fa-file-alt me-1"></i> Request #{{ $designRequest->request_number }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Design Request Info - Collapsible Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-kp-blue text-white d-flex justify-content-between align-items-center cursor-pointer" data-bs-toggle="collapse" data-bs-target="#designRequestInfo">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Design Request Information
                    </h5>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="collapse show" id="designRequestInfo">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong class="text-muted">Customer:</strong>
                                    <span>{{ $designRequest->customer->name }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong class="text-muted">Title:</strong>
                                    <span>{{ $designRequest->title }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong class="text-muted">Requested:</strong>
                                    <span>{{ $designRequest->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong class="text-muted">Description:</strong>
                                    <p class="mb-0">{{ $designRequest->description }}</p>
                                </div>
                                @if($designRequest->technical_requirements)
                                <div class="mt-2">
                                    <strong class="text-muted">Technical Requirements:</strong>
                                    <p class="mb-0 text-muted">{{ $designRequest->technical_requirements }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.quotations.store') }}" method="POST" id="quotationForm">
        @csrf
        <input type="hidden" name="design_request_id" value="{{ $designRequest->id }}">

        <div class="row">
            <!-- LEFT COLUMN: Service Selection (70%) -->
            <div class="col-lg-8">
                <!-- Customer Requirements - Compact -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-list-check me-2"></i>Customer Requirements
                        </h6>
                    </div>
                    <div class="card-body py-2">
                        <textarea name="customer_notes" id="customer_notes" class="form-control" rows="2"
                                  placeholder="Add any specific customer requirements or notes...">{{ old('customer_notes') }}</textarea>
                    </div>
                </div>

                <!-- Commercial Routes -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-kp-blue text-white d-flex justify-content-between align-items-center py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-route me-2"></i>Commercial Routes
                        </h6>
                        <div class="d-flex gap-2">
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control routes-search" placeholder="Search routes...">
                            </div>
                            <span class="badge bg-light text-dark">{{ $commercialRoutes->flatten()->count() }} routes</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($commercialRoutes->count() > 0)
                            <div class="p-3 border-bottom bg-light">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAllRoutes">
                                    <label class="form-check-label" for="selectAllRoutes">
                                        <i class="fas fa-check-double me-1"></i>Select All Routes
                                    </label>
                                </div>
                            </div>
                            <div class="routes-container p-3" style="max-height: 500px; overflow-y: auto;">
                                @php
                                    $groupedRoutes = [];
                                    foreach($commercialRoutes as $option => $routes) {
                                        foreach($routes as $route) {
                                            $groupKey = "{$route->all_region}-{$route->name_of_route}({$option},{$route->tech_type})";
                                            $groupedRoutes[$groupKey][] = $route;
                                        }
                                    }
                                @endphp

                                @foreach($groupedRoutes as $groupName => $routes)
                                    <div class="mb-3 route-group border rounded">
                                        <div class="route-group-header bg-light p-2 rounded d-flex justify-content-between align-items-center cursor-pointer">
                                            <h6 class="mb-0 text-kp-blue small">
                                                <i class="fas fa-layer-group me-2"></i>{{ $groupName }}
                                                <span class="badge bg-secondary ms-2">{{ count($routes) }}</span>
                                            </h6>
                                            <button type="button" class="btn btn-sm btn-link group-toggle-btn" data-target="group-{{ $loop->index }}">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        </div>
                                        <div class="route-group-body p-2" id="group-{{ $loop->index }}" style="display: none;">
                                            @foreach($routes as $route)
                                                <x-commercial-route-card :route="$route" :designRequest="$designRequest" />
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="p-2 bg-light border-top">
                                <div class="row text-center small">
                                    <div class="col-3">
                                        <span class="fw-bold text-kp-blue">{{ $commercialRoutes->flatten()->count() }}</span>
                                        <span class="text-muted d-block">Total</span>
                                    </div>
                                    <div class="col-3">
                                        <span class="fw-bold text-kp-yellow">{{ $commercialRoutes['Premium']->count() ?? 0 }}</span>
                                        <span class="text-muted d-block">Premium</span>
                                    </div>
                                    <div class="col-3">
                                        <span class="fw-bold text-kp-blue">{{ $commercialRoutes['Non Premium']->count() ?? 0 }}</span>
                                        <span class="text-muted d-block">Non-Premium</span>
                                    </div>
                                    <div class="col-3">
                                        <span class="fw-bold text-info">{{ $commercialRoutes['Metro']->count() ?? 0 }}</span>
                                        <span class="text-muted d-block">Metro</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-route fa-3x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No Commercial Routes Available</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Designer Custom Routes & Colocation Services Row -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-drafting-compass me-2"></i>Custom Routes
                                </h6>
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#customRouteModal">
                                    <i class="fas fa-plus me-1"></i>Add
                                </button>
                            </div>
                            <div class="card-body p-2" style="max-height: 400px; overflow-y: auto;">
                                @if(isset($customRoutes) && $customRoutes->count())
                                    @foreach($customRoutes as $route)
                                        <div class="card mb-2 border-warning">
                                            <div class="card-body p-2">
                                                <div class="form-check">
                                                    <input class="form-check-input custom-route-select"
                                                           type="checkbox"
                                                           name="selected_custom_routes[]"
                                                           value="{{ $route->id }}"
                                                           id="custom_route_{{ $route->id }}"
                                                           data-monthly-cost="{{ $route->monthly_cost }}"
                                                           data-capex="{{ $route->capital_expenditure }}"
                                                           data-duration="{{ $route->contract_duration_months }}">
                                                    <label class="form-check-label w-100" for="custom_route_{{ $route->id }}">
                                                        <div class="d-flex justify-content-between">
                                                            <span class="fw-bold small">{{ $route->name_of_route }}</span>
                                                            <span class="badge bg-warning text-dark">Custom</span>
                                                        </div>
                                                        <div class="small text-muted">
                                                            {{ $route->region ?? 'N/A' }} | {{ $route->option }} | {{ $route->tech_type }}
                                                        </div>
                                                        <div class="small mt-1">
                                                            <span class="text-success">${{ number_format($route->monthly_cost, 2) }}/mo</span>
                                                            <span class="text-muted ms-2">CAPEX: ${{ number_format($route->capital_expenditure, 2) }}</span>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-3">
                                        <i class="fas fa-drafting-compass fa-2x text-muted mb-2"></i>
                                        <p class="text-muted small mb-0">No custom routes created</p>
                                        <button type="button" class="btn btn-sm btn-warning mt-2" data-bs-toggle="modal" data-bs-target="#customRouteModal">
                                            <i class="fas fa-plus me-1"></i>Create Custom Route
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-kp-yellow text-dark d-flex justify-content-between align-items-center py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-server me-2"></i>Colocation
                                </h6>
                                <div class="d-flex gap-2">
                                    <div class="input-group input-group-sm" style="width: 150px;">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control services-search" placeholder="Search...">
                                    </div>
                                    <span class="badge bg-light text-dark">{{ $colocationServices->count() }}</span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                @if($colocationServices->count() > 0)
                                    <div class="p-2 border-bottom bg-light">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAllColocationServices">
                                            <label class="form-check-label small" for="selectAllColocationServices">
                                                Select All Services
                                            </label>
                                        </div>
                                    </div>
                                    <div class="services-container p-2" style="max-height: 350px; overflow-y: auto;">
                                        @php
                                            $groupedServices = [];
                                            foreach($colocationServices as $service) {
                                                $serviceType = $service->service_type ?? $service->servicetype ?? $service->type ?? 'Unknown';
                                                $serviceCategory = $service->service_category ?? $service->servicecategory ?? $service->category ?? 'Uncategorized';
                                                $groupKey = "{$serviceType}-{$serviceCategory}";
                                                $groupedServices[$groupKey][] = $service;
                                            }
                                        @endphp

                                        @foreach($groupedServices as $groupName => $services)
                                            <div class="mb-2 service-group border rounded">
                                                <div class="service-group-header bg-light p-2 d-flex justify-content-between align-items-center cursor-pointer">
                                                    <small class="fw-bold">
                                                        <i class="fas fa-folder me-1"></i>{{ $groupName }}
                                                        <span class="badge bg-secondary ms-1">{{ count($services) }}</span>
                                                    </small>
                                                    <button type="button" class="btn btn-sm btn-link p-0 group-toggle-btn" data-target="service-group-{{ $loop->index }}">
                                                        <i class="fas fa-chevron-down small"></i>
                                                    </button>
                                                </div>
                                                <div class="service-group-body p-2" id="service-group-{{ $loop->index }}" style="display: none;">
                                                    @foreach($services as $service)
                                                        @php
                                                            $serviceId = $service->service_id ?? $service->id ?? $service->serviceid ?? uniqid();
                                                            $serviceType = $service->service_type ?? $service->servicetype ?? $service->type ?? $service->name ?? 'Unknown Service';
                                                            $serviceCategory = $service->service_category ?? $service->servicecategory ?? $service->category ?? 'Uncategorized';
                                                            $monthlyPriceUsd = floatval($service->monthly_price_usd ?? $service->monthly_price ?? $service->monthly_rate ?? 0);
                                                            $recurrentPerAnnum = floatval($service->recurrent_per_Annum ?? $service->recurrent_per_annum ?? $service->annual_rate ?? 0);
                                                            $setupFeeUsd = floatval($service->setup_fee_usd ?? $service->setup_fee ?? 0);
                                                            $oneoffRate = floatval($service->oneoff_rate ?? $service->oneoffrate ?? 0);
                                                            $monthlyRateNumeric = $monthlyPriceUsd > 0 ? $monthlyPriceUsd : ($recurrentPerAnnum > 0 ? $recurrentPerAnnum / 12 : 0);
                                                            $setupFeeNumeric = $setupFeeUsd > 0 ? $setupFeeUsd : ($oneoffRate > 0 ? $oneoffRate : 0);
                                                            $minContractMonths = intval($service->min_contract_months ?? $service->min_contract ?? 12);
                                                        @endphp
                                                        <div class="card mb-2 service-card">
                                                            <div class="card-body p-2">
                                                                <div class="form-check">
                                                                    <input class="form-check-input service-select"
                                                                           type="checkbox"
                                                                           name="selected_services[]"
                                                                           value="{{ $serviceId }}"
                                                                           id="service_{{ $serviceId }}"
                                                                           data-service-id="{{ $serviceId }}"
                                                                           data-monthly-rate="{{ $monthlyRateNumeric }}"
                                                                           data-setup-fee="{{ $setupFeeNumeric }}">
                                                                    <label class="form-check-label w-100" for="service_{{ $serviceId }}">
                                                                        <div class="d-flex justify-content-between">
                                                                            <strong class="small">{{ $serviceType }}</strong>
                                                                            <span class="text-success small">${{ number_format($monthlyRateNumeric, 2) }}/mo</span>
                                                                        </div>
                                                                        <div class="service-configuration mt-2 p-2 bg-light rounded" style="display: none;">
                                                                            <div class="row g-1">
                                                                                <div class="col-6">
                                                                                    <label class="small">Duration (Months)</label>
                                                                                    <input type="number" name="service_duration[{{ $serviceId }}]" class="form-control form-control-sm service-duration-input" value="{{ $minContractMonths }}" min="{{ $minContractMonths }}" data-service-id="{{ $serviceId }}">
                                                                                </div>
                                                                                <div class="col-6">
                                                                                    <label class="small">Quantity</label>
                                                                                    <input type="number" name="service_quantity[{{ $serviceId }}]" class="form-control form-control-sm service-quantity-input" value="1" min="1" data-service-id="{{ $serviceId }}">
                                                                                </div>
                                                                            </div>
                                                                            <div class="d-flex justify-content-between mt-1 small">
                                                                                <span>Monthly:</span>
                                                                                <strong class="monthly-cost text-success" data-service-id="{{ $serviceId }}">${{ number_format($monthlyRateNumeric, 2) }}</strong>
                                                                            </div>
                                                                            <div class="d-flex justify-content-between small">
                                                                                <span>Setup:</span>
                                                                                <span>${{ number_format($setupFeeNumeric, 2) }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-server fa-2x text-muted mb-2"></i>
                                        <p class="text-muted small mb-0">No services available</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Items -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>Custom Items
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div id="customItemsContainer"></div>
                        <button type="button" id="addCustomItem" class="btn btn-outline-secondary btn-sm mt-2">
                            <i class="fas fa-plus me-1"></i>Add Custom Item
                        </button>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Pricing & Details (30%) - Sticky -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 20px;">
                    <!-- Live Pricing Summary -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-kp-blue text-white py-2">
                            <h6 class="mb-0">
                                <i class="fas fa-calculator me-2"></i>Live Pricing Summary
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between mb-2 pb-1 border-bottom">
                                <span class="small">Commercial/Custom Routes</span>
                                <strong id="routesTotal">$0.00</strong>
                            </div>
                            {{-- <div class="d-flex justify-content-between mb-2 pb-1 border-bottom">
                                <span class="small">Custom Routes</span>
                                <strong class="text-warning" id="customRoutesTotal">$0.00</strong>
                            </div> --}}
                            <div class="d-flex justify-content-between mb-2 pb-1 border-bottom">
                                <span class="small">Colocation Services</span>
                                <strong id="servicesTotal">$0.00</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-1 border-bottom">
                                <span class="small">Custom Items</span>
                                <strong id="customItemsTotal">$0.00</strong>
                            </div>
                            <div class="d-flex justify-content-between mt-2 pt-1 border-top">
                                <span class="fw-bold">Subtotal</span>
                                <span class="fw-bold h6 mb-0 text-kp-blue" id="subtotal">$0.00</span>
                            </div>
                            <div class="row mt-2 g-1">
                                <div class="col-7">
                                    <label class="small text-muted mb-0">Tax Rate (%)</label>
                                    <input type="number" name="tax_rate" id="tax_rate" value="0.16" min="0" max="0.5" step="0.01" class="form-control form-control-sm">
                                </div>
                                <div class="col-5 text-end">
                                    <label class="small text-muted mb-0">Tax Amount</label>
                                    <div class="fw-bold" id="tax_amount">$0.00</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                                <span class="h6 mb-0 fw-bold">Total Amount</span>
                                <span class="h5 mb-0 fw-bold text-kp-blue" id="total_amount">$0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quotation Details - Accordion Style -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white py-2 cursor-pointer" data-bs-toggle="collapse" data-bs-target="#quotationDetails">
                            <h6 class="mb-0">
                                <i class="fas fa-file-contract me-2"></i>Quotation Details
                                <i class="fas fa-chevron-down float-end"></i>
                            </h6>
                        </div>
                        <div class="collapse show" id="quotationDetails">
                            <div class="card-body p-3">
                                <div class="mb-2">
                                    <label class="small fw-bold">Scope of Work</label>
                                    <div class="btn-group btn-group-sm mb-1 w-100">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadDefaultScope()">Quick Fill</button>
                                        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#scopeTemplatesModal">Templates</button>
                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="formatScopeOfWork()">Format</button>
                                    </div>
                                    <textarea name="scope_of_work" id="scope_of_work" class="form-control form-control-sm" rows="3" required>{{ old('scope_of_work', 'SCOPE OF WORK') }}</textarea>
                                    <small class="text-muted"><span id="scope_counter">0</span> characters</small>
                                </div>

                                <div class="mb-2">
                                    <label class="small fw-bold">Terms & Conditions</label>
                                    <textarea name="terms_and_conditions" id="terms_and_conditions" class="form-control form-control-sm" rows="3">{{ old('terms_and_conditions', $defaultTerms ?? '') }}</textarea>
                                </div>

                                <div class="mb-2">
                                    <label class="small fw-bold">Pricing Notes</label>
                                    <textarea name="pricing_notes" id="pricing_notes" class="form-control form-control-sm" rows="2" placeholder="Discounts, payment plans..."></textarea>
                                </div>

                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="small fw-bold">Valid Until</label>
                                        <input type="date" name="valid_until" id="valid_until" class="form-control form-control-sm" value="{{ \Carbon\Carbon::now()->addDays(30)->format('Y-m-d') }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="small fw-bold">Validity (days)</label>
                                        <input type="number" name="validity_days" id="validity_days" class="form-control form-control-sm" value="30">
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-1">
                                    <input class="form-check-input" type="checkbox" name="include_tax" id="include_tax" checked>
                                    <label class="form-check-label small" for="include_tax">Include Tax</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="allow_negotiation" id="allow_negotiation" checked>
                                    <label class="form-check-label small" for="allow_negotiation">Allow Negotiation</label>
                                </div>

                                <div class="mb-2">
                                    <label class="small fw-bold">Special Instructions</label>
                                    <textarea name="special_instructions" id="special_instructions" class="form-control form-control-sm" rows="2" placeholder="Internal notes..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-grid gap-2">
                                <button type="submit" name="action" value="draft" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-save me-2"></i>Save as Draft
                                </button>
                                @if(!in_array(auth()->user()->role, ['account_manager', 'designer']))
                                    <button type="submit" name="action" value="send" class="btn btn-kp-primary btn-sm">
                                        <i class="fas fa-paper-plane me-2"></i>Save & Send
                                    </button>
                                @endif
                                <a href="{{ route('admin.design-requests.index') }}" class="btn btn-link text-muted small">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Requests
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden fields -->
        <div style="display: none;">
            <input type="hidden" name="routes_total" id="hidden_routes_total" value="0">
            <input type="hidden" name="services_total" id="hidden_services_total" value="0">
            <input type="hidden" name="custom_items_total" id="hidden_custom_items_total" value="0">
            <input type="hidden" name="subtotal" id="hidden_subtotal" value="0">
            <input type="hidden" name="tax_amount" id="hidden_tax_amount" value="0">
            <input type="hidden" name="total_amount" id="hidden_total_amount" value="0">
        </div>
    </form>
</div>

    <!-- Scope of Work Templates Modal -->
    <div class="modal fade" id="scopeTemplatesModal" tabindex="-1" aria-labelledby="scopeTemplatesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-kp-blue text-white">
                <h5 class="modal-title" id="scopeTemplatesModalLabel">
                    <i class="fas fa-templates me-2"></i>Scope of Work Templates
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <!-- Standard Colocation Template -->
                    <div class="col-md-6">
                        <div class="card h-100 template-card border-kp-blue" onclick="loadTemplate('standard_colocation')">
                            <div class="card-header bg-kp-blue text-white py-2">
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
                        <div class="card h-100 template-card border-kp-yellow" onclick="loadTemplate('premium_colocation')">
                            <div class="card-header bg-kp-yellow text-dark py-2">
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
                        <div class="card h-100 template-card border-kp-green" onclick="loadTemplate('managed_services')">
                            <div class="card-header bg-kp-green text-white py-2">
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
                <button type="button" class="btn btn-outline-kp-primary" onclick="saveCustomTemplate()">
                    <i class="fas fa-save me-1"></i>Save as Custom Template
                </button>
            </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="customRouteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('designer.custom-routes.store') }}" method="POST" class="modal-content">
            @csrf

            <input type="hidden" name="design_request_id" value="{{ $designRequest->id }}">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="fas fa-drafting-compass me-2"></i>Create Designer Custom Route
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-info">
                    Use this when the route does not exist in the commercial routes table.
                </div>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Route Name</label>
                        <input type="text" name="name_of_route" class="form-control" required
                               placeholder="Example: EADC to New Customer POP">
                    </div>

                    <div class="col-md-4 mb-3">
    <label class="form-label">Region / County</label>
    <select name="region" class="form-select" required>
        <option value="">Select Region</option>
        @foreach($counties as $county)
            <option value="{{ $county->region ?? $county->name }}">
                {{ $county->name }} {{ $county->code ? '(' . $county->code . ')' : '' }}
            </option>
        @endforeach
    </select>
    </div>

                    <div class="col-md-4 mb-3">
    <label class="form-label">Route Option</label>
    <select name="option" id="custom_route_option" class="form-select" required>
        <option value="Non Premium" data-unit-cost="18">Non Premium - USD 18</option>
        <option value="Premium" data-unit-cost="19">Premium - USD 19</option>
        <option value="Metro" data-unit-cost="20">Metro - USD 20</option>
    </select>
    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Technology</label>
                        <select name="tech_type" class="form-select" required>
                            <option value="ADSS">ADSS</option>
                            <option value="OPGW">OPGW</option>
                            <option value="UG">UG</option>
                            <option value="OPGW/ADSS">OPGW/ADSS</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Currency</label>
                        <select name="currency" class="form-select" required>
                            <option value="USD">USD</option>
                            <option value="KES">KES</option>
                        </select>
                    </div>

                   <div class="col-md-3 mb-3">
    <label class="form-label">Total Fiber Cores</label>
    <input type="number" name="fiber_cores" class="form-control" min="1" value="48">
    </div>

    <div class="col-md-3 mb-3">
    <label class="form-label">Contract Duration Months</label>
    <input type="number"
           name="contract_duration_months"
           id="custom_route_duration"
           class="form-control"
           value="12"
           min="1"
           max="360"
           required>
    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Cores Required</label>
                        <input type="number" name="no_of_cores_required" class="form-control" value="1" min="1" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Distance KM</label>
                        <input type="number" name="approx_distance_km" class="form-control" step="0.01" min="0" required>
                    </div>

                    <div class="col-md-3 mb-3">
    <label class="form-label">Unit Cost / Core / KM / Month</label>
    <input type="number"
           name="unit_cost_per_core_per_km_per_month"
           id="custom_route_unit_cost"
           class="form-control"
           step="0.01"
           min="0"
           required
           readonly>
    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">CAPEX</label>
                        <input type="number" name="capital_expenditure" class="form-control" step="0.01" min="0" value="0">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Availability</label>
                        <select name="availability" class="form-select" required>
                            <option value="YES">YES</option>
                            <option value="NO">NO</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
    <label class="form-label">Estimated Monthly Cost</label>
    <input type="text" id="customRoutePreviewCost" class="form-control bg-light" readonly value="0.00">
    </div>

    <div class="col-md-4 mb-3">
    <label class="form-label">Estimated Contract Value</label>
    <input type="text" id="customRoutePreviewTotal" class="form-control bg-light" readonly value="0.00">
    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Route Description</label>
                        <textarea name="route_description" class="form-control" rows="2"
                                  placeholder="Describe start point, end point, route assumptions..."></textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Designer Notes</label>
                        <textarea name="design_notes" class="form-control" rows="2"
                                  placeholder="Add design assumptions, risks, missing survey details, or special instructions..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save me-1"></i>Save Custom Route
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .route-group, .service-group {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 15px;
}

.route-group-header, .service-group-header {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.route-group-header:hover, .service-group-header:hover {
    background-color: #f1f3f4 !important;
}

.route-group-body, .service-group-body {
    padding: 0 15px;
}

.route-group-body .route-card, .service-group-body .service-card {
    margin-bottom: 10px;
    border: 1px solid #dee2e6 !important;
}
.route-group-card {
    border-left: 4px solid #ffc107;
    transition: all 0.3s ease;
}

.route-group-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.route-card {
    border: 1px solid #e9ecef;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.route-card:hover {
    border-color: #17a2b8;
    background-color: #f8f9fa;
}

.group-toggle {
    transition: transform 0.3s ease;
}

.route-info {
    font-size: 0.8rem;
}

.route-configuration {
    border-top: 1px dashed #dee2e6;
    padding-top: 8px;
    margin-top: 8px;
}

/* Indeterminate checkbox styling */
.group-select:indeterminate {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/* Smooth collapse transitions */
.collapse {
    transition: all 0.3s ease;
}

/* Search highlight */
.highlight {
    background-color: #ffeb3b;
    padding: 1px 2px;
    border-radius: 2px;
    font-weight: bold;
}

.route-group-card .card-header {
    cursor: pointer;
}

.collapsing {
    transition: height 0.35s ease;
}

/* Route and service cards */
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

.text-kp-yellow {
    color: #ffc107 !important;
}

/* Service Card Specific Styles */
.service-card {
    transition: all 0.3s ease;
    border-left-width: 3px !important;
}

.service-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.service-header h6 {
    font-size: 0.95rem;
}

.service-details {
    font-size: 0.85rem;
}

.border-left-primary {
    border-left-color: #0d6efd !important;
}

.border-left-info {
    border-left-color: #0dcaf0 !important;
}

.service-configuration {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.cursor-pointer {
    cursor: pointer;
}

.sticky-top {
    position: sticky;
    z-index: 1020;
}

/* Compact cards */
.card-header .btn-sm {
    padding: 0.2rem 0.5rem;
    font-size: 0.75rem;
}

/* Better spacing for mobile */
@media (max-width: 768px) {
    .sticky-top {
        position: relative;
        top: 0;
    }

    .btn-group-sm .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.7rem;
    }
}

/* Route and service group styling */
.route-group, .service-group {
    border: 1px solid #e9ecef;
    border-radius: 6px;
    margin-bottom: 8px;
}

.route-group-header, .service-group-header {
    background-color: #f8f9fa;
}

/* Custom item styling */
.custom-item {
    background: #f8f9fa;
    padding: 8px;
    border-radius: 6px;
    margin-bottom: 8px;
}

/* Scrollbar styling */
.routes-container::-webkit-scrollbar,
.services-container::-webkit-scrollbar {
    width: 6px;
}

.routes-container::-webkit-scrollbar-track,
.services-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.routes-container::-webkit-scrollbar-thumb,
.services-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    initializeSearch();
    initializeGroupToggles();
    initializeCharacterCounters();
    initializeDateSync();
    initializeRouteSelection();
    initializeCustomRouteSelection();
    initializeServiceSelection();
    initializeCustomItems();
    initializeTaxCalculation();
    initializeCustomRouteModalPreview();
    initializeFormSubmitValidation();

    calculateTotals();
});

function initializeSearch() {
    document.querySelectorAll('.routes-search').forEach(searchBox => {
        searchBox.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase().trim();
            const routeCards = document.querySelectorAll('.routes-container .route-card');
            let visibleCount = 0;

            routeCards.forEach(card => {
                const isVisible = card.textContent.toLowerCase().includes(searchTerm);
                card.style.display = isVisible ? 'block' : 'none';
                if (isVisible) visibleCount++;
            });

            document.querySelectorAll('.route-group').forEach(group => {
                const hasVisible = Array.from(group.querySelectorAll('.route-card'))
                    .some(card => card.style.display !== 'none');

                group.style.display = hasVisible ? 'block' : 'none';
            });

            const badge = document.querySelector('.card-header.bg-kp-blue .badge');
            if (badge) badge.textContent = `${visibleCount} routes filtered`;
        });
    });

    document.querySelectorAll('.services-search').forEach(searchBox => {
        searchBox.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase().trim();
            const serviceCards = document.querySelectorAll('.services-container .service-card');
            let visibleCount = 0;

            serviceCards.forEach(card => {
                const isVisible = card.textContent.toLowerCase().includes(searchTerm);
                card.style.display = isVisible ? 'block' : 'none';
                if (isVisible) visibleCount++;
            });

            document.querySelectorAll('.service-group').forEach(group => {
                const hasVisible = Array.from(group.querySelectorAll('.service-card'))
                    .some(card => card.style.display !== 'none');

                group.style.display = hasVisible ? 'block' : 'none';
            });

            const badge = document.querySelector('.card-header.bg-kp-yellow .badge');
            if (badge) badge.textContent = `${visibleCount} services filtered`;
        });
    });
}

function initializeGroupToggles() {
    document.querySelectorAll('.group-toggle-btn').forEach(button => {
        button.addEventListener('click', function () {
            const targetElement = document.getElementById(this.dataset.target);
            const icon = this.querySelector('i');

            if (!targetElement || !icon) return;

            const isHidden = targetElement.style.display === 'none';

            targetElement.style.display = isHidden ? 'block' : 'none';
            icon.classList.toggle('fa-chevron-down', isHidden);
            icon.classList.toggle('fa-chevron-up', !isHidden);
        });
    });
}

function initializeCharacterCounters() {
    updateCharacterCount('scope_counter', document.getElementById('scope_of_work'));
    updateCharacterCount('terms_counter', document.getElementById('terms_and_conditions'));
    updateCharacterCount('pricing_counter', document.getElementById('pricing_notes'));
    updateCharacterCount('instructions_counter', document.getElementById('special_instructions'));
}

function initializeDateSync() {
    const validUntil = document.getElementById('valid_until');
    const validityDays = document.getElementById('validity_days');

    if (validUntil) validUntil.addEventListener('change', updateValidityDays);
    if (validityDays) validityDays.addEventListener('change', updateValidUntilDate);
}

function calculateRouteCost(routeId) {
    const checkbox = document.querySelector(`#route_${routeId}`);
    if (!checkbox) return 0;

    const monthlyCost = parseFloat(checkbox.dataset.monthlyCost) || 0;
    const capitalExpenditure = parseFloat(checkbox.dataset.capitalExpenditure) || 0;

    const coresInput = document.querySelector(`input[name="route_cores[${routeId}]"]`);
    const durationInput = document.querySelector(`input[name="route_duration[${routeId}]"]`);

    const cores = parseInt(coresInput?.value) || 1;
    const duration = parseInt(durationInput?.value) || 12;

    const newMonthlyCost = monthlyCost * cores;
    const total = (newMonthlyCost * duration) + capitalExpenditure;

    const monthlyCostElement = document.querySelector(`.monthly-cost[data-route-id="${routeId}"]`);
    const totalCostElement = document.querySelector(`.total-cost[data-route-id="${routeId}"]`);

    if (monthlyCostElement) monthlyCostElement.textContent = `$${newMonthlyCost.toFixed(2)}`;
    if (totalCostElement) totalCostElement.textContent = `$${total.toFixed(2)}`;

    return total;
}

function calculateServiceCost(serviceId) {
    const checkbox = document.querySelector(`#service_${serviceId}`);
    if (!checkbox) return 0;

    const monthlyRate = parseFloat(checkbox.dataset.monthlyRate) || 0;
    const setupFee = parseFloat(checkbox.dataset.setupFee) || 0;
    const oneoffFee = parseFloat(checkbox.dataset.oneoffFee) || 0;

    const durationInput = document.querySelector(`input[name="service_duration[${serviceId}]"]`);
    const quantityInput = document.querySelector(`input[name="service_quantity[${serviceId}]"]`);

    const duration = parseInt(durationInput?.value) || 12;
    const quantity = parseInt(quantityInput?.value) || 1;

    const monthlyCost = monthlyRate * quantity;
    const setupCost = setupFee * quantity;
    const oneoffCost = oneoffFee * quantity;
    const totalCost = (monthlyCost * duration) + setupCost + oneoffCost;

    const monthlyCostElement = document.querySelector(`.monthly-cost[data-service-id="${serviceId}"]`);
    const totalCostElement = document.querySelector(`.total-cost[data-service-id="${serviceId}"]`);

    if (monthlyCostElement) monthlyCostElement.textContent = `$${monthlyCost.toFixed(2)}`;
    if (totalCostElement) totalCostElement.textContent = `$${totalCost.toFixed(2)}`;

    return totalCost;
}

function calculateTotals() {
    let routesTotal = 0;
    let servicesTotal = 0;
    let customItemsTotal = 0;

    document.querySelectorAll('.route-select:checked').forEach(checkbox => {
        const routeId = checkbox.dataset.routeId;
        if (routeId) routesTotal += calculateRouteCost(routeId);
    });

    document.querySelectorAll('.custom-route-select:checked').forEach(checkbox => {
    const monthlyCost = parseFloat(checkbox.dataset.monthlyCost || 0);
    const capex = parseFloat(checkbox.dataset.capex || 0);
    const duration = parseInt(checkbox.dataset.duration || 12);

    routesTotal += (monthlyCost * duration) + capex;
});

    document.querySelectorAll('.service-select:checked').forEach(checkbox => {
        const serviceId = checkbox.dataset.serviceId;
        if (serviceId) servicesTotal += calculateServiceCost(serviceId);
    });

    document.querySelectorAll('input[name^="custom_items"][name$="[total]"]').forEach(input => {
        customItemsTotal += parseFloat(input.value) || 0;
    });

    const subtotal = routesTotal + servicesTotal + customItemsTotal;
    const taxRate = parseFloat(document.getElementById('tax_rate')?.value) || 0;
    const taxAmount = subtotal * taxRate;
    const totalAmount = subtotal + taxAmount;

    setText('routesTotal', `$${routesTotal.toFixed(2)}`);
    setText('servicesTotal', `$${servicesTotal.toFixed(2)}`);
    setText('customItemsTotal', `$${customItemsTotal.toFixed(2)}`);
    setText('subtotal', `$${subtotal.toFixed(2)}`);
    setText('tax_amount', `$${taxAmount.toFixed(2)}`);
    setText('total_amount', `$${totalAmount.toFixed(2)}`);

    setValue('hidden_routes_total', routesTotal.toFixed(2));
    setValue('hidden_services_total', servicesTotal.toFixed(2));
    setValue('hidden_custom_items_total', customItemsTotal.toFixed(2));
    setValue('hidden_subtotal', subtotal.toFixed(2));
    setValue('hidden_tax_amount', taxAmount.toFixed(2));
    setValue('hidden_total_amount', totalAmount.toFixed(2));
}

function initializeRouteSelection() {
    document.querySelectorAll('.route-select').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const routeId = this.dataset.routeId;
            const configDiv = this.closest('.route-card')?.querySelector('.route-configuration');

            if (configDiv) configDiv.style.display = this.checked ? 'block' : 'none';
            if (this.checked && routeId) calculateRouteCost(routeId);

            calculateTotals();
        });
    });

    document.querySelectorAll('input[name^="route_cores"], input[name^="route_duration"]').forEach(input => {
        input.addEventListener('input', function () {
            const matches = this.name.match(/\[(\d+)\]/);
            if (!matches) return;

            const routeId = matches[1];
            const checkbox = document.querySelector(`#route_${routeId}`);

            if (checkbox?.checked) {
                calculateRouteCost(routeId);
                calculateTotals();
            }
        });
    });

    const selectAllRoutes = document.getElementById('selectAllRoutes');

    if (selectAllRoutes) {
        selectAllRoutes.addEventListener('change', function () {
            document.querySelectorAll('.routes-container .route-select').forEach(checkbox => {
                checkbox.checked = this.checked;
                checkbox.dispatchEvent(new Event('change'));
            });
        });
    }
}

function initializeCustomRouteSelection() {
    document.querySelectorAll('.custom-route-select').forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotals);
    });
}

function initializeServiceSelection() {
    document.querySelectorAll('.service-select').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const serviceId = this.dataset.serviceId;
            const configDiv = this.closest('.service-card')?.querySelector('.service-configuration');

            if (configDiv) configDiv.style.display = this.checked ? 'block' : 'none';
            if (this.checked && serviceId) calculateServiceCost(serviceId);

            calculateTotals();
        });
    });

    document.querySelectorAll('.service-duration-input, .service-quantity-input').forEach(input => {
        input.addEventListener('input', function () {
            const serviceId = this.dataset.serviceId;
            const checkbox = document.querySelector(`#service_${serviceId}`);

            if (checkbox?.checked) {
                calculateServiceCost(serviceId);
                calculateTotals();
            }
        });
    });

    const selectAllServices = document.getElementById('selectAllColocationServices');

    if (selectAllServices) {
        selectAllServices.addEventListener('change', function () {
            document.querySelectorAll('.services-container .service-select').forEach(checkbox => {
                checkbox.checked = this.checked;
                checkbox.dispatchEvent(new Event('change'));
            });
        });
    }
}

function initializeCustomItems() {
    let customItemIndex = 0;
    const addCustomItemBtn = document.getElementById('addCustomItem');

    if (!addCustomItemBtn) return;

    addCustomItemBtn.addEventListener('click', function () {
        const container = document.getElementById('customItemsContainer');
        if (!container) return;

        const newItem = document.createElement('div');
        newItem.className = 'custom-item row mb-3';
        newItem.innerHTML = `
            <div class="col-md-4">
                <input type="text" name="custom_items[${customItemIndex}][description]" class="form-control" placeholder="Item description">
            </div>
            <div class="col-md-2">
                <input type="number" name="custom_items[${customItemIndex}][quantity]" class="form-control custom-item-qty" value="1" min="1">
            </div>
            <div class="col-md-2">
                <input type="number" name="custom_items[${customItemIndex}][unit_price]" class="form-control custom-item-price" step="0.01" min="0">
            </div>
            <div class="col-md-2">
                <input type="text" name="custom_items[${customItemIndex}][total]" class="form-control custom-item-total" readonly>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
            </div>
        `;

        container.appendChild(newItem);

        const calculateItemTotal = () => {
            const qty = parseFloat(newItem.querySelector('.custom-item-qty')?.value || 0);
            const price = parseFloat(newItem.querySelector('.custom-item-price')?.value || 0);
            const total = qty * price;

            const totalInput = newItem.querySelector('.custom-item-total');
            if (totalInput) totalInput.value = total.toFixed(2);

            calculateTotals();
        };

        newItem.querySelector('.custom-item-qty')?.addEventListener('input', calculateItemTotal);
        newItem.querySelector('.custom-item-price')?.addEventListener('input', calculateItemTotal);
        newItem.querySelector('.remove-item')?.addEventListener('click', function () {
            newItem.remove();
            calculateTotals();
        });

        customItemIndex++;
    });
}

function initializeTaxCalculation() {
    document.getElementById('tax_rate')?.addEventListener('input', calculateTotals);
}

function initializeCustomRouteModalPreview() {
    const modal = document.getElementById('customRouteModal');
    if (!modal) return;

    const optionInput = document.getElementById('custom_route_option');
    const unitCostInput = document.getElementById('custom_route_unit_cost');
    const distanceInput = modal.querySelector('[name="approx_distance_km"]');
    const coresInput = modal.querySelector('[name="no_of_cores_required"]');
    const durationInput = document.getElementById('custom_route_duration');
    const capexInput = modal.querySelector('[name="capital_expenditure"]');
    const monthlyPreview = document.getElementById('customRoutePreviewCost');
    const totalPreview = document.getElementById('customRoutePreviewTotal');

    function applyOptionRate() {
        const selected = optionInput.options[optionInput.selectedIndex];
        const unitCost = parseFloat(selected.dataset.unitCost || 0);

        unitCostInput.value = unitCost.toFixed(2);
        updateCustomRoutePreview();
    }

    function updateCustomRoutePreview() {
        const distance = parseFloat(distanceInput?.value || 0);
        const cores = parseInt(coresInput?.value || 0);
        const unitCost = parseFloat(unitCostInput?.value || 0);
        const duration = parseInt(durationInput?.value || 12);
        const capex = parseFloat(capexInput?.value || 0);

        const monthly = distance * cores * unitCost;
        const total = (monthly * duration) + capex;

        if (monthlyPreview) {
            monthlyPreview.value = monthly.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        if (totalPreview) {
            totalPreview.value = total.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }

    [distanceInput, coresInput, durationInput, capexInput].forEach(input => {
        if (input) input.addEventListener('input', updateCustomRoutePreview);
    });

    if (optionInput) {
        optionInput.addEventListener('change', applyOptionRate);
        applyOptionRate();
    }
}

function initializeFormSubmitValidation() {
    const form = document.getElementById('quotationForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const scopeTextarea = document.getElementById('scope_of_work');

        if (scopeTextarea && scopeTextarea.value.trim() === 'undefined') {
            e.preventDefault();
            alert('Please enter a valid Scope of Work or use the Quick Fill button.');
            scopeTextarea.focus();
            return;
        }

        calculateTotals();
    });
}

function setText(id, value) {
    const element = document.getElementById(id);
    if (element) element.textContent = value;
}

function setValue(id, value) {
    const element = document.getElementById(id);
    if (element) element.value = value;
}

function updateCharacterCount(counterId, textarea) {
    const counter = document.getElementById(counterId);

    if (counter && textarea) {
        counter.textContent = textarea.value.length;
    }
}

const scopeTemplates = {
    standard_colocation: `SCOPE OF WORK

PROJECT OVERVIEW:
Provision of standard colocation services including rack space, power allocation, and network connectivity.

INCLUDED SERVICES:
• Rack/cabinet allocation
• Power provisioning
• Basic connectivity
• Standard support

DELIVERABLES:
• Service activation confirmation
• Access procedures
• Support and escalation matrix`,

    premium_colocation: `SCOPE OF WORK

PROJECT OVERVIEW:
Provision of premium colocation services with enhanced support, dedicated cabinet allocation, and higher availability.

INCLUDED SERVICES:
• Dedicated cabinet/rack space
• A/B power where available
• Premium connectivity
• 24/7 support

DELIVERABLES:
• Premium service documentation
• SLA and escalation matrix
• Installation and handover report`,

    managed_services: `SCOPE OF WORK

PROJECT OVERVIEW:
Provision of managed infrastructure services including monitoring, support, maintenance, and reporting.

INCLUDED SERVICES:
• Monitoring
• Preventive maintenance
• Incident support
• Reporting

DELIVERABLES:
• Monthly reports
• Support tickets summary
• Maintenance schedule`,

    dark_fibre: `SCOPE OF WORK

PROJECT OVERVIEW:
Provision of dedicated dark fibre connectivity between agreed endpoints.

INCLUDED SERVICES:
• Dark fibre route provisioning
• Fibre testing
• Handover documentation
• SLA support

DELIVERABLES:
• Fibre route diagram
• OTDR test results where applicable
• Service handover certificate`,

    custom_solution: `SCOPE OF WORK

PROJECT OVERVIEW:
Provision of a customized network infrastructure solution based on customer-specific requirements.

INCLUDED SERVICES:
• Custom route/service design
• Technical evaluation
• Implementation planning
• Handover documentation

DELIVERABLES:
• Custom design documentation
• Commercial proposal
• SLA documentation`,

    empty: `SCOPE OF WORK

PROJECT OVERVIEW:

INCLUDED SERVICES:

DELIVERABLES:

ASSUMPTIONS:

EXCLUSIONS:`
};

const defaultTerms = `TERMS AND CONDITIONS

1. PAYMENT TERMS:
   1.1. Payment is due within 30 days of invoice date
   1.2. Late payments may attract penalties
   1.3. Prices are quoted in USD unless stated otherwise

2. QUOTATION VALIDITY:
   2.1. This quotation is valid for 30 days
   2.2. Acceptance must be in writing

3. SERVICE DELIVERY:
   3.1. Delivery is subject to site survey and availability
   3.2. Scope changes may affect pricing and timelines

4. TERMINATION:
   4.1. Written notice is required for cancellation
   4.2. Setup fees are non-refundable once work has commenced`;

function loadDefaultScope() {
    const scopeTextarea = document.getElementById('scope_of_work');

    if (scopeTextarea) {
        scopeTextarea.value = scopeTemplates.dark_fibre;
        updateCharacterCount('scope_counter', scopeTextarea);
    }
}

function loadTemplate(templateType) {
    const scopeTextarea = document.getElementById('scope_of_work');

    if (scopeTextarea) {
        scopeTextarea.value = scopeTemplates[templateType] || scopeTemplates.dark_fibre;
        updateCharacterCount('scope_counter', scopeTextarea);

        const modalElement = document.getElementById('scopeTemplatesModal');
        const modal = bootstrap.Modal.getInstance(modalElement);

        if (modal) modal.hide();
    }
}

function loadDefaultTerms() {
    const termsTextarea = document.getElementById('terms_and_conditions');

    if (termsTextarea) {
        termsTextarea.value = defaultTerms;
        updateCharacterCount('terms_counter', termsTextarea);
    }
}

function formatScopeOfWork() {
    const textarea = document.getElementById('scope_of_work');
    if (!textarea) return;

    textarea.value = textarea.value
        .replace(/\n\s*\n\s*\n/g, '\n\n')
        .replace(/^[•\-]\s*/gm, '• ')
        .replace(/\s+$/gm, '');

    updateCharacterCount('scope_counter', textarea);
}

function insertPricingTemplate() {
    const textarea = document.getElementById('pricing_notes');

    if (textarea) {
        textarea.value = `PRICING NOTES:

• All prices are exclusive of VAT unless specified
• Setup fees are one-time and non-refundable
• Monthly recurring charges are billed in advance
• Prices are valid within the stated quotation validity period`;

        updateCharacterCount('pricing_counter', textarea);
    }
}

function updateValidUntilDate() {
    const daysInput = document.getElementById('validity_days');
    const validUntilInput = document.getElementById('valid_until');

    if (!daysInput || !validUntilInput) return;

    const days = parseInt(daysInput.value) || 30;
    const validUntil = new Date();

    validUntil.setDate(validUntil.getDate() + days);
    validUntilInput.value = validUntil.toISOString().split('T')[0];
}

function updateValidityDays() {
    const validUntilInput = document.getElementById('valid_until');
    const daysInput = document.getElementById('validity_days');

    if (!validUntilInput || !daysInput) return;

    const validUntil = new Date(validUntilInput.value);
    const today = new Date();
    const diffTime = validUntil - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    daysInput.value = diffDays > 0 ? diffDays : 30;
}

function setDefaultValidity() {
    const daysInput = document.getElementById('validity_days');

    if (daysInput) {
        daysInput.value = 30;
        updateValidUntilDate();
    }
}
</script>
@endpush
