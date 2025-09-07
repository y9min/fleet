<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ Hyvikk::get('app_name') }}</title>
    <link rel="icon" href="{{ asset('assets/images/'. Hyvikk::get('icon_img') ) }}" type="icon_img">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <!-- Custom styles from customer dashboard -->
    <link rel="stylesheet" href="{{asset('assets/customer_dashboard/assets/css/soft-ui-dashboard.css?v=345435')}}">
    <link rel="stylesheet" href="{{asset('assets/customer_dashboard/assets/css/style.css?v=1.1234568')}}">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Open Sans', sans-serif;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .login-left {
            background: linear-gradient(45deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            padding: 50px 40px;
        }
        
        .login-right {
            background: url('{{ asset('assets/customer_dashboard/assets/img/svg/pexels-taras-makarenko 1.jpg') }}') center/cover;
            position: relative;
        }
        
        .login-right::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.8), rgba(118, 75, 162, 0.8));
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-container img {
            max-height: 80px;
            max-width: 300px;
        }
        
        .login-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #344767;
            margin-bottom: 10px;
        }
        
        .login-subtitle {
            color: #67748e;
            font-size: 1.1rem;
            margin-bottom: 40px;
        }
        
        .form-control {
            border: 1px solid #d2d6da;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #cb0c9f;
            box-shadow: 0 0 0 0.2rem rgba(203, 12, 159, 0.15);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .forgot-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .forgot-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .alert-custom {
            border-radius: 8px;
            border: none;
            padding: 12px 16px;
        }
        
        
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        @media (max-width: 768px) {
            .login-card {
                margin: 20px;
            }
            .login-left {
                padding: 30px 20px;
            }
            .login-title {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid login-container">
        <div class="row justify-content-center w-100">
            <div class="col-lg-10 col-xl-9">
                <div class="login-card">
                    <div class="row g-0">
                        <div class="col-md-6 login-left">
                            <div class="logo-container">
                                <img src="{{ asset('assets/images/'. Hyvikk::get('logo_img') ) }}" alt="Logo" />
                            </div>
                            
                            <h2 class="login-title">Welcome Back!</h2>
                            <p class="login-subtitle">Sign in to access your dashboard</p>

                            <!-- Alert Messages -->
                            <div id="alert-container">
                                @if(Session::has('error'))
                                <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                                    {{ Session::get('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                @endif

                                @if(Session::has('success'))
                                <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                                    {{ Session::get('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                @endif

                                @if (isset($errors) && $errors->any())
                                <div class="alert alert-danger alert-custom alert-dismissible fade show">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                @endif
                            </div>


                            <!-- Login Form -->
                            <form id="unifiedLoginForm" method="POST" action="{{ route('unified.login') }}">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" id="email" 
                                           value="{{ old('email') }}" placeholder="Enter your email address" 
                                           autocomplete="email" autofocus required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" id="password" 
                                           placeholder="Enter your password" autocomplete="current-password" required>
                                </div>

                                <div class="row align-items-center mb-3">
                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" 
                                                   id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">
                                                Remember me
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-login btn-primary w-100" id="loginBtn">
                                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                    <span id="loginBtnText">Sign In</span>
                                </button>
                            </form>

                            <!-- Sign Up Link -->
                            <div class="text-center mt-4">
                                <p class="mb-0">Don't have an account? 
                                    <a href="{{ route('sign_up') }}" class="forgot-link">Sign Up</a>
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-md-6 login-right d-none d-md-block">
                            <!-- Background image handled by CSS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('unifiedLoginForm');
            
            // Handle form submission
            loginForm.addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('loginBtn');
                const spinner = submitBtn.querySelector('.spinner-border');
                const btnText = document.getElementById('loginBtnText');
                
                // Show loading state
                spinner.classList.remove('d-none');
                btnText.textContent = 'Signing In...';
                submitBtn.disabled = true;
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    if (!alert.classList.contains('alert-permanent')) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                });
            }, 5000);
        });
    </script>
</body>

</html>