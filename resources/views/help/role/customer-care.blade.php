@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-green text-white">
        <h4 class="mb-0">
            <i class="fas fa-headset me-2"></i>
            Customer Care Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-kp-success">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Customer Care Overview:</strong> Manage customer relationships, handle inquiries, and ensure customer satisfaction.
        </div>

        <h3>Key Responsibilities</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-ticket-alt text-kp-blue"></i> Support Ticket Management</h5>
                        <p>Respond to customer issues and track resolution progress</p>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-comments text-info"></i> Customer Communication</h5>
                        <p>Handle inquiries via phone, email, and WeChat</p>
                    </div>
                    <div class="col-md-6 mt-3">
                        <h5><i class="fas fa-chart-line text-kp-green"></i> Satisfaction Monitoring</h5>
                        <p>Track customer satisfaction scores (Current: 100%)</p>
                    </div>
                    <div class="col-md-6 mt-3">
                        <h5><i class="fas fa-file-signature text-kp-yellow"></i> Issue Escalation</h5>
                        <p>Escalate complex issues to technical teams</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Support Ticket Workflow</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <i class="fas fa-inbox fa-2x text-kp-blue"></i>
                        <p>1. Ticket Received</p>
                    </div>
                    <div class="col-md-1">→</div>
                    <div class="col-md-3">
                        <i class="fas fa-clipboard-list fa-2x text-info"></i>
                        <p>2. Categorize & Prioritize</p>
                    </div>
                    <div class="col-md-1">→</div>
                    <div class="col-md-3">
                        <i class="fas fa-tools fa-2x text-kp-yellow"></i>
                        <p>3. Investigate & Resolve</p>
                    </div>
                    <div class="col-md-1">→</div>
                    <div class="col-md-3">
                        <i class="fas fa-check-circle fa-2x text-kp-green"></i>
                        <p>4. Close & Follow Up</p>
                    </div>
                </div>
            </div>
        </div>

        <h3>SLA Guidelines</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>Priority</th><th>Response Time</th><th>Resolution Time</th><th>Example</th></tr>
                </thead>
                <tbody>
                    <tr><td class="bg-danger text-white">Critical</td><td>15 minutes</td><td>4 hours</td><td>Major outage</td></tr>
                    <tr><td class="bg-kp-yellow">High</td><td>1 hour</td><td>8 hours</td><td>Performance degradation</td></tr>
                    <tr><td class="bg-info text-white">Medium</td><td>4 hours</td><td>24 hours</td><td>Billing inquiry</td></tr>
                    <tr><td class="bg-secondary text-white">Low</td><td>24 hours</td><td>72 hours</td><td>General question</td></tr>
                </tbody>
            </table>
        </div>

        <h3>Customer Communication Channels</h3>
        <div class="row mb-4">
            <div class="col-md-3 text-center">
                <i class="fas fa-phone-alt fa-2x text-kp-blue"></i>
                <p>Phone Support<br><small>020 3201 000</small></p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-envelope fa-2x text-kp-green"></i>
                <p>Email Support<br><small>support@darkfibre.co.ke</small></p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fab fa-whatsapp fa-2x text-kp-green"></i>
                <p>WhatsApp<br><small>0703 070707</small></p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fab fa-weixin fa-2x text-kp-blue"></i>
                <p>WeChat<br><small>DarkFibre_Support</small></p>
            </div>
        </div>

        <h3>Best Practices</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ul>
                    <li><strong>Acknowledge quickly</strong> - Respond to tickets within SLA</li>
                    <li><strong>Be empathetic</strong> - Understand customer frustration</li>
                    <li><strong>Document everything</strong> - Keep detailed notes for follow-up</li>
                    <li><strong>Follow up</strong> - Confirm resolution with customer</li>
                    <li><strong>Escalate appropriately</strong> - Know when to involve technical teams</li>
                </ul>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Tip:</strong> Use the "Customer Insights" module to view customer history and identify recurring issues.
        </div>

        <h3>Quick Links</h3>
        <div class="row">
            <div class="col-md-6">
                <a href="{{ url('/tickets') }}" class="btn btn-outline-kp-primary w-100 mb-2">
                    <i class="fas fa-ticket-alt"></i> Manage Tickets
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ url('/customers') }}" class="btn btn-outline-kp-success w-100 mb-2">
                    <i class="fas fa-users"></i> Customer List
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
