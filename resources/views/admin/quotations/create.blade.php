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
<!-- Services Selection Section -->
<div class="row">
    <!-- Commercial Routes -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-route me-2"></i>Commercial Routes
                </h5>

                <span class="badge bg-light text-dark">{{ $commercialRoutes->flatten()->count() }} routes available</span>
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

                        <!-- Add to COMMERCIAL ROUTES card header (after <h5> tag) -->
<div class="ms-auto" style="max-width: 250px;">
    <div class="input-group input-group-sm">
        <span class="input-group-text"><i class="fas fa-search"></i></span>
        <input type="text"
               class="form-control routes-search"
               placeholder="Search routes...">
    </div>
</div>
                    </div>

                    <div class="routes-container" style="max-height: 500px; overflow-y: auto;">
                        @php
                            // Group routes by all-region-name_of_route(option,tech_type)
                            $groupedRoutes = [];
                            foreach($commercialRoutes as $option => $routes) {
                                foreach($routes as $route) {
                                    $groupKey = "{$route->all_region}-{$route->name_of_route}({$option},{$route->tech_type})";
                                    $groupedRoutes[$groupKey][] = $route;
                                }
                            }
                        @endphp

                        @foreach($groupedRoutes as $groupName => $routes)
                            <div class="mb-4 route-group">
                                <div class="route-group-header bg-light p-3 rounded mb-2 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-layer-group me-2"></i>{{ $groupName }}
                                        <small class="text-muted">({{ count($routes) }} routes)</small>
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary group-toggle-btn"
                                            data-target="group-{{ $loop->index }}">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>

                                <div class="route-group-body" id="group-{{ $loop->index }}">
                                    @foreach($routes as $route)
                                        <x-commercial-route-card :route="$route" :designRequest="$designRequest" />
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Route Summary -->
                    @php
                        $totalRoutes = $commercialRoutes->flatten()->count();
                        $premiumRoutes = $commercialRoutes['Premium']->count() ?? 0;
                        $nonPremiumRoutes = $commercialRoutes['Non Premium']->count() ?? 0;
                        $metroRoutes = $commercialRoutes['Metro']->count() ?? 0;
                    @endphp

                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="mb-2"><i class="fas fa-list-check me-2"></i>Routes Summary</h6>
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <h5 class="text-primary mb-1">{{ $totalRoutes }}</h5>
                                <small class="text-muted">Total Routes</small>
                            </div>
                            <div class="col-md-3 text-center">
                                <h5 class="text-warning mb-1">{{ $premiumRoutes }}</h5>
                                <small class="text-muted">Premium</small>
                            </div>
                            <div class="col-md-3 text-center">
                                <h5 class="text-primary mb-1">{{ $nonPremiumRoutes }}</h5>
                                <small class="text-muted">Non-Premium</small>
                            </div>
                            <div class="col-md-3 text-center">
                                <h5 class="text-info mb-1">{{ $metroRoutes }}</h5>
                                <small class="text-muted">Metro</small>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-route fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Commercial Routes Available</h5>
                        <p class="text-muted">Please check back later or contact support.</p>
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
                            <input class="form-check-input" type="checkbox" id="selectAllColocationServices">
                            <label class="form-check-label" for="selectAllColocationServices">
                                Select All Services
                            </label>
                        </div>

                        <!-- Add to COLOCATION SERVICES card header (after <h5> tag) -->
<div class="ms-auto" style="max-width: 250px;">
    <div class="input-group input-group-sm">
        <span class="input-group-text"><i class="fas fa-search"></i></span>
        <input type="text"
               class="form-control services-search"
               placeholder="Search services...">
    </div>
</div>
                    </div>

                    <div class="services-container" style="max-height: 400px; overflow-y: auto;">
                        @php
                            // Group services by all-service_type-service_category
                            $groupedServices = [];
                            foreach($colocationServices as $service) {
                                $serviceType = $service->service_type ?? $service->servicetype ?? $service->type ?? 'Unknown';
                                $serviceCategory = $service->service_category ?? $service->servicecategory ?? $service->category ?? 'Uncategorized';
                                $groupKey = "{$serviceType}-{$serviceCategory}";
                                $groupedServices[$groupKey][] = $service;
                            }
                        @endphp

                        @foreach($groupedServices as $groupName => $services)
                            <div class="mb-4 service-group">
                                <div class="service-group-header bg-light p-3 rounded mb-2 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 text-dark">
                                        <i class="fas fa-folder me-2"></i>{{ $groupName }}
                                        <small class="text-muted">({{ count($services) }} services)</small>
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-outline-warning group-toggle-btn"
                                            data-target="service-group-{{ $loop->index }}">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>

                                <div class="service-group-body" id="service-group-{{ $loop->index }}">
                                    @foreach($services as $service)
                                        @php
                                            // Extract data with fallbacks
                                            $serviceId = $service->service_id ?? $service->id ?? $service->serviceid ?? 'N/A';
                                            $serviceType = $service->service_type ?? $service->servicetype ?? $service->type ?? $service->name ?? 'Unknown Service';
                                            $serviceCategory = $service->service_category ?? $service->servicecategory ?? $service->category ?? 'Uncategorized';
                                            $fibrestatus = $service->fibrestatus ?? $service->status ?? 'Unknown';
                                            $specifications = $service->specifications ?? $service->description ?? $service->specs ?? '';

                                            // Get numeric values with proper defaults
                                            $powerKw = floatval($service->power_kw ?? $service->powerkw ?? $service->power ?? 0);
                                            $spaceSqm = floatval($service->space_sqm ?? $service->spacesqm ?? $service->space ?? 0);
                                            $oneoffRate = floatval($service->oneoff_rate ?? $service->oneoffrate ?? $service->setup_fee ?? 0);
                                            $oneoffRateUsd = floatval($service->oneoff_rate ?? $service->oneoffrate ?? 0);
                                            $recurrentPerAnnum = floatval($service->recurrent_per_Annum ?? $service->recurrent_per_annum ?? $service->annual_rate ?? 0);
                                            $monthlyPriceUsd = floatval($service->monthly_price_usd ?? $service->monthly_price ?? $service->monthly_rate ?? 0);
                                            $setupFeeUsd = floatval($service->setup_fee_usd ?? $service->setup_fee ?? 0);
                                            $minContractMonths = intval($service->min_contract_months ?? $service->min_contract ?? 12);

                                            // Calculate monthly rate (ensure numeric)
                                            if ($monthlyPriceUsd > 0) {
                                                $monthlyRateNumeric = $monthlyPriceUsd;
                                            } elseif ($recurrentPerAnnum > 0) {
                                                $monthlyRateNumeric = $recurrentPerAnnum / 12;
                                            } else {
                                                $monthlyRateNumeric = 0;
                                            }

                                            // Calculate setup fee (ensure numeric)
                                            if ($setupFeeUsd > 0) {
                                                $setupFeeNumeric = $setupFeeUsd;
                                            } elseif ($oneoffRate > 0) {
                                                $setupFeeNumeric = $oneoffRate;
                                            } else {
                                                $setupFeeNumeric = 0;
                                            }

                                            // Calculate oneoff fee (ensure numeric)
                                            if ($oneoffRateUsd > 0) {
                                                $oneoffRateNumeric = $oneoffRateUsd;
                                            } elseif ($oneoffRate > 0) {
                                                $oneoffRateNumeric = $oneoffRate;
                                            } else {
                                                $oneoffRateNumeric = 0;
                                            }

                                            // Format for display (only for display purposes)
                                            $monthlyRateFormatted = number_format($monthlyRateNumeric, 2);
                                            $oneoffRateFormatted = number_format($oneoffRateNumeric, 2);
                                            $setupFeeFormatted = number_format($setupFeeNumeric, 2);
                                            $powerKwFormatted = $powerKw > 0 ? number_format($powerKw, 2) : null;
                                            $spaceSqmFormatted = $spaceSqm > 0 ? number_format($spaceSqm, 2) : null;

                                            // Calculate initial total using numeric values
                                            $initialTotalNumeric = ($monthlyRateNumeric * 12) + $setupFeeNumeric + $oneoffRateNumeric;
                                            $initialTotalFormatted = number_format($initialTotalNumeric, 2);
                                        @endphp

                                        <div class="card service-card mb-3 border-left-{{ $loop->iteration % 2 == 0 ? 'primary' : 'info' }} border-left-3">
                                            <div class="card-body">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input service-select"
                                                           type="checkbox"
                                                           name="selected_services[]"
                                                           value="{{ $serviceId }}"
                                                           id="service_{{ $serviceId }}"
                                                           data-service-id="{{ $serviceId }}"
                                                           data-monthly-rate="{{ $monthlyRateNumeric }}"
                                                           data-setup-fee="{{ $setupFeeNumeric }}"
                                                           data-oneoff-fee="{{ $oneoffRateNumeric }}">
                                                    <label class="form-check-label" for="service_{{ $serviceId }}">
                                                        <div class="service-header mb-2">
                                                            <h6 class="mb-1 text-dark">
                                                                <strong>{{ $serviceId }}: {{ $serviceType }}</strong>
                                                                @if($fibrestatus)
                                                                    <span class="badge bg-{{ strtolower($fibrestatus) == 'active' ? 'success' : (strtolower($fibrestatus) == 'inactive' ? 'danger' : 'warning') }} ms-2">
                                                                        {{ ucfirst($fibrestatus) }}
                                                                    </span>
                                                                @endif
                                                            </h6>
                                                        </div>

                                                        <!-- Service Details -->
                                                        <div class="service-details ps-3 ms-2 border-start border-2 border-light">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <!-- Category -->
                                                                    <div class="mb-1">
                                                                        <span class="text-muted small">
                                                                            <i class="fas fa-tag me-1"></i>
                                                                            <strong>Category:</strong> {{ $serviceCategory }}
                                                                        </span>
                                                                    </div>

                                                                    <!-- Power -->
                                                                    @if($powerKwFormatted)
                                                                    <div class="mb-1">
                                                                        <span class="text-muted small">
                                                                            <i class="fas fa-bolt me-1"></i>
                                                                            <strong>Power:</strong> {{ $powerKwFormatted }} kW
                                                                        </span>
                                                                    </div>
                                                                    @endif

                                                                    <!-- Space -->
                                                                    @if($spaceSqmFormatted)
                                                                    <div class="mb-1">
                                                                        <span class="text-muted small">
                                                                            <i class="fas fa-arrows-alt me-1"></i>
                                                                            <strong>Space:</strong> {{ $spaceSqmFormatted }} m²
                                                                        </span>
                                                                    </div>
                                                                    @endif

                                                                    <!-- Monthly Rate -->
                                                                    <div class="mb-1">
                                                                        <span class="text-success small">
                                                                            <i class="fas fa-calendar-alt me-1"></i>
                                                                            <strong>Monthly:</strong> ${{ $monthlyRateFormatted }}/month
                                                                        </span>
                                                                    </div>

                                                                    <!-- Setup Fee -->
                                                                    <div class="mb-1">
                                                                        <span class="text-info small">
                                                                            <i class="fas fa-wrench me-1"></i>
                                                                            <strong>Setup Fee:</strong> ${{ $setupFeeFormatted }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="mb-1">
                                                                        <span class="text-info small">
                                                                            <i class="fas fa-wrench me-1"></i>
                                                                            <strong>Oneoff Fee:</strong> ${{ $oneoffRateFormatted }}/one off
                                                                        </span>
                                                                    </div>

                                                                    <!-- Specifications -->
                                                                    @if($specifications)
                                                                    <div class="mb-1">
                                                                        <span class="text-secondary small">
                                                                            <i class="fas fa-info-circle me-1"></i>
                                                                            <strong>Specs:</strong> {{ $specifications }}
                                                                        </span>
                                                                    </div>
                                                                    @endif

                                                                    <!-- Min Contract -->
                                                                    @if($minContractMonths)
                                                                    <div class="mb-1">
                                                                        <span class="text-warning small">
                                                                            <i class="fas fa-file-contract me-1"></i>
                                                                            <strong>Min Contract:</strong> {{ $minContractMonths }} months
                                                                        </span>
                                                                    </div>
                                                                    @endif
                                                                </div>

                                                                <!-- Configuration Panel -->
                                                                <div class="col-md-4">
                                                                    <div class="service-configuration p-2 bg-light rounded" style="display: none;">
                                                                        <h6 class="small fw-bold mb-2 text-center">Configure Service</h6>

                                                                        <!-- Duration -->
                                                                        <div class="mb-2">
                                                                            <label class="form-label small fw-bold">
                                                                                <i class="fas fa-calendar me-1"></i>Duration (Months)
                                                                            </label>
                                                                            <input type="number"
                                                                                   name="service_duration[{{ $serviceId }}]"
                                                                                   class="form-control form-control-sm service-duration-input"
                                                                                   value="{{ $minContractMonths }}"
                                                                                   min="{{ $minContractMonths }}"
                                                                                   max="120"
                                                                                   data-service-id="{{ $serviceId }}">
                                                                        </div>

                                                                        <!-- Quantity -->
                                                                        <div class="mb-2">
                                                                            <label class="form-label small fw-bold">
                                                                                <i class="fas fa-cubes me-1"></i>Quantity
                                                                            </label>
                                                                            <input type="number"
                                                                                   name="service_quantity[{{ $serviceId }}]"
                                                                                   class="form-control form-control-sm service-quantity-input"
                                                                                   value="1"
                                                                                   min="1"
                                                                                   max="100"
                                                                                   data-service-id="{{ $serviceId }}">
                                                                        </div>

                                                                        <!-- Cost Summary -->
                                                                        <div class="service-cost small bg-white p-2 rounded border">
                                                                            <div class="d-flex justify-content-between mb-1">
                                                                                <span class="text-muted">Monthly:</span>
                                                                                <span class="monthly-cost fw-bold text-success"
                                                                                      data-service-id="{{ $serviceId }}"
                                                                                      data-base-monthly="{{ $monthlyRateNumeric }}">
                                                                                    ${{ $monthlyRateFormatted }}
                                                                                </span>
                                                                            </div>
                                                                            <div class="d-flex justify-content-between mb-1">
                                                                                <span class="text-muted">Setup:</span>
                                                                                <span class="setup-cost text-info"
                                                                                      data-base-setup="{{ $setupFeeNumeric }}">
                                                                                    ${{ $setupFeeFormatted }}
                                                                                </span>
                                                                            </div>
                                                                            <div class="d-flex justify-content-between mb-1">
                                                                                <span class="text-muted">Oneoff:</span>
                                                                                <span class="setup-cost text-info"
                                                                                      data-base-setup="{{ $oneoffRateNumeric }}">
                                                                                    ${{ $oneoffRateFormatted }}
                                                                                </span>
                                                                            </div>
                                                                            <hr class="my-1">
                                                                            <div class="d-flex justify-content-between">
                                                                                <span class="text-dark fw-bold">Total:</span>
                                                                                <span class="total-cost fw-bold text-primary"
                                                                                      data-service-id="{{ $serviceId }}">
                                                                                    ${{ $initialTotalFormatted }}
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
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

                    <!-- Summary of all services with totals -->
                    @php
                        $totalMonthlyNumeric = 0;
                        $totalSetupNumeric = 0;
                        $totaloneoffRateNumeric = 0;
                        $totalServices = $colocationServices->count();

                        foreach($colocationServices as $service) {
                            // Extract and calculate numeric values
                            $monthlyPriceUsd = floatval($service->monthly_price_usd ?? $service->monthly_price ?? $service->monthly_rate ?? 0);
                            $recurrentPerAnnum = floatval($service->recurrent_per_Annum ?? $service->recurrent_per_annum ?? $service->annual_rate ?? 0);
                            $setupFeeUsd = floatval($service->setup_fee_usd ?? $service->setup_fee ?? 0);
                            $oneoffRate = floatval($service->oneoff_rate ?? $service->oneoffrate ?? 0);
                            $oneoffRateUsd = floatval($service->oneoff_rate ?? $service->oneoffrate ?? 0);

                            if ($monthlyPriceUsd > 0) {
                                $totalMonthlyNumeric += $monthlyPriceUsd;
                            } elseif ($recurrentPerAnnum > 0) {
                                $totalMonthlyNumeric += ($recurrentPerAnnum / 12);
                            }

                            if ($setupFeeUsd > 0) {
                                $totalSetupNumeric += $setupFeeUsd;
                            } elseif ($oneoffRate > 0) {
                                $totalSetupNumeric += $oneoffRate;
                            }

                            if ($oneoffRateUsd > 0) {
                                $totaloneoffRateNumeric += $oneoffRateUsd;
                            } elseif ($oneoffRate > 0) {
                                $totaloneoffRateNumeric += $oneoffRate;
                            }
                        }

                        $totalMonthlyFormatted = number_format($totalMonthlyNumeric, 2);
                        $totalSetupFormatted = number_format($totalSetupNumeric, 2);
                        $totaloneoffRateFormatted = number_format($totaloneoffRateNumeric, 2);
                    @endphp

                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="mb-2"><i class="fas fa-list-check me-2"></i>Service Summary</h6>
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <h5 class="text-primary mb-1">{{ $totalServices }}</h5>
                                <small class="text-muted">Total Services</small>
                            </div>
                            <div class="col-md-4 text-center">
                                <h5 class="text-success mb-1">${{ $totalMonthlyFormatted }}</h5>
                                <small class="text-muted">Total Monthly</small>
                            </div>
                            <div class="col-md-4 text-center">
                                <h5 class="text-info mb-1">${{ $totalSetupFormatted }}</h5>
                                <small class="text-muted">Total Setup Fees</small>
                            </div>
                            <div class="col-md-4 text-center">
                                <h5 class="text-info mb-1">${{ $totaloneoffRateFormatted }}</h5>
                                <small class="text-muted">Total Oneoff</small>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-server fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Colocation Services Available</h5>
                        <p class="text-muted">Please check back later or contact support.</p>
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
                            <!-- Custom items will be added here dynamically -->
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
                           <textarea name="scope_of_work" id="scope_of_work" class="form-control scope-textarea" rows="7" required>
{{ old('scope_of_work', 'SCOPE OF WORK') }}
</textarea>
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
                                      required>{{ old('terms_and_conditions', $defaultTerms ?? '') }}</textarea>
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
                                      oninput="updateCharacterCount('pricing_counter', this)">{{ old('pricing_notes', '') }}</textarea>
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
                                               value="{{ old('valid_until', \Carbon\Carbon::now()->addDays(30)->format('Y-m-d')) }}"
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
                                               value="{{ old('validity_days', 30) }}"
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
                                        <input class="form-check-input" type="checkbox" name="include_tax" id="include_tax" checked>
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
                                        <input class="form-check-input" type="checkbox" name="allow_negotiation" id="allow_negotiation" checked>
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
                                      oninput="updateCharacterCount('instructions_counter', this)">{{ old('special_instructions', '') }}</textarea>
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
                                @if((auth()->user()->role === 'account_manager') || (auth()->user()->role === 'designer'))
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
{{-- Add these hidden fields inside your form --}}
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

.text-warning {
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
</style>
@endpush

@push('scripts')
<script>

    // Search functionality for Commercial Routes
document.querySelectorAll('.routes-search').forEach(searchBox => {
    searchBox.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        const routeCards = document.querySelectorAll('.routes-container .route-card');
        let visibleCount = 0;

        routeCards.forEach(card => {
            const routeText = card.textContent.toLowerCase();
            const isVisible = routeText.includes(searchTerm);
            card.style.display = isVisible ? 'block' : 'none';

            if (isVisible) visibleCount++;
        });

        // Show/hide empty group messages
        document.querySelectorAll('.route-group').forEach(group => {
            const hasVisible = Array.from(group.querySelectorAll('.route-card'))
                .some(card => card.style.display !== 'none');
            group.style.display = hasVisible ? 'block' : 'none';
        });

        // Update counter badge
        const badge = document.querySelector('.card-header.bg-primary .badge');
        if (badge) {
            badge.textContent = `${visibleCount} routes filtered`;
        }
    });
});

// Search functionality for Colocation Services
document.querySelectorAll('.services-search').forEach(searchBox => {
    searchBox.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        const serviceCards = document.querySelectorAll('.services-container .service-card');
        let visibleCount = 0;

        serviceCards.forEach(card => {
            const serviceText = card.textContent.toLowerCase();
            const isVisible = serviceText.includes(searchTerm);
            card.style.display = isVisible ? 'block' : 'none';

            if (isVisible) visibleCount++;
        });

        // Show/hide empty group messages
        document.querySelectorAll('.service-group').forEach(group => {
            const hasVisible = Array.from(group.querySelectorAll('.service-card'))
                .some(card => card.style.display !== 'none');
            group.style.display = hasVisible ? 'block' : 'none';
        });

        // Update counter badge
        const badge = document.querySelector('.card-header.bg-warning .badge');
        if (badge) {
            badge.textContent = `${visibleCount} services filtered`;
        }
    });
});

// Clear search on page refresh/reset (optional enhancement)
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.routes-search, .services-search').forEach(input => {
        input.value = '';
    });
});
    // Group toggle functionality
document.querySelectorAll('.group-toggle-btn').forEach(button => {
    button.addEventListener('click', function() {
        const targetId = this.dataset.target;
        const targetElement = document.getElementById(targetId);
        const icon = this.querySelector('i');

        if (targetElement) {
            if (targetElement.style.display === 'none') {
                targetElement.style.display = 'block';
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            } else {
                targetElement.style.display = 'none';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            }
        }
    });
});
document.addEventListener('DOMContentLoaded', function() {
    // Initialize character counters
    updateCharacterCount('scope_counter', document.getElementById('scope_of_work'));
    updateCharacterCount('terms_counter', document.getElementById('terms_and_conditions'));
    updateCharacterCount('pricing_counter', document.getElementById('pricing_notes'));
    updateCharacterCount('instructions_counter', document.getElementById('special_instructions'));

    // Initialize date synchronization
    document.getElementById('valid_until').addEventListener('change', updateValidityDays);
    document.getElementById('validity_days').addEventListener('change', updateValidUntilDate);

    // Initialize all components - FIXED: Correct function names
    initializeRouteSelection();
    initializeServiceSelection();
    initializeCustomItems();
    initializeTaxCalculation();

    // Calculate initial totals
    calculateTotals();
});

// FIXED: Simple route calculation functions
function calculateRouteCost(routeId) {
    const checkbox = document.querySelector(`#route_${routeId}`);
    if (!checkbox) return 0;

    const monthlyCost = parseFloat(checkbox.dataset.monthlyCost) || 0;
    const capitalExpenditure = parseFloat(checkbox.dataset.capitalExpenditure) || 0;

    // Get inputs - FIXED: Correct selector for route configuration inputs
    const coresInput = document.querySelector(`input[name="route_cores[${routeId}]"]`);
    const durationInput = document.querySelector(`input[name="route_duration[${routeId}]"]`);

    const cores = parseInt(coresInput?.value) || 1;
    const duration = parseInt(durationInput?.value) || 12;

    // Calculate new monthly cost based on cores
    const newMonthlyCost = monthlyCost * cores;
    const total = (newMonthlyCost * duration) + capitalExpenditure;

    // Update display
    const monthlyCostElement = document.querySelector(`.monthly-cost[data-route-id="${routeId}"]`);
    const totalCostElement = document.querySelector(`.total-cost[data-route-id="${routeId}"]`);

    if (monthlyCostElement) {
        monthlyCostElement.textContent = `$${newMonthlyCost.toFixed(2)}`;
    }

    if (totalCostElement) {
        totalCostElement.textContent = `$${total.toFixed(2)}`;
    }

    return total;
}

// FIXED: Calculate colocation service cost
// FIXED: Calculate colocation service cost WITH one-off fee
function calculateServiceCost(serviceId) {
    const checkbox = document.querySelector(`#service_${serviceId}`);
    if (!checkbox) return 0;

    const monthlyRate = parseFloat(checkbox.dataset.monthlyRate) || 0;
    const setupFee = parseFloat(checkbox.dataset.setupFee) || 0;
    const oneoffFee = parseFloat(checkbox.dataset.oneoffFee) || 0; // Get one-off fee from data attribute

    // Get configuration inputs
    const durationInput = document.querySelector(`input[name="service_duration[${serviceId}]"]`);
    const quantityInput = document.querySelector(`input[name="service_quantity[${serviceId}]"]`);

    const duration = parseInt(durationInput?.value) || 12;
    const quantity = parseInt(quantityInput?.value) || 1;

    const monthlyCost = monthlyRate * quantity;
    const setupCost = setupFee * quantity;
    const oneoffCost = oneoffFee * quantity; // Calculate one-off cost

    // CORRECTED: Include one-off fee in total
    const totalCost = (monthlyCost * duration) + setupCost + oneoffCost;

    // Update display
    const monthlyCostElement = document.querySelector(`.monthly-cost[data-service-id="${serviceId}"]`);
    const setupCostElement = document.querySelector(`.setup-cost[data-service-id="${serviceId}"]`);
    const totalCostElement = document.querySelector(`.total-cost[data-service-id="${serviceId}"]`);

    if (monthlyCostElement) {
        monthlyCostElement.textContent = `$${monthlyCost.toFixed(2)}`;
    }

    if (totalCostElement) {
        totalCostElement.textContent = `$${totalCost.toFixed(2)}`;
    }

    return totalCost;
}

// FIXED: Main totals calculation
function calculateTotals() {
    let routesTotal = 0;
    let servicesTotal = 0;
    let customItemsTotal = 0;

    // Calculate routes total
    document.querySelectorAll('.route-select:checked').forEach(checkbox => {
        const routeId = checkbox.dataset.routeId;
        routesTotal += calculateRouteCost(routeId) || 0;
    });

    // Calculate services total
    document.querySelectorAll('.service-select:checked').forEach(checkbox => {
        const serviceId = checkbox.dataset.serviceId;
        servicesTotal += calculateServiceCost(serviceId) || 0;
    });

    // Calculate custom items total
    document.querySelectorAll('input[name^="custom_items"][name$="[total]"]').forEach(input => {
        customItemsTotal += parseFloat(input.value) || 0;
    });

    // Calculate final totals
    const subtotal = routesTotal + servicesTotal + customItemsTotal;
    const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0.16;
    const taxAmount = subtotal * taxRate;
    const totalAmount = subtotal + taxAmount;

    // Update visible display
    document.getElementById('routesTotal').textContent = `$${routesTotal.toFixed(2)}`;
    document.getElementById('servicesTotal').textContent = `$${servicesTotal.toFixed(2)}`;
    document.getElementById('customItemsTotal').textContent = `$${customItemsTotal.toFixed(2)}`;
    document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('tax_amount').textContent = `$${taxAmount.toFixed(2)}`;
    document.getElementById('total_amount').textContent = `$${totalAmount.toFixed(2)}`;

    // CRITICAL: Ensure hidden fields exist and set them
    const hiddenFields = [
        { id: 'hidden_routes_total', value: routesTotal },
        { id: 'hidden_services_total', value: servicesTotal },
        { id: 'hidden_custom_items_total', value: customItemsTotal },
        { id: 'hidden_subtotal', value: subtotal },
        { id: 'hidden_tax_amount', value: taxAmount },
        { id: 'hidden_total_amount', value: totalAmount }
    ];

    hiddenFields.forEach(field => {
        let element = document.getElementById(field.id);
        if (!element) {
            // Create the hidden field if it doesn't exist
            element = document.createElement('input');
            element.type = 'hidden';
            element.name = field.id.replace('hidden_', '');
            element.id = field.id;
            document.getElementById('quotationForm').appendChild(element);
        }
        element.value = field.value.toFixed(2);
    });

    // AFTER calculating totals, set the hidden fields:
    document.getElementById('hidden_routes_total').value = routesTotal.toFixed(2);
    document.getElementById('hidden_services_total').value = servicesTotal.toFixed(2);
    document.getElementById('hidden_custom_items_total').value = customItemsTotal.toFixed(2);
    document.getElementById('hidden_subtotal').value = subtotal.toFixed(2);
    document.getElementById('hidden_tax_amount').value = taxAmount.toFixed(2);
    document.getElementById('hidden_total_amount').value = totalAmount.toFixed(2);
}

// Add form submit validation
document.getElementById('quotationForm').addEventListener('submit', function(e) {
    // Ensure scope_of_work is not "undefined"
    const scopeTextarea = document.getElementById('scope_of_work');
    if (scopeTextarea && scopeTextarea.value === 'undefined') {
        e.preventDefault();
        alert('Please enter a valid Scope of Work or use the Quick Fill button.');
        scopeTextarea.focus();
        return;
    }

    // Ensure totals are calculated one last time
    calculateTotals();

    // Log what's being submitted for debugging
    console.log('Submitting form with totals:', {
        routes_total: document.getElementById('hidden_routes_total').value,
        services_total: document.getElementById('hidden_services_total').value,
        subtotal: document.getElementById('hidden_subtotal').value,
        total_amount: document.getElementById('hidden_total_amount').value
    });
});
// FIXED: Route selection initialization
function initializeRouteSelection() {
    // Individual route selection
    document.querySelectorAll('.route-select').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const routeId = this.dataset.routeId;
            const configDiv = this.closest('.route-card').querySelector('.route-configuration');

            if (configDiv) {
                configDiv.style.display = this.checked ? 'block' : 'none';
            }

            if (this.checked) {
                calculateRouteCost(routeId);
            }
            calculateTotals();
        });
    });

    // Route configuration inputs
    document.querySelectorAll('input[name^="route_cores"], input[name^="route_duration"]').forEach(input => {
        input.addEventListener('input', function() {
            // Extract route ID from name
            const name = this.name;
            const matches = name.match(/\[(\d+)\]/);
            if (matches && matches[1]) {
                const routeId = matches[1];
                const checkbox = document.querySelector(`#route_${routeId}`);
                if (checkbox && checkbox.checked) {
                    calculateRouteCost(routeId);
                    calculateTotals();
                }
            }
        });
    });

    // Select all routes
    document.getElementById('selectAllRoutes').addEventListener('change', function() {
        const isChecked = this.checked;
        document.querySelectorAll('.routes-container .route-select').forEach(checkbox => {
            checkbox.checked = isChecked;
            // Trigger change event
            const event = new Event('change');
            checkbox.dispatchEvent(event);
        });
    });
}

// FIXED: Service selection initialization
function initializeServiceSelection() {
    // Individual service selection
    document.querySelectorAll('.service-select').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const serviceId = this.dataset.serviceId;
            const configDiv = this.closest('.service-card').querySelector('.service-configuration');

            if (configDiv) {
                configDiv.style.display = this.checked ? 'block' : 'none';
            }

            if (this.checked) {
                calculateServiceCost(serviceId);
            }
            calculateTotals();
        });
    });

    // Service configuration inputs
    document.querySelectorAll('.service-duration-input, .service-quantity-input').forEach(input => {
        input.addEventListener('input', function() {
            const serviceId = this.dataset.serviceId;
            const checkbox = document.querySelector(`#service_${serviceId}`);
            if (checkbox && checkbox.checked) {
                calculateServiceCost(serviceId);
                calculateTotals();
            }
        });
    });

    // Select all colocation services - FIXED: Correct ID
    const selectAllColocationCheckbox = document.getElementById('selectAllColocationServices');
    if (selectAllColocationCheckbox) {
        selectAllColocationCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.services-container .service-select').forEach(checkbox => {
                checkbox.checked = isChecked;
                // Trigger change event
                const event = new Event('change');
                checkbox.dispatchEvent(event);
            });
        });
    }
}

// FIXED: Custom items management
function initializeCustomItems() {
    let customItemIndex = 0; // Start from 0 for first item

    // Add custom item
    const addCustomItemBtn = document.getElementById('addCustomItem');
    if (addCustomItemBtn) {
        addCustomItemBtn.addEventListener('click', function() {
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

            // Add event listeners to new inputs
            const quantityInput = newItem.querySelector('input[name^="custom_items"][name$="[quantity]"]');
            const unitPriceInput = newItem.querySelector('input[name^="custom_items"][name$="[unit_price]"]');

            const calculateItemTotal = () => {
                const quantity = parseFloat(quantityInput.value) || 0;
                const unitPrice = parseFloat(unitPriceInput.value) || 0;
                const total = quantity * unitPrice;

                const totalInput = newItem.querySelector('input[name$="[total]"]');
                if (totalInput) {
                    totalInput.value = total.toFixed(2);
                }
                calculateTotals();
            };

            if (quantityInput) quantityInput.addEventListener('input', calculateItemTotal);
            if (unitPriceInput) unitPriceInput.addEventListener('input', calculateItemTotal);

            // Remove item functionality
            const removeBtn = newItem.querySelector('.remove-item');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    newItem.remove();
                    calculateTotals();
                });
            }

            customItemIndex++;
        });
    }
}

// FIXED: Tax calculation
function initializeTaxCalculation() {
    const taxRateInput = document.getElementById('tax_rate');
    if (taxRateInput) {
        taxRateInput.addEventListener('input', calculateTotals);
    }
}

// Keep the template functions (they work fine)
const scopeTemplates = {
    // ... (keep your existing template objects) ...
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
    if (counter && textarea) {
        counter.textContent = textarea.value.length;
    }
}

function loadDefaultScope() {
    const scopeTextarea = document.getElementById('scope_of_work');
    if (scopeTextarea) {
        scopeTextarea.value = `SCOPE OF WORK

PROJECT OVERVIEW:
This quotation covers fibre connectivity services including dark fibre lease, colocation services, and related infrastructure as specified below.

INCLUDED SERVICES:
• Dark Fibre Connectivity between specified points
• Colocation services including rack space and power
• 24/7 network monitoring and support
• Installation and testing of all services

DELIVERABLES:
• Fibre connectivity documentation and diagrams
• Colocation facility access procedures
• Service Level Agreement (SLA) documentation
• Emergency contact and escalation procedures

ASSUMPTIONS:
• Site access permissions are provided by customer
• Power and space requirements are as specified
• Installation timeline: 30-60 days after approval`;
        updateCharacterCount('scope_counter', scopeTextarea);
    }
}

function loadTemplate(templateType) {
    const scopeTextarea = document.getElementById('scope_of_work');
    if (scopeTextarea) {
        scopeTextarea.value = scopeTemplates[templateType] || scopeTemplates.standard_colocation;
        updateCharacterCount('scope_counter', scopeTextarea);

        const modal = bootstrap.Modal.getInstance(document.getElementById('scopeTemplatesModal'));
        if (modal) {
            modal.hide();
        }
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

    let content = textarea.value;
    content = content.replace(/\n\s*\n\s*\n/g, '\n\n');
    content = content.replace(/^[•\-]\s*/gm, '• ');
    content = content.replace(/\s+$/gm, '');

    textarea.value = content;
    updateCharacterCount('scope_counter', textarea);
}

function insertPricingTemplate() {
    const template = `PRICING NOTES:

• All prices are exclusive of VAT unless specified
• Payment plan available: 50% upfront, 50% on completion
• Early payment discount: 2% if paid within 10 days
• Prices valid for 30 days from quotation date
• Setup fees are one-time and non-refundable
• Monthly recurring charges billed in advance`;

    const textarea = document.getElementById('pricing_notes');
    if (textarea) {
        textarea.value = template;
        updateCharacterCount('pricing_counter', textarea);
    }
}

// Date management functions
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
