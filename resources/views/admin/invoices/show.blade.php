@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Invoice {{ $invoice->invoice_number }}</h3>
                    <div class="btn-group">
                        <a href="{{ route('admin.invoices.download', $invoice->id) }}" class="btn btn-primary">
                            Download PDF
                        </a>
                        @if($invoice->status !== 'sent')
                        <a href="{{ route('admin.invoices.send', $invoice->id) }}" class="btn btn-success">
                            Send Invoice
                        </a>
                        @endif
                        <a href="{{ route('admin.leases.show', $invoice->lease_id) }}" class="btn btn-secondary">
                            Back to Lease
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Add invoice details display similar to PDF but with Bootstrap styling -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
