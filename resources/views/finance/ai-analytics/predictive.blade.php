@extends('layouts.app')

@section('title', 'Predictive Analytics')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 class="mb-0">
                <i class="fas fa-chart-line text-primary me-2"></i>Predictive Analytics
            </h4>
            <p class="text-muted mb-0">AI-powered predictions and forecasts for debt management across USD and KSH</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('finance.ai-analytics.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
            <button onclick="location.reload()" class="btn btn-outline-primary">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Prediction Summary Cards - USD -->
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="mb-3">
                <span class="badge bg-primary me-2">USD</span> US Dollar Predictions
            </h5>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="text-muted small">Expected Collections (Next 30 Days)</div>
                    <h3 class="mb-0">${{ number_format($usdMetrics->expected_collections ?? 0, 0) }}</h3>
                    <small class="text-muted">Based on historical patterns</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="text-muted small">Predicted Default Risk</div>
                    <h3 class="mb-0">{{ number_format($usdMetrics->default_risk ?? 0, 1) }}%</h3>
                    <small class="text-muted">USD portfolio risk</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="text-muted small">Cash Flow Forecast</div>
                    <h3 class="mb-0">${{ number_format($usdMetrics->cash_flow_forecast ?? 0, 0) }}</h3>
                    <small class="text-muted">Next 90 days projection</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="text-muted small">High-Risk Customers</div>
                    <h3 class="mb-0">{{ $usdMetrics->high_risk_count ?? 0 }}</h3>
                    <small class="text-muted">Require immediate attention</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Prediction Summary Cards - KSH -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <span class="badge bg-secondary me-2">KSH</span> Kenyan Shilling Predictions
            </h5>
        </div>
        <div class="col-md-3">
            <div class="card border-left-secondary shadow h-100">
                <div class="card-body">
                    <div class="text-muted small">Expected Collections (Next 30 Days)</div>
                    <h3 class="mb-0">KSH {{ number_format($kshMetrics->expected_collections ?? 0, 0) }}</h3>
                    <small class="text-muted">Based on historical patterns</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="text-muted small">Predicted Default Risk</div>
                    <h3 class="mb-0">{{ number_format($kshMetrics->default_risk ?? 0, 1) }}%</h3>
                    <small class="text-muted">KSH portfolio risk</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="text-muted small">Cash Flow Forecast</div>
                    <h3 class="mb-0">KSH {{ number_format($kshMetrics->cash_flow_forecast ?? 0, 0) }}</h3>
                    <small class="text-muted">Next 90 days projection</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="text-muted small">High-Risk Customers</div>
                    <h3 class="mb-0">{{ $kshMetrics->high_risk_count ?? 0 }}</h3>
                    <small class="text-muted">Require immediate attention</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection Forecast Chart -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>Collection Forecast (Next 12 Months)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="forecastChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie text-danger me-2"></i>Risk Distribution - USD
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="riskChartUsd" height="180"></canvas>
                </div>
            </div>
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie text-warning me-2"></i>Risk Distribution - KSH
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="riskChartKsh" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Aging Analysis -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar text-info me-2"></i>Aging Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Age Bucket</th>
                                    <th class="text-end">USD</th>
                                    <th class="text-end">KSH</th>
                                    <th class="text-end">% of Portfolio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Current (Not overdue)</th>
                                    <td class="text-end">${{ number_format($usdAging->current ?? 0, 2) }}</td>
                                    <td class="text-end">KSH {{ number_format($kshAging->current ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format((($usdAging->current_percentage ?? 0) + ($kshAging->current_percentage ?? 0)) / 2, 1) }}%</td>
                                </tr>
                                <tr>
                                    <td>1-30 Days Overdue</th>
                                    <td class="text-end text-warning">${{ number_format($usdAging->days1_30 ?? 0, 2) }}</td>
                                    <td class="text-end text-warning">KSH {{ number_format($kshAging->days1_30 ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format((($usdAging->days1_30_percentage ?? 0) + ($kshAging->days1_30_percentage ?? 0)) / 2, 1) }}%</td>
                                </tr>
                                <tr>
                                    <td>31-60 Days Overdue</th>
                                    <td class="text-end text-warning">${{ number_format($usdAging->days31_60 ?? 0, 2) }}</td>
                                    <td class="text-end text-warning">KSH {{ number_format($kshAging->days31_60 ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format((($usdAging->days31_60_percentage ?? 0) + ($kshAging->days31_60_percentage ?? 0)) / 2, 1) }}%</td>
                                </tr>
                                <tr>
                                    <td>61-90 Days Overdue</th>
                                    <td class="text-end text-warning">${{ number_format($usdAging->days61_90 ?? 0, 2) }}</td>
                                    <td class="text-end text-warning">KSH {{ number_format($kshAging->days61_90 ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format((($usdAging->days61_90_percentage ?? 0) + ($kshAging->days61_90_percentage ?? 0)) / 2, 1) }}%</td>
                                </tr>
                                <tr>
                                    <td>Over 90 Days Overdue</th>
                                    <td class="text-end text-danger fw-bold">${{ number_format($usdAging->days_over_90 ?? 0, 2) }}</td>
                                    <td class="text-end text-danger fw-bold">KSH {{ number_format($kshAging->days_over_90 ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format((($usdAging->days_over_90_percentage ?? 0) + ($kshAging->days_over_90_percentage ?? 0)) / 2, 1) }}%</td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>TOTAL</th>
                                    <td class="text-end">${{ number_format($usdAging->total ?? 0, 2) }}</td>
                                    <td class="text-end">KSH {{ number_format($kshAging->total ?? 0, 2) }}</td>
                                    <td class="text-end">100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Insights -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-brain text-warning me-2"></i>Key Predictions
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($insights['key_findings'] ?? [] as $finding)
                            <li class="list-group-item bg-transparent px-0">
                                <i class="fas fa-chart-line text-primary me-2"></i>
                                {{ $finding }}
                            </li>
                        @empty
                            <li class="list-group-item bg-transparent text-muted">No predictions available.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb text-success me-2"></i>Recommended Actions
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($insights['recommendations'] ?? [] as $recommendation)
                            <li class="list-group-item bg-transparent px-0">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                {{ is_array($recommendation) ? ($recommendation['action'] ?? '') : $recommendation }}
                            </li>
                        @empty
                            <li class="list-group-item bg-transparent text-muted">No recommendations available.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Collection Forecast Chart
        const forecastData = {!! json_encode($collectionForecast) !!};
        const forecastLabels = forecastData.map(function(item) { return item.month; });
        const usdForecast = forecastData.map(function(item) { return item.usd_expected; });
        const kshForecast = forecastData.map(function(item) { return item.ksh_expected; });

        const forecastCtx = document.getElementById('forecastChart')?.getContext('2d');
        if (forecastCtx) {
            new Chart(forecastCtx, {
                type: 'line',
                data: {
                    labels: forecastLabels,
                    datasets: [
                        {
                            label: 'USD Expected Collections ($)',
                            data: usdForecast,
                            borderColor: 'rgb(54, 162, 235)',
                            backgroundColor: 'rgba(54, 162, 235, 0.1)',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y'
                        },
                        {
                            label: 'KSH Expected Collections (KSH)',
                            data: kshForecast,
                            borderColor: 'rgb(255, 99, 132)',
                            backgroundColor: 'rgba(255, 99, 132, 0.1)',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    var value = context.parsed.y;
                                    if (label.includes('USD')) {
                                        return label + ': $' + value.toLocaleString();
                                    } else {
                                        return label + ': KSH ' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Amount (USD)' },
                            ticks: { callback: function(value) { return '$' + value.toLocaleString(); } }
                        },
                        y1: {
                            position: 'right',
                            beginAtZero: true,
                            title: { display: true, text: 'Amount (KSH)' },
                            ticks: { callback: function(value) { return 'KSH ' + value.toLocaleString(); } },
                            grid: { drawOnChartArea: false }
                        }
                    }
                }
            });
        }

        // Risk Distribution Chart - USD
        @php
            $riskDataUsd = isset($riskDistribution['usd']) ? $riskDistribution['usd'] : ['low' => 0, 'medium' => 0, 'high' => 0, 'critical' => 0];
            $riskDataKsh = isset($riskDistribution['ksh']) ? $riskDistribution['ksh'] : ['low' => 0, 'medium' => 0, 'high' => 0, 'critical' => 0];
        @endphp

        const riskDataUsd = {
            low: {{ $riskDataUsd['low']['percentage'] ?? 0 }},
            medium: {{ $riskDataUsd['medium']['percentage'] ?? 0 }},
            high: {{ $riskDataUsd['high']['percentage'] ?? 0 }},
            critical: {{ $riskDataUsd['critical']['percentage'] ?? 0 }}
        };
        const riskCtxUsd = document.getElementById('riskChartUsd')?.getContext('2d');
        if (riskCtxUsd) {
            new Chart(riskCtxUsd, {
                type: 'doughnut',
                data: {
                    labels: ['Low Risk', 'Medium Risk', 'High Risk', 'Critical'],
                    datasets: [{
                        data: [riskDataUsd.low, riskDataUsd.medium, riskDataUsd.high, riskDataUsd.critical],
                        backgroundColor: ['#28a745', '#ffc107', '#fd7e14', '#dc3545'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = context.raw || 0;
                                    return label + ': ' + value.toFixed(1) + '%';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Risk Distribution Chart - KSH
        const riskDataKsh = {
            low: {{ $riskDataKsh['low']['percentage'] ?? 0 }},
            medium: {{ $riskDataKsh['medium']['percentage'] ?? 0 }},
            high: {{ $riskDataKsh['high']['percentage'] ?? 0 }},
            critical: {{ $riskDataKsh['critical']['percentage'] ?? 0 }}
        };
        const riskCtxKsh = document.getElementById('riskChartKsh')?.getContext('2d');
        if (riskCtxKsh) {
            new Chart(riskCtxKsh, {
                type: 'doughnut',
                data: {
                    labels: ['Low Risk', 'Medium Risk', 'High Risk', 'Critical'],
                    datasets: [{
                        data: [riskDataKsh.low, riskDataKsh.medium, riskDataKsh.high, riskDataKsh.critical],
                        backgroundColor: ['#28a745', '#ffc107', '#fd7e14', '#dc3545'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = context.raw || 0;
                                    return label + ': ' + value.toFixed(1) + '%';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
