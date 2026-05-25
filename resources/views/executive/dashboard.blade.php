@extends('layouts.app')

@section('title', 'Executive Dashboard - Dark Fibre CRM')

@section('content')

@if(session('success'))
    <div class="alert alert-success rounded-4 border-0 shadow-sm">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
    </div>
@endif

<div class="container-fluid px-0">

<div class="dropdown d-inline-block">
    <button class="btn btn-kp-primary rounded-pill px-4 py-2 dropdown-toggle px-md-5 mt-4"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            style="background: linear-gradient(135deg, #0066B3, #009639); border: none;">
        <i class="fas fa-map-marked-alt me-2"></i>
        GIS Maps
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
        <li>
            <a class="dropdown-item" href="{{ route('executive.dashboard.gis') }}">
                <i class="fas fa-map me-2 text-kp-blue"></i> Fibre Network Map
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('executive.dashboard.gis') }}?layer=leases">
                <i class="fas fa-network-wired me-2 text-kp-green"></i> Active Leases
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('executive.dashboard.gis') }}?layer=capacity">
                <i class="fas fa-chart-line me-2 text-warning"></i> Capacity Heatmap
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="{{ route('executive.dashboard.gis') }}?export=true">
                <i class="fas fa-download me-2 text-success"></i> Export Map Data
            </a>
        </li>
    </ul>
</div>

    {{-- Action Buttons Bar --}}
    {{-- <div class="container-fluid px-3 px-sm-4 px-md-5 mb-4"> --}}
        <div class="container-fluid px-3 px-sm-4 px-md-5 mt-4">
        <div class="d-flex gap-2 justify-content-lg-end flex-wrap align-items-center">
    <form method="GET" action="{{ route('executive.dashboard') }}" class="d-flex gap-2">
        <div class="input-group" style="max-width: 280px;">
            <span class="input-group-text bg-white border-end-0">
                <i class="fas fa-calendar-alt text-kp-blue"></i>
            </span>
            <input type="date" name="date" class="form-control border-start-0"
                   value="{{ request('date') }}" style="border-left: none;">
        </div>
        <button class="btn btn-kp-primary rounded-pill btn-compact">
            <i class="fas fa-filter me-1"></i>Filter
        </button>
    </form>

    <button onclick="window.print()" class="btn btn-light rounded-pill border btn-compact">
        <i class="fas fa-print me-1"></i>Print
    </button>

    <a href="{{ route('executive.dashboard') }}?date={{ request('date') }}&export=pdf"
       class="btn btn-danger rounded-pill btn-compact">
        <i class="fas fa-file-pdf me-1"></i>PDF
    </a>

    <a href="{{ route('executive.dashboard') }}?date={{ request('date') }}&export=excel"
       class="btn btn-success rounded-pill btn-compact">
        <i class="fas fa-file-excel me-1"></i>Excel
    </a>

    <a href="{{ route('executive.dashboard') }}?date={{ request('date') }}&export=csv"
       class="btn btn-secondary rounded-pill btn-compact">
        <i class="fas fa-file-csv me-1"></i>CSV
    </a>

    <form method="POST" action="{{ route('executive.dashboard.refresh') }}">
        @csrf
        <input type="hidden" name="date" value="{{ request('date') ?: now()->toDateString() }}">
        <button class="btn btn-warning rounded-pill btn-compact">
            <i class="fas fa-sync-alt me-1"></i>Refresh
        </button>
    </form>
</div>
    </div>

    <div class="container-fluid px-3 px-sm-4 px-md-5 py-4">

        @if(!$kpis)
            <div class="alert alert-warning rounded-4 border-0 shadow-sm">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ $message }}
            </div>
        @else

            {{-- KPI Cards Row 1 (Always Visible) --}}
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="kpi-card kpi-card-primary rounded-4 p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-uppercase fw-semibold text-white-70">Revenue</small>
                                <div class="kpi-value fw-bold mt-2">
                                    <div>KSH {{ number_format($kpis->revenue_ksh, 2) }}</div>
                                    <div class="mt-1">USD {{ number_format($kpis->revenue_usd, 2) }}</div>
                                </div>
                            </div>
                            <div class="kpi-icon bg-white-20 rounded-3">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="kpi-card kpi-card-danger rounded-4 p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-uppercase fw-semibold text-white-70">Accounts Receivable</small>
                                <div class="kpi-value fw-bold mt-2">
                                    <div>KSH {{ number_format($kpis->accounts_receivable_ksh, 2) }}</div>
                                    <div class="mt-1">USD {{ number_format($kpis->accounts_receivable_usd, 2) }}</div>
                                </div>
                            </div>
                            <div class="kpi-icon bg-white-20 rounded-3">
                                <i class="fas fa-file-invoice fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="kpi-card kpi-card-success rounded-4 p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-uppercase fw-semibold text-white-70">Active Leases</small>
                                <div class="kpi-value-large fw-bold mt-2">
                                    {{ number_format($kpis->active_leases) }}
                                </div>
                            </div>
                            <div class="kpi-icon bg-white-20 rounded-3">
                                <i class="fas fa-network-wired fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="kpi-card kpi-card-info rounded-4 p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-uppercase fw-semibold text-white-70">Active Contracts</small>
                                <div class="kpi-value-large fw-bold mt-2">
                                    {{-- {{ number_format($kpis->active_contracts) }} --}}
                                    {{ $contracts->sum('contract_count') }}
                                </div>
                            </div>
                            <div class="kpi-icon bg-white-20 rounded-3">
                                <i class="fas fa-file-contract fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI Cards Row 2 (Always Visible) --}}
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <small class="text-muted text-uppercase fw-semibold">Quotation Pipeline</small>
                            <div class="mt-2">
                                <div class="fw-bold text-kp-blue">KSH {{ number_format($kpis->quotation_pipeline_ksh, 2) }}</div>
                                <div class="fw-bold text-kp-green mt-1">USD {{ number_format($kpis->quotation_pipeline_usd, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <small class="text-muted text-uppercase fw-semibold">Overdue Debt</small>
                            <div class="mt-2">
                                <div class="fw-bold text-danger">KSH {{ number_format($kpis->overdue_ksh, 2) }}</div>
                                <div class="fw-bold text-danger mt-1">USD {{ number_format($kpis->overdue_usd, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <small class="text-muted text-uppercase fw-semibold">Core Utilization</small>
                            <div class="mt-2">
                                <div class="display-6 fw-bold text-kp-blue">{{ number_format($kpis->core_utilization_percent, 2) }}%</div>
                                <small class="text-muted">{{ $kpis->used_cores }} used / {{ $kpis->total_cores }} total</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <small class="text-muted text-uppercase fw-semibold">Network Availability</small>
                            <div class="mt-2">
                                <div class="display-6 fw-bold text-kp-green">{{ number_format($kpis->network_availability_percent, 2) }}%</div>
                                <small class="text-muted">SLA {{ number_format($kpis->sla_compliance_percent, 2) }}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- revenue forcast --}}
            <button class="section-toggle btn w-100 text-start mb-3 rounded-4 shadow-sm"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#forecastSection"
        aria-expanded="true">
    <i class="fas fa-chevron-down me-2"></i>
    Revenue Forecast
</button>

<div class="collapse show" id="forecastSection">
    <div class="row g-4 mb-5">
        @forelse($revenueForecasts as $forecast)
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase fw-semibold">
                            {{ $forecast->currency }} Revenue Forecast
                        </small>

                        <h3 class="fw-bold text-kp-blue mt-2">
                            {{ $forecast->currency }}
                            {{ number_format($forecast->forecast_revenue, 2) }}
                        </h3>

                        <p class="mb-1">
                            <strong>Actual:</strong>
                            {{ $forecast->currency }}
                            {{ number_format($forecast->actual_revenue, 2) }}
                        </p>

                        <p class="mb-1">
                            <strong>Growth Rate:</strong>
                            <span class="{{ $forecast->growth_rate_percent >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($forecast->growth_rate_percent, 2) }}%
                            </span>
                        </p>

                        <small class="text-muted">
                            Method: {{ strtoupper(str_replace('_', ' ', $forecast->forecast_method)) }}
                        </small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info rounded-4 border-0 shadow-sm">
                    No revenue forecast available. Run:
                    <strong>php artisan reports:generate-executive --date={{ $snapshotDate }}</strong>
                </div>
            </div>
        @endforelse
    </div>
    <div class="row g-4 mb-5">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-chart-area text-kp-blue me-2"></i>
                    Revenue Forecast Comparison
                </h5>
            </div>

            <div class="card-body">
                <canvas id="revenueForecastChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
</div>

            {{-- insights --}}
            @if(isset($insightDate) && $insightDate)
    <div class="alert alert-info rounded-4 border-0 shadow-sm mb-3">
        <i class="fas fa-lightbulb me-2"></i>
        Showing executive insights for:
        <strong>{{ \Carbon\Carbon::parse($insightDate)->format('Y-m-d') }}</strong>
    </div>
@endif
            <button class="section-toggle btn w-100 text-start mb-3 rounded-4 shadow-sm"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#insightsSection"
        aria-expanded="true">
    <i class="fas fa-chevron-down me-2"></i>
    Executive AI Insights
</button>

<div class="collapse show" id="insightsSection">
    <div class="row g-4 mb-5">
        @forelse($insights as $insight)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <span class="badge bg-{{
                            $insight->severity === 'critical'
                                ? 'danger'
                                : ($insight->severity === 'warning' ? 'warning' : 'info')
                        }} rounded-pill mb-2">
                            {{ strtoupper($insight->severity) }}
                        </span>

                        <h5 class="fw-bold">{{ $insight->title }}</h5>
                        <p class="text-muted mb-1">{{ $insight->message }}</p>

                        @if($insight->value)
                            <strong>
                                {{ $insight->currency ? $insight->currency . ' ' : '' }}
                                {{ number_format($insight->value, 2) }}
                            </strong>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-success rounded-4 border-0 shadow-sm">
                    No critical insights detected for this snapshot.
                </div>
            </div>
        @endforelse
    </div>
</div>

            {{-- Expandable/Collapsible Sections --}}
            <div class="accordion" id="executiveAccordion">

                {{-- Section 1: Debt Management --}}
                <div class="accordion-item border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <h2 class="accordion-header" id="headingDebt">
                        <button class="accordion-button bg-kp-blue-light fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDebt" aria-expanded="true" aria-controls="collapseDebt">
                            <i class="fas fa-chart-pie text-kp-blue me-2"></i>
                            Debt Management
                            <span class="badge bg-danger ms-2">{{ $debtAging->where('overdue_count', '>', 0)->count() }} Overdue</span>
                        </button>
                    </h2>
                    <div id="collapseDebt" class="accordion-collapse collapse show" data-bs-parent="#executiveAccordion">
                        <div class="accordion-body p-4">
                            {{-- Charts --}}
                            <div class="row g-4 mb-4">
    <div class="col-md-6 d-flex">
        <div class="card border-0 shadow-sm rounded-4 w-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Debt Aging Summary</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div class="chart-wrapper">
                    <canvas id="debtAgingChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 d-flex">
        <div class="card border-0 shadow-sm rounded-4 w-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Revenue Summary</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div class="chart-wrapper">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

                            {{-- Debt Summary Cards --}}
                            {{-- <div class="row g-4 mb-4"> --}}
                            <div class="row g-4 mb-4 mt-3">
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small class="text-muted text-uppercase fw-semibold">Total Debt KSH</small>
                                            <h4 class="fw-bold text-danger mt-2">
                                                {{ number_format($debtAging->where('currency', 'KSH')->sum('total_outstanding'), 2) }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small class="text-muted text-uppercase fw-semibold">Total Debt USD</small>
                                            <h4 class="fw-bold text-danger mt-2">
                                                {{ number_format($debtAging->where('currency', 'USD')->sum('total_outstanding'), 2) }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small class="text-muted text-uppercase fw-semibold">Customers With Debt</small>
                                            <h3 class="fw-bold mt-2">{{ number_format($debtAging->count()) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small class="text-muted text-uppercase fw-semibold">Overdue Accounts</small>
                                            <h3 class="fw-bold text-warning mt-2">
                                                {{ number_format($debtAging->where('overdue_count', '>', 0)->count()) }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Debt Buckets --}}
                            <div class="row g-4 mb-4">
                                @foreach([
                                    'Current' => 'current_amount',
                                    '1-30 Days' => 'days_1_30',
                                    '31-60 Days' => 'days_31_60',
                                    '61-90 Days' => 'days_61_90',
                                    '91-120 Days' => 'days_91_120',
                                    '120+ Days' => 'days_120_plus',
                                ] as $label => $field)
                                    <div class="col-md-2">
                                        <div class="card border-0 shadow-sm rounded-4">
                                            <div class="card-body">
                                                <small class="text-muted">{{ $label }}</small>
                                                <h5 class="{{ in_array($field, ['days_91_120']) ? 'text-warning' : ($field == 'days_120_plus' ? 'text-danger' : '') }}">
                                                    {{ number_format($debtAging->sum($field), 2) }}
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @include('executive.partials.table-debt-aging', ['debtAging' => $debtAging])
                        </div>
                    </div>
                </div>

                {{-- Section 2: Revenue & Customers --}}
                <div class="accordion-item border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <h2 class="accordion-header" id="headingRevenue">
                        <button class="accordion-button collapsed bg-kp-green-light fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRevenue" aria-expanded="false" aria-controls="collapseRevenue">
                            <i class="fas fa-chart-line text-kp-green me-2"></i>
                            Revenue & Customers
                        </button>
                    </h2>
                    <div id="collapseRevenue" class="accordion-collapse collapse" data-bs-parent="#executiveAccordion">
                        <div class="accordion-body p-4">
                            {{-- Revenue Summary Cards --}}
                            <div class="row g-4 mb-4">
                                <div class="col-md-3">
                                    <div class="card shadow-sm rounded-4 border-0">
                                        <div class="card-body">
                                            <small>Total Revenue KSH</small>
                                            <h3>{{ number_format($summary['revenue_ksh'] ?? 0, 2) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card shadow-sm rounded-4 border-0">
                                        <div class="card-body">
                                            <small>Total Revenue USD</small>
                                            <h3>{{ number_format($summary['revenue_usd'] ?? 0, 2) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card shadow-sm rounded-4 border-0">
                                        <div class="card-body">
                                            <small>Total Paid KSH</small>
                                            <h3>{{ number_format($summary['paid_ksh'] ?? 0, 2) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card shadow-sm rounded-4 border-0">
                                        <div class="card-body">
                                            <small>Outstanding KSH</small>
                                            <h3 class="text-danger">{{ number_format($summary['outstanding_ksh'] ?? 0, 2) }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @include('executive.partials.table-revenue', ['revenue' => $revenue])

                            {{-- Top Customers Summary --}}
                            <div class="row g-4 mb-4 mt-4">
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Top Customers</small>
                                            <h3>{{ $summary['top_customer_count'] ?? $topCustomers->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Highest Revenue Customer</small>
                                            <h4>{{ optional(optional($topCustomers->first())->customer)->name ?? 'N/A' }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Total Contribution</small>
                                            <h4>{{ number_format($topCustomers->sum('revenue_contribution_percent'), 2) }}%</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @include('executive.partials.table-top-customers', ['topCustomers' => $topCustomers])
                        </div>
                    </div>
                </div>

                {{-- Section 3: Quotation Pipeline --}}
                <div class="accordion-item border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <h2 class="accordion-header" id="headingQuotation">
                        <button class="accordion-button collapsed bg-info-light fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQuotation" aria-expanded="false" aria-controls="collapseQuotation">
                            <i class="fas fa-file-invoice-dollar text-info me-2"></i>
                            Quotation Pipeline
                        </button>
                    </h2>
                    <div id="collapseQuotation" class="accordion-collapse collapse" data-bs-parent="#executiveAccordion">
                        <div class="accordion-body p-4">
                            {{-- Charts --}}
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                                            <h5 class="fw-bold mb-0">Quotation Pipeline Chart</h5>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="quotationChart" height="140"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                                            <h5 class="fw-bold mb-0">Fibre Utilization</h5>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="fiberUtilizationChart" height="140"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Quotation Summary Cards --}}
                            <div class="row g-4 mb-4">
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small class="text-muted text-uppercase fw-semibold">Total Quotations</small>
                                            <h3 class="fw-bold mt-2">{{ number_format($quotations->sum('quotation_count')) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small class="text-muted text-uppercase fw-semibold">Pipeline Value</small>
                                            <h5 class="fw-bold mt-2">KSH {{ number_format($quotations->where('currency','KSH')->sum('pipeline_value'), 2) }}</h5>
                                            <h5 class="fw-bold">USD {{ number_format($quotations->where('currency','USD')->sum('pipeline_value'), 2) }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small class="text-muted text-uppercase fw-semibold">Won Value</small>
                                            <h5 class="fw-bold text-success mt-2">KSH {{ number_format($quotations->where('currency','KSH')->sum('won_value'), 2) }}</h5>
                                            <h5 class="fw-bold text-success">USD {{ number_format($quotations->where('currency','USD')->sum('won_value'), 2) }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small class="text-muted text-uppercase fw-semibold">Average Conversion</small>
                                            <h3 class="fw-bold text-primary mt-2">{{ number_format($quotations->avg('conversion_rate_percent'), 2) }}%</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @include('executive.partials.table-quotations', ['quotations' => $quotations])
                        </div>
                    </div>
                </div>

                {{-- Section 4: Contracts --}}
                <div class="accordion-item border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <h2 class="accordion-header" id="headingContracts">
                        <button class="accordion-button collapsed bg-purple-light fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContracts" aria-expanded="false" aria-controls="collapseContracts">
                            <i class="fas fa-file-contract text-purple me-2"></i>
                            Contracts
                        </button>
                    </h2>
                    <div id="collapseContracts" class="accordion-collapse collapse" data-bs-parent="#executiveAccordion">
                        <div class="accordion-body p-4">
                            {{-- Contracts Summary Cards --}}
                            <div class="row g-4 mb-4">
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Total Contracts</small>
                                            <h3>{{ $contracts->sum('contract_count') }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Contract Value</small>
                                            <h5>KSH {{ number_format($contracts->where('currency','KSH')->sum('contract_value'), 2) }}</h5>
                                            <h5>USD {{ number_format($contracts->where('currency','USD')->sum('contract_value'), 2) }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Expiring 30 Days</small>
                                            <h3 class="text-warning">{{ $contracts->sum('expiring_30_days') }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Expiring 90 Days</small>
                                            <h3 class="text-danger">{{ $contracts->sum('expiring_90_days') }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Revenue At Risk</small>
                                            <h5 class="text-danger">{{ number_format($contracts->sum('renewal_revenue_at_risk'), 2) }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @include('executive.partials.table-contracts', ['contracts' => $contracts])
                        </div>
                    </div>
                </div>

                {{-- Section 5: Leases --}}
                <div class="accordion-item border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <h2 class="accordion-header" id="headingLeases">
                        <button class="accordion-button collapsed bg-kp-blue-light fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLeases" aria-expanded="false" aria-controls="collapseLeases">
                            <i class="fas fa-network-wired text-kp-blue me-2"></i>
                            Leases
                        </button>
                    </h2>
                    <div id="collapseLeases" class="accordion-collapse collapse" data-bs-parent="#executiveAccordion">
                        <div class="accordion-body p-4">
                            {{-- Leases Summary Cards --}}
                            <div class="row g-4 mb-4">
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Total Leases</small>
                                            <h3>{{ number_format($leases->sum('lease_count')) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Monthly Revenue</small>
                                            <h5>KSH {{ number_format($leases->where('currency', 'KSH')->sum('monthly_revenue'), 2) }}</h5>
                                            <h5>USD {{ number_format($leases->where('currency', 'USD')->sum('monthly_revenue'), 2) }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Leased Distance</small>
                                            <h3>{{ number_format($leases->sum('leased_distance_km'), 2) }} KM</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Leased Cores</small>
                                            <h3>{{ number_format($leases->sum('leased_cores')) }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @include('executive.partials.table-leases', ['leases' => $leases])
                        </div>
                    </div>
                </div>

                {{-- Section 6: Fibre Infrastructure --}}
                <div class="accordion-item border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <h2 class="accordion-header" id="headingFibre">
                        <button class="accordion-button collapsed bg-kp-green-light fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFibre" aria-expanded="false" aria-controls="collapseFibre">
                            <i class="fas fa-chart-bar text-kp-green me-2"></i>
                            Fibre Infrastructure
                        </button>
                    </h2>
                    <div id="collapseFibre" class="accordion-collapse collapse" data-bs-parent="#executiveAccordion">
                        <div class="accordion-body p-4">
                            {{-- Fibre Summary Cards --}}
                            <div class="row g-4 mb-4">
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Network Segments</small>
                                            <h3>{{ $fiberUtilization->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Total Fibre Length</small>
                                            <h3>{{ number_format($fiberUtilization->sum('total_fibre_km'), 2) }} KM</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Total Cores</small>
                                            <h3>{{ number_format($fiberUtilization->sum('total_cores')) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Used Cores</small>
                                            <h3 class="text-warning">{{ number_format($fiberUtilization->sum('used_cores')) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Avg Utilization</small>
                                            <h3 class="text-primary">{{ number_format($fiberUtilization->avg('utilization_percent'), 2) }}%</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @include('executive.partials.table-fiber-utilization', ['fiberUtilization' => $fiberUtilization])
                        </div>
                    </div>
                </div>

                {{-- Section 7: SLA & Network Performance --}}
                <div class="accordion-item border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <h2 class="accordion-header" id="headingSLA">
                        <button class="accordion-button collapsed bg-danger-light fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSLA" aria-expanded="false" aria-controls="collapseSLA">
                            <i class="fas fa-shield-alt text-danger me-2"></i>
                            SLA & Network Performance
                        </button>
                    </h2>
                    <div id="collapseSLA" class="accordion-collapse collapse" data-bs-parent="#executiveAccordion">
                        <div class="accordion-body p-4">
                            {{-- SLA Summary Cards --}}
                            <div class="row g-4 mb-4">
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Total Incidents</small>
                                            <h3>{{ number_format($slaNetwork->sum('total_incidents')) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Open Incidents</small>
                                            <h3 class="text-warning">{{ number_format($slaNetwork->sum('open_incidents')) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Resolved</small>
                                            <h3 class="text-success">{{ number_format($slaNetwork->sum('resolved_incidents')) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>SLA Breaches</small>
                                            <h3 class="text-danger">{{ number_format($slaNetwork->sum('sla_breaches')) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <small>Avg Uptime</small>
                                            <h3 class="text-primary">{{ number_format($slaNetwork->avg('uptime_percent'), 2) }}%</h3>
                                            <small class="text-muted">
                                                Target: {{ number_format($slaNetwork->avg('sla_target_percent'), 2) }}%
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @include('executive.partials.table-sla-network', ['slaNetwork' => $slaNetwork])
                        </div>
                    </div>
                </div>

            </div>

        @endif
    </div>
</div>

@if($kpis)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Preserve accordion state in localStorage
    const accordionButtons = document.querySelectorAll('.accordion-button');
    const accordionItems = document.querySelectorAll('.accordion-collapse');

    // Load saved state
    accordionItems.forEach((item, index) => {
        const savedState = localStorage.getItem(`accordion_${item.id}`);
        if (savedState === 'show') {
            item.classList.add('show');
            const button = accordionButtons[index];
            if (button) button.classList.remove('collapsed');
        }
    });

    // Save state on change
    accordionItems.forEach((item, index) => {
        item.addEventListener('shown.bs.collapse', function () {
            localStorage.setItem(`accordion_${this.id}`, 'show');
        });
        item.addEventListener('hidden.bs.collapse', function () {
            localStorage.setItem(`accordion_${this.id}`, 'hide');
        });
    });

    // Initialize Charts
    const debtCtx = document.getElementById('debtAgingChart');
    const revenueCtx = document.getElementById('revenueChart');
    const quotationCtx = document.getElementById('quotationChart');
    const fiberCtx = document.getElementById('fiberUtilizationChart');
    const revenueForecastCtx = document.getElementById('revenueForecastChart');

    if (debtCtx) {
        new Chart(debtCtx, {
            type: 'bar',
            data: {
                labels: ['Current', '1-30', '31-60', '61-90', '91-120', '120+'],
                datasets: [{
                    label: 'Debt Aging (KSH)',
                    data: [
                        {{ (float) $debtAging->sum('current_amount') }},
                        {{ (float) $debtAging->sum('days_1_30') }},
                        {{ (float) $debtAging->sum('days_31_60') }},
                        {{ (float) $debtAging->sum('days_61_90') }},
                        {{ (float) $debtAging->sum('days_91_120') }},
                        {{ (float) $debtAging->sum('days_120_plus') }}
                    ],
                    backgroundColor: 'rgba(0, 102, 179, 0.8)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    }

    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'doughnut',
            data: {
                labels: ['KSH Revenue', 'USD Revenue'],
                datasets: [{
                    data: [
                        {{ (float) ($summary['revenue_ksh'] ?? 0) }},
                        {{ (float) ($summary['revenue_usd'] ?? 0) }}
                    ],
                    backgroundColor: ['#0066B3', '#009639'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    if (quotationCtx) {
        new Chart(quotationCtx, {
            type: 'bar',
            data: {
                labels: ['KSH Pipeline', 'USD Pipeline', 'Won Value', 'Lost Value'],
                datasets: [{
                    label: 'Quotation Value',
                    data: [
                        {{ (float) $quotations->where('currency', 'KSH')->sum('pipeline_value') }},
                        {{ (float) $quotations->where('currency', 'USD')->sum('pipeline_value') }},
                        {{ (float) $quotations->sum('won_value') }},
                        {{ (float) $quotations->sum('lost_value') }}
                    ],
                    backgroundColor: ['#0066B3', '#009639', '#28a745', '#dc3545'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    }

    if (fiberCtx) {
        new Chart(fiberCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($fiberUtilization->take(10) as $fiber)
                        "{{ \Illuminate\Support\Str::limit($fiber->route_name ?? 'N/A', 15) }}",
                    @endforeach
                ],
                datasets: [{
                    label: 'Utilization %',
                    data: [
                        @foreach($fiberUtilization->take(10) as $fiber)
                            {{ (float) $fiber->utilization_percent }},
                        @endforeach
                    ],
                    backgroundColor: '#FFD700',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: { display: true, text: 'Utilization (%)' }
                    }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    }

if (revenueForecastCtx) {
    new Chart(revenueForecastCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($revenueForecasts as $forecast)
                    "{{ $forecast->currency }}",
                @endforeach
            ],
            datasets: [
                {
                    label: 'Actual Revenue',
                    data: [
                        @foreach($revenueForecasts as $forecast)
                            {{ (float) $forecast->actual_revenue }},
                        @endforeach
                    ],
                    borderWidth: 1
                },
                {
                    label: 'Forecast Revenue',
                    data: [
                        @foreach($revenueForecasts as $forecast)
                            {{ (float) $forecast->forecast_revenue }},
                        @endforeach
                    ],
                    borderWidth: 1
                }
            ]
        }
    });
}
});
</script>
@endif

<style>
:root {
    --kp-blue: #0066B3;
    --kp-green: #009639;
    --kp-yellow: #FFD700;
    --kp-dark: #003f20;
}

.dashboard-hero {
    background: linear-gradient(135deg, var(--kp-blue) 0%, var(--kp-green) 100%);
}

.kpi-card {
    color: white;
    transition: all 0.3s ease;
}

.kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.kpi-card-primary {
    background: linear-gradient(135deg, var(--kp-blue) 0%, #005499 100%);
}

.kpi-card-danger {
    background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
}

.kpi-card-success {
    background: linear-gradient(135deg, var(--kp-green) 0%, #00802c 100%);
}

.kpi-card-info {
    background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
}

.kpi-value {
    font-size: 1.25rem;
}

.kpi-value-large {
    font-size: 2rem;
}

.kpi-icon {
    width: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Accordion Styles */
.accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, var(--kp-blue) 0%, var(--kp-green) 100%);
    color: white;
}

.accordion-button:not(.collapsed) i {
    color: white !important;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: transparent;
}

/* Color Classes */
.bg-white-20 {
    background: rgba(255, 255, 255, 0.2);
}

.bg-kp-blue-light {
    background: rgba(0, 102, 179, 0.08);
}

.bg-kp-green-light {
    background: rgba(0, 150, 57, 0.08);
}

.bg-info-light {
    background: rgba(54, 185, 204, 0.08);
}

.bg-purple-light {
    background: rgba(111, 66, 193, 0.08);
}

.bg-danger-light {
    background: rgba(220, 53, 69, 0.08);
}

.text-white-70 {
    color: rgba(255, 255, 255, 0.7);
}

.text-kp-blue {
    color: var(--kp-blue) !important;
}

.text-kp-green {
    color: var(--kp-green) !important;
}

.text-kp-yellow {
    color: var(--kp-yellow) !important;
}

.text-purple {
    color: #6f42c1 !important;
}

.btn-kp-primary {
    background: var(--kp-blue);
    border-color: var(--kp-blue);
    color: white;
}

.btn-kp-primary:hover {
    background: #005499;
    border-color: #005499;
    color: white;
}

/* Table Styles */
.table th {
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #5a5c69;
}

.table td {
    vertical-align: middle;
}

.rounded-4 {
    border-radius: 1rem !important;
}

@media (max-width: 768px) {
    .kpi-value-large {
        font-size: 1.5rem;
    }
}

@media print {
    .dashboard-hero,
    .btn,
    .accordion-button {
        display: none !important;
    }

    .accordion-collapse {
        display: block !important;
    }
    .chart-wrapper {
    height: 300px;
    position: relative;
    width: 100%;
}

.chart-wrapper canvas {
    max-height: 100%;
    width: 100%;
}

@media (max-width: 768px) {
    .chart-wrapper {
        height: 250px;
    }
}

@media (max-width: 576px) {
    .chart-wrapper {
        height: 200px;
    }
}

/* Compact button style for executive dashboard */
.btn-compact {
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    height: 32px;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    line-height: 1;
}

.btn-compact i {
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .btn-compact {
        height: 30px;
        padding: 0.25rem 0.6rem;
    }
}
}
</style>
@endsection
