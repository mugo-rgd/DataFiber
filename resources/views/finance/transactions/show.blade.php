{{-- resources/views/finance/transactions/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>Transaction Details
                    </h4>
                    <div>
                        <a href="{{ route('finance.transactions.edit', $transaction->id) }}" class="btn btn-light btn-sm me-2">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('finance.transactions.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Transaction Information -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Transaction Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="150">Transaction #:</th>
                                            <td>{{ $transaction->transaction_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>Date:</th>
                                            <td>{{ $transaction->transaction_date->format('F d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Type:</th>
                                            <td>
                                                <span class="badge bg-{{ $transaction->type == 'income' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Category:</th>
                                            <td>{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                @if($transaction->status == 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @elseif($transaction->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($transaction->status == 'failed')
                                                    <span class="badge bg-danger">Failed</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Reference:</th>
                                            <td>{{ $transaction->reference_number ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Information -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Amount Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="150">Amount:</th>
                                            <td class="fw-bold {{ $transaction->amount_class }}">
                                                {{ $transaction->formatted_amount }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Direction:</th>
                                            <td>
                                                @if($transaction->direction == 'in')
                                                    <span class="text-success">
                                                        <i class="fas fa-arrow-down me-1"></i>Incoming
                                                    </span>
                                                @else
                                                    <span class="text-danger">
                                                        <i class="fas fa-arrow-up me-1"></i>Outgoing
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Currency:</th>
                                            <td>
                                                <span class="badge bg-{{ $transaction->currency == 'USD' ? 'primary' : 'warning' }}">
                                                    {{ $transaction->currency }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Balance After:</th>
                                            <td class="fw-bold">{{ $transaction->formatted_balance }}</td>
                                        </tr>
                                        <tr>
                                            <th>Payment Method:</th>
                                            <td>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method ?? 'N/A')) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Customer Information</h5>
                                </div>
                                <div class="card-body">
                                    @if($transaction->user)
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th width="150">Name:</th>
                                                <td>{{ $transaction->user->name }}</td>
                                            </tr>
                                            @if($transaction->user->company_name)
                                            <tr>
                                                <th>Company:</th>
                                                <td>{{ $transaction->user->company_name }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th>Email:</th>
                                                <td>{{ $transaction->user->email }}</td>
                                            </tr>
                                            @if($transaction->user->phone)
                                            <tr>
                                                <th>Phone:</th>
                                                <td>{{ $transaction->user->phone }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    @else
                                        <p class="text-muted mb-0">No customer associated</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Additional Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="150">Description:</th>
                                            <td>{{ $transaction->description }}</td>
                                        </tr>
                                        @if($transaction->notes)
                                        <tr>
                                            <th>Notes:</th>
                                            <td>{{ $transaction->notes }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>Created By:</th>
                                            <td>
                                                @if($transaction->createdBy)
                                                    {{ $transaction->createdBy->name }}
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $transaction->created_at->format('F d, Y H:i:s') }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">System</span>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $transaction->created_at->format('F d, Y H:i:s') }}
                                                    </small>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($transaction->completed_at)
                                        <tr>
                                            <th>Completed At:</th>
                                            <td>{{ $transaction->completed_at->format('F d, Y H:i:s') }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related Billing Information -->
                    @if($transaction->reference)
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Related Billing</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">
                                        <strong>Billing Number:</strong>
                                        <a href="{{ route('finance.billing.show', $transaction->reference) }}">
                                            {{ $transaction->reference }}
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                @if($transaction->status == 'pending')
                                <form action="{{ route('finance.transactions.complete', $transaction->id) }}" method="POST" class="me-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Mark this transaction as completed?')">
                                        <i class="fas fa-check-circle me-1"></i>Mark as Completed
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('finance.transactions.destroy', $transaction->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this transaction?')">
                                        <i class="fas fa-trash me-1"></i>Delete Transaction
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table-borderless th {
        font-weight: 600;
        color: #495057;
    }
    .table-borderless td {
        color: #212529;
    }
    .card-header {
        font-weight: 600;
    }
</style>
@endpush
