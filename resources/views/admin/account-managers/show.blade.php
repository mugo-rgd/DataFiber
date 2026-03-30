@extends('layouts.app')

@section('title', 'Account Manager Details - Admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-tie me-2"></i>Account Manager Details
        </h1>
        <div>
            <a href="{{ route('admin.account-managers.edit', $manager->id) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.account-managers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Profile Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="avatar-circle bg-primary text-white mx-auto mb-3"
                         style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 48px;">
                        {{ strtoupper(substr($manager->name, 0, 1)) }}
                    </div>
                    <h4 class="mb-1">{{ $manager->name }}</h4>
                    <p class="text-muted mb-2">{{ $manager->email }}</p>
                    <div class="mb-3">
                        @if($manager->status === 'active')
                            <span class="badge bg-success px-3 py-2">Active</span>
                        @else
                            <span class="badge bg-secondary px-3 py-2">Inactive</span>
                        @endif
                    </div>

                    <hr>

                    <div class="text-start">
                        <p><strong><i class="fas fa-phone me-2"></i>Phone:</strong> {{ $manager->phone ?? 'Not provided' }}</p>
                        <p><strong><i class="fas fa-building me-2"></i>Company:</strong> {{ $manager->company_name ?? 'Not provided' }}</p>
                        <p><strong><i class="fas fa-calendar me-2"></i>Joined:</strong> {{ $manager->created_at->format('F d, Y') }}</p>
                        <p><strong><i class="fas fa-clock me-2"></i>Last Updated:</strong> {{ $manager->updated_at->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="col-xl-8 col-md-6 mb-4">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Customers</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $manager->managed_customers_count }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Active Customers</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $manager->managedCustomers ? $manager->managedCustomers->where('status', 'active')->count() : 0 }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Inactive Customers</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $manager->managedCustomers ? $manager->managedCustomers->where('status', 'inactive')->count() : 0 }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        This Month Assignments</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $manager->managedCustomers ? $manager->managedCustomers->where('assigned_at', '>=', now()->startOfMonth())->count() : 0 }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Customers List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-users me-2"></i>Assigned Customers
            </h6>
            <span class="badge bg-light text-primary px-3 py-2">{{ $manager->managed_customers_count }} customers</span>
        </div>

        <div class="card-body">
            @if($manager->managedCustomers && $manager->managed_customers_count > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Customer Name</th>
                                <th>Company</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Assigned Date</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($manager->managedCustomers as $index => $customer)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $customer->name }}</strong>
                                </td>
                                <td>{{ $customer->company_name ?? 'N/A' }}</td>
                                <td>
                                    <a href="mailto:{{ $customer->email }}" class="text-decoration-none">
                                        {{ $customer->email }}
                                    </a>
                                </td>
                                <td>
                                    @if($customer->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($customer->status === 'inactive')
                                        <span class="badge bg-warning text-dark">Inactive</span>
                                    @else
                                        <span class="badge bg-danger">Suspended</span>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->assigned_at)
                                        <span title="{{ \Carbon\Carbon::parse($customer->assigned_at)->format('F d, Y h:i A') }}">
                                            {{ \Carbon\Carbon::parse($customer->assigned_at)->format('M d, Y') }}
                                        </span>
                                        @if($customer->assignment_notes)
                                            <i class="fas fa-info-circle text-info ms-1"
                                               title="{{ $customer->assignment_notes }}"></i>
                                        @endif
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                             <a href="{{ route('admin.customers.show', $customer->id) }}?manager={{ $manager->id }}"
                                class="btn btn-sm btn-info"
                                title="View Customer Details">
                                <i class="fas fa-eye"></i>
                            </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-user-slash fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted">No Customers Assigned</h5>
                    <p class="text-muted mb-3">This account manager doesn't have any customers yet.</p>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Assign Customers
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Assignment Notes -->
    @if($manager->assignment_notes)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-sticky-note me-2"></i>Assignment Notes
            </h6>
        </div>
        <div class="card-body">
            <p class="mb-0">{{ $manager->assignment_notes }}</p>
        </div>
    </div>
    @endif

    <!-- Recent Activity Timeline -->
    @if($manager->managedCustomers && $manager->managedCustomers->whereNotNull('assigned_at')->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-chart-line me-2"></i>Recent Assignment Activity
            </h6>
        </div>
        <div class="card-body">
            <div class="timeline">
                @foreach($manager->managedCustomers->whereNotNull('assigned_at')->sortByDesc('assigned_at')->take(5) as $customer)
                    <div class="timeline-item">
                        <div class="timeline-item-marker">
                            <div class="timeline-item-marker-indicator bg-primary"></div>
                        </div>
                        <div class="timeline-item-content">
                            <small class="text-muted">{{ \Carbon\Carbon::parse($customer->assigned_at)->diffForHumans() }}</small>
                            <h6 class="mb-0">
                                <a href="{{ route('admin.customers.show', $customer->id) }}"
                                   target="_blank"
                                   class="text-decoration-none">
                                    {{ $customer->name }}
                                </a>
                                @if($customer->assignment_notes)
                                    <i class="fas fa-info-circle text-info ms-1"
                                       title="{{ $customer->assignment_notes }}"></i>
                                @endif
                            </h6>
                            <small>{{ $customer->company_name ?? 'Individual' }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 2rem;
        margin-bottom: 1rem;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }

    .timeline-item-marker {
        position: absolute;
        left: -2rem;
        width: 2rem;
        text-align: center;
    }

    .timeline-item-marker-indicator {
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        margin: 0 auto;
    }

    .timeline-item-content {
        padding-left: 0.5rem;
    }

    .avatar-circle {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }

    .btn-group .btn i {
        font-size: 0.875rem;
    }

    .bg-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
</style>
@endpush

@push('scripts')
<script>
function showAssignmentHistory(customerId) {
    window.location.href = `{{ url('admin/customers') }}/${customerId}/assignments`;
}
</script>
@endpush
@endsection
