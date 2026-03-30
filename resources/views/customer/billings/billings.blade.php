{{-- resources/views/customer/billings.blade.php --}}
@extends('layouts.app')

@section('title', 'My Billings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Billings</li>
                    </ol>
                </div>
                <h4 class="page-title">My Billings</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h4 class="header-title">Billing History</h4>
                            <p class="text-muted font-14">
                                View and manage your billing statements
                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="text-md-end">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                                    <i class="fas fa-download me-2"></i>Export
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Billings</h5>
                                    <h3>{{ $billings->total() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Paid</h5>
                                    <h3>{{ $billings->where('status', 'paid')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Pending</h5>
                                    <h3>{{ $billings->where('status', 'pending')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Overdue</h5>
                                    <h3>{{ $billings->where('status', 'overdue')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billings Table -->
                    <div class="table-responsive">
                        <table class="table table-centered table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Billing #</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($billings as $billing)
                                    <tr>
                                        <td>
                                            <strong>{{ $billing->billing_number }}</strong>
                                        </td>
                                        <td>{{ $billing->billing_date->format('d M Y') }}</td>
                                        <td>
                                            {{ $billing->due_date->format('d M Y') }}
                                            @if($billing->due_date->isPast() && $billing->status !== 'paid')
                                                <span class="badge bg-danger ms-1">Overdue</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($billing->description ?? 'N/A', 30) }}</td>
                                        <td>
                                            <strong>{{ number_format($billing->total_amount, 2) }} {{ $billing->currency }}</strong>
                                            @if($billing->currency !== 'KES' && isset($billing->total_amount_kes))
                                                <br>
                                                <small class="text-muted">≈ {{ number_format($billing->total_amount_kes, 2) }} KES</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusClasses = [
                                                    'draft' => 'secondary',
                                                    'pending' => 'warning',
                                                    'paid' => 'success',
                                                    'overdue' => 'danger',
                                                    'cancelled' => 'secondary'
                                                ];
                                                $statusClass = $statusClasses[$billing->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ ucfirst($billing->status) }}
                                            </span>
                                            @if($billing->tevin_control_code)
                                                <br>
                                                <small class="text-muted">KRA Validated</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('customer.billings.show', $billing->id) }}"
                                                   class="btn btn-sm btn-info"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('customer.billings.download', $billing->id) }}"
                                                   class="btn btn-sm btn-primary"
                                                   title="Download PDF">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @if($billing->status === 'pending' || $billing->status === 'overdue')
                                                    <button type="button"
                                                            class="btn btn-sm btn-success"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#payModal{{ $billing->id }}"
                                                            title="Make Payment">
                                                        <i class="fas fa-credit-card"></i>
                                                    </button>
                                                @endif
                                            </div>

                                            <!-- Payment Modal -->
                                            @if($billing->status === 'pending' || $billing->status === 'overdue')
                                                <div class="modal fade" id="payModal{{ $billing->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Make Payment</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('customer.billings.pay', $billing->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <p><strong>Billing #:</strong> {{ $billing->billing_number }}</p>
                                                                    <p><strong>Amount:</strong> {{ number_format($billing->total_amount, 2) }} {{ $billing->currency }}</p>

                                                                    <div class="mb-3">
                                                                        <label for="payment_method" class="form-label">Payment Method</label>
                                                                        <select class="form-select" name="payment_method" required>
                                                                            <option value="">Select Method</option>
                                                                            <option value="bank_transfer">Bank Transfer</option>
                                                                            <option value="mpesa">M-PESA</option>
                                                                            <option value="credit_card">Credit Card</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label for="payment_reference" class="form-label">Payment Reference</label>
                                                                        <input type="text" class="form-control" name="payment_reference"
                                                                               placeholder="Enter transaction reference">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-success">Confirm Payment</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No billing records found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                {{ $billings->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Billings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('customer.billings.export') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Export Format</label>
                        <select class="form-select" name="format" required>
                            <option value="pdf">PDF</option>
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" class="form-control" name="date_from" placeholder="From">
                            </div>
                            <div class="col-md-6">
                                <input type="date" class="form-control" name="date_to" placeholder="To">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All Statuses</option>
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.4rem 0.6rem;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    });
</script>
@endpush
