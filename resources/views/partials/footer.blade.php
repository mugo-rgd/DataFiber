<footer class="footer-compact bg-dark text-light py-3 py-sm-4 mt-auto">
    <div class="container-fluid px-3 px-sm-4">
        <div class="row align-items-center g-2 g-sm-3">
            <div class="col-lg-4 mb-2 mb-lg-0">
                <div class="footer-brand d-flex align-items-center mb-2">
                    <div class="brand-icon me-2">
                        <i class="fas fa-network-wired fa-lg" style="color: var(--kp-yellow);"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold" style="color: var(--kp-yellow);">Dark Fibre CRM</h5>
                        <p class="mb-0 text-light opacity-75 small">Kenya Power Fibre Infrastructure Management</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 mb-2 mb-lg-0">
                <div class="row g-2">
                    <div class="col-6 col-sm-3">
                        <h6 class="footer-heading mb-1 small fw-bold" style="color: var(--kp-yellow);">Quick Links</h6>
                        <ul class="list-unstyled footer-links mb-0">
                            <li class="mb-1"><a href="{{ url('/') }}" class="footer-link small">Home</a></li>
                            @if(Route::has('help.index'))
                                <li class="mb-1"><a href="{{ route('help.index') }}" class="footer-link small">Help Center</a></li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-6 col-sm-3">
                        <h6 class="footer-heading mb-1 small fw-bold" style="color: var(--kp-yellow);">Legal</h6>
                        <ul class="list-unstyled footer-links mb-0">
                            <li class="mb-1"><a href="#" class="footer-link small">Privacy Policy</a></li>
                            <li class="mb-1"><a href="#" class="footer-link small">Terms of Service</a></li>
                        </ul>
                    </div>
                    <div class="col-12 col-sm-6">
                        <h6 class="footer-heading mb-1 small fw-bold" style="color: var(--kp-yellow);">Contact</h6>
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-1 d-flex align-items-start">
                                <i class="fas fa-map-marker-alt fa-xs me-1 mt-1" style="color: var(--kp-yellow);"></i>
                                <span class="text-light opacity-75">Nairobi, Kenya</span>
                            </li>
                            <li class="mb-1 d-flex align-items-start">
                                <i class="fas fa-envelope fa-xs me-1 mt-1" style="color: var(--kp-yellow);"></i>
                                <span class="text-light opacity-75">Fibre@kplc.co.ke</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="system-status d-flex align-items-center justify-content-lg-end mb-2">
                    <div class="status-indicator me-2">
                        <div class="status-dot bg-kp-green"></div>
                    </div>
                    <span class="text-kp-green fw-bold small">System Operational</span>
                </div>
                <div class="footer-meta d-flex flex-wrap justify-content-lg-end gap-1 small">
                    <span class="badge px-2 py-1" style="background: linear-gradient(135deg, var(--kp-blue), var(--kp-green)); color: white;">
                        v{{ config('app.version', '1.0.0') }}
                    </span>
                    @if(app()->environment('local'))
                        <span class="badge px-2 py-1" style="background: var(--kp-yellow); color: var(--kp-dark);">Development</span>
                    @elseif(app()->environment('staging'))
                        <span class="badge px-2 py-1" style="background: #17a2b8; color: white;">Staging</span>
                    @else
                        <span class="badge px-2 py-1" style="background: var(--kp-green); color: white;">Production</span>
                    @endif
                </div>
            </div>
        </div>

        <hr class="my-3 bg-light opacity-25">

        <div class="row align-items-center">
            <div class="col-md-6 mb-2 mb-md-0">
                <div class="copyright small">
                    <p class="mb-0 text-light opacity-75">
                        &copy; {{ date('Y') }} <strong style="color: var(--kp-yellow);">Kenya Power and Lighting Company</strong>. All rights reserved.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-md-end align-items-center">
                    <button class="btn btn-outline-light btn-sm back-to-top" id="backToTop">
                        <i class="fas fa-arrow-up"></i>
                        <span class="d-none d-sm-inline ms-1">Top</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Back to top button
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Initialize tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        try {
            new bootstrap.Tooltip(el);
        } catch (e) {
            console.log('Tooltip error:', e);
        }
    });

    // Fix for mobile dropdowns
    if (window.innerWidth < 992) {
        document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const dropdown = this.nextElementSibling;
                if (dropdown && dropdown.classList.contains('dropdown-menu')) {
                    dropdown.classList.toggle('show');
                }
            });
        });
    }

    // Auto-dismiss alerts after 5 seconds
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Notification Functions
window.markAsRead = function(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
};

window.markAllAsRead = function() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
};

window.openChat = function(conversationId) {
    if (conversationId && conversationId > 0) {
        window.location.href = '{{ route("chat.index") }}?conversation=' + conversationId;
    }
};
</script>

@stack('scripts')
