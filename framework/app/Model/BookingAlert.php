<?php

namespace App\Model;

use App\Model\BaseUuidModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingAlert extends BaseUuidModel 
{
  
    use SoftDeletes;
    protected $table = 'booking_alerts';

    
	public function booking() {
		return $this->hasOne("App\Model\Bookings", "id", "booking_id")->withTrashed();
	}

}
