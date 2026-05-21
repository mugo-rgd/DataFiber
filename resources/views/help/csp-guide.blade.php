@extends('layouts.help')

@section('help-content')
<h1><i class="fas fa-envelope text-kp-green me-2"></i> CSP Compliance Guide</h1>
<hr>

<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Content Service Provider (CSP)</strong> - Guide for submitting CSP compliance returns.
</div>

<p>This guide is being prepared. Please check back later for detailed documentation.</p>

<h3>Quick Links</h3>
<ul>
    <li><a href="{{ route('csp.index') }}">View CSP Returns</a></li>
    <li><a href="{{ route('csp.create') }}">Create New CSP Return</a></li>
    <li><a href="{{ route('help.faq') }}">View FAQ</a></li>
</ul>
@endsection
