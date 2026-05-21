{{-- resources/views/admin/csp-list.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>CSP Compliance Returns</h2>
        <div>
            <a href="{{ route('csp.create') }}" class="btn btn-kp-primary">+ New CSP Return</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-kp-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="alert alert-info">
                <h5>CSP Compliance Module</h5>
                <p>Submit your Content Service Provider compliance returns here.</p>
                <a href="{{ route('csp.create') }}" class="btn btn-kp-primary">Create New CSP Return</a>
            </div>
        </div>
    </div>
</div>
@endsection
