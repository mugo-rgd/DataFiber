@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-exchange-alt me-2"></i>Transactions
                </h1>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-tools fa-4x text-muted mb-3"></i>
            <h3 class="text-muted">Transactions Module</h3>
            <p class="text-muted">This feature is currently under development.</p>
            <a href="{{ route('finance.billing.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Billings
            </a>
        </div>
    </div>
</div>
@endsection
