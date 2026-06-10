@extends('layouts.app')

@section('title', 'Dashboard - Dark Fibre CRM')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Dashboard Loading Issue</h4>
                </div>
                <div class="card-body text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-4x text-warning mb-4"></i>
                    <h4>Something went wrong</h4>
                    <p class="text-muted">{{ $error ?? 'Unable to load dashboard. Please try again later.' }}</p>
                    @if(config('app.debug') && isset($message))
                        <p class="text-danger small">{{ $message }}</p>
                    @endif
                    <div class="mt-4">
                        <a href="{{ route('customer.profile.show') }}" class="btn btn-primary mx-2">
                            <i class="fas fa-user me-2"></i>My Profile
                        </a>
                        <a href="{{ route('customer.design-requests.create') }}" class="btn btn-success mx-2">
                            <i class="fas fa-plus me-2"></i>New Request
                        </a>
                        <button onclick="location.reload()" class="btn btn-outline-secondary mx-2">
                            <i class="fas fa-sync-alt me-2"></i>Retry
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
