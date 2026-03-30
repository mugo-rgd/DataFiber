{{-- resources/views/emails/customer-approved.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Contract Approved by Customer</h2>
        </div>
        <div class="content">
            <p>Dear Admin,</p>

            <p>A contract has been approved by the customer and requires your final approval.</p>

            <p><strong>Contract Details:</strong></p>
            <ul>
                <li><strong>Contract Number:</strong> {{ $contract->contract_number }}</li>
                <li><strong>Customer Approved:</strong> {{ $contract->customer_approved_at->format('F j, Y g:i A') }}</li>
                <li><strong>Project:</strong> {{ $contract->quotation->project_title }}</li>
            </ul>

            <p>Please review and approve the contract to start the work execution.</p>

            <p>Best regards,<br>Contract Management System</p>
        </div>
    </div>
</body>
</html>clear
