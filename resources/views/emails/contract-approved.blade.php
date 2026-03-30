@extends('emails.layouts.app')

@section('content')
<h2>Contract Approved</h2>

<p>Dear {{ $customer->name }},</p>

<p>Your contract <strong>{{ $contract->contract_number }}</strong> has been approved and is now active.</p>

<p><strong>Contract Details:</strong></p>
<ul>
    <li>Contract Number: {{ $contract->contract_number }}</li>
    <li>Status: Approved</li>
    <li>Approval Date: {{ now()->format('F j, Y') }}</li>
</ul>

<p>You can now proceed with the next steps in our service delivery process.</p>

<a href="{{ url('/customer/contracts/' . $contract->id) }}"
   style="background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
    View Contract
</a>

<p>Thank you for choosing KPLC Dark Fibre services.</p>

<p>Best regards,<br>KPLC Dark Fibre Team</p>
@endsection
