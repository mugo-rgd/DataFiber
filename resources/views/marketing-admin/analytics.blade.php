@extends('layouts.app')

@section('title', 'Performance Analytics - Marketing Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line me-2"></i>Performance Analytics
        </h1>
        <div class="btn-group">
            <button class="btn btn-outline-primary btn-sm">Last 7 Days</button>
            <button class="btn btn-outline-primary btn-sm">Last 30 Days</button>
            <button class="btn btn-primary btn-sm">Last 90 Days</button>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Conversion Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">24.7%</div>
                            <div class="text-xs text-success">
                                <i class="fas fa-arrow-up me-1"></i>2.3% from last period
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percent fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Customer Acquisition
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">156</div>
                            <div class="text-xs text-success">
                                <i class="fas fa-arrow-up me-1"></i>18% from last period
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Avg Response Time
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">2.4h</div>
                            <div class="text-xs text-danger">
                                <i class="fas fa-arrow-down me-1"></i>0.3h from last period
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Customer Satisfaction
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">4.7/5</div>
                            <div class="text-xs text-success">
                                <i class="fas fa-arrow-up me-1"></i>0.2 from last period
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lead Conversion Trends</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="conversionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lead Sources</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="leadSourceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Team Performance Metrics</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Account Manager</th>
                                    <th>Leads</th>
                                    <th>Conversions</th>
                                    <th>Conversion Rate</th>
                                    <th>Revenue Generated</th>
                                    <th>Customer Satisfaction</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>John Smith</td>
                                    <td>45</td>
                                    <td>12</td>
                                    <td>26.7%</td>
                                    <td>$45,000</td>
                                    <td>4.8/5</td>
                                </tr>
                                <tr>
                                    <td>Sarah Johnson</td>
                                    <td>38</td>
                                    <td>10</td>
                                    <td>26.3%</td>
                                    <td>$38,500</td>
                                    <td>4.6/5</td>
                                </tr>
                                <tr>
                                    <td>Mike Davis</td>
                                    <td>52</td>
                                    <td>14</td>
                                    <td>26.9%</td>
                                    <td>$52,000</td>
                                    <td>4.7/5</td>
                                </tr>
                                <tr>
                                    <td>Emily Wilson</td>
                                    <td>41</td>
                                    <td>11</td>
                                    <td>26.8%</td>
                                    <td>$41,200</td>
                                    <td>4.9/5</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Conversion Trend Chart
    const conversionCtx = document.getElementById('conversionChart').getContext('2d');
    new Chart(conversionCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [{
                label: 'Conversion Rate (%)',
                data: [18, 22, 25, 23, 26, 24, 27],
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
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
                y: {
                    beginAtZero: true,
                    max: 30
                }
            }
        }
    });

    // Lead Source Chart
    const leadSourceCtx = document.getElementById('leadSourceChart').getContext('2d');
    new Chart(leadSourceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Website', 'Referral', 'Social Media', 'Email', 'Events'],
            datasets: [{
                data: [35, 25, 20, 15, 5],
                backgroundColor: [
                    '#4e73df',
                    '#1cc88a',
                    '#36b9cc',
                    '#f6c23e',
                    '#e74a3b'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endsection
