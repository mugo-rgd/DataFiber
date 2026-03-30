{{-- resources/views/financial-parameters/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Edit Financial Parameter</h5>
                </div>
                <div class="card-body">
                    {{-- Debugging alerts --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6>Validation Errors:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('finance.financial-parameters.update', $financialParameter) }}" method="POST" id="updateForm">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="parameter_name">Parameter Name</label>
                            <input type="text" class="form-control" value="{{ $financialParameter->parameter_name }}" readonly>
                            <small class="form-text text-muted">Parameter name cannot be changed</small>
                        </div>

                        <div class="form-group">
                            <label for="parameter_value">Parameter Value *</label>
                            <input type="number" step="0.000001" name="parameter_value" id="parameter_value"
                                   class="form-control" value="{{ old('parameter_value', $financialParameter->parameter_value) }}" required>
                            @error('parameter_value')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="effective_from">Effective From</label>
                            <input type="date" class="form-control" value="{{ $financialParameter->effective_from->format('Y-m-d') }}" readonly>
                            <small class="form-text text-muted">Effective date cannot be changed. Create a new parameter for a new date.</small>
                        </div>

                        <div class="form-group">
                            <label for="currency_code">Currency Code</label>
                            <input type="text" class="form-control" value="{{ $financialParameter->currency_code ?? 'N/A' }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $financialParameter->description) }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary" id="updateButton">
                            <i class="fas fa-save"></i> Update Parameter
                        </button>
                        <a href="{{ route('finance.financial-parameters.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Parameter Information</h6>
                </div>
                <div class="card-body">
                    <p><strong>Created:</strong> {{ $financialParameter->created_at->format('M d, Y H:i') }}</p>
                    <p><strong>By:</strong> {{ $financialParameter->creator->name ?? 'Unknown' }}</p>
                    <p><strong>Last Updated:</strong> {{ $financialParameter->updated_at->format('M d, Y H:i') }}</p>
                    <p><strong>By:</strong> {{ $financialParameter->updater->name ?? 'Unknown' }}</p>
                    <p><strong>Country:</strong> {{ $financialParameter->country_code }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge badge-{{ $financialParameter->effective_to ? 'secondary' : 'success' }}">
                            {{ $financialParameter->effective_to ? 'Expired' : 'Active' }}
                        </span>
                    </p>
                </div>
            </div>

            @if(!$financialParameter->effective_to)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title">Danger Zone</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('finance.financial-parameters.destroy', $financialParameter) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this parameter? This action cannot be undone.')">
                            <i class="fas fa-trash"></i> Delete Parameter
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Edit page loaded');

    const form = document.getElementById('updateForm');
    const button = document.getElementById('updateButton');
    const parameterValue = document.getElementById('parameter_value');

    if (!form) {
        console.error('Form not found!');
        return;
    }

    if (!button) {
        console.error('Button not found!');
        return;
    }

    console.log('Form and button found successfully');

    // Add click event to button for debugging
    button.addEventListener('click', function(e) {
        console.log('Update button clicked');
        console.log('Parameter value:', parameterValue.value);
        console.log('Form action:', form.action);

        // Validate form before submission
        if (!form.checkValidity()) {
            console.log('Form is invalid');
            form.reportValidity();
            e.preventDefault();
            return;
        }

        console.log('Form is valid, submitting...');

        // Show loading state
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    });

    // Also log form submission
    form.addEventListener('submit', function(e) {
        console.log('Form submit event triggered');
    });

    // Test if button is clickable
    console.log('Button type:', button.type);
    console.log('Button disabled:', button.disabled);
});
</script>
@endsection
