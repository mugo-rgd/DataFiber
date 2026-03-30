@extends('layouts.app')

@section('title', 'Maintenance Reports & Analytics')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Maintenance Reports & Analytics
                    </h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Print Report
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="exportBtn">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Date Range Filter -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('maintenance.reports') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control"
                                           value="{{ $startDate }}" id="start_date">
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control"
                                           value="{{ $endDate }}" id="end_date">
                                </div>
                                <div class="col-md-3">
                                    <label for="period" class="form-label">Period</label>
                                    <select name="period" class="form-select" id="period">
                                        <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                                    <a href="{{ route('maintenance.reports') }}" class="btn btn-outline-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Requests</h6>
                                            <h3 class="mb-0">{{ $requestsStats['total'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clipboard-list fa-2x"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small>
                                            {{ $requestsStats['open'] }} Open •
                                            {{ $requestsStats['in_progress'] }} In Progress •
                                            {{ $requestsStats['completed'] }} Completed
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Work Orders</h6>
                                            <h3 class="mb-0">{{ $workOrderStats['total'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-tasks fa-2x"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small>
                                            {{ $workOrderStats['assigned'] }} Assigned •
                                            {{ $workOrderStats['in_progress'] }} In Progress •
                                            {{ $workOrderStats['overdue'] }} Overdue
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Equipment</h6>
                                            <h3 class="mb-0">{{ $equipmentStats['total'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-toolbox fa-2x"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small>
                                            {{ $equipmentStats['available'] }} Available •
                                            {{ $equipmentStats['in_use'] }} In Use •
                                            {{ $equipmentStats['calibration_due'] }} Calibration Due
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Statistics -->
                    <div class="row">
                        <!-- Priority Distribution -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Requests by Priority</h6>
                                </div>
                                <div class="card-body">
                                    @foreach($priorityDistribution as $priority)
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-capitalize">{{ $priority->priority }}</span>
                                                <span class="fw-bold">{{ $priority->count }}</span>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-{{ $priority->priority == 'critical' ? 'danger' : ($priority->priority == 'high' ? 'warning' : ($priority->priority == 'medium' ? 'info' : 'success')) }}"
                                                     style="width: {{ ($priority->count / $requestsStats['total']) * 100 }}%">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Maintenance Type Distribution -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Requests by Type</h6>
                                </div>
                                <div class="card-body">
                                    @foreach($typeDistribution as $type)
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-capitalize">{{ str_replace('_', ' ', $type->maintenance_type) }}</span>
                                                <span class="fw-bold">{{ $type->count }}</span>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-primary"
                                                     style="width: {{ ($type->count / $requestsStats['total']) * 100 }}%">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Technician Performance -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Technician Performance</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Technician</th>
                                                    <th>Completed Orders</th>
                                                    <th>Avg. Completion Time</th>
                                                    <th>Efficiency Rating</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($technicianPerformance as $performance)
                                                    <tr>
                                                        <td>{{ $performance->technician->name ?? 'Unknown' }}</td>
                                                        <td>{{ $performance->completed_orders }}</td>
                                                        <td>
                                                            @if($performance->avg_completion_time)
                                                                {{ round($performance->avg_completion_time) }} hours
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                                $rating = $performance->avg_completion_time ?
                                                                    min(5, max(1, 5 - ($performance->avg_completion_time / 24))) : 3;
                                                            @endphp
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star {{ $i <= $rating ? 'text-warning' : 'text-muted' }}"></i>
                                                            @endfor
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

                    <!-- Completion Trend -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Completion Trend</h6>
                                </div>
                                <div class="card-body">
                                    @if($completionTrend->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Period</th>
                                                        <th>Completed Requests</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($completionTrend as $trend)
                                                        <tr>
                                                            <td>{{ date('F Y', mktime(0, 0, 0, $trend->month, 1, $trend->year)) }}</td>
                                                            <td>{{ $trend->count }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted text-center">No completion data available for the selected period.</p>
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Export functionality
        document.getElementById('exportBtn').addEventListener('click', function() {
            // Simple CSV export implementation
            const table = document.querySelector('table');
            const rows = table.querySelectorAll('tr');
            let csv = [];

            rows.forEach(row => {
                let rowData = [];
                row.querySelectorAll('th, td').forEach(cell => {
                    rowData.push(cell.innerText);
                });
                csv.push(rowData.join(','));
            });

            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.setAttribute('hidden', '');
            a.setAttribute('href', url);
            a.setAttribute('download', 'maintenance_report.csv');
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });
    });
</script>
@endsection
@endsection
