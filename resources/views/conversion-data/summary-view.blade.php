@extends('layouts.app')

@section('title', 'Customers Summary')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Customers Summary</h1>
        <div>
            <a href="{{ route('conversion-data.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-1"></i> Add New Data
            </a>
            <button onclick="window.print()" class="btn btn-secondary shadow-sm">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $customers->total() }}</div>
                            <div class="text-xs text-muted mt-1">
                                Showing {{ $customers->firstItem() ?? 0 }}-{{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }}
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
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Contracts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalContracts) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
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
                                Total Value (USD)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($totalValueUSD, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                Total Value (KES)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">KES {{ number_format($totalValueKES, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination Info (Top) -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="text-muted">
                Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} entries
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <!-- Optional: Items per page selector -->
                <div class="dropdown me-2">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        {{ $customers->perPage() }} per page
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}">25</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}">50</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 100]) }}">100</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 250]) }}">250</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Customers Contract Summary</h6>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu">
                    <a href="{{ route('conversion-data.export.excel') }}"
           class="list-group-item list-group-item-action"
           onclick="applyExportFilters(this)">
            <i class="fas fa-file-excel text-success me-2"></i> Export as Excel
        </a>

        <a href="{{ route('conversion-data.export.csv') }}"
           class="list-group-item list-group-item-action"
           onclick="applyExportFilters(this)">
            <i class="fas fa-file-csv text-info me-2"></i> Export as CSV
        </a>

        <a href="{{ route('conversion-data.export.pdf') }}"
           class="list-group-item list-group-item-action"
           onclick="applyExportFilters(this)">
            <i class="fas fa-file-pdf text-danger me-2"></i> Export as PDF
        </a>

        <!-- Option 2: Using single route with format parameter -->
        <a href="{{ route('conversion-data.export', ['format' => 'excel']) }}"
           class="list-group-item list-group-item-action d-none">
            <i class="fas fa-file-excel text-success me-2"></i> Export as Excel
        </a>

                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="customersTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Customer Name</th>
                            <th>Customer REF</th>
                            <th>Contracts</th>
                            <th>Total Value (USD)</th>
                            <th>Total Value (KES)</th>
                            <th>Distance (KM)</th>
                            <th>Share of Total (USD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // These calculations are for current page only
                            $customersWithUSD = $customers->where('total_contract_value_usd', '>', 0)->count();
                            $customersWithKES = $customers->where('total_contract_value_kes', '>', 0)->count();
                            $customersWithBoth = $customers->where('total_contract_value_usd', '>', 0)
                                                           ->where('total_contract_value_kes', '>', 0)
                                                           ->count();
                        @endphp
                        @forelse($customers as $index => $customer)
                        @php
                            $totalUSD = $customer->total_contract_value_usd ?? 0;
                            $totalKES = $customer->total_contract_value_kes ?? 0;
                            $contractCount = $customer->contract_count ?? 0;
                            $distanceKM = $customer->distance_km ?? 0;
                            $shareOfTotal = $totalValueUSD > 0 ? ($totalUSD / $totalValueUSD) * 100 : 0;
                        @endphp
                        <tr>
                            <td>{{ $customers->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $customer->customer_name ?? 'N/A' }}</strong>
                            </td>
                            <td>
                                @if($customer->customer_ref)
                                    <span class="badge bg-info">{{ $customer->customer_ref }}</span>
                                @else
                                    <span class="text-muted">No REF</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary rounded-pill">{{ $contractCount }}</span>
                            </td>
                            <td class="text-success">
                                <strong>
                                    @if($totalUSD > 0)
                                        ${{ number_format($totalUSD, 2) }}
                                    @else
                                        <span class="text-muted">$0.00</span>
                                    @endif
                                </strong>
                            </td>
                            <td class="text-warning">
                                <strong>
                                    @if($totalKES > 0)
                                        KES {{ number_format($totalKES, 2) }}
                                    @else
                                        <span class="text-muted">KES 0.00</span>
                                    @endif
                                </strong>
                            </td>
                            <td>
                                @if($distanceKM > 0)
                                    {{ number_format($distanceKM, 2) }} km
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-primary" role="progressbar"
                                         style="width: {{ min($shareOfTotal, 100) }}%"
                                         aria-valuenow="{{ $shareOfTotal }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        {{ number_format($shareOfTotal, 1) }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-2x mb-3"></i>
                                <p>No customers found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="font-weight-bold">
                        <tr>
                            <td colspan="3" class="text-end"><strong>GRAND TOTAL</strong></td>
                            <td><strong>{{ number_format($totalContracts) }}</strong></td>
                            <td class="text-success"><strong>${{ number_format($totalValueUSD, 2) }}</strong></td>
                            <td class="text-warning"><strong>KES {{ number_format($totalValueKES, 2) }}</strong></td>
                            <td><strong>{{ number_format($totalDistanceKM, 2) }} km</strong></td>
                            <td><strong>100%</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} entries
                </div>
                <nav aria-label="Page navigation">
                    {{ $customers->withQueryString()->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="row">
        <!-- Top 10 Customers -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 Customers by Value (USD)</h6>
                    @if(isset($topCustomers) && $topCustomers->count() > 0)
                        <span class="badge bg-primary">{{ $topCustomers->count() }} customers</span>
                    @endif
                </div>
                <div class="card-body">
                    @if(isset($topCustomers) && $topCustomers->count() > 0)
                        <div class="chart-container" style="position: relative; height:300px;">
                            <canvas id="topCustomersChart"></canvas>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-chart-bar fa-3x mb-3"></i>
                            <p>No data available for top customers chart</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Currency Distribution Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Currency Distribution</h6>
                    @php
                        $usdOnly = $currencyDistribution->usd_only ?? 0;
                        $kesOnly = $currencyDistribution->kes_only ?? 0;
                        $both = $currencyDistribution->both ?? 0;
                    @endphp
                    @if(($usdOnly + $kesOnly + $both) > 0)
                        <span class="badge bg-primary">{{ $usdOnly + $kesOnly + $both }} customers</span>
                    @endif
                </div>
                <div class="card-body">
                    @if(($usdOnly + $kesOnly + $both) > 0)
                        <div class="chart-container" style="position: relative; height:300px;">
                            <canvas id="currencyDistributionChart"></canvas>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-chart-pie fa-3x mb-3"></i>
                            <p>No data available for currency distribution chart</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Section -->
    <div class="card shadow mt-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle me-2"></i>Summary Notes
            </h6>
        </div>
        <div class="card-body">
            @php
                $usdOnly = $currencyDistribution->usd_only ?? 0;
                $kesOnly = $currencyDistribution->kes_only ?? 0;
                $both = $currencyDistribution->both ?? 0;
                $totalCustomers = $usdOnly + $kesOnly + $both;
            @endphp
            <ul>
                <li>Total of <strong>{{ $totalCustomers }}</strong> unique customers with contracts</li>
                <li><strong>{{ $usdOnly }}</strong> customers with USD contracts only</li>
                <li><strong>{{ $kesOnly }}</strong> customers with KES contracts only</li>
                <li><strong>{{ $both }}</strong> customers with both USD & KES contracts</li>
                <li><strong>{{ number_format($totalValueUSD, 2) }}</strong> total contract value in USD</li>
                <li><strong>{{ number_format($totalValueKES, 2) }}</strong> total contract value in KES</li>
                <li>Total fiber distance: <strong>{{ number_format($totalDistanceKM, 2) }} km</strong></li>
                <li>Average contract value: <strong>${{ number_format($totalValueUSD / max($totalContracts, 1), 2) }}</strong></li>
                <li>Average distance per contract: <strong>{{ number_format($totalDistanceKM / max($totalContracts, 1), 2) }} km</strong></li>
                <li>Data as of {{ now()->format('F j, Y') }}</li>
                <li>Displaying <strong>{{ $customers->count() }}</strong> customers on this page of {{ $customers->total() }} total</li>
            </ul>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
    // Export Functions (keep these as they are)
    function exportToCSV() {
        const table = document.getElementById('customersTable');
        let csv = [];
        const rows = table.querySelectorAll('tr');

        for (let i = 0; i < rows.length; i++) {
            const row = [], cols = rows[i].querySelectorAll('td, th');
            for (let j = 0; j < cols.length; j++) {
                if (j === 7) continue;
                let text = cols[j].innerText
                    .replace(/(\r\n|\n|\r)/gm, '')
                    .replace(/(\s\s)/gm, ' ')
                    .trim();
                if (text.includes(',')) {
                    text = '"' + text + '"';
                }
                row.push(text);
            }
            csv.push(row.join(','));
        }
        downloadCSV(csv.join('\n'), 'customers-summary-page-{{ $customers->currentPage() }}.csv');
    }

    function exportAllToCSV() {
        if (confirm('This will export all {{ $customers->total() }} customers. Continue?')) {
            alert('Export all functionality would require a backend endpoint to fetch all records.');
        }
    }

    function downloadCSV(csv, filename) {
        const csvFile = new Blob([csv], {type: 'text/csv'});
        const downloadLink = document.createElement('a');
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = 'none';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }

    function exportToExcel() {
        alert('Excel export functionality would be implemented here');
    }

    function exportToPDF() {
        alert('PDF export functionality would be implemented here');
    }

    // Initialize charts when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing charts...');

        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded!');
            return;
        }

        // Prepare data for Top Customers Chart
        @if(isset($topCustomers) && $topCustomers->count() > 0)
            const topCustomers = @json($topCustomers);
            console.log('Top customers data:', topCustomers);

            const customerNames = topCustomers.map(c => c.customer_name || 'Unknown');
            const customerValues = topCustomers.map(c => parseFloat(c.total_contract_value_usd) || 0);

            console.log('Customer names:', customerNames);
            console.log('Customer values:', customerValues);

            // Create Top Customers Bar Chart
            const barCanvas = document.getElementById('topCustomersChart');
            if (barCanvas) {
                const barCtx = barCanvas.getContext('2d');

                // Clear any existing chart
                if (window.topCustomersChartInstance) {
                    window.topCustomersChartInstance.destroy();
                }

                window.topCustomersChartInstance = new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: customerNames,
                        datasets: [{
                            label: 'Total Value (USD)',
                            data: customerValues,
                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return '$' + context.parsed.y.toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });
                console.log('Top customers chart created');
            }
        @endif

        // Prepare data for Currency Distribution Chart
        @php
            $usdOnly = $currencyDistribution->usd_only ?? 0;
            $kesOnly = $currencyDistribution->kes_only ?? 0;
            $both = $currencyDistribution->both ?? 0;
        @endphp

        @if(($usdOnly + $kesOnly + $both) > 0)
            const usdOnly = {{ $usdOnly }};
            const kesOnly = {{ $kesOnly }};
            const both = {{ $both }};

            console.log('Currency distribution:', { usdOnly, kesOnly, both });

            // Create Currency Distribution Pie Chart
            const pieCanvas = document.getElementById('currencyDistributionChart');
            if (pieCanvas) {
                const pieCtx = pieCanvas.getContext('2d');

                // Clear any existing chart
                if (window.currencyDistributionChartInstance) {
                    window.currencyDistributionChartInstance.destroy();
                }

                window.currencyDistributionChartInstance = new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: ['USD Only', 'KES Only', 'Both USD & KES'],
                        datasets: [{
                            data: [usdOnly, kesOnly, both],
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(255, 206, 86, 0.7)',
                                'rgba(75, 192, 192, 0.7)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return `${context.label}: ${value} customers (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Currency distribution chart created');
            }
        @endif
    });

    // Print-specific styling
    window.onbeforeprint = function() {
        document.querySelectorAll('.card').forEach(card => {
            card.classList.add('print-mode');
        });
    };

    window.onafterprint = function() {
        document.querySelectorAll('.card').forEach(card => {
            card.classList.remove('print-mode');
        });
    };
</script>

<style>
    /* Print-specific styles */
    @media print {
        .btn, .dropdown, .progress-bar .progress-bar-text, .pagination, .text-muted:not(.print-keep) {
            display: none !important;
        }
        .progress-bar {
            border: 1px solid #ddd !important;
            background-color: #f8f9fa !important;
            color: #333 !important;
            text-align: center;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }
        table {
            font-size: 11px;
        }
        .no-print {
            display: none !important;
        }
        .page-heading {
            page-break-after: avoid;
        }
        .chart-container {
            page-break-inside: avoid;
        }
        .table tbody tr {
            page-break-inside: avoid;
        }
    }

    /* Pagination styling */
    .pagination {
        margin-bottom: 0;
    }
    .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    /* Progress bar styling */
    .progress {
        background-color: #e9ecef;
        border-radius: 4px;
    }
    .progress-bar {
        border-radius: 4px;
        font-size: 11px;
        line-height: 20px;
        color: white;
        text-shadow: 0 0 2px rgba(0,0,0,0.5);
    }

    /* Hover effects */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    /* Card animations */
    .card {
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-2px);
    }

    /* Badge styling */
    .badge {
        font-weight: 500;
        font-size: 0.85em;
    }

    /* Empty state styling */
    .table tbody tr td.text-center {
        background-color: #f8f9fa;
    }

    /* Chart container styling */
    .chart-container {
        position: relative;
    }
</style>
@endsection

@section('styles')
<!-- Additional styles -->
<style>
    .badge {
        font-weight: 500;
    }
    .card-header {
        background-color: #f8f9fc;
    }
    .table th {
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    /* Chart styling */
    canvas {
        display: block;
        max-width: 100%;
    }
    /* Currency-specific text colors */
    .text-success {
        color: #28a745 !important;
    }
    .text-warning {
        color: #ffc107 !important;
    }
</style>
@endsection
