{{-- resources/views/maintenance/requests/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Maintenance Request')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Create Maintenance Request
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('maintenance.requests.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Request Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority *</label>
                                    <select class="form-select @error('priority') is-invalid @enderror"
                                            id="priority" name="priority" required>
                                        <option value="">Select Priority</option>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="equipment_id" class="form-label">Equipment</label>
                                    <select class="form-select @error('equipment_id') is-invalid @enderror"
                                            id="equipment_id" name="equipment_id">
                                        <option value="">Select Equipment (Optional)</option>
                                        @foreach($availableEquipment as $equipment)
                                            <option value="{{ $equipment->id }}" {{ old('equipment_id') == $equipment->id ? 'selected' : '' }}>
                                                {{ $equipment->name }} - {{ $equipment->model }} ({{ $equipment->serial_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('equipment_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="maintenance_type" class="form-label">Maintenance Type *</label>
                                    <select class="form-select @error('maintenance_type') is-invalid @enderror"
                                            id="maintenance_type" name="maintenance_type" required>
                                        <option value="">Select Type</option>
                                        <option value="preventive" {{ old('maintenance_type') == 'preventive' ? 'selected' : '' }}>Preventive Maintenance</option>
                                        <option value="corrective" {{ old('maintenance_type') == 'corrective' ? 'selected' : '' }}>Corrective Maintenance</option>
                                        <option value="emergency" {{ old('maintenance_type') == 'emergency' ? 'selected' : '' }}>Emergency Repair</option>
                                        <option value="calibration" {{ old('maintenance_type') == 'calibration' ? 'selected' : '' }}>Calibration</option>
                                        <option value="inspection" {{ old('maintenance_type') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                                    </select>
                                    @error('maintenance_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="scheduled_date" class="form-label">Scheduled Date</label>
                                    <input type="datetime-local" class="form-control @error('scheduled_date') is-invalid @enderror"
                                           id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date') }}">
                                    @error('scheduled_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_duration" class="form-label">Estimated Duration (hours)</label>
                                    <input type="number" class="form-control @error('estimated_duration') is-invalid @enderror"
                                           id="estimated_duration" name="estimated_duration"
                                           value="{{ old('estimated_duration') }}" min="0.5" step="0.5">
                                    @error('estimated_duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Create Maintenance Request
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
    // Auto-set scheduled date to current date/time if not set
    document.addEventListener('DOMContentLoaded', function() {
        const scheduledDateField = document.getElementById('scheduled_date');
        if (!scheduledDateField.value) {
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            scheduledDateField.value = now.toISOString().slice(0, 16);
        }

        // Add character counter for description
        const descriptionField = document.getElementById('description');
        const descriptionCounter = document.createElement('small');
        descriptionCounter.className = 'form-text text-muted';
        descriptionCounter.textContent = '0 characters';
        descriptionField.parentNode.appendChild(descriptionCounter);

        descriptionField.addEventListener('input', function() {
            descriptionCounter.textContent = this.value.length + ' characters';
        });
    });
</script>
@endsection
@endsection
