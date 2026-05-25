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
                <div class="alert alert-kp-warning">
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

        <!-- Design Request Info -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header bg-kp-blue text-white">
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
                                      placeholder="Add any specific customer requirements or notes...">{{ old('customer_notes', $quotation->customer_notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Selection Section -->
        <div class="row">
            <!-- Commercial Routes -->
            {{-- Commercial Routes --}}
<div class="col-lg-6">
    <div class="card shadow mb-4">
        <div class="card-header bg-kp-green text-white d-flex justify-content-between align-items-center">
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
                                            <span class="badge bg-success ms-2">
                                                Selected
                                            </span>
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
                                                <label class="form-label small">Cores Required</label>

                                                <input type="number"
                                                       name="route_cores[{{ $route->id }}]"
                                                       class="form-control form-control-sm cores-input"
                                                       value="{{ $routePivot->quantity ?? $route->no_of_cores_required ?? 1 }}"
                                                       min="1"
                                                       data-route-id="{{ $route->id }}">
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label small">Duration (Months)</label>

                                                <input type="number"
                                                       name="route_duration[{{ $route->id }}]"
                                                       class="form-control form-control-sm duration-input"
                                                       value="{{ $routePivot->duration_months ?? 12 }}"
                                                       min="1"
                                                       data-route-id="{{ $route->id }}">
                                            </div>

                                            <div class="route-cost small">
                                                <strong>
                                                    Monthly:
                                                    <span class="monthly-cost" data-route-id="{{ $route->id }}">
                                                        ${{ number_format($monthlyBaseCost * ($routePivot->quantity ?? $route->no_of_cores_required ?? 1), 2) }}
                                                    </span>
                                                </strong>

                                                <br>

                                                <strong>
                                                    Total:
                                                    <span class="total-cost" data-route-id="{{ $route->id }}">
                                                        ${{ number_format(
                                                            ($monthlyBaseCost * ($routePivot->quantity ?? $route->no_of_cores_required ?? 1) * ($routePivot->duration_months ?? 12))
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

            {{-- Custom Routes --}}
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
                    @foreach($customRoutes as $route)
                        @php
                            $isSelected = $quotation->customRoutes->contains('id', $route->id);
                            $routePivot = $quotation->customRoutes->firstWhere('id', $route->id)?->pivot;
                            $monthlyCost = $routePivot->monthly_cost ?? $route->monthly_cost;
                            $capex = $routePivot->capital_expenditure ?? $route->capital_expenditure;
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
                                           data-duration="{{ $route->contract_duration_months ?? 12 }}"
                                           {{ $isSelected ? 'checked' : '' }}>

                                    <label class="form-check-label fw-bold" for="custom_route_{{ $route->id }}">
                                        {{ $route->name_of_route }}
                                        <span class="badge bg-warning text-dark ms-1">Custom</span>
                                    </label>
                                </div>

                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $route->region ?? 'N/A' }}<br>
                                    <i class="fas fa-network-wired me-1"></i>{{ $route->tech_type }} |
                                    {{ $route->option }}<br>
                                    <i class="fas fa-ruler me-1"></i>{{ $route->approx_distance_km }} km<br>
                                    <i class="fas fa-circle-nodes me-1"></i>{{ $route->no_of_cores_required }} cores
                                </small>

                                <div class="mt-2 small">
                                    <strong>Monthly:</strong>
                                    ${{ number_format($monthlyCost, 2) }}<br>

                                    <strong>Duration:</strong>
                                    {{ $route->contract_duration_months ?? 12 }} months<br>

                                    <strong>CAPEX:</strong>
                                    ${{ number_format($capex, 2) }}<br>

                                    <strong>Total:</strong>
                                    <span class="custom-route-total" data-custom-route-id="{{ $route->id }}">
                                        ${{ number_format(($monthlyCost * ($route->contract_duration_months ?? 12)) + $capex, 2) }}
                                    </span>
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
                    <div class="card-header bg-kp-yellow text-dark d-flex justify-content-between align-items-center">
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
                                    @php
                                        $isSelected = $quotation->colocationServices->contains($service->service_id);
                                        $servicePivot = $quotation->colocationServices->find($service->service_id)?->pivot;
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
                                                       {{ $isSelected ? 'checked' : '' }}>
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
                                                    <div class="service-configuration" style="{{ $isSelected ? 'display: block;' : 'display: none;' }}">
                                                        <div class="mb-2">
                                                            <label class="form-label small">Duration (Months)</label>
                                                            <input type="number"
                                                                   name="service_duration[{{ $service->service_id }}]"
                                                                   class="form-control form-control-sm service-duration-input"
                                                                   value="{{ $servicePivot->duration_months ?? ($service->min_contract_months ?? 12) }}"
                                                                   min="{{ $service->min_contract_months ?? 1 }}"
                                                                   data-service-id="{{ $service->service_id }}">
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label small">Quantity</label>
                                                            <input type="number"
                                                                   name="service_quantity[{{ $service->service_id }}]"
                                                                   class="form-control form-control-sm service-quantity-input"
                                                                   value="{{ $servicePivot->quantity ?? 1 }}"
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
                                                   class="form-control" value="{{ $item['quantity'] }}" min="1" placeholder="Qty">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="custom_items[{{ $index }}][unit_price]"
                                                   class="form-control" step="0.01" min="0"
                                                   value="{{ $item['unit_price'] }}" placeholder="Unit price">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="custom_items[{{ $index }}][total]"
                                                   class="form-control" value="{{ number_format($item['total'], 2) }}" placeholder="Total" readonly>
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
                            @endif
                        </div>
                        <button type="button" id="addCustomItem" class="btn btn-outline-kp-primary btn-sm">
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
                    <div class="card-header bg-kp-blue text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-calculator me-2"></i>Pricing Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong>Commercial + Custom Routes Total:</strong>
                            </div>
                           @php
    $commercialTotal = collect($quotation->line_items)->where('type', 'commercial_route')->sum('total');

    $customRoutesTotal = $quotation->customRoutes->sum(function ($route) {
        $monthly = $route->pivot->monthly_cost ?? $route->monthly_cost ?? 0;
        $capex = $route->pivot->capital_expenditure ?? $route->capital_expenditure ?? 0;
        $duration = $route->contract_duration_months ?? 12;

        return ($monthly * $duration) + $capex;
    });
@endphp

<span id="routesTotal">
    ${{ number_format($commercialTotal + $customRoutesTotal, 2) }}
</span>
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
                                <span id="total_amount" class="h5 text-kp-blue">${{ number_format($quotation->total_amount, 2) }}</span>
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
                                <a href="{{ route('designer.requests.index', $quotation) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                @if(auth()->user()->role === 'admin')
                                    <button type="button" class="btn btn-danger" onclick="deleteQuotation({{ $quotation->id }})">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
                                @endif
                            </div>
                            <div>
                                <button type="submit" class="btn btn-kp-primary">
                                    <i class="fas fa-save me-2"></i>Update Quotation
                                </button>
                                @if(auth()->user()->role === 'admin')
                                    <button type="button" class="btn btn-kp-success" onclick="sendQuotation({{ $quotation->id }})">
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

    // initialize existing selected routes
    document.querySelectorAll('.route-select:checked').forEach(checkbox => {
        const routeId = checkbox.dataset.routeId;

        if(routeId){
            calculateRouteCost(routeId);
        }
    });

    // initialize existing selected services
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

function setValue(id, value) {
    const el = document.getElementById(id);
    if (el) el.value = value;
}

function calculateRouteCost(routeId) {
    const checkbox = document.getElementById(`route_${routeId}`);
    if (!checkbox) return 0;

    const card = checkbox.closest('.route-card');
    if (!card) return 0;

    // const monthlyBase = Number(checkbox.dataset.monthlyCost ?? 0);
    const monthlyBase = parseFloat(checkbox.dataset.monthlyCost || 0);
console.log(
    'Route:',
    routeId,
    'Monthly:',
    monthlyBase
);
    const capex = parseFloat(checkbox.dataset.capex || checkbox.dataset.capitalExpenditure || 0);

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
    const oneoffFee = parseFloat(checkbox.dataset.oneoffFee || 0);

    const durationInput = card.querySelector(`input[name="service_duration[${serviceId}]"]`);
    const quantityInput = card.querySelector(`input[name="service_quantity[${serviceId}]"]`);

    const duration = parseInt(durationInput?.value || 12);
    const quantity = parseInt(quantityInput?.value || 1);

    const monthly = monthlyRate * quantity;
    const total = (monthly * duration) + (setupFee * quantity) + (oneoffFee * quantity);

    const monthlyEl = card.querySelector(`.monthly-cost[data-service-id="${serviceId}"]`);
    const totalEl = card.querySelector(`.total-cost[data-service-id="${serviceId}"]`);

    if (monthlyEl) monthlyEl.textContent = money(monthly);
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

    document.querySelectorAll('input[name^="custom_items"][name$="[total]"]').forEach(input => {
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
            const config = this.closest('.route-card')?.querySelector('.route-configuration');
            if (config) config.style.display = this.checked ? 'block' : 'none';
            calculateTotals();
        });
    });

    document.querySelectorAll('.cores-input, .duration-input').forEach(input => {
        input.addEventListener('input', calculateTotals);
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
        checkbox.addEventListener('change', calculateTotals);
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
        input.addEventListener('input', calculateTotals);
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
    const qty = item.querySelector('.custom-item-qty, input[name$="[quantity]"]');
    const price = item.querySelector('.custom-item-price, input[name$="[unit_price]"]');
    const total = item.querySelector('.custom-item-total, input[name$="[total]"]');
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
    document.getElementById('tax_rate')?.addEventListener('input', calculateTotals);
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

function sendQuotation(quotationId) {
    if (!confirm('Are you sure you want to update and send this quotation to the customer?')) return;

    const form = document.getElementById('quotationForm');

    let actionInput = form.querySelector('input[name="action"]');
    if (!actionInput) {
        actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        form.appendChild(actionInput);
    }

    actionInput.value = 'send';
    calculateTotals();
    form.submit();
}
</script>
@endsection

@push('styles')
<style>
.selected-route-card {
    background: #f0fff4;
    border-width: 2px;
}
</style>
@endpush
