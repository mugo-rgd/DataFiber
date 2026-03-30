@extends('layouts.app')

@section('title', 'My Design Requests')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    <!-- New Request Button - On the LEFT -->
                    <a href="{{ route('customer.design-requests.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> New Request
                    </a>
                </div>

                <h1 class="h3 text-gray-800 position-absolute start-50 translate-middle-x">
                    <i class="fas fa-drafting-compass text-primary"></i> My Design Requests
                </h1>

                <div class="d-flex gap-2">
                    <!-- Return to Dashboard Button - On the RIGHT -->
                    <a href="{{ route('customer.customer-dashboard') }}" class="btn btn-warning shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i>
                        <strong>BACK TO DASHBOARD</strong>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    @if($designRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Request #</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Designer</th>
                                        <th>Requested At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($designRequests as $designRequest)
                                        <tr>
                                            <td>#{{ $designRequest->request_number }}</td>
                                            <td>{{ $designRequest->title }}</td>
                                            <td>
                                                <span class="badge bg-{{ $designRequest->status === 'completed' ? 'success' : ($designRequest->status === 'in_progress' ? 'primary' : 'warning') }}">
                                                    {{ ucfirst($designRequest->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($designRequest->designer)
                                                    {{ $designRequest->designer->name }}
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            </td>
                                            <td>{{ $designRequest->created_at->format('M j, Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('customer.design-requests.show', $designRequest) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">You haven't created any design requests yet.</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('customer.design-requests.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Create Your First Request
                                </a>
                                <a href="{{ route('customer.customer-dashboard') }}" class="btn btn-warning">
                                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
