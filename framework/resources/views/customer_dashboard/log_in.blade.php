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
// Wait for jQuery to be available with proper checking
function initializeLogin() {
    if (typeof window.jQuery === 'undefined') {
        console.log('Waiting for jQuery to load...');
        setTimeout(initializeLogin, 200);
        return;
    }
    
    jQuery(document).ready(function($) {
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var spinner = submitBtn.find('.spinner-border');
            var btnText = submitBtn.find('.hide-2');
            
            // Show loading state
            spinner.removeClass('d-none');
            btnText.text('Please wait...');
            submitBtn.prop('disabled', true);
            
            // Clear previous errors
            $('.custom-alerts').html('');
            $('.focus-bg').html('');
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.status == 100) {
                        // Successful login
                        $('.custom-alerts').html('<div class="alert alert-success">Login successful! Redirecting...</div>');
                        setTimeout(function() {
                            window.location.href = response.redirect_url || "{{ url('/dashboard') }}";
                        }, 1000);
                    } else if (response.status == 200) {
                        // Not a customer user
                        $('.custom-alerts').html('<div class="alert alert-danger">Access denied. Customer account required.</div>');
                    } else if (response.status == 300) {
                        // Invalid credentials
                        $('.custom-alerts').html('<div class="alert alert-danger">Invalid email or password. Please try again.</div>');
                    } else {
                        $('.custom-alerts').html('<div class="alert alert-danger">Login failed. Please try again.</div>');
                    }
                },
                error: function(xhr) {
                    var errorHtml = '<div class="alert alert-danger">';
                    if (xhr.responseJSON && (xhr.responseJSON.error || xhr.responseJSON.errors)) {
                        var errors = xhr.responseJSON.error || xhr.responseJSON.errors;
                        errorHtml += '<ul class="mb-0">';
                        if (typeof errors === 'object') {
                            $.each(errors, function(field, messages) {
                                if (Array.isArray(messages)) {
                                    $.each(messages, function(index, message) {
                                        errorHtml += '<li style="color:#fff">' + message + '</li>';
                                    });
                                } else {
                                    errorHtml += '<li style="color:#fff">' + messages + '</li>';
                                }
                            });
                        } else {
                            errorHtml += '<li style="color:#fff">' + errors + '</li>';
                        }
                        errorHtml += '</ul>';
                    } else {
                        errorHtml += 'An error occurred. Please try again.';
                    }
                    errorHtml += '</div>';
                    $('.custom-alerts').html(errorHtml);
                },
                complete: function() {
                    // Reset loading state
                    spinner.addClass('d-none');
                    btnText.text(@json(__('frontend.login')));
                    submitBtn.prop('disabled', false);
                }
            });
        });
        
        console.log('Login form initialized successfully');
    });
}

// Start initialization
initializeLogin();
</script>
@endsection