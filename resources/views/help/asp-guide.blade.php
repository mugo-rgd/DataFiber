@extends('layouts.help')

@section('help-content')
<h1><i class="fas fa-server text-kp-blue me-2"></i> ASP Compliance Guide</h1>
<hr>

<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Application Service Provider (ASP)</strong> - Guide for submitting ASP compliance returns.
</div>

<p>This guide is being prepared. Please check back later for detailed documentation.</p>

<h3>Quick Links</h3>
<ul>
    <li><a href="{{ route('asp.index') }}">View ASP Returns</a></li>
    <li><a href="{{ route('asp.create') }}">Create New ASP Return</a></li>
    <li><a href="{{ route('help.faq') }}">View FAQ</a></li>
</ul>
@endsection
