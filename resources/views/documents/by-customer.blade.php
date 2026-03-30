@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4>Documents for Customer: {{ $customer->company_name ?? $customer->name }}</h4>
                    <p class="mb-0">
                        <strong>Email:</strong> {{ $customer->email }} |
                        <strong>Phone:</strong> {{ $customer->phone }} |
                        <strong>Total Requests:</strong> {{ $designRequests->total() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @include('documents.partials.requests-table')
</div>
@endsection
