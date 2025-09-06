<?php
/*
@copyright

Fleet Manager v7.1.2

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Mail;

use App\Model\Bookings;
use Hyvikk;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VehicleBooked extends Mailable {
	use SerializesModels;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public $booking;
	public function __construct(Bookings $booking) {
		$this->booking = $booking;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {
		return $this->from(Hyvikk::get("email"))->subject('Ride Booked. Booking ID: ' . $this->booking->id)->markdown('emails.booked_customer');
	}
}
