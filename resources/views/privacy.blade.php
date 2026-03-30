@extends('layouts.app')

@section('title', 'Privacy Policy - Dark Fibre CRM')

@section('content')
<!-- Hero Section -->
<section class="privacy-hero py-5 mb-5" style="background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-light opacity-75">Home</a></li>
                        <li class="breadcrumb-item active text-light" aria-current="page">Privacy Policy</li>
                    </ol>
                </nav>
                <h1 class="display-4 fw-bold text-white mb-4">Privacy Policy</h1>
                <p class="lead text-light opacity-85 mb-4">Your privacy is important to us. This policy explains how we collect, use, and protect your data.</p>
                <div class="d-flex flex-wrap gap-3">
                    <div class="badge bg-light text-dark px-3 py-2">
                        <i class="fas fa-calendar-alt me-2"></i> Last Updated: {{ date('F j, Y') }}
                    </div>
                    <div class="badge bg-light text-dark px-3 py-2">
                        <i class="fas fa-shield-alt me-2"></i> GDPR Compliant
                    </div>
                    <div class="badge bg-light text-dark px-3 py-2">
                        <i class="fas fa-lock me-2"></i> Data Encrypted
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="text-center">
                    <div class="privacy-visualization position-relative d-inline-block">
                        <div class="shield-icon">
                            <i class="fas fa-shield-alt fa-5x text-primary"></i>
                        </div>
                        <div class="encryption-ring encryption-ring-1"></div>
                        <div class="encryption-ring encryption-ring-2"></div>
                        <div class="encryption-ring encryption-ring-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Navigation -->
<section class="sticky-nav-wrapper mb-5">
    <div class="container">
        <div class="card border-0 shadow-lg">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-3 mb-3 mb-lg-0">
                        <h5 class="mb-0 text-primary"><i class="fas fa-stream me-2"></i> Quick Navigation</h5>
                    </div>
                    <div class="col-lg-9">
                        <div class="privacy-nav">
                            <a href="#introduction" class="privacy-nav-link active">Introduction</a>
                            <a href="#data-collection" class="privacy-nav-link">Data Collection</a>
                            <a href="#data-use" class="privacy-nav-link">Data Use</a>
                            <a href="#data-sharing" class="privacy-nav-link">Data Sharing</a>
                            <a href="#your-rights" class="privacy-nav-link">Your Rights</a>
                            <a href="#security" class="privacy-nav-link">Security</a>
                            <a href="#cookies" class="privacy-nav-link">Cookies</a>
                            <a href="#contact" class="privacy-nav-link">Contact</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Privacy Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Introduction -->
                <section id="introduction" class="privacy-section mb-5">
                    <div class="section-header mb-4">
                        <span class="section-number">01</span>
                        <h2 class="h3 mb-3">Introduction</h2>
                        <div class="section-divider"></div>
                    </div>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <p class="lead">Welcome to Dark Fibre CRM. We are committed to protecting your privacy and ensuring the security of your personal information.</p>
                            <p>This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our fibre infrastructure management platform and services (collectively, the "Service").</p>
                            <p>Please read this Privacy Policy carefully. By using our Service, you agree to the collection and use of information in accordance with this policy. If you do not agree with the terms of this Privacy Policy, please do not access the Service.</p>
                            <div class="alert alert-primary mt-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> This policy applies to all users of our Service, including visitors, customers, and partners.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Data Collection -->
                <section id="data-collection" class="privacy-section mb-5">
                    <div class="section-header mb-4">
                        <span class="section-number">02</span>
                        <h2 class="h3 mb-3">Information We Collect</h2>
                        <div class="section-divider"></div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="data-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                                            <i class="fas fa-user fa-2x"></i>
                                        </div>
                                        <h4 class="h5 mb-0">Personal Information</h4>
                                    </div>
                                    <p>We collect personal information that you voluntarily provide when you:</p>
                                    <ul class="list-unstyled small">
                                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Create an account</li>
                                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Contact our support</li>
                                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Subscribe to newsletters</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i> Participate in surveys</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="data-icon bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                                            <i class="fas fa-network-wired fa-2x"></i>
                                        </div>
                                        <h4 class="h5 mb-0">Network Data</h4>
                                    </div>
                                    <p>As a fibre management platform, we collect:</p>
                                    <ul class="list-unstyled small">
                                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Fibre route information</li>
                                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Network performance metrics</li>
                                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Equipment inventory data</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i> Customer infrastructure details</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="data-icon bg-info bg-opacity-10 text-info rounded-circle p-3 me-3">
                                            <i class="fas fa-laptop fa-2x"></i>
                                        </div>
                                        <h4 class="h5 mb-0">Usage Data</h4>
                                    </div>
                                    <p>We automatically collect usage information:</p>
                                    <ul class="list-unstyled small">
                                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> IP addresses and browser types</li>
                                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Pages visited and features used</li>
                                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Time stamps and session durations</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i> Device information and operating systems</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="data-icon bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                                            <i class="fas fa-cookie-bite fa-2x"></i>
                                        </div>
                                        <h4 class="h5 mb-0">Cookies & Tracking</h4>
                                    </div>
                                    <p>We use cookies and similar technologies:</p>
                                    <ul class="list-unstyled small">
                                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Session cookies for authentication</li>
                                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Analytics cookies for improvement</li>
                                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Preference cookies for settings</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i> Security cookies for protection</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Data Use -->
                <section id="data-use" class="privacy-section mb-5">
                    <div class="section-header mb-4">
                        <span class="section-number">03</span>
                        <h2 class="h3 mb-3">How We Use Your Information</h2>
                        <div class="section-divider"></div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <p>We use the information we collect for various purposes, including:</p>

                            <div class="row mt-4">
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex">
                                        <div class="use-icon me-3">
                                            <i class="fas fa-cogs fa-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <h5 class="h6 mb-2">Service Delivery</h5>
                                            <p class="small text-muted mb-0">To provide, maintain, and improve our fibre management services</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="d-flex">
                                        <div class="use-icon me-3">
                                            <i class="fas fa-headset fa-2x text-success"></i>
                                        </div>
                                        <div>
                                            <h5 class="h6 mb-2">Customer Support</h5>
                                            <p class="small text-muted mb-0">To respond to your inquiries and provide technical support</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="d-flex">
                                        <div class="use-icon me-3">
                                            <i class="fas fa-chart-line fa-2x text-info"></i>
                                        </div>
                                        <div>
                                            <h5 class="h6 mb-2">Analytics & Improvement</h5>
                                            <p class="small text-muted mb-0">To analyze usage patterns and enhance our platform</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="d-flex">
                                        <div class="use-icon me-3">
                                            <i class="fas fa-bell fa-2x text-warning"></i>
                                        </div>
                                        <div>
                                            <h5 class="h6 mb-2">Communication</h5>
                                            <p class="small text-muted mb-0">To send important updates, security alerts, and service notifications</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="d-flex">
                                        <div class="use-icon me-3">
                                            <i class="fas fa-shield-alt fa-2x text-danger"></i>
                                        </div>
                                        <div>
                                            <h5 class="h6 mb-2">Security & Fraud Prevention</h5>
                                            <p class="small text-muted mb-0">To protect our platform and users from unauthorized access</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="d-flex">
                                        <div class="use-icon me-3">
                                            <i class="fas fa-gavel fa-2x text-secondary"></i>
                                        </div>
                                        <div>
                                            <h5 class="h6 mb-2">Legal Compliance</h5>
                                            <p class="small text-muted mb-0">To comply with applicable laws, regulations, and legal processes</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Data Sharing -->
                <section id="data-sharing" class="privacy-section mb-5">
                    <div class="section-header mb-4">
                        <span class="section-number">04</span>
                        <h2 class="h3 mb-3">Data Sharing and Disclosure</h2>
                        <div class="section-divider"></div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <p>We may share your information in the following situations:</p>

                            <div class="sharing-table mt-4">
                                <div class="sharing-row">
                                    <div class="sharing-party">
                                        <i class="fas fa-user-tie text-primary me-2"></i>
                                        <strong>Service Providers</strong>
                                    </div>
                                    <div class="sharing-purpose">
                                        With trusted third parties who assist us in operating our platform, conducting our business, or servicing you
                                    </div>
                                </div>

                                <div class="sharing-row">
                                    <div class="sharing-party">
                                        <i class="fas fa-handshake text-success me-2"></i>
                                        <strong>Business Partners</strong>
                                    </div>
                                    <div class="sharing-purpose">
                                        With our partners for joint offerings, but only with your explicit consent
                                    </div>
                                </div>

                                <div class="sharing-row">
                                    <div class="sharing-party">
                                        <i class="fas fa-gavel text-warning me-2"></i>
                                        <strong>Legal Requirements</strong>
                                    </div>
                                    <div class="sharing-purpose">
                                        When required by law or to respond to valid legal processes
                                    </div>
                                </div>

                                <div class="sharing-row">
                                    <div class="sharing-party">
                                        <i class="fas fa-building text-info me-2"></i>
                                        <strong>Business Transfers</strong>
                                    </div>
                                    <div class="sharing-purpose">
                                        In connection with a merger, acquisition, or sale of assets
                                    </div>
                                </div>

                                <div class="sharing-row">
                                    <div class="sharing-party">
                                        <i class="fas fa-shield-alt text-danger me-2"></i>
                                        <strong>Protection of Rights</strong>
                                    </div>
                                    <div class="sharing-purpose">
                                        To protect the rights, property, or safety of Dark Fibre CRM, our users, or others
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning mt-4">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Important:</strong> We do not sell, rent, or trade your personal information to third parties for their marketing purposes.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Your Rights -->
                <section id="your-rights" class="privacy-section mb-5">
                    <div class="section-header mb-4">
                        <span class="section-number">05</span>
                        <h2 class="h3 mb-3">Your Data Protection Rights</h2>
                        <div class="section-divider"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4 text-center">
                                    <div class="rights-icon mb-4 mx-auto">
                                        <i class="fas fa-eye fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="h5 mb-3">Right to Access</h5>
                                    <p class="mb-0">You have the right to request copies of your personal data.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4 text-center">
                                    <div class="rights-icon mb-4 mx-auto">
                                        <i class="fas fa-edit fa-3x text-success"></i>
                                    </div>
                                    <h5 class="h5 mb-3">Right to Rectification</h5>
                                    <p class="mb-0">You have the right to request correction of inaccurate information.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4 text-center">
                                    <div class="rights-icon mb-4 mx-auto">
                                        <i class="fas fa-trash-alt fa-3x text-danger"></i>
                                    </div>
                                    <h5 class="h5 mb-3">Right to Erasure</h5>
                                    <p class="mb-0">You have the right to request deletion of your personal data.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4 text-center">
                                    <div class="rights-icon mb-4 mx-auto">
                                        <i class="fas fa-ban fa-3x text-warning"></i>
                                    </div>
                                    <h5 class="h5 mb-3">Right to Restrict Processing</h5>
                                    <p class="mb-0">You have the right to request restriction of processing your data.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4 text-center">
                                    <div class="rights-icon mb-4 mx-auto">
                                        <i class="fas fa-file-export fa-3x text-info"></i>
                                    </div>
                                    <h5 class="h5 mb-3">Right to Data Portability</h5>
                                    <p class="mb-0">You have the right to request transfer of your data to another organization.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4 text-center">
                                    <div class="rights-icon mb-4 mx-auto">
                                        <i class="fas fa-hand-paper fa-3x text-secondary"></i>
                                    </div>
                                    <h5 class="h5 mb-3">Right to Object</h5>
                                    <p class="mb-0">You have the right to object to our processing of your personal data.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-body p-4">
                            <h5 class="h5 mb-3">Exercising Your Rights</h5>
                            <p>To exercise any of these rights, please contact us using the information in the "Contact Us" section below. We will respond to your request within 30 days.</p>
                            <p class="mb-0">Please note that we may need to verify your identity before processing your request.</p>
                        </div>
                    </div>
                </section>

                <!-- Security -->
                <section id="security" class="privacy-section mb-5">
                    <div class="section-header mb-4">
                        <span class="section-number">06</span>
                        <h2 class="h3 mb-3">Data Security</h2>
                        <div class="section-divider"></div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <p>We implement appropriate technical and organizational security measures to protect your personal data against unauthorized access, alteration, disclosure, or destruction.</p>

                            <div class="row mt-4">
                                <div class="col-md-6 mb-4">
                                    <div class="security-feature">
                                        <div class="security-icon">
                                            <i class="fas fa-lock fa-2x text-primary"></i>
                                        </div>
                                        <div class="security-content">
                                            <h5 class="h6 mb-2">Encryption</h5>
                                            <p class="small mb-0">All data is encrypted in transit using TLS 1.3 and at rest using AES-256 encryption.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="security-feature">
                                        <div class="security-icon">
                                            <i class="fas fa-shield-alt fa-2x text-success"></i>
                                        </div>
                                        <div class="security-content">
                                            <h5 class="h6 mb-2">Access Controls</h5>
                                            <p class="small mb-0">Strict access controls and authentication mechanisms limit data access to authorized personnel.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="security-feature">
                                        <div class="security-icon">
                                            <i class="fas fa-clipboard-check fa-2x text-info"></i>
                                        </div>
                                        <div class="security-content">
                                            <h5 class="h6 mb-2">Regular Audits</h5>
                                            <p class="small mb-0">Regular security audits and vulnerability assessments ensure ongoing protection.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="security-feature">
                                        <div class="security-icon">
                                            <i class="fas fa-user-shield fa-2x text-warning"></i>
                                        </div>
                                        <div class="security-content">
                                            <h5 class="h6 mb-2">Employee Training</h5>
                                            <p class="small mb-0">All employees receive regular data protection and security training.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-4">
                                <i class="fas fa-lightbulb me-2"></i>
                                <strong>Security Tip:</strong> While we implement robust security measures, no method of transmission over the Internet or electronic storage is 100% secure. We cannot guarantee absolute security.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Cookies -->
                <section id="cookies" class="privacy-section mb-5">
                    <div class="section-header mb-4">
                        <span class="section-number">07</span>
                        <h2 class="h3 mb-3">Cookies and Tracking Technologies</h2>
                        <div class="section-divider"></div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <p>We use cookies and similar tracking technologies to track activity on our Service and hold certain information.</p>

                            <div class="cookie-types mt-4">
                                <h5 class="h5 mb-3">Types of Cookies We Use</h5>

                                <div class="cookie-type">
                                    <div class="cookie-header">
                                        <span class="cookie-badge bg-primary">Essential</span>
                                        <strong>Necessary Cookies</strong>
                                    </div>
                                    <p class="mb-2">Required for the operation of our Service. They enable basic functions like page navigation and access to secure areas.</p>
                                    <small class="text-muted">You cannot opt-out of these cookies as the Service would not function without them.</small>
                                </div>

                                <div class="cookie-type">
                                    <div class="cookie-header">
                                        <span class="cookie-badge bg-success">Performance</span>
                                        <strong>Analytics Cookies</strong>
                                    </div>
                                    <p class="mb-2">Allow us to recognize and count visitors and see how visitors move around our Service. This helps us improve how our Service works.</p>
                                    <small class="text-muted">These cookies collect information in aggregate form.</small>
                                </div>

                                <div class="cookie-type">
                                    <div class="cookie-header">
                                        <span class="cookie-badge bg-warning">Functional</span>
                                        <strong>Functionality Cookies</strong>
                                    </div>
                                    <p class="mb-2">Used to recognize you when you return to our Service. They enable us to personalize our content and remember your preferences.</p>
                                    <small class="text-muted">These cookies may be set by us or by third-party providers.</small>
                                </div>

                                <div class="cookie-type">
                                    <div class="cookie-header">
                                        <span class="cookie-badge bg-info">Targeting</span>
                                        <strong>Marketing Cookies</strong>
                                    </div>
                                    <p class="mb-2">Track your browsing habits to enable us to show advertising more relevant to you and your interests.</p>
                                    <small class="text-muted">These cookies are usually placed by advertising networks with our permission.</small>
                                </div>
                            </div>

                            <div class="cookie-controls mt-4">
                                <h5 class="h5 mb-3">Cookie Control</h5>
                                <p>You can control and manage cookies in various ways. Most web browsers allow you to control cookies through their settings preferences.</p>

                                <div class="row mt-3">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle text-success me-3 fa-lg"></i>
                                            <div>
                                                <h6 class="h6 mb-1">Browser Settings</h6>
                                                <p class="small mb-0">Most browsers allow you to refuse cookies or delete them.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle text-success me-3 fa-lg"></i>
                                            <div>
                                                <h6 class="h6 mb-1">Cookie Consent Tool</h6>
                                                <p class="small mb-0">Use our cookie consent tool to manage preferences.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cookiePreferences">
                                    <i class="fas fa-cookie-bite me-2"></i> Manage Cookie Preferences
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Contact -->
                <section id="contact" class="privacy-section">
                    <div class="section-header mb-4">
                        <span class="section-number">08</span>
                        <h2 class="h3 mb-3">Contact Us</h2>
                        <div class="section-divider"></div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <p>If you have any questions or concerns about this Privacy Policy or our data practices, please contact us:</p>

                            <div class="row mt-4">
                                <div class="col-md-6 mb-4">
                                    <div class="contact-method">
                                        <div class="contact-icon">
                                            <i class="fas fa-envelope fa-2x text-primary"></i>
                                        </div>
                                        <div class="contact-details">
                                            <h5 class="h6 mb-2">Email</h5>
                                            <p class="mb-0">
                                                <a href="mailto:privacy@darkfibre-crm.test">privacy@darkfibre-crm.test</a><br>
                                                <a href="mailto:data-protection@darkfibre-crm.test">data-protection@darkfibre-crm.test</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="contact-method">
                                        <div class="contact-icon">
                                            <i class="fas fa-phone fa-2x text-success"></i>
                                        </div>
                                        <div class="contact-details">
                                            <h5 class="h6 mb-2">Phone</h5>
                                            <p class="mb-0">
                                                <a href="tel:+254700000000">+254 700 000 000</a><br>
                                                (Data Protection Office)
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="contact-method">
                                        <div class="contact-icon">
                                            <i class="fas fa-map-marker-alt fa-2x text-info"></i>
                                        </div>
                                        <div class="contact-details">
                                            <h5 class="h6 mb-2">Address</h5>
                                            <p class="mb-0">
                                                Data Protection Officer<br>
                                                Dark Fibre CRM<br>
                                                Nairobi, Kenya<br>
                                                P.O. Box 00000-00100
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="contact-method">
                                        <div class="contact-icon">
                                            <i class="fas fa-clock fa-2x text-warning"></i>
                                        </div>
                                        <div class="contact-details">
                                            <h5 class="h6 mb-2">Response Time</h5>
                                            <p class="mb-0">
                                                We aim to respond to all privacy-related inquiries within <strong>72 hours</strong>.<br>
                                                For urgent matters, please mark your email as "URGENT: Privacy".
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Summary Card -->
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h5 class="h5 mb-4"><i class="fas fa-file-alt me-2 text-primary"></i> Policy Summary</h5>

                        <div class="summary-item mb-4">
                            <h6 class="h6 mb-2"><i class="fas fa-calendar me-2 text-success"></i> Effective Date</h6>
                            <p class="mb-0 small">{{ date('F j, Y') }}</p>
                        </div>

                        <div class="summary-item mb-4">
                            <h6 class="h6 mb-2"><i class="fas fa-sync-alt me-2 text-info"></i> Review Frequency</h6>
                            <p class="mb-0 small">Annual review, or as needed for legal changes</p>
                        </div>

                        <div class="summary-item mb-4">
                            <h6 class="h6 mb-2"><i class="fas fa-language me-2 text-warning"></i> Available Languages</h6>
                            <p class="mb-0 small">English (primary), Swahili (coming soon)</p>
                        </div>

                        <div class="summary-item mb-4">
                            <h6 class="h6 mb-2"><i class="fas fa-download me-2 text-danger"></i> Download Policy</h6>
                            <div class="d-grid gap-2">
                                <a href="#" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-file-pdf me-2"></i> PDF Version
                                </a>
                            </div>
                        </div>

                        <hr>

                        <div class="policy-updates">
                            <h6 class="h6 mb-3"><i class="fas fa-history me-2 text-secondary"></i> Update History</h6>
                            <div class="update-item">
                                <div class="update-date">{{ date('F j, Y') }}</div>
                                <div class="update-description">Current version published</div>
                            </div>
                            <div class="update-item">
                                <div class="update-date">{{ date('F j, Y', strtotime('-6 months')) }}</div>
                                <div class="update-description">Updated data retention policies</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Compliance Badges -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4 text-center">
                        <h5 class="h5 mb-4">Compliance Standards</h5>
                        <div class="compliance-badges">
                            <div class="compliance-badge">
                                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                                <div class="badge-label">GDPR</div>
                            </div>
                            <div class="compliance-badge">
                                <i class="fas fa-lock fa-3x text-success mb-3"></i>
                                <div class="badge-label">ISO 27001</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <h5 class="h5 mb-4">Quick Actions</h5>
                        <div class="d-grid gap-2">
                            <a href="{{ route('contact') }}" class="btn btn-outline-primary">
                                <i class="fas fa-question-circle me-2"></i> Ask a Question
                            </a>
                            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#dataRequest">
                                <i class="fas fa-database me-2"></i> Submit Data Request
                            </button>
                            <button class="btn btn-outline-danger" id="exportDataBtn">
                                <i class="fas fa-file-export me-2"></i> Export My Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Cookie Preferences Modal -->
<div class="modal fade" id="cookiePreferences" tabindex="-1" aria-labelledby="cookiePreferencesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="cookiePreferencesLabel"><i class="fas fa-cookie-bite me-2"></i> Cookie Preferences</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-4">Manage your cookie preferences. You can change these settings at any time.</p>

                <div class="cookie-settings">
                    <div class="cookie-setting mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="h6 mb-1">Essential Cookies</h6>
                                <p class="small text-muted mb-0">Required for the website to function</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="essentialCookies" checked disabled>
                                <label class="form-check-label" for="essentialCookies">Always On</label>
                            </div>
                        </div>
                    </div>

                    <div class="cookie-setting mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="h6 mb-1">Performance Cookies</h6>
                                <p class="small text-muted mb-0">Help us improve our website</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="performanceCookies" checked>
                                <label class="form-check-label" for="performanceCookies"></label>
                            </div>
                        </div>
                    </div>

                    <div class="cookie-setting mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="h6 mb-1">Functional Cookies</h6>
                                <p class="small text-muted mb-0">Remember your preferences</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="functionalCookies" checked>
                                <label class="form-check-label" for="functionalCookies"></label>
                            </div>
                        </div>
                    </div>

                    <div class="cookie-setting mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="h6 mb-1">Marketing Cookies</h6>
                                <p class="small text-muted mb-0">Show relevant advertising</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="marketingCookies">
                                <label class="form-check-label" for="marketingCookies"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex mt-4">
                    <button class="btn btn-primary" id="saveCookiePreferences">
                        <i class="fas fa-save me-2"></i> Save Preferences
                    </button>
                    <button class="btn btn-outline-secondary" id="acceptAllCookies">
                        <i class="fas fa-check-circle me-2"></i> Accept All
                    </button>
                    <button class="btn btn-outline-danger" id="rejectAllCookies">
                        <i class="fas fa-ban me-2"></i> Reject All
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Request Modal -->
<div class="modal fade" id="dataRequest" tabindex="-1" aria-labelledby="dataRequestLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="dataRequestLabel"><i class="fas fa-database me-2"></i> Data Request Form</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-4">Submit a request regarding your personal data (access, correction, deletion, etc.)</p>

                <form id="dataRequestForm">
                    <div class="mb-3">
                        <label for="requestType" class="form-label">Request Type *</label>
                        <select class="form-select" id="requestType" required>
                            <option value="" selected disabled>Select request type</option>
                            <option value="access">Access my data</option>
                            <option value="correction">Correct my data</option>
                            <option value="deletion">Delete my data</option>
                            <option value="restriction">Restrict processing</option>
                            <option value="portability">Data portability</option>
                            <option value="objection">Object to processing</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="requestEmail" class="form-label">Your Email *</label>
                        <input type="email" class="form-control" id="requestEmail" required>
                    </div>

                    <div class="mb-3">
                        <label for="requestDetails" class="form-label">Additional Details</label>
                        <textarea class="form-control" id="requestDetails" rows="3" placeholder="Provide additional information to help us process your request..."></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i> Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.privacy-hero {
    position: relative;
    overflow: hidden;
}

.privacy-hero::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 300px;
    background: linear-gradient(45deg, rgba(79, 70, 229, 0.1), transparent);
    border-radius: 50%;
}

.privacy-visualization {
    width: 300px;
    height: 300px;
    position: relative;
}

.shield-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 2;
}

.encryption-ring {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border: 2px solid rgba(79, 70, 229, 0.2);
    border-radius: 50%;
    animation: pulse 3s infinite;
}

.encryption-ring-1 {
    width: 200px;
    height: 200px;
    animation-delay: 0s;
}

.encryption-ring-2 {
    width: 250px;
    height: 250px;
    animation-delay: 1s;
}

.encryption-ring-3 {
    width: 300px;
    height: 300px;
    animation-delay: 2s;
}

@keyframes pulse {
    0% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(1.3);
        opacity: 0;
    }
}

.sticky-nav-wrapper {
    position: sticky;
    top: 80px;
    z-index: 1000;
}

.privacy-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.privacy-nav-link {
    padding: 8px 16px;
    background: rgba(79, 70, 229, 0.1);
    color: #4f46e5;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.privacy-nav-link:hover,
.privacy-nav-link.active {
    background: #4f46e5;
    color: white;
    transform: translateY(-2px);
}

.privacy-section {
    scroll-margin-top: 140px;
}

.section-header {
    position: relative;
    padding-left: 60px;
}

.section-number {
    position: absolute;
    left: 0;
    top: 0;
    font-size: 2.5rem;
    font-weight: 800;
    color: rgba(79, 70, 229, 0.1);
    line-height: 1;
}

.section-divider {
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, #4f46e5, #8b5cf6);
    border-radius: 2px;
}

.data-icon {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.use-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.sharing-table {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
}

.sharing-row {
    display: flex;
    border-bottom: 1px solid #e9ecef;
}

.sharing-row:last-child {
    border-bottom: none;
}

.sharing-party {
    flex: 0 0 200px;
    background: #f8f9fa;
    padding: 1rem;
    border-right: 1px solid #e9ecef;
    display: flex;
    align-items: center;
}

.sharing-purpose {
    flex: 1;
    padding: 1rem;
}

.rights-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(139, 92, 246, 0.1));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.security-feature {
    display: flex;
    align-items: flex-start;
}

.security-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(139, 92, 246, 0.1));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
}

.security-content {
    flex: 1;
}

.cookie-type {
    padding: 1rem;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.cookie-header {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.cookie-badge {
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-right: 0.5rem;
    color: white;
}

.contact-method {
    display: flex;
    align-items: flex-start;
}

.contact-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(139, 92, 246, 0.1));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
}

.contact-details {
    flex: 1;
}

.update-item {
    display: flex;
    margin-bottom: 0.75rem;
}

.update-date {
    flex: 0 0 100px;
    font-weight: 600;
    font-size: 0.875rem;
}

.update-description {
    flex: 1;
    font-size: 0.875rem;
    color: #6c757d;
}

.compliance-badges {
    display: flex;
    justify-content: center;
    gap: 2rem;
}

.compliance-badge {
    text-align: center;
}

.badge-label {
    font-weight: 600;
    color: #4f46e5;
}

@media (max-width: 768px) {
    .privacy-nav {
        justify-content: center;
    }

    .privacy-nav-link {
        font-size: 0.75rem;
        padding: 6px 12px;
    }

    .sharing-row {
        flex-direction: column;
    }

    .sharing-party {
        flex: none;
        border-right: none;
        border-bottom: 1px solid #e9ecef;
    }

    .privacy-visualization {
        width: 250px;
        height: 250px;
        margin-top: 2rem;
    }

    .privacy-hero h1 {
        font-size: 2.5rem;
    }
}

@media (max-width: 576px) {
    .section-header {
        padding-left: 50px;
    }

    .section-number {
        font-size: 2rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for privacy navigation
    document.querySelectorAll('.privacy-nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            // Update active state
            document.querySelectorAll('.privacy-nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');

            // Scroll to section
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

    // Update active nav link on scroll
    const sections = document.querySelectorAll('.privacy-section');
    const navLinks = document.querySelectorAll('.privacy-nav-link');

    function updateActiveNav() {
        let scrollY = window.pageYOffset + 150; // Offset for sticky nav

        sections.forEach(section => {
            const sectionHeight = section.offsetHeight;
            const sectionTop = section.offsetTop;
            const sectionId = section.getAttribute('id');
            const navLink = document.querySelector(`.privacy-nav-link[href="#${sectionId}"]`);

            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                navLinks.forEach(link => link.classList.remove('active'));
                if (navLink) navLink.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', updateActiveNav);
    updateActiveNav(); // Initial call

    // Cookie preferences modal functionality
    const saveCookieBtn = document.getElementById('saveCookiePreferences');
    const acceptAllBtn = document.getElementById('acceptAllCookies');
    const rejectAllBtn = document.getElementById('rejectAllCookies');

    if (saveCookieBtn) {
        saveCookieBtn.addEventListener('click', function() {
            const performance = document.getElementById('performanceCookies').checked;
            const functional = document.getElementById('functionalCookies').checked;
            const marketing = document.getElementById('marketingCookies').checked;

            // In a real application, save these preferences to cookies/localStorage
            localStorage.setItem('cookiePreferences', JSON.stringify({
                performance: performance,
                functional: functional,
                marketing: marketing,
                timestamp: new Date().toISOString()
            }));

            alert('Cookie preferences saved successfully!');
            const modal = bootstrap.Modal.getInstance(document.getElementById('cookiePreferences'));
            if (modal) modal.hide();
        });
    }

    if (acceptAllBtn) {
        acceptAllBtn.addEventListener('click', function() {
            document.getElementById('performanceCookies').checked = true;
            document.getElementById('functionalCookies').checked = true;
            document.getElementById('marketingCookies').checked = true;

            localStorage.setItem('cookiePreferences', JSON.stringify({
                performance: true,
                functional: true,
                marketing: true,
                timestamp: new Date().toISOString()
            }));

            alert('All cookies accepted!');
            const modal = bootstrap.Modal.getInstance(document.getElementById('cookiePreferences'));
            if (modal) modal.hide();
        });
    }

    if (rejectAllBtn) {
        rejectAllBtn.addEventListener('click', function() {
            document.getElementById('performanceCookies').checked = false;
            document.getElementById('functionalCookies').checked = false;
            document.getElementById('marketingCookies').checked = false;

            localStorage.setItem('cookiePreferences', JSON.stringify({
                performance: false,
                functional: false,
                marketing: false,
                timestamp: new Date().toISOString()
            }));

            alert('All non-essential cookies rejected!');
            const modal = bootstrap.Modal.getInstance(document.getElementById('cookiePreferences'));
            if (modal) modal.hide();
        });
    }

    // Data request form
    const dataRequestForm = document.getElementById('dataRequestForm');
    if (dataRequestForm) {
        dataRequestForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
            submitBtn.disabled = true;

            setTimeout(() => {
                alert('Thank you! Your data request has been submitted. We will process it within 30 days as required by GDPR.');

                // Reset form
                dataRequestForm.reset();

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('dataRequest'));
                if (modal) modal.hide();

                // Restore button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 1500);
        });
    }

    // Export data button
    const exportDataBtn = document.getElementById('exportDataBtn');
    if (exportDataBtn) {
        exportDataBtn.addEventListener('click', function() {
            if (confirm('This will export all your personal data from our systems. Continue?')) {
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Preparing export...';
                this.disabled = true;

                setTimeout(() => {
                    alert('Your data export is being prepared. You will receive an email with a download link within 48 hours.');
                    this.innerHTML = '<i class="fas fa-file-export me-2"></i> Export My Data';
                    this.disabled = false;
                }, 2000);
            }
        });
    }

    // Load saved cookie preferences
    const savedPreferences = localStorage.getItem('cookiePreferences');
    if (savedPreferences) {
        try {
            const preferences = JSON.parse(savedPreferences);
            document.getElementById('performanceCookies').checked = preferences.performance;
            document.getElementById('functionalCookies').checked = preferences.functional;
            document.getElementById('marketingCookies').checked = preferences.marketing;
        } catch (e) {
            console.error('Error loading cookie preferences:', e);
        }
    }

    // Animate encryption rings
    const rings = document.querySelectorAll('.encryption-ring');
    rings.forEach((ring, index) => {
        ring.style.animationDelay = `${index * 0.5}s`;
    });
});
</script>
@endpush
