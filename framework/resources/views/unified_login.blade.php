<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | {{ Hyvikk::get('app_name') }}</title>
    <link rel="icon" href="{{ asset('assets/images/'. Hyvikk::get('icon_img') ) }}" type="icon_img">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #032127;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            max-width: 500px;
            width: 100%;
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            display: flex;
            min-height: 600px;
        }

        .left-panel {
            width: 100%;
            padding: 60px 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo {
            margin-bottom: 60px;
            display: flex;
            justify-content: center;
        }

        .pco-logo {
            display: inline-block;
        }

        .pco-logo-img {
            height: 60px;
            width: auto;
            object-fit: contain;
        }

        .form-heading {
            font-size: 28px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .form-subheading {
            font-size: 14px;
            color: #6B7280;
            margin-bottom: 32px;
        }

        .form-container {
            max-width: 400px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            height: 48px;
            padding: 12px 16px;
            font-size: 16px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            background: #FFFFFF;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #79D1DC;
            box-shadow: 0 0 0 3px rgba(121, 209, 220, 0.1);
        }

        .form-input::placeholder {
            color: #9CA3AF;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid #D1D5DB;
            cursor: pointer;
        }

        .checkbox-label {
            font-size: 14px;
            color: #1a1a1a;
            cursor: pointer;
        }

        .forgot-link {
            color: #79D1DC;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            height: 48px;
            background: #79D1DC;
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .login-button:hover {
            background: #5fc0cc;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(121, 209, 220, 0.3);
        }

        .login-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }


        .form-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #6B7280;
        }

        .signup-link {
            color: #79D1DC;
            font-weight: 500;
            text-decoration: none;
        }

        .signup-link:hover {
            text-decoration: underline;
        }

        .back-to-dashboard {
            text-align: center;
            margin-top: 20px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #6B7280;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .back-button:hover {
            color: #79D1DC;
            background: rgba(121, 209, 220, 0.1);
            text-decoration: none;
        }

        .back-button svg {
            transition: transform 0.2s ease;
        }

        .back-button:hover svg {
            transform: translateX(-2px);
        }


        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #D1FAE5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }

        .alert-danger {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
            }

            .login-container {
                max-width: 100%;
                width: 100%;
                margin: 0 auto;
                min-height: auto;
            }

            .left-panel {
                padding: 40px 30px;
                text-align: center;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            .logo {
                margin-bottom: 40px;
                text-align: center;
            }

            .form-container {
                max-width: 100%;
                width: 100%;
                text-align: center;
            }

            .form-heading {
                font-size: 24px;
                text-align: center;
                margin-bottom: 8px;
            }

            .form-subheading {
                text-align: center;
                margin-bottom: 32px;
            }

            .form-group {
                text-align: left;
                margin-bottom: 20px;
            }

            .form-label {
                text-align: left;
                display: block;
            }

            .form-input {
                text-align: left;
            }

            .form-options {
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 10px;
            }

            .login-button {
                margin: 0 auto;
            }

            .form-footer {
                text-align: center;
            }

            .back-to-dashboard {
                text-align: center;
            }
        }

        /* Extra small screens */
        @media (max-width: 480px) {
            body {
                padding: 5px;
            }

            .left-panel {
                padding: 30px 20px;
            }

            .form-heading {
                font-size: 22px;
            }

            .form-subheading {
                font-size: 13px;
            }

            .form-input {
                height: 44px;
                font-size: 16px; /* Prevents zoom on iOS */
            }

            .login-button {
                height: 44px;
                font-size: 16px;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .forgot-link {
                align-self: flex-end;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Panel - Login Form -->
        <div class="left-panel">
            <div class="logo">
                <div class="pco-logo">
                    <img src="{{ asset('assets/images/pco-flow-logo-black.png') }}" alt="PCO Flow" class="pco-logo-img">
                </div>
            </div>

            <div class="form-container">
                <h1 class="form-heading">Log in to your account</h1>
                <p class="form-subheading">Welcome back! Please enter your details.</p>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if (isset($errors) && $errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('unified.login') }}" id="loginForm">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" 
                               class="form-input" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="Enter your email" 
                               required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" 
                               class="form-input" 
                               id="password" 
                               name="password" 
                               placeholder="••••••••" 
                               required>
                    </div>

                    <div class="form-options">
                        <div class="checkbox-group">
                            <input type="checkbox" class="checkbox" name="remember" id="remember">
                            <label class="checkbox-label" for="remember">Remember</label>
                        </div>
                        <a href="{{ url('forgot-password') }}" class="forgot-link">Forgot password</a>
                    </div>

                    <button type="submit" class="login-button" id="loginButton">
                        Login
                    </button>
                </form>

                {{-- Login footer sign-up link hidden intentionally (backend route retained) --}}

                <div class="back-to-dashboard">
                    <a href="{{ url('/') }}" class="back-button">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6"/>
                        </svg>
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            
            if (!loginForm) return;
            
            loginForm.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value.trim();
                
                // Basic validation
                if (!email || !password) {
                    showError('Please fill in all required fields.');
                    e.preventDefault();
                    return false;
                }
                
                if (!isValidEmail(email)) {
                    showError('Please enter a valid email address.');
                    e.preventDefault();
                    return false;
                }
                
                // Form is valid, allow submission
                return true;
            });
            
            function isValidEmail(email) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailPattern.test(email);
            }
            
            function showError(message) {
                // Create error alert
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger';
                alertDiv.innerHTML = message;
                
                // Insert before form
                const formContainer = document.querySelector('.form-container');
                const form = document.getElementById('loginForm');
                formContainer.insertBefore(alertDiv, form);
                
                // Auto-hide after 5 seconds
                setTimeout(function() {
                    alertDiv.style.transition = 'opacity 0.5s';
                    alertDiv.style.opacity = '0';
                    setTimeout(function() {
                        if (alertDiv.parentNode) alertDiv.parentNode.removeChild(alertDiv);
                    }, 500);
                }, 5000);
            }
            
            
            // Auto-hide existing alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        if (alert.parentNode) alert.parentNode.removeChild(alert);
                    }, 500);
                });
            }, 5000);
            
        });
    </script>
</body>
</html>