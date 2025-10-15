@component('mail::message')
# Pickup Invitation

Dear {{$booking->driver->name}},

    You have a new pickup invitation. Details below:

@component('mail::table')
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')

<table>
	<tr><td>Customer Name: </td><td>{{$booking->customer->name}}</td></tr>
	<tr><td>Journey Date: </td><td>{{date($date_format_setting,strtotime($booking->pickup))}}</td></tr>
	<tr><td>Pickup Time: </td><td>{{date('g:i A',strtotime($booking->pickup))}}</td></tr>
	<tr><td>Pickup Address: </td><td>{{$booking->pickup_addr}}</td></tr>
    <tr><td>Vehicle: </td><td>{{ optional($booking->vehicle)->make_name }} {{ optional($booking->vehicle)->model_name }} ({{ optional($booking->vehicle)->license_plate }})</td></tr>
    <tr><td>Destination Address: </td><td>{{$booking->dest_addr ?: '-'}}</td></tr>
	<tr><td>Travellers: </td><td>{{$booking->travellers}}</td></tr>

</table>
@endcomponent

Please confirm availability and proceed to pickup at the scheduled time.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
