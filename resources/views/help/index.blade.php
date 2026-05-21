@extends('layouts.help')

@section('help-content')
<div class="text-center mb-5">
    <i class="fas fa-question-circle fa-4x text-kp-blue mb-3"></i>
    <h2>Welcome to DarkFibre Help Center</h2>
    <p class="lead">Everything you need to know about managing CAK compliance returns</p>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-rocket fa-3x text-kp-blue mb-3"></i>
                <h5>Getting Started</h5>
                <p class="small">New to DarkFibre? Learn the basics of logging in and navigating the dashboard.</p>
                <a href="{{ route('help.getting-started') }}" class="btn btn-sm btn-outline-kp-primary">Learn More</a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-file-alt fa-3x text-kp-green mb-3"></i>
                <h5>Compliance Guides</h5>
                <p class="small">Step-by-step instructions for ASP, CSP, and NFP returns.</p>
                <a href="{{ route('help.asp') }}" class="btn btn-sm btn-outline-kp-success">View Guides</a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-download fa-3x text-info mb-3"></i>
                <h5>Export Data</h5>
                <p class="small">Learn how to export compliance data to Excel, CSV, or PDF.</p>
                <a href="{{ route('help.export') }}" class="btn btn-sm btn-outline-info">Export Guide</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header bg-kp-yellow text-dark">
                <i class="fas fa-clock me-2"></i> Quick Tips
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Quarterly returns are due within 15 days after each quarter ends</li>
                    <li>Use the "Save Draft" button if you need to complete the form later</li>
                    <li>For NFP returns, use the map picker to set your network location</li>
                    <li>Export your data regularly for backup purposes</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <i class="fas fa-exclamation-triangle me-2"></i> Common Issues
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>500 Error? Clear your browser cache or try a different browser</li>
                    <li>Can't upload? Ensure file is under 5MB (PNG, JPG, or PDF)</li>
                    <li>Map not working? Use HTTPS or localhost for development</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-kp-blue text-white">
                <i class="fas fa-headset me-2"></i> Need Additional Help?
            </div>
            <div class="card-body text-center">
                <p>Can't find what you're looking for? Our support team is ready to assist you.</p>
                <a href="{{ route('help.contact') }}" class="btn btn-kp-primary">
                    <i class="fas fa-envelope"></i> Contact Support
                </a>
                <a href="{{ route('help.faq') }}" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-question-circle"></i> View FAQ
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
