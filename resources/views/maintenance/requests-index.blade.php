{{-- resources/views/maintenance/requests-index.blade.php --}}
@extends('layouts.app')

@section('title', 'Maintenance Requests')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tools me-2"></i>Maintenance Requests
                    </h5>
                    @can('create-maintenance-request')
                        <a href="{{ route('maintenance.requests.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> New Request
                        </a>
                    @endcan
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter" onchange="filterRequests()">
                                <option value="all">All Status</option>
                                <option value="open">Open</option>
                                <option value="assigned">Assigned</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="priorityFilter" onchange="filterRequests()">
                                <option value="all">All Priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="typeFilter" onchange="filterRequests()">
                                <option value="all">All Types</option>
                                <option value="preventive">Preventive</option>
                                <option value="corrective">Corrective</option>
                                <option value="emergency">Emergency</option>
                                <option value="calibration">Calibration</option>
                                <option value="inspection">Inspection</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchFilter" placeholder="Search..." onkeyup="filterRequests()">
                        </div>
                    </div>

                    @if($requests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Equipment</th>
                                        <th>Priority</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Requested By</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $request)
                                        <tr class="request-row"
                                            data-status="{{ $request->status }}"
                                            data-priority="{{ $request->priority }}"
                                            data-type="{{ $request->maintenance_type }}"
                                            data-search="{{ strtolower($request->title . ' ' . $request->description) }}">
                                            <td>
                                                <strong>#{{ $request->id }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $request->title }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($request->description, 50) }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($request->equipment)
                                                    {{ $request->equipment->name }}
                                                    <br>
                                                    <small class="text-muted">{{ $request->equipment->model }}</small>
                                                @else
                                                    <span class="text-muted">No equipment</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $priorityColors = [
                                                        'low' => 'secondary',
                                                        'medium' => 'warning',
                                                        'high' => 'danger',
                                                        'critical' => 'dark'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $priorityColors[$request->priority] ?? 'secondary' }}">
                                                    {{ ucfirst($request->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ ucfirst($request->maintenance_type) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'open' => 'warning',
                                                        'assigned' => 'primary',
                                                        'in_progress' => 'info',
                                                        'completed' => 'success',
                                                        'cancelled' => 'secondary'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$request->status] ?? 'secondary' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($request->requestedBy)
                                                    {{ $request->requestedBy->name }}
                                                    <br>
                                                    <small class="text-muted">{{ $request->requestedBy->role }}</small>
                                                @else
                                                    <span class="text-muted">System</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $request->created_at->format('M j, Y') }}
                                                <br>
                                                <small class="text-muted">{{ $request->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('maintenance.requests.show', $request->id) }}"
                                                       class="btn btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @can('edit-maintenance-request')
                                                        <a href="{{ route('maintenance.requests.edit', $request->id) }}"
                                                           class="btn btn-outline-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @if($request->status === 'open' && Auth::user()->role === 'admin')
                                                        <button type="button" class="btn btn-outline-success"
                                                                onclick="assignRequest({{ $request->id }})" title="Assign">
                                                            <i class="fas fa-user-check"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($requests->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $requests->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tools fa-4x text-muted mb-3"></i>
                            <h4>No Maintenance Requests Found</h4>
                            <p class="text-muted">There are no maintenance requests matching your criteria.</p>
                            @can('create-maintenance-request')
                                <a href="{{ route('maintenance.requests.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-1"></i>Create First Request
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Request Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Maintenance Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="technician_id" class="form-label">Assign to Technician</label>
                        <select class="form-select" id="technician_id" name="technician_id" required>
                            <option value="">Select Technician</option>
                            <!-- Technicians will be loaded here -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assignment_notes" class="form-label">Assignment Notes</label>
                        <textarea class="form-control" id="assignment_notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitAssignment()">Assign Request</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentRequestId = null;

function filterRequests() {
    const statusFilter = document.getElementById('statusFilter').value;
    const priorityFilter = document.getElementById('priorityFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const searchFilter = document.getElementById('searchFilter').value.toLowerCase();

    document.querySelectorAll('.request-row').forEach(row => {
        const status = row.getAttribute('data-status');
        const priority = row.getAttribute('data-priority');
        const type = row.getAttribute('data-type');
        const search = row.getAttribute('data-search');

        const statusMatch = statusFilter === 'all' || status === statusFilter;
        const priorityMatch = priorityFilter === 'all' || priority === priorityFilter;
        const typeMatch = typeFilter === 'all' || type === typeFilter;
        const searchMatch = search.includes(searchFilter);

        if (statusMatch && priorityMatch && typeMatch && searchMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function assignRequest(requestId) {
    currentRequestId = requestId;

    // Load technicians (you might want to load this via AJAX)
    // For now, we'll use a simple approach
    const modal = new bootstrap.Modal(document.getElementById('assignModal'));
    modal.show();
}

function submitAssignment() {
    if (!currentRequestId) return;

    const form = document.getElementById('assignForm');
    form.action = `/maintenance/requests/${currentRequestId}/assign`;
    form.submit();
}

// Initialize filters from URL parameters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);

    const status = urlParams.get('status');
    const priority = urlParams.get('priority');
    const type = urlParams.get('type');
    const search = urlParams.get('search');

    if (status) document.getElementById('statusFilter').value = status;
    if (priority) document.getElementById('priorityFilter').value = priority;
    if (type) document.getElementById('typeFilter').value = type;
    if (search) document.getElementById('searchFilter').value = search;

    filterRequests();
});
</script>
@endsection
