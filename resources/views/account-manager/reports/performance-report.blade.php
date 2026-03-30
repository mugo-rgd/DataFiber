<!-- G:\project\darkfibre-crm\resources\views\account-manager\reports\performance-report.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Performance Report</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('account-manager.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('account-manager.reports.performance') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Performance Report</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="performanceReportFilter" class="row g-3" method="GET" action="{{ route('account-manager.reports.performance') }}">
                        <div class="col-md-3">
                            <label for="dateRange" class="form-label">Date Range</label>
                            <select class="form-select" id="dateRange" name="dateRange">
                                <option value="7" {{ request('dateRange', 30) == 7 ? 'selected' : '' }}>Last 7 Days</option>
                                <option value="30" {{ request('dateRange', 30) == 30 ? 'selected' : '' }}>Last 30 Days</option>
                                <option value="90" {{ request('dateRange', 30) == 90 ? 'selected' : '' }}>Last 90 Days</option>
                                <option value="365" {{ request('dateRange', 30) == 365 ? 'selected' : '' }}>Last 12 Months</option>
                                <option value="custom" {{ request('dateRange') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3 custom-date-range {{ request('dateRange') == 'custom' ? '' : 'd-none' }}">
                            <label for="startDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="startDate" name="startDate" value="{{ request('startDate') }}">
                        </div>
                        <div class="col-md-3 custom-date-range {{ request('dateRange') == 'custom' ? '' : 'd-none' }}">
                            <label for="endDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="endDate" name="endDate" value="{{ request('endDate') }}">
                        </div>
                        @if(auth()->user()->isAdmin() || auth()->user()->isSystemAdmin())
                        <div class="col-md-3">
                            <label for="accountManager" class="form-label">Account Manager</label>
                            <select class="form-select" id="accountManager" name="accountManager">
                                <option value="all" {{ request('accountManager', 'all') == 'all' ? 'selected' : '' }}>All Managers</option>
                                @foreach($accountManagers as $manager)
                                    <option value="{{ $manager->id }}" {{ request('accountManager') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <input type="hidden" name="accountManager" value="{{ auth()->id() }}">
                        @endif
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Total Revenue</p>
                            <h4 class="mb-2">${{ number_format($performanceData['totalRevenue'], 2) }}</h4>
                            <p class="text-muted mb-0">
                                <span class="{{ ($growthMetrics['totalRevenue'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }} me-2">
                                    <i class="fas fa-arrow-{{ ($growthMetrics['totalRevenue'] ?? 0) >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($growthMetrics['totalRevenue'] ?? 0) }}%
                                </span>
                                from previous period
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="fas fa-dollar-sign font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">New Customers</p>
                            <h4 class="mb-2">{{ $performanceData['newCustomers'] }}</h4>
                            <p class="text-muted mb-0">
                                <span class="{{ ($growthMetrics['newCustomers'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }} me-2">
                                    <i class="fas fa-arrow-{{ ($growthMetrics['newCustomers'] ?? 0) >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($growthMetrics['newCustomers'] ?? 0) }}%
                                </span>
                                from previous period
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="fas fa-user-plus font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Average Deal Size</p>
                            <h4 class="mb-2">${{ number_format($performanceData['averageDealSize'], 2) }}</h4>
                            <p class="text-muted mb-0">
                                <span class="{{ ($growthMetrics['averageDealSize'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }} me-2">
                                    <i class="fas fa-arrow-{{ ($growthMetrics['averageDealSize'] ?? 0) >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($growthMetrics['averageDealSize'] ?? 0) }}%
                                </span>
                                from previous period
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="fas fa-chart-pie font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Conversion Rate</p>
                            <h4 class="mb-2">{{ number_format($performanceData['conversionRate'], 2) }}%</h4>
                            <p class="text-muted mb-0">
                                <span class="{{ ($growthMetrics['conversionRate'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }} me-2">
                                    <i class="fas fa-arrow-{{ ($growthMetrics['conversionRate'] ?? 0) >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($growthMetrics['conversionRate'] ?? 0) }}%
                                </span>
                                from previous period
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="fas fa-percentage font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Metrics -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Collection Rate</p>
                            <h4 class="mb-2">{{ number_format($performanceData['collectionRate'], 2) }}%</h4>
                            <p class="text-muted mb-0">
                                <span class="{{ ($growthMetrics['collectionRate'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }} me-2">
                                    <i class="fas fa-arrow-{{ ($growthMetrics['collectionRate'] ?? 0) >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($growthMetrics['collectionRate'] ?? 0) }}%
                                </span>
                                from previous period
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="fas fa-credit-card font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Active Leases</p>
                            <h4 class="mb-2">{{ $performanceData['activeLeases'] }}</h4>
                            <p class="text-muted mb-0">
                                <span class="{{ ($growthMetrics['activeLeases'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }} me-2">
                                    <i class="fas fa-arrow-{{ ($growthMetrics['activeLeases'] ?? 0) >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($growthMetrics['activeLeases'] ?? 0) }}%
                                </span>
                                from previous period
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="fas fa-network-wired font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">New Leases</p>
                            <h4 class="mb-2">{{ $performanceData['newLeases'] }}</h4>
                            <p class="text-muted mb-0">
                                <span class="{{ ($growthMetrics['newLeases'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }} me-2">
                                    <i class="fas fa-arrow-{{ ($growthMetrics['newLeases'] ?? 0) >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($growthMetrics['newLeases'] ?? 0) }}%
                                </span>
                                from previous period
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="fas fa-file-contract font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Resolved Tickets</p>
                            <h4 class="mb-2">{{ $performanceData['resolvedTickets'] }}</h4>
                            <p class="text-muted mb-0">
                                <span class="{{ ($growthMetrics['resolvedTickets'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }} me-2">
                                    <i class="fas fa-arrow-{{ ($growthMetrics['resolvedTickets'] ?? 0) >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($growthMetrics['resolvedTickets'] ?? 0) }}%
                                </span>
                                from previous period
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="fas fa-ticket-alt font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Revenue Trend</h4>
                    <div id="revenue-chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Lease Status Distribution</h4>
                    <div id="deal-status-chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Charts -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Services Performance</h4>
                    <div id="services-chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Ticket Metrics</h4>
                    <div id="ticket-metrics-chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manager Performance Table (Admin only) -->
    @if((auth()->user()->isAdmin() || auth()->user()->isSystemAdmin()) && $accountManagerId == 'all')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Account Manager Performance Comparison</h4>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Account Manager</th>
                                    <th>Total Revenue</th>
                                    <th>Active Customers</th>
                                    <th>Active Leases</th>
                                    <th>New Leases</th>
                                    <th>New Customers</th>
                                    <th>Conversion Rate</th>
                                    <th>Avg. Deal Size</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($managerPerformance as $performance)
                                <tr>
                                    <td>{{ $performance['name'] }}</td>
                                    <td>${{ number_format($performance['total_revenue'], 2) }}</td>
                                    <td>{{ $performance['active_customers'] }}</td>
                                    <td>{{ $performance['active_leases'] }}</td>
                                    <td>{{ $performance['deals_closed'] }}</td>
                                    <td>{{ $performance['new_customers'] }}</td>
                                    <td>{{ number_format($performance['conversion_rate'], 2) }}%</td>
                                    <td>${{ number_format($performance['avg_deal_size'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<!-- Apexcharts -->
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Date Range Toggle
        const dateRangeSelect = document.getElementById('dateRange');
        const customDateRange = document.querySelectorAll('.custom-date-range');

        dateRangeSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateRange.forEach(el => el.classList.remove('d-none'));
            } else {
                customDateRange.forEach(el => el.classList.add('d-none'));
            }
        });

        // Revenue Chart
        var revenueChartOptions = {
            series: [{
                name: 'Revenue',
                data: @json($charts['revenueTrend']['data'] ?? [])
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: {
                    show: false
                }
            },
            colors: ['#556ee6'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: @json($charts['revenueTrend']['labels'] ?? [])
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return "$" + value.toLocaleString();
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return "$" + value.toLocaleString();
                    }
                }
            }
        };

        var revenueChart = new ApexCharts(document.querySelector("#revenue-chart"), revenueChartOptions);
        revenueChart.render();

        // Deal Status Chart
        var dealStatusChartOptions = {
            series: @json($charts['dealStatus']['data'] ?? []),
            chart: {
                height: 320,
                type: 'donut',
            },
            labels: @json($charts['dealStatus']['labels'] ?? []),
            colors: ['#34c38f', '#f1b44c', '#f46a6a', '#50a5f1'],
            legend: {
                position: 'bottom'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%'
                    }
                }
            }
        };

        var dealStatusChart = new ApexCharts(document.querySelector("#deal-status-chart"), dealStatusChartOptions);
        dealStatusChart.render();

        // Services Chart
        var servicesChartOptions = {
            series: [{
                data: @json($charts['servicesPerformance']['data'] ?? [])
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: @json($charts['servicesPerformance']['labels'] ?? []),
            }
        };

        var servicesChart = new ApexCharts(document.querySelector("#services-chart"), servicesChartOptions);
        servicesChart.render();

        // Ticket Metrics Chart
        var ticketMetricsChartOptions = {
            series: [
                {
                    name: 'Tickets by Status',
                    type: 'column',
                    data: @json($charts['ticketMetrics']['status']['data'] ?? [])
                },
                {
                    name: 'Tickets by Priority',
                    type: 'line',
                    data: @json($charts['ticketMetrics']['priority']['data'] ?? [])
                }
            ],
            chart: {
                height: 350,
                type: 'line',
                toolbar: {
                    show: false
                }
            },
            stroke: {
                width: [0, 4]
            },
            dataLabels: {
                enabled: false
            },
            labels: @json($charts['ticketMetrics']['status']['labels'] ?? []),
            xaxis: {
                type: 'category'
            },
            yaxis: [{
                title: {
                    text: 'Tickets by Status',
                },
            }, {
                opposite: true,
                title: {
                    text: 'Tickets by Priority'
                }
            }]
        };

        var ticketMetricsChart = new ApexCharts(document.querySelector("#ticket-metrics-chart"), ticketMetricsChartOptions);
        ticketMetricsChart.render();
    });
</script>
@endpush
