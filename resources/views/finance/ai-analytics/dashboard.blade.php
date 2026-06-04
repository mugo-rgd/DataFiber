@extends('layouts.app')

@section('title', 'AI-Powered Debt Analytics')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 class="mb-0">
                <i class="fas fa-brain text-primary me-2"></i>AI-Powered Debt Analytics
            </h4>
            <p class="text-muted mb-0">Intelligent insights and predictions for debt management across USD and KSH</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('finance.ai-analytics.predictive') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-chart-line me-1"></i> Predictive Analytics
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>

    <!-- Summary Cards - USD -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <span class="badge bg-primary me-2">USD</span> US Dollar Summary
            </h5>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Outstanding</div>
                    <h3 class="mb-0">${{ number_format($usdMetrics->total_outstanding, 2) }}</h3>
                    <small class="text-muted">{{ number_format($usdMetrics->overdue_percentage, 1) }}% overdue</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="text-muted small">Collection Rate</div>
                    <h3 class="mb-0">{{ number_format($usdMetrics->collection_rate, 1) }}%</h3>
                    <small class="text-muted">{{ $usdMetrics->collection_rate >= 70 ? 'Good' : 'Needs improvement' }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="text-muted small">Overdue Amount</div>
                    <h3 class="mb-0">${{ number_format($usdMetrics->overdue_amount, 2) }}</h3>
                    <small class="text-muted">{{ number_format($usdMetrics->overdue_percentage, 1) }}% of total</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="text-muted small">Today's Collections</div>
                    <h3 class="mb-0">${{ number_format($usdMetrics->today_collections, 2) }}</h3>
                    <small class="text-muted">Updated: {{ now()->format('g:i A') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards - KSH -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <span class="badge bg-secondary me-2">KSH</span> Kenyan Shilling Summary
            </h5>
        </div>
        <div class="col-md-3">
            <div class="card border-left-secondary shadow h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Outstanding</div>
                    <h3 class="mb-0">KSH {{ number_format($kshMetrics->total_outstanding, 2) }}</h3>
                    <small class="text-muted">{{ number_format($kshMetrics->overdue_percentage, 1) }}% overdue</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="text-muted small">Collection Rate</div>
                    <h3 class="mb-0">{{ number_format($kshMetrics->collection_rate, 1) }}%</h3>
                    <small class="text-muted">{{ $kshMetrics->collection_rate >= 70 ? 'Good' : 'Needs improvement' }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="text-muted small">Overdue Amount</div>
                    <h3 class="mb-0">KSH {{ number_format($kshMetrics->overdue_amount, 2) }}</h3>
                    <small class="text-muted">{{ number_format($kshMetrics->overdue_percentage, 1) }}% of total</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="text-muted small">Today's Collections</div>
                    <h3 class="mb-0">KSH {{ number_format($kshMetrics->today_collections, 2) }}</h3>
                    <small class="text-muted">Updated: {{ now()->format('g:i A') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Insights Section -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>Key Findings
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @forelse($insights['key_findings'] as $finding)
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> {{ $finding }}</li>
                        @empty
                            <li class="text-muted">No key findings available</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Risk Analysis
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @forelse($insights['risk_analysis'] as $risk)
                            <li class="mb-2"><i class="fas fa-chart-line text-warning me-2"></i> {{ $risk }}</li>
                        @empty
                            <li class="text-muted">No risk analysis available</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb text-success me-2"></i>Recommended Actions
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @forelse($insights['recommendations'] as $recommendation)
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                {{ is_array($recommendation) ? ($recommendation['action'] ?? '') : $recommendation }}
                                @if(is_array($recommendation) && isset($recommendation['priority']))
                                    <span class="badge bg-{{ $recommendation['priority'] == 'High' ? 'danger' : ($recommendation['priority'] == 'Medium' ? 'warning' : 'info') }} ms-2">
                                        Priority: {{ $recommendation['priority'] }}
                                    </span>
                                @endif
                            </li>
                        @empty
                            <li class="text-muted">No recommendations available</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Aging Analysis Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>Aging Analysis
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Age Bucket</th>
                                    <th class="text-end">USD</th>
                                    <th class="text-end">KSH</th>
                                    <th class="text-end">Invoices</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Current (Not overdue)</strong></td>
                                    <td class="text-end">${{ number_format($usdAging->current ?? 0, 2) }}</td>
                                    <td class="text-end">KSH {{ number_format($kshAging->current ?? 0, 2) }}</td>
                                    <td class="text-end">{{ ($usdAging->invoice_count ?? 0) + ($kshAging->invoice_count ?? 0) }}</td>
                                    <td class="text-end">{{ number_format((($usdAging->current_percentage ?? 0) + ($kshAging->current_percentage ?? 0)) / 2, 1) }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>1-30 Days Overdue</strong></td>
                                    <td class="text-end text-warning">${{ number_format($usdAging->days1_30 ?? 0, 2) }}</td>
                                    <td class="text-end text-warning">KSH {{ number_format($kshAging->days1_30 ?? 0, 2) }}</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">{{ number_format((($usdAging->days1_30_percentage ?? 0) + ($kshAging->days1_30_percentage ?? 0)) / 2, 1) }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>31-60 Days Overdue</strong></td>
                                    <td class="text-end text-warning">${{ number_format($usdAging->days31_60 ?? 0, 2) }}</td>
                                    <td class="text-end text-warning">KSH {{ number_format($kshAging->days31_60 ?? 0, 2) }}</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">{{ number_format((($usdAging->days31_60_percentage ?? 0) + ($kshAging->days31_60_percentage ?? 0)) / 2, 1) }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>61-90 Days Overdue</strong></td>
                                    <td class="text-end text-warning">${{ number_format($usdAging->days61_90 ?? 0, 2) }}</td>
                                    <td class="text-end text-warning">KSH {{ number_format($kshAging->days61_90 ?? 0, 2) }}</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">{{ number_format((($usdAging->days61_90_percentage ?? 0) + ($kshAging->days61_90_percentage ?? 0)) / 2, 1) }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>Over 90 Days Overdue</strong></td>
                                    <td class="text-end text-danger fw-bold">${{ number_format($usdAging->days_over_90 ?? 0, 2) }}</td>
                                    <td class="text-end text-danger fw-bold">KSH {{ number_format($kshAging->days_over_90 ?? 0, 2) }}</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">{{ number_format((($usdAging->days_over_90_percentage ?? 0) + ($kshAging->days_over_90_percentage ?? 0)) / 2, 1) }}%</td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>TOTAL</td>
                                    <td class="text-end">${{ number_format($usdAging->total ?? 0, 2) }}</td>
                                    <td class="text-end">KSH {{ number_format($kshAging->total ?? 0, 2) }}</td>
                                    <td class="text-end">{{ ($usdAging->invoice_count ?? 0) + ($kshAging->invoice_count ?? 0) }}</td>
                                    <td class="text-end">100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 10 Debtors -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-trophy text-warning me-2"></i>Top 10 Debtors
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th class="text-end">USD Outstanding</th>
                                    <th class="text-end">KSH Outstanding</th>
                                    <th class="text-end">Overdue Invoices</th>
                                    <th>Risk Level</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topDebtors as $debtor)
                                    <tr>
                                        <td>
                                            <strong>{{ $debtor['customer_name'] }}</strong>
                                        </td>
                                        <td>{{ $debtor['email'] }}</td>
                                        <td class="text-end">${{ number_format($debtor['usd_outstanding'], 2) }}</td>
                                        <td class="text-end">KSH {{ number_format($debtor['ksh_outstanding'], 2) }}</td>
                                        <td class="text-end text-danger">{{ $debtor['overdue_count'] }}</td>
                                        <td>
                                            @php
                                                $riskClass = $debtor['risk_level'] == 'Critical' ? 'danger' : ($debtor['risk_level'] == 'High' ? 'warning' : ($debtor['risk_level'] == 'Medium' ? 'info' : 'success'));
                                            @endphp
                                            <span class="badge bg-{{ $riskClass }}">{{ $debtor['risk_level'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('finance.ai-analytics.customer', $debtor['user_id']) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <button class="btn btn-sm btn-outline-warning" onclick="sendReminder({{ $debtor['user_id'] }}, '{{ addslashes($debtor['customer_name']) }}')">
                                                <i class="fas fa-bell"></i> Remind
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            No debtor data available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection Trends -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line text-success me-2"></i>Collection Trends (Last 30 Days)
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="collectionChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie text-info me-2"></i>Trend Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Total Collected (USD)</small>
                        <h5 class="mb-0">${{ number_format($collectionTrends['total_usd'], 2) }}</h5>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Total Collected (KSH)</small>
                        <h5 class="mb-0">KSH {{ number_format($collectionTrends['total_ksh'], 2) }}</h5>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Average Daily (USD)</small>
                        <h5 class="mb-0">${{ number_format($collectionTrends['avg_daily_usd'], 2) }}</h5>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Average Daily (KSH)</small>
                        <h5 class="mb-0">KSH {{ number_format($collectionTrends['avg_daily_ksh'], 2) }}</h5>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Total Payments</small>
                        <h5 class="mb-0">{{ number_format($collectionTrends['total_payments']) }}</h5>
                    </div>
                    <hr>
                    <div>
                        <small class="text-muted">Trend Analysis</small>
                        <h5 class="mb-0 {{ $collectionTrends['trend_direction'] == 'up' ? 'text-success' : ($collectionTrends['trend_direction'] == 'down' ? 'text-danger' : 'text-secondary') }}">
                            <i class="fas fa-arrow-{{ $collectionTrends['trend_direction'] == 'up' ? 'up' : ($collectionTrends['trend_direction'] == 'down' ? 'down' : 'right') }} me-1"></i>
                            {{ $collectionTrends['trend_percentage'] }}%
                        </h5>
                        <small class="text-muted">{{ $collectionTrends['trend_message'] }}</small>
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
    document.addEventListener('DOMContentLoaded', function() {
        const collectionCtx = document.getElementById('collectionChart')?.getContext('2d');
        if (collectionCtx) {
            const chartData = @json($collectionTrends);
            new Chart(collectionCtx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'USD Collections ($)',
                            data: chartData.usd_amounts,
                            borderColor: 'rgb(54, 162, 235)',
                            backgroundColor: 'rgba(54, 162, 235, 0.1)',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y'
                        },
                        {
                            label: 'KSH Collections (KSH)',
                            data: chartData.ksh_amounts,
                            borderColor: 'rgb(255, 99, 132)',
                            backgroundColor: 'rgba(255, 99, 132, 0.1)',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.parsed.y;
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
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Amount (USD)' },
                            ticks: { callback: (value) => '$' + value.toLocaleString() }
                        },
                        y1: {
                            position: 'right',
                            beginAtZero: true,
                            title: { display: true, text: 'Amount (KSH)' },
                            ticks: { callback: (value) => 'KSH ' + value.toLocaleString() },
                            grid: { drawOnChartArea: false }
                        }
                    }
                }
            });
        }
    });

    function sendReminder(customerId, customerName) {
        if (confirm(`Send payment reminder to ${customerName}?`)) {
            fetch(`/finance/ai-analytics/send-reminder/${customerId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message || 'Reminder sent successfully');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to send reminder');
            });
        }
    }
</script>
@endsection
