<!DOCTYPE html>
<html>
<head>
    <title>New Contact Form Submission</title>
</head>
<body>
    <h2>New Contact Form Submission</h2>

    <p><strong>Name:</strong> {{ $data['name'] }}</p>
    <p><strong>Email:</strong> {{ $data['email'] }}</p>
    @if($data['company'])
    <p><strong>Company:</strong> {{ $data['company'] }}</p>
    @endif
    <p><strong>Subject:</strong> {{ $data['subject'] }}</p>
    <p><strong>Category:</strong> {{ ucfirst($data['category']) }}</p>
    <p><strong>Message:</strong></p>
    <p>{{ $data['message'] }}</p>

    @if($data['newsletter'] ?? false)
    <p><em>User subscribed to newsletter</em></p>
    @endif
</body>
</html>
