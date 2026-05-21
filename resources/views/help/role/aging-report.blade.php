@extends('layouts.role-help')

@section('role-help-content')
<div class="card shadow-sm">
    <div class="card-header bg-danger text-white">
        <h4 class="mb-0">
            <i class="fas fa-chart-simple me-2"></i>
            Aging Report Guide
        </h4>
    </div>
    <div class="card-body">

        <h3>Aging Summary</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>Age Bucket</th><th>Invoices</th><th>USD Amount</th><th>KES Amount</th></tr>
                </thead>
                <tbody>
                    <tr><td>0-30 days</td><td>53</td><td>$1,601,202.60</td><td>KSh 21,720,250.05</td></tr>
                    <tr><td>31-60 days</td><td>0</td><td>$0.00</td><td>KSh 0.00</td></tr>
                    <tr><td>61-90 days</td><td>51</td><td>$1,601,406.06</td><td>KSh 21,720,250.05</td></tr>
                    <tr><td>90+ days</td><td>0</td><td>$0.00</td><td>KSh 0.00</td></tr>
                </tbody>
            </table>
        </div>

        <h3>Top Debtors</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>Customer</th><th>Invoices</th><th>Outstanding</th><th>Days</th></tr>
                </thead>
                <tbody>
                    <tr><td>MINISTRY OF ICT</td><td>2</td><td class="text-danger">KSh 23,006,204.10</td><td>83</td></tr>
                    <tr><td>KENGEN PLC</td><td>2</td><td class="text-danger">KSh 18,506,496.00</td><td>83</td></tr>
                    <tr><td>SAFARICOM PLC</td><td>2</td><td class="text-danger">$746,310.18</td><td>83</td></tr>
                </tbody>
            </table>
        </div>

        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Priority Action:</strong> Contact top debtors immediately to arrange payment.
        </div>

    </div>
</div>
@endsection
