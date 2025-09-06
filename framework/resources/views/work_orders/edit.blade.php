@extends('layouts.app')
@section('extra_css')
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-datepicker.min.css')}}">
@endsection
@section("breadcrumb")
<li class="breadcrumb-item "><a href="{{ route('work_order.index')}}"> @lang('fleet.work_orders') </a></li>
<li class="breadcrumb-item active">@lang('fleet.edit_workorder')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-warning">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.edit_workorder')</h3>
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
        {!! Form::open(['route' => ['work_order.update',$data->id],'method'=>'PATCH']) !!}
        {!! Form::hidden('user_id',Auth::user()->id)!!}
        {!! Form::hidden('id',$data->id)!!}
        {!! Form::hidden('type','Updated')!!}
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('vehicle_id',__('fleet.vehicle'), ['class' => 'form-label']) !!}
              <select id="vehicle_id" name="vehicle_id" class="form-control" required>
                <option value="">-</option>
                @foreach($vehicles as $vehicle)
                <option value="{{$vehicle->id}}" @if($vehicle->id == $data->vehicle_id) selected @endif>
                  {{$vehicle->make_name}} - {{$vehicle->model_name}} - {{$vehicle->license_plate}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              {!! Form::label('required_by', __('fleet.required_by'), ['class' => 'form-label']) !!}
              <div class="input-group date">
                <div class="input-group-prepend"><span class="input-group-text"><span class="fa fa-calendar"></span>
                </div>
                {!! Form::text('required_by',$data->required_by,['class'=>'form-control','required']) !!}
              </div>
            </div>
            <div class="form-group">
              {!! Form::label('status',__('fleet.status'), ['class' => 'form-label']) !!}
              {!! Form::select('status',["Pending"=>__('fleet.pending'), "Processing"=>__('fleet.Processing'),
              "Completed"=>__('fleet.Completed'),"Hold"=>__('fleet.Hold')],$data->status,['class' => 'form-control','required']) !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('vendor_id',__('fleet.vendor'), ['class' => 'form-label']) !!}
              <select id="vendor_id" name="vendor_id" class="form-control" required>
                <option value="">-</option>
                @foreach($vendors as $vendor)
                <option value="{{$vendor->id}}" @if($vendor->id == $data->vendor_id) selected @endif>{{$vendor->name}}
                </option>
                @endforeach
              </select>
            </div>
            
            <div class="form-group">
              {!! Form::label('price',__('fleet.work_order_price'), ['class' => 'form-label']) !!}
              <div class="input-group mb-3">
                <div class="input-group-prepend date">
                  <span class="input-group-text">{{Hyvikk::get('currency')}}</span>
                </div>
                {!! Form::text('price',$data->price,['class'=>'form-control','id'=>'work_order_price','required']) !!}
              </div>
            </div>
            <div class="form-group">
              {!! Form::label('note',__('fleet.note'), ['class' => 'form-label']) !!}
              {!! Form::textarea('note',$data->note,['class'=>'form-control','size'=>'30x1']) !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('mechanic_id',__('fleet.mechanics'), ['class' => 'form-label']) !!}
              <select id="mechanic_id" name="mechanic_id" class="form-control" required>
                <option value="">-</option>
                @foreach($mechanic as $m)
                <option value="{{$m->id}}" @if($m->id == $data->mechanic_id) selected @endif> {{$m->name}} </option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              {!! Form::label('meter',Hyvikk::get('dis_format')." ".__('fleet.reading'), ['class' => 'form-label']) !!}
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">{{Hyvikk::get('dis_format')}}</span>
                </div>
                {!! Form::number('meter',$data->meter,['class'=>'form-control']) !!}
              </div>
            </div>
            <div class="form-group">
              {!! Form::label('description',__('fleet.description'), ['class' => 'form-label']) !!}
              {!! Form::textarea('description',$data->description,['class'=>'form-control','size'=>'30x1']) !!}
            </div>
          </div>
        </div>
        <hr>
        {{-- <div class="row" style="margin-bottom: 25px;">
          <div class="col-md-6">
            <div class="form-group"> <label class="form-label">@lang('fleet.selectPart')</label> <select
                id="select_part" class="form-control" name="part_list">
                <option></option>@foreach($parts as $part) <option value="{{ $part->id }}" title="{{ $part->title }}"
                  qty="{{ $part->stock }}" price="{{ $part->unit_cost }}">{{ $part->title }}</option> @endforeach
              </select> </div>
          </div>
          <div class="col-md-6" style="margin-top: 30px">
            <button type="button" class="btn btn-warning attach">@lang('fleet.attachPart')</button>
          </div>
        </div> --}}

        <div class="row orignalElement">
          <div class="col-md-4">
            <div class="form-group"> <label class="form-label">@lang('fleet.attachPart')</label>
              <select id="select_part_0" class="form-control" name="part_list" data-index="0">
                <option value="">@lang('fleet.selectPart')</option>
                @foreach($parts as $part) 
                  <option value="{{ $part->id }}" title="{{ $part->title }}" qty="{{ $part->stock }}" price="{{ $part->unit_cost }}">{{ $part->title }}</option> 
                @endforeach
              </select> 
            </div>
          </div>
          <div class="row col-md-8">
            <div class="col-md-3">
              <div class="form-group">
                <label class="form-label">@lang('fleet.qty')</label>
                <input type="number" name="parts[0]" min="1" class="form-control" id="qty_0" max='1' data-index="0" required disabled>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="form-label">@lang('fleet.unit_cost')</label>
                <input type="number" class="form-control price_0" disabled>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="form-label">@lang('fleet.total_cost')</label>
                <input type="number" class="form-control total_cost_0" disabled>
              </div>
            </div>
            <div class="row col-md-3">
              <div class="form-group" style="margin-top: 30px;">
                <button class="btn btn-danger ml-2 removePart" type="button">Remove</button>
              </div>
              <div class="form-group" style="margin-top: 30px;">
                <button class="btn btn-warning ml-2 addPart" type="button">New Part</button>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          @foreach($data->parts as $row)
          <div class="row col-md-12">
            <div class="col-md-4">
              <div class="form-group"> <label class="form-label">@lang('fleet.selectPart')</label> <select
                  class="form-control" disabled>
                  <option value="{{ $row->part_id }}" selected>{{ $row->part->title }}</option>
                </select> </div>
            </div>
            <div class="col-md-2">
              <div class="form-group"> <label class="form-label">@lang('fleet.qty')</label> <input type="number"
                  name="parts[{{ $row->part_id }}]" min="1" value="{{ $row->qty }}" class="form-control" required
                  disabled> </div>
            </div>
            <div class="col-md-2">
              <div class="form-group"> <label class="form-label">@lang('fleet.unit_cost')</label> <input type="number"
                  value="{{ $row->price }}" class="form-control" disabled> </div>
            </div>
            <div class="col-md-2">
              <div class="form-group"> <label class="form-label">@lang('fleet.total_cost')</label> <input type="number"
                  value="{{ $row->price * $row->qty }}" class="form-control" disabled> </div>
            </div>
            <div class="col-md-2">
              <div class="form-group" style="margin-top: 30px"><a class="btn btn-danger"
                  href="{{ url('admin/remove-part/'.$row->id) }}">@lang('fleet.Remove')</a> </div>
            </div>
          </div>
          @endforeach
          <div class="parts col-md-12"></div>
        </div>
        <div class="row">
          <div class="col-md-12">
            {!! Form::submit(__('fleet.update'), ['class' => 'btn btn-warning']) !!}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section("script")
<script type="text/javascript">
  $(document).ready(function() {
  $('#work_order_price').on('input', function() {
    var inputValue = $(this).val();
    // Remove any non-digit characters and leading zeros
    inputValue = inputValue.replace(/[^0-9.]/g, '');
    // Split the value into integer and decimal parts
    var parts = inputValue.split('.');
    // Ensure there are at most 10 digits in total
    if (parts[0].length > 8) {
        parts[0] = parts[0].substring(0, 8);
    }
    // Ensure there are at most 2 decimal places
    if (parts.length > 1) {
      parts[1] = (parts[1] || "").substring(0, 2);
    }
    // Combine the parts back into a valid number
    inputValue = parts.join('.');
    // Update the input field value
    $(this).val(inputValue);
  });
  $('#vehicle_id').select2({placeholder: "@lang('fleet.selectVehicle')"});
  $('#vendor_id').select2({placeholder: "@lang('fleet.select_vendor')"});
  $('#mechanic_id').select2({placeholder: "@lang('fleet.select_mechanic')"});
  $('#select_part').select2({placeholder: "@lang('fleet.selectPart')"});
  $('#required_by').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    });
  $('#created_on').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    });
  //Flat green color scheme for iCheck
  // $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
  //   checkboxClass: 'icheckbox_flat-green',
  //   radioClass   : 'iradio_flat-green'
  // });
  // $('.attach').on('click',function(){
  //   var selectPartMessage = @json(__('fleet.select_part'));
  //   var field = $('#select_part').val();
  //   if(field == "" || field == null){
  //     alert(selectPartMessage);
  //   }
  //   else{
  //     var qty=$('#select_part option:selected').attr('qty');
  //     var title=$('#select_part option:selected').attr('title');
  //     var price=$('#select_part option:selected').attr('price');
  //     // alert($('#select_part option:selected').attr('title'));
  //     // alert($('#select_part option:selected').attr('qty'));
  //     $(".parts").append('<div class="row col-md-12"><div class="col-md-4">  <div class="form-group"> <label class="form-label">@lang('fleet.selectPart')</label> <select  class="form-control" disabled>  <option value="'+field+'" selected >'+title+'</option> </select> </div></div> <div class="col-md-2">  <div class="form-group"> <label class="form-label">@lang('fleet.qty')</label> <input type="number" name="parts['+field+']" min="1" value="1" class="form-control calc" max='+qty+' required> </div></div><div class="col-md-2">  <div class="form-group"> <label class="form-label">@lang('fleet.unit_cost')</label> <input type="number" value="'+price+'" class="form-control" disabled> </div></div><div class="col-md-2">  <div class="form-group"> <label class="form-label">@lang('fleet.total_cost')</label> <input type="number" value="'+price+'" class="form-control total_cost" disabled id="'+field+'"> </div></div> <div class="col-md-2"> <div class="form-group" style="margin-top: 30px"><button class="btn btn-danger" type="button" onclick="this.parentElement.parentElement.parentElement.remove();">Remove</button> </div></div></div>');
  //     $('#select_part').val('').change();
  //     $('.calc').on('change',function(){
  //       // alert($(this).val()*price);
  //       $('#'+field).val($(this).val()*price);
  //     });
  //   }
  // });


  var selectedValues = [];
  var maxParts = $('#select_part_0').find('option').length - 1;

  $(document).on('change', '[id^="select_part"]', function () {
    var field = $(this).val();
    var index = $(this).data('index');
    if (field == "" || field == null) {
      alert('Please select part');
      onNullSelect(index);
    } else {
      var qty = $('#select_part_' + index + ' option:selected').attr('qty');
      var price = $('#select_part_' + index + ' option:selected').attr('price');
      $('#qty_' + index).attr('name', 'parts[' + field + ']');
      $('#qty_' + index).attr('max', qty);
      $('#qty_' + index).removeAttr('disabled', true);
      $('#qty_' + index).val(1);
      $('.price_' + index).val(price);
      $('.total_cost_' + index).val(price);
      // selectedValues.push(field);
    }
  });

  $(document).on('click', '.addPart', function () {
    // var field = $('#select_part_0').val();
    selectedValues = [];
    var allSelects = $('[id^="select_part"]');
    var hasNullValue = false;

    allSelects.each(function() {
      if ($(this).val() === '') {
        hasNullValue = true;
        return false; // Exit the loop early if a null value is found
      }
      else{
        selectedValues.push($(this).val());
      }
    });
    
    if (selectedValues.length >= maxParts) {
      alert('All part types have been added. No more can be added.');
      return;
    }
  
    if (hasNullValue) {
      alert('You cannot add an extra empty part');
    } else {
      var index = getLastElementIndex();
      var originalElement = $('#select_part_0');
      var clonedElement = originalElement.clone();
      clonedElement.attr('id', 'select_part_' + (index + 1));
      clonedElement.attr('data-index', index + 1);

      // Filter out selected values from the options
      var options = clonedElement.find('option').filter(function () {
          return selectedValues.indexOf($(this).val()) === -1;
      });
      clonedElement.find('option').remove();
      clonedElement.append(options);

      clonedElement.find('option[value=""]').prop('selected', true);

      $(".parts").append('<div class="row col-md-12"><div class="col-md-4">  <div class="form-group newPart_'+(index+1)+'"> <label class="form-label">@lang('fleet.selectPart')</label></div></div> <div class="col-md-2">  <div class="form-group"> <label class="form-label">@lang('fleet.qty')</label> <input type="number" name="parts[1]" min="1" class="form-control" id="qty_' + (index + 1) + '" max="1" required data-index="' + (index + 1) + '" disabled> </div></div><div class="col-md-2">  <div class="form-group"> <label class="form-label">@lang('fleet.unit_cost')</label> <input type="number" value="" class="form-control price_' + (index + 1) + '" disabled> </div></div><div class="col-md-2">  <div class="form-group"> <label class="form-label">@lang('fleet.total_cost')</label> <input type="number" value="" class="form-control total_cost_' + (index + 1) + '" disabled> </div></div> <div class="col-md-2"> <div class="form-group" style="margin-top: 30px"><button class="btn btn-danger remove" type="button" data-index="'+(index+1)+'">Remove</button> </div></div></div>')
      $(".parts .newPart_"+(index+1)).append(clonedElement);
    }
  });

  $(document).on('change','[id^="qty_"]',function(){
    var qty = $(this).val();
    var index = $(this).data('index');
    var price=$('#select_part_'+index+' option:selected').attr('price');
    $('.total_cost_'+index).val(qty*price);
  });

  $('.removePart').on('click',function(){
    $('#qty_0').attr('disabled',true); 
    $('.price_0').attr('disabled',true); 
    $('.total_cost_0').attr('disabled',true); 
    $('.price_0').val('');
    $('.total_cost_0').val('');
    $('#qty_0').val(''); 
    $('#select_part_0').val('');
  });

  $(document).on('click','.remove',function(){
    var index = $(this).data('index');
    var selectedValueToRemove = $('#select_part_' + index).val();

    // Remove the value from selectedValues array
    var valueIndex = selectedValues.indexOf(selectedValueToRemove);
    if (valueIndex !== -1) {
      selectedValues.splice(valueIndex, 1);
    }

    $(this).closest('.row').remove();
  });

  function getLastElementIndex() {
    var elements = $('[id^="select_part_"]');
    var lastElement = elements.last();
    var lastIndex = lastElement.data('index');
    return lastIndex;
  }

  function onNullSelect(index){
    $('#qty_' + index).attr('disabled', true);
    $('#qty_' + index).val('');
    $('.price_' + index).val('');
    $('.total_cost_' + index).val('');
  }

});
</script>
@endsection