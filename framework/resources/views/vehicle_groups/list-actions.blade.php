<div class="d-flex justify-content-center" style="gap: 8px;">
  <a href="{{ route('vehicles.create', ['group_id' => $row->id]) }}" class="btn btn-sm btn-info" title="Add Vehicle to Group" style="padding: 6px 8px;">
    <i class="fas fa-plus"></i>
  </a>

  <a href="{{ url('admin/vehicle_group/'.$row->id.'/edit') }}" class="btn btn-sm btn-primary" title="@lang('fleet.edit')" style="padding: 6px 8px;">
    <i class="fas fa-edit"></i>
  </a>

  <button type="button" class="btn btn-sm btn-danger" data-id="{{$row->id}}" data-toggle="modal" data-target="#myModal" title="@lang('fleet.delete')" style="padding: 6px 8px;">
    <i class="fas fa-trash"></i>
  </button>
</div>
{!! Form::open(['url' => 'admin/vehicle_group/'.$row->id,'method'=>'DELETE','class'=>'form-horizontal','id'=>'form_'.$row->id]) !!}
{!! Form::hidden('id', $row->id) !!}
{!! Form::close() !!}