@extends('layouts.help')

@section('help-content')
<div class="card shadow-sm">
    <div class="card-header bg-kp-blue text-white">
        <h4 class="mb-0">
            <i class="fas fa-file-alt me-2"></i>
            CAK Compliance Forms - Complete Filling Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>CAK Compliance Overview:</strong> This guide will help you accurately fill out ASP, CSP, and NFP compliance returns for the Communications Authority of Kenya.
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" id="complianceTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="asp-tab" data-bs-toggle="tab" data-bs-target="#asp" type="button" role="tab">
                    <i class="fas fa-server me-2"></i>ASP Guide
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="csp-tab" data-bs-toggle="tab" data-bs-target="#csp" type="button" role="tab">
                    <i class="fas fa-envelope me-2"></i>CSP Guide
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="nfp-tab" data-bs-toggle="tab" data-bs-target="#nfp" type="button" role="tab">
                    <i class="fas fa-network-wired me-2"></i>NFP Guide
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="common-tab" data-bs-toggle="tab" data-bs-target="#common" type="button" role="tab">
                    <i class="fas fa-question-circle me-2"></i>Common Tips
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="complianceTabContent">

            <!-- ASP Guide Tab -->
            <div class="tab-pane fade show active" id="asp" role="tabpanel">
                <div class="card border-kp-blue">
                    <div class="card-header bg-kp-blue text-white">
                        <h5 class="mb-0"><i class="fas fa-server me-2"></i>Application Service Provider (ASP) Form Guide</h5>
                    </div>
                    <div class="card-body">

                        <div class="alert alert-kp-primary">
                            <strong>ASP License Number Format:</strong> AFP:TL/NFP/XXXXX
                        </div>

                        <h3 class="mt-4">Section 1: General Information</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5>1.1 Licence Details</h5>
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr><th>Field</th><th>Description</th><th>Example</th><th>Required</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Name of Licensee</td>
                                            <td>Your company's registered name</td>
                                            <td>Kenya Power & Lighting Company (KPLC)</td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td>License No.</td>
                                            <td>Your ASP license number from CAK</td>
                                            <td>AFP:TL/NFP/00051</td>
                                            <td><span class="badge bg-danger">Yes</span></td>
                                        </tr>
                                        <tr>
                                            <td>Other Licenses Held</td>
                                            <td>Any other CAK licenses you hold</td>
                                            <td>CSP:TL/CSP/00451, NFP:TL/NFP/00051</td>
                                            <td><span class="badge bg-secondary">Optional</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <h3>Section 2: Period Under Review</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="alert alert-kp-warning">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <strong>Important:</strong> Financial year in Kenya runs from July 1 to June 30.
                                </div>
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr><th>Quarter</th><th>Period</th><th>Due Date</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>Q1</td><td>July 1 – September 30</td><td>October 15</td></tr>
                                        <tr><td>Q2</td><td>October 1 – December 31</td><td>January 15</td></tr>
                                        <tr><td>Q3</td><td>January 1 – March 31</td><td>April 15</td></tr>
                                        <tr><td>Q4</td><td>April 1 – June 30</td><td>July 15</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <h3>Section 3: Contact Information</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Physical Address</h5>
                                        <ul>
                                            <li><strong>County:</strong> Nairobi, Mombasa, Kisumu, etc.</li>
                                            <li><strong>Town/City:</strong> Nairobi, Mombasa</li>
                                            <li><strong>Street/Road:</strong> Kolobot Road</li>
                                            <li><strong>Building Name:</strong> Stima Plaza</li>
                                            <li><strong>Floor & Room:</strong> 8th Floor, Room 801</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Postal Address</h5>
                                        <ul>
                                            <li><strong>P.O. Box:</strong> 30099</li>
                                            <li><strong>Postal Town:</strong> Nairobi</li>
                                            <li><strong>Postal Code:</strong> 00100</li>
                                        </ul>
                                        <h5 class="mt-3">Contact Numbers</h5>
                                        <ul>
                                            <li><strong>Telephone:</strong> 020 3201 000</li>
                                            <li><strong>Mobile:</strong> 0703 070707</li>
                                            <li><strong>Email:</strong> customercare@kplc.co.ke</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3>Section 4: Staff Details</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <p>Report staff count by category. Totals are calculated automatically.</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Staff Category</th>
                                                <th>Local Male</th>
                                                <th>Local Female</th>
                                                <th>Expat Male</th>
                                                <th>Expat Female</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td>Technical - Permanent</td><td>_</td><td>_</td><td>_</td><td>_</td></tr>
                                            <tr><td>Technical - Contract</td><td>_</td><td>_</td><td>_</td><td>_</td></tr>
                                            <tr><td>Non-Technical - Permanent</td><td>_</td><td>_</td><td>_</td><td>_</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <h3>Section 5: Environmental Compliance</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5>E-Waste Initiatives</h5>
                                <p>Describe your e-waste collection and disposal methods:</p>
                                <div class="alert alert-secondary">
                                    <strong>Example:</strong><br>
                                    "KPLC collects incandescent bulbs through contracted service providers who crash and segregate materials for recycling.
                                    Obsolete meters are stored in containments awaiting safe disposal."
                                </div>

                                <h5 class="mt-3">Carbon Footprint Reduction</h5>
                                <p>Describe your carbon reduction initiatives:</p>
                                <div class="alert alert-secondary">
                                    <strong>Example:</strong><br>
                                    "• 90% of energy distributed is green energy<br>
                                    • E-mobility adoption with electric motorcycles<br>
                                    • Tree planting partnerships with KFS and KWS<br>
                                    • Phase out of thermal power generation stations"
                                </div>

                                <h5 class="mt-3">EMCA Compliance Status</h5>
                                <p>Describe your adherence to EMCA Waste Management regulations:</p>
                                <div class="alert alert-secondary">
                                    <strong>Example:</strong><br>
                                    "All projects undergo Integrated Environmental Impact Assessment.
                                    KPLC has a dedicated HSE Department ensuring environmental compliance."
                                </div>
                            </div>
                        </div>

                        <h3>Section 6: PWD Compliance (Persons with Disabilities)</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 40%;">Aware of KS2952 Standard?</th>
                                        <td>Select <span class="badge bg-kp-green">Yes</span> or <span class="badge bg-danger">No</span></td>
                                    </tr>
                                    <tr>
                                        <th>Complied with the standard?</th>
                                        <td>Select <span class="badge bg-kp-green">Yes</span> or <span class="badge bg-danger">No</span></td>
                                    </tr>
                                    <tr>
                                        <th>Actions taken</th>
                                        <td>Describe ramps, accessible toilets, express rooms for lactating mothers</td>
                                    </tr>
                                    <tr>
                                        <th>Challenges faced</th>
                                        <td>Describe any difficulties in implementing PWD compliance</td>
                                    </tr>
                                    <tr>
                                        <th>Future plans</th>
                                        <td>Upcoming accessibility improvements</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <h3>Section 7: Submitting the Form</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="text-center p-3 border rounded">
                                            <i class="fas fa-save fa-2x text-secondary"></i>
                                            <h5 class="mt-2">Save as Draft</h5>
                                            <p>Use if you need to complete later</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-center p-3 border rounded bg-kp-green text-white">
                                            <i class="fas fa-paper-plane fa-2x"></i>
                                            <h5 class="mt-2">Submit to CAK</h5>
                                            <p>Final submission - cannot edit after</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- CSP Guide Tab -->
            <div class="tab-pane fade" id="csp" role="tabpanel">
                <div class="card border-kp-green">
                    <div class="card-header bg-kp-green text-white">
                        <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Content Service Provider (CSP) Form Guide</h5>
                    </div>
                    <div class="card-body">

                        <div class="alert alert-kp-success">
                            <strong>CSP License Number Format:</strong> CSP:TL/CSP/XXXXX
                        </div>

                        <h3 class="mt-4">CSP-Specific Sections</h3>

                        <h3>Services Offered</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <p>List all content services provided during the quarter:</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Service Name</th>
                                                <th>Short Code/USSD</th>
                                                <th>Tariff (KES)</th>
                                                <th>Volume M1</th>
                                                <th>Volume M2</th>
                                                <th>Volume M3</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input type="text" class="form-control form-control-sm" placeholder="e.g., Weather Alerts"></td>
                                                <td><input type="text" class="form-control form-control-sm" placeholder="e.g., 12345"></td>
                                                <td><input type="text" class="form-control form-control-sm" placeholder="5.00"></td>
                                                <td><input type="text" class="form-control form-control-sm" placeholder="1000"></td>
                                                <td><input type="text" class="form-control form-control-sm" placeholder="1200"></td>
                                                <td><input type="text" class="form-control form-control-sm" placeholder="1100"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-info mt-2">
                                    <i class="fas fa-chart-line me-2"></i>
                                    <strong>Months Explained:</strong> M1 = First month of quarter, M2 = Second month, M3 = Third month
                                </div>
                            </div>
                        </div>

                        <h3>Bulk SMS Services</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <p>Report bulk SMS services provided to other CSPs:</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr><th>CSP Name</th><th>Tariff (KES)</th><th>Volume</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr><td><input type="text" class="form-control form-control-sm" placeholder="Company name"></td><td><input type="text" class="form-control form-control-sm" placeholder="0.50"></td><td><input type="text" class="form-control form-control-sm" placeholder="10000"></td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <h3>Mobile Money Transfers</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Transaction Types</h5>
                                        <ul>
                                            <li><strong>B2B:</strong> Business to Business</li>
                                            <li><strong>B2C:</strong> Business to Consumer</li>
                                            <li><strong>C2B:</strong> Consumer to Business</li>
                                            <li><strong>C2G:</strong> Consumer to Government</li>
                                            <li><strong>G2C:</strong> Government to Consumer</li>
                                            <li><strong>P2P:</strong> Person to Person</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Cross-Network Transfers</h5>
                                        <p>Report P2P transfers to/from other networks:</p>
                                        <ul>
                                            <li>P2P sent to other networks (Volume & Value)</li>
                                            <li>P2P received from other networks (Volume & Value)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3>Numbering Resources</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr><th>Resource Type</th><th>Assigned by CAK</th><th>In Use</th><th>Not in Use</th><th>Reason</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr><td>Short Codes</td><td>_</td><td>_</td><td>_</td><td>_</td></tr>
                                            <tr><td>USSD Codes</td><td>_</td><td>_</td><td>_</td><td>_</td></tr>
                                            <tr><td>Toll Free Numbers</td><td>_</td><td>_</td><td>_</td><td>_</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <h3>Complaints Handling</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <p>Report complaints received and resolved by category:</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr><th>Category</th><th>M1 Received</th><th>M1 Resolved</th><th>M2 Received</th><th>M2 Resolved</th><th>M3 Received</th><th>M3 Resolved</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr><td>Customer Care</td><td>_</td><td>_</td><td>_</td><td>_</td><td>_</td><td>_</td></tr>
                                            <tr><td>Billing/Charges</td><td>_</td><td>_</td><td>_</td><td>_</td><td>_</td><td>_</td></tr>
                                            <tr><td>Network Failures</td><td>_</td><td>_</td><td>_</td><td>_</td><td>_</td><td>_</td></tr>
                                            <tr><td>Spamming</td><td>_</td><td>_</td><td>_</td><td>_</td><td>_</td><td>_</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-kp-warning mt-3">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>Tip:</strong> If no services were offered during the quarter, mark "N/A" and provide explanation in comments.
                        </div>

                    </div>
                </div>
            </div>

            <!-- NFP Guide Tab -->
            <div class="tab-pane fade" id="nfp" role="tabpanel">
                <div class="card border-kp-yellow">
                    <div class="card-header bg-kp-yellow text-dark">
                        <h5 class="mb-0"><i class="fas fa-network-wired me-2"></i>Network Facility Provider (NFP) Form Guide</h5>
                    </div>
                    <div class="card-body">

                        <div class="alert alert-kp-warning">
                            <strong>NFP License Number Format:</strong> NFP:TL/NFP/XXXXX
                        </div>

                        <h3 class="mt-4">NFP-Specific Sections</h3>

                        <h3>Infrastructure Deployed</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <p>List all types of infrastructure deployed:</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr><th>Type</th><th>Description</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select class="form-select form-select-sm">
                                                        <option>Select type</option>
                                                        <option>Fibre Optic Cable</option>
                                                        <option>Telecommunication Mast/Tower</option>
                                                        <option>Data Centre</option>
                                                        <option>Microwave Link</option>
                                                        <option>Base Station</option>
                                                    </select>
                                                </td>
                                                <td><textarea class="form-control" rows="2" placeholder="Describe location, length, capacity..."></textarea></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-secondary mt-2">
                                    <strong>Example - Fibre Optic Cable:</strong><br>
                                    "OPGW aerial deployment from Nairobi to Mombasa with underground segments.
                                    Includes ADSS self-supporting cables and buried ducted sections."
                                </div>
                            </div>
                        </div>

                        <h3>Network Facility Location (Map Feature)</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Methods to Set Location:</h5>
                                        <ol>
                                            <li><strong>Manual Entry:</strong> Enter Lat/Long coordinates</li>
                                            <li><strong>Get Current Location:</strong> Uses device GPS</li>
                                            <li><strong>Pick from Map:</strong> Interactive map selector</li>
                                        </ol>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 bg-light text-center">
                                            <i class="fas fa-map-marker-alt fa-3x text-danger mb-2"></i>
                                            <p><strong>Example Coordinates:</strong><br>
                                            Latitude: -1.286389<br>
                                            Longitude: 36.817223<br>
                                            (Nairobi, Kenya)</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-chart-line"></i> Fibre Optic Cable Length</h5>
                                        <p>Enter total length in kilometers (km)</p>
                                        <input type="text" class="form-control" placeholder="e.g., 450.5">
                                    </div>
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-tower-broadcast"></i> Number of Towers</h5>
                                        <p>Enter total number of telecommunication towers</p>
                                        <input type="text" class="form-control" placeholder="e.g., 25">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3>Broadband Infrastructure</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr><th>Type</th><th>Ownership</th><th>Capacity Owned (Gbps)</th><th>Capacity Utilized (Gbps)</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input type="text" class="form-control form-control-sm" placeholder="Optical Fibre"></td>
                                                <td><input type="text" class="form-control form-control-sm" placeholder="Owned/Leased"></td>
                                                <td><input type="text" class="form-control form-control-sm" placeholder="100"></td>
                                                <td><input type="text" class="form-control form-control-sm" placeholder="75"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <h3>Number Assignments</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5>Primary Numbers (NFP-T1 Only)</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Short Codes: <input type="text" class="form-control" placeholder="Assigned / Utilized"></label>
                                    </div>
                                    <div class="col-md-6">
                                        <label>USSD Codes: <input type="text" class="form-control" placeholder="Assigned / Utilized"></label>
                                    </div>
                                </div>

                                <h5 class="mt-3">Secondary Number Assignment</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr><th>CSP Name</th><th>Shortcode/USSD</th><th>Tariff (KES)</th><th>Volume</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr><td><input class="form-control form-control-sm"></td><td><input class="form-control form-control-sm"></td><td><input class="form-control form-control-sm"></td><td><input class="form-control form-control-sm"></td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-map-marked-alt me-2"></i>
                            <strong>Pro Tip:</strong> Use the "Pick from Map" button to accurately mark your network facility location - it's the easiest method!
                        </div>

                    </div>
                </div>
            </div>

            <!-- Common Tips Tab -->
            <div class="tab-pane fade" id="common" role="tabpanel">
                <div class="card border-secondary">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Common Tips & Best Practices</h5>
                    </div>
                    <div class="card-body">

                        <h3>📋 Before You Start</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <ul>
                                    <li>Have your license number ready</li>
                                    <li>Prepare digital signature and company stamp (PNG/JPG format)</li>
                                    <li>Gather staff count data by category</li>
                                    <li>Collect environmental compliance documentation</li>
                                    <li>Review previous quarter's return for consistency</li>
                                </ul>
                            </div>
                        </div>

                        <h3>⚠️ Common Mistakes to Avoid</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-dark">
                                            <tr><th>Mistake</th><th>Consequence</th><th>How to Avoid</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Missing signature or stamp</td>
                                                <td>Return rejected</td>
                                                <td>Upload before submitting</td>
                                            </tr>
                                            <tr>
                                                <td>Incorrect financial year</td>
                                                <td>Wrong period reporting</td>
                                                <td>Check auto-filled value</td>
                                            </tr>
                                            <tr>
                                                <td>Empty required fields</td>
                                                <td>Form won't submit</td>
                                                <td>Review all red asterisk fields</td>
                                            </tr>
                                            <tr>
                                                <td>File too large</td>
                                                <td>Upload fails</td>
                                                <td>Compress images (max 2MB)</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <h3>📅 Filing Deadlines</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="alert alert-kp-warning">
                                    <strong>Quarterly returns must be submitted within 15 days after each quarter ends.</strong>
                                </div>
                                <ul>
                                    <li><strong>Q1 (Jul-Sep):</strong> Due by October 15</li>
                                    <li><strong>Q2 (Oct-Dec):</strong> Due by January 15</li>
                                    <li><strong>Q3 (Jan-Mar):</strong> Due by April 15</li>
                                    <li><strong>Q4 (Apr-Jun):</strong> Due by July 15</li>
                                </ul>
                            </div>
                        </div>

                        <h3>📄 Document Requirements</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-image"></i> Signature & Stamp</h5>
                                        <ul>
                                            <li>Format: PNG, JPG, JPEG</li>
                                            <li>Size: Max 2MB per file</li>
                                            <li>Clear and readable</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-file-pdf"></i> Supporting Documents</h5>
                                        <ul>
                                            <li>Format: PDF</li>
                                            <li>Size: Max 5MB per file</li>
                                            <li>Official company documents</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3>✅ Pre-Submission Checklist</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul>
                                            <li>☐ All required fields filled</li>
                                            <li>☐ Signature uploaded</li>
                                            <li>☐ Company stamp uploaded</li>
                                            <li>☐ Financial year correct</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul>
                                            <li>☐ Quarter selected correctly</li>
                                            <li>☐ Contact information accurate</li>
                                            <li>☐ Staff numbers verified</li>
                                            <li>☐ Comments added (if nil returns)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3>🆘 Where to Get Help</h3>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <i class="fas fa-envelope fa-2x text-kp-blue"></i>
                                        <p><strong>Email</strong><br>compliance@darkfibre.co.ke</p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <i class="fas fa-phone fa-2x text-kp-green"></i>
                                        <p><strong>Phone</strong><br>020 3201 000</p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <i class="fas fa-ticket-alt fa-2x text-kp-yellow"></i>
                                        <p><strong>Submit Ticket</strong><br>Support → New Ticket</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-kp-success mt-3">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Remember:</strong> Save your progress frequently using the "Save Draft" button. You can return later to complete and submit.
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('cak.dashboard') }}" class="btn btn-kp-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to CAK Dashboard
            </a>
            <a href="{{ route('asp.create') }}" class="btn btn-kp-success">
                <i class="fas fa-plus me-2"></i>Create New ASP Return
            </a>
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print me-2"></i>Print Guide
            </button>
        </div>

    </div>
</div>
@endsection
