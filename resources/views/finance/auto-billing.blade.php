@extends('layouts.app')

@section('title', 'Auto Billing Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800 mb-1">
                        <i class="fas fa-robot text-primary me-2"></i>Auto Billing Dashboard
                    </h1>
                    <p class="text-muted mb-0">Automate your billing process and manage recurring payments</p>
                </div>
                <div class="btn-group">
                    <form method="POST" action="{{ route('finance.generate-invoices') }}" class="me-2">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-bolt me-2"></i>Generate Billings Now
                        </button>
                    </form>
                    <a href="{{ route('finance.billing.createSingle') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Create Manual Billing
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-start-primary shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Due Today</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['due_today'] ?? 0 }}</div>
                            <small class="text-muted">Customers</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-primary opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-start-warning shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Pending Generation</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['pending_generation'] ?? 0 }}</div>
                            <small class="text-muted">Ready to process</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-warning opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-start-success shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Auto Billing Enabled</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['auto_billing_customers'] ?? 0 }}</div>
                            <small class="text-muted">Active customers</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-success opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-start-danger shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">Overdue Billings</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['overdue_invoices'] ?? 0 }}</div>
                            <small class="text-muted">Need attention</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-start-info shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Due This Month</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['due_this_month'] ?? 0 }}</div>
                            <small class="text-muted">Upcoming</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-info opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-start-secondary shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-secondary text-uppercase mb-1">Monthly Revenue</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">${{ number_format($stats['monthly_recurring_revenue'] ?? 0, 0) }}</div>
                            <small class="text-muted">Recurring</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-secondary opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-4">
            <div class="card bg-gradient-primary text-white shadow">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-bolt fa-3x mb-3"></i>
                        <h4 class="card-title">Quick Generate</h4>
                        <p class="card-text">Generate all pending billings with one click</p>
                        <form method="POST" action="{{ route('finance.generate-invoices') }}">
                            @csrf
                            <button type="submit" class="btn btn-light btn-lg w-100">
                                <i class="fas fa-play me-2"></i>Run Auto Billing
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card bg-gradient-success text-white shadow">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-cog fa-3x mb-3"></i>
                        <h4 class="card-title">Billing Settings</h4>
                        <p class="card-text">Configure auto-billing rules and schedules</p>
                        <a href="{{ route('finance.billing.index') }}" class="btn btn-light btn-lg w-100">
                            <i class="fas fa-sliders-h me-2"></i>Manage Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card bg-gradient-info text-white shadow">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                        <h4 class="card-title">Reports & Analytics</h4>
                        <p class="card-text">View billing performance and trends</p>
                        <a href="{{ route('finance.reports') }}" class="btn btn-light btn-lg w-100">
                            <i class="fas fa-chart-bar me-2"></i>View Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="row">
        <!-- Due Customers -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clock text-warning me-2"></i>
                        Customers Due for Billing
                        <span class="badge bg-warning ms-2">{{ $dueCustomers->total() }}</span>
                    </h5>
                    <a href="{{ route('finance.billing.create') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i>New Billing
                    </a>
                </div>
                <div class="card-body">
                    @if($dueCustomers->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($dueCustomers as $customer)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-3">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $customer->name }}</h6>
                                        <small class="text-muted">{{ $customer->email }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="badge bg-warning mb-1">{{ $customer->leaseBillings->count() }} pending</div>
                                    <div class="text-success fw-bold">${{ number_format($customer->leaseBillings->sum('total_amount'), 2) }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @if($dueCustomers->hasPages())
                        <div class="mt-3">
                            {{ $dueCustomers->links() }}
                        </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-muted">All Caught Up!</h5>
                            <p class="text-muted">No customers due for billing today.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Auto Billing Customers -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-robot text-success me-2"></i>
                        Auto Billing Customers
                        <span class="badge bg-success ms-2">{{ $autoBillingCustomers->total() }}</span>
                    </h5>
                    <a href="{{ route('finance.billing.index') }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-eye me-1"></i>View All
                    </a>
                </div>
                <div class="card-body">
                    @if($autoBillingCustomers->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($autoBillingCustomers as $customer)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-3">
                                        <i class="fas fa-user-check text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $customer->name }}</h6>
                                        <small class="text-muted">{{ $customer->email }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $customer->pending_billings_count > 0 ? 'warning' : 'success' }} mb-1">
                                        {{ $customer->pending_billings_count }} pending
                                    </span>
                                    <div>
                                        <span class="badge bg-success">
                                            <i class="fas fa-robot me-1"></i>Auto
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @if($autoBillingCustomers->hasPages())
                        <div class="mt-3">
                            {{ $autoBillingCustomers->links() }}
                        </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-robot fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Auto Billing Customers</h5>
                            <p class="text-muted">Enable auto billing for customers to see them here.</p>
                            <a href="{{ route('finance.billing.index') }}" class="btn btn-primary">
                                <i class="fas fa-cog me-2"></i>Configure Billing
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Help Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title mb-1">Need help with auto billing?</h5>
                            <p class="card-text text-muted mb-0">Check out our documentation or contact support for assistance with setting up automated billing.</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <button class="btn btn-outline-primary me-2">
                                <i class="fas fa-book me-2"></i>Documentation
                            </button>
                            <button class="btn btn-primary">
                                <i class="fas fa-headset me-2"></i>Contact Support
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
}
.bg-gradient-primary {
    background: linear-gradient(45deg, #4e73df, #224abe);
}
.bg-gradient-success {
    background: linear-gradient(45deg, #1cc88a, #13855c);
}
.bg-gradient-info {
    background: linear-gradient(45deg, #36b9cc, #258391);
}
.card {
    border: none;
    border-radius: 0.5rem;
}
.list-group-item {
    border: none;
    border-bottom: 1px solid #e3e6f0;
}
.list-group-item:last-child {
    border-bottom: none;
}
</style>
@endsection
