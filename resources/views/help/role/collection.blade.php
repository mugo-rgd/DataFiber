@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-danger text-white">
        <h4 class="mb-0">
            <i class="fas fa-hand-holding-usd me-2"></i>
            Collection Guide
        </h4>
    </div>
    <div class="card-body">

        <h3>Collection Workflow</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <i class="fas fa-envelope fa-2x text-kp-blue"></i>
                        <p>1. Email Reminder<br><small>0-30 days</small></p>
                    </div>
                    <div class="col-md-1">→</div>
                    <div class="col-md-3">
                        <i class="fas fa-phone-alt fa-2x text-kp-yellow"></i>
                        <p>2. Phone Call<br><small>31-60 days</small></p>
                    </div>
                    <div class="col-md-1">→</div>
                    <div class="col-md-3">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                        <p>3. Escalate<br><small>61-90 days</small></p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Collection Script Template</h3>
        <div class="card mb-4">
            <div class="card-body">
                <p><strong>For 30-day overdue:</strong></p>
                <blockquote class="blockquote p-3 bg-light">
                    "Dear customer, this is a reminder that invoice #XXXX for amount $XXX is now 30 days overdue.
                    Please make payment at your earliest convenience to avoid service interruption."
                </blockquote>

                <p><strong>For 60+ days overdue:</strong></p>
                <blockquote class="blockquote p-3 bg-light">
                    "Dear customer, your account is severely overdue. Please contact our collections department
                    immediately at 020 3201 000 to arrange payment."
                </blockquote>
            </div>
        </div>

        <div class="alert alert-kp-success">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Tip:</strong> Document all collection activities in the system for audit purposes.
        </div>

    </div>
</div>
@endsection
