@extends('layouts.app')

@section('title', 'Create Work Order')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Create Work Order
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('maintenance.work-orders.store') }}" method="POST" id="workOrderForm">
                        @csrf

                        <!-- Maintenance Request Selection -->
                        <div class="mb-4">
                            <label for="maintenance_request_id" class="form-label">Maintenance Request <span class="text-danger">*</span></label>
                            <select class="form-select @error('maintenance_request_id') is-invalid @enderror"
                                    id="maintenance_request_id" name="maintenance_request_id" required>
                                <option value="">-- Select Maintenance Request --</option>
                                @foreach($maintenanceRequests as $req)
                                    <option value="{{ $req->id }}"
                                        data-request-id="{{ $req->id }}"
                                        data-title="{{ $req->title }}"
                                        data-description="{{ $req->description }}"
                                        data-priority="{{ $req->priority }}"
                                        data-equipment="{{ $req->equipment ? $req->equipment->name : 'N/A' }}"
                                        data-customer="{{ $req->designRequest && $req->designRequest->customer ? $req->designRequest->customer->name : 'N/A' }}"
                                        data-location="{{ $req->location ?? 'N/A' }}"
                                        {{ old('maintenance_request_id') == $req->id ? 'selected' : '' }}>
                                        #{{ $req->id }} - {{ $req->title }}
                                        @if($req->equipment)
                                            ({{ $req->equipment->name }})
                                        @endif
                                        @if($req->designRequest && $req->designRequest->customer)
                                            - {{ $req->designRequest->customer->name }}
                                        @endif
                                        - Priority: {{ ucfirst($req->priority) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('maintenance_request_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Technician Assignment -->
                        <div class="mb-4">
                            <label for="assigned_technician" class="form-label">Assign to Technician <span class="text-danger">*</span></label>
                            <select class="form-select @error('assigned_technician') is-invalid @enderror"
                                    id="assigned_technician" name="assigned_technician" required>
                                <option value="">-- Select Technician --</option>
                                @foreach($technicians as $technician)
                                    <option value="{{ $technician->id }}" {{ old('assigned_technician') == $technician->id ? 'selected' : '' }}>
                                        {{ $technician->name }}
                                        @if($technician->employee_id)
                                            ({{ $technician->employee_id }})
                                        @endif
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
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_hours" class="form-label">Estimated Hours</label>
                                    <input type="number" step="0.5" class="form-control @error('estimated_hours') is-invalid @enderror"
                                           id="estimated_hours" name="estimated_hours" value="{{ old('estimated_hours') }}" placeholder="2.5">
                                    @error('estimated_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="mb-4">
                            <label for="instructions" class="form-label">Work Instructions</label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror"
                                      id="instructions" name="instructions" rows="5"
                                      placeholder="Provide detailed instructions for the technician...">{{ old('instructions') }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Request Preview -->
                        <div class="mb-4" id="requestPreview" style="display: none;">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Maintenance Request Details
                                    </h6>
                                </div>
                                <div class="card-body" id="previewContent">
                                    <!-- Dynamic content -->
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
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
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const requestSelect = document.getElementById('maintenance_request_id');
    const previewDiv = document.getElementById('requestPreview');
    const previewContent = document.getElementById('previewContent');
    const dueDateField = document.getElementById('due_date');

    // Set default due date to 3 days from now
    if (!dueDateField.value) {
        const threeDaysLater = new Date();
        threeDaysLater.setDate(threeDaysLater.getDate() + 3);
        threeDaysLater.setHours(9, 0, 0);
        dueDateField.value = threeDaysLater.toISOString().slice(0, 16);
    }

    // Show request preview when selected
    requestSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (this.value && this.value !== '') {
            const title = selectedOption.dataset.title || 'N/A';
            const description = selectedOption.dataset.description || 'No description';
            const priority = selectedOption.dataset.priority || 'medium';
            const equipment = selectedOption.dataset.equipment || 'N/A';
            const customer = selectedOption.dataset.customer || 'N/A';
            const location = selectedOption.dataset.location || 'N/A';

            let priorityClass = 'secondary';
            if (priority === 'critical') priorityClass = 'danger';
            else if (priority === 'high') priorityClass = 'danger';
            else if (priority === 'medium') priorityClass = 'warning';
            else if (priority === 'low') priorityClass = 'info';

            previewContent.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">Title</small>
                        <p class="mb-2"><strong>${escapeHtml(title)}</strong></p>
                        <small class="text-muted">Equipment</small>
                        <p class="mb-2">${escapeHtml(equipment)}</p>
                        <small class="text-muted">Priority</small>
                        <p class="mb-2"><span class="badge bg-${priorityClass}">${priority.toUpperCase()}</span></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Customer</small>
                        <p class="mb-2">${escapeHtml(customer)}</p>
                        <small class="text-muted">Location</small>
                        <p class="mb-2">${escapeHtml(location)}</p>
                    </div>
                    <div class="col-12 mt-2">
                        <small class="text-muted">Description</small>
                        <p class="mb-0">${escapeHtml(description)}</p>
                    </div>
                </div>
            `;
            previewDiv.style.display = 'block';
        } else {
            previewDiv.style.display = 'none';
        }
    });

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    if (requestSelect.value && requestSelect.value !== '') {
        requestSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
