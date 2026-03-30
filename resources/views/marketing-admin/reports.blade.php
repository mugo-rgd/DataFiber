@extends('layouts.app')

@section('title', 'Marketing Reports - Marketing Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt me-2"></i>Marketing Reports
        </h1>
        <div class="btn-group">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateReportModal">
                <i class="fas fa-plus me-2"></i>Generate Report
            </button>
            <button class="btn btn-outline-primary">
                <i class="fas fa-download me-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Report Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Report Filters</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date Range</label>
                            <select class="form-select">
                                <option>Last 7 Days</option>
                                <option>Last 30 Days</option>
                                <option selected>Last 90 Days</option>
                                <option>Last 12 Months</option>
                                <option>Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Report Type</label>
                            <select class="form-select">
                                <option>Performance Summary</option>
                                <option>Campaign Analysis</option>
                                <option>Customer Insights</option>
                                <option>Revenue Analysis</option>
                                <option>Team Performance</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Format</label>
                            <select class="form-select">
                                <option>PDF</option>
                                <option>Excel</option>
                                <option>CSV</option>
                                <option>HTML</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <button class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Quarterly Performance
                            </div>
                            <div class="h6 mb-0 text-gray-800">Q3 2024 Marketing Report</div>
                            <div class="text-xs text-muted">Generated: Oct 15, 2024</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-pdf fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-primary me-1">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn btn-sm btn-outline-success me-1">
                            <i class="fas fa-download"></i> Download
                        </button>
                        <button class="btn btn-sm btn-outline-info">
                            <i class="fas fa-share"></i> Share
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Campaign Analysis
                            </div>
                            <div class="h6 mb-0 text-gray-800">Summer Promotion 2024</div>
                            <div class="text-xs text-muted">Generated: Sep 30, 2024</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-excel fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-primary me-1">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn btn-sm btn-outline-success me-1">
                            <i class="fas fa-download"></i> Download
                        </button>
                        <button class="btn btn-sm btn-outline-info">
                            <i class="fas fa-share"></i> Share
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Customer Insights
                            </div>
                            <div class="h6 mb-0 text-gray-800">Q3 Customer Behavior</div>
                            <div class="text-xs text-muted">Generated: Sep 25, 2024</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-primary me-1">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn btn-sm btn-outline-success me-1">
                            <i class="fas fa-download"></i> Download
                        </button>
                        <button class="btn btn-sm btn-outline-info">
                            <i class="fas fa-share"></i> Share
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Templates -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Report Templates</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title">Performance Dashboard</h5>
                                    <p class="card-text text-muted">Comprehensive overview of marketing performance metrics and KPIs.</p>
                                    <button class="btn btn-primary">Use Template</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-bullhorn fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">Campaign Analysis</h5>
                                    <p class="card-text text-muted">Detailed analysis of campaign performance and ROI.</p>
                                    <button class="btn btn-success">Use Template</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-3x text-info mb-3"></i>
                                    <h5 class="card-title">Customer Insights</h5>
                                    <p class="card-text text-muted">Deep dive into customer behavior and segmentation.</p>
                                    <button class="btn btn-info">Use Template</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Reports</h6>
                    <button class="btn btn-sm btn-outline-primary">View All</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Report Name</th>
                                    <th>Type</th>
                                    <th>Generated By</th>
                                    <th>Date</th>
                                    <th>Size</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Q3_Marketing_Performance.pdf</td>
                                    <td>Performance</td>
                                    <td>System</td>
                                    <td>Oct 15, 2024</td>
                                    <td>2.4 MB</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-success" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-outline-info" title="Share">
                                                <i class="fas fa-share"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Campaign_Analysis_Q3.xlsx</td>
                                    <td>Campaign</td>
                                    <td>John Smith</td>
                                    <td>Oct 10, 2024</td>
                                    <td>1.8 MB</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-success" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-outline-info" title="Share">
                                                <i class="fas fa-share"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Customer_Insights_Report.pdf</td>
                                    <td>Insights</td>
                                    <td>System</td>
                                    <td>Oct 5, 2024</td>
                                    <td>3.1 MB</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-success" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-outline-info" title="Share">
                                                <i class="fas fa-share"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Monthly_Performance_Dashboard.pdf</td>
                                    <td>Dashboard</td>
                                    <td>System</td>
                                    <td>Oct 1, 2024</td>
                                    <td>1.5 MB</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-success" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-outline-info" title="Share">
                                                <i class="fas fa-share"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Report Modal -->
<div class="modal fade" id="generateReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate New Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Report generation feature is currently under development.
                </div>
                <p class="text-muted">This functionality will allow you to generate custom reports with various filters, metrics, and visualization options.</p>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6>Planned Features:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Custom date ranges</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Multiple export formats</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Automated scheduling</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Custom metrics selection</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6>Available Soon:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-clock text-warning me-2"></i>Real-time data</li>
                                    <li><i class="fas fa-clock text-warning me-2"></i>Advanced filters</li>
                                    <li><i class="fas fa-clock text-warning me-2"></i>Team sharing</li>
                                    <li><i class="fas fa-clock text-warning me-2"></i>API integration</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" disabled>Generate Report</button>
            </div>
        </div>
    </div>
</div>
@endsection
