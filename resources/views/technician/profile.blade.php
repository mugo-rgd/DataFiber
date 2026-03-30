{{-- resources/views/technician/profile.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Technician Profile
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-circle fa-4x text-primary mb-3"></i>
                        <h4>{{ Auth::user()->name }}</h4>
                        <p class="text-muted">Technician</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Email:</strong><br>
                                {{ Auth::user()->email }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Member Since:</strong><br>
                                {{ Auth::user()->created_at->format('F j, Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Profile management features coming soon.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
