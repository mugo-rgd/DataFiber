{{-- resources/views/emails/contract-generated.blade.php --}}
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
            <h2>New Contract Generated</h2>
        </div>
        <div class="content">
            <p>Dear Admin,</p>

            <p>A new contract has been generated and requires customer approval.</p>

            <p><strong>Contract Details:</strong></p>
            <ul>
                <li><strong>Contract Number:</strong> {{ $contract->contract_number }}</li>
                <li><strong>Project:</strong> {{ $contract->quotation->project_title }}</li>
                <li><strong>Customer:</strong> {{ $contract->quotation->customer->name }}</li>
                <li><strong>Generated Date:</strong> {{ $contract->created_at->format('F j, Y') }}</li>
            </ul>

            <p>Please monitor the system for customer approval.</p>

            <p>Best regards,<br>Contract Management System</p>
        </div>
    </div>
</body>
</html>
