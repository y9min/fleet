@extends('layouts.app')

@php
  use App\Model\Bookings;
  $date_format_setting = Hyvikk::get('date_format') ?? 'd-m-Y';
@endphp

@section("breadcrumb")
  <li class="breadcrumb-item active">@lang('menu.my_bookings')</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('menu.my_bookings')</h3>
      </div>

      <div class="card-body table-responsive">
        <table class="table" id="data_table">
          <thead class="thead-inverse">
            <tr>
              <th>@lang('fleet.id')</th>
              <th>@lang('fleet.customer')</th>
              <th>@lang('fleet.vehicle')</th>
              <th>@lang('fleet.pickup')</th>
              <th>@lang('fleet.dropoff')</th>
              <th>@lang('fleet.pickup_addr')</th>
              <th>@lang('fleet.dropoff_addr')</th>
              <th>@lang('fleet.passengers')</th>
              <th>Booking Type</th>

              @if(Hyvikk::get('driver_ride_control') == 1)
              <th>Action</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @foreach($data as $row)
              <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $row->customer->name ?? '-' }}</td>
                <td>
                  {{ $row->vehicle->make_name ?? '' }} -
                  {{ $row->vehicle->model_name ?? '' }} -
                  {{ $row->vehicle->license_plate ?? '' }}
                </td>
                <td>
                  @if($row->pickup)
                    {{ date($date_format_setting . ' g:i A', strtotime($row->pickup)) }}
                  @endif
                </td>
                <td>
                  @if($row->dropoff)
                    {{ date($date_format_setting . ' g:i A', strtotime($row->dropoff)) }}
                  @endif
                </td>
                <td>{{ $row->pickup_addr }}</td>
                <td>{{ $row->dest_addr }}</td>
                <td>{{ $row->travellers }}</td>
                <td >
                  @php
                    $bookingType = $row->getMeta('return_flag');
                    $parentBookingId = $row->getMeta('parent_booking_id');
                    $parentBooking = $parentBookingId ? Bookings::find($parentBookingId) : null;
                  @endphp

                  @if($bookingType == 1 && $parentBooking)
                    <img src="{{ asset('assets/customer_dashboard/assets/img/return_way.svg') }}" alt="Return Way" height="30" width="30">
                  @else
                    <img src="{{ asset('assets/customer_dashboard/assets/img/one_way.svg') }}" alt="One Way" height="30" width="30">
                  @endif
                </td>
                  @if(Hyvikk::get('driver_ride_control') == 1)
                <td style="width:150px;">

                  @if($row->ride_status == "Upcoming")
                  <a href="{{url('admin/ride-view'.'/'.$row->id)}}" class="btn btn-warning btn-sm">Start Ride</a>

                  @elseif($row->ride_status == "Ongoing")
                  <a href="{{url('admin/start-ride'.'/'.$row->id)}}" class="btn btn-success btn-sm">Ongoing Ride</a>

                  @elseif($row->ride_status == "Completed")
                  <a href="{{url('admin/complete-ride'.'/'.$row->id)}}" class="btn btn-secondary btn-sm">Completed Ride</a>

                  @else
                  --
                  @endif
                
                </td>
                @endif
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th></th>
              <th>@lang('fleet.customer')</th>
              <th>@lang('fleet.vehicle')</th>
              <th>@lang('fleet.pickup')</th>
              <th>@lang('fleet.dropoff')</th>
              <th>@lang('fleet.pickup_addr')</th>
              <th>@lang('fleet.dropoff_addr')</th>
              <th>@lang('fleet.passengers')</th>
              <th>Booking Type</th>
                @if(Hyvikk::get('driver_ride_control') == 1)
              <th>Action</th>
              @endif
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')


  @if(Session::get('success'))
  <script>
    new PNotify({
        title: 'Success!',
        text: '{{ Session::get('success') }}',
        type: 'success'
      });
      </script>
  @endif

 @if(Session::get('error'))
    <script>
        new PNotify({
            title: 'Error!',
            text: '{{ Session::get('error') }}',
            type: 'error' // or 'danger', depending on PNotify version
        });
    </script>
@endif




@endsection