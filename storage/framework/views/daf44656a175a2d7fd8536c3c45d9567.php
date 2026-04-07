<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <img src="/images/logo.png" alt="Kenya Power Logo">
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
   <form class="auth-form active" id="login-form" method="POST" action="<?php echo e(route('login')); ?>">
    <?php echo csrf_field(); ?>
    <h2 class="form-title">Login to Your Account</h2>

    <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-input" placeholder="Enter your email" required value="<?php echo e(old('email')); ?>">
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span style="color: red; font-size: 0.875rem;"><?php echo e($message); ?></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>


    <div class="form-group">
        <label class="form-label">Password</label>
       <input type="password" name="password" class="form-input" placeholder="Enter your password" required
       autocomplete="current-password">
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span style="color: red; font-size: 0.875rem;"><?php echo e($message); ?></span>
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

    <button type="submit" class="btn btn-primary">Login</button>
</form>
<form class="auth-form" id="password-reset-form" method="POST" action="<?php echo e(route('password.email')); ?>">
    <?php echo csrf_field(); ?>
    <h2 class="form-title">Reset Your Password</h2>
    <p style="margin-bottom: 1rem;">Enter your email address and we'll send you a link to reset your password.</p>

    <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-input" placeholder="Enter your email" required>
    </div>

    <button type="submit" class="btn btn-primary">Send Reset Link</button>

    <div style="text-align: center; margin-top: 1rem;">
        <a href="#" class="form-link" id="show-login-from-reset">Back to Login</a>
    </div>
</form>



                <!-- Customer Registration Form -->
<form class="auth-form" id="customer-signup-form" method="POST" action="<?php echo e(route('register.customer')); ?>">
    <?php echo csrf_field(); ?>

    <?php if($errors->any()): ?>
        <div style="color: red; margin-bottom: 1rem; padding: 0.5rem; background: #ffe6e6; border-radius: 4px;">
            <ul style="margin: 0; padding-left: 1rem;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div style="color: green; margin-bottom: 1rem; padding: 0.5rem; background: #e6ffe6; border-radius: 4px;">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <h2 class="form-title">Create Customer Account</h2>

    <div class="form-group">
        <label class="form-label">Company Name</label>
        <input type="text" name="company_name" class="form-input" value="<?php echo e(old('company_name')); ?>" required>
        <?php $__errorArgs = ['company_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span style="color: red; font-size: 0.875rem;"><?php echo e($message); ?></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-input" value="<?php echo e(old('email')); ?>" required>
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span style="color: red; font-size: 0.875rem;"><?php echo e($message); ?></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label class="form-label">Phone Number</label>
        <input type="tel" name="phone" class="form-input" value="<?php echo e(old('phone')); ?>" required>
        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span style="color: red; font-size: 0.875rem;"><?php echo e($message); ?></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-input" required autocomplete="new-password">
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span style="color: red; font-size: 0.875rem;"><?php echo e($message); ?></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-input" required autocomplete="new-password">
    </div>

    <button type="submit" class="btn btn-secondary">Create Account</button>
</form>




                <!-- Admin Registration Form -->
                <form class="auth-form" id="admin-signup-form">
                    <h2 class="form-title">Request Admin Access</h2>
                    <p style="color: var(--kp-gray); margin-bottom: 1.5rem;">Admin accounts require verification. Please provide your details and we'll contact you for verification.</p>

                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-input" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Work Email Address</label>
                        <input type="email" class="form-input" placeholder="Enter your work email" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Employee ID</label>
                        <input type="text" class="form-input" placeholder="Enter your employee ID" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Department</label>
                        <select class="form-input" required>
                            <option value="">Select your department</option>
                            <option value="it">IT Department</option>
                            <option value="network">Network Operations</option>
                            <option value="sales">Sales & Marketing</option>
                            <option value="customer">Customer Support</option>
                            <option value="finance">Finance</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Justification for Access</label>
                        <textarea class="form-input" placeholder="Explain why you need admin access" rows="3" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-secondary">Submit Request</button>

                    <div style="text-align: center; margin-top: 1.5rem;">
                        <span style="color: var(--kp-gray);">Already have an account? </span>
                        <a href="#" class="form-link" id="show-login-from-admin">Login</a>
                    </div>
                </form>
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
            // Tab switching functionality
            const tabs = document.querySelectorAll('.auth-tab');
            const forms = document.querySelectorAll('.auth-form');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');

                    // Update active tab
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    // Show corresponding form
                    forms.forEach(form => {
                        form.classList.remove('active');
                        if (form.id === `${tabId}-form`) {
                            form.classList.add('active');
                        }
                    });
                });
            });

            // Quick links between forms
            // document.getElementById('show-customer-signup').addEventListener('click', function(e) {
            //     e.preventDefault();
            //     document.querySelector('[data-tab="customer-signup"]').click();
            // });

            // document.getElementById('show-admin-signup').addEventListener('click', function(e) {
            //     e.preventDefault();
            //     document.querySelector('[data-tab="admin-signup"]').click();
            // });

            // document.getElementById('show-login-from-customer').addEventListener('click', function(e) {
            //     e.preventDefault();
            //     document.querySelector('[data-tab="login"]').click();
            // });

            // document.getElementById('show-login-from-admin').addEventListener('click', function(e) {
            //     e.preventDefault();
            //     document.querySelector('[data-tab="login"]').click();
            // });

            // // Form submission
            // const allForms = document.querySelectorAll('.auth-form');
            // allForms.forEach(form => {
            //     form.addEventListener('submit', function(e) {
            //         e.preventDefault();
            //         // Here you would typically handle form submission to your backend
            //         alert('Form submission would be handled here. In a real application, this would connect to your authentication system.');
            //     });
            // });
        });
        document.getElementById('forgot-password-link').addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
    document.getElementById('password-reset-form').classList.add('active');

    // Update tabs if needed
    document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
});

document.getElementById('show-login-from-reset').addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
    document.getElementById('login-form').classList.add('active');

    // Update tabs if needed
    document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
    document.querySelector('[data-tab="login"]').classList.add('active');
});

    </script>
</body>
</html>
<?php /**PATH G:\project\darkfibre-crm\resources\views/auth/login.blade.php ENDPATH**/ ?>