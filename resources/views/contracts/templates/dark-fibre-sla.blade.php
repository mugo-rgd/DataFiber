<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            line-height: 1.0;
            color: #333;
            margin: 0;
            padding: 6px;
            font-size: 11px;
        }
        .header-container {
            width: 100%;
            margin-bottom: 4px;
        }
        .logo-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            width: 100%;
            margin-bottom: 2px; /* Reduced */
        }
        .logo-left, .logo-right {
            flex: 0 0 120px; /* Reduced from 150px */
            min-height: 50px; /* Reduced from 60px */
            display: flex;
            align-items: flex-start;
        }
        .logo-left {
            justify-content: flex-start;
        }
        .logo-right {
            justify-content: flex-end;
        }
        .logo-left img, .logo-right img {
            max-height: 50px; /* Reduced from 60px */
            max-width: 120px; /* Reduced from 150px */
            width: auto;
            height: auto;
            object-fit: contain;
        }
        /* If you need responsive behavior: */
@media (max-width: 768px) {
    .logo-left, .logo-right {
        flex: 0 0 100px;  /* Smaller on mobile */
    }
}
        .document-title {
            text-align: center;
            margin: 2px 0 0 0; /* Reduced top margin */
            width: 100%;
        }
        .document-title h2 {
            margin: 0 0 1px 0;
            padding: 0;
            font-size: 14px;
            font-weight: bold;
        }
        .document-title h3 {
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
                .document-title h4 {
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
                .document-title h5 {
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
   .section {
    margin-bottom: 6px;          /* reduced from 10px */
    page-break-inside: avoid;
    padding: 3px;                  /* FIX: remove invalid padding: 3 */
    text-align: justify;
}
        .clause {
            margin-bottom: 1px;
        }
       .clause-title {
    font-weight: bold;
    margin: 2px 0 4px 0;         /* tighter title spacing */
}
      .section + .section {
    margin-top: 0;
}
        .sub-clause {
            margin-left: 15px;
            margin-bottom: 0;
        }

        .section table {
    margin-top: 4px;
    margin-bottom: 4px;
}

.section p {
    margin-bottom: 2px;
}

        .signature-section {
            margin-top: 10px;
            page-break-inside: avoid;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 250px;
            margin-top: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 2px 0;
            font-size: 9px;
        }
        table, th, td {
            border: 1px solid #333;
        }
        th, td {
            padding: 2px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 8px;
            font-size: 8px;
            color: #666;
            text-align: center;
        }
        .party-details {
            margin: 2px 0;
        }
        .witness-section {
            margin-top: 15px;
        }
        .signature-block {
            margin: 5px 0;
        }
        .list-item {
            margin-left: 15px;
            margin-bottom: 0;
        }
        .agreement-header {
            text-align: center;
            font-weight: bold;
            margin: 2px 0;
             padding: 5px;
        }
        .note-box {
            background-color: #f9f9f9;
            border-left: 2px solid #ccc;
            padding: 3px;
            margin: 2px 0;
            font-size: 9px;
        }
        .formula {
            text-align: center;
            margin: 2px 0;
            font-style: italic;
        }
        p {
            margin: 0 0 1px 0;
            padding: 3px;
        }
        .compact {
            margin: 0;
            padding: 3px;
        }
        .signature-container {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
        }
        .signature-column {
            width: 48%;
        }
    </style>
</head>
<body>
    <!-- Header with Logos -->
    <div class="header-container">
    <table style="width:100%; border:none; margin-bottom:4px;">
        <tr>
            <!-- Left Logo -->
            <td style="width:50%; text-align:left; vertical-align:middle; border:none;">
                @php
                    $kplcLogoPath = public_path('images/logo.png');
                @endphp
                @if(file_exists($kplcLogoPath))
                    <img
                        src="file://{{ $kplcLogoPath }}"
                        alt="KPLC Logo"
                        style="height:50px; width:auto;"
                    >
                @endif
            </td>

            <!-- Right Logo -->
            <td style="width:50%; text-align:right; vertical-align:middle; border:none;">
                @php
                    $customerLogoPath = null;
                    if ($contract->quotation->customer->companyProfile->profile_photo ?? false) {
                        $customerLogoPath = storage_path(
                            'app/public/' . $contract->quotation->customer->companyProfile->profile_photo
                        );
                    }
                @endphp

                @if($customerLogoPath && file_exists($customerLogoPath))
                    <img
                        src="file://{{ $customerLogoPath }}"
                        alt="Customer Logo"
                        style="height:50px; width:auto;"
                    >
                @else
                    <!-- Placeholder if customer logo missing -->
                    <span style="
                        display:inline-block;
                        height:50px;
                        line-height:50px;
                        font-size:9px;
                        color:#999;
                        border:1px dashed #ccc;
                        padding:0 8px;
                    ">
                        CUSTOMER LOGO
                    </span>
                @endif
            </td>
        </tr>
    </table>

    <!-- Document Title -->
    <div class="document-title">
        <h2>SERVICE LEVEL AGREEMENT</h2>
        <h3>FOR LEASE OF DARK FIBRE</h3>
        <br><br>
        <h3>THE KENYA POWER & LIGHTING COMPANY PLC</h3>
        <h4>- AND -</h4>
        <h5>{{ $contract->quotation->customer->name ?? 'Customer' }}</h5>
    </div>
</div>


    <!-- SLA Introduction -->
    <div class="section compact">
    <div class="agreement-header">
        SERVICE LEVEL AGREEMENT
    </div>

    <p>
        <strong>THIS SERVICE LEVEL AGREEMENT</strong> is made on the
        {{ now()->format('j') }} day of {{ now()->format('F Y') }}.
    </p>

   <div class="party-details">
    <p style="text-align: center;"><strong>BETWEEN:</strong></p>

    <p>
        <strong>THE KENYA POWER AND LIGHTING COMPANY PLC</strong>
        A public limited company duly incorporated under the Companies Act
        Registered Office: Stima Plaza, Kolobot Road, Parklands, Nairobi
        P.O. Box 30099-00100, Nairobi, Kenya
        (Hereinafter referred to as the <strong>"Lessor"</strong>)
    </p>

    <p style="text-align: center;"><strong>AND</strong></p>

    <p>
        <strong>{{ $contract->quotation->customer->name ?? 'Customer' }}</strong>
        A limited liability company duly incorporated under the Companies Act,
        No. 17 of 2015 of the Laws of Kenya
        Situated at {{ $contract->quotation->customer->address }},
        Post Office Box Number {{ $contract->quotation->customer->city }}, Kenya
        (Hereinafter referred to as the <strong>"Lessee"</strong>, which expression
        shall where the context so admits include its successors in title and assigns)
    </p>
</div>


    <p>
        <strong>AND IS SUPPLEMENTAL</strong> to the Master Agreement for Lease of Dark Fibre
        (hereinafter referred to as the <strong>"Master Lease Agreement"</strong>) dated
        …………………… and made between the Lessor of the one part and the Lessee of the other part.
    </p>

    <p>
        <strong>THIS SERVICE LEVEL AGREEMENT ("SLA")</strong> shall take effect on the
        Effective Date indicated in the respective Acceptance Certificate and shall remain
        in force and effect for the duration of the respective Lease Order Form.
    </p>
</div>


    <!-- Clause 1: Definitions and Interpretation -->
    <div class="section">
        <div class="clause-title">1. DEFINITIONS AND INTERPRETATION</div>
        <p>In this Agreement, unless specifically stated, words and expressions shall have the same meanings as are respectively assigned to them in the Master Lease Agreement.</p>
        <p>Unless the context or express provision otherwise requires:</p>

        <div class="sub-clause">
            <p><strong>1.1 Service Availability</strong> means the measure of the probability that a service is available on the Dark Fibres at any given time and is expressed as being 98% (ADSS) and 99.99% (OPGW) for the purposes of this SLA.</p>
            <p><strong>1.2 Change Request Form</strong> means a written notification of intended works issued by the Lessor to the Lessee indicating the date, duration and expected impact to services.</p>
            <p><strong>1.3 Response time</strong> means the acknowledgment by the Lessor of a fault notification made by email or telephone from the Lessee and which is followed by issuance of a Trouble Ticket or Fault Reference Number by the Lessor.</p>
            <p><strong>1.4 Service Credits</strong> means a deduction against the Lessor's subsequent quarterly invoice for non-fulfilment of the service availability level under Clause 9.3 herein and shall be equivalent to the amounts shown in table of Clause 9.3 of this SLA.</p>
            <p><strong>1.5 Unscheduled Maintenance</strong> means emergency or ad hoc works of an urgent nature required on the Lessor's Equipment and/or Lessor's Fibre Network.</p>
            <p><strong>1.6 Incident</strong> means an unplanned interruption to service availability or reduction in the quality of service.</p>
            <p><strong>1.7 Incident Report Number</strong> means a unique number to be issued upon reporting of an incident/complaint for purposes of tracking its resolution.</p>
            <p><strong>1.8 Incident Report</strong> means the detailed chronological report on failure including the causes of failure and actions carried out to resolve by the Lessor.</p>
            <p><strong>1.9 Restoration time</strong> means the time between escalation of an incident to the Lessor by the Lessee to when the incident has been resolved or a temporary solution has been put in place.</p>
            <p><strong>1.10 Incident Status Update Interval</strong> means the time interval after the escalation of an incident whereby the Lessor updates the Lessee on the progress of the fault rectification and any support required from the Lessee.</p>
            <p><strong>1.11 Incident Closure time</strong> this is the total time extended from the period of incident restoration to allow for post restoration monitoring that may in certain circumstances call for re-opening of an incident ticket to the final closure of that ticket.</p>
            <p><strong>1.12 MRC</strong> means Monthly Recurrent Charge.</p>
        </div>
    </div>

    <!-- Clause 2: Scope of this Service Level Agreement -->
    <div class="section">
        <div class="clause-title">2. SCOPE OF THIS SERVICE LEVEL AGREEMENT</div>
        <p>The SLA will cover the following areas:</p>
        <div class="list-item">2.1 Incident/ problem reporting</div>
        <div class="list-item">2.2 Fault categorization</div>
        <div class="list-item">2.3 Reported incident escalation procedure</div>
        <div class="list-item">2.4 Maintenance of Lessor's fibre network</div>
        <div class="list-item">2.5 Access by Lessee</div>
        <div class="list-item">2.6 Incident resolution times</div>
        <div class="list-item">2.7 Service credits</div>
        <div class="list-item">2.8 Miscellaneous provisions</div>
    </div>

    <!-- Clause 3: Incident/Problem reporting -->
    <div class="section">
        <div class="clause-title">3. INCIDENT/PROBLEM REPORTING</div>
        <p><strong>3.1</strong> When reporting an incident, the Lessee is required to give the following information:</p>
        <div class="list-item">3.1.1. Description of incident</div>
        <div class="list-item">3.1.2. Name and contacts of person reporting the incident</div>
        <div class="list-item">3.1.3. Location of the incident</div>
        <div class="list-item">3.1.4. Time of incident</div>
        <div class="list-item">3.1.5. Trouble Ticket (TT) issued by the Lessee</div>

        <p><strong>3.2</strong> All incidents should be reported through either:</p>
        <div class="list-item">3.2.1. Telecommunication Business Call Center,</div>
        <div class="list-item">3.2.2. Telecommunication Business Service portal (website) or</div>
        <div class="list-item">3.2.3. Telecommunication Business incident e-mail.</div>

        <p><strong>3.3</strong> Upon reporting an incident, the Lessee shall be issued with an Incident Report Number (IRN) which will be issued by the Lessor for tracking the incident until it is resolved.</p>
    </div>

    <!-- Clause 4: Fault Categorization -->
    <div class="section">
        <div class="clause-title">4. FAULT CATEGORIZATION</div>
        <p><strong>4.1 Critical Fault</strong> – will be deemed to have occurred when total loss of service will have been experienced by the Lessee.</p>
        <p><strong>4.2 Severe Fault</strong> – will be deemed to have occurred when a significant degradation of service will have been experienced by the Lessee.</p>
        <p><strong>4.3 Minor Fault</strong> – will be deemed to have occurred when a minor degradation of service will have been experienced by the Lessee.</p>

        <table>
            <tr>
                <th>Fault category</th>
                <th>Description</th>
            </tr>
            <tr>
                <td>Critical Fault</td>
                <td>- Fibre cut/break<br>- Business Disruptions<br>- Tower Collapse</td>
            </tr>
            <tr>
                <td>Severe Fault</td>
                <td>- Business Disruptions<br>- Poor/ faulty fiber accessories<br>- Faulty equipment</td>
            </tr>
            <tr>
                <td>Minor Fault</td>
                <td>- Business Disruptions<br>- Poor/ faulty fiber accessories<br>- Faulty equipment</td>
            </tr>
        </table>
    </div>

    <!-- Clause 5: Reported incidents and escalation procedures -->
    <div class="section">
        <div class="clause-title">5. REPORTED INCIDENTS AND ESCALATION PROCEDURES</div>
        <p>The Lessee may escalate incidents within the stipulated SLA times as follows:</p>

        <p><strong>5.1 Commercial Incident Escalation</strong><br>
        The Lessor shall provide to the Lessee the following personnel to handle commercial incidents:</p>

        <table>
            <tr>
                <th>Escalation Level</th>
                <th>Escalation Point (Contact)</th>
            </tr>
            <tr>
                <td>1st Level</td>
                <td>Benson Kimani<br>Senior Business Development Assistant<br>Tel: +254719079833<br>Mobile: +254726105124<br>Email: bkimani@kplc.co.ke</td>
            </tr>
            <tr>
                <td>2nd Level</td>
                <td>Wilson Mwirigi<br>Senior Business Development Officer<br>Tel: +254711031698<br>Mobile: +254700000330<br>Email: WMwirigi@kplc.co.ke</td>
            </tr>
            <tr>
                <td>3rd Level</td>
                <td>Daniel Kiniti<br>Deputy Director, Telecom Services<br>Tel: +254719079734<br>Mobile: +254711279235<br>Email: DKiniti@kplc.co.ke</td>
            </tr>
            <tr>
                <td>4th Level</td>
                <td>Robert Mugo<br>General Manager, ICT<br>Tel: +254 705700567<br>Email: Rmugo@kplc.co.ke</td>
            </tr>
        </table>

        <p><strong>5.2 Technical Incident escalation</strong><br>
        The Lessor shall provide to the Lessee the qualifications of their technical personnel indicating their areas of specialization. The Lessor shall appoint and provide to the Lessee the technical personnel for each region.</p>

        <table>
            <tr>
                <th>Escalation Level</th>
                <th>Escalation Point (Contact)</th>
            </tr>
            <tr>
                <td>1st Level</td>
                <td>NOC Service Desk<br>Email: NOCServiceDesk@kplc.co.ke<br>Dial mobile numbers 0732111800 or 0711-031800<br>WhatsApp +254 731952924<br>Shift Leader on Duty NMC-NOC<br>Telephone: +254 732-111800 or +254 711-031800<br>WhatsApp +254 731952924<br>email: NOCServiceDesk@kplc.co.ke</td>
            </tr>
            <tr>
                <td>2nd Level</td>
                <td>John Terrence Maina<br>Principal Officer – Head Of Service Delivery<br>Tel: +254 711031668<br>Mobile: +254722342844<br>Email: JTMaina@kplc.co.ke</td>
            </tr>
            <tr>
                <td>3rd Level</td>
                <td>Benjamin Muoki<br>Manager, Service Delivery<br>Tel: +254 711031569<br>Mobile: +254721672053<br>Email: BMuoki@kplc.co.ke</td>
            </tr>
            <tr>
                <td>4th Level</td>
                <td>Robert Mugo<br>General Manager, ICT<br>Tel: +254 711031695<br>Email: RMugo@kplc.co.ke</td>
            </tr>
        </table>

        <p><strong>5.3</strong> The Lessee can only escalate the reported incident if the service restoration times indicated below have been exceeded:</p>

        <table>
            <tr>
                <th>Fault Category</th>
                <th>2nd Level escalation (Hours)</th>
                <th>3rd Level escalation (Hours)</th>
                <th>4th Level escalation (Hours)</th>
            </tr>
            <tr>
                <td>Critical Fault</td>
                <td>4</td>
                <td>8</td>
                <td>12</td>
            </tr>
            <tr>
                <td>Severe Fault</td>
                <td>6</td>
                <td>12</td>
                <td>24</td>
            </tr>
            <tr>
                <td>Minor Fault</td>
                <td>6</td>
                <td>12</td>
                <td>24</td>
            </tr>
        </table>
    </div>

    <!-- Clause 6: Maintenance of the Lessor's Fibre Network -->
    <div class="section">
        <div class="clause-title">6. MAINTENANCE OF THE LESSOR'S FIBRE NETWORK</div>
        <p><strong>6.1 Unscheduled Maintenance</strong> – Where the Lessor requires an unscheduled or urgent maintenance activity to be conducted the Lessor shall issue a Change Request Notice to the Lessee one (1) day in advance.</p>
        <p><strong>6.2 Planned Maintenance</strong> - Where the Lessor requires a planned maintenance activity to be conducted the Lessor shall issue a Change Request Notice to the Lessee ten (10) days in advance.</p>
        <p><strong>6.3</strong> When undertaking maintenance of any nature the Lessor shall ensure that connectivity is maintained for the Purchased Capacity even if alternative routes are utilized to maintain the connectivity.</p>
    </div>

    <!-- Clause 7: Access by Lessee -->
    <div class="section">
        <div class="clause-title">7. ACCESS BY LESSEE</div>
        <p><strong>7.1</strong> The Lessee will be required to notify the Lessor of the need to access the colocation facility through the Telecommunication Services Call center, Telecommunication Service portal (website) or the Telecommunication Service incident email as follows:</p>

        <table>
            <tr>
                <th>Access Type</th>
                <th>Notification Time</th>
            </tr>
            <tr>
                <td>Routine Maintenance</td>
                <td>2 working days</td>
            </tr>
            <tr>
                <td>Emergency Access</td>
                <td>Immediately</td>
            </tr>
            <tr>
                <td>Site Surveys</td>
                <td>2 working days</td>
            </tr>
        </table>

        <p><strong>7.2</strong> The Lessee will be required to provide the following information:</p>
        <div class="list-item">7.2.1. Time and date when access is required</div>
        <div class="list-item">7.2.2. Authorized personnel to be granted access (Name, National ID. Number and contacts)</div>
        <div class="list-item">7.2.3. Description of works to be undertaken.</div>
    </div>

    <!-- Clause 8: Incidence Resolution Times -->
    <div class="section">
        <div class="clause-title">8. INCIDENCE RESOLUTION TIMES</div>
        <p>The Lessor shall resolve the reported incident as indicated in the table below:</p>

        <table>
            <tr>
                <th>Description CRM – incidence</th>
                <th>Restoration Time (hours)</th>
            </tr>
            <tr>
                <td>Critical Fault</td>
                <td>12</td>
            </tr>
            <tr>
                <td>Severe Fault</td>
                <td>24</td>
            </tr>
            <tr>
                <td>Minor Fault</td>
                <td>36</td>
            </tr>
        </table>
    </div>

    <!-- Clause 9: Service Credits -->
    <div class="section">
        <div class="clause-title">9. SERVICE CREDITS</div>
        <p><strong>9.1</strong> The Lessee shall impose Service Credits if the Lessor fails to meet the agreed Service Availability requirements.</p>
        <p><strong>9.2</strong> The Lessor and the Lessee shall reach a consensus on the service credits to be imposed by the Lessee after mutually referring to a reconciled report of Ticketed outages (verified by Lessee Outage Tickets and Lessors (IRN)) and QoS issues for the route in reference.</p>
        <p><strong>9.3</strong> The Lessor shall consequently and following the consensus issue service credits against the subsequent quarterly invoice.</p>
        <p>The service credits will be calculated as shown in the tables below:</p>

        <p><strong>TABLE 1: OPGW</strong></p>
        <table>
            <tr>
                <th>Service Availability</th>
                <th>Description - continuous downtime in minutes/month</th>
                <th>Service Credit USD per Month</th>
            </tr>
            <tr>
                <td>&gt;99.00%</td>
                <td>≤432 Minutes</td>
                <td>Nil</td>
            </tr>
            <tr>
                <td>&lt;99.00% - ≥98.00%</td>
                <td>&gt;432 ≤864 Minutes</td>
                <td>5% of MRC</td>
            </tr>
            <tr>
                <td>&lt;98.00% - ≥96.00%</td>
                <td>&gt;864 Minutes ≤1728 Minutes</td>
                <td>10% of MRC</td>
            </tr>
            <tr>
                <td>&lt;96.00%</td>
                <td>&gt;1728 Minutes</td>
                <td>20% of MRC</td>
            </tr>
        </table>

        <p><strong>TABLE 2: ADSS</strong></p>
        <table>
            <tr>
                <th>Service Availability</th>
                <th>Description continuous Down time in Minutes/Month</th>
                <th>Service credits USD Per Month</th>
            </tr>
            <tr>
                <td>≥ 98%</td>
                <td>864 Minutes</td>
                <td>Nil</td>
            </tr>
            <tr>
                <td>&lt;98.00% - ≥ 97.00%</td>
                <td>&gt;864 ≤ 1,296 Minutes</td>
                <td>5% of MRC</td>
            </tr>
            <tr>
                <td>&lt;97.00% - ≥ 96.00%</td>
                <td>&gt;1,296 ≤ 1,728 Minutes</td>
                <td>10% of MRC</td>
            </tr>
            <tr>
                <td>&lt; 96.00% - ≥ 93.00%</td>
                <td>&gt;1,728 Minutes ≤ 3,068 Minutes</td>
                <td>15% of MRC</td>
            </tr>
            <tr>
                <td>&lt;93.00%</td>
                <td>&lt;3,068 Minutes</td>
                <td>30% of MRC</td>
            </tr>
        </table>

        <p class="formula"><strong>Calculations of MRC</strong></p>
        <p class="formula">A = Charges per core per kilometre per month which is Rate per Km/Month</p>
        <p class="formula">B = Link distance in Kilometres</p>
        <p class="formula">Therefore</p>
        <p class="formula">MRC = A × B, Where MRC is Monthly Recurrent Charge.</p>

        <div class="note-box">
            <p><strong>Note:</strong></p>
            <div class="list-item">i) A link may constitute sections using different technologies (ADSS/OPGW)</div>
            <div class="list-item">ii) Service credits will be deducted subject to Clause 9 of the Master Lease Agreement with respect to tax</div>
        </div>
    </div>

    <!-- Clause 10: Miscellaneous Provisions -->
    <div class="section">
        <div class="clause-title">10. MISCELLANEOUS PROVISIONS</div>
        <p><strong>10.1</strong> The Parties agree that save for the provisions contained in this SLA the provisions and contents of the Master Lease Agreement shall apply to this SLA.</p>
        <p><strong>10.2</strong> All additions, amendments or variations to this SLA shall only be binding and effective if in writing and signed by both Parties.</p>
    </div>

    <!-- Signatures Section -->
    <div class="witness-section">
        <div class="agreement-header">IN WITNESS WHEREOF</div>
        <p>This Agreement has been duly executed by the parties hereto the day and year first hereinabove written.</p>

        <div class="signature-container">
            <!-- Lessor Signature Block -->
            <div class="signature-column">
                <p><strong>SIGNED for and on BEHALF of the Lessor</strong><br>
                THE KENYA POWER & LIGHTING COMPANY PLC by</p>
                <div class="signature-line"></div>
                <p>GENERAL MANAGER, ICT</p>
                <div class="signature-line"></div>
                <p>GENERAL MANAGER, ICT NAME</p>
                <p><strong>In the presence of:</strong></p>
                <div class="signature-line"></div>
                <p>MANAGER, SERVICE DELIVERY & TELECOMMUNICATION</p>
                <div class="signature-line"></div>
                <p>MANAGER, SERVICE DELIVERY & TELECOMMUNICATION NAME</p>
            </div>

            <!-- Lessee Signature Block -->
            <div class="signature-column">
                <p><strong>SIGNED for and on BEHALF of the Lessee</strong><br>
                {{ $contract->quotation->customer->name ?? 'Customer' }} by</p>
                <div class="signature-line"></div>
                <p>DIRECTOR</p>
                <div class="signature-line"></div>
                <p>DIRECTOR'S FULL NAME</p>
                <p><strong>In the presence of:</strong></p>
                <div class="signature-line"></div>
                <p>ADVOCATE</p>
                <p>Stamp</p>
                <div class="signature-line"></div>
                <p>ADVOCATE'S NAME</p>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Contract Number: {{ $contract->contract_number }} | Quotation: {{ $contract->quotation->quotation_number ?? 'N/A' }}</p>
        <p>Generated on: {{ $contract->created_at->format('F j, Y g:i A') }}</p>
        <p>This document constitutes a legally binding agreement between the parties.</p>
    </div>
</body>
</html>
