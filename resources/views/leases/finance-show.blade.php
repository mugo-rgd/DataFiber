@extends('layouts.app')

@section('title', 'Lease Details - ' . $lease->lease_number)

@section('content')
@php
    $isUSD = $lease->currency === 'USD';
    $currencySymbol = $isUSD ? '$' : '';
    $currencyClass = $isUSD ? 'text-primary' : 'text-warning';
    $currencyBadge = $isUSD ? 'bg-primary' : 'bg-warning text-dark';

    // Parse dates safely
    $startDate = $lease->start_date ? \Carbon\Carbon::parse($lease->start_date) : null;
    $endDate = $lease->end_date ? \Carbon\Carbon::parse($lease->end_date) : null;
    $nextBillingDate = $lease->next_billing_date ? \Carbon\Carbon::parse($lease->next_billing_date) : null;
    $activatedAt = $lease->activated_at ? \Carbon\Carbon::parse($lease->activated_at) : null;
    $lastBilledAt = $lease->last_billed_at ? \Carbon\Carbon::parse($lease->last_billed_at) : null;
    $acceptanceCertificateGeneratedAt = $lease->acceptance_certificate_generated_at ? \Carbon\Carbon::parse($lease->acceptance_certificate_generated_at) : null;
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                @if(\Illuminate\Support\Facades\Route::has('leases.finance.index'))
                                    <a href="{{ route('leases.finance.index') }}">Leases</a>
                                @else
                                    <span>Leases</span>
                                @endif
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $lease->lease_number }}</li>
                        </ol>
                    </nav>
                    <h2 class="mb-0">Lease Details</h2>
                    <p class="text-muted mb-0">{{ $lease->title }}</p>
                </div>
                <div class="btn-group">
                    @if(\Illuminate\Support\Facades\Route::has('leases.finance.index'))
                        <a href="{{ route('leases.finance.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    @endif

                    @if(\Illuminate\Support\Facades\Route::has('leases.finance.edit'))
                        <a href="{{ route('leases.finance.edit', $lease->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    @endif

                    @if(\Illuminate\Support\Facades\Route::has('invoices.create'))
                        <a href="{{ route('invoices.create', ['lease_id' => $lease->id]) }}" class="btn btn-success">
                            <i class="fas fa-file-invoice-dollar"></i> Create Invoice
                        </a>
                    @endif
                </div>
            </div>

            <!-- Lease Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lease Information</h5>
                    <div>
                        <span class="badge bg-light text-dark me-2">{{ $lease->lease_number }}</span>
                        <span class="badge {{ $currencyBadge }} me-2">
                            {{ $lease->currency }}
                        </span>
                        @php
                            $statusColors = [
                                'draft' => 'secondary',
                                'pending' => 'warning',
                                'active' => 'success',
                                'expired' => 'info',
                                'terminated' => 'dark',
                                'cancelled' => 'danger'
                            ];
                        @endphp
                        <span class="badge bg-{{ $statusColors[$lease->status] ?? 'secondary' }}">
                            {{ ucfirst($lease->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Customer</label>
                                <p class="mb-0 fw-bold">{{ $lease->customer->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Service Type</label>
                                <p class="mb-0">
                                    <span class="badge bg-info">
                                        {{ str_replace('_', ' ', $lease->service_type) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Contract Term</label>
                                <p class="mb-0 fw-bold">{{ $lease->contract_term_months }} months</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Start & End Dates</label>
                                <p class="mb-0">
                                    @if($startDate && $endDate)
                                        {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}
                                        <span class="text-muted ms-2">({{ $startDate->diffInDays($endDate) }} days)</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Billing Cycle</label>
                                <p class="mb-0">
                                    <span class="badge bg-secondary">{{ $lease->billing_cycle }}</span>
                                    @if($nextBillingDate)
                                        <span class="ms-2 {{ $nextBillingDate < now() ? 'text-danger fw-bold' : '' }}">
                                            Next: {{ $nextBillingDate->format('M d, Y') }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Route</label>
                                <p class="mb-0">
                                    <i class="fas fa-map-marker-alt text-danger"></i> {{ $lease->start_location }}
                                    <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                    <i class="fas fa-map-marker-alt text-success"></i> {{ $lease->end_location }}
                                    @if($lease->distance_km)
                                        <span class="text-muted ms-2">({{ $lease->distance_km }} km)</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Information -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Financial Details</h5>
                                <span class="badge {{ $currencyBadge }} fs-6">{{ $lease->currency }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body text-center">
                                            <label class="form-label text-muted small mb-1">Monthly Cost</label>
                                            <h4 class="{{ $currencyClass }} mb-0">
                                                {{ $currencySymbol }}{{ number_format($lease->monthly_cost, 2) }}
                                            </h4>
                                            <small class="text-muted">{{ $lease->currency }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body text-center">
                                            <label class="form-label text-muted small mb-1">Installation Fee</label>
                                            <h4 class="{{ $currencyClass }} mb-0">
                                                {{ $currencySymbol }}{{ number_format($lease->installation_fee, 2) }}
                                            </h4>
                                            <small class="text-muted">{{ $lease->currency }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body text-center">
                                            <label class="form-label text-muted small mb-1">Total Contract Value</label>
                                            <h4 class="{{ $currencyClass }} mb-0">
                                                {{ $currencySymbol }}{{ number_format($lease->total_contract_value, 2) }}
                                            </h4>
                                            <small class="text-muted">{{ $lease->currency }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Metrics -->
                            @isset($financialMetrics)
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <label class="form-label text-muted small mb-1">Total Invoiced</label>
                                            <h4 class="{{ $currencyClass }} mb-0">
                                                {{ $currencySymbol }}{{ number_format($financialMetrics['total_invoiced'] ?? 0, 2) }}
                                            </h4>
                                            <small class="text-muted">{{ $lease->currency }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <label class="form-label text-muted small mb-1">Outstanding Balance</label>
                                            <h4 class="text-danger mb-0">
                                                {{ $currencySymbol }}{{ number_format($financialMetrics['outstanding_balance'] ?? 0, 2) }}
                                            </h4>
                                            <small class="text-muted">{{ $lease->currency }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endisset

                            <!-- Revenue Projection -->
                            <div class="mt-4">
                                <h6 class="border-bottom pb-2 mb-3">Revenue Projection ({{ $lease->currency }})</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Year</th>
                                                <th class="text-end">Monthly Revenue</th>
                                                <th class="text-end">Annual Revenue</th>
                                                <th class="text-end">Cumulative</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $years = ceil($lease->contract_term_months / 12);
                                                $cumulative = 0;
                                                $currentDate = now();
                                            @endphp
                                            @for($i = 1; $i <= $years; $i++)
                                                @php
                                                    $monthsInYear = min(12, $lease->contract_term_months - ($i-1)*12);
                                                    $annualRevenue = $monthsInYear * $lease->monthly_cost;
                                                    $cumulative += $annualRevenue;
                                                    $yearStart = $startDate ? $startDate->copy()->addMonths(($i-1)*12) : null;
                                                    $yearEnd = $yearStart ? $yearStart->copy()->addMonths($monthsInYear) : null;
                                                    $isPast = $yearEnd && $yearEnd < $currentDate;
                                                    $isCurrent = $yearStart && $yearStart <= $currentDate && $yearEnd && $yearEnd > $currentDate;
                                                @endphp
                                                @if($yearStart && $yearEnd)
                                                <tr class="{{ $isCurrent ? 'table-warning' : ($isPast ? 'table-light' : '') }}">
                                                    <td>
                                                        Year {{ $i }}
                                                        <div class="text-muted small">
                                                            {{ $yearStart->format('M Y') }} - {{ $yearEnd->format('M Y') }}
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        {{ $currencySymbol }}{{ number_format($lease->monthly_cost, 2) }}
                                                    </td>
                                                    <td class="text-end">
                                                        {{ $currencySymbol }}{{ number_format($annualRevenue, 2) }}
                                                    </td>
                                                    <td class="text-end">
                                                        {{ $currencySymbol }}{{ number_format($cumulative, 2) }}
                                                    </td>
                                                    <td>
                                                        @if($isPast)
                                                            <span class="badge bg-success">Realized</span>
                                                        @elseif($isCurrent)
                                                            <span class="badge bg-warning">In Progress</span>
                                                        @else
                                                            <span class="badge bg-info">Projected</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endif
                                            @endfor
                                            <!-- Total Row -->
                                            <tr class="table-primary">
                                                <td><strong>Total Contract</strong></td>
                                                <td class="text-end">
                                                    <strong>{{ $currencySymbol }}{{ number_format($lease->monthly_cost, 2) }}</strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong>{{ $currencySymbol }}{{ number_format($lease->contract_term_months * $lease->monthly_cost, 2) }}</strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong>{{ $currencySymbol }}{{ number_format($lease->total_contract_value, 2) }}</strong>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Technical Specifications -->
                    @if($lease->technical_specifications || $lease->service_level_agreement)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Service Details</h5>
                        </div>
                        <div class="card-body">
                            @if($lease->technical_specifications)
                            <div class="mb-4">
                                <h6>Technical Specifications</h6>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($lease->technical_specifications)) !!}
                                </div>
                            </div>
                            @endif

                            @if($lease->service_level_agreement)
                            <div>
                                <h6>Service Level Agreement</h6>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($lease->service_level_agreement)) !!}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Right Sidebar -->
                <div class="col-md-4">
                    <!-- Timeline -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Billing Timeline</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled timeline">
                                @if($startDate)
                                <li class="mb-3">
                                    <div class="d-flex">
                                        <div class="timeline-icon bg-success text-white">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-1">Contract Start</h6>
                                            <p class="text-muted mb-0">{{ $startDate->format('M d, Y') }}</p>
                                            <small class="text-muted">{{ $startDate->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($activatedAt)
                                <li class="mb-3">
                                    <div class="d-flex">
                                        <div class="timeline-icon bg-info text-white">
                                            <i class="fas fa-play-circle"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-1">Activated</h6>
                                            <p class="text-muted mb-0">{{ $activatedAt->format('M d, Y') }}</p>
                                            <small class="text-muted">{{ $activatedAt->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($nextBillingDate)
                                <li class="mb-3">
                                    <div class="d-flex">
                                        <div class="timeline-icon {{ $nextBillingDate < now() ? 'bg-danger' : 'bg-warning' }} text-white">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-1 {{ $nextBillingDate < now() ? 'text-danger' : '' }}">
                                                Next Billing Date
                                            </h6>
                                            <p class="{{ $nextBillingDate < now() ? 'text-danger fw-bold' : 'text-muted' }} mb-0">
                                                {{ $nextBillingDate->format('M d, Y') }}
                                            </p>
                                            <small class="{{ $nextBillingDate < now() ? 'text-danger' : 'text-muted' }}">
                                                {{ $nextBillingDate->diffForHumans() }}
                                                @if($nextBillingDate < now())
                                                    <span class="badge bg-danger ms-1">Overdue</span>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($lastBilledAt)
                                <li class="mb-3">
                                    <div class="d-flex">
                                        <div class="timeline-icon bg-secondary text-white">
                                            <i class="fas fa-receipt"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-1">Last Billed</h6>
                                            <p class="text-muted mb-0">{{ $lastBilledAt->format('M d, Y') }}</p>
                                            <small class="text-muted">{{ $lastBilledAt->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($endDate)
                                <li class="mb-3">
                                    <div class="d-flex">
                                        <div class="timeline-icon bg-dark text-white">
                                            <i class="fas fa-calendar-times"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-1">Contract End</h6>
                                            <p class="text-muted mb-0">{{ $endDate->format('M d, Y') }}</p>
                                            <small class="text-muted">{{ $endDate->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <!-- Currency Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Currency Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Currency</label>
                                <div class="d-flex align-items-center">
                                    <span class="badge {{ $currencyBadge }} fs-6 px-3 py-2 me-2">
                                        {{ $lease->currency }}
                                    </span>
                                    @if($lease->currency == 'USD')
                                        <i class="fas fa-dollar-sign text-primary"></i>
                                        <span class="ms-1">United States Dollar</span>
                                    @else
                                        <i class="fas fa-shilling-sign text-warning"></i>
                                        <span class="ms-1">Kenyan Shilling</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Financial Summary</label>
                                <div class="bg-light p-3 rounded">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Monthly:</span>
                                        <strong class="{{ $currencyClass }}">
                                            {{ $currencySymbol }}{{ number_format($lease->monthly_cost, 2) }}
                                        </strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Installation:</span>
                                        <strong class="{{ $currencyClass }}">
                                            {{ $currencySymbol }}{{ number_format($lease->installation_fee, 2) }}
                                        </strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Total Value:</span>
                                        <strong class="{{ $currencyClass }}">
                                            {{ $currencySymbol }}{{ number_format($lease->total_contract_value, 2) }}
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if(\Illuminate\Support\Facades\Route::has('invoices.create'))
                                    <a href="{{ route('invoices.create', ['lease_id' => $lease->id]) }}"
                                       class="btn btn-success">
                                        <i class="fas fa-file-invoice-dollar me-2"></i> Create Invoice
                                    </a>
                                @endif

                                @if(\Illuminate\Support\Facades\Route::has('leases.finance.edit'))
                                    <a href="{{ route('leases.finance.edit', $lease->id) }}"
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-edit me-2"></i> Update Lease
                                    </a>
                                @endif

                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#notesModal">
                                    <i class="fas fa-sticky-note me-2"></i> Add Note
                                </button>

                                @if($nextBillingDate && $lease->status == 'active' && \Illuminate\Support\Facades\Route::has('leases.mark-billed'))
                                <form action="{{ route('leases.mark-billed', $lease->id) }}" method="POST" class="d-grid">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success">
                                        <i class="fas fa-check-circle me-2"></i> Mark as Billed
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Related Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Last Billed</label>
                                <p class="mb-0">
                                    @if($lastBilledAt)
                                        {{ $lastBilledAt->format('M d, Y H:i') }}
                                        <small class="text-muted d-block">{{ $lastBilledAt->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Acceptance Certificate</label>
                                <p class="mb-0">
                                    @if($lease->acceptance_certificate_path)
                                        <a href="{{ asset('storage/' . $lease->acceptance_certificate_path) }}"
                                           target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-certificate"></i> View Certificate
                                        </a>
                                        @if($acceptanceCertificateGeneratedAt)
                                            <small class="text-muted d-block mt-1">
                                                Generated: {{ $acceptanceCertificateGeneratedAt->format('M d, Y') }}
                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted">Not generated</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Attachments</label>
                                <p class="mb-0">
                                    @if($lease->attachments)
                                        @php
                                            $attachments = json_decode($lease->attachments, true);
                                            $attachmentCount = is_array($attachments) ? count($attachments) : 0;
                                        @endphp
                                        <span class="badge bg-info">{{ $attachmentCount }} file(s)</span>
                                    @else
                                        <span class="text-muted">No attachments</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($lease->notes)
                    <div class="mt-3">
                        <label class="form-label text-muted small mb-1">Notes</label>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($lease->notes)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notes Modal -->
<div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            @if(\Illuminate\Support\Facades\Route::has('leases.add-note'))
            <form action="{{ route('leases.add-note', $lease->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="notesModalLabel">Add Note to Lease</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea class="form-control" id="note" name="note" rows="4"
                                  placeholder="Add a note about this lease..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Note</button>
                </div>
            </form>
            @else
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Note functionality is currently unavailable.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .timeline li:not(:last-child):after {
        content: '';
        position: absolute;
        left: 20px;
        top: 50px;
        bottom: -25px;
        width: 2px;
        background-color: #e9ecef;
    }
    .timeline li {
        position: relative;
        padding-left: 0;
    }
    .table th {
        font-size: 0.85rem;
        text-transform: uppercase;
    }
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 0.5rem;
    }
    .nav-tabs .nav-link.active {
        background-color: #f8f9fa;
        border-bottom-color: #f8f9fa;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-focus on note textarea when modal opens
        const notesModal = document.getElementById('notesModal');
        if (notesModal) {
            notesModal.addEventListener('shown.bs.modal', function() {
                const noteInput = document.getElementById('note');
                if (noteInput) {
                    noteInput.focus();
                }
            });
        }

        // Confirm before marking as billed
        const markBilledForm = document.querySelector('form[action*="mark-billed"]');
        if (markBilledForm) {
            markBilledForm.addEventListener('submit', function(e) {
                if (!confirm('Mark this lease as billed? This will update the next billing date based on the billing cycle.')) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endsection
