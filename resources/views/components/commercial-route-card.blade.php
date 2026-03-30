@props(['route', 'designRequest'])

@php
    $defaultCores = $route->no_of_cores_required;
    $defaultMonthly = $route->calculateMonthlyCost();
    $defaultTotal = $route->calculateTotalContractValue($defaultCores, 12);
@endphp

{{-- Fixed line 10: Added missing closing parenthesis after 'info' --}}
<div class="card route-card mb-3 border-left-{{ $route->option === 'Premium' ? 'warning' : ($route->option === 'Non Premium' ? 'primary' : 'info') }} border-left-3">
    <div class="card-body">
        <div class="form-check mb-3">
            <input class="form-check-input route-select"
                   type="checkbox"
                   name="selected_routes[]"
                   value="{{ $route->id }}"
                   id="route_{{ $route->id }}"
                   data-route-id="{{ $route->id }}"
                   data-monthly-cost="{{ $defaultMonthly }}"
                   data-capital-expenditure="{{ $route->capital_expenditure }}">
            <label class="form-check-label w-100" for="route_{{ $route->id }}">
                <div class="route-header mb-2">
                    <h6 class="mb-1 text-dark">
                        <strong>{{ $route->name_of_route }}</strong>
                        <span class="badge bg-{{ $route->option === 'Premium' ? 'warning' : ($route->option === 'Non Premium' ? 'primary' : 'info') }} ms-2">
                            {{ $route->option }}
                        </span>
                        <span class="badge bg-secondary ms-1">
                            {{ $route->tech_type }}
                        </span>
                    </h6>
                    <small class="text-muted">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ $route->region ?? 'N/A' }}
                    </small>
                </div>

                <div class="route-details ps-3 ms-2 border-start border-2 border-light">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Route Specifications -->
                            <div class="mb-2">
                                <div class="row g-1">
                                    <div class="col-6">
                                        <span class="text-muted small">
                                            <i class="fas fa-road me-1"></i>
                                            <strong>Distance:</strong> {{ number_format($route->approx_distance_km, 2) }} km
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted small">
                                            <i class="fas fa-bolt me-1"></i>
                                            <strong>Tech:</strong> {{ $route->technology_type }}
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted small">
                                            <i class="fas fa-network-wired me-1"></i>
                                            <strong>Available Cores:</strong> {{ $route->fiber_cores ?? 'Unlimited' }}
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted small">
                                            <i class="fas fa-wrench me-1"></i>
                                            <strong>Required Cores:</strong> {{ $defaultCores }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing Details -->
                            <div class="mb-2">
                                <div class="row g-1">
                                    <div class="col-6">
                                        <span class="text-muted small">
                                            <i class="fas fa-dollar-sign me-1"></i>
                                            <strong>Unit Cost:</strong> {{ $route->currency }} {{ number_format($route->unit_cost_per_core_per_km_per_month, 4) }}/core/km/month
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted small">
                                            <i class="fas fa-calculator me-1"></i>
                                            <strong>Monthly/Core:</strong> {{ $route->currency }} {{ number_format($route->unit_cost_per_core_per_km_per_month * $route->approx_distance_km, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Monthly Cost -->
                            <div class="mb-1">
                                <span class="text-success small">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <strong>Monthly Cost ({{ $defaultCores }} cores):</strong> {{ $route->currency }} {{ number_format($defaultMonthly, 2) }}/month
                                </span>
                            </div>

                            <!-- Capital Expenditure -->
                            @if($route->capital_expenditure > 0)
                            <div class="mb-1">
                                <span class="text-info small">
                                    <i class="fas fa-tools me-1"></i>
                                    <strong>Setup/CAPEX:</strong> {{ $route->currency }} {{ number_format($route->capital_expenditure, 2) }}
                                </span>
                            </div>
                            @endif

                            <!-- Currency -->
                            <div class="mb-1">
                                <span class="text-warning small">
                                    <i class="fas fa-money-bill-wave me-1"></i>
                                    <strong>Currency:</strong> {{ $route->currency }}
                                </span>
                            </div>

                            <!-- Availability -->
                            <div class="mb-1">
                                <span class="text-{{ $route->availability === 'YES' ? 'success' : 'danger' }} small">
                                    <i class="fas fa-{{ $route->availability === 'YES' ? 'check' : 'times' }}-circle me-1"></i>
                                    <strong>Availability:</strong> {{ $route->availability }}
                                </span>
                            </div>
                        </div>

                        <!-- Configuration Panel -->
                        <div class="col-md-4">
                            <div class="route-configuration p-2 bg-light rounded" style="display: none;">
                                <h6 class="small fw-bold mb-2 text-center">Configure Route</h6>

                                <!-- Cores -->
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">
                                        <i class="fas fa-network-wired me-1"></i>Number of Cores
                                    </label>
                                    <input type="number"
                                           name="route_cores[{{ $route->id }}]"
                                           class="form-control form-control-sm route-cores-input"
                                           value="{{ $defaultCores }}"
                                           min="1"
                                           max="{{ $route->fiber_cores ?? 999 }}"
                                           data-route-id="{{ $route->id }}">
                                </div>

                                <!-- Duration -->
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">
                                        <i class="fas fa-calendar me-1"></i>Duration (Months)
                                    </label>
                                    <input type="number"
                                           name="route_duration[{{ $route->id }}]"
                                           class="form-control form-control-sm route-duration-input"
                                           value="12"
                                           min="1"
                                           max="120"
                                           data-route-id="{{ $route->id }}">
                                </div>

                                <!-- Cost Summary -->
                                <div class="route-cost small bg-white p-2 rounded border">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Monthly:</span>
                                        <span class="monthly-cost fw-bold text-success"
                                              data-route-id="{{ $route->id }}"
                                              data-base-monthly="{{ $defaultMonthly }}">
                                            {{ $route->currency }} {{ number_format($defaultMonthly, 2) }}
                                        </span>
                                    </div>
                                    @if($route->capital_expenditure > 0)
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Setup/CAPEX:</span>
                                        <span class="capex-cost text-info"
                                              data-base-capex="{{ $route->capital_expenditure }}">
                                            {{ $route->currency }} {{ number_format($route->capital_expenditure, 2) }}
                                        </span>
                                    </div>
                                    @endif
                                    <hr class="my-1">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-dark fw-bold">Total:</span>
                                        <span class="total-cost fw-bold text-primary"
                                              data-route-id="{{ $route->id }}">
                                            {{ $route->currency }} {{ number_format($defaultTotal, 2) }}
                                        </span>
                                    </div>
                                    <small class="text-muted d-block text-center mt-1">
                                        ({{ $defaultCores }} cores × {{ number_format($route->unit_cost_per_core_per_km_per_month * $route->approx_distance_km, 2) }}/core/month × 12 months)
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </label>
        </div>
    </div>
</div>
