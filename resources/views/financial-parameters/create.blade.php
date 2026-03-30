{{-- resources/views/financial-parameters/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Create Financial Parameter</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('finance.financial-parameters.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="parameter_name">Parameter Name *</label>
                            <select name="parameter_name" id="parameter_name" class="form-control" required>
                                <option value="">Select Parameter</option>
                                @foreach($predefinedParameters as $key => $label)
                                    <option value="{{ $key }}" {{ old('parameter_name') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parameter_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="parameter_value">Parameter Value *</label>
                            <input type="number" step="0.000001" name="parameter_value" id="parameter_value"
                                   class="form-control" value="{{ old('parameter_value') }}" required>
                            @error('parameter_value')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="effective_from">Effective From *</label>
                            <input type="date" name="effective_from" id="effective_from"
                                   class="form-control" value="{{ old('effective_from') }}" required>
                            @error('effective_from')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="currency_code">Currency Code</label>
                            <input type="text" name="currency_code" id="currency_code"
                                   class="form-control" value="{{ old('currency_code') }}"
                                   placeholder="e.g., KES, EUR, GBP">
                            <small class="form-text text-muted">Only for exchange rate parameters</small>
                            @error('currency_code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control"
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Create Parameter</button>
                        <a href="{{ route('finance.financial-parameters.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('parameter_name').addEventListener('change', function() {
    const paramName = this.value;
    const currencyField = document.getElementById('currency_code');

    // Auto-fill currency code for exchange rates
    if (paramName.includes('_to_usd')) {
        const currencyCode = paramName.split('_to_usd')[0].toUpperCase();
        currencyField.value = currencyCode;
    } else {
        currencyField.value = '';
    }
});
</script>
@endsection
