{{-- resources/views/finance/ai-analytics/report.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Analytics Report - {{ $reportData['period'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .report-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 10px;
        }
        .metric-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }
        .insight-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .risk-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        .table-custom {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .print-only {
            display: none;
        }
        @media print {
            .no-print {
                display: none;
            }
            .print-only {
                display: block;
            }
            body {
                background: white;
                font-size: 12pt;
            }
            .metric-card, .insight-card, .table-custom {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="report-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-3">
                        <i class="fas fa-brain me-2"></i>Analytics Report
                    </h1>
                    <p class="lead mb-0">Period: {{ ucfirst($reportData['period']) }} | Generated: {{ $reportData['generated_at'] }}</p>
                </div>
                <div class="col-md-4 text-end no-print">
                    <button onclick="window.print()" class="btn btn-light btn-lg">
                        <i class="fas fa-print me-2"></i>Print Report
                    </button>
                    <a href="{{ route('finance.ai.dashboard') }}" class="btn btn-outline-light btn-lg ms-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Executive Summary -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="insight-card">
                    <h4 class="text-primary mb-3">
                        <i class="fas fa-chart-line me-2"></i>Executive Summary
                    </h4>
                    <p class="lead">{{ $reportData['summary']['executive_summary'] }}</p>

                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h2 class="text-primary mb-1">${{ number_format($reportData['metrics']['total_outstanding'], 0) }}</h2>
                                <small class="text-muted">Total Outstanding</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h2 class="text-danger mb-1">{{ $reportData['metrics']['overdue_percentage'] }}%</h2>
                                <small class="text-muted">Overdue Percentage</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h2 class="text-success mb-1">{{ number_format($reportData['metrics']['collection_rate'], 1) }}%</h2>
                                <small class="text-muted">Collection Rate</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h2 class="text-info mb-1">{{ $reportData['metrics']['overdue_count'] }}</h2>
                                <small class="text-muted">Overdue Invoices</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Insights -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <!-- Key Findings -->
                <div class="insight-card h-100">
                    <h4 class="text-primary mb-3">
                        <i class="fas fa-search me-2"></i>Key Findings
                    </h4>
                    <ul class="list-group list-group-flush">
                        @foreach($reportData['insights']['key_findings'] as $finding)
                        <li class="list-group-item d-flex align-items-start border-0 px-0">
                            <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                            <span>{{ $finding }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <!-- Priority Actions -->
                <div class="insight-card h-100">
                    <h4 class="text-primary mb-3">
                        <i class="fas fa-bullseye me-2"></i>Priority Actions
                    </h4>
                    <div class="list-group">
                        @foreach($reportData['summary']['priority_actions'] as $index => $action)
                        <div class="list-group-item border-start-4 border-primary mb-2">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Action {{ $index + 1 }}</h6>
                                <small class="text-muted">High Priority</small>
                            </div>
                            <p class="mb-1">{{ $action }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Analysis & Recommendations -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="insight-card h-100">
                    <h4 class="text-primary mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>Risk Analysis
                    </h4>
                    <div class="row">
                        @foreach($reportData['insights']['risk_analysis'] as $risk)
                        <div class="col-md-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body py-2">
                                    <p class="mb-0 small">{{ $risk }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="insight-card h-100">
                    <h4 class="text-primary mb-3">
                        <i class="fas fa-lightbulb me-2"></i>All Recommendations
                    </h4>
                    <div class="list-group list-group-flush">
                        @foreach($reportData['insights']['recommendations'] as $recommendation)
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-arrow-right text-info mt-1 me-2"></i>
                                <span>{{ $recommendation }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Aging Analysis -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="insight-card">
                    <h4 class="text-primary mb-3">
                        <i class="fas fa-calendar-alt me-2"></i>Aging Analysis
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-hover table-custom">
                            <thead class="table-light">
                                <tr>
                                    <th>Age Bucket</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">Invoices</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $aging = $reportData['aging_analysis'];
                                    $total = $aging['total'];
                                    $buckets = [
                                        ['label' => 'Current (0-30 days)', 'amount' => $aging['current'], 'count' => $aging['current_count']],
                                        ['label' => '31-60 days', 'amount' => $aging['days_31_60'], 'count' => $aging['days_31_60_count']],
                                        ['label' => '61-90 days', 'amount' => $aging['days_61_90'], 'count' => $aging['days_61_90_count']],
                                        ['label' => 'Over 90 days', 'amount' => $aging['days_over_90'], 'count' => $aging['days_over_90_count']]
                                    ];
                                @endphp
                                @foreach($buckets as $bucket)
                                <tr>
                                    <td>{{ $bucket['label'] }}</td>
                                    <td class="text-end">${{ number_format($bucket['amount'], 0) }}</td>
                                    <td class="text-end">{{ $bucket['count'] }}</td>
                                    <td class="text-end">
                                        {{ $total > 0 ? number_format(($bucket['amount'] / $total) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="table-active">
                                    <td><strong>Total</strong></td>
                                    <td class="text-end"><strong>${{ number_format($total, 0) }}</strong></td>
                                    <td class="text-end"><strong>{{ array_sum(array_column($buckets, 'count')) }}</strong></td>
                                    <td class="text-end"><strong>100%</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Debtors -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="insight-card">
                    <h4 class="text-primary mb-3">
                        <i class="fas fa-users me-2"></i>Top {{ count($reportData['top_debtors']) }} Debtors
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-hover table-custom">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th class="text-end">Outstanding</th>
                                    <th class="text-end">Overdue Invoices</th>
                                    <th class="text-end">Max Days Overdue</th>
                                    <th>Risk Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData['top_debtors'] as $debtor)
                                <tr>
                                    <td>
                                        <strong>{{ $debtor['name'] }}</strong>
                                    </td>
                                    <td>
                                        <small>{{ $debtor['email'] }}</small>
                                    </td>
                                    <td class="text-end">
                                        <strong>${{ number_format($debtor['outstanding'], 0) }}</strong>
                                    </td>
                                    <td class="text-end">{{ $debtor['overdue_invoices'] }}</td>
                                    <td class="text-end">{{ $debtor['max_days_overdue'] }}</td>
                                    <td>
                                        @php
                                            $badgeClass = [
                                                'critical' => 'danger',
                                                'high' => 'warning',
                                                'medium' => 'info',
                                                'low' => 'success'
                                            ][$debtor['risk_level']] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }} risk-badge">
                                            {{ ucfirst($debtor['risk_level']) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="table-active">
                                    <td colspan="2"><strong>Total</strong></td>
                                    <td class="text-end">
                                        <strong>${{ number_format($reportData['top_debtors']->sum('outstanding'), 0) }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ $reportData['top_debtors']->sum('overdue_invoices') }}</strong>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collection Trends -->
        <div class="row">
            <div class="col-12">
                <div class="insight-card">
                    <h4 class="text-primary mb-3">
                        <i class="fas fa-chart-line me-2"></i>Collection Trends Summary
                    </h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="metric-card">
                                <h6 class="text-muted">Total Collected ({{ count($reportData['collection_trends']['labels']) }} days)</h6>
                                <h2 class="text-success">${{ number_format($reportData['collection_trends']['total_collected'], 0) }}</h2>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="metric-card">
                                <h6 class="text-muted">Average Daily Collection</h6>
                                <h2 class="text-success">${{ number_format($reportData['collection_trends']['average_daily'], 0) }}</h2>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="metric-card">
                                <h6 class="text-muted">Total Payments</h6>
                                <h2 class="text-success">{{ array_sum($reportData['collection_trends']['counts']) }}</h2>
                            </div>
                        </div>
                    </div>

                    <!-- Trend Data -->
                    <div class="mt-4">
                        <h6 class="text-muted mb-3">Recent Collection Dates</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-end">Payments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $trends = array_combine(
                                            $reportData['collection_trends']['labels'],
                                            array_map(null,
                                                $reportData['collection_trends']['amounts'],
                                                $reportData['collection_trends']['counts']
                                            )
                                        );
                                        $displayCount = min(10, count($trends));
                                        $counter = 0;
                                    @endphp
                                    @foreach($trends as $date => $data)
                                        @if($counter++ < $displayCount)
                                        <tr>
                                            <td>{{ $date }}</td>
                                            <td class="text-end">${{ number_format($data[0], 0) }}</td>
                                            <td class="text-end">{{ $data[1] }}</td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="text-center text-muted py-3 border-top">
                    <p class="mb-0">
                        <i class="fas fa-shield-alt me-1"></i>Confidential Report - Generated by AI Analytics System
                    </p>
                    <small>Report ID: {{ strtoupper(substr(md5($reportData['generated_at']), 0, 8)) }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Section -->
    <div class="print-only">
        <div style="page-break-before: always;"></div>
        <div class="text-center mt-5">
            <h4>End of Report</h4>
            <p>Thank you for using AI Analytics System</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Print functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Add print button functionality
            const printBtn = document.querySelector('button[onclick="window.print()"]');
            if (printBtn) {
                printBtn.addEventListener('click', function() {
                    window.print();
                });
            }
        });
    </script>
</body>
</html>
