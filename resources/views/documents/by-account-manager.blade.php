@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4>Documents for Account Manager: {{ $accountManager->name }}</h4>
                    <p class="mb-0">
                        <strong>Email:</strong> {{ $accountManager->email }} |
                        <strong>Phone:</strong> {{ $accountManager->phone }} |
                        <strong>Total Assigned Requests:</strong> {{ $designRequests->total() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @include('documents.partials.requests-table')
</div>
@endsection
