@extends('layouts.help')

@section('help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-green text-white">
        <h4 class="mb-0">
            <i class="fas fa-users me-2"></i>
            Customer Management Guide for Account Managers
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-kp-success">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Your Portfolio:</strong> You currently manage <strong>7 active customers</strong>. This guide will help you effectively manage your customer relationships.
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center bg-kp-blue text-white">
                    <div class="card-body">
                        <h2 class="mb-0">7</h2>
                        <p class="mb-0">Total Customers</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-kp-yellow text-dark">
                    <div class="card-body">
                        <h2 class="mb-0">0</h2>
                        <p class="mb-0">Open Tickets</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-danger text-white">
                    <div class="card-body">
                        <h2 class="mb-0">0</h2>
                        <p class="mb-0">Pending Payments</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-kp-green text-white">
                    <div class="card-body">
                        <h2 class="mb-0">100%</h2>
                        <p class="mb-0">Satisfaction Rate</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer List Section -->
        <h3><i class="fas fa-list me-2"></i> Your Customers</h3>
        <div class="card mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Customer Name</th>
                                <th>Contact Email</th>
                                <th>Phone</th>
                                <th>Open Tickets</th>
                                <th>Pending Payments</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>KENYA EDUCATION NETWORK</strong></td>
                                <td>jopuck@kenet.or.ke</td>
                                <td>254704569074</td>
                                <td><span class="badge bg-kp-green">0</span></td>
                                <td><span class="badge bg-kp-green">$0</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-kp-primary" onclick="viewCustomer(1)">View</button>
                                    <button class="btn btn-sm btn-outline-info" onclick="contactCustomer(1)">Contact</button>
                                 </td>
                            </tr>
                            <tr>
                                <td><strong>NOVIA EAST AFRICA LIMITED</strong></td>
                                <td>matin.maina@gmail.com</td>
                                <td>254726000000</td>
                                <td><span class="badge bg-kp-green">0</span></td>
                                <td><span class="badge bg-kp-green">$0</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-kp-primary">View</button>
                                    <button class="btn btn-sm btn-outline-info">Contact</button>
                                 </td>
                            </tr>
                            <tr>
                                <td><strong>ATIS TELCOM LIMITED</strong></td>
                                <td>washington.zeddy@atistelcom.com</td>
                                <td>254727000000</td>
                                <td><span class="badge bg-kp-green">0</span></td>
                                <td><span class="badge bg-kp-green">$0</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-kp-primary">View</button>
                                    <button class="btn btn-sm btn-outline-info">Contact</button>
                                 </td>
                            </tr>
                            <tr>
                                <td><strong>KENYA AIRWAYS PLC</strong></td>
                                <td>Evans.Ligare@kenya-airways.com</td>
                                <td>254742000000</td>
                                <td><span class="badge bg-kp-green">0</span></td>
                                <td><span class="badge bg-kp-green">$0</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-kp-primary">View</button>
                                    <button class="btn btn-sm btn-outline-info">Contact</button>
                                 </td>
                            </tr>
                            <tr>
                                <td><strong>KEMNET TECHNOLOGIES LIMITED</strong></td>
                                <td>alex@kemnet.co.ke</td>
                                <td>254721000000</td>
                                <td><span class="badge bg-kp-green">0</span></td>
                                <td><span class="badge bg-kp-green">$0</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-kp-primary">View</button>
                                    <button class="btn btn-sm btn-outline-info">Contact</button>
                                 </td>
                            </tr>
                            <tr>
                                <td><strong>SEACOM KENYA LIMITED</strong></td>
                                <td>peter.ouko@seacom.com</td>
                                <td>254722000000</td>
                                <td><span class="badge bg-kp-green">0</span></td>
                                <td><span class="badge bg-kp-green">$0</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-kp-primary">View</button>
                                    <button class="btn btn-sm btn-outline-info">Contact</button>
                                 </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Customer Management Sections -->
        <div class="row">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-kp-blue text-white">
                        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Adding a New Customer</h5>
                    </div>
                    <div class="card-body">
                        <ol>
                            <li>Go to <strong>My Customers</strong> from the dashboard</li>
                            <li>Click <strong>"Add Customer"</strong> button</li>
                            <li>Fill in customer details:
                                <ul>
                                    <li>Company Name</li>
                                    <li>Contact Person</li>
                                    <li>Email Address</li>
                                    <li>Phone Number</li>
                                    <li>Physical Address</li>
                                </ul>
                            </li>
                            <li>Assign to yourself as account manager</li>
                            <li>Click <strong>"Save Customer"</strong></li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Managing Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <ol>
                            <li>Click on any customer name to view details</li>
                            <li>You can:
                                <ul>
                                    <li>✏️ Edit contact information</li>
                                    <li>📋 View lease history</li>
                                    <li>💰 Check payment status</li>
                                    <li>🎫 View support tickets</li>
                                    <li>📊 Generate customer reports</li>
                                </ul>
                            </li>
                            <li>Click <strong>"Update"</strong> to save changes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-kp-yellow text-dark">
                        <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Managing Support Tickets</h5>
                    </div>
                    <div class="card-body">
                        <h6>Viewing Customer Tickets:</h6>
                        <ol>
                            <li>Go to <strong>Support Tickets</strong> from the dashboard</li>
                            <li>Filter by customer name or status</li>
                            <li>Click on a ticket to view details</li>
                        </ol>

                        <h6 class="mt-3">Responding to Tickets:</h6>
                        <ol>
                            <li>Open the ticket</li>
                            <li>Read the customer's issue</li>
                            <li>Add your response in the comment section</li>
                            <li>Update ticket status (In Progress/Resolved/Closed)</li>
                            <li>Click <strong>"Add Response"</strong></li>
                        </ol>

                        <div class="alert alert-info mt-2">
                            <i class="fas fa-clock me-2"></i>
                            <strong>SLA Reminder:</strong> Respond to critical tickets within 1 hour, standard tickets within 24 hours.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Managing Payment Follow-ups</h5>
                    </div>
                    <div class="card-body">
                        <h6>Checking Payment Status:</h6>
                        <ol>
                            <li>Go to <strong>Payment Followups</strong> from the dashboard</li>
                            <li>View customers with overdue payments</li>
                            <li>Check invoice details and due dates</li>
                        </ol>

                        <h6 class="mt-3">Payment Follow-up Process:</h6>
                        <ol>
                            <li><strong>0-30 days overdue:</strong> Send email reminder</li>
                            <li><strong>31-60 days overdue:</strong> Phone call follow-up</li>
                            <li><strong>61-90 days overdue:</strong> Escalate to debt manager</li>
                            <li><strong>90+ days overdue:</strong> Legal review</li>
                        </ol>

                        <div class="alert alert-kp-warning mt-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Current Status:</strong> All customers have zero pending payments. Great job!
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header bg-kp-green text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Customer Health Monitoring</h5>
                    </div>
                    <div class="card-body">
                        <h6>Key Indicators to Monitor:</h6>
                        <ul>
                            <li><strong>Ticket Volume:</strong> Sudden increase may indicate issues</li>
                            <li><strong>Payment History:</strong> Late payments = financial stress</li>
                            <li><strong>Service Usage:</strong> Declining usage may signal churn risk</li>
                            <li><strong>Communication Frequency:</strong> Lack of contact may indicate dissatisfaction</li>
                        </ul>
                        <div class="mt-3">
                            <div class="progress mb-2">
                                <div class="progress-bar bg-kp-green" style="width: 100%">100% Healthy</div>
                            </div>
                            <small class="text-muted">All customers currently healthy</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Quarterly Business Reviews</h5>
                    </div>
                    <div class="card-body">
                        <h6>Schedule QBR with each customer:</h6>
                        <ul>
                            <li>Review service performance (uptime, tickets)</li>
                            <li>Discuss upcoming needs and upgrades</li>
                            <li>Identify upsell opportunities</li>
                            <li>Gather feedback and satisfaction scores</li>
                            <li>Review contract terms and renewals</li>
                        </ul>
                        <div class="alert alert-secondary mt-2">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>Pro Tip:</strong> Schedule QBRs for top customers monthly instead of quarterly.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header bg-kp-blue text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Customer Reports</h5>
                    </div>
                    <div class="card-body">
                        <h6>Available Reports:</h6>
                        <ul>
                            <li><strong>Customer Summary Report</strong> - Portfolio overview</li>
                            <li><strong>Ticket Analysis Report</strong> - Support performance</li>
                            <li><strong>Payment History Report</strong> - Financial status</li>
                            <li><strong>Lease Utilization Report</strong> - Service usage</li>
                            <li><strong>Satisfaction Report</strong> - Customer feedback</li>
                        </ul>
                        <button class="btn btn-sm btn-outline-kp-primary w-100">
                            <i class="fas fa-download me-1"></i>Generate Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Best Practices Section -->
        <h3 class="mt-4"><i class="fas fa-star text-kp-yellow me-2"></i>Best Practices for Account Managers</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <i class="fas fa-phone-alt fa-2x text-kp-blue mb-2"></i>
                            <h6>Proactive Communication</h6>
                            <p class="small">Reach out monthly, not just when issues arise</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <i class="fas fa-bell fa-2x text-kp-yellow mb-2"></i>
                            <h6>Set Expectations</h6>
                            <p class="small">Communicate SLAs and response times clearly</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <i class="fas fa-chart-line fa-2x text-kp-green mb-2"></i>
                            <h6>Track Metrics</h6>
                            <p class="small">Monitor customer health scores regularly</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <i class="fas fa-file-alt fa-2x text-info mb-2"></i>
                            <h6>Document Everything</h6>
                            <p class="small">Keep detailed notes of all customer interactions</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <i class="fas fa-gift fa-2x text-danger mb-2"></i>
                            <h6>Recognize Milestones</h6>
                            <p class="small">Acknowledge anniversaries and achievements</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <i class="fas fa-sync-alt fa-2x text-secondary mb-2"></i>
                            <h6>Escalate When Needed</h6>
                            <p class="small">Know when to involve technical teams or management</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-kp-primary w-100 mb-2">
                            <i class="fas fa-user-plus me-2"></i>Add Customer
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-info w-100 mb-2">
                            <i class="fas fa-ticket-alt me-2"></i>New Ticket
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-warning w-100 mb-2">
                            <i class="fas fa-file-invoice me-2"></i>Generate Statement
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-kp-success w-100 mb-2">
                            <i class="fas fa-chart-line me-2"></i>Run Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-headset me-2"></i>
            <strong>Need Help?</strong> Contact the Account Manager Admin or IT support for assistance with customer management.
        </div>

    </div>
</div>

@push('scripts')
<script>
function viewCustomer(customerId) {
    // Redirect to customer details page
    window.location.href = '/customers/' + customerId;
}

function contactCustomer(customerId) {
    // Open contact modal or redirect
    alert('Contact customer functionality - will open communication options');
}
</script>
@endpush
@endsection
