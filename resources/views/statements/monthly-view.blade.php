{{-- resources/views/statements/monthly-view.blade.php --}}
@extends('layouts.app')

@php
    function formatCurrency($amount, $currency = 'USD') {
        if ($currency === 'USD') {
            return '$' . number_format($amount, 2);
        } elseif ($currency === 'KSH' || $currency === 'KES') {
            return 'KSh ' . number_format($amount, 2);
        } else {
            return $currency . ' ' . number_format($amount, 2);
        }
    }

    function getAmountClass($amount) {
        return $amount >= 0 ? 'text-success' : 'text-danger';
    }
@endphp

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Statements for {{ $month->format('F Y') }}
                    </h4>
                    <a href="{{ route('statements.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back to Generator
                    </a>
                </div>
                <div class="card-body">
                    <!-- Summary Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Total Statements</h6>
                                    <h3>{{ $statements->total() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Total Amount</h6>
                                    <h3 class="text-primary">
                                        ${{ number_format($statements->sum('closing_balance'), 2) }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Sent Statements</h6>
                                    <h3 class="text-success">
                                        {{ $statements->where('status', 'sent')->count() }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Pending</h6>
                                    <h3 class="text-warning">
                                        {{ $statements->whereIn('status', ['draft', 'generated'])->count() }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($statements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Statement #</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Period</th>
                                        <th class="text-end">Opening Balance</th>
                                        <th class="text-end">Total Debits</th>
                                        <th class="text-end">Total Credits</th>
                                        <th class="text-end">Closing Balance</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statements as $statement)
                                    @php
                                        // Determine currency (you may need to adjust this based on your data)
                                        $currency = $statement->currency ?? 'USD';
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $statement->statement_number }}</strong>
                                        </td>
                                        <td>
                                            {{ $statement->customer->name ?? 'N/A' }}
                                            @if(!empty($statement->customer->company_name))
                                                <br><small class="text-muted">{{ $statement->customer->company_name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $statement->statement_date->format('d/m/Y') }}</td>
                                        <td>
                                            <small>
                                                {{ $statement->period_start->format('d/m/Y') }}<br>
                                                <span class="text-muted">to</span><br>
                                                {{ $statement->period_end->format('d/m/Y') }}
                                            </small>
                                        </td>
                                        <td class="text-end {{ getAmountClass($statement->opening_balance) }}">
                                            {{ formatCurrency($statement->opening_balance, $currency) }}
                                        </td>
                                        <td class="text-end text-danger">
                                            {{ formatCurrency($statement->total_debits, $currency) }}
                                        </td>
                                        <td class="text-end text-success">
                                            {{ formatCurrency($statement->total_credits, $currency) }}
                                        </td>
                                        <td class="text-end {{ getAmountClass($statement->closing_balance) }} fw-bold">
                                            {{ formatCurrency($statement->closing_balance, $currency) }}
                                        </td>
                                        <td>
                                            @if($statement->status == 'draft')
                                                <span class="badge bg-secondary">Draft</span>
                                            @elseif($statement->status == 'generated')
                                                <span class="badge bg-info">Generated</span>
                                            @elseif($statement->status == 'sent')
                                                <span class="badge bg-success">Sent</span>
                                            @elseif($statement->status == 'viewed')
                                                <span class="badge bg-primary">Viewed</span>
                                            @elseif($statement->status == 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($statement->status == 'overdue')
                                                <span class="badge bg-danger">Overdue</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('statements.download', $statement->id) }}"
                                                   class="btn btn-info"
                                                   title="Download PDF"
                                                   target="_blank">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-success"
                                                        title="Send to Customer"
                                                        onclick="sendStatement({{ $statement->id }})"
                                                        {{ $statement->status == 'sent' ? 'disabled' : '' }}>
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-primary"
                                                        title="View Details"
                                                        onclick="viewStatement({{ $statement->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <th colspan="4" class="text-end">Totals:</th>
                                        <th class="text-end">{{ formatCurrency($statements->sum('opening_balance'), 'USD') }}</th>
                                        <th class="text-end text-danger">{{ formatCurrency($statements->sum('total_debits'), 'USD') }}</th>
                                        <th class="text-end text-success">{{ formatCurrency($statements->sum('total_credits'), 'USD') }}</th>
                                        <th class="text-end">{{ formatCurrency($statements->sum('closing_balance'), 'USD') }}</th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                Showing {{ $statements->firstItem() }} to {{ $statements->lastItem() }} of {{ $statements->total() }} statements
                            </div>
                            <div>
                                {{ $statements->links() }}
                            </div>
                        </div>

                        <!-- Bulk Actions -->
                        <div class="mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Bulk Actions</h5>
                                </div>
                                <div class="card-body">
                                    <button type="button" class="btn btn-success me-2" onclick="sendAllStatements()">
                                        <i class="fas fa-envelope me-1"></i>Send All Unsent
                                    </button>
                                    <button type="button" class="btn btn-primary me-2" onclick="downloadAllStatements()">
                                        <i class="fas fa-download me-1"></i>Download All
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="exportToExcel()">
                                        <i class="fas fa-file-excel me-1"></i>Export to Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No statements found for {{ $month->format('F Y') }}.
                            <a href="{{ route('statements.index') }}" class="alert-link">Generate new statements</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statement Details Modal -->
<div class="modal fade" id="statementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Statement Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="statementModalBody">
                Loading...
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function sendStatement(statementId) {
    if (confirm('Are you sure you want to send this statement to the customer?')) {
        const button = event.currentTarget;
        const originalHtml = button.innerHTML;

        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;

        fetch(`/statements/${statementId}/send`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', data.message);
                // Update status badge
                const row = button.closest('tr');
                const statusCell = row.querySelector('td:nth-child(9)');
                statusCell.innerHTML = '<span class="badge bg-success">Sent</span>';
                button.disabled = true;
            } else {
                showNotification('error', 'Error: ' + data.message);
            }
        })
        .catch(error => {
            showNotification('error', 'Error sending statement: ' + error.message);
        })
        .finally(() => {
            button.innerHTML = originalHtml;
        });
    }
}

function viewStatement(statementId) {
    const modal = new bootstrap.Modal(document.getElementById('statementModal'));
    const modalBody = document.getElementById('statementModalBody');

    modalBody.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p>Loading...</p></div>';
    modal.show();

    // Fetch statement details via AJAX
    fetch(`/statements/${statementId}/details`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayStatementDetails(data.statement);
        } else {
            modalBody.innerHTML = '<div class="alert alert-danger">Error loading statement details</div>';
        }
    })
    .catch(error => {
        modalBody.innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
    });
}

function displayStatementDetails(statement) {
    const modalBody = document.getElementById('statementModalBody');
    let html = `
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr><th>Statement #:</th><td>${statement.number}</td></tr>
                    <tr><th>Customer:</th><td>${statement.customer}</td></tr>
                    <tr><th>Date:</th><td>${statement.date}</td></tr>
                    <tr><th>Period:</th><td>${statement.period}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr><th>Opening Balance:</th><td class="text-end">${statement.opening_balance}</td></tr>
                    <tr><th>Closing Balance:</th><td class="text-end fw-bold ${statement.closing_balance_class}">${statement.closing_balance}</td></tr>
                    <tr><th>Status:</th><td><span class="badge bg-${statement.status_color}">${statement.status}</span></td></tr>
                </table>
            </div>
        </div>
    `;
    modalBody.innerHTML = html;
}

function sendAllStatements() {
    if (confirm('Send all unsent statements to their customers?')) {
        showNotification('info', 'Sending statements...');
        // Implement bulk send logic
    }
}

function downloadAllStatements() {
    showNotification('info', 'Preparing ZIP file for download...');
    // Implement bulk download logic
}

function exportToExcel() {
    const month = '{{ $month->format('Y-m') }}';
    window.location.href = `/statements/export/excel?month=${month}`;
}

function showNotification(type, message) {
    // You can replace this with toastr or any other notification library
    if (type === 'error') {
        alert('Error: ' + message);
    } else {
        console.log(type + ': ' + message);
    }
}

// Auto-refresh every 30 seconds for pending statements
setInterval(function() {
    const hasPending = document.querySelector('.badge.bg-info, .badge.bg-secondary');
    if (hasPending) {
        location.reload();
    }
}, 30000);
</script>
@endpush
@endsection
