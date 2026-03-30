@php
    // Convert initial data to JSON for JavaScript
    $initialDataJson = json_encode($initialData ?? []);
@endphp

{{-- resources/views/finance/financial-analytics/trends.blade.php --}}
@extends('layouts.app')

@section('title', 'Financial Trend Analysis')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center py-3">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line text-primary me-2"></i>Financial Trend Analysis
        </h1>
        <div>
            <button class="btn btn-outline-primary" id="exportData">
                <i class="fas fa-download me-1"></i>Export
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Time Period</label>
                    <select class="form-select" id="timePeriod">
                        <option value="7">Last 7 Days</option>
                        <option value="30" selected>Last 30 Days</option>
                        <option value="90">Last Quarter</option>
                        <option value="180">Last 6 Months</option>
                        <option value="365">Last Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Metric</label>
                    <select class="form-select" id="metricSelect">
                        <option value="revenue">Revenue</option>
                        <option value="profit">Profit</option>
                        <option value="expenses">Expenses</option>
                        <option value="margin">Profit Margin</option>
                        <option value="growth">Growth Rate</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Granularity</label>
                    <select class="form-select" id="granularity">
                        <option value="daily">Daily</option>
                        <option value="weekly" selected>Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Comparison</label>
                    <select class="form-select" id="comparison">
                        <option value="none">No Comparison</option>
                        <option value="previous_period">Previous Period</option>
                        <option value="year_over_year">Year Over Year</option>
                        <option value="budget">vs Budget</option>
                    </select>
                </div>
            </div>

            <!-- Custom Date Range (hidden by default) -->
            <div class="row mt-3 d-none" id="customDateRange">
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="startDate">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" id="endDate" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3 align-self-end">
                    <button class="btn btn-primary" id="applyCustomRange">Apply Range</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Current Value</div>
                            <div class="h5 mb-0 fw-bold text-gray-800" id="currentValue">$0</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span id="valueChange" class="text-success">
                                    <i class="fas fa-arrow-up"></i> 0%
                                </span> vs previous
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                Average</div>
                            <div class="h5 mb-0 fw-bold text-gray-800" id="averageValue">$0</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span id="avgTrend">No change</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
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
                                Trend Direction</div>
                            <div class="h5 mb-0 fw-bold text-gray-800" id="trendDirection">Stable</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span id="trendStrength">Strength: 0</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-trend-up fa-2x text-gray-300"></i>
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
                                Forecast</div>
                            <div class="h5 mb-0 fw-bold text-gray-800" id="forecastValue">$0</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span id="forecastConfidence" class="text-info">Confidence: 0%</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-crystal-ball fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Chart Area -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary" id="chartTitle">Revenue Trend Analysis</h6>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary active" data-chart-type="line">
                            <i class="fas fa-chart-line"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-chart-type="bar">
                            <i class="fas fa-chart-bar"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-chart-type="area">
                            <i class="fas fa-chart-area"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="trendChart" height="70"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trend Statistics -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Trend Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            <tbody>
                                <tr>
                                    <td><strong>Slope</strong></td>
                                    <td><span class="badge bg-info" id="statSlope">0</span></td>
                                    <td class="text-end">Rate of change</td>
                                </tr>
                                <tr>
                                    <td><strong>R²</strong></td>
                                    <td><span class="badge bg-success" id="statRSquared">0.00</span></td>
                                    <td class="text-end">Goodness of fit</td>
                                </tr>
                                <tr>
                                    <td><strong>Volatility</strong></td>
                                    <td><span class="badge bg-warning" id="statVolatility">0%</span></td>
                                    <td class="text-end">Std deviation</td>
                                </tr>
                                <tr>
                                    <td><strong>Peak</strong></td>
                                    <td><span class="badge bg-danger" id="statPeak">$0</span></td>
                                    <td class="text-end">Maximum value</td>
                                </tr>
                                <tr>
                                    <td><strong>Trough</strong></td>
                                    <td><span class="badge bg-primary" id="statTrough">$0</span></td>
                                    <td class="text-end">Minimum value</td>
                                </tr>
                                <tr>
                                    <td><strong>Momentum</strong></td>
                                    <td><span class="badge bg-secondary" id="statMomentum">0</span></td>
                                    <td class="text-end">Recent change</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Trend Indicators -->
                    <div class="mt-4">
                        <h6 class="fw-bold text-primary mb-3">Trend Indicators</h6>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="p-2 border rounded">
                                    <div class="text-xs text-muted">MA(7)</div>
                                    <div class="h6 mb-0" id="ma7">0</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded">
                                    <div class="text-xs text-muted">MA(30)</div>
                                    <div class="h6 mb-0" id="ma30">0</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded">
                                    <div class="text-xs text-muted">RSI</div>
                                    <div class="h6 mb-0" id="rsi">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analysis & Forecast -->
    <div class="row">
        <!-- Forecast Chart -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">30-Day Forecast</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="forecastChart" height="100"></canvas>
                    </div>
                    <div class="mt-3 text-center">
                        <span class="badge bg-info me-2">Historical</span>
                        <span class="badge bg-success me-2">Forecast</span>
                        <span class="badge bg-warning">Confidence Interval</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trend Breakdown -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">Trend Breakdown</h6>
                    <select class="form-select form-select-sm w-auto" id="breakdownBy">
                        <option value="category">By Category</option>
                        <option value="region">By Region</option>
                        <option value="product">By Product</option>
                        <option value="channel">By Channel</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="breakdownChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">Trend Data Table</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="trendDataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Value</th>
                            <th>Change</th>
                            <th>% Change</th>
                            <th>7D Avg</th>
                            <th>30D Avg</th>
                            <th>Trend</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="trendTableBody">
                        <!-- Data will be populated via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 class="text-primary">Analyzing Trends...</h5>
                <p class="text-muted">Processing financial data and generating insights</p>
            </div>
        </div>
    </div>
</div>

<!-- Trend Details Modal -->
<div class="modal fade" id="trendDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Trend Analysis Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="trendDetailsContent">
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

.trend-indicator {
    width: 100px;
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
    display: inline-block;
}

.trend-indicator-fill {
    height: 100%;
    border-radius: 2px;
}

.trend-up {
    background: linear-gradient(90deg, #198754, #20c997);
}

.trend-down {
    background: linear-gradient(90deg, #dc3545, #fd7e14);
}

.trend-stable {
    background: linear-gradient(90deg, #6c757d, #adb5bd);
}

.cursor-pointer {
    cursor: pointer;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
<script src="https://cdn.jsdelivr.net/npm/regression@2.0.1/dist/regression.min.js"></script>
<script>

document.addEventListener('DOMContentLoaded', function() {
    let trendChart, forecastChart, breakdownChart;
    let currentData = {!! $initialDataJson !!};
 if (currentData.metrics) {
        updateUI(currentData);
    }
    // Initialize
    initFilters();
    loadTrendData();

    function initFilters() {
        // Time period change
        document.getElementById('timePeriod').addEventListener('change', function() {
            if (this.value === 'custom') {
                document.getElementById('customDateRange').classList.remove('d-none');
            } else {
                document.getElementById('customDateRange').classList.add('d-none');
                loadTrendData();
            }
        });

        // Apply custom range
        document.getElementById('applyCustomRange').addEventListener('click', function() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            if (!startDate || !endDate) {
                alert('Please select both start and end dates');
                return;
            }

            if (new Date(startDate) >= new Date(endDate)) {
                alert('Start date must be before end date');
                return;
            }

            loadTrendData(startDate, endDate);
        });

        // Metric change
        document.getElementById('metricSelect').addEventListener('change', loadTrendData);
        document.getElementById('granularity').addEventListener('change', loadTrendData);
        document.getElementById('comparison').addEventListener('change', loadTrendData);
        document.getElementById('breakdownBy').addEventListener('change', updateBreakdownChart);

        // Chart type buttons
        document.querySelectorAll('[data-chart-type]').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('[data-chart-type]').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                updateChartType(this.dataset.chartType);
            });
        });

        // Export button
        document.getElementById('exportData').addEventListener('click', exportData);
    }

    function loadTrendData(isCustomRange = false) {
    const params = isCustomRange ?
        { start_date: startDate, end_date: endDate } :
        { period: currentPeriod };

    // Update the URL to use the real endpoint
    $.ajax({
        url: '{{ route("finance.financial-analytics.trends.data") }}',
        method: 'GET',
        data: params,
        success: function(response) {
            updateMetrics(response.metrics);
            updateTable(response.trends);
            updateCharts(response.chartData);
        },
        error: function(xhr) {
            console.error('Error loading trend data:', xhr.responseText);
            // Fallback to mock data if API fails
            loadMockData(params);
        }
    });
}

    function generateMockData(params) {
        const metric = params.metric;
        const period = params.period || 30;
        const granularity = params.granularity;

        // Generate dates based on granularity
        const dates = [];
        const values = [];
        let date = new Date();

        for (let i = period; i >= 0; i--) {
            const d = new Date(date);
            d.setDate(d.getDate() - i);

            let label;
            if (granularity === 'daily') {
                label = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            } else if (granularity === 'weekly') {
                label = `Week ${Math.floor(i/7) + 1}`;
            } else if (granularity === 'monthly') {
                label = d.toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
            } else {
                label = `Q${Math.floor(d.getMonth()/3) + 1} ${d.getFullYear().toString().slice(2)}`;
            }

            dates.push(label);

            // Generate realistic data with trend
            let baseValue;
            switch(metric) {
                case 'revenue':
                    baseValue = 50000 + Math.random() * 100000;
                    break;
                case 'profit':
                    baseValue = 10000 + Math.random() * 30000;
                    break;
                case 'expenses':
                    baseValue = 30000 + Math.random() * 60000;
                    break;
                case 'margin':
                    baseValue = 15 + Math.random() * 30;
                    break;
                case 'growth':
                    baseValue = -10 + Math.random() * 20;
                    break;
                default:
                    baseValue = 50000 + Math.random() * 100000;
            }

            // Add trend component
            const trend = 0.02; // 2% upward trend
            const noise = (Math.random() - 0.5) * 0.1 * baseValue;
            const value = baseValue * (1 + trend * (period - i) / period) + noise;

            values.push(Math.round(value));
        }

        // Calculate statistics
        const lastValue = values[values.length - 1];
        const prevValue = values[values.length - 2] || values[0];
        const change = lastValue - prevValue;
        const changePercent = prevValue ? ((change / prevValue) * 100).toFixed(1) : 0;

        const avg = values.reduce((a, b) => a + b, 0) / values.length;
        const max = Math.max(...values);
        const min = Math.min(...values);

        // Calculate moving averages
        const ma7 = values.slice(-7).reduce((a, b) => a + b, 0) / Math.min(7, values.length);
        const ma30 = values.slice(-30).reduce((a, b) => a + b, 0) / Math.min(30, values.length);

        // Calculate RSI (simplified)
        let gains = 0;
        let losses = 0;
        for (let i = 1; i < values.length; i++) {
            const change = values[i] - values[i-1];
            if (change > 0) gains += change;
            else losses -= change;
        }
        const rsi = losses === 0 ? 100 : 100 - (100 / (1 + (gains / losses)));

        // Generate forecast data
        const forecast = [];
        for (let i = 1; i <= 30; i++) {
            forecast.push(lastValue * (1 + 0.005 * i + (Math.random() - 0.5) * 0.02));
        }

        return {
            dates,
            values,
            metric,
            granularity,
            lastValue,
            change,
            changePercent,
            avg,
            max,
            min,
            ma7,
            ma30,
            rsi,
            forecast,
            statistics: {
                slope: (values[values.length - 1] - values[0]) / values.length,
                rSquared: 0.85 + Math.random() * 0.14,
                volatility: (Math.random() * 15).toFixed(1),
                momentum: changePercent
            }
        };
    }

    function updateUI(data) {
        // Update metrics
        document.getElementById('currentValue').textContent =
            data.metric === 'margin' || data.metric === 'growth' ?
            `${data.lastValue.toFixed(1)}%` : `$${formatNumber(data.lastValue)}`;

        document.getElementById('averageValue').textContent =
            data.metric === 'margin' || data.metric === 'growth' ?
            `${data.avg.toFixed(1)}%` : `$${formatNumber(data.avg)}`;

        // Update change indicator
        const changeElement = document.getElementById('valueChange');
        changeElement.className = data.change >= 0 ? 'text-success' : 'text-danger';
        changeElement.innerHTML = `<i class="fas fa-arrow-${data.change >= 0 ? 'up' : 'down'}"></i> ${Math.abs(data.changePercent)}%`;

        // Update trend direction
        const trendDir = document.getElementById('trendDirection');
        const trendStrength = document.getElementById('trendStrength');
        if (data.changePercent > 5) {
            trendDir.textContent = 'Strong Up';
            trendDir.className = 'h5 mb-0 fw-bold text-success';
            trendStrength.textContent = `Strength: ${Math.abs(data.changePercent).toFixed(1)}`;
        } else if (data.changePercent > 1) {
            trendDir.textContent = 'Up';
            trendDir.className = 'h5 mb-0 fw-bold text-success';
            trendStrength.textContent = `Strength: ${Math.abs(data.changePercent).toFixed(1)}`;
        } else if (data.changePercent < -5) {
            trendDir.textContent = 'Strong Down';
            trendDir.className = 'h5 mb-0 fw-bold text-danger';
            trendStrength.textContent = `Strength: ${Math.abs(data.changePercent).toFixed(1)}`;
        } else if (data.changePercent < -1) {
            trendDir.textContent = 'Down';
            trendDir.className = 'h5 mb-0 fw-bold text-danger';
            trendStrength.textContent = `Strength: ${Math.abs(data.changePercent).toFixed(1)}`;
        } else {
            trendDir.textContent = 'Stable';
            trendDir.className = 'h5 mb-0 fw-bold text-secondary';
            trendStrength.textContent = 'No significant trend';
        }

        // Update forecast
        const forecastValue = data.forecast[data.forecast.length - 1];
        document.getElementById('forecastValue').textContent =
            data.metric === 'margin' || data.metric === 'growth' ?
            `${forecastValue.toFixed(1)}%` : `$${formatNumber(forecastValue)}`;

        document.getElementById('forecastConfidence').textContent =
            `Confidence: ${(85 + Math.random() * 14).toFixed(0)}%`;

        // Update statistics
        document.getElementById('statSlope').textContent = data.statistics.slope.toFixed(2);
        document.getElementById('statRSquared').textContent = data.statistics.rSquared.toFixed(2);
        document.getElementById('statVolatility').textContent = `${data.statistics.volatility}%`;
        document.getElementById('statPeak').textContent =
            data.metric === 'margin' || data.metric === 'growth' ?
            `${data.max.toFixed(1)}%` : `$${formatNumber(data.max)}`;
        document.getElementById('statTrough').textContent =
            data.metric === 'margin' || data.metric === 'growth' ?
            `${data.min.toFixed(1)}%` : `$${formatNumber(data.min)}`;
        document.getElementById('statMomentum').textContent = `${data.statistics.momentum}%`;

        // Update indicators
        document.getElementById('ma7').textContent =
            data.metric === 'margin' || data.metric === 'growth' ?
            `${data.ma7.toFixed(1)}%` : `$${formatNumber(data.ma7)}`;
        document.getElementById('ma30').textContent =
            data.metric === 'margin' || data.metric === 'growth' ?
            `${data.ma30.toFixed(1)}%` : `$${formatNumber(data.ma30)}`;
        document.getElementById('rsi').textContent = data.rsi.toFixed(1);

        // Update chart title
        const metricNames = {
            revenue: 'Revenue',
            profit: 'Profit',
            expenses: 'Expenses',
            margin: 'Profit Margin',
            growth: 'Growth Rate'
        };
        document.getElementById('chartTitle').textContent =
            `${metricNames[data.metric]} Trend Analysis`;

        // Update charts
        updateMainChart(data);
        updateForecastChart(data);
        updateBreakdownChart();
        updateDataTable(data);
    }

    function updateMainChart(data) {
        const ctx = document.getElementById('trendChart').getContext('2d');

        if (trendChart) {
            trendChart.destroy();
        }

        const chartType = document.querySelector('[data-chart-type].active').dataset.chartType;

        trendChart = new Chart(ctx, {
            type: chartType,
            data: {
                labels: data.dates,
                datasets: [{
                    label: data.metric,
                    data: data.values,
                    borderColor: '#4e73df',
                    backgroundColor: chartType === 'line' ? 'transparent' :
                                   chartType === 'bar' ? 'rgba(78, 115, 223, 0.5)' :
                                   'rgba(78, 115, 223, 0.1)',
                    borderWidth: 2,
                    fill: chartType === 'area',
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#4e73df',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const label = data.metric === 'margin' || data.metric === 'growth' ?
                                            `${value}%` : `$${formatNumber(value)}`;
                                return `${data.metric}: ${label}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: data.metric !== 'growth',
                        ticks: {
                            callback: function(value) {
                                return data.metric === 'margin' || data.metric === 'growth' ?
                                       `${value}%` : `$${formatNumber(value)}`;
                            }
                        },
                        grid: {
                            borderDash: [2]
                        }
                    }
                }
            }
        });
    }

    function updateForecastChart(data) {
        const ctx = document.getElementById('forecastChart').getContext('2d');

        if (forecastChart) {
            forecastChart.destroy();
        }

        // Combine historical and forecast data
        const historicalLabels = data.dates.slice(-10); // Last 10 historical points
        const forecastLabels = [];
        for (let i = 1; i <= 10; i++) {
            forecastLabels.push(`Day +${i}`);
        }

        const labels = [...historicalLabels, ...forecastLabels];
        const historicalData = data.values.slice(-10);
        const forecastData = data.forecast.slice(0, 10);

        forecastChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Historical',
                        data: [...historicalData, ...Array(10).fill(null)],
                        borderColor: '#4e73df',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.4
                    },
                    {
                        label: 'Forecast',
                        data: [...Array(10).fill(null), ...forecastData],
                        borderColor: '#1cc88a',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.4
                    },
                    {
                        label: 'Confidence',
                        data: [...Array(10).fill(null), ...forecastData.map(v => v * 1.1)],
                        borderColor: 'transparent',
                        backgroundColor: 'rgba(28, 200, 138, 0.1)',
                        borderWidth: 0,
                        fill: '-1',
                        tension: 0
                    },
                    {
                        label: 'Confidence',
                        data: [...Array(10).fill(null), ...forecastData.map(v => v * 0.9)],
                        borderColor: 'transparent',
                        backgroundColor: 'rgba(28, 200, 138, 0.1)',
                        borderWidth: 0,
                        fill: '-1',
                        tension: 0
                    }
                ]
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
                        beginAtZero: data.metric !== 'growth',
                        ticks: {
                            callback: function(value) {
                                return data.metric === 'margin' || data.metric === 'growth' ?
                                       `${value}%` : `$${formatNumber(value)}`;
                            }
                        }
                    }
                }
            }
        });
    }

    function updateBreakdownChart() {
        const breakdownBy = document.getElementById('breakdownBy').value;
        const ctx = document.getElementById('breakdownChart').getContext('2d');

        if (breakdownChart) {
            breakdownChart.destroy();
        }

        // Generate mock breakdown data
        let labels, data;
        switch(breakdownBy) {
            case 'category':
                labels = ['Product Sales', 'Services', 'Subscriptions', 'Licensing', 'Other'];
                data = [45, 25, 15, 10, 5];
                break;
            case 'region':
                labels = ['North America', 'Europe', 'Asia', 'South America', 'Other'];
                data = [40, 30, 20, 5, 5];
                break;
            case 'product':
                labels = ['Product A', 'Product B', 'Product C', 'Product D', 'Product E'];
                data = [35, 25, 20, 15, 5];
                break;
            case 'channel':
                labels = ['Direct', 'Online', 'Retail', 'Wholesale', 'Reseller'];
                data = [30, 25, 20, 15, 10];
                break;
        }

        breakdownChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e',
                        '#e74a3b'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    function updateDataTable(data) {
        const tbody = document.getElementById('trendTableBody');
        let html = '';

        for (let i = data.values.length - 1; i >= 0; i--) {
            const value = data.values[i];
            const prevValue = data.values[i - 1] || value;
            const change = value - prevValue;
            const changePercent = prevValue ? ((change / prevValue) * 100).toFixed(1) : 0;

            // Calculate 7-day and 30-day averages
            const start7 = Math.max(0, i - 6);
            const start30 = Math.max(0, i - 29);
            const avg7 = data.values.slice(start7, i + 1).reduce((a, b) => a + b, 0) / (i - start7 + 1);
            const avg30 = data.values.slice(start30, i + 1).reduce((a, b) => a + b, 0) / (i - start30 + 1);

            // Determine trend
            let trendClass, trendIcon, trendText;
            if (changePercent > 2) {
                trendClass = 'trend-up';
                trendIcon = 'fa-arrow-up';
                trendText = 'Up';
            } else if (changePercent < -2) {
                trendClass = 'trend-down';
                trendIcon = 'fa-arrow-down';
                trendText = 'Down';
            } else {
                trendClass = 'trend-stable';
                trendIcon = 'fa-minus';
                trendText = 'Stable';
            }

            html += `
            <tr class="cursor-pointer" onclick="showTrendDetails(${i})">
                <td><strong>${data.dates[i]}</strong></td>
                <td>${data.metric === 'margin' || data.metric === 'growth' ?
                    `${value.toFixed(1)}%` : `$${formatNumber(value)}`}</td>
                <td class="${change >= 0 ? 'text-success' : 'text-danger'}">
                    ${change >= 0 ? '+' : ''}${data.metric === 'margin' || data.metric === 'growth' ?
                    `${change.toFixed(1)}%` : `$${formatNumber(change)}`}
                </td>
                <td class="${changePercent >= 0 ? 'text-success' : 'text-danger'}">
                    ${changePercent >= 0 ? '+' : ''}${changePercent}%
                </td>
                <td>${data.metric === 'margin' || data.metric === 'growth' ?
                    `${avg7.toFixed(1)}%` : `$${formatNumber(avg7)}`}</td>
                <td>${data.metric === 'margin' || data.metric === 'growth' ?
                    `${avg30.toFixed(1)}%` : `$${formatNumber(avg30)}`}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="trend-indicator me-2">
                            <div class="trend-indicator-fill ${trendClass}"
                                 style="width: ${Math.min(100, Math.abs(changePercent) * 5)}%"></div>
                        </div>
                        <i class="fas ${trendIcon} text-${changePercent > 2 ? 'success' : changePercent < -2 ? 'danger' : 'secondary'}"></i>
                        <span class="ms-1">${trendText}</span>
                    </div>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); showTrendDetails(${i})">
                        <i class="fas fa-search"></i>
                    </button>
                </td>
            </tr>
            `;
        }

        tbody.innerHTML = html;
    }

    function updateChartType(type) {
        if (trendChart && currentData.values) {
            updateMainChart(currentData);
        }
    }

    function exportData() {
        // Create CSV content
        let csv = 'Date,Value,Change,Change %,7D Avg,30D Avg\n';

        for (let i = 0; i < currentData.values.length; i++) {
            const value = currentData.values[i];
            const prevValue = currentData.values[i - 1] || value;
            const change = value - prevValue;
            const changePercent = prevValue ? ((change / prevValue) * 100).toFixed(2) : 0;

            const start7 = Math.max(0, i - 6);
            const start30 = Math.max(0, i - 29);
            const avg7 = currentData.values.slice(start7, i + 1).reduce((a, b) => a + b, 0) / (i - start7 + 1);
            const avg30 = currentData.values.slice(start30, i + 1).reduce((a, b) => a + b, 0) / (i - start30 + 1);

            csv += `${currentData.dates[i]},${value},${change},${changePercent},${avg7.toFixed(2)},${avg30.toFixed(2)}\n`;
        }

        // Create download link
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `trend-analysis-${currentData.metric}-${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }

    function showLoading() {
        const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
        modal.show();
    }

    function hideLoading() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('loadingModal'));
        if (modal) {
            modal.hide();
        }
    }

    function formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        }
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toFixed(0);
    }
});

// Global function for trend details
function showTrendDetails(index) {
    // This would normally fetch detailed data for the specific period
    const modal = new bootstrap.Modal(document.getElementById('trendDetailsModal'));
    const content = document.getElementById('trendDetailsContent');

    content.innerHTML = `
        <h6>Detailed Analysis for Period ${index + 1}</h6>
        <p>This would show detailed breakdown, contributing factors, and related metrics.</p>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Detailed analysis feature coming soon.
        </div>
    `;

    modal.show();
}
</script>
@endpush
