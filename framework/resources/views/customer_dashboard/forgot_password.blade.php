<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password | {{ Hyvikk::get('app_name') }}</title>
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

        .forgot-container {
            max-width: 500px;
            width: 100%;
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            display: flex;
            min-height: 600px;
        }

        .forgot-panel {
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
            text-align: center;
        }

        .form-subheading {
            font-size: 14px;
            color: #6B7280;
            margin-bottom: 32px;
            text-align: center;
        }

        .form-container {
            max-width: 400px;
            margin: 0 auto;
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

        .reset-button {
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

        .reset-button:hover {
            background: #5fc0cc;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(121, 209, 220, 0.3);
        }

        .reset-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid #FFFFFF;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .form-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #6B7280;
        }

        .login-link {
            color: #79D1DC;
            font-weight: 500;
            text-decoration: none;
        }

        .login-link:hover {
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
            }

            .forgot-panel {
                padding: 30px 20px;
            }

            .form-heading {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="forgot-panel">
            <div class="logo">
                <div class="pco-logo">
                    <img src="{{ asset('assets/images/pco-flow-logo-black.png') }}" alt="PCO Flow" class="pco-logo-img">
                </div>
            </div>

            <div class="form-container">
                @if(Route::currentRouteName() == "new_password")
                    <h1 class="form-heading">Reset your password</h1>
                    <p class="form-subheading">Enter your new password below.</p>

                    <div class="msg-forget-email custom-alerts"></div>

                    <form method="post" id="reset-password-email">
                        @csrf
                        <input type="hidden" name="token" value="{{$token}}">
                        
                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" 
                                   class="form-input" 
                                   id="email" 
                                   name="email" 
                                   value="{{ isset($_GET['email']) ? $_GET['email'] : '' }}" 
                                   placeholder="Enter your email" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">New Password</label>
                            <input type="password" 
                                   class="form-input" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter your new password" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password_confirmation">Confirm Password</label>
                            <input type="password" 
                                   class="form-input" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="Confirm your new password" 
                                   required>
                        </div>

                        <button type="submit" class="reset-button" id="resetButton">
                            <div class="spinner d-none" id="spinner"></div>
                            <span id="buttonText">Reset Password</span>
                        </button>
                    </form>
                @else
                    <h1 class="form-heading">Reset your password</h1>
                    <p class="form-subheading">You will receive an email within a few minutes to reset your password.</p>

                    <div class="msg-forget-email custom-alerts"></div>

                    <form method="post" id="forget-password-email">
                        @csrf
                        
                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" 
                                   class="form-input" 
                                   id="email" 
                                   name="email" 
                                   placeholder="Enter your email" 
                                   required>
                        </div>

                        <button type="submit" class="reset-button" id="resetButton">
                            <div class="spinner d-none" id="spinner"></div>
                            <span id="buttonText">Send Reset Link</span>
                        </button>
                    </form>
                @endif

                <div class="form-footer">
                    Remember your password? <a href="{{ route('login') }}" class="login-link">Login</a>
                </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const resetForm = document.getElementById('reset-password-email') || document.getElementById('forget-password-email');
            const resetButton = document.getElementById('resetButton');
            const spinner = document.getElementById('spinner');
            const buttonText = document.getElementById('buttonText');
            
            if (!resetForm) return;
            
            resetForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Show loading spinner
                if (spinner) spinner.classList.remove('d-none');
                if (buttonText) buttonText.style.display = 'none';
                if (resetButton) resetButton.disabled = true;
                
                const email = document.getElementById('email').value.trim();
                
                if (!email) {
                    showError('Please enter your email address.');
                    resetButton();
                    return;
                }
                
                if (!isValidEmail(email)) {
                    showError('Please enter a valid email address.');
                    resetButton();
                    return;
                }
                
                // Check if this is a password reset form
                const passwordField = document.getElementById('password');
                if (passwordField) {
                    const password = passwordField.value.trim();
                    const confirmPassword = document.getElementById('password_confirmation').value.trim();
                    
                    if (!password || !confirmPassword) {
                        showError('Please fill in all password fields.');
                        resetButton();
                        return;
                    }
                    
                    if (password !== confirmPassword) {
                        showError('Passwords do not match.');
                        resetButton();
                        return;
                    }
                    
                    if (password.length < 6) {
                        showError('Password must be at least 6 characters long.');
                        resetButton();
                        return;
                    }
                }
                
                // Submit form
                const formData = new FormData(resetForm);
                
                @if(Route::currentRouteName() == "new_password")
                    fetch('{{ url("reset-password") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            showError('Password reset failed. Please try again.');
                            resetButton();
                        } else {
                            showSuccess('Password reset successful! You can now login.');
                            setTimeout(() => {
                                window.location.href = '{{ route("login") }}';
                            }, 2000);
                        }
                    })
                    .catch(error => {
                        showError('Password reset failed. Please try again.');
                        resetButton();
                    });
                @else
                    fetch('{{ url("forgot-password") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            showError('Failed to send reset link. Please try again.');
                            resetButton();
                        } else {
                            showSuccess('Reset link sent! Please check your email.');
                        }
                    })
                    .catch(error => {
                        showError('Failed to send reset link. Please try again.');
                        resetButton();
                    });
                @endif
            });
            
            function isValidEmail(email) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailPattern.test(email);
            }
            
            function showError(message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger';
                alertDiv.innerHTML = message;
                
                const formContainer = document.querySelector('.form-container');
                const form = resetForm;
                formContainer.insertBefore(alertDiv, form);
                
                setTimeout(function() {
                    alertDiv.style.transition = 'opacity 0.5s';
                    alertDiv.style.opacity = '0';
                    setTimeout(function() {
                        if (alertDiv.parentNode) alertDiv.parentNode.removeChild(alertDiv);
                    }, 500);
                }, 5000);
            }
            
            function showSuccess(message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success';
                alertDiv.innerHTML = message;
                
                const formContainer = document.querySelector('.form-container');
                const form = resetForm;
                formContainer.insertBefore(alertDiv, form);
            }
            
            function resetButton() {
                if (spinner) spinner.classList.add('d-none');
                if (buttonText) buttonText.style.display = 'block';
                if (resetButton) resetButton.disabled = false;
            }
        });
    </script>
</body>
</html>