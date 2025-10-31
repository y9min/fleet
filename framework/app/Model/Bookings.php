<?php
/*
@copyright
Fleet Manager v7.1.2
Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>
 */
namespace App\Model;
use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model\BaseUuidModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kodeine\Metable\Metable;

class Bookings extends BaseUuidModel {
	use HasFactory;
	use Metable;
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = "bookings";
	protected $metaTable = 'bookings_meta';
	protected $fillable = [
		'customer_id', 'vehicle_id', 'user_id', 'pickup', 'dropoff', 'pickup_addr', 'dest_addr', 'travellers', 'status', 'comment', 'dropoff_time', 'driver_id', 'note', 'cancellation', 'completed_at',
	];

	protected $casts = [
		'status' => BookingStatus::class,
		'pickup' => 'datetime',
		'dropoff' => 'datetime',
	];

	protected function getMetaKeyName() {
		return 'booking_id'; // The parent foreign key
	}

	public function vehicle() {
		return $this->hasOne("App\Model\VehicleModel", "id", "vehicle_id")->withTrashed();
	}

	public function customer() {
		return $this->hasOne("App\Model\User", "id", "customer_id")->withTrashed();
	}

	public function driver() {
		return $this->hasOne("App\Model\User", "id", "driver_id")->withTrashed();
	}
	
	public function user() {
		return $this->hasOne("App\Model\User", "id", "user_id")->withTrashed();
	}

	public function vehicletype() {
		return $this->hasOne("App\Model\VehicleTypeModel", "id", "type_id")->withTrashed();
	}

	/**
	 * Scope: overlapping active bookings for a vehicle within [start, end]
	 */
	public function scopeOverlappingActive($query, string $vehicleId, $start, $end, array $activeStatuses = []) {
		$statuses = !empty($activeStatuses)
			? $activeStatuses
			: [BookingStatus::Pending, BookingStatus::Confirmed, BookingStatus::InProgress];
		
		// Ensure start and end are Carbon instances for proper comparison
		if (!$start instanceof \Carbon\Carbon) {
			$start = \Carbon\Carbon::parse($start);
		}
		if (!$end instanceof \Carbon\Carbon) {
			$end = \Carbon\Carbon::parse($end);
		}
		
		return $query
			->whereIn('status', array_map(fn(BookingStatus $s) => $s->value, $statuses))
			->where('vehicle_id', $vehicleId)
			->whereNull('deleted_at')
			->where(function ($q) {
				// Exclude cancelled bookings - check cancellation field (0/null/false = not cancelled, 1/true = cancelled)
				$q->where(function ($qq) {
					$qq->where('cancellation', '=', 0)
						->orWhere('cancellation', '=', false)
						->orWhereNull('cancellation');
				});
			})
			->where(function ($q) use ($start, $end) {
				// Check for any time overlap
				$q->where(function ($qq) use ($start, $end) {
					// New booking starts during existing booking (pickup between start and end)
					$qq->whereBetween('pickup', [$start, $end])
						// Or new booking ends during existing booking (dropoff between start and end)
						->orWhereBetween('dropoff', [$start, $end])
						// Or existing booking completely contains new booking (existing starts before and ends after)
						->orWhere(function ($qqq) use ($start, $end) {
							$qqq->where('pickup', '<=', $start)
								->where('dropoff', '>=', $end);
						})
						// Or new booking completely contains existing booking (new starts before and ends after)
						->orWhere(function ($qqq) use ($start, $end) {
							$qqq->where('pickup', '>=', $start)
								->where('dropoff', '<=', $end);
						});
				});
			});
	}

	// multivehicle test
	// function test1() {
	//     return $this->hasMany("App\Model\VehicleModel", "id", "v_id")->withTrashed();
	// }
	// function test() {
	//     return $this->belongsTo("App\Model\VehicleModel", "v_id", "id")->withTrashed();
	// }
}
