@extends('layouts.app')

@section('title', 'Design Request #' . $designRequest->request_number)

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800 mb-1">
                        <i class="fas fa-file-alt text-primary me-2"></i>Design Request Details
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('customer.customer-dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('customer.design-requests.index') }}">My Requests</a></li>
                            <li class="breadcrumb-item active" aria-current="page">DR-{{ $designRequest->request_number }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
    <a href="{{ route('customer.design-requests.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Requests
    </a>
    {{-- Edit button disabled since route doesn't exist --}}
    <button class="btn btn-outline-primary" disabled title="Edit functionality not available">
        <i class="fas fa-edit me-1"></i> Edit Request
    </button>
</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            <!-- Request Overview Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Request Overview
                    </h5>
                    <span class="badge bg-light text-dark fs-6">#{{ $designRequest->request_number }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Title</label>
                                <p class="mb-0 fw-semibold">{{ $designRequest->title }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Description</label>
                                <p class="mb-0">{{ $designRequest->description }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Status</label>
                                <div>
                                    @php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'assigned' => 'info',
                                            'in_design' => 'primary',
                                            'designed' => 'success',
                                            'completed' => 'dark'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$designRequest->status] ?? 'secondary' }} fs-6">
                                        <i class="fas fa-circle me-1 small"></i>
                                        {{ ucfirst($designRequest->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Designer</label>
                                <p class="mb-0">
                                    @if($designRequest->designer)
                                        <i class="fas fa-user-check text-success me-1"></i>
                                        {{ $designRequest->designer->name }}
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-user-clock me-1"></i>
                                            Not assigned yet
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Requested</label>
                                <p class="mb-0">
                                    <i class="far fa-calendar me-1"></i>
                                    {{ $designRequest->created_at->format('M j, Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colocation Sites Section -->
            @if($designRequest->colocationSites && $designRequest->colocationSites->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-data-center me-2"></i>Colocation Sites
                    </h5>
                    <span class="badge bg-light text-dark">{{ $designRequest->colocationSites->count() }} sites</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Site Name</th>
                                    <th>Service Type</th>
                                    <th width="120">Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($designRequest->colocationSites as $site)
                                <tr>
                                    <td class="text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-location-dot text-primary me-2"></i>
                                            <strong>{{ $site->site_name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $serviceTypeConfig = [
                                                'shelter_space' => ['color' => 'primary', 'icon' => '🏠', 'label' => 'Shelter Space'],
                                                'rack' => ['color' => 'success', 'icon' => '🗄️', 'label' => 'Rack'],
                                                'cage' => ['color' => 'warning', 'icon' => '🏗️', 'label' => 'Cage'],
                                                'suites' => ['color' => 'info', 'icon' => '🏢', 'label' => 'Suites']
                                            ];
                                            $config = $serviceTypeConfig[$site->service_type] ?? ['color' => 'secondary', 'icon' => '📍', 'label' => $site->service_type];
                                        @endphp
                                        <span class="badge bg-{{ $config['color'] }}">
                                            {{ $config['icon'] }} {{ $config['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $site->created_at->format('M j, Y') }}
                                        </small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Sites Statistics -->
                    <div class="row mt-4">
                        @php
                            $siteTypes = $designRequest->colocationSites->groupBy('service_type');
                        @endphp
                        @foreach($siteTypes as $type => $sites)
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center p-3">
                                    @php
                                        $config = $serviceTypeConfig[$type] ?? ['color' => 'secondary', 'icon' => '📍'];
                                    @endphp
                                    <div class="fs-4 mb-1">{{ $config['icon'] }}</div>
                                    <h4 class="text-{{ $config['color'] }} mb-1">{{ $sites->count() }}</h4>
                                    <small class="text-muted text-truncate d-block">
                                        {{ $config['label'] ?? ucfirst(str_replace('_', ' ', $type)) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center p-3">
                                    <div class="fs-4 mb-1">📊</div>
                                    <h4 class="text-primary mb-1">{{ $designRequest->colocationSites->count() }}</h4>
                                    <small class="text-muted">Total Sites</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Technical Specifications Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>Technical Specifications
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Technology</label>
                                    <p class="mb-0 fw-semibold">{{ $designRequest->technology_type ?? 'N/A' }}</p>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Link Class</label>
                                    <p class="mb-0 fw-semibold">{{ $designRequest->link_class ?? 'N/A' }}</p>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Cores Required</label>
                                    <p class="mb-0 fw-semibold">{{ $designRequest->cores_required ?? 'N/A' }}</p>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Distance</label>
                                    <p class="mb-0 fw-semibold">{{ $designRequest->distance ?? 'N/A' }} km</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Unit Cost</label>
                                    <p class="mb-0 fw-semibold">${{ $designRequest->unit_cost ?? '0.00' }}</p>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Tax Rate</label>
                                    <p class="mb-0 fw-semibold">{{ $designRequest->tax_rate ?? '0' }}%</p>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Terms</label>
                                    <p class="mb-0 fw-semibold">{{ $designRequest->terms ?? 'N/A' }} months</p>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Route Name</label>
                                    <p class="mb-0 fw-semibold">{{ $designRequest->route_name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($designRequest->technical_requirements)
                    <div class="mt-4 pt-3 border-top">
                        <label class="form-label text-muted small mb-2">Technical Requirements</label>
                        <div class="bg-light rounded p-3">
                            <p class="mb-0">{{ $designRequest->technical_requirements }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <!-- Quick Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($designRequest->status === 'pending')
                        <button class="btn btn-outline-primary btn-sm" disabled title="Edit functionality not available">
    <i class="fas fa-edit me-1"></i> Edit Request
</button>
                        @endif
                        <a href="{{ route('customer.tickets') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-question-circle me-1"></i> Get Support
                        </a>
                        <button class="btn btn-outline-info btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Print Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Request Summary Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Request Summary
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Colocation Sites Summary -->
                    @if($designRequest->colocationSites && $designRequest->colocationSites->count() > 0)
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-data-center me-1"></i>Colocation Sites
                        </h6>
                        <div class="mb-3">
                            @foreach($designRequest->colocationSites->take(2) as $site)
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-truncate">{{ $site->site_name }}</span>
                                @php
                                    $config = $serviceTypeConfig[$site->service_type] ?? ['color' => 'secondary'];
                                @endphp
                                <span class="badge bg-{{ $config['color'] }} small">{{ ucfirst(str_replace('_', ' ', $site->service_type)) }}</span>
                            </div>
                            @endforeach
                            @if($designRequest->colocationSites->count() > 2)
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    +{{ $designRequest->colocationSites->count() - 2 }} more sites
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Request Type Badges -->
                    <div class="mb-3">
                        <h6 class="text-muted small mb-2">REQUEST TYPE</h6>
                        <div>
                            <span class="badge bg-primary me-1">Dark Fibre</span>
                            @if($designRequest->colocationSites && $designRequest->colocationSites->count() > 0)
                            <span class="badge bg-success">+ Colocation</span>
                            @endif
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="text-muted small mb-3">TIMELINE</h6>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <small class="text-muted">Requested</small>
                                    <div>{{ $designRequest->created_at->format('M j, Y') }}</div>
                                </div>
                            </div>
                            @if($designRequest->assigned_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <small class="text-muted">Assigned</small>
                                    <div>{{ $designRequest->assigned_at->format('M j, Y') }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support Information -->
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-life-ring me-2"></i>Support
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        Need assistance with your design request? Our support team is here to help.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="mailto:support@company.com" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-envelope me-1"></i> Email Support
                        </a>
                        <a href="tel:+1234567890" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-phone me-1"></i> Call Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: box-shadow 0.15s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.7rem;
    font-weight: 500;
}

.timeline {
    position: relative;
    padding-left: 1.5rem;
}

.timeline-item {
    position: relative;
    margin-bottom: 1rem;
}

.timeline-marker {
    position: absolute;
    left: -1.5rem;
    top: 0.25rem;
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px currentColor;
}

.timeline-content {
    margin-left: 0;
}

.timeline-content small {
    font-size: 0.75rem;
}

.btn {
    border-radius: 0.375rem;
    font-weight: 500;
}
</style>
@endsection
