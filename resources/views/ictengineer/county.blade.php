{{-- resources/views/ictengineer/county.blade.php --}}
@extends('layouts.app')

@section('title', 'County Requests')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-map-marker-alt text-primary"></i> {{ $county->name }} - Design Requests
                </h1>
                <a href="{{ route('ictengineer.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('ictengineer.requests.index') }}">Design Requests</a></li>
                    <li class="breadcrumb-item active">{{ $county->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- County Information -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">County Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>County:</strong> {{ $county->name }}</p>
                            <p><strong>Code:</strong> {{ $county->code ?? 'N/A' }}</p>
                            <p><strong>Region:</strong> {{ $county->region ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Requests:</strong> {{ $countyRequests->total() }}</p>
                            <p><strong>Your Requests:</strong> {{ $countyRequests->count() }}</p>
                            @if($county->description)
                                <p><strong>Description:</strong> {{ $county->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $statusCounts = [
                                'pending' => $countyRequests->where('status', 'pending')->count(),
                                'in_progress' => $countyRequests->where('status', 'in_progress')->count(),
                                'completed' => $countyRequests->where('status', 'completed')->count(),
                            ];
                        @endphp

                        <div class="col-4 text-center">
                            <div class="card bg-light">
                                <div class="card-body p-2">
                                    <h3 class="text-primary mb-0">{{ $statusCounts['pending'] }}</h3>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="card bg-light">
                                <div class="card-body p-2">
                                    <h3 class="text-warning mb-0">{{ $statusCounts['in_progress'] }}</h3>
                                    <small class="text-muted">In Progress</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="card bg-light">
                                <div class="card-body p-2">
                                    <h3 class="text-success mb-0">{{ $statusCounts['completed'] }}</h3>
                                    <small class="text-muted">Completed</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- County Requests Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Design Requests in {{ $county->name }}</h5>
                    <span class="badge bg-light text-dark">{{ $countyRequests->total() }} requests</span>
                </div>
                <div class="card-body">
                    @if($countyRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Request #</th>
                                        <th>Customer</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>ICT Status</th>
                                        <th>Priority</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($countyRequests as $request)
                                    <tr>
                                        <td>
                                            <strong>{{ $request->request_number }}</strong>
                                        </td>
                                        <td>{{ $request->customer->name ?? 'N/A' }}</td>
                                        <td>{{ Str::limit($request->title, 40) }}</td>
                                        <td>
                                            <span class="badge bg-{{ match($request->status) {
                                                'pending' => 'secondary',
                                                'in_progress' => 'warning',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                                default => 'secondary'
                                            } }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ match($request->ict_status) {
                                                'pending_assignment' => 'secondary',
                                                'assigned' => 'primary',
                                                'inspection_scheduled' => 'warning',
                                                'inspection_completed' => 'info',
                                                'certificate_generated' => 'success',
                                                'certificate_sent' => 'success',
                                                'completed' => 'success',
                                                default => 'secondary'
                                            } }}">
                                                {{ ucfirst(str_replace('_', ' ', $request->ict_status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($request->priority)
                                            <span class="badge bg-{{ match($request->priority) {
                                                'low' => 'success',
                                                'medium' => 'warning',
                                                'high' => 'danger',
                                                default => 'secondary'
                                            } }}">
                                                {{ ucfirst($request->priority) }}
                                            </span>
                                            @endif
                                        </td>
                                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('ictengineer.requests.show', $request->id) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $countyRequests->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                            <h4>No Design Requests Found</h4>
                            <p class="text-muted">You don't have any design requests in {{ $county->name }} county.</p>
                            <a href="{{ route('ictengineer.requests.index') }}" class="btn btn-primary">
                                <i class="fas fa-list"></i> View All Requests
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- County Map or Additional Info (Optional) -->
    @if($county->latitude && $county->longitude)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">County Location</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt fa-3x text-success mb-3"></i>
                        <p><strong>Coordinates:</strong> {{ $county->latitude }}, {{ $county->longitude }}</p>
                        <a href="https://maps.google.com/?q={{ $county->latitude }},{{ $county->longitude }}"
                           target="_blank" class="btn btn-outline-success">
                            <i class="fas fa-external-link-alt"></i> View on Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any JavaScript needed for the county page
        console.log('County requests page loaded');
    });
</script>
@endsection
