{{-- resources/views/customer-portal/statements/show.blade.php --}}
@extends('layouts.app')

@php
    // Determine currencies used in transactions
    $currencies = [];
    $primaryCurrency = 'USD'; // Default

    if (isset($transactions) && $transactions->count() > 0) {
        foreach ($transactions as $trans) {
            if (!empty($trans->currency)) {
                $currencies[$trans->currency] = true;
            }
        }

        // If there's only one currency type, use that as primary
        if (count($currencies) === 1) {
            $primaryCurrency = array_key_first($currencies);
        }
    }

    // Format currency function
    function formatCurrency($amount, $currency = 'USD') {
        if ($currency === 'USD') {
            return '$' . number_format($amount, 2);
        } elseif ($currency === 'KSH' || $currency === 'KES') {
            return 'KSh ' . number_format($amount, 2);
        } else {
            return $currency . ' ' . number_format($amount, 2);
        }
    }

    // Format amount with appropriate class
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
                        <i class="fas fa-file-invoice me-2"></i>Statement Details
                    </h4>
                    <div>
                        @if(count($currencies) > 1)
                            <span class="badge bg-warning text-dark me-2">Multi-Currency</span>
                        @endif
                        <a href="{{ route('statements.download', $statement->id) }}" class="btn btn-light btn-sm me-2">
                            <i class="fas fa-download me-1"></i>Download PDF
                        </a>
                        <a href="{{ route('customer.statements') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statement Header -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Statement Information</h5>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="150"><strong>Statement #:</strong></td>
                                    <td>{{ $statement->statement_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date:</strong></td>
                                    <td>{{ $statement->statement_date->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Period:</strong></td>
                                    <td>{{ $statement->period_start->format('F d, Y') }} - {{ $statement->period_end->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($statement->status == 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($statement->status == 'generated')
                                            <span class="badge bg-info">Generated</span>
                                        @elseif($statement->status == 'sent')
                                            <span class="badge bg-success">Sent</span>
                                        @elseif($statement->status == 'viewed')
                                            <span class="badge bg-primary">Viewed</span>
                                        @endif
                                    </td>
                                </tr>
                                @if(count($currencies) > 1)
                                <tr>
                                    <td><strong>Currencies:</strong></td>
                                    <td>{{ implode(', ', array_keys($currencies)) }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Summary</h5>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="150"><strong>Opening Balance:</strong></td>
                                    <td class="{{ getAmountClass($statement->opening_balance) }}">
                                        {{ formatCurrency($statement->opening_balance, $primaryCurrency) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Debits:</strong></td>
                                    <td class="text-danger">
                                        {{ formatCurrency($statement->total_debits, $primaryCurrency) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Credits:</strong></td>
                                    <td class="text-success">
                                        {{ formatCurrency($statement->total_credits, $primaryCurrency) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Closing Balance:</strong></td>
                                    <td class="{{ getAmountClass($statement->closing_balance) }} fw-bold">
                                        {{ formatCurrency($statement->closing_balance, $primaryCurrency) }}
                                    </td>
                                </tr>
                                @if(count($currencies) > 1)
                                <tr>
                                    <td colspan="2" class="text-muted small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Summary shown in {{ $primaryCurrency }}
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Transactions Table -->
                    <h5>Transaction Details</h5>
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Reference</th>
                                        <th>Currency</th>
                                        <th class="text-end">Debit</th>
                                        <th class="text-end">Credit</th>
                                        <th class="text-end">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                    @php
                                        $txCurrency = $transaction->currency ?? 'USD';
                                    @endphp
                                    <tr>
                                        <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                                        <td>{{ $transaction->description }}</td>
                                        <td>{{ $transaction->reference ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $txCurrency === 'USD' ? 'bg-primary' : 'bg-warning text-dark' }}">
                                                {{ $txCurrency }}
                                            </span>
                                        </td>
                                        <td class="text-end text-danger">
                                            {{ $transaction->direction == 'out' ? formatCurrency($transaction->amount, $txCurrency) : '-' }}
                                        </td>
                                        <td class="text-end text-success">
                                            {{ $transaction->direction == 'in' ? formatCurrency($transaction->amount, $txCurrency) : '-' }}
                                        </td>
                                        <td class="text-end">
                                            {{ formatCurrency($transaction->balance, $txCurrency) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="6" class="text-end">Statement Total ({{ $primaryCurrency }}):</th>
                                        <th class="text-end {{ getAmountClass($statement->closing_balance) }}">
                                            {{ formatCurrency($statement->closing_balance, $primaryCurrency) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Currency Summary -->
                        @if(count($currencies) > 1)
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Multi-Currency Statement:</strong>
                                This statement contains transactions in multiple currencies.
                                <hr>
                                <div class="row">
                                    @foreach(array_keys($currencies) as $currency)
                                        @php
                                            $currencyTransactions = $transactions->where('currency', $currency);
                                            $currencyTotal = $currencyTransactions->sum('amount');
                                            $currencyCount = $currencyTransactions->count();
                                            $currencyDirection = $currencyTransactions->first()->direction ?? 'in';
                                        @endphp
                                        <div class="col-md-4">
                                            <div class="border rounded p-2 mb-2">
                                                <strong class="d-block">{{ $currency }}</strong>
                                                <small>
                                                    {{ $currencyCount }} transaction(s)<br>
                                                    Total: {{ formatCurrency($currencyTotal, $currency) }}
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No transactions found for this period.
                        </div>
                    @endif

                    <!-- Footer Note -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <div class="row">
                            <div class="col-md-8">
                                <i class="fas fa-info-circle me-1 text-primary"></i>
                                <span class="text-muted small">
                                    This statement was generated on {{ $statement->generated_at ? $statement->generated_at->format('F d, Y H:i:s') : 'N/A' }}.
                                    @if($statement->sent_at)
                                        <br>It was sent to you on {{ $statement->sent_at->format('F d, Y H:i:s') }}.
                                    @endif
                                </span>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="badge bg-{{ $statement->status === 'paid' ? 'success' : ($statement->status === 'overdue' ? 'danger' : 'secondary') }}">
                                    Status: {{ ucfirst($statement->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    @if($statement->closing_balance > 0)
                        <div class="mt-3 p-3 bg-warning bg-opacity-10 border border-warning rounded">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle text-warning me-3 fa-2x"></i>
                                <div>
                                    <strong>Payment Required:</strong><br>
                                    Please pay the outstanding balance of
                                    <strong class="text-danger">{{ formatCurrency($statement->closing_balance, $primaryCurrency) }}</strong>
                                    before the due date.
                                </div>
                            </div>
                        </div>
                    @elseif($statement->closing_balance < 0)
                        <div class="mt-3 p-3 bg-success bg-opacity-10 border border-success rounded">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-3 fa-2x"></i>
                                <div>
                                    <strong>Credit Balance:</strong><br>
                                    Your account has a credit balance of
                                    <strong class="text-success">{{ formatCurrency(abs($statement->closing_balance), $primaryCurrency) }}</strong>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
    }
    .table td {
        vertical-align: middle;
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .text-success {
        color: #28a745 !important;
        font-weight: 500;
    }
    .text-danger {
        color: #dc3545 !important;
        font-weight: 500;
    }
    .bg-primary {
        background-color: #007bff !important;
    }
    .bg-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }
    .border-warning {
        border-color: #ffc107 !important;
    }
    .border-success {
        border-color: #28a745 !important;
    }
</style>
@endpush
