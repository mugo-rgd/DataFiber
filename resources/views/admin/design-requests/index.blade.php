@extends('layouts.app')

@section('title', 'Design Requests Management - Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-drafting-compass me-2"></i>Design Requests Management
        </h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <!-- Pending Requests Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-warning">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-clock me-1"></i> Pending Requests
                <span class="badge bg-light text-dark ms-2">{{ $pendingRequests->count() }}</span>
            </h6>
        </div>
        <div class="card-body">
            @if($pendingRequests->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Request #</th>
                            <th>Customer</th>
                            <th>Title</th>
                            <th>Priority</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>


                    <tbody>
                        @foreach($pendingRequests as $request)

                        <tr>
                            <td>
                                <strong>{{ $request->request_number }}</strong>
                                @if($request->relationLoaded('quotations') && $request->quotations->count() > 0)
                                    <span class="badge bg-success ms-1" title="Quotation exists">
                                        <i class="fas fa-file-invoice-dollar me-1"></i>{{ $request->quotations->count() }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $request->customer->name ?? 'N/A' }}</td>
                            <td>{{ Str::limit($request->title, 50) }}</td>
                            <td>
                                <span class="badge bg-{{ $request->priority === 'urgent' ? 'danger' : ($request->priority === 'high' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($request->priority) }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <!-- View Button -->
                                    <a href="{{ route('admin.design-requests.show', $request->request_number) }}"
                                       class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.design-requests.edit', $request->request_number) }}"
                                       class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if($request->quotations->isEmpty())
                                        <!-- Generate Quote Button - Only show if no quotations exist -->
                                        {{-- <a href="{{ route('account-manager.quotations.create', ['design_request_id' => $request->request_number]) }}"
                                           class="btn btn-success" title="Generate Quote">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </a> --}}

                                         <!-- Quotation Actions -->
    @php
        // Pass openInNewWindow parameter to quotation actions if they include quotation links
        $openInNewWindow = true;
    @endphp
    @include('partials.quotation-actions', [
        'request' => $request,
        'openInNewWindow' => $openInNewWindow
    ])

                                        <!-- Assign Designer Button - Only show if no quotations exist -->
                                        <button type="button" class="btn btn-primary assign-designer-btn"
                                                data-request-number="{{ $request->request_number }}" title="Assign Designer">
                                            <i class="fas fa-user-tie"></i>
                                        </button>

                                        <!-- Assign Surveyor Button - Only show if no quotations exist -->
                                        <button type="button" class="btn btn-secondary assign-surveyor-btn"
                                                data-request-number="{{ $request->request_number }}" title="Assign Surveyor">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </button>
                                    @else
<!-- View Quotations Button -->
<a href="{{ route('admin.quotations.show', $request->quotation->id) }}"
   class="btn btn-info" title="View Quotations">
    <i class="fas fa-list"></i>
</a>

<!-- Create New Lease Button (with both parameters) -->

					<a href="{{ route('account-manager.leases.create', [
    'customer_id' =>  $request->customer_id,
    'design_request_id' => $request->request_number,
    'design_request_title' => $request->title
]) }}" class="btn btn-primary">
    Create Lease
</a>
                                        <span class="btn btn-outline-success" title="Quotation already generated">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h5>No Pending Requests</h5>
                <p class="text-muted">All design requests have been processed.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Assigned Requests Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-info">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-tasks me-1"></i> Assigned Requests
                <span class="badge bg-light text-dark ms-2">{{ $assignedRequests->count() }}</span>
            </h6>
        </div>
        <div class="card-body">
            @if($assignedRequests->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Request #</th>
                            <th>Customer</th>
                            <th>Designer</th>
                            <th>Surveyor</th>
                            <th>Status</th>
                            <th>Assigned</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignedRequests as $request)
                        <tr>
                            <td>
                                <strong>{{ $request->request_number }}</strong>
                                @if($request->relationLoaded('quotations') && $request->quotations->count() > 0)
                                    <span class="badge bg-success ms-1" title="Quotation exists">
                                        <i class="fas fa-file-invoice-dollar me-1"></i>{{ $request->quotations->count() }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $request->customer->name ?? 'N/A' }}</td>
                            <td>
                                @if($request->designer)
                                    {{ $request->designer->name }}
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </td>
                            <td>
                                @if($request->surveyor)
                                    {{ $request->surveyor->name }}
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $request->status === 'completed' ? 'success' : ($request->status === 'in_progress' ? 'primary' : 'warning') }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td>{{ $request->assigned_at ? $request->assigned_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <!-- View Button -->
                                    <a href="{{ route('account-manager.design-requests.show', $request->request_number) }}"
                                       class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <!-- Edit Button -->
                                    <a href="{{ route('account-manager.design-requests.edit', $request->request_number) }}"
                                       class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if($request->relationLoaded('quotations') && $request->quotations->count() == 0)
                                        <!-- Generate Quote Button - Only show if no quotations exist -->
                                        {{-- <a href="{{ route('account-manager.quotations.create', ['design_request_id' => $request->request_number]) }}"
                                           class="btn btn-success" title="Generate Quote">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </a> --}}
                                        @php
        // Pass openInNewWindow parameter to quotation actions if they include quotation links
        $openInNewWindow = true;
    @endphp
    @include('partials.quotation-actions', [
        'request' => $request,
        'openInNewWindow' => $openInNewWindow
    ])
                                    @else
                                        <!-- View Quotations Button - Show when quotations exist -->
                                        <a href="{{ route('account-manager.quotations.index', ['design_request_id' => $request->request_number]) }}"
                                           class="btn btn-info" title="View Quotations">
                                            <i class="fas fa-list"></i>
                                        </a>
                                        <span class="btn btn-outline-success" title="Quotation already generated">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    @endif

                                    <!-- Update Status Button -->
                                    <button type="button" class="btn btn-secondary update-status-btn"
                                            data-request-number="{{ $request->request_number }}" title="Update Status">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4">
                <i class="fas fa-clipboard-list fa-3x text-info mb-3"></i>
                <h5>No Assigned Requests</h5>
                <p class="text-muted">No design requests have been assigned yet.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Assign Designer Modal -->
<div class="modal fade" id="assignDesignerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Designer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignDesignerForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="design_request_id" id="design_request_id">
                    <div class="mb-3">
                        <label for="designer_id" class="form-label">Select Designer</label>
                        <select class="form-select" id="designer_id" name="designer_id" required>
                            <option value="">Choose a designer...</option>
                            @foreach($designers as $designer)
                            <option value="{{ $designer->id }}">{{ $designer->name }} ({{ $designer->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Designer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Surveyor Modal -->
<div class="modal fade" id="assignSurveyorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Surveyor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignSurveyorForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="design_request_id" id="surveyor_design_request_id">
                    <div class="mb-3">
                        <label for="surveyor_id" class="form-label">Select Surveyor</label>
                        <select class="form-select" id="surveyor_id" name="surveyor_id" required>
                            <option value="">Choose a surveyor...</option>
                            @foreach($surveyors as $surveyor)
                            <option value="{{ $surveyor->id }}">{{ $surveyor->name }} ({{ $surveyor->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Surveyor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Request Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateStatusForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="design_request_id" id="status_design_request_id">
                    <div class="mb-3">
                        <label for="status" class="form-label">Select Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="assigned">Assigned</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    position: relative;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Assign Designer Modal Handler
    const assignDesignerBtns = document.querySelectorAll('.assign-designer-btn');
    assignDesignerBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const requestNumber = this.getAttribute('data-request-number');
            document.getElementById('design_request_id').value = requestNumber;
            document.getElementById('assignDesignerForm').action = `/account-manager/design-requests/${requestNumber}/assign-designer`;
            new bootstrap.Modal(document.getElementById('assignDesignerModal')).show();
        });
    });

    // Assign Surveyor Modal Handler
    const assignSurveyorBtns = document.querySelectorAll('.assign-surveyor-btn');
    assignSurveyorBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const requestNumber = this.getAttribute('data-request-number');
            document.getElementById('surveyor_design_request_id').value = requestNumber;
            document.getElementById('assignSurveyorForm').action = `/account-manager/design-requests/${requestNumber}/assign-surveyor`;
            new bootstrap.Modal(document.getElementById('assignSurveyorModal')).show();
        });
    });

    // Update Status Modal Handler
    const updateStatusBtns = document.querySelectorAll('.update-status-btn');
    updateStatusBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const requestNumber = this.getAttribute('data-request-number');
            document.getElementById('status_design_request_id').value = requestNumber;
            document.getElementById('updateStatusForm').action = `/admin/design-requests/${requestNumber}/status`;
            new bootstrap.Modal(document.getElementById('updateStatusModal')).show();
        });
    });
});
</script>
@endsection
