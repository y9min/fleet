@extends('layouts.app')
@section('extra_css')
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datepicker.min.css')}}">

<style type="text/css">
  .select2-selection:not(.select2-selection--multiple) {
    height: 38px !important;
  }
</style>

@endsection

@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('drivers.index')}}">@lang('fleet.drivers')</a></li>
<li class="breadcrumb-item active">@lang('fleet.edit_driver')</li>

@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-warning">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.edit_driver')</h3>
      </div>

      <div class="card-body">
        <div class="alert alert-info">
          <i class="fas fa-info-circle"></i> Fields marked with <span style="color: red;">*</span> are required and must be filled before saving.
        </div>
        @if (count($errors) > 0)
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        {!! Form::open(['route' => ['drivers.update',$driver->id],'files'=>true,'method'=>'PATCH']) !!}
        {!! Form::hidden('id',$driver->id) !!}
        {!! Form::hidden('edit',"1") !!}
        {!! Form::hidden('detail_id',$driver->getMeta('id')) !!}
        {!! Form::hidden('user_id',Auth::user()->id) !!}
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('first_name', __('fleet.firstname') . ' <span style="color: red;">*</span>', ['class' => 'form-label required'], false) !!}
              {!! Form::text('first_name', $driver->getMeta('first_name'),['class' => 'form-control','required']) !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('middle_name', __('fleet.middlename'), ['class' => 'form-label']) !!}
              {!! Form::text('middle_name', $driver->getMeta('middle_name'),['class' => 'form-control']) !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('last_name', __('fleet.lastname') . ' <span style="color: red;">*</span>', ['class' => 'form-label required'], false) !!}
              {!! Form::text('last_name', $driver->getMeta('last_name'),['class' => 'form-control','required']) !!}
            </div>
          </div>
        </div>
        <div class="row">
          
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('address', __('fleet.address'), ['class' => 'form-label']) !!}
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-address-book"></i></span>
                </div>
                {!! Form::text('address', $driver->getMeta('address'),['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('email', __('fleet.email') . ' <span style="color: red;">*</span>', ['class' => 'form-label required'], false) !!}
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                </div>
                {!! Form::email('email', $driver->email,['class' => 'form-control','required']) !!}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('phone', __('fleet.phone') . ' <span style="color: red;">*</span>', ['class' => 'form-label required'], false) !!}
              <div class="input-group">
                <div class="input-group-prepend">
                  {!! Form::select('phone_code',$phone_code,'+44',['class' => 'form-control
                  code','required','style'=>'width:80px;']) !!}
                </div>
                {!! Form::number('phone', $driver->getMeta('phone'),['class' => 'form-control','required']) !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('emp_id', __('fleet.employee_id'), ['class' => 'form-label']) !!}
              {!! Form::text('emp_id', $driver->getMeta('emp_id'),['class' => 'form-control','required']) !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('contract_number', __('fleet.contract'), ['class' => 'form-label']) !!}
              {!! Form::text('contract_number', $driver->getMeta('contract_number'),['class' =>
              'form-control','required']) !!}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('license_number', __('fleet.licenseNumber'), ['class' => 'form-label required']) !!}
              {!! Form::text('license_number', $driver->getMeta('license_number'),['class' =>
              'form-control','required']) !!}
            </div>
          </div>


          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('exp_date', 'License Expiration Date', ['class' => 'form-label']) !!}
              <div class="input-group date">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span>
                </div>
                {!! Form::text('exp_date', $driver->getMeta('exp_date'),['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('start_date', __('fleet.join_date'), ['class' => 'form-label']) !!}
              <div class="input-group date">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span>
                </div>
                {!! Form::text('start_date', $driver->getMeta('start_date'),['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('end_date', __('fleet.leave_date'), ['class' => 'form-label']) !!}
              <div class="input-group date">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span>
                </div>
                {!! Form::text('end_date', $driver->getMeta('end_date'),['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('driver_image', __('fleet.driverImage'), ['class' => 'form-label']) !!}
              @php
                $driverImage = $driver->getMeta('driver_image');
              @endphp
              @if($driverImage != null)
              <a href="{{ asset('uploads/'.$driverImage) }}" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> View</a>
              @endif
              {!! Form::file('driver_image',null,['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
              {!! Form::label('license_image', __('fleet.licenseImage'), ['class' => 'form-label']) !!}
              @php
                $licenseImage = $driver->getMeta('license_upload_path') ?: $driver->getMeta('license_image');
              @endphp
              @if($licenseImage != null)
                <a href="{{ asset('uploads/' . $licenseImage) }}" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-id-card"></i> View License</a>
              @endif
              {!! Form::file('license_image',null,['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
              {!! Form::label('insurance_image', 'Insurance Document', ['class' => 'form-label']) !!}
              @php
                $insuranceImage = $driver->getMeta('insurance_upload_path') ?: $driver->getMeta('insurance_image') ?: $driver->getMeta('documents');
              @endphp
              @if($insuranceImage != null)
                <a href="{{ asset('uploads/' . $insuranceImage) }}" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-shield-alt"></i> View Insurance</a>
              @endif
              {!! Form::file('insurance_image',null,['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
              {!! Form::label('documents', __('fleet.documents'), ['class' => 'form-label']) !!}
              @php
                $documents = $driver->getMeta('documents');
              @endphp
              @if($documents != null && !$insuranceImage)
              <a href="{{ asset('uploads/'.$documents) }}" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> View</a>
              @endif
              {!! Form::file('documents',null,['class' => 'form-control']) !!}
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('econtact', __('fleet.emergency_details'), ['class' => 'form-label']) !!}
              {!! Form::textarea('econtact',$driver->getMeta('econtact'),['class' => 'form-control']) !!}
            </div>
          </div>
        </div>
        <div class="col-md-12">
          {!! Form::submit(__('fleet.update'), ['class' => 'btn btn-warning']) !!}
          <a href="{{route('drivers.index')}}" class="btn btn-danger">@lang('fleet.back')</a>
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>

@endsection

@section("script")
<script type="text/javascript">
  $(document).ready(function() {
    $('#driver_commision_type').on('change', function(){
      var val = $(this).val();
      if(val==''){
        $('#driver_commision_container').hide();
      }else{
        if(val =='amount'){
          $('#driver_commision').attr('placeholder',"@lang('fleet.enter_amount')");
        }else{
          $('#driver_commision').attr('placeholder',"@lang('fleet.enter_percent')")
        }
        $('#driver_commision_container').show();
      }
    });
    $('#driver_commision_type').trigger('change');
    $('.code').select2();
    $('#vehicle_id').select2({
      placeholder:"@lang('fleet.selectVehicle')"
    });
    $('#end_date').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
      }).on('show', function() {
    var pickupdate = $( "#start_date" ).datepicker('getDate');
    if (pickupdate) {
      // $("#end_date").datepicker('setStartDate', pickupdate);
    }
  
  });
  //   $('#exp_date').datepicker({
  //       autoclose: true,
  //       format: 'yyyy-mm-dd'
  //     }).on('show', function() {
  //   var pickupdate = $( "#issue_date" ).datepicker('getDate');
  //   if (pickupdate) {
  //     $("#exp_date").datepicker('setStartDate', pickupdate);
  //   }
  // });
  //   $('#issue_date').datepicker({
  //       autoclose: true,
  //       format: 'yyyy-mm-dd',
  //       endDate: new Date() 
  //     });


  $('#issue_date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                startView: 2,
                minViewMode: 0
            }).on('changeDate', function (e) {
                var startDate = e.date;
                $('#exp_date').datepicker('setStartDate', startDate);
                $('#exp_date').val(''); // Reset end_date if it's before new start_date
            });

            $('#exp_date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                startView: 2,
                minViewMode: 0
            });


    $('#start_date').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
      });

    //Flat green color scheme for iCheck
    // $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
    //   checkboxClass: 'icheckbox_flat-green',
    //   radioClass   : 'iradio_flat-green'
    // });

  });
</script>
@endsection