{{-- resources/views/customer-portal/statements/preview.blade.php --}}
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

<div class="card">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-file-invoice me-2"></i>Statement Preview
        </h5>
        <div>
            @if(count($currencies) > 1)
                <span class="badge bg-warning text-dark me-2">Multi-Currency</span>
            @endif
            <span class="badge bg-light text-dark">Generated: {{ now()->format('d M Y H:i') }}</span>
        </div>
    </div>
    <div class="card-body">
        <!-- Company Header -->
        <div class="text-center mb-4">
            <h3>{{ config('app.name') }}</h3>
            <p class="text-muted">Customer Statement of Account</p>
        </div>

        <!-- Statement Info -->
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="120"><strong>Statement #:</strong></td>
                        <td>{{ $statementNumber }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date Range:</strong></td>
                        <td>{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</td>
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
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="120"><strong>Customer:</strong></td>
                        <td>{{ $user->name }}</td>
                    </tr>
                    @if($user->company_name)
                    <tr>
                        <td><strong>Company:</strong></td>
                        <td>{{ $user->company_name }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Summary Box -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <label class="text-muted">Opening Balance</label>
                                    <h4 class="{{ getAmountClass($openingBalance) }}">
                                        {{ formatCurrency($openingBalance, $primaryCurrency) }}
                                    </h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <label class="text-muted">Total Debits</label>
                                    <h4 class="text-danger">
                                        {{ formatCurrency($totalDebits, $primaryCurrency) }}
                                    </h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <label class="text-muted">Total Credits</label>
                                    <h4 class="text-success">
                                        {{ formatCurrency($totalCredits, $primaryCurrency) }}
                                    </h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <label class="text-muted">Closing Balance</label>
                                    <h4 class="{{ getAmountClass($closingBalance) }}">
                                        {{ formatCurrency($closingBalance, $primaryCurrency) }}
                                    </h4>
                                    @if(count($currencies) > 1)
                                        <small class="text-muted">(Converted to {{ $primaryCurrency }})</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        @if($transactions->count() > 0)
            <h5 class="mb-3">Transaction Details</h5>
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
                        <tr class="table-secondary">
                            <td colspan="6" class="text-end"><strong>Opening Balance:</strong></td>
                            <td class="text-end">
                                <strong>{{ formatCurrency($openingBalance, $primaryCurrency) }}</strong>
                                @if(count($currencies) > 1)
                                    <br><small class="text-muted">({{ $primaryCurrency }})</small>
                                @endif
                            </td>
                        </tr>

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

                        <tr class="table-primary fw-bold">
                            <td colspan="6" class="text-end"><strong>Closing Balance:</strong></td>
                            <td class="text-end">
                                <strong class="{{ getAmountClass($closingBalance) }}">
                                    {{ formatCurrency($closingBalance, $primaryCurrency) }}
                                </strong>
                                @if(count($currencies) > 1)
                                    <br><small class="text-muted">({{ $primaryCurrency }} equivalent)</small>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Currency Summary -->
            @if(count($currencies) > 1)
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Multi-Currency Statement:</strong>
                    This statement contains transactions in multiple currencies.
                    The summary totals are shown in {{ $primaryCurrency }} for reference.
                    <hr>
                    <div class="row">
                        @foreach(array_keys($currencies) as $currency)
                            @php
                                $currencyTotal = $transactions->where('currency', $currency)->sum('amount');
                                $currencyCount = $transactions->where('currency', $currency)->count();
                            @endphp
                            <div class="col-md-4">
                                <strong>{{ $currency }}:</strong>
                                {{ $currencyCount }} transaction(s) -
                                {{ formatCurrency($currencyTotal, $currency) }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No transactions found for the selected period.
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="mt-4 d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Print
            </button>
            <button type="button" class="btn btn-success" id="downloadFromPreview">
                <i class="fas fa-download me-1"></i>Download PDF
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
    .badge {
        font-size: 0.8rem;
        padding: 0.3rem 0.5rem;
    }
    .table td {
        vertical-align: middle;
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
    }
</style>
@endpush

@push('scripts')
<script>
document.getElementById('downloadFromPreview')?.addEventListener('click', function() {
    const startDate = '{{ $startDate->format('Y-m-d') }}';
    const endDate = '{{ $endDate->format('Y-m-d') }}';

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("customer.statements.download") }}';

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    const start = document.createElement('input');
    start.type = 'hidden';
    start.name = 'start_date';
    start.value = startDate;
    form.appendChild(start);

    const end = document.createElement('input');
    end.type = 'hidden';
    end.name = 'end_date';
    end.value = endDate;
    form.appendChild(end);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
});
</script>
@endpush
