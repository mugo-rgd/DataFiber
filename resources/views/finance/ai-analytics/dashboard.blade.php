{{-- resources/views/finance/ai-analytics/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'AI-Powered Debt Analytics')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="page-title mb-0">
                            <i class="fas fa-brain text-primary me-2"></i>AI-Powered Debt Analytics
                        </h4>
                        <p class="text-muted mb-0">Intelligent insights and predictions for debt management across USD and KSH</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('finance.ai.report') }}" class="btn btn-outline-primary">
                            <i class="fas fa-file-pdf me-1"></i> Generate Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Currency Filter -->
    <div class="row mb-3">
        <div class="col-md-3">
            <select class="form-select" id="currencyFilter" onchange="filterByCurrency(this.value)">
                <option value="all" {{ request('currency', 'all') == 'all' ? 'selected' : '' }}>All Currencies</option>
                <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD Only</option>
                <option value="KSH" {{ request('currency') == 'KSH' ? 'selected' : '' }}>KSH Only</option>
            </select>
        </div>
    </div>

    <!-- Alert based on AI insights -->
    @if(isset($insights['alerts']) && count($insights['alerts']) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">AI Alert</h5>
                        <p class="mb-0">{{ $insights['alerts'][0] ?? 'Attention needed' }}</p>
                        @if(count($insights['alerts']) > 1)
                        <small class="text-muted">+{{ count($insights['alerts']) - 1 }} more alerts</small>
                        @endif
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
    @endif

    <!-- Key Metrics - Dual Currency -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Outstanding (USD)</h6>
                            <h3 class="mb-0">${{ number_format($metrics['total_outstanding_usd'] ?? 0, 0) }}</h3>
                            <small class="text-muted">
                                <span class="text-danger">{{ $metrics['overdue_count_usd'] ?? 0 }} overdue</span>
                            </small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary rounded-circle">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Outstanding (KSH)</h6>
                            <h3 class="mb-0">KSH {{ number_format($metrics['total_outstanding_ksh'] ?? 0, 0) }}</h3>
                            <small class="text-muted">
                                <span class="text-danger">{{ $metrics['overdue_count_ksh'] ?? 0 }} overdue</span>
                            </small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success rounded-circle">
                                <i class="fas fa-shilling-sign"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Collection Rate (USD)</h6>
                            <h3 class="mb-0">{{ number_format($metrics['collection_rate_usd'] ?? 0, 1) }}%</h3>
                            <small class="text-muted">
                                @if(($metrics['collection_rate_usd'] ?? 0) < 50)
                                <span class="text-danger">Needs improvement</span>
                                @elseif(($metrics['collection_rate_usd'] ?? 0) < 80)
                                <span class="text-warning">Moderate</span>
                                @else
                                <span class="text-success">Good</span>
                                @endif
                            </small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning rounded-circle">
                                <i class="fas fa-chart-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Collection Rate (KSH)</h6>
                            <h3 class="mb-0">{{ number_format($metrics['collection_rate_ksh'] ?? 0, 1) }}%</h3>
                            <small class="text-muted">
                                @if(($metrics['collection_rate_ksh'] ?? 0) < 50)
                                <span class="text-danger">Needs improvement</span>
                                @elseif(($metrics['collection_rate_ksh'] ?? 0) < 80)
                                <span class="text-warning">Moderate</span>
                                @else
                                <span class="text-success">Good</span>
                                @endif
                            </small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-info rounded-circle">
                                <i class="fas fa-percentage"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card border-left-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Overdue Amount (USD)</h6>
                            <h3 class="mb-0 text-danger">${{ number_format($metrics['overdue_amount_usd'] ?? 0, 0) }}</h3>
                            <small class="text-muted">
                                {{ $metrics['overdue_percentage_usd'] ?? 0 }}% of total
                            </small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-secondary rounded-circle">
                                <i class="fas fa-exclamation-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card border-left-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Overdue Amount (KSH)</h6>
                            <h3 class="mb-0 text-danger">KSH {{ number_format($metrics['overdue_amount_ksh'] ?? 0, 0) }}</h3>
                            <small class="text-muted">
                                {{ $metrics['overdue_percentage_ksh'] ?? 0 }}% of total
                            </small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-dark rounded-circle">
                                <i class="fas fa-exclamation-triangle"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card border-left-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Today's Collections</h6>
                            <h3 class="mb-0 text-primary">${{ number_format($metrics['today_collections_usd'] ?? 0, 0) }}</h3>
                            <h5 class="mb-0 text-success">KSH {{ number_format($metrics['today_collections_ksh'] ?? 0, 0) }}</h5>
                            <small class="text-muted">Updated: {{ now()->format('h:i A') }}</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-danger rounded-circle">
                                <i class="fas fa-hand-holding-usd"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Insights & Charts -->
    <div class="row mb-4">
        <!-- AI Insights -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb text-warning me-2"></i>AI Insights
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Key Findings -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-search me-2"></i>Key Findings
                        </h6>
                        <div class="list-group list-group-flush">
                            @foreach($insights['key_findings'] as $finding)
                            <div class="list-group-item d-flex align-items-start">
                                <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                <span>{{ $finding }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Risk Analysis -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>Risk Analysis
                        </h6>
                        <div class="row">
                            @foreach($insights['risk_analysis'] as $risk)
                            <div class="col-md-6 mb-2">
                                <div class="card border-warning">
                                    <div class="card-body py-2">
                                        <p class="mb-0 small">{{ $risk }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Recommendations -->
                    <div>
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-bullseye me-2"></i>Recommended Actions
                        </h6>
                        <div class="list-group">
                            @foreach($insights['recommendations'] as $index => $recommendation)
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Action {{ $index + 1 }}</h6>
                                    <small class="text-muted">Priority: High</small>
                                </div>
                                <p class="mb-1">{{ $recommendation }}</p>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aging Analysis & Top Debtors -->
        <div class="col-lg-6">
            <!-- Aging Analysis with Dual Currency -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt text-info me-2"></i>Aging Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Age Bucket</th>
                                    <th class="text-end">USD</th>
                                    <th class="text-end">KSH</th>
                                    <th class="text-end">Invoices</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalUsd = $agingAnalysis['total_usd'] ?? 0;
                                    $totalKsh = $agingAnalysis['total_ksh'] ?? 0;
                                    $totalCombined = $totalUsd + ($totalKsh / 130);
                                    $buckets = [
                                        [
                                            'label' => 'Current (0-30 days)',
                                            'usd' => $agingAnalysis['current_usd'] ?? 0,
                                            'ksh' => $agingAnalysis['current_ksh'] ?? 0,
                                            'count' => $agingAnalysis['current_count'] ?? 0
                                        ],
                                        [
                                            'label' => '31-60 days',
                                            'usd' => $agingAnalysis['days_31_60_usd'] ?? 0,
                                            'ksh' => $agingAnalysis['days_31_60_ksh'] ?? 0,
                                            'count' => $agingAnalysis['days_31_60_count'] ?? 0
                                        ],
                                        [
                                            'label' => '61-90 days',
                                            'usd' => $agingAnalysis['days_61_90_usd'] ?? 0,
                                            'ksh' => $agingAnalysis['days_61_90_ksh'] ?? 0,
                                            'count' => $agingAnalysis['days_61_90_count'] ?? 0
                                        ],
                                        [
                                            'label' => 'Over 90 days',
                                            'usd' => $agingAnalysis['days_over_90_usd'] ?? 0,
                                            'ksh' => $agingAnalysis['days_over_90_ksh'] ?? 0,
                                            'count' => $agingAnalysis['days_over_90_count'] ?? 0
                                        ]
                                    ];
                                @endphp
                                @foreach($buckets as $bucket)
                                <tr>
                                    <td>{{ $bucket['label'] }}</td>
                                    <td class="text-end">${{ number_format($bucket['usd'], 0) }}</td>
                                    <td class="text-end">KSH {{ number_format($bucket['ksh'], 0) }}</td>
                                    <td class="text-end">{{ $bucket['count'] }}</td>
                                    <td class="text-end">
                                        @php
                                            $bucketCombined = $bucket['usd'] + ($bucket['ksh'] / 130);
                                            $percentage = $totalCombined > 0 ? ($bucketCombined / $totalCombined) * 100 : 0;
                                        @endphp
                                        {{ number_format($percentage, 1) }}%
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="table-active">
                                    <td><strong>Total</strong></td>
                                    <td class="text-end"><strong>${{ number_format($totalUsd, 0) }}</strong></td>
                                    <td class="text-end"><strong>KSH {{ number_format($totalKsh, 0) }}</strong></td>
                                    <td class="text-end"><strong>{{ array_sum(array_column($buckets, 'count')) }}</strong></td>
                                    <td class="text-end"><strong>100%</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Debtors with Currency Split -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-users text-danger me-2"></i>Top 5 Debtors
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($topDebtors as $debtor)
                        <a href="{{ route('finance.ai.customer', $debtor['id']) }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{ $debtor['name'] }}</h6>
                                <small class="text-muted">{{ $debtor['email'] }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $debtor['risk_level'] == 'critical' ? 'danger' : ($debtor['risk_level'] == 'high' ? 'warning' : 'secondary') }} rounded-pill mb-1">
                                    {{ $debtor['risk_level'] }}
                                </span>
                                <div>
                                    @if($debtor['outstanding_usd'] > 0)
                                        <strong>${{ number_format($debtor['outstanding_usd'], 0) }}</strong>
                                    @endif
                                    @if($debtor['outstanding_ksh'] > 0)
                                        <br><strong>KSH {{ number_format($debtor['outstanding_ksh'], 0) }}</strong>
                                    @endif
                                    <small class="text-muted d-block">{{ $debtor['overdue_invoices'] }} overdue</small>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection Trends with Dual Currency -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line text-success me-2"></i>Collection Trends (Last 30 Days)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <canvas id="collectionChart" height="250"></canvas>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="text-success mb-3">Trend Summary</h6>
                                    <div class="mb-3">
                                        <small class="text-muted d-block">Total Collected (USD)</small>
                                        <h4 class="text-primary">${{ number_format($collectionTrends['total_collected_usd'] ?? 0, 0) }}</h4>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted d-block">Total Collected (KSH)</small>
                                        <h4 class="text-success">KSH {{ number_format($collectionTrends['total_collected_ksh'] ?? 0, 0) }}</h4>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted d-block">Average Daily (USD)</small>
                                        <h4 class="text-primary">${{ number_format($collectionTrends['average_daily_usd'] ?? 0, 0) }}</h4>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted d-block">Average Daily (KSH)</small>
                                        <h4 class="text-success">KSH {{ number_format($collectionTrends['average_daily_ksh'] ?? 0, 0) }}</h4>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted d-block">Total Payments</small>
                                        <h4 class="text-success">{{ array_sum($collectionTrends['counts'] ?? [0]) }}</h4>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Trend Analysis</small>
                                        @php
                                            $trend = $collectionTrends['trend'] ?? ['direction' => 'stable', 'percentage' => 0, 'message' => 'No data'];
                                        @endphp
                                        <h4 class="{{ $trend['direction'] == 'up' ? 'text-success' : ($trend['direction'] == 'down' ? 'text-danger' : 'text-secondary') }}">
                                            <i class="fas fa-arrow-{{ $trend['direction'] }} me-1"></i>
                                            {{ $trend['percentage'] }}%
                                        </h4>
                                        <small class="text-muted">{{ $trend['message'] }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Currency filter function
    function filterByCurrency(currency) {
        const url = new URL(window.location.href);
        url.searchParams.set('currency', currency);
        window.location.href = url.toString();
    }

    // Collection Trend Chart with Dual Currency
    const ctx = document.getElementById('collectionChart').getContext('2d');
    const collectionChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($collectionTrends['labels'] ?? []),
            datasets: [
                {
                    label: 'USD Collections ($)',
                    data: @json($collectionTrends['amounts_usd'] ?? []),
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.3,
                    fill: true,
                    yAxisID: 'y-usd'
                },
                {
                    label: 'KSH Collections',
                    data: @json($collectionTrends['amounts_ksh'] ?? []),
                    borderColor: 'rgb(40, 167, 69)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.3,
                    fill: true,
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
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.dataset.label || '';
                            const value = context.parsed.y || 0;
                            if (label.includes('USD')) {
                                return label + ': $' + value.toLocaleString();
                            } else {
                                return label + ': KSH ' + value.toLocaleString();
                            }
                        }
                    }
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
</script>
@endsection
