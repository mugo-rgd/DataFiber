@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="header-actions">
        <div>
            <h1 class="h3 text-gray-800 mb-2">
                <i class="fas fa-drafting-compass text-primary me-2"></i> Customer Design Requests
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}" class="text-decoration-none"><i class="fas fa-users me-1"></i>Customers</a></li>
                    <li class="breadcrumb-item active text-primary"><i class="fas fa-drafting-compass me-1"></i>Design Requests</li>
                </ol>
            </nav>
        </div>
        <div>
            <button type="button" class="btn btn-outline-secondary" onclick="goBack()">
                <i class="fas fa-arrow-left me-2"></i>Go Back
            </button>
        </div>
    </div>

    <!-- Customer Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="card-title">{{ $customer->name }}</h5>
                    <p class="card-text mb-1"><i class="fas fa-envelope me-2 text-muted"></i>{{ $customer->email }}</p>
                    @if($customer->phone)
                    <p class="card-text mb-1"><i class="fas fa-phone me-2 text-muted"></i>{{ $customer->phone }}</p>
                    @endif
                    @if($customer->company)
                    <p class="card-text"><i class="fas fa-building me-2 text-muted"></i>{{ $customer->company }}</p>
                    @endif
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-primary fs-6">Total Requests: {{ $requests->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Design Requests Table -->
    <div class="card shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i> Design Requests List</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Request ID</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Distance</th>
                            <th>Cores</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>#{{ $request->id }}</strong></td>
                            <td>{{ $request->title }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'in_progress' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        'approved' => 'primary',
                                        'rejected' => 'secondary'
                                    ];
                                    $statusColor = $statusColors[$request->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">{{ ucfirst(str_replace('_', ' ', $request->status)) }}</span>
                            </td>
                            <td>
                                @if($request->distance)
                                    {{ $request->distance }} km
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </td>
                            <td>
                                @if($request->cores_required)
                                    {{ $request->cores_required }} cores
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.design-requests.show', $request->id) }}">
                                                <i class="fas fa-eye me-2"></i>View Details
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.design-requests.edit', $request->id) }}">
                                                <i class="fas fa-edit me-2"></i>Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.quotations.create', ['request' => $request->id]) }}">
                                                <i class="fas fa-file-invoice me-2"></i>Create Quotation
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-drafting-compass fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No design requests found</h5>
                                <p class="text-muted">This customer hasn't submitted any design requests yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($requests->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} entries
                    </div>
                    <div>
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function goBack() {
    if (document.referrer && document.referrer.includes(window.location.host)) {
        window.history.back();
    } else {
        window.location.href = "{{ route('admin.customers.index') }}";
    }
}
</script>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    border-radius: 50%;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #6c757d;
}

.btn-group .dropdown-toggle::after {
    margin-left: 0.5em;
}
</style>
@endsection
