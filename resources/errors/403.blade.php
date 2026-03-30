@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1>403 - Unauthorized</h1>
            <p>You don't have permission to access this page.</p>
            <p>Your role: {{ auth()->user()->role ?? 'Not logged in' }}</p>
            <a href="{{ url('/') }}" class="btn btn-primary">Return Home</a>
        </div>
    </div>
</div>
@endsection
