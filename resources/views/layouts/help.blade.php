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
                        <i class="fas fa-envelope me-1"></i> Email Support
                    </a>
                    <a href="tel:+254203201000" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fas fa-phone me-1"></i> Call: 020 3201 000
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <i class="fas fa-book-open text-kp-blue me-2"></i>
                        <strong>Help Center</strong>
                    </div>
                    <div>
                        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    @yield('help-content')
                </div>

                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Last updated: {{ date('F Y') }}
                        </small>
                        <div class="d-flex align-items-center gap-2">
                            <small class="text-muted">Was this page helpful?</small>
                            <button class="btn btn-sm btn-outline-kp-success feedback-btn" data-helpful="1">
                                <i class="fas fa-thumbs-up me-1"></i> Yes
                            </button>
                            <button class="btn btn-sm btn-outline-danger feedback-btn" data-helpful="0">
                                <i class="fas fa-thumbs-down me-1"></i> No
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
    /* ========================================
       COLORFUL ICONS - HELP CENTER
       All text is visible on all devices
    ========================================= */

    /* Colorful Icons - Different colors for different categories */
    .list-group-item i.fa-home {
        color: #00d2ff !important;
    }

    .list-group-item i.fa-rocket {
        color: #ff6b6b !important;
    }

    .list-group-item i.fa-file-alt {
        color: #FFD700 !important;
    }

    .list-group-item i.fa-server {
        color: #9b59b6 !important;
    }

    .list-group-item i.fa-envelope {
        color: #3498db !important;
    }

    .list-group-item i.fa-network-wired {
        color: #1abc9c !important;
    }

    .list-group-item i.fa-download {
        color: #2ecc71 !important;
    }

    .list-group-item i.fa-user-circle,
    .list-group-item i.fa-user-edit {
        color: #1abc9c !important;
    }

    .list-group-item i.fa-lock {
        color: #e67e22 !important;
    }

    .list-group-item i.fa-bell {
        color: #f1c40f !important;
    }

    .list-group-item i.fa-history {
        color: #95a5a6 !important;
    }

    .list-group-item i.fa-question-circle,
    .list-group-item i.fa-question {
        color: #9b59b6 !important;
    }

    .list-group-item i.fa-headset {
        color: #fd79a8 !important;
    }

    .list-group-item i.fa-video {
        color: #e74c3c !important;
    }

    /* Icon sizing and spacing */
    .list-group-item i {
        width: 1.6rem;
        font-size: 1.1rem;
        transition: all 0.25s ease;
        flex-shrink: 0;
    }

    /* Hover effect - icons scale and change color */
    .list-group-item:hover i {
        transform: scale(1.15);
        filter: brightness(1.2);
    }

    /* Active state icons */
    .list-group-item.active i {
        color: white !important;
    }

    /* Active state background */
    .list-group-item.active {
        background: linear-gradient(135deg, var(--kp-blue, #0066B3), var(--kp-green, #009639));
        border-color: transparent;
        color: white;
    }

    /* Hover effects for list items */
    .list-group-item-action {
        transition: all 0.25s ease;
    }

    .list-group-item-action:hover {
        background-color: var(--kp-light-blue, #e8f4fd);
        transform: translateX(5px);
    }

    /* Section headers styling */
    .list-group-item-light {
        background-color: #f8f9fa;
        color: #2c3e50;
        font-weight: 600;
        border-top: 1px solid #e9ecef;
        margin-top: 4px;
    }

    .list-group-item-light i {
        color: var(--kp-blue, #0066B3) !important;
    }

    /* Support card styling */
    .support-card .card-body i {
        font-size: 2rem;
    }

    /* Button icon spacing */
    .btn i {
        margin-right: 0.25rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .list-group-item {
            padding: 0.7rem 1rem;
        }

        .list-group-item i {
            font-size: 1rem;
            width: 1.5rem;
        }

        .card-header {
            flex-direction: column;
            text-align: center;
        }

        .card-footer .d-flex {
            flex-direction: column;
            text-align: center;
            gap: 0.75rem !important;
        }
    }

    /* Print styles */
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

        .list-group-item {
            border: none;
            padding: 0.25rem 0;
        }
    }

    /* Ensure text is always visible on all devices */
    .menu-text {
        display: inline !important;
    }

    /* Fix for any hidden text issues */
    .list-group-item span,
    .card-header strong,
    .card-body p,
    .card-footer small,
    .btn span,
    h6,
    p {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
</style>
@endpush
@endsection
