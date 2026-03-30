@extends('layouts.app')

@section('title', 'Edit Work Order')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Work Order #WO-{{ $workOrder->id }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('maintenance.work-orders.update', $workOrder->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Work Order Information -->
                        <div class="mb-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">Work Order Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Request:</strong><br>
                                            {{ $workOrder->maintenanceRequest->title ?? 'N/A' }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Current Status:</strong><br>
                                            <span class="badge bg-{{
                                                $workOrder->status == 'completed' ? 'success' :
                                                ($workOrder->status == 'in_progress' ? 'primary' :
                                                ($workOrder->status == 'assigned' ? 'warning' : 'secondary'))
                                            }}">
                                                {{ ucfirst($workOrder->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <strong>Created:</strong><br>
                                            {{ $workOrder->created_at->format('M j, Y g:i A') }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Last Updated:</strong><br>
                                            {{ $workOrder->updated_at->format('M j, Y g:i A') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Technician Assignment -->
                        <div class="mb-4">
                            <label for="assigned_technician" class="form-label">Assigned Technician *</label>
                            <select class="form-select @error('assigned_technician') is-invalid @enderror"
                                    id="assigned_technician" name="assigned_technician" required>
                                <option value="">Select Technician</option>
                                @foreach($technicians as $technician)
                                    <option value="{{ $technician->id }}"
                                        {{ old('assigned_technician', $workOrder->assigned_technician) == $technician->id ? 'selected' : '' }}>
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

                        <!-- Status Update -->
                        <div class="mb-4">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status" name="status" required>
                                <option value="assigned" {{ old('status', $workOrder->status) == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="in_progress" {{ old('status', $workOrder->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status', $workOrder->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $workOrder->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Due Date</label>
                                    <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror"
                                           id="due_date" name="due_date"
                                           value="{{ old('due_date', $workOrder->due_date ? $workOrder->due_date->format('Y-m-d\TH:i') : '') }}">
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_hours" class="form-label">Estimated Hours</label>
                                    <input type="number" class="form-control @error('estimated_hours') is-invalid @enderror"
                                           id="estimated_hours" name="estimated_hours"
                                           value="{{ old('estimated_hours', $workOrder->estimated_hours) }}"
                                           min="0.5" step="0.5" placeholder="2.5">
                                    @error('estimated_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
                                </div>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="mb-4">
                            <label for="instructions" class="form-label">Work Instructions</label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror"
                                      id="instructions" name="instructions" rows="4"
                                      placeholder="Provide detailed instructions for the technician...">{{ old('instructions', $workOrder->instructions) }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror>
                        </div>

                        <!-- Technician Notes (if work started) -->
                        @if($workOrder->status == 'in_progress' || $workOrder->status == 'completed')
                        <div class="mb-4">
                            <label for="technician_notes" class="form-label">Technician Notes</label>
                            <textarea class="form-control @error('technician_notes') is-invalid @enderror"
                                      id="technician_notes" name="technician_notes" rows="3"
                                      placeholder="Technician's notes and observations...">{{ old('technician_notes', $workOrder->technician_notes) }}</textarea>
                            @error('technician_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror>
                        </div>
                        @endif

                        <!-- Completion Details (if completed) -->
                        @if($workOrder->status == 'completed')
                        <div class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="actual_hours" class="form-label">Actual Hours Spent</label>
                                    <input type="number" class="form-control @error('actual_hours') is-invalid @enderror"
                                           id="actual_hours" name="actual_hours"
                                           value="{{ old('actual_hours', $workOrder->actual_hours) }}"
                                           min="0.1" step="0.1" placeholder="3.0">
                                    @error('actual_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
                                </div>
                                <div class="col-md-6">
                                    <label for="completion_notes" class="form-label">Completion Notes</label>
                                    <textarea class="form-control @error('completion_notes') is-invalid @enderror"
                                              id="completion_notes" name="completion_notes" rows="2"
                                              placeholder="Final completion notes...">{{ old('completion_notes', $workOrder->completion_notes) }}</textarea>
                                    @error('completion_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('maintenance.work-orders.show', $workOrder->id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                <a href="{{ route('maintenance.work-orders.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Back to List
                                </a>
                            </div>
                            <div>
                                @if($workOrder->status != 'completed' && $workOrder->status != 'cancelled')
                                    <button type="submit" name="action" value="save" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Update Work Order
                                    </button>
                                @else
                                    <button type="submit" name="action" value="save" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Update Details
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>

                    <!-- Danger Zone -->
                    @if($workOrder->status != 'completed')
                    <div class="mt-5">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="card-title mb-0">Danger Zone</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">Once you delete a work order, there is no going back. Please be certain.</p>
                                <form action="{{ route('maintenance.work-orders.destroy', $workOrder->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this work order? This action cannot be undone.')">
                                        <i class="fas fa-trash me-1"></i>Delete Work Order
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status');
        const completionFields = document.getElementById('completionFields');

        // Show/hide completion fields based on status
        function toggleCompletionFields() {
            const completionSection = document.getElementById('completionSection');
            if (completionSection) {
                if (statusSelect.value === 'completed') {
                    completionSection.style.display = 'block';
                } else {
                    completionSection.style.display = 'none';
                }
            }
        }

        statusSelect.addEventListener('change', toggleCompletionFields);
        toggleCompletionFields(); // Initial check
    });
</script>
@endsection
@endsection
