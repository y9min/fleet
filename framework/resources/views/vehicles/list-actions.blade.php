<div class="btn-group">
    <button type="button" class="btn btn-sm dropdown-toggle" data-toggle="dropdown" style="background: #7FD7E1; border: none; color: white;">
      <span class="fa fa-cog"></span>
      <span class="sr-only">Toggle Dropdown</span>
    </button>
    <div class="dropdown-menu custom" role="menu">
      @can('Vehicles edit')
        <a class="dropdown-item" href="{{ url("admin/vehicles/".$row->id."/edit") }}">
          <span aria-hidden="true" class="fa fa-edit" style="color: #17a2b8;"></span> @lang('fleet.edit')
        </a>
      @endcan
      {!! Form::hidden("id",$row->id) !!}
      @can('Vehicles delete')
        <a class="dropdown-item" data-id="{{$row->id}}" data-toggle="modal" data-target="#myModal">
          <span aria-hidden="true" class="fa fa-trash" style="color: #dc3545"></span> @lang('fleet.delete')
        </a>
      @endcan
      <a class="dropdown-item openBtn" data-id="{{$row->id}}" data-toggle="modal" data-target="#myModal2">
        <span class="fa fa-eye" aria-hidden="true" style="color: #28a745"></span> @lang('fleet.view_vehicle')
      </a>
      @if($row->in_service==1)
        <a class="dropdown-item" href="{{ url("admin/vehicles/disable/".$row->id)}}" data-toggle="tooltip" title="@lang('fleet.disable')@lang('fleet.assigned_vehicle')">
          <span class="fa fa-times" aria-hidden="true" style="color: #ffc107;"></span> @lang('fleet.disable')@lang('fleet.assigned_vehicle')
        </a>
      @else
        <a class="dropdown-item" href="{{ url("admin/vehicles/enable/".$row->id)}}" data-toggle="tooltip" title="@lang('fleet.enable')@lang('fleet.assigned_vehicle')">
          <span class="fa fa-check" aria-hidden="true" style="color: #28a745;"></span> @lang('fleet.enable')@lang('fleet.assigned_vehicle')
        </a>
      @endif
    </div>
  </div>
  {!! Form::open(['url' => 'admin/vehicles/'.$row->id,'method'=>'DELETE','class'=>'form-horizontal','id'=>'form_'.$row->id]) !!}

  {!! Form::hidden("id",$row->id) !!}

  {!! Form::close() !!}