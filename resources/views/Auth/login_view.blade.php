<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR MS | Login</title> <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
   <style>
        :root {
            --primary-blue: #0a2463;
            --secondary-blue: #1e3a8a;
            --accent-blue: #3a86ff;
            --light-blue: #e6f0ff;
            --dark-text: #121826;
            --light-text: #f8fafc;
            --success-green: #10b981;
            --error-red: #ef4444;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            color: var(--dark-text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        /* Main Card Container */
        .login-card-container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            min-height: 500px;
            max-height: 600px;
        }
        
        /* Left Side - Branding & Info */
        .brand-section {
            flex: 0.8;
            background: linear-gradient(145deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--light-text);
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .brand-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
            z-index: 0;
        }
        
        .brand-content {
            position: relative;
            z-index: 1;
        }
        
        .brand-logo {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            font-family: 'Poppins', sans-serif;
        }
        
        .logo-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .logo-icon i {
            font-size: 1.5rem;
            color: white;
        }
        
        .logo-text {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .brand-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 1rem;
        }
        
        .brand-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
            line-height: 1.5;
            font-weight: 300;
        }
        
        .features-list {
            list-style: none;
            margin: 1.5rem 0;
        }
        
        .features-list li {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: flex-start;
            font-size: 0.9rem;
        }
        
        .features-list i {
            color: var(--accent-blue);
            margin-right: 0.8rem;
            font-size: 1rem;
            margin-top: 0.1rem;
            flex-shrink: 0;
        }
        
        /* Right Side - Login Form */
        .login-section {
            flex: 1;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-header {
            margin-bottom: 2rem;
        }
        
        .login-header h2 {
            font-family: 'Poppins', sans-serif;
            color: var(--primary-blue);
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }
        
        .login-header p {
            color: #64748b;
            font-size: 0.95rem;
        }
        
        .login-form {
            width: 100%;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--primary-blue);
            font-size: 0.9rem;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 10;
            font-size: 1rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.9rem 1rem 0.9rem 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
        }
        
        .form-control:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(58, 134, 255, 0.15);
            outline: none;
            background-color: white;
        }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            z-index: 10;
            font-size: 1rem;
            padding: 5px;
        }
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.8rem;
            font-size: 0.9rem;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
        }
        
        .remember-me input {
            margin-right: 0.5rem;
            width: 16px;
            height: 16px;
            accent-color: var(--accent-blue);
            cursor: pointer;
        }
        
        .remember-me label {
            cursor: pointer;
            color: #475569;
            font-weight: 500;
        }
        
        .forgot-password {
            color: var(--accent-blue);
            text-decoration: none;
            font-weight: 600;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(to right, var(--primary-blue), var(--secondary-blue));
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 1.5rem;
            font-family: 'Poppins', sans-serif;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(10, 36, 99, 0.2);
        }
        
        .btn-login:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #64748b;
            font-size: 0.85rem;
        }
        
        .login-footer a {
            color: var(--accent-blue);
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        /* Alert Styles */
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border-left: 4px solid var(--success-green);
        }
        
        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border-left: 4px solid var(--error-red);
        }
        
        .alert-info {
            background-color: #e0f2fe;
            color: #075985;
            border-left: 4px solid var(--accent-blue);
        }
        
        .alert i {
            margin-right: 0.8rem;
            font-size: 1.1rem;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .login-card {
                flex-direction: column;
                max-width: 500px;
                max-height: none;
                margin: 0 auto;
            }
            
            .brand-section, .login-section {
                padding: 2rem;
            }
            
            .brand-title {
                font-size: 1.6rem;
            }
            
            .features-list {
                display: none;
            }
        }
        
        @media (max-width: 576px) {
            .brand-section, .login-section {
                padding: 1.5rem;
            }
            
            .brand-title {
                font-size: 1.4rem;
            }
            
            .login-header h2 {
                font-size: 1.5rem;
            }
            
            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.8rem;
            }
            
            .logo-text {
                font-size: 1.3rem;
            }
            
            body {
                padding: 15px;
            }
        }
        
        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 0.5rem;
            vertical-align: middle;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Copyright */
        .copyright {
            position: fixed;
            bottom: 15px;
            left: 0;
            width: 100%;
            text-align: center;
            color: #64748b;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="login-card-container">
        <div class="login-card">
            <div class="brand-section">
                <div class="brand-content">
                    <div class="brand-logo">
                        <div class="logo-icon">
                            <img style="height: 50px;" src="{{ asset('assets/logo/white_logo.png') }}" alt="Logo">
                        </div>
                        <div class="logo-text">HR MS</div>
                    </div>
                    
                    <h1 class="brand-title">Human Resource Management System</h1>
                    <p class="brand-subtitle">Streamline your HR operations and employee management</p>
                </div>
            </div>
            
            <div class="login-section">
                <div class="login-container">
                    <div class="login-header">
                        <h2>Welcome Back</h2>
                        <p>Sign in to your HR MS account</p>
                    </div>
                    
                    <div id="alertPlaceholder"></div>
                    
                    <form class="login-form" id="loginForm">
                       @csrf
@if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="fas fa-exclamation-triangle mr-2"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
                        <div class="form-group">
                            <label class="form-label" for="identity">Username or Email</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" class="form-control" id="identity" name="identity" required placeholder="Enter your username or email">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                                <button type="button" class="password-toggle" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-options">
                            <div class="remember-me">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">Remember me</label>
                            </div>
                            <a href="#" class="forgot-password">Forgot password?</a>
                        </div>
                        
                        <button type="submit" class="btn-login" id="loginBtn">
                            <span id="btnText">Sign In</span>
                            <span id="btnSpinner" class="d-none">
                                <span class="spinner"></span> Signing in...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // CSRF Token Setup for jQuery AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Password Toggle Logic
            $('#togglePassword').on('click', function() {
                const passwordInput = $('#password');
                const icon = $(this).find('i');
                const isPassword = passwordInput.attr('type') === 'password';
                passwordInput.attr('type', isPassword ? 'text' : 'password');
                icon.toggleClass('fa-eye fa-eye-slash');
            });
            
            // AJAX Form Submission
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                const btn = $('#loginBtn');
                const btnText = $('#btnText');
                const btnSpinner = $('#btnSpinner');
                
                btn.prop('disabled', true);
                btnText.addClass('d-none');
                btnSpinner.removeClass('d-none');
                $('#alertPlaceholder').empty();

                $.ajax({
                    url: "{{ route('auth.login.process') }}", 
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            showAlert('success', 'Login successful! Redirecting...');
                            setTimeout(() => {
                                window.location.href = res.redirect; // Laravel route url full aayega
                            }, 1000);
                        } else {
                            showAlert('danger', res.message);
                            resetBtn();
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Server error. Please try again.';
                        if(xhr.status === 422) msg = "Invalid data provided.";
                        showAlert('danger', msg);
                        resetBtn();
                    }
                });

                function resetBtn() {
                    btn.prop('disabled', false);
                    btnText.removeClass('d-none');
                    btnSpinner.addClass('d-none');
                }
            });

            function showAlert(type, message) {
                const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                
                const alertHtml = `
                    <div class="alert ${alertClass}">
                        <i class="fas ${icon}"></i>
                        <div>${message}</div>
                    </div>`;
                
                $('#alertPlaceholder').html(alertHtml);
            }
        });
    </script>
</body>
</html>