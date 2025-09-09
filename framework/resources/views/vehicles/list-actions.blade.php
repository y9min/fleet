<div class="btn-group">
    <button type="button" class="btn btn-sm dropdown-toggle" data-toggle="dropdown" style="background: #7FD7E1; border: none; color: white; border-radius: 4px;">
      <i class="fa fa-ellipsis-v"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-right" role="menu">
      @can('Vehicles edit')
        <a class="dropdown-item" href="{{ url("admin/vehicles/".$row->id."/edit") }}">
          <i class="fa fa-edit text-info"></i> Edit Vehicle
        </a>
        <div class="dropdown-divider"></div>
      @endcan
      @can('Vehicles delete')
        <a class="dropdown-item" data-id="{{$row->id}}" data-toggle="modal" data-target="#deleteModal" onclick="setDeleteId({{$row->id}})">
          <i class="fa fa-trash text-danger"></i> Delete Vehicle
        </a>
        <div class="dropdown-divider"></div>
      @endcan
      @if($row->in_service==1)
        <a class="dropdown-item" href="{{ url("admin/vehicles/disable/".$row->id)}}" onclick="return confirm('Are you sure you want to disable this vehicle?')">
          <i class="fa fa-ban text-warning"></i> Disable Vehicle
        </a>
      @else
        <a class="dropdown-item" href="{{ url("admin/vehicles/enable/".$row->id)}}" onclick="return confirm('Are you sure you want to enable this vehicle?')">
          <i class="fa fa-check-circle text-success"></i> Enable Vehicle
        </a>
      @endif
    </div>
</div>
  {!! Form::open(['url' => 'admin/vehicles/'.$row->id,'method'=>'DELETE','class'=>'form-horizontal','id'=>'form_'.$row->id]) !!}

  {!! Form::hidden("id",$row->id) !!}

  {!! Form::close() !!}