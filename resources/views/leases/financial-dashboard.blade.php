@extends('layouts.app')

@section('title', 'Financial Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-chart-line"></i> Financial Dashboard</h4>
                </div>
                <div class="card-body">
                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted">Total Contract Value</h6>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <small>USD</small>
                                            <h4 class="text-primary">${{ number_format($totalStats['total_contract_value_usd'], 2) }}</h4>
                                        </div>
                                        <div>
                                            <small>KSH</small>
                                            <h4 class="text-warning">{{ number_format($totalStats['total_contract_value_ksh'], 0) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted">Active Leases</h6>
                                    <h4 class="text-success">{{ $totalStats['active_leases'] }}</h4>
                                    <small>out of {{ $totalStats['total_leases'] }} total leases</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted">Monthly Revenue</h6>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <small>USD</small>
                                            <h4 class="text-success">${{ number_format($totalStats['monthly_revenue_usd'], 2) }}</h4>
                                        </div>
                                        <div>
                                            <small>KSH</small>
                                            <h4 class="text-success">{{ number_format($totalStats['monthly_revenue_ksh'], 0) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted">Quick Actions</h6>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('leases.finance.index') }}" class="btn btn-outline-primary btn-sm">View All Leases</a>
                                        <a href="{{ route('leases.finance.expiring-soon') }}" class="btn btn-outline-warning btn-sm">Expiring Soon</a>
                                        <a href="{{ route('leases.finance.overdue-billing') }}" class="btn btn-outline-danger btn-sm">Overdue Billing</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts and Graphs -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Leases by Service Type</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="serviceTypeChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Currency Distribution</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="currencyChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Billing -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Upcoming Billing (Next 30 Days)</h6>
                                </div>
                                <div class="card-body">
                                    @if($upcomingBilling->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Lease #</th>
                                                        <th>Customer</th>
                                                        <th>Next Billing</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($upcomingBilling as $lease)
                                                    <tr>
                                                        <td>{{ $lease->lease_number }}</td>
                                                        <td>{{ $lease->customer->name }}</td>
                                                        <td>{{ $lease->next_billing_date->format('M d, Y') }}</td>
                                                        <td>
                                                            <span class="{{ $lease->currency == 'USD' ? 'text-primary' : 'text-warning' }}">
                                                                {{ $lease->currency == 'USD' ? '$' : '' }}{{ number_format($lease->monthly_cost, 2) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success">{{ $lease->status }}</span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('leases.finance.show', $lease->id) }}" class="btn btn-sm btn-outline-primary">
                                                                View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted text-center py-4">No upcoming billing in the next 30 days.</p>
                                    @endif
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
    document.addEventListener('DOMContentLoaded', function() {
        // Service Type Chart
        const serviceCtx = document.getElementById('serviceTypeChart').getContext('2d');
        const serviceData = @json($serviceDistribution);

        new Chart(serviceCtx, {
            type: 'bar',
            data: {
                labels: serviceData.map(item => item.service_type.replace('_', ' ')),
                datasets: [{
                    label: 'Number of Leases',
                    data: serviceData.map(item => item.count),
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Currency Chart
        const currencyCtx = document.getElementById('currencyChart').getContext('2d');
        const currencyData = @json($currencyDistribution);

        new Chart(currencyCtx, {
            type: 'pie',
            data: {
                labels: currencyData.map(item => item.currency),
                datasets: [{
                    data: currencyData.map(item => item.count),
                    backgroundColor: [
                        '#36A2EB', // USD
                        '#FFCE56'  // KSH
                    ]
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
    });
</script>
@endsection
