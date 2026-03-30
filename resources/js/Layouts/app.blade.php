<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kenya Power - Dark Fibre Leasing')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --kp-yellow: #FFD700;
            --kp-blue: #0066B3;
            --kp-green: #009639;
            --kp-dark: #003f20;
        }

        .bg-kp-primary {
            background: linear-gradient(to right, var(--kp-blue), var(--kp-green)) !important;
        }

        .btn-primary {
            background-color: var(--kp-yellow);
            border-color: var(--kp-yellow);
            color: var(--kp-dark);
        }

        .btn-primary:hover {
            background-color: #e6c300;
            border-color: #e6c300;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-kp-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-bolt me-2"></i>
                Kenya Power - Dark Fibre Leasing
            </a>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
