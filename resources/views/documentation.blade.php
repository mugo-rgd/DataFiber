@extends('layouts.app')

@section('title', 'Documentation - Dark Fibre CRM')

@section('content')
<div class="container-fluid px-0">
    <div class="row g-0">
        <!-- Sidebar Documentation Navigation -->
        <div class="col-lg-3 col-xl-2 d-none d-lg-block border-end">
            <div class="sidebar-sticky pt-4" style="position: sticky; top: 0; height: 100vh; overflow-y: auto;">
                <div class="px-3">
                    <!-- Search -->
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" placeholder="Search documentation...">
                        </div>
                    </div>

                    <!-- Navigation -->
                    <nav class="docs-nav">
                        <h6 class="text-uppercase text-muted mb-3 small fw-bold">Getting Started</h6>
                        <ul class="nav flex-column mb-4">
                            <li class="nav-item">
                                <a class="nav-link active" href="#introduction">
                                    <i class="fas fa-play-circle me-2"></i> Introduction
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#installation">
                                    <i class="fas fa-download me-2"></i> Installation
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#configuration">
                                    <i class="fas fa-cog me-2"></i> Configuration
                                </a>
                            </li>
                        </ul>

                        <h6 class="text-uppercase text-muted mb-3 small fw-bold">Core Features</h6>
                        <ul class="nav flex-column mb-4">
                            <li class="nav-item">
                                <a class="nav-link" href="#fibre-management">
                                    <i class="fas fa-network-wired me-2"></i> Fibre Management
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#customer-management">
                                    <i class="fas fa-users me-2"></i> Customer Management
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#network-monitoring">
                                    <i class="fas fa-chart-line me-2"></i> Network Monitoring
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#reports">
                                    <i class="fas fa-chart-bar me-2"></i> Reports & Analytics
                                </a>
                            </li>
                        </ul>

                        <h6 class="text-uppercase text-muted mb-3 small fw-bold">API Reference</h6>
                        <ul class="nav flex-column mb-4">
                            <li class="nav-item">
                                <a class="nav-link" href="#authentication">
                                    <i class="fas fa-key me-2"></i> Authentication
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#endpoints">
                                    <i class="fas fa-code me-2"></i> API Endpoints
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#webhooks">
                                    <i class="fas fa-bell me-2"></i> Webhooks
                                </a>
                            </li>
                        </ul>

                        <h6 class="text-uppercase text-muted mb-3 small fw-bold">Advanced</h6>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="#customization">
                                    <i class="fas fa-paint-brush me-2"></i> Customization
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#troubleshooting">
                                    <i class="fas fa-wrench me-2"></i> Troubleshooting
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#faq">
                                    <i class="fas fa-question-circle me-2"></i> FAQ
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main Documentation Content -->
        <div class="col-lg-9 col-xl-10">
            <div class="docs-content p-4 p-lg-5">
                <!-- Introduction -->
                <section id="introduction" class="docs-section mb-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-lg bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                            <i class="fas fa-play-circle fa-lg"></i>
                        </div>
                        <div>
                            <h1 class="display-6 fw-bold mb-2">Documentation</h1>
                            <p class="text-muted">Complete guide to Dark Fibre CRM</p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-3">Welcome to Dark Fibre CRM</h2>
                            <p class="lead">Dark Fibre CRM is a comprehensive solution for managing fibre optic infrastructure, from network planning to customer management and billing.</p>

                            <div class="row mt-4">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="fas fa-check-circle text-success fa-lg"></i>
                                        </div>
                                        <div>
                                            <h5 class="h6 mb-2">Complete Fibre Management</h5>
                                            <p class="small text-muted mb-0">Manage your entire fibre network from a single dashboard</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="fas fa-check-circle text-success fa-lg"></i>
                                        </div>
                                        <div>
                                            <h5 class="h6 mb-2">Real-time Monitoring</h5>
                                            <p class="small text-muted mb-0">Monitor network performance and receive instant alerts</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="fas fa-check-circle text-success fa-lg"></i>
                                        </div>
                                        <div>
                                            <h5 class="h6 mb-2">Customer Portal</h5>
                                            <p class="small text-muted mb-0">Provide customers with self-service access to their services</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="fas fa-check-circle text-success fa-lg"></i>
                                        </div>
                                        <div>
                                            <h5 class="h6 mb-2">API Integration</h5>
                                            <p class="small text-muted mb-0">Integrate with existing systems using our REST API</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Start -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h3 class="h5 mb-0">Quick Start Guide</h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="steps">
                                <div class="step mb-4">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h4 class="h6">Create Your Account</h4>
                                        <p>Sign up for a Dark Fibre CRM account and verify your email address.</p>
                                    </div>
                                </div>
                                <div class="step mb-4">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h4 class="h6">Configure Your Network</h4>
                                        <p>Add your fibre routes, nodes, and network equipment to the system.</p>
                                    </div>
                                </div>
                                <div class="step mb-4">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h4 class="h6">Add Customers</h4>
                                        <p>Import or manually add your customers and assign them to network segments.</p>
                                    </div>
                                </div>
                                <div class="step">
                                    <div class="step-number">4</div>
                                    <div class="step-content">
                                        <h4 class="h6">Monitor & Manage</h4>
                                        <p>Use the dashboard to monitor performance and manage your network.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Fibre Management -->
                <section id="fibre-management" class="docs-section mb-5">
                    <h2 class="h3 mb-4 text-primary"><i class="fas fa-network-wired me-2"></i> Fibre Management</h2>

                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="icon-md bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </div>
                                        <div>
                                            <h4 class="h5 mb-1">Network Mapping</h4>
                                            <p class="text-muted small">Visualize your entire fibre network</p>
                                        </div>
                                    </div>
                                    <p>Create interactive maps of your fibre routes with detailed information about each segment, including length, capacity, and current usage.</p>
                                    <ul class="list-unstyled small">
                                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i> GPS-based route tracking</li>
                                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Multiple map layers</li>
                                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Export to CAD/GIS formats</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="icon-md bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-tools"></i>
                                        </div>
                                        <div>
                                            <h4 class="h5 mb-1">Infrastructure Management</h4>
                                            <p class="text-muted small">Track all network components</p>
                                        </div>
                                    </div>
                                    <p>Manage all your network infrastructure including fibre cables, splice closures, distribution points, and network equipment.</p>
                                    <ul class="list-unstyled small">
                                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Equipment inventory</li>
                                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Maintenance scheduling</li>
                                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Spare parts management</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Code Example -->
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="fas fa-code me-2"></i> API Example: Add Fibre Route</h5>
                        </div>
                        <div class="card-body bg-dark">
                            <pre class="mb-0 text-light" style="background: transparent;"><code class="language-javascript">
// Example: Creating a new fibre route via API
const response = await fetch('/api/v1/fibre-routes', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer YOUR_API_TOKEN',
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        name: 'Main Backbone Route',
        start_point: { lat: -1.2921, lng: 36.8219 },
        end_point: { lat: -1.3032, lng: 36.8123 },
        length_km: 25.5,
        fibre_count: 144,
        capacity_gbps: 100,
        owner: 'Your Company',
        status: 'active'
    })
});

const data = await response.json();
console.log('Route created:', data);
                            </code></pre>
                        </div>
                    </div>
                </section>

                <!-- FAQ Section -->
                <section id="faq" class="docs-section">
                    <h2 class="h3 mb-4 text-primary"><i class="fas fa-question-circle me-2"></i> Frequently Asked Questions</h2>

                    <div class="accordion" id="docsAccordion">
                        @foreach([
                            ['q' => 'How do I add new fibre routes?', 'a' => 'Navigate to Network → Fibre Routes → Add New Route. You can draw on the map or enter GPS coordinates manually.'],
                            ['q' => 'Can I import existing network data?', 'a' => 'Yes, we support CSV, Excel, and GIS file formats. Go to Settings → Data Import to upload your existing data.'],
                            ['q' => 'How is pricing calculated?', 'a' => 'Pricing is based on fibre length, capacity, and service level agreements. You can configure pricing models in Billing → Pricing Plans.'],
                            ['q' => 'Is there a mobile app?', 'a' => 'Yes, Dark Fibre CRM has native mobile apps for iOS and Android available on their respective app stores.'],
                            ['q' => 'How do I set up automated reports?', 'a' => 'Go to Reports → Schedule Report to configure automated reports that will be emailed to specified recipients.'],
                            ['q' => 'What browsers are supported?', 'a' => 'We support Chrome 80+, Firefox 75+, Safari 13+, and Edge 80+. JavaScript must be enabled.'],
                        ] as $index => $faq)
                        <div class="accordion-item border-0 shadow-sm mb-3">
                            <h3 class="accordion-header">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $index }}">
                                    {{ $faq['q'] }}
                                </button>
                            </h3>
                            <div id="faqCollapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#docsAccordion">
                                <div class="accordion-body">
                                    {{ $faq['a'] }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </section>

                <!-- Download Links -->
                <div class="mt-5 pt-5 border-top">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card text-center h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="icon-xl bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4">
                                        <i class="fas fa-file-pdf fa-2x"></i>
                                    </div>
                                    <h5 class="h5 mb-3">PDF Guide</h5>
                                    <p class="text-muted small mb-4">Complete documentation in PDF format</p>
                                    <a href="#" class="btn btn-outline-primary">Download PDF</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card text-center h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="icon-xl bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4">
                                        <i class="fas fa-video fa-2x"></i>
                                    </div>
                                    <h5 class="h5 mb-3">Video Tutorials</h5>
                                    <p class="text-muted small mb-4">Step-by-step video guides</p>
                                    <a href="#" class="btn btn-outline-primary">Watch Videos</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card text-center h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="icon-xl bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4">
                                        <i class="fas fa-code fa-2x"></i>
                                    </div>
                                    <h5 class="h5 mb-3">API Reference</h5>
                                    <p class="text-muted small mb-4">Complete API documentation</p>
                                    <a href="#" class="btn btn-outline-primary">View API Docs</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.docs-nav .nav-link {
    color: #495057;
    padding: 0.5rem 0;
    border-radius: 5px;
    margin-bottom: 0.25rem;
    transition: all 0.3s ease;
}

.docs-nav .nav-link:hover,
.docs-nav .nav-link.active {
    color: #4f46e5;
    background: rgba(79, 70, 229, 0.1);
    padding-left: 1rem;
}

.docs-nav .nav-link i {
    width: 20px;
}

.icon-lg {
    width: 70px;
    height: 70px;
}

.icon-md {
    width: 50px;
    height: 50px;
}

.icon-xl {
    width: 80px;
    height: 80px;
}

.steps {
    position: relative;
    padding-left: 2rem;
}

.steps::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(180deg, #4f46e5, #667eea);
}

.step {
    position: relative;
    display: flex;
    align-items: flex-start;
}

.step-number {
    position: absolute;
    left: -2rem;
    width: 2.5rem;
    height: 2.5rem;
    background: linear-gradient(135deg, #4f46e5, #667eea);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.875rem;
}

.step-content {
    flex: 1;
}

pre {
    border-radius: 8px;
    padding: 1rem;
    background: #1a202c !important;
}

code {
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    font-size: 0.875rem;
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

.accordion-item {
    border-radius: 8px !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for documentation navigation
    document.querySelectorAll('.docs-nav a').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Highlight active section in docs nav
    const sections = document.querySelectorAll('.docs-section');
    const navLinks = document.querySelectorAll('.docs-nav .nav-link');

    function highlightNav() {
        let scrollY = window.pageYOffset;

        sections.forEach(section => {
            const sectionHeight = section.offsetHeight;
            const sectionTop = section.offsetTop - 150;
            const sectionId = section.getAttribute('id');
            const navLink = document.querySelector(`.docs-nav a[href="#${sectionId}"]`);

            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                navLinks.forEach(link => link.classList.remove('active'));
                if (navLink) navLink.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', highlightNav);
    highlightNav(); // Initial call

    // Search functionality
    const searchInput = document.querySelector('input[placeholder="Search documentation..."]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const content = document.querySelectorAll('.docs-content h2, .docs-content h3, .docs-content p, .docs-content li');

            content.forEach(element => {
                const text = element.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    element.style.backgroundColor = 'rgba(255, 255, 0, 0.2)';
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    element.style.backgroundColor = '';
                }
            });
        });
    }

    // Copy code blocks
    document.querySelectorAll('pre code').forEach(block => {
        const button = document.createElement('button');
        button.className = 'btn btn-sm btn-outline-light position-absolute top-0 end-0 m-2';
        button.innerHTML = '<i class="fas fa-copy"></i>';
        button.title = 'Copy code';

        block.parentElement.style.position = 'relative';
        block.parentElement.appendChild(button);

        button.addEventListener('click', function() {
            navigator.clipboard.writeText(block.textContent).then(() => {
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.classList.remove('btn-outline-light');
                button.classList.add('btn-success');

                setTimeout(() => {
                    button.innerHTML = '<i class="fas fa-copy"></i>';
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-light');
                }, 2000);
            });
        });
    });
});
</script>
@endpush
