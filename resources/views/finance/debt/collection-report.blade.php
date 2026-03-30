@extends('layouts.app')

@section('title', 'Collection Performance Report')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 text-primary">
                <i class="fas fa-chart-line me-2"></i>Collection Performance Report
            </h1>
            <p class="text-muted mb-0">Track and analyze collection performance metrics by currency</p>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{ route('finance.debt.dashboard') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
            </a>
            <button class="btn btn-success" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Print Report
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('finance.debt.collection.report') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                           value="{{ $startDate }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                           value="{{ $endDate }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="period" class="form-label">Period</label>
                    <select name="period" id="period" class="form-control">
                        <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Key Metrics - Dual Currency -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Collected (USD)</div>
                            <div class="h5 mb-0">${{ number_format($collectionSummary['total_collected_usd'] ?? 0, 2) }}</div>
                            <div class="mt-2 text-xs">
                                <i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($startDate)->format('M d') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Collected (KSH)</div>
                            <div class="h5 mb-0">KSH {{ number_format($collectionSummary['total_collected_ksh'] ?? 0, 2) }}</div>
                            <div class="mt-2 text-xs">
                                <i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($startDate)->format('M d') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shilling-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Collection Rate (USD)</div>
                            <div class="h5 mb-0">{{ number_format($collectionSummary['collection_rate_usd'] ?? 0, 1) }}%</div>
                            <div class="mt-2 text-xs">
                                <i class="fas fa-chart-line me-1"></i>${{ number_format($collectionSummary['total_invoiced_usd'] ?? 0, 0) }} invoiced
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Collection Rate (KSH)</div>
                            <div class="h5 mb-0">{{ number_format($collectionSummary['collection_rate_ksh'] ?? 0, 1) }}%</div>
                            <div class="mt-2 text-xs">
                                <i class="fas fa-chart-line me-1"></i>KSH {{ number_format($collectionSummary['total_invoiced_ksh'] ?? 0, 0) }} invoiced
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card bg-secondary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Avg Collection Period</div>
                            <div class="h5 mb-0">{{ $collectionSummary['average_collection_period'] ?? 0 }} days</div>
                            <div class="mt-2 text-xs">
                                <i class="fas fa-clock me-1"></i>From due date to payment
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Overdue Collected (USD)</div>
                            <div class="h5 mb-0">${{ number_format($collectionSummary['overdue_collected_usd'] ?? 0, 2) }}</div>
                            <div class="mt-2 text-xs">
                                <i class="fas fa-exclamation-triangle me-1"></i>Recovered from overdue
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-dark text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Overdue Collected (KSH)</div>
                            <div class="h5 mb-0">KSH {{ number_format($collectionSummary['overdue_collected_ksh'] ?? 0, 2) }}</div>
                            <div class="mt-2 text-xs">
                                <i class="fas fa-exclamation-triangle me-1"></i>Recovered from overdue
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection Trend Chart -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-area me-2"></i>Collection Trend by Currency
                    </h5>
                </div>
                <div class="card-body">
                    @if(!empty($collectionTrend))
                        <canvas id="collectionTrendChart" height="250"></canvas>
                    @else
                        <p class="text-muted text-center py-5">No collection data available for the selected period.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Aging Collection -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-hourglass-half me-2"></i>Aging Collection Analysis
                    </h5>
                </div>
                <div class="card-body">
                    @if(!empty($agingCollection))
                        <canvas id="agingCollectionChart" height="250"></canvas>
                    @else
                        <p class="text-muted text-center py-5">No aging data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Collector Performance -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-tie me-2"></i>Top Collectors Performance (USD)
                    </h5>
                </div>
                <div class="card-body">
                    @if($collectorPerformance->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Collector</th>
                                        <th>Assigned</th>
                                        <th>Collected (USD)</th>
                                        <th>Rate</th>
                                        <th>Performance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($collectorPerformance as $collector)
                                    <tr>
                                        <td>{{ $collector['name'] }}</td>
                                        <td>{{ $collector['total_assigned'] }}</td>
                                        <td>${{ number_format($collector['collected_amount_usd'] ?? 0, 2) }}</td>
                                        <td>{{ number_format($collector['collection_rate_usd'] ?? 0, 1) }}%</td>
                                        <td>
                                            @php
                                                $percentage = min(100, $collector['collection_rate_usd'] ?? 0);
                                                $color = $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger');
                                            @endphp
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-{{ $color }}"
                                                     role="progressbar"
                                                     style="width: {{ $percentage }}%;"
                                                     aria-valuenow="{{ $percentage }}"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100">
                                                    {{ number_format($percentage, 0) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No collector performance data available.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-tie me-2"></i>Top Collectors Performance (KSH)
                    </h5>
                </div>
                <div class="card-body">
                    @if($collectorPerformance->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Collector</th>
                                        <th>Assigned</th>
                                        <th>Collected (KSH)</th>
                                        <th>Rate</th>
                                        <th>Performance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($collectorPerformance as $collector)
                                    <tr>
                                        <td>{{ $collector['name'] }}</td>
                                        <td>{{ $collector['total_assigned'] }}</td>
                                        <td>KSH {{ number_format($collector['collected_amount_ksh'] ?? 0, 2) }}</td>
                                        <td>{{ number_format($collector['collection_rate_ksh'] ?? 0, 1) }}%</td>
                                        <td>
                                            @php
                                                $percentage = min(100, $collector['collection_rate_ksh'] ?? 0);
                                                $color = $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger');
                                            @endphp
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-{{ $color }}"
                                                     role="progressbar"
                                                     style="width: {{ $percentage }}%;"
                                                     aria-valuenow="{{ $percentage }}"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100">
                                                    {{ number_format($percentage, 0) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No collector performance data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performing Customers -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-trophy me-2"></i>Top Performing Customers (USD)
                    </h5>
                </div>
                <div class="card-body">
                    @if($topPerformingCustomers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Payments</th>
                                        <th>Total Paid (USD)</th>
                                        <th>Avg Payment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topPerformingCustomers->where('currency', 'USD') as $customer)
                                    <tr>
                                        <td>
                                            {{ $customer->name }}
                                            @if($customer->company_name)
                                                <br><small class="text-muted">{{ $customer->company_name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $customer->payments_count }}</td>
                                        <td>${{ number_format($customer->payments_sum_amount, 2) }}</td>
                                        <td>
                                            @if($customer->payments_count > 0)
                                                ${{ number_format($customer->payments_sum_amount / $customer->payments_count, 2) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No USD payment data available.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-trophy me-2"></i>Top Performing Customers (KSH)
                    </h5>
                </div>
                <div class="card-body">
                    @if($topPerformingCustomers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Payments</th>
                                        <th>Total Paid (KSH)</th>
                                        <th>Avg Payment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topPerformingCustomers->where('currency', 'KSH') as $customer)
                                    <tr>
                                        <td>
                                            {{ $customer->name }}
                                            @if($customer->company_name)
                                                <br><small class="text-muted">{{ $customer->company_name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $customer->payments_count }}</td>
                                        <td>KSH {{ number_format($customer->payments_sum_amount, 2) }}</td>
                                        <td>
                                            @if($customer->payments_count > 0)
                                                KSH {{ number_format($customer->payments_sum_amount / $customer->payments_count, 2) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No KSH payment data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Problematic Customers -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>Problematic Customers (Frequently Overdue)
            </h5>
        </div>
        <div class="card-body">
            @if($problematicCustomers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Overdue Invoices</th>
                                <th>Total Overdue (USD)</th>
                                <th>Total Overdue (KSH)</th>
                                <th>Avg Overdue</th>
                                <th>Last Payment</th>
                                <th>Contact</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($problematicCustomers as $customer)
                            <tr>
                                <td>
                                    <strong>{{ $customer->name }}</strong>
                                    @if($customer->company_name)
                                        <br><small class="text-muted">{{ $customer->company_name }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-danger">{{ $customer->billings_count }}</span>
                                </td>
                                <td class="font-weight-bold text-danger">
                                    @if($customer->billings_sum_total_amount_usd > 0)
                                        ${{ number_format($customer->billings_sum_total_amount_usd, 2) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="font-weight-bold text-danger">
                                    @if($customer->billings_sum_total_amount_ksh > 0)
                                        KSH {{ number_format($customer->billings_sum_total_amount_ksh, 2) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->billings_count > 0 && ($customer->billings_sum_total_amount_usd > 0 || $customer->billings_sum_total_amount_ksh > 0))
                                        @if($customer->billings_sum_total_amount_usd > 0)
                                            ${{ number_format($customer->billings_sum_total_amount_usd / $customer->billings_count, 2) }}
                                        @endif
                                        @if($customer->billings_sum_total_amount_ksh > 0)
                                            KSH {{ number_format($customer->billings_sum_total_amount_ksh / $customer->billings_count, 2) }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $lastPayment = $customer->payments()->latest()->first();
                                    @endphp
                                    @if($lastPayment)
                                        {{ \Carbon\Carbon::parse($lastPayment->payment_date)->format('M d, Y') }}
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($lastPayment->payment_date)->diffForHumans() }}</small>
                                    @else
                                        <span class="text-danger">Never</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $customer->email ?? 'N/A' }}
                                    @if($customer->phone)
                                        <br><small>{{ $customer->phone }}</small>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-3">No problematic customers identified.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Collection Trend Chart
    @if(!empty($collectionTrend))
        const trendCtx = document.getElementById('collectionTrendChart').getContext('2d');
        const trendLabels = @json(array_column($collectionTrend, 'period'));
        const usdData = @json(array_column($collectionTrend, 'total_collected_usd'));
        const kshData = @json(array_column($collectionTrend, 'total_collected_ksh'));

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [
                    {
                        label: 'USD Collection',
                        data: usdData,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y-usd'
                    },
                    {
                        label: 'KSH Collection',
                        data: kshData,
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y-ksh'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Collection Trend by Currency'
                    }
                },
                scales: {
                    'y-usd': {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'USD Amount ($)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    },
                    'y-ksh': {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'KSH Amount'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'KSH ' + value.toLocaleString();
                            }
                        },
                        grid: {
                            drawOnChartArea: false,
                        }
                    }
                }
            }
        });
    @endif

    // Aging Collection Chart
    @if(!empty($agingCollection))
        const agingCtx = document.getElementById('agingCollectionChart').getContext('2d');

        new Chart(agingCtx, {
            type: 'doughnut',
            data: {
                labels: ['Current', '1-30 Days', '31-60 Days', '61-90 Days', 'Over 90 Days'],
                datasets: [
                    {
                        label: 'USD Amount',
                        data: [
                            {{ $agingCollection['current_usd'] ?? 0 }},
                            {{ $agingCollection['1_30_days_usd'] ?? 0 }},
                            {{ $agingCollection['31_60_days_usd'] ?? 0 }},
                            {{ $agingCollection['61_90_days_usd'] ?? 0 }},
                            {{ $agingCollection['over_90_days_usd'] ?? 0 }}
                        ],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.7)',
                            'rgba(23, 162, 184, 0.7)',
                            'rgba(255, 193, 7, 0.7)',
                            'rgba(253, 126, 20, 0.7)',
                            'rgba(220, 53, 69, 0.7)'
                        ]
                    },
                    {
                        label: 'KSH Amount',
                        data: [
                            {{ $agingCollection['current_ksh'] ?? 0 }},
                            {{ $agingCollection['1_30_days_ksh'] ?? 0 }},
                            {{ $agingCollection['31_60_days_ksh'] ?? 0 }},
                            {{ $agingCollection['61_90_days_ksh'] ?? 0 }},
                            {{ $agingCollection['over_90_days_ksh'] ?? 0 }}
                        ],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.3)',
                            'rgba(23, 162, 184, 0.3)',
                            'rgba(255, 193, 7, 0.3)',
                            'rgba(253, 126, 20, 0.3)',
                            'rgba(220, 53, 69, 0.3)'
                        ]
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Aging Collection Analysis by Currency'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.dataset.label || '';
                                const value = context.raw || 0;
                                const currency = label.includes('USD') ? '$' : 'KSH';
                                return label + ': ' + currency + ' ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    @endif
});
</script>
@endpush

<style>
.card {
    border-radius: 0.5rem;
}

.progress {
    border-radius: 0.25rem;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
</style>
