@can('Users edit')
<button type="button" class="btn btn-sm btn-primary" 
        onclick="window.location.href='{{ url("admin/users/".$row->id."/edit") }}'">
    <i class="fas fa-edit"></i> Edit
</button>
@endcan
@if($row->id!=1)
@can('Users delete')
<button type="button" class="btn btn-sm btn-danger" 
        data-id="{{$row->id}}" data-toggle="modal" data-target="#myModal">
    <i class="fas fa-trash"></i> Delete
</button>
@endcan
@endif
{!! Form::open(['url' => 'admin/users/'.$row->id,'method'=>'DELETE','class'=>'form-horizontal','id'=>'form_'.$row->id]) !!}
{!! Form::hidden("id",$row->id) !!}
{!! Form::close() !!}