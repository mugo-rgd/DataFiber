@extends('layouts.help')

@section('help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-green text-white">
        <h4 class="mb-0">
            <i class="fas fa-headset me-2"></i>
            Contact Support
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Our support team is ready to assist you with any questions or issues.
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-phone-alt fa-3x text-kp-blue mb-3"></i>
                        <h5>Phone Support</h5>
                        <p class="mb-1"><strong>General Support:</strong><br>020 3201 000</p>
                        <p class="mb-1"><strong>Emergency (24/7):</strong><br>0703 070707</p>
                        <small class="text-muted">Available Mon-Fri, 8am-5pm</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-envelope fa-3x text-kp-green mb-3"></i>
                        <h5>Email Support</h5>
                        <p class="mb-1"><strong>Technical Support:</strong><br><a href="mailto:support@darkfibre.co.ke">support@darkfibre.co.ke</a></p>
                        <p class="mb-1"><strong>Compliance:</strong><br><a href="mailto:compliance@darkfibre.co.ke">compliance@darkfibre.co.ke</a></p>
                        <p class="mb-1"><strong>Billing:</strong><br><a href="mailto:finance@darkfibre.co.ke">finance@darkfibre.co.ke</a></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fab fa-whatsapp fa-2x text-kp-green mb-2"></i>
                        <h6>WhatsApp</h6>
                        <p class="mb-0">0703 070707</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fab fa-weixin fa-2x text-kp-blue mb-2"></i>
                        <h6>WeChat</h6>
                        <p class="mb-0">DarkFibre_Support</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x text-info mb-2"></i>
                        <h6>SLA Response</h6>
                        <p class="mb-0">Within 1 hour</p>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="mt-4">Submit a Support Ticket</h3>
        <div class="card mb-4">
            <div class="card-body">
                <p>For faster resolution, submit a ticket directly through the system:</p>
                <ol>
                    <li>Go to <strong>Tickets</strong> from the main menu</li>
                    <li>Click <strong>"New Ticket"</strong></li>
                    <li>Select the issue category</li>
                    <li>Describe your issue in detail</li>
                    <li>Attach screenshots if applicable</li>
                    <li>Click <strong>"Submit"</strong></li>
                </ol>
                <a href="{{ url('/tickets') }}" class="btn btn-kp-primary">
                    <i class="fas fa-ticket-alt"></i> Create Support Ticket
                </a>
            </div>
        </div>

        <h3>Physical Address</h3>
        <div class="card mb-4">
            <div class="card-body">
                <p>
                    <strong>DarkFibre CRM Support Office</strong><br>
                    Stima Plaza, 8th Floor<br>
                    Kolobot Road<br>
                    Nairobi, Kenya
                </p>
                <p>
                    <strong>Hours:</strong> Monday-Friday, 8:00 AM - 5:00 PM<br>
                    <strong>Weekend:</strong> Closed (emergency support only)
                </p>
            </div>
        </div>

        <div class="alert alert-kp-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Emergency Support:</strong> For critical outages or security incidents, call our 24/7 emergency line at <strong>0703 070707</strong>.
        </div>

    </div>
</div>
@endsection
