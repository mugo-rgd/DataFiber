@extends('layouts.help')

@section('help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-blue text-white">
        <h4 class="mb-0">
            <i class="fas fa-question-circle me-2"></i>
            Frequently Asked Questions
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Find answers to common questions about DarkFibre CRM.
        </div>

        <div class="accordion" id="faqAccordion">
            <!-- General FAQs -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        <strong>How do I log in to DarkFibre CRM?</strong>
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <ol>
                            <li>Navigate to <code>https://darkfiber.kplc.co.ke/login</code></li>
                            <li>Enter your registered email address</li>
                            <li>Enter your password</li>
                            <li>Click the <strong>"Login"</strong> button</li>
                        </ol>
                        <p>If you've forgotten your password, click the "Forgot Password?" link to reset it.</p>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        <strong>What should I do if I forget my password?</strong>
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <ol>
                            <li>Click the <strong>"Forgot Password?"</strong> link on the login page</li>
                            <li>Enter your registered email address</li>
                            <li>Check your email for a password reset link</li>
                            <li>Click the link and follow the instructions to create a new password</li>
                        </ol>
                        <div class="alert alert-kp-warning mt-2">
                            <i class="fas fa-envelope me-2"></i>
                            If you don't receive the email within 5 minutes, check your spam folder or contact IT support.
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        <strong>How do I update my profile information?</strong>
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <ol>
                            <li>Click on your <strong>name/avatar</strong> in the top-right corner</li>
                            <li>Select <strong>"My Profile"</strong> from the dropdown menu</li>
                            <li>Click the <strong>"Edit Profile"</strong> button</li>
                            <li>Update your information</li>
                            <li>Click <strong>"Save Changes"</strong></li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        <strong>How do I export data from the system?</strong>
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <ol>
                            <li>Go to the <strong>Export Data</strong> module from the main menu</li>
                            <li>Select the data type (ASP, CSP, NFP, or Combined)</li>
                            <li>Apply filters if needed (status, financial year, quarter, date range)</li>
                            <li>Choose export format (Excel, CSV, or PDF)</li>
                            <li>Click <strong>"Export"</strong></li>
                        </ol>
                        <p>The file will automatically download to your computer.</p>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                        <strong>How often should I submit CAK compliance returns?</strong>
                    </button>
                </h2>
                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>Quarterly returns must be submitted within <strong>15 days after each quarter ends</strong>.</p>
                        <ul>
                            <li>Q1 (Jul-Sep): Due by Oct 15</li>
                            <li>Q2 (Oct-Dec): Due by Jan 15</li>
                            <li>Q3 (Jan-Mar): Due by Apr 15</li>
                            <li>Q4 (Apr-Jun): Due by Jul 15</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 className="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                        <strong>What file formats are accepted for uploads?</strong>
                    </button>
                </h2>
                <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <ul>
                            <li><strong>Images:</strong> PNG, JPG, JPEG (max 2MB per file)</li>
                            <li><strong>Documents:</strong> PDF (max 5MB per file)</li>
                            <li><strong>Signature & Stamp:</strong> PNG or JPG recommended</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                        <strong>How do I submit a support ticket?</strong>
                    </button>
                </h2>
                <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <ol>
                            <li>Go to <strong>Tickets</strong> from the main menu</li>
                            <li>Click <strong>"New Ticket"</strong></li>
                            <li>Select the appropriate category (Outage, Performance, Billing, General)</li>
                            <li>Describe your issue in detail</li>
                            <li>Attach screenshots if applicable</li>
                            <li>Click <strong>"Submit"</strong></li>
                        </ol>
                        <p><strong>Response SLA:</strong> Critical within 1 hour, Standard within 24 hours</p>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">
                        <strong>How do I change my notification preferences?</strong>
                    </button>
                </h2>
                <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <ol>
                            <li>Go to <strong>My Profile</strong> from the top-right menu</li>
                            <li>Click on the <strong>"Notifications"</strong> tab</li>
                            <li>Select your preferred notification channels (Email, In-app, SMS)</li>
                            <li>Choose which events trigger notifications</li>
                            <li>Click <strong>"Save Preferences"</strong></li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq9">
                        <strong>Can I access DarkFibre CRM from my mobile phone?</strong>
                    </button>
                </h2>
                <div id="faq9" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>Yes! DarkFibre CRM is fully responsive and works on mobile phones and tablets. However, for complex data entry (especially tables), a desktop computer is recommended.</p>
                        <p>You can access the system using any modern mobile browser (Chrome, Safari, Firefox).</p>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq10">
                        <strong>Who do I contact for technical support?</strong>
                    </button>
                </h2>
                <div id="faq10" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <ul>
                            <li><strong>Email:</strong> <a href="mailto:support@darkfibre.co.ke">support@darkfibre.co.ke</a></li>
                            <li><strong>Phone:</strong> 020 3201 000</li>
                            <li><strong>WhatsApp:</strong> 0703 070707</li>
                            <li><strong>WeChat:</strong> DarkFibre_Support</li>
                        </ul>
                        <p>Support hours: Monday-Friday, 8:00 AM - 5:00 PM</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-kp-success mt-4">
            <i class="fas fa-question-circle me-2"></i>
            <strong>Still have questions?</strong>
            <a href="{{ route('help.contact') }}">Contact our support team</a> for personalized assistance.
        </div>

    </div>
</div>
@endsection
