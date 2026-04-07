@extends('layouts.app')

@section('title', 'Predictive Analytics')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="page-title mb-0">
                            <i class="fas fa-chart-line text-primary me-2"></i>Predictive Analytics
                        </h4>
                        <p class="text-muted mb-0">AI-powered predictions and forecasts for debt management</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('finance.ai.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                        </a>
                        <button onclick="refreshPredictions()" class="btn btn-outline-primary">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Prediction Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Expected Collections (Next 30 Days)</h6>
                            <h3 class="mb-0" id="expectedCollections">$0</h3>
                            <small class="text-white-50">Based on historical patterns</small>
                        </div>
                        <i class="fas fa-chart-line fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Predicted Default Risk</h6>
                            <h3 class="mb-0" id="defaultRisk">0%</h3>
                            <small class="text-white-50">Overall portfolio risk</small>
                        </div>
                        <i class="fas fa-chart-pie fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">High-Risk Customers</h6>
                            <h3 class="mb-0" id="highRiskCount">0</h3>
                            <small class="text-white-50">Require immediate attention</small>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Cash Flow Forecast</h6>
                            <h3 class="mb-0" id="cashFlowForecast">$0</h3>
                            <small class="text-white-50">Next 90 days projection</small>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Prediction Charts -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>Collection Forecast (Next 90 Days)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="forecastChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie text-success me-2"></i>Risk Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="riskChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Risk Analysis -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-users text-danger me-2"></i>High-Risk Customers Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="riskTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Outstanding (USD)</th>
                                    <th>Outstanding (KSH)</th>
                                    <th>Overdue Days</th>
                                    <th>Risk Score</th>
                                    <th>Predicted Default</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="riskTableBody">
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <br>Loading predictions...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Insights -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-brain text-warning me-2"></i>AI-Powered Insights & Recommendations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Key Predictions</h6>
                            <ul id="keyPredictions">
                                <li>Loading predictions...</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">Recommended Actions</h6>
                            <ul id="recommendedActions">
                                <li>Loading recommendations...</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Simulate AI predictions (replace with actual API calls)
function loadPredictions() {
    // Simulate API call
    setTimeout(() => {
        // Update summary cards
        document.getElementById('expectedCollections').innerHTML = '$' + (Math.random() * 500000 + 100000).toFixed(0);
        document.getElementById('defaultRisk').innerHTML = (Math.random() * 30 + 5).toFixed(1) + '%';
        document.getElementById('highRiskCount').innerHTML = Math.floor(Math.random() * 20 + 3);
        document.getElementById('cashFlowForecast').innerHTML = '$' + (Math.random() * 1000000 + 200000).toFixed(0);

        // Generate forecast data
        const forecastLabels = [];
        const forecastData = [];
        for (let i = 0; i < 90; i++) {
            if (i % 7 === 0) {
                forecastLabels.push(`Day ${i+1}`);
                forecastData.push(Math.random() * 50000 + 5000);
            }
        }

        // Create forecast chart
        const forecastCtx = document.getElementById('forecastChart').getContext('2d');
        new Chart(forecastCtx, {
            type: 'line',
            data: {
                labels: forecastLabels,
                datasets: [{
                    label: 'Predicted Collections ($)',
                    data: forecastData,
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Create risk distribution chart
        const riskCtx = document.getElementById('riskChart').getContext('2d');
        new Chart(riskCtx, {
            type: 'doughnut',
            data: {
                labels: ['Low Risk', 'Medium Risk', 'High Risk', 'Critical'],
                datasets: [{
                    data: [45, 30, 15, 10],
                    backgroundColor: ['#28a745', '#ffc107', '#fd7e14', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Generate risk table data
        const riskData = [
            { name: 'MINISTRY OF ICT', usd: 11503102.05, ksh: 0, days: 45, riskScore: 92, defaultProb: 85 },
            { name: 'KENGEN PLC', usd: 9253248, ksh: 0, days: 42, riskScore: 88, defaultProb: 78 },
            { name: 'NATIONAL CEMENT COMPANY LTD', usd: 0, ksh: 963900, days: 40, riskScore: 75, defaultProb: 65 },
            { name: 'JAMII TELECOMMUNICATIONS LTD', usd: 255000.51, ksh: 0, days: 38, riskScore: 70, defaultProb: 58 },
            { name: 'SAFARICOM PLC', usd: 373155.09, ksh: 0, days: 35, riskScore: 65, defaultProb: 52 }
        ];

        const tbody = document.getElementById('riskTableBody');
        tbody.innerHTML = '';

        riskData.forEach(customer => {
            const riskClass = customer.riskScore > 80 ? 'danger' : (customer.riskScore > 60 ? 'warning' : 'success');
            const row = `
                <tr>
                    <td><strong>${customer.name}</strong></td>
                    <td>$${customer.usd.toLocaleString()}</td>
                    <td>KSH ${customer.ksh.toLocaleString()}</td>
                    <td class="text-danger">${customer.days} days</td>
                    <td>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-${riskClass}" style="width: ${customer.riskScore}%"></div>
                        </div>
                        <small>${customer.riskScore}/100</small>
                    </td>
                    <td class="text-${riskClass}">${customer.defaultProb}%</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewCustomer(${customer.id})">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-bell"></i> Alert
                        </button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });

        // Update insights
        const predictions = [
            'Expected 15% increase in collections next month based on seasonal trends',
            '3 customers predicted to become high-risk within 30 days',
            'Cash flow expected to peak in week 8 of the forecast period',
            'Payment delays typically increase by 20% during holiday seasons'
        ];

        const recommendations = [
            'Prioritize collection efforts on MINISTRY OF ICT and KENGEN PLC',
            'Implement automated payment reminders for high-risk customers',
            'Consider offering early payment discounts to improve cash flow',
            'Review credit limits for customers with risk score above 70'
        ];

        document.getElementById('keyPredictions').innerHTML = predictions.map(p => `<li><i class="fas fa-chart-line text-primary me-2"></i>${p}</li>`).join('');
        document.getElementById('recommendedActions').innerHTML = recommendations.map(r => `<li><i class="fas fa-check-circle text-success me-2"></i>${r}</li>`).join('');

    }, 1500);
}

function refreshPredictions() {
    location.reload();
}

function viewCustomer(id) {
    window.location.href = `/finance/ai-analytics/customer/${id}`;
}

// Load predictions on page load
document.addEventListener('DOMContentLoaded', loadPredictions);
</script>
@endsection
