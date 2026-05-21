<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Kenya Power - Dark Fibre Leasing Portal</title>
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
            position: relative;
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

        .form-input.error {
            border-color: #dc3545;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: block;
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

        .btn-kp-primary {
            background: var(--kp-yellow);
            color: var(--kp-dark);
        }

        .btn-kp-primary:hover {
            background: #e6c300;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-kp-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
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

        .btn-secondary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Password Toggle Button */
        .btn-toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
            font-size: 1rem;
        }

        .btn-toggle-password:hover {
            color: var(--kp-blue);
        }

        /* Alert Messages */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert-kp-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-kp-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
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

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 9999;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 350px;
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

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        .password-wrapper{
    position: relative;
    width: 100%;
}

.password-wrapper .form-input{
    width: 100%;
    padding-right: 50px; /* space for eye icon */
}

.btn-toggle-password{
    position: absolute;
    top: 50%;
    right: 14px;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    color: #6c757d;
    cursor: pointer;
    padding: 0;
    z-index: 5;

    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-toggle-password:hover{
    color: #198754;
}

.btn-toggle-password:focus{
    outline: none;
    box-shadow: none;
}
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <img src="/images/logo.png" alt="Kenya Power Logo" onerror="this.style.display='none'">
                    <div class="logo-text">
                        <span class="logo-main">KENYA POWER</span>
                        <span class="logo-sub">Dark Fibre Leasing System</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Auth Container -->
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

            <div class="auth-forms">
                <div class="auth-tabs">
                    <div class="auth-tab active" data-tab="login">Login</div>
                    <div class="auth-tab" data-tab="customer-signup">Customer Registration</div>
                    <?php if(app()->environment('local')): ?>
                        <div class="auth-tab" data-tab="admin-signup">Admin Registration</div>
                    <?php endif; ?>
                </div>

                <!-- Login Form -->
                <!-- Login Form -->
<!-- Login Form -->
<form class="auth-form active" id="login-form" method="POST" action="<?php echo e(route('login')); ?>">
    <?php echo csrf_field(); ?>
    <h2 class="form-title">Login to Your Account</h2>

    <?php
        use Illuminate\Support\Str;
        use Illuminate\Cache\RateLimiter;

        // Get email from multiple sources
        $loginEmail = old('email', session('login_email', ''));
        $showAttempts = !empty($loginEmail);

        $maxAttempts = 5;
        $attempts = 0;
        $remainingAttempts = 0;
        $lockedOut = false;
        $lockoutMinutes = 0;

        if ($showAttempts) {
            try {
                $throttleKey = Str::lower($loginEmail) . '|' . request()->ip();
                $limiter = app(RateLimiter::class);

                $attempts = $limiter->attempts($throttleKey);
                $remainingAttempts = max(0, $maxAttempts - $attempts);
                $lockedOut = $limiter->tooManyAttempts($throttleKey, $maxAttempts);

                if ($lockedOut) {
                    $seconds = $limiter->availableIn($throttleKey);
                    $lockoutMinutes = ceil($seconds / 60);
                }
            } catch (\Exception $e) {
                // Fallback if rate limiter fails
                \Log::error('Rate limiter error: ' . $e->getMessage());
            }
        }
    ?>

    <?php if($lockedOut): ?>
        <div class="alert alert-danger">
            <i class="fas fa-lock me-2"></i>
            <strong>Account Temporarily Locked!</strong><br>
            Too many failed login attempts.<br>
            Please try again in <strong><?php echo e($lockoutMinutes); ?> minute(s)</strong>.
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i> <?php echo e($errors->first()); ?>

        </div>
    <?php elseif($showAttempts && !$lockedOut && $attempts > 0 && $remainingAttempts > 0): ?>
        <div class="alert alert-kp-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Login Attempts:</strong> You have <?php echo e($remainingAttempts); ?> out of <?php echo e($maxAttempts); ?> attempt(s) remaining.
            <?php if($remainingAttempts <= 2): ?>
                <br><small>After 5 failed attempts, your account will be locked for 15 minutes.</small>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="Enter your email" required value="<?php echo e(old('email')); ?>" autofocus>
    </div>

   <div class="form-group">
    <label class="form-label">Password</label>

    <div class="password-wrapper">
        <input
            type="password"
            name="password"
            class="form-input <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            placeholder="Enter your password"
            required
            autocomplete="current-password"
        >

        <button type="button" class="btn-toggle-password">
            <i class="fas fa-eye"></i>
        </button>
    </div>

    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <small class="text-danger"><?php echo e($message); ?></small>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

    <div class="form-options">
        <div class="form-check">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Remember Me</label>
        </div>
        <a href="#" class="form-link" id="forgot-password-link">Forgot Password?</a>
    </div>

    <button type="submit" class="btn btn-kp-primary" id="login-btn">
        <i class="fas fa-sign-in-alt me-2"></i>Login
    </button>
</form>

                <!-- Password Reset Form -->
                <form class="auth-form" id="password-reset-form" method="POST" action="<?php echo e(route('password.email')); ?>">
                    <?php echo csrf_field(); ?>
                    <h2 class="form-title">Reset Your Password</h2>
                    <p style="margin-bottom: 1rem;">Enter your email address and we'll send you a link to reset your password.</p>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" placeholder="Enter your email" required>
                    </div>

                    <button type="submit" class="btn btn-kp-primary">
                        <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                    </button>

                    <div style="text-align: center; margin-top: 1rem;">
                        <a href="#" class="form-link" id="show-login-from-reset">Back to Login</a>
                    </div>
                </form>

                <!-- Customer Registration Form -->
                <form class="auth-form" id="customer-signup-form" method="POST" action="<?php echo e(route('register.customer')); ?>">
                    <?php echo csrf_field(); ?>
                    <h2 class="form-title">Create Customer Account</h2>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul style="margin: 0; padding-left: 1rem;">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if(session('success')): ?>
                        <div class="alert alert-kp-success">
                            <i class="fas fa-check-circle me-2"></i> <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" class="form-input" value="<?php echo e(old('company_name')); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" value="<?php echo e(old('email')); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-input" value="<?php echo e(old('phone')); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-input" required autocomplete="new-password">
                        <button type="button" class="btn-toggle-password" type="button">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-input" required autocomplete="new-password">
                        <button type="button" class="btn-toggle-password" type="button">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>
                </form>

                <!-- Admin Registration Form - Disabled -->
                <?php if(app()->environment('local')): ?>
                    <form class="auth-form" id="admin-signup-form" method="POST" action="#">
                        <?php echo csrf_field(); ?>
                        <h2 class="form-title">Request Admin Access</h2>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Admin registration is currently being configured. Please contact the system administrator directly to request admin access.
                        </div>

                        <div style="text-align: center; margin-top: 1.5rem;">
                            <span style="color: var(--kp-gray);">Already have an account? </span>
                            <a href="#" class="form-link" id="show-login-from-admin">Login</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <span class="footer-logo-text">KENYA POWER</span>
                    <span>Dark Fibre Leasing System</span>
                </div>
                <div class="footer-links">
                    <a href="#" class="footer-link">Contact Us</a>
                    <a href="#" class="footer-link">Support</a>
                    <a href="#" class="footer-link">Network Map</a>
                    <a href="#" class="footer-link">FAQ</a>
                    <a href="#" class="footer-link">Service Status</a>
                </div>
            </div>
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> Kenya Power and Lighting Company. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==================== TAB SWITCHING ====================
            const tabs = document.querySelectorAll('.auth-tab');
            const forms = document.querySelectorAll('.auth-form');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');

                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    forms.forEach(form => {
                        form.classList.remove('active');
                        if (form.id === `${tabId}-form`) {
                            form.classList.add('active');
                        }
                    });

                    sessionStorage.setItem('activeTab', tabId);
                });
            });

            // Restore active tab
            const savedTab = sessionStorage.getItem('activeTab');
            if (savedTab && savedTab !== 'login') {
                const tabToActivate = document.querySelector(`[data-tab="${savedTab}"]`);
                if (tabToActivate) tabToActivate.click();
            }

            // ==================== PASSWORD VISIBILITY TOGGLE ====================
            const toggleButtons = document.querySelectorAll('.btn-toggle-password');

            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const parentGroup = this.closest('.form-group');
                    const passwordInput = parentGroup.querySelector('input[type="password"], input[type="text"]');
                    if (passwordInput) {
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', type);
                        this.querySelector('i').classList.toggle('fa-eye');
                        this.querySelector('i').classList.toggle('fa-eye-slash');
                    }
                });
            });

            // ==================== FORM HANDLERS ====================

            // Forgot password link
            const forgotLink = document.getElementById('forgot-password-link');
            if (forgotLink) {
                forgotLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    forms.forEach(f => f.classList.remove('active'));
                    const resetForm = document.getElementById('password-reset-form');
                    if (resetForm) resetForm.classList.add('active');
                    tabs.forEach(t => t.classList.remove('active'));
                });
            }

            // Back to login from reset
            const showLoginFromReset = document.getElementById('show-login-from-reset');
            if (showLoginFromReset) {
                showLoginFromReset.addEventListener('click', function(e) {
                    e.preventDefault();
                    forms.forEach(f => f.classList.remove('active'));
                    const loginFormElement = document.getElementById('login-form');
                    if (loginFormElement) loginFormElement.classList.add('active');
                    tabs.forEach(t => t.classList.remove('active'));
                    const loginTab = document.querySelector('[data-tab="login"]');
                    if (loginTab) loginTab.classList.add('active');
                });
            }

            // Back to login from admin
            const showLoginFromAdmin = document.getElementById('show-login-from-admin');
            if (showLoginFromAdmin) {
                showLoginFromAdmin.addEventListener('click', function(e) {
                    e.preventDefault();
                    forms.forEach(f => f.classList.remove('active'));
                    const loginFormElement = document.getElementById('login-form');
                    if (loginFormElement) loginFormElement.classList.add('active');
                    tabs.forEach(t => t.classList.remove('active'));
                    const loginTab = document.querySelector('[data-tab="login"]');
                    if (loginTab) loginTab.classList.add('active');
                });
            }

            // ==================== LOGIN FORM VALIDATION ====================
            const loginFormElement = document.getElementById('login-form');
            if (loginFormElement) {
                loginFormElement.addEventListener('submit', function(e) {
                    const email = this.querySelector('input[name="email"]');
                    const password = this.querySelector('input[name="password"]');
                    const submitBtn = this.querySelector('button[type="submit"]');

                    if (!email.value.trim()) {
                        e.preventDefault();
                        showToast('Please enter your email address', 'error');
                        email.focus();
                        return false;
                    }

                    if (!password.value) {
                        e.preventDefault();
                        showToast('Please enter your password', 'error');
                        password.focus();
                        return false;
                    }

                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';
                });
            }

            // ==================== TOAST NOTIFICATION ====================
            function showToast(message, type = 'info') {
                const existingToast = document.querySelector('.toast-notification');
                if (existingToast) existingToast.remove();

                const toast = document.createElement('div');
                toast.className = 'toast-notification';

                let bgColor, textColor, icon;

                switch(type) {
                    case 'error':
                        bgColor = '#dc3545';
                        textColor = '#ffffff';
                        icon = 'fa-exclamation-circle';
                        break;
                    case 'warning':
                        bgColor = '#ffc107';
                        textColor = '#856404';
                        icon = 'fa-exclamation-triangle';
                        break;
                    case 'success':
                        bgColor = '#28a745';
                        textColor = '#ffffff';
                        icon = 'fa-check-circle';
                        break;
                    default:
                        bgColor = '#0066B3';
                        textColor = '#ffffff';
                        icon = 'fa-info-circle';
                }

                toast.style.cssText = `
                    background: ${bgColor};
                    color: ${textColor};
                    font-family: inherit;
                `;

                toast.innerHTML = `<i class="fas ${icon}"></i>${message}`;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => toast.remove(), 300);
                }, 5000);
            }

            // Show server errors as toast
            // <?php if($errors->has('email') || $errors->has('password')): ?>
            //     const errorMsg = '<?php echo e(addslashes($errors->first())); ?>';
            //     showToast(errorMsg, 'error');
            // <?php endif; ?>

            <?php if(session('error')): ?>
                showToast('<?php echo e(addslashes(session('error'))); ?>', 'error');
            <?php endif; ?>

            <?php if(session('success')): ?>
                showToast('<?php echo e(addslashes(session('success'))); ?>', 'success');
            <?php endif; ?>

            <?php if(session('warning')): ?>
                showToast('<?php echo e(addslashes(session('warning'))); ?>', 'warning');
            <?php endif; ?>
        });
    </script>
    
</body>
</html>
<?php /**PATH G:\project\darkfibre-crm\resources\views/auth/login.blade.php ENDPATH**/ ?>