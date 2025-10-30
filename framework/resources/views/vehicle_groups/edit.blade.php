@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('vehicle_group.index')}}">@lang('fleet.vehicleGroup')</a></li>
<li class="breadcrumb-item active">@lang('fleet.editGroup')</li>
@endsection
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header" style="background-color:#7FD7E1; color:#fff;">
        <h3 class="card-title">@lang('fleet.editGroup')</h3>
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

        {!! Form::open(['route' => ['vehicle_group.update',$data->id],'method'=>'PATCH']) !!}
        @csrf
        {!! Form::hidden('user_id',Auth::user()->id)!!}
        {!! Form::hidden('id',$data->id)!!}

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('name',__('fleet.groupName'), ['class' => 'form-label']) !!}
              {!! Form::text('name',$data->name,['class'=>'form-control','required']) !!}
            </div>

            <div class="form-group">
              {!! Form::label('description',__('fleet.description'), ['class' => 'form-label']) !!}
              {!! Form::text('description',$data->description,['class'=>'form-control']) !!}
            </div>

            
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            {!! Form::submit(__('fleet.update'), ['class' => 'btn', 'style' => 'background-color:#7FD7E1; color:#fff; border:none;']) !!}
            <a href="{{ route('vehicle_group.index') }}" class="btn" style="background-color:#6b7280; color:#fff; border:none; margin-left:10px;">@lang('fleet.back')</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection