@extends('layouts.app')

@section('title', 'Collection Performance Report')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h2 text-kp-blue">
                <i class="fas fa-chart-line me-2"></i>Collection Performance Report
            </h1>
            <p class="text-muted mb-0">Track and analyze collection performance metrics by currency (USD & KES)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('finance.debt.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
            </a>
            <button class="btn btn-kp-success" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Print Report
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('finance.debt.collection.report') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label fw-semibold">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                           value="{{ request('start_date', $startDate ?? now()->startOfMonth()->format('Y-m-d')) }}"
                           max="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label fw-semibold">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                           value="{{ request('end_date', $endDate ?? now()->format('Y-m-d')) }}"
                           max="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="period" class="form-label fw-semibold">Period</label>
                    <select name="period" id="period" class="form-select">
                        <option value="daily" {{ ($period ?? 'monthly') == 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ ($period ?? 'monthly') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ ($period ?? 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-kp-primary w-100">
                        <i class="fas fa-filter me-1"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Key Metrics - Dual Currency -->
    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-uppercase small fw-semibold opacity-75">Total Collected (USD)</div>
                            <div class="h2 mb-0 fw-bold">${{ number_format($collectionSummary['total_collected_usd'] ?? 0, 2) }}</div>
                            <div class="mt-2 small opacity-75">
                                <i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($startDate ?? now())->format('M d') }} - {{ \Carbon\Carbon::parse($endDate ?? now())->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-uppercase small fw-semibold opacity-75">Total Collected (KES)</div>
                            <div class="h2 mb-0 fw-bold">KES {{ number_format($collectionSummary['total_collected_ksh'] ?? 0, 2) }}</div>
                            <div class="mt-2 small opacity-75">
                                <i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($startDate ?? now())->format('M d') }} - {{ \Carbon\Carbon::parse($endDate ?? now())->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shilling-sign fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-uppercase small fw-semibold opacity-75">Collection Rate (USD)</div>
                            <div class="h2 mb-0 fw-bold">{{ number_format($collectionSummary['collection_rate_usd'] ?? 0, 1) }}%</div>
                            <div class="mt-2 small opacity-75">
                                <i class="fas fa-chart-line me-1"></i>${{ number_format($collectionSummary['total_invoiced_usd'] ?? 0, 0) }} invoiced
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-uppercase small fw-semibold opacity-75">Collection Rate (KES)</div>
                            <div class="h2 mb-0 fw-bold">{{ number_format($collectionSummary['collection_rate_ksh'] ?? 0, 1) }}%</div>
                            <div class="mt-2 small opacity-75">
                                <i class="fas fa-chart-line me-1"></i>KES {{ number_format($collectionSummary['total_invoiced_ksh'] ?? 0, 0) }} invoiced
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics -->
    <div class="row mb-4 g-3">
        <div class="col-xl-4 col-md-6">
            <div class="card bg-secondary text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-uppercase small fw-semibold opacity-75">Avg Collection Period</div>
                            <div class="h2 mb-0 fw-bold">{{ number_format($collectionSummary['average_collection_period'] ?? 0, 0) }} days</div>
                            <div class="mt-2 small opacity-75">
                                <i class="fas fa-clock me-1"></i>From due date to payment
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-danger text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-uppercase small fw-semibold opacity-75">Overdue Collected (USD)</div>
                            <div class="h2 mb-0 fw-bold">${{ number_format($collectionSummary['overdue_collected_usd'] ?? 0, 2) }}</div>
                            <div class="mt-2 small opacity-75">
                                <i class="fas fa-exclamation-triangle me-1"></i>Recovered from overdue
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-dark text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-uppercase small fw-semibold opacity-75">Overdue Collected (KES)</div>
                            <div class="h2 mb-0 fw-bold">KES {{ number_format($collectionSummary['overdue_collected_ksh'] ?? 0, 2) }}</div>
                            <div class="mt-2 small opacity-75">
                                <i class="fas fa-exclamation-triangle me-1"></i>Recovered from overdue
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection Trend Chart -->
    <div class="row mb-4 g-3">
        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-chart-area text-kp-blue me-2"></i>Collection Trend by Currency
                    </h5>
                </div>
                <div class="card-body">
                    @if(!empty($collectionTrend) && count($collectionTrend) > 0)
                        <canvas id="collectionTrendChart" height="250"></canvas>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No collection data available for the selected period.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Aging Collection -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-hourglass-half text-kp-blue me-2"></i>Aging Collection Analysis
                    </h5>
                </div>
                <div class="card-body">
                    @if(!empty($agingCollection))
                        <canvas id="agingCollectionChart" height="250"></canvas>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No aging data available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Top Collectors Performance - USD -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-chart-line text-kp-blue me-2"></i>Top Collectors Performance (USD)
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($collectorPerformance['usd']) && $collectorPerformance['usd']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Collector</th>
                                        <th class="text-center">Assigned</th>
                                        <th class="text-end">Collected (USD)</th>
                                        <th class="text-center">Rate</th>
                                        <th>Performance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($collectorPerformance['usd'] as $collector)
                                    <tr>
                                        <td class="fw-semibold">{{ $collector->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $collector->total_assigned ?? 0 }}</td>
                                        <td class="text-end">${{ number_format($collector->collected_amount ?? 0, 2) }}</td>
                                        <td class="text-center">{{ number_format($collector->collection_rate ?? 0, 1) }}%</td>
                                        <td style="width: 100px;">
                                            @php
                                                $percentage = min(100, $collector->collection_rate ?? 0);
                                                $color = $percentage >= 80 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
                                            @endphp
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $color }}" style="width: {{ $percentage }}%;"></div>
                                            </div>
                                            <small class="text-muted">{{ number_format($percentage, 0) }}%</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No USD collector performance data available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Collectors Performance - KES -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-chart-line text-kp-blue me-2"></i>Top Collectors Performance (KES)
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($collectorPerformance['ksh']) && $collectorPerformance['ksh']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Collector</th>
                                        <th class="text-center">Assigned</th>
                                        <th class="text-end">Collected (KES)</th>
                                        <th class="text-center">Rate</th>
                                        <th>Performance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($collectorPerformance['ksh'] as $collector)
                                    <tr>
                                        <td class="fw-semibold">{{ $collector->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $collector->total_assigned ?? 0 }}</td>
                                        <td class="text-end">KES {{ number_format($collector->collected_amount ?? 0, 2) }}</td>
                                        <td class="text-center">{{ number_format($collector->collection_rate ?? 0, 1) }}%</td>
                                        <td style="width: 100px;">
                                            @php
                                                $percentage = min(100, $collector->collection_rate ?? 0);
                                                $color = $percentage >= 80 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
                                            @endphp
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $color }}" style="width: {{ $percentage }}%;"></div>
                                            </div>
                                            <small class="text-muted">{{ number_format($percentage, 0) }}%</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No KES collector performance data available.</p>
                        </div>
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
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-trophy text-warning me-2"></i>Top Performing Customers (USD)
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($topPerformingCustomers) && $topPerformingCustomers->where('currency', 'USD')->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Customer</th>
                                        <th class="text-center">Payments</th>
                                        <th class="text-end">Total Paid (USD)</th>
                                        <th class="text-end">Avg Payment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topPerformingCustomers->where('currency', 'USD') as $customer)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $customer->name ?? 'N/A' }}</div>
                                            @if(property_exists($customer, 'company_name') && $customer->company_name)
                                                <small class="text-muted">{{ $customer->company_name }}</small>
                                            @endif
                                         </td>
                                        <td class="text-center">{{ $customer->payments_count ?? 0 }}</td>
                                        <td class="text-end">${{ number_format($customer->payments_sum_amount ?? 0, 2) }}</td>
                                        <td class="text-end">
                                            @if(($customer->payments_count ?? 0) > 0)
                                                ${{ number_format(($customer->payments_sum_amount ?? 0) / $customer->payments_count, 2) }}
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
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No USD payment data available for the selected period.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-trophy text-warning me-2"></i>Top Performing Customers (KES)
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($topPerformingCustomers) && $topPerformingCustomers->where('currency', 'KSH')->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Customer</th>
                                        <th class="text-center">Payments</th>
                                        <th class="text-end">Total Paid (KES)</th>
                                        <th class="text-end">Avg Payment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topPerformingCustomers->where('currency', 'KSH') as $customer)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $customer->name ?? 'N/A' }}</div>
                                            @if(property_exists($customer, 'company_name') && $customer->company_name)
                                                <small class="text-muted">{{ $customer->company_name }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $customer->payments_count ?? 0 }}</td>
                                        <td class="text-end">KES {{ number_format($customer->payments_sum_amount ?? 0, 2) }}</td>
                                        <td class="text-end">
                                            @if(($customer->payments_count ?? 0) > 0)
                                                KES {{ number_format(($customer->payments_sum_amount ?? 0) / $customer->payments_count, 2) }}
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
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No KES payment data available for the selected period.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Problematic Customers -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 fw-semibold">
                <i class="fas fa-exclamation-triangle text-danger me-2"></i>Problematic Customers (Frequently Overdue)
            </h5>
        </div>
        <div class="card-body p-0">
            @if(isset($problematicCustomers) && $problematicCustomers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Customer</th>
                                <th class="text-center">Overdue Invoices</th>
                                <th class="text-end">Total Overdue (USD)</th>
                                <th class="text-end">Total Overdue (KES)</th>
                                <th>Last Payment</th>
                                <th>Contact</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($problematicCustomers as $customer)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $customer->name ?? 'N/A' }}</div>
                                    @if(property_exists($customer, 'company_name') && $customer->company_name)
                                        <small class="text-muted">{{ $customer->company_name }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger">{{ $customer->overdue_invoices_count ?? 0 }}</span>
                                </td>
                                <td class="text-end fw-semibold">
                                    @if(($customer->total_overdue_usd ?? 0) > 0)
                                        <span class="text-danger">${{ number_format($customer->total_overdue_usd, 2) }}</span>
                                    @else
                                        <span class="text-muted">$0.00</span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">
                                    @if(($customer->total_overdue_ksh ?? 0) > 0)
                                        <span class="text-warning">KES {{ number_format($customer->total_overdue_ksh, 2) }}</span>
                                    @else
                                        <span class="text-muted">KES 0.00</span>
                                    @endif
                                </td>
                                <td>
                                    @if(property_exists($customer, 'last_payment_date') && $customer->last_payment_date)
                                        {{ \Carbon\Carbon::parse($customer->last_payment_date)->format('M d, Y') }}
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($customer->last_payment_date)->diffForHumans() }}</small>
                                    @else
                                        <span class="text-danger">Never</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $customer->email ?? 'N/A' }}
                                    @if(property_exists($customer, 'phone') && $customer->phone)
                                        <br><small class="text-muted">{{ $customer->phone }}</small>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <p class="text-muted mb-0">No problematic customers identified.</p>
                </div>
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
    @if(!empty($collectionTrend) && count($collectionTrend) > 0)
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
                        borderColor: '#0066B3',
                        backgroundColor: 'rgba(0, 102, 179, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#0066B3',
                        pointBorderColor: '#fff',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'KES Collection',
                        data: kshData,
                        borderColor: '#009639',
                        backgroundColor: 'rgba(0, 150, 57, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#009639',
                        pointBorderColor: '#fff',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = context.raw || 0;
                                let currency = label.includes('USD') ? '$' : 'KES';
                                return label + ': ' + currency + ' ' + value.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: function(value) { return value.toLocaleString(); } }
                    }
                }
            }
        });
    @endif

    // Aging Collection Chart
    @if(!empty($agingCollection))
        const agingCtx = document.getElementById('agingCollectionChart').getContext('2d');

        // Determine which currency has data
        const hasUsdData = {{ ($agingCollection['current_usd'] ?? 0) > 0 ? 'true' : 'false' }};
        const agingData = hasUsdData ? [
            {{ $agingCollection['current_usd'] ?? 0 }},
            {{ $agingCollection['1_30_days_usd'] ?? 0 }},
            {{ $agingCollection['31_60_days_usd'] ?? 0 }},
            {{ $agingCollection['61_90_days_usd'] ?? 0 }},
            {{ $agingCollection['over_90_days_usd'] ?? 0 }}
        ] : [
            {{ $agingCollection['current_ksh'] ?? 0 }},
            {{ $agingCollection['1_30_days_ksh'] ?? 0 }},
            {{ $agingCollection['31_60_days_ksh'] ?? 0 }},
            {{ $agingCollection['61_90_days_ksh'] ?? 0 }},
            {{ $agingCollection['over_90_days_ksh'] ?? 0 }}
        ];

        new Chart(agingCtx, {
            type: 'doughnut',
            data: {
                labels: ['Current', '1-30 Days', '31-60 Days', '61-90 Days', '90+ Days'],
                datasets: [{
                    data: agingData,
                    backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#fd7e14', '#dc3545'],
                    borderWidth: 0,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'right' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const currency = hasUsdData ? '$' : 'KES';
                                return context.label + ': ' + currency + ' ' + context.raw.toLocaleString();
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

@push('styles')
<style>
    .card {
        border-radius: 0.75rem;
        border: none;
        overflow: hidden;
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }
    .table th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    .table td {
        vertical-align: middle;
        font-size: 0.85rem;
    }
    .badge {
        font-weight: 500;
        padding: 0.35rem 0.65rem;
        border-radius: 0.5rem;
    }
    @media (max-width: 768px) {
        .table th, .table td {
            font-size: 0.7rem;
            padding: 0.5rem;
        }
        .h2 { font-size: 1.3rem; }
    }
    @media print {
        .btn, .btn-group, form { display: none !important; }
        .card { border: 1px solid #ddd; break-inside: avoid; }
    }
</style>
@endpush
