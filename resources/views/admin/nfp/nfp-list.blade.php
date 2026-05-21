{{-- resources/views/admin/nfp-list.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>NFP Compliance Returns</h2>
        <div>
            <a href="{{ route('nfp.create') }}" class="btn btn-kp-primary">+ New NFP Return</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-kp-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="alert alert-info">
                <h5>NFP Compliance Module</h5>
                <p>Submit your Numbering Framework Provider compliance returns here.</p>
                <a href="{{ route('nfp.create') }}" class="btn btn-kp-primary">Create New NFP Return</a>
            </div>
        </div>
    </div>
</div>
@endsection
