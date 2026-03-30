@extends('layouts.app')

@section('title', 'Lease Details - ' . $lease->lease_number)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-file-contract text-primary"></i> Lease Details
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('account-manager.leases.index') }}">Lease Management</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $lease->lease_number }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="btn-group">
                    <a href="{{ route('account-manager.leases.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Leases
                    </a>
                    <a href="{{ route('account-manager.leases.edit', $lease) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Lease
                    </a>
                    <a href="{{ route('account-manager.leases.pdf', $lease) }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-pdf me-2"></i>PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Lease Details -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Lease Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Lease Number:</strong>
                                <div class="form-control bg-light">{{ $lease->lease_number }}</div>
                            </div>
                            <div class="mb-3">
                                <strong>Customer:</strong>
                                <div class="form-control bg-light">{{ $lease->customer->name }}</div>
                            </div>
                            <div class="mb-3">
                                <strong>Service Type:</strong>
                                <div class="form-control bg-light">{{ ucfirst(str_replace('_', ' ', $lease->service_type)) }}</div>
                            </div>
                            <div class="mb-3">
                                <strong>Status:</strong>
                                <div class="form-control bg-light">
                                    <span class="badge bg-{{ $lease->status == 'active' ? 'success' : ($lease->status == 'pending' ? 'warning' : ($lease->status == 'draft' ? 'secondary' : 'danger')) }}">
                                        {{ ucfirst($lease->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Monthly Cost:</strong>
                                <div class="form-control bg-light">{{ number_format($lease->monthly_cost, 2) }} {{ $lease->currency }}</div>
                            </div>
                            <div class="mb-3">
                                <strong>Start Date:</strong>
                                <div class="form-control bg-light">{{ $lease->start_date->format('M d, Y') }}</div>
                            </div>
                            <div class="mb-3">
                                <strong>End Date:</strong>
                                <div class="form-control bg-light">{{ $lease->end_date->format('M d, Y') }}</div>
                            </div>
                            <div class="mb-3">
                                <strong>Contract Term:</strong>
                                <div class="form-control bg-light">{{ $lease->contract_term_months }} months</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Route:</strong>
                                <div class="form-control bg-light">{{ $lease->start_location }} → {{ $lease->end_location }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($lease->distance_km)
                            <div class="mb-3">
                                <strong>Distance:</strong>
                                <div class="form-control bg-light">{{ $lease->distance_km }} km</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($lease->bandwidth)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Bandwidth:</strong>
                                <div class="form-control bg-light">{{ $lease->bandwidth }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($lease->technology)
                            <div class="mb-3">
                                <strong>Technology:</strong>
                                <div class="form-control bg-light">{{ $lease->technology }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($lease->billing_cycle)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Billing Cycle:</strong>
                                <div class="form-control bg-light">{{ ucfirst($lease->billing_cycle) }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Installation Fee:</strong>
                                <div class="form-control bg-light">${{ number_format($lease->installation_fee, 2) }} {{ $lease->currency }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Technical Specifications -->
            @if($lease->technical_specifications)
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs me-2"></i>Technical Specifications
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-control bg-light" style="white-space: pre-line; min-height: 100px;">{{ $lease->technical_specifications }}</div>
                </div>
            </div>
            @endif

            <!-- Service Level Agreement -->
            @if($lease->service_level_agreement)
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-handshake me-2"></i>Service Level Agreement
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-control bg-light" style="white-space: pre-line; min-height: 100px;">{{ $lease->service_level_agreement }}</div>
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($lease->notes)
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-sticky-note me-2"></i>Additional Notes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-control bg-light" style="white-space: pre-line; min-height: 60px;">{{ $lease->notes }}</div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar with Actions -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs me-2"></i>Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('account-manager.leases.edit', $lease) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Lease
                        </a>
                        <a href="{{ route('account-manager.leases.pdf', $lease) }}" class="btn btn-outline-primary">
                            <i class="fas fa-file-pdf me-2"></i>Generate PDF
                        </a>
                        <a href="{{ route('account-manager.leases.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>Customer Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Name:</strong>
                        <div>{{ $lease->customer->name }}</div>
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong>
                        <div>{{ $lease->customer->email }}</div>
                    </div>
                    @if($lease->customer->phone)
                    <div class="mb-3">
                        <strong>Phone:</strong>
                        <div>{{ $lease->customer->phone }}</div>
                    </div>
                    @endif
                    @if($lease->customer->company)
                    <div class="mb-3">
                        <strong>Company:</strong>
                        <div>{{ $lease->customer->company }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Contract Timeline -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar me-2"></i>Contract Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Start Date:</strong>
                        <div class="text-success">{{ $lease->start_date->format('M d, Y') }}</div>
                    </div>
                    <div class="mb-3">
                        <strong>End Date:</strong>
                        <div class="text-primary">{{ $lease->end_date->format('M d, Y') }}</div>
                    </div>
                    <div class="mb-3">
                        <strong>Duration:</strong>
                        <div>{{ $lease->contract_term_months }} months</div>
                    </div>
                    <div class="mb-3">
                        <strong>Remaining:</strong>
                        <div class="{{ $lease->isExpired() ? 'text-danger' : 'text-warning' }}">
                            @if($lease->isExpired())
                                Expired
                            @else
                                {{ $lease->daysUntilExpiry() }} days
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endpush
