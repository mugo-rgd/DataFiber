@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kenya Dark Fibre Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #2E8B57;
            --secondary-color: #FF6B35;
            --accent-color: #1E4D78;
            --warning-color: #FFA500;
            --danger-color: #DC3545;
            --success-color: #28A745;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .metric-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-left: 4px solid var(--primary-color);
            transition: transform 0.3s ease;
        }

        .metric-card:hover {
            transform: translateY(-5px);
        }

        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .metric-label {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .county-coverage {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .progress {
            height: 8px;
            margin-top: 5px;
        }

        .incident-high { border-left-color: var(--danger-color); }
        .incident-medium { border-left-color: var(--warning-color); }
        .incident-low { border-left-color: var(--success-color); }

        .network-status {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .operational { background-color: var(--success-color); }
        .degraded { background-color: var(--warning-color); }
        .maintenance { background-color: var(--accent-color); }
        .down { background-color: var(--danger-color); }

        .kenya-flag {
            background: linear-gradient(135deg, black 33%, red 33%, red 66%, green 66%);
            width: 30px;
            height: 20px;
            display: inline-block;
            margin-right: 10px;
            border-radius: 2px;
        }

        /* Billing specific styles */
        .billing-cycle-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .billing-monthly { background-color: #e3f2fd; color: #1976d2; }
        .billing-quarterly { background-color: #f3e5f5; color: #7b1fa2; }
        .billing-annually { background-color: #e8f5e9; color: #388e3c; }

        /* Add to your existing CSS */
.chart-loading {
    position: relative;
}

.chart-loading::after {
    content: 'Loading...';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #6c757d;
    font-size: 14px;
}

.loading-pulse {
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% { opacity: 0.6; }
    50% { opacity: 1; }
    100% { opacity: 0.6; }
}
    </style>
</head>
<body>
    <!-- Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-globe-africa me-2"></i>Kenya Dark Fibre Network Dashboard</h1>
                    <p class="mb-0">Comprehensive overview of dark fibre infrastructure across Kenya</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="kenya-flag"></span>
                    <span id="lastUpdated" class="text-light">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading dashboard data...</p>
        </div>

        <!-- Error Message -->
        <div id="errorMessage" class="alert alert-danger d-none" role="alert">
            <h4 class="alert-heading">Failed to Load Dashboard</h4>
            <p id="errorText">An error occurred while loading the dashboard data.</p>
            <button id="retryButton" class="btn btn-danger">Retry</button>
        </div>

        <!-- Dashboard Content (initially hidden) -->
        <div id="dashboardContent" class="d-none">
            <!-- Control Bar -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <button id="refreshBtn" class="btn btn-primary">
                                        <i class="fas fa-sync-alt me-2"></i>Refresh Data
                                    </button>
                                    <button class="btn btn-outline-secondary ms-2">
                                        <i class="fas fa-download me-2"></i>Export Report
                                    </button>
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="network-status">
                                        <span class="status-dot operational"></span>
                                        <span>Operational: <strong id="operationalCount">0</strong></span>
                                        <span class="status-dot degraded ms-3"></span>
                                        <span>Degraded: <strong id="degradedCount">0</strong></span>
                                        <span class="status-dot down ms-3"></span>
                                        <span>Down: <strong id="downCount">0</strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overview Metrics -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card">
                        <div class="metric-value" id="totalFibreKm">0 km</div>
                        <div class="metric-label">Total Fibre Infrastructure</div>
                        <small>Across all counties</small>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card">
                        <div class="metric-value" id="activeCustomers">0</div>
                        <div class="metric-label">Active Customers</div>
                        <small>Enterprise & ISP clients</small>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card">
                        <div class="metric-value" id="countiesCovered">0</div>
                        <div class="metric-label">Counties Covered</div>
                        <small>Out of 47 counties</small>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="metric-card">
                        <div class="metric-value" id="networkUptime">0%</div>
                        <div class="metric-label">Network Uptime</div>
                        <small>Last 30 days</small>
                    </div>
                </div>
            </div>

            <!-- Billing and Customer Information Section -->
            <div class="row mb-4">
                <!-- Active Customers by Billing Frequency -->
                <div class="col-lg-6 mb-4">
                    <div class="county-coverage">
                        <h5><i class="fas fa-users me-2"></i>Active Customers by Billing Frequency</h5>
                        <div id="customerBillingDistribution">
                            <!-- This will be populated by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Revenue by Billing Cycle -->
                <div class="col-lg-6 mb-4">
                    <div class="county-coverage">
                        <h5><i class="fas fa-money-bill-wave me-2"></i>Revenue by Billing Cycle</h5>
                        <div id="revenueByBillingCycle">
                            <!-- This will be populated by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Consolidated Billing Summary -->
                <div class="col-lg-8 mb-4">
                    <div class="county-coverage">
                        <h5><i class="fas fa-file-invoice-dollar me-2"></i>Recent Consolidated Billings</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Billing #</th>
                                        <th>Customer</th>
                                        <th>Total Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="recentBillingsTable">
                                    <!-- Will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Billing Status Overview -->
                <div class="col-lg-4 mb-4">
                    <div class="county-coverage">
                        <h5><i class="fas fa-chart-pie me-2"></i>Billing Status Overview</h5>
                        <canvas id="billingStatusChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Charts and Detailed Information -->
            <div class="row">
                <!-- County Coverage -->
                <div class="col-lg-6 mb-4">
                    <div class="county-coverage">
                        <h5><i class="fas fa-map-marked-alt me-2"></i>County Coverage Analysis</h5>
                        <div id="countyCoverageList">
                            <!-- Example data - replace with actual data -->
                            <div class="row align-items-center mb-3">
                                <div class="col-4">
                                    <strong>Nairobi</strong>
                                </div>
                                <div class="col-8">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>3,500 km fibre</small>
                                        <small>1,250 connections</small>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: 85%; background-color: #2E8B57;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted">85% coverage</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

     <!-- Top Customers by Revenue -->
<div class="col-lg-6 mb-4">
    <div class="county-coverage">
        <h5><i class="fas fa-users me-2"></i>Top Customers by Revenue</h5>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="40">#</th>
                        <th>Customer Name</th>
                        <th>Revenue</th>
                        <th>Share</th>
                    </tr>
                </thead>
                <tbody id="topCustomersTable">
                    <!-- This will be populated by JavaScript -->
                    <tr id="topCustomersLoading">
                        <td colspan="4" class="text-center">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <small class="ms-2">Loading customer data...</small>
                        </td>
                    </tr>
                </tbody>
                <tfoot id="topCustomersFooter" class="d-none">
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            <small>Showing top 10 customers by revenue</small>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

                <!-- Revenue Analytics -->
                <div class="col-lg-8 mb-4">
                    <div class="county-coverage">
                        <h5><i class="fas fa-chart-line me-2"></i>Monthly Revenue Trend (USD Millions)</h5>
                        <canvas id="revenueChart" height="150"></canvas>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
        <div class="county-coverage">
        <h5><i class="fas fa-chart-bar me-2"></i>Revenue Statistics</h5>
        <div id="revenueStats">
            <!-- Will be populated by JavaScript -->
        </div>
    </div>
</div>

                <!-- Network Health -->
                <div class="col-lg-4 mb-4">
                    <div class="county-coverage">
                        <h5><i class="fas fa-heartbeat me-2"></i>Network Health Status</h5>
                        <canvas id="healthChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
class KenyaFibreDashboard {
    constructor() {
        this.data = null;
        this.charts = {};
        this.elements = {};
        this.timeoutDuration = 30000; // 30 second timeout
        this.init();
    }

    async init() {
        this.cacheElements();
        this.showLoading();
        await this.loadData();
        this.setupEventListeners();
    }

    cacheElements() {
console.log('Caching DOM elements...');

        // Cache all DOM elements we'll be using
        this.elements = {
            loadingSpinner: document.getElementById('loadingSpinner'),
            errorMessage: document.getElementById('errorMessage'),
            errorText: document.getElementById('errorText'),
            retryButton: document.getElementById('retryButton'),
            dashboardContent: document.getElementById('dashboardContent'),
            lastUpdated: document.getElementById('lastUpdated'),
            refreshBtn: document.getElementById('refreshBtn'),

            // Metric elements
            totalFibreKm: document.getElementById('totalFibreKm'),
            activeCustomers: document.getElementById('activeCustomers'),
            countiesCovered: document.getElementById('countiesCovered'),
            networkUptime: document.getElementById('networkUptime'),

            // Network status
            operationalCount: document.getElementById('operationalCount'),
            degradedCount: document.getElementById('degradedCount'),
            downCount: document.getElementById('downCount'),

            // Chart containers
            countyCoverageList: document.getElementById('countyCoverageList'),

            // Chart canvases
            providerChart: document.getElementById('providerChart'),
            revenueChart: document.getElementById('revenueChart'),
            healthChart: document.getElementById('healthChart'),

            // New billing elements
            customerBillingDistribution: document.getElementById('customerBillingDistribution'),
            revenueByBillingCycle: document.getElementById('revenueByBillingCycle'),
            recentBillingsTable: document.getElementById('recentBillingsTable'),
            billingStatusChart: document.getElementById('billingStatusChart'),
            revenueStats: document.getElementById('revenueStats'),
            topCustomersTable: document.getElementById('topCustomersTable'),
        topCustomersLoading: document.getElementById('topCustomersLoading'),
        topCustomersFooter: document.getElementById('topCustomersFooter')
        };

         console.log('topCustomersTable element:', this.elements.topCustomersTable);
    console.log('All cached elements:', Object.keys(this.elements));
    }

    async loadData() {
        try {
            console.log('Loading dashboard data...');

            // Set up timeout
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Request timeout after 30 seconds')), this.timeoutDuration);
            });

            // Set up fetch request
            const fetchPromise = fetch('/kenya-fibre-data', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            // Race between fetch and timeout
            const response = await Promise.race([fetchPromise, timeoutPromise]);

            if (!response.ok) {
                throw new Error(`Server returned ${response.status}`);
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Expected JSON response but got: ' + contentType);
            }

            this.data = await response.json();

            // Check for error in response
            if (this.data.error) {
                throw new Error(this.data.message || this.data.error);
            }

            console.log('Data loaded successfully');

            this.hideLoading();
            this.showDashboard();
            this.renderDashboard();

        } catch (error) {
            console.error('Error loading dashboard data:', error);

            // Show specific error messages
            let errorMessage = 'Failed to load dashboard data: ';

            if (error.message.includes('timeout')) {
                errorMessage += 'Request took too long. Please try again.';
            } else if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
                errorMessage += 'Network error. Please check your connection.';
            } else {
                errorMessage += error.message;
            }

            this.showError(errorMessage);
        }
    }

    showLoading() {
        this.elements.loadingSpinner.classList.remove('d-none');
        this.elements.errorMessage.classList.add('d-none');
        this.elements.dashboardContent.classList.add('d-none');
    }

    hideLoading() {
        this.elements.loadingSpinner.classList.add('d-none');
    }

    showDashboard() {
        this.elements.dashboardContent.classList.remove('d-none');
    }

    showError(message) {
        this.elements.loadingSpinner.classList.add('d-none');
        this.elements.dashboardContent.classList.add('d-none');
        this.elements.errorMessage.classList.remove('d-none');
        this.elements.errorText.textContent = message;
    }

    setupEventListeners() {
        this.elements.refreshBtn.addEventListener('click', () => {
            this.refreshData();
        });

        this.elements.retryButton.addEventListener('click', () => {
            this.refreshData();
        });
    }

    async refreshData() {
        this.showLoading();
        await this.loadData();
    }

    renderDashboard() {
        if (!this.data) {
            this.showError('No data available');
            return;
        }

        try {
            // Show loading for charts
            this.showChartLoading();

            // Render in sequence to prevent blocking
            setTimeout(() => {
                this.renderOverviewMetrics();
                this.renderBillingInformation();
                this.renderRevenueStats();
                this.renderTopCustomersTable();

                setTimeout(() => {
                    this.renderRevenueChart();
                    this.renderNetworkHealth();
                    this.renderBillingStatusChart();

                    // Update last updated time
                    if (this.elements.lastUpdated) {
                        this.elements.lastUpdated.textContent = `Last updated: ${this.data.last_updated}`;
                    }

                    this.hideChartLoading();
                }, 100);

            }, 50);

        } catch (error) {
            console.error('Error rendering dashboard:', error);
            this.showError('Error displaying dashboard: ' + error.message);
        }
    }

    renderOverviewMetrics() {
        const metrics = this.data.overview_metrics;

        // Safely update metric elements
        if (this.elements.totalFibreKm) {
            this.elements.totalFibreKm.textContent = `${metrics.total_fibre_km.toLocaleString()} km`;
        }
        if (this.elements.activeCustomers) {
            this.elements.activeCustomers.textContent = metrics.active_customers.toLocaleString();
        }
        if (this.elements.countiesCovered) {
            this.elements.countiesCovered.textContent = metrics.counties_covered;
        }
        if (this.elements.networkUptime) {
            this.elements.networkUptime.textContent = `${metrics.network_uptime}%`;
        }

        // Update network status counts
        const health = this.data.network_health;
        if (this.elements.operationalCount) {
            this.elements.operationalCount.textContent = health.operational;
        }
        if (this.elements.degradedCount) {
            this.elements.degradedCount.textContent = health.degraded;
        }
        if (this.elements.downCount) {
            this.elements.downCount.textContent = health.down;
        }
    }

    renderBillingInformation() {
        // Customer Billing Distribution
        if (this.elements.customerBillingDistribution && this.data.customer_billing_distribution) {
            const distribution = this.data.customer_billing_distribution;
            let html = '<div class="row">';

            Object.entries(distribution).forEach(([cycle, count]) => {
                const cycleClass = `billing-${cycle.toLowerCase()}`;
                const cycleLabel = cycle.charAt(0).toUpperCase() + cycle.slice(1);

                html += `
                    <div class="col-6 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <span class="billing-cycle-badge ${cycleClass}">${cycleLabel}</span>
                                <div class="metric-value mt-2" style="font-size: 1.5rem;">${count}</div>
                                <small class="text-muted">Customers</small>
                            </div>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            this.elements.customerBillingDistribution.innerHTML = html;
        }

        // Revenue by Billing Cycle
        if (this.elements.revenueByBillingCycle && this.data.revenue_by_billing_cycle) {
            const revenueData = this.data.revenue_by_billing_cycle;
            let html = '';

            Object.entries(revenueData).forEach(([cycle, data]) => {
                const percentage = data.percentage;
                const cycleClass = `billing-${cycle.toLowerCase()}`;
                const cycleLabel = cycle.charAt(0).toUpperCase() + cycle.slice(1);

                html += `
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="billing-cycle-badge ${cycleClass}">${cycleLabel}</span>
                                <small class="ms-2">${data.count || 0} invoices</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">${data.revenue || '$0.00'}</div>
                                <small class="text-muted">${percentage}% of total</small>
                            </div>
                        </div>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar" style="width: ${percentage}%; background-color: ${this.getBillingCycleColor(cycle)}"></div>
                        </div>
                    </div>
                `;
            });

            this.elements.revenueByBillingCycle.innerHTML = html;
        }

        // Recent Consolidated Billings
        if (this.elements.recentBillingsTable && this.data.recent_consolidated_billings) {
            const billings = this.data.recent_consolidated_billings;
            let html = '';

            billings.forEach(billing => {
                const statusBadge = this.getStatusBadge(billing.status);
                const dueDate = new Date(billing.due_date).toLocaleDateString();

                html += `
                    <tr>
                        <td><small>${billing.billing_number}</small></td>
                        <td><small>${billing.customer_name}</small></td>
                        <td><strong>${billing.total_amount}</strong></td>
                        <td><small class="${this.isOverdue(billing.due_date) ? 'text-danger' : 'text-muted'}">${dueDate}</small></td>
                        <td>${statusBadge}</td>
                    </tr>
                `;
            });

            this.elements.recentBillingsTable.innerHTML = html;
        }

        // Billing Status Chart
        if (this.elements.billingStatusChart && this.data.billing_status_overview) {
            this.renderBillingStatusChart();
        }
    }

    getBillingCycleColor(cycle) {
        const colors = {
            'monthly': '#1976d2',
            'quarterly': '#7b1fa2',
            'annually': '#388e3c'
        };
        return colors[cycle.toLowerCase()] || '#6c757d';
    }

    getStatusBadge(status) {
        const badges = {
            'draft': 'badge bg-secondary',
            'pending': 'badge bg-warning',
            'sent': 'badge bg-info',
            'paid': 'badge bg-success',
            'overdue': 'badge bg-danger',
            'cancelled': 'badge bg-dark'
        };
        return `<span class="${badges[status] || 'badge bg-light text-dark'}">${status}</span>`;
    }

    isOverdue(dueDate) {
        const today = new Date();
        const due = new Date(dueDate);
        return due < today;
    }

    renderRevenueChart() {
        if (!this.elements.revenueChart || !this.data.revenue_analytics) return;

        const ctx = this.elements.revenueChart.getContext('2d');
        const revenueData = this.data.revenue_analytics.monthly;

        // Extract labels and data
        const labels = Object.keys(revenueData);
        const data = Object.values(revenueData);

        // Calculate statistics
        const totalRevenue = data.reduce((sum, value) => sum + value, 0);
        const avgRevenue = totalRevenue / data.length;
        const maxRevenue = Math.max(...data);
        const minRevenue = Math.min(...data);

        if (this.charts.revenue) {
            this.charts.revenue.destroy();
        }

        this.charts.revenue = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Revenue (USD M)',
                    data: data,
                    borderColor: '#2E8B57',
                    backgroundColor: 'rgba(46, 139, 87, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#2E8B57',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
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
                                label += 'USD ' + context.parsed.y.toFixed(2) + 'M';
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Revenue (USD Millions)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'USD ' + value.toFixed(1) + 'M';
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                }
            }
        });

        // Log statistics
        console.log('Revenue Statistics:', {
            total: 'USD ' + totalRevenue.toFixed(2) + 'M',
            average: 'USD ' + avgRevenue.toFixed(2) + 'M',
            maximum: 'USD ' + maxRevenue.toFixed(2) + 'M',
            minimum: 'USD ' + minRevenue.toFixed(2) + 'M'
        });
    }

    renderNetworkHealth() {
        if (!this.elements.healthChart || !this.data.network_health) return;

        const ctx = this.elements.healthChart.getContext('2d');
        const health = this.data.network_health;

        if (this.charts.health) {
            this.charts.health.destroy();
        }

        this.charts.health = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Operational', 'Degraded', 'Maintenance', 'Down'],
                datasets: [{
                    data: [health.operational, health.degraded, health.maintenance, health.down],
                    backgroundColor: ['#28A745', '#FFA500', '#1E4D78', '#DC3545']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    showChartLoading() {
        // Add loading indicators to chart containers
        const chartContainers = [
            'revenueChart',
            'billingStatusChart',
            'healthChart'
        ];

        chartContainers.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.style.opacity = '0.5';
                element.style.transition = 'opacity 0.3s';
            }
        });
    }

    hideChartLoading() {
        const chartContainers = [
            'revenueChart',
            'billingStatusChart',
            'healthChart'
        ];

        chartContainers.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.style.opacity = '1';
            }
        });
    }

    renderBillingStatusChart() {
        if (!this.elements.billingStatusChart || !this.data.billing_status_overview) return;

        const ctx = this.elements.billingStatusChart.getContext('2d');
        const statusData = this.data.billing_status_overview;

        // Destroy existing chart if it exists
        if (this.charts.billingStatus) {
            this.charts.billingStatus.destroy();
        }

        this.charts.billingStatus = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusData),
                datasets: [{
                    data: Object.values(statusData).map(item => item.count),
                    backgroundColor: ['#28A745', '#FFC107', '#6C757D', '#DC3545', '#0DCAF0', '#6F42C1']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const status = Object.values(statusData)[context.dataIndex];
                                return `${context.label}: ${status.count} bills (${status.percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    renderRevenueStats() {
        if (!this.data.revenue_analytics) return;

        const revenueData = Object.values(this.data.revenue_analytics.monthly);
        const totalRevenue = revenueData.reduce((sum, value) => sum + value, 0);
        const avgRevenue = totalRevenue / revenueData.length;
        const maxRevenue = Math.max(...revenueData);
        const minRevenue = Math.min(...revenueData);

        const statsHtml = `
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <small>Total (12 months):</small>
                    <strong class="text-primary">USD ${totalRevenue.toFixed(2)}M</strong>
                </div>
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <small>Monthly Average:</small>
                    <strong>USD ${avgRevenue.toFixed(2)}M</strong>
                </div>
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <small>Highest Month:</small>
                    <strong class="text-success">USD ${maxRevenue.toFixed(2)}M</strong>
                </div>
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <small>Lowest Month:</small>
                    <strong class="text-warning">USD ${minRevenue.toFixed(2)}M</strong>
                </div>
            </div>
        `;

        if (this.elements.revenueStats) {
            this.elements.revenueStats.innerHTML = statsHtml;
        }
    }

    renderTopCustomersTable() {
    if (!this.elements.topCustomersTable || !this.data.top_customers) {
        return;
    }

    const customers = this.data.top_customers;

    // Clear loading state
    if (this.elements.topCustomersLoading) {
        this.elements.topCustomersLoading.style.display = 'none';
    }

    // Show footer
    if (this.elements.topCustomersFooter) {
        this.elements.topCustomersFooter.classList.remove('d-none');
    }

    // Clear table (keep loading row hidden)
    const rows = this.elements.topCustomersTable.querySelectorAll('tr:not(#topCustomersLoading)');
    rows.forEach(row => row.remove());

    // Handle empty data
    if (!customers || customers.length === 0) {
        const emptyHtml = `
            <tr>
                <td colspan="4" class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-database fa-2x mb-3"></i>
                        <p class="mb-0">No customer revenue data found</p>
                        <small>Add billing data to see customer rankings</small>
                    </div>
                </td>
            </tr>
        `;
        this.elements.topCustomersTable.innerHTML = emptyHtml;
        return;
    }

    // Calculate total revenue for additional stats
    const totalRevenue = customers.reduce((sum, customer) => {
        const revenue = parseFloat(customer.revenue_value) || 0;
        return sum + revenue;
    }, 0);

    // Add stats row at top
    const statsHtml = `
        <tr class="table-info">
            <td colspan="4">
                <div class="d-flex justify-content-between align-items-center">
                    <small><strong>Total:</strong> $${totalRevenue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</small>
                    <small><strong>Customers:</strong> ${customers.length}</small>
                    <small><strong>Avg Revenue:</strong> $${(totalRevenue / customers.length).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</small>
                </div>
            </td>
        </tr>
    `;
    this.elements.topCustomersTable.insertAdjacentHTML('beforeend', statsHtml);

    // Add customer rows
    customers.forEach((customer, index) => {
        const rank = index + 1;
        const customerName = customer.customer_name || 'Unknown';
        const revenue = parseFloat(customer.revenue_value) || 0;
        const percentage = customer.revenue_percentage || 0;
        const invoiceCount = customer.invoice_count || 0;

        // Determine styling based on rank
        let rowClass = '';
        let rankBadge = 'badge bg-secondary';

        if (rank === 1) {
            rowClass = 'table-warning';
            rankBadge = 'badge bg-warning text-dark';
        } else if (rank === 2) {
            rowClass = 'table-light';
            rankBadge = 'badge bg-light text-dark border';
        } else if (rank === 3) {
            rowClass = 'table-info';
            rankBadge = 'badge bg-info text-dark';
        }

        const rowHtml = `
            <tr class="${rowClass}">
                <td class="align-middle">
                    <span class="${rankBadge}">${rank}</span>
                </td>
                <td>
                    <div class="fw-bold">${customerName}</div>
                    <div class="text-muted small">
                        <i class="fas fa-receipt me-1"></i>${invoiceCount} invoices
                    </div>
                </td>
                <td class="align-middle text-end">
                    <div class="fw-bold text-primary">$${revenue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                    <div class="small text-muted">${percentage.toFixed(1)}% of total</div>
                </td>
                <td class="align-middle" width="150">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success"
                             style="width: ${Math.min(percentage, 100)}%"
                             role="progressbar">
                        </div>
                    </div>
                </td>
            </tr>
        `;

        this.elements.topCustomersTable.insertAdjacentHTML('beforeend', rowHtml);
    });
}
}

// Initialize with error handling
document.addEventListener('DOMContentLoaded', function() {
    try {
        new KenyaFibreDashboard();
    } catch (error) {
        console.error('Failed to initialize dashboard:', error);
        document.getElementById('errorMessage').classList.remove('d-none');
        document.getElementById('errorText').textContent = 'Failed to initialize dashboard: ' + error.message;
        document.getElementById('loadingSpinner').classList.add('d-none');
    }
});
</script>
</body>
</html>
@endsection
