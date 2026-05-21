@extends('layouts.help')

@section('help-content')
<h1><i class="fas fa-network-wired text-kp-yellow me-2"></i> NFP Compliance Guide</h1>
<hr>

<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Network Facility Provider (NFP)</strong> - Guide for submitting NFP compliance returns.
</div>

<p>This guide is being prepared. Please check back later for detailed documentation.</p>

<h3>Quick Links</h3>
<ul>
    <li><a href="{{ route('nfp.index') }}">View NFP Returns</a></li>
    <li><a href="{{ route('nfp.create') }}">Create New NFP Return</a></li>
    <li><a href="{{ route('help.faq') }}">View FAQ</a></li>
</ul>
@endsection
