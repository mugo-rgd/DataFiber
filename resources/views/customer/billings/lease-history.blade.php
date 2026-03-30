@extends('layouts.app')

@section('title', 'Lease Billing History')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-history me-2"></i>Billing History - {{ $lease->lease_number }}
        </h1>
        <a href="{{ route('customer.leases.show', $lease->id) }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Lease
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Billing History</h6>
        </div>
        <div class="card-body">
            @if($lineItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Billing #</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lineItems as $item)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->period_start)->format('M Y') }} -
                                    {{ \Carbon\Carbon::parse($item->period_end)->format('M Y') }}
                                </td>
                                <td>
                                    <a href="{{ route('customer.billings.show', $item->consolidatedBilling->id) }}">
                                        {{ $item->consolidatedBilling->billing_number }}
                                    </a>
                                </td>
                                <td>{{ number_format($item->amount, 2) }} {{ $item->currency }}</td>
                                <td>
                                    @php
                                        $statusClass = match($item->consolidatedBilling->status) {
                                            'paid' => 'success',
                                            'pending' => 'warning',
                                            'overdue' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ ucfirst($item->consolidatedBilling->status) }}
                                    </span>
                                </td>
                                <td>{{ $item->consolidatedBilling->created_at->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $lineItems->links() }}
            @else
                <p class="text-center py-4">No billing history found for this lease.</p>
            @endif
        </div>
    </div>
</div>
@endsection
