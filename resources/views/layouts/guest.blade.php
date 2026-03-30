<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') - DarkFibre CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .login-header {
            background: linear-gradient(135deg, #343a40 0%, #495057 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-body {
            padding: 2rem;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .login-logo {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .login-footer {
            text-align: center;
            padding: 1rem 2rem;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }

        /* Mobile optimizations */
        @media (max-width: 575.98px) {
            .login-container {
                padding: 10px;
            }

            .login-card {
                max-width: 100%;
                margin: 10px;
            }

            .login-header {
                padding: 1.5rem;
            }

            .login-body {
                padding: 1.5rem;
            }

            .login-logo {
                font-size: 2rem;
            }
        }

        /* Very small screens */
        @media (max-width: 375px) {
            .login-header {
                padding: 1rem;
            }

            .login-body {
                padding: 1rem;
            }

            .btn {
                padding: 0.6rem;
            }
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-network-wired"></i>
                </div>
                <h4 class="mb-0">DarkFibre CRM</h4>
                <p class="mb-0 mt-2 opacity-75">Customer Portal</p>
            </div>

            <!-- Body -->
            <div class="login-body">
                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>

            <!-- Footer -->
            <div class="login-footer">
                <small class="text-muted">
                    &copy; {{ date('Y') }} DarkFibre CRM. All rights reserved.
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced form handling
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');

            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="loading"></span> Processing...';
                    }
                });
            });

            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert.classList.contains('show')) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
