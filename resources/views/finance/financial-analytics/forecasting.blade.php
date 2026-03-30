{{-- resources/views/finance/financial-analytics/forecasting.blade.php --}}
@extends('layouts.app')

@section('title', 'Financial Forecasting')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center py-3">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-crystal-ball text-primary me-2"></i>Financial Forecasting
        </h1>
        <div>
            <button class="btn btn-outline-primary" id="exportForecast">
                <i class="fas fa-download me-1"></i>Export Forecast
            </button>
            <button class="btn btn-primary ms-2" id="runForecast">
                <i class="fas fa-sync-alt me-1"></i>Refresh Forecast
            </button>
        </div>
    </div>

    <!-- Forecast Controls -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Forecast Horizon</label>
                    <select class="form-select" id="forecastHorizon">
                        <option value="30">30 Days</option>
                        <option value="90" selected>90 Days</option>
                        <option value="180">6 Months</option>
                        <option value="365">1 Year</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Forecast Model</label>
                    <select class="form-select" id="forecastModel">
                        <option value="arima">ARIMA (Statistical)</option>
                        <option value="exponential" selected>Exponential Smoothing</option>
                        <option value="moving_average">Moving Average</option>
                        <option value="regression">Linear Regression</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Confidence Level</label>
                    <select class="form-select" id="confidenceLevel">
                        <option value="80">80% (Aggressive)</option>
                        <option value="90" selected>90% (Balanced)</option>
                        <option value="95">95% (Conservative)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Scenario</label>
                    <select class="form-select" id="scenario">
                        <option value="baseline" selected>Baseline</option>
                        <option value="optimistic">Optimistic (+10%)</option>
                        <option value="pessimistic">Pessimistic (-10%)</option>
                        <option value="worst_case">Worst Case (-25%)</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="includeSeasonality" checked>
                        <label class="form-check-label" for="includeSeasonality">Include Seasonality</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="includeTrend" checked>
                        <label class="form-check-label" for="includeTrend">Include Trend</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="adjustForInflation">
                        <label class="form-check-label" for="adjustForInflation">Adjust for Inflation (3%)</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Forecast Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Next 30 Days Forecast</div>
                            <div class="h5 mb-0 fw-bold text-gray-800" id="next30Days">$0</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span id="next30Change" class="text-success">
                                    <i class="fas fa-arrow-up"></i> 0%
                                </span> vs previous
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Next Quarter Forecast</div>
                            <div class="h5 mb-0 fw-bold text-gray-800" id="nextQuarter">$0</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span id="nextQuarterChange" class="text-success">
                                    <i class="fas fa-arrow-up"></i> 0%
                                </span> vs previous
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                Forecast Confidence</div>
                            <div class="h5 mb-0 fw-bold text-gray-800" id="confidenceScore">0%</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span id="confidenceLevelText">Based on historical data</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-brain fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                Forecast Error (MAPE)</div>
                            <div class="h5 mb-0 fw-bold text-gray-800" id="forecastError">0%</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span id="errorTrend" class="text-success">Improving</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Forecast Chart -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Forecast</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <a class="dropdown-item" href="#" data-chart="revenue">Revenue</a>
                            <a class="dropdown-item" href="#" data-chart="profit">Profit</a>
                            <a class="dropdown-item" href="#" data-chart="cashflow">Cash Flow</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" id="toggleConfidence">Toggle Confidence Interval</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="forecastChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forecast Details -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Forecast Details</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            <tbody>
                                <tr>
                                    <td><strong>Model Used</strong></td>
                                    <td><span class="badge bg-info" id="modelUsed">Exponential</span></td>
                                    <td class="text-end">Smoothing factor: 0.3</td>
                                </tr>
                                <tr>
                                    <td><strong>Data Points</strong></td>
                                    <td><span class="badge bg-success" id="dataPoints">0</span></td>
                                    <td class="text-end">Last 90 days</td>
                                </tr>
                                <tr>
                                    <td><strong>Seasonality</strong></td>
                                    <td><span class="badge bg-warning" id="seasonalityDetected">Weekly</span></td>
                                    <td class="text-end">Pattern detected</td>
                                </tr>
                                <tr>
                                    <td><strong>Trend</strong></td>
                                    <td><span class="badge bg-primary" id="trendStrength">Positive</span></td>
                                    <td class="text-end">Slope: +2.3%</td>
                                </tr>
                                <tr>
                                    <td><strong>Outliers</strong></td>
                                    <td><span class="badge bg-danger" id="outlierCount">0</span></td>
                                    <td class="text-end">Removed from model</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated</strong></td>
                                    <td colspan="2" class="text-end" id="lastUpdated">Just now</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Assumptions -->
                    <div class="mt-4">
                        <h6 class="fw-bold text-primary mb-3">Key Assumptions</h6>
                        <div class="form-group">
                            <label class="small">Monthly Growth Rate</label>
                            <input type="range" class="form-range" id="growthRate" min="0" max="10" step="0.5" value="3">
                            <div class="d-flex justify-content-between">
                                <span class="small text-muted">0%</span>
                                <span class="small" id="growthRateValue">3%</span>
                                <span class="small text-muted">10%</span>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label class="small">Collection Rate</label>
                            <input type="range" class="form-range" id="collectionRate" min="70" max="100" step="1" value="85">
                            <div class="d-flex justify-content-between">
                                <span class="small text-muted">70%</span>
                                <span class="small" id="collectionRateValue">85%</span>
                                <span class="small text-muted">100%</span>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary w-100 mt-3" id="updateAssumptions">
                            <i class="fas fa-calculator me-1"></i> Recalculate with New Assumptions
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scenario Analysis -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Scenario Analysis</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Scenario</th>
                                    <th>Probability</th>
                                    <th>Next 30 Days</th>
                                    <th>Next Quarter</th>
                                    <th>Impact</th>
                                    <th>Key Drivers</th>
                                    <th>Recommendation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <span class="badge bg-success">Baseline</span>
                                    </td>
                                    <td>60%</td>
                                    <td id="scenarioBaseline30">$0</td>
                                    <td id="scenarioBaselineQ">$0</td>
                                    <td>Neutral</td>
                                    <td>Current trends continue</td>
                                    <td>Monitor performance</td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-info">Optimistic</span>
                                    </td>
                                    <td>20%</td>
                                    <td id="scenarioOptimistic30">$0</td>
                                    <td id="scenarioOptimisticQ">$0</td>
                                    <td>+10-15%</td>
                                    <td>New customer acquisition</td>
                                    <td>Increase capacity</td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-warning">Pessimistic</span>
                                    </td>
                                    <td>15%</td>
                                    <td id="scenarioPessimistic30">$0</td>
                                    <td id="scenarioPessimisticQ">$0</td>
                                    <td>-10-15%</td>
                                    <td>Customer churn increase</td>
                                    <td>Cost optimization</td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-danger">Worst Case</span>
                                    </td>
                                    <td>5%</td>
                                    <td id="scenarioWorstCase30">$0</td>
                                    <td id="scenarioWorstCaseQ">$0</td>
                                    <td>-25%+</td>
                                    <td>Market downturn</td>
                                    <td>Contingency planning</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk Assessment -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Risk Assessment</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            // Handle risk forecast data - it might be in different formats
                            $riskData = [];

                            if (isset($forecasts['risk_forecast'])) {
                                if (is_array($forecasts['risk_forecast'])) {
                                    // Check if it's an associative array with keys like 'high_risk', 'medium_risk', etc.
                                    if (isset($forecasts['risk_forecast']['high_risk'])) {
                                        // It's in the expected format from controller
                                        $riskData = [
                                            $forecasts['risk_forecast']['high_risk'] ?? [],
                                            $forecasts['risk_forecast']['medium_risk'] ?? [],
                                            $forecasts['risk_forecast']['low_risk'] ?? []
                                        ];
                                    } else {
                                        // It might be a regular indexed array
                                        $riskData = $forecasts['risk_forecast'];
                                    }
                                }
                            }

                            // Default risks if no data
                            if (empty($riskData)) {
                                $riskData = [
                                    [
                                        'category' => 'Collections',
                                        'probability' => 0.3,
                                        'impact' => 'High',
                                        'mitigation' => 'Implement stricter credit terms'
                                    ],
                                    [
                                        'category' => 'Customer Churn',
                                        'probability' => 0.2,
                                        'impact' => 'Medium',
                                        'mitigation' => 'Improve customer service'
                                    ],
                                    [
                                        'category' => 'Market Competition',
                                        'probability' => 0.1,
                                        'impact' => 'Low',
                                        'mitigation' => 'Differentiate service offerings'
                                    ]
                                ];
                            }
                        @endphp

                        @foreach($riskData as $risk)
                            @php
                                // Ensure $risk is an array
                                if (!is_array($risk)) {
                                    continue;
                                }

                                $impact = $risk['impact'] ?? 'Medium';
                                $category = $risk['category'] ?? 'Unknown Risk';
                                $probability = $risk['probability'] ?? 0.5;
                                $mitigation = $risk['mitigation'] ?? 'No mitigation plan';

                                $impactClass = $impact == 'High' ? 'danger' :
                                             ($impact == 'Medium' ? 'warning' : 'info');
                                $impactIcon = $impact == 'High' ? 'fa-exclamation-triangle' :
                                            ($impact == 'Medium' ? 'fa-exclamation-circle' : 'fa-info-circle');
                            @endphp

                            <div class="col-md-6 mb-3">
                                <div class="card border-left-{{ $impactClass }} h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="card-title text-{{ $impactClass }}">
                                                    {{ $category }}
                                                </h6>
                                                <div class="small">
                                                    Probability: {{ ($probability * 100)|number_format(0) }}%
                                                </div>
                                                <div class="small">
                                                    Impact: {{ $impact }}
                                                </div>
                                            </div>
                                            <div class="text-{{ $impactClass }}">
                                                <i class="fas {{ $impactIcon }} fa-2x"></i>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <div class="progress mb-2" style="height: 10px;">
                                                <div class="progress-bar bg-{{ $impactClass }}"
                                                     style="width: {{ $probability * 100 }}%">
                                                </div>
                                            </div>
                                            <p class="small mb-0">{{ $mitigation }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Forecast Accuracy & History -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Forecast Accuracy History</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="accuracyChart" height="200"></canvas>
                    </div>
                    <div class="mt-4">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="p-2 border rounded">
                                    <div class="text-xs text-muted">Last Month</div>
                                    <div class="h6 mb-0 text-success" id="accuracyLastMonth">0%</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded">
                                    <div class="text-xs text-muted">Last Quarter</div>
                                    <div class="h6 mb-0 text-primary" id="accuracyLastQuarter">0%</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded">
                                    <div class="text-xs text-muted">All Time</div>
                                    <div class="h6 mb-0 text-info" id="accuracyAllTime">0%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="forecastLoading" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 class="text-primary">Running Forecast Models...</h5>
                <p class="text-muted">Analyzing historical data and generating predictions</p>
                <div class="progress mt-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                         style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forecast Details Modal -->
<div class="modal fade" id="forecastDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detailed Forecast Analysis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="forecastDetailsContent">
                    <!-- Content loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}

.cursor-pointer {
    cursor: pointer;
}

.progress-bar-striped {
    background-image: linear-gradient(45deg, rgba(255,255,255,0.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.15) 50%, rgba(255,255,255,0.15) 75%, transparent 75%, transparent);
    background-size: 1rem 1rem;
}

.progress-bar-animated {
    animation: progress-bar-stripes 1s linear infinite;
}

@keyframes progress-bar-stripes {
    0% { background-position: 1rem 0; }
    100% { background-position: 0 0; }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let forecastChart, accuracyChart;
    let showConfidenceInterval = true;

    // Initialize with data from controller
    @if(isset($forecasts))
        initializeForecasts(@json($forecasts));
    @else
        loadForecastData();
    @endif

    // Event listeners
    document.getElementById('forecastHorizon').addEventListener('change', loadForecastData);
    document.getElementById('forecastModel').addEventListener('change', loadForecastData);
    document.getElementById('confidenceLevel').addEventListener('change', loadForecastData);
    document.getElementById('scenario').addEventListener('change', updateScenario);
    document.getElementById('runForecast').addEventListener('click', loadForecastData);
    document.getElementById('exportForecast').addEventListener('click', exportForecast);
    document.getElementById('toggleConfidence').addEventListener('click', toggleConfidence);
    document.getElementById('updateAssumptions').addEventListener('click', updateAssumptions);

    // Range inputs
    document.getElementById('growthRate').addEventListener('input', function() {
        document.getElementById('growthRateValue').textContent = this.value + '%';
    });

    document.getElementById('collectionRate').addEventListener('input', function() {
        document.getElementById('collectionRateValue').textContent = this.value + '%';
    });

    // Chart type dropdown
    document.querySelectorAll('[data-chart]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const chartType = this.dataset.chart;
            updateChartType(chartType);
        });
    });

    function initializeForecasts(forecasts) {
        // Update metrics from controller data
        if (forecasts.revenue_forecast) {
            const rev = forecasts.revenue_forecast;
            document.getElementById('next30Days').textContent = '$' + formatNumber(rev.next_month || 0);
            document.getElementById('nextQuarter').textContent = '$' + formatNumber(rev.next_quarter || 0);

            // Handle confidence - it might be a string or object
            let confidenceValue = 'MEDIUM';
            if (typeof rev.confidence === 'string') {
                confidenceValue = rev.confidence.toUpperCase();
            } else if (rev.confidence) {
                confidenceValue = 'MEDIUM';
            }

            document.getElementById('confidenceScore').textContent = confidenceValue;
            document.getElementById('modelUsed').textContent = rev.assumptions ? (Array.isArray(rev.assumptions) ? rev.assumptions[0] : rev.assumptions) : 'Exponential';
        }

        // Update scenarios
        updateScenarioValues();

        // Initialize charts
        updateForecastChart();
        updateAccuracyChart();

        // Update last updated time
        document.getElementById('lastUpdated').textContent = new Date().toLocaleString();
    }

    function loadForecastData() {
        showLoading();

        const horizon = document.getElementById('forecastHorizon').value;
        const model = document.getElementById('forecastModel').value;
        const confidence = document.getElementById('confidenceLevel').value;
        const scenario = document.getElementById('scenario').value;
        const includeSeasonality = document.getElementById('includeSeasonality').checked;
        const includeTrend = document.getElementById('includeTrend').checked;
        const adjustForInflation = document.getElementById('adjustForInflation').checked;

        // In a real app, you would make an API call here
        // For now, simulate with mock data
        setTimeout(() => {
            generateMockForecast(horizon, model, confidence);
            hideLoading();
        }, 2000);
    }

    function generateMockForecast(horizon, model, confidence) {
        // Generate mock forecast data
        const days = parseInt(horizon);
        const historicalDays = Math.min(90, days);

        // Historical data
        const historicalLabels = [];
        const historicalData = [];
        let baseValue = 50000;

        for (let i = historicalDays; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            historicalLabels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));

            // Generate realistic historical data with trend and seasonality
            const trend = 0.0005; // 0.05% daily growth
            const seasonality = Math.sin(i * 2 * Math.PI / 7) * 0.1; // Weekly seasonality
            const noise = (Math.random() - 0.5) * 0.15; // Random noise
            const value = baseValue * (1 + trend * (historicalDays - i)) * (1 + seasonality) * (1 + noise);

            historicalData.push(Math.max(1000, value));
        }

        // Forecast data
        const forecastLabels = [];
        const forecastData = [];
        const upperBound = [];
        const lowerBound = [];

        const lastHistoricalValue = historicalData[historicalData.length - 1];
        const growthRate = parseFloat(document.getElementById('growthRate').value) / 100 / 30; // Convert monthly to daily

        for (let i = 1; i <= days; i++) {
            const date = new Date();
            date.setDate(date.getDate() + i);
            forecastLabels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));

            // Generate forecast with confidence interval
            const trend = growthRate * i;
            const seasonality = Math.sin((historicalDays + i) * 2 * Math.PI / 7) * 0.1;
            const baseForecast = lastHistoricalValue * (1 + trend) * (1 + seasonality);

            forecastData.push(baseForecast);

            // Confidence interval based on confidence level
            const confidenceMultiplier = (100 - parseInt(confidence)) / 100;
            const interval = baseForecast * confidenceMultiplier * 0.1;

            upperBound.push(baseForecast + interval);
            lowerBound.push(baseForecast - interval);
        }

        // Update metrics
        const next30Days = forecastData.slice(0, 30).reduce((a, b) => a + b, 0) / 30;
        const nextQuarter = forecastData.slice(0, 90).reduce((a, b) => a + b, 0) / 90;

        document.getElementById('next30Days').textContent = '$' + formatNumber(next30Days);
        document.getElementById('nextQuarter').textContent = '$' + formatNumber(nextQuarter);

        // Calculate changes
        const avgHistorical = historicalData.reduce((a, b) => a + b, 0) / historicalData.length;
        const next30Change = ((next30Days - avgHistorical) / avgHistorical) * 100;
        const nextQuarterChange = ((nextQuarter - avgHistorical) / avgHistorical) * 100;

        updateChangeIndicator('next30Change', next30Change);
        updateChangeIndicator('nextQuarterChange', nextQuarterChange);

        // Update confidence score
        let confidenceScore;
        if (confidence >= 95) {
            confidenceScore = 'HIGH';
        } else if (confidence >= 90) {
            confidenceScore = 'MEDIUM';
        } else {
            confidenceScore = 'LOW';
        }

        document.getElementById('confidenceScore').textContent = confidenceScore;
        document.getElementById('confidenceLevelText').textContent = confidence + '% confidence level';

        // Update forecast error (simulated)
        const forecastError = 5 + Math.random() * 10;
        document.getElementById('forecastError').textContent = forecastError.toFixed(1) + '%';

        const errorTrend = Math.random() > 0.5 ? 'Improving' : 'Stable';
        document.getElementById('errorTrend').textContent = errorTrend;
        document.getElementById('errorTrend').className = errorTrend === 'Improving' ? 'text-success' : 'text-warning';

        // Update model details
        document.getElementById('modelUsed').textContent = document.getElementById('forecastModel').options[document.getElementById('forecastModel').selectedIndex].text;
        document.getElementById('dataPoints').textContent = historicalData.length;
        document.getElementById('seasonalityDetected').textContent = document.getElementById('includeSeasonality').checked ? 'Weekly' : 'None';
        document.getElementById('trendStrength').textContent = growthRate > 0 ? 'Positive' : growthRate < 0 ? 'Negative' : 'Neutral';
        document.getElementById('outlierCount').textContent = Math.floor(Math.random() * 5);
        document.getElementById('lastUpdated').textContent = new Date().toLocaleString();

        // Update charts
        updateForecastChart(historicalLabels, historicalData, forecastLabels, forecastData, upperBound, lowerBound);
        updateAccuracyChart();
        updateScenarioValues();
    }

    function updateForecastChart(historicalLabels = [], historicalData = [], forecastLabels = [], forecastData = [], upperBound = [], lowerBound = []) {
        const ctx = document.getElementById('forecastChart').getContext('2d');

        if (forecastChart) {
            forecastChart.destroy();
        }

        // Combine historical and forecast data
        const allLabels = [...historicalLabels, ...forecastLabels];
        const allData = [...historicalData, ...forecastData];

        // Create datasets
        const datasets = [
            {
                label: 'Historical',
                data: [...historicalData, ...Array(forecastData.length).fill(null)],
                borderColor: '#4e73df',
                backgroundColor: 'transparent',
                borderWidth: 2,
                tension: 0.4,
                pointRadius: 3
            },
            {
                label: 'Forecast',
                data: [...Array(historicalData.length).fill(null), ...forecastData],
                borderColor: '#1cc88a',
                backgroundColor: 'transparent',
                borderWidth: 2,
                borderDash: [5, 5],
                tension: 0.4,
                pointRadius: 3
            }
        ];

        // Add confidence interval if enabled
        if (showConfidenceInterval && upperBound.length > 0 && lowerBound.length > 0) {
            datasets.push({
                label: 'Confidence Interval',
                data: [...Array(historicalData.length).fill(null), ...upperBound],
                borderColor: 'transparent',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                borderWidth: 0,
                fill: '-1',
                tension: 0
            });

            datasets.push({
                label: 'Confidence Interval',
                data: [...Array(historicalData.length).fill(null), ...lowerBound],
                borderColor: 'transparent',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                borderWidth: 0,
                fill: '-1',
                tension: 0
            });
        }

        forecastChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: allLabels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '$' + formatNumber(context.parsed.y);
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxTicksLimit: 10
                        }
                    },
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return '$' + formatNumber(value);
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    function updateAccuracyChart() {
        const ctx = document.getElementById('accuracyChart').getContext('2d');

        if (accuracyChart) {
            accuracyChart.destroy();
        }

        // Generate mock accuracy data
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const accuracyData = months.map(() => 80 + Math.random() * 15);

        // Calculate averages
        const lastMonthAccuracy = accuracyData[accuracyData.length - 1];
        const lastQuarterAccuracy = accuracyData.slice(-3).reduce((a, b) => a + b, 0) / 3;
        const allTimeAccuracy = accuracyData.reduce((a, b) => a + b, 0) / accuracyData.length;

        document.getElementById('accuracyLastMonth').textContent = lastMonthAccuracy.toFixed(1) + '%';
        document.getElementById('accuracyLastQuarter').textContent = lastQuarterAccuracy.toFixed(1) + '%';
        document.getElementById('accuracyAllTime').textContent = allTimeAccuracy.toFixed(1) + '%';

        accuracyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Forecast Accuracy %',
                    data: accuracyData,
                    borderColor: '#36b9cc',
                    backgroundColor: 'rgba(54, 185, 204, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        min: 70,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    function updateScenario() {
        const scenario = document.getElementById('scenario').value;
        updateScenarioValues(scenario);
    }

    function updateScenarioValues(scenario = 'baseline') {
        const base30 = parseFloat(document.getElementById('next30Days').textContent.replace(/[^0-9.-]+/g, ''));
        const baseQ = parseFloat(document.getElementById('nextQuarter').textContent.replace(/[^0-9.-]+/g, ''));

        let multiplier30, multiplierQ;

        switch(scenario) {
            case 'optimistic':
                multiplier30 = 1.1;
                multiplierQ = 1.12;
                break;
            case 'pessimistic':
                multiplier30 = 0.9;
                multiplierQ = 0.88;
                break;
            case 'worst_case':
                multiplier30 = 0.75;
                multiplierQ = 0.7;
                break;
            default: // baseline
                multiplier30 = 1;
                multiplierQ = 1;
        }

        document.getElementById('scenarioBaseline30').textContent = '$' + formatNumber(base30);
        document.getElementById('scenarioBaselineQ').textContent = '$' + formatNumber(baseQ);
        document.getElementById('scenarioOptimistic30').textContent = '$' + formatNumber(base30 * 1.1);
        document.getElementById('scenarioOptimisticQ').textContent = '$' + formatNumber(baseQ * 1.12);
        document.getElementById('scenarioPessimistic30').textContent = '$' + formatNumber(base30 * 0.9);
        document.getElementById('scenarioPessimisticQ').textContent = '$' + formatNumber(baseQ * 0.88);
        document.getElementById('scenarioWorstCase30').textContent = '$' + formatNumber(base30 * 0.75);
        document.getElementById('scenarioWorstCaseQ').textContent = '$' + formatNumber(baseQ * 0.7);
    }

    function updateChartType(type) {
        // This would switch between revenue, profit, cashflow charts
        // For now, just update the chart title
        const titles = {
            'revenue': 'Revenue Forecast',
            'profit': 'Profit Forecast',
            'cashflow': 'Cash Flow Forecast'
        };

        document.querySelector('#forecastChart').closest('.card').querySelector('.card-header h6').textContent =
            titles[type] || 'Revenue Forecast';
    }

    function toggleConfidence(e) {
        e.preventDefault();
        showConfidenceInterval = !showConfidenceInterval;
        loadForecastData(); // Reload to update chart
    }

    function updateAssumptions() {
        loadForecastData();
    }

    function updateChangeIndicator(elementId, change) {
        const element = document.getElementById(elementId);
        const icon = change >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
        const colorClass = change >= 0 ? 'text-success' : 'text-danger';

        element.className = colorClass;
        element.innerHTML = `<i class="fas ${icon}"></i> ${Math.abs(change).toFixed(1)}%`;
    }

    function exportForecast() {
        const forecastData = {
            next30Days: document.getElementById('next30Days').textContent,
            nextQuarter: document.getElementById('nextQuarter').textContent,
            confidence: document.getElementById('confidenceScore').textContent,
            model: document.getElementById('modelUsed').textContent,
            timestamp: new Date().toISOString()
        };

        // Create CSV content
        let csv = 'Financial Forecast Report\n';
        csv += 'Generated: ' + new Date().toLocaleDateString() + '\n\n';
        csv += 'Metric,Value\n';
        csv += 'Next 30 Days Forecast,' + forecastData.next30Days + '\n';
        csv += 'Next Quarter Forecast,' + forecastData.nextQuarter + '\n';
        csv += 'Forecast Confidence,' + forecastData.confidence + '\n';
        csv += 'Model Used,' + forecastData.model + '\n';
        csv += 'Generated At,' + new Date(forecastData.timestamp).toLocaleString() + '\n\n';

        csv += 'Scenario Analysis\n';
        csv += 'Scenario,Next 30 Days,Next Quarter\n';
        csv += 'Baseline,' + document.getElementById('scenarioBaseline30').textContent + ',' + document.getElementById('scenarioBaselineQ').textContent + '\n';
        csv += 'Optimistic,' + document.getElementById('scenarioOptimistic30').textContent + ',' + document.getElementById('scenarioOptimisticQ').textContent + '\n';
        csv += 'Pessimistic,' + document.getElementById('scenarioPessimistic30').textContent + ',' + document.getElementById('scenarioPessimisticQ').textContent + '\n';
        csv += 'Worst Case,' + document.getElementById('scenarioWorstCase30').textContent + ',' + document.getElementById('scenarioWorstCaseQ').textContent + '\n';

        // Create download link
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `financial_forecast_${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);

        showAlert('Forecast exported successfully!', 'success');
    }

    function showLoading() {
        const modal = new bootstrap.Modal(document.getElementById('forecastLoading'));
        modal.show();
    }

    function hideLoading() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('forecastLoading'));
        if (modal) {
            modal.hide();
        }
    }

    function showAlert(message, type = 'info') {
        const alertClass = type === 'success' ? 'alert-success' :
                          type === 'error' ? 'alert-danger' : 'alert-info';

        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(alert);

        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    function formatNumber(num) {
        if (typeof num !== 'number') {
            num = parseFloat(num) || 0;
        }

        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        }
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toFixed(0);
    }
});
</script>
@endpush
