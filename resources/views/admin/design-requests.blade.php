@extends('layouts.app')

@section('title', 'Manage Design Requests')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-drafting-compass text-primary"></i> Manage Design Requests
            </h1>
            <p class="text-muted">Assign design requests to available designers</p>
        </div>
    </div>

     @if(auth()->user()->role === 'account_manager')
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x me-3"></i>
                    <div>
                        <h6 class="alert-heading mb-1">Managed Customers View</h6>
                        <p class="mb-0">
                            You are viewing design requests for your managed customers only.
                            @php
                                $managedCustomers = App\Models\User::where('account_manager_id', auth()->id())
                                                                 ->where('role', 'customer')
                                                                 ->count();
                                $managedPendingRequests = App\Models\DesignRequest::whereHas('customer', function($query) {
                                        $query->where('account_manager_id', auth()->id());
                                    })
                                    ->where('status', 'pending')
                                    ->count();
                                $managedAssignedRequests = App\Models\DesignRequest::whereHas('customer', function($query) {
                                        $query->where('account_manager_id', auth()->id());
                                    })
                                    ->whereIn('status', ['assigned', 'in_design'])
                                    ->count();
                            @endphp
                            <strong>Managed Customers: {{ $managedCustomers }}</strong> •
                            <strong>Pending Requests: {{ $managedPendingRequests }}</strong> •
                            <strong>Assigned Requests: {{ $managedAssignedRequests }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Pending Requests Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Pending Design Requests
                        <span class="badge bg-dark ms-2">{{ $pendingRequests->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($pendingRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Request #</th>
                                        <th>Customer</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Requested</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingRequests as $request)
                                        <tr>
                                            <td><strong>#{{ $request->request_number }}</strong></td>
                                            <td>
    <div>
        <strong>{{ $request->customer->name }}</strong>
        @if(auth()->user()->role === 'account_manager')
            <br>
            <small class="text-muted">
                <i class="fas fa-envelope me-1"></i>{{ $request->customer->email }}
                @if($request->customer->phone)
                    <br><i class="fas fa-phone me-1"></i>{{ $request->customer->phone }}
                @endif
                @if($request->customer->company)
                    <br><i class="fas fa-building me-1"></i>{{ $request->customer->company }}
                @endif
            </small>
        @endif
    </div>
</td>
                                            <td>{{ Str::limit($request->title, 30) }}</td>
                                            <td>{{ Str::limit($request->description, 50) }}</td>
                                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-primary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#assignModal{{ $request->id }}">
                                                        <i class="fas fa-user-plus me-1"></i>Assign
                                                    </button>

                                                    <!-- View Details Button -->
                                                    <button class="btn btn-outline-info btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#detailsModal{{ $request->id }}">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </button>

                                                    <!-- Quote Button -->
                                                    <!-- Quote Button -->
<button class="btn btn-outline-info btn-sm">
<a href="{{ route('admin.quotations.create', ['design_request_id' => $request->id]) }}"
   class="btn btn-success btn-sm">
    <i class="fas fa-file-invoice-dollar me-1"></i>Quote

</a>
</button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Assign Designer Modal -->
                                        <div class="modal fade" id="assignModal{{ $request->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Assign Designer</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('admin.design-requests.assign-designer', $request) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p><strong>Request:</strong> #{{ $request->request_number }} - {{ $request->title }}</p>
                                                            <p><strong>Customer:</strong> {{ $request->customer->name }}</p>

                                                            <div class="mb-3">
                                                                <label for="designer_id" class="form-label">Select Designer</label>
                                                                <select class="form-select" id="designer_id" name="designer_id" required>
                                                                    <option value="">Choose a designer...</option>
                                                                    @foreach($designers as $designer)
                                                                        <option value="{{ $designer->id }}">
                                                                            {{ $designer->name }} ({{ $designer->email }})
                                                                        </option>
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

                                        <!-- Details Modal -->
                                        <div class="modal fade" id="detailsModal{{ $request->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Request Details - #{{ $request->request_number }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <p><strong>Title:</strong> {{ $request->title }}</p>
                                                                <p><strong>Customer:</strong> {{ $request->customer->name }}</p>
                                                                <p><strong>Email:</strong> {{ $request->customer->email }}</p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p><strong>Requested:</strong> {{ $request->created_at->format('M d, Y H:i') }}</p>
                                                                <p><strong>Status:</strong>
                                                                    <span class="badge bg-warning">Pending Assignment</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3">
                                                            <h6>Description:</h6>
                                                            <p class="text-muted">{{ $request->description }}</p>
                                                        </div>
                                                        @if($request->technical_requirements)
                                                        <div class="mt-3">
                                                            <h6>Technical Requirements:</h6>
                                                            <p class="text-muted">{{ $request->technical_requirements }}</p>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-muted">No pending design requests. All caught up!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Requests Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-check me-2"></i>Assigned Design Requests
                        <span class="badge bg-light text-dark ms-2">{{ $assignedRequests->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($assignedRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Request #</th>
                                        <th>Customer</th>
                                        <th>Designer</th>
                                        <th>Title</th>
                                        <th>Assigned</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignedRequests as $request)
                                        <tr>
                                            <td><strong>#{{ $request->request_number }}</strong></td>
                                            <td>
    <div>
        <strong>{{ $request->customer->name }}</strong>
        @if(auth()->user()->role === 'account_manager')
            <br>
            <small class="text-muted">
                <i class="fas fa-envelope me-1"></i>{{ $request->customer->email }}
                @if($request->customer->phone)
                    <br><i class="fas fa-phone me-1"></i>{{ $request->customer->phone }}
                @endif
            </small>
        @endif
    </div>
</td>
                                            <td>
                                                @if($request->designer)
                                                    <span class="badge bg-info">{{ $request->designer->name }}</span>
                                                @else
                                                    <span class="badge bg-secondary">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($request->title, 25) }}</td>
                                            <td>{{ $request->assigned_at?->format('M d, Y') ?? 'Not assigned' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $request->status === 'assigned' ? 'primary' : 'warning' }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if($request->designer)
                                                        <form action="{{ route('admin.design-requests.unassign-designer', $request) }}"
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('POST')
                                                            <button type="submit" class="btn btn-warning btn-sm"
                                                                    onclick="return confirm('Are you sure you want to unassign this request?')">
                                                                <i class="fas fa-user-times me-1"></i>Unassign
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <button class="btn btn-outline-primary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#detailsModal{{ $request->id }}">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </button>

                                                    <!-- Quote Button -->
                                                    <a href="{{ route('admin.quotations.create', ['design_request_id' => $request->id]) }}"
                                                       class="btn btn-success btn-sm">
                                                        <i class="fas fa-file-invoice-dollar me-1"></i>Quote
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No design requests have been assigned yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- resources/views/admin/design-requests/show.blade.php --}}

<!-- Admin Survey Assignment Section -->
@can('admin')
<div class="card shadow mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">
            <i class="fas fa-user-check text-primary"></i> Survey Management
            @if($designRequest->surveyor)
                <span class="badge bg-success ms-2">Assigned</span>
            @else
                <span class="badge bg-warning ms-2">Pending Assignment</span>
            @endif
        </h5>
    </div>
    <div class="card-body">

        @if($designRequest->surveyor)
            <!-- Surveyor Already Assigned -->
            <div class="row">
                <div class="col-md-6">
                    <h6>Assigned Surveyor:</h6>
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar bg-primary text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            {{ substr($designRequest->surveyor->user->name, 0, 1) }}
                        </div>
                        <div>
                            <strong>{{ $designRequest->surveyor->user->name }}</strong>
                            <br>
                            <small class="text-muted">
                                {{ $designRequest->surveyor->employee_id }}
                                @if($designRequest->surveyor->specialization)
                                    | {{ $designRequest->surveyor->specialization }}
                                @endif
                            </small>
                        </div>
                    </div>

                    <p><strong>Status:</strong>
                        <span class="badge bg-{{ match($designRequest->survey_status) {
                            'assigned' => 'primary',
                            'in_progress' => 'warning',
                            'completed' => 'success',
                            'failed' => 'danger',
                            default => 'secondary'
                        } }}">
                            {{ ucfirst(str_replace('_', ' ', $designRequest->survey_status)) }}
                        </span>
                    </p>

                    <p><strong>Scheduled:</strong>
                        {{ $designRequest->survey_scheduled_at?->format('M d, Y H:i') ?? 'Not scheduled' }}
                    </p>
                </div>

                <div class="col-md-6">
                    <h6>Assignment Details:</h6>
                    <p><strong>Requirements:</strong><br>
                        <small class="text-muted">{{ $designRequest->survey_requirements }}</small>
                    </p>
                    <p><strong>Estimated Hours:</strong> {{ $designRequest->survey_estimated_hours }}h</p>
                    <p><strong>Assigned On:</strong> {{ $designRequest->survey_requested_at?->format('M d, Y H:i') }}</p>
                </div>
            </div>

            <!-- Admin Actions for Assigned Survey -->
            <div class="mt-4 border-top pt-3">
                <h6>Admin Actions:</h6>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#reassignSurveyorModal">
                        <i class="fas fa-sync-alt me-1"></i>Reassign
                    </button>
                    <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#updateSurveyModal">
                        <i class="fas fa-edit me-1"></i>Update Details
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelSurveyModal">
                        <i class="fas fa-times me-1"></i>Cancel Survey
                    </button>
                </div>
            </div>

        @else
            <!-- No Surveyor Assigned -->
            <div class="text-center py-4">
                <i class="fas fa-user-plus fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Surveyor Assigned</h5>
                <p class="text-muted">This design request requires a field survey. Assign a surveyor to proceed.</p>
                {{-- To this link --}}
<a href="{{ route('admin.design-requests.assign-surveyor-form', $request) }}"
   class="btn btn-info btn-sm">
    <i class="fas fa-map-marked-alt me-1"></i>Assign Surveyor2
</a>
            </div>
        @endif

    </div>
</div>
@endcan
</div>
@endsection

@section('scripts')
<script>
// Auto-close alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endsection
