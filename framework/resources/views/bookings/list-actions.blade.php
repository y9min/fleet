<div class="d-flex justify-content-center" style="gap: 0;">
    {{-- Green tick button: Mark Vehicle Collected --}}
    <button type="button" class="btn btn-sm delete-invitation-btn" 
            data-id="{{ $row->id }}" 
            data-action="mark-collected"
            title="Mark Vehicle Collected"
            style="width: 35px; height: 35px; padding: 0; display: flex; align-items: center; justify-content: center; background-color: #28a745; border: none; color: #fff;">
        <i class="fa fa-check" aria-hidden="true"></i>
    </button>

    {{-- Red delete button --}}
    <button type="button" class="btn btn-sm delete-invitation-btn" 
            data-id="{{ $row->id }}" 
            data-action="delete"
            title="@lang('fleet.delete')"
            style="width: 35px; height: 35px; padding: 0; display: flex; align-items: center; justify-content: center; background-color: #dc3545; border: none; color: #fff;">
        <i class="fa fa-trash" aria-hidden="true"></i>
    </button>
</div>
{!! Form::open([
    'url' => 'admin/invitations/' . $row->id,
    'method' => 'DELETE',
    'class' => 'form-horizontal',
    'id' => 'book_' . $row->id,
]) !!}
{!! Form::hidden('id', $row->id) !!}

<input type="hidden" name="check" class="check">

{!! Form::close() !!}
