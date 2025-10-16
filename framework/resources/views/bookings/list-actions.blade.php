<div class="btn-group">
    {{-- actions container (no dropdown) --}}

        

        @if ($row->status == 0 && $row->ride_status != 'Cancelled')

            


            @if ($row->receipt != 1)
                <a class="btn btn-success btn-sm vtype" href="{{ route('bookings.collected', $row->id) }}" title="Mark as collected">
                    <span class="fa fa-check" aria-hidden="true"></span>
                </a>
            @endif
        @endif
      

        @can('Bookings delete')
            @php
                $trackMessage = '';
                $b = \App\Model\Bookings::where('id', $row->parent_booking_id)->first();

                if ($b) {
                    $trackMessage =
                        'This booking is part of a return trip. Do you want to remove the parent booking too?';
                } else {
                    $d = \App\Model\Bookings::join('bookings_meta', 'bookings_meta.booking_id', '=', 'bookings.id')
                        ->where('bookings_meta.key', 'parent_booking_id')
                        ->where('bookings_meta.value', $row->id)
                        ->first();

                    if ($d) {
                        $trackMessage =
                            'This booking is part of a return trip. Do you want to remove the child booking too?';
                    } else {
                        $trackMessage = '';
                    }
                }
            @endphp

            <a class="btn btn-danger btn-sm vtype" data-id="{{ $row->id }}" data-track="{{ $trackMessage }}"
                data-toggle="modal" data-target="#myModal" title="@lang('fleet.delete')">
                <span class="fa fa-trash" aria-hidden="true"></span>
            </a>
        @endcan


        {{-- @endif --}}
        @if ($row->vehicle_id != null)
            @if ($row->status == 0 && $row->receipt != 1)
                
            @elseif($row->receipt == 1)
                <a class="btn btn-info btn-sm" href="{{ url('admin/invitations/receipt/' . $row->id) }}" title="@lang('fleet.receipt')"><span aria-hidden="true"
                        class="fa fa-list"></span>
                </a>
                @if ($row->receipt == 1 && $row->status == 0 && Auth::user()->user_type != 'C')
                    <a class="btn btn-success btn-sm" href="{{ url('admin/invitations/complete/' . $row->id) }}"
                        data-id="{{ $row->id }}" data-toggle="modal" data-target="#journeyModal" title="@lang('fleet.complete')"><span
                            aria-hidden="true" class="fa fa-check"></span>
                    </a>
                @endif
            @endif
        @endif

        @if ($row->status == 1)
            @if ($row->payment == 0 && Auth::user()->user_type != 'C')
                <a class="btn btn-success btn-sm" href="{{ url('admin/invitations/payment/' . $row->id) }}" title="@lang('fleet.make_payment')">
                    <span aria-hidden="true" class="fa fa-credit-card"></span>
                </a>
            @elseif($row->payment == 1)
                <span class="btn btn-secondary btn-sm disabled" title="@lang('fleet.paid')">
                    <span aria-hidden="true" class="fa fa-credit-card"></span>
                </span>
            @endif
        @endif
    
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
