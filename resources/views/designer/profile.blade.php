<!-- resources/views/designer/profile.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Designer Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h2>Designer Profile</h2>
                        <a href="{{ route('designer.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Personal Information</h4>
                                <p><strong>Name:</strong> {{ $user->name }}</p>
                                <p><strong>Email:</strong> {{ $user->email }}</p>
                                <p><strong>Role:</strong> Designer</p>
                            </div>
                            <div class="col-md-6">
                                <h4>Quick Actions</h4>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary">Edit Profile</button>
                                    <button class="btn btn-outline-primary">Change Password</button>
                                    <button class="btn btn-outline-info">View Portfolio</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
