@extends('layouts.help')

@section('help-content')
<div class="card shadow-sm">
    <div class="card-header bg-danger text-white">
        <h4 class="mb-0">
            <i class="fas fa-video me-2"></i>
            Video Tutorials
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Watch these video tutorials to learn how to use DarkFibre CRM effectively.
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-play-circle fa-4x text-kp-blue mb-3"></i>
                        <h5>Getting Started</h5>
                        <p>Learn the basics of logging in and navigating the dashboard.</p>
                        <small class="text-muted">Duration: 5 minutes</small>
                        <div class="mt-2">
                            <span class="badge bg-secondary">Coming Soon</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-play-circle fa-4x text-kp-green mb-3"></i>
                        <h5>CAK Compliance Returns</h5>
                        <p>How to submit ASP, CSP, and NFP compliance returns.</p>
                        <small class="text-muted">Duration: 10 minutes</small>
                        <div class="mt-2">
                            <span class="badge bg-secondary">Coming Soon</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-play-circle fa-4x text-kp-yellow mb-3"></i>
                        <h5>Network Map Feature</h5>
                        <p>How to set and view network facility locations on the map.</p>
                        <small class="text-muted">Duration: 3 minutes</small>
                        <div class="mt-2">
                            <span class="badge bg-secondary">Coming Soon</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-play-circle fa-4x text-info mb-3"></i>
                        <h5>Exporting Data</h5>
                        <p>How to export compliance data to Excel, CSV, and PDF.</p>
                        <small class="text-muted">Duration: 4 minutes</small>
                        <div class="mt-2">
                            <span class="badge bg-secondary">Coming Soon</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-secondary mt-3">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Request a Tutorial:</strong> If you need help with a specific feature, email <a href="mailto:training@darkfibre.co.ke">training@darkfibre.co.ke</a> to request a custom tutorial.
        </div>

    </div>
</div>
@endsection
