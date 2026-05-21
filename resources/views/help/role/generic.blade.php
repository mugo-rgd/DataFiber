@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0">
            <i class="fas fa-question-circle me-2"></i>
            {{ $pageTitle ?? ucfirst($role) . ' Guide' }}
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>{{ $roleDisplayName }} Help Guide</strong>
            <p class="mb-0 mt-2">This guide provides essential information for your role in DarkFibre CRM.</p>
        </div>

        <h3>Quick Tips for Your Role</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul>
                    @foreach($quickTips as $tip)
                        <li><i class="fas fa-check-circle text-kp-green me-2"></i> {{ $tip }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <h3>Need More Help?</h3>
        <div class="row">
            <div class="col-md-6">
                <a href="{{ route('help.faq') }}" class="btn btn-outline-kp-primary w-100 mb-2">
                    <i class="fas fa-question-circle"></i> View FAQ
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('help.contact') }}" class="btn btn-outline-kp-success w-100 mb-2">
                    <i class="fas fa-headset"></i> Contact Support
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
