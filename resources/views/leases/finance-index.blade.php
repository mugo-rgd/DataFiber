@extends('layouts.app')

@section('title', 'Leases - Finance Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-file-contract"></i> Leases Management - Finance View</h4>
                    <div>

                        @if(\Illuminate\Support\Facades\Route::has('leases.export.finance'))
                            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                                <i class="fas fa-download"></i> Export
                            </button>
                        @else
                            {{-- <span class="badge bg-secondary" title="Export route not configured">
                                <i class="fas fa-download"></i> Export (Setup Required)
                            </span> --}}

                            <button class="badge bg-secondary" onclick="window.print()">
                                <i class="fas fa-print me-1"></i>Print Report
                            </button>
                        @endif
 <span class="badge bg-light text-dark me-2">Finance Access</span>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            @if(\Illuminate\Support\Facades\Route::has('leases.finance.index'))
                            <form method="GET" action="{{ route('leases.finance.index') }}" id="searchForm">
                            @else
                            <form method="GET" action="#" id="searchForm">
                            @endif
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <input type="text" name="search" class="form-control"
                                               placeholder="Search lease number, title..."
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <select name="status" class="form-select">
                                            <option value="">All Statuses</option>
                                            @foreach(['draft','pending','active','expired','terminated','cancelled'] as $status)
                                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                                    {{ ucfirst($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="service_type" class="form-select">
                                            <option value="">All Service Types</option>
                                            @foreach(['dark_fibre','wavelength','ethernet','ip_transit','colocation'] as $type)
                                                <option value="{{ $type }}" {{ request('service_type') == $type ? 'selected' : '' }}>
                                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="billing_cycle" class="form-select">
                                            <option value="">All Billing Cycles</option>
                                            @foreach(['monthly','quarterly','annually','one_time'] as $cycle)
                                                <option value="{{ $cycle }}" {{ request('billing_cycle') == $cycle ? 'selected' : '' }}>
                                                    {{ ucfirst($cycle) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="currency" class="form-select">
                                            <option value="">All Currencies</option>
                                            <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                            <option value="KSH" {{ request('currency') == 'KSH' ? 'selected' : '' }}>KSH</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="overdue"
                                                   id="overdue" value="1" {{ request('overdue') ? 'checked' : '' }}>
                                            <label class="form-check-label text-danger" for="overdue">
                                                <i class="fas fa-exclamation-triangle"></i> Show Overdue Billing
                                            </label>
                                        </div>
                                        @if(\Illuminate\Support\Facades\Route::has('leases.finance.index'))
                                        <a href="{{ route('leases.finance.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                            Clear Filters
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Summary Stats with FILTERED and OVERALL totals -->
                    @php
                        // FILTERED totals (current page/search results)
                        $filteredValueUSD = $leases->where('currency', 'USD')->sum('total_contract_value');
                        $filteredValueKSH = $leases->where('currency', 'KSH')->sum('total_contract_value');
                        $filteredMonthlyUSD = $leases->where('status', 'active')->where('currency', 'USD')->sum('monthly_cost');
                        $filteredMonthlyKSH = $leases->where('status', 'active')->where('currency', 'KSH')->sum('monthly_cost');
                        $filteredActiveUSD = $leases->where('status', 'active')->where('currency', 'USD')->count();
                        $filteredActiveKSH = $leases->where('status', 'active')->where('currency', 'KSH')->count();
                        $filteredLeasesUSD = $leases->where('currency', 'USD')->count();
                        $filteredLeasesKSH = $leases->where('currency', 'KSH')->count();

                        // OVERALL totals (entire database) - passed from controller
                        $overallValueUSD = $overallTotals['total_value_usd'] ?? 0;
                        $overallValueKSH = $overallTotals['total_value_ksh'] ?? 0;
                        $overallMonthlyUSD = $overallTotals['monthly_revenue_usd'] ?? 0;
                        $overallMonthlyKSH = $overallTotals['monthly_revenue_ksh'] ?? 0;
                        $overallActiveUSD = $overallTotals['active_leases_usd'] ?? 0;
                        $overallActiveKSH = $overallTotals['active_leases_ksh'] ?? 0;
                        $overallLeasesUSD = $overallTotals['total_leases_usd'] ?? 0;
                        $overallLeasesKSH = $overallTotals['total_leases_ksh'] ?? 0;
                        $overallInactiveUSD = $overallTotals['inactive_leases_usd'] ?? 0;
                        $overallInactiveKSH = $overallTotals['inactive_leases_ksh'] ?? 0;

                        // Status colors for badges
                        $statusColors = [
                            'draft' => 'secondary',
                            'pending' => 'warning',
                            'active' => 'success',
                            'expired' => 'info',
                            'terminated' => 'dark',
                            'cancelled' => 'danger'
                        ];
                    @endphp

                    <div class="row mb-4">
                        <!-- Total Contract Value Card -->
                        <div class="col-md-3">
                            <div class="card bg-light h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-muted mb-1">Total Contract Value</h6>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="badge bg-primary">USD</span>
                                            <h4 class="text-primary mb-0">${{ number_format($filteredValueUSD, 2) }}</h4>
                                            <small class="text-muted">Filtered</small>
                                        </div>
                                        <div>
                                            <span class="badge bg-warning text-dark">KSH</span>
                                            <h4 class="text-warning mb-0">{{ number_format($filteredValueKSH, 0) }}</h4>
                                            <small class="text-muted">Filtered</small>
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="text-muted small">
                                        <i class="fas fa-database me-1"></i> Overall:
                                        <span class="text-primary">${{ number_format($overallValueUSD, 2) }}</span>
                                        <span class="mx-1">|</span>
                                        <span class="text-warning">{{ number_format($overallValueKSH, 0) }} KSH</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Revenue Card -->
                        <div class="col-md-3">
                            <div class="card bg-light h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-muted mb-1">Monthly Revenue (Active)</h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">USD</small>
                                            <h5 class="text-success mb-0">${{ number_format($filteredMonthlyUSD, 2) }}</h5>
                                            <small class="text-muted">Filtered</small>
                                        </div>
                                        <div>
                                            <small class="text-muted">KSH</small>
                                            <h5 class="text-success mb-0">{{ number_format($filteredMonthlyKSH, 0) }}</h5>
                                            <small class="text-muted">Filtered</small>
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="text-muted small">
                                        <i class="fas fa-database me-1"></i> Overall:
                                        <span class="text-success">${{ number_format($overallMonthlyUSD, 2) }}</span>
                                        <span class="mx-1">|</span>
                                        <span class="text-success">{{ number_format($overallMonthlyKSH, 0) }} KSH</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Leases Card -->
                        <div class="col-md-3">
                            <div class="card bg-light h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-muted mb-1">Active Leases</h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-primary">{{ $filteredActiveUSD }}</span>
                                            <small class="text-muted">USD</small>
                                        </div>
                                        <div>
                                            <span class="badge bg-warning text-dark">{{ $filteredActiveKSH }}</span>
                                            <small class="text-muted">KSH</small>
                                        </div>
                                    </div>
                                    <h4 class="text-success mb-0 mt-2">{{ $filteredActiveUSD + $filteredActiveKSH }}</h4>
                                    <small class="text-muted">Filtered Active Leases</small>
                                    <hr class="my-2">
                                    <div class="text-muted small">
                                        <i class="fas fa-database me-1"></i> Overall:
                                        <span class="text-primary">{{ $overallActiveUSD }} USD</span>
                                        <span class="mx-1">|</span>
                                        <span class="text-warning">{{ $overallActiveKSH }} KSH</span>
                                        <span class="ms-1">({{ $overallActiveUSD + $overallActiveKSH }} total)</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Inactive Leases Card (NEW) -->
                        <div class="col-md-3">
                            <div class="card bg-light h-100 border-secondary">
                                <div class="card-body p-3">
                                    <h6 class="text-muted mb-1">Inactive Leases</h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-secondary">{{ $overallInactiveUSD }}</span>
                                            <small class="text-muted">USD</small>
                                        </div>
                                        <div>
                                            <span class="badge bg-secondary">{{ $overallInactiveKSH }}</span>
                                            <small class="text-muted">KSH</small>
                                        </div>
                                    </div>
                                    <h4 class="text-secondary mb-0 mt-2">
                                        {{ $overallInactiveUSD + $overallInactiveKSH }}
                                    </h4>
                                    <small class="text-muted">Overall Inactive Leases</small>
                                    <hr class="my-2">
                                    <div class="text-muted small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Statuses: pending, expired, terminated, cancelled
                                    </div>
                                    <div class="mt-1 small">
                                        @if(request('status') != 'active' && request('status') != '')
                                            <span class="badge bg-info">
                                                <i class="fas fa-filter"></i> Filtered: {{ $filteredLeasesUSD + $filteredLeasesKSH - ($filteredActiveUSD + $filteredActiveKSH) }} showing
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Second Row - Currency Distribution and Status Breakdown -->
                    <div class="row mb-4">
                        <!-- Currency Distribution Card -->
                        <div class="col-md-4">
                            <div class="card bg-light h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-muted mb-1">Currency Distribution</h6>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>USD Leases</span>
                                            <span class="fw-bold">{{ $filteredLeasesUSD }}</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            @php
                                                $totalFiltered = $filteredLeasesUSD + $filteredLeasesKSH;
                                                $usdWidth = $totalFiltered > 0 ? ($filteredLeasesUSD/$totalFiltered)*100 : 0;
                                            @endphp
                                            <div class="progress-bar bg-primary" style="width: {{ $usdWidth }}%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>KSH Leases</span>
                                            <span class="fw-bold">{{ $filteredLeasesKSH }}</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            @php
                                                $kshWidth = $totalFiltered > 0 ? ($filteredLeasesKSH/$totalFiltered)*100 : 0;
                                            @endphp
                                            <div class="progress-bar bg-warning" style="width: {{ $kshWidth }}%"></div>
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="text-muted small">
                                        <i class="fas fa-database me-1"></i> Overall:
                                        <span class="text-primary">{{ $overallLeasesUSD }} USD</span>
                                        <span class="mx-1">|</span>
                                        <span class="text-warning">{{ $overallLeasesKSH }} KSH</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Breakdown Card (NEW) -->
                        <div class="col-md-4">
                            <div class="card bg-light h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-muted mb-1">Status Breakdown (Overall)</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="badge bg-success">Active</span>
                                                <span class="fw-bold">{{ $overallActiveUSD + $overallActiveKSH }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="badge bg-warning">Pending</span>
                                                <span class="fw-bold">{{
                                                    ($overallTotals['pending_leases_usd'] ?? 0) +
                                                    ($overallTotals['pending_leases_ksh'] ?? 0)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="badge bg-info">Expired</span>
                                                <span class="fw-bold">{{
                                                    ($overallTotals['expired_leases_usd'] ?? 0) +
                                                    ($overallTotals['expired_leases_ksh'] ?? 0)
                                                }}</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="badge bg-dark">Terminated</span>
                                                <span class="fw-bold">{{
                                                    ($overallTotals['terminated_leases_usd'] ?? 0) +
                                                    ($overallTotals['terminated_leases_ksh'] ?? 0)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="badge bg-danger">Cancelled</span>
                                                <span class="fw-bold">{{
                                                    ($overallTotals['cancelled_leases_usd'] ?? 0) +
                                                    ($overallTotals['cancelled_leases_ksh'] ?? 0)
                                                }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="badge bg-secondary">Draft</span>
                                                <span class="fw-bold">{{
                                                    ($overallTotals['draft_leases_usd'] ?? 0) +
                                                    ($overallTotals['draft_leases_ksh'] ?? 0)
                                                }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="text-muted small">
                                        <i class="fas fa-chart-pie me-1"></i>
                                        Active: {{ round(($overallActiveUSD + $overallActiveKSH) / max(($overallLeasesUSD + $overallLeasesKSH), 1) * 100) }}% |
                                        Inactive: {{ round(($overallInactiveUSD + $overallInactiveKSH) / max(($overallLeasesUSD + $overallLeasesKSH), 1) * 100) }}%
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats Card -->
                        <div class="col-md-4">
                            <div class="card bg-light h-100">
                                <div class="card-body p-3">
                                    <h6 class="text-muted mb-1">Quick Stats</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><i class="fas fa-file-contract text-primary"></i> Total Leases:</span>
                                        <span class="fw-bold">{{ $overallLeasesUSD + $overallLeasesKSH }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><i class="fas fa-check-circle text-success"></i> Active:</span>
                                        <span class="fw-bold">{{ $overallActiveUSD + $overallActiveKSH }} ({{ round(($overallActiveUSD + $overallActiveKSH) / max(($overallLeasesUSD + $overallLeasesKSH), 1) * 100) }}%)</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><i class="fas fa-pause-circle text-secondary"></i> Inactive:</span>
                                        <span class="fw-bold">{{ $overallInactiveUSD + $overallInactiveKSH }} ({{ round(($overallInactiveUSD + $overallInactiveKSH) / max(($overallLeasesUSD + $overallLeasesKSH), 1) * 100) }}%)</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span><i class="fas fa-chart-line text-info"></i> Avg. Contract Value:</span>
                                        <span class="fw-bold">
                                            ${{ number_format(($overallValueUSD + ($overallValueKSH / 130)) / max(($overallLeasesUSD + $overallLeasesKSH), 1), 0) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Currency Tabs with FILTERED and OVERALL totals -->
                    <ul class="nav nav-tabs mb-3" id="currencyTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                                All Currencies <span class="badge bg-secondary ms-1">{{ $leases->total() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="usd-tab" data-bs-toggle="tab" data-bs-target="#usd" type="button" role="tab">
                                <i class="fas fa-dollar-sign"></i> USD <span class="badge bg-primary ms-1">{{ $filteredLeasesUSD }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ksh-tab" data-bs-toggle="tab" data-bs-target="#ksh" type="button" role="tab">
                                <i class="fas fa-shilling-sign"></i> KSH <span class="badge bg-warning text-dark ms-1">{{ $filteredLeasesKSH }}</span>
                            </button>
                        </li>

                        <!-- Totals Section - Both Filtered and Overall -->
                        <li class="nav-item ms-auto" role="presentation">
                            <div class="d-flex align-items-center gap-4 h-100 px-3">
                                <!-- Filtered Totals (Current Search) -->
                                <div class="text-end">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-search me-1"></i>FILTERED TOTAL
                                    </small>
                                    <div>
                                        <span class="fw-bold text-primary">${{ number_format($filteredValueUSD, 2) }}</span>
                                        <span class="mx-1 text-muted">|</span>
                                        <span class="fw-bold text-warning">{{ number_format($filteredValueKSH, 0) }} KSH</span>
                                    </div>
                                    <small class="text-muted">{{ $leases->total() }} leases</small>
                                </div>

                                <!-- Divider -->
                                <div class="border-end" style="height: 30px;"></div>

                                <!-- Overall Totals (All Leases) -->
                                <div class="text-end">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-database me-1"></i>OVERALL TOTAL
                                    </small>
                                    <div>
                                        <span class="fw-bold text-primary">${{ number_format($overallValueUSD, 2) }}</span>
                                        <span class="mx-1 text-muted">|</span>
                                        <span class="fw-bold text-warning">{{ number_format($overallValueKSH, 0) }} KSH</span>
                                    </div>
                                    <small class="text-muted">{{ $overallLeasesUSD + $overallLeasesKSH }} total leases</small>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <div class="tab-content" id="currencyTabContent">
                        <!-- All Currencies Tab -->
                        <div class="tab-pane fade show active" id="all" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Lease #</th>
                                            <th>Title</th>
                                            <th>Customer</th>
                                            <th>Service Type</th>
                                            <th class="text-end">Monthly Cost</th>
                                            <th class="text-end">Total Value</th>
                                            <th>Currency</th>
                                            <th>Billing Cycle</th>
                                            <th>Next Billing</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($leases as $lease)
                                            @php
                                                // Parse dates to Carbon instances with error handling
                                                $startDate = $lease->start_date ? \Carbon\Carbon::parse($lease->start_date) : null;
                                                $endDate = $lease->end_date ? \Carbon\Carbon::parse($lease->end_date) : null;
                                                $nextBillingDate = $lease->next_billing_date ? \Carbon\Carbon::parse($lease->next_billing_date) : null;
                                                $isOverdue = $nextBillingDate && $nextBillingDate < now();
                                                $currencyClass = $lease->currency == 'USD' ? 'text-primary fw-bold' : 'text-warning fw-bold';
                                                $currencySymbol = $lease->currency == 'USD' ? '$' : '';
                                                $currencyBadge = $lease->currency == 'USD' ? 'bg-primary' : 'bg-warning text-dark';
                                            @endphp
                                            <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                                <td>
                                                    <strong>{{ $lease->lease_number }}</strong>
                                                    <div class="text-muted small">
                                                        @if($startDate && $endDate)
                                                            {{ $startDate->format('Y-m-d') }} to {{ $endDate->format('Y-m-d') }}
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ Str::limit($lease->title, 30) }}
                                                    @if($lease->distance_km)
                                                        <div class="text-muted small">{{ $lease->distance_km }} km</div>
                                                    @endif
                                                </td>
                                                <td>{{ $lease->customer->name ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ str_replace('_', ' ', $lease->service_type) }}
                                                    </span>
                                                    @if($lease->bandwidth)
                                                        <div class="text-muted small">{{ $lease->bandwidth }}</div>
                                                    @endif
                                                </td>
                                                <td class="text-end {{ $currencyClass }}">
                                                    <strong>{{ $currencySymbol }}{{ number_format($lease->monthly_cost, 2) }}</strong>
                                                    <div class="text-muted small">{{ $lease->currency }}</div>
                                                </td>
                                                <td class="text-end {{ $currencyClass }}">
                                                    <strong>{{ $currencySymbol }}{{ number_format($lease->total_contract_value, 2) }}</strong>
                                                    <div class="text-muted small">{{ $lease->currency }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $currencyBadge }}">
                                                        {{ $lease->currency }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        {{ $lease->billing_cycle }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($nextBillingDate)
                                                        <span class="{{ $isOverdue ? 'text-danger fw-bold' : ($nextBillingDate < now()->addDays(7) ? 'text-warning' : '') }}">
                                                            {{ $nextBillingDate->format('Y-m-d') }}
                                                        </span>
                                                        @if($isOverdue)
                                                            <div class="text-danger small">Overdue</div>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $statusColors[$lease->status] ?? 'secondary' }}">
                                                        {{ ucfirst($lease->status) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        @if(\Illuminate\Support\Facades\Route::has('leases.finance.show'))
                                                            <a href="{{ route('leases.finance.show', $lease->id) }}"
                                                               class="btn btn-outline-primary" title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @endif

                                                        @if(\Illuminate\Support\Facades\Route::has('invoices.create'))
                                                            <a href="{{ route('invoices.create', ['lease_id' => $lease->id]) }}"
                                                               class="btn btn-outline-success" title="Create Invoice">
                                                                <i class="fas fa-file-invoice-dollar"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                                        <p>No leases found matching your criteria.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <td colspan="4" class="text-end"><strong>Filtered Page Totals:</strong></td>
                                            <td class="text-end">
                                                <div><strong>USD: ${{ number_format($filteredMonthlyUSD, 2) }}</strong></div>
                                                <div><strong>KSH: {{ number_format($filteredMonthlyKSH, 0) }}</strong></div>
                                            </td>
                                            <td class="text-end">
                                                <div><strong>USD: ${{ number_format($filteredValueUSD, 2) }}</strong></div>
                                                <div><strong>KSH: {{ number_format($filteredValueKSH, 0) }}</strong></div>
                                            </td>
                                            <td colspan="5">
                                                <small class="text-muted">
                                                    <i class="fas fa-database me-1"></i> Overall: ${{ number_format($overallValueUSD, 2) }} USD | {{ number_format($overallValueKSH, 0) }} KSH
                                                    | Active: {{ $overallActiveUSD + $overallActiveKSH }} | Inactive: {{ $overallInactiveUSD + $overallInactiveKSH }}
                                                </small>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- USD Tab -->
                        <div class="tab-pane fade" id="usd" role="tabpanel">
                            @php
                                $usdLeases = $leases->where('currency', 'USD');
                            @endphp
                            @if($usdLeases->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>Lease #</th>
                                                <th>Title</th>
                                                <th>Customer</th>
                                                <th>Monthly Cost (USD)</th>
                                                <th class="text-end">Total Value (USD)</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($usdLeases as $lease)
                                            <tr>
                                                <td>{{ $lease->lease_number }}</td>
                                                <td>{{ Str::limit($lease->title, 40) }}</td>
                                                <td>{{ $lease->customer->name ?? 'N/A' }}</td>
                                                <td class="text-primary fw-bold">${{ number_format($lease->monthly_cost, 2) }}</td>
                                                <td class="text-end text-primary fw-bold">${{ number_format($lease->total_contract_value, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $statusColors[$lease->status] ?? 'secondary' }}">
                                                        {{ ucfirst($lease->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if(\Illuminate\Support\Facades\Route::has('leases.finance.show'))
                                                        <a href="{{ route('leases.finance.show', $lease->id) }}"
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-light">
                                                <td colspan="3" class="text-end"><strong>USD Totals (Filtered):</strong></td>
                                                <td class="text-primary fw-bold">${{ number_format($usdLeases->sum('monthly_cost'), 2) }}</td>
                                                <td class="text-end text-primary fw-bold">${{ number_format($usdLeases->sum('total_contract_value'), 2) }}</td>
                                                <td colspan="2">
                                                    <small class="text-muted">
                                                        Overall: ${{ number_format($overallValueUSD, 2) }} | Active: {{ $overallActiveUSD }} | Inactive: {{ $overallInactiveUSD }}
                                                    </small>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No USD leases found matching your criteria.
                                    <small class="d-block mt-1 text-muted">Overall: {{ $overallLeasesUSD }} USD leases ({{ $overallActiveUSD }} active, {{ $overallInactiveUSD }} inactive)</small>
                                </div>
                            @endif
                        </div>

                        <!-- KSH Tab -->
                        <div class="tab-pane fade" id="ksh" role="tabpanel">
                            @php
                                $kshLeases = $leases->where('currency', 'KSH');
                            @endphp
                            @if($kshLeases->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-warning">
                                            <tr>
                                                <th>Lease #</th>
                                                <th>Title</th>
                                                <th>Customer</th>
                                                <th>Monthly Cost (KSH)</th>
                                                <th class="text-end">Total Value (KSH)</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($kshLeases as $lease)
                                            <tr>
                                                <td>{{ $lease->lease_number }}</td>
                                                <td>{{ Str::limit($lease->title, 40) }}</td>
                                                <td>{{ $lease->customer->name ?? 'N/A' }}</td>
                                                <td class="text-warning fw-bold">{{ number_format($lease->monthly_cost, 0) }}</td>
                                                <td class="text-end text-warning fw-bold">{{ number_format($lease->total_contract_value, 0) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $statusColors[$lease->status] ?? 'secondary' }}">
                                                        {{ ucfirst($lease->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if(\Illuminate\Support\Facades\Route::has('leases.finance.show'))
                                                        <a href="{{ route('leases.finance.show', $lease->id) }}"
                                                           class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-light">
                                                <td colspan="3" class="text-end"><strong>KSH Totals (Filtered):</strong></td>
                                                <td class="text-warning fw-bold">{{ number_format($kshLeases->sum('monthly_cost'), 0) }}</td>
                                                <td class="text-end text-warning fw-bold">{{ number_format($kshLeases->sum('total_contract_value'), 0) }}</td>
                                                <td colspan="2">
                                                    <small class="text-muted">
                                                        Overall: {{ number_format($overallValueKSH, 0) }} KSH | Active: {{ $overallActiveKSH }} | Inactive: {{ $overallInactiveKSH }}
                                                    </small>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No KSH leases found matching your criteria.
                                    <small class="d-block mt-1 text-muted">Overall: {{ $overallLeasesKSH }} KSH leases ({{ $overallActiveKSH }} active, {{ $overallInactiveKSH }} inactive)</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Pagination -->
                    @if($leases->hasPages())
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted">
                                        Showing {{ $leases->firstItem() }} to {{ $leases->lastItem() }} of {{ $leases->total() }} entries
                                        <span class="mx-2">|</span>
                                        <span class="text-primary">
                                            <i class="fas fa-database"></i> Overall: {{ $overallLeasesUSD + $overallLeasesKSH }} total leases
                                            ({{ $overallActiveUSD + $overallActiveKSH }} active, {{ $overallInactiveUSD + $overallInactiveKSH }} inactive)
                                        </span>
                                    </div>
                                    {{ $leases->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(\Illuminate\Support\Facades\Route::has('leases.export.finance'))
<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('leases.export.finance') }}" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Export Leases Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Format</label>
                        <select class="form-select" name="format">
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Currency</label>
                        <select class="form-select" name="export_currency">
                            <option value="all">All Currencies</option>
                            <option value="USD">USD Only</option>
                            <option value="KSH">KSH Only</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="export_status">
                            <option value="all">All Statuses</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Inactive (All Non-Active)</option>
                            @foreach(['pending','expired','terminated','cancelled','draft'] as $status)
                                <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@section('styles')
<style>
    .table th {
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
    }
    .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    .card-header {
        padding: 1rem 1.25rem;
    }
    .nav-tabs .nav-link.active {
        background-color: #f8f9fa;
        border-bottom-color: #f8f9fa;
    }
    .progress {
        background-color: #e9ecef;
    }
    .nav-tabs .nav-link .badge {
        font-size: 0.7rem;
    }
    .border-secondary {
        border-left: 3px solid #6c757d !important;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit form on filter change
        document.querySelectorAll('#searchForm select').forEach(select => {
            select.addEventListener('change', function() {
                document.getElementById('searchForm').submit();
            });
        });

        // Handle tab switching with URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const currency = urlParams.get('currency');

        if (currency === 'USD') {
            const usdTab = new bootstrap.Tab(document.getElementById('usd-tab'));
            usdTab.show();
        } else if (currency === 'KSH') {
            const kshTab = new bootstrap.Tab(document.getElementById('ksh-tab'));
            kshTab.show();
        }

        // Update URL when tab changes
        document.querySelectorAll('#currencyTab button').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function (e) {
                const target = e.target.id;
                const params = new URLSearchParams(window.location.search);

                if (target === 'usd-tab') {
                    params.set('currency', 'USD');
                } else if (target === 'ksh-tab') {
                    params.set('currency', 'KSH');
                } else {
                    params.delete('currency');
                }

                window.history.replaceState({}, '', `${window.location.pathname}?${params}`);
            });
        });
    });
</script>
@endsection
