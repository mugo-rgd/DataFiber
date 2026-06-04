@extends('layouts.app')

@section('title', 'Edit Quotation')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-edit text-kp-blue"></i> Edit Quotation: {{ $quotation->quotation_number }}
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.quotations.index') }}">Quotations</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.quotations.show', $quotation) }}">{{ $quotation->quotation_number }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Status Alert -->
    <div class="row">
        <div class="col-12">
            @if(auth()->user()->role === 'account_manager')
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    You are editing a draft quotation. Only administrators can send quotations to customers.
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    You are editing a draft quotation. Remember to send it to the customer when ready.
                </div>
            @endif
        </div>
    </div>

    <form action="{{ route('admin.quotations.update', $quotation) }}" method="POST" id="quotationForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="design_request_id" value="{{ $quotation->design_request_id }}">

        <!-- Design Request Info with Customer Requirements -->
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
                                <p><strong>Request #:</strong> #{{ $quotation->designRequest->request_number }}</p>
                                <p><strong>Customer:</strong> {{ $quotation->designRequest->customer->name }}</p>
                                <p><strong>Title:</strong> {{ $quotation->designRequest->title }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Requested:</strong> {{ $quotation->designRequest->created_at->format('M d, Y') }}</p>
                                <p><strong>Description:</strong> {{ $quotation->designRequest->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Original Requirements (Design Request Defaults) -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4 border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>Customer Original Requirements (From Design Request)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="alert alert-light mb-0 border">
                                    <strong><i class="fas fa-microchip me-2"></i>Requested Cores:</strong>
                                    <span class="h5">{{ $quotation->designRequest->cores_required ?? 2 }}</span> cores
                                    <small class="text-muted d-block">Customer's core requirement</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-light mb-0 border">
                                    <strong><i class="fas fa-calendar-alt me-2"></i>Requested Contract Term:</strong>
                                    <span class="h5">{{ $quotation->designRequest->terms ?? 12 }}</span> months
                                    <small class="text-muted d-block">Customer's preferred duration</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-light mb-0 border">
                                    <strong><i class="fas fa-ruler me-2"></i>Estimated Distance:</strong>
                                    <span class="h5">{{ number_format($quotation->designRequest->distance ?? 0, 2) }}</span> km
                                    <small class="text-muted d-block">Approximate route distance</small>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-edit me-2"></i>
                            <strong>Note:</strong> The cores and duration fields below are pre-filled with the customer's requirements.
                            You can edit them as needed for this quotation.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Requirements Section -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-list-check me-2"></i>Customer Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="customer_notes" class="form-label">Customer Notes & Specific Requirements</label>
                            <textarea name="customer_notes" id="customer_notes" class="form-control" rows="3"
                                      placeholder="Add any specific customer requirements or notes...">{{ old('customer_notes', $quotation->customer_notes) }}</textarea>
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
                        <span class="badge bg-light text-dark">
                            {{ $commercialRoutes->count() }} available
                        </span>
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
                                @php
                                    $sortedRoutes = $commercialRoutes->sortByDesc(function ($route) use ($quotation) {
                                        return $quotation->commercialRoutes->contains('id', $route->id);
                                    });
                                    // Get design request defaults for display
                                    $designRequestDefaults = $quotation->designRequest;
                                    $defaultCores = $designRequestDefaults->cores_required ?? 2;
                                    $defaultDuration = $designRequestDefaults->terms ?? 12;
                                @endphp

                                @foreach($sortedRoutes as $route)
                                    @php
                                        $isSelected = $quotation->commercialRoutes->contains('id', $route->id);
                                        $routePivot = $quotation->commercialRoutes->firstWhere('id', $route->id)?->pivot;

                                        $routeUnitCost = $route->unit_cost_per_core_km_per_month
                                            ?? $route->unit_cost_per_core_per_km_per_month
                                            ?? $route->unit_cost
                                            ?? $route->monthly_cost
                                            ?? match($route->option ?? null) {
                                                'Non Premium' => 18,
                                                'Premium' => 19,
                                                'Metro' => 20,
                                                default => 0,
                                            };

                                        $routeDistance = (float) ($route->approx_distance_km ?? 0);
                                        $monthlyBaseCost = (float) $routeUnitCost * $routeDistance;
                                        // Use pivot values if available, otherwise use design request defaults
                                        $coresValue = $routePivot->quantity ?? $defaultCores;
                                        $durationValue = $routePivot->duration_months ?? $defaultDuration;
                                    @endphp

                                    <div class="card route-card mb-3 {{ $isSelected ? 'border-success selected-route-card' : '' }}">
                                        <div class="card-body">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input route-select"
                                                       type="checkbox"
                                                       name="selected_routes[]"
                                                       value="{{ $route->id }}"
                                                       id="route_{{ $route->id }}"
                                                       data-route-id="{{ $route->id }}"
                                                       data-monthly-cost="{{ $monthlyBaseCost }}"
                                                       data-capex="{{ $route->capital_expenditure ?? 0 }}"
                                                       {{ $isSelected ? 'checked' : '' }}>

                                                <label class="form-check-label fw-bold" for="route_{{ $route->id }}">
                                                    {{ $route->name_of_route }}
                                                    @if($isSelected)
                                                        <span class="badge bg-success ms-2">Selected</span>
                                                    @endif
                                                </label>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <i class="fas fa-wifi me-1"></i>{{ $route->tech_type }}<br>
                                                        <i class="fas fa-ruler me-1"></i>{{ number_format($routeDistance, 2) }} km<br>
                                                        <i class="fas fa-toggle-on me-1"></i>{{ $route->availability }}<br>
                                                        <i class="fas fa-dollar-sign me-1"></i>
                                                        Unit: ${{ number_format($routeUnitCost, 2) }} / core / km / month
                                                    </small>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="route-configuration" style="{{ $isSelected ? 'display: block;' : 'display: none;' }}">
                                                        <div class="mb-2">
                                                            <label class="form-label small">
                                                                Cores Required
                                                                <span class="text-muted">(Customer requested: {{ $defaultCores }})</span>
                                                            </label>
                                                            <input type="number"
                                                                   name="route_cores[{{ $route->id }}]"
                                                                   class="form-control form-control-sm cores-input"
                                                                   value="{{ $coresValue }}"
                                                                   min="1"
                                                                   data-route-id="{{ $route->id }}">
                                                        </div>

                                                        <div class="mb-2">
                                                            <label class="form-label small">
                                                                Duration (Months)
                                                                <span class="text-muted">(Customer requested: {{ $defaultDuration }})</span>
                                                            </label>
                                                            <input type="number"
                                                                   name="route_duration[{{ $route->id }}]"
                                                                   class="form-control form-control-sm duration-input"
                                                                   value="{{ $durationValue }}"
                                                                   min="1"
                                                                   data-route-id="{{ $route->id }}">
                                                        </div>

                                                        <div class="route-cost small">
                                                            <strong>
                                                                Monthly:
                                                                <span class="monthly-cost" data-route-id="{{ $route->id }}">
                                                                    ${{ number_format($monthlyBaseCost * $coresValue, 2) }}
                                                                </span>
                                                            </strong>
                                                            <br>
                                                            <strong>
                                                                Total:
                                                                <span class="total-cost" data-route-id="{{ $route->id }}">
                                                                    ${{ number_format(
                                                                        ($monthlyBaseCost * $coresValue * $durationValue)
                                                                        + ($route->capital_expenditure ?? 0),
                                                                        2
                                                                    ) }}
                                                                </span>
                                                            </strong>
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

            <!-- Custom Routes -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-drafting-compass me-2"></i>Custom Routes
                        </h5>
                        <span class="badge bg-warning text-dark">
                            {{ $customRoutes->count() }} available
                        </span>
                    </div>
                    <div class="card-body">
                        @if($customRoutes->count() > 0)
                            <div class="custom-routes-container" style="max-height:400px; overflow-y:auto;">
                                @php
                                    $designRequestDefaults = $quotation->designRequest;
                                    $defaultDuration = $designRequestDefaults->terms ?? 12;
                                @endphp
                                @foreach($customRoutes as $route)
                                    @php
                                        $isSelected = $quotation->customRoutes->contains('id', $route->id);
                                        $routePivot = $quotation->customRoutes->firstWhere('id', $route->id)?->pivot;

                                        $monthlyCost = (float) ($routePivot->monthly_cost ?? $route->monthly_cost ?? 0);
                                        $capex = (float) ($routePivot->capital_expenditure ?? $route->capital_expenditure ?? 0);
                                        $duration = (int) ($route->contract_duration_months ?? $defaultDuration);
                                        $totalCost = ($monthlyCost * $duration) + $capex;
                                    @endphp

                                    <div class="card custom-route-card mb-3 border-warning">
                                        <div class="card-body">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input custom-route-select"
                                                       type="checkbox"
                                                       name="selected_custom_routes[]"
                                                       value="{{ $route->id }}"
                                                       id="custom_route_{{ $route->id }}"
                                                       data-monthly-cost="{{ $monthlyCost }}"
                                                       data-capex="{{ $capex }}"
                                                       data-duration="{{ $duration }}"
                                                       {{ $isSelected ? 'checked' : '' }}>

                                                <label class="form-check-label fw-bold" for="custom_route_{{ $route->id }}">
                                                    {{ $route->name_of_route }}
                                                    <span class="badge bg-warning text-dark ms-1">Custom</span>
                                                    @if($isSelected)
                                                        <span class="badge bg-success ms-1">Selected</span>
                                                    @endif
                                                </label>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $route->region ?? 'N/A' }}<br>
                                                        <i class="fas fa-network-wired me-1"></i>{{ $route->tech_type }} |
                                                        {{ $route->option }}<br>
                                                        <i class="fas fa-ruler me-1"></i>{{ number_format($route->approx_distance_km ?? 0, 2) }} km<br>
                                                        <i class="fas fa-circle-nodes me-1"></i>{{ $route->no_of_cores_required ?? 1 }} cores
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="small">
                                                        <strong>Monthly:</strong> ${{ number_format($monthlyCost, 2) }}<br>
                                                        <strong>Duration:</strong> {{ $duration }} months<br>
                                                        <strong>CAPEX:</strong> ${{ number_format($capex, 2) }}<br>
                                                        <strong class="text-primary">Total:</strong>
                                                        <span class="custom-route-total" data-custom-route-id="{{ $route->id }}">
                                                            ${{ number_format($totalCost, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-drafting-compass fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No custom routes created.</p>
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
                                @php
                                    $defaultDuration = $quotation->designRequest->terms ?? 12;
                                @endphp
                                @foreach($colocationServices as $service)
                                    @php
                                        $isSelected = $quotation->colocationServices->contains($service->service_id);
                                        $servicePivot = $quotation->colocationServices->find($service->service_id)?->pivot;

                                        $monthlyRate = (float) ($service->monthly_price_usd ?? (($service->recurrent_per_Annum ?? 0) / 12));
                                        $setupFee = (float) ($service->setup_fee_usd ?? $service->oneoff_rate ?? 0);
                                        $quantity = $servicePivot->quantity ?? 1;
                                        $duration = $servicePivot->duration_months ?? $defaultDuration;
                                    @endphp
                                    <div class="card service-card mb-3">
                                        <div class="card-body">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input service-select"
                                                       type="checkbox"
                                                       name="selected_services[]"
                                                       value="{{ $service->service_id }}"
                                                       id="service_{{ $service->service_id }}"
                                                       data-service-id="{{ $service->service_id }}"
                                                       data-monthly-rate="{{ $monthlyRate }}"
                                                       data-setup-fee="{{ $setupFee }}"
                                                       {{ $isSelected ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="service_{{ $service->service_id }}">
                                                    {{ $service->service_type }}
                                                </label>
                                                <input type="hidden" name="service_source[{{ $service->service_id }}]" value="list">
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
                                                        <i class="fas fa-dollar-sign me-1"></i>
                                                        Monthly: ${{ number_format($monthlyRate, 2) }}<br>
                                                        <i class="fas fa-dollar-sign me-1"></i>
                                                        Setup: ${{ number_format($setupFee, 2) }}
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="service-configuration" style="{{ $isSelected ? 'display: block;' : 'display: none;' }}">
                                                        <div class="mb-2">
                                                            <label class="form-label small">
                                                                Duration (Months)
                                                                <span class="text-muted">(Default: {{ $defaultDuration }})</span>
                                                            </label>
                                                            <input type="number"
                                                                   name="service_duration[{{ $service->service_id }}]"
                                                                   class="form-control form-control-sm service-duration-input"
                                                                   value="{{ $duration }}"
                                                                   min="{{ $service->min_contract_months ?? 1 }}"
                                                                   data-service-id="{{ $service->service_id }}">
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label small">Quantity</label>
                                                            <input type="number"
                                                                   name="service_quantity[{{ $service->service_id }}]"
                                                                   class="form-control form-control-sm service-quantity-input"
                                                                   value="{{ $quantity }}"
                                                                   min="1"
                                                                   data-service-id="{{ $service->service_id }}">
                                                        </div>
                                                        <div class="service-cost small">
                                                            <strong>Monthly: <span class="monthly-cost" data-service-id="{{ $service->service_id }}">${{ number_format($monthlyRate * $quantity, 2) }}</span></strong><br>
                                                            <strong>Setup: <span class="setup-cost" data-service-id="{{ $service->service_id }}">${{ number_format($setupFee * $quantity, 2) }}</span></strong><br>
                                                            <strong>Total: <span class="total-cost" data-service-id="{{ $service->service_id }}">${{ number_format(
                                                                ($monthlyRate * $quantity * $duration) + ($setupFee * $quantity),
                                                                2
                                                            ) }}</span></strong>
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
                            @php
                                $customItems = collect($quotation->line_items)->where('type', 'custom_item')->values();
                            @endphp
                            @if($customItems->count() > 0)
                                @foreach($customItems as $index => $item)
                                    <div class="custom-item row mb-3">
                                        <div class="col-md-4">
                                            <input type="text" name="custom_items[{{ $index }}][description]"
                                                   class="form-control" placeholder="Item description"
                                                   value="{{ $item['description'] }}">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="custom_items[{{ $index }}][quantity]"
                                                   class="form-control custom-item-qty" value="{{ $item['quantity'] }}" min="1" placeholder="Qty">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="custom_items[{{ $index }}][unit_price]"
                                                   class="form-control custom-item-price" step="0.01" min="0"
                                                   value="{{ $item['unit_price'] }}" placeholder="Unit price">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="custom_items[{{ $index }}][total]"
                                                   class="form-control custom-item-total" value="{{ number_format($item['total'], 2) }}" placeholder="Total" readonly>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="custom-item row mb-3">
                                    <div class="col-md-4">
                                        <input type="text" name="custom_items[0][description]" class="form-control" placeholder="Item description">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="custom_items[0][quantity]" class="form-control custom-item-qty" value="1" min="1" placeholder="Qty">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="custom_items[0][unit_price]" class="form-control custom-item-price" step="0.01" min="0" placeholder="Unit price">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="custom_items[0][total]" class="form-control custom-item-total" placeholder="Total" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                                    </div>
                                </div>
                            @endif
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
                                <strong>Routes Total:</strong>
                            </div>
                            <div class="col-6 text-end">
                                <span id="routesTotal">
                                    ${{ number_format(
                                        collect($quotation->line_items)->where('type', 'commercial_route')->sum('total') +
                                        $quotation->customRoutes->sum(function ($route) {
                                            $monthly = $route->pivot->monthly_cost ?? $route->monthly_cost ?? 0;
                                            $capex = $route->pivot->capital_expenditure ?? $route->capital_expenditure ?? 0;
                                            $duration = $route->contract_duration_months ?? 12;
                                            return ($monthly * $duration) + $capex;
                                        }),
                                        2
                                    ) }}
                                </span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>Colocation Services Total:</strong>
                            </div>
                            <div class="col-6 text-end">
                                <span id="servicesTotal">${{ number_format(collect($quotation->line_items)->where('type', 'colocation_service')->sum('total'), 2) }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>Custom Items Total:</strong>
                            </div>
                            <div class="col-6 text-end">
                                <span id="customItemsTotal">${{ number_format(collect($quotation->line_items)->where('type', 'custom_item')->sum('total'), 2) }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>Subtotal:</strong>
                            </div>
                            <div class="col-6 text-end">
                                <span id="subtotal">${{ number_format($quotation->subtotal, 2) }}</span>
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
                                       value="{{ old('tax_rate', $quotation->tax_rate) }}"
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
                                <span id="tax_amount">${{ number_format($quotation->tax_amount, 2) }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong class="h5">Total Amount:</strong>
                            </div>
                            <div class="col-6 text-end">
                                <span id="total_amount" class="h5 text-primary">${{ number_format($quotation->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quotation Details -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>Quotation Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="scope_of_work" class="form-label">Scope of Work</label>
                            <textarea name="scope_of_work"
                                      id="scope_of_work"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Describe the scope of work and services to be provided..."
                                      required>{{ old('scope_of_work', $quotation->scope_of_work) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="terms_and_conditions" class="form-label">Terms & Conditions</label>
                            <textarea name="terms_and_conditions"
                                      id="terms_and_conditions"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Specify the terms and conditions..."
                                      required>{{ old('terms_and_conditions', $quotation->terms_and_conditions) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="valid_until" class="form-label">Valid Until</label>
                            <input type="date"
                                   name="valid_until"
                                   id="valid_until"
                                   class="form-control"
                                   value="{{ old('valid_until', $quotation->valid_until->format('Y-m-d')) }}"
                                   min="{{ \Carbon\Carbon::now()->addDay()->format('Y-m-d') }}"
                                   required>
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
                            <div>
                                <a href="{{ route('admin.quotations.show', $quotation) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                @if(auth()->user()->role === 'admin')
                                    <button type="button" class="btn btn-danger" onclick="deleteQuotation({{ $quotation->id }})">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
                                @endif
                            </div>
                            <div>
                                <button type="submit" name="action" value="draft" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Quotation
                                </button>
                                @if(auth()->user()->role === 'admin')
                                    <button type="submit" name="action" value="send" class="btn btn-success">
                                        <i class="fas fa-paper-plane me-2"></i>Update & Send
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    initializeRouteSelection();
    initializeCustomRouteSelection();
    initializeServiceSelection();
    initializeCustomItems();
    initializeTaxCalculation();

    // Initialize existing selected routes
    document.querySelectorAll('.route-select:checked').forEach(checkbox => {
        const routeId = checkbox.dataset.routeId;
        if(routeId){
            calculateRouteCost(routeId);
        }
    });

    // Initialize existing selected services
    document.querySelectorAll('.service-select:checked').forEach(checkbox => {
        const serviceId = checkbox.dataset.serviceId;
        if(serviceId){
            calculateServiceCost(serviceId);
        }
    });

    calculateTotals();
});

function money(value) {
    return '$' + Number(value || 0).toFixed(2);
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

function calculateRouteCost(routeId) {
    const checkbox = document.getElementById(`route_${routeId}`);
    if (!checkbox) return 0;

    const card = checkbox.closest('.route-card');
    if (!card) return 0;

    const monthlyBase = parseFloat(checkbox.dataset.monthlyCost || 0);
    const capex = parseFloat(checkbox.dataset.capex || 0);

    const coresInput = card.querySelector(`input[name="route_cores[${routeId}]"]`);
    const durationInput = card.querySelector(`input[name="route_duration[${routeId}]"]`);

    const cores = parseInt(coresInput?.value || 1);
    const duration = parseInt(durationInput?.value || 12);

    const monthly = monthlyBase * cores;
    const total = (monthly * duration) + capex;

    const monthlyEl = card.querySelector(`.monthly-cost[data-route-id="${routeId}"]`);
    const totalEl = card.querySelector(`.total-cost[data-route-id="${routeId}"]`);

    if (monthlyEl) monthlyEl.textContent = money(monthly);
    if (totalEl) totalEl.textContent = money(total);

    return total;
}

function calculateServiceCost(serviceId) {
    const checkbox = document.getElementById(`service_${serviceId}`);
    if (!checkbox) return 0;

    const card = checkbox.closest('.service-card');
    if (!card) return 0;

    const monthlyRate = parseFloat(checkbox.dataset.monthlyRate || 0);
    const setupFee = parseFloat(checkbox.dataset.setupFee || 0);

    const durationInput = card.querySelector(`input[name="service_duration[${serviceId}]"]`);
    const quantityInput = card.querySelector(`input[name="service_quantity[${serviceId}]"]`);

    const duration = parseInt(durationInput?.value || 12);
    const quantity = parseInt(quantityInput?.value || 1);

    const monthlyTotal = monthlyRate * quantity;
    const setupTotal = setupFee * quantity;
    const total = (monthlyTotal * duration) + setupTotal;

    const monthlyEl = card.querySelector(`.monthly-cost[data-service-id="${serviceId}"]`);
    const setupEl = card.querySelector(`.setup-cost[data-service-id="${serviceId}"]`);
    const totalEl = card.querySelector(`.total-cost[data-service-id="${serviceId}"]`);

    if (monthlyEl) monthlyEl.textContent = money(monthlyTotal);
    if (setupEl) setupEl.textContent = money(setupTotal);
    if (totalEl) totalEl.textContent = money(total);

    return total;
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

    document.querySelectorAll('.custom-item-total').forEach(input => {
        customItemsTotal += parseFloat(input.value || 0);
    });

    const subtotal = routesTotal + servicesTotal + customItemsTotal;
    const taxRate = parseFloat(document.getElementById('tax_rate')?.value || 0);
    const taxAmount = subtotal * taxRate;
    const totalAmount = subtotal + taxAmount;

    setText('routesTotal', money(routesTotal));
    setText('servicesTotal', money(servicesTotal));
    setText('customItemsTotal', money(customItemsTotal));
    setText('subtotal', money(subtotal));
    setText('tax_amount', money(taxAmount));
    setText('total_amount', money(totalAmount));
}

function initializeRouteSelection() {
    document.querySelectorAll('.route-select').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const config = this.closest('.route-card')?.querySelector('.route-configuration');
            if (config) config.style.display = this.checked ? 'block' : 'none';
            calculateTotals();
        });
    });

    document.querySelectorAll('.cores-input, .duration-input').forEach(input => {
        input.addEventListener('input', () => calculateTotals());
    });

    document.getElementById('selectAllRoutes')?.addEventListener('change', function () {
        document.querySelectorAll('.route-select').forEach(checkbox => {
            checkbox.checked = this.checked;
            checkbox.dispatchEvent(new Event('change'));
        });
    });
}

function initializeCustomRouteSelection() {
    document.querySelectorAll('.custom-route-select').forEach(checkbox => {
        checkbox.addEventListener('change', () => calculateTotals());
    });
}

function initializeServiceSelection() {
    document.querySelectorAll('.service-select').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const config = this.closest('.service-card')?.querySelector('.service-configuration');
            if (config) config.style.display = this.checked ? 'block' : 'none';
            calculateTotals();
        });
    });

    document.querySelectorAll('.service-duration-input, .service-quantity-input').forEach(input => {
        input.addEventListener('input', () => calculateTotals());
    });

    document.getElementById('selectAllServices')?.addEventListener('change', function () {
        document.querySelectorAll('.service-select').forEach(checkbox => {
            checkbox.checked = this.checked;
            checkbox.dispatchEvent(new Event('change'));
        });
    });
}

function initializeCustomItems() {
    let customItemIndex = document.querySelectorAll('.custom-item').length;

    document.getElementById('addCustomItem')?.addEventListener('click', function () {
        const container = document.getElementById('customItemsContainer');

        const item = document.createElement('div');
        item.className = 'custom-item row mb-3';
        item.innerHTML = `
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

        container.appendChild(item);
        bindCustomItemEvents(item);
        customItemIndex++;
    });

    document.querySelectorAll('.custom-item').forEach(bindCustomItemEvents);
}

function bindCustomItemEvents(item) {
    const qty = item.querySelector('.custom-item-qty');
    const price = item.querySelector('.custom-item-price');
    const total = item.querySelector('.custom-item-total');
    const remove = item.querySelector('.remove-item');

    function updateItemTotal() {
        const value = (parseFloat(qty?.value || 0) * parseFloat(price?.value || 0));
        if (total) total.value = value.toFixed(2);
        calculateTotals();
    }

    qty?.addEventListener('input', updateItemTotal);
    price?.addEventListener('input', updateItemTotal);

    remove?.addEventListener('click', function () {
        item.remove();
        calculateTotals();
    });

    updateItemTotal();
}

function initializeTaxCalculation() {
    document.getElementById('tax_rate')?.addEventListener('input', () => calculateTotals());
}

function deleteQuotation(quotationId) {
    if (!confirm('Are you sure you want to delete this quotation? This action cannot be undone.')) return;

    fetch(`/admin/quotations/${quotationId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message || 'Quotation deleted.');
        if (data.success) {
            window.location.href = '{{ route('admin.quotations.index') }}';
        }
    })
    .catch(error => {
        console.error(error);
        alert('An error occurred while deleting the quotation.');
    });
}
</script>
@endsection

@push('styles')
<style>
.selected-route-card {
    background: #f0fff4;
    border-width: 2px;
}

.route-configuration, .service-configuration {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    margin-top: 5px;
}

/* Alert styling */
.alert-light.border {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6 !important;
}

 /* ===== PROMINENT CHECKBOX STYLES ===== */

    /* Custom checkbox container for better visibility */
    .form-check {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }

    /* Make checkboxes large and bold */
    .form-check-input {
        width: 20px !important;
        height: 20px !important;
        margin: 0 !important;
        cursor: pointer !important;
        background-color: #ffffff !important;
        border: 2px solid #3b82f6 !important;
        border-radius: 6px !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        position: relative !important;
        transition: all 0.2s ease !important;
        flex-shrink: 0 !important;
    }

    /* Checked state with bold color */
    .form-check-input:checked {
        background-color: #3b82f6 !important;
        border-color: #3b82f6 !important;
    }

    /* Checkmark icon */
    .form-check-input:checked::before {
        content: "✓" !important;
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        color: white !important;
        font-size: 14px !important;
        font-weight: bold !important;
    }

    /* Hover effect */
    .form-check-input:hover {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
        transform: scale(1.05) !important;
    }

    /* Focus state */
    .form-check-input:focus {
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3) !important;
        border-color: #2563eb !important;
    }

    /* Disabled state */
    .form-check-input:disabled {
        opacity: 0.5 !important;
        cursor: not-allowed !important;
    }

    /* Specific styles for route and service checkboxes */
    .route-select,
    .custom-route-select,
    .service-select {
        width: 20px !important;
        height: 20px !important;
        border: 2px solid #10b981 !important;
        border-radius: 6px !important;
    }

    .route-select:checked,
    .custom-route-select:checked,
    .service-select:checked {
        background-color: #10b981 !important;
        border-color: #10b981 !important;
    }

    /* Select All checkboxes - more prominent */
    #selectAllRoutes,
    #selectAllColocationServices,
    #selectAllCheckbox {
        width: 22px !important;
        height: 22px !important;
        border: 2px solid #0066B3 !important;
        border-radius: 6px !important;
        background-color: #ffffff !important;
    }

    #selectAllRoutes:checked,
    #selectAllColocationServices:checked,
    #selectAllCheckbox:checked {
        background-color: #0066B3 !important;
        border-color: #0066B3 !important;
    }

    /* Route group header checkboxes */
    .route-group-header .form-check-input {
        width: 18px !important;
        height: 18px !important;
        border: 2px solid #6366f1 !important;
    }

    .route-group-header .form-check-input:checked {
        background-color: #6366f1 !important;
        border-color: #6366f1 !important;
    }

    /* Label styling to align with checkboxes */
    .form-check-label {
        cursor: pointer !important;
        font-weight: 500 !important;
        user-select: none !important;
    }

    /* Hover effect on labels */
    .form-check-label:hover {
        color: #3b82f6 !important;
    }

    /* Card checkbox section */
    .card .form-check {
        padding-left: 0 !important;
        margin-bottom: 8px !important;
    }

    /* Make sure checkboxes are always visible */
    input[type="checkbox"] {
        opacity: 1 !important;
        visibility: visible !important;
        display: inline-block !important;
        pointer-events: auto !important;
    }

    /* Dark background checkboxes (for colored headers) */
    .bg-dark .form-check-input,
    .bg-kp-blue .form-check-input,
    .bg-info .form-check-input {
        border-color: #ffffff !important;
        background-color: rgba(255, 255, 255, 0.9) !important;
    }

    .bg-dark .form-check-input:checked,
    .bg-kp-blue .form-check-input:checked,
    .bg-info .form-check-input:checked {
        background-color: #ffffff !important;
    }

    .bg-dark .form-check-input:checked::before,
    .bg-kp-blue .form-check-input:checked::before,
    .bg-info .form-check-input:checked::before {
        color: #0066B3 !important;
    }

    /* Custom checkbox for dark backgrounds */
    .bg-dark .form-check-input,
    .bg-secondary .form-check-input {
        border: 2px solid #ffc107 !important;
    }

    .bg-dark .form-check-input:checked,
    .bg-secondary .form-check-input:checked {
        background-color: #ffc107 !important;
    }

    .bg-dark .form-check-input:checked::before,
    .bg-secondary .form-check-input:checked::before {
        color: #1e293b !important;
    }
</style>
@endpush
