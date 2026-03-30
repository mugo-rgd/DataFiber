@extends('layouts.app')

@section('title', 'Create Work Order')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Create Work Order
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('maintenance.work-orders.store') }}" method="POST">
                        @csrf

                        <!-- Maintenance Request Selection -->
                        <div class="mb-4">
                            <label for="maintenance_request_id" class="form-label">Maintenance Request *</label>
                            <select class="form-select @error('maintenance_request_id') is-invalid @enderror"
                                    id="maintenance_request_id" name="maintenance_request_id" required>
                                <option value="">Select Maintenance Request</option>
                                @foreach($maintenanceRequests as $request)
                                    <option value="{{ $request->id }}" {{ old('maintenance_request_id') == $request->id ? 'selected' : '' }}>
                                        #{{ $request->id }} - {{ $request->title }}
                                        @if($request->equipment)
                                            ({{ $request->equipment->name }})
                                        @endif
                                        @if($request->designRequest && $request->designRequest->customer)
                                            - {{ $request->designRequest->customer->name }}
                                        @endif
                                        - Priority: {{ ucfirst($request->priority) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('maintenance_request_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Select the maintenance request that needs a work order
                            </small>
                        </div>

                        <!-- Technician Assignment -->
                        <div class="mb-4">
                            <label for="assigned_technician" class="form-label">Assign to Technician *</label>
                            <select class="form-select @error('assigned_technician') is-invalid @enderror"
                                    id="assigned_technician" name="assigned_technician" required>
                                <option value="">Select Technician</option>
                                @foreach($technicians as $technician)
                                    <option value="{{ $technician->id }}" {{ old('assigned_technician') == $technician->id ? 'selected' : '' }}>
                                        {{ $technician->name }}
                                        @if($technician->employee_id)
                                            ({{ $technician->employee_id }})
                                        @endif
                                        - {{ $technician->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_technician')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Due Date</label>
                                    <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror"
                                           id="due_date" name="due_date" value="{{ old('due_date') }}">
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Set a deadline for this work order
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_hours" class="form-label">Estimated Hours</label>
                                    <input type="number" class="form-control @error('estimated_hours') is-invalid @enderror"
                                           id="estimated_hours" name="estimated_hours"
                                           value="{{ old('estimated_hours') }}" min="0.5" step="0.5" placeholder="2.5">
                                    @error('estimated_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
                                    <small class="form-text text-muted">
                                        Estimated time to complete (in hours)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="mb-4">
                            <label for="instructions" class="form-label">Work Instructions</label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror"
                                      id="instructions" name="instructions" rows="4"
                                      placeholder="Provide detailed instructions for the technician...">{{ old('instructions') }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Specific instructions, safety requirements, or special tools needed
                            </small>
                        </div>

                        <!-- Request Preview (Dynamic) -->
                        <div class="mb-4" id="requestPreview" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Request Preview</h6>
                                </div>
                                <div class="card-body">
                                    <div id="previewContent">
                                        <!-- Dynamic content will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('maintenance.work-orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Create Work Order
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
        const requestSelect = document.getElementById('maintenance_request_id');
        const previewDiv = document.getElementById('requestPreview');
        const previewContent = document.getElementById('previewContent');
        const dueDateField = document.getElementById('due_date');

        // Set default due date to 3 days from now if not set
        if (!dueDateField.value) {
            const threeDaysLater = new Date();
            threeDaysLater.setDate(threeDaysLater.getDate() + 3);
            dueDateField.value = threeDaysLater.toISOString().slice(0, 16);
        }

        // Show request preview when a request is selected
        requestSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (this.value) {
                // Extract request details from option text (you could enhance this with AJAX)
                const optionText = selectedOption.text;
                previewContent.innerHTML = `
                    <h6>${optionText}</h6>
                    <p class="text-muted">Details will be shown here when a request is selected.</p>
                `;
                previewDiv.style.display = 'block';
            } else {
                previewDiv.style.display = 'none';
            }
        });

        // Trigger change event on page load if there's a value
        if (requestSelect.value) {
            requestSelect.dispatchEvent(new Event('change'));
        }

        // Auto-fill instructions based on maintenance type (if available)
        requestSelect.addEventListener('change', function() {
            if (this.value && !document.getElementById('instructions').value) {
                // You could enhance this with AJAX to get actual maintenance type
                const selectedText = this.options[this.selectedIndex].text.toLowerCase();

                let defaultInstructions = '';
                if (selectedText.includes('preventive')) {
                    defaultInstructions = 'Perform routine preventive maintenance as per schedule. Check all components and replace consumables if needed.';
                } else if (selectedText.includes('corrective') || selectedText.includes('repair')) {
                    defaultInstructions = 'Diagnose and repair the reported issue. Document findings and parts used.';
                } else if (selectedText.includes('calibration')) {
                    defaultInstructions = 'Perform calibration according to manufacturer specifications. Document calibration results.';
                } else if (selectedText.includes('inspection')) {
                    defaultInstructions = 'Conduct thorough inspection. Document condition and any recommendations.';
                }

                if (defaultInstructions) {
                    document.getElementById('instructions').value = defaultInstructions;
                }
            }
        });
    });
</script>
@endsection
@endsection
