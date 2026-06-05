@extends('layouts.app')

@section('title', 'Customer Debt List')

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row mb-4 g-3">
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-primary text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1 opacity-75">Customers with Debt</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($summary['total_customers'] ?? 0) }}</h2>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-danger text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1 opacity-75">Total Balance (USD)</h6>
                            <h2 class="mb-0 fw-bold">${{ number_format($summary['total_balance_usd'] ?? 0, 2) }}</h2>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-success text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1 opacity-75">Total Balance (KES)</h6>
                            <h2 class="mb-0 fw-bold">KES {{ number_format($summary['total_balance_ksh'] ?? 0, 2) }}</h2>
                        </div>
                        <i class="fas fa-shilling-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-warning text-dark h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1 opacity-75">Total Invoices</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($summary['total_invoices'] ?? 0) }}</h2>
                        </div>
                        <i class="fas fa-file-invoice fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h1 class="h3 mb-0">
                    <i class="fas fa-users text-kp-blue me-2"></i>Customer Debt List
                </h1>
                <div class="btn-group">
                    <button type="button" class="btn btn-kp-primary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print Report
                    </button>
                    <a href="{{ route('finance.debt.collection.report') }}" class="btn btn-secondary">
                        <i class="fas fa-file-invoice-dollar me-1"></i> Collection Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0" id="customersTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th>Customer Name</th>
                                    <th class="d-none d-md-table-cell">Contact</th>
                                    <th class="text-center">Inv</th>
                                    <th class="text-end">USD Balance</th>
                                    <th class="text-end">KES Balance</th>
                                    <th class="text-center">Due Date</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width: 100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($billings as $index => $billing)
                                @php
                                    $rowClass = '';
                                    $statusBadge = 'bg-success';
                                    $statusText = 'Active';
                                    $dueDate = null;

                                    if ($billing->last_due_date) {
                                        $dueDate = \Carbon\Carbon::parse($billing->last_due_date);
                                        $today = \Carbon\Carbon::today();
                                        if ($dueDate->lt($today)) {
                                            $rowClass = 'table-danger';
                                            $statusBadge = 'bg-danger';
                                            $statusText = 'Overdue';
                                        } elseif ($dueDate->diffInDays($today) <= 7) {
                                            $rowClass = 'table-warning';
                                            $statusBadge = 'bg-warning text-dark';
                                            $statusText = 'Due Soon';
                                        }
                                    }
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $billing->customer_name }}</div>
                                        <small class="text-muted d-block d-md-none">
                                            <i class="fas fa-phone fa-xs me-1"></i>{{ $billing->phone ?? 'N/A' }}
                                        </small>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <div><i class="fas fa-phone fa-xs text-muted me-1"></i>{{ $billing->phone ?? 'N/A' }}</div>
                                        <small class="text-muted"><i class="fas fa-envelope fa-xs me-1"></i>{{ $billing->email ?? 'N/A' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ number_format($billing->billing_count ?? 0) }}</span>
                                    </td>
                                    <td class="text-end fw-bold">
                                        @if(($billing->balance_usd ?? 0) > 0)
                                            <span class="text-danger">${{ number_format($billing->balance_usd, 2) }}</span>
                                        @else
                                            <span class="text-muted">$0.00</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">
                                        @if(($billing->balance_ksh ?? 0) > 0)
                                            <span class="text-danger">KES {{ number_format($billing->balance_ksh, 2) }}</span>
                                        @else
                                            <span class="text-muted">KES 0.00</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($dueDate)
                                            <span class="{{ $dueDate->lt(now()) ? 'text-danger' : ($dueDate->diffInDays(now()) <= 7 ? 'text-warning' : 'text-muted') }}">
                                                {{ $dueDate->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $statusBadge }} px-2 py-1">{{ $statusText }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('finance.debt.customer', ['id' => $billing->id]) }}"
                                           class="btn btn-sm btn-outline-kp-primary"
                                           data-bs-toggle="tooltip"
                                           title="View Customer Details">
                                            <i class="fas fa-eye"></i>
                                            <span class="d-none d-md-inline ms-1">View</span>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">
                                        <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                        <p class="mb-0">No outstanding customer debts found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($billings->count() > 0)
                            <tfoot class="table-active fw-bold">
                                <tr>
                                    <th colspan="3" class="text-end">TOTALS:</th>
                                    <th class="text-center">{{ number_format($summary['total_invoices'] ?? 0) }}</th>
                                    <th class="text-end text-danger">${{ number_format($summary['total_balance_usd'] ?? 0, 2) }}</th>
                                    <th class="text-end text-danger">KES {{ number_format($summary['total_balance_ksh'] ?? 0, 2) }}</th>
                                    <th colspan="3"></th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- jQuery and DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

<script>
$(document).ready(function() {
    @if($billings->count() > 0)
        $('#customersTable').DataTable({
            "pageLength": 25,
            "order": [[4, 'desc']], // Sort by USD balance column
            "responsive": true,
            "language": {
                "search": "Search customers:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ customers",
                "paginate": {
                    "previous": "<",
                    "next": ">"
                }
            },
            "columnDefs": [
                { "orderable": false, "targets": [0, 7, 8] },
                { "className": "dt-center", "targets": [0, 3, 6, 7, 8] }
            ]
        });
    @endif

    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush

@push('styles')
<style>
    /* Summary Cards */
    .card.bg-primary, .card.bg-danger, .card.bg-success {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: linear-gradient(135deg, #0066B3, #005199) !important;
    }

    .card.bg-danger {
        background: linear-gradient(135deg, #dc3545, #c82333) !important;
    }

    .card.bg-success {
        background: linear-gradient(135deg, #28a745, #1e7e34) !important;
    }

    .card.bg-primary:hover, .card.bg-danger:hover, .card.bg-success:hover, .card.bg-warning:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .card.bg-warning {
        background: linear-gradient(135deg, #f39c12, #e67e22) !important;
        color: white !important;
    }

    .card.bg-warning .opacity-75 {
        color: rgba(255,255,255,0.9) !important;
    }

    .card.bg-warning h2,
    .card.bg-warning h6,
    .card.bg-warning i {
        color: white !important;
    }

    /* Table improvements */
    .table th {
        font-weight: 600;
        font-size: 0.85rem;
        white-space: nowrap;
    }

    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .text-danger {
        font-weight: 600;
    }

    .text-warning {
        color: #e67e22 !important;
        font-weight: 500;
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
        .table th, .table td {
            font-size: 0.8rem;
            padding: 0.6rem 0.4rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
        }

        .btn-group {
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        h2 {
            font-size: 1.3rem;
        }

        h6 {
            font-size: 0.7rem;
        }
    }

    /* Print styles */
    @media print {
        .btn-group, .btn, .dataTables_filter, .dataTables_length, .dataTables_paginate {
            display: none !important;
        }

        .table {
            font-size: 10pt;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #ddd;
        }

        .badge {
            border: 1px solid #000;
            background: none !important;
            color: #000 !important;
        }

        .table-danger, .table-warning {
            background: #f5f5f5 !important;
        }
    }

    /* DataTables styling */
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 0.3rem 0.8rem;
        margin-left: 0.5rem;
    }

    .dataTables_wrapper .dataTables_length select {
        border-radius: 20px;
        padding: 0.2rem 0.5rem;
    }

    .dataTables_info {
        padding-top: 0.75rem;
    }

    .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
        margin: 0 2px;
    }

    .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, var(--kp-blue), var(--kp-green)) !important;
        border-color: transparent !important;
        color: white !important;
    }
</style>
@endpush
@endsection
