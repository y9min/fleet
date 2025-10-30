<div class="d-flex justify-content-center" style="gap: 0;">
  <button type="button" class="btn btn-sm" title="Add Vehicle to Group" data-toggle="modal" data-target="#manageGroupVehiclesModal" data-group-id="{{$row->id}}" data-group-name="{{$row->name}}" style="width:35px; height:35px; padding:0; display:flex; align-items:center; justify-content:center; background-color:#28a745; border:none; color:#fff;">
    <i class="fas fa-plus"></i>
  </button>

  <div class="dropdown" style="margin: 0 4px;">
    <button type="button" class="btn btn-sm btn-info dropdown-toggle view-group-vehicles-btn" title="View Vehicles in Group" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-group-id="{{$row->id}}" style="width:35px; height:35px; padding:0; display:flex; align-items:center; justify-content:center; background-color:#1da1b2; border:none; color:#fff;">
      <i class="fas fa-eye"></i>
    </button>
    <div class="dropdown-menu p-0" aria-labelledby="view_group_btn_{{$row->id}}" id="view_group_vehicles_menu_{{$row->id}}" style="max-height: 320px; overflow:auto; min-width: 320px;">
      <div class="px-3 py-2 text-muted" style="font-size:12px;">Loading...</div>
    </div>
  </div>

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