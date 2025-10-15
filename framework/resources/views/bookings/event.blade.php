<table class="table table-striped">
	<tr>
		<th>@lang('fleet.driver')</th>
		<td>{{ ($booking->driver->name) ?? ""}}</td>
	</tr>
	<tr>
		<th>@lang('fleet.vehicle')</th>
		@if($booking->vehicle_id != null)
		<td>{{ $booking->vehicle->make_name}} - {{ $booking->vehicle->model_name}} - {{ $booking->vehicle->license_plate}}</td>
		@endif
	</tr>
	<tr>
		<th>@lang('fleet.pickup')</th>
		<td>{{date('d/m/Y g:i a',strtotime($booking->pickup))}}</td>
	</tr>
	<tr>
		<th>@lang('fleet.pickup_addr')</th>
		<td>{{ $booking->pickup_addr}}</td>
	</tr>
</table>

@if($booking->getMeta('ride_status') != 'Ongoing' && $booking->status != 1)
<div class="text-center mt-3">
	<a href="{{ route('bookings.collected', $booking->id) }}" class="btn btn-success btn-sm">
		<i class="fa fa-check"></i> Mark as Collected
	</a>
</div>
@elseif($booking->getMeta('ride_status') == 'Ongoing')
<div class="text-center mt-3">
	<span class="badge badge-secondary">Already Collected</span>
</div>
@endif