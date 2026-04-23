@extends('layouts.app')

@section('title', 'KPI Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="bi bi-person-badge"></i>
            @if(auth()->user()->role === 'account_manager')
                My Performance Dashboard
            @else
                Account Manager Performance Dashboard
            @endif
        </h2>
        @if(auth()->user()->role === 'account_manager')
            <p class="text-muted">Welcome back, {{ auth()->user()->name }}! Here's your performance overview.</p>
        @endif
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('kpi.export', ['account_manager_id' => $accountManagerId, 'currency' => $currency]) }}" class="btn btn-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('kpi.dashboard') }}" class="row g-3">

            @if(auth()->user()->role === 'account_manager')
                {{-- Account Manager View: Show hidden input and display name --}}
                <input type="hidden" name="account_manager_id" value="{{ auth()->user()->id }}">
                <div class="col-md-4">
                    <label class="form-label">Account Manager</label>
                    <div class="form-control bg-light">
                        <i class="bi bi-person-circle"></i>
                        <strong>{{ auth()->user()->name }}</strong> (You)
                    </div>
                    <small class="text-muted">Viewing your own KPI data</small>
                </div>
            @else
                {{-- Admin View: Show dropdown of all account managers --}}
                <div class="col-md-4">
                    <label for="account_manager_id" class="form-label">Account Manager</label>
                    <select name="account_manager_id" id="account_manager_id" class="form-select">
                        <option value="">All Account Managers</option>
                        @foreach($accountManagers as $manager)
                            <option value="{{ $manager->id }}" {{ $accountManagerId == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Currency Filter -->
            <div class="col-md-3">
                <label for="currency" class="form-label">Currency Filter</label>
                <select name="currency" id="currency" class="form-select">
                    <option value="">All Currencies</option>
                    <option value="USD" {{ $currency == 'USD' ? 'selected' : '' }}>USD Only</option>
                    <option value="KSH" {{ $currency == 'KSH' ? 'selected' : '' }}>KSH Only</option>
                </select>
            </div>

            <div class="col-md-5 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Apply Filter
                </button>
                @if($accountManagerId || $currency)
                    <a href="{{ route('kpi.dashboard') }}" class="btn btn-secondary ms-2">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

@if(empty($kpis))
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        @if(auth()->user()->role === 'account_manager')
            No KPI data found for your account. This could be because you have no active customers or leases.
        @else
            No data found for the selected criteria.
        @endif
    </div>
@else
    @foreach($kpis as $kpi)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">{{ $kpi['account_manager']['name'] }}</h3>
                <small>{{ $kpi['account_manager']['email'] }}</small>
                @if($kpi['filter_currency'])
                    <span class="badge bg-info ms-2">Filtered: {{ $kpi['filter_currency'] }} Only</span>
                @endif
            </div>
            <div class="card-body">
                <!-- Performance Summary Badge -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="alert
                            @if($kpi['performance_summary']['rating'] == 'Excellent') alert-success
                            @elseif($kpi['performance_summary']['rating'] == 'Good') alert-info
                            @elseif($kpi['performance_summary']['rating'] == 'Average') alert-warning
                            @else alert-danger @endif">
                            <strong>Performance Rating: {{ $kpi['performance_summary']['rating'] }}</strong>
                            (Score: {{ $kpi['performance_summary']['score'] }}/100)
                        </div>
                    </div>
                </div>

                <!-- Financial KPIs Row -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total MRR</h5>
                                <h3 class="text-primary">
                                    @if(!$kpi['filter_currency'])
                                        ${{ number_format($kpi['financial']['total_mrr'], 2) }}
                                        <small class="text-muted">(Combined)</small>
                                    @elseif($kpi['filter_currency'] == 'USD')
                                        ${{ number_format($kpi['financial']['total_mrr'], 2) }}
                                    @else
                                        KSh {{ number_format($kpi['financial']['total_mrr'], 2) }}
                                    @endif
                                </h3>
                                <small class="text-muted">Monthly Recurring Revenue</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total TCV</h5>
                                <h3 class="text-success">
                                    @if(!$kpi['filter_currency'])
                                        ${{ number_format($kpi['financial']['total_tcv'], 2) }}
                                    @elseif($kpi['filter_currency'] == 'USD')
                                        ${{ number_format($kpi['financial']['total_tcv'], 2) }}
                                    @else
                                        KSh {{ number_format($kpi['financial']['total_tcv'], 2) }}
                                    @endif
                                </h3>
                                <small class="text-muted">Total Contract Value</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="card-title">ARPC</h5>
                                <h3>
                                    @if(!$kpi['filter_currency'])
                                        ${{ number_format($kpi['financial']['arpc'], 2) }}
                                    @elseif($kpi['filter_currency'] == 'USD')
                                        ${{ number_format($kpi['financial']['arpc'], 2) }}
                                    @else
                                        KSh {{ number_format($kpi['financial']['arpc'], 2) }}
                                    @endif
                                </h3>
                                <small class="text-muted">Avg Revenue Per Customer</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="card-title">Churn Rate</h5>
                                <h3 class="text-danger">{{ $kpi['customer_health']['churn_rate'] }}%</h3>
                                <small class="text-muted">Last 12 months</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Currency Breakdown Row -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-currency-dollar"></i> USD Portfolio</h5>
                                <p><strong>Revenue:</strong> ${{ number_format($kpi['financial']['usd']['total_mrr'], 2) }}/month</p>
                                <p><strong>Contracts:</strong> {{ $kpi['financial']['usd']['leases_count'] }} leases</p>
                                <p><strong>Customers:</strong> {{ $kpi['portfolio']['customers_by_currency']['usd_customers'] }} customers</p>
                                <p><strong>Distance:</strong> {{ number_format($kpi['utilization']['distance_by_currency']['usd_distance'], 2) }} km</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-currency-exchange"></i> KSH Portfolio</h5>
                                <p><strong>Revenue:</strong> KSh {{ number_format($kpi['financial']['ksh']['total_mrr'], 2) }}/month</p>
                                <p><strong>Contracts:</strong> {{ $kpi['financial']['ksh']['leases_count'] }} leases</p>
                                <p><strong>Customers:</strong> {{ $kpi['portfolio']['customers_by_currency']['ksh_customers'] }} customers</p>
                                <p><strong>Distance:</strong> {{ number_format($kpi['utilization']['distance_by_currency']['ksh_distance'], 2) }} km</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Distribution and Contract Term -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Revenue Distribution by Currency</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="currencyChart{{ $loop->index }}"></canvas>
                                <div class="mt-3">
                                    <p><strong>USD:</strong> {{ $kpi['financial']['breakdown']['usd_revenue_percentage'] }}%</p>
                                    <p><strong>KSH:</strong> {{ $kpi['financial']['breakdown']['ksh_revenue_percentage'] }}%</p>
                                    @if($kpi['portfolio']['customers_by_currency']['mixed_currency_customers'] > 0)
                                        <p class="text-info">
                                            <i class="bi bi-info-circle"></i>
                                            {{ $kpi['portfolio']['customers_by_currency']['mixed_currency_customers'] }} customers have both currencies
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Contract Term Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="termChart{{ $loop->index }}"></canvas>
                                <div class="mt-3">
                                    <p><strong>Short Term (≤24mo):</strong> {{ $kpi['contract_health']['short_term_contracts'] }}</p>
                                    <p><strong>Mid Term (25-60mo):</strong> {{ $kpi['contract_health']['mid_term_contracts'] }}</p>
                                    <p><strong>Long Term (>60mo):</strong> {{ $kpi['contract_health']['long_term_contracts'] }}</p>
                                    <p><strong>Avg Term:</strong> {{ $kpi['contract_health']['avg_contract_term_years'] }} years</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Portfolio and Utilization -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-people"></i> Customer Portfolio</h5>
                                <p><strong>Active Customers:</strong> {{ $kpi['portfolio']['total_customers'] }}</p>
                                <p><strong>Total Leases:</strong> {{ $kpi['portfolio']['total_leases'] }}</p>
                                <p><strong>Active Leases:</strong> {{ $kpi['portfolio']['active_leases'] }}</p>
                                <p><strong>Terminated Leases:</strong> {{ $kpi['portfolio']['terminated_leases'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-diagram-3"></i> Utilization</h5>
                                <p><strong>Total Distance:</strong> {{ number_format($kpi['utilization']['total_distance_km'], 2) }} km</p>
                                <p><strong>Total Cores:</strong> {{ $kpi['utilization']['total_cores_leased'] }}</p>
                                <p><strong>Avg Link Distance:</strong> {{ number_format($kpi['utilization']['avg_link_distance'], 2) }} km</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-trophy"></i> Top Customers</h5>
                                @foreach($kpi['customer_health']['top_customers_by_revenue'] as $customer)
                                    <p>
                                        <strong>{{ $customer['name'] }}</strong><br>
                                        {{ $customer['formatted_revenue'] }}/month ({{ $customer['leases_count'] }} leases)
                                    </p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Renewals Alert -->
                @if($kpi['contract_health']['upcoming_renewals_90days'] > 0)
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>{{ $kpi['contract_health']['upcoming_renewals_90days'] }} contracts</strong> expiring in next 90 days
                        (Revenue at risk:
                        @if(!$kpi['filter_currency'])
                            ${{ number_format($kpi['contract_health']['renewal_revenue_at_risk'], 2) }}
                        @elseif($kpi['filter_currency'] == 'USD')
                            ${{ number_format($kpi['contract_health']['renewal_revenue_at_risk'], 2) }}
                        @else
                            KSh {{ number_format($kpi['contract_health']['renewal_revenue_at_risk'], 2) }}
                        @endif
                        )
                        <button class="btn btn-sm btn-warning float-end" type="button" data-bs-toggle="collapse" data-bs-target="#renewalsList{{ $loop->index }}">
                            View Details
                        </button>
                        <div class="collapse mt-3" id="renewalsList{{ $loop->index }}">
                            <div class="card card-body">
                                @foreach($kpi['contract_health']['renewals_list'] as $renewal)
                                    <p>
                                        <strong>{{ $renewal['lease_number'] }}</strong> -
                                        {{ $renewal['customer_name'] }} -
                                        Expires: {{ $renewal['end_date'] }} -
                                        Monthly: {{ $renewal['formatted_cost'] }}
                                    </p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Technology Mix Chart -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Technology Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="techChart{{ $loop->index }}"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Currency Distribution Chart
            const currencyCtx{{ $loop->index }} = document.getElementById('currencyChart{{ $loop->index }}').getContext('2d');
            new Chart(currencyCtx{{ $loop->index }}, {
                type: 'doughnut',
                data: {
                    labels: ['USD ({{ $kpi['financial']['breakdown']['usd_revenue_percentage'] }}%)', 'KSH ({{ $kpi['financial']['breakdown']['ksh_revenue_percentage'] }}%)'],
                    datasets: [{
                        data: [{{ $kpi['financial']['usd']['total_mrr'] }}, {{ $kpi['financial']['ksh']['total_mrr'] }}],
                        backgroundColor: ['rgba(54, 162, 235, 0.8)', 'rgba(255, 99, 132, 0.8)'],
                        borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Contract Term Distribution Chart
            const termCtx{{ $loop->index }} = document.getElementById('termChart{{ $loop->index }}').getContext('2d');
            new Chart(termCtx{{ $loop->index }}, {
                type: 'bar',
                data: {
                    labels: ['Short Term', 'Mid Term', 'Long Term'],
                    datasets: [{
                        label: 'Number of Contracts',
                        data: [
                            {{ $kpi['contract_health']['short_term_contracts'] }},
                            {{ $kpi['contract_health']['mid_term_contracts'] }},
                            {{ $kpi['contract_health']['long_term_contracts'] }}
                        ],
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Contracts'
                            }
                        }
                    }
                }
            });

            // Technology Mix Chart
            const techData{{ $loop->index }} = @json($kpi['customer_health']['technology_mix']);
            const techCtx{{ $loop->index }} = document.getElementById('techChart{{ $loop->index }}').getContext('2d');

            new Chart(techCtx{{ $loop->index }}, {
                type: 'bar',
                data: {
                    labels: techData{{ $loop->index }}.map(t => t.technology),
                    datasets: [
                        {
                            label: 'Number of Leases',
                            data: techData{{ $loop->index }}.map(t => t.total_count),
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Revenue (USD)',
                            data: techData{{ $loop->index }}.map(t => t.total_revenue),
                            backgroundColor: 'rgba(75, 192, 192, 0.5)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Leases'
                            }
                        },
                        y1: {
                            position: 'right',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Revenue ($)'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    }
                }
            });
        </script>
        @endpush
    @endforeach

    <!-- Revenue Growth Trend -->
    @if(!empty($revenueGrowth))
    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <h4>Revenue Growth Trend (Last 12 Months)</h4>
            @if($currency)
                <small>Filtered by: {{ $currency }} only</small>
            @endif
        </div>
        <div class="card-body">
            <canvas id="revenueGrowthChart"></canvas>
        </div>
    </div>

    @push('scripts')
    <script>
        const revenueData = @json($revenueGrowth);
        const growthCtx = document.getElementById('revenueGrowthChart').getContext('2d');

        new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: revenueData.map(r => r.month),
                datasets: [
                    {
                        label: 'USD Revenue ($)',
                        data: revenueData.map(r => r.revenue_usd),
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        tension: 0.1,
                        fill: true
                    },
                    {
                        label: 'KSH Revenue (KSh)',
                        data: revenueData.map(r => r.revenue_ksh),
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        tension: 0.1,
                        fill: true
                    },
                    {
                        label: 'Growth Rate (%)',
                        data: revenueData.map(r => r.growth_rate),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        borderDash: [5, 5],
                        tension: 0.1,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: @json($currency == 'KSH' ? 'Revenue (KSh)' : 'Revenue ($)')
                        }
                    },
                    y1: {
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Growth Rate (%)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    </script>
    @endpush
    @endif
@endif
@endsection
