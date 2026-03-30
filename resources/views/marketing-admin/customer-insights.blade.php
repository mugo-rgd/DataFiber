@extends('layouts.app')

@section('title', 'Customer Insights - Marketing Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users me-2"></i>Customer Insights
        </h1>
        <div class="btn-group">
            <button class="btn btn-outline-primary btn-sm">All Customers</button>
            <button class="btn btn-outline-primary btn-sm">Active</button>
            <button class="btn btn-outline-primary btn-sm">New</button>
            <button class="btn btn-outline-primary btn-sm">At Risk</button>
        </div>
    </div>

    <!-- Customer Metrics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\User::where('role', 'customer')->count() }}</div>
                            <div class="text-xs text-success">
                                <i class="fas fa-arrow-up me-1"></i>12% growth
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Active Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Lease::where('status', 'active')->distinct('customer_id')->count('customer_id') }}</div>
                            <div class="text-xs text-success">
                                <i class="fas fa-arrow-up me-1"></i>8% growth
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                                Avg Customer Value
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">$2,450</div>
                            <div class="text-xs text-success">
                                <i class="fas fa-arrow-up me-1"></i>5% increase
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Satisfaction Score
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">4.6/5</div>
                            <div class="text-xs text-success">
                                <i class="fas fa-arrow-up me-1"></i>0.2 improvement
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

    <!-- Customer Segmentation -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Segmentation</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="segmentationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Growth Trend</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Demographics -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Demographics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Industry Distribution</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td>Telecommunications</td>
                                        <td>35%</td>
                                        <td><div class="progress" style="height: 8px; width: 150px;">
                                            <div class="progress-bar bg-primary" style="width: 35%"></div>
                                        </div></td>
                                    </tr>
                                    <tr>
                                        <td>Enterprise IT</td>
                                        <td>28%</td>
                                        <td><div class="progress" style="height: 8px; width: 150px;">
                                            <div class="progress-bar bg-success" style="width: 28%"></div>
                                        </div></td>
                                    </tr>
                                    <tr>
                                        <td>Healthcare</td>
                                        <td>15%</td>
                                        <td><div class="progress" style="height: 8px; width: 150px;">
                                            <div class="progress-bar bg-info" style="width: 15%"></div>
                                        </div></td>
                                    </tr>
                                    <tr>
                                        <td>Education</td>
                                        <td>12%</td>
                                        <td><div class="progress" style="height: 8px; width: 150px;">
                                            <div class="progress-bar bg-warning" style="width: 12%"></div>
                                        </div></td>
                                    </tr>
                                    <tr>
                                        <td>Other</td>
                                        <td>10%</td>
                                        <td><div class="progress" style="height: 8px; width: 150px;">
                                            <div class="progress-bar bg-secondary" style="width: 10%"></div>
                                        </div></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Geographic Distribution</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td>North America</td>
                                        <td>42%</td>
                                        <td><div class="progress" style="height: 8px; width: 150px;">
                                            <div class="progress-bar bg-primary" style="width: 42%"></div>
                                        </div></td>
                                    </tr>
                                    <tr>
                                        <td>Europe</td>
                                        <td>31%</td>
                                        <td><div class="progress" style="height: 8px; width: 150px;">
                                            <div class="progress-bar bg-success" style="width: 31%"></div>
                                        </div></td>
                                    </tr>
                                    <tr>
                                        <td>Asia Pacific</td>
                                        <td>18%</td>
                                        <td><div class="progress" style="height: 8px; width: 150px;">
                                            <div class="progress-bar bg-info" style="width: 18%"></div>
                                        </div></td>
                                    </tr>
                                    <tr>
                                        <td>Other Regions</td>
                                        <td>9%</td>
                                        <td><div class="progress" style="height: 8px; width: 150px;">
                                            <div class="progress-bar bg-warning" style="width: 9%"></div>
                                        </div></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Behavior Insights -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Behavior Insights</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="card border-left-info">
                                <div class="card-body">
                                    <i class="fas fa-shopping-cart fa-2x text-info mb-3"></i>
                                    <h5>Average Purchase Frequency</h5>
                                    <h3 class="text-info">2.3x/month</h3>
                                    <small class="text-muted">Per active customer</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="card border-left-success">
                                <div class="card-body">
                                    <i class="fas fa-clock fa-2x text-success mb-3"></i>
                                    <h5>Average Tenure</h5>
                                    <h3 class="text-success">18 months</h3>
                                    <small class="text-muted">Customer lifetime</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="card border-left-warning">
                                <div class="card-body">
                                    <i class="fas fa-share-alt fa-2x text-warning mb-3"></i>
                                    <h5>Referral Rate</h5>
                                    <h3 class="text-warning">23%</h3>
                                    <small class="text-muted">Of customers refer others</small>
                                </div>
                            </div>
                        </div>
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
    // Segmentation Chart
    const segmentationCtx = document.getElementById('segmentationChart').getContext('2d');
    new Chart(segmentationCtx, {
        type: 'doughnut',
        data: {
            labels: ['Enterprise', 'SMB', 'Startup', 'Individual'],
            datasets: [{
                data: [45, 30, 15, 10],
                backgroundColor: [
                    '#4e73df',
                    '#1cc88a',
                    '#36b9cc',
                    '#f6c23e'
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

    // Growth Chart
    const growthCtx = document.getElementById('growthChart').getContext('2d');
    new Chart(growthCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
            datasets: [{
                label: 'New Customers',
                data: [45, 52, 48, 61, 58, 65, 70, 63, 72, 78],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
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
            }
        }
    });
});
</script>
@endsection
