@extends('layouts.app')

@section('title', 'Payment Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Payment Management</h4>
                    <div class="card-tools">
                        <form action="{{ route('finance.debt.payments') }}" method="GET" class="form-inline">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control" placeholder="Search billing number or customer..." value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form action="{{ route('finance.debt.payments') }}" method="GET">
                                <div class="row">
                                    <div class="col-md-3">
                                        <select name="status" class="form-control" onchange="this.form.submit()">
                                            <option value="">All Status</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From Date">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To Date">
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('finance.debt.payments') }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Payments Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <!-- In the table, add these columns -->
<thead>
    <tr>
        <th>Billing #</th>
        <th>Customer</th>
        <th>Billing Date</th>
        <th>Due Date</th>
         <th class="text-end">Rate</th>
        <th class="text-end">Total (USD)</th>
        <th class="text-end">Paid (USD)</th>
        <th class="text-end">Balance (USD)</th>
        <th>% Paid</th>
        <th>Status</th>
        <th>Payment Date</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    @foreach($payments as $payment)
    @php
        // Calculate directly in the view
        $totalUSD = $payment->total_amount ?? $payment->total_amount;
        $paidUSD = $payment->paid_amount ?? $payment->paid_amount ?? 0;
        $balanceUSD = $totalUSD - $paidUSD;
        $paymentPercentage = $totalUSD > 0 ? ($paidUSD / $totalUSD) * 100 : 0;
    @endphp
    <tr>
        <td>{{ $payment->billing_number }}</td>
        <td>{{ $payment->user->name ?? 'N/A' }}</td>
        <td>{{ $payment->billing_date->format('d/m/Y') }}</td>
        <td>{{ $payment->due_date->format('d/m/Y') }}</td>
        <td class="text-end">{{ $payment->exchange_rate }}</td>
        <td class="text-end">$ {{ number_format($totalUSD, 2) }}</td>
        <td class="text-end">$ {{ number_format($paidUSD, 2) }}</td>
        <td class="text-end">$ {{ number_format($balanceUSD, 2) }}</td>
        <td>
            <div class="progress" style="height: 20px;">
                <div class="progress-bar {{ $paymentPercentage >= 100 ? 'bg-success' : ($paymentPercentage > 0 ? 'bg-warning' : 'bg-secondary') }}"
                     role="progressbar"
                     style="width: {{ min(100, $paymentPercentage) }}%;">
                    {{ number_format($paymentPercentage, 1) }}%
                </div>
            </div>
        </td>
        <td>
            <span class="badge bg-{{ $payment->status == 'paid' ? 'success' : ($payment->status == 'overdue' ? 'danger' : ($payment->status == 'pending' ? 'warning' : 'secondary')) }}">
                {{ ucfirst($payment->status) }}
            </span>
        </td>
        <td>{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y H:i') : 'N/A' }}</td>
        <td>
            <div class="btn-group">
                <a href="{{ route('finance.debt.payments.edit', $payment) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @if($payment->kra_status != 'verified')
                <form action="{{ route('finance.debt.payments.verify', $payment) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark this payment as verified?')">
                        <i class="fas fa-check"></i> Verify
                    </button>
                </form>
                @endif
            </div>
        </td>
    </tr>
    @endforeach
</tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-submit date filters when dates are selected
    document.querySelectorAll('input[type="date"]').forEach(input => {
        input.addEventListener('change', function() {
            if(this.value) {
                this.form.submit();
            }
        });
    });
</script>
@endsection
