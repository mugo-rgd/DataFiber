{{-- resources/views/admin/asp-list.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>ASP Compliance Returns</h2>
        <div>
            <a href="{{ route('asp.create') }}" class="btn btn-primary">+ New ASP Return</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="alert alert-info">
                <h5>ASP Compliance Module</h5>
                <p>Submit your Application Service Provider compliance returns here.</p>
                <a href="{{ route('asp.create') }}" class="btn btn-primary">Create New ASP Return</a>
            </div>
        </div>
    </div>
</div>
@endsection
