{{-- resources/views/finance/financial-analytics/kpis.blade.php --}}
@extends('layouts.app')

@section('title', 'Financial KPIs Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Financial KPIs Dashboard
                </h1>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary" type="button" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Report
                    </button>
                </div>
            </div>
            <p class="text-muted mb-0">Key Performance Indicators for financial management</p>
        </div>
    </div>

    <!-- Period Comparison -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Period Comparison</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Current Period</h6>
                                    <p class="mb-0">This Month ({{ date('M Y') }})</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Previous Period</h6>
                                    <p class="mb-0">{{ date('M Y', strtotime('-1 month')) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Year to Date</h6>
                                    <p class="mb-0">Jan {{ date('Y') }} - Present</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue KPIs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Revenue KPIs
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($kpis['current']['revenue']))
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th class="text-end">Current</th>
                                    <th class="text-end">Previous</th>
                                    <th class="text-end">YTD</th>
                                    <th class="text-end">Change</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kpis['current']['revenue'] as $metric => $value)
                                @if(in_array($metric, ['total_revenue', 'collected_revenue', 'collection_rate', 'revenue_growth', 'active_customers']))
                                <tr>
                                    <td>
                                        {{ str_replace('_', ' ', ucfirst($metric)) }}
                                        @if($metric == 'collection_rate' || $metric == 'revenue_growth')
                                            <small class="text-muted d-block">%</small>
                                        @elseif(in_array($metric, ['total_revenue', 'collected_revenue']))
                                            <small class="text-muted d-block">$</small>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(in_array($metric, ['total_revenue', 'collected_revenue']))
                                            ${{ number_format($value, 0) }}
                                        @elseif($metric == 'collection_rate' || $metric == 'revenue_growth')
                                            {{ number_format($value, 1) }}%
                                        @else
                                            {{ number_format($value, 0) }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(isset($kpis['previous']['revenue'][$metric]))
                                            @if(in_array($metric, ['total_revenue', 'collected_revenue']))
                                                ${{ number_format($kpis['previous']['revenue'][$metric], 0) }}
                                            @elseif($metric == 'collection_rate' || $metric == 'revenue_growth')
                                                {{ number_format($kpis['previous']['revenue'][$metric], 1) }}%
                                            @else
                                                {{ number_format($kpis['previous']['revenue'][$metric], 0) }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(isset($kpis['ytd']['revenue'][$metric]))
                                            @if(in_array($metric, ['total_revenue', 'collected_revenue']))
                                                ${{ number_format($kpis['ytd']['revenue'][$metric], 0) }}
                                            @elseif($metric == 'collection_rate' || $metric == 'revenue_growth')
                                                {{ number_format($kpis['ytd']['revenue'][$metric], 1) }}%
                                            @else
                                                {{ number_format($kpis['ytd']['revenue'][$metric], 0) }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(isset($comparisons['revenue'][$metric]['change']))
                                            @php $change = $comparisons['revenue'][$metric]['change']; @endphp
                                            <span class="{{ $change > 0 ? 'text-success' : ($change < 0 ? 'text-danger' : 'text-muted') }}">
                                                {{ $change > 0 ? '+' : '' }}{{ number_format($change, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($comparisons['revenue'][$metric]['trend']))
                                            @if($comparisons['revenue'][$metric]['trend'] == 'up')
                                                <i class="fas fa-arrow-up text-success"></i>
                                            @elseif($comparisons['revenue'][$metric]['trend'] == 'down')
                                                <i class="fas fa-arrow-down text-danger"></i>
                                            @else
                                                <i class="fas fa-minus text-muted"></i>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <h5>No revenue data available</h5>
                        <p class="text-muted">Revenue KPIs could not be calculated</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Profitability KPIs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Profitability KPIs
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($kpis['current']['profitability']))
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th class="text-end">Current</th>
                                    <th class="text-end">Previous</th>
                                    <th class="text-end">YTD</th>
                                    <th class="text-end">Change</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kpis['current']['profitability'] as $metric => $value)
                                @if(in_array($metric, ['gross_profit', 'net_profit', 'gross_margin', 'net_margin']))
                                <tr>
                                    <td>
                                        {{ str_replace('_', ' ', ucfirst($metric)) }}
                                        @if(in_array($metric, ['gross_margin', 'net_margin']))
                                            <small class="text-muted d-block">%</small>
                                        @elseif(in_array($metric, ['gross_profit', 'net_profit']))
                                            <small class="text-muted d-block">$</small>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(in_array($metric, ['gross_profit', 'net_profit']))
                                            ${{ number_format($value, 0) }}
                                        @elseif(in_array($metric, ['gross_margin', 'net_margin']))
                                            {{ number_format($value, 1) }}%
                                        @else
                                            {{ number_format($value, 0) }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(isset($kpis['previous']['profitability'][$metric]))
                                            @if(in_array($metric, ['gross_profit', 'net_profit']))
                                                ${{ number_format($kpis['previous']['profitability'][$metric], 0) }}
                                            @elseif(in_array($metric, ['gross_margin', 'net_margin']))
                                                {{ number_format($kpis['previous']['profitability'][$metric], 1) }}%
                                            @else
                                                {{ number_format($kpis['previous']['profitability'][$metric], 0) }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(isset($kpis['ytd']['profitability'][$metric]))
                                            @if(in_array($metric, ['gross_profit', 'net_profit']))
                                                ${{ number_format($kpis['ytd']['profitability'][$metric], 0) }}
                                            @elseif(in_array($metric, ['gross_margin', 'net_margin']))
                                                {{ number_format($kpis['ytd']['profitability'][$metric], 1) }}%
                                            @else
                                                {{ number_format($kpis['ytd']['profitability'][$metric], 0) }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(isset($comparisons['profitability'][$metric]['change']))
                                            @php $change = $comparisons['profitability'][$metric]['change']; @endphp
                                            <span class="{{ $change > 0 ? 'text-success' : ($change < 0 ? 'text-danger' : 'text-muted') }}">
                                                {{ $change > 0 ? '+' : '' }}{{ number_format($change, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($comparisons['profitability'][$metric]['trend']))
                                            @if($comparisons['profitability'][$metric]['trend'] == 'up')
                                                <i class="fas fa-arrow-up text-success"></i>
                                            @elseif($comparisons['profitability'][$metric]['trend'] == 'down')
                                                <i class="fas fa-arrow-down text-danger"></i>
                                            @else
                                                <i class="fas fa-minus text-muted"></i>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                        <h5>No profitability data available</h5>
                        <p class="text-muted">Profitability KPIs could not be calculated</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Liquidity KPIs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cash-register me-2"></i>Liquidity KPIs
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($kpis['current']['liquidity']))
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th class="text-end">Current</th>
                                    <th class="text-end">Previous</th>
                                    <th class="text-end">YTD</th>
                                    <th class="text-end">Change</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kpis['current']['liquidity'] as $metric => $value)
                                @if(in_array($metric, ['current_ratio', 'quick_ratio', 'working_capital', 'days_sales_outstanding']))
                                <tr>
                                    <td>
                                        {{ str_replace('_', ' ', ucfirst($metric)) }}
                                        @if($metric == 'working_capital')
                                            <small class="text-muted d-block">$</small>
                                        @elseif($metric == 'days_sales_outstanding')
                                            <small class="text-muted d-block">days</small>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($metric == 'working_capital')
                                            ${{ number_format($value, 0) }}
                                        @else
                                            {{ number_format($value, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(isset($kpis['previous']['liquidity'][$metric]))
                                            @if($metric == 'working_capital')
                                                ${{ number_format($kpis['previous']['liquidity'][$metric], 0) }}
                                            @else
                                                {{ number_format($kpis['previous']['liquidity'][$metric], 2) }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(isset($kpis['ytd']['liquidity'][$metric]))
                                            @if($metric == 'working_capital')
                                                ${{ number_format($kpis['ytd']['liquidity'][$metric], 0) }}
                                            @else
                                                {{ number_format($kpis['ytd']['liquidity'][$metric], 2) }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(isset($comparisons['liquidity'][$metric]['change']))
                                            @php $change = $comparisons['liquidity'][$metric]['change']; @endphp
                                            <span class="{{ $change > 0 ? 'text-success' : ($change < 0 ? 'text-danger' : 'text-muted') }}">
                                                {{ $change > 0 ? '+' : '' }}{{ number_format($change, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($comparisons['liquidity'][$metric]['trend']))
                                            @if($comparisons['liquidity'][$metric]['trend'] == 'up')
                                                <i class="fas fa-arrow-up text-success"></i>
                                            @elseif($comparisons['liquidity'][$metric]['trend'] == 'down')
                                                <i class="fas fa-arrow-down text-danger"></i>
                                            @else
                                                <i class="fas fa-minus text-muted"></i>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-cash-register fa-3x text-muted mb-3"></i>
                        <h5>No liquidity data available</h5>
                        <p class="text-muted">Liquidity KPIs could not be calculated</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Key Insights</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @php
                            $insights = [];
                            if(isset($comparisons['revenue']['collection_rate']['change'])) {
                                $change = $comparisons['revenue']['collection_rate']['change'];
                                if($change > 5) {
                                    $insights[] = "✓ Collection rate improved by " . number_format($change, 1) . "%";
                                } elseif($change < -5) {
                                    $insights[] = "⚠ Collection rate declined by " . number_format(abs($change), 1) . "%";
                                }
                            }
                            if(isset($comparisons['profitability']['net_margin']['change'])) {
                                $change = $comparisons['profitability']['net_margin']['change'];
                                if($change > 3) {
                                    $insights[] = "✓ Net margin improved by " . number_format($change, 1) . "%";
                                } elseif($change < -3) {
                                    $insights[] = "⚠ Net margin declined by " . number_format(abs($change), 1) . "%";
                                }
                            }
                            if(isset($kpis['current']['liquidity']['current_ratio'])) {
                                $currentRatio = $kpis['current']['liquidity']['current_ratio'];
                                if($currentRatio < 1.0) {
                                    $insights[] = "⚠ Low current ratio: " . number_format($currentRatio, 2);
                                } elseif($currentRatio > 2.0) {
                                    $insights[] = "✓ Strong current ratio: " . number_format($currentRatio, 2);
                                }
                            }
                        @endphp

                        @if(count($insights) > 0)
                            @foreach($insights as $insight)
                                <li class="mb-2">
                                    {!! $insight !!}
                                </li>
                            @endforeach
                        @else
                            <li class="text-muted">No significant insights available</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recommendations</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @php
                            $recommendations = [];
                            if(isset($kpis['current']['revenue']['collection_rate']) && $kpis['current']['revenue']['collection_rate'] < 80) {
                                $recommendations[] = "Improve collection rate from " . number_format($kpis['current']['revenue']['collection_rate'], 1) . "% to 85%+";
                            }
                            if(isset($kpis['current']['profitability']['net_margin']) && $kpis['current']['profitability']['net_margin'] < 15) {
                                $recommendations[] = "Increase net profit margin to 15%+";
                            }
                            if(isset($kpis['current']['liquidity']['current_ratio']) && $kpis['current']['liquidity']['current_ratio'] < 1.5) {
                                $recommendations[] = "Strengthen liquidity position";
                            }
                            if(isset($kpis['current']['revenue']['revenue_growth']) && $kpis['current']['revenue']['revenue_growth'] < 10) {
                                $recommendations[] = "Accelerate revenue growth";
                            }
                        @endphp

                        @if(count($recommendations) > 0)
                            @foreach($recommendations as $recommendation)
                                <li class="mb-2">
                                    <i class="fas fa-lightbulb text-warning me-2"></i>{{ $recommendation }}
                                </li>
                            @endforeach
                        @else
                            <li class="text-muted">All KPIs are within target ranges</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th {
        font-weight: 600;
        border-top: none;
    }
    .table td {
        vertical-align: middle;
    }
    .card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        border-radius: 8px 8px 0 0 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any JavaScript functionality here
        console.log('KPI Dashboard loaded');
    });
</script>
@endpush
@endsection
