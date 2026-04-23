@extends('layouts.app')

@section('title', 'Team Performance - Marketing Admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-trophy me-2"></i>Team Performance Dashboard
        </h1>
        <div>
            <a href="{{ route('marketing-admin.performance.export', ['period' => $period, 'currency' => $currency]) }}"
               class="btn btn-success btn-sm">
                <i class="fas fa-download me-1"></i> Export Report
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('marketing-admin.performance') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="period" class="form-label">Time Period</label>
                    <select name="period" id="period" class="form-select">
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Current Month</option>
                        <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>Current Quarter</option>
                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Current Year</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="currency" class="form-label">Currency View</label>
                    <select name="currency" id="currency" class="form-select">
                        <option value="all" {{ $currency == 'all' ? 'selected' : '' }}>All Currencies</option>
                        <option value="USD" {{ $currency == 'USD' ? 'selected' : '' }}>USD Only</option>
                        <option value="KSH" {{ $currency == 'KSH' ? 'selected' : '' }}>KSH Only</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Apply Filter
                    </button>
                    <a href="{{ route('marketing-admin.performance') }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Team Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Team MRR
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $currency == 'KSH' ? 'KSh' : '$' }} {{ number_format($teamAggregates['total_mrr'], 2) }}
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
                                Active Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($teamAggregates['total_customers']) }}
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
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Leases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($teamAggregates['total_leases']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
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
                                Team Performance
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($teamAggregates['avg_performance_score']) }}/100
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Growth Comparison -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-1"></i> Period-over-Period Growth
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <small class="text-muted">MRR Growth</small>
                                <h4 class="mb-0 {{ $comparison['growth']['mrr_growth'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $comparison['growth']['mrr_growth'] >= 0 ? '+' : '' }}{{ $comparison['growth']['mrr_growth'] }}%
                                </h4>
                                <small>{{ $comparison['previous']['total_mrr'] }} → {{ $comparison['current']['total_mrr'] }}</small>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <small class="text-muted">Customer Growth</small>
                                <h4 class="mb-0 {{ $comparison['growth']['customer_growth'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $comparison['growth']['customer_growth'] >= 0 ? '+' : '' }}{{ $comparison['growth']['customer_growth'] }}%
                                </h4>
                                <small>{{ $comparison['previous']['total_customers'] }} → {{ $comparison['current']['total_customers'] }}</small>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <small class="text-muted">Lease Growth</small>
                                <h4 class="mb-0 {{ $comparison['growth']['lease_growth'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $comparison['growth']['lease_growth'] >= 0 ? '+' : '' }}{{ $comparison['growth']['lease_growth'] }}%
                                </h4>
                                <small>{{ $comparison['previous']['total_leases'] }} → {{ $comparison['current']['total_leases'] }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-crown me-1"></i> Top 5 Performers
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Account Manager</th>
                                    <th>Performance Score</th>
                                    <th>Rating</th>
                                    <th>Total MRR</th>
                                    <th>Customers</th>
                                    <th>Leases</th>
                                    <th>New Customers</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPerformers as $index => $performer)
                                <tr>
                                    <td>
                                        @if($index == 0)
                                            <i class="fas fa-trophy text-warning"></i> 1st
                                        @elseif($index == 1)
                                            <i class="fas fa-medal text-secondary"></i> 2nd
                                        @elseif($index == 2)
                                            <i class="fas fa-medal text-bronze"></i> 3rd
                                        @else
                                            {{ $index + 1 }}th
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $performer['manager_name'] }}</strong><br>
                                        <small class="text-muted">{{ $performer['manager_email'] }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px; width: 100px;">
                                                <div class="progress-bar bg-{{ $performer['performance_score'] >= 80 ? 'success' : ($performer['performance_score'] >= 65 ? 'info' : 'warning') }}"
                                                     role="progressbar"
                                                     style="width: {{ $performer['performance_score'] }}%"
                                                     aria-valuenow="{{ $performer['performance_score'] }}"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span>{{ $performer['performance_score'] }}/100</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $performer['performance_rating'] == 'Excellent' ? 'success' : ($performer['performance_rating'] == 'Good' ? 'info' : ($performer['performance_rating'] == 'Average' ? 'warning' : 'danger')) }}">
                                            {{ $performer['performance_rating'] }}
                                        </span>
                                    </td>
                                    <td>{{ $currency == 'KSH' ? 'KSh' : '$' }} {{ number_format($performer['metrics']['total_mrr'], 2) }}</td>
                                    <td>{{ number_format($performer['metrics']['total_customers']) }}</td>
                                    <td>{{ number_format($performer['metrics']['total_leases']) }}</td>
                                    <td>
                                        <span class="text-success">
                                            <i class="fas fa-user-plus"></i> +{{ $performer['metrics']['new_customers'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-info"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#details{{ $performer['manager_id'] }}">
                                            <i class="fas fa-chart-line"></i> View Details
                                        </button>
                                    </td>
                                </tr>
                                <tr class="collapse" id="details{{ $performer['manager_id'] }}">
                                    <td colspan="9">
                                        <div class="p-3 bg-light">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <small class="text-muted">Total Distance</small>
                                                    <p class="mb-0"><strong>{{ number_format($performer['metrics']['total_distance_km'], 2) }} km</strong></p>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Total Cores</small>
                                                    <p class="mb-0"><strong>{{ number_format($performer['metrics']['total_cores']) }}</strong></p>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Avg Lease Value</small>
                                                    <p class="mb-0"><strong>{{ $currency == 'KSH' ? 'KSh' : '$' }} {{ number_format($performer['metrics']['avg_lease_value'], 2) }}</strong></p>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">New Leases</small>
                                                    <p class="mb-0"><strong>+{{ number_format($performer['metrics']['new_leases']) }}</strong></p>
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small class="text-muted">USD Revenue</small>
                                                    <p class="mb-0"><strong>${{ number_format($performer['metrics']['usd_revenue'], 2) }}</strong></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted">KSH Revenue</small>
                                                    <p class="mb-0"><strong>KSh {{ number_format($performer['metrics']['ksh_revenue'], 2) }}</strong></p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Trend Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area me-1"></i> Team Revenue Trend
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueTrendChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Team Performance Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-1"></i> Complete Team Performance
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="performanceTable">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Account Manager</th>
                            <th>Performance Score</th>
                            <th>Rating</th>
                            <th>Total MRR</th>
                            <th>Customers</th>
                            <th>Leases</th>
                            <th>New Customers</th>
                            <th>Distance (km)</th>
                            <th>Cores</th>
                            <th>Avg Lease Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teamMetrics as $index => $metrics)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ $metrics['manager_name'] }}
                                <br>
                                <small class="text-muted">{{ $metrics['manager_email'] }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $metrics['performance_score'] >= 80 ? 'success' : ($metrics['performance_score'] >= 65 ? 'info' : 'warning') }}"
                                             style="width: {{ $metrics['performance_score'] }}%">
                                        </div>
                                    </div>
                                    {{ $metrics['performance_score'] }}/100
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $metrics['performance_rating'] == 'Excellent' ? 'success' : ($metrics['performance_rating'] == 'Good' ? 'info' : ($metrics['performance_rating'] == 'Average' ? 'warning' : 'danger')) }}">
                                    {{ $metrics['performance_rating'] }}
                                </span>
                            </td>
                            <td>{{ $currency == 'KSH' ? 'KSh' : '$' }} {{ number_format($metrics['metrics']['total_mrr'], 2) }}</td>
                            <td>{{ number_format($metrics['metrics']['total_customers']) }}</td>
                            <td>{{ number_format($metrics['metrics']['total_leases']) }}</td>
                            <td class="text-success">+{{ number_format($metrics['metrics']['new_customers']) }}</td>
                            <td>{{ number_format($metrics['metrics']['total_distance_km'], 2) }}</td>
                            <td>{{ number_format($metrics['metrics']['total_cores']) }}</td>
                            <td>{{ $currency == 'KSH' ? 'KSh' : '$' }} {{ number_format($metrics['metrics']['avg_lease_value'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Trend Chart
    const revenueData = @json($revenueTrend);
    const ctx = document.getElementById('revenueTrendChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: revenueData.map(r => r.month),
            datasets: [{
                label: 'Team Revenue ({{ $currency == "KSH" ? "KSh" : "$" }})',
                data: revenueData.map(r => r.revenue),
                borderColor: 'rgba(78, 115, 223, 1)',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.raw;
                            let currency = '{{ $currency }}';
                            if (currency === 'KSH') {
                                return label + ': KSh ' + value.toLocaleString();
                            }
                            return label + ': $' + value.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            let currency = '{{ $currency }}';
                            if (currency === 'KSH') {
                                return 'KSh ' + value.toLocaleString();
                            }
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>

<script>
    // DataTable initialization (optional)
    $(document).ready(function() {
        $('#performanceTable').DataTable({
            pageLength: 10,
            order: [[2, 'desc']], // Sort by performance score
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries"
            }
        });
    });
</script>
@endpush
<script>
    /* Add to your custom CSS file */
.text-bronze {
    color: #cd7f32;
}

.btn-outline-bronze {
    color: #cd7f32;
    border-color: #cd7f32;
}

.btn-outline-bronze:hover {
    background-color: #cd7f32;
    color: white;
}
</script>

@endsection
