@extends('layouts.app')

@section('title', 'Support - Dark Fibre CRM')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Page Header -->
            <div class="page-header mb-5">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Support</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold mb-3">Support Center</h1>
                <p class="lead text-muted">Get help with Dark Fibre CRM. We're here to assist you.</p>
            </div>

            <!-- Support Tabs -->
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <!-- Support Navigation -->
                    <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                        <div class="card-body p-4">
                            <div class="support-nav">
                                <h5 class="mb-3 text-primary">Support Categories</h5>
                                <div class="list-group list-group-flush">
                                    <a href="#getting-started" class="list-group-item list-group-item-action border-0 py-3">
                                        <i class="fas fa-rocket me-2 text-primary"></i> Getting Started
                                    </a>
                                    <a href="#account-management" class="list-group-item list-group-item-action border-0 py-3">
                                        <i class="fas fa-user-cog me-2 text-primary"></i> Account Management
                                    </a>
                                    <a href="#fibre-management" class="list-group-item list-group-item-action border-0 py-3">
                                        <i class="fas fa-network-wired me-2 text-primary"></i> Fibre Management
                                    </a>
                                    <a href="#billing" class="list-group-item list-group-item-action border-0 py-3">
                                        <i class="fas fa-credit-card me-2 text-primary"></i> Billing & Payments
                                    </a>
                                    <a href="#technical" class="list-group-item list-group-item-action border-0 py-3">
                                        <i class="fas fa-cogs me-2 text-primary"></i> Technical Issues
                                    </a>
                                    <a href="#contact" class="list-group-item list-group-item-action border-0 py-3">
                                        <i class="fas fa-headset me-2 text-primary"></i> Contact Support
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card shadow-sm border-0 mt-4">
                        <div class="card-body p-4">
                            <h5 class="mb-3 text-primary">Support Statistics</h5>
                            <div class="support-stats">
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Response Time:</span>
                                    <span class="text-success fw-bold">Under 2 hours</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Resolution Rate:</span>
                                    <span class="text-success fw-bold">95%</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Support Hours:</span>
                                    <span class="text-primary fw-bold">24/7</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Satisfaction:</span>
                                    <span class="text-warning fw-bold">4.8/5</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <!-- Getting Started -->
                    <section id="getting-started" class="mb-5">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary text-white">
                                <h2 class="h4 mb-0"><i class="fas fa-rocket me-2"></i> Getting Started</h2>
                            </div>
                            <div class="card-body p-4">
                                <div class="accordion" id="gettingStartedAccordion">
                                    <!-- FAQ Items -->
                                    @foreach([
                                        ['q' => 'How do I create an account?', 'a' => 'Click the "Sign Up" button on the homepage, fill in your details, and verify your email address.'],
                                        ['q' => 'What are the system requirements?', 'a' => 'Dark Fibre CRM works on all modern browsers (Chrome, Firefox, Safari, Edge) with JavaScript enabled.'],
                                        ['q' => 'How do I import my existing data?', 'a' => 'Navigate to Settings > Data Import and follow the step-by-step wizard to import your fibre infrastructure data.'],
                                        ['q' => 'Can I use the mobile app?', 'a' => 'Yes, Dark Fibre CRM is fully responsive and works on mobile devices. We also offer native mobile apps for iOS and Android.'],
                                    ] as $index => $faq)
                                    <div class="accordion-item border-0 mb-2">
                                        <h3 class="accordion-header">
                                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $index }}">
                                                {{ $faq['q'] }}
                                            </button>
                                        </h3>
                                        <div id="faq-{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#gettingStartedAccordion">
                                            <div class="accordion-body">
                                                {{ $faq['a'] }}
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Account Management -->
                    <section id="account-management" class="mb-5">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary text-white">
                                <h2 class="h4 mb-0"><i class="fas fa-user-cog me-2"></i> Account Management</h2>
                            </div>
                            <div class="card-body p-4">
                                <p>Manage your account settings, users, and permissions.</p>
                                <ul class="list-group list-group-flush mb-3">
                                    <li class="list-group-item border-0"><i class="fas fa-check-circle text-success me-2"></i> Update profile information</li>
                                    <li class="list-group-item border-0"><i class="fas fa-check-circle text-success me-2"></i> Manage team members</li>
                                    <li class="list-group-item border-0"><i class="fas fa-check-circle text-success me-2"></i> Set user permissions</li>
                                    <li class="list-group-item border-0"><i class="fas fa-check-circle text-success me-2"></i> Configure notifications</li>
                                </ul>
                                <p class="mb-0">Go to <strong>Settings → Account</strong> to manage your account.</p>
                            </div>
                        </div>
                    </section>

                    <!-- Fibre Management -->
                    <section id="fibre-management" class="mb-5">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary text-white">
                                <h2 class="h4 mb-0"><i class="fas fa-network-wired me-2"></i> Fibre Management</h2>
                            </div>
                            <div class="card-body p-4">
                                <h5 class="mb-3">Common Tasks</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 border">
                                            <div class="card-body">
                                                <h6><i class="fas fa-plus-circle text-primary me-2"></i> Add Fibre Route</h6>
                                                <p class="small">Learn how to add new fibre routes to your network map.</p>
                                                <a href="#" class="btn btn-sm btn-outline-primary">View Guide</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 border">
                                            <div class="card-body">
                                                <h6><i class="fas fa-chart-line text-primary me-2"></i> Monitor Usage</h6>
                                                <p class="small">Track bandwidth usage and performance metrics.</p>
                                                <a href="#" class="btn btn-sm btn-outline-primary">View Guide</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Contact Support -->
                    <section id="contact" class="mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary text-white">
                                <h2 class="h4 mb-0"><i class="fas fa-headset me-2"></i> Contact Support</h2>
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="contact-method text-center">
                                            <div class="icon-circle mb-3">
                                                <i class="fas fa-envelope fa-2x text-primary"></i>
                                            </div>
                                            <h5>Email Support</h5>
                                            <p class="mb-2">For detailed inquiries</p>
                                            <a href="mailto:Fiber@kplc.co.ke" class="btn btn-primary">Fiber@kplc.co.ke</a>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="contact-method text-center">
                                            <div class="icon-circle mb-3">
                                                <i class="fas fa-phone fa-2x text-primary"></i>
                                            </div>
                                            <h5>Phone Support</h5>
                                            <p class="mb-2">24/7 emergency support</p>
                                            <a href="tel:+254700000000" class="btn btn-primary">+254 700 000 000</a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Support Form -->
                                <div class="mt-4 pt-4 border-top">
                                    <h5 class="mb-3">Submit a Support Ticket</h5>
                                    <form id="supportForm">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="subject" class="form-label">Subject</label>
                                            <input type="text" class="form-control" id="subject" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Category</label>
                                            <select class="form-select" id="category" required>
                                                <option value="">Select a category</option>
                                                <option value="technical">Technical Issue</option>
                                                <option value="billing">Billing Inquiry</option>
                                                <option value="feature">Feature Request</option>
                                                <option value="bug">Bug Report</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" rows="4" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit Ticket</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem;
    border-radius: 10px;
    color: white;
}

.page-header .breadcrumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 5px;
    padding: 0.5rem 1rem;
}

.page-header .breadcrumb-item a {
    color: rgba(255, 255, 255, 0.9);
}

.page-header .breadcrumb-item.active {
    color: white;
}

.page-header .lead {
    color: rgba(255, 255, 255, 0.8);
}

.support-nav .list-group-item {
    border-radius: 8px;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.support-nav .list-group-item:hover,
.support-nav .list-group-item.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: translateX(5px);
}

.icon-circle {
    width: 80px;
    height: 80px;
    background: rgba(102, 126, 234, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.accordion-button {
    font-weight: 500;
    border-radius: 8px !important;
}

.accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: none;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for support navigation
    document.querySelectorAll('.support-nav a').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Support form submission
    const supportForm = document.getElementById('supportForm');
    if (supportForm) {
        supportForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // In a real application, you would submit to your backend
            alert('Thank you! Your support ticket has been submitted. We will contact you within 2 hours.');
            supportForm.reset();
        });
    }

    // Highlight active section in support nav
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.support-nav a');

    function highlightNav() {
        let scrollY = window.pageYOffset;

        sections.forEach(section => {
            const sectionHeight = section.offsetHeight;
            const sectionTop = section.offsetTop - 100;
            const sectionId = section.getAttribute('id');
            const navLink = document.querySelector(`.support-nav a[href="#${sectionId}"]`);

            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                navLinks.forEach(link => link.classList.remove('active'));
                if (navLink) navLink.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', highlightNav);
    highlightNav(); // Initial call
});
</script>
@endpush
