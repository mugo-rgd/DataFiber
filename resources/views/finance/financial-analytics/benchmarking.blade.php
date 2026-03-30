{{-- resources/views/finance/financial-analytics/benchmarking.blade.php --}}
@extends('layouts.app')

@section('title', 'Financial Benchmarking')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center py-3">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-bar text-primary me-2"></i>Financial Benchmarking
        </h1>
        <div>
            <button class="btn btn-outline-primary" id="exportBenchmarking">
                <i class="fas fa-download me-1"></i>Export Report
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Benchmark Period</label>
                    <select class="form-select" id="benchmarkPeriod">
                        <option value="current_month">Current Month</option>
                        <option value="last_quarter" selected>Last Quarter</option>
                        <option value="ytd">Year to Date</option>
                        <option value="last_year">Last Year</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Compare Against</label>
                    <select class="form-select" id="compareAgainst">
                        <option value="industry_average">Industry Average</option>
                        <option value="company_targets" selected>Company Targets</option>
                        <option value="previous_period">Previous Period</option>
                        <option value="top_performers">Top Performers</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Metric Category</label>
                    <select class="form-select" id="metricCategory">
                        <option value="all">All Categories</option>
                        <option value="revenue">Revenue</option>
                        <option value="profitability">Profitability</option>
                        <option value="liquidity">Liquidity</option>
                        <option value="efficiency">Efficiency</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Score -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Overall Benchmark Score</h6>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <div class="display-4 fw-bold" id="overallScore">0</div>
                            <div class="text-muted">Out of 100</div>
                            <div class="mt-2">
                                <span class="badge bg-success" id="scoreRating">Poor</span>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="progress mb-3" style="height: 25px;">
                                <div class="progress-bar bg-success" id="scoreProgress" role="progressbar" style="width: 0%">
                                    <span class="progress-text">0%</span>
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-3">
                                    <div class="text-muted small">Revenue</div>
                                    <div class="h5 mb-0" id="revenueScore">0</div>
                                </div>
                                <div class="col-3">
                                    <div class="text-muted small">Profitability</div>
                                    <div class="h5 mb-0" id="profitabilityScore">0</div>
                                </div>
                                <div class="col-3">
                                    <div class="text-muted small">Liquidity</div>
                                    <div class="h5 mb-0" id="liquidityScore">0</div>
                                </div>
                                <div class="col-3">
                                    <div class="text-muted small">Efficiency</div>
                                    <div class="h5 mb-0" id="efficiencyScore">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Benchmarking Cards -->
    <div class="row mb-4">
        @foreach($groupedComparison ?? [] as $category => $metrics)
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $category }} Benchmarks</h6>
                    <span class="badge" id="{{ strtolower($category) }}Badge">0%</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Current</th>
                                    <th>Target</th>
                                    <th>Gap</th>
                                    <th>Achievement</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($metrics as $metricName => $data)
                                @php
                                    $statusClass = match($data['status'] ?? 'below') {
                                        'exceeded' => 'success',
                                        'met' => 'primary',
                                        'near' => 'warning',
                                        default => 'danger'
                                    };

                                    $statusIcon = match($data['status'] ?? 'below') {
                                        'exceeded' => 'fa-check-circle',
                                        'met' => 'fa-check',
                                        'near' => 'fa-exclamation-circle',
                                        default => 'fa-times-circle'
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ str_replace('_', ' ', ucfirst($metricName)) }}</strong>
                                    </td>
                                    <td>
                                        @if(str_contains($metricName, 'rate') || str_contains($metricName, 'margin') || str_contains($metricName, 'ratio'))
                                            {{ number_format($data['current'] ?? 0, 1) }}%
                                        @else
                                            ${{ number_format($data['current'] ?? 0, 0) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(str_contains($metricName, 'rate') || str_contains($metricName, 'margin') || str_contains($metricName, 'ratio'))
                                            {{ number_format($data['target'] ?? 0, 1) }}%
                                        @else
                                            ${{ number_format($data['target'] ?? 0, 0) }}
                                        @endif
                                    </td>
                                    <td class="{{ ($data['gap'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                        @if(str_contains($metricName, 'rate') || str_contains($metricName, 'margin') || str_contains($metricName, 'ratio'))
                                            {{ ($data['gap'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($data['gap'] ?? 0, 1) }}%
                                        @else
                                            {{ ($data['gap'] ?? 0) >= 0 ? '+' : '' }}${{ number_format($data['gap'] ?? 0, 0) }}
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $statusClass }}"
                                                     role="progressbar"
                                                     style="width: {{ min(100, $data['achievement'] ?? 0) }}%">
                                                </div>
                                            </div>
                                            <span class="small">{{ number_format($data['achievement'] ?? 0, 0) }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $statusClass }}">
                                            <i class="fas {{ $statusIcon }} me-1"></i>
                                            {{ ucfirst($data['status'] ?? 'below') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Performance Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Performance vs Targets</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="benchmarkChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Insights & Recommendations -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Key Insights & Recommendations</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-left-success mb-3">
                                <div class="card-body">
                                    <h6 class="card-title text-success">
                                        <i class="fas fa-check-circle me-2"></i>Strengths
                                    </h6>
                                    <ul class="mb-0">
                                        <li id="strength1">Loading strengths...</li>
                                        <li id="strength2"></li>
                                        <li id="strength3"></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-left-danger mb-3">
                                <div class="card-body">
                                    <h6 class="card-title text-danger">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Areas for Improvement
                                    </h6>
                                    <ul class="mb-0">
                                        <li id="improvement1">Loading improvement areas...</li>
                                        <li id="improvement2"></li>
                                        <li id="improvement3"></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6 class="text-primary mb-3">Action Plan</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Priority</th>
                                        <th>Action Item</th>
                                        <th>Responsible</th>
                                        <th>Timeline</th>
                                        <th>Expected Impact</th>
                                    </tr>
                                </thead>
                                <tbody id="actionPlan">
                                    <tr>
                                        <td colspan="5" class="text-center">Loading action plan...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Industry Comparison -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Industry Comparison</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-muted small mb-2">Your Position in Market</h6>
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-grow-1 me-3">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-info" id="marketPositionBar" style="width: 50%"></div>
                                </div>
                            </div>
                            <div class="fw-bold" id="marketPosition">50th</div>
                        </div>
                        <div class="text-muted small">Percentile rank in industry</div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted small mb-2">Key Industry Benchmarks</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Metric</th>
                                        <th>Industry Avg</th>
                                        <th>Top 25%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Collection Rate</td>
                                        <td>85%</td>
                                        <td class="text-success">95%</td>
                                    </tr>
                                    <tr>
                                        <td>Net Margin</td>
                                        <td>18%</td>
                                        <td class="text-success">25%</td>
                                    </tr>
                                    <tr>
                                        <td>DSO</td>
                                        <td>40 days</td>
                                        <td class="text-success">25 days</td>
                                    </tr>
                                    <tr>
                                        <td>Revenue Growth</td>
                                        <td>12%</td>
                                        <td class="text-success">20%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Industry benchmarks are based on telecom sector averages. Adjust targets based on your specific market position.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Benchmarking Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Export Format</label>
                    <select class="form-select" id="exportFormat">
                        <option value="pdf">PDF Document</option>
                        <option value="excel">Excel Spreadsheet</option>
                        <option value="csv">CSV File</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Include</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="includeCharts" checked>
                        <label class="form-check-label" for="includeCharts">Charts & Graphs</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="includeData" checked>
                        <label class="form-check-label" for="includeData">Detailed Data</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="includeActionPlan" checked>
                        <label class="form-check-label" for="includeActionPlan">Action Plan</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmExport">Export</button>
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

.progress-text {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    font-weight: bold;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.badge.bg-success { background-color: #1cc88a !important; }
.badge.bg-primary { background-color: #4e73df !important; }
.badge.bg-warning { background-color: #f6c23e !important; }
.badge.bg-danger { background-color: #e74a3b !important; }
.badge.bg-info { background-color: #36b9cc !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let benchmarkChart;

    // Initialize
    loadBenchmarkData();

    // Filter changes
    document.getElementById('benchmarkPeriod').addEventListener('change', loadBenchmarkData);
    document.getElementById('compareAgainst').addEventListener('change', loadBenchmarkData);
    document.getElementById('metricCategory').addEventListener('change', updateChart);

    // Export button
    document.getElementById('exportBenchmarking').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('exportModal'));
        modal.show();
    });

    document.getElementById('confirmExport').addEventListener('click', exportReport);

    function loadBenchmarkData() {
        // In a real application, you would fetch data from your controller
        // For now, we'll use the data passed from PHP and simulate loading

        updateScores();
        updateInsights();
        updateChart();
        updateActionPlan();
    }

    function updateScores() {
        // Calculate overall score from the PHP data
        let totalAchievement = 0;
        let metricCount = 0;

        @if(isset($groupedComparison))
            @foreach($groupedComparison as $category => $metrics)
                @foreach($metrics as $metricName => $data)
                    totalAchievement += {{ $data['achievement'] ?? 0 }};
                    metricCount++;
                @endforeach
            @endforeach
        @endif

        const overallScore = metricCount > 0 ? Math.round(totalAchievement / metricCount) : 0;

        // Update overall score
        document.getElementById('overallScore').textContent = overallScore;
        document.getElementById('scoreProgress').style.width = overallScore + '%';
        document.getElementById('scoreProgress').querySelector('.progress-text').textContent = overallScore + '%';

        // Update score rating
        let rating, ratingClass;
        if (overallScore >= 90) {
            rating = 'Excellent';
            ratingClass = 'success';
        } else if (overallScore >= 75) {
            rating = 'Good';
            ratingClass = 'primary';
        } else if (overallScore >= 60) {
            rating = 'Fair';
            ratingClass = 'warning';
        } else {
            rating = 'Needs Improvement';
            ratingClass = 'danger';
        }

        const ratingBadge = document.getElementById('scoreRating');
        ratingBadge.textContent = rating;
        ratingBadge.className = 'badge bg-' + ratingClass;

        // Calculate category scores (simplified)
        @if(isset($groupedComparison))
            @php
                $categoryScores = [];
                foreach($groupedComparison as $category => $metrics) {
                    $catTotal = 0;
                    $catCount = 0;
                    foreach($metrics as $data) {
                        $catTotal += $data['achievement'] ?? 0;
                        $catCount++;
                    }
                    $categoryScores[$category] = $catCount > 0 ? round($catTotal / $catCount) : 0;
                }
            @endphp

            @foreach($categoryScores as $category => $score)
                document.getElementById('{{ strtolower($category) }}Score').textContent = {{ $score }};
                document.getElementById('{{ strtolower($category) }}Badge').textContent = {{ $score }} + '%';
                document.getElementById('{{ strtolower($category) }}Badge').className = 'badge bg-' +
                    ({{ $score }} >= 90 ? 'success' :
                     {{ $score }} >= 75 ? 'primary' :
                     {{ $score }} >= 60 ? 'warning' : 'danger');
            @endforeach
        @endif
    }

    function updateInsights() {
        // Generate strengths and improvement areas based on data
        const strengths = [];
        const improvements = [];

        @if(isset($groupedComparison))
            @foreach($groupedComparison as $category => $metrics)
                @foreach($metrics as $metricName => $data)
                    @if(($data['achievement'] ?? 0) >= 100)
                        @php
                            $metricLabel = str_replace('_', ' ', ucfirst($metricName));
                            $strength = $metricLabel . " exceeds target by " . ($data['gap'] ?? 0) . "%";
                        @endphp
                        strengths.push("{{ $strength }}");
                    @elseif(($data['achievement'] ?? 0) < 70)
                        @php
                            $metricLabel = str_replace('_', ' ', ucfirst($metricName));
                            $improvement = $metricLabel . " needs improvement (currently at " . ($data['achievement'] ?? 0) . "% of target)";
                        @endphp
                        improvements.push("{{ $improvement }}");
                    @endif
                @endforeach
            @endforeach
        @endif

        // Add default if no specific insights
        if (strengths.length === 0) {
            strengths.push("Strong customer retention rates");
            strengths.push("Efficient invoice processing");
        }

        if (improvements.length === 0) {
            improvements.push("Collection rate below industry average");
            improvements.push("Revenue growth needs acceleration");
            improvements.push("Days Sales Outstanding (DSO) could be improved");
        }

        // Update DOM
        document.getElementById('strength1').textContent = strengths[0] || 'No specific strengths identified';
        document.getElementById('strength2').textContent = strengths[1] || '';
        document.getElementById('strength3').textContent = strengths[2] || '';

        document.getElementById('improvement1').textContent = improvements[0] || 'All targets being met';
        document.getElementById('improvement2').textContent = improvements[1] || '';
        document.getElementById('improvement3').textContent = improvements[2] || '';
    }

    function updateChart() {
        const ctx = document.getElementById('benchmarkChart').getContext('2d');
        const category = document.getElementById('metricCategory').value;

        if (benchmarkChart) {
            benchmarkChart.destroy();
        }

        // Prepare data based on selected category
        let labels = [];
        let currentValues = [];
        let targetValues = [];

        @if(isset($groupedComparison))
            @foreach($groupedComparison as $cat => $metrics)
                @if($category === 'all' || strtolower($category) === strtolower($cat))
                    @foreach($metrics as $metricName => $data)
                        @php
                            $label = str_replace('_', ' ', ucfirst($metricName));
                            if (strlen($label) > 20) {
                                $label = substr($label, 0, 20) . '...';
                            }
                        @endphp
                        labels.push("{{ $label }}");
                        currentValues.push({{ $data['current'] ?? 0 }});
                        targetValues.push({{ $data['target'] ?? 0 }});
                    @endforeach
                @endif
            @endforeach
        @endif

        // If no specific category selected or no data, show sample
        if (labels.length === 0) {
            labels = ['Collection Rate', 'Net Margin', 'Current Ratio', 'DSO', 'Revenue Growth'];
            currentValues = [85, 18, 1.8, 45, 12];
            targetValues = [90, 20, 2.0, 30, 15];
        }

        benchmarkChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Current',
                        data: currentValues,
                        backgroundColor: '#4e73df',
                        borderColor: '#4e73df',
                        borderWidth: 1
                    },
                    {
                        label: 'Target',
                        data: targetValues,
                        backgroundColor: '#1cc88a',
                        borderColor: '#1cc88a',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }

                                const index = context.dataIndex;
                                const metricName = labels[index].toLowerCase();

                                if (metricName.includes('rate') || metricName.includes('margin') ||
                                    metricName.includes('growth') || metricName.includes('ratio')) {
                                    label += context.parsed.y + '%';
                                } else if (metricName.includes('dso') || metricName.includes('days')) {
                                    label += context.parsed.y + ' days';
                                } else {
                                    label += '$' + context.parsed.y.toLocaleString();
                                }

                                return label;
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
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                // Determine if this is a percentage or currency value
                                const activeDataset = this.chart.data.datasets[0];
                                const datasetIndex = this.chart.scales.x._activeElements?.[0]?.datasetIndex || 0;
                                const dataIndex = this.chart.scales.x._activeElements?.[0]?.index || 0;

                                if (dataIndex < labels.length) {
                                    const metricName = labels[dataIndex].toLowerCase();
                                    if (metricName.includes('rate') || metricName.includes('margin') ||
                                        metricName.includes('growth') || metricName.includes('ratio')) {
                                        return value + '%';
                                    } else if (metricName.includes('dso') || metricName.includes('days')) {
                                        return value + ' days';
                                    }
                                }
                                return value;
                            }
                        }
                    }
                }
            }
        });
    }

    function updateActionPlan() {
        const actionPlan = document.getElementById('actionPlan');

        // Generate action plan based on improvement areas
        const actions = [];

        @if(isset($groupedComparison))
            @foreach($groupedComparison as $category => $metrics)
                @foreach($metrics as $metricName => $data)
                    @if(($data['achievement'] ?? 0) < 80)
                        @php
                            $metricLabel = str_replace('_', ' ', ucfirst($metricName));
                            $priority = ($data['achievement'] ?? 0) < 60 ? 'High' :
                                       (($data['achievement'] ?? 0) < 75 ? 'Medium' : 'Low');

                            $action = match($metricName) {
                                'collection_rate' => 'Implement automated payment reminders and offer early payment discounts',
                                'net_margin' => 'Review cost structure and negotiate better vendor terms',
                                'current_ratio' => 'Optimize working capital management',
                                'dsos' => 'Strengthen credit control and collection procedures',
                                'revenue_growth' => 'Develop new customer acquisition strategy',
                                'customer_acquisition_cost' => 'Optimize marketing channels and improve conversion rates',
                                'customer_lifetime_value' => 'Enhance customer retention programs',
                                default => 'Develop improvement plan for ' . $metricLabel
                            };

                            $responsible = match($category) {
                                'Revenue' => 'Sales Director',
                                'Profitability' => 'Finance Manager',
                                'Liquidity' => 'CFO',
                                'Customer' => 'Marketing Head',
                                default => 'Department Head'
                            };

                            $timeline = ($data['achievement'] ?? 0) < 60 ? '30 days' :
                                       (($data['achievement'] ?? 0) < 75 ? '60 days' : '90 days');

                            $impact = ($data['gap'] ?? 0) < 0 ? 'Improve by ' . abs($data['gap']) . '%' : 'Meet target';
                        @endphp

                        actions.push({
                            priority: '{{ $priority }}',
                            action: '{{ $action }}',
                            responsible: '{{ $responsible }}',
                            timeline: '{{ $timeline }}',
                            impact: '{{ $impact }}'
                        });
                    @endif
                @endforeach
            @endforeach
        @endif

        // Add default actions if none generated
        if (actions.length === 0) {
            actions.push(
                {
                    priority: 'Medium',
                    action: 'Maintain current performance levels and monitor trends',
                    responsible: 'All Department Heads',
                    timeline: 'Ongoing',
                    impact: 'Sustain current achievements'
                },
                {
                    priority: 'Low',
                    action: 'Explore opportunities for incremental improvements',
                    responsible: 'Strategy Team',
                    timeline: '90 days',
                    impact: 'Identify 5% improvement opportunities'
                }
            );
        }

        // Sort by priority (High > Medium > Low)
        actions.sort((a, b) => {
            const priorityOrder = { High: 3, Medium: 2, Low: 1 };
            return priorityOrder[b.priority] - priorityOrder[a.priority];
        });

        // Update table
        let html = '';
        actions.forEach((action, index) => {
            const priorityClass = action.priority === 'High' ? 'danger' :
                                 action.priority === 'Medium' ? 'warning' : 'success';

            html += `
            <tr>
                <td>
                    <span class="badge bg-${priorityClass}">${action.priority}</span>
                </td>
                <td>${action.action}</td>
                <td>${action.responsible}</td>
                <td>${action.timeline}</td>
                <td>${action.impact}</td>
            </tr>
            `;
        });

        actionPlan.innerHTML = html;

        // Update market position based on overall score
        const overallScore = parseInt(document.getElementById('overallScore').textContent);
        let marketPosition, positionPercent;

        if (overallScore >= 90) {
            marketPosition = 'Top 10';
            positionPercent = 90;
        } else if (overallScore >= 75) {
            marketPosition = 'Top 25';
            positionPercent = 75;
        } else if (overallScore >= 50) {
            marketPosition = 'Average';
            positionPercent = 50;
        } else {
            marketPosition = 'Below Average';
            positionPercent = 25;
        }

        document.getElementById('marketPosition').textContent = marketPosition;
        document.getElementById('marketPositionBar').style.width = positionPercent + '%';
    }

    function exportReport() {
        const format = document.getElementById('exportFormat').value;
        const includeCharts = document.getElementById('includeCharts').checked;
        const includeData = document.getElementById('includeData').checked;
        const includeActionPlan = document.getElementById('includeActionPlan').checked;

        // Show loading state
        const exportBtn = document.getElementById('confirmExport');
        const originalText = exportBtn.innerHTML;
        exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Exporting...';
        exportBtn.disabled = true;

        // Simulate export process
        setTimeout(() => {
            // Create a simple CSV export for demonstration
            let csvContent = "Financial Benchmarking Report\n";
            csvContent += "Generated: " + new Date().toLocaleDateString() + "\n\n";

            @if(isset($groupedComparison))
                csvContent += "Category,Metric,Current,Target,Gap,Achievement %,Status\n";

                @foreach($groupedComparison as $category => $metrics)
                    @foreach($metrics as $metricName => $data)
                        @php
                            $metricLabel = str_replace('_', ' ', ucfirst($metricName));
                            $current = $data['current'] ?? 0;
                            $target = $data['target'] ?? 0;
                            $gap = $data['gap'] ?? 0;
                            $achievement = $data['achievement'] ?? 0;
                            $status = $data['status'] ?? 'below';
                        @endphp
                        csvContent += `"{{ $category }}","{{ $metricLabel }}",{{ $current }},{{ $target }},{{ $gap }},{{ $achievement }},"{{ $status }}"\n`;
                    @endforeach
                @endforeach
            @endif

            // Create download link
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `benchmarking_report_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            // Reset button
            exportBtn.innerHTML = originalText;
            exportBtn.disabled = false;

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
            modal.hide();

            // Show success message
            showAlert('Report exported successfully!', 'success');

        }, 1500);
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

    // Initial load
    updateChart();
});
</script>
@endpush
