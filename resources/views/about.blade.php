@extends('layouts.app')

@section('title', 'About Us - Dark Fibre CRM')

@section('content')
<!-- Hero Section -->
<section class="hero-section py-5 mb-5" style="background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-light opacity-75">Home</a></li>
                        <li class="breadcrumb-item active text-light" aria-current="page">About Us</li>
                    </ol>
                </nav>
                <h1 class="display-4 fw-bold text-white mb-4">Revolutionizing Fibre Infrastructure Management</h1>
                <p class="lead text-light opacity-85 mb-4">We are pioneers in creating intelligent solutions for fibre network operators, ISPs, and telecommunications companies across Africa and beyond.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('contact') }}" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-envelope me-2"></i> Contact Us
                    </a>
                    <a href="{{ route('documentation') }}" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-book me-2"></i> View Docs
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <div class="network-visualization position-relative d-inline-block">
                        <div class="node node-center">
                            <i class="fas fa-network-wired fa-3x text-primary"></i>
                        </div>
                        <div class="node node-top">
                            <i class="fas fa-server fa-2x text-success"></i>
                        </div>
                        <div class="node node-right">
                            <i class="fas fa-cloud fa-2x text-info"></i>
                        </div>
                        <div class="node node-bottom">
                            <i class="fas fa-users fa-2x text-warning"></i>
                        </div>
                        <div class="node node-left">
                            <i class="fas fa-chart-line fa-2x text-danger"></i>
                        </div>
                        <div class="connections"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Story -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1558494949-ef010cbdcc31?ixlib=rb-1.2.1&auto=format&fit=crop&w=700&q=80"
                         alt="Fibre Network Operations"
                         class="img-fluid rounded-3 shadow-lg mb-4">
                    <div class="bg-primary text-white p-4 rounded-3 shadow position-absolute bottom-0 end-0" style="max-width: 300px;">
                        <h5 class="mb-2"><i class="fas fa-trophy me-2"></i> Industry Leaders</h5>
                        <p class="small mb-0">Serving 50+ telecom companies across Africa since 2020</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2">Our Story</span>
                <h2 class="display-5 fw-bold mb-4">Building Africa's Digital Backbone</h2>
                <p class="lead mb-4">Founded in Nairobi, Kenya, Dark Fibre CRM was born out of a clear need: the African continent's rapidly expanding fibre infrastructure required smarter, more efficient management tools.</p>

                <div class="timeline-wrapper">
                    <div class="timeline-item mb-4">
                        <div class="timeline-date">2020</div>
                        <div class="timeline-content">
                            <h5 class="mb-2">The Beginning</h5>
                            <p class="mb-0">Started as a small project to help local ISPs manage their fibre networks more efficiently.</p>
                        </div>
                    </div>
                    <div class="timeline-item mb-4">
                        <div class="timeline-date">2021</div>
                        <div class="timeline-content">
                            <h5 class="mb-2">First Major Deployment</h5>
                            <p class="mb-0">Deployed our solution for a major telecommunications company serving 5 countries.</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-date">2023</div>
                        <div class="timeline-content">
                            <h5 class="mb-2">Expansion & Innovation</h5>
                            <p class="mb-0">Launched AI-powered predictive maintenance and real-time network analytics.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mission & Vision -->
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                                <i class="fas fa-bullseye fa-2x"></i>
                            </div>
                            <h3 class="h4 mb-0">Our Mission</h3>
                        </div>
                        <p>To empower fibre infrastructure providers with intelligent tools that simplify complex network management, reduce operational costs, and enhance service delivery across Africa.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Simplify complex network operations</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Reduce operational costs by 40%</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Enhance service reliability</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Support Africa's digital transformation</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                                <i class="fas fa-eye fa-2x"></i>
                            </div>
                            <h3 class="h4 mb-0">Our Vision</h3>
                        </div>
                        <p>To become the leading platform for fibre infrastructure management in emerging markets, connecting communities and businesses through reliable, efficient, and intelligent network solutions.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Lead in emerging markets by 2025</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Connect 10,000+ km of fibre</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Power 500+ telecom operators</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Enable 1M+ digital connections</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Core Values -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2">Our Values</span>
                <h2 class="display-5 fw-bold mb-4">What Guides Us</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">Our core values shape our culture and drive our commitment to excellence in everything we do.</p>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body p-4 text-center">
                        <div class="value-icon-wrapper mb-4">
                            <i class="fas fa-lightbulb fa-3x text-primary"></i>
                        </div>
                        <h4 class="h5 mb-3">Innovation</h4>
                        <p class="mb-0">We constantly push boundaries to develop cutting-edge solutions that solve real-world problems in fibre management.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body p-4 text-center">
                        <div class="value-icon-wrapper mb-4">
                            <i class="fas fa-handshake fa-3x text-success"></i>
                        </div>
                        <h4 class="h5 mb-3">Reliability</h4>
                        <p class="mb-0">We build systems that telecom operators can depend on 24/7, ensuring uninterrupted connectivity for their customers.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body p-4 text-center">
                        <div class="value-icon-wrapper mb-4">
                            <i class="fas fa-users fa-3x text-info"></i>
                        </div>
                        <h4 class="h5 mb-3">Collaboration</h4>
                        <p class="mb-0">We believe in working closely with our clients to understand their unique challenges and co-create effective solutions.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body p-4 text-center">
                        <div class="value-icon-wrapper mb-4">
                            <i class="fas fa-shield-alt fa-3x text-warning"></i>
                        </div>
                        <h4 class="h5 mb-3">Integrity</h4>
                        <p class="mb-0">We maintain the highest standards of honesty and transparency in all our business dealings and data handling.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Features -->
        <div class="row align-items-center mb-5">
            <div class="col-lg-6">
                <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2">Why Choose Us</span>
                <h2 class="display-5 fw-bold mb-4">Powerful Features for Modern Networks</h2>
                <p class="lead mb-4">Our platform combines industry expertise with technological innovation to deliver unparalleled value.</p>

                <div class="feature-list">
                    <div class="feature-item d-flex mb-4">
                        <div class="feature-icon me-4">
                            <i class="fas fa-bolt fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h4 class="h5 mb-2">Real-time Monitoring</h4>
                            <p class="mb-0">Monitor your entire fibre network in real-time with intuitive dashboards and alerts.</p>
                        </div>
                    </div>

                    <div class="feature-item d-flex mb-4">
                        <div class="feature-icon me-4">
                            <i class="fas fa-robot fa-2x text-success"></i>
                        </div>
                        <div>
                            <h4 class="h5 mb-2">AI-Powered Analytics</h4>
                            <p class="mb-0">Predict network issues before they occur with our advanced machine learning algorithms.</p>
                        </div>
                    </div>

                    <div class="feature-item d-flex">
                        <div class="feature-icon me-4">
                            <i class="fas fa-mobile-alt fa-2x text-info"></i>
                        </div>
                        <div>
                            <h4 class="h5 mb-2">Mobile-First Design</h4>
                            <p class="mb-0">Manage your network from anywhere with our fully responsive mobile interface.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-lg overflow-hidden">
                    <div class="card-body p-0">
                        <div class="stats-grid">
                            <div class="stat-item bg-primary text-white">
                                <h3 class="display-4 fw-bold mb-2">50+</h3>
                                <p class="mb-0">Telecom Partners</p>
                            </div>
                            <div class="stat-item bg-success text-white">
                                <h3 class="display-4 fw-bold mb-2">5,000+</h3>
                                <p class="mb-0">KM Fibre Managed</p>
                            </div>
                            <div class="stat-item bg-info text-white">
                                <h3 class="display-4 fw-bold mb-2">24/7</h3>
                                <p class="mb-0">Support Available</p>
                            </div>
                            <div class="stat-item bg-warning text-white">
                                <h3 class="display-4 fw-bold mb-2">99.9%</h3>
                                <p class="mb-0">Uptime SLA</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Section (Optional - Can be added later) -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2">Leadership</span>
                <h2 class="display-5 fw-bold mb-5">Meet Our Leadership Team</h2>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 text-center">
                    <div class="card-body p-4">
                        <div class="team-avatar mb-4 mx-auto">
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                        </div>
                        <h4 class="h5 mb-2">Benjamen Muoki</h4>
                        <p class="text-primary mb-3">Telecommunications Manager ICT</p>
                        <p class="mb-0">15+ years in telecommunications and network infrastructure management.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 text-center">
                    <div class="card-body p-4">
                        <div class="team-avatar mb-4 mx-auto">
                            <i class="fas fa-user-circle fa-5x text-success"></i>
                        </div>
                        <h4 class="h5 mb-2">Eng. Eric Wanjala</h4>
                        <p class="text-primary mb-3">Chief TBU</p>
                        <p class="mb-0">Expert in cloud infrastructure, AI, and network security with 12 years experience.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 text-center">
                    <div class="card-body p-4">
                        <div class="team-avatar mb-4 mx-auto">
                            <i class="fas fa-user-circle fa-5x text-info"></i>
                        </div>
                        <h4 class="h5 mb-2">Michael Chege</h4>
                        <p class="text-primary mb-3">Head of Operations</p>
                        <p class="mb-0">Specializes in large-scale network deployments and operational efficiency.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 text-center">
                    <div class="card-body p-4">
                        <div class="team-avatar mb-4 mx-auto">
                            <i class="fas fa-user-circle fa-5x text-info"></i>
                        </div>
                        <h4 class="h5 mb-2">Wilson Mwirigi</h4>
                        <p class="text-primary mb-3">Head of Marketing</p>
                        <p class="mb-0">Specializes in marketing of dark Fiber network deployments and operational efficiency.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg bg-primary text-white overflow-hidden">
                    <div class="card-body p-5">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h2 class="display-6 fw-bold mb-3">Ready to Transform Your Fibre Network Management?</h2>
                                <p class="lead mb-0 opacity-85">Join 50+ telecom companies who trust Dark Fibre CRM to manage their critical infrastructure.</p>
                            </div>
                            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                                <a href="{{ route('contact') }}" class="btn btn-light btn-lg px-5">
                                    <i class="fas fa-calendar-check me-2"></i> Schedule Demo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.hero-section {
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 300px;
    background: linear-gradient(45deg, rgba(79, 70, 229, 0.1), transparent);
    border-radius: 50%;
}

.network-visualization {
    width: 400px;
    height: 400px;
}

.node {
    position: absolute;
    width: 80px;
    height: 80px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.node:hover {
    transform: scale(1.1);
}

.node-center {
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100px;
    height: 100px;
}

.node-top {
    top: 20%;
    left: 50%;
    transform: translateX(-50%);
}

.node-right {
    top: 50%;
    right: 20%;
    transform: translateY(-50%);
}

.node-bottom {
    bottom: 20%;
    left: 50%;
    transform: translateX(-50%);
}

.node-left {
    top: 50%;
    left: 20%;
    transform: translateY(-50%);
}

.connections {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.connections::before,
.connections::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border: 2px solid rgba(79, 70, 229, 0.2);
    border-radius: 50%;
}

.connections::before {
    width: 200px;
    height: 200px;
}

.connections::after {
    width: 300px;
    height: 300px;
}

.timeline-wrapper {
    position: relative;
    padding-left: 40px;
}

.timeline-wrapper::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(180deg, #4f46e5, #8b5cf6);
}

.timeline-item {
    position: relative;
}

.timeline-date {
    position: absolute;
    left: -40px;
    top: 0;
    width: 30px;
    height: 30px;
    background: #4f46e5;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.875rem;
}

.timeline-content {
    padding-left: 20px;
}

.icon-wrapper {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.value-icon-wrapper {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(139, 92, 246, 0.1));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
}

.feature-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(139, 92, 246, 0.1));
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-gap: 0;
}

.stat-item {
    padding: 3rem 1.5rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.stat-item:nth-child(1) { background: linear-gradient(135deg, #4f46e5, #6366f1); }
.stat-item:nth-child(2) { background: linear-gradient(135deg, #10b981, #34d399); }
.stat-item:nth-child(3) { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
.stat-item:nth-child(4) { background: linear-gradient(135deg, #f59e0b, #fbbf24); }

.stat-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(100%);
    transition: transform 0.3s ease;
}

.stat-item:hover::before {
    transform: translateY(0);
}

.team-avatar {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(139, 92, 246, 0.1));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

@media (max-width: 768px) {
    .network-visualization {
        width: 300px;
        height: 300px;
        margin-top: 2rem;
    }

    .node {
        width: 60px;
        height: 60px;
    }

    .node-center {
        width: 80px;
        height: 80px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .hero-section h1 {
        font-size: 2.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate network visualization
    const nodes = document.querySelectorAll('.node');
    nodes.forEach((node, index) => {
        node.style.animation = `float ${3 + index * 0.5}s infinite ease-in-out`;
    });

    // Add CSS animation for floating nodes
    const style = document.createElement('style');
    style.textContent = `
        @keyframes float {
            0%, 100% { transform: translate(var(--tx, -50%), var(--ty, -50%)) translateY(0px); }
            50% { transform: translate(var(--tx, -50%), var(--ty, -50%)) translateY(-10px); }
        }

        .node-top { --tx: -50%; --ty: 0; }
        .node-right { --tx: 0; --ty: -50%; }
        .node-bottom { --tx: -50%; --ty: 0; }
        .node-left { --tx: 0; --ty: -50%; }
    `;
    document.head.appendChild(style);

    // Animate stats counter
    const statItems = document.querySelectorAll('.stat-item h3');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const finalValue = parseInt(target.textContent);
                let currentValue = 0;
                const increment = finalValue / 50;
                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        target.textContent = finalValue + (target.textContent.includes('+') ? '+' :
                                                          target.textContent.includes('.') ? '%' : '');
                        clearInterval(timer);
                    } else {
                        target.textContent = Math.floor(currentValue) +
                                           (target.textContent.includes('+') ? '+' :
                                            target.textContent.includes('.') ? '%' : '');
                    }
                }, 30);
                observer.unobserve(target);
            }
        });
    }, { threshold: 0.5 });

    statItems.forEach(item => {
        observer.observe(item);
    });

    // Add hover effect to feature items
    const featureItems = document.querySelectorAll('.feature-item');
    featureItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.querySelector('.feature-icon').style.transform = 'scale(1.1)';
        });

        item.addEventListener('mouseleave', function() {
            this.querySelector('.feature-icon').style.transform = 'scale(1)';
        });
    });
});
</script>
@endpush
