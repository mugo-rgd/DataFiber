{{-- resources/views/admin/survey-requests/error.blade.php --}}
@extends('layouts.app')

@section('title', 'Error - Survey Requests')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>System Error
                    </h4>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-bug fa-5x text-danger mb-4"></i>
                        <h3 class="text-danger">Something Went Wrong</h3>
                        <p class="lead text-muted">
                            We encountered an error while loading the survey requests.
                        </p>
                    </div>

                    <div class="alert alert-danger text-start">
                        <h5 class="alert-heading">Error Details:</h5>
                        <p class="mb-0">{{ $errorMessage }}</p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary me-3">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                        <a href="{{ route('admin.survey-requests') }}" class="btn btn-primary">
                            <i class="fas fa-redo me-2"></i>Try Again
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
