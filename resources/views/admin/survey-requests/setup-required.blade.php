{{-- resources/views/admin/survey-requests/setup-required.blade.php --}}
@extends('layouts.app')

@section('title', 'Setup Required - Survey Requests')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-tools me-2"></i>Database Setup Required
                    </h4>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-database fa-5x text-warning mb-4"></i>
                        <h3 class="text-warning">Survey System Not Configured</h3>
                        <p class="lead text-muted">
                            The survey management system requires additional database tables and columns to be set up.
                        </p>
                    </div>

                    <div class="alert alert-info text-start">
                        <h5 class="alert-heading">What needs to be done:</h5>
                        <ul class="mb-0">
                            <li>Create survey-related database columns</li>
                            <li>Set up surveyors table</li>
                            <li>Create survey assignments table</li>
                        </ul>
                    </div>

                    <div class="mt-4">
                        <h5>Quick Setup Instructions:</h5>
                        <div class="bg-light p-4 rounded text-start">
                            <code class="d-block mb-3">
                                # Run database migrations<br>
                                php artisan migrate
                            </code>
                            <small class="text-muted">
                                This will create all the necessary database tables and columns automatically.
                            </small>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary me-3">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                        <a href="{{ route('admin.survey-requests') }}" class="btn btn-primary">
                            <i class="fas fa-redo me-2"></i>Retry After Setup
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
