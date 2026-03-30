@extends('layouts.app')

@section('title', 'Design Request Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-drafting-compass text-primary"></i> Design Request Details
                </h1>
                <a href="{{ route('ictengineer.requests') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Requests
                </a>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('ictengineer.requests') }}">Design Requests</a></li>
                    <li class="breadcrumb-item active">{{ $request->request_number }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Design Request Header -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Design Request: {{ $request->request_number }}</h5>
                        <div>
                            <span class="badge bg-{{ match($request->status) {
                                'pending' => 'secondary',
                                'in_progress' => 'warning',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'secondary'
                            } }}">
                                {{ ucfirst($request->status) }}
                            </span>
                            <span class="badge bg-{{ match($request->ict_status) {
                                'pending_assignment' => 'secondary',
                                'assigned' => 'info',
                                'inspection_scheduled' => 'warning',
                                'inspection_completed' => 'primary',
                                'certificate_generated' => 'success',
                                'certificate_sent' => 'success',
                                'completed' => 'success',
                                default => 'secondary'
                            } }}">
                                {{ ucfirst(str_replace('_', ' ', $request->ict_status)) }}
                            </span>
                            @if($request->priority)
                            <span class="badge bg-{{ match($request->priority) {
                                'low' => 'success',
                                'medium' => 'warning',
                                'high' => 'danger',
                                default => 'secondary'
                            } }}">
                                {{ ucfirst($request->priority) }} Priority
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Request #:</strong> {{ $request->request_number }}</p>
                            <p><strong>Customer:</strong> {{ $request->customer->name ?? 'Not Assigned' }}</p>
                            <p><strong>Title:</strong> {{ $request->title }}</p>
                            <p><strong>Route Name:</strong> {{ $request->route_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Created:</strong> {{ $request->created_at->format('M d, Y') }}</p>
                            <p><strong>Updated:</strong> {{ $request->updated_at->format('M d, Y') }}</p>
                            <p><strong>Requested At:</strong> {{ $request->requested_at->format('M d, Y') }}</p>
                            @if($request->assigned_to_ict_at)
                                <p><strong>Assigned to ICT:</strong> {{ $request->assigned_to_ict_at->format('M d, Y') }}</p>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <p><strong>ICT Engineer:</strong> {{ $request->ictEngineer->name ?? 'Not Assigned' }}</p>
                            <p><strong>Designer:</strong> {{ $request->designer->name ?? 'Not Assigned' }}</p>
                            <p><strong>Surveyor:</strong> {{ $request->surveyor->name ?? 'Not Assigned' }}</p>
                            <p><strong>Estimated Cost:</strong>
                                @if($request->estimated_cost)
                                    ${{ number_format($request->estimated_cost, 2) }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Design Items & Technical Details -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Technical Specifications</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p><strong>Cores Required:</strong> {{ $request->cores_required ?? 'N/A' }}</p>
                            <p><strong>Technology Type:</strong> {{ $request->technology_type ?? 'N/A' }}</p>
                            <p><strong>Link Class:</strong> {{ $request->link_class ?? 'N/A' }}</p>
                            <p><strong>Unit Cost:</strong>
                                @if($request->unit_cost)
                                    ${{ number_format($request->unit_cost, 2) }}/km
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-6">
                            <p><strong>Distance:</strong> {{ $request->distance ?? '0' }} km</p>
                            <p><strong>Total Distance:</strong> {{ $request->total_distance ?? '0' }} km</p>
                            <p><strong>Terms:</strong> {{ $request->terms ?? 'N/A' }} months</p>
                            <p><strong>Tax Rate:</strong> {{ $request->tax_rate ?? '0' }}%</p>
                        </div>
                    </div>

                    @if($request->technical_requirements)
                    <div class="mt-3">
                        <strong>Technical Requirements:</strong>
                        <div class="mt-1 p-3 bg-light rounded">
                            {!! nl2br(e($request->technical_requirements)) !!}
                        </div>
                    </div>
                    @endif

                    @if($request->design_specifications)
                    <div class="mt-3">
                        <strong>Design Specifications:</strong>
                        <div class="mt-1 p-3 bg-light rounded">
                            {!! nl2br(e($request->design_specifications)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Description -->
            <div class="card shadow mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Request Description</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Title:</strong>
                        <p class="mt-1">{{ $request->title }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Description:</strong>
                        <div class="mt-1 p-3 bg-light rounded">
                            {!! nl2br(e($request->description)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- ICT Status & Actions -->
            <div class="card shadow">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">ICT Status & Actions</h5>
                </div>
                <div class="card-body">
                    <!-- Current ICT Status -->
                    <div class="mb-4">
                        <h6>Current ICT Status:
                            <span class="badge bg-{{ match($request->ict_status) {
                                'pending_assignment' => 'secondary',
                                'assigned' => 'info',
                                'inspection_scheduled' => 'warning',
                                'inspection_completed' => 'primary',
                                'certificate_generated' => 'success',
                                'certificate_sent' => 'success',
                                'completed' => 'success',
                                default => 'secondary'
                            } }}">
                                {{ ucfirst(str_replace('_', ' ', $request->ict_status)) }}
                            </span>
                        </h6>

                        @if($request->ict_status == 'assigned')
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> This request has been assigned to you. Please schedule an inspection.
                            </div>
                        @elseif($request->ict_status == 'inspection_scheduled')
                            <div class="alert alert-warning">
                                <i class="fas fa-calendar-check"></i> Inspection scheduled for {{ $request->inspection_date ? $request->inspection_date->format('M d, Y') : 'TBD' }}.
                            </div>
                        @elseif($request->ict_status == 'inspection_completed')
                            <div class="alert alert-primary">
                                <i class="fas fa-clipboard-check"></i> Inspection completed. Ready to generate certificate.
                            </div>
                        @elseif($request->ict_status == 'certificate_generated')
                            <div class="alert alert-success">
                                <i class="fas fa-certificate"></i> Certificate has been generated.
                            </div>
                        @endif
                    </div>

                    <!-- ICT Status Update Form -->
                    @if($request->ict_engineer_id == auth()->id())
                    <form action="{{ route('ictengineer.requests.update', $request->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="ict_status" class="form-label">Update ICT Status</label>
                            <select name="ict_status" id="ict_status" class="form-select" required>
                                <option value="pending_assignment" {{ $request->ict_status == 'pending_assignment' ? 'selected' : '' }} disabled>Pending Assignment</option>
                                <option value="assigned" {{ $request->ict_status == 'assigned' ? 'selected' : '' }} disabled>Assigned</option>
                                <option value="inspection_scheduled" {{ $request->ict_status == 'inspection_scheduled' ? 'selected' : '' }}>Inspection Scheduled</option>
                                <option value="inspection_completed" {{ $request->ict_status == 'inspection_completed' ? 'selected' : '' }}>Inspection Completed</option>
                                <option value="certificate_generated" {{ $request->ict_status == 'certificate_generated' ? 'selected' : '' }}>Certificate Generated</option>
                                <option value="certificate_sent" {{ $request->ict_status == 'certificate_sent' ? 'selected' : '' }}>Certificate Sent</option>
                                <option value="completed" {{ $request->ict_status == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>

                        <!-- Inspection Date (only for inspection_scheduled) -->
                        <div class="mb-3" id="inspection_date_group" style="display: none;">
                            <label for="inspection_date" class="form-label">Inspection Date</label>
                            <input type="date" name="inspection_date" id="inspection_date" class="form-control"
                                   value="{{ old('inspection_date', $request->inspection_date ? $request->inspection_date->format('Y-m-d') : '') }}">
                        </div>

                        <!-- Inspection Notes -->
                        <div class="mb-3" id="inspection_notes_group" style="display: none;">
                            <label for="inspection_notes" class="form-label">Inspection Notes</label>
                            <textarea name="inspection_notes" id="inspection_notes" class="form-control" rows="3">{{ old('inspection_notes', $request->inspection_notes) }}</textarea>
                        </div>

                        <!-- Engineer Notes (always visible) -->
                        <div class="mb-3">
                            <label for="engineer_notes" class="form-label">Engineer Notes</label>
                            <textarea name="engineer_notes" id="engineer_notes" class="form-control" rows="3"
                                      placeholder="Add any technical notes or comments...">{{ old('engineer_notes', $request->engineer_notes) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </form>
                    @else
                        <div class="alert alert-secondary">
                            <i class="fas fa-user"></i> This request is assigned to another ICT Engineer.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Survey Information -->
            <div class="card shadow mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Survey Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p><strong>Survey Status:</strong>
                                <span class="badge bg-{{ match($request->survey_status) {
                                    'not_required' => 'secondary',
                                    'requested' => 'info',
                                    'assigned' => 'warning',
                                    'in_progress' => 'primary',
                                    'completed' => 'success',
                                    'failed' => 'danger',
                                    'cancelled' => 'dark',
                                    default => 'secondary'
                                } }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->survey_status)) }}
                                </span>
                            </p>
                            <p><strong>Point Count:</strong> {{ $request->point_count }}</p>
                        </div>
                        <div class="col-6">
                            @if($request->survey_requested_at)
                                <p><strong>Survey Requested:</strong> {{ $request->survey_requested_at->format('M d, Y') }}</p>
                            @endif
                            @if($request->survey_completed_at)
                                <p><strong>Survey Completed:</strong> {{ $request->survey_completed_at->format('M d, Y') }}</p>
                            @endif
                        </div>
                    </div>

                    @if($request->survey_requirements)
                    <div class="mt-3">
                        <strong>Survey Requirements:</strong>
                        <div class="mt-1 p-3 bg-light rounded">
                            {!! nl2br(e($request->survey_requirements)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Technical Status & Certificates -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Technical Review</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p><strong>Technical Status:</strong>
                                <span class="badge bg-{{ match($request->technical_status) {
                                    'under_technical_review' => 'info',
                                    'technically_approved' => 'success',
                                    'technical_revisions_required' => 'warning',
                                    'ready_for_acceptance' => 'primary',
                                    'accepted' => 'success',
                                    'rejected' => 'danger',
                                    default => 'secondary'
                                } }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->technical_status)) }}
                                </span>
                            </p>
                            @if($request->technical_reviewed_at)
                                <p><strong>Reviewed At:</strong> {{ $request->technical_reviewed_at->format('M d, Y') }}</p>
                            @endif
                        </div>
                        <div class="col-6">
                            @if($request->quotation_id)
                                <p><strong>Quotation ID:</strong> {{ $request->quotation_id }}</p>
                            @endif
                            @if($request->certificate_id)
                                <p><strong>Certificate ID:</strong> {{ $request->certificate_id }}</p>
                            @endif
                        </div>
                    </div>

                    @if($request->technical_notes)
                    <div class="mt-3">
                        <strong>Technical Notes:</strong>
                        <div class="mt-1 p-3 bg-light rounded">
                            {!! nl2br(e($request->technical_notes)) !!}
                        </div>
                    </div>
                    @endif

                    @if($request->design_notes)
                    <div class="mt-3">
                        <strong>Design Notes:</strong>
                        <div class="mt-1 p-3 bg-light rounded">
                            {!! nl2br(e($request->design_notes)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item {{ $request->requested_at ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>Request Created</h6>
                                <p>{{ $request->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        @if($request->assigned_at)
                        <div class="timeline-item {{ $request->assigned_at ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>Assigned to Designer</h6>
                                <p>{{ $request->assigned_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($request->assigned_to_ict_at)
                        <div class="timeline-item {{ $request->assigned_to_ict_at ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>Assigned to ICT</h6>
                                <p>{{ $request->assigned_to_ict_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($request->inspection_date)
                        <div class="timeline-item {{ $request->inspection_date ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>Inspection Scheduled</h6>
                                <p>{{ $request->inspection_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($request->certificate_generated_at)
                        <div class="timeline-item {{ $request->certificate_generated_at ? 'active' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>Certificate Generated</h6>
                                <p>{{ $request->certificate_generated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attachments & Documents -->
    @if($request->attachments)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Attachments</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $attachments = json_decode($request->attachments, true) ?? [];
                        @endphp
                        @foreach($attachments as $attachment)
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                    <h6>{{ $attachment['name'] ?? 'Document' }}</h6>
                                    <a href="{{ $attachment['path'] ?? '#' }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #dee2e6;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: #dee2e6;
        border: 3px solid #fff;
    }

    .timeline-item.active .timeline-marker {
        background-color: #0d6efd;
    }

    .timeline-content {
        padding: 5px 0;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ictStatusSelect = document.getElementById('ict_status');
        const inspectionDateGroup = document.getElementById('inspection_date_group');
        const inspectionNotesGroup = document.getElementById('inspection_notes_group');

        function toggleInspectionFields() {
            const selectedStatus = ictStatusSelect.value;

            if (selectedStatus === 'inspection_scheduled') {
                inspectionDateGroup.style.display = 'block';
                inspectionNotesGroup.style.display = 'none';
            } else if (selectedStatus === 'inspection_completed') {
                inspectionDateGroup.style.display = 'none';
                inspectionNotesGroup.style.display = 'block';
            } else {
                inspectionDateGroup.style.display = 'none';
                inspectionNotesGroup.style.display = 'none';
            }
        }

        // Initial toggle
        toggleInspectionFields();

        // Toggle on change
        if (ictStatusSelect) {
            ictStatusSelect.addEventListener('change', toggleInspectionFields);
        }

        // Real-time status badge update
        const statusSelect = document.getElementById('ict_status');

        function updateStatusBadge() {
            const statusBadge = document.querySelector('.card-header .badge.bg-info');
            if(statusBadge && statusSelect) {
                const statusText = statusSelect.options[statusSelect.selectedIndex].text;
                const statusColor = getStatusColor(statusSelect.value);
                statusBadge.textContent = statusText;
                statusBadge.className = `badge bg-${statusColor}`;
            }
        }

        function getStatusColor(status) {
            switch(status) {
                case 'pending_assignment': return 'secondary';
                case 'assigned': return 'info';
                case 'inspection_scheduled': return 'warning';
                case 'inspection_completed': return 'primary';
                case 'certificate_generated': return 'success';
                case 'certificate_sent': return 'success';
                case 'completed': return 'success';
                default: return 'secondary';
            }
        }

        if(statusSelect) {
            statusSelect.addEventListener('change', updateStatusBadge);
        }

        // Form submission confirmation for certain statuses
        const form = document.querySelector('form');
        if(form) {
            form.addEventListener('submit', function(e) {
                const statusSelect = document.getElementById('ict_status');
                if(statusSelect && statusSelect.value === 'completed') {
                    if(!confirm('Are you sure you want to mark this request as completed? This action cannot be undone.')) {
                        e.preventDefault();
                    }
                }
            });
        }
    });
</script>
@endsection
