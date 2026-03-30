@extends('layouts.app')

@section('title', 'Terms of Service - Dark Fibre CRM')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Page Header -->
            <div class="page-header mb-5">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Terms of Service</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold mb-3">Terms of Service</h1>
                <p class="lead text-muted">Last updated: {{ date('F j, Y') }}</p>
            </div>

            <!-- Terms Content -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4 p-lg-5">
                    <!-- Introduction -->
                    <section class="mb-5">
                        <h2 class="h4 mb-3 text-primary">1. Introduction</h2>
                        <p>Welcome to Dark Fibre CRM ("we," "our," or "us"). These Terms of Service ("Terms") govern your access to and use of our fibre infrastructure management platform, services, and applications (collectively, the "Service").</p>
                        <p>By accessing or using our Service, you agree to be bound by these Terms. If you disagree with any part of these Terms, you may not access the Service.</p>
                    </section>

                    <!-- Account Terms -->
                    <section class="mb-5">
                        <h2 class="h4 mb-3 text-primary">2. Account Terms</h2>
                        <div class="ms-3">
                            <h3 class="h5 mb-2">2.1 Eligibility</h3>
                            <p>You must be at least 18 years old to use our Service. By using the Service, you represent that you are at least 18 and have the legal capacity to enter into these Terms.</p>

                            <h3 class="h5 mb-2">2.2 Account Security</h3>
                            <p>You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. You agree to notify us immediately of any unauthorized use of your account.</p>

                            <h3 class="h5 mb-2">2.3 Account Information</h3>
                            <p>You must provide accurate, complete, and current information when creating an account and keep it updated at all times.</p>
                        </div>
                    </section>

                    <!-- Service Usage -->
                    <section class="mb-5">
                        <h2 class="h4 mb-3 text-primary">3. Acceptable Use</h2>
                        <p>You agree not to misuse the Service. Prohibited activities include:</p>
                        <ul>
                            <li>Violating any applicable laws or regulations</li>
                            <li>Attempting to breach or circumventing security measures</li>
                            <li>Using the Service to distribute malware or harmful code</li>
                            <li>Reverse engineering, decompiling, or disassembling the Service</li>
                            <li>Interfering with other users' access to the Service</li>
                            <li>Uploading or transmitting unlawful, defamatory, or infringing content</li>
                            <li>Using the Service for unauthorized commercial purposes</li>
                        </ul>
                    </section>

                    <!-- Data and Privacy -->
                    <section class="mb-5">
                        <h2 class="h4 mb-3 text-primary">4. Data and Privacy</h2>
                        <p>Our <a href="{{ route('privacy') }}">Privacy Policy</a> explains how we collect, use, and protect your data. By using our Service, you agree to the collection and use of information in accordance with our Privacy Policy.</p>
                        <p>You retain all rights to your data. We only process your data to provide and improve the Service.</p>
                    </section>

                    <!-- Intellectual Property -->
                    <section class="mb-5">
                        <h2 class="h4 mb-3 text-primary">5. Intellectual Property</h2>
                        <p>The Service and its original content, features, and functionality are owned by Dark Fibre CRM and are protected by international copyright, trademark, and other intellectual property laws.</p>
                        <p>You may not reproduce, distribute, modify, or create derivative works of any part of the Service without our express written permission.</p>
                    </section>

                    <!-- Termination -->
                    <section class="mb-5">
                        <h2 class="h4 mb-3 text-primary">6. Termination</h2>
                        <p>We may terminate or suspend your account and access to the Service immediately, without prior notice or liability, for any reason, including if you breach these Terms.</p>
                        <p>Upon termination, your right to use the Service will immediately cease. All provisions of these Terms which by their nature should survive termination shall survive.</p>
                    </section>

                    <!-- Disclaimer -->
                    <section class="mb-5">
                        <h2 class="h4 mb-3 text-primary">7. Disclaimer</h2>
                        <p>The Service is provided "as is" and "as available" without warranties of any kind, either express or implied. We do not warrant that:</p>
                        <ul>
                            <li>The Service will be uninterrupted or error-free</li>
                            <li>The Service will meet your specific requirements</li>
                            <li>The results from using the Service will be accurate or reliable</li>
                            <li>The quality of the Service will meet your expectations</li>
                        </ul>
                    </section>

                    <!-- Limitation of Liability -->
                    <section class="mb-5">
                        <h2 class="h4 mb-3 text-primary">8. Limitation of Liability</h2>
                        <p>To the maximum extent permitted by law, Dark Fibre CRM shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses.</p>
                    </section>

                    <!-- Changes to Terms -->
                    <section class="mb-5">
                        <h2 class="h4 mb-3 text-primary">9. Changes to Terms</h2>
                        <p>We reserve the right to modify or replace these Terms at any time. We will provide notice of significant changes by posting the new Terms on this page and updating the "Last updated" date.</p>
                        <p>Your continued use of the Service after any changes constitutes acceptance of the new Terms.</p>
                    </section>

                    <!-- Contact Information -->
                    <section class="mb-4">
                        <h2 class="h4 mb-3 text-primary">10. Contact Us</h2>
                        <p>If you have any questions about these Terms, please contact us:</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-envelope me-2 text-primary"></i> legal@darkfibre-crm.test</li>
                            <li><i class="fas fa-phone me-2 text-primary"></i> +254 700 000 000</li>
                            <li><i class="fas fa-map-marker-alt me-2 text-primary"></i> Nairobi, Kenya</li>
                        </ul>
                    </section>
                </div>
            </div>

            <!-- Acceptance Card -->
            <div class="card border-primary">
                <div class="card-body text-center p-4">
                    <h3 class="h5 mb-3 text-primary">Acceptance of Terms</h3>
                    <p class="mb-0">By using Dark Fibre CRM, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service.</p>
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

.card {
    border-radius: 10px;
}

.card h2, .card h3 {
    border-left: 4px solid #4f46e5;
    padding-left: 1rem;
}

.card ul {
    padding-left: 1.5rem;
}

.card ul li {
    margin-bottom: 0.5rem;
}

.card a {
    color: #4f46e5;
    text-decoration: none;
}

.card a:hover {
    text-decoration: underline;
}
</style>
@endpush
