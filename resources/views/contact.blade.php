@extends('layouts.app')

@section('title', 'Contact Us - Dark Fibre CRM')
<link rel="stylesheet" href="{{ asset('css/contact.css') }}">

@section('content')
<!-- Hero Section -->
<section class="contact-hero py-5 mb-5" style="background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-light opacity-75">Home</a></li>
                        <li class="breadcrumb-item active text-light" aria-current="page">Contact Us</li>
                    </ol>
                </nav>
                <h1 class="display-4 fw-bold text-white mb-4">Get in Touch</h1>
                <p class="lead text-light opacity-85 mb-4">We're here to help you with your fibre infrastructure needs. Reach out to us for inquiries, support, or partnership opportunities.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#contact-form" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-paper-plane me-2"></i> Send Message
                    </a>
                    <a href="tel:+254700000000" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-phone me-2"></i> Call Now
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="contact-visualization position-relative">
                    <div class="contact-node main-node">
                        <i class="fas fa-headset fa-3x text-primary"></i>
                        <div class="node-label">Support</div>
                    </div>
                    <div class="contact-node sales-node">
                        <i class="fas fa-handshake fa-3x text-success"></i>
                        <div class="node-label">Sales</div>
                    </div>
                    <div class="contact-node tech-node">
                        <i class="fas fa-cogs fa-3x text-info"></i>
                        <div class="node-label">Technical</div>
                    </div>
                    <div class="contact-node partner-node">
                        <i class="fas fa-users fa-3x text-warning"></i>
                        <div class="node-label">Partnership</div>
                    </div>
                    <div class="pulse-ring"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2">Contact Channels</span>
                <h2 class="display-5 fw-bold mb-4">Multiple Ways to Connect</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">Choose the most convenient way to get in touch with our team. We're always ready to assist.</p>
            </div>
        </div>

        <!-- Contact Cards -->
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 text-center hover-lift">
                    <div class="card-body p-4">
                        <div class="contact-icon-wrapper mb-4 mx-auto">
                            <i class="fas fa-phone-alt fa-3x text-primary"></i>
                        </div>
                        <h4 class="h5 mb-3">Phone Support</h4>
                        <p class="text-muted mb-3">Available 24/7 for urgent issues and emergencies.</p>
                        <div class="contact-info">
                            <a href="tel:+254700000000" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-phone me-1"></i> +254 700 000 000
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 text-center hover-lift">
                    <div class="card-body p-4">
                        <div class="contact-icon-wrapper mb-4 mx-auto">
                            <i class="fas fa-envelope fa-3x text-success"></i>
                        </div>
                        <h4 class="h5 mb-3">Email</h4>
                        <p class="text-muted mb-3">Get detailed responses within 2 hours during business hours.</p>
                        <div class="contact-info">
                            <a href="mailto:support@darkfibre-crm.test" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-envelope me-1"></i> support@darkfibre-crm.test
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 text-center hover-lift">
                    <div class="card-body p-4">
                        <div class="contact-icon-wrapper mb-4 mx-auto">
                            <i class="fas fa-comments fa-3x text-info"></i>
                        </div>
                        <h4 class="h5 mb-3">Live Chat</h4>
                        <p class="text-muted mb-3">Chat instantly with our support team during business hours.</p>
                        <div class="contact-info">
                            <button class="btn btn-outline-info btn-sm" id="startChatBtn">
                                <i class="fas fa-comment-dots me-1"></i> Start Chat
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 text-center hover-lift">
                    <div class="card-body p-4">
                        <div class="contact-icon-wrapper mb-4 mx-auto">
                            <i class="fas fa-calendar-alt fa-3x text-warning"></i>
                        </div>
                        <h4 class="h5 mb-3">Schedule Call</h4>
                        <p class="text-muted mb-3">Book a meeting with our experts at your convenience.</p>
                        <div class="contact-info">
                            <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                                <i class="fas fa-calendar-check me-1"></i> Schedule Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form & Info -->
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg" id="contact-form">
                    <div class="card-header bg-primary text-white py-3">
                        <h3 class="h4 mb-0"><i class="fas fa-paper-plane me-2"></i> Send Us a Message</h3>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <form id="contactForm" method="POST" action="{{ route('contact.submit') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-user text-primary"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0" id="name" name="name" required placeholder="Enter your full name">
                                    </div>
                                    <div class="form-text">Please enter your full name</div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-envelope text-primary"></i>
                                        </span>
                                        <input type="email" class="form-control border-start-0" id="email" name="email" required placeholder="your.email@example.com">
                                    </div>
                                    <div class="form-text">We'll never share your email</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="company" class="form-label fw-bold">Company Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-building text-primary"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="company" name="company" placeholder="Your company (optional)">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="subject" class="form-label fw-bold">Subject <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-tag text-primary"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="subject" name="subject" required placeholder="What is this regarding?">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="category" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="" selected disabled>Select a category</option>
                                    <option value="sales">Sales Inquiry</option>
                                    <option value="support">Technical Support</option>
                                    <option value="partnership">Partnership Opportunity</option>
                                    <option value="billing">Billing Question</option>
                                    <option value="feedback">Feedback & Suggestions</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="message" class="form-label fw-bold">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="6" required placeholder="Please provide details about your inquiry..."></textarea>
                                <div class="form-text">Maximum 2000 characters</div>
                                <div class="mt-2">
                                    <small class="text-muted" id="charCount">0/2000 characters</small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                                    <label class="form-check-label" for="newsletter">
                                        Subscribe to our newsletter for updates and tips
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-paper-plane me-2"></i> Send Message
                                </button>
                                <button type="reset" class="btn btn-outline-secondary btn-lg px-5">
                                    <i class="fas fa-redo me-2"></i> Reset Form
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Contact Information & Map -->
            <div class="col-lg-4">
                <!-- Office Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h4 class="h5 mb-0"><i class="fas fa-map-marker-alt me-2 text-primary"></i> Our Office</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="office-info mb-4">
                            <div class="d-flex mb-3">
                                <div class="office-icon me-3">
                                    <i class="fas fa-map-pin fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="h6 mb-1">Address</h5>
                                    <p class="mb-0 text-muted">Nairobi, Kenya<br>P.O. Box 00000-00100</p>
                                </div>
                            </div>

                            <div class="d-flex mb-3">
                                <div class="office-icon me-3">
                                    <i class="fas fa-clock fa-2x text-success"></i>
                                </div>
                                <div>
                                    <h5 class="h6 mb-1">Business Hours</h5>
                                    <p class="mb-0 text-muted">
                                        Monday - Friday: 8:00 AM - 6:00 PM<br>
                                        Saturday: 9:00 AM - 1:00 PM<br>
                                        Sunday: Closed
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex mb-3">
                                <div class="office-icon me-3">
                                    <i class="fas fa-phone fa-2x text-info"></i>
                                </div>
                                <div>
                                    <h5 class="h6 mb-1">Phone Numbers</h5>
                                    <p class="mb-0 text-muted">
                                        Main: +254 700 000 000<br>
                                        Support: +254 711 000 000<br>
                                        Sales: +254 722 000 000
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex">
                                <div class="office-icon me-3">
                                    <i class="fas fa-envelope fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <h5 class="h6 mb-1">Email Addresses</h5>
                                    <p class="mb-0 text-muted">
                                        General: info@darkfibre-crm.test<br>
                                        Support: support@darkfibre-crm.test<br>
                                        Sales: sales@darkfibre-crm.test
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Contact -->
                        <div class="quick-contact">
                            <h5 class="h6 mb-3">Quick Contact</h5>
                            <div class="d-grid gap-2">
                                <a href="https://wa.me/254700000000" class="btn btn-success" target="_blank">
                                    <i class="fab fa-whatsapp me-2"></i> WhatsApp Us
                                </a>
                                <a href="skype:darkfibre.crm?call" class="btn btn-info">
                                    <i class="fab fa-skype me-2"></i> Skype Call
                                </a>
                                <a href="mailto:support@darkfibre-crm.test" class="btn btn-warning">
                                    <i class="fas fa-envelope me-2"></i> Quick Email
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Placeholder -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h4 class="h5 mb-0"><i class="fas fa-map me-2 text-primary"></i> Our Location</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="map-placeholder position-relative">
                            <div class="map-overlay d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                                    <h5 class="mb-2">Nairobi, Kenya</h5>
                                    <p class="small mb-0">Click to view on Google Maps</p>
                                </div>
                            </div>
                            <img src="https://images.unsplash.com/photo-1514454529242-9e4677563e7b?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80"
                                 alt="Nairobi Map"
                                 class="img-fluid w-100">
                        </div>
                    </div>
                </div>

                <!-- Response Time -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4 text-center">
                        <div class="response-time">
                            <i class="fas fa-bolt fa-2x text-primary mb-3"></i>
                            <h5 class="h6 mb-2">Average Response Time</h5>
                            <div class="display-6 fw-bold text-success mb-2">2.3 hours</div>
                            <p class="small text-muted mb-0">For all inquiries during business hours</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="row mt-5 pt-5 border-top">
            <div class="col-12 text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2">FAQ</span>
                <h2 class="display-5 fw-bold mb-4">Frequently Asked Questions</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">Quick answers to common questions about contacting us.</p>
            </div>

            <div class="col-lg-10 mx-auto">
                <div class="accordion" id="contactFAQ">
                    @foreach([
                        ['q' => 'What is your typical response time?', 'a' => 'We aim to respond to all inquiries within 2 hours during business hours (8 AM - 6 PM EAT). Emergency support tickets receive priority attention.'],
                        ['q' => 'Do you offer 24/7 support?', 'a' => 'Yes, we offer 24/7 emergency support for critical network issues. Standard support is available during business hours.'],
                        ['q' => 'Can I schedule a product demo?', 'a' => 'Absolutely! Use the "Schedule Call" button or contact our sales team to arrange a personalized demo at your convenience.'],
                        ['q' => 'Do you have offices outside Kenya?', 'a' => 'Currently, our main office is in Nairobi, Kenya, but we serve clients across Africa and provide remote support globally.'],
                        ['q' => 'What information should I include in my support request?', 'a' => 'Please include your company name, contact details, specific issue description, error messages (if any), and steps to reproduce the problem.'],
                        ['q' => 'How can I become a partner?', 'a' => 'We welcome partnership inquiries. Please select "Partnership Opportunity" in the contact form category or email partners@darkfibre-crm.test.'],
                    ] as $index => $faq)
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}">
                                {{ $faq['q'] }}
                            </button>
                        </h3>
                        <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#contactFAQ">
                            <div class="accordion-body">
                                {{ $faq['a'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Schedule Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="scheduleModalLabel"><i class="fas fa-calendar-alt me-2"></i> Schedule a Call</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-4">Select a convenient time for a call with our team. We'll confirm the appointment via email.</p>

                <form id="scheduleForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="scheduleName" class="form-label">Name *</label>
                            <input type="text" class="form-control" id="scheduleName" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="scheduleEmail" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="scheduleEmail" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="scheduleTopic" class="form-label">Discussion Topic *</label>
                        <select class="form-select" id="scheduleTopic" required>
                            <option value="" selected disabled>Select topic</option>
                            <option value="demo">Product Demo</option>
                            <option value="sales">Sales Inquiry</option>
                            <option value="technical">Technical Discussion</option>
                            <option value="partnership">Partnership Meeting</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="scheduleDate" class="form-label">Preferred Date *</label>
                        <input type="date" class="form-control" id="scheduleDate" required min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-4">
                        <label for="scheduleTime" class="form-label">Preferred Time *</label>
                        <select class="form-select" id="scheduleTime" required>
                            <option value="" selected disabled>Select time slot</option>
                            <option value="09:00">9:00 AM - 10:00 AM</option>
                            <option value="10:00">10:00 AM - 11:00 AM</option>
                            <option value="11:00">11:00 AM - 12:00 PM</option>
                            <option value="14:00">2:00 PM - 3:00 PM</option>
                            <option value="15:00">3:00 PM - 4:00 PM</option>
                            <option value="16:00">4:00 PM - 5:00 PM</option>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-calendar-check me-2"></i> Schedule Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Chat Widget (Simulated) -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11; display: none;" id="chatWidget">
    <div class="card shadow-lg border-0" style="width: 350px;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-headset me-2"></i>
                <strong>Live Support</strong>
            </div>
            <button type="button" class="btn-close btn-close-white" id="closeChatBtn"></button>
        </div>
        <div class="card-body p-0">
            <div class="chat-messages p-3" style="height: 300px; overflow-y: auto;" id="chatMessages">
                <div class="chat-message support">
                    <div class="message-content">Hello! How can I help you today?</div>
                    <div class="message-time">Just now</div>
                </div>
            </div>
            <div class="chat-input p-3 border-top">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Type your message..." id="chatInput">
                    <button class="btn btn-primary" type="button" id="sendChatBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <small class="text-muted mt-2 d-block">Typically replies in under 5 minutes</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.contact-hero {
    position: relative;
    overflow: hidden;
}

.contact-hero::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 300px;
    background: linear-gradient(45deg, rgba(79, 70, 229, 0.1), transparent);
    border-radius: 50%;
}

.contact-visualization {
    width: 400px;
    height: 400px;
    margin: 0 auto;
    position: relative;
}

.contact-node {
    position: absolute;
    width: 80px;
    height: 80px;
    background: white;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    z-index: 2;
}

.contact-node:hover {
    transform: scale(1.1);
}

.contact-node i {
    margin-bottom: 5px;
}

.node-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #4f46e5;
}

.main-node {
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100px;
    height: 100px;
}

.sales-node {
    top: 20%;
    right: 20%;
}

.tech-node {
    bottom: 20%;
    right: 20%;
}

.partner-node {
    bottom: 20%;
    left: 20%;
}

.pulse-ring {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 200px;
    height: 200px;
    border: 2px solid rgba(79, 70, 229, 0.2);
    border-radius: 50%;
    animation: pulse 3s infinite;
}

@keyframes pulse {
    0% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(1.5);
        opacity: 0;
    }
}

.contact-icon-wrapper {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(139, 92, 246, 0.1));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.office-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.map-placeholder {
    position: relative;
    height: 300px;
    overflow: hidden;
}

.map-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    color: white;
    opacity: 0;
    transition: opacity 0.3s ease;
    cursor: pointer;
}

.map-placeholder:hover .map-overlay {
    opacity: 1;
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

.chat-message {
    margin-bottom: 1rem;
    padding: 0.75rem;
    border-radius: 10px;
    max-width: 80%;
}

.chat-message.support {
    background: #e3f2fd;
    margin-right: auto;
}

.message-content {
    margin-bottom: 0.25rem;
}

.message-time {
    font-size: 0.75rem;
    color: #6c757d;
}

@media (max-width: 768px) {
    .contact-visualization {
        width: 300px;
        height: 300px;
        margin-top: 2rem;
    }

    .contact-node {
        width: 60px;
        height: 60px;
    }

    .contact-node i {
        font-size: 1.5rem;
    }

    .main-node {
        width: 80px;
        height: 80px;
    }

    .pulse-ring {
        width: 150px;
        height: 150px;
    }

    .contact-hero h1 {
        font-size: 2.5rem;
    }
}

.form-control:focus, .form-select:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for message textarea
    const messageTextarea = document.getElementById('message');
    const charCount = document.getElementById('charCount');

    if (messageTextarea && charCount) {
        messageTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = `${length}/2000 characters`;

            if (length > 1800) {
                charCount.classList.add('text-warning');
            } else {
                charCount.classList.remove('text-warning');
            }

            if (length > 2000) {
                charCount.classList.add('text-danger');
            } else {
                charCount.classList.remove('text-danger');
            }
        });
    }

    // Contact form submission
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Get form data
            const formData = new FormData(this);
            const formObject = Object.fromEntries(formData);

            // In a real application, you would submit to your backend
            // For now, simulate a successful submission
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
            submitBtn.disabled = true;

            setTimeout(() => {
                // Show success message
                alert('Thank you! Your message has been sent successfully. We will respond within 2 hours.');

                // Reset form
                contactForm.reset();
                if (charCount) {
                    charCount.textContent = '0/2000 characters';
                }

                // Restore button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 1500);
        });
    }

    // Schedule form submission
    const scheduleForm = document.getElementById('scheduleForm');
    if (scheduleForm) {
        scheduleForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Scheduling...';
            submitBtn.disabled = true;

            setTimeout(() => {
                // Show success message
                alert('Appointment scheduled successfully! You will receive a confirmation email shortly.');

                // Reset form
                scheduleForm.reset();

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('scheduleModal'));
                if (modal) modal.hide();

                // Restore button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 1500);
        });
    }

    // Live chat functionality
    const startChatBtn = document.getElementById('startChatBtn');
    const chatWidget = document.getElementById('chatWidget');
    const closeChatBtn = document.getElementById('closeChatBtn');
    const sendChatBtn = document.getElementById('sendChatBtn');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');

    if (startChatBtn && chatWidget) {
        startChatBtn.addEventListener('click', function() {
            chatWidget.style.display = 'block';

            // Add welcome message if not already added
            if (chatMessages.children.length === 1) {
                setTimeout(() => {
                    addSupportMessage("Welcome to Dark Fibre CRM support! How can I assist you today?");
                }, 500);
            }
        });
    }

    if (closeChatBtn && chatWidget) {
        closeChatBtn.addEventListener('click', function() {
            chatWidget.style.display = 'none';
        });
    }

    function addSupportMessage(text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message support';
        messageDiv.innerHTML = `
            <div class="message-content">${text}</div>
            <div class="message-time">${getCurrentTime()}</div>
        `;
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function addUserMessage(text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message user';
        messageDiv.style.marginLeft = 'auto';
        messageDiv.style.background = '#d1ecf1';
        messageDiv.innerHTML = `
            <div class="message-content">${text}</div>
            <div class="message-time">${getCurrentTime()}</div>
        `;
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function getCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    if (sendChatBtn && chatInput) {
        sendChatBtn.addEventListener('click', function() {
            const message = chatInput.value.trim();
            if (message) {
                addUserMessage(message);
                chatInput.value = '';

                // Simulate support response
                setTimeout(() => {
                    const responses = [
                        "Thanks for your message. I'll look into that for you.",
                        "I understand. Can you provide more details about the issue?",
                        "That's a great question! Let me check that for you.",
                        "I can help with that. Please give me a moment to review.",
                        "I'll connect you with our technical team for further assistance."
                    ];
                    const randomResponse = responses[Math.floor(Math.random() * responses.length)];
                    addSupportMessage(randomResponse);
                }, 1000);
            }
        });

        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendChatBtn.click();
            }
        });
    }

    // Map click handler
    const mapPlaceholder = document.querySelector('.map-placeholder');
    if (mapPlaceholder) {
        mapPlaceholder.addEventListener('click', function() {
            window.open('https://www.google.com/maps?q=Nairobi+Kenya', '_blank');
        });
    }

    // Animate contact nodes
    const contactNodes = document.querySelectorAll('.contact-node');
    contactNodes.forEach((node, index) => {
        node.style.animationDelay = `${index * 0.2}s`;
    });
});
</script>
@endpush
