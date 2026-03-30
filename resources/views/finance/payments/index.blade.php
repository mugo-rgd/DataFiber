<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments Management - Finance</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .stats-card {
            border-left: 4px solid #007bff;
        }
        .stats-card.success {
            border-left-color: #28a745;
        }
        .stats-card.warning {
            border-left-color: #ffc107;
        }
        .stats-card.danger {
            border-left-color: #dc3545;
        }
        .payment-status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .payment-status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .payment-status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 text-primary">
                    <i class="fas fa-credit-card me-2"></i>Payments Management
                </h1>
                <p class="text-muted mb-0">Manage and track all payment transactions</p>
            </div>
            <a href="{{ route('finance.dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stats-card success h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">Total Collected</h6>
                                <h3 class="text-success">KSh {{ number_format($paymentStats['total_collected'] ?? 0, 2) }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card warning h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">Pending Payments</h6>
                                <h3 class="text-warning">{{ $paymentStats['pending_payments'] ?? 0 }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card danger h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">Failed Payments</h6>
                                <h3 class="text-danger">{{ $paymentStats['failed_payments'] ?? 0 }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>Filters
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('finance.payments') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" name="date_from" id="date_from"
                                   class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" name="date_to" id="date_to"
                                   class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Payment Records
                </h5>
            </div>
            <div class="card-body">
                @if($payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Payment Date</th>
                                    <th>Status</th>
                                    <th>Reference</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td>#{{ $payment->id }}</td>
                                        <td>
                                            @if(isset($payment->customer_name))
                                                <strong>{{ $payment->customer_name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $payment->customer_email ?? 'N/A' }}</small>
                                            @else
                                                <span class="text-muted">Customer #{{ $payment->user_id ?? $payment->customer_id }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>KSh {{ number_format($payment->amount, 2) }}</strong>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                                        </td>
                                        <td>
                                            <span class="badge payment-status-{{ $payment->status }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <code>{{ $payment->reference ?? $payment->transaction_id ?? 'N/A' }}</code>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary"
                                                        data-bs-toggle="tooltip"
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @if($payment->status === 'pending')
                                                    <button class="btn btn-outline-success"
                                                            data-bs-toggle="tooltip"
                                                            title="Mark as Completed">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-outline-info"
                                                        data-bs-toggle="tooltip"
                                                        title="Download Receipt">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} entries
                        </div>
                        <div>
                            {{ $payments->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="text-muted mb-3">
                            <i class="fas fa-credit-card fa-4x"></i>
                        </div>
                        <h4 class="text-muted">No Payments Found</h4>
                        <p class="text-muted">No payment records match your current filters.</p>
                        <a href="{{ route('finance.payments') }}" class="btn btn-primary">
                            <i class="fas fa-refresh me-2"></i>Clear Filters
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bootstrap & Font Awesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Auto-submit form when filters change (optional)
        document.getElementById('status').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>
</html>
