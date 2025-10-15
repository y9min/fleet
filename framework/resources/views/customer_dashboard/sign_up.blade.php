<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign Up | {{ Hyvikk::get('app_name') }}</title>
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

        .signup-container {
            max-width: 600px;
            width: 100%;
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            display: flex;
            min-height: 700px;
        }

        .signup-panel {
            width: 100%;
            padding: 60px 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo {
            margin-bottom: 40px;
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
            max-width: 100%;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 16px;
        }

        .form-row .form-group {
            flex: 1;
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

        .radio-group {
            display: flex;
            gap: 24px;
            margin-bottom: 20px;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .radio-input {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .radio-label {
            font-size: 14px;
            color: #1a1a1a;
            cursor: pointer;
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 32px;
        }

        .checkbox {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid #D1D5DB;
            cursor: pointer;
            margin-top: 2px;
        }

        .checkbox-label {
            font-size: 14px;
            color: #1a1a1a;
            cursor: pointer;
            line-height: 1.4;
        }

        .checkbox-label a {
            color: #79D1DC;
            text-decoration: none;
        }

        .checkbox-label a:hover {
            text-decoration: underline;
        }

        .signup-button {
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

        .signup-button:hover {
            background: #5fc0cc;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(121, 209, 220, 0.3);
        }

        .signup-button:disabled {
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

            .signup-panel {
                padding: 30px 20px;
            }

            .form-heading {
                font-size: 24px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .radio-group {
                flex-direction: column;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-panel">
            <div class="logo">
                <div class="pco-logo">
                    <img src="{{ asset('assets/images/pco-flow-logo-black.png') }}" alt="PCO Flow" class="pco-logo-img">
                </div>
            </div>

            <div class="form-container">
                <h1 class="form-heading">Create your account</h1>
                <p class="form-subheading">Join us today! Please fill in your details to get started.</p>

                <div class="register-msg custom-alerts"></div>

                <form id="signupForm" method="POST">
                    @csrf
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="first_name">First Name</label>
                            <input type="text" 
                                   class="form-input" 
                                   id="first_name" 
                                   name="first_name" 
                                   placeholder="Enter your first name" 
                                   required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="last_name">Last Name</label>
                            <input type="text" 
                                   class="form-input" 
                                   id="last_name" 
                                   name="last_name" 
                                   placeholder="Enter your last name" 
                                   required>
                        </div>
                    </div>


                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" 
                                   class="form-input" 
                                   id="email" 
                                   name="email" 
                                   placeholder="Enter your email" 
                                   required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone Number</label>
                            <input type="tel" 
                                   class="form-input" 
                                   id="phone" 
                                   name="phone" 
                                   placeholder="Enter your phone number" 
                                   maxlength="15" 
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="address">Address (Optional)</label>
                        <input type="text" 
                               class="form-input" 
                               id="address" 
                               name="address" 
                               placeholder="Enter your address">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" 
                                   class="form-input" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Type your password" 
                                   required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="confirm_password">Confirm Password</label>
                            <input type="password" 
                                   class="form-input" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   placeholder="Re-type your password" 
                                   required>
                        </div>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" class="checkbox" name="agree" id="agree" required>
                        <label class="checkbox-label" for="agree">
                            I agree to all <a href="{{Hyvikk::frontend('terms')}}">Terms & Conditions</a> and <a href="{{Hyvikk::frontend('privacy_policy')}}">Privacy Policies</a> of the company
                        </label>
                    </div>

                    <button type="submit" class="signup-button" id="signupButton">
                        <div class="spinner d-none" id="spinner"></div>
                        <span id="buttonText">Sign Up</span>
                    </button>
                </form>

                <div class="form-footer">
                    Already have an account? <a href="{{ route('login') }}" class="login-link">Login</a>
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
            const signupForm = document.getElementById('signupForm');
            const signupButton = document.getElementById('signupButton');
            const spinner = document.getElementById('spinner');
            const buttonText = document.getElementById('buttonText');
            
            if (!signupForm) return;
            
            // Name validation - only letters and spaces
            function validateName(input) {
                input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
            }
            
            // Phone validation - only numbers
            function validatePhone(input) {
                input.value = input.value.replace(/[^0-9]/g, '');
            }
            
            // Add event listeners for validation
            document.getElementById('first_name').addEventListener('input', function() {
                validateName(this);
            });
            
            document.getElementById('last_name').addEventListener('input', function() {
                validateName(this);
            });
            
            document.getElementById('phone').addEventListener('input', function() {
                validatePhone(this);
            });
            
            signupForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Show loading spinner
                if (spinner) spinner.classList.remove('d-none');
                if (buttonText) buttonText.style.display = 'none';
                if (signupButton) signupButton.disabled = true;
                
                // Basic validation
                const firstName = document.getElementById('first_name').value.trim();
                const lastName = document.getElementById('last_name').value.trim();
                const email = document.getElementById('email').value.trim();
                const phone = document.getElementById('phone').value.trim();
                const password = document.getElementById('password').value.trim();
                const confirmPassword = document.getElementById('confirm_password').value.trim();
                const agree = document.getElementById('agree').checked;
                
                if (!firstName || !lastName || !email || !phone || !password || !confirmPassword) {
                    showError('Please fill in all required fields.');
                    resetButton();
                    return;
                }
                
                if (!isValidEmail(email)) {
                    showError('Please enter a valid email address.');
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
                
                if (!agree) {
                    showError('Please agree to the terms and conditions.');
                    resetButton();
                    return;
                }
                
                // Submit form
                const formData = new FormData(signupForm);
                
                fetch('{{ url("user-register") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showError('Registration failed. Please try again.');
                        resetButton();
                    } else {
                        showSuccess('Registration successful! You can now login.');
                        setTimeout(() => {
                            window.location.href = '{{ route("login") }}';
                        }, 2000);
                    }
                })
                .catch(error => {
                    showError('Registration failed. Please try again.');
                    resetButton();
                });
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
                const form = document.getElementById('signupForm');
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
                const form = document.getElementById('signupForm');
                formContainer.insertBefore(alertDiv, form);
            }
            
            function resetButton() {
                if (spinner) spinner.classList.add('d-none');
                if (buttonText) buttonText.style.display = 'block';
                if (signupButton) signupButton.disabled = false;
            }
        });
    </script>
</body>
</html>
