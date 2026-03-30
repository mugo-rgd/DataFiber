<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Kenya Power Dark Fibre Leasing</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --kp-yellow: #FFD700;
            --kp-blue: #0066B3;
            --kp-green: #009639;
            --kp-dark: #003f20;
            --kp-light: #f8f9fa;
            --kp-gray: #6c757d;
            --kp-white: #ffffff;
            --border-radius: 8px;
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: linear-gradient(to right, var(--kp-blue), var(--kp-green));
            color: white;
            padding: 0.8rem 0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header-content {
            display: flex;
            align-items: center;
        }

        .logo {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .logo img {
            height: 50px;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .logo-main {
            font-weight: 700;
            font-size: 1.4rem;
        }

        .logo-sub {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        /* Page container */
        .auth-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 20px;
        }

        .auth-card {
            background: #fff;
            border-radius: var(--border-radius);
            width: 100%;
            max-width: 900px;
            display: flex;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .auth-welcome {
            flex: 1;
            background: linear-gradient(to bottom right, var(--kp-blue), var(--kp-green));
            color: #fff;
            padding: 3rem;
            display: flex;
            justify-content: center;
            flex-direction: column;
        }

        .auth-welcome h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .auth-welcome p {
            opacity: 0.9;
            font-size: 1rem;
        }

        .auth-form-wrapper {
            flex: 1;
            padding: 2.5rem;
        }

        .form-title {
            font-size: 1.7rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--kp-dark);
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.4rem;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border-radius: var(--border-radius);
            border: 1px solid #ddd;
            transition: 0.3s;
            font-size: 1rem;
        }

        .form-input:focus {
            border-color: var(--kp-blue);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,102,179,0.1);
        }

        .btn {
            display: block;
            width: 100%;
            padding: 0.75rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 1rem;
            margin-top: 1rem;
            transition: 0.2s;
        }

        .btn-primary {
            background: var(--kp-yellow);
            color: var(--kp-dark);
        }

        .btn-primary:hover {
            background: #e6c300;
            transform: translateY(-2px);
        }

        .form-link {
            color: var(--kp-blue);
            text-decoration: none;
            display: block;
            margin-top: 1rem;
            font-size: 0.9rem;
            text-align: center;
        }

        .form-link:hover {
            text-decoration: underline;
        }

        footer {
            padding: 1.5rem 0;
            background: white;
            text-align: center;
            color: var(--kp-gray);
            border-top: 1px solid #eee;
        }
         :root {
            --kp-yellow: #FFD700;
            --kp-blue: #0066B3;
            --kp-green: #009639;
            --kp-dark: #003f20;
            --kp-light: #f8f9fa;
            --kp-gray: #6c757d;
            --kp-white: #ffffff;
            --border-radius: 8px;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            background: linear-gradient(to right, var(--kp-blue), var(--kp-green));
            color: white;
            padding: 0.8rem 0;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo img {
            height: 50px;
            width: auto;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
        }

        .logo-main {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .logo-sub {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        /* Auth Container */
        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 2rem 0;
        }

        .auth-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 900px;
            overflow: hidden;
            display: flex;
        }

        .auth-welcome {
            flex: 1;
            background: linear-gradient(to bottom right, var(--kp-blue), var(--kp-green));
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-welcome h2 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }

        .auth-welcome p {
            margin-bottom: 1.5rem;
            opacity: 0.9;
        }

        .auth-features {
            list-style: none;
            margin-top: 2rem;
        }

        .auth-features li {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1rem;
        }

        .auth-features i {
            color: var(--kp-yellow);
        }

        .auth-forms {
            flex: 1;
            padding: 2rem;
        }

        .auth-tabs {
            display: flex;
            border-bottom: 1px solid #eee;
            margin-bottom: 2rem;
        }

        .auth-tab {
            padding: 1rem 1.5rem;
            cursor: pointer;
            font-weight: 600;
            color: var(--kp-gray);
            border-bottom: 3px solid transparent;
            transition: var(--transition);
        }

        .auth-tab.active {
            color: var(--kp-blue);
            border-bottom: 3px solid var(--kp-blue);
        }

        .auth-form {
            display: none;
        }

        .auth-form.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        .form-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--kp-dark);
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #555;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--kp-blue);
            box-shadow: 0 0 0 3px rgba(0, 102, 179, 0.1);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-link {
            color: var(--kp-blue);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .form-link:hover {
            text-decoration: underline;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
            justify-content: center;
        }

        .btn-primary {
            background: var(--kp-yellow);
            color: var(--kp-dark);
        }

        .btn-primary:hover {
            background: #e6c300;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: var(--kp-green);
            color: white;
        }

        .btn-secondary:hover {
            background: #00802c;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: #eee;
        }

        .divider-text {
            padding: 0 1rem;
            color: var(--kp-gray);
            font-size: 0.9rem;
        }

        .social-login {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .btn-social {
            background: #f5f5f5;
            color: #333;
            justify-content: center;
        }

        .btn-social:hover {
            background: #e8e8e8;
        }

        /* Footer */
        footer {
            background: white;
            padding: 2rem 0;
            margin-top: 3rem;
            border-top: 1px solid #eee;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-logo {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .footer-logo-text {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--kp-blue);
        }

        .footer-links {
            display: flex;
            gap: 1.5rem;
        }

        .footer-link {
            color: var(--kp-gray);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-link:hover {
            color: var(--kp-blue);
        }

        .copyright {
            color: var(--kp-gray);
            font-size: 0.9rem;
            margin-top: 1.5rem;
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .auth-card {
                flex-direction: column;
            }

            .auth-welcome {
                padding: 2rem;
            }
        }

        @media (max-width: 600px) {
            .social-login {
                grid-template-columns: 1fr;
            }

            .footer-content {
                flex-direction: column;
                gap: 1.5rem;
                text-align: center;
            }

            .footer-links {
                flex-wrap: wrap;
                justify-content: center;
            }

            .auth-tabs {
                flex-direction: column;
            }

            .auth-tab {
                text-align: center;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>

<header>
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <img src="/images/logo.png" alt="Kenya Power">
                <div class="logo-text">
                    <span class="logo-main">KENYA POWER</span>
                    <span class="logo-sub">Dark Fibre Leasing System</span>
                </div>
            </div>
        </div>
    </div>
</header>


<div class="auth-container">
    <div class="auth-card">

      <div class="auth-welcome">
                <h2>Welcome to Kenya Power Dark Fibre Leasing</h2>
                <p>Access Kenya's premier dark fibre network with reliable, high-speed connectivity solutions for businesses and service providers.</p>

                <ul class="auth-features">
                    <li><i class="fas fa-bolt"></i> High-speed fibre optic network</li>
                    <li><i class="fas fa-shield-alt"></i> Secure and reliable connectivity</li>
                    <li><i class="fas fa-map-marker-alt"></i> Nationwide coverage</li>
                    <li><i class="fas fa-headset"></i> 24/7 technical support</li>
                </ul>
            </div>

        <div class="auth-form-wrapper">

            <h2 class="form-title">Create New Password</h2>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input"
       value="{{ $email }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-input"
                           autocomplete="new-password" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                           class="form-input" required>
                </div>

                <button class="btn btn-primary" type="submit">Reset Password</button>

                <a href="{{ route('login') }}" class="form-link">← Back to Login</a>

            </form>
        </div>
    </div>
</div>


<footer>
    &copy; {{ date('Y') }} Kenya Power and Lighting Company. All rights reserved.
</footer>

</body>
</html>
