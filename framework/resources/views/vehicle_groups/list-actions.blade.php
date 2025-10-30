<div class="d-flex justify-content-center" style="gap: 0;">
  <button type="button" class="btn btn-sm" title="Add Vehicle to Group" data-toggle="modal" data-target="#manageGroupVehiclesModal" data-group-id="{{$row->id}}" data-group-name="{{$row->name}}" style="width:35px; height:35px; padding:0; display:flex; align-items:center; justify-content:center; background-color:#28a745; border:none; color:#fff;">
    <i class="fas fa-plus"></i>
  </button>

  <a href="{{ url('admin/vehicle_group/'.$row->id.'/edit') }}" class="btn btn-sm btn-primary" title="@lang('fleet.edit')" style="width:35px; height:35px; padding:0; display:flex; align-items:center; justify-content:center;">
    <i class="fas fa-edit"></i>
  </a>

  <button type="button" class="btn btn-sm btn-danger" data-id="{{$row->id}}" data-toggle="modal" data-target="#myModal" title="@lang('fleet.delete')" style="width:35px; height:35px; padding:0; display:flex; align-items:center; justify-content:center;">
    <i class="fas fa-trash"></i>
  </button>
</div>
{!! Form::open(['url' => 'admin/vehicle_group/'.$row->id,'method'=>'DELETE','class'=>'form-horizontal','id'=>'form_'.$row->id]) !!}
{!! Form::hidden('id', $row->id) !!}
{!! Form::close() !!}