<div class="d-flex align-items-center" style="gap: 8px;">
  <a href="{{ route('vehicles.create', ['group_id' => $row->id]) }}" class="btn btn-sm btn-success" title="Add Vehicle to Group">
    <i class="fas fa-plus"></i> Add Vehicle
  </a>

  <a href="{{ url('admin/vehicle_group/'.$row->id.'/edit') }}" class="btn btn-sm btn-primary" title="@lang('fleet.edit')">
    <i class="fas fa-edit"></i> @lang('fleet.edit')
  </a>

  <button type="button" class="btn btn-sm btn-danger" data-id="{{$row->id}}" data-toggle="modal" data-target="#myModal" title="@lang('fleet.delete')">
    <i class="fas fa-trash-alt"></i> @lang('fleet.delete')
  </button>
</div>
{!! Form::open(['url' => 'admin/vehicle_group/'.$row->id,'method'=>'DELETE','class'=>'form-horizontal','id'=>'form_'.$row->id]) !!}
{!! Form::hidden('id', $row->id) !!}
{!! Form::close() !!}