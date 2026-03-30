@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-chart-bar text-primary"></i> Reports & Analytics
            </h1>
            <p class="text-muted">Comprehensive system reports and analytics dashboard</p>
        </div>
    </div>

    <!-- Report Filters -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Report Filters
            </h6>
        </div>
        <div class="card-body">
            <form id="reportFilters">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="report_type" class="form-label">Report Type</label>
                            <select class="form-select" id="report_type" name="report_type">
                                <option value="financial">Financial Reports</option>
                                <option value="customer">Customer Reports</option>
                                <option value="lease">Lease Reports</option>
                                <option value="design">Design Requests</option>
                                <option value="quotation">Quotation Reports</option>
                                <option value="survey">Survey Reports</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_range" class="form-label">Date Range</label>
                            <select class="form-select" id="date_range" name="date_range">
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="last_7_days" selected>Last 7 Days</option>
                                <option value="last_30_days">Last 30 Days</option>
                                <option value="this_month">This Month</option>
                                <option value="last_month">Last Month</option>
                                <option value="this_quarter">This Quarter</option>
                                <option value="this_year">This Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Generate
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">$245,380</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success me-2"><i class="fas fa-arrow-up"></i> 12.5%</span>
                                <span>Since last month</span>
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
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Leases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">342</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success me-2"><i class="fas fa-arrow-up"></i> 8.2%</span>
                                <span>Since last month</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-network-wired fa-2x text-gray-300"></i>
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
                                New Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">48</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success me-2"><i class="fas fa-arrow-up"></i> 15.3%</span>
                                <span>Since last month</span>
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-danger me-2"><i class="fas fa-arrow-down"></i> 4.3%</span>
                                <span>Since last month</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="row">
        <!-- Financial Reports -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-line me-2"></i>Financial Reports
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
                                Revenue Report
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-money-bill-wave text-success me-2"></i>
                                Payment Collection Report
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-receipt text-info me-2"></i>
                                Invoice Summary
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-chart-pie text-warning me-2"></i>
                                Revenue by Service Type
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Reports -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-users me-2"></i>Customer Reports
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-user-plus text-success me-2"></i>
                                Customer Acquisition Report
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-chart-bar text-primary me-2"></i>
                                Customer Segmentation
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-star text-warning me-2"></i>
                                Customer Satisfaction
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-history text-info me-2"></i>
                                Customer Activity Log
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operational Reports -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-cogs me-2"></i>Operational Reports
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-drafting-compass text-info me-2"></i>
                                Design Request Status
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-invoice text-primary me-2"></i>
                                Quotation Performance
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-map-marked-alt text-success me-2"></i>
                                Survey Completion Rate
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-tools text-warning me-2"></i>
                                Maintenance Reports
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-download me-2"></i>Export Reports
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                Export as PDF
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-excel text-success me-2"></i>
                                Export as Excel
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-csv text-primary me-2"></i>
                                Export as CSV
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-chart-bar text-info me-2"></i>
                                Custom Report Builder
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-tachometer-alt me-2"></i>Quick Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-primary">1,248</h4>
                                <small class="text-muted">Total Customers</small>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-success">856</h4>
                                <small class="text-muted">Active Leases</small>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-info">342</h4>
                                <small class="text-muted">Design Requests</small>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-warning">128</h4>
                                <small class="text-muted">Pending Surveys</small>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-danger">45</h4>
                                <small class="text-muted">Overdue Payments</small>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-dark">98.2%</h4>
                                <small class="text-muted">System Uptime</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date range functionality
    const dateRangeSelect = document.getElementById('date_range');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    function setDefaultDates() {
        const today = new Date();
        const lastWeek = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);

        startDateInput.value = lastWeek.toISOString().split('T')[0];
        endDateInput.value = today.toISOString().split('T')[0];
    }

    // Set default dates
    setDefaultDates();

    // Handle date range changes
    dateRangeSelect.addEventListener('change', function() {
        const today = new Date();
        let startDate = new Date();

        switch(this.value) {
            case 'today':
                startDate = today;
                break;
            case 'yesterday':
                startDate.setDate(today.getDate() - 1);
                break;
            case 'last_7_days':
                startDate.setDate(today.getDate() - 7);
                break;
            case 'last_30_days':
                startDate.setDate(today.getDate() - 30);
                break;
            case 'this_month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                break;
            case 'last_month':
                startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                break;
            case 'this_quarter':
                const quarter = Math.floor(today.getMonth() / 3);
                startDate = new Date(today.getFullYear(), quarter * 3, 1);
                break;
            case 'this_year':
                startDate = new Date(today.getFullYear(), 0, 1);
                break;
            case 'custom':
                // Do nothing for custom
                return;
        }

        if (this.value !== 'custom') {
            startDateInput.value = startDate.toISOString().split('T')[0];
            endDateInput.value = today.toISOString().split('T')[0];
        }
    });

    // Form submission
    document.getElementById('reportFilters').addEventListener('submit', function(e) {
        e.preventDefault();
        // Here you would typically make an AJAX request to generate the report
        alert('Report generation would be triggered here with the selected filters.');
    });
});
</script>
@endpush
@endsection
