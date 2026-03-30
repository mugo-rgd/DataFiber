@extends('layouts.app')

@section('title', 'Design Request #' . $designRequest->request_number)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-file-alt text-primary"></i> Design Request Details
                </h1>
                <a href="{{ route('admin.design-requests.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
            <p class="text-muted">Request #{{ $designRequest->request_number }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Design Request Details Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Request Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Request Number:</strong> #{{ $designRequest->request_number }}</p>
                            <p><strong>Title:</strong> {{ $designRequest->title }}</p>
                            <p><strong>Description:</strong> {{ $designRequest->description }}</p>
                            <p><strong>Status:</strong>
                                <span class="badge bg-{{ $designRequest->status === 'completed' ? 'success' : ($designRequest->status === 'in_progress' ? 'primary' : 'warning') }}">
                                    {{ ucfirst($designRequest->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Customer:</strong>
                                @if($designRequest->customer)
                                    {{ $designRequest->customer->name }}
                                @else
                                    <span class="text-muted">Customer not found</span>
                                @endif
                            </p>
                            <p><strong>Designer:</strong>
                                @if($designRequest->designer)
                                    {{ $designRequest->designer->name }}
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </p>
                            <p><strong>Requested At:</strong>
                                @if($designRequest->requested_at)
                                    {{ $designRequest->requested_at->format('M j, Y H:i') }}
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </p>
                            <p><strong>Assigned At:</strong>
                                @if($designRequest->assigned_at)
                                    {{ $designRequest->assigned_at->format('M j, Y H:i') }}
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical Requirements Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Technical Specifications</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Technology Type:</strong> {{ $designRequest->technology_type ?? 'N/A' }}</p>
                            <p><strong>Link Class:</strong> {{ $designRequest->link_class ?? 'N/A' }}</p>
                            <p><strong>Cores Required:</strong> {{ $designRequest->cores_required ?? 'N/A' }}</p>
                            <p><strong>Distance:</strong> {{ $designRequest->distance ?? 'N/A' }} km</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Unit Cost:</strong> ${{ $designRequest->unit_cost ?? '0.00' }}</p>
                            <p><strong>Tax Rate:</strong> {{ $designRequest->tax_rate ?? '0' }}%</p>
                            <p><strong>Terms:</strong> {{ $designRequest->terms ?? 'N/A' }} months</p>
                            <p><strong>Route Name:</strong> {{ $designRequest->route_name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if($designRequest->technical_requirements)
                    <div class="mt-3">
                        <strong>Technical Requirements:</strong>
                        <p class="mt-1">{{ $designRequest->technical_requirements }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Survey Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Survey Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Survey Status:</strong>
                        <span class="badge bg-{{ $designRequest->survey_status === 'completed' ? 'success' : ($designRequest->survey_status === 'assigned' ? 'primary' : ($designRequest->survey_status === 'in_progress' ? 'info' : 'secondary')) }}">
                            {{ ucfirst(str_replace('_', ' ', $designRequest->survey_status)) }}
                        </span>
                    </p>

                    <p><strong>Surveyor:</strong>
                        @if($designRequest->surveyor && $designRequest->surveyor->user)
                            {{ $designRequest->surveyor->user->name }}
                        @else
                            <span class="text-muted">Not assigned</span>
                        @endif
                    </p>

                    @if($designRequest->survey_scheduled_at)
                    <p><strong>Scheduled At:</strong> {{ $designRequest->survey_scheduled_at->format('M j, Y H:i') }}</p>
                    @endif

                    @if($designRequest->survey_estimated_hours)
                    <p><strong>Estimated Hours:</strong> {{ $designRequest->survey_estimated_hours }} hours</p>
                    @endif

                    @if($designRequest->survey_requirements)
                    <div class="mt-3">
                        <strong>Survey Requirements:</strong>
                        <p class="mt-1 small">{{ $designRequest->survey_requirements }}</p>
                    </div>
                    @endif

                    <!-- Assign Surveyor Button -->
                    @if(!$designRequest->surveyor_id)
                    <div class="mt-3">
                        <a href="{{ route('account-manager.design-requests.assign-surveyor', $designRequest->request_number) }}"
                           class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-user-plus me-1"></i> Assign Surveyor
                        </a>
                    </div>
                    @else
                    <div class="mt-3">
                        <form action="{{ route('account-manager.design-requests.unassign-surveyor', $designRequest->request_number) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                    onclick="return confirm('Are you sure you want to unassign this surveyor?')">
                                <i class="fas fa-user-times me-1"></i> Unassign Surveyor
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($designRequest->designer_id)
                        <form action="{{ route('admin.design-requests.unassign-designer', $designRequest->request_number) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-warning btn-sm w-100"
                                    onclick="return confirm('Are you sure you want to unassign the designer?')">
                                <i class="fas fa-user-times me-1"></i> Unassign Designer
                            </button>
                        </form>
                        @else
                        <a href="{{ route('account-manager.design-requests.assign-designer', $designRequest->request_number) }}"
                           class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-user-plus me-1"></i> Assign Designer
                        </a>
                        @endif

                        @if($designRequest->status !== 'completed')
                        <form action="{{ route('admin.design-requests.complete', $designRequest->request_number) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-success btn-sm w-100"
                                    onclick="return confirm('Mark this design request as completed?')">
                                <i class="fas fa-check me-1"></i> Mark Complete
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('admin.design-requests.edit', $designRequest->request_number) }}" class="btn btn-outline-info btn-sm w-100">
                            <i class="fas fa-edit me-1"></i> Edit Request
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Remove the modal section at the bottom since we're using a separate page -->
@endsection
