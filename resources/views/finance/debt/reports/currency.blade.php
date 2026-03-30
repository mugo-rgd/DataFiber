@extends('layouts.app')

@section('title', 'Currency Analysis Report')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-exchange-alt text-primary me-2"></i>Currency Analysis Report
        </h1>
        <a href="{{ route('finance.debt.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <!-- Currency Summary Cards -->
    <div class="row mb-4">
        @foreach($currencySummary as $summary)
        <div class="col-md-6 mb-3">
            <div class="card shadow h-100 border-left-{{ $summary->currency == 'USD' ? 'primary' : 'success' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            @if($summary->currency == 'USD')
                                <i class="fas fa-dollar-sign text-primary"></i> US Dollar (USD)
                            @else
                                <i class="fas fa-shilling-sign text-success"></i> Kenyan Shilling (KSH)
                            @endif
                        </h5>
                        <span class="badge bg-{{ $summary->currency == 'USD' ? 'primary' : 'success' }} fs-6">
                            {{ $summary->invoice_count }} Invoices
                        </span>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <small class="text-muted d-block">Total Billed</small>
                                <strong class="h5">
                                    @if($summary->currency == 'USD')
                                        ${{ number_format($summary->total_billed, 2) }}
                                    @else
                                        KSH {{ number_format($summary->total_billed, 2) }}
                                    @endif
                                </strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Total Paid</small>
                                <strong class="h5 text-success">
                                    @if($summary->currency == 'USD')
                                        ${{ number_format($summary->total_paid, 2) }}
                                    @else
                                        KSH {{ number_format($summary->total_paid, 2) }}
                                    @endif
                                </strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <small class="text-muted d-block">Outstanding</small>
                                <strong class="h5 text-danger">
                                    @if($summary->currency == 'USD')
                                        ${{ number_format($summary->outstanding, 2) }}
                                    @else
                                        KSH {{ number_format($summary->outstanding, 2) }}
                                    @endif
                                </strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Collection Rate</small>
                                @php
                                    $collectionRate = $summary->total_billed > 0 ?
                                        ($summary->total_paid / $summary->total_billed) * 100 : 0;
                                @endphp
                                <div class="progress mb-1" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $collectionRate }}%"></div>
                                </div>
                                <small class="text-muted">{{ number_format($collectionRate, 1) }}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
