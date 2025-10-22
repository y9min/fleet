@extends('layouts.app')
@section('extra_css')
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datepicker.min.css')}}">
<style type="text/css">
  .select2-selection:not(.select2-selection--multiple) {
    height: 38px !important;
  }

  .input-group-append,
  .input-group-prepend {
    display: flex;
    /* width: calc(100% / 2); */
  }
  
  /* Custom cyan color override */
  .card-custom {
    border-top: 3px solid #7fd7e1 !important;
  }
  
  .btn-custom {
    background-color: #7fd7e1 !important;
    border-color: #7fd7e1 !important;
    color: #fff !important;
  }
  
  .btn-custom:hover {
    background-color: #6bc5d1 !important;
    border-color: #6bc5d1 !important;
  }
</style>
@endsection
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('drivers.index')}}">@lang('fleet.drivers')</a></li>
<li class="breadcrumb-item active">@lang('fleet.addDriver')</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-custom">
      <div class="card-header with-border">
        <h3 class="card-title">@lang('fleet.addDriver')</h3>
      </div>

      <div class="card-body">
        @if (count($errors) > 0)
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        {!! Form::open(['route' => 'drivers.store','files'=>true,'method'=>'post','id'=>'driver-create-form']) !!}
        {!! Form::hidden('is_active',1) !!}
        {!! Form::hidden('is_available',0) !!}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('first_name', __('fleet.firstname'), ['class' => 'form-label required','autofocus']) !!}
              {!! Form::text('first_name', null,['class' => 'form-control','required','autofocus']) !!}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('last_name', __('fleet.lastname'), ['class' => 'form-label required']) !!}
              {!! Form::text('last_name', null,['class' => 'form-control','required']) !!}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('email', __('fleet.email'), ['class' => 'form-label required']) !!}
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                </div>
                {!! Form::email('email', null,['class' => 'form-control','required','id' => 'email']) !!}
                <div class="input-group-append">
                  <span class="input-group-text" id="emailStatus">
                    <i class="fas fa-spinner fa-spin" id="emailSpinner" style="display: none;"></i>
                    <i class="fas fa-check text-success" id="emailValid" style="display: none;"></i>
                    <i class="fas fa-times text-danger" id="emailInvalid" style="display: none;"></i>
                  </span>
                </div>
              </div>
              <div class="invalid-feedback" id="emailError"></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('phone', __('fleet.phone'), ['class' => 'form-label required']) !!}
              <div class="input-group">
                <div class="input-group-prepend">
                  {!! Form::select('phone_code',$phone_code,'+44',['class' => 'form-control
                  code','required','style'=>'width:80px']) !!}
                </div>
                {!! Form::number('phone', null,['class' => 'form-control','required']) !!}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('license_number', __('fleet.licenseNumber'), ['class' => 'form-label required']) !!}
              {!! Form::text('license_number', null,['class' => 'form-control','required']) !!}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('exp_date', __('fleet.expirationDate'), ['class' => 'form-label required']) !!}
              <div class="input-group date">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span>
                </div>
                {!! Form::text('exp_date', null,['class' => 'form-control','required']) !!}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('password', __('fleet.password'), ['class' => 'form-label']) !!}
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-lock"></i></span>
                </div>
                {!! Form::password('password', ['class' => 'form-control','required','id' => 'password']) !!}
                <div class="input-group-append">
                  <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                    <i class="fas fa-eye" id="toggleIcon"></i>
                  </button>
                </div>
              </div>
              <div class="password-strength mt-2">
                <div class="progress" style="height: 5px;">
                  <div class="progress-bar" id="passwordStrengthBar" role="progressbar" style="width: 0%"></div>
                </div>
                <small class="text-muted" id="passwordStrengthText">Enter a password</small>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('gender', __('fleet.gender') , ['class' => 'form-label']) !!}<br>
              <input type="radio" name="gender" class="flat-red gender" value="1" checked> @lang('fleet.male')<br>

              <input type="radio" name="gender" class="flat-red gender" value="0"> @lang('fleet.female')
            </div>

            <div class="form-group">
              {!! Form::label('driver_image', __('fleet.driverImage'), ['class' => 'form-label']) !!}

              {!! Form::file('driver_image',null,['class' => 'form-control','required']) !!}
            </div>
            <div class="form-group">
              {!! Form::label('documents', __('fleet.documents'), ['class' => 'form-label']) !!}
              {!! Form::file('documents',null,['class' => 'form-control','required']) !!}
            </div>


            <div class="form-group">
              {!! Form::label('license_image', __('fleet.licenseImage'), ['class' => 'form-label']) !!}
              {!! Form::file('license_image',null,['class' => 'form-control','required']) !!}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('econtact', __('fleet.emergency_details'), ['class' => 'form-label']) !!}
              {!! Form::textarea('econtact',null,['class' => 'form-control']) !!}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('documents', __('fleet.documents'), ['class' => 'form-label']) !!}
              {!! Form::file('documents',null,['class' => 'form-control','required']) !!}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('license_image', __('fleet.licenseImage'), ['class' => 'form-label']) !!}
              {!! Form::file('license_image',null,['class' => 'form-control','required']) !!}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('econtact', __('fleet.emergency_details'), ['class' => 'form-label']) !!}
              {!! Form::textarea('econtact',null,['class' => 'form-control']) !!}
            </div>
          </div>
        </div>
        <div class="col-md-12">
          {!! Form::submit(__('fleet.saveDriver'), ['class' => 'btn btn-custom']) !!}
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>

@endsection

@section("script")
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('.code').select2();
    
    $("#first_name").focus();
    
    $('#exp_date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true,
      startView: 2,
      minViewMode: 0
    });

    $("#driver-create-form").validate({
      // in 'rules' user have to specify all the constraints for respective fields
      rules: {        
        password: {
          required:true,
          minlength: 6
        }
      },
      // in 'messages' user have to specify message as per rules
      messages: {
        vehicle_id: "Assign Vehicle field is required.",           
      },
      errorPlacement: function (error, element) {
        if(element.hasClass('select2-hidden-accessible') && element.next('.select2-container').length) {
            error.insertAfter(element.next('.select2-container'));
        }else if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        }
        else if (element.prop('type') === 'radio' && element.parent('.radio-inline').length) {
            error.insertAfter(element.parent().parent());
        }
        else if (element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
            error.appendTo(element.parent().parent());
        }
        else {
            error.insertAfter(element);
        }
      },
      highlight: function (element, errorClass, validClass) {
        if($(element).hasClass('select2-hidden-accessible') && $(element).next('.select2-container').length) {
          
          $(element).next('.select2-container').find('.select2-selection').addClass('border-danger');
        }else{

        $(element).addClass('is-invalid');     
        }
        // return false;
      },
      unhighlight: function (element, errorClass, validClass) {
        if($(element).hasClass('select2-hidden-accessible') && $(element).next('.select2-container').length) {
        console.log(element, errorClass, validClass)

          $(element).next('.select2-container').find('.select2-selection').removeClass('border-danger');
        }else{
          $(element).removeClass('is-invalid');
        }
      }
    });
    
    //Flat red color scheme for iCheck
    // $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
    //   checkboxClass: 'icheckbox_flat-green',
    //   radioClass   : 'iradio_flat-green'
    // })

    // Enhanced form functionality
    let emailCheckTimeout;
    
    // Email validation with AJAX
    $('#email').on('input', function() {
        clearTimeout(emailCheckTimeout);
        const email = $(this).val();
        
        if (email.length > 5 && email.includes('@')) {
            emailCheckTimeout = setTimeout(function() {
                checkEmailAvailability(email);
            }, 500);
        } else {
            resetEmailStatus();
        }
    });
    
    function checkEmailAvailability(email) {
        $('#emailSpinner').show();
        $('#emailValid, #emailInvalid').hide();
        
        $.ajax({
            url: '{{ url("admin/check-email") }}',
            method: 'POST',
            data: {
                email: email,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#emailSpinner').hide();
                if (response.available) {
                    $('#emailValid').show();
                    $('#email').removeClass('is-invalid').addClass('is-valid');
                } else {
                    $('#emailInvalid').show();
                    $('#email').removeClass('is-valid').addClass('is-invalid');
                    $('#emailError').text('This email is already registered.');
                }
            },
            error: function() {
                $('#emailSpinner').hide();
                resetEmailStatus();
            }
        });
    }
    
    function resetEmailStatus() {
        $('#emailSpinner, #emailValid, #emailInvalid').hide();
        $('#email').removeClass('is-valid is-invalid');
        $('#emailError').text('');
    }
    
    // Password strength indicator
    $('#password').on('input', function() {
        const password = $(this).val();
        const strength = calculatePasswordStrength(password);
        updatePasswordStrengthUI(strength);
    });
    
    function calculatePasswordStrength(password) {
        let score = 0;
        let feedback = [];
        
        if (password.length >= 8) score += 1;
        else feedback.push('at least 8 characters');
        
        if (/[a-z]/.test(password)) score += 1;
        else feedback.push('lowercase letters');
        
        if (/[A-Z]/.test(password)) score += 1;
        else feedback.push('uppercase letters');
        
        if (/[0-9]/.test(password)) score += 1;
        else feedback.push('numbers');
        
        if (/[^A-Za-z0-9]/.test(password)) score += 1;
        else feedback.push('special characters');
        
        return { score: score, feedback: feedback };
    }
    
    function updatePasswordStrengthUI(strength) {
        const bar = $('#passwordStrengthBar');
        const text = $('#passwordStrengthText');
        
        const percentage = (strength.score / 5) * 100;
        bar.css('width', percentage + '%');
        
        if (strength.score <= 1) {
            bar.removeClass('bg-success bg-warning').addClass('bg-danger');
            text.text('Weak password - add: ' + strength.feedback.join(', '));
        } else if (strength.score <= 3) {
            bar.removeClass('bg-danger bg-success').addClass('bg-warning');
            text.text('Medium strength - add: ' + strength.feedback.join(', '));
        } else {
            bar.removeClass('bg-danger bg-warning').addClass('bg-success');
            text.text('Strong password!');
        }
    }
    
    // Password visibility toggle
    $('#togglePassword').on('click', function() {
        const passwordField = $('#password');
        const toggleIcon = $('#toggleIcon');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            toggleIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            toggleIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Enhanced form submission with success notification
    $("#driver-create-form").on("submit", function(e) {
        // Check if email is valid before submission
        if ($('#emailInvalid').is(':visible')) {
            e.preventDefault();
            new PNotify({
                title: 'Invalid Email',
                text: 'Please enter a valid and available email address.',
                type: 'error'
            });
            return false;
        }
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating Driver...');
        
        // Let the form submit normally, but add success handling via page load
        setTimeout(function() {
            submitBtn.prop('disabled', false).html(originalText);
        }, 3000);
    });
    
    // Success notification on page load (if redirected from successful creation)
    @if(session('success'))
        new PNotify({
            title: 'Success!',
            text: '{{ session("success") }}',
            type: 'success',
            delay: 5000
        });
    @endif
  });
</script>




@endsection


