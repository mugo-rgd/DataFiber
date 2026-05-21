@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-kp-blue text-white">
            <h4 class="mb-0">
                <i class="fas fa-download me-2"></i>
                Export Data Module
            </h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                The Export module is currently under development. Please check back later.
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-file-excel fa-3x text-kp-green mb-2"></i>
                            <h5>Excel Export</h5>
                            <p class="small">Coming soon</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-file-csv fa-3x text-kp-blue mb-2"></i>
                            <h5>CSV Export</h5>
                            <p class="small">Coming soon</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                            <h5>PDF Export</h5>
                            <p class="small">Coming soon</p>
                        </div>
                    </div>
                </div>
            </div>

            <a href="{{ url('/') }}" class="btn btn-kp-primary">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
