@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-kp-blue text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i> Help Center
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('help.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('help.index') ? 'active' : '' }}">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </a>
                    <a href="{{ route('help.getting-started') }}" class="list-group-item list-group-item-action {{ request()->routeIs('help.getting-started') ? 'active' : '' }}">
                        <i class="fas fa-rocket me-2"></i> Getting Started
                    </a>

                    <div class="list-group-item list-group-item-light fw-bold">
                        <i class="fas fa-file-alt me-2"></i> Compliance Guides
                    </div>
                    <a href="{{ route('help.cak-compliance-guide') }}" class="list-group-item list-group-item-action">
    <i class="fas fa-file-alt me-2"></i> CAK Forms Guide
</a>
                    <a href="{{ route('help.asp') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-server me-2"></i> ASP Guide
                    </a>
                    <a href="{{ route('help.csp') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-envelope me-2"></i> CSP Guide
                    </a>
                    <a href="{{ route('help.nfp') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-network-wired me-2"></i> NFP Guide
                    </a>
                    <a href="{{ route('help.export') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-download me-2"></i> Export Guide
                    </a>

                    <div class="list-group-item list-group-item-light fw-bold">
                        <i class="fas fa-user-circle me-2"></i> Profile Management
                    </div>
                    <a href="{{ route('help.profile.index') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-user-edit me-2"></i> Profile Information
                    </a>
                    <a href="{{ route('help.profile.security') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-lock me-2"></i> Password & Security
                    </a>
                    <a href="{{ route('help.profile.notifications') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-bell me-2"></i> Notifications
                    </a>
                    <a href="{{ route('help.profile.activity') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-history me-2"></i> Activity Log
                    </a>

                    <div class="list-group-item list-group-item-light fw-bold">
                        <i class="fas fa-question-circle me-2"></i> Support
                    </div>
                    <a href="{{ route('help.faq') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-question me-2"></i> FAQ
                    </a>
                    <a href="{{ route('help.contact') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-headset me-2"></i> Contact Support
                    </a>
                    <a href="{{ route('help.video-tutorials') }}" class="list-group-item list-group-item-action ps-4">
                        <i class="fas fa-video me-2"></i> Video Tutorials
                    </a>
                </div>
            </div>

            <!-- Support Contact Card -->
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-headset fa-2x text-kp-blue mb-2"></i>
                    <h6>Need More Help?</h6>
                    <p class="small text-muted">Contact our support team</p>
                    <a href="mailto:support@darkfibre.co.ke" class="btn btn-sm btn-outline-kp-primary w-100 mb-1">
                        <i class="fas fa-envelope"></i> Email Support
                    </a>
                    <a href="tel:+254203201000" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fas fa-phone"></i> Call: 020 3201 000
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-book-open text-kp-blue me-2"></i>
                        <strong>Help Center</strong>
                    </div>
                    <div>
                        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    @yield('help-content')
                </div>

                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Last updated: {{ date('F Y') }}
                        </small>
                        <div>
                            <small class="text-muted me-2">Was this page helpful?</small>
                            <button class="btn btn-sm btn-outline-kp-success feedback-btn" data-helpful="1">
                                <i class="fas fa-thumbs-up"></i> Yes
                            </button>
                            <button class="btn btn-sm btn-outline-danger feedback-btn" data-helpful="0">
                                <i class="fas fa-thumbs-down"></i> No
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Feedback buttons
    document.querySelectorAll('.feedback-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const helpful = this.dataset.helpful;
            const page = window.location.pathname;

            fetch('{{ route("help.feedback") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    page: page,
                    helpful: helpful
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Thank you for your feedback!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});
</script>
@endpush

@push('styles')
<style>
    .list-group-item.active {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .list-group-item-action:hover {
        background-color: #f8f9fa;
    }

    @media print {
        .btn, .dropdown, .list-group-item-action, .card-footer, .support-card {
            display: none !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .container-fluid {
            padding: 0 !important;
        }
    }
</style>
@endpush
@endsection
