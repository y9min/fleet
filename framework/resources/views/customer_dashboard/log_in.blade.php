@extends('customer_dashboard.layouts.app')

@section('title')
    <title>@lang('frontend.login') | {{ Hyvikk::get('app_name') }}</title>
@endsection

@section('content')
    <section class="position-relative">
        <div class="page-header">
            <div class="container">
                <div class="msg-login custom-alerts"></div>
                <div class="row">
                    <div class="col-xl-4 col-lg-5 col-md-12 d-flex flex-column">
                        <div
                            class="card card-plain mt-7 mt-sm-7 mt-md-7 mt-lg-6 mt-xl-6 ps-0 ps-sm-0 ps-md-0 ps-lg-5 ps-xl-5">
                            <div class="card-header pb-0 text-left bg-transparent">
                                <p class="font-weight-bolder login_title">@lang('frontend.Welcome')</p>
                                <p class="mb-0 login-content">@lang('frontend.Access_Customer')</p>
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div  id="successAlert" class="alert alert-success xs-mt" style="margin-bottom: 35px !important;">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if (isset($errors) && $errors->any())
                                    <div id="errorAlert"  class="alert alert-danger p-1">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li style="color:#fff">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form role="form" method="POST" action="{{ url('user-login') }}" id="loginForm">
                                    @csrf
                                    <label class="form_label">@lang('frontend.Email_Id')</label>
                                    <div class="mb-3">
                                        <input type="email" class="form-control user-email" name="email"
                                            value="{{ old('email') }}" placeholder="Enter your Email Address"
                                            aria-label="Email" aria-describedby="email-addon">
                                            <span class="focus-bg error-email1"></span>
                                    </div>

                                    <label class="form_label">@lang('frontend.Password')</label>
                                    <div class="mb-0">
                                        <input type="password" class="form-control user-pass" id="password" name="password"
                                            placeholder="Enter your Password" aria-label="Password"
                                            aria-describedby="password-addon">
                                            <span class="focus-bg error-password1"></span>
                                    </div>

                                    <div class="forgot_pass_link mt-2">
                                        <a href="{{ url('forgot-password') }}" class="me-2">@lang('frontend.Forgot_Password')</a>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn w-100 mt-3 mb-0 login_btn pt-2 pb-2">
                                            <div class="spinner-border text-light hide-1 d-none"
                                            role="status">
                                            <span class="sr-only"></span>
                                            </div>
                                            <div class="hide-2">
                                                @lang('frontend.login')
                                            </div>
                                        </button>
                                    </div>
                                </form>

                                <div
                                    class="pt-4 px-lg-2 px-1 d-flex justify-content-center justify-content-sm-center justify-content-md-center justify-content-lg-start justify-content-xl-start">
                                    <p class="mb-4 text-sm mx-auto">
                                        @lang('frontend.do_have')
                                        <a href="{{ route('sign_up') }}" class="font-weight-bolder"
                                            style="color:rgba(33, 82, 255, 1);">@lang('frontend.sign_up')</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-0 col-lg-6 col-xl-6 ">
                        <div class="oblique position-absolute top-0 h-100 d-lg-block d-none me-n8">
                            <div class="oblique-image bg-cover position-absolute fixed-top ms-auto h-100 z-index-0 ms-n6"
                                style="background-image:url('{{ asset('assets/customer_dashboard/assets/img/svg/pexels-taras-makarenko\ 1.jpg') }}')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
<script>
// Completely reliable login handler using native form submission with AJAX enhancement
document.addEventListener('DOMContentLoaded', function() {
    console.log('Setting up login form...');
    var loginForm = document.getElementById('loginForm');
    if (!loginForm) {
        console.error('Login form not found');
        return;
    }
    
    // Make sure we have essential elements
    var tokenInput = loginForm.querySelector('input[name="_token"]');
    if (!tokenInput) {
        console.error('CSRF token input not found - form will use native submission');
        return; // Let form submit normally
    }
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted, processing login...');
        
        var submitBtn = this.querySelector('button[type="submit"]');
        var spinner = submitBtn.querySelector('.spinner-border');
        var btnText = submitBtn.querySelector('.hide-2');
        var alertContainer = document.querySelector('.custom-alerts');
        
        // Show loading state
        if (spinner) spinner.classList.remove('d-none');
        if (btnText) btnText.textContent = 'Please wait...';
        submitBtn.disabled = true;
        
        // Clear previous alerts
        if (alertContainer) alertContainer.innerHTML = '';
        
        var email = this.querySelector('input[name="email"]').value.trim();
        var password = this.querySelector('input[name="password"]').value;
        var token = tokenInput.value; // Use the hidden input token directly
        
        // Debug information
        console.log('Form action:', this.action);
        console.log('CSRF Token length:', token.length);
        console.log('Email:', email);
        
        // Basic validation
        if (!email || !password) {
            if (alertContainer) alertContainer.innerHTML = '<div class="alert alert-danger">Please enter both email and password.</div>';
            if (spinner) spinner.classList.add('d-none');
            if (btnText) btnText.textContent = @json(__('frontend.login'));
            submitBtn.disabled = false;
            return;
        }
        
        // Create FormData object for proper form submission
        var formData = new FormData();
        formData.append('email', email);
        formData.append('password', password);
        formData.append('_token', token);
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', this.action, true);
        xhr.timeout = 15000; // 15 second timeout
        xhr.setRequestHeader('X-CSRF-TOKEN', token);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
        
        xhr.onload = function() {
            console.log('Response received. Status:', xhr.status);
            console.log('Response headers:', xhr.getAllResponseHeaders());
            console.log('Response text:', xhr.responseText);
            
            // Reset loading state
            if (spinner) spinner.classList.add('d-none');
            if (btnText) btnText.textContent = @json(__('frontend.login'));
            submitBtn.disabled = false;
            
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    console.log('Parsed response:', response);
                    
                    if (response.status == 100) {
                        // Successful login
                        if (alertContainer) alertContainer.innerHTML = '<div class="alert alert-success">Login successful! Redirecting...</div>';
                        setTimeout(function() {
                            window.location.href = "{{ url('/dashboard') }}";
                        }, 1000);
                    } else if (response.status == 200) {
                        // Wrong user type - admin trying to use customer login
                        if (alertContainer) alertContainer.innerHTML = '<div class="alert alert-danger">This login is for customers only. Please use the admin login.</div>';
                    } else if (response.status == 300) {
                        // Invalid credentials
                        if (alertContainer) alertContainer.innerHTML = '<div class="alert alert-danger">Invalid email or password. Please try again.</div>';
                    } else {
                        if (alertContainer) alertContainer.innerHTML = '<div class="alert alert-danger">Login failed. Status: ' + response.status + '</div>';
                    }
                } catch (e) {
                    console.error('Failed to parse response:', e);
                    if (alertContainer) alertContainer.innerHTML = '<div class="alert alert-danger">Invalid response from server.</div>';
                }
            } else if (xhr.status === 419) {
                if (alertContainer) alertContainer.innerHTML = '<div class="alert alert-danger">Session expired. Please refresh the page and try again.</div>';
            } else if (xhr.status === 422) {
                // Validation errors
                try {
                    var response = JSON.parse(xhr.responseText);
                    var errorHtml = '<div class="alert alert-danger"><ul class="mb-0">';
                    if (response && response.errors) {
                        Object.keys(response.errors).forEach(function(field) {
                            response.errors[field].forEach(function(message) {
                                errorHtml += '<li style="color:#fff">' + message + '</li>';
                            });
                        });
                    } else {
                        errorHtml += '<li style="color:#fff">Validation error occurred.</li>';
                    }
                    errorHtml += '</ul></div>';
                    if (alertContainer) alertContainer.innerHTML = errorHtml;
                } catch (e) {
                    if (alertContainer) alertContainer.innerHTML = '<div class="alert alert-danger">Validation error occurred.</div>';
                }
            } else {
                if (alertContainer) alertContainer.innerHTML = '<div class="alert alert-danger">Network error. Status: ' + xhr.status + '</div>';
            }
        };
        
        xhr.onerror = function() {
            console.error('Network error occurred');
            if (spinner) spinner.classList.add('d-none');
            if (btnText) btnText.textContent = @json(__('frontend.login'));
            submitBtn.disabled = false;
            if (alertContainer) alertContainer.innerHTML = '<div class="alert alert-danger">Network error. Please check your connection and try again.</div>';
        };
        
        xhr.ontimeout = function() {
            console.error('Request timeout occurred');
            if (spinner) spinner.classList.add('d-none');
            if (btnText) btnText.textContent = @json(__('frontend.login'));
            submitBtn.disabled = false;
            if (alertContainer) alertContainer.innerHTML = '<div class="alert alert-danger">Request timeout. Please try again.</div>';
        };
        
        console.log('Sending request with FormData...');
        xhr.send(formData);
    });
});
</script>
@endsection