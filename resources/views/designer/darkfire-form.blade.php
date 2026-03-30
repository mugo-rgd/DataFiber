@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-fire me-2"></i> {{ $title }}</h5>
                        <a href="{{ route('designer.darkfire-items', ['table' => $table]) }}"
                           class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ $item ? route('designer.darkfire-items.update', [$table, $item->id ?? $item->service_id]) : route('designer.darkfire-items.store', $table) }}"
                          method="POST">
                        @csrf
                        @if($item)
                            @method($table === 'commercial_routes' && !$item->id ? 'POST' : 'PUT')
                        @endif

                        @if($table === 'commercial_routes')
                            <!-- Commercial Route Form Fields -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Option *</label>
                                    <select name="option" class="form-select" required>
                                        <option value="">Select Option</option>
                                        @foreach($formData['options'] as $option)
                                            <option value="{{ $option }}" {{ old('option', $item->option ?? '') == $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('option')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Route Name *</label>
                                    <input type="text" name="name_of_route" class="form-control"
                                           value="{{ old('name_of_route', $item->name_of_route ?? '') }}" required>
                                    @error('name_of_route')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Region</label>
                                    <input type="text" name="region" class="form-control"
                                           value="{{ old('region', $item->region ?? '') }}">
                                    @error('region')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fiber Cores</label>
                                    <input type="number" name="fiber_cores" class="form-control"
                                           value="{{ old('fiber_cores', $item->fiber_cores ?? '') }}" min="0">
                                    @error('fiber_cores')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cores Required *</label>
                                    <input type="number" name="no_of_cores_required" class="form-control"
                                           value="{{ old('no_of_cores_required', $item->no_of_cores_required ?? '') }}" min="1" required>
                                    @error('no_of_cores_required')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Unit Cost/Km/Month *</label>
                                    <input type="number" step="0.01" name="unit_cost_per_core_per_km_per_month"
                                           class="form-control" value="{{ old('unit_cost_per_core_per_km_per_month', $item->unit_cost_per_core_per_km_per_month ?? '') }}"
                                           min="0" required>
                                    @error('unit_cost_per_core_per_km_per_month')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Distance (Km) *</label>
                                    <input type="number" step="0.01" name="approx_distance_km" class="form-control"
                                           value="{{ old('approx_distance_km', $item->approx_distance_km ?? '') }}" min="0" required>
                                    @error('approx_distance_km')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Capital Expenditure *</label>
                                    <input type="number" step="0.01" name="capital_expenditure" class="form-control"
                                           value="{{ old('capital_expenditure', $item->capital_expenditure ?? '0.00') }}" min="0" required>
                                    @error('capital_expenditure')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Availability *</label>
                                    <select name="availability" class="form-select" required>
                                        @foreach($formData['availability_options'] as $option)
                                            <option value="{{ $option }}" {{ old('availability', $item->availability ?? '') == $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('availability')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Currency *</label>
                                    <select name="currency" class="form-select" required>
                                        @foreach($formData['currency_options'] as $currency)
                                            <option value="{{ $currency }}" {{ old('currency', $item->currency ?? '') == $currency ? 'selected' : '' }}>
                                                {{ $currency }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('currency')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tech Type *</label>
                                    <select name="tech_type" class="form-select" required>
                                        @foreach($formData['tech_type_options'] as $tech)
                                            <option value="{{ $tech }}" {{ old('tech_type', $item->tech_type ?? '') == $tech ? 'selected' : '' }}>
                                                {{ $tech }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tech_type')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>

                        @else
                            <!-- Colocation Form Fields -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Service ID *</label>
                                    <input type="text" name="service_id" class="form-control"
                                           value="{{ old('service_id', $item->service_id ?? '') }}" required>
                                    @error('service_id')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Service Category *</label>
                                    <input type="text" name="service_category" class="form-control"
                                           value="{{ old('service_category', $item->service_category ?? '') }}" required>
                                    @error('service_category')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Service Type *</label>
                                    <input type="text" name="service_type" class="form-control"
                                           value="{{ old('service_type', $item->service_type ?? '') }}" required>
                                    @error('service_type')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status *</label>
                                    <select name="fibrestatus" class="form-select" required>
                                        @foreach($formData['status_options'] as $status)
                                            <option value="{{ $status }}" {{ old('fibrestatus', $item->fibrestatus ?? '') == $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('fibrestatus')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Specifications</label>
                                    <textarea name="specifications" class="form-control" rows="3">{{ old('specifications', $item->specifications ?? '') }}</textarea>
                                    @error('specifications')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Power (KW)</label>
                                    <input type="number" step="0.01" name="power_kw" class="form-control"
                                           value="{{ old('power_kw', $item->power_kw ?? '') }}" min="0">
                                    @error('power_kw')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Space (SQM)</label>
                                    <input type="number" step="0.01" name="space_sqm" class="form-control"
                                           value="{{ old('space_sqm', $item->space_sqm ?? '') }}" min="0">
                                    @error('space_sqm')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">One-off Rate *</label>
                                    <input type="number" step="0.01" name="oneoff_rate" class="form-control"
                                           value="{{ old('oneoff_rate', $item->oneoff_rate ?? '') }}" min="0" required>
                                    @error('oneoff_rate')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Recurrent/Year *</label>
                                    <input type="number" step="0.01" name="recurrent_per_Annum" class="form-control"
                                           value="{{ old('recurrent_per_Annum', $item->recurrent_per_Annum ?? '') }}" min="0" required>
                                    @error('recurrent_per_Annum')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Monthly Price (USD)</label>
                                    <input type="number" step="0.01" name="monthly_price_usd" class="form-control"
                                           value="{{ old('monthly_price_usd', $item->monthly_price_usd ?? '') }}" min="0">
                                    @error('monthly_price_usd')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Setup Fee (USD)</label>
                                    <input type="number" step="0.01" name="setup_fee_usd" class="form-control"
                                           value="{{ old('setup_fee_usd', $item->setup_fee_usd ?? '') }}" min="0">
                                    @error('setup_fee_usd')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Min Contract (Months)</label>
                                    <input type="number" name="min_contract_months" class="form-control"
                                           value="{{ old('min_contract_months', $item->min_contract_months ?? '') }}" min="1">
                                    @error('min_contract_months')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> {{ $item ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
