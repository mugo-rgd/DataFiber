@extends('layouts.app')

@section('title', 'Financial Analytics Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="page-title mb-0">
                            <i class="fas fa-chart-line text-primary me-2"></i>Financial Analytics Dashboard
                        </h4>
                        <p class="text-muted mb-0">Comprehensive financial analysis and insights</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-download me-1"></i> Export Report
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('finance.financial-analytics.report', ['format' => 'pdf']) }}">PDF</a></li>
                                <li><a class="dropdown-item" href="{{ route('finance.financial-analytics.report', ['format' => 'excel']) }}">Excel</a></li>
                                <li><a class="dropdown-item" href="{{ route('finance.financial-analytics.report', ['format' => 'json']) }}">JSON</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Health Scorecard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-heartbeat me-2"></i>Financial Health Scorecard
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $metrics = $report['financial_metrics'] ?? [];
                            $revenue = $metrics['revenue_metrics'] ?? [];
                            $profitability = $metrics['profitability_metrics'] ?? [];
                            $liquidity = $metrics['liquidity_metrics'] ?? [];
                        @endphp

                        <div class="col-md-3 col-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Collection Rate</h6>
                                    <h3 class="text-primary mb-1">{{ number_format($revenue['collection_rate'] ?? 0, 1) }}%</h3>
                                    <small class="text-muted">
                                        @if(($revenue['collection_rate'] ?? 0) >= 85)
                                        <span class="text-success">Excellent</span>
                                        @elseif(($revenue['collection_rate'] ?? 0) >= 70)
                                        <span class="text-warning">Good</span>
                                        @else
                                        <span class="text-danger">Needs Attention</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Net Profit Margin</h6>
                                    <h3 class="text-success mb-1">{{ number_format($profitability['net_margin'] ?? 0, 1) }}%</h3>
                                    <small class="text-muted">
                                        @if(($profitability['net_margin'] ?? 0) >= 20)
                                        <span class="text-success">Strong</span>
                                        @elseif(($profitability['net_margin'] ?? 0) >= 10)
                                        <span class="text-warning">Moderate</span>
                                        @else
                                        <span class="text-danger">Low</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6 mb-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Current Ratio</h6>
                                    <h3 class="text-info mb-1">{{ number_format($liquidity['current_ratio'] ?? 0, 2) }}</h3>
                                    <small class="text-muted">
                                        @if(($liquidity['current_ratio'] ?? 0) >= 2)
                                        <span class="text-success">Healthy</span>
                                        @elseif(($liquidity['current_ratio'] ?? 0) >= 1.5)
                                        <span class="text-warning">Adequate</span>
                                        @else
                                        <span class="text-danger">Risky</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Revenue Growth</h6>
                                    <h3 class="text-warning mb-1">{{ number_format($metrics['growth_metrics']['revenue_growth'] ?? 0, 1) }}%</h3>
                                    <small class="text-muted">
                                        @if(($metrics['growth_metrics']['revenue_growth'] ?? 0) >= 15)
                                        <span class="text-success">High Growth</span>
                                        @elseif(($metrics['growth_metrics']['revenue_growth'] ?? 0) >= 5)
                                        <span class="text-warning">Stable</span>
                                        @else
                                        <span class="text-danger">Declining</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue & Profit Analysis -->
    <div class="row mb-4">
        <!-- Revenue Metrics -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Revenue Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">Target</th>
                                    <th class="text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $revenueMetrics = [
                                        ['label' => 'Total Revenue', 'value' => $revenue['total_revenue'] ?? 0, 'target' => ($revenue['total_revenue'] ?? 0) * 1.1],
                                        ['label' => 'Collected Revenue', 'value' => $revenue['collected_revenue'] ?? 0, 'target' => $revenue['total_revenue'] ?? 0],
                                        ['label' => 'Outstanding', 'value' => $revenue['outstanding_revenue'] ?? 0, 'target' => ($revenue['total_revenue'] ?? 0) * 0.1],
                                        ['label' => 'Avg Invoice Value', 'value' => $revenue['average_invoice_value'] ?? 0, 'target' => ($revenue['average_invoice_value'] ?? 0) * 1.15],
                                        ['label' => 'Active Customers', 'value' => $revenue['active_customers'] ?? 0, 'target' => ($revenue['active_customers'] ?? 0) * 1.05]
                                    ];
                                @endphp
                                @foreach($revenueMetrics as $metric)
                                <tr>
                                    <td>{{ $metric['label'] }}</td>
                                    <td class="text-end">
                                        @if(strpos($metric['label'], 'Customers') !== false)
                                            {{ number_format($metric['value']) }}
                                        @else
                                            ${{ number_format($metric['value'], 0) }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(strpos($metric['label'], 'Customers') !== false)
                                            {{ number_format($metric['target']) }}
                                        @else
                                            ${{ number_format($metric['target'], 0) }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @php
                                            $percentage = $metric['target'] > 0 ? ($metric['value'] / $metric['target']) * 100 : 0;
                                            $statusClass = $percentage >= 100 ? 'text-success' : ($percentage >= 80 ? 'text-warning' : 'text-danger');
                                        @endphp
                                        <span class="{{ $statusClass }}">
                                            {{ number_format($percentage, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profitability Metrics -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Profitability Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">Industry Avg</th>
                                    <th class="text-end">Variance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $profitMetrics = [
                                        ['label' => 'Gross Profit', 'value' => $profitability['gross_profit'] ?? 0, 'industry' => ($profitability['gross_profit'] ?? 0) * 0.9],
                                        ['label' => 'Net Profit', 'value' => $profitability['net_profit'] ?? 0, 'industry' => ($profitability['net_profit'] ?? 0) * 0.85],
                                        ['label' => 'Gross Margin %', 'value' => $profitability['gross_margin'] ?? 0, 'industry' => 60],
                                        ['label' => 'Net Margin %', 'value' => $profitability['net_margin'] ?? 0, 'industry' => 15],
                                        ['label' => 'ROI %', 'value' => $profitability['roi'] ?? 0, 'industry' => 20]
                                    ];
                                @endphp
                                @foreach($profitMetrics as $metric)
                                <tr>
                                    <td>{{ $metric['label'] }}</td>
                                    <td class="text-end">
                                        @if(strpos($metric['label'], '%') !== false)
                                            {{ number_format($metric['value'], 1) }}%
                                        @else
                                            ${{ number_format($metric['value'], 0) }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(strpos($metric['label'], '%') !== false)
                                            {{ number_format($metric['industry'], 1) }}%
                                        @else
                                            ${{ number_format($metric['industry'], 0) }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @php
                                            $variance = $metric['value'] - $metric['industry'];
                                            $varianceClass = $variance >= 0 ? 'text-success' : 'text-danger';
                                            $varianceSymbol = $variance >= 0 ? '+' : '';
                                        @endphp
                                        <span class="{{ $varianceClass }}">
                                            {{ $varianceSymbol }}{{ number_format($variance, 1) }}
                                            @if(strpos($metric['label'], '%') !== false) % @endif
                                        </span>
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

    <!-- AI Insights & Recommendations -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-brain me-2"></i>AI-Powered Financial Insights
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($report['ai_insights']))
                        <div class="row">
                            <!-- Key Insights -->
                            <div class="col-lg-6">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-lightbulb me-2"></i>Key Financial Insights
                                </h6>
                                <div class="list-group list-group-flush">
                                    @if(is_array($report['ai_insights']))
                                        @foreach(($report['ai_insights']['key_findings'] ?? []) as $insight)
                                        <div class="list-group-item d-flex align-items-start border-0 px-0">
                                            <i class="fas fa-chart-line text-info mt-1 me-2"></i>
                                            <span>{{ $insight }}</span>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            {{ $report['ai_insights'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Recommendations -->
                            <div class="col-lg-6">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-bullseye me-2"></i>Strategic Recommendations
                                </h6>
                                <div class="list-group">
                                    @foreach(($report['recommendations'] ?? []) as $rec)
                                    <div class="list-group-item mb-2">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $rec['category'] }}</h6>
                                            <span class="badge bg-{{ $rec['priority'] == 'High' ? 'danger' : 'warning' }}">
                                                {{ $rec['priority'] }} Priority
                                            </span>
                                        </div>
                                        <p class="mb-1">{{ $rec['recommendation'] }}</p>
                                        <small class="text-muted">{{ $rec['action'] }}</small>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-robot fa-3x text-muted mb-3"></i>
                            <p class="text-muted">AI insights are being generated...</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Cards -->
    <div class="row">
        <div class="col-md-3 col-6 mb-3">
            <a href="{{ route('finance.financial-analytics.kpis') }}" class="card card-hover text-decoration-none">
                <div class="card-body text-center">
                    <i class="fas fa-tachometer-alt fa-2x text-primary mb-3"></i>
                    <h6>Financial KPIs</h6>
                    <small class="text-muted">Key Performance Indicators</small>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <a href="{{ route('finance.financial-analytics.trends') }}" class="card card-hover text-decoration-none">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-2x text-success mb-3"></i>
                    <h6>Trend Analysis</h6>
                    <small class="text-muted">Historical trends & patterns</small>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <a href="{{ route('finance.financial-analytics.benchmarking') }}" class="card card-hover text-decoration-none">
                <div class="card-body text-center">
                    <i class="fas fa-balance-scale fa-2x text-warning mb-3"></i>
                    <h6>Benchmarking</h6>
                    <small class="text-muted">Compare with industry</small>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <a href="{{ route('finance.financial-analytics.forecasting') }}" class="card card-hover text-decoration-none">
                <div class="card-body text-center">
                    <i class="fas fa-crystal-ball fa-2x text-info mb-3"></i>
                    <h6>Forecasting</h6>
                    <small class="text-muted">Future predictions</small>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
    .card-hover {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
</style>
@endsection
