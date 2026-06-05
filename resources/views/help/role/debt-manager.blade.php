@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-danger text-white">
        <h4 class="mb-0">
            <i class="fas fa-chart-simple me-2"></i>
            Debt Manager Help Guide
        </h4>
    </div>
    <div class="card-body">

        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Urgent:</strong> Total overdue: <strong>$3,202,608.66 USD</strong> | <strong>KSh 43,440,500.10</strong> across 104 invoices
        </div>

        <h3>Aging Analysis</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>Age Bucket</th><th>Invoices</th><th>USD Outstanding</th><th>KES Outstanding</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>0-30 days</td>
                        <td>53</td>
                        <td>$1,601,202.60</td>
                        <td>KSh 21,720,250.05</td>
                        <td>Send reminder</td>
                    </tr>
                    <tr>
                        <td>61-90 days</td>
                        <td>51</td>
                        <td>$1,601,406.06</td>
                        <td>KSh 21,720,250.05</td>
                        <td>Call customer</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3>Top Debtors to Focus On</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>Customer</th><th>Outstanding</th><th>Days Overdue</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <tr><td>MINISTRY OF ICT</td><td class="text-danger">KSh 23,006,204.10</td><td>83 days</td><td><button class="btn btn-sm btn-kp-warning">Contact</button></td></tr>
                    <tr><td>KENGEN PLC</td><td class="text-danger">KSh 18,506,496.00</td><td>83 days</td><td><button class="btn btn-sm btn-kp-warning">Contact</button></td></tr>
                    <tr><td>SAFARICOM PLC</td><td class="text-danger">$746,310.18</td><td>83 days</td><td><button class="btn btn-sm btn-kp-warning">Contact</button></td></tr>
                    <tr><td>JAMII TELECOMMUNICATIONS LTD</td><td class="text-danger">$510,001.02</td><td>83 days</td><td><button class="btn btn-sm btn-kp-warning">Contact</button></td></tr>
                    <tr><td>AIRTEL NETWORKS KENYA LIMITED</td><td class="text-danger">$396,155.40</td><td>83 days</td><td><button class="btn btn-sm btn-kp-warning">Contact</button></td></tr>
                </tbody>
            </table>
        </div>

        <h3>Collection Workflow</h3>
        <div class="card mb-4">
            <div class="card-body">
                <ol>
                    <li><strong>0-30 Days Overdue</strong> - Send automated email reminder</li>
                    <li><strong>31-60 Days Overdue</strong> - Call customer and send follow-up email</li>
                    <li><strong>61-90 Days Overdue</strong> - Escalate to account manager</li>
                    <li><strong>90+ Days Overdue</strong> - Legal review and possible suspension</li>
                </ol>
            </div>
        </div>

        <h3>Using AI Analytics</h3>
        <div class="card mb-4">
            <div class="card-body">
                <p>The AI Analytics module helps predict payment behavior:</p>
                <ul>
                    <li><strong>Payment Prediction</strong> - Forecast likely payment dates</li>
                    <li><strong>Risk Scoring</strong> - Identify high-risk customers</li>
                    <li><strong>Collection Priority</strong> - Rank customers by collection urgency</li>
                    <li><strong>Trend Analysis</strong> - Track payment patterns over time</li>
                </ul>
                <a href="{{ route('finance.ai-analytics.predictive') }}" class="btn btn-sm btn-kp-primary">
                    <i class="fas fa-brain"></i> Open AI Analytics
                </a>
            </div>
        </div>

        <div class="alert alert-kp-success">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Success Tip:</strong> Document all collection activities in the system. This creates an audit trail and helps with legal escalation if needed.
        </div>

    </div>
</div>
@endsection
