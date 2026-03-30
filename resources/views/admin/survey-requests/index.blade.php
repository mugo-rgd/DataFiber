{{-- resources/views/admin/survey-requests/index.blade.php --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@php
    $surveyors = $surveyors ?? collect();
@endphp

@extends('layouts.app')

@section('title', 'Manage Survey Requests')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-map-marked-alt text-primary"></i> Manage Survey Requests
                </h1>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
            <p class="text-muted">Assign surveyors to design requests and manage field surveys</p>
        </div>
    </div>

    @if(isset($error))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ $error }}
        </div>
    @endif

    @if($designRequests->isEmpty())
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Survey Requests Found</h4>
                <p class="text-muted">
                    @if(request('filter') || request('search'))
                        Try adjusting your filters or search terms.
                    @else
                        There are currently no design requests that require survey assignment.
                    @endif
                </p>
                @if(request('filter') || request('search'))
                    <a href="{{ route('admin.survey-requests') }}" class="btn btn-primary">
                        Clear Filters
                    </a>
                @endif
            </div>
        </div>
    @else

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.survey-requests') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="filter" class="form-label">Filter by Status</label>
                    <select class="form-select" id="filter" name="filter">
                        <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All Survey Requests</option>
                        <option value="needs_survey" {{ request('filter') == 'needs_survey' ? 'selected' : '' }}>Needs Survey Assignment</option>
                        <option value="survey_requested" {{ request('filter') == 'survey_requested' ? 'selected' : '' }}>Survey Requested</option>
                        <option value="survey_assigned" {{ request('filter') == 'survey_assigned' ? 'selected' : '' }}>Survey Assigned</option>
                        <option value="survey_in_progress" {{ request('filter') == 'survey_in_progress' ? 'selected' : '' }}>Survey In Progress</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Search by request number, title, or customer...">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Need Assignment</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\DesignRequest::where('survey_status', 'not_required')->whereNull('surveyor_id')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Assigned</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\DesignRequest::where('survey_status', 'assigned')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                In Progress</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\DesignRequest::where('survey_status', 'in_progress')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Available Surveyors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\User::where('role', 'surveyor')->where('status', 'active')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Survey Requests Table -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Request #</th>
                            <th>Customer</th>
                            <th>Title</th>
                            <th>Priority</th>
                            <th>Survey Status</th>
                            <th>Assigned Surveyor</th>
                            <th>Requested</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($designRequests as $designRequest)
                        <tr>
                            <td>
                                <strong>{{ $designRequest->request_number }}</strong>
                            </td>
                            <td>{{ $designRequest->customer->name }}</td>
                            <td>{{ Str::limit($designRequest->title, 40) }}</td>
                            <td>
                                <span class="badge bg-{{ match($designRequest->priority) {
                                    'low' => 'secondary',
                                    'medium' => 'info',
                                    'high' => 'warning',
                                    'urgent' => 'danger',
                                    default => 'light'
                                } }}">
                                    {{ ucfirst($designRequest->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ match($designRequest->survey_status) {
                                    'not_required' => 'secondary',
                                    'requested' => 'warning',
                                    'assigned' => 'primary',
                                    'in_progress' => 'info',
                                    'completed' => 'success',
                                    'failed' => 'danger',
                                    default => 'light'
                                } }}">
                                    {{ $designRequest->survey_status ? ucfirst(str_replace('_', ' ', $designRequest->survey_status)) : 'Not Set' }}
                                </span>
                            </td>
                            <td>
                                @if($designRequest->surveyor_id)
                                    @php
                                        $surveyor = \App\Models\User::find($designRequest->surveyor_id);
                                    @endphp
                                    @if($surveyor)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-primary text-white rounded-circle me-2"
                                                 style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                                {{ substr($surveyor->name, 0, 1) }}
                                            </div>
                                            {{ $surveyor->name }}
                                        </div>
                                    @else
                                        <span class="text-muted">Unknown Surveyor (ID: {{ $designRequest->surveyor_id }})</span>
                                    @endif
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </td>
                            <td>{{ $designRequest->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <!-- View Details -->
                                    <a href="{{ route('admin.design-requests.show', $designRequest) }}"
                                       class="btn btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Assign/Reassign Surveyor Button -->
                                    @if(!$designRequest->surveyor_id || $designRequest->survey_status == 'not_required')
                                        <button type="button" class="btn btn-outline-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#surveyorModal{{ $designRequest->id }}"
                                                title="Assign Surveyor">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                    @else
                                        <!-- Reassign Surveyor -->
                                        <button type="button" class="btn btn-outline-warning"
                                                data-bs-toggle="modal"
                                                data-bs-target="#surveyorModal{{ $designRequest->id }}"
                                                title="Reassign Surveyor">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    @endif

                                    <!-- View Survey Progress -->
                                    @if($designRequest->surveyor_id && $designRequest->survey_status != 'not_required')
                                        <a href="{{ route('admin.design-requests.show', $designRequest) }}#survey"
                                           class="btn btn-outline-info" title="View Survey Progress">
                                            <i class="fas fa-chart-line"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($designRequests->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $designRequests->firstItem() }} to {{ $designRequests->lastItem() }}
                    of {{ $designRequests->total() }} results
                </div>
                {{ $designRequests->links() }}
            </div>
            @endif
        </div>
    </div>
    @endif {{-- End of @else for empty check --}}
</div>

<!-- Include Modals for each design request -->
@foreach($designRequests as $designRequest)
    <!-- Assign/Reassign Surveyor Modal -->
    <div class="modal fade" id="surveyorModal{{ $designRequest->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.design-requests.assign-surveyor', $designRequest) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $designRequest->surveyor_id ? 'Reassign Surveyor' : 'Assign Surveyor' }}
                            – Request #{{ $designRequest->request_number }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select Surveyor</label>
                            <select name="surveyor_id" class="form-select @error('surveyor_id') is-invalid @enderror" required>
                                <option value="">-- Choose Surveyor --</option>
                                @foreach($surveyors as $surveyor)
                                    <option value="{{ $surveyor->id }}"
                                        {{ $designRequest->surveyor_id == $surveyor->id ? 'selected' : '' }}
                                        {{ old('surveyor_id') == $surveyor->id ? 'selected' : '' }}>
                                        {{ $surveyor->user->name }} ({{ $surveyor->user->email }})
                                        @if($surveyor->specialization)
                                            - {{ $surveyor->specialization }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('surveyor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Survey Requirements</label>
                            <textarea name="survey_requirements"
                                    class="form-control @error('survey_requirements') is-invalid @enderror"
                                    rows="3"
                                    placeholder="Enter specific survey requirements..."
                                    required>{{ old('survey_requirements', $designRequest->survey_requirements) }}</textarea>
                            @error('survey_requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Scheduled At</label>
                                <input type="datetime-local"
                                    name="survey_scheduled_at"
                                    class="form-control @error('survey_scheduled_at') is-invalid @enderror"
                                    value="{{ old('survey_scheduled_at', optional($designRequest->survey_scheduled_at)->format('Y-m-d\TH:i')) }}"
                                    required>
                                @error('survey_scheduled_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estimated Hours</label>
                                <input type="number"
                                    name="survey_estimated_hours"
                                    class="form-control @error('survey_estimated_hours') is-invalid @enderror"
                                    step="0.5" min="0.5" max="24"
                                    value="{{ old('survey_estimated_hours', $designRequest->survey_estimated_hours ?? 2) }}"
                                    placeholder="Estimated hours"
                                    required>
                                @error('survey_estimated_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-check me-1"></i>
                            {{ $designRequest->surveyor_id ? 'Reassign' : 'Assign' }} Surveyor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection

@push('scripts')
<script>
// Auto-submit filter form when select changes
document.getElementById('filter').addEventListener('change', function() {
    this.form.submit();
});

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
</script>
@endpush
