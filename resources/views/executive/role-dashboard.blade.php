@extends('layouts.app')

@section('title', 'Executive Dashboard')

@section('content')
<div class="container-fluid p-2 p-md-4">

    {{-- Header Section - Stacked on mobile --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1 fs-2 fs-md-1">
                <i class="fas fa-chart-line text-primary me-2"></i>
                Executive Dashboard
            </h2>
            <p class="text-muted mb-0 small">
                Strategic business performance overview
            </p>
        </div>

        <form method="GET" action="{{ route('executive.role.dashboard') }}" class="d-flex gap-2 w-100 w-md-auto">
            <input type="date" name="date" class="form-control form-control-sm"
                   value="{{ $snapshotDate }}">

            <button class="btn btn-primary rounded-pill px-3 px-md-4 btn-sm">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
        </form>
    </div>



    @if(!$kpis)
        <div class="alert alert-warning rounded-4 shadow-sm">
            No executive snapshot found for this period.
            Run:
            <strong class="d-block mt-1 small">php artisan reports:generate-executive --date={{ $snapshotDate }}</strong>
        </div>
    @else

        {{-- KPI Cards - 2 columns on mobile, 4 on desktop --}}
        <div class="row g-3 g-md-4 mb-4">

            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-muted text-uppercase fw-bold small">Revenue KSH</small>
                                <h3 class="fw-bold text-success mt-1 mt-md-2 mb-0 fs-5 fs-md-3">
                                    {{ number_format($kpis->revenue_ksh, 2) }}
                                </h3>
                            </div>
                            <i class="fas fa-money-bill-wave fa-lg text-success opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-muted text-uppercase fw-bold small">Revenue USD</small>
                                <h3 class="fw-bold text-success mt-1 mt-md-2 mb-0 fs-5 fs-md-3">
                                    {{ number_format($kpis->revenue_usd, 2) }}
                                </h3>
                            </div>
                            <i class="fas fa-dollar-sign fa-lg text-success opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-muted text-uppercase fw-bold small">AR KSH</small>
                                <h3 class="fw-bold text-danger mt-1 mt-md-2 mb-0 fs-5 fs-md-3">
                                    {{ number_format($kpis->accounts_receivable_ksh, 2) }}
                                </h3>
                            </div>
                            <i class="fas fa-file-invoice-dollar fa-lg text-danger opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-muted text-uppercase fw-bold small">AR USD</small>
                                <h3 class="fw-bold text-danger mt-1 mt-md-2 mb-0 fs-5 fs-md-3">
                                    {{ number_format($kpis->accounts_receivable_usd, 2) }}
                                </h3>
                            </div>
                            <i class="fas fa-file-invoice-dollar fa-lg text-danger opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Second Row - Active Leases, Contracts, Utilization --}}
        <div class="row g-3 g-md-4 mb-4">

            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-muted text-uppercase fw-bold small">Active Leases</small>
                                <h3 class="fw-bold text-primary mt-1 mt-md-2 mb-0 fs-5 fs-md-3">
                                    {{ number_format($kpis->active_leases) }}
                                </h3>
                            </div>
                            <i class="fas fa-network-wired fa-lg text-primary opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-muted text-uppercase fw-bold small">Active Contracts</small>
                                <h3 class="fw-bold text-primary mt-1 mt-md-2 mb-0 fs-5 fs-md-3">
                                    {{ number_format($kpis->active_contracts) }}
                                </h3>
                            </div>
                            <i class="fas fa-file-contract fa-lg text-primary opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-muted text-uppercase fw-bold small">Core Utilization</small>
                                <h3 class="fw-bold text-warning mt-1 mt-md-2 mb-0 fs-5 fs-md-3">
                                    {{ number_format($kpis->core_utilization_percent, 2) }}%
                                </h3>
                            </div>
                            <i class="fas fa-microchip fa-lg text-warning opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-muted text-uppercase fw-bold small">Network Availability</small>
                                <h3 class="fw-bold text-success mt-1 mt-md-2 mb-0 fs-5 fs-md-3">
                                    {{ number_format($kpis->network_availability_percent, 2) }}%
                                </h3>
                            </div>
                            <i class="fas fa-signal fa-lg text-success opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Charts Section - Stack vertically on mobile --}}
        <div class="row g-3 g-md-4 mb-4">

            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-3 pt-md-4 px-3 px-md-4">
                        <h5 class="fw-bold mb-0 fs-6 fs-md-5">
                            <i class="fas fa-chart-pie text-success me-2"></i>
                            Revenue by Currency
                        </h5>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="chart-container" style="position: relative; height: 240px; width: 100%;">
                            <canvas id="revenueCurrencyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-3 pt-md-4 px-3 px-md-4">
                        <h5 class="fw-bold mb-0 fs-6 fs-md-5">
                            <i class="fas fa-chart-bar text-danger me-2"></i>
                            Accounts Receivable
                        </h5>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="chart-container" style="position: relative; height: 240px; width: 100%;">
                            <canvas id="receivableChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-3 g-md-4 mb-4">

            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-3 pt-md-4 px-3 px-md-4">
                        <h5 class="fw-bold mb-0 fs-6 fs-md-5">
                            <i class="fas fa-network-wired text-primary me-2"></i>
                            Lease & Contract Summary
                        </h5>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="chart-container" style="position: relative; height: 240px; width: 100%;">
                            <canvas id="leaseContractChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-3 pt-md-4 px-3 px-md-4">
                        <h5 class="fw-bold mb-0 fs-6 fs-md-5">
                            <i class="fas fa-signal text-warning me-2"></i>
                            Network Performance
                        </h5>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="chart-container" style="position: relative; height: 240px; width: 100%;">
                            <canvas id="networkPerformanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-3 g-md-4 mb-4">

            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-3 pt-md-4 px-3 px-md-4">
                        <h5 class="fw-bold mb-0 fs-6 fs-md-5">
                            <i class="fas fa-chart-area text-info me-2"></i>
                            Forecast vs Actual Revenue
                        </h5>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="chart-container" style="position: relative; height: 280px; width: 100%;">
                            <canvas id="forecastChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Insights & Forecast Combined Section --}}
        <div class="row g-3 g-md-4 mb-4">

            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-3 pt-md-4 px-3 px-md-4">
                        <h5 class="fw-bold mb-0 fs-6 fs-md-5">
                            <i class="fas fa-lightbulb text-warning me-2"></i>
                            Executive Insights
                        </h5>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        @forelse($insights as $insight)
                            <div class="border-bottom pb-3 mb-3">
                                <span class="badge bg-{{
                                    $insight->severity === 'critical'
                                        ? 'danger'
                                        : ($insight->severity === 'warning' ? 'warning' : 'info')
                                }} rounded-pill mb-2 px-2 py-1 small">
                                    {{ strtoupper($insight->severity) }}
                                </span>

                                <h6 class="fw-bold mb-1 fs-6">{{ $insight->title }}</h6>
                                <p class="text-muted mb-0 small">{{ $insight->message }}</p>
                            </div>
                        @empty
                            <div class="alert alert-success rounded-4 mb-0 small">
                                No critical executive insights detected.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-3 pt-md-4 px-3 px-md-4">
                        <h5 class="fw-bold mb-0 fs-6 fs-md-5">
                            <i class="fas fa-chart-area text-primary me-2"></i>
                            Revenue Forecast
                        </h5>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        @forelse($forecasts as $forecast)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        <small class="text-muted fw-bold">{{ $forecast->currency }} FORECAST</small>
                                        <h4 class="fw-bold text-primary mb-1 fs-5">
                                            {{ $forecast->currency }}
                                            {{ number_format($forecast->forecast_revenue, 2) }}
                                        </h4>
                                    </div>
                                    <span class="badge {{ $forecast->growth_rate_percent >= 0 ? 'bg-success' : 'bg-danger' }} rounded-pill px-2 py-1">
                                        {{ $forecast->growth_rate_percent >= 0 ? '+' : '' }}{{ number_format($forecast->growth_rate_percent, 2) }}% growth
                                    </span>
                                </div>

                                <div class="mt-2">
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>Actual Revenue</span>
                                        <span><strong>{{ $forecast->currency }} {{ number_format($forecast->actual_revenue, 2) }}</strong></span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        @php
                                            $progressPercent = $forecast->forecast_revenue > 0 ? ($forecast->actual_revenue / $forecast->forecast_revenue) * 100 : 0;
                                        @endphp
                                        <div class="progress-bar {{ $progressPercent >= 100 ? 'bg-success' : 'bg-warning' }}"
                                             role="progressbar"
                                             style="width: {{ min(100, $progressPercent) }}%"
                                             aria-valuenow="{{ $progressPercent }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <p class="mb-0 small text-muted mt-1">
                                        {{ number_format($progressPercent, 1) }}% of forecast achieved
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info rounded-4 mb-0 small">
                                No forecast data available.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- Action Buttons - 2 columns on mobile, 4 on desktop --}}
        <div class="row g-3 mt-2">

            <div class="col-6 col-md-3">
                <a href="{{ route('executive.dashboard') }}" class="btn btn-outline-primary rounded-4 w-100 py-3 py-md-4 d-flex flex-column align-items-center justify-content-center">
                    <i class="fas fa-table fa-xl mb-2"></i>
                    <span class="small fw-medium">Full Reports</span>
                </a>
            </div>

            <div class="col-6 col-md-3">
                <a href="{{ route('executive.dashboard.gis') }}" class="btn btn-outline-success rounded-4 w-100 py-3 py-md-4 d-flex flex-column align-items-center justify-content-center">
                    <i class="fas fa-map-marked-alt fa-xl mb-2"></i>
                    <span class="small fw-medium">GIS Analytics</span>
                </a>
            </div>

            <div class="col-6 col-md-3">
                <a href="{{ route('executive.dashboard') }}?export=pdf" class="btn btn-outline-danger rounded-4 w-100 py-3 py-md-4 d-flex flex-column align-items-center justify-content-center">
                    <i class="fas fa-file-pdf fa-xl mb-2"></i>
                    <span class="small fw-medium">Download PDF</span>
                </a>
            </div>

            <div class="col-6 col-md-3">
                <a href="{{ route('executive.dashboard') }}?export=excel" class="btn btn-outline-success rounded-4 w-100 py-3 py-md-4 d-flex flex-column align-items-center justify-content-center">
                    <i class="fas fa-file-excel fa-xl mb-2"></i>
                    <span class="small fw-medium">Download Excel</span>
                </a>
            </div>

        </div>

    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.rounded-4 {
    border-radius: 1rem !important;
}

.card {
    transition: all 0.2s ease;
}

.card:active {
    transform: scale(0.98);
}

@media (hover: hover) {
    .card:hover {
        transform: translateY(-2px);
    }
}

/* Better touch targets for mobile */
.btn, .card {
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
}

/* Improved scrolling for insights section */
.card-body {
    overflow-x: hidden;
}

/* Chart container responsiveness */
.chart-container canvas {
    max-width: 100%;
    height: auto !important;
}

/* Progress bar styling */
.progress {
    background-color: #e9ecef;
    border-radius: 1rem;
}

/* Small screen adjustments */
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 12px;
        padding-right: 12px;
    }

    .card-body {
        padding: 12px !important;
    }

    h2 {
        font-size: 1.5rem;
    }
}
</style>

@if($kpis)
<script>
document.addEventListener('DOMContentLoaded', function () {

    const revenueKsh = {{ (float) $kpis->revenue_ksh }};
    const revenueUsd = {{ (float) $kpis->revenue_usd }};

    const receivableKsh = {{ (float) $kpis->accounts_receivable_ksh }};
    const receivableUsd = {{ (float) $kpis->accounts_receivable_usd }};

    const activeLeases = {{ (int) $kpis->active_leases }};
    const activeContracts = {{ (int) $kpis->active_contracts }};

    const coreUtilization = {{ (float) $kpis->core_utilization_percent }};
    const networkAvailability = {{ (float) $kpis->network_availability_percent }};
    const slaCompliance = {{ (float) $kpis->sla_compliance_percent }};

    const forecastLabels = [
        @foreach($forecasts as $forecast)
            "{{ $forecast->currency }}",
        @endforeach
    ];

    const actualRevenueData = [
        @foreach($forecasts as $forecast)
            {{ (float) $forecast->actual_revenue }},
        @endforeach
    ];

    const forecastRevenueData = [
        @foreach($forecasts as $forecast)
            {{ (float) $forecast->forecast_revenue }},
        @endforeach
    ];

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: {
                        size: window.innerWidth < 576 ? 10 : 12
                    }
                }
            },
            tooltip: {
                bodyFont: {
                    size: window.innerWidth < 576 ? 11 : 12
                }
            }
        }
    };

    const revenueCurrencyCtx = document.getElementById('revenueCurrencyChart');

    if (revenueCurrencyCtx) {
        new Chart(revenueCurrencyCtx, {
            type: 'doughnut',
            data: {
                labels: ['KSH Revenue', 'USD Revenue'],
                datasets: [{
                    data: [revenueKsh, revenueUsd],
                    backgroundColor: ['#28a745', '#20c997'],
                    borderWidth: 0
                }]
            },
            options: chartOptions
        });
    }

    const receivableCtx = document.getElementById('receivableChart');

    if (receivableCtx) {
        new Chart(receivableCtx, {
            type: 'bar',
            data: {
                labels: ['KSH', 'USD'],
                datasets: [{
                    label: 'Accounts Receivable',
                    data: [receivableKsh, receivableUsd],
                    backgroundColor: '#dc3545',
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: chartOptions
        });
    }

    const leaseContractCtx = document.getElementById('leaseContractChart');

    if (leaseContractCtx) {
        new Chart(leaseContractCtx, {
            type: 'bar',
            data: {
                labels: ['Active Leases', 'Active Contracts'],
                datasets: [{
                    label: 'Count',
                    data: [activeLeases, activeContracts],
                    backgroundColor: '#007bff',
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: chartOptions
        });
    }

    const networkPerformanceCtx = document.getElementById('networkPerformanceChart');

    if (networkPerformanceCtx) {
        new Chart(networkPerformanceCtx, {
            type: 'bar',
            data: {
                labels: ['Core Utilization', 'Network Availability', 'SLA Compliance'],
                datasets: [{
                    label: 'Percentage',
                    data: [coreUtilization, networkAvailability, slaCompliance],
                    backgroundColor: ['#ffc107', '#28a745', '#17a2b8'],
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            display: window.innerWidth >= 576
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: window.innerWidth < 576 ? 10 : 12
                            }
                        }
                    }
                }
            }
        });
    }

    const forecastCtx = document.getElementById('forecastChart');

    if (forecastCtx && forecastLabels.length > 0) {
        new Chart(forecastCtx, {
            type: 'bar',
            data: {
                labels: forecastLabels,
                datasets: [
                    {
                        label: 'Actual Revenue',
                        data: actualRevenueData,
                        backgroundColor: '#28a745',
                        borderRadius: 8,
                        borderWidth: 0
                    },
                    {
                        label: 'Forecast Revenue',
                        data: forecastRevenueData,
                        backgroundColor: '#ffc107',
                        borderRadius: 8,
                        borderWidth: 0
                    }
                ]
            },
            options: chartOptions
        });
    }

    // Handle window resize to reload charts
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            window.location.reload();
        }, 250);
    });

});
</script>
@endif
@endsection
