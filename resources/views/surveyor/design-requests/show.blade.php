@extends('layouts.app')

@section('title', 'Design Request Details')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Design Request Details</h1>
                <a href="{{ route('surveyor.routes') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Routes
                </a>
            </div>

            <!-- Design Request Details Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Request Information #{{ $designRequest->id }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Request Details</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td>{{ $designRequest->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $designRequest->status === 'completed' ? 'success' : ($designRequest->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $designRequest->status)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Priority:</strong></td>
                                    <td>
                                        @if(isset($designRequest->priority))
                                            <span class="badge bg-{{ $designRequest->priority === 'high' ? 'danger' : ($designRequest->priority === 'medium' ? 'warning' : 'info') }}">
                                                {{ ucfirst($designRequest->priority) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Not Set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $designRequest->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $designRequest->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-primary">Customer Information</h6>
                            @if($designRequest->customer)
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $designRequest->customer->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $designRequest->customer->email }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $designRequest->customer->phone ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Company:</strong></td>
                                        <td>{{ $designRequest->customer->company ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            @else
                                <p class="text-muted">No customer information available.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    @if($designRequest->description || $designRequest->address)
                    <div class="row mt-4">
                        @if($designRequest->description)
                        <div class="col-12 mb-3">
                            <h6 class="text-primary">Description</h6>
                            <div class="border rounded p-3 bg-light">
                                {{ $designRequest->description }}
                            </div>
                        </div>
                        @endif

                        @if($designRequest->address)
                        <div class="col-12">
                            <h6 class="text-primary">Address/Location</h6>
                            <div class="border rounded p-3 bg-light">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                {{ $designRequest->address }}
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="{{ route('surveyor.routes') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Routes
                        </a>
                        <a href="#" class="btn btn-success">
                            <i class="fas fa-map-marked-alt me-1"></i> Get Directions
                        </a>
                        <button class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Update Status
                        </button>
                        <button class="btn btn-info">
                            <i class="fas fa-file-alt me-1"></i> Generate Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
