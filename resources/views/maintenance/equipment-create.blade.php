@extends('layouts.app')

@section('title', 'Add New Equipment')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Add New Equipment
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('maintenance.equipment.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Equipment Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required
                                           placeholder="Enter equipment name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="model" class="form-label">Model</label>
                                    <input type="text" class="form-control @error('model') is-invalid @enderror"
                                           id="model" name="model" value="{{ old('model') }}"
                                           placeholder="Enter model number">
                                    @error('model')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="serial_number" class="form-label">Serial Number</label>
                                    <input type="text" class="form-control @error('serial_number') is-invalid @enderror"
                                           id="serial_number" name="serial_number" value="{{ old('serial_number') }}"
                                           placeholder="Enter serial number">
                                    @error('serial_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select @error('status') is-invalid @enderror"
                                            id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="in_use" {{ old('status') == 'in_use' ? 'selected' : '' }}>In Use</option>
                                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                        <option value="retired" {{ old('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="Enter equipment description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                                           id="location" name="location" value="{{ old('location') }}"
                                           placeholder="Enter storage location">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="purchase_date" class="form-label">Purchase Date</label>
                                    <input type="date" class="form-control @error('purchase_date') is-invalid @enderror"
                                           id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}">
                                    @error('purchase_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_calibration" class="form-label">Last Calibration Date</label>
                                    <input type="date" class="form-control @error('last_calibration') is-invalid @enderror"
                                           id="last_calibration" name="last_calibration" value="{{ old('last_calibration') }}">
                                    @error('last_calibration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="next_calibration" class="form-label">Next Calibration Date</label>
                                    <input type="date" class="form-control @error('next_calibration') is-invalid @enderror"
                                           id="next_calibration" name="next_calibration" value="{{ old('next_calibration') }}">
                                    @error('next_calibration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="specifications" class="form-label">Technical Specifications</label>
                            <textarea class="form-control @error('specifications') is-invalid @enderror"
                                      id="specifications" name="specifications" rows="3"
                                      placeholder="Enter technical specifications">{{ old('specifications') }}</textarea>
                            @error('specifications')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="2"
                                      placeholder="Any additional notes or comments">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('maintenance.equipment.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Add Equipment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set default purchase date to today
        const purchaseDateField = document.getElementById('purchase_date');
        if (!purchaseDateField.value) {
            const today = new Date().toISOString().split('T')[0];
            purchaseDateField.value = today;
        }

        // Auto-set next calibration date based on last calibration
        const lastCalibrationField = document.getElementById('last_calibration');
        const nextCalibrationField = document.getElementById('next_calibration');

        lastCalibrationField.addEventListener('change', function() {
            if (this.value && !nextCalibrationField.value) {
                const lastCalibration = new Date(this.value);
                const nextCalibration = new Date(lastCalibration);
                nextCalibration.setFullYear(nextCalibration.getFullYear() + 1); // Default to 1 year later
                nextCalibrationField.value = nextCalibration.toISOString().split('T')[0];
            }
        });
    });
</script>
@endsection
@endsection
