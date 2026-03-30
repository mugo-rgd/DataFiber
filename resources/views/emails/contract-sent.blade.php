@extends('emails.layouts.app')


@section('content')
<h2>Contract Ready for Review</h2>

<p>Dear {{ $customer->name }},</p>

<p>Your contract <strong>{{ $contract->contract_number }}</strong> is now ready for your review and approval.</p>

<p><strong>Contract Details:</strong></p>
<ul>
    <li>Contract Number: {{ $contract->contract_number }}</li>
    <li>Related Quotation: {{ $contract->quotation->quotation_number }}</li>
    <li>Generated Date: {{ $contract->created_at->format('F j, Y') }}</li>
</ul>

<p>Please log in to your account to review the contract terms and conditions.</p>

<a href="{{ url('/customer/contracts/' . $contract->id) }}"
   style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
    Review Contract
</a>

<p>If you have any questions, please contact your account manager.</p>

<p>Best regards,<br>KPLC Dark Fibre Team</p>
@endsection
